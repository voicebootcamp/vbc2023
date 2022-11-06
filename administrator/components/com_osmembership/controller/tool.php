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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;

class OSMembershipControllerTool extends MPFController
{
	public function display_root_path()
	{
		if (Factory::getUser()->authorise('core.admin'))
		{
			echo JPATH_ROOT;
		}
		else
		{
			echo 'You do not have permission to view JPPATH_ROOT';
		}
	}

	public function fix_profile_data()
	{
		$start = $this->input->getInt('start', 0);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('user_id, MIN(id) AS profile_id')
			->from('#__osmembership_subscribers')
			->where('user_id > 0 ')
			->where('(published >= 1 OR payment_method LIKE "os_offline%")')
			->group('user_id');
		$db->setQuery($query, $start, 2000);
		$rows = $db->loadObjectList();

		if (count($rows) == 0)
		{
			// OK, job done
			$this->setRedirect('index.php?option=com_osmembership&view=subscriptions', 'Profile Data Successfully Corrected');
		}
		else
		{
			foreach ($rows as $row)
			{
				// Set the first record as profile record
				$query->clear()
					->update('#__osmembership_subscribers')
					->set('is_profile = 1')
					->set('profile_id = id')
					->where('id = ' . $row->profile_id);
				$db->setQuery($query)
					->execute();

				// Set other records as none profile records
				$query->clear()
					->update('#__osmembership_subscribers')
					->set('is_profile = 0')
					->set('profile_id = ' . $row->profile_id)
					->where('id  != ' . $row->profile_id)
					->where('user_id = ' . $row->user_id);
				$db->setQuery($query)
					->execute();
			}

			$start += count($rows);
			$this->setRedirect('index.php?option=com_osmembership&task=tool.fix_profile_data&start=' . $start);
		}
	}

	public function generate_formatted_invoice_number()
	{
		$config = OSMembershipHelper::getConfig();
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_subscribers')
			->where('invoice_number > 0');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('formatted_invoice_number = ' . $db->quote(OSMembershipHelper::formatInvoiceNumber($row, $config)))
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}

