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

class plgSystemOsbcrontask extends JPlugin
{
	public function onAfterRender()
	{
		if (file_exists(JPATH_ROOT . '/components/com_osservicesbooking/osservicesbooking.php'))
		{
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			$lastRun   = (int) $this->params->get('last_run', 0);
			$now       = time();
			$cacheTime = 600; // 600 minutes
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
				->where('`element`="osbcrontask"')
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
			include_once (JPATH_ROOT.'/components/com_osservicesbooking/helpers/ics.php');
			include_once (JPATH_ROOT.'/administrator/components/com_osservicesbooking/helpers/helper.php');
			global $configClass;
			$configClass = OSBHelper::loadConfig();
			$current_time = HelperOSappscheduleCommon::getRealTime();

			if($configClass['value_sch_reminder_enable'] == 1)
			{
				if($configClass['enable_reminder'] == 1)
				{
					$extraSql = " and b.receive_reminder = '1' ";
				}
				else
				{
					$extraSql = "";
				}
				$reminder = $configClass['value_sch_reminder_email_before'];
				$reminder = $current_time + $reminder*3600;
				$query = "Select a.* from #__app_sch_order_items as a"
						." inner join #__app_sch_orders as b on b.id = a.order_id"
						." where a.start_time <= '$reminder' and a.start_time > '$current_time' $extraSql and b.order_status = 'S' and a.id not in (Select order_item_id from #__app_sch_cron) and a.start_time > 0 and a.end_time > 0 order by a.start_time limit 1";
				$db->setQuery($query);
				//echo $db->getQuery();die();
				$rows = $db->loadObjectList();
				//print_r($rows);die();
				if(count($rows) > 0)
				{
					for($i=0;$i<count($rows);$i++)
					{
						$row = $rows[$i];
						if($row->start_time != "" && $row->end_time != "" && $row->sid > 0 && $row->eid > 0)
						{
							HelperOSappscheduleCommon::sendEmail('reminder',$row->id);
							HelperOSappscheduleCommon::sendSMS('reminder',$row->order_id, $row->id);
							//add into the cron table
							$db->setQuery("Insert into #__app_sch_cron (id,order_item_id) values (NULL,'$row->id')");
							$db->execute();
						}
					}
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
