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

class plgSystemOSMembershipReminder extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var JDatabaseDriver
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array    $config
	 */
	public function __construct(&$subject, $config = [])
	{
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * The sending reminder emails is triggered after the page has fully rendered.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function onAfterRender()
	{
		if (!$this->canRun())
		{
			return;
		}

		$bccEmail                = $this->params->get('bcc_email', '');
		$numberEmailSendEachTime = (int) $this->params->get('number_subscribers', 5);
		$now                     = time();

		//Store last run time
		$this->params->set('last_run', $now);
		$params = $this->params->toString();
		$db     = $this->db;
		$query  = $db->getQuery(true)
			->update('#__extensions')
			->set('params=' . $db->quote($params))
			->where('`element`="osmembershipreminder"')
			->where('`folder`="system"');

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
			// If we failed to execite
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

		try
		{
			// Require library + register autoloader
			require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';
		}
		catch (Exception $e)
		{
			// Return to avoid fatal error
			return;
		}

		$message = OSMembershipHelper::getMessages();

		PluginHelper::importPlugin('osmembership');

		try
		{
			$query->clear()
				->select('a.*, b.title AS plan_title, b.recurring_subscription, b.number_payments, c.username')
				->select('IF(b.send_first_reminder > 0, DATEDIFF(to_date, NOW()), DATEDIFF(NOW(), to_date)) AS number_days')
				->from('#__osmembership_subscribers AS a')
				->innerJoin('#__osmembership_plans AS b  ON a.plan_id = b.id')
				->leftJoin('#__users AS c  ON a.user_id = c.id')
				->where('b.send_first_reminder != 0')
				->where('b.lifetime_membership != 1')
				->where('a.published IN (1, 2)')
				->where('a.first_reminder_sent = 0')
				->where('a.group_admin_id = 0')
				->where('b.send_first_reminder != 0')
				->where('IF(b.send_first_reminder > 0, b.send_first_reminder >= DATEDIFF(to_date, NOW()) AND DATEDIFF(to_date, NOW()) >= 0, DATEDIFF(NOW(), to_date) >= ABS(b.send_first_reminder) AND DATEDIFF(NOW(), to_date) <= 60)')
				->order('a.to_date');
			$db->setQuery($query, 0, $numberEmailSendEachTime);

			try
			{
				$rows = $db->loadObjectList();

				if (!empty($rows))
				{
					$reminderType = 'first_reminder';
					$this->app->triggerEvent('onBeforeSendingReminderEmails', [$rows, $reminderType]);
					OSMembershipHelper::callOverridableHelperMethod('Mail', 'sendReminderEmails', [$rows, $bccEmail, 1]);
				}
			}
			catch (Exception $e)
			{

			}

			$query->clear()
				->select('a.*, b.title AS plan_title, b.recurring_subscription, b.number_payments, c.username')
				->select('IF(b.send_second_reminder > 0, DATEDIFF(to_date, NOW()), DATEDIFF(NOW(), to_date)) AS number_days')
				->from('#__osmembership_subscribers AS a')
				->innerJoin('#__osmembership_plans AS b ON a.plan_id = b.id')
				->leftJoin('#__users AS c  ON a.user_id = c.id')
				->where('b.send_second_reminder != 0')
				->where('b.lifetime_membership != 1')
				->where('a.published IN (1, 2)')
				->where('a.second_reminder_sent = 0')
				->where('a.group_admin_id = 0')
				->where('b.send_second_reminder != 0')
				->where('IF(b.send_second_reminder > 0, b.send_second_reminder >= DATEDIFF(to_date, NOW()) AND DATEDIFF(to_date, NOW()) >= 0, DATEDIFF(NOW(), to_date) >= ABS(b.send_second_reminder) AND DATEDIFF(NOW(), to_date) <= 60)')
				->order('a.to_date');
			$db->setQuery($query, 0, $numberEmailSendEachTime);

			try
			{
				$rows = $db->loadObjectList();

				if (!empty($rows))
				{
					$reminderType = 'second_reminder';
					$this->app->triggerEvent('onBeforeSendingReminderEmails', [$rows, $reminderType]);
					OSMembershipHelper::callOverridableHelperMethod('Mail', 'sendReminderEmails', [$rows, $bccEmail, 2]);
				}
			}
			catch (Exception $e)
			{

			}

			$query->clear()
				->select('a.*, b.title AS plan_title, b.recurring_subscription, b.number_payments, c.username')
				->select('IF(b.send_third_reminder > 0, DATEDIFF(to_date, NOW()), DATEDIFF(NOW(), to_date)) AS number_days')
				->from('#__osmembership_subscribers AS a')
				->innerJoin('#__osmembership_plans AS b ON a.plan_id = b.id')
				->leftJoin('#__users AS c  ON a.user_id = c.id')
				->where('b.send_third_reminder != 0')
				->where('b.lifetime_membership != 1')
				->where('a.published IN (1, 2)')
				->where('a.third_reminder_sent = 0')
				->where('a.group_admin_id = 0')
				->where('b.send_third_reminder != 0')
				->where('IF(b.send_third_reminder > 0, b.send_third_reminder >= DATEDIFF(to_date, NOW()) AND DATEDIFF(to_date, NOW()) >= 0, DATEDIFF(NOW(), to_date) >= ABS(b.send_third_reminder) AND DATEDIFF(NOW(), to_date) <= 60 )')
				->order('a.to_date');
			$db->setQuery($query, 0, $numberEmailSendEachTime);

			try
			{
				$rows = $db->loadObjectList();

				if (!empty($rows))
				{
					$reminderType = 'third_reminder';
					$this->app->triggerEvent('onBeforeSendingReminderEmails', [$rows, $reminderType]);
					OSMembershipHelper::callOverridableHelperMethod('Mail', 'sendReminderEmails', [$rows, $bccEmail, 3]);
				}
			}
			catch (Exception $e)
			{

			}

			if (empty($message->subscription_end_email_subject))
			{
				return;
			}

			// Subscription end
			$query->clear()
				->select('a.*, b.title AS plan_title, b.recurring_subscription, b.number_payments, c.username')
				->select('IF(b.send_subscription_end > 0, DATEDIFF(to_date, NOW()), DATEDIFF(NOW(), to_date)) AS number_days')
				->from('#__osmembership_subscribers AS a')
				->innerJoin('#__osmembership_plans AS b ON a.plan_id = b.id')
				->leftJoin('#__users AS c  ON a.user_id = c.id')
				->where('b.send_subscription_end != 0')
				->where('b.recurring_subscription = 1')
				->where('b.number_payments > 0')
				->where('a.published IN (1, 2)')
				->where('a.subscription_end_sent = 0')
				->where('a.group_admin_id = 0')
				->where('a.payment_made = b.number_payments')
				->where('IF(b.send_subscription_end > 0, b.send_subscription_end >= DATEDIFF(to_date, NOW()) AND DATEDIFF(to_date, NOW()) >= 0, DATEDIFF(NOW(), to_date) >= ABS(b.send_subscription_end) AND DATEDIFF(NOW(), to_date) <= 60 )')
				->order('a.to_date');
			$db->setQuery($query, 0, $numberEmailSendEachTime);

			try
			{
				$rows = $db->loadObjectList();

				if (!empty($rows))
				{
					OSMembershipHelper::callOverridableHelperMethod('Mail', 'sendSubscriptionEndEmails', [$rows, $bccEmail]);
				}
			}
			catch (Exception $e)
			{

			}
		}
		catch (Exception $e)
		{
			// Ignore
		}

		return true;
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

		// If trigger reminder code is set, we will only process sending reminder from cron job
		if (trim($this->params->get('trigger_reminder_code', ''))
			&& trim($this->params->get('trigger_reminder_code', '')) != $this->app->input->getString('trigger_reminder_code'))
		{
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

		$lastRun   = (int) $this->params->get('last_run', 0);
		$now       = time();
		$cacheTime = (int) $this->params->get('cache_time', 2) * 3600;

		if (($now - $lastRun) < $cacheTime)
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
	 * @since   2.0.0
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
