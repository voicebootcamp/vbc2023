<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class plgOSMembershipJoomlagroups1 extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 */
	protected $db;

	/**
	 * Make language files will be loaded automatically.
	 *
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * Render settings from
	 *
	 * @param   OSMembershipTablePlan  $row
	 *
	 * @return array
	 */
	public function onEditSubscriptionPlan($row)
	{
		if (!$this->isExecutable())
		{
			return [];
		}

		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return ['title' => Text::_('PLG_OSMEMBERSHIP_JOOMLA_GROUPS_SETTINGS'),
		        'form'  => $form,
		];
	}

	/**
	 * Store setting into database
	 *
	 * @param   OSMembershipTablePlan  $row
	 * @param   Boolean                $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$params = new Registry($row->params);

		$keys = ['joomla_group_ids', 'remove_joomla_group_ids', 'subscription_expired_joomla_group_ids', 'joomla_expried_group_ids'];

		foreach ($keys as $key)
		{
			if (!isset($data[$key]))
			{
				$data[$key] = [];
			}
		}

		$params->set('joomla_group_ids', implode(',', $data['joomla_group_ids']));
		$params->set('remove_joomla_group_ids', implode(',', $data['remove_joomla_group_ids']));
		$params->set('subscription_expired_joomla_group_ids', implode(',', $data['subscription_expired_joomla_group_ids']));
		$params->set('joomla_expried_group_ids', implode(',', $data['joomla_expried_group_ids']));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Run when a membership activated
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onMembershipActive($row)
	{
		if (!$row->user_id)
		{
			return;
		}

		$user          = Factory::getUser($row->user_id);
		$currentGroups = $user->get('groups');
		$plan          = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params         = new Registry($plan->params);
		$groups         = explode(',', $params->get('joomla_group_ids', ''));
		$removeGroupIds = explode(',', $params->get('remove_joomla_group_ids', ''));
		$currentGroups  = array_unique(array_merge($currentGroups, $groups));

		if ($row->group_admin_id > 0 && PluginHelper::isEnabled('osmembership', 'groupmembership'))
		{
			// This is group member, need to exclude from some groups if needed
			$plugin = PluginHelper::getPlugin('osmembership', 'groupmembership');

			if ($plugin)
			{
				$params          = new Registry($plugin->params);
				$excludeGroupIds = $params->get('exclude_group_ids', '');

				if ($excludeGroupIds)
				{
					$excludeGroupIds = explode(',', $excludeGroupIds);
					$excludeGroupIds = ArrayHelper::toInteger($excludeGroupIds);
					$currentGroups   = array_diff($currentGroups, $excludeGroupIds);
				}
			}
		}

		// Get Joomla group from custom fields selection
		$currentGroups = array_merge($currentGroups, $this->getJoomlaGroupsFromFields($row));

		// Remove from Joomla groups when active
		$currentGroups = array_diff($currentGroups, $removeGroupIds);

		if ($row->act === 'upgrade')
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('from_plan_id')
				->from('#__osmembership_upgraderules')
				->where('id = ' . $row->upgrade_option_id);
			$db->setQuery($query);
			$fromPlanId = $db->loadResult();

			if ($fromPlanId)
			{
				$fromPlan = Table::getInstance('Plan', 'OSMembershipTable');
				$fromPlan->load($fromPlanId);

				$params        = new Registry($fromPlan->params);
				$groups        = explode(',', $params->get('joomla_group_ids', ''));
				$currentGroups = array_unique(array_merge($currentGroups, $groups));
			}
		}

		$user->set('groups', $currentGroups);
		$user->save(true);
	}

	/**
	 * Run when a membership expiried die
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onMembershipExpire($row)
	{
		if (!$row->user_id)
		{
			return;
		}

		$user          = Factory::getUser($row->user_id);
		$currentGroups = $user->get('groups');
		$plan          = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params                      = new Registry($plan->params);
		$groups                      = explode(',', $params->get('joomla_expried_group_ids', ''));
		$subscriptionExpiredGroupIds = explode(',', $params->get('subscription_expired_joomla_group_ids', ''));
		$activePlans                 = OSMembershipHelperSubscription::getActiveMembershipPlans($row->user_id, [$row->id]);

		// Subscriber should be removed from the user groups which he is assigned to from custom fields if he does not have active subscription of this plan anymore
		if (!in_array($row->plan_id, $activePlans))
		{
			$customFieldUserGroups = $this->getJoomlaGroupsFromFields($row);

			if (count($customFieldUserGroups))
			{
				$groups = array_merge($groups, $customFieldUserGroups);
			}
		}

		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('params')
			->from('#__osmembership_plans')
			->where('id IN  (' . implode(',', $activePlans) . ')');
		$db->setQuery($query);
		$rowPlans = $db->loadObjectList();

		// Subscribers will be assigned to this group if he has no more active subscription of this plan, haven't renewed yet
		if (!in_array($row->plan_id, $activePlans) && count($subscriptionExpiredGroupIds))
		{
			// We need to remove he groups which is configured to be removed when there is active subscription of other plan
			foreach ($rowPlans as $rowPlan)
			{
				$planParams                  = new Registry($rowPlan->params);
				$planGroups                  = explode(',', $planParams->get('remove_joomla_group_ids', ''));
				$subscriptionExpiredGroupIds = array_diff($subscriptionExpiredGroupIds, $planGroups);
			}

			$currentGroups = array_merge($currentGroups, $subscriptionExpiredGroupIds);

			reset($rowPlans);
		}

		foreach ($rowPlans as $rowPlan)
		{
			$planParams = new Registry($rowPlan->params);
			$planGroups = explode(',', $planParams->get('joomla_group_ids', ''));
			$groups     = array_diff($groups, $planGroups);
		}

		$currentGroups = array_unique(array_diff($currentGroups, $groups));
		$user->set('groups', $currentGroups);
		$user->save(true);
	}

	/**
	 * Get Joomla groups from custom fields which subscriber select for their subscription
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 *
	 * @return array
	 */
	private function getJoomlaGroupsFromFields($row)
	{
		$groups = [];

		$rowFields        = OSMembershipHelper::getProfileFields($row->plan_id, true, $row->language, $row->act);
		$subscriptionData = OSMembershipHelper::getProfileData($row, $row->plan_id, $rowFields);

		foreach ($rowFields as $field)
		{
			if (empty($field->joomla_group_ids) || empty($field->values) || empty($subscriptionData[$field->name]))
			{
				continue;
			}

			$fieldValue = $subscriptionData[$field->name];

			$groups = array_merge($groups, OSMembershipHelperSubscription::getUserGroupsFromFieldValue($field, $fieldValue));
		}

		return $groups;
	}

	/**
	 * Method to check if the plugin is executable
	 *
	 * @return bool
	 */
	private function isExecutable()
	{
		if ($this->app->isClient('site') && !$this->params->get('show_on_frontend'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param   OSMembershipTablePlan  $row
	 */
	private function drawSettingForm($row)
	{
		$excludeUserGroups = (array) $this->params->get('exclude_user_groups');
		$groups            = array_values(JHelperUsergroups::getInstance()->getAll());
		$options           = [];

		for ($i = 0, $n = count($groups); $i < $n; $i++)
		{
			$group = $groups[$i];

			if ($this->app->isClient('site') && in_array($group->id, $excludeUserGroups))
			{
				continue;
			}

			$options[] = HTMLHelper::_('select.option', $group->id, str_repeat('- ', $group->level) . $group->title);
		}

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}
}
