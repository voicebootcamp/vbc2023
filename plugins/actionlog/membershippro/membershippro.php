<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

class plgActionlogMembershippro extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;

	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 *
	 * @since       6.4.0
	 */
	public function __construct(& $subject, $config)
	{
		// Make sure Akeeba Backup is installed
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership'))
		{
			return;
		}

		if (!ComponentHelper::isEnabled('com_osmembership'))
		{
			return;
		}

		// No point in logging guest actions
		if (Factory::getUser()->guest)
		{
			return;
		}

		// If any of the above statement returned, our plugin is not attached to the subject, so it's basically disabled
		parent::__construct($subject, $config);
	}

	/**
	 * Log add/edit plan action
	 *
	 * @param   OSMembershipTablePlan  $row
	 * @param   bool                   $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		$message = [];

		if ($isNew)
		{
			$messageKey = 'MP_LOG_PLAN_ADDED';
		}
		else
		{
			$messageKey = 'MP_LOG_PLAN_UPDATED';
		}

		$message['itemlink'] = 'index.php?option=com_osmembership&view=plan&id=' . $row->id;
		$message['title']    = $row->title;

		$this->addLog($message, $messageKey);
	}

	/**
	 * Log publish/unpublish event action
	 *
	 * @param   string  $context
	 * @param   array   $pks
	 * @param           $value
	 */
	public function onPlanChangeState($context, $pks, $value)
	{
		$message = [];

		if ($value)
		{
			$messageKey = 'MP_LOG_PLANS_PUBLISHED';
		}
		else
		{
			$messageKey = 'MP_LOG_PLANS_UNPUBLISHED';
		}

		$message['ids']         = implode(',', $pks);
		$message['numberplans'] = count($pks);

		$this->addLog($message, $messageKey);
	}

	/**
	 * Log delete plans action
	 *
	 * @param   string  $context
	 * @param   array   $pks
	 */
	public function onPlansAfterDelete($context, $pks)
	{
		$message = [];

		$messageKey             = 'MP_LOG_PLANS_DELETED';
		$message['ids']         = implode(',', $pks);
		$message['numberplans'] = count($pks);

		$this->addLog($message, $messageKey);
	}

	/**
	 * Log add/edit registrant action
	 *
	 * @param   string                       $context
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   bool                         $isNew
	 */
	public function onSubscriptionAfterSave($context, $row, $data, $isNew)
	{
		$message = [];

		if ($isNew)
		{
			$messageKey = 'MP_LOG_SUBSCRIPTION_ADDED';
		}
		else
		{
			$messageKey = 'MP_LOG_SUBSCRIPTION_UPDATED';
		}

		$message['id']       = $row->id;
		$message['name']     = trim($row->first_name . ' ' . $row->last_name);
		$message['itemlink'] = 'index.php?option=com_osmembership&view=subscription&id=' . $row->id;

		$this->addLog($message, $messageKey);
	}

	/**
	 * Log delete registrants action
	 *
	 * @param   string  $context
	 * @param   array   $pks
	 */
	public function onSubscriptionsAfterDelete($context, $pks)
	{
		$message = [];

		$messageKey                     = 'MP_LOG_SUBSCRIPTIONS_DELETED';
		$message['ids']                 = implode(',', $pks);
		$message['numbersubscriptions'] = count($pks);

		$this->addLog($message, $messageKey);
	}

	/**
	 * Log publish/unpublish event action
	 *
	 * @param   string  $context
	 * @param   array   $pks
	 * @param           $value
	 */
	public function onSubscriptionChangeState($context, $pks, $value)
	{
		$message = [];

		if ($value)
		{
			$messageKey = 'MP_LOG_SUBSCRIPTIONS_PUBLISHED';
		}
		else
		{
			$messageKey = 'MP_LOG_SUBSCRIPTIONS_UNPUBLISHED';
		}

		$message['ids']                 = implode(',', $pks);
		$message['numbersubscriptions'] = count($pks);

		$this->addLog($message, $messageKey);
	}

	/**
	 * Log publish/unpublish event action
	 *
	 * @param   MPFModelState int
	 * @param   int  $numberSubscriptions
	 */
	public function onSubscriptionsExport($planId, $numberSubscriptions)
	{
		$message = [];

		$message['numbersubscriptions'] = $numberSubscriptions;

		if ($planId)
		{
			$messageKey    = 'MP_LOG_PLAN_SUBSCRIPTIONS_EXPORTED';
			$message['id'] = $planId;
		}
		else
		{
			$messageKey = 'MP_LOG_SUBSCRIPTIONS_EXPORTED';
		}

		$this->addLog($message, $messageKey);
	}

	/**
	 * Log an action
	 *
	 * @param   array   $message
	 * @param   string  $messageKey
	 */
	private function addLog($message, $messageKey)
	{
		$user = Factory::getUser();

		if (!array_key_exists('userid', $message))
		{
			$message['userid'] = $user->id;
		}

		if (!array_key_exists('username', $message))
		{
			$message['username'] = $user->username;
		}

		if (!array_key_exists('accountlink', $message))
		{
			$message['accountlink'] = 'index.php?option=com_users&task=user.edit&id=' . $user->id;
		}

		try
		{

			if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
			{
				/** @var \Joomla\Component\Actionlogs\Administrator\Model\ActionlogModel $model */
				$model = $this->app->bootComponent('com_actionlogs')
					->getMVCFactory()->createModel('Actionlog', 'Administrator', ['ignore_request' => true]);
			}
			else
			{
				// Require action log library
				JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_actionlogs/models', 'ActionlogsModel');

				/** @var \ActionlogsModelActionlog $model * */
				$model = \JModelLegacy::getInstance('Actionlog', 'ActionlogsModel');
			}

			$model->addLog([$message], $messageKey, 'com_osmembership', $user->id);
		}
		catch (\Exception $e)
		{
			// Ignore any error
		}
	}
}
