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

class plgOSMembershipVMShopperGroups extends CMSPlugin
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
	 * Constructor
	 *
	 * @param   object &$subject  The object to observe
	 * @param   array   $config   An optional associative array of configuration settings.
	 */
	public function __construct($subject, array $config = [])
	{
		if (!file_exists(JPATH_ROOT . '/components/com_virtuemart/virtuemart.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

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

		return ['title' => Text::_('OSM_VM_SHOPPER_GROUPS_SETTINGS'),
		        'form'  => $form,
		];
	}

	/**
	 * Store settings into database
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

		$params->set('vm_shopper_group_ids', implode(',', $data['vm_shopper_group_ids'] ?? []));
		$params->set('vm_expired_shopper_group_ids', implode(',', $data['vm_expired_shopper_group_ids'] ?? []));
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
		if (!$this->app)
		{
			return;
		}

		if (!$row->user_id)
		{
			return;
		}

		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params = new Registry($plan->params);
		$groups = explode(',', $params->get('vm_shopper_group_ids'));
		$groups = array_filter(ArrayHelper::toInteger($groups));

		if (empty($groups))
		{
			return;
		}

		// Get all the shopper groups which the subscriber was assigned
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('virtuemart_shoppergroup_id')
			->from('#__virtuemart_vmuser_shoppergroups')
			->where('virtuemart_user_id = ' . $row->user_id);
		$db->setQuery($query);
		$groupIds = $db->loadColumn();

		foreach ($groups as $group)
		{
			if (in_array($group, $groupIds))
			{
				continue;
			}

			$query->clear()
				->insert('#__virtuemart_vmuser_shoppergroups')
				->columns('virtuemart_user_id, virtuemart_shoppergroup_id')
				->values(implode(',', $db->quote([$row->user_id, $group])));
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Run when a membership expiried die
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onMembershipExpire($row)
	{
		if (!$this->app)
		{
			return;
		}

		if (!$row->user_id)
		{
			return;
		}

		$activePlans = OSMembershipHelperSubscription::getActiveMembershipPlans($row->user_id, [$row->id]);

		// He renewed his subscription before, so don't remove him from the groups
		if (in_array($row->plan_id, $activePlans))
		{
			return;
		}

		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params         = new Registry($plan->params);
		$removeGroupIds = explode(',', $params->get('vm_expired_shopper_group_ids', ''));
		$removeGroupIds = array_filter(ArrayHelper::toInteger($removeGroupIds));

		if (empty($removeGroupIds))
		{
			return;
		}

		// If user
		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('params')
			->from('#__osmembership_plans')
			->where('id IN  (' . implode(',', $activePlans) . ')');
		$db->setQuery($query);
		$rowPlans = $db->loadObjectList();

		foreach ($rowPlans as $rowPlan)
		{
			$planParams     = new Registry($rowPlan->params);
			$planGroups     = explode(',', $planParams->get('vm_shopper_group_ids', ''));
			$planGroups     = array_filter(ArrayHelper::toInteger($planGroups));
			$removeGroupIds = array_diff($removeGroupIds, $planGroups);
		}

		foreach ($removeGroupIds as $removeGroupId)
		{
			$query->clear()->delete('#__virtuemart_vmuser_shoppergroups')
				->where('virtuemart_shoppergroup_id =' . $removeGroupId)
				->where('virtuemart_user_id =' . $row->user_id);
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Method to check if the plugin is executable
	 *
	 * @return bool
	 */
	private function isExecutable()
	{
		if (!$this->app)
		{
			return false;
		}

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
		Factory::getLanguage()->load('com_virtuemart_shoppers', JPATH_ROOT . '/components/com_virtuemart');

		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('virtuemart_shoppergroup_id, shopper_group_name')->from('#__virtuemart_shoppergroups');
		$db->setQuery($query);
		$shopperGroups = $db->loadObjectList();

		$options = [];

		foreach ($shopperGroups as $shopperGroup)
		{
			$options[] = HTMLHelper::_('select.option', $shopperGroup->virtuemart_shoppergroup_id, Text::_($shopperGroup->shopper_group_name));
		}

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}
}
