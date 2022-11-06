<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class com_eventbookingInstallerScript
{
	/**
	 * Language files
	 *
	 * @var array
	 */
	public static $languageFiles = ['en-GB.com_eventbooking.ini', 'admin.en-GB.com_eventbookingcommon.ini'];

	/**
	 * The original version, use for update process
	 *
	 * @var string
	 */
	protected $installedVersion = '3.7.0';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Get and store current installed version
		$this->getInstalledVersion();
	}

	/**
	 * Method to run before installing the component
	 */
	public function preflight($type, $parent)
	{
		if (version_compare($this->installedVersion, '3.7.0', '<'))
		{
			Factory::getApplication()->enqueueMessage('Update from older version than 3.7.0 is not supported! You need to update to version 3.17.6 first. Please contact support to get that old Events Booking version', 'error');

			return false;
		}

		// If this is new install, we don't have to do anything
		if (strtolower($type) != 'update')
		{
			return true;
		}

		$this->deleteFilesFolders();

		// Allow custom translation for backend language file from 4.0.2
		if (version_compare($this->installedVersion, '4.0.2', '>=') && !in_array('admin.en-GB.com_eventbooking.ini', self::$languageFiles))
		{
			self::$languageFiles[] = 'admin.en-GB.com_eventbooking.ini';
		}

		//Backup the old language files
		foreach (self::$languageFiles as $languageFile)
		{
			if (strpos($languageFile, 'admin') !== false)
			{
				$languageFolder = JPATH_ADMINISTRATOR . '/language/en-GB/';
				$languageFile   = substr($languageFile, 6);
			}
			else
			{
				$languageFolder = JPATH_ROOT . '/language/en-GB/';
			}

			if (File::exists($languageFolder . $languageFile))
			{
				File::copy($languageFolder . $languageFile, $languageFolder . 'bak.' . $languageFile);
			}
		}

		//Backup even custom fields
		if (File::exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			File::copy(JPATH_ROOT . '/components/com_eventbooking/fields.xml', JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml');
		}
	}

	/**
	 * Method to run after installing the component
	 */
	public function postflight($type, $parent)
	{
		// We do not have to do anything on uninstall
		if (strtolower($type) == 'uninstall')
		{
			return;
		}

		// Force invalidation helper file so new method is available
		if (function_exists('opcache_invalidate'))
		{
			opcache_invalidate(JPATH_ROOT . '/components/com_eventbooking/helper/helper.php', true);
		}

		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

		//Restore the modified language files + event custom fields file
		$this->restoreFiles();

		// Create needed files and folders
		$this->createFilesFolders();

		if (strtolower($type) == 'update')
		{
			// Create new tables if not exist
			static::createTablesIfNotExist();

			// Synchronize db schema to latest version
			$this->updateDBSchema();

			// Migrate menu items
			$this->migrateMenuItems();

			// Migrate tax rules
			$this->migrateTaxRules();

			// Migrate common language items when upgrading from version smaller than or equal 4.0.0
			$this->migrateCommonLanguageItems();
		}

		// Delete old update site
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__update_sites')
			->where('location = ' . $db->quote('https://www.joomdonation.com/updates/eventsbooking.xml'));
		$db->setQuery($query)
			->execute();

		// Setup default data
		self::setupDefaultData();

		// Enable required plugins
		self::enableRequiredPlugin();

		if (Multilanguage::isEnabled())
		{
			EventbookingHelper::callOverridableHelperMethod('Helper', 'setupMultilingual');
		}
	}

	/**
	 * Restore the files which were changed during installation process
	 *
	 */
	private function restoreFiles()
	{
		// Allow custom translation for backend language file from 4.0.2
		if (version_compare($this->installedVersion, '4.0.2', '>=') && !in_array('admin.en-GB.com_eventbooking.ini', self::$languageFiles))
		{
			self::$languageFiles[] = 'admin.en-GB.com_eventbooking.ini';
		}

		//Restore the modified language strings by merging to language files
		foreach (self::$languageFiles as $languageFile)
		{
			$registry = new Registry;

			if (strpos($languageFile, 'admin') !== false)
			{
				$languageFolder = JPATH_ADMINISTRATOR . '/language/en-GB/';
				$languageFile   = substr($languageFile, 6);
			}
			else
			{
				$languageFolder = JPATH_ROOT . '/language/en-GB/';
			}

			$backupFile  = $languageFolder . 'bak.' . $languageFile;
			$currentFile = $languageFolder . $languageFile;

			if (File::exists($currentFile) && File::exists($backupFile))
			{
				$registry->loadFile($currentFile, 'INI');
				$currentItems = $registry->toArray();
				$registry->loadFile($backupFile, 'INI');
				$backupItems = $registry->toArray();
				$items       = array_merge($currentItems, $backupItems);

				LanguageHelper::saveToIniFile($currentFile, $items);
			}
		}

		// Update English admin language files
		$updateLanguageItems = require JPATH_ADMINISTRATOR . '/components/com_eventbooking/updates/items.php';

		if (count($updateLanguageItems))
		{
			$saveRequired      = false;
			$adminLanguageFile = JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_eventbooking.ini';
			$registry          = new Registry;
			$registry->loadFile($adminLanguageFile, 'INI');
			$items = $registry->toArray();

			foreach ($updateLanguageItems as $version => $itemsToUpdate)
			{
				if (version_compare($this->installedVersion, $version, '<='))
				{
					$items        = array_merge($items, $itemsToUpdate);
					$saveRequired = true;
				}
			}

			if ($saveRequired)
			{
				LanguageHelper::saveToIniFile($adminLanguageFile, $items);
			}
		}

		//Restore the renamed files
		if (File::exists(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml'))
		{
			File::copy(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml', JPATH_ROOT . '/components/com_eventbooking/fields.xml');
			File::delete(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml');
		}
	}

	/**
	 * Create necessary files and folders
	 */
	private function createFilesFolders()
	{
		// Create custom css file if it does not exist
		$customCss = JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css';

		if (!file_exists($customCss))
		{
			$fp = fopen($customCss, 'w');
			fclose($fp);
			@chmod($customCss, 0644);
		}
		else
		{
			@chmod($customCss, 0644);
		}

		$foldersToCreate = [];

		if (version_compare($this->installedVersion, '3.10.5', '<'))
		{
			$foldersToCreate = [
				JPATH_ROOT . '/images/com_eventbooking',
				JPATH_ROOT . '/images/com_eventbooking/categories',
				JPATH_ROOT . '/images/com_eventbooking/categories/thumb',
				JPATH_ROOT . '/images/com_eventbooking/galleries/thumbs',
				JPATH_ROOT . '/images/com_eventbooking/speakers',
				JPATH_ROOT . '/images/com_eventbooking/speakers/thumbs',
				JPATH_ROOT . '/images/com_eventbooking/sponsors',
				JPATH_ROOT . '/images/com_eventbooking/speakers/thumbs',
				JPATH_ROOT . '/images/com_eventbooking/sponsors',
				JPATH_ROOT . '/images/com_eventbooking/sponsors/thumbs',
				JPATH_ROOT . '/images/com_eventbooking/galleries',
				JPATH_ROOT . '/images/com_eventbooking/galleries/thumbs',
			];
		}

		foreach ($foldersToCreate as $folder)
		{
			if (!Folder::exists($folder))
			{
				Folder::create($folder);
			}
		}
	}

	/**
	 *  Delete files/folders which were using on old version but not needed on new version anymore
	 */
	private function deleteFilesFolders()
	{
		$deleteFiles   = [];
		$deleteFolders = [];

		if (version_compare($this->installedVersion, '3.8.3', '<'))
		{
			$deleteFiles = [
				// CSS files
				JPATH_ROOT . '/components/com_eventbooking/assets/css/default.css',
				JPATH_ROOT . '/components/com_eventbooking/assets/css/fire.css',
				JPATH_ROOT . '/components/com_eventbooking/assets/css/leaf.css',
				JPATH_ROOT . '/components/com_eventbooking/assets/css/ocean.css',
				JPATH_ROOT . '/components/com_eventbooking/assets/css/sky.css',
				JPATH_ROOT . '/components/com_eventbooking/assets/css/tree.css',
			];

			$deleteFolders = [
				JPATH_ROOT . '/components/com_eventbooking/views',
				JPATH_ROOT . '/components/com_eventbooking/view/common',
				JPATH_ROOT . '/components/com_eventbooking/emailtemplates',
				JPATH_ROOT . '/administrator/components/com_eventbooking/controller',
			];
		}

		if (version_compare($this->installedVersion, '3.11.0', '<'))
		{
			$deleteFiles[] = JPATH_ROOT . '/administrator/components/com_eventbooking/elements/ebcurrency';
		}

		if (version_compare($this->installedVersion, '4.0.0', '<'))
		{
			$deleteFolders[] = JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/vendor/serbanghita';
			$deleteFolders[] = JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/vendor/spatie';
			$deleteFolders[] = JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/vendor/valitron';
			$deleteFolders[] = JPATH_ROOT . '/components/com_eventbooking/view/archive';
			$deleteFolders[] = JPATH_ROOT . '/components/com_eventbooking/themes/default/archive';

			$deleteFiles[] = JPATH_ROOT . '/components/com_eventbooking/model/archive.php';
		}

		// If there are more files need to be deleted on new versions, it will need to be added to $deleteFiles and $deleteFolders array

		foreach ($deleteFiles as $file)
		{
			if (File::exists($file))
			{
				File::delete($file);
			}
		}

		foreach ($deleteFolders as $folder)
		{
			if (Folder::exists($folder))
			{
				Folder::delete($folder);
			}
		}
	}

	/**
	 * Create new tables if not exist during update
	 */
	public static function createTablesIfNotExist()
	{
		$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/createifnotexists.eventbooking.sql';

		EventbookingHelper::executeSqlFile($sqlFile);
	}

	/*
	 * Proxy method for static method synchronizeDBSchema
	 *
	 * @return void
	 */
	private function updateDBSchema()
	{
		self::synchronizeDBSchema($this->installedVersion);
	}

	/**
	 * Synchronize db schema with latest version
	 *
	 * @param   string  $installedVersion
	 */
	public static function synchronizeDBSchema($installedVersion = '2.0.0')
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Change row format for some tables
		if (version_compare($installedVersion, '3.13.0', '<='))
		{
			$tables = [
				'#__eb_categories',
				'#__eb_events',
				'#__eb_fields',
				'#__eb_locations',
				'#__eb_registrants',
			];

			try
			{
				foreach ($tables as $table)
				{
					$sql = "ALTER TABLE `$table` ROW_FORMAT = DYNAMIC";
					$db->setQuery($sql)
						->execute();
				}
			}
			catch (Exception $e)
			{

			}
		}

		// Categories table
		$fields = array_keys($db->getTableColumns('#__eb_categories'));

		if (!in_array('tax_rate', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_categories` ADD `tax_rate` decimal(10,2) DEFAULT 0.00;";
			$db->setQuery($sql)
				->execute();
		}

		$varcharFields = [
			'paypal_email',
			'notification_emails',
			'payment_methods',
			'reminder_email_subject',
			'second_reminder_email_subject',
			'third_reminder_email_subject',
		];

		foreach ($varcharFields as $varcharField)
		{
			if (!in_array($varcharField, $fields))
			{
				$sql = "ALTER TABLE  `#__eb_categories` ADD `$varcharField` varchar(255) NOT NULL DEFAULT '';";
				$db->setQuery($sql)
					->execute();
			}
		}

		$textFields = [
			'admin_email_body',
			'user_email_body',
			'user_email_body_offline',
			'group_member_email_body',
			'thanks_message',
			'thanks_message_offline',
			'registration_approved_email_body',
			'reminder_email_body',
			'second_reminder_email_body',
			'third_reminder_email_body'
		];

		foreach ($textFields as $textField)
		{
			if (!in_array($textField, $fields))
			{
				$sql = "ALTER TABLE  `#__eb_categories` ADD `$textField` text;";
				$db->setQuery($sql)
					->execute();
			}
		}

		// Events table
		$fields = array_keys($db->getTableColumns('#__eb_events'));

		if (!in_array('tax_rate', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `tax_rate` decimal(10,2) DEFAULT 0.00;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('reply_to_email', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `reply_to_email` VARCHAR(150) DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('registrants_emailed', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `registrants_emailed` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('registration_complete_url', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `registration_complete_url` TEXT NULL ;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('offline_payment_registration_complete_url', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `offline_payment_registration_complete_url` TEXT NULL ;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('registrant_edit_close_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `registrant_edit_close_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('admin_email_body', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `admin_email_body` TEXT;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('hidden', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `hidden` TINYINT(4) NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('created_language', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `created_language` VARCHAR(50) DEFAULT '*';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('group_member_email_body', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `group_member_email_body` TEXT NULL;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('enable_sms_reminder', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `enable_sms_reminder` tinyint(3) UNSIGNED DEFAULT 0;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('waiting_list_capacity', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `waiting_list_capacity` INT UNSIGNED DEFAULT 0;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('first_reminder_frequency', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `first_reminder_frequency` CHAR(1) DEFAULT 'd';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('second_reminder_frequency', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `second_reminder_frequency` CHAR(1) DEFAULT 'd';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('created_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('reminder_email_subject', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `reminder_email_subject` VARCHAR(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('second_reminder_email_subject', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `second_reminder_email_subject` VARCHAR(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('send_third_reminder', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `send_third_reminder` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('third_reminder_frequency', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `third_reminder_frequency` CHAR(1) DEFAULT 'd';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('third_reminder_email_subject', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `third_reminder_email_subject` VARCHAR(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('third_reminder_email_body', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD  `third_reminder_email_body` TEXT NULL;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('deposit_until_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_events` ADD `deposit_until_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($sql)
				->execute();
		}

		// Registrants table
		$fields = array_keys($db->getTableColumns('#__eb_registrants'));

		if (!in_array('created_by', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `created_by` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql)
				->execute();

			$sql = 'UPDATE #__eb_registrants SET `created_by` = `user_id`';
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('registration_cancel_date', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `registration_cancel_date` datetime";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('invoice_year', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `invoice_year` INT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql)
				->execute();

			$sql = 'UPDATE #__eb_registrants SET `invoice_year` = YEAR(`register_date`)';
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('is_offline_payment_reminder_sent', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `is_offline_payment_reminder_sent` TINYINT NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('certificate_sent', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `certificate_sent` TINYINT NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('refunded', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `refunded` TINYINT NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('tax_rate', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `tax_rate` DECIMAL( 10, 2 ) NULL DEFAULT  '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('formatted_invoice_number', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `formatted_invoice_number` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('first_sms_reminder_sent', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `first_sms_reminder_sent` TINYINT(4) NOT NULL DEFAULT 0;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('second_sms_reminder_sent', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `second_sms_reminder_sent` TINYINT(4) NOT NULL DEFAULT 0;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('is_third_reminder_sent', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `is_third_reminder_sent` TINYINT NOT NULL DEFAULT  '0';";
			$db->setQuery($sql)
				->execute();
		}

		//Ticket Types table
		$fields = array_keys($db->getTableColumns('#__eb_ticket_types'));

		if (!in_array('publish_up', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_ticket_types` ADD  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('publish_down', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_ticket_types` ADD  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('ordering', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_ticket_types` ADD  `ordering` INT NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();

			$sql = 'UPDATE `#__eb_ticket_types` SET `ordering` = `id`';
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('discount_rules', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_ticket_types` ADD  `discount_rules` TEXT NULL;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('access', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_ticket_types` ADD  `access` INT NOT NULL DEFAULT '1';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('weight', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_ticket_types` ADD  `weight` INT NOT NULL DEFAULT '1';";
			$db->setQuery($sql)
				->execute();
		}

		// Coupons table
		$fields = array_keys($db->getTableColumns('#__eb_coupons'));

		if (!in_array('min_number_registrants', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_coupons` ADD  `min_number_registrants` INT NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('max_number_registrants', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_coupons` ADD  `max_number_registrants` INT NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('note', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_coupons` ADD  `note` VARCHAR( 50 ) NULL DEFAULT  NULL;";
			$db->setQuery($sql)
				->execute();
		}

		// Fields table
		$fields = array_keys($db->getTableColumns('#__eb_fields'));

		if (!in_array('payment_method', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `payment_method` varchar(255) DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('input_mask', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `input_mask` varchar(255) DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('readonly', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `readonly` TINYINT NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('show_on_registration_type', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `show_on_registration_type` TINYINT NOT NULL DEFAULT '0';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('populate_from_previous_registration', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `populate_from_previous_registration` TINYINT NOT NULL DEFAULT '1';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('taxable', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD `taxable`  tinyint(3) UNSIGNED DEFAULT 1;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('position', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD `position` tinyint(3) UNSIGNED DEFAULT 0;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('container_size', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `container_size` VARCHAR(50) NOT NULL DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('input_size', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `input_size` VARCHAR(50) NOT NULL DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('prompt_text', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_fields` ADD  `prompt_text` VARCHAR(255) NOT NULL DEFAULT '';";
			$db->setQuery($sql)
				->execute();
		}

		// Ticket types table
		$fields = array_keys($db->getTableColumns('#__eb_urls'));

		if (!in_array('route', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_urls` ADD  `route` VARCHAR( 400 ) NULL DEFAULT  NULL;";
			$db->setQuery($sql)
				->execute();
		}

		// Discounts table
		$fields = array_keys($db->getTableColumns('#__eb_discounts'));

		if (!in_array('discount_type', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_discounts` ADD  `discount_type` TINYINT(4) NOT NULL DEFAULT 1;";
			$db->setQuery($sql)
				->execute();
		}

		if (!in_array('number_events', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_discounts` ADD  `number_events` INT NOT NULL DEFAULT  '0' ;";
			$db->setQuery($sql)
				->execute();
		}

		$fields = array_keys($db->getTableColumns('#__eb_taxes'));

		if (!in_array('category_id', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_taxes` ADD  `category_id` INT UNSIGNED DEFAULT 0 ;";
			$db->setQuery($sql)
				->execute();
		}

		$fields = array_keys($db->getTableColumns('#__eb_locations'));

		if (!in_array('gsd_venue_mapping', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_locations` ADD  `gsd_venue_mapping` VARCHAR(400) NULL;";
			$db->setQuery($sql)
				->execute();
		}

		// Change default value for datetime fields
		if (version_compare($installedVersion, '3.15.0', '<='))
		{
			$fieldsToChange = [
				'#__eb_coupons'      => ['valid_from', 'valid_to'],
				'#__eb_discounts'    => ['from_date', 'to_date'],
				'#__eb_emails'       => ['sent_at'],
				'#__eb_events'       => [
					'event_date',
					'event_end_date',
					'cut_off_date',
					'early_bird_discount_date',
					'cancel_before_date',
					'recurring_end_date',
					'registration_start_date',
					'max_end_date',
					'late_fee_date',
				],
				'#__eb_registrants'  => ['register_date', 'payment_date', 'checked_in_at', 'checked_out_at'],
				'#__eb_ticket_types' => ['publish_up', 'publish_down'],
			];

			try
			{
				$nullDate = $db->getNullDate();

				foreach ($fieldsToChange as $table => $fields)
				{
					foreach ($fields as $field)
					{
						$sql = "ALTER TABLE $table CHANGE $field $field datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
						$db->setQuery($sql)
							->execute();

						$query->clear()
							->update($table)
							->set($field . '=' . $db->quote($nullDate))
							->where($field . ' IS NULL');
						$db->setQuery($query)
							->execute();
					}
				}
			}
			catch (Exception $e)
			{

			}
		}

		if (version_compare($installedVersion, '3.15.1', '<='))
		{
			$sql = "ALTER TABLE  `#__eb_events` MODIFY  `thumb` VARCHAR( 255 ) NULL DEFAULT  NULL;";
			$db->setQuery($sql)
				->execute();
		}

		$fields = array_keys($db->getTableColumns('#__eb_countries'));

		if (!in_array('ordering', $fields))
		{
			$sql = "ALTER TABLE `#__eb_countries` ADD `ordering` INT UNSIGNED NOT NULL DEFAULT 0";
			$db->setQuery($sql)
				->execute();

			$query->clear()
				->select('id')
				->from('#__eb_countries')
				->order('name');
			$db->setQuery($query);

			$ordering = 0;

			foreach ($db->loadObjectList() as $row)
			{
				$query->clear()
					->update('#__eb_countries')
					->set('ordering = ' . $ordering)
					->where('id = ' . $row->id);
				$db->setQuery($query)
					->execute();
				$ordering++;
			}
		}

		$fields = array_keys($db->getTableColumns('#__eb_mitems'));

		if (!in_array('title_en', $fields))
		{
			$sql = "ALTER TABLE  `#__eb_mitems` ADD  `title_en` VARCHAR(400) NULL;";
			$db->setQuery($sql)
				->execute();

			$query->clear()
				->select('id, title')
				->from('#__eb_mitems');
			$db->setQuery($query);

			// Load backend default language file
			$registry = new Registry;
			$registry->loadFile(JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_eventbooking.ini');

			foreach ($db->loadObjectList() as $rowMessage)
			{
				$title = $rowMessage->title;

				if (!$title)
				{
					continue;
				}

				$query->clear()
					->update('#__eb_mitems')
					->set('title_en = ' . $db->quote(Text::_($registry->get($title))))
					->where('id = ' . $rowMessage->id);
				$db->setQuery($query)
					->execute();
			}
		}
	}

	/**
	 * Insert additional default data on upgrade
	 */
	public static function setupDefaultData()
	{
		$db = Factory::getDbo();

		// Setup default configuration
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__eb_configs');
		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/config.eventbooking.sql';
			EventbookingHelper::executeSqlFile($sqlFile);
		}

		// Custom fields
		$query->clear()
			->select('COUNT(*)')
			->from('#__eb_fields');
		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/fields.eventbooking.sql';
			EventbookingHelper::executeSqlFile($sqlFile);
		}

		// Setup default themes
		$query->clear()
			->select('COUNT(*)')
			->from('#__eb_themes');
		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/themes.eventbooking.sql';
			EventbookingHelper::executeSqlFile($sqlFile);
		}

		// Setup default payment plugins
		$query->clear()
			->select('COUNT(*)')
			->from('#__eb_payment_plugins');
		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/plugins.eventbooking.sql';
			EventbookingHelper::executeSqlFile($sqlFile);
		}

		// Setup menus
		$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/menus.eventbooking.sql';
		EventbookingHelper::executeSqlFile($sqlFile);

		// Custom admin menus
		$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/custommenus.eventbooking.sql';

		if (file_exists($sqlFile))
		{
			EventbookingHelper::executeSqlFile($sqlFile);
		}

		// Default messages
		$query->clear()
			->select('COUNT(*)')
			->from('#__eb_messages');
		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/messages.eventbooking.sql';
			EventbookingHelper::executeSqlFile($sqlFile);
		}


		$message             = EventbookingHelper::getMessages();
		$possibleNewMessages = require JPATH_ADMINISTRATOR . '/components/com_eventbooking/updates/messages.php';
		$query               = $db->getQuery(true);

		foreach ($possibleNewMessages as $key => $value)
		{
			if (!isset($message->{$key}))
			{
				$query->clear()
					->insert('#__eb_messages')
					->columns($db->quoteName(['id', 'message_key', 'message']))
					->values(implode(',', $db->quote([0, $key, $value])));
				$db->setQuery($query)
					->execute();
			}
		}

		$config          = EventbookingHelper::getConfig();
		$possibleConfigs = require JPATH_ADMINISTRATOR . '/components/com_eventbooking/updates/configs.php';

		foreach ($possibleConfigs as $key => $value)
		{
			if (!isset($config->{$key}))
			{
				$query->clear()
					->insert('#__eb_configs')
					->columns($db->quoteName(['id', 'config_key', 'config_value']))
					->values(implode(',', $db->quote([0, $key, $value])));
				$db->setQuery($query)
					->execute();
			}
		}

		// Countries, States database
		$query->clear()
			->select('COUNT(*)')
			->from('#__eb_countries');
		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/countries_states.sql';
			EventbookingHelper::executeSqlFile($sqlFile);
		}

		// Message items
		$query->clear()
			->select('COUNT(*)')
			->from('#__eb_mitems');
		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$sqlFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/mitems.eventbooking.sql';
			EventbookingHelper::executeSqlFile($sqlFile);
		}

		// Insert new message items
		$newItems = require JPATH_ADMINISTRATOR . '/components/com_eventbooking/updates/mitems.php';

		self::insertMessageItems($newItems);

		// Insert custom message items if available
		if (file_exists(JPATH_ADMINISTRATOR . '/components/com_eventbooking/updates/custom.mitems.php'))
		{
			$customItems = require JPATH_ADMINISTRATOR . '/components/com_eventbooking/updates/custom.mitems.php';

			self::insertMessageItems($customItems);
		}

	}

	/**
	 * Enable required plugins for Events Booking to work
	 *
	 * @return void
	 */
	public static function enableRequiredPlugin()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$requiredPlugins = [
			'eventbooking' => [
				'system',
			],
			'installer'    => [
				'eventbooking',
			],
		];

		foreach ($requiredPlugins as $folder => $plugins)
		{
			foreach ($plugins as $plugin)
			{
				$query->clear()
					->update('#__extensions')
					->set('enabled = 1')
					->where('element = ' . $db->quote($plugin))
					->where('folder = ' . $db->quote($folder));
				$db->setQuery($query)
					->execute();
			}
		}
	}

	/**
	 * Get installed version of the component
	 *
	 * @return void
	 */
	private function getInstalledVersion()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('manifest_cache')
			->from('#__extensions')
			->where($db->quoteName('element') . ' = "com_eventbooking"')
			->where($db->quoteName('type') . ' = "component"');
		$db->setQuery($query);
		$manifestCache = $db->loadResult();

		if ($manifestCache)
		{
			$manifest               = json_decode($manifestCache);
			$this->installedVersion = $manifest->version;
		}
	}

	/**
	 * Migrate common language items
	 */
	private function migrateCommonLanguageItems()
	{
		// We only do it when upgrading from old version to 4.0.0 or 4.0.1
		if (!File::exists(JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_eventbookingcommon.ini')
			|| !File::exists(JPATH_ROOT . '/language/en-GB/en-GB.com_eventbooking.ini'))
		{
			return;
		}

		// Populate common language file for en-GB base on the value of frontend customized translation
		if (version_compare($this->installedVersion, '4.0.1', '<='))
		{
			$feLanguage = new Registry;
			$coLanguage = new Registry;

			$feLanguage->loadFile(JPATH_ROOT . '/language/en-GB/en-GB.com_eventbooking.ini', 'INI');
			$coLanguage->loadFile(JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_eventbookingcommon.ini', 'INI');

			foreach ($coLanguage->toArray() as $key => $value)
			{
				if ($feLanguage->exists($key))
				{
					$coLanguage->set($key, $feLanguage->get($key));
					$feLanguage->remove($key);
				}
			}

			LanguageHelper::saveToIniFile(JPATH_ROOT . '/language/en-GB/en-GB.com_eventbooking.ini', $feLanguage->toArray());
			LanguageHelper::saveToIniFile(JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_eventbookingcommon.ini', $coLanguage->toArray());
		}

		// Create common language file for none English languages
		$languages = LanguageHelper::getInstalledLanguages(0);

		foreach ($languages as $language)
		{
			self::ensureCommonLanguageFileExist($language->element);
		}
	}

	/**
	 * Migrate menu item types from Events Booking version 3 to Events Booking 4
	 */
	private function migrateMenuItems()
	{
		if (version_compare($this->installedVersion, '4.0.0', '>='))
		{
			return;
		}

		$menus     = Factory::getApplication()->getMenu('site');
		$db        = Factory::getDbo();
		$query     = $db->getQuery(true);
		$component = ComponentHelper::getComponent('com_eventbooking');
		$items     = $menus->getItems('component_id', $component->id);

		foreach ($items as $item)
		{
			if (!isset($item->query['view']) || !in_array($item->query['view'], ['archive', 'category']))
			{
				continue;
			}

			if ($item->query['view'] == 'archive')
			{
				// Migrate to category view
				$item->getParams()->set('display_events_type', 3);
				$item->getParams()->set('menu_filter_order', 'tbl.event_date');
				$item->getParams()->set('menu_filter_order_dir', 'DESC');
			}

			if ($item->query['view'] == 'category')
			{
				$params = $item->getParams();

				if ($params->get('hide_past_events') == 0)
				{
					// Display All Events
					$params->set('display_events_type', 1);
				}
				elseif ($params->get('hide_past_events') == 1)
				{
					// Display Upcoming Events
					$params->set('display_events_type', 2);
				}
				else
				{
					// Use global configuration
					$params->set('display_events_type', 0);
				}
			}

			$link = str_replace('view=' . $item->query['view'], 'view=category', $item->link);

			$query->clear()
				->update('#__menu')
				->set('link = ' . $db->quote($link))
				->set('params = ' . $db->quote($item->getParams()->toString()))
				->where('id = ' . $item->id);
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Migrate tax rates from old system to new system
	 */
	private function migrateTaxRules()
	{
		if (version_compare($this->installedVersion, '4.0.0', '>='))
		{
			return;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Setup EU Tax Rules
		if (EventbookingHelperRegistration::isEUVatTaxRulesEnabled())
		{
			$config = EventbookingHelper::getConfig();
			$db->truncateTable('#__eb_taxes');
			$defaultCountry     = $config->default_country;
			$defaultCountryCode = EventbookingHelper::getCountryCode($defaultCountry);

			$query->insert('#__eb_taxes')
				->columns('event_id, country, rate, vies, published');

			// Without VAT number, use local tax rate
			foreach (EventbookingHelperEuvat::$europeanUnionVATInformation as $countryCode => $vatInfo)
			{
				$countryName    = $vatInfo[0];
				$countryTaxRate = EventbookingHelperEuvat::getEUCountryTaxRate($countryCode);

				$query->values(implode(',', $db->quote([0, $countryName, $countryTaxRate, 0, 1])));


				if ($countryCode == $defaultCountryCode)
				{
					$localTaxRate = EventbookingHelperEuvat::getEUCountryTaxRate($defaultCountryCode);
					$query->values(implode(',', $db->quote([0, $countryName, $localTaxRate, 1, 1])));
				}
			}

			$db->setQuery($query)
				->execute();

			return;
		}

		$query->select('COUNT(*)')
			->from('#__eb_taxes');
		$db->setQuery($query);

		if ($db->loadResult() > 0)
		{
			return;
		}

		$query->clear()
			->select('DISTINCT tax_rate')
			->from('#__eb_events')
			->where('tax_rate > 0');
		$db->setQuery($query);
		$taxRates = $db->loadColumn();

		if (!count($taxRates))
		{
			// The existing system does not use tax
			return;
		}

		if (count($taxRates) == 1)
		{
			// Only has single tax rate, assume that tax rate is the same for all events
			$query->clear()
				->insert('#__eb_taxes')
				->columns('event_id, rate, published')
				->values(implode(',', $db->quote([0, $taxRates[0], 1])));
			$db->setQuery($query)
				->execute();
		}
		else
		{
			// There are different tax rates for different event
			$query->clear()
				->select('id, tax_rate')
				->from('#__eb_events')
				->where('tax_rate > 0');
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			$query->clear()
				->insert('#__eb_taxes')
				->columns('event_id, rate, published');

			foreach ($rows as $row)
			{
				$query->values(implode(',', $db->quote([$row->id, $row->tax_rate, 1])));
			}

			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Insert message items
	 *
	 * @param   array  $items
	 */
	private static function insertMessageItems($items)
	{
		if (!count($items))
		{
			return;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('name')
			->from('#__eb_mitems');
		$db->setQuery($query);
		$existingItems = $db->loadColumn();

		$query->clear()
			->insert('#__eb_mitems')
			->columns($db->quoteName(['name', 'title', 'type', 'group']));

		$count = 0;

		foreach ($items as $item)
		{
			if (in_array($item['name'], $existingItems))
			{
				continue;
			}

			$count++;

			$query->values(implode(',', $db->quote([$item['name'], $item['title'], $item['type'], $item['group']])));
		}

		if ($count)
		{
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Method to check and make sure common language file for certain language exist
	 *
	 * @param   string  $tag
	 */
	private static function ensureCommonLanguageFileExist($tag)
	{
		// Ignore, we always have common language file for English
		if ($tag === 'en-GB')
		{
			return;
		}

		$coLanguageFile = JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_eventbookingcommon.ini';

		// Common language file for this language was created before, stop
		if (File::exists($coLanguageFile))
		{
			return;
		}

		// No frontend language file, we need to create common language file for this language base on English language
		if (!File::exists(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_eventbooking.ini'))
		{
			File::copy(JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_eventbookingcommon.ini', $coLanguageFile);

			return;
		}

		// Generate common language file base on frontend and backend translation
		$feLanguageFileExist = false;
		$beLanguageFileExist = false;
		$feLanguageFile      = JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_eventbooking.ini';
		$beLanguageFile      = JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_eventbooking.ini';
		$beLanguage          = new Registry;
		$feLanguage          = new Registry;
		$coLanguage          = new Registry;

		// Load frontend language registry if exists to populate common language file
		if (File::exists($feLanguageFile))
		{
			$feLanguageFileExist = true;
			$feLanguage->loadFile($feLanguageFile, 'INI');
		}

		// Load backend language registry if exists to populate common language file
		if (File::exists($beLanguageFile))
		{
			$beLanguageFileExist = true;
			$beLanguage->loadFile($beLanguageFile, 'INI');
		}

		$coLanguage->loadFile(JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_eventbookingcommon.ini', 'INI');

		foreach ($coLanguage->toArray() as $key => $value)
		{
			if ($feLanguage->exists($key))
			{
				$coLanguage->set($key, $feLanguage->get($key));
				$feLanguage->remove($key);
				$beLanguage->remove($key);
			}
			elseif ($beLanguage->exists($key))
			{
				$coLanguage->set($key, $beLanguage->get($key));
				$beLanguage->remove($key);
			}
		}

		if ($feLanguageFileExist)
		{
			LanguageHelper::saveToIniFile($feLanguageFile, $feLanguage->toArray());
		}

		if ($beLanguageFileExist)
		{
			LanguageHelper::saveToIniFile($beLanguageFile, $beLanguage->toArray());
		}

		LanguageHelper::saveToIniFile($coLanguageFile, $coLanguage->toArray());
	}
}