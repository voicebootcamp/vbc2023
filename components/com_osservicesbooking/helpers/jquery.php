<?php
/**
 * @version		   1.7.5
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2011 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

abstract class OSBHelperJquery
{		
	/**
	 * Method to load the colorbox into the document head
	 *
	 * If debugging mode is on an uncompressed version of colorbox is included for easier debugging.
	 *
	 * @param string $class
	 * @param string $width
	 * @param string $height
	 * @param string $iframe
	 * @param string $inline
	 *
	 * @return  void
	 */
	public static function colorbox($class = 'sr-iframe', $width = '80%', $height = '80%', $iframe = "true", $inline = "false", $scrolling = "true")
	{
		static $loaded = false;
		$siteUrl = OSBHelper::getSiteUrl();
		if (!$loaded)
		{
			JHtml::_('stylesheet', $siteUrl . 'media/com_osservicesbooking/assets/js/colorbox/colorbox.css', false, false);
			JHtml::_('script', $siteUrl . 'media/com_osservicesbooking/assets/js/colorbox/jquery.colorbox.min.js', false, false);

			$activeLanguageTag   = JFactory::getLanguage()->getTag();
			$allowedLanguageTags = array('ar-AA', 'bg-BG', 'ca-ES', 'cs-CZ', 'da-DK', 'de-DE', 'el-GR', 'es-ES', 'et-EE',
				'fa-IR', 'fi-FI', 'fr-FR', 'he-IL', 'hr-HR', 'hu-HU', 'it-IT', 'ja-JP', 'ko-KR', 'lv-LV', 'nb-NO', 'nl-NL',
				'pl-PL', 'pt-BR', 'ro-RO', 'ru-RU', 'sk-SK', 'sr-RS', 'sv-SE', 'tr-TR', 'uk-UA', 'zh-CN', 'zh-TW',
			);

			// English is bundled into the source therefore we don't have to load it.
			if (in_array($activeLanguageTag, $allowedLanguageTags))
			{
				JHtml::_('script', $siteUrl . 'media/com_osservicesbooking/assets/js/colorbox/i18n/jquery.colorbox-' . $activeLanguageTag . '.js', false, false);
			}

			$loaded = true;
		}

		if ($class == 'a.edocman-modal')
		{
			$options = array(
				'maxWidth'  => '80%',
				'maxHeight' => '80%',
			);
			$script  = 'jQuery(document).ready(function(){jQuery("' . $class . '").colorbox(' . self::getJSObject($options) . ');});';
		}
		else
		{
			$options = array(
				'iframe'     => $iframe,
				'fastIframe' => false,
				'inline'     => $inline,
				'width'      => $width,
				'height'     => $height,
				'scrolling'  => $scrolling,
			);
			$script  = 'jQuery(document).ready(function(){jQuery(".' . $class . '").colorbox(' . self::getJSObject($options) . ');});';
		}

		JFactory::getDocument()->addScriptDeclaration($script);
	}

	/**
	 * Convert an array to js object
	 *
	 * @param array $array
	 *
	 * @return string
	 */
	public static function getJSObject(array $array = array())
	{
		$object = '{';

		// Iterate over array to build objects
		foreach ((array) $array as $k => $v)
		{
			if (is_null($v))
			{
				continue;
			}

			if ($v === 'true')
			{
				$v = true;
			}

			if ($v === 'false')
			{
				$v = false;
			}

			if (is_bool($v))
			{
				$object .= ' ' . $k . ': ';
				$object .= ($v) ? 'true' : 'false';
				$object .= ',';
			}
			elseif (!is_array($v) && !is_object($v))
			{
				$object .= ' ' . $k . ': ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'" . $v . "'";
				$object .= ',';
			}
			else
			{
				$object .= ' ' . $k . ': ' . self::getJSObject($v) . ',';
			}
		}

		if (substr($object, -1) == ',')
		{
			$object = substr($object, 0, -1);
		}

		$object .= '}';

		return $object;
	}
	
}