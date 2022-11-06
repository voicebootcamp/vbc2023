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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

class plgSystemMembershipProSms extends CMSPlugin
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
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array    $config
	 */
	public function __construct(&$subject, $config = [])
	{
		if (!file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Generate invoice number after registrant complete payment for registration
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 *
	 * @return void
	 */
	public function onMembershipActive($row)
	{
		// Workaround to prevent listening to event trigger with same name (from our other extensions)
		if (!property_exists($row, 'plan_id')
			|| !property_exists($row, 'first_sms_reminder_sent'))
		{
			return;
		}

		if (strpos($row->payment_method, 'os_offline') === false)
		{
			$this->sendSMSMessageToAdmin($row);
		}
	}

	/**
	 * Generate invoice number after registrant complete registration in case he uses offline payment
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onAfterStoreSubscription($row)
	{
		if (strpos($row->payment_method, 'os_offline') !== false)
		{
			$this->sendSMSMessageToAdmin($row);
		}
	}

	/**
	 * Method to send SMS message to administrator
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	private function sendSMSMessageToAdmin($row)
	{
		if ($this->app->isClient('administrator'))
		{
			return;
		}

		$phones = $this->params->get('phones');

		if (!$phones)
		{
			return;
		}

		$phones = explode(',', $phones);
		$phones = array_filter($phones);

		if (!count($phones))
		{
			return;
		}

		// Get extra data for the registration record
		$fieldSuffix = OSMembershipHelper::getFieldSuffix($row->language);
		$plan        = OSMembershipHelperDatabase::getPlan($row->plan_id, $fieldSuffix);

		// Admin does not allow sending SMS, stop
		if (!$plan->enable_sms_reminder)
		{
			return;
		}

		$message = OSMembershipHelper::getMessages();

		switch ($row->act)
		{
			case 'renew':
				if (!empty($plan->new_subscription_renewal_admin_sms))
				{
					$smsMessage = $plan->new_subscription_renewal_admin_sms;
				}
				else
				{
					$smsMessage = $message->new_subscription_renewal_admin_sms;
				}
				break;
			case 'upgrade':
				if (!empty($plan->new_subscription_upgrade_admin_sms))
				{
					$smsMessage = $plan->new_subscription_upgrade_admin_sms;
				}
				else
				{
					$smsMessage = $message->new_subscription_upgrade_admin_sms;
				}
				break;
			default:
				if (!empty($plan->new_subscription_admin_sms))
				{
					$smsMessage = $plan->new_subscription_admin_sms;
				}
				else
				{
					$smsMessage = $message->new_subscription_admin_sms;
				}
				break;
		}

		if (!trim((string) $smsMessage))
		{
			return;
		}

		$replaces               = OSMembershipHelper::buildSMSTags($row);
		$replaces['plan_title'] = $plan->title;

		foreach ($replaces as $key => $value)
		{
			$value      = (string) $value;
			$smsMessage = str_ireplace('[' . $key . ']', $value, $smsMessage);
		}

		$admins = [];

		foreach ($phones as $phone)
		{
			$admin = clone $row;

			$admin->phone = $phone;

			$admin->sms_message = $smsMessage;

			$admins[] = $admin;
		}

		// Trigger
		if (count($admins))
		{
			PluginHelper::importPlugin('membershipprosms');

			$this->app->triggerEvent('onMembershipProSendingSMSReminder', [$admins]);
		}
	}

	/**
	 * Handle onAfterRespond event to send SMS reminder
	 *
	 * @return bool|void
	 * @throws Exception
	 */

	public function onAfterRespond()
	{
		if (!$this->canRun())
		{
			return;
		}

		//Store last run time
		$this->params->set('last_run', time());

		$db    = $this->db;
		$query = $db->getQuery(true)
			->update('#__extensions')
			->set('params = ' . $db->quote($this->params->toString()))
			->where('`element`= "ebsmsreminder"')
			->where('`folder`= "system"');
		try
		{
			// Lock the tables to prevent multiple plugin executions causing a race condition
			$db->lockTable('#__extensions');
		}
		catch (Exception $e)
		{
			// If we can't lock the tables it's too risk continuing execution
			return;
		}

		try
		{
			// Update the plugin parameters
			$result = $db->setQuery($query)->execute();
			$this->clearCacheGroups(['com_plugins'], [0, 1]);
		}
		catch (Exception $exc)
		{
			// If we failed to execute
			$db->unlockTables();
			$result = false;
		}

		try
		{
			// Unlock the tables after writing
			$db->unlockTables();
		}
		catch (Exception $e)
		{
			// If we can't lock the tables assume we have somehow failed
			$result = false;
		}

		// Abort on failure
		if (!$result)
		{
			return;
		}

		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		// Send first, second and third SMS reminder
		$this->sendSMSReminder(1);
		$this->sendSMSReminder(2);
		$this->sendSMSReminder(3);

		return true;
	}

	/**
	 * Method to send sms reminder to subscribers
	 *
	 * @param   int  $number
	 */
	private function sendSMSReminder($number)
	{
		if (!in_array($number, [1, 2, 3]))
		{
			return;
		}

		switch ($number)
		{
			case 2:
				$smsMessageField   = 'second_reminder_sms';
				$sendReminderField = 'b.send_second_reminder';
				$reminderSentField = 'a.second_sms_reminder_sent';
				break;
			case 3:
				$smsMessageField   = 'third_reminder_sms';
				$sendReminderField = 'b.send_third_reminder';
				$reminderSentField = 'a.third_sms_reminder_sent';
				break;
			default:
				$smsMessageField   = 'first_reminder_sms';
				$sendReminderField = 'b.send_first_reminder';
				$reminderSentField = 'a.first_sms_reminder_sent';
				break;
		}

		$message = OSMembershipHelper::getMessages();

		// Stop processing it further if the sms message is not configured
		if (!trim((string) $message->{$smsMessageField}))
		{
			return;
		}

		$numberEmailSendEachTime = (int) $this->params->get('number_subscribers', 0) ?: 15;

		$db = $this->db;

		// Workaround to allow supporting different sms messages in each plan
		$fields = array_keys($db->getTableColumns('#__osmembership_plans'));

		$planFields = [];

		if (in_array('first_reminder_sms', $fields))
		{
			$planFields[] = 'b.first_reminder_sms';
		}

		if (in_array('second_reminder_sms', $fields))
		{
			$planFields[] = 'b.second_reminder_sms';
		}

		if (in_array('third_reminder_sms', $fields))
		{
			$planFields[] = 'b.third_reminder_sms';
		}

		$query = $db->getQuery(true)
			->select('a.*, b.title AS plan_title, b.recurring_subscription, b.number_payments, c.username')
			->select('IF(b.send_first_reminder > 0, DATEDIFF(to_date, NOW()), DATEDIFF(NOW(), to_date)) AS number_days')
			->from('#__osmembership_subscribers AS a')
			->innerJoin('#__osmembership_plans AS b  ON a.plan_id = b.id')
			->leftJoin('#__users AS c  ON a.user_id = c.id')
			->where("$sendReminderField != 0")
			->where('b.lifetime_membership != 1')
			->where('b.enable_sms_reminder = 1')
			->where('a.published IN (1, 2)')
			->where("$reminderSentField = 0")
			->where('a.group_admin_id = 0')
			->where("IF($sendReminderField > 0, $sendReminderField >= DATEDIFF(to_date, NOW()) AND DATEDIFF(to_date, NOW()) >= 0, DATEDIFF(NOW(), to_date) >= ABS($sendReminderField) AND DATEDIFF(NOW(), to_date) <= 60)")
			->order('a.to_date');
		$db->setQuery($query, 0, $numberEmailSendEachTime);

		if (count($planFields))
		{
			$query->select($db->quoteName($planFields));
		}

		try
		{
			$rows = $db->loadObjectList();
		}
		catch (Exception  $e)
		{
			$rows = [];
		}

		if (!count($rows))
		{
			return;
		}

		$ids = [];

		foreach ($rows as $row)
		{
			$ids[] = $row->id;

			if (!$row->phone)
			{
				continue;
			}

			if (!empty($row->{$smsMessageField}))
			{
				$smsMessage = $row->{$smsMessageField};
			}
			else
			{
				$smsMessage = $message->{$smsMessageField};
			}

			$replaces = OSMembershipHelper::buildSMSTags($row);

			foreach ($replaces as $key => $value)
			{
				$value      = (string) $value;
				$smsMessage = str_ireplace('[' . $key . ']', $value, $smsMessage);
			}

			$row->sms_message = $smsMessage;
		}

		PluginHelper::importPlugin('membershipprosms');

		$result = $this->app->triggerEvent('onMembershipProSendingSMSReminder', [$rows]);

		if (in_array(true, $result, true))
		{
			$query->clear()
				->update('#__osmembership_subscribers AS a')
				->set("$reminderSentField = 1")
				->where('id IN (' . implode(',', $ids) . ')');

			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Method to check whether this plugin should be run
	 *
	 * @return bool
	 */
	private function canRun()
	{
		if (!$this->app)
		{
			return false;
		}

		// Process sending reminder on every page load if debug mode enabled
		if ($this->params->get('debug', 0))
		{
			return true;
		}

		// If trigger reminder code is set, we will only process sending reminder from cron job
		if (trim($this->params->get('trigger_reminder_code', '')))
		{
			if ($this->params->get('trigger_reminder_code') == $this->app->input->getString('trigger_reminder_code'))
			{
				return true;
			}

			return false;
		}

		// If time ranges is set and current time is not within these specified ranges, we won't process sending reminder

		if ($this->params->get('time_ranges'))
		{
			$withinTimeRage = false;
			$date           = Factory::getDate('Now', Factory::getApplication()->get('offset'));
			$currentHour    = $date->format('G', true);
			$timeRanges     = explode(';', $this->params->get('time_ranges'));// Time ranges format 6,10;14,20

			foreach ($timeRanges as $timeRange)
			{
				if (strpos($timeRange, ',') == false)
				{
					continue;
				}

				list($fromHour, $toHour) = explode(',', $timeRange);

				if ($fromHour <= $currentHour && $toHour >= $currentHour)
				{
					$withinTimeRage = true;
					break;
				}
			}

			if (!$withinTimeRage)
			{
				return false;
			}
		}

		// Send reminder if the last time reminder emails are sent was more than 20 minutes ago
		$lastRun = (int) $this->params->get('last_run', 0);

		if ((time() - $lastRun) < 1200)
		{
			return false;
		}

		return true;
	}

	/**
	 * Clears cache groups. We use it to clear the plugins cache after we update the last run timestamp.
	 *
	 * @param   array  $clearGroups   The cache groups to clean
	 * @param   array  $cacheClients  The cache clients (site, admin) to clean
	 *
	 * @return  void
	 *
	 * @since   2.0.4
	 */
	private function clearCacheGroups(array $clearGroups, array $cacheClients = [0, 1])
	{
		$conf = Factory::getConfig();
		foreach ($clearGroups as $group)
		{
			foreach ($cacheClients as $client_id)
			{
				try
				{
					$options = [
						'defaultgroup' => $group,
						'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' :
							$conf->get('cache_path', JPATH_SITE . '/cache'),
					];
					$cache   = JCache::getInstance('callback', $options);
					$cache->clean();
				}
				catch (Exception $e)
				{
					// Ignore it
				}
			}
		}
	}
}