		echo 'Successfully Generating Formatted Invoice Number';
	}

	public function generate_membership_id()
	{
		$config = OSMembershipHelper::getConfig();
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true)
			->select('DISTINCT user_id')
			->from('#__osmembership_subscribers')
			->where('user_id > 0')
			->where('(published >= 1 OR payment_method LIKE "os_offline%")')
			->order('id');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$start = (int) $config->membership_id_start_number ?: 1000;

		foreach ($rows as $row)
		{
			$sql = 'UPDATE #__osmembership_subscribers SET membership_id=' . $start . ' WHERE user_id = ' . $row->user_id;
			$db->setQuery($sql)
				->execute();
			$start++;
		}
	}

	public function generate_formatted_membership_id()
	{
		$config = OSMembershipHelper::getConfig();
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_subscribers')
			->where('membership_id > 0');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('formatted_membership_id = ' . $db->quote(OSMembershipHelper::formatMembershipId($row, $config)))
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}

		echo 'Successfully Generating Formatted Membership ID';
	}

	public function download_and_set_font()
	{
		$font = $this->input->getString('font');

		$fontPackageUrl = 'https://joomdonation.com/tcpdf/fonts.zip';

		$fontFile = InstallerHelper::downloadPackage($fontPackageUrl, 'fonts.zip');

		if ($fontFile === false)
		{
			echo Text::_('The requested font could not be downloaded');

			return;
		}

		$tmpPath = $this->app->get('tmp_path');

		if (!Folder::exists($tmpPath))
		{
			$tmpPath = JPATH_ROOT . '/tmp';
		}

		$fontPackage = $tmpPath . '/fonts.zip';

		$extractDir = JPATH_ROOT . '/components/com_osmembership/tcpdf/fonts';

		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$archive = new Joomla\Archive\Archive(['tmp_path' => $tmpPath]);
			$result  = $archive->extract($fontPackage, $extractDir);
		}
		else
		{
			$result = JArchive::extract($fontPackage, $extractDir);
		}

		if (!$result)
		{
			echo 'Error extract font package';

			return;
		}

		// Delete the downloaded zip file
		File::delete($fontPackage);

		if ($font)
		{
			// Now, set font to that font
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->update('#__osmembership_configs')
				->set('config_value = ' . $db->quote($font))
				->set('config_key = "pdf_font"');
			$db->setQuery($query)
				->execute();

			echo 'Font was successfully downloaded and set for PDF Font config option';
		}
		else
		{
			echo 'Fonts were successfully downloaded and extracted';
		}

	}

	public function download_mpdf_font()
	{
		$fontPackageUrl = 'https://joomdonation.com/tcpdf/ttfonts.zip';

		$fontFile = InstallerHelper::downloadPackage($fontPackageUrl, 'ttfonts.zip');

		if ($fontFile === false)
		{
			echo Text::_('The requested font could not be downloaded');

			return;
		}

		$tmpPath = $this->app->get('tmp_path');

		if (!Folder::exists($tmpPath))
		{
			$tmpPath = JPATH_ROOT . '/tmp';
		}

		$fontPackage = $tmpPath . '/ttfonts.zip';

		$extractDir = JPATH_ROOT . '/plugins/osmembership/mpdf/mpdf';

		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$archive = new Joomla\Archive\Archive(['tmp_path' => $tmpPath]);
			$result  = $archive->extract($fontPackage, $extractDir);
		}
		else
		{
			$result = JArchive::extract($fontPackage, $extractDir);
		}

		if (!$result)
		{
			echo 'Error extract font package';

			return;
		}

		// Delete the downloaded zip file
		File::delete($fontPackage);

		$this->setRedirect('index.php?option=com_osmembership&view=subscriptions', 'ttfonts for MPDF is downloaded and extracted');
	}

	public function download_mpdf_font_full()
	{
		$fontPackageUrl = 'https://joomdonation.com/tcpdf/ttfonts_full.zip';

		$fontFile = InstallerHelper::downloadPackage($fontPackageUrl, 'ttfonts.zip');

		if ($fontFile === false)
		{
			echo Text::_('The requested font could not be downloaded');

			return;
		}

		$tmpPath = $this->app->get('tmp_path');

		if (!Folder::exists($tmpPath))
		{
			$tmpPath = JPATH_ROOT . '/tmp';
		}

		$fontPackage = $tmpPath . '/ttfonts.zip';

		$extractDir = JPATH_ROOT . '/plugins/osmembership/mpdf/mpdf';

		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$archive = new Joomla\Archive\Archive(['tmp_path' => $tmpPath]);
			$result  = $archive->extract($fontPackage, $extractDir);
		}
		else
		{
			$result = JArchive::extract($fontPackage, $extractDir);
		}

		if (!$result)
		{
			echo 'Error extract font package';

			return;
		}

		// Delete the downloaded zip file
		File::delete($fontPackage);

		$this->setRedirect('index.php?option=com_osmembership&view=subscriptions', 'ttfonts for MPDF is downloaded and extracted');
	}

	public function update_recurring_payment_amounts()
	{
		$db                    = Factory::getDbo();
		$query                 = $db->getQuery(true);
		$planId                = $this->input->getInt('plan_id', 0);
		$regularAmount         = $this->input->getFloat('regular_amount', 0);
		$regularDiscountAmount = $this->input->getFloat('regular_discount_amount', 0);
		$regularTaxAmount      = $this->input->getFloat('regular_tax_amount', 0);
		$paymentProcessingFee  = $this->input->getFloat('payment_processing_fee', 0);
		$regularGrossAmount    = $this->input->getFloat('regular_gross_amount', 0);

		if ($regularAmount > 0 && $regularGrossAmount == 0)
		{
			$regularGrossAmount = $regularAmount - $regularDiscountAmount + $regularTaxAmount + $paymentProcessingFee;
		}

		$plan = OSMembershipHelperDatabase::getPlan($planId);

		if (!$plan || !$plan->recurring_subscription)
		{
			echo 'This tool is used for plan with recurring subscription enabled only';
		}

		$query->select('id, params')
			->from('#__osmembership_subscribers')
			->where('plan_id = ' . $planId);
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$params = new JRegistry($row->params);
			$params->set('regular_amount', $regularAmount);
			$params->set('regular_discount_amount', $regularDiscountAmount);
			$params->set('regular_tax_amount', $regularTaxAmount);
			$params->set('payment_processing_fee', $paymentProcessingFee);
			$params->set('regular_gross_amount', $regularGrossAmount);

			$query->clear()
				->update('#__osmembership_subscribers')
				->set('params = ' . $db->quote($params->toString()))
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}

		echo 'Done';
	}

	public function fix_state_2code_data()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id, country, state')
			->from('#__osmembership_subscribers')
			->where('LENGTH(state) > 2');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$state2Code = OSMembershipHelper::getStateCode($row->country, $row->state);
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('state = ' . $db->quote($state2Code))
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}

		echo 'Done';
	}

	public function fix_user_id_plan_id_data()
	{
		$db  = Factory::getDbo();
		$sql = 'ALTER TABLE `#__osmembership_subscribers` CHANGE `plan_id` `plan_id` INT(11) NOT NULL DEFAULT "0";';
		$db->setQuery($sql)
			->execute();

		$query = $db->getQuery(true)
			->update('#__osmembership_subscribers')
			->set('plan_id = 0')
			->where('plan_id IS NULL');
		$db->setQuery($query)
			->execute();

		$sql = 'ALTER TABLE `#__osmembership_subscribers` CHANGE `user_id` `user_id` INT(11) NOT NULL DEFAULT "0";';
		$db->setQuery($sql)
			->execute();

		$query = $db->getQuery(true)
			->update('#__osmembership_subscribers')
			->set('user_id = 0')
			->where('user_id IS NULL');
		$db->setQuery($query)
			->execute();
	}

	/**
	 * Alter Decimal
	 */
	public function alter_price_fields()
	{
		$db = Factory::getDbo();

		$changes = [
			'#__osmembership_plans'            => ['price', 'trial_amount', 'setup_fee'],
			'#__osmembership_renewrates'       => ['price'],
			'#__osmembership_upgraderules'     => ['price'],
			'#__osmembership_taxes'            => ['rate'],
			'#__osmembership_renewaldiscounts' => ['discount_amount'],
			'#__osmembership_subscribers'      => '[tax_rate]',
		];

		foreach ($changes as $table => $fields)
		{
			foreach ($fields as $field)
			{
				$sql = "ALTER TABLE  `$table` CHANGE  `$field` `$field`  DECIMAL(15,8) DEFAULT '0';";
				$db->setQuery($sql)
					->execute();
			}
		}

		echo 'Done';
	}

	/**
	 * Method to allow sharing language files for Events Booking
	 */
	public function share_translation()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('lang_code')
			->from('#__languages')
			->where('published = 1')
			->where('lang_code != "en-GB"')
			->order('ordering');
		$db->setQuery($query);
		$languages = $db->loadObjectList();

		if (count($languages))
		{
			$mailer   = Factory::getMailer();
			$jConfig  = Factory::getConfig();
			$mailFrom = $jConfig->get('mailfrom');
			$fromName = $jConfig->get('fromname');
			$mailer->setSender([$mailFrom, $fromName]);
			$mailer->addRecipient('tuanpn@joomdonation.com');
			$mailer->setSubject('Language Packages for Membership Pro shared by ' . Uri::root());
			$mailer->setBody('Dear Tuan \n. I am happy to share my language packages for Membership Pro.\n Enjoy!');
			foreach ($languages as $language)
			{
				$tag = $language->lang_code;

				if (file_exists(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini'))
				{
					$mailer->addAttachment(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini', $tag . '.com_osmembership.ini');
				}

				if (file_exists(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini'))
				{
					$mailer->addAttachment(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osmembership.ini',
						'admin.' . $tag . '.com_osmembership.ini');
				}

				if (file_exists(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osmembershipcommon.ini'))
				{
					$mailer->addAttachment(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_osmembershipcommon.ini',
						$tag . '.com_osmembershipcommon.ini');
				}
			}

			require_once JPATH_COMPONENT . '/libraries/vendor/dbexporter/dumper.php';

			$tables = [$db->replacePrefix('#__eb_fields'), $db->replacePrefix('#__eb_messages')];

			try
			{

				$sqlFile = $tag . '.com_osmembership.sql';
				$options = [
					'host'           => $jConfig->get('host'),
					'username'       => $jConfig->get('user'),
					'password'       => $jConfig->get('password'),
					'db_name'        => $jConfig->get('db'),
					'include_tables' => $tables,
				];
				$dumper  = Shuttle_Dumper::create($options);
				$dumper->dump(JPATH_ROOT . '/tmp/' . $sqlFile);

				$mailer->addAttachment(JPATH_ROOT . '/tmp/' . $sqlFile, $sqlFile);

			}
			catch (Exception $e)
			{
				//Do nothing
			}

			$mailer->Send();

			$msg = 'Thanks so much for sharing your language files to Membership Pro Community';
		}
		else
		{
			$msg = 'Thanks so willing to share your language files to Membership Pro Community. However, you don"t have any none English language file to share';
		}

		$this->setRedirect('index.php?option=com_osmembership&view=dashboard', $msg);
	}

	/**
	 * Reset SEF urls
	 */
	public function reset_urls()
	{
		$db = Factory::getDbo();
		$db->truncateTable('#__osmembership_sefurls');
		$this->setRedirect('index.php?option=com_osmembership&view=dashboard', Text::_('SEF urls has successfully been reset'));
	}

	public function trigger_store_event()
	{
		$plugin = $this->input->getCmd('plugin');
		$planId = $this->input->getInt('plan_id', 0);

		PluginHelper::importPlugin('osmembership', $plugin);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__osmembership_subscribers')
			->order('id');

		if ($planId > 0)
		{
			$query->where('plan_id = ' . $planId);
		}

		$db->setQuery($query);
		$ids = $db->loadColumn();

		foreach ($ids as $id)
		{
			$row = Table::getInstance('Subscriber', 'OSMembershipTable');
			$row->load($id);
			$this->app->triggerEvent('onAfterStoreSubscription', [$row]);
		}
	}

	/**
	 * Trigger expired event to expired subscriptions
	 *
	 * @return void
	 */
	public function trigger_expired_event()
	{
		$plugin = $this->input->getCmd('plugin');
		$planId = $this->input->getInt('plan_id', 0);

		PluginHelper::importPlugin('osmembership', $plugin);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__osmembership_subscribers')
			->where('published = 2')
			->order('id');

		if ($planId > 0)
		{
			$query->where('plan_id = ' . $planId);
		}

		$db->setQuery($query);
		$ids = $db->loadColumn();

		foreach ($ids as $id)
		{
			$row = Table::getInstance('Subscriber', 'OSMembershipTable');
			$row->load($id);
			$this->app->triggerEvent('onMembershipExpire', [$row]);
		}
	}

	/**
	 * Trigger active events to active subscriptions
	 *
	 * @return void
	 */
	public function trigger_active_event()
	{
		$start  = $this->input->getInt('start', 0);
		$plugin = $this->input->getCmd('plugin');
		$planId = $this->input->getInt('plan_id', 0);

		PluginHelper::importPlugin('osmembership', $plugin);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__osmembership_subscribers')
			->where('published = 1')
			->order('id');

		if ($planId)
		{
			$query->where('plan_id = ' . $planId);
		}

		$db->setQuery($query, $start, 100);
		$ids = $db->loadColumn();

		foreach ($ids as $id)
		{
			$row = Table::getInstance('Subscriber', 'OSMembershipTable');
			$row->load($id);
			$this->app->triggerEvent('onMembershipActive', [$row]);
		}

		if (count($ids) === 0)
		{
			echo 'Complete active event trigger';
		}
		else
		{
			$start += count($ids);

			echo 'Please wait until the process finished';

			sleep(3);

			$this->setRedirect('index.php?option=com_osmembership&task=tool.trigger_active_event&start=' . $start . ($plugin ? '&plugin=' . $plugin : ''));
		}
	}

	/**
	 * Trigger active events to active subscriptions
	 *
	 * @return void
	 */
	public function trigger_active_event_for_joomlagroups()
	{
		PluginHelper::importPlugin('osmembership', 'joomlagroups');

		$start  = $this->input->getInt('start', 0);
		$planId = $this->input->getInt('plan_id', 0);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__osmembership_subscribers')
			->where('published = 1')
			->order('id');

		if ($planId > 0)
		{
			$query->where('plan_id = ' . $planId);
		}

		$db->setQuery($query, $start, 100);
		$ids = $db->loadColumn();

		foreach ($ids as $id)
		{
			$row = Table::getInstance('Subscriber', 'OSMembershipTable');
			$row->load($id);
			$this->app->triggerEvent('onMembershipActive', [$row]);
		}

		if (count($ids) === 0)
		{
			echo 'Complete process Joomla Groups';
		}
		else
		{
			$start += count($ids);

			echo 'Please wait until the process finished';

			sleep(3);

			$this->setRedirect('index.php?option=com_osmembership&task=tool.trigger_active_event_for_joomlagroups&start=' . $start);
		}
	}

	/**
	 * Change language code
	 *
	 * @return void
	 */
	public function change_language_code()
	{
		$db = Factory::getDbo();

		#Process for #__osmembership_categories table
		$varcharFields = [
			'alias',
			'title',
		];

		$oldLanguageCode = 'ar-AA';
		$newLanguageCode = 'ar';

		foreach ($varcharFields as $varcharField)
		{
			$oldFieldName = $varcharField . '_' . $oldLanguageCode;
			$fieldName    = $varcharField . '_' . $newLanguageCode;
			$sql          = "ALTER TABLE  `#__osmembership_categories` CHANGE  `$oldFieldName` `$fieldName` VARCHAR( 255 );";
			$db->setQuery($sql);
			$db->execute();
		}

		$textFields = [
			'description',
		];

		foreach ($textFields as $textField)
		{
			$oldFieldName = $textField . '_' . $oldLanguageCode;
			$fieldName    = $textField . '_' . $newLanguageCode;

			$sql = "ALTER TABLE  `#__osmembership_categories` CHANGE `$oldFieldName` `$fieldName` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		#Process for #__osmembership_plans table
		$varcharFields = [
			'alias',
			'title',
			'user_email_subject',
			'subscription_approved_email_subject',
			'user_renew_email_subject',
		];

		foreach ($varcharFields as $varcharField)
		{
			$oldFieldName = $varcharField . '_' . $oldLanguageCode;
			$fieldName    = $varcharField . '_' . $newLanguageCode;
			$sql          = "ALTER TABLE  `#__osmembership_plans` CHANGE  `$oldFieldName` `$fieldName` VARCHAR( 255 );";
			$db->setQuery($sql);
			$db->execute();
		}

		$textFields = [
			'short_description',
			'description',
			'subscription_form_message',
			'user_email_body',
			'user_email_body_offline',
			'subscription_approved_email_body',
			'thanks_message',
			'thanks_message_offline',
			'user_renew_email_body',
		];

		foreach ($textFields as $textField)
		{
			$oldFieldName = $textField . '_' . $oldLanguageCode;
			$fieldName    = $textField . '_' . $newLanguageCode;

			$sql = "ALTER TABLE  `#__osmembership_plans` CHANGE `$oldFieldName` `$fieldName` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		#Process for #__osmembership_fields table
		$varcharFields = [
			'title',
		];

		foreach ($varcharFields as $varcharField)
		{
			$oldFieldName = $varcharField . '_' . $oldLanguageCode;
			$fieldName    = $varcharField . '_' . $newLanguageCode;
			$sql          = "ALTER TABLE  `#__osmembership_fields` CHANGE  `$oldFieldName` `$fieldName` VARCHAR( 255 );";
			$db->setQuery($sql);
			$db->execute();
		}

		$textFields = [
			'description',
			'values',
			'default_values',
			'fee_values',
			'depend_on_options',
		];

		foreach ($textFields as $textField)
		{
			$oldFieldName = $textField . '_' . $oldLanguageCode;
			$fieldName    = $textField . '_' . $newLanguageCode;

			$sql = "ALTER TABLE  `#__osmembership_fields` CHANGE `$oldFieldName` `$fieldName` TEXT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__osmembership_messages');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			if (strpos($row->message_key, $oldLanguageCode) !== false)
			{
				$newKey = str_replace($oldLanguageCode, $newLanguageCode, $row->message_key);
				$query->clear()
					->update('#__osmembership_messages')
					->set('message_key = ' . $db->quote($newKey))
					->where('id = ' . $row->id);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Build EU tax rules
	 */
	public function build_eu_tax_rules()
	{
		$config = OSMembershipHelper::getConfig();
		$db     = Factory::getDbo();
		$db->truncateTable('#__osmembership_taxes');
		$defaultCountry     = $config->default_country;
		$defaultCountryCode = OSMembershipHelper::getCountryCode($defaultCountry);
		// Without VAT number, use local tax rate
		foreach (OSMembershipHelperEuvat::$europeanUnionVATInformation as $countryCode => $vatInfo)
		{
			$countryName    = $db->quote($vatInfo[0]);
			$countryTaxRate = OSMembershipHelperEuvat::getEUCountryTaxRate($countryCode);
			$sql            = "INSERT INTO #__osmembership_taxes(plan_id, country, rate, vies, published) VALUES(0, $countryName, $countryTaxRate, 0, 1)";
			$db->setQuery($sql);
			$db->execute();

			if ($countryCode == $defaultCountryCode)
			{
				$localTaxRate = OSMembershipHelperEuvat::getEUCountryTaxRate($defaultCountryCode);
				$sql          = "INSERT INTO #__osmembership_taxes(plan_id, country, rate, vies, published) VALUES(0, $countryName, $localTaxRate, 1, 1)";
				$db->setQuery($sql);
				$db->execute();
			}
		}

		$this->setRedirect('index.php?option=com_osmembership&view=taxes', Text::_('EU Tax Rules were successfully created'));
	}

	/**
	 * Fix "Row size too large" issue
	 */
	public function fix_row_size()
	{
		$db = Factory::getDbo();

		$tables = [
			'#__osmembership_categories',
			'#__osmembership_plans',
			'#__osmembership_fields',
			'#__osmembership_subscribers',
		];

		foreach ($tables as $table)
		{
			$query = "ALTER TABLE `$table` ROW_FORMAT = DYNAMIC";
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Method to make a given field search and sortable easier
	 */
	public function make_field_search_sort_able()
	{
		$db      = Factory::getDbo();
		$query   = $db->getQuery(true);
		$fieldId = $this->input->getInt('field_id');

		$query->select('*')
			->from('#__osmembership_fields')
			->where('id = ' . (int) $fieldId);
		$db->setQuery($query);
		$field = $db->loadObject();

		if (!$field)
		{
			throw new Exception('The field does not exist');
		}

		// Add new field to #__eb_registrants
		$fields = array_keys($db->getTableColumns('#__osmembership_subscribers'));

		if (!in_array($field->name, $fields))
		{
			$sql = "ALTER TABLE  `#__osmembership_subscribers` ADD  `$field->name` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();

			$query->clear()
				->select('*')
				->from('#__osmembership_field_value')
				->where('field_id = ' . $fieldId);
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			$fieldName = $db->quoteName($field->name);

			foreach ($rows as $row)
			{
				$query->clear()
					->update('#__osmembership_subscribers')
					->set($fieldName . ' = ' . $db->quote($row->field_value))
					->where('id = ' . $row->subscriber_id);
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Mark the field as searchable
		$query->clear()
			->update('#__osmembership_fields')
			->set('is_searchable = 1')
			->where('id = ' . (int) $fieldId);
		$db->setQuery($query);
		$db->execute();

		echo 'Done !';
	}

	/**
	 * The second option to fix row size
	 */
	public function fix_row_size2()
	{
		$db        = Factory::getDbo();
		$languages = OSMembershipHelper::getLanguages();

		if (count($languages))
		{
			$categoryTableFields = array_keys($db->getTableColumns('#__osmembership_categories'));
			$planTableFields     = array_keys($db->getTableColumns('#__osmembership_plans'));
			$fieldTableFields    = array_keys($db->getTableColumns('#__osmembership_fields'));

			foreach ($languages as $language)
			{
				$prefix = $language->sef;

				$fields = [
					'alias',
					'title',
					'description',
				];

				foreach ($fields as $field)
				{
					$fieldName = $field . '_' . $prefix;

					if (!in_array($fieldName, $categoryTableFields))
					{
						$sql = "ALTER TABLE  `#__osmembership_categories` ADD  `$fieldName` TEXT NULL;";
					}
					else
					{
						$sql = "ALTER TABLE  `#__osmembership_categories` MODIFY  `$fieldName` TEXT NULL;";
					}

					$db->setQuery($sql);

					try
					{
						$db->execute();
					}
					catch (Exception $e)
					{
						$this->app->enqueueMessage(sprintf('Field %s already exist in table %s', $fieldName, '#__osmembership_categories'));
					}
				}

				$fields = [
					'alias',
					'title',
					'page_title',
					'page_heading',
					'meta_keywords',
					'meta_description',
					'user_email_subject',
					'subscription_approved_email_subject',
					'user_renew_email_subject',
					'short_description',
					'description',
					'subscription_form_message',
					'user_email_body',
					'user_email_body_offline',
					'subscription_approved_email_body',
					'thanks_message',
					'thanks_message_offline',
					'user_renew_email_body',
				];

				foreach ($fields as $field)
				{
					$fieldName = $field . '_' . $prefix;

					if (!in_array($fieldName, $planTableFields))
					{
						$sql = "ALTER TABLE  `#__osmembership_plans` ADD  `$fieldName` TEXT NULL;";
					}
					else
					{
						$sql = "ALTER TABLE  `#__osmembership_plans` MODIFY  `$fieldName` TEXT NULL;";
					}

					$db->setQuery($sql);

					try
					{
						$db->execute();
					}
					catch (Exception $e)
					{
						$this->app->enqueueMessage(sprintf('Field %s already exist in table %s', $fieldName, '#__osmembership_plans'));
					}
				}

				$fields = [
					'title',
					'place_holder',
					'description',
					'values',
					'default_values',
					'fee_values',
					'depend_on_options',
				];

				foreach ($fields as $field)
				{
					$fieldName = $field . '_' . $prefix;

					if (!in_array($fieldName, $fieldTableFields))
					{
						$sql = "ALTER TABLE  `#__osmembership_fields` ADD  `$fieldName` TEXT NULL;";
					}
					else
					{
						$sql = "ALTER TABLE  `#__osmembership_fields` MODIFY  `$fieldName` TEXT NULL;";
					}

					$db->setQuery($sql);

					try
					{
						$db->execute();
					}
					catch (Exception $e)
					{
						$this->app->enqueueMessage(sprintf('Field %s already exist in table %s', $fieldName, '#__eb_fields'));
					}
				}
			}
		}
	}

	/**
	 * Tool to update subscription_id of subscribers base on exported data from CSV file
	 */
	public function update_stripe_subscription_ids()
	{
		$db          = Factory::getDbo();
		$query       = $db->getQuery(true);
		$file        = JPATH_ADMINISTRATOR . '/components/com_osmembership/subscriptions.csv';
		$subscribers = OSMembershipHelperData::getDataFromFile($file);

		$notFound = [];
		$updated  = 0;

		foreach ($subscribers as $subscriber)
		{
			$subscriptionId = $subscriber['id'];

			// First, check to see whether this subscription exists in the system
			$query->clear()
				->select('COUNT(*)')
				->from('#__osmembership_subscribers')
				->where('subscription_id = ' . $db->quote($subscriptionId));
			$db->setQuery($query);

			if ($db->loadResult())
			{
				// Subscription exists, continue
				continue;
			}

			$email = $subscriber['Customer Email'];
			$plan  = str_replace('membership_plan_', '', $subscriber['Plan']);
			$parts = explode('_', $plan);

			$found = false;

			if (count($parts) > 1)
			{
				$planId = (int) $parts[0];
				$query->clear()
					->select('id')
					->from('#__osmembership_subscribers')
					->where('plan_id = ' . $planId)
					->where('email=' . $db->quote($email))
					->where('LENGTH(subscription_id) = 0');
				$db->setQuery($query);
				$id = (int) $db->loadResult();

				if ($id)
				{
					$query->clear()
						->update('#__osmembership_subscribers')
						->set('subscription_id = ' . $db->quote($subscriptionId))
						->where('id=' . $id);
					$db->setQuery($query);
					$db->execute();
					$updated++;
					$found = true;
				}
			}

			if (!$found)
			{
				$notFound[] = $subscriptionId;
			}
		}

		echo sprintf('%s subscriptions updated', $updated) . '<br />';

		echo 'The following subscriptions could not be found from your system:' . implode("<br />", $notFound);
	}

	public function fix_profile_id()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id, user_id')
			->from('#__osmembership_subscribers')
			->where('profile_id = 0')
			->where('(published >= 1 OR payment_method LIKE "os_offline%")')
			->order('id');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$isProfile = 1;
			$profileId = $row->id;

			if ($row->user_id > 0)
			{
				$query->clear()
					->select('id')
					->from('#__osmembership_subscribers')
					->where('user_id = ' . $row->user_id)
					->where('(published >= 1 OR payment_method LIKE "os_offline%")')
					->where('is_profile = 1');
				$db->setQuery($query);
				$existingProfileId = $db->loadResult();

				if ($existingProfileId && $existingProfileId != $row->id)
				{
					$isProfile = 0;
					$profileId = $existingProfileId;
				}
			}

			$query->clear()
				->update('#__osmembership_subscribers')
				->set('is_profile = ' . $isProfile)
				->set('profile_id = ' . $profileId)
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Change database schema to support setting up price in more decimal numbers
	 */
	public function support_more_decimal_numbers()
	{
		$db = Factory::getDbo();

		$sql = "ALTER TABLE  `#__osmembership_plans` CHANGE  `price`	`price` DECIMAL( 15, 8 ) NULL DEFAULT  '0';";
		$db->setQuery($sql)
			->execute();
		$sql = "ALTER TABLE  `#__osmembership_plans` CHANGE  `trial_amount`	`trial_amount` DECIMAL( 15, 8 ) NULL DEFAULT  '0';";
		$db->setQuery($sql)
			->execute();
		$sql = "ALTER TABLE  `#__osmembership_plans` CHANGE  `setup_fee`	`setup_fee` DECIMAL( 15, 8 ) NULL DEFAULT  '0';";
		$db->setQuery($sql)
			->execute();
		$sql = "ALTER TABLE  `#__osmembership_renewrates` CHANGE  `price`	`price` DECIMAL( 15, 8 ) NULL DEFAULT  '0';";
		$db->setQuery($sql)
			->execute();
		$sql = "ALTER TABLE  `#__osmembership_upgraderules` CHANGE  `price`	`price` DECIMAL( 15, 8 ) NULL DEFAULT  '0';";
		$db->setQuery($sql)
			->execute();
	}

	/**
	 * Tool to convert state name to state_2_code
	 */
	public static function convert_to_state_2_code()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id, country, state')
			->from('#__osmembership_subscribers')
			->where('CHAR_LENGTH(state) > 2');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$count = 0;

		foreach ($rows as $row)
		{
			$state = OSMembershipHelper::getStateCode($row->country, $row->state);

			if ($state == $row->state)
			{
				continue;
			}

			$query->clear()
				->update('#__osmembership_subscribers')
				->set('state = ' . $db->quote($state))
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();

			$count++;
		}

		echo sprintf('Succssfully converted %s state to state 2 code', $count);
	}

	/**
	 * Generate Subscription Ids for offline payments
	 */
	public function generate_subscription_ids_for_offline_payments()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id')
			->from('#__osmembership_subscribers AS a')
			->where('plan_id IN (SELECT id FROM #__osmembership_plans WHERE recurring_subscription = 1)')
			->where('a.payment_method LIKE "os_offline%"')
			->where('LENGTH(a.subscription_id) = 0');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			// Get random subscription ID
			$subscriptionId = UserHelper::genRandomPassword(15);
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('subscription_id = ' . $db->quote($subscriptionId))
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Tool to reset profile_id
	 */
	public function reset_profile_id_data()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('user_id, MIN(id) AS profile_id')
			->from('#__osmembership_subscribers')
			->where('user_id > 0')
			->where('(published >= 1 OR payment_method LIKE "os_offline%")')
			->group('user_id');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('profile_id = ' . $row->profile_id)
				->set('is_profile = 0')
				->where('user_id = ' . $row->user_id);
			$db->setQuery($query)->execute();

			$query->clear()
				->update('#__osmembership_subscribers')
				->set('is_profile  = 1')
				->where('id = ' . $row->profile_id);
			$db->setQuery($query)
				->execute();
		}
	}

	public function fix_profile_id_data()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id, user_id')
			->from('#__osmembership_subscribers')
			->where('user_id > 0')
			->where('is_profile = 1');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('profile_id = ' . $row->id)
				->set('is_profile = 0')
				->where('user_id = ' . $row->user_id);
			$db->setQuery($query)->execute();

			$query->clear()
				->update('#__osmembership_subscribers')
				->set('is_profile  = 1')
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}
	}

	public function fix_profile_id_for_user()
	{
		$userId = $this->input->getInt('user_id');

		if ($userId)
		{
			OSMembershipHelper::fixProfileId($userId);
		}
	}

	/**
	 * Fix data for a subscription record, including main record data and profile data
	 */
	public function fix_subscription_data()
	{
		$id = $this->input->getInt('id');

		$row = Table::getInstance('Subscriber', 'OSMembershipTable');

		if (!$row->load($id))
		{
			echo 'Invalid Subscription';

			return;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Fix profile data
		$row->is_profile = 1;

		if ($row->user_id > 0)
		{
			$query->clear()
				->select('id')
				->from('#__osmembership_subscribers')
				->where('user_id = ' . $row->user_id)
				->where('(published >= 1 OR payment_method LIKE "os_offline%")')
				->where('is_profile = 1');
			$db->setQuery($query);
			$profileId = $db->loadResult();

			if ($profileId && $profileId != $row->id)
			{
				$row->is_profile = 0;
				$row->profile_id = $profileId;
			}
		}

		if ($row->is_profile == 1)
		{
			$row->profile_id = $row->id;
		}

		$row->store();

		$query->clear()
			->select('id, profile_id, plan_id, published, from_date, to_date, plan_main_record')
			->from('#__osmembership_subscribers')
			->where('plan_id = ' . $row->plan_id)
			->where('profile_id = ' . $row->profile_id)
			->where('(published >= 1 OR payment_method LIKE "os_offline%")')
			->order('id');
		$db->setQuery($query);
		$subscriptions = $db->loadObjectList();

		if (!empty($subscriptions))
		{
			$isActive         = false;
			$isPending        = false;
			$isExpired        = false;
			$lastActiveDate   = null;
			$lastExpiredDate  = null;
			$planMainRecordId = 0;
			$planFromDate     = $subscriptions[0]->from_date;

			foreach ($subscriptions as $subscription)
			{
				if ($subscription->plan_main_record)
				{
					$planMainRecordId = $subscription->id;
				}

				if ($subscription->published == 1)
				{
					$isActive       = true;
					$lastActiveDate = $subscription->to_date;
				}
				elseif ($subscription->published == 0)
				{
					$isPending = true;
				}
				elseif ($subscription->published == 2)
				{
					$isExpired       = true;
					$lastExpiredDate = $subscription->to_date;
				}
			}

			if ($isActive)
			{
				$published  = 1;
				$planToDate = $lastActiveDate;
			}
			elseif ($isPending)
			{
				$published = 0;
			}
			elseif ($isExpired)
			{
				$published  = 2;
				$planToDate = $lastExpiredDate;
			}
			else
			{
				$published  = 3;
				$planToDate = $subscription->to_date;
			}

			$query->clear()
				->update('#__osmembership_subscribers')
				->set('plan_subscription_status = ' . (int) $published)
				->set('plan_subscription_from_date = ' . $db->quote($planFromDate))
				->set('plan_subscription_to_date = ' . $db->quote($planToDate))
				->where('plan_id = ' . $row->plan_id)
				->where('profile_id = ' . $row->profile_id);
			$db->setQuery($query);
			$db->execute();

			if (empty($planMainRecordId))
			{
				$planMainRecordId = $subscriptions[0]->id;
				$query->clear()
					->update('#__osmembership_subscribers')
					->set('plan_main_record = 1')
					->where('id = ' . $planMainRecordId);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Method to update to new countries and states database
	 */
	public function update_countries_states_database()
	{
		if (!Factory::getUser()->authorise('core.admin'))
		{
			echo 'You do not have permission to execute this task';
		}

		// We need to change data type for state_2_code and state_3_code so that it can store longer data
		$db = Factory::getDbo();

		$sql = "ALTER TABLE  `#__osmembership_states` CHANGE  `state_2_code` `state_2_code` char(10) DEFAULT NULL";
		$db->setQuery($sql)
			->execute();

		$sql = "ALTER TABLE  `#__osmembership_states` CHANGE  `state_3_code` `state_3_code` char(10) DEFAULT NULL";
		$db->setQuery($sql)
			->execute();

		OSMembershipHelper::executeSqlFile(JPATH_ADMINISTRATOR . '/components/com_osmembership/sql/countries_states.sql');

		echo 'Countries, States database successfully updated';
	}

	public function remove_unused_language_fields()
	{
		if (!Factory::getUser()->authorise('core.admin'))
		{
			echo 'You do not have permission to execute this task';
		}

		$db = Factory::getDbo();

		$categoryTableFields = array_keys($db->getTableColumns('#__osmembership_categories'));
		$planTableFields     = array_keys($db->getTableColumns('#__osmembership_plans'));
		$fieldTableFields    = array_keys($db->getTableColumns('#__osmembership_fields'));

		$query = $db->getQuery(true)
			->select('*')
			->from('#__languages')
			->where('published = 0')
			->order('ordering ASC');
		$db->setQuery($query);
		$languages = $db->loadObjectList();

		foreach ($languages as $language)
		{
			$sef = $language->sef;

			$fields = [
				'alias',
				'title',
				'description',
			];

			foreach ($fields as $field)
			{
				$field .= '_' . $sef;

				if (in_array($field, $categoryTableFields))
				{
					// Drop
					$sql = "ALTER TABLE  `#__osmembership_categories` DROP COLUMN  `$field`;";
					$db->setQuery($sql)
						->execute();
				}
			}

			$fields = [
				'alias',
				'title',
				'page_title',
				'page_heading',
				'meta_keywords',
				'meta_description',
				'user_email_subject',
				'subscription_approved_email_subject',
				'user_renew_email_subject',
				'short_description',
				'description',
				'subscription_form_message',
				'user_email_body',
				'user_email_body_offline',
				'subscription_approved_email_body',
				'thanks_message',
				'thanks_message_offline',
				'user_renew_email_body',
				'user_renew_email_body_offline',
				'renew_thanks_message',
				'renew_thanks_message_offline',
				'user_upgrade_email_body',
				'user_upgrade_email_body_offline',
				'upgrade_thanks_message',
				'upgrade_thanks_message_offline',
			];

			foreach ($fields as $field)
			{
				$field .= '_' . $sef;

				if (in_array($field, $planTableFields))
				{
					// Drop
					$sql = "ALTER TABLE  `#__osmembership_plans` DROP COLUMN  `$field`;";
					$db->setQuery($sql)
						->execute();
				}
			}

			$fields = [
				'title',
				'place_holder',
				'prompt_text',
				'description',
				'values',
				'default_values',
				'fee_values',
				'depend_on_options',
			];

			foreach ($fields as $field)
			{
				$field .= '_' . $sef;

				if (in_array($field, $fieldTableFields))
				{
					// Drop
					$sql = "ALTER TABLE  `#__osmembership_fields` DROP COLUMN  `$field`;";
					$db->setQuery($sql)
						->execute();
				}
			}
		}
	}

	/**
	 * Generate subscription_code for records missing that data
	 */
	public function generate_subscription_codes()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id')
			->from('#__osmembership_subscribers')
			->where('(CHAR_LENGTH(subscription_code) = 0 OR subscription_code IS NULL)');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$subscriptionCode = OSMembershipHelper::getUniqueCodeForField('subscription_code', '#__osmembership_subscribers');
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('subscription_code = ' . $db->quote($subscriptionCode))
				->where('id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Method to drop fields from plans table which are not needed when Simple Multilingual is activated
	 *
	 * @return void
	 */
	public function drop_unnecessary_multilingual_fields()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('sef')
			->from('#__languages');
		$db->setQuery($query);
		$languages = $db->loadObjectList();

		$planTableFields = array_keys($db->getTableColumns('#__osmembership_plans'));
		$planDropFields  = [
			'user_email_subject',
			'subscription_approved_email_subject',
			'user_renew_email_subject',
			'subscription_form_message',
			'user_email_body',
			'user_email_body_offline',
			'subscription_approved_email_body',
			'thanks_message',
			'thanks_message_offline',
			'user_renew_email_body',
			'user_renew_email_body_offline',
			'renew_thanks_message',
			'renew_thanks_message_offline',
			'user_upgrade_email_body',
			'user_upgrade_email_body_offline',
			'upgrade_thanks_message',
			'upgrade_thanks_message_offline',
		];

		foreach ($languages as $language)
		{
			$prefix = $language->sef;

			foreach ($planDropFields as $planDropField)
			{
				$field = $planDropField . '_' . $prefix;

				if (in_array($field, $planTableFields))
				{
					$field = $db->quoteName($field);
					$sql   = "ALTER TABLE #__osmembership_plans DROP COLUMN $field";
					$db->setQuery($sql)
						->execute();
				}
			}
		}
	}

	/**
	 * Add tool to remove PDF invoices to save space
	 */
	public function remove_pdf_invoices()
	{
		$invoicesPath = JPATH_ROOT . '/media/com_osmembership/invoices';

		$files = Folder::files($invoicesPath, '.pdf', false, true);

		foreach ($files as $file)
		{
			File::delete($file);
		}

		echo 'Deleted ' . count($files) . ' PDF invoice files';
	}

	/**
	 * Add tool to remove PDF invoices to save space
	 */
	public function remove_pdf_member_cards()
	{
		$ticketsPath = JPATH_ROOT . '/media/com_osmembership/membercards';

		$files = Folder::files($ticketsPath, '.pdf', false, true);

		foreach ($files as $file)
		{
			File::delete($file);
		}

		echo 'Deleted ' . count($files) . ' PDF member card files';
	}

	/**
	 * Method to remove all subscription records without user account
	 *
	 * @return void
	 */
	public function remove_subscriptions_without_user_account()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__osmembership_subscribers')
			->where('user_id > 0')
			->where('user_id NOT IN (SELECT id FROM #__users)');
		$db->setQuery($query)
			->execute();

		echo 'Done';
	}
}
