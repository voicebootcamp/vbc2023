<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

class plgOSMembershipLimitSubscriptions extends CMSPlugin
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
	 * Render setting form
	 *
	 * @param   PlanOSMembership  $row
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
		$form = ob_get_clean();

		return ['title' => Text::_('PLG_OSMEMBERSHIP_MAX_SUBCRIPTIONS_SETTING'),
		        'form'  => $form,
		];
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param   PlanOsMembership  $row
	 * @param   bool              $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$params = new Registry($row->params);
		$params->set('max_subscriptions', $data['max_subscriptions']);
		$row->params = $params->toString();
		$row->store();
	}

	/**
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onMembershipActive($row)
	{
		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params           = new Registry($plan->params);
		$maxSubscriptions = (int) $params->get('max_subscriptions', 0);

		if (!$maxSubscriptions)
		{
			return;
		}

		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('COUNT(id)')
			->from('#__osmembership_subscribers')
			->where('plan_id = ' . (int) $row->plan_id)
			->where('published IN (1,2)');
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total >= $maxSubscriptions)
		{
			$plan->published = 0;
			$plan->store();
		}
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
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   OSMembershipTablePlan  $row
	 */
	private function drawSettingForm($row)
	{
		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}
}
