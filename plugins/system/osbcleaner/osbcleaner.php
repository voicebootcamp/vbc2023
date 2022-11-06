<?php
/**
 * @package        Joomla
 * @subpackage     OS Services Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2020 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

/**
 * OS Services Booking cleaner Plugin
 *
 * @package        Joomla
 * @subpackage     OS Services Booking
 */
class plgSystemOSBCleaner extends JPlugin
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
	 * Whether the plugin should be run when events are triggered
	 *
	 * @var bool
	 */
	protected $canRun;

	/**
	 * Constructor
	 *
	 * @param object &$subject The object to observe
	 * @param array   $config  An optional associative array of configuration settings.
	 */
	public function __construct($subject, array $config = [])
	{
		parent::__construct($subject, $config);

		$this->canRun = file_exists(JPATH_ADMINISTRATOR . '/components/com_osservicesbooking/osservicesbooking.php');
	}

	/**
	 * Clean up incomplete payment subscriptions
	 *
	 * @return bool|void
	 */
	public function onAfterRender()
	{
		if (!$this->canRun)
		{
			return;
		}

		$secretCode = trim($this->params->get('secret_code'));

		if ($secretCode && ($this->app->input->getString('secret_code') != $secretCode))
		{
			return;
		}


		$lastRun    = (int) $this->params->get('last_run', 0);
		$numberDays = (int) $this->params->get('number_days', 30) ?: 30;
		$now        = time();
		$cacheTime  = 3600 * (int) $this->params->get('cache_time', 24); // The cleaner process will be run every 1 days

		if (($now - $lastRun) < $cacheTime)
		{
			return;
		}

		//Store last run time
		$query = $this->db->getQuery(true);
		$this->params->set('last_run', $now);
		$params = $this->params->toString();
		$query->clear();
		$query->update('#__extensions')
			->set('params=' . $this->db->quote($params))
			->where('`element`="osbcleaner"')
			->where('`folder`="system"');

		try
		{
			// Lock the tables to prevent multiple plugin executions causing a race condition
			$this->db->lockTable('#__extensions');
		}
		catch (Exception $e)
		{
			// If we can't lock the tables it's too risk continuing execution
			return;
		}

		try
		{
			// Update the plugin parameters
			$result = $this->db->setQuery($query)->execute();
			$this->clearCacheGroups(['com_plugins'], [0, 1]);
		}
		catch (Exception $exc)
		{
			// If we failed to execite
			$this->db->unlockTables();
			$result = false;
		}
		try
		{
			// Unlock the tables after writing
			$this->db->unlockTables();
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

		$now = JFactory::getDate()->toSql();
		$query = "Select id from #__app_sch_orders where order_status like 'P' and `order_payment` NOT LIKE 'os_offline%' and `order_payment` <> '' and DATEDIFF('$now', order_date) >= ".$numberDays;
		$this->db->setQuery($query);
		$orderIds = $db->loadColumn(0);
		if(count($orderIds) > 0)
		{
			$query = "Delete from #__app_sch_orders where id in (".implode(',', $orderIds).")";
			$this->db->setQuery($query);
			$this->db->execute();

			$query = "Delete from #__app_sch_order_items where order_id in (".implode(',', $orderIds).")";
			$this->db->setQuery($query);
			$this->db->execute();
		}

		return true;
	}

	/**
	 * Clears cache groups. We use it to clear the plugins cache after we update the last run timestamp.
	 *
	 * @param array $clearGroups  The cache groups to clean
	 * @param array $cacheClients The cache clients (site, admin) to clean
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	private function clearCacheGroups(array $clearGroups, array $cacheClients = [0, 1])
	{
		$conf = JFactory::getConfig();

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
