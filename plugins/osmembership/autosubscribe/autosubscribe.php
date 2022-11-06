<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

class plgOSMembershipAutoSubscribe extends CMSPlugin
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

		return ['title' => Text::_('PLG_AUTO_SUBSCRIBE'),
		        'form'  => $form,
		];
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param   OSMembershipTablePlan  $row
	 * @param   bool                   $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$params = new Registry($row->params);

		$params->set('auto_subscribe_plan_ids', implode(',', $data['auto_subscribe_plan_ids'] ?? []));
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
		if ($row->auto_subscribe_processed)
		{
			return;
		}

		/* @var OSMembershipTablePlan $rowPlan */
		$rowPlan = Table::getInstance('Plan', 'OSMembershipTable');
		$rowPlan->load($row->plan_id);
		$params  = new Registry($rowPlan->params);
		$planIds = explode(',', $params->get('auto_subscribe_plan_ids', ''));
		$planIds = array_filter($planIds);

		if (empty($planIds))
		{
			return;
		}

		/* @var OSMembershipModelApi $model */
		$model = MPFModel::getInstance('Api', 'OSMembershipModel', ['ignore_request' => true, 'remember_states' => false]);

		// First, get details information about the subscription
		$data              = $model->getSubscriptionData($row->id);
		$data['published'] = 1;
		$data['user_id']   = $row->user_id;
		$data['parent_id'] = $row->id;

		// Reset amount data, set it to 0  for the auto-subscribed subscription
		$data['amount'] = $data['discount_amount'] = $data['tax_amount'] = $data['payment_processing_fee'] = $data['tax_rate'] = 0;

		foreach ($planIds as $planId)
		{
			$data['plan_id'] = $planId;

			try
			{
				$model->store($data);
			}
			catch (Exception $e)
			{

			}
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
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('title');

		if ($row->id > 0)
		{
			$query->where('id != ' . $row->id);
		}

		$db->setQuery($query);

		$options = [];

		foreach ($db->loadObjectList() as $plan)
		{
			$options[] = HTMLHelper::_('select.option', $plan->id, $plan->title);
		}

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}
}
