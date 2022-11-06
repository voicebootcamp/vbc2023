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
use Joomla\CMS\Uri\Uri;

class plgOSMembershipRegistrationhistory extends CMSPlugin
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
	 * Plugin constructor.
	 *
	 * @param   object  $subject
	 * @param   array   $config
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(& $subject, $config)
	{
		if (!file_exists(JPATH_ROOT . '/components/com_eventbooking/eventbooking.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Render setting form
	 *
	 * @param   JTable  $row
	 *
	 * @return array
	 */
	public function onProfileDisplay($row)
	{
		if (!$this->app)
		{
			return ;
		}

		if ($this->app->isClient('administrator'))
		{
			return;
		}

		ob_start();
		$this->drawRegistrationHistory($row);

		return ['title' => Text::_('EB_REGISTRATION_HISTORY'),
		        'form'  => ob_get_clean(),
		];
	}

	/**
	 * Display registration history of the current logged in user
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	private function drawRegistrationHistory($row)
	{
		// Require libraries
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

		EventbookingHelper::loadLanguage();

		JLoader::register('EventbookingModelHistory', JPATH_ROOT . '/components/com_eventbooking/model/history.php');

		/* @var EventbookingModelHistory $model */
		$model = RADModel::getInstance('History', 'EventbookingModel', [
			'table_prefix'    => '#__eb_',
			'remember_states' => false,
			'ignore_request'  => true,
		]);

		$model->setUserId($row->user_id);

		$items = $model->setState('limitstart', 0)
			->setState('limit', 0)
			->getData();

		if (empty($items))
		{
			return;
		}

		$config = EventbookingHelper::getConfig();

		$showDownloadCertificate = false;
		$showDownloadTicket      = false;
		$showDueAmountColumn     = false;

		$numberPaymentMethods = EventbookingHelper::getNumberNoneOfflinePaymentMethods();

		if ($numberPaymentMethods > 0)
		{
			foreach ($items as $item)
			{
				if ($item->payment_status != 1)
				{
					$showDueAmountColumn = true;
					break;
				}
			}
		}

		foreach ($items as $item)
		{
			$item->show_download_certificate = false;

			if ($item->published == 1 && $item->activate_certificate_feature == 1
				&& $item->event_end_date_minutes >= 0
				&& (!$config->download_certificate_if_checked_in || $item->checked_in)
			)
			{
				$showDownloadCertificate         = true;
				$item->show_download_certificate = true;
			}

			if ($item->ticket_code && $item->payment_status == 1)
			{
				$showDownloadTicket = true;
			}
		}

		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('id')
			->from('#__eb_payment_plugins')
			->where('published = 1')
			->where('name NOT LIKE "os_offline%"');
		$db->setQuery($query);
		$onlinePaymentPlugins = $db->loadColumn();

		if (in_array('last_name', EventbookingHelper::getPublishedCoreFields()))
		{
			$showLastName = true;
		}
		else
		{
			$showLastName = false;
		}

		$return = base64_encode(Uri::getInstance()->toString());

		require PluginHelper::getLayoutPath('osmembership', 'registrationhistory', 'default');
	}
}
