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
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class OSMembershipViewConfigurationHtml extends MPFViewHtml
{
	public function display()
	{
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);
		$config = OSMembershipHelper::getConfig();

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 2, Text::_('OSM_VERSION_2'));
		$options[] = HTMLHelper::_('select.option', 3, Text::_('OSM_VERSION_3'));
		$options[] = HTMLHelper::_('select.option', 4, Text::_('OSM_VERSION_4'));
		$options[] = HTMLHelper::_('select.option', 5, Text::_('OSM_VERSION_5'));

		$options[] = HTMLHelper::_('select.option', 'uikit3', Text::_('OSM_UIKIT_3'));

		if (OSMembershipHelper::isJoomla4())
		{
			$defaultTBSVersion = 5;
		}
		else
		{
			$defaultTBSVersion = 2;
		}

		$lists['twitter_bootstrap_version'] = HTMLHelper::_('select.genericlist', $options, 'twitter_bootstrap_version', 'class="form-select"', 'value', 'text', $config->twitter_bootstrap_version ? $config->twitter_bootstrap_version : $defaultTBSVersion);

		$currencies = require_once JPATH_ROOT . '/components/com_osmembership/helper/currencies.php';
		$options    = [];
		$options[]  = HTMLHelper::_('select.option', '', Text::_('OSM_SELECT_CURRENCY'));

		foreach ($currencies as $code => $title)
		{
			$options[] = HTMLHelper::_('select.option', $code, $title);
		}

		$lists['currency_code'] = HTMLHelper::_('select.genericlist', $options, 'currency_code', 'class="form-select chosen"', 'value', 'text', isset($config->currency_code) ? $config->currency_code : 'USD');

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'm', Text::_('OSM_MINUTES'));
		$options[] = HTMLHelper::_('select.option', 'h', Text::_('OSM_HOURS'));
		$options[] = HTMLHelper::_('select.option', 'd', Text::_('OSM_DAYS'));

		$lists['grace_period_unit'] = HTMLHelper::_('select.genericlist', $options, 'grace_period_unit', ' class="input-small form-select d-inline-block" ', 'value', 'text', $config->get('grace_period_unit', 'd'));

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'default', Text::_('OSM_DEFAULT_LAYOUT'));
		$options[] = HTMLHelper::_('select.option', 'columns', Text::_('OSM_COLUMNS_LAYOUT'));

		$lists['subscription_form_layout'] = HTMLHelper::_('select.genericlist', $options, 'subscription_form_layout', 'class="input-large form-select"', 'value', 'text', $config->get('subscription_form_layout', ''));

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'horizontal', Text::_('OSM_HORIZONTAL'));
		$options[] = HTMLHelper::_('select.option', 'columns', Text::_('OSM_STACKED'));

		$lists['form_format'] = HTMLHelper::_('select.genericlist', $options, 'form_format', 'class="input-large form-select"', 'value', 'text', $config->get('form_format', 'horizontal'));

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_SELECT_POSITION'));
		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_BEFORE_AMOUNT'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('OSM_AFTER_AMOUNT'));

		$lists['currency_position'] = HTMLHelper::_('select.genericlist', $options, 'currency_position', ' class="form-select"', 'value', 'text', $config->currency_position);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'use_tooltip', Text::_('OSM_USE_TOOLTIP'));
		$options[] = HTMLHelper::_('select.option', 'under_field_label', Text::_('OSM_UNDER_FIELD_LABEL'));
		$options[] = HTMLHelper::_('select.option', 'under_field_input', Text::_('OSM_UNDER_FIELD_INPUT'));
		$options[] = HTMLHelper::_('select.option', 'next_to_field_input', Text::_('OSM_NEXT_TO_FIELD_INPUT'));

		$lists['display_field_description'] = HTMLHelper::_('select.genericlist', $options, 'display_field_description', 'class="form-select"', 'value', 'text', $config->get('display_field_description', 'use_tooltip'));

		// EU VAT Number field selection
		$query->select('name, title')
			->from('#__osmembership_fields')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_SELECT'), 'name', 'title');
		$options   = array_merge($options, $db->loadObjectList());

		$lists['eu_vat_number_field'] = HTMLHelper::_('select.genericlist', $options, 'eu_vat_number_field', ' class="form-select"', 'name', 'title', $config->eu_vat_number_field);

		//Get list of country
		$query->clear()
			->select('name AS value, name AS text')
			->from('#__osmembership_countries')
			->where('published = 1')
			->order('name');
		$db->setQuery($query);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_SELECT_DEFAULT_COUNTRY'));
		$options   = array_merge($options, $db->loadObjectList());

		$lists['country_list'] = HTMLHelper::_('select.genericlist', $options, 'default_country', 'class="form-select chosen"', 'value', 'text', $config->default_country);

		// Editor
		$query->clear()
			->select($db->quoteName(['element', 'name'], ['value', 'text']))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('editors'))
			->where($db->quoteName('enabled') . ' = 1')
			->order($db->quoteName(['ordering', 'name']));

		$db->setQuery($query);
		$editorPlugins = $db->loadObjectList();

		$lang    = Factory::getLanguage();
		$options = [];

		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_USE_GLOBAL'));

		foreach ($editorPlugins as $editorPlugin)
		{
			$source    = JPATH_PLUGINS . '/editors/' . $editorPlugin->value;
			$extension = 'plg_editors_' . $editorPlugin->value;
			$lang->load($extension . '.sys', JPATH_ADMINISTRATOR) || $lang->load($extension . '.sys', $source);
			$options[] = HTMLHelper::_('select.option', $editorPlugin->value, Text::_($editorPlugin->text));
		}

		$lists['editor'] = HTMLHelper::_('select.genericlist', $options, 'editor', 'class="form-select"', 'value', 'text', $config->get('editor', Factory::getApplication()->get('editor')));

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_SELECT_FORMAT'));
		$options[] = HTMLHelper::_('select.option', '%Y-%m-%d', 'Y-m-d');
		$options[] = HTMLHelper::_('select.option', '%Y/%m/%d', 'Y/m/d');
		$options[] = HTMLHelper::_('select.option', '%Y.%m.%d', 'Y.m.d');
		$options[] = HTMLHelper::_('select.option', '%m-%d-%Y', 'm-d-Y');
		$options[] = HTMLHelper::_('select.option', '%m/%d/%Y', 'm/d/Y');
		$options[] = HTMLHelper::_('select.option', '%m.%d.%Y', 'm.d.Y');
		$options[] = HTMLHelper::_('select.option', '%d-%m-%Y', 'd-m-Y');
		$options[] = HTMLHelper::_('select.option', '%d/%m/%Y', 'd/m/Y');
		$options[] = HTMLHelper::_('select.option', '%d.%m.%Y', 'd.m.Y');

		$lists['date_field_format'] = HTMLHelper::_('select.genericlist', $options, 'date_field_format', 'class="form-select"', 'value', 'text', isset($config->date_field_format) ? $config->date_field_format : 'Y-m-d');

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_NO'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('OSM_YES'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('OSM_ONLY_FOR_PUBLIC_USER'));

		$lists['enable_captcha'] = HTMLHelper::_('select.genericlist', $options, 'enable_captcha', 'class="form-select"', 'value', 'text', $config->enable_captcha);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'csv', Text::_('OSM_FILE_CSV'));
		$options[] = HTMLHelper::_('select.option', 'xls', Text::_('OSM_FILE_EXCEL_2003'));
		$options[] = HTMLHelper::_('select.option', 'xlsx', Text::_('OSM_FILE_EXCEL_2007'));

		$lists['export_data_format'] = HTMLHelper::_('select.genericlist', $options, 'export_data_format', 'class="form-select"', 'value', 'text', empty($config->export_data_format) ? 'xlsx' : $config->export_data_format);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'create_subscription', Text::_('OSM_CREATE_NEW_SUBSCRIPTION'));
		$options[] = HTMLHelper::_('select.option', 'update_subscription', Text::_('OSM_UPDATE_SUBSCRIPTION'));

		$lists['subscription_renew_behavior'] = HTMLHelper::_('select.genericlist', $options, 'subscription_renew_behavior', ' class="input-xlarge form-select" ', 'value', 'text', $config->subscription_renew_behavior ? $config->subscription_renew_behavior : 'create_subscription');

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_PENDING'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('OSM_ACTIVE'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('OSM_EXPIRED'));
		$options[] = HTMLHelper::_('select.option', 3, Text::_('OSM_CANCELLED_PENDING'));
		$options[] = HTMLHelper::_('select.option', 4, Text::_('OSM_CANCELLED_REFUNDED'));

		$lists['export_exclude_status'] = HTMLHelper::_('select.genericlist', $options, 'export_exclude_status', ' multiple ', 'value', 'text', explode(',', $config->get('export_exclude_status', '')));

		// Custom Fee Fields Behavior
		$options = [];

		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_TRIAL_PAYMENT_ONLY'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('OSM_RECURRING_PAYMENT_ONLY'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('OSM_BOTH'));

		$lists['custom_fee_behavior'] = HTMLHelper::_('select.genericlist', $options, 'custom_fee_behavior', 'class="form-select"', 'value', 'text', $config->get('custom_fee_behavior', 2));

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'P', Text::_('Portrait'));
		$options[] = HTMLHelper::_('select.option', 'L', Text::_('Landscape'));

		$lists['card_page_orientation'] = HTMLHelper::_('select.genericlist', $options, 'card_page_orientation', 'class="form-select"', 'value', 'text', $config->get('card_page_orientation', 'P'));

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'A4', Text::_('A4'));
		$options[] = HTMLHelper::_('select.option', 'A5', Text::_('A5'));
		$options[] = HTMLHelper::_('select.option', 'A6', Text::_('A6'));
		$options[] = HTMLHelper::_('select.option', 'A7', Text::_('A7'));

		$lists['card_page_format'] = HTMLHelper::_('select.genericlist', $options, 'card_page_format', 'class="form-select"', 'value', 'text', $config->get('card_page_format', 'A4'));

		$fontsPath = JPATH_ROOT . '/components/com_osmembership/tcpdf/fonts/';

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_SELECT_FONT'));
		$options[] = HTMLHelper::_('select.option', 'courier', Text::_('Courier'));
		$options[] = HTMLHelper::_('select.option', 'helvetica', Text::_('Helvetica'));
		$options[] = HTMLHelper::_('select.option', 'symbol', Text::_('Symbol'));
		$options[] = HTMLHelper::_('select.option', 'times', Text::_('Times New Roman'));
		$options[] = HTMLHelper::_('select.option', 'zapfdingbats', Text::_('Zapf Dingbats'));

		$additionalFonts = [
			'aealarabiya',
			'aefurat',
			'cid0cs',
			'cid0ct',
			'cid0jp',
			'cid0kr',
			'dejavusans',
			'dejavuserif',
			'freemono',
			'freesans',
			'freeserif',
			'hysmyeongjostdmedium',
			'kozgopromedium',
			'kozminproregular',
			'msungstdlight',
			'arial',
		];

		foreach ($additionalFonts as $fontName)
		{
			if (file_exists($fontsPath . $fontName . '.php'))
			{
				$options[] = HTMLHelper::_('select.option', $fontName, ucfirst($fontName));
			}
		}

		// Support True Type Font
		$trueTypeFonts = Folder::files($fontsPath, '.ttf');

		foreach ($trueTypeFonts as $trueTypeFont)
		{
			$options[] = HTMLHelper::_('select.option', $trueTypeFont, $trueTypeFont);
		}

		$lists['pdf_font'] = HTMLHelper::_('select.genericlist', $options, 'pdf_font', ' class="form-select"', 'value', 'text', empty($config->pdf_font) ? 'times' : $config->pdf_font);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', -1, Text::_('Auto'));

		for ($i = 1; $i <= 40; $i++)
		{
			$options[] = HTMLHelper::_('select.option', $i, $i);
		}

		$lists['qrcode_size'] = HTMLHelper::_('select.genericlist', $options, 'qrcode_size', 'class="form-select chosen"', 'value', 'text', $config->get('qrcode_size', 3));

		$keys = [
			'export_exclude_status',
			'currency_code',
			'country_list',
			'eu_vat_number_field',
			'pdf_font',
			'qrcode_size',
		];

		foreach ($keys as $key)
		{
			$lists[$key] = OSMembershipHelperHtml::getChoicesJsSelect($lists[$key]);
		}

		$this->lists     = $lists;
		$this->config    = $config;
		$this->languages = OSMembershipHelper::getLanguages();

		parent::display();
	}
}
