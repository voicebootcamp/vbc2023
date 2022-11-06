<?php
/**
 * @version               1.22.0
 * @package               Joomla
 * @subpackage            OS Services Booking
 * @author                Tuan Pham Ngoc
 * @copyright             Copyright (C) 2012 - 2022 Ossolution Team
 * @license               GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;
use Joomla\CMS\Date\Date;
class plgSystemOsbgcalendarsync extends JPlugin
{
	public function onAfterRender()
	{
		if (file_exists(JPATH_ROOT . '/components/com_osservicesbooking/osservicesbooking.php'))
		{
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			$lastRun   = (int) $this->params->get('last_run', 0);
			$now       = time();
			$cacheTime = 86400; // 600 minutes
			if (($now - $lastRun) < $cacheTime)
			{
				return;
			}

			//Store last run time
			$db          = JFactory::getDbo();
			$query       = $db->getQuery(true);
			$insertQuery = $db->getQuery(true);
			$this->params->set('last_run', $now);
			$params = $this->params->toString();
			$query->clear();
			$query->update('#__extensions')
				->set('params=' . $db->quote($params))
				->where('`element`="osbgcalendarsync"')
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
				$this->clearCacheGroups(array('com_plugins'), array(0, 1));
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
			

			//run to find corresponding order items and send Emails & SMS to customers
			include_once (JPATH_ROOT.'/components/com_osservicesbooking/classes/default.php');
			include_once (JPATH_ROOT.'/components/com_osservicesbooking/helpers/common.php');
			include_once (JPATH_ROOT.'/administrator/components/com_osservicesbooking/helpers/helper.php');

			$configClass = OSBHelper::loadConfig();
			$current_time = HelperOSappscheduleCommon::getRealTime();

			if($configClass['integrate_gcalendar'] == 1 && JFile::exists(JPATH_ROOT."/libraries/osgcalendar/src/Google/Client.php"))
			{
				$db = JFactory::getDbo();
				$db->setQuery("Select count(id) from #__app_sch_employee where published = '1' and id not in (Select eid from #__app_sch_gcalendarsync) and app_email_address <> '' and p12_key_filename <> '' and client_id <> ''");
				$count				= $db->loadResult();
				if($count == 0)
				{
					$db->setQuery("Delete from #__app_sch_gcalendarsync");
					$db->execute();
				}
				$db->setQuery("Select * from #__app_sch_employee where published = '1' and id not in (Select eid from #__app_sch_gcalendarsync) and app_email_address <> '' and p12_key_filename <> '' and client_id <> '' order by ordering limit 1");
				$employee			= $db->loadObject();

				$client_id			= $employee->client_id;
				$app_name			= $employee->app_name;
				$app_email_address	= $employee->app_email_address;
				$p12_key_filename	= $employee->p12_key_filename;
				$gcalendarid		= $employee->gcalendarid;
				$path				= JPATH_ROOT."/libraries/osgcalendar/src/Google";
				set_include_path(get_include_path() . PATH_SEPARATOR . $path);

				if(!file_exists ( $path.'/Client.php' ) || !file_exists ( $path.'/Service.php' ))
				{
					echo "OSB set to use Google Calendar but the Google Library is not installed.";
					exit;
				}	
				require_once $path."/Client.php";
				require_once $path."/Service.php";
				
				try {
					$client = new Google_Client();
					$client->setApplicationName($app_name);
					$client->setClientId($client_id);
					$client->setAssertionCredentials( 
						new Google_Auth_AssertionCredentials(
							$app_email_address,
							array("https://www.googleapis.com/auth/calendar"),
							file_get_contents(JPATH_ROOT.'/components/com_osservicesbooking/'.$p12_key_filename),
							'notasecret','http://oauth.net/grant_type/jwt/1.0/bearer',false,false
						)
					);
				}
				catch (RuntimeException $e) {
					return 'Problem authenticating Google Calendar:'.$e->getMessage();
				}

				$config		= JFactory::getConfig();
				$date		= JFactory::getDate('now', $config->get('offset'));
				$date->setDate($date->year, 1, 1);
				$date->setTime(0, 0, 0);

				$date1     = JFactory::getDate('now', $config->get('offset'));
                $date1->setDate($date1->year, 12, 31);
                $date1->setTime(23, 59, 59);

				$optParams = array(
					'maxResults' => 10,
					'orderBy' => 'startTime',
					'singleEvents' => TRUE,
					'timeMin' => $date->format('c'),
					'timeMax' => $date1->format('c'),
				  );

				try {
					$service  = new Google_Service_Calendar($client);		
					$results  = $service->events->listEvents($gcalendarid, $optParams);
					$events	  = $results->getItems();
					$db->setQuery("Delete from #__app_sch_employee_busy_time where eid = '$employee->id' and feed_from_google = '1' ");
					$db->execute();
					foreach ($events as $event) 
					{
						$start  = $event->start->dateTime;
						if (empty($start)) 
						{
							$start = $event->start->date;
						}

						$end  = $event->end->dateTime;
						if (empty($end)) 
						{
							$end = $event->end->date;
						}
						$dateStart	= new DateTime($start);
						$dateStart->setTimezone(new DateTimeZone($config->get('offset')));
						$dateEnd	= new DateTime($end);
						$dateEnd->setTimezone(new DateTimeZone($config->get('offset')));
						
						$busydate	= $dateStart->format("Y-m-d");
						$busy_from	= $dateStart->format("H:i");

						$busy_to	= $dateEnd->format("H:i");

						$db->setQuery("Select count(id) from #__app_sch_employee_busy_time where eid = '$employee->id' and busy_date = '$busydate' and busy_from = '$busy_from' and busy_to = '$busy_to'");
						$count		= $db->loadResult();
						if($count == 0)
						{
							$db->setQuery("Insert into #__app_sch_employee_busy_time (id, eid, busy_date, busy_from, busy_to, feed_from_google) values (NULL,'$employee->id', '$busydate', '$busy_from', '$busy_to', 1)");
							$db->execute();
						}
					}
					$db->setQuery("Insert into #__app_sch_gcalendarsync (id, eid) values (NULL, '$employee->id')");
					$db->execute();
				} 
				catch (Google_ServiceException $e) 
				{
					echo $e->getMessage();
					exit;
				}
			}	
		}

		return true;
	}

	/**
	 * Clears cache groups. We use it to clear the plugins cache after we update the last run timestamp.
	 *
	 * @param   array $clearGroups  The cache groups to clean
	 * @param   array $cacheClients The cache clients (site, admin) to clean
	 *
	 * @return  void
	 *
	 * @since   1.6.8
	 */
	private function clearCacheGroups(array $clearGroups, array $cacheClients = array(0, 1))
	{
		$conf = JFactory::getConfig();
		foreach ($clearGroups as $group)
		{
			foreach ($cacheClients as $client_id)
			{
				try
				{
					$options = array(
						'defaultgroup' => $group,
						'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' :
							$conf->get('cache_path', JPATH_SITE . '/cache')
					);
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
