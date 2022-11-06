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
use Joomla\Registry\Registry;

class plgOSMembershipScript extends CMSPlugin
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
	 * @param $row
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

		return ['title' => Text::_('PLG_OSMEMBERSHIP_SCRIPTS'),
		        'form'  => $form,
		];
	}

	/**
	 * Store setting into database
	 *
	 * @param   PlanOsMembership  $row
	 * @param   Boolean           $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$params = new Registry($row->params);
		$params->set('subscription_store_script', $data['subscription_store_script']);
		$params->set('subscription_active_script', $data['subscription_active_script']);
		$params->set('subscription_expired_script', $data['subscription_expired_script']);
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Run the PHP script when subscription is stored in database
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public function onAfterStoreSubscription($row)
	{
		$params = $this->getPlanParams($row->plan_id);
		$script = trim($params->get('subscription_store_script', ''));

		if ($script)
		{
			try
			{
				eval($script);
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage(Text::_('The PHP script is wrong. Please contact Administrator'), 'error');
			}
		}

		return true;
	}

	/**
	 * Run the PHP script when membership is activated
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public function onMembershipActive($row)
	{
		$params = $this->getPlanParams($row->plan_id);
		$script = trim($params->get('subscription_active_script', ''));

		if ($script)
		{
			try
			{
				eval($script);
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage(Text::_('The PHP script is wrong. Please contact Administrator'), 'error');
			}
		}

		return true;
	}

	/**
	 * Run the PHP script when membership expired
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public function onMembershipExpire($row)
	{
		$params = $this->getPlanParams($row->plan_id);
		$script = trim($params->get('subscription_expired_script', ''));

		if ($script)
		{
			try
			{
				eval($script);
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage(Text::_('The PHP script is wrong. Please contact Administrator'), 'error');
			}
		}

		return true;
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
	 * @param   object  $row
	 */
	private function drawSettingForm($row)
	{
		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}

	/**
	 * The params of the subscription plan
	 *
	 * @param $planId
	 *
	 * @return Registry
	 */
	private function getPlanParams($planId)
	{
		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('params')
			->from('#__osmembership_plans')
			->where('id = ' . $planId);
		$db->setQuery($query);

		return new Registry($db->loadResult());
	}
}
