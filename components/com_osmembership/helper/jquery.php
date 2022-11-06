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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class OSMembershipHelperJquery
{
	public static function loadjQuery()
	{
		static $loaded = false;

		if ($loaded === false)
		{
			HTMLHelper::_('jquery.framework');

			if (File::exists(JPATH_ROOT . '/media/com_osmembership/assets/js/membershipprojq.min.js'))
			{
				Factory::getDocument()->addScript(Uri::root(true) . '/media/com_osmembership/assets/js/membershipprojq.min.js');
			}

			$loaded = true;
		}
	}

	/**
	 *
	 * Load colorbox library
	 *
	 */
	public static function colorbox()
	{
		static $loaded;

		if ($loaded === true)
		{
			return;
		}

		self::loadjQuery();

		$rootUri  = Uri::root(true);
		$document = Factory::getDocument();
		$document->addStyleSheet($rootUri . '/media/com_osmembership/assets/js/colorbox/colorbox.min.css');
		$document->addScript($rootUri . '/media/com_osmembership/assets/js/colorbox/jquery.colorbox.min.js');

		$activeLanguageTag   = Factory::getLanguage()->getTag();
		$allowedLanguageTags = ['ar-AA', 'bg-BG', 'ca-ES', 'cs-CZ', 'da-DK', 'de-DE', 'el-GR', 'es-ES', 'et-EE',
			'fa-IR', 'fi-FI', 'fr-FR', 'he-IL', 'hr-HR', 'hu-HU', 'it-IT', 'ja-JP', 'ko-KR', 'lv-LV', 'nb-NO', 'nl-NL',
			'pl-PL', 'pt-BR', 'ro-RO', 'ru-RU', 'sk-SK', 'sr-RS', 'sv-SE', 'tr-TR', 'uk-UA', 'zh-CN', 'zh-TW',
		];

		/// English is bundled into the source therefore we don't have to load it.
		if (in_array($activeLanguageTag, $allowedLanguageTags))
		{
			$document->addScript($rootUri . '/media/com_osmembership/assets/js/colorbox/i18n/jquery.colorbox-' . $activeLanguageTag . '.js');
		}

		$loaded = true;
	}

	/**
	 * validate form
	 */
	public static function validateForm()
	{
		self::loadjQuery();

		if (OSMembershipHelper::isMethodOverridden('OSMembershipHelperOverrideJquery', 'validateForm'))
		{
			OSMembershipHelperOverrideJquery::validateForm();

			return;
		}

		$document = Factory::getDocument();
		$config   = OSMembershipHelper::getConfig();

		$dateFormat = $config->date_field_format ?: '%Y-%m-%d';
		$dateFormat = str_replace('%', '', $dateFormat);

		$humanFormat = str_replace('Y', 'YYYY', $dateFormat);
		$humanFormat = str_replace('m', 'MM', $humanFormat);
		$humanFormat = str_replace('d', 'DD', $humanFormat);

		$separator          = '';
		$possibleSeparators = ['.', '-', '/'];

		foreach ($possibleSeparators as $possibleSeparator)
		{
			if (strpos($dateFormat, $possibleSeparator) !== false)
			{
				$separator = $possibleSeparator;
				break;
			}
		}

		$dateParts = explode($separator, $dateFormat);

		$yearIndex  = array_search('Y', $dateParts);
		$monthIndex = array_search('m', $dateParts);
		$dayIndex   = array_search('d', $dateParts);

		$regex = $dateFormat;
		$regex = str_replace($separator, '[\\' . $separator . ']', $regex);
		$regex = str_replace('d', '(0?[1-9]|[12][0-9]|3[01])', $regex);
		$regex = str_replace('Y', '(\d{4})', $regex);
		$regex = str_replace('m', '(0?[1-9]|1[012])', $regex);
		$regex = 'var pattern = new RegExp(/^' . $regex . '$/);';

		$rootUri = Uri::root(true);
		$document->addStyleSheet($rootUri . '/media/com_osmembership/assets/js/validate/css/validationEngine.jquery.min.css');
		$document->addScriptDeclaration("
			var yearPartIndex = $yearIndex;
			var monthPartIndex = $monthIndex;
			var dayPartIndex = $dayIndex;
			var customDateFormat = '$dateFormat';
			$regex
		");

		$document->addScriptOptions('humanFormat', $humanFormat)
			->addScriptOptions('rootUri', $rootUri);

		$languageItems = [
			'OSM_FIELD_REQUIRED',
			'OSM_PLEASE_SELECT_AN_OPTION',
			'OSM_CHECKBOX_REQUIRED',
			'OSM_BOTH_DATE_RANGE_FIELD_REQUIRED',
			'OSM_FIELD_MUST_EQUAL_TEST',
			'OSM_INVALID',
			'OSM_DATE_TIME_RANGE',
			'OSM_CHARACTERS_REQUIRED',
			'OSM_CHACTERS_ALLOWED',
			'OSM_GROUP_REQUIRED',
			'OSM_MIN',
			'OSM_MAX',
			'OSM_DATE_PRIOR_TO',
			'OSM_DATE_PAST',
			'OSM_MAXIMUM',
			'OSM_MINIMUM',
			'OSM_OPTION_ALLOW',
			'OSM_PLEASE_SELECT',
			'OSM_FIELDS_DO_NOT_MATCH',
			'OSM_INVALID_CREDIT_CARD_NUMBER',
			'OSM_INVALID_PHONE_NUMBER',
			'OSM_INVALID_EMAIL_ADDRESS',
			'OSM_NOT_A_VALID_INTEGER',
			'OSM_INVALID_FLOATING_DECIMAL_NUMBER',
			'OSM_INVALID_DATE',
			'OSM_INVALID_IP_ADDRESS',
			'OSM_INVALID_URL',
			'OSM_NUMBER_ONLY',
			'OSM_LETTERS_ONLY',
			'OSM_NO_SPECIAL_CHACTERS_ALLOWED',
			'OSM_INVALID_USERNAME',
			'OSM_INVALID_EMAIL',
			'OSM_INVALID_PASSWORD',
			'OSM_INVALID_DATE',
			'OSM_EXPECTED_FORMAT',
		];

		foreach ($languageItems as $item)
		{
			Text::script($item, true);
		}

		OSMembershipHelperHtml::addOverridableScript('media/com_osmembership/assets/js/validate/js/jquery.validationEngine.lang.min.js');

		if (OSMembershipHelper::isJoomla4())
		{
			$document->addScript($rootUri . '/media/com_osmembership/assets/js/validate/js/j4.jquery.validationEngine.min.js');
		}
		else
		{
			$document->addScript($rootUri . '/media/com_osmembership/assets/js/validate/js/jquery.validationEngine.min.js');
		}
	}

	/**
	 * Equal Heights Plugin
	 * Equalize the heights of elements. Great for columns or any elements
	 * that need to be the same size (floats, etc)
	 */
	public static function equalHeights()
	{
		self::loadjQuery();

		Factory::getDocument()->addScript(Uri::root(true) . '/media/com_osmembership/assets/js/query.equalHeights.min.js');
	}

	/**
	 * Use responsive equal height script to make equal height columns
	 *
	 * @param   string  $selector
	 * @param   int     $minHeight
	 */
	public static function responsiveEqualHeight($selector, $minHeight = 0)
	{
		static $scriptLoaded = false;
		static $loaded = [];

		if (!$scriptLoaded)
		{
			Factory::getDocument()->addScript(Uri::root(true) . '/media/com_osmembership/assets/js/responsive-auto-height.min.js');
		}

		if (isset($loaded[$selector]))
		{
			return true;
		}

		Factory::getDocument()->addScriptDeclaration('
			document.addEventListener("DOMContentLoaded", function() {
				new ResponsiveAutoHeight("' . $selector . '");	
			});
		');

		if ($minHeight > 0)
		{
			Factory::getDocument()->addStyleDeclaration("$selector {min-height: $minHeight" . "px}");

		}

		$loaded[$selector] = true;
	}
}
