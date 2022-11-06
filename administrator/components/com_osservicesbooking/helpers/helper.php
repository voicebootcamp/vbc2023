<?php
/*------------------------------------------------------------------------
# helper.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
defined('_JEXEC') or die();

if (!defined('CAL_GREGORIAN')) 
{
	define('CAL_GREGORIAN', 1);
}

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\CMS\Date\Date;


class OSBHelper
{
	static function getInstalledVersion()
	{
		return '2.19.2';
	}

	/**
	 * Display Copyright information
	 * 
	 */
	public static function displayCopyRight()
	{
		echo '<div class="copyright" style="text-align:center;margin-top: 5px;"><strong><a href="http://joomdonation.com/joomla-extensions/joomla-services-appointment-booking.html" target="_blank">OS Services Booking</a></strong> version <strong>'.self::getInstalledVersion().'</strong>, Copyright (C) '.date('Y').' <strong><a href="http://joomdonation.com" target="_blank">Ossolution Team</a></strong></div>';
	}

	public static function renderSubmenu($task)
	{
		global $jinput;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__app_sch_menus')
			->where('published = 1')
			->where('parent_id = 0')
			->order('ordering');
		$db->setQuery($query);
		$menus = $db->loadObjectList();
		$html = '';
		//$html .= '<div id="submenu-box"><div class="m"><ul class="nav nav-tabs">';
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$html .= '<ul id="mp-dropdown-menu" class="nav nav-tabs nav-hover osb-joomla4">';
		}
		else
		{
			$html .= '<ul id="mp-dropdown-menu" class="nav nav-tabs nav-hover">';
		}
		for ($i = 0; $n = count($menus), $i < $n; $i++)
		{
			$menu = $menus[$i];
			$query->clear();
			$query->select('*')
				->from('#__app_sch_menus')
				->where('published = 1')
				->where('parent_id = ' . intval($menu->id))
				->order('ordering');
			$db->setQuery($query);
			$subMenus = $db->loadObjectList();
			if (!count($subMenus))
			{
				$class = '';
				if ($menu->menu_task == $task)
				{
					$class = ' class="active"';
					$extraClass = 'active';
				}
				else
				{
					$class = '';
					$extraClass = '';
				}
				$html .= '<li' . $class . '>' ;
				$html .= '<a class="nav-link dropdown-item ' . $extraClass . '" href="index.php?option=com_osservicesbooking&task=' . $menu->menu_task . '">';
				if($menu->menu_icon != ""){
					$html .= '<i class="'.$menu->menu_icon.'"></i>&nbsp;';
				}
				$html .= JText::_($menu->menu_name) . '</a></li>';
			}
			else
			{
				$class = ' class="dropdown"';
				for ($j = 0; $m = count($subMenus), $j < $m; $j++)
				{
					$subMenu = $subMenus[$j];
					$lName = $jinput->get('layout','','string');
					if ( $task == $subMenu->menu_task )
					{
						$class = ' class="dropdown active"';
						break;
					}
					else
					{

						$taskArr = explode("_",$task);
						$task1   = $taskArr[0];
						$taskArr = explode("_",$subMenu->menu_task);
						$task2   = $taskArr[0];
						$class = ' class="dropdown"';
						if ( $task1 == $task2 && $task != "cpanel_list" && $task != "cpanel_optimizedatabase" && $task != "service_specialrates" && $task != "service_list")
						{
							$class = ' class="dropdown active"';
							break;
						}
						
					}
				}
				$html .= '<li' . $class . '>';
				if(OSBHelper::isJoomla4())
				{
					$dropdownToggle = 'data-bs-toggle="dropdown"';
				}
				else
				{
					$dropdownToggle = 'data-toggle="dropdown"';
				}
				$html .= '<a id="drop_' . $menu->id . '" href="#" '.$dropdownToggle.' role="button" class="nav-link dropdown-toggle">' ;
				if($menu->menu_icon != ""){
					$html .= '<i class="'.$menu->menu_icon.'"></i>&nbsp;';
				}
				$html .= JText::_($menu->menu_name) . ' <b class="caret"></b></a>';
				$html .= '<ul aria-labelledby="drop_' . $menu->id . '" role="menu" class="dropdown-menu" id="menu_' . $menu->id . '">';
				for ($j = 0; $m = count($subMenus), $j < $m; $j++)
				{
					$subMenu = $subMenus[$j];
					$layoutLink = '';
					$class = '';
					$lName = $jinput->get('layout','','string');
					if ((!$subMenu->menu_layout && $task == $subMenu->menu_task ) || ($lName != '' && $lName == $subMenu->menu_layout))
					{
						$class = ' class="active"';
						$extraClass = 'active';
					}
					else
					{
						$class = '';
						$extraClass = '';
					}
					$html .= '<li' . $class . '><a class="nav-link dropdown-item ' . $extraClass . '" href="index.php?option=com_osservicesbooking&task=' .
						 $subMenu->menu_task . $layoutLink . '" tabindex="-1">' . JText::_($subMenu->menu_name) . '</a></li>';
				}
				$html .= '</ul>';
				$html .= '</li>';
			}
		}
		$html .= '</ul></div></div>';
		if (version_compare(JVERSION, '3.0', 'le'))
		{
			JFactory::getDocument()->setBuffer($html, array('type' => 'modules', 'name' => 'submenu'));
		}
		else
		{
			echo $html;
		}
	}

	public static function loadBootstrap($loadJs = true)
	{
		$document = JFactory::getDocument();
		$configClass = self::loadConfig();
		if($configClass['load_bootstrap'])
		{
			if(self::isJoomla4())
			{
				if ($loadJs)
				{
					JHtml::_('jquery.framework');
					HTMLHelper::_('bootstrap.framework');
				}
				HTMLHelper::_('bootstrap.loadCss');
			}
			else
			{
				//joomla3
				if ($loadJs)
				{
					JHtml::_('jquery.framework');
					$document->addScript(JUri::root() . 'media/com_osservicesbooking/assets/css/bootstrap/js/jquery.min.js');
					$document->addScript(JUri::root() . 'media/com_osservicesbooking/assets/css/bootstrap/js/jquery-noconflict.js');
					$document->addScript(JUri::root() . 'media/com_osservicesbooking/assets/css/bootstrap/js/bootstrap.js');
					//HTMLHelper::_('bootstrap.framework');
				}
				$document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bootstrap/css/bootstrap.css');
				$document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bootstrap/css/bootstrap-responsive.css');
				//HTMLHelper::_('bootstrap.loadCss');
			}
		}
	}
	
	public static function loadBootstrapStylesheet(){
		$configClass = self::loadConfig();
		$document = JFactory::getDocument();
		if(!JFactory::getApplication()->isClient('administrator'))
		{
			if((int)$configClass['bootstrap_version'] == 0){
				$document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bootstrap/css/bootstrap.css');
				$document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bootstrap/css/bootstrap-responsive.css');
				$document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bs2.css');
			}else{
				$document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bs3.css');
			}
		}
	}

	public static function loadMedia($loadJs = true)
	{
	    $configClass = self::loadConfig();
	    $rootUrl = JUri::root();
		$document = JFactory::getDocument();
		if($configClass['waiting_list'] && $configClass['waiting_window_type'] == 0)
		{
			require_once JPATH_ROOT .'/components/com_osservicesbooking/helpers/jquery.php';
			if(!self::isJoomla4())
			{
				JHTML::_('behavior.modal','osmodal');
			}
			else
			{
				OSBHelperJquery::colorbox('osmodal');
			}
        }
		
		JHtml::_('jquery.framework');
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/ajax.js");
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/paymentmethods.js");
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/javascript.js");
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/osbjq.min.js");
		$document->addStyleSheet(JURI::root()."media/com_osservicesbooking/assets/css/style.css");
		if(self::isJoomla4())
		{
			$document->addStyleSheet(JURI::root()."media/com_osservicesbooking/assets/css/style4.css");
		}
		if (file_exists(JPATH_ROOT . '/media/com_osservicesbooking/assets/css/custom.css') && filesize(JPATH_ROOT . '/media/com_osservicesbooking/assets/css/custom.css') > 0)
		{
			$document->addStylesheet($rootUrl . 'media/com_osservicesbooking/assets/css/custom.css');
		}
		if($configClass['load_button_style'] == 1)
		{
			$document->addStylesheet($rootUrl . 'media/com_osservicesbooking/assets/css/button.css');
		}
        if((int)$configClass['bootstrap_version'] == 0)
		{
            $document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bs2.css');
        }
		elseif((int)$configClass['bootstrap_version'] == 1)
		{
            $document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bs3.css');
        }
		elseif((int)$configClass['bootstrap_version'] == 2)
		{
            $document->addStyleSheet(JURI::root() . 'media/com_osservicesbooking/assets/css/bs4.css');
        }

		if ($loadJs && self::isJoomla4())
		{
			//$document->addScript(JUri::root() . 'media/com_osservicesbooking/assets/css/bootstrap/js/jquery.min.js');
			//$document->addScript(JUri::root() . 'media/com_osservicesbooking/assets/css/bootstrap/js/jquery-noconflict.js');
			//$document->addScript(JUri::root() . 'media/com_osservicesbooking/assets/css/bootstrap/js/bootstrap.js');
			HTMLHelper::_('bootstrap.framework');
		}
	}
	
	/**
	 * This static function is used to load Config and return the Configuration Variable
	 *
	 */
	public static function loadConfig(){
		$db = Jfactory::getDbo();
		$db->setQuery("Select * from #__app_sch_configuation");
		$configs = $db->loadObjectList();
		$configClass = array();
		foreach ($configs as $config) {
			$configClass[$config->config_key] = $config->config_value;
		}
		if($configClass['currency_format'] == "")
		{
			$configClass['currency_format'] = "USD";
		}
		$db->setQuery("Select currency_symbol from #__app_sch_currencies where currency_code like '".$configClass['currency_format']."'");
		$currency_symbol = $db->loadResult();
		
		$configClass['currency_symbol'] = $currency_symbol;

        if((JFactory::getUser()->id > 0) and ($configClass['group_payment'] > 0)){
            $db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '".JFactory::getUser()->id."' and group_id = '".$configClass['group_payment']."'");
            $count = $db->loadResult();
            if($count > 0){
                $configClass['disable_payments'] = 0;
            }
        }
		return $configClass;
	}
	
/**
	 * Get field suffix used in sql query
	 *
	 * @return string
	 */
	public static function getFieldSuffix($activeLanguage = null)
	{
		$prefix = '';
		if (JLanguageMultilang::isEnabled())
		{
			if (!$activeLanguage)
				$activeLanguage = JFactory::getLanguage()->getTag();
			if ($activeLanguage != self::getDefaultLanguage())
			{
				$prefix = '_' . substr($activeLanguage, 0, 2);
			}
		}
		return $prefix;
	}
	
	
	/**
	 *
	 * static function to get all available languages except the default language
	 * @return languages object list
	 */
	public static function getAllLanguages()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$default = self::getDefaultLanguage();
		$query->select('lang_id, lang_code, title, `sef`')
			->from('#__languages')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$languages = $db->loadObjectList();
		return $languages;
	}

	/**
	 *
	 * static function to get all available languages except the default language
	 * @return languages object list
	 */
	public static function getLanguages()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$default = self::getDefaultLanguage();
		$query->select('lang_id, lang_code, title, `sef`')
			->from('#__languages')
			->where('published = 1')
			->where('lang_code != "' . $default . '"')
			->order('ordering');
		$db->setQuery($query);
		$languages = $db->loadObjectList();
		return $languages;
	}

	/**
	 * Get front-end default language
	 * @return string
	 */
	public static function getDefaultLanguage()
	{
		$params = JComponentHelper::getParams('com_languages');
		return $params->get('site', 'en-GB');
	}
	
	/**
	 * Get default language of user
	 *
	 */
	public static function getUserLanguage($user_id){
		$default_language = self::getDefaultLanguage();
		if($user_id > 0){
			$user = JFactory::getUser($user_id);
			$default_language = $user->getParam('language',$default_language);
		}else{
			return $default_language;
		}
		return $default_language;
	}
	
	public static function getLanguageFieldValue($obj,$fieldname){
		global $languages;
		$lgs = self::getLanguages();
		$translatable = JLanguageMultilang::isEnabled() && count($lgs);
		if($translatable){
			$suffix = self::getFieldSuffix();
			$returnValue = $obj->{$fieldname.$suffix};
			if($returnValue == ""){
				$returnValue = $obj->{$fieldname};
			}
		}else{
			$returnValue = $obj->{$fieldname};
		}
		return $returnValue;
	}
	
	public static function getLanguageFieldValueOrder($obj,$fieldname,$lang = ""){
		global $languages;
		$lgs = self::getLanguages();
		$translatable = JLanguageMultilang::isEnabled() && count($lgs);
		$default_language = self::getDefaultLanguage();
		if($lang == ""){
			$lang = $default_language;
		}
		if($translatable){
			//$suffix = self::getFieldSuffix();
			if($default_language != $lang){
				$langugeArr = explode("-",$lang);
				$suffix = "_".$langugeArr[0];
			}
			$returnValue = $obj->{$fieldname.$suffix};
			if($returnValue == ""){
				$returnValue = $obj->{$fieldname};
			}
		}else{
			$returnValue = $obj->{$fieldname};
		}
		return $returnValue;
	}
	
	public static function getLanguageFieldValueBackend($obj,$fieldname,$suffix){
		global $languages;
		$lgs = self::getLanguages();
		$translatable = JLanguageMultilang::isEnabled() && count($lgs);
		if($translatable){
			$returnValue = $obj->{$fieldname.$suffix};
			if($returnValue == ""){
				$returnValue = $obj->{$fieldname};
			}
		}else{
			$returnValue = $obj->{$fieldname};
		}
		return $returnValue;
	}
	
	/**
	 * This static function is used to check to see whether we need to update the database to support multilingual or not
	 *
	 * @return boolean
	 */
	public static function isSyncronized()
	{
		$db = JFactory::getDbo();
		//#__osrs_tags
		$fields = array_keys($db->getTableColumns('#__app_sch_venues'));
		$extraLanguages = self::getLanguages();
		if (count($extraLanguages))
		{
			foreach ($extraLanguages as $extraLanguage)
			{
				$prefix = $extraLanguage->sef;
				if (!in_array('address_' . $prefix, $fields))
				{
					return false;
				}
			}
		}
		
		//app_sch_emails
		$fields = array_keys($db->getTableColumns('#__app_sch_emails'));
		$extraLanguages = self::getLanguages();
		if (count($extraLanguages))
		{
			foreach ($extraLanguages as $extraLanguage)
			{
				$prefix = $extraLanguage->sef;
				if (!in_array('email_subject_' . $prefix, $fields))
				{
					return false;
				}
			}
		}
		
		//app_sch_services
		$fields = array_keys($db->getTableColumns('#__app_sch_services'));
		$extraLanguages = self::getLanguages();
		if (count($extraLanguages))
		{
			foreach ($extraLanguages as $extraLanguage)
			{
				$prefix = $extraLanguage->sef;
				if (!in_array('service_name_' . $prefix, $fields))
				{
					return false;
				}
			}
		}
		
		//app_sch_categories
		$fields = array_keys($db->getTableColumns('#__app_sch_categories'));
		$extraLanguages = self::getLanguages();
		if (count($extraLanguages))
		{
			foreach ($extraLanguages as $extraLanguage)
			{
				$prefix = $extraLanguage->sef;
				if (!in_array('category_name_' . $prefix, $fields))
				{
					return false;
				}
			}
		}
		
		//app_sch_fields
		$fields = array_keys($db->getTableColumns('#__app_sch_fields'));
		$extraLanguages = self::getLanguages();
		if (count($extraLanguages))
		{
			foreach ($extraLanguages as $extraLanguage)
			{
				$prefix = $extraLanguage->sef;
				if (!in_array('field_label_' . $prefix, $fields))
				{
					return false;
				}
				if (!in_array('message_' . $prefix, $fields))
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Syncronize OS Services Booking database to support multilingual
	 */
	public static function setupMultilingual()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$languages = self::getLanguages();
		if (count($languages))
		{
			//venue table
			$db->setQuery("SHOW COLUMNS FROM #__app_sch_venues");
			$fields = $db->loadObjectList();
			if(count($fields) > 0){
				$fieldArr = array();
				for($i=0;$i<count($fields);$i++){
					$field = $fields[$i];
					$fieldname = $field->Field;
					$fieldArr[$i] = $fieldname;
				}
			}
			foreach ($languages as $language)
			{
				#Process for #__osrs_states table
				$prefix = $language->sef;
				if (!in_array('address_' . $prefix, $fieldArr))
				{
					$fieldName = 'address_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_venues` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();
					
					$fieldName = 'city_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_venues` ADD  `$fieldName` VARCHAR( 50 );";
					$db->setQuery($sql);
					$db->execute();
					
					$fieldName = 'state_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_venues` ADD  `$fieldName` VARCHAR( 50 );";
					$db->setQuery($sql);
					$db->execute();
				}
			}
			
			$db->setQuery("SHOW COLUMNS FROM #__app_sch_emails");
			$fields = $db->loadObjectList();
			if(count($fields) > 0){
				$fieldArr = array();
				for($i=0;$i<count($fields);$i++){
					$field = $fields[$i];
					$fieldname = $field->Field;
					$fieldArr[$i] = $fieldname;
				}
			}
			foreach ($languages as $language)
			{
				#Process for #__osrs_states table
				$prefix = $language->sef;
				if (!in_array('email_subject_' . $prefix, $fieldArr))
				{
					$fieldName = 'email_subject_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_emails` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();
					
					$fieldName = 'email_content_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_emails` ADD  `$fieldName` TEXT;";
					$db->setQuery($sql);
					$db->execute();
				}
			}
			
			
			$db->setQuery("SHOW COLUMNS FROM #__app_sch_services");
			$fields = $db->loadObjectList();
			if(count($fields) > 0){
				$fieldArr = array();
				for($i=0;$i<count($fields);$i++){
					$field = $fields[$i];
					$fieldname = $field->Field;
					$fieldArr[$i] = $fieldname;
				}
			}
			foreach ($languages as $language)
			{
				#Process for #__osrs_states table
				$prefix = $language->sef;
				if (!in_array('service_name_' . $prefix, $fieldArr))
				{
					$fieldName = 'service_name_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_services` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();
					
					$fieldName = 'service_description_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_services` ADD  `$fieldName` TEXT;";
					$db->setQuery($sql);
					$db->execute();
				}
			}
			
			$db->setQuery("SHOW COLUMNS FROM #__app_sch_categories");
			$fields = $db->loadObjectList();
			if(count($fields) > 0){
				$fieldArr = array();
				for($i=0;$i<count($fields);$i++){
					$field = $fields[$i];
					$fieldname = $field->Field;
					$fieldArr[$i] = $fieldname;
				}
			}
			foreach ($languages as $language)
			{
				#Process for #__osrs_states table
				$prefix = $language->sef;
				if (!in_array('category_name_' . $prefix, $fieldArr))
				{
					$fieldName = 'category_name_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_categories` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();
					
					$fieldName = 'category_description_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_categories` ADD  `$fieldName` TEXT;";
					$db->setQuery($sql);
					$db->execute();
				}
			}
			
			
			$db->setQuery("SHOW COLUMNS FROM #__app_sch_fields");
			$fields = $db->loadObjectList();
			if(count($fields) > 0){
				$fieldArr = array();
				for($i=0;$i<count($fields);$i++){
					$field = $fields[$i];
					$fieldname = $field->Field;
					$fieldArr[$i] = $fieldname;
				}
			}
			foreach ($languages as $language)
			{
				#Process for #__osrs_states table
				$prefix = $language->sef;
				if (!in_array('field_label_' . $prefix, $fieldArr))
				{
					$fieldName = 'field_label_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_fields` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();
				}
				if (!in_array('message_' . $prefix, $fieldArr))
				{
					$fieldName = 'message_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_fields` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();
				}
			}
			
			$db->setQuery("SHOW COLUMNS FROM #__app_sch_field_options");
			$fields = $db->loadObjectList();
			if(count($fields) > 0){
				$fieldArr = array();
				for($i=0;$i<count($fields);$i++){
					$field = $fields[$i];
					$fieldname = $field->Field;
					$fieldArr[$i] = $fieldname;
				}
			}
			foreach ($languages as $language)
			{
				#Process for #__osrs_states table
				$prefix = $language->sef;
				if (!in_array('field_option_' . $prefix, $fieldArr))
				{
					$fieldName = 'field_option_' . $prefix;
					$sql = "ALTER TABLE  `#__app_sch_field_options` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
	}
	
	public static function getCurrentDate(){
		$config = new JConfig();
		$offset = $config->offset;
		$ctoday = strtotime(JFactory::getDate('now',$offset));
		return $ctoday;
	}
	
	public static function checkDate($date_type){
		$ctoday = self::getCurrentDate();
		$return = array();
		switch ($date_type){
			case "today":
				$start_time = strtotime(date("Y-m-d",$ctoday)." 00:00:01");
				$end_time   = strtotime(date("Y-m-d",$ctoday)." 23:59:59");
				$return[0]  = $start_time;
				$return[1]  = $end_time;
			break;
			case "yesterday":
				$yesterday  = $ctoday -  3600*24;
				$start_time = strtotime(date("Y-m-d",$yesterday)." 00:00:01");
				$end_time   = strtotime(date("Y-m-d",$yesterday)." 23:59:59");
				$return[0]  = $start_time;
				$return[1]  = $end_time;
			break;
			case "current_month": 
				$cmonth		= date("m",$ctoday);
				$start_time = strtotime(date("Y",$ctoday)."-".$cmonth."-01 00:00:01");
				$end_time   = $ctoday;
				$return[0]  = $start_time;
				$return[1]  = $end_time;
			break;
			case "last_month":
				$cmonth		= intval(date("m",$ctoday));
				$cyear		= date("Y",$ctoday);
				if($cmonth == 1){
					$lmonth = 12;
					$lyear  = $cyear-1;
				}else{
					$lmonth = $cmonth - 1;
					$lyear  = $cyear;
				}
				$start_time = strtotime($lyear."-".$lmonth."-01 00:00:01");
				$starttimethismonth = strtotime($cyear."-".$cmonth."-01 00:00:00");
				$end_time   = $starttimethismonth - 1;
				$return[0]  = $start_time;
				$return[1]  = $end_time;
			break;
			case "current_year":
				$cyear		= date("Y",$ctoday);
				$start_time = strtotime($cyear."-01-01 00:00:01");
				$end_time   = $ctoday;
				$return[0]  = $start_time;
				$return[1]  = $end_time;
			break;
			case "last_year":
				$cyear		= date("Y",$ctoday);
				$lyear		= $cyear - 1;
				$start_time = strtotime($lyear."-01-01 00:00:01");
				$starttimethisyear = strtotime($cyear."-01-01 00:00:00");
				$end_time   = strtotime($starttimethisyear-1);
				$return[0]  = $start_time;
				$return[1]  = $end_time;
			break;
		}
		return $return;
	}
	
	/**
	 * Init Availability calendar for Employee In Backend
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	static function initCalendarInBackend($eid,$year,$month){
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);	
		include_once(JPATH_COMPONENT_ADMINISTRATOR."/classes/ajax.php");
		$today						= self::getCurrentDate();
		$current_month 				= intval(date("m",$today));
		$current_year				= intval(date("Y",$today));
		$current_date				= intval(date("d",$today));
		//set up the first date
		$start_date_current_month 	= strtotime($year."-".$month."-01");
		if($configClass['start_day_in_week'] == "monday")
		{
			$start_date_in_week		= date("N",$start_date_current_month);
		}
		else
		{
			$start_date_in_week		= date("w",$start_date_current_month);	
		}
		
		$number_days_in_month		= self::cal_days_in_month(CAL_GREGORIAN,$month,$year);
		
		$monthArr = array( JText::_('OS_JANUARY'), JText::_('OS_FEBRUARY'), JText::_('OS_MARCH'), JText::_('OS_APRIL'), JText::_('OS_MAY'), JText::_('OS_JUNE'), JText::_('OS_JULY'), JText::_('OS_AUGUST'), JText::_('OS_SEPTEMBER'), JText::_('OS_OCTOBER'), JText::_('OS_NOVEMBER'), JText::_('OS_DECEMBER'));
		
		$suffix = "";
		if(!$mainframe->isClient('administrator')){
			$suffix = "_front";
		}
		?>
		<div id="cal<?php echo intval($month)?><?php echo $year?>">
			<table  width="100%" class="apptable">
				<thead>
					<tr>
						<th width="40%" align="right" style="span-weight:bold;span-size:15px;">
							<a href="javascript:prevBigCal<?php echo $suffix;?>(2,'<?php echo $eid?>','<?php echo JUri::base();?>')" class="applink">
							<strong><</strong>
							</a>
						</th>
						<th width="20%" align="center" style="height:25px;span-weight:bold;">
							<?php
							echo $monthArr[$month-1];
							?>
							&nbsp;
							<?php echo $year;?>
						</th>
						<th width="40%" align="left" style="span-weight:bold;span-size:15px;">
							<a href="javascript:nextBigCal<?php echo $suffix;?>(2,'<?php echo $eid?>','<?php echo JUri::base();?>')" class="applink">
							<strong>></strong>
							</a>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="100%" colspan="3" style="padding:3px;text-align:center;">
							<select name="ossm" class="input-small form-select form-control" id="ossm" onchange="javascript:updateMonth(this.value)">
								<?php							
								for($i=0;$i<count($monthArr);$i++)
								{
									if(intval($month) == $i + 1)
									{
										$selected = "selected";
									}
									else
									{
										$selected = "";
									}
									?>
									<option value="<?php echo $i + 1?>" <?php echo $selected?>><?php echo $monthArr[$i]?></option>
									<?php
								}
								?>
							</select>
							<select name="ossy" class="input-small form-select form-control" id="ossy" onchange="javascript:updateYear(this.value)">
								<?php
								for($i=date("Y",$today);$i<=date("Y",$today)+3;$i++){
									if(intval($year) == $i){
										$selected = "selected";
									}else{
										$selected = "";
									}
									?>
									<option value="<?php echo $i?>" <?php echo $selected?>><?php echo $i?></option>
									<?php
								}
								?>
							</select>
							<input type="button" class="goBtn btn btn-primary" value="<?php echo JText::_('OS_GO');?>" onclick="javascript:calendarMovingBigCal<?php echo $suffix;?>(2,'<?php echo $eid?>','<?php echo JUri::base();?>');">
						</td>
					</tr>
				</tbody>
			</table>
			<table width="100%" class="mainTable" style="margin-top:5px;">
				<thead>
					<tr>
						<?php
						if($configClass['start_day_in_week'] == "sunday")
						{
						?>
							<th width="14%">
								<span class="header_rounded">
									<?php echo JText::_('OS_SUN')?>
								</span>
							</th>
						<?php
						}			
						?>
						<th  width="14%">
							<span class="header_rounded">
								<?php echo JText::_('OS_MON')?>
							</span>
						</th>
						<th width="14%">
							<span class="header_rounded">
								<?php echo JText::_('OS_TUE')?>
							</span>
						</th>
						<th width="14%">
							<span class="header_rounded">
								<?php echo JText::_('OS_WED')?>
							</span>
						</th>
						<th width="14%">
							<span class="header_rounded">
								<?php echo JText::_('OS_THU')?>
							</span>
						</th>
						<th width="14%">
							<span class="header_rounded">
								<?php echo JText::_('OS_FRI')?>
							</span>
						</th>
						<th width="14%">
							<span class="header_rounded">
								<?php echo JText::_('OS_SAT')?>
							</span>
						</th>
						<?php
						if($configClass['start_day_in_week'] == "monday")
						{
						?>
						<th width="14%">
							<span class="header_rounded">
								<?php echo JText::_('OS_SUN')?>
							</span>
						</th>
						<?php
						}
						?>
					</tr>
				</thead>
				<tr>
					<?php
					if($configClass['start_day_in_week'] == "sunday")
					{
						$start = 0;
					}
					else
					{
						$start = 1;
					}
					for($i=$start;$i<$start_date_in_week;$i++){
						//empty
						?>
						<td>
						
						</td>
						<?php
					}
					$j = $start_date_in_week-1;
					
					$m = "";
					if(intval($month) < 10){
						$m = "0".$month;
					}else{
						$m = $month;
					}
					$month = $m;
					
					for($i=1;$i<=$number_days_in_month;$i++)
					{
						$j++;
						$nolink = 0;
						//check to see if today
						if($i == $current_date && $month == $current_month && $year == $current_year)
						{
							$bgcolor = "pink";
						}
						else
						{
							$bgcolor = "#F1F1F1";
						}
						
						if($i < 10)
						{
							$day = "0".$i;
						}
						else
						{
							$day = $i;
						}
						$tempdate1 = strtotime($year."-".$month."-".$day);
						$tempdate2 = strtotime($current_year."-".$current_month."-".$current_date);
						
						if($tempdate1 < $tempdate2){
							$bgcolor = "#ABAAB2";
							$nolink = 4;
						}
						
						if($i < 10)
						{
							$day = "0".$i;
						}
						else
						{
							$day = $i;
						}
						$date = $year."-".$month."-".$day;
						?>
						<td id="td_cal_<?php echo $i?>"  align="center" class="td_date" style="text-align:center;" valign="top">
							<div id="a<?php echo $i;?>" class="div-rounded">
								<?php
								self::calendarItemAjax($i,$eid,$date);
								?>
							</div>
						</td>
						<?php
						if($configClass['start_day_in_week'] == "sunday")
						{
							if($j >= 6)
							{
								$j = -1;
								echo "</tr><tr>";
							}
						}
						else
						{
							if($j >= 7)
							{
								$j = 0;
								echo "</tr><tr>";
							}
						}
					}
					?>
				</tr>
			</table>
			<input type="hidden" name="current_item_value" id="current_item_value" value="" />
		</div>
		<?php
	}
	
	/**
	 * Init Availability calendar for Employee In Backend
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	static function initEmployeeCalendar($eid,$year,$month)
	{
		global $mainframe,$configClass,$jinput;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		//JHTML::_('behavior.modal','a.osmodal');
		if(version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			JHTML::_('behavior.modal','a.osmodal');
		}
		else
		{
			OSBHelperJquery::colorbox('osmodal');
		}
		$db = JFactory::getDbo();
		include_once(JPATH_COMPONENT_ADMINISTRATOR."/classes/ajax.php");
		$today						= self::getCurrentDate();
		$current_month 				= intval(date("m",$today));
		$current_year				= intval(date("Y",$today));
		$current_date				= intval(date("d",$today));
		//set up the first date
		$start_date_current_month 	= strtotime($year."-".$month."-01");
		$start_date_in_week			= date("N",$start_date_current_month);
		
		$number_days_in_month		= self::cal_days_in_month(CAL_GREGORIAN,$month,$year);
		
		$monthArr = array( JText::_('OS_JANUARY'), JText::_('OS_FEBRUARY'), JText::_('OS_MARCH'), JText::_('OS_APRIL'), JText::_('OS_MAY'), JText::_('OS_JUNE'), JText::_('OS_JULY'), JText::_('OS_AUGUST'), JText::_('OS_SEPTEMBER'), JText::_('OS_OCTOBER'), JText::_('OS_NOVEMBER'), JText::_('OS_DECEMBER'));
		//$monthArr = array("January","February","March","April","May","June","July","August","September","October","November","December");
		?>
		<div id="cal<?php echo intval($month)?><?php echo $year?>">
			<table  width="100%" class="apptable">
				<tr>
					<td width="40%" align="right" style="span-weight:bold;span-size:15px;">
						<a href="javascript:prevBigCal(1,'<?php echo $eid?>','<?php echo JUri::root();?>')" class="applink">
						<strong><</strong>
						</a>
					</td>
					<td width="20%" align="center" style="height:25px;span-weight:bold;">
						<?php
						echo $monthArr[$month-1];
						?>
						&nbsp;
						<?php echo $year;?>
					</td>
					<td width="40%" align="left" style="span-weight:bold;span-size:15px;">
						<a href="javascript:nextBigCal(1,'<?php echo $eid?>','<?php echo JUri::root();?>')" class="applink">
						<strong>></strong>
						</a>
					</td>
				</tr>
				<tr>
					<td width="100%" colspan="3" style="padding:3px;text-align:center;">
						<select name="ossm" class="input-small form-select imedium" id="ossm" onchange="javascript:updateMonth(this.value)">
							<?php							
							for($i=0;$i<count($monthArr);$i++){
								if(intval($month) == $i + 1){
									$selected = "selected";
								}else{
									$selected = "";
								}
								?>
								<option value="<?php echo $i + 1?>" <?php echo $selected?>><?php echo $monthArr[$i]?></option>
								<?php
							}
							?>
						</select>
						<select name="ossy" class="input-small form-select ishort" id="ossy" onchange="javascript:updateYear(this.value)">
							<?php
							for($i=date("Y",$today);$i<=date("Y",$today)+3;$i++){
								if(intval($year) == $i){
									$selected = "selected";
								}else{
									$selected = "";
								}
								?>
								<option value="<?php echo $i?>" <?php echo $selected?>><?php echo $i?></option>
								<?php
							}
							?>
						</select>
						<input type="button" class="button btn btn-primary" value="Go" onclick="javascript:calendarMovingBigCal(1,'<?php echo $eid?>','<?php echo JUri::root();?>');">
					</td>
				</tr>
			</table>
			<table  width="100%">
				<tr>
					<td  width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_MON')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_TUE')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_WED')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_THU')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_FRI')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_SAT')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_SUN')?>
						</span>
					</td>
				</tr>
				<tr>
					<?php
					for($i=1;$i<$start_date_in_week;$i++){
						//empty
						?>
						<td>
						
						</td>
						<?php
					}
					$j = $start_date_in_week-1;
					
					$m = "";
					if(intval($month) < 10){
						$m = "0".$month;
					}else{
						$m = $month;
					}
					$month = $m;
					
					for($i=1;$i<=$number_days_in_month;$i++){
						$j++;
						$nolink = 0;
						//check to see if today
						if(($i == $current_date) and ($month == $current_month) and ($year == $current_year)){
							$bgcolor = "pink";
						}else{
							$bgcolor = "#F1F1F1";
						}
						
						if($i < 10){
							$day = "0".$i;
						}else{
							$day = $i;
						}
						$tempdate1 = strtotime($year."-".$month."-".$day);
						$tempdate2 = strtotime($current_year."-".$current_month."-".$current_date);
						
						if($tempdate1 < $tempdate2){
							$bgcolor = "#ABAAB2";
							$nolink = 4;
						}
						
						if($i < 10){
							$day = "0".$i;
						}else{
							$day = $i;
						}
						$date = $year."-".$month."-".$day;
						?>
						<td id="td_cal_<?php echo $i?>"  align="center" class="td_date" style="text-align:center;" valign="top">
							<?php
							$db->setQuery("SELECT COUNT(id) FROM #__app_sch_employee_rest_days WHERE eid = '$eid' AND rest_date <= '$date' AND rest_date_to >= '$date'");
							$rest = $db->loadResult();
							if($rest > 0){
								$divname = "div-rounded-rest";
							}else if($date == date("Y-m-d",OSBHelper::getCurrentDate())){
								$divname = "div-rounded-current";
							}else{
								$divname = "div-rounded";
							}
							?>
							<div id="a<?php echo $i;?>" style="border:1px solid #efefef;width:90%" class="<?php echo $divname?>">
								<?php
								self::calendarEmployeeItemAjax($i,$eid,$date);
								?>
							</div>
						</td>
						<?php
						if($j >= 7){
							$j = 0;
							echo "</tr><tr>";
						}
						
					}
					?>
				</tr>
			</table>
			<input type="hidden" name="current_item_value" id="current_item_value" value="" />
			<input type="hidden" name="current_td" id="current_td" value="" />
			<input type="hidden" name="date" id="date" value="" />
			<input type="hidden" name="month" id="month" value="<?php echo $jinput->getInt('month',intval(date("m",HelperOSappscheduleCommon::getRealTime())));?>" />
			<input type="hidden" name="year" id="year" value="<?php echo $jinput->getInt('year',intval(date("Y",HelperOSappscheduleCommon::getRealTime())));?>" />
			<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid', 0); ?>" />
		</div>
		<?php
	}
	
	static function initCustomerCalendar($year,$month){
		global $mainframe,$configClass,$jinput;
		$db = JFactory::getDbo();
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		include_once(JPATH_COMPONENT_ADMINISTRATOR."/classes/ajax.php");
		$today						= self::getCurrentDate();
		$current_month 				= intval(date("m",$today));
		$current_year				= intval(date("Y",$today));
		$current_date				= intval(date("d",$today));
		//set up the first date
		$start_date_current_month 	= strtotime($year."-".$month."-01");
		$start_date_in_week			= date("N",$start_date_current_month);
		$number_days_in_month		= self::cal_days_in_month(CAL_GREGORIAN,$month,$year);
		$monthArr = array( JText::_('OS_JANUARY'), JText::_('OS_FEBRUARY'), JText::_('OS_MARCH'), JText::_('OS_APRIL'), JText::_('OS_MAY'), JText::_('OS_JUNE'), JText::_('OS_JULY'), JText::_('OS_AUGUST'), JText::_('OS_SEPTEMBER'), JText::_('OS_OCTOBER'), JText::_('OS_NOVEMBER'), JText::_('OS_DECEMBER'));
		//$monthArr = array("January","February","March","April","May","June","July","August","September","October","November","December");
		?>
		<div id="cal<?php echo intval($month)?><?php echo $year?>">
			<table  width="100%" class="apptable">
				<tr>
					<td width="40%" align="right" style="span-weight:bold;span-size:15px;">
						<a href="javascript:prevBigCal(0,'<?php echo $eid?>','<?php echo JUri::root();?>')" class="applink">
						<strong><</strong>
						</a>
					</td>
					<td width="20%" align="center" style="height:25px;span-weight:bold;">
						<?php
						echo $monthArr[$month-1];
						?>
						&nbsp;
						<?php echo $year;?>
					</td>
					<td width="40%" align="left" style="span-weight:bold;span-size:15px;">
						<a href="javascript:nextBigCal(0,'<?php echo $eid?>','<?php echo JUri::root();?>')" class="applink">
						<strong>></strong>
						</a>
					</td>
				</tr>
				<tr>
					<td width="100%" colspan="3" style="padding:3px;text-align:center;">
						<select name="ossm" class="input-small form-select" id="ossm" onchange="javascript:updateMonth(this.value)">
							<?php							
							for($i=0;$i<count($monthArr);$i++){
								if(intval($month) == $i + 1){
									$selected = "selected";
								}else{
									$selected = "";
								}
								?>
								<option value="<?php echo $i + 1?>" <?php echo $selected?>><?php echo $monthArr[$i]?></option>
								<?php
							}
							?>
						</select>
						<select name="ossy" class="input-small form-select" id="ossy" onchange="javascript:updateYear(this.value)">
							<?php
							for($i=date("Y",$today);$i<=date("Y",$today)+3;$i++){
								if(intval($year) == $i){
									$selected = "selected";
								}else{
									$selected = "";
								}
								?>
								<option value="<?php echo $i?>" <?php echo $selected?>><?php echo $i?></option>
								<?php
							}
							?>
						</select>
						<input type="button" class="btn btn-primary" value="Go" onclick="javascript:calendarMovingBigCal(0,'<?php echo $eid?>','<?php echo JUri::root();?>');">
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td  width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_MON')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_TUE')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_WED')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_THU')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_FRI')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_SAT')?>
						</span>
					</td>
					<td width="14%">
						<span class="header_rounded">
							<?php echo JText::_('OS_SUN')?>
						</span>
					</td>
				</tr>
				<tr>
					<?php
					for($i=1;$i<$start_date_in_week;$i++){
						//empty
						?>
						<td>
						
						</td>
						<?php
					}
					$j = $start_date_in_week-1;
					
					$m = "";
					if(intval($month) < 10){
						$m = "0".$month;
					}else{
						$m = $month;
					}
					$month = $m;
					
					for($i=1;$i<=$number_days_in_month;$i++){
						$j++;
						$nolink = 0;
						//check to see if today
						if(($i == $current_date) and ($month == $current_month) and ($year == $current_year)){
							$bgcolor = "pink";
						}else{
							$bgcolor = "#F1F1F1";
						}
						
						if($i < 10){
							$day = "0".$i;
						}else{
							$day = $i;
						}
						$tempdate1 = strtotime($year."-".$month."-".$day);
						$tempdate2 = strtotime($current_year."-".$current_month."-".$current_date);
						
						if($tempdate1 < $tempdate2){
							$bgcolor = "#ABAAB2";
							$nolink = 4;
						}
						
						if($i < 10){
							$day = "0".$i;
						}else{
							$day = $i;
						}
						$date = $year."-".$month."-".$day;
						?>
						<td id="td_cal_<?php echo $i?>"  align="center" class="td_date" style="text-align:center;" valign="top">
							<div id="a<?php echo $i;?>" style="border:1px solid #efefef;" class="div-rounded">
								<?php
								self::calendarCustomerItemAjax($i,$date);
								?>
							</div>
						</td>
						<?php
						if($j >= 7){
							$j = 0;
							echo "</tr><tr>";
						}
						
					}
					?>
				</tr>
			</table>
			<input type="hidden" name="current_item_value" id="current_item_value" value="" />
			<input type="hidden" name="current_td" id="current_td" value="" />
			<input type="hidden" name="date" id="date" value="" />
			<input type="hidden" name="month" id="month" value="<?php echo $jinput->getInt('month',intval(date("m",HelperOSappscheduleCommon::getRealTime())));?>" />
			<input type="hidden" name="year" id="year" value="<?php echo $jinput->getInt('year',intval(date("Y",HelperOSappscheduleCommon::getRealTime())));?>" />
			<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid', 0); ?>" />
		</div>
		<?php
	}
	
	public static function calendarCustomerItemAjax($i,$day)
	{
		global $mainframe,$configClass;
		$user = JFactory::getUser();
		$db  = JFactory::getDbo();
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$db->setQuery("SELECT a.*,c.service_name, b.order_name, b.order_email FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON b.id = a.order_id INNER JOIN #__app_sch_services AS c ON c.id = a.sid WHERE b.user_id = '$user->id' AND b.order_status IN ('P','S','A') AND a.booking_date = '$day'");
		$rows = $db->loadObjectList();
		$class = "";
		?>
		<strong>
			<?php
			if($day == date("Y-m-d",OSBHelper::getCurrentDate())){
				echo "<span color='#E3462D'>";
				echo $i;
				echo "</span>";
			}else{
				echo $i;
			}
			?>
		</strong>
		<BR />
		<div class="div-schedule">
		<?php
		if(count($rows) > 0)
		{
			for($k=0;$k<count($rows);$k++)
			{
				$row = $rows[$k];
				?>
				<i class="icon-ok"></i>
				<span class="hasTip" title="<?php echo self::generateBookingItem($row,1);?>">
				<?php
				echo date($configClass['time_format'],$row->start_time);
				echo "-";
				echo date($configClass['time_format'],$row->end_time);
				echo "  [".$row->service_name."]";
				?>
				</span>
				<?php
				$cancel_before = $configClass['cancel_before'];
				$current_time = HelperOSappscheduleCommon::getRealTime();
				if($configClass['allow_remove_items'] == 1 && $current_time + $cancel_before*3600 < $row->start_time)
				{
				?>
					<a href="javascript:removeItemCalendar(<?php echo $row->order_id?>,<?php echo $row->id?>,<?php echo $row->sid?>,<?php echo $row->eid?>,<?php echo $i?>,'<?php echo $day?>','<?php echo JText::_('OS_DO_YOU_WANT_T0_REMOVE_ORDER_ITEM')?>','<?php echo JURI::root()?>');" title="<?php echo JText::_('OS_CLICK_HERE_TO_REMOVE_ITEM')?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
						  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
						</svg>
					</a>
				<BR />
				<?php
				}
			}
		}
		?>
		</div>
		<?php
	}
	
	public static function calendarItemAjax($i,$eid,$day){
		global $mainframe,$configClass;
		$db  = JFactory::getDbo();
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$db->setQuery("SELECT COUNT(id) FROM #__app_sch_employee_rest_days WHERE eid = '$eid' AND rest_date <= '$day' and rest_date_to >= '$day'");
		$rest = $db->loadResult();
		$db->setQuery("SELECT a.*,c.service_name, service_color FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON b.id = a.order_id INNER JOIN #__app_sch_services AS c ON c.id = a.sid WHERE a.eid = '$eid' AND b.order_status IN ('P','S','A') AND a.booking_date = '$day'");
		$rows = $db->loadObjectList();
		?>
		<strong>
			<?php
			echo date($configClass['date_format'], strtotime($day));
			?>
		</strong>
		<BR />
		<?php
		if($rest > 0)
		{ //is not avaiable
			?>
			<a href="javascript:removerestday(<?php echo $i?>,'<?php echo $day?>',<?php echo $eid?>,'<?php echo JURI::root()?>');" title="<?php echo JText::_('OS_CLICK_TO_REMOVE_THE_REST_DAY');?>">
			<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/unpublish.png" />
			</a>
			<?php
			echo  "<span color='#7E1432'>[".JText::_('OS_UNAVAILABLE')."]</span>";
		}else{
			?>
			<a  href="javascript:addrestday(<?php echo $i?>,'<?php echo $day?>',<?php echo $eid?>,'<?php echo JURI::root()?>');" title="<?php echo JText::_('OS_CLICK_TO_ADD_THE_REST_DAY');?>">
				<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/publish.png" />
			</a>
			<?php	
			echo  "<span color='#2BA396'>[".JText::_('OS_AVAILABLE')."]</span>";
		}
		if($mainframe->isClient('administrator'))
		{
			?>
			<div class="div-schedule">
			<?php
			if(count($rows) > 0)
			{
				for($k=0;$k<count($rows);$k++)
				{
					$row = $rows[$k];
					if($row->service_color != "")
					{
						$backgroundColor = "background-color:".$row->service_color.";";
					}
					?>
					<div style="width:100%;<?php echo $backgroundColor;?>">
						<?php
						echo $k + 1;
						?>
						<span class="hasTip" title="">
						<?php
						echo ". ";
						echo date($configClass['time_format'],$row->start_time);
						echo "-";
						echo date($configClass['time_format'],$row->end_time);
						echo "  [".$row->service_name."]";
						echo "<BR />";
						?>
						</span>
					</div>
					<?php
				}
			}
			else
			{
				echo JText::_('OS_NO_BOOKING_REQUEST');
			}
			?>
			</div>
			<?php
		}
		else
		{
			?>
			<style>
			.div-rounded{
				min-height:60px !important;
			}
			</style>
			<?php
		}
	}
	
	public static function calendarEmployeeItemAjax($i,$eid,$day){
		global $mainframe,$configClass;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$db  = JFactory::getDbo();
		$db->setQuery("SELECT COUNT(id) FROM #__app_sch_employee_rest_days WHERE eid = '$eid' AND rest_date = '$day'");
		$rest = $db->loadResult();
		$db->setQuery("SELECT a.*,c.service_name,c.service_color,b.order_status,b.order_name, b.order_email FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON b.id = a.order_id INNER JOIN #__app_sch_services AS c ON c.id = a.sid WHERE a.eid = '$eid' AND b.order_status IN ('P','S','A') AND a.booking_date = '$day' order by a.start_time");
		$rows = $db->loadObjectList();
		?>
		<div style="float:left;width:50%;">
		<strong>
			<?php
			echo date($configClass['date_format'], strtotime($day));
			?>
		</strong>
		</div>
		<div style="float:left;width:50%;text-align:right;">
			<?php
			if($rest == 1)
			{
				
			}
			elseif(count($rows) == 0)
			{
				
			}else{
			?>
			<a href='<?php echo JURI::root()?>index.php?option=com_osservicesbooking&task=calendar_dateinfo&date=<?php echo $day?>' class='osmodal'  title="<?php echo JText::_('OS_CLICK_HERE_TO_VIEW_CALENDAR_DETAILS');?>" rel="{handler: 'iframe', size: {x: 600, y: 400}}">
				<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/orderdetails.png" border="0"/>
			</a>
			<?php
			}
			?>
		</div>
		<div style="clear: both;"></div>
		<div class="div-schedule">
		<?php
		if(count($rows) > 0){
			for($k=0;$k<count($rows);$k++)
			{
				$config = new JConfig();
				$offset = $config->offset;
				date_default_timezone_set($offset);
				$row = $rows[$k];
				if($row->service_color != "")
				{
					$backgroundColor = "background-color:".$row->service_color.";";
				}
				?>
				<div style="width:100%;<?php echo $backgroundColor;?>">
					<?php
					echo $k + 1;
					?>
					<span class="hasTip" title="<?php echo self::generateBookingItem($row,0);?>">
					<?php
					echo ". ";
					//echo "<a href='".JURI::root()."index.php?option=com_osservicesbooking&task=calendar_dateinfo&date=$day' class='modal'>";
					echo date($configClass['time_format'],$row->start_time);
					echo "-";
					echo date($configClass['time_format'],$row->end_time);
					//echo "</a>";
					echo "  [".$row->service_name."]";
					echo '<span class="label" style="color:red;font-weight:bold;">'.OSBHelper::orderStatus(0,$row->order_status).'</span>';
					echo "<BR />";
					?>
					</span>
				</div>
				<?php
			}
		}
		else
		{
			if($rest > 0){
				echo JText::_('OS_REST_DAY');	
			}else{
				echo JText::_('OS_NO_BOOKING_REQUEST');
			}
		}
		?>
		</div>
		<?php
	}
	
	public static function generateBookingItem($row,$isEmployee)
	{
		global $mainframe,$configClass;
		$data = self::generateData($row);
		$return = $data[0]->service_name."::";
		$return.= "<br />".JText::_('OS_FROM').": ".date($configClass['time_format'],$data[5]);
		$return.= "  <br />".JText::_('OS_TO').": ".date($configClass['time_format'],$data[6]);
		$return.= "  <br />".JText::_('OS_ON').": ".date($configClass['date_format'],$data[5]);
		$return.= "  <br />".JText::_('OS_NAME').": ".$row->order_name;
		$return.= "  <br />".JText::_('OS_EMAIL').": ".$row->order_email;
		if($row->order_phone != "")
		{
			$return.= "  <br />".JText::_('OS_PHONE').": ".$row->order_phone;
		}
		if($isEmployee == 1)
		{
			$return.= "  <br />".JText::_('OS_EMPLOYEE').": ".$data[1]->employee_name;
		}
		if($data[4] != "")
		{
			$return.= "  <br />".JText::_('OS_ADDITIONAL_INFORMATION').": ".$data[4];
		}
		return $return;
	}
	
	public static function generateData($row){
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		$sid = $row->sid;
		$db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
		$service = $db->loadObject();
		if($service->service_time_type == 1){
			$nslots = $row->nslots;
		}else{
			$nslots = 0;
		}
		$order_item_id		= $row->id;
		$start_booking_date = $row->start_time;
		$week_date			= date("N",$start_booking_date);
		$end_booking_date   = $row->end_time;
		$eid				= $row->eid;
		$db->setQuery("SELECT field_id FROM #__app_sch_order_field_options WHERE order_item_id = '$order_item_id' GROUP BY field_id");
		$fields = $db->loadObjectList();
		//calculate option value and additional price
		if(count($fields) > 0){
			//prepare the field array
			$fieldArr = array();
			for($i=0;$i<count($fields);$i++){
				$field = $fields[$i];
				if(!in_array($field->field_id,$fieldArr)){
					$fieldArr[count($fieldArr)] = $field->field_id;
				}
			}
			$field_amount = 0;
			$field_data   = "";
			for($i=0;$i<count($fieldArr);$i++)
			{
				$fieldid = $fieldArr[$i];
				$db->setQuery("Select id,field_label,field_type from #__app_sch_fields where  id = '$fieldid'");
				$field = $db->loadObject();
				$field_type = $field->field_type;
				if($field_type == 1)
				{
					//get field value
					$db->setQuery("SELECT option_id FROM #__app_sch_order_field_options WHERE order_item_id= '$order_item_id'");
					$fieldvalue = $db->loadResult();
					$db->setQuery("Select * from #__app_sch_field_options where id = '$fieldvalue'");
					$fieldOption = $db->loadObject();
					if($fieldOption->additional_price > 0)
					{
						$field_amount += $fieldOption->additional_price;
					}
					
					$field_data .= "<strong>$field->field_label</strong>: ".$fieldOption->field_option;
					if($fieldOption->additional_price > 0){
						$field_data.= " - ".$fieldOption->additional_price." ".$configClass['currency_format'];
					}
					$field_data .= "<BR />";
				}
				elseif($field_type == 2)
				{
					$db->setQuery("SELECT option_id FROM #__app_sch_order_field_options WHERE order_item_id= '$order_item_id' and field_id = '$fieldid'");
					$fieldValueArr = $db->loadObjectList();
					if(count($fieldValueArr) > 0)
					{
						$fieldValue = array();
						for($j=0;$j<count($fieldValueArr);$j++)
						{
							$fieldValue[$j] = $fieldValueArr[$j]->option_id;
						}
					}
					if(count($fieldValue) > 0)
					{
						$field_data .= "<strong>$field->field_label</strong>: ";
						for($j=0;$j<count($fieldValue);$j++){
							$temp = $fieldValue[$j];
							$db->setQuery("Select * from #__app_sch_field_options where id = '$temp'");
							$fieldOption = $db->loadObject();
							if($fieldOption->additional_price > 0){
								$field_amount += $fieldOption->additional_price;
							}
							$field_data .= $fieldOption->field_option;
							if($fieldOption->additional_price > 0){
								$field_data.= " - ".$fieldOption->additional_price." ".$configClass['currency_format'];
							}
							$field_data .= ",";
						}
						$field_data = substr($field_data,0,strlen($field_data)-1);
						$field_data .= "<BR />";
					}
				}
			}
		}
		
		$db->setQuery("Select a.*,b.additional_price from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.id = '$eid' and b.service_id = '$sid'");
		$employee = $db->loadObject();
		
		//get extra cost
		$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '$week_date' or week_date = '0')");
		//echo $db->getQuery();
		$extras = $db->loadObjectList();
		$extra_cost = 0;
		if(count($extras) > 0){
			for($j=0;$j<count($extras);$j++){
				$extra = $extras[$j];
				$stime = $extra->start_time;
				$etime = $extra->end_time;
				$stime = date($configClass['date_format'],$start_booking_date)." ".$stime.":00";
				$etime = date($configClass['date_format'],$start_booking_date)." ".$etime.":00";
				$stime = strtotime($stime);
				$etime = strtotime($etime);
				if(($start_booking_date >= $stime) and ($start_booking_date <= $etime)){
					$extra_cost += $extra->extra_cost;
				}
			}
		}
		
		$return[0] = $service;
		$return[1] = $employee;
		$return[2] = $stime;
		$return[3] = $etime;
		$return[4] = $field_data;
		$return[5] = $start_booking_date;
		$return[6] = $end_booking_date;
		$return[7] = $nslots;
		return $return;
	}
	
	/**
	 * Show money with currency symbol and currency code
	 *
	 * @param unknown_type $amount
	 */
	public static function showMoney($amount,$showCode)
	{
		global $mainframe,$configClass;
		$decimal_separator			= $configClass['decimal_separator'];
		$thousands_separator		= $configClass['thousands_separator'];
		$number_of_decimal_digits	= (int) $configClass['number_of_decimal_digits'];
		$money = "";
		if($configClass['currency_symbol'] == '')
		{
			$db = JFactory::getDbo();
			$db->setQuery("Select currency_symbol from #__app_sch_currencies where currency_code = '".$configClass['currency_format']."'");
			$currency_symbol = $db->loadResult();
		}
		else
		{
			$currency_symbol = $configClass['currency_symbol'];
		}
		$currency = $configClass['currency_format'];

		if($configClass['currency_symbol_position'] == 0)
		{
			$money = $currency_symbol."";
		}
		$money .= number_format($amount, $number_of_decimal_digits, $decimal_separator ,$thousands_separator)." ";
		if($configClass['currency_symbol_position'] == 1)
		{
			$money .= $currency_symbol."";
		}
		if($showCode == 1 && $currency != $currency_symbol && $currency != "CHF")
		{
			$money .= $currency;
		}
		return $money;
	}
	
	/**
	 * Generate Order PDF layout
	 *
	 * @param unknown_type $id
	 */
	static function generateOrderPdf($id)
	{
		global $configClass;
		$realtime		= HelperOSappscheduleCommon::getRealTime();
		$config			= new JConfig();
		$offset			= $config->offset;
		date_default_timezone_set($offset);
		$mainframe		= JFactory::getApplication ();
		$sitename		= $configClass['business_name'];
		$db				= JFactory::getDBO();
		$query			= $db->getQuery(true);
		
		$query->select("*")->from("#__app_sch_orders")->where("id='$id'");
		$db->setQuery($query);
		$order			= $db->loadObject();
		$order_lang		= $order->order_lang;
		$query->clear();
		$default_lang	= self::getDefaultLanguage();
		if($order_lang == "")
		{
			$order_lang = $default_lang;
		}
		if($order_lang == $default_lang){
			$lang_suffix = "";
		}
		else
		{
			$order_lang_arr = explode("-",$order_lang);
			$lang_suffix = "_".$order_lang_arr[0];
		}
		
		require_once JPATH_ROOT . "/components/com_osservicesbooking/tcpdf/tcpdf.php";
		//require_once JPATH_ROOT . "/components/com_osservicesbooking/tcpdf/config/lang/eng.php";
		$row			= &JTable::getInstance ( 'Order', 'OsAppTable' );
		$row->load ( ( int ) $id );
		$query->select ( 'a.*, c.service_name'.$lang_suffix.' as service_name,c.service_time_type, b.employee_name' )->from ( '#__app_sch_order_items AS a' )->join ( 'INNER', '#__app_sch_employee AS b ON a.eid = b.id' )->join ( 'INNER', '#__app_sch_services AS c ON a.sid = c.id' )->where ( "a.order_id='" . $id ."'");
		//$db->setQuery("Select a.*, c.service_name,c.service_time_type, b.employee_name from #__app_sch_order_items AS a  INNER JOIN #__app_sch_employee AS b ON a.eid = b.id INNER JOIN #__app_sch_services AS c ON a.sid = c.id WHERE a.order_id='$row->id'");
		
		$db->setQuery ( $query );
		$rows			= $db->loadObjectList ();

		//Filename
		$pdf_root		= JPATH_ROOT . '/media/com_osservicesbooking/invoices/';
		if((int) $order->invoice_number == 0)
		{
			$invoice_number = OsbInvoice::getInvoiceNumber($order);
			$db->setQuery("Update #__app_sch_orders set invoice_number = '$invoice_number' where id = '$order->id'");
			$db->execute();
			$order->invoice_number = $invoice_number;
		}
		$invoiceNumber = OsbInvoice::formatInvoiceNumber($order->invoice_number, $configClass, $order);
		$invoicePath = $pdf_root . $invoiceNumber . '.pdf';
		$fileName = $invoiceNumber . '.pdf';
		
		$pdf = new TCPDF ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->SetCreator ( PDF_CREATOR );
		$pdf->SetAuthor ( $sitename );
		$pdf->SetTitle ( 'Invoice' );
		$pdf->SetSubject ( 'Invoice' );
		$pdf->SetKeywords ( 'Invoice' );
		$pdf->setHeaderFont ( Array (PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
		$pdf->setFooterFont ( Array (PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ) );
		$pdf->setPrintHeader ( false );
		$pdf->setPrintFooter ( false );
		$pdf->SetMargins ( PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT );
		$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
		$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );
		
		//set auto page breaks
		$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_BOTTOM );
		
		//set image scale factor
		$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );
		$font = empty($configClass['pdf_font']) ? 'times' :$configClass['pdf_font'];
		// True type font
		if (substr($font, -4) == '.ttf')
		{
			$font = TCPDF_FONTS::addTTFfont(JPATH_ROOT . '/components/com_osservicesbooking/tcpdf/fonts/' . $font, 'TrueTypeUnicode', '', 96);
		}
		$pdf->SetFont ( $font , '', 8 );
		$pdf->AddPage ();
		
		//get html details
        $html  = "";
		
		$html = '<table cellspacing="0" cellpadding="2" width="100%" style="border:1px solid #999;">';
		$html .= '<tr>';
		$html .= '<td width="5%" style="border:1px solid #999;">' . JText::_ ( '#' ) . '</td>';
		if($configClass['use_qrcode'])
		{
			$html .= '<td width="10%" style="border:1px solid #999;"> ' . JText::_ ( 'OS_QRCODE' ) . '</td>';
		}
		$html .= '<td width="15%" style="border:1px solid #999;">' . JText::_ ( 'OS_SERVICES' ) . '</td>';
		$html .= '<td width="15%" style="border:1px solid #999;">' . JText::_ ( 'OS_EMPLOYEE' ) . '</td>';
		$html .= '<td width="7%" style="border:1px solid #999;">' . JText::_ ( 'OS_WORKTIME_START_TIME' ) . '</td>';
		$html .= '<td width="7%" style="border:1px solid #999;">' . JText::_ ( 'OS_WORKTIME_END_TIME' ) . '</td>';
		$html .= '<td width="9%" style="border:1px solid #999;">' . JText::_ ( 'OS_DATE' ) . '</td>';
		$html .= '<td width="20%" style="border:1px solid #999;">' . JText::_ ( 'OS_OTHER_INFORMATION' ) . '</td>';
		$html .= '<td width="10%" style="border:1px solid #999;">' . JText::_ ( 'OS_ITEM_COST' ) . '</td>';
		$html .= '</tr>';
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);	
		if (count($rows) > 0) 
		{
			for($i=0;$i<count($rows);$i++) 
			{
				$item = &$rows[$i];
				$ordering = $i + 1;
				$html .= '<tr>';
				$html .= '<td style="border:1px solid #999;">' . $ordering . '</td>';
				if($configClass['use_qrcode'])
				{
					if(!file_exists(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes/item_'.$item->id.'.png'))
					{
						OSBHelper::generateQrcode($order->id);
					}
					$html .= ' <td style="text-align:center;"> ';
					$html .= ' <img src="'.JUri::root().'media/com_osservicesbooking/qrcodes/item_'.$item->id.'.png" border="0"/>';
					$html .= ' </td>';
				}
				$html .= '<td style="border:1px solid #999;">' . $item->service_name . '</td>';
				$html .= '<td style="border:1px solid #999;">' . $item->employee_name . '</td>';
				$html .= '<td style="border:1px solid #999;">' . date ( $configClass ['time_format'], $item->start_time ) . '</td>';
				$html .= '<td style="border:1px solid #999;">' . date ( $configClass ['time_format'], $item->end_time ) . '</td>';
				$html .= '<td style="border:1px solid #999;">' . date ( $configClass ['date_format'], strtotime ( $item->booking_date ) ) . '</td>';
				$html .= '<td style="border:1px solid #999;">';
				if($item->service_time_type == 1)
				{
					$html .= JText::_('OS_NUMBER_SLOT').": ".$item->nslots."<BR />";
				}
				$query->clear();
				$query->select ( '*' )->from ( '#__app_sch_fields' )->where ( 'field_area= "0"' )->where ( 'published = 1' );
				$db->setQuery ( $query );
				$fields = $db->loadObjectList ();
				if (count ( $fields ) > 0) {
					for($i1 = 0; $i1 < count ( $fields ); $i1 ++) {
						$field = $fields [$i1];
						$query->clear ();
						$query->select ( 'count(id)' )->from ( '#__app_sch_order_field_options' )->where ( 'order_item_id=' . $item->id )->where ( 'field_id=' . $field->id );
						$db->setQuery ( $query );
						$count = $db->loadResult ();
						if ($count > 0) {
							if ($field->field_type == 1) {
								$query->clear ();
								$query->select ( 'option_id' )->from ( '#__app_sch_order_field_options' )->where ( 'order_item_id=' . $item->id )->where ( 'field_id=' . $field->id );
								$db->setQuery ( $query );
								$option_id = $db->loadResult ();
								$query->clear ();
								$query->select ( '*' )->from ( '#__app_sch_field_options' )->where ( 'id=' . $option_id );
								$db->setQuery ( $query );
								$optionvalue = $db->loadObject ();
								?>
								<?php
								$html .= self::getLanguageFieldValueOrder($field,'field_label',$order_lang).":";
								?>
								<?php
								$field_data = self::getLanguageFieldValueOrder($optionvalue,'field_option',$order_lang);
								if ($optionvalue->additional_price > 0) {
									$field_data .= " - " . $optionvalue->additional_price . " " . $configClass ['currency_format'];
								}
								$html .= $field_data;
								$html .= "<BR />";
							} elseif ($field->field_type == 2) {
								$query->clear ();
								$query->select ( 'option_id' )->from ( '#__app_sch_order_field_options' )->where ( 'order_item_id=' . $item->id )->where ( 'field_id=' . $field->id );
								$db->setQuery ( $query );
								$option_ids = $db->loadObjectList ();
								$fieldArr = array ();
								for($j = 0; $j < count ( $option_ids ); $j ++) {
									$oid = $option_ids [$j];
									$query->clear ();
									$query->select ( '*' )->from ( '#__app_sch_field_options' )->where ( 'id=' . $oid->option_id );
									$db->setQuery ( $query );
									$optionvalue = $db->loadObject ();
									$field_data = self::getLanguageFieldValueOrder($optionvalue,'field_option',$order_lang);
									if ($optionvalue->additional_price > 0) {
										$field_data .= " - " . $optionvalue->additional_price . " " . $configClass ['currency_format'];
									}
									$fieldArr [] = $field_data;
								}
								
								$html .= self::getLanguageFieldValueOrder($field,'field_label',$order_lang).":";
								?>
								<?php
								$html .= implode ( ",", $fieldArr );
								$html .= "<BR />";
							}
						}
					}
				}
				$html .= '</td>';
				$html .= '<td style="border:1px solid #999;">' . OSBHelper::showMoney($item->total_cost,1) . '</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
		$replaces = array ();
		$replaces ['name']					= $row->order_name;
		if($row->dial_code > 0)
		{
			$db->setQuery("Select dial_code from #__app_sch_dialing_codes where id = '$row->dial_code'");
			$dial_code = $db->loadResult();
			$dial_code = $dial_code."-";
		}
		else
		{
			$dial_code = "";
		}
		$replaces ['phone']					= $dial_code.$row->order_phone;
		$replaces ['email']					= $row->order_email;
		$replaces ['address']				= $row->order_address;
		$replaces ['city']					= $row->order_city;
		$replaces ['zip']					= $row->order_zip;
		$replaces ['state']					= $row->order_state;
		$replaces ['country']				= $row->order_country;
		$replaces ['invoice_number']		= $invoiceNumber;
		$replaces ['invoice_date']			= date($configClass['date_format'], $realtime);
		$replaces ['date']					= $row->order_date;
		$replaces ['status']				= OSBHelper::orderStatus(0,$row->order_status);
		$replaces ['discount_amount']		= OSBHelper::showMoney( $row->order_discount,1);
		$replaces ['sub_total']				= OSBHelper::showMoney($row->order_total,1);
		$replaces ['total_amount']			= OSBHelper::showMoney( $row->order_final_cost,1);
		$replaces ['upfront']				= OSBHelper::showMoney( $row->order_upfront,1);
		$replaces ['tax_amount']			= OSBHelper::showMoney( $row->order_tax,1);
		$replaces ['details']				= $html;
		$order_payment = "";
		if($row->order_payment != "")
		{
			$db->setQuery("Select `title` from #__app_sch_plugins where `name` like '".$row->order_payment."'");
			$order_payment = $db->loadResult();
		}
		$replaces ['payment_method']			= $order_payment;

		foreach ( $replaces as $key => $value ) 
		{
			$key = strtoupper ( $key );
			$configClass ['invoice_format'] = str_replace ( "[$key]", $value, $configClass ['invoice_format'] );
		}

		//for extra fields
        $replaces = HelperOSappscheduleCommon::buildReplaceTags($id);
        foreach ($replaces as $key => $value)
        {
            $key     = strtoupper($key);
            $configClass ['invoice_format'] = str_replace ( "[$key]", $value, $configClass ['invoice_format'] );
        }
		
		$invoiceOutput = self::convertImgTags ( $configClass ['invoice_format'] );
		$pdf->writeHTML ( $invoiceOutput, true, false, false, false, '' );
		
		$pdf->Output ( $pdf_root . $fileName, 'F' );
		$returnArr = array();
		$returnArr[0] = $pdf_root . $fileName;
		$returnArr[1] = $fileName;
		return $returnArr;
	}
	
	//check multiple work list of one employee
	static function checkMultipleEmployees($sid,$eid,$start_time,$end_time)
	{
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid <> '$sid' and a.eid = '$eid' and ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time'))");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid <> '$sid' and a.eid = '$eid' and a.end_time = '$start_time'");
			$count1 = $db->loadResult();
			if(($count1 > 0) and ($count1 == $count)){
				return true;
			}else{
				$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid <> '$sid' and a.eid = '$eid' and a.start_time = '$end_time'");
				$count2 = $db->loadResult();
				if(($count2 > 0) and ($count2 == $count)){
					return true;
				}else{
					return false;		
				}
			}
			return false;
		}else{
			return true;
		}
	}
	
	//check multiple work list of one employee
	static function checkMultipleEmployeesInTempOrderTable($sid,$eid,$start_time,$end_time)
    {
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		//$unique_cookie = $_COOKIE['unique_cookie'];
		$unique_cookie = self::getUniqueCookie();
		$db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid <> '$sid' and a.eid = '$eid' and ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time'))");
		
		$count = $db->loadResult();
		//if($count > 0){
		//	return false;
		//}else{
		//	return true;
		//}
		if($count > 0)
		{
			$db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid <> '$sid' and a.eid = '$eid' and a.end_time = '$start_time'");
			$count1 = $db->loadResult();
			if(($count1 > 0) and ($count1 == $count))
			{
				return true;
			}else{
				$db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid <> '$sid' and a.eid = '$eid' and a.start_time = '$end_time'");
				$count2 = $db->loadResult();
				if(($count2 > 0) and ($count2 == $count))
				{
					return true;
				}else{
					return false;		
				}
			}
			return false;
		}else{
			return true;
		}
	}

	//check multiple work list of one employee
	static function checkMultipleServices($sid,$eid,$start_time,$end_time)
    {
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid = '$sid' and a.eid <> '$eid' and ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time'))");
		$count = $db->loadResult();
		if($count > 0)
		{
			$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid = '$sid' and a.eid <> '$eid' and a.end_time = '$start_time'");
			$count1 = $db->loadResult();
			if(($count1 > 0) and ($count1 == $count))
			{
				return true;
			}
			else
			{
				$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid = '$sid' and a.eid <> '$eid' and a.start_time = '$end_time'");
				$count2 = $db->loadResult();
				if(($count2 > 0) and ($count2 == $count))
				{
					return true;
				}
				else
				{
					return false;		
				}
			}
			return false;
		}
		else
		{
			return true;
		}
	}

    static function checkLinkedServices($sid,$start_time,$end_time)
    {
        global $mainframe,$configClass;
        $db = JFactory::getDbo();
        $linkedServices = self::getLinkedService($sid);
        if(count($linkedServices) == 0)
        {
            return true;
        }
        else
        {
            $db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid in (".implode(',', $linkedServices).") and ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time'))");
            $count = $db->loadResult();
            if ($count > 0)
            {
                $db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid in (".implode(',', $linkedServices).") and a.end_time = '$start_time'");
                $count1 = $db->loadResult();
                if (($count1 > 0) && ($count1 == $count))
                {
                    return true;
                }
                else
                {
                    $db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.sid in (".implode(',', $linkedServices).") and a.start_time = '$end_time'");
                    $count2 = $db->loadResult();
                    if (($count2 > 0) && ($count2 == $count))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    static function checkMultipleVenues($eid,$vid,$start_time,$end_time)
    {
        global $configClass;
        $db = JFactory::getDbo();
        $db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.vid = '$vid' and ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time'))");
        //echo $db->getQuery();
        //echo "<BR />";
        $count = $db->loadResult();
        if($count > 0)
        {
            $db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.vid = '$vid' and a.end_time = '$start_time'");
            $count1 = $db->loadResult();
            if(($count1 > 0) && ($count1 == $count))
            {
                return true;
            }
            else
            {
                $db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('S','P') and a.vid = '$vid' and a.start_time = '$end_time'");
                $count2 = $db->loadResult();
                if(($count2 > 0) && ($count2 == $count))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            return false;
        }
        else
        {
            return true;
        }
    }

    static function checkLinkedServicesInTempOrderTable($sid,$start_time,$end_time)
    {
        global $mainframe,$configClass;
        $db = JFactory::getDbo();

        $linkedServices = self::getLinkedService($sid);
        if(count($linkedServices) == 0)
        {
            return true;
        }
        else
        {
            $unique_cookie = self::getUniqueCookie();
            $db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid in (".implode(',', $linkedServices).") and ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time'))");
            $count = $db->loadResult();

            if ($count > 0) {
                $db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid in (".implode(',', $linkedServices).") and a.end_time = '$start_time'");
                $count1 = $db->loadResult();
                if (($count1 > 0) && ($count1 == $count)) {
                    return true;
                } else {
                    $db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid in (".implode(',', $linkedServices).") and a.start_time = '$end_time'");
                    $count2 = $db->loadResult();
                    if (($count2 > 0) && ($count2 == $count)) {
                        return true;
                    } else {
                        return false;
                    }
                }
                return false;
            } else {
                return true;
            }
        }
    }

	//check multiple work list of one employee
	static function checkMultipleServicesInTempOrderTable($sid,$eid,$start_time,$end_time)
    {
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		//$unique_cookie = $_COOKIE['unique_cookie'];
		$unique_cookie = self::getUniqueCookie();
		$db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid = '$sid' and a.eid <> '$eid' and ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time'))");
		$count = $db->loadResult();

		if($count > 0)
		{
			$db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid = '$sid' and a.eid <> '$eid' and a.end_time = '$start_time'");
			$count1 = $db->loadResult();
			if(($count1 > 0) && ($count1 == $count))
			{
				return true;
			}
			else
			{
				$db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid = '$sid' and a.eid <> '$eid' and a.start_time = '$end_time'");
				$count2 = $db->loadResult();
				if(($count2 > 0) && ($count2 == $count))
				{
					return true;
				}
				else
				{
					return false;		
				}
			}
			return false;
		}
		else
		{
			return true;
		}
	}

    static function checkMultipleVenuesInTempOrderTable($eid,$vid,$start_time,$end_time)
    {
        global $mainframe,$configClass;
        $db = JFactory::getDbo();
        //$unique_cookie = $_COOKIE['unique_cookie'];
        $unique_cookie = self::getUniqueCookie();
        $db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.vid = '$vid' and ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time'))");

        $count = $db->loadResult();

        if($count > 0)
        {
            $db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.vid = '$vid' and a.end_time = '$start_time'");
            $count1 = $db->loadResult();
            if(($count1 > 0) && ($count1 == $count))
            {
                return true;
            }
            else
            {
                $db->setQuery("Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.vid = '$vid' and a.start_time = '$end_time'");
                $count2 = $db->loadResult();
                if(($count2 > 0) && ($count2 == $count))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            return false;
        }
        else
        {
            return true;
        }
    }
	
	/**
	 * Find Address
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @return unknown
	 */
	public static function findAddress($address)
    {
		global $mainframe;
		$address = trim($address);
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false";
		if(self::_iscurlinstalled())
		{
			$ch = curl_init();
		    curl_setopt ($ch, CURLOPT_URL, $url);
		    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
		    $return_data = curl_exec($ch);
		    curl_close($ch);
		}else{
			$return_data = file_get_contents($url) or die("url not loading");
		}
		$return_data = json_decode($return_data);
		$return_location = $return_data->results;
		$return = array();
		if($return_data->status == "OK")
		{
			$return[0] = $return_location[0]->geometry->location->lat;
			$return[1] = $return_location[0]->geometry->location->lng;
			$return[2] = $return_data->status;
		}
		return $return;
	}
	
	/**
	 * Check curl existing
	 *
	 * @return unknown
	 */
	static function _iscurlinstalled() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Add event on Google Calendar
	 *
	 * @param unknown_type $orderId
	 */
	public static function updateGoogleCalendar($orderId)
    {
		global $mainframe,$configClass;
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		if($configClass['integrate_gcalendar'] == 1 && JFile::exists(JPATH_ROOT."/libraries/osgcalendar/src/Google/Client.php"))
		{
			$db = JFactory::getDbo();
			$db->setQuery("Select a.id, a.eid,c.service_name, a.start_time,a.end_time,a.booking_date,e.client_id,e.app_name,e.app_email_address,e.p12_key_filename,e.gcalendarid from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id inner join #__app_sch_services as c on c.id = a.sid inner join #__app_sch_employee as e on e.id = a.eid where a.gcalendar_event_id = '' and b.id = '$orderId' and b.order_status in ('S','P')");
			$eids = $db->loadObjectList();
			
			if(count($eids) > 0)
			{
				for($i=0;$i<count($eids);$i++)
				{
					$item				= $eids[$i];
					$service_name		= $item->service_name;
					$start_time			= $item->start_time;
					$end_time			= $item->end_time;
					$booking_date		= $item->booking_date;
					$eid				= $item->eid;
					$client_id			= $item->client_id;
					$app_name			= $item->app_name;
					$app_email_address	= $item->app_email_address;
					$p12_key_filename	= $item->p12_key_filename;
					$gcalendarid		= $item->gcalendarid;
					if($client_id != "" && $app_name != "" && $app_email_address != "" && $gcalendarid != "" && $p12_key_filename != "" && JFile::exists(JPATH_COMPONENT_SITE."/".$p12_key_filename))
					{
						self::addEventonGCalendar(trim($client_id),trim($app_name),trim($app_email_address),trim($p12_key_filename),trim($gcalendarid),$service_name,$start_time,$end_time,$booking_date,$item->id,$orderId);
					}
				}
			}
		}
	}

	public static function retrieveevents()
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_employee where id = '1'");
		$employee			= $db->loadObject();
		$client_id			= $employee->client_id;
		$app_name			= $employee->app_name;
		$app_email_address	= $employee->app_email_address;
		$p12_key_filename	= $employee->p12_key_filename;
		$gcalendarid		= $employee->gcalendarid;
		$path = JPATH_ROOT."/libraries/osgcalendar/src/Google";
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);

		if(!file_exists ( $path.DS.'Client.php' ) || !file_exists ( $path.DS.'Service.php' ))
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
					file_get_contents(JPATH_COMPONENT_SITE.DS.$p12_key_filename),
					'notasecret','http://oauth.net/grant_type/jwt/1.0/bearer',false,false
				)
			);
		}
		catch (RuntimeException $e) {
		    return 'Problem authenticating Google Calendar:'.$e->getMessage();
		}


		$optParams = array(
			'maxResults' => 10,
			'orderBy' => 'startTime',
			'singleEvents' => TRUE,
			'timeMin' => date('c'),
		  );

		try {
			$service  = new Google_Service_Calendar($client);		
			$results  = $service->events->listEvents($gcalendarid, $optParams);
			$events	  = $results->getItems();
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

				$dateStart = new Date($start);
				
				$dateEnd = new Date($end); 

				//echo $dateStart->toSQL() . " - ". $dateEnd->toSQL();
				//echo "<BR />";

				echo $dateStart->format("Y-m-d");
				echo "<BR />";
			}
		} 
		catch (Google_ServiceException $e) 
		{
			echo $e->getMessage();
			exit;
		}	
	}
	
	
	/**
	 * Add event into GCalendar
	 *
	 * @param unknown_type $gusername
	 * @param unknown_type $gpassword
	 * @param unknown_type $gcalendarid
	 * @param unknown_type $service_name
	 * @param unknown_type $start_time
	 * @param unknown_type $end_time
	 * @param unknown_type $booking_date
	 */
	public static function addEventonGCalendar($client_id,$app_name,$app_email_address,$p12_key_filename,$gcalendarid,$service_name,$start_time,$end_time,$booking_date,$order_item_id,$orderId)
	{
		global $mainframe,$configClass;
		$current = self::getCurrentDate();
		$gmttime =  strtotime(JFactory::getDate('now'));
		$distance = round(($current - $gmttime)/3600);
		if($distance <= 0)
		{
			$distance = str_replace("-","",$distance);
			$distance = intval($distance);
			if($distance < 10)
			{
				$distance = "0".$distance;
			}
			$distance = "-".$distance;
		}
		if($distance > 0)
		{
			if($distance < 10)
			{
				$distance = "0".$distance;
			}
			$distance = "+".$distance;
		}
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_orders where id = '$orderId'");
		$order = $db->loadObject();

		$db->setQuery("Select * from #__app_sch_order_items where id = '$order_item_id'");
		$item = $db->loadObject();

		$db->setQuery("Select service_time_type from #__app_sch_services where id = '$item->sid'");
		$service_time_type = $db->loadResult();


		$desc = "";
		if($order->order_name != "")
		{
			$desc .= $order->order_name;
		}
		if($order->order_email != "")
		{
			$desc .= ", ".$order->order_email;
		}
		if($order->order_phone != "")
		{
			if($order->dial_code != "")
			{
				$dial_code = $order->dial_code;
				if($dial_code != "")
				{
					$dial_code = "(".$dial_code.")";
				}
			}
			$desc .= ", ".$dial_code.$order->order_phone;
		}

		$desc1 = "";

		if($service_time_type == 1 && $item->nslots > 0)
		{
			$desc .= JText::_('OS_NUMBER_SLOT').": ".$item->nslots;
		}

		//order custom fields
		$desc1 .= self::getOrderFields($order, "\n");
		
		//timeslot custom fields
		$desc1 .= self::getTimeslotFields($item, "\n ");
		
		$location = "";
		if($item->vid > 0)
		{
			$db->setQuery("Select * from #__app_sch_venues where id = '$item->vid'");
			$venue = $db->loadObject();
			if($venue->address != "")
			{
				$location .= $venue->address;
			}
			if($venue->city != "")
			{
				$location .= ", ".$venue->city;
			}
			if($venue->state != "")
			{
				$location .= ", ".$venue->state;
			}
			if($venue->country != "")
			{
				$location .= ", ".$venue->country;
			}
		}
		
		// connect to service
		//$gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		//$client = Zend_Gdata_ClientLogin::getHttpClient($gusername, $gpassword, $gcal);
		//$gcal = new Zend_Gdata_Calendar($client);
		$path = JPATH_ROOT."/libraries/osgcalendar/src/Google";
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		//echo $path;
		if(!file_exists ( $path.DS.'Client.php' ) || !file_exists ( $path.DS.'Service.php' ))
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
					file_get_contents(JPATH_COMPONENT_SITE.DS.$p12_key_filename),
					'notasecret','http://oauth.net/grant_type/jwt/1.0/bearer',false,false
				)
			);
		}
		catch (RuntimeException $e) {
		    return 'Problem authenticating Google Calendar:'.$e->getMessage();
		}
		
		// validate input
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		
		$title 			= $service_name;
		$title		   .= " [".$desc."]";
		$date			= new DateTime('@'.$start_time);
		

		  //get the exact GMT format
		//echo 'GMT '.$date_time->format('P');die();

		$start_date		= $date->format("d");
		$start_month 	= $date->format("m");
		$start_year 	= $date->format("Y");

		$end_date 		= $start_date;
		$end_month 		= $start_month;
		$end_year 		= $start_year;
		
		$start_hour 	= date("H",$start_time);
		$start_min		= date("i",$start_time);
		$end_hour		= date("H",$end_time);
		$end_min		= date("i",$end_time);

		$config         = new JConfig();
		$offset         = $config->offset;
		$target_time_zone = new DateTimeZone($offset);
		$date_time      = new DateTime($date->format("Y-m-d"), $target_time_zone);
		$distance       = $date_time->format('P');
		$start          =  $start_year."-".$start_month."-".$start_date."T".$start_hour.":".$start_min.":00".$distance.":00";
		//echo $start;die();
		$end            =  $start_year."-".$end_month."-".$end_date."T".$end_hour.":".$end_min.":00".$distance.":00";
		// construct event object
		// save to server    
		
		$service        = new Google_Service_Calendar($client);		
		$newEvent       = new Google_Service_Calendar_Event();
		$newEvent->setSummary($title);
		$newEvent->setLocation($location);
		$newEvent->setDescription($desc. $desc1);
		$event_start    = new Google_Service_Calendar_EventDateTime();
		$event_start->setDateTime($start);
		$newEvent->setStart($event_start);
		$event_end      = new Google_Service_Calendar_EventDateTime();
		$event_end->setDateTime($end);
		$newEvent->setEnd($event_end);
		
		$createdEvent = null;
		//if($this->cal_id != ""){
		try {
			$createdEvent = $service->events->insert($gcalendarid, $newEvent);
			$createdEvent_id= $createdEvent->getId();
			$db->setQuery("Update #__app_sch_order_items set gcalendar_event_id = '$createdEvent_id' where id = '$order_item_id'");
			$db->execute();
		} 
		catch (Google_ServiceException $e) 
		{
			echo $e->getMessage();
			exit;
		}	
	}

	public static function getOrderFields($order, $separator = ", ")
	{
		$db			= JFactory::getDbo();
		$orderId	= $order->id;
		$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and show_in_calendar = '1' and published = '1'");
		$fields		= $db->loadObjectList();
		if(count($fields) > 0)
		{
			$field_content_array = array();
			for($i2=0;$i2<count($fields);$i2++)
			{
				$field = $fields[$i2];
				$field_value = OsAppscheduleDefault::orderFieldData($field,$orderId);
				if($field_value != "")
				{
					$desc1 .= $separator. OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang).": ".$field_value;
				}
			}
		}
		return $desc1;
	}

	public static function getTimeslotFields($item,  $separator = ", ")
	{
		$db				= JFactory::getDbo();
		$orderId		= $item->order_id;
		$order_item_id	= $item->id;
		$db->setQuery("Select * from #__app_sch_orders where id = '$orderId'");
		$order			= $db->loadObject();

		$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' and show_in_calendar = '1' order by ordering");
		$fields = $db->loadObjectList();
		$item_content = "";
		if(count($fields) > 0)
		{
			for($i1=0;$i1<count($fields);$i1++)
			{
				$field = $fields[$i1];
				$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$order_item_id' and field_id = '$field->id'");
				$count = $db->loadResult();
				if($count > 0)
				{
					if($field->field_type == 1)
					{
						$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$order_item_id' and field_id = '$field->id'");
						//echo $db->getQuery();
						$option_id		= $db->loadResult();
						$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
						$optionvalue	= $db->loadObject();
						$item_content  .= $separator.OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang).": ";
						
						$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang);
						if($optionvalue->additional_price > 0 && $configClass['disable_payments'] == 1)
						{
							$field_data.= " - ".OSBHelper::showMoney($optionvalue->additional_price,0);
						}
						$item_content .= $field_data;
					}
					elseif($field->field_type == 2)
					{
						$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$order_item_id' and field_id = '$field->id'");
						$option_ids = $db->loadObjectList();
						$fieldArr = array();
						for($j1=0;$j1<count($option_ids);$j1++)
						{
							$oid = $option_ids[$j1];
							$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
							$optionvalue	= $db->loadObject();
							$field_data		= OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang);
							if($optionvalue->additional_price > 0 && $configClass['disable_payments'] == 1)
							{
								$field_data.= " - ".OSBHelper::showMoney($optionvalue->additional_price,0);
							}
							$fieldArr[] = $field_data;
						}
						$item_content .= $separator.OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang).": ";
						$item_content .= implode(", ",$fieldArr);
					}
				}
			}
		}
		return $item_content;
	}
	
	/**
	 * Remove events of Order on Google calendar
	 *
	 * @param unknown_type $id
	 */
	static function removeEventOnGCalendar($id)
    {
		global $mainframe, $configClass;
		if($configClass['integrate_gcalendar'] == 1)
		{
			jimport('joomla.filesystem.file');	
			$db             = JFactory::getDbo();
			$db->setQuery("Select * from #__app_sch_order_items where order_id = '$id'");
			$items          = $db->loadObjectList();
			if(count($items) > 0)
			{
				for($i=0;$i<count($items);$i++)
				{
					$item   = $items[$i];
					self::removeOneEventOnGCalendar($item->id);
				}
			}
		}
	}

	static function removeOneEventOnGCalendar($id)
    {
		global $configClass;
		if($configClass['integrate_gcalendar'] == 1)
		{
			$db					= JFactory::getDbo();
			$db->setQuery("Select * from #__app_sch_order_items where id = '$id'");
			$item				= $db->loadObject();
			$db->setQuery("Select * from #__app_sch_employee where id = '$item->eid'");
			$employee			= $db->loadObject();
			$client_id			= $employee->client_id;
			$app_name			= $employee->app_name;
			$app_email_address	= $employee->app_email_address;
			$p12_key_filename	= $employee->p12_key_filename;
			$gcalendarid = $employee->gcalendarid;
			if( $client_id != "" && $app_name != "" && $app_email_address != "" && $gcalendarid != "" && $p12_key_filename != "" && JFile::exists(JPATH_COMPONENT_SITE."/".$p12_key_filename))
			{
				self::removeEventsonGCalendar(trim($client_id),trim($app_name),trim($app_email_address),trim($p12_key_filename),trim($gcalendarid),$item->id,$item->gcalendar_event_id);
			}
		}
	}
	
	/**
	 * Remove Event on Google calendar
	 *
	 * @param unknown_type $gusername
	 * @param unknown_type $gpassword
	 * @param unknown_type $gcalendarid
	 * @param unknown_type $id
	 * @param unknown_type $gcalendar_event_id
	 */
	public static function removeEventsonGCalendar($client_id,$app_name,$app_email_address,$p12_key_filename,$gcalendarid,$id,$gcalendar_event_id)
    {
		global $mainframe,$configClass;
		// load classes
		$path = JPATH_ROOT."/libraries/osgcalendar/src/Google";
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		//echo $path;
		
		if(!file_exists ( $path.DS.'Client.php' )){
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
					file_get_contents(JPATH_COMPONENT_SITE.DS.$p12_key_filename),
					'notasecret','http://oauth.net/grant_type/jwt/1.0/bearer',false,false
				)
			);
		}
		catch (RuntimeException $e) {
		    return 'Problem authenticating Google Calendar:'.$e->getMessage();
		}
		
		
		$service = new Google_Service_Calendar($client);
		if($gcalendar_event_id != ""){
			//try {
			$service->events->delete($gcalendarid, $gcalendar_event_id);
				$db = JFactory::getDbo();
				$db->setQuery("Update #__app_sch_order_items set gcalendar_event_id = '' where id = '$id'");
				$db->execute();
			//} catch (Exception $e) {
				//echo $e->getMessage();
				//exit;
			//}
		}
	}
	
	/**
	 * Check available date
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 * @param unknown_type $date
	 * @return unknown
	 */
	public static function checkAvailableDate($sid,$eid,$date)
	{
		global $mainframe;
		$db		  = JFactory::getDbo();
		$date_int = strtotime($date);
		$date	  = date("Y-m-d", $date_int);
		$date_we  = date("N",$date_int);
		$db->setQuery("Select `is_day_off` from #__app_sch_working_time where id = '$date_we'");
		$is_day_off = $db->loadResult();
		if($is_day_off == 1){
			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where `worktime_date` <= '$date' and `worktime_date_to` >= '$date'");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select `is_day_off` from #__app_sch_working_time_custom where `worktime_date` <= '$date' and `worktime_date_to` >= '$date'");
				$vl = $db->loadResult();
				if($vl == 0)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
				return true;
			}
		}
		else
		{
			//return false;
			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where `worktime_date` <= '$date' and `worktime_date_to` >= '$date'");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select `is_day_off` from #__app_sch_working_time_custom where `worktime_date` <= '$date' and `worktime_date_to` >= '$date'");
				$vl = $db->loadResult();
				if($vl == 0)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
				return false;
			}
		}
	}
	
	public static function isEmployeeAvailableInSpecificDate($sid,$eid,$date)
	{
		$db = Jfactory::getDbo();
		$date_int	  = strtotime($date);
		$date_in_week = date("D",$date_int);
		$date		  = date("Y-m-d", $date_int);
		$date_in_week = strtolower($date_in_week);
		$date_in_week = substr($date_in_week,0,2);
		
		$query = $db->getQuery(true);
		$query->select('count(id)')->from('#__app_sch_employee_service')->where('employee_id = "'.$eid.'" and service_id = "'.$sid.'" and `'.$date_in_week.'` = 1');
		$db->setQuery($query);
		$count = $db->loadResult();
		
		if($count == 0){
			return false;
		}else{
			return true;
		}
	}

	public static function checkTimeSlotsAvailable($sid,$eid,$date,$vid = 0)
    {
        global $mainframe;

        $config         = new JConfig();
        $offset         = $config->offset;
        date_default_timezone_set($offset);
        $db             = JFactory::getDbo();
        //check to see if employee work on this day
        $date_int       = strtotime($date);
		$date			= date("Y-m-d", $date_int);
        $date_in_week   = date("D",$date_int);
        $date_in_week   = strtolower($date_in_week);
        $date_in_week   = substr($date_in_week,0,2);

		if($vid > 0)
		{
			$vidSql = " and employee_id IN (Select employee_id from #__app_sch_employee_service where service_id = '$sid' and vid = '$vid')";
		}
		else
		{
			$vidSql = "";
		}

        $query          = $db->getQuery(true);
        $query->select('count(id)')->from('#__app_sch_employee_service')->where('employee_id = "'.$eid.'" and service_id = "'.$sid.'" and `'.$date_in_week.'` = 1 '.$vidSql.' and employee_id NOT IN (Select eid from #__app_sch_employee_rest_days where rest_date <= '.$date.' and rest_date_to >= '.$date.')' );

        $db->setQuery($query);
        $count          = $db->loadResult();
        if($count > 0)
        {
            $query      = $db->getQuery(true);
            $query->select("service_time_type");
            $query->from("#__app_sch_services");
            $query->where("id = '$sid'");
            $db->setQuery($query);
            $service_time_type = $db->loadResult();
            if($service_time_type == 0)
            {
                return self::checkNormalTimeSlots($sid,$eid,$date,$vid);
            }
            else
            {
                return self::checkCustomTimeSlots($sid,$eid,$date,$vid);
            }

        }
		else
		{
            return false;
        }
    }

	public static function checkTimeSlotsAvailables($services, $employees, $date, $vid = 0)
	{
		global $mainframe;
        $config         = new JConfig();
        $offset         = $config->offset;
        date_default_timezone_set($offset);
        $db             = JFactory::getDbo();
        //check to see if employee work on this day
        $date_int       = strtotime($date);
		$date			= date("Y-m-d", $date_int);
        $date_in_week   = date("D",$date_int);
        $date_in_week   = strtolower($date_in_week);
        $date_in_week   = substr($date_in_week,0,2);
		
		$db = JFactory::getDbo();
		foreach($services as $service)
		{
			$sid = $service->id;
			if($vid > 0)
			{
				$vidSql = " and a.employee_id IN (Select employee_id from #__app_sch_employee_service where service_id = '$sid' and vid = '$vid')";
			}
			else
			{
				$vidSql = "";
			}
			$db->setQuery("Select a.employee_id from #__app_sch_employee_service as a inner join #__app_sch_employee as b on b.id = a.employee_id where a.service_id = '".$sid."' and a.".$date_in_week." = 1 and b.published = '1' and a.employee_id NOT IN (Select eid from #__app_sch_employee_rest_days where rest_date <= '$date' and rest_date_to >= '$date') ". $vidSql);
			$employees	= $db->loadObjectList();
			foreach($employees as $employee)
			{
				$eid = $employee->employee_id;
				$return = self::checkTimeSlotsAvailable($sid, $eid, $date, $vid);
				if($return)
				{
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * Load time slots
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 * @param unknown_type $date
	 */
	public static function loadTimeSlots($sid,$eid,$date, $vid = 0, $id = 0)
	{
		global $mainframe;
		$config         = new JConfig();
		$offset         = $config->offset;
		date_default_timezone_set($offset);
		$db             = JFactory::getDbo();
		//check to see if employee work on this day
		$date_int       = strtotime($date);
		$date			= date("Y-m-d", $date_int);
		$date_in_week   = date("D",$date_int);
		$date_in_week   = strtolower($date_in_week);
		$date_in_week   = substr($date_in_week,0,2);
		
		if($vid > 0)
		{
			$vidSql = ' and vid = '.$vid;
		}
		else
		{
			$vidSql = '';
		}

		$query          = $db->getQuery(true);
		$query->select('count(id)')->from('#__app_sch_employee_service')->where('employee_id = "'.$eid.'" '.$vidSql.' and service_id = "'.$sid.'" and `'.$date_in_week.'` = 1');
		$db->setQuery($query);
		$count          = $db->loadResult();
		
		if($count > 0)
		{
			$query      = $db->getQuery(true);
			$query->select("service_time_type");
			$query->from("#__app_sch_services");
			$query->where("id = '$sid'");
			$db->setQuery($query);
			$service_time_type = $db->loadResult();
			if($service_time_type == 0)
			{
				self::loadNormalTimeSlots($sid,$eid,$date, $vid, $id);
			}
			else
			{
				self::loadCustomTimeSlots($sid,$eid,$date, $vid, $id);
			}
			
		}else{
			echo "<h3>".Jtext::_('OS_UNAVAILABLE')."</h3>";
		}
	}

    /**
     * Load Normal time slots
     *
     * @param unknown_type $sid
     * @param unknown_type $eid
     * @param unknown_type $date
     */
    public static function checkNormalTimeSlots($sid,$eid,$date,$vid)
	{
        global $mainframe,$configClass,$mapClass;
        $option = "com_osservicesbooking";
        $realtime = HelperOSappscheduleCommon::getRealTime();
        $config = new JConfig();
        $offset = $config->offset;
        date_default_timezone_set($offset);

        $date_int		= strtotime($date);
        $date_in_week	= date("N",$date_int);
		$date			= date("Y-m-d", $date_int);
		$dateformat		= $date;
		$checkdate		= $date;
        $db = JFactory::getDbo();

		if($vid > 0)
		{
			$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
			$venue = $db->loadObject();
			$disable_booking_before = $venue->disable_booking_before;
			$number_date_before = $venue->number_date_before;
			$number_hour_before = $venue->number_hour_before;
			$disable_date_before = $venue->disable_date_before;
			if($disable_booking_before == 1)
			{
				$disable_time = strtotime(date("Y",$realtime)."-".date("m",$realtime)."-".date("d",$realtime)." 23:59:59");
			}
			elseif($disable_booking_before == 2)
            {
				$disable_time = $realtime + ($number_date_before-1)*24*3600 + $remain_time;
			}
			elseif($disable_booking_before  == 3)
            {
				$disable_time = strtotime($disable_date_before);
			}
			elseif($disable_booking_before == 4)
            {
				$disable_time = $realtime + $number_hour_before*3600;

			}

            if($disable_time > (int) strtotime($dateformat))
            {
                $date[2]     = date("Y", $disable_time);
                $date[1]     = date("m", $disable_time);
                $date[0]     = date("d", $disable_time);
                $dateformat  = date("Y-m-d", $disable_time);
            }

			$disable_booking_after = $venue->disable_booking_after;
			$number_date_after = $venue->number_date_after;
			$disable_date_after = $venue->disable_date_after ;
			if($disable_booking_after == 2)
			{
				$disable_time_after = $realtime + $number_date_after*24*3600;
			}
			elseif($disable_booking_after  == 3)
			{
				$disable_time_after = strtotime($disable_date_after);
			}
		}
		else
		{
			$disable_booking_after = 1;
			$disable_booking_before = 1;
		}
		
        if($configClass['multiple_work']  == 1)
        {
            $db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' AND a.sid = '$sid' and a.booking_date = '$date' AND b.order_status IN ('P','S','A')");
        }
        else
        {
            $db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' and a.booking_date = '$date' AND b.order_status IN ('P','S','A')");
        }
        //echo $db->getQuery();die();
        $employees = $db->loadObjectList();
        $tempEmployee = array();
        if(count($employees) > 0){
            for($i=0;$i<count($employees);$i++)
            {
                $employee = $employees[$i];
				$tmp			  = new \stdClass();
                $tmp->start_time  = $employees[$i]->start_time;
                $tmp->end_time    = $employees[$i]->end_time;
				$tmp->show		  = 1;
				$tempEmployee[$i] = $tmp;
            }
        }
		

        $db->setQuery("Select * from #__app_sch_employee_service_breaktime where sid = '$sid' and eid = '$eid' and date_in_week = '$date_in_week'");
        $breaks = $db->loadObjectList();
        $breakTime = array();
        for($i=0;$i<count($breaks);$i++)
		{
            $break_time_start = $date." ".$breaks[$i]->break_from;
            $break_time_sint  = strtotime($break_time_start);
            $break_time_end   = $date." ".$breaks[$i]->break_to;
            $break_time_eint  = strtotime($break_time_end);
            $count = count($tempEmployee);
			$tmp		      = new \stdClass();
            $tmp->start_time  = $break_time_sint;
            $tmp->end_time    = $break_time_eint;
            $tmp->show 		  = 0;
			$tempEmployee[$count] = $tmp;
            $count			  = count($breakTime);
			$tmp		      = new \stdClass();
            $tmp->start_time    = $break_time_sint;
            $tmp->end_time	    = $break_time_eint;
			$breakTime[$count]  = $tmp;
        }

		$db->setQuery("Select * from #__app_sch_custom_breaktime where sid = '$sid' and eid = '$eid' and bdate = '$checkdate'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count				= count($tempEmployee);
				$tmp				= new \stdClass();
				$tmp->start_time	= strtotime($checkdate." ".$custom->bstart);
				$tmp->end_time		= strtotime($checkdate." ".$custom->bend);
				$tmp->show			= 0;
				$tempEmployee[$count] = $tmp;

				$count			    = count($breakTime);
				$tmp		        = new \stdClass();
				$tmp->start_time    = strtotime($checkdate." ".$custom->bstart);
				$tmp->end_time	    = strtotime($checkdate." ".$custom->bend);
				$breakTime[$count]  = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_service_availability where sid = '$sid' and avail_date = '$checkdate'");
		$unavailable_values = $db->loadObjectList();
		if(count($unavailable_values) > 0)
		{
			for($i=0;$i<count($unavailable_values);$i++)
			{
				$employee			= $unavailable_values[$i];
				$count				= count($tempEmployee);
				$tmp				= new \stdClass();
				$tmp->start_time	= strtotime($date." ".$employee->start_time);
				$tmp->end_time		= strtotime($date." ".$employee->end_time);
				$tmp->show			= 0;
				$tempEmployee[$count] = $tmp;
			}
		}
	
		$db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$eid' and `busy_date` = '".date("Y-m-d", $date_int)."'");
        $breaks = $db->loadObjectList();
        //$breakTime = array();
        for($i=0;$i<count($breaks);$i++)
		{
            $break_time_start = $date." ".$breaks[$i]->busy_from;
            $break_time_sint  = strtotime($break_time_start);
            $break_time_end   = $date." ".$breaks[$i]->busy_to;
            $break_time_eint  = strtotime($break_time_end);
            $count = count($tempEmployee);
			$tmp		     = new \stdClass();
            $tmp->start_time = $break_time_sint;
            $tmp->end_time   = $break_time_eint;
            $tmp->show 		 = 0;
			$tempEmployee[$count]			  = $tmp;
            $count = count($breakTime);
			$tmp		     = new \stdClass();
            $tmp->start_time    = $break_time_sint;
            $tmp->end_time	  = $break_time_eint;
			$breakTime[$count] = $tmp;
        }

        //print_r($tempEmployee);

        $db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
        $service = $db->loadObject();
        $service_length  = $service->service_total;
        $service_total   = $service->service_total;
        $service_total_int = $service_total*60;

        $dateArr = explode("-",$date);
        $dateArr1[0] = $dateArr[2];
        $dateArr1[1] = $dateArr[1];
        $dateArr1[2] = $dateArr[0];
        $time = HelperOSappscheduleCalendar::getAvailableTime($option,$dateArr1);
        $starttimetoday  = strtotime($date." ".$time->start_time);
        $endtimetoday    = strtotime($date." ".$time->end_time);
        $cannotbookstart = $endtimetoday - $service_total_int;

        //$amount	 = $configClass['step_format']*60;

        $step_in_minutes = $service->step_in_minutes;
        if($step_in_minutes == 0)
		{
            $amount	 = $configClass['step_format']*60;
        }
		elseif($step_in_minutes == 1)
		{
            $amount  = $service_total_int;
        }
		else
		{
            $amount  = $step_in_minutes*60;
        }

        $j = 0;

        $str = "";

        for($inctime = $starttimetoday;$inctime<=$endtimetoday;$inctime = $inctime + $amount)
        {

            $start_booking_time = $inctime;
            $end_booking_time	= $inctime + $service_length*60;
            //Modify on 1st May to add the start time from break time
            foreach ($breakTime as $break)
            {
                if(($inctime >= $break->start_time) and ($inctime <= $break->end_time))
                {
                    $inctime = $break->end_time;
                    $start_booking_time = $inctime;
                    $end_booking_time	= $inctime + $service_length*60;
                }
            }

            $arr1 = array();
            $arr2 = array();
            $arr3 = array();

            if(count($tempEmployee) > 0)
            {
                for($i=0;$i<count($tempEmployee);$i++)
                {
                    $employee = $tempEmployee[$i];
                    $before_service = $employee->start_time - $service->service_total*60;
                    $after_service  = $employee->end_time + $service->service_total*60;
                    if(($employee->start_time < $inctime) and ($inctime < $employee->end_time) and ($inctime + $service->service_total*60 == $employee->end_time))
                    {
                        //echo "1";
                        $arr1[] = $inctime;
                        $bgcolor = $configClass['timeslot_background'];
                        $nolink = true;
                    }
                    elseif(($employee->start_time > $inctime) and ($employee->start_time < $end_booking_time))
                    {

                        //echo "4";
                        $arr2[] = $inctime;
                        $bgcolor = "gray";
                        $nolink = true;
                    }
                    elseif(($employee->end_time > $inctime) and ($employee->end_time < $end_booking_time))
                    {
                        //echo "5";

                        $arr2[] = $inctime;
                        $bgcolor = "gray";
                        $nolink = true;
                    }
                    elseif(($employee->start_time > $inctime) and ($employee->end_time < $end_booking_time))
                    {

                        //echo "6";
                        $arr2[] = $inctime;
                        $bgcolor = "gray";
                        $nolink = true;
                    }
                    elseif(($employee->start_time < $inctime) and ($employee->end_time > $end_booking_time))
                    {
                        //echo "7";

                        $arr2[] = $inctime;
                        $bgcolor = "gray";
                        $nolink = true;
                    }
                    elseif(($employee->start_time == $inctime) or ($employee->end_time == $end_booking_time))
                    {
                        //echo "7";

                        $arr2[] = $inctime;
                        $bgcolor = "gray";
                        $nolink = true;
                    }
                    else
                    {
                        //echo "8";
                        $arr3[] = $inctime;
                        $bgcolor = $configClass['timeslot_background'];
                        $nolink = false;
                    }
                }
            }
            else
            {
                $arr3[] = $inctime;
                $bgcolor = $configClass['timeslot_background'];
                $nolink = false;
            }
            //echo $bgcolor;

            $gray =  0;
            if($inctime + $service->service_total*60 > $endtimetoday)
            {
                //if($inctime >= $cannotbookstart){

                $bgcolor = "gray";
                $nolink  = true;

                $gray = 1;
            }
            if($configClass['multiple_work'] == 0){
                if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_booking_time,$end_booking_time))
                {
                    $bgcolor = "gray";
                    $nolink  = true;
                }
            }

            if(($date[2] == date("Y",$realtime) and ($date[1] == intval(date("m",$realtime))) and ($date[0] == intval(date("d",$realtime)))))
            {
                //today
                if($inctime <= $realtime)
                {
                    $bgcolor = "gray";
                    $nolink  = true;

                    $gray = 1;
                }
            }

            if($gray == 0)
            {
                if(in_array($inctime,$arr2))
                {
                    $bgcolor = "gray";
                    $nolink = true;
                }
                elseif(in_array($inctime,$arr1))
                {
                    $bgcolor = "#FA4876";
                    $nolink = true;
                }
                else
                {
                    $bgcolor = "#7BA1EB";
                    $nolink = false;
                }
            }
            elseif($gray == 1)
            {
                $bgcolor = "gray";
                $nolink  = true;
            }

			if($configClass['multiple_work'] == 0)
			{
				if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
				if(!OSBHelper::checkMultipleEmployeesInTempOrderTable($sid,$eid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
			
			if($configClass['disable_timeslot'] == 1)
			{
				if(!OSBHelper::checkMultipleServices($sid,$eid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
				if(!OSBHelper::checkMultipleServicesInTempOrderTable($sid,$eid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
			
			if($configClass['active_linked_service'] == 1)
			{
				if(!OSBHelper::checkLinkedServices($sid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
				if(!OSBHelper::checkLinkedServicesInTempOrderTable($sid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}

			if($configClass['disable_venuetimeslot'] == 1)
			{
				if(!OSBHelper::checkMultipleVenues($eid,$vid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
				if(!OSBHelper::checkMultipleVenuesInTempOrderTable($eid,$vid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
			
			//echo $bgcolor;

			if($disable_booking_before >= 1)
			{
				if($inctime < $disable_time)
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
			//echo date("H:i", $inctime). " - ".$bgcolor;
			if($disable_booking_after > 1)
			{
				if($inctime > $disable_time_after){
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
            
            if($end_booking_time <= $endtimetoday)
            {
                if(!$nolink)
				{
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Load Custom time slots
     *
     * @param unknown_type $sid
     * @param unknown_type $eid
     * @param unknown_type $date
     */
    public static function checkCustomTimeSlots($sid,$eid,$date)
	{
        global $mainframe,$configClass;
        $option			= "com_osservicesbooking";
        $config			= new JConfig();
        $offset			= $config->offset;
        date_default_timezone_set($offset);
        $realtime		= HelperOSappscheduleCommon::getRealTime();
        $db				= JFactory::getDbo();

        $date_int		= strtotime($date);
		$date			= date("Y-m-d", $date_int);
        $date_in_week	= date("N",$date_int);
		$checkdate		= $date;
		$breakTime		= [];
	
		if($vid > 0)
		{
			$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
			$venue = $db->loadObject();
			$disable_booking_before = $venue->disable_booking_before;
			$number_date_before = $venue->number_date_before;
			$number_hour_before = $venue->number_hour_before;
			$disable_date_before = $venue->disable_date_before;
			if($disable_booking_before == 1)
			{
				$disable_time = strtotime(date("Y",$realtime)."-".date("m",$realtime)."-".date("d",$realtime)." 23:59:59");
			}
			elseif($disable_booking_before == 2)
            {
				$disable_time = $realtime + ($number_date_before-1)*24*3600 + $remain_time;
			}
			elseif($disable_booking_before  == 3)
            {
				$disable_time = strtotime($disable_date_before);
			}
			elseif($disable_booking_before == 4)
            {
				$disable_time = $realtime + $number_hour_before*3600;

			}

            if($disable_time > (int) strtotime($dateformat))
            {
                $date[2]     = date("Y", $disable_time);
                $date[1]     = date("m", $disable_time);
                $date[0]     = date("d", $disable_time);
                $dateformat  = date("Y-m-d", $disable_time);
            }

			$disable_booking_after = $venue->disable_booking_after;
			$number_date_after = $venue->number_date_after;
			$disable_date_after = $venue->disable_date_after ;
			if($disable_booking_after == 2)
			{
				$disable_time_after = $realtime + $number_date_after*24*3600;
			}
			elseif($disable_booking_after  == 3)
			{
				$disable_time_after = strtotime($disable_date_after);
			}
		}
		else
		{
			$disable_booking_after = 1;
			$disable_booking_before = 1;
		}
		
        if($configClass['multiple_work']  == 1)
        {
            $db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' AND a.sid = '$sid' and a.booking_date = '$date' AND b.order_status IN ('P','S','A')");
        }else{
            $db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' and a.booking_date = '$date' AND b.order_status IN ('P','S','A')");
        }

        $employees = $db->loadObjectList();
        $tempEmployee = array();
        if(count($employees) > 0)
        {
            for($i=0;$i<count($employees);$i++)
            {
                $employee		  = $employees[$i];
				$tmp			  = new \stdClass();
                $tmp->start_time  = $employees[$i]->start_time;
				$tmp->end_time    = $employees[$i]->end_time;
				$tmp->show		  = 1;
                $tempEmployee[$i] = $tmp;
            }
        }
		

        $db->setQuery("Select * from #__app_sch_employee_service_breaktime where sid = '$sid' and eid = '$eid' and date_in_week = '$date_in_week'");
        $breaks = $db->loadObjectList();

        for($i=0;$i<count($breaks);$i++)
        {
            $break_time_start = $date." ".$breaks[$i]->break_from;
            $break_time_sint  = strtotime($break_time_start);
            $break_time_end   = $date." ".$breaks[$i]->break_to;
            $break_time_eint  = strtotime($break_time_end);
            $count = count($tempEmployee);
			$tmp			  = new \stdClass();
            $tmp->start_time  = $break_time_sint;
            $tmp->end_time    = $break_time_eint;
            $tmp->show 	      = 0;
			$tempEmployee[$count] = $tmp;
        }

		$db->setQuery("Select * from #__app_sch_custom_breaktime where sid = '$sid' and eid = '$eid' and bdate = '$checkdate'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count				= count($tempEmployee);
				$tmp				= new \stdClass();
				$tmp->start_time	= strtotime($checkdate." ".$custom->bstart);
				$tmp->end_time		= strtotime($checkdate." ".$custom->bend);
				$tmp->show			= 0;
				$tempEmployee[$count] = $tmp;

				$count			    = count($breakTime);
				$tmp		        = new \stdClass();
				$tmp->start_time    = strtotime($checkdate." ".$custom->bstart);
				$tmp->end_time	    = strtotime($checkdate." ".$custom->bend);
				$breakTime[$count]  = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_service_availability where sid = '$sid' and avail_date = '$checkdate'");
		$unavailable_values = $db->loadObjectList();
		if(count($unavailable_values) > 0)
		{
			for($i=0;$i<count($unavailable_values);$i++)
			{
				$employee			= $unavailable_values[$i];
				$count				= count($tempEmployee);
				$tmp				= new \stdClass();
				$tmp->start_time	= strtotime($date." ".$employee->start_time);
				$tmp->end_time		= strtotime($date." ".$employee->end_time);
				$tmp->show			= 0;
				$tempEmployee[$count] = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$eid' and `busy_date` = '".date("Y-m-d", $date_int)."'");
		//echo $db->getQuery();
		//echo "<BR />";
        $breaks = $db->loadObjectList();
		//print_r($breaks);
        //$breakTime = array();
        for($i=0;$i<count($breaks);$i++)
		{
            $break_time_start = date("Y-m-d", $date_int)." ".$breaks[$i]->busy_from;
			//echo $break_time_start;
			//echo "<BR />";
            $break_time_sint  = strtotime($break_time_start);
            $break_time_end   = date("Y-m-d", $date_int)." ".$breaks[$i]->busy_to;
            $break_time_eint  = strtotime($break_time_end);
            $count = count($tempEmployee);
			$tmp		     = new \stdClass();
            $tmp->start_time = $break_time_sint;
            $tmp->end_time   = $break_time_eint;
            $tmp->show 		 = 0;
			$tempEmployee[$count]			  = $tmp;
			//print_r($tempEmployee);
        }

		//print_r($tempEmployee);

        $db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
        $service = $db->loadObject();
        $service_length  = $service->service_total;
        $service_total   = $service->service_total;
        $service_total_int = $service_total*60;

        $dateArr = explode("-",$date);
        $dateArr1[0] = $dateArr[2];
        $dateArr1[1] = $dateArr[1];
        $dateArr1[2] = $dateArr[0];

        $time = HelperOSappscheduleCalendar::getAvailableTime($option,$dateArr1);
        $starttimetoday  = strtotime($date." ".$time->start_time);
        $endtimetoday    = strtotime($date." ".$time->end_time);
        $cannotbookstart = $endtimetoday - $service_total_int;

        $amount	 = $configClass['step_format']*60;
        $db->setQuery("Select * from #__app_sch_custom_time_slots where sid = '$sid' and id in (Select time_slot_id from #__app_sch_custom_time_slots_relation where date_in_week = '$date_in_week') order by start_hour,start_min");
        $rows = $db->loadObjectList();

        $j = 0;
        $str = "";
        for($i=0;$i<count($rows);$i++)
        {
            $row            = $rows[$i];
            $start_hour     = $row->start_hour;
            if($start_hour < 10){
                $start_hour = "0".$start_hour;
            }
            $start_min = $row->start_min;
            if($start_min < 10){
                $start_min = "0".$start_min;
            }

            $start_time     = $dateArr1[2]."-".$dateArr1[1]."-".$dateArr1[0]." ".$start_hour.":".$start_min.":00";
            $start_time_int = strtotime($start_time);
			//echo $start_time.' - '.date("H:i:s",$start_time_int);
			//echo "<BR />";
			
            $end_hour       = $row->end_hour;
            if($end_hour < 10){
                $end_hour   = "0".$end_hour;
            }
            $end_min = $row->end_min;
            if($end_min < 10){
                $end_min    = "0".$end_min;
            }

            $end_time       = $dateArr1[2]."-".$dateArr1[1]."-".$dateArr1[0]." ".$end_hour.":".$end_min.":00";
            $end_time_int   = strtotime($end_time);

            $db->setQuery("Select SUM(a.nslots) as nslots from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status IN ('P','S','A') and a.start_time =  '$start_time_int' and a.end_time = '$end_time_int' and a.sid = '$sid' and a.eid = '$eid'");
			
            //$count = $db->loadResult();
            $nslotsbooked = $db->loadObject();
            $count = intval($nslotsbooked->nslots);

			
			//echo $date.' '.$count.' '.$db->getQuery();
			//echo "<BR />";
            $temp_start_hour = $row->start_hour;
            $temp_start_min  = $row->start_min;
            $temp_end_hour 	 = $row->end_hour;
            $temp_end_min    = $row->end_min;

            $db->setQuery("Select nslots from #__app_sch_custom_time_slots where sid = '$service->id' and start_hour = '$temp_start_hour' and start_min = '$temp_start_min' and end_hour = '$temp_end_hour' and end_min = '$temp_end_min'");
            //echo $db->getQuery();
            $nslots = $db->loadResult();

            //get the number count of the cookie table
            $query = "SELECT SUM(a.nslots) as bnslots FROM #__app_sch_temp_order_items AS a INNER JOIN #__app_sch_temp_orders AS b ON a.order_id = b.id WHERE a.sid = '$sid' AND a.eid = '$eid' AND a.start_time =  '$start_time_int' and a.end_time = '$end_time_int'";
            $db->setQuery($query);
            $bslots = $db->loadObject();
            $count_book = $bslots->bnslots;
            $avail = $nslots - $count - $count_book;
			//echo date("Y-m-d H:i:s", $start_time_int) . " - ". $nslots ." - ".$avail;
			//echo "<BR />";
//echo $date .' '.(int)$avail;
//echo "<BR />";
            if($avail <= 0)
			{
                $bgcolor = "gray";
                $nolink = true;
            }
			else
			{
				if($configClass['disable_timeslot'] == 1 && $count + $count_book > 0)
				{
					$bgcolor = "gray";
					$nolink = true;
				}
				else
				{
					$bgcolor = "#7BA1EB";
					$nolink = false;
				}
            }
            if($configClass['multiple_work'] == 0)
            {
                if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_time_int,$end_time_int))
                {
                    $bgcolor = "gray";
                    $nolink  = true;
                }
            }

            if(($dateArr1[2] == date("Y",$realtime) and ($dateArr1[1] == intval(date("m",$realtime))) and ($dateArr1[0] == intval(date("d",$realtime))))){
                //today
                if($start_time_int <= $realtime)
                {
                    $bgcolor = "gray";
                    $nolink  = true;
                }
            }

            if(count($tempEmployee) > 0)
            {
                //print_r($tempEmployee);
                for($k=0;$k<count($tempEmployee);$k++)
				{
                    $employee = $tempEmployee[$k];

                    $before_service = $employee->start_time;
                    //echo date("d-m-Y H:i", $start_time_int). " - " .$avail;
                    //echo "<BR />";
                    $after_service  = $employee->end_time;
                   
                    if(($employee->start_time < $start_time_int) && ($end_time_int < $employee->end_time)){
                        //echo "1";
                        if(($avail <= 0) or ($employee->show == 0)){
                            $bgcolor = "gray";
                            $nolink = true;
                        }
                    }elseif(($employee->start_time > $start_time_int) && ($employee->start_time < $end_time_int)){

                        //echo "2";
                        if(($avail <= 0) or ($employee->show == 0)){
                            $bgcolor = "gray";
                            $nolink = true;
                        }
                    }elseif(($employee->end_time > $start_time_int) && ($employee->end_time < $end_time_int)){

                        //echo "3";
                        if(($avail <= 0) or ($employee->show == 0)){
                            $bgcolor = "gray";
                            $nolink = true;
                        }
                    }
					elseif(($employee->start_time <= $start_time_int && $employee->end_time > $end_time_int) || ($employee->start_time < $start_time_int && $employee->end_time >= $end_time_int))
					{
                        
                        if($avail <= 0 || $employee->show == 0){
                            $bgcolor = "gray";
                            $nolink = true;
                        }
                    }elseif($end_time_int <= $employee->start_time){
                        if($bgcolor != "gray"){
                            $bgcolor = $configClass['timeslot_background'];
                            $nolink = false;
                        }
                    }else{
                        if($bgcolor != "gray"){
                            $bgcolor = $configClass['timeslot_background'];
                            $nolink = false;
                        }
                    }
                }
            }

			if($configClass['multiple_work'] == 0)
			{
				if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
				if(!OSBHelper::checkMultipleEmployeesInTempOrderTable($sid,$eid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
			
			if($configClass['disable_timeslot'] == 1)
			{
				if(!OSBHelper::checkMultipleServices($sid,$eid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
				if(!OSBHelper::checkMultipleServicesInTempOrderTable($sid,$eid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
			
			if($configClass['active_linked_service'] == 1)
			{
				if(!OSBHelper::checkLinkedServices($sid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
				if(!OSBHelper::checkLinkedServicesInTempOrderTable($sid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}

			if($configClass['disable_venuetimeslot'] == 1)
			{
				if(!OSBHelper::checkMultipleVenues($eid,$vid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
				if(!OSBHelper::checkMultipleVenuesInTempOrderTable($eid,$vid,$start_booking_time,$end_booking_time))
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
			
			//echo $bgcolor;

			if($disable_booking_before >= 1)
			{
				if($inctime < $disable_time)
				{
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
			//echo date("H:i", $inctime). " - ".$bgcolor;
			if($disable_booking_after > 1)
			{
				if($inctime > $disable_time_after){
					$bgcolor = $configClass['booked_timeslot_background'];
					$nolink  = true;
				}
			}
            
            if((($nolink) && (($configClass['show_occupied'] == 1)) || (!$nolink)))
            {
                if($end_time_int <= $endtimetoday && $start_time_int >= $starttimetoday)
                {
                    $j++;
                    if(!$nolink)
                    {
                        return true;
                    }
                }
            }
        }
    }
	
	/**
	 * Load Normal time slots
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 * @param unknown_type $date
	 */
	public static function loadNormalTimeSlots($sid,$eid,$date,$vid, $id = 0)
	{
		global $mainframe,$configClass,$mapClass;
        $option = "com_osservicesbooking";
		$realtime = HelperOSappscheduleCommon::getRealTime();
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);	
		
		if(JFactory::getApplication()->isClient('administrator'))
		{
			$type = "checkbox";
		}
		else
		{
			$type = "radio";
		}

		$date_int = strtotime($date);
		$date	  = date("Y-m-d", $date_int);
		$date_in_week = date("N",$date_int);
		
		$db = JFactory::getDbo();

		if($id > 0)
		{
			$db->setQuery("Select * from #__app_sch_order_items where id = '$id'");
			$booked_item = $db->loadObject();
		}

		if($configClass['multiple_work']  == 1)
		{
			$db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' AND a.sid = '$sid' and a.booking_date = '$date' AND b.order_status IN ('P','S','A')");
		}
		else
		{
			$db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' and a.booking_date = '$date' AND b.order_status IN ('P','S','A')");
		}
		//echo $db->getQuery();
		$employees = $db->loadObjectList();
		$tempEmployee = array();
		if(count($employees) > 0){
			for($i=0;$i<count($employees);$i++){
				$employee = $employees[$i];
				$tmp			  = new \stdClass();
				$tmp->start_time = $employees[$i]->start_time;
				$tmp->end_time   = $employees[$i]->end_time;
				$tempEmployee[$i] = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_custom_breaktime where sid = '$sid' and eid = '$eid' and bdate = '$date'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count = count($tempEmployee);
				$tmp			  = new \stdClass();
				$tmp->start_time = strtotime($date." ".$custom->bstart);
				$tmp->end_time   = strtotime($date." ".$custom->bend);
				$tmp->show		  = 0;
				$tempEmployee[$count] = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$eid' and busy_date = '$date'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count = count($tempEmployee);
				$tmp			  = new \stdClass();
				$tmp->start_time = strtotime($date." ".$custom->busy_from);
				$tmp->end_time   = strtotime($date." ".$custom->busy_to);
				$tmp->show		  = 0;
				$tempEmployee[$count] = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_service_availability where sid = '$sid' and avail_date = '$date'");
		$unavailable_values = $db->loadObjectList();
		if(count($unavailable_values) > 0)
		{
			for($i=0;$i<count($unavailable_values);$i++)
			{
				$employee = $unavailable_values[$i];
				$count = count($tempEmployee);
				$tmp			  = new \stdClass();
				$tmp->start_time = strtotime($date." ".$employee->start_time);
				$tmp->end_time   = strtotime($date." ".$employee->end_time);
				$tmp->show		  = 0;
				$tempEmployee[$count] = $tmp;
			}
		}
		
		$db->setQuery("Select * from #__app_sch_employee_service_breaktime where sid = '$sid' and eid = '$eid' and date_in_week = '$date_in_week'");
		$breaks = $db->loadObjectList();
		$breakTime = array();
		for($i=0;$i<count($breaks);$i++){
			$break_time_start = $date." ".$breaks[$i]->break_from;
			$break_time_sint  = strtotime($break_time_start);
			$break_time_end   = $date." ".$breaks[$i]->break_to;
			$break_time_eint  = strtotime($break_time_end);
			$count = count($tempEmployee);
			$tmp			  = new \stdClass();
			$tmp->start_time = $break_time_sint;
			$tmp->end_time   = $break_time_eint;
			$tmp->show 	  = 0;
			$tmp = $tmp;
			$count = count($breakTime);
			$tmp			  = new \stdClass();
			$tmp->start_time    = $break_time_sint;
			$tmp->end_time	  = $break_time_eint;
			$breakTime[$count] = $tmp;
		}
		//print_r($tempEmployee);
		
		$db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
		$service = $db->loadObject();
		$service_length  = $service->service_total;
		$service_total   = $service->service_total;
		$service_total_int = $service_total*60;

		$dateArr = explode("-",$date);
		$dateArr1[0] = $dateArr[2];
		$dateArr1[1] = $dateArr[1];
		$dateArr1[2] = $dateArr[0];
		$time = HelperOSappscheduleCalendar::getAvailableTime($option,$dateArr1);
		if($vid > 0)
		{
			$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
			$venue = $db->loadObject();
		    $start_hour  = $venue->opening_hour;
		    $start_min   = $venue->opening_minute;
		    if($start_hour > 0)
		    {
		        $time->start_time = $start_hour.":".$start_min.":00";
            }
        }
		$starttimetoday  = strtotime($date." ".$time->start_time);
		$endtimetoday    = strtotime($date." ".$time->end_time);
		$cannotbookstart = $endtimetoday - $service_total_int;

		//$amount	 = $configClass['step_format']*60;

		$step_in_minutes = $service->step_in_minutes;
		if($step_in_minutes == 0){
			$amount	 = $configClass['step_format']*60;
		}elseif($step_in_minutes == 1){
			$amount  = $service_total_int;
		}else{
			$amount  = $step_in_minutes*60;
		}
		?>
		<div class="<?php echo $mapClass['row-fluid']?> row-fluid">
		<?php
		$j = 0;
		
		$str = "";
		
		for($inctime = $starttimetoday;$inctime<=$endtimetoday;$inctime = $inctime + $amount){
			
			$start_booking_time = $inctime;
			$end_booking_time	= $inctime + $service_length*60;
			//Modify on 1st May to add the start time from break time
			foreach ($breakTime as $break){
				if(($inctime >= $break->start_time) and ($inctime <= $break->end_time)){
					$inctime = $break->end_time;
					$start_booking_time = $inctime;
					$end_booking_time	= $inctime + $service_length*60;
				}
			}

			$arr1 = array();
			$arr2 = array();
			$arr3 = array();

			if(count($tempEmployee) > 0){
				for($i=0;$i<count($tempEmployee);$i++){
					$employee = $tempEmployee[$i];
					$before_service = $employee->start_time - $service->service_total*60;
					$after_service  = $employee->end_time + $service->service_total*60;
					if(($employee->start_time < $inctime) and ($inctime < $employee->end_time) and ($inctime + $service->service_total*60 == $employee->end_time)){
						//echo "1";
						$arr1[] = $inctime;
						$bgcolor = $configClass['timeslot_background'];
						$nolink = true;
					}elseif(($employee->start_time > $inctime) and ($employee->start_time < $end_booking_time)){
	
						//echo "4";
						$arr2[] = $inctime;
						$bgcolor = "gray";
						$nolink = true;
					}elseif(($employee->end_time > $inctime) and ($employee->end_time < $end_booking_time)){
						//echo "5";
	
						$arr2[] = $inctime;
						$bgcolor = "gray";
						$nolink = true;
					}elseif(($employee->start_time > $inctime) and ($employee->end_time < $end_booking_time)){
	
						//echo "6";
						$arr2[] = $inctime;
						$bgcolor = "gray";
						$nolink = true;
					}elseif(($employee->start_time < $inctime) and ($employee->end_time > $end_booking_time)){
						//echo "7";
	
						$arr2[] = $inctime;
						$bgcolor = "gray";
						$nolink = true;
					}elseif(($employee->start_time == $inctime) or ($employee->end_time == $end_booking_time)){
						//echo "7";
	
						$arr2[] = $inctime;
						$bgcolor = "gray";
						$nolink = true;
					}else{
						//echo "8";
						$arr3[] = $inctime;
						$bgcolor = $configClass['timeslot_background'];
						$nolink = false;
					}
				}
			}else{
				$arr3[] = $inctime;
				$bgcolor = $configClass['timeslot_background'];
				$nolink = false;
			}
			//echo $bgcolor;

			$gray =  0;
			if($inctime + $service->service_total*60 > $endtimetoday){
			//if($inctime >= $cannotbookstart){
				
				$bgcolor = "gray";
				$nolink  = true;

				$gray = 1;
			}
			if($configClass['multiple_work'] == 0){
				if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_booking_time,$end_booking_time)){
					$bgcolor = "gray";
					$nolink  = true;
				}
			}
				
			if(($date[2] == date("Y",$realtime) and ($date[1] == intval(date("m",$realtime))) and ($date[0] == intval(date("d",$realtime))))){

				//today
				if($inctime <= $realtime){
					$bgcolor = "gray";
					$nolink  = true;

					$gray = 1;
				}
			}
			
			if($gray == 0){
				if(in_array($inctime,$arr2)){
					$bgcolor = "gray";
					$nolink = true;
				}elseif(in_array($inctime,$arr1)){
					$bgcolor = "#FA4876";
					$nolink = true;
				}else{
					$bgcolor = "#7BA1EB";
					$nolink = false;
				}
			}elseif($gray == 1){
				$bgcolor = "gray";
				$nolink  = true;
			}
			//if(!$nolink){
			if($end_booking_time <= $endtimetoday)
			{
				$j++;
			?>
				<div class="span6 <?php echo $mapClass['span6']?>" style="border-bottom:1px solid #efefef !important;background-color:<?php echo $bgcolor?> !important;padding:2px;color:white;padding-left:10px;">
					<?php
					if(!$nolink)
				    {
						if($id > 0 && $booked_item->start_time == $inctime && $booked_item->end_time == $end_booking_time)
						{
							$checked = "checked";
							$selected = "selected";
						}
						else
						{
							$checked = "";
							$selected = "";
						}
						$text = JText::_('OS_BOOK_THIS_EMPLOYEE_FROM')."[".date($configClass['date_time_format'],$inctime)."] to [".date($configClass['date_time_format'],$end_booking_time)."]";
						?>
						<input type="<?php echo $type; ?>" <?php echo $checked; ?> name="<?php echo $eid?>[]" id="<?php echo $eid?>_<?php echo $inctime?>" onclick="javascript:addBackendBooking('<?php echo $eid?>_<?php echo $inctime?>','<?php echo $inctime?>','<?php echo $end_booking_time;?>');">
						<?php
						$str .= "<option value='".$inctime."-".$end_booking_time."' ".$selected.">".$inctime."</option>";
					}
					else
					{
						?>
						<span color="White"><?php echo JText::_('OS_OCCUPIED')?></span>
						<?php
					}
					?>
					&nbsp;&nbsp;&nbsp;
					<?php
					echo date($configClass['time_format'],$inctime);
					?>
					&nbsp; - &nbsp;
					<?php
					echo date($configClass['time_format'],$end_booking_time);
					?>
				</div>
				<?php	
				if($j==2){
					?>
					</div><div class="row-fluid <?php echo $mapClass['row-fluid']?>">
					<?php
					$j = 0;
				}
			}
		}
		if($j == 1){
			?>
			</div>
			<?php
		}
		if($j==0){
		?>
		</div>
		<?php
		}
		?>
        <div style="display: none;">
		<select name="selected_timeslots[]" id="selected_timeslots" multiple>
			<?php 
			echo $str;
			?>
		</select>
        </div>
		<?php
	}
	
	
	/**
	 * Load Custom time slots
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 * @param unknown_type $date
	 */
	public static function loadCustomTimeSlots($sid,$eid,$date, $vid, $id = 0)
	{
		global $mainframe,$configClass;
        $option = "com_osservicesbooking";
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
        $realtime = HelperOSappscheduleCommon::getRealTime();
		$tempEmployee = array();
		if(JFactory::getApplication()->isClient('administrator'))
		{
			$type = "checkbox";
		}
		else
		{
			$type = "radio";
		}

		$db = JFactory::getDbo();
		if($id > 0)
		{
			$db->setQuery("Select * from #__app_sch_order_items where id = '$id'");
			$booked_item = $db->loadObject();
		}
		$date_int = strtotime($date);
		$date	  = date("Y-m-d", $date_int);
		$date_in_week = date("N",$date_int);
		/*
		if($configClass['multiple_work']  == 1){
			$db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' AND a.sid = '$sid' and a.booking_date = '$date' AND b.order_status IN ('P','S','A')");
		}else{
			$db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' and a.booking_date = '$date' AND b.order_status IN ('P','S','A')");
		}
		
		$employees = $db->loadObjectList();
		$tempEmployee = array();
		if(count($employees) > 0){
			for($i=0;$i<count($employees);$i++){
				$employee = $employees[$i];
				$tempEmployee[$i]->start_time = $employees[$i]->start_time;
				$tempEmployee[$i]->end_time   = $employees[$i]->end_time;
			}
		}
		*/

		$db->setQuery("Select * from #__app_sch_custom_breaktime where sid = '$sid' and eid = '$eid' and bdate = '$date'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count = count($tempEmployee);
				$tmp							= new \stdClass();
				$tmp->start_time = strtotime($date." ".$custom->bstart);
				$tmp->end_time   = strtotime($date." ".$custom->bend);
				$tmp->show		  = 0;
				$tempEmployee[$count] = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$eid' and busy_date = '$date'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count = count($tempEmployee);
				$tmp							= new \stdClass();
				$tmp->start_time = strtotime($date." ".$custom->busy_from);
				$tmp->end_time   = strtotime($date." ".$custom->busy_to);
				$tmp->show		  = 0;
				$tempEmployee[$count] = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_service_availability where sid = '$sid' and avail_date = '$date'");
		$unavailable_values = $db->loadObjectList();
		if(count($unavailable_values) > 0)
		{
			for($i=0;$i<count($unavailable_values);$i++)
			{
				$employee = $unavailable_values[$i];
				$count = count($tempEmployee);
				$tmp							= new \stdClass();
				$tmp->start_time = strtotime($date." ".$employee->start_time);
				$tmp->end_time   = strtotime($date." ".$employee->end_time);
				$tmp->show		  = 0;
				$tempEmployee[$count] = $tmp;
			}
		}
		
		$db->setQuery("Select * from #__app_sch_employee_service_breaktime where sid = '$sid' and eid = '$eid' and date_in_week = '$date_in_week'");
		$breaks = $db->loadObjectList();

		for($i=0;$i<count($breaks);$i++){
			$break_time_start = $date." ".$breaks[$i]->break_from;
			$break_time_sint  = strtotime($break_time_start);
			$break_time_end   = $date." ".$breaks[$i]->break_to;
			$break_time_eint  = strtotime($break_time_end);
			$count = count($tempEmployee);
			$tmp							= new \stdClass();
			$tmp->start_time = $break_time_sint;
			$tmp->end_time   = $break_time_eint;
			$tmp->show 	  = 0;
			$tempEmployee[$count] = $tmp;
		}
		
		$db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
		$service = $db->loadObject();
		$service_length  = $service->service_total;
		$service_total   = $service->service_total;
		$service_total_int = $service_total*60;

		$dateArr = explode("-",$date);
		$dateArr1[0] = $dateArr[2];
		$dateArr1[1] = $dateArr[1];
		$dateArr1[2] = $dateArr[0];
		
		$time = HelperOSappscheduleCalendar::getAvailableTime($option,$dateArr1);
		if($vid > 0)
		{
			$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
			$venue = $db->loadObject();
		    $start_hour  = $venue->opening_hour;
		    $start_min   = $venue->opening_minute;
		    if($start_hour > 0)
		    {
		        $time->start_time = $start_hour.":".$start_min.":00";
            }
        }
		$starttimetoday  = strtotime($date." ".$time->start_time);
		$endtimetoday    = strtotime($date." ".$time->end_time);
		$cannotbookstart = $endtimetoday - $service_total_int;

		$amount	 = $configClass['step_format']*60;
		$db->setQuery("Select * from #__app_sch_custom_time_slots where sid = '$sid' and id in (Select time_slot_id from #__app_sch_custom_time_slots_relation where date_in_week = '$date_in_week') order by start_hour,start_min");
		$rows = $db->loadObjectList();
		?>
		<div class="row-fluid">
			<?php
			$j = 0;
			$str = "";
			for($i=0;$i<count($rows);$i++){
				$config         = new JConfig();
				$offset         = $config->offset;
				date_default_timezone_set($offset);	
				$row            = $rows[$i];
				$start_hour     = $row->start_hour;
				if($start_hour < 10){
					$start_hour = "0".$start_hour;
				}
				$start_min = $row->start_min;
				if($start_min < 10){
					$start_min = "0".$start_min;
				}

				$start_time     = $dateArr1[2]."-".$dateArr1[1]."-".$dateArr1[0]." ".$start_hour.":".$start_min.":00";
				//echo $start_time;
				$start_time_int = strtotime($start_time);

				$end_hour       = $row->end_hour;
				if($end_hour < 10){
					$end_hour   = "0".$end_hour;
				}
				$end_min = $row->end_min;
				if($end_min < 10){
					$end_min    = "0".$end_min;
				}

				$end_time       = $dateArr1[2]."-".$dateArr1[1]."-".$dateArr1[0]." ".$end_hour.":".$end_min.":00";
				$end_time_int   = strtotime($end_time);

				$db->setQuery("Select SUM(a.nslots) as nslots from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status IN ('P','S','A') and a.start_time =  '$start_time_int' and a.end_time = '$end_time_int' and a.sid = '$sid' and a.eid = '$eid'");
				//$count = $db->loadResult();
				$nslotsbooked = $db->loadObject();
				$count = intval($nslotsbooked->nslots);
				$temp_start_hour = $row->start_hour;
				$temp_start_min  = $row->start_min;
				$temp_end_hour 	 = $row->end_hour;
				$temp_end_min    = $row->end_min;

				$db->setQuery("Select nslots from #__app_sch_custom_time_slots where sid = '$service->id' and start_hour = '$temp_start_hour' and start_min = '$temp_start_min' and end_hour = '$temp_end_hour' and end_min = '$temp_end_min'");
				//echo $db->getQuery();
				$nslots = $db->loadResult();

				//get the number count of the cookie table
				$query = "SELECT SUM(a.nslots) as bnslots FROM #__app_sch_temp_order_items AS a INNER JOIN #__app_sch_temp_orders AS b ON a.order_id = b.id WHERE a.sid = '$sid' AND a.eid = '$eid' AND a.start_time =  '$start_time_int' and a.end_time = '$end_time_int'";
				$db->setQuery($query);
				$bslots = $db->loadObject();
				$count_book = $bslots->bnslots;
				$avail = $nslots - $count - $count_book;

				if($avail <= 0){
					$bgcolor = "#FA4876";
					$nolink = true;
				}else{
					$bgcolor = "#7BA1EB";
					$nolink = false;
				}
				if($configClass['multiple_work'] == 0){
					if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_time_int,$end_time_int)){
						$bgcolor = "gray";
						$nolink  = true;
					}
				}
				
				if(($dateArr1[2] == date("Y",$realtime) and ($dateArr1[1] == intval(date("m",$realtime))) and ($dateArr1[0] == intval(date("d",$realtime))))){
					//today
					if($start_time_int <= $realtime){
						$bgcolor = "gray";
						$nolink  = true;
					}
				}
				
				if(count((array)$tempEmployee) > 0)
				{
					//print_r($tempEmployee);
					for($k=0;$k<count($tempEmployee);$k++)
					{
						$employee = $tempEmployee[$k];

						$before_service = $employee->start_time;
						//echo date("H:i",$after_service);
						//echo "<BR />";
						$after_service  = $employee->end_time;
						
						if(($employee->start_time < $start_time_int) and ($end_time_int < $employee->end_time)){
							//echo "1";
							if(($avail <= 0) or ($employee->show == 0)){
								$bgcolor = "gray";
								$nolink = true;
							}
						}elseif(($employee->start_time > $start_time_int) and ($employee->start_time < $end_time_int)){
						
							//echo "2";
							if(($avail <= 0) or ($employee->show == 0)){
								$bgcolor = "gray";
								$nolink = true;
							}
						}elseif(($employee->end_time > $start_time_int) and ($employee->end_time < $end_time_int)){
							
							//echo "3";
							if(($avail <= 0) or ($employee->show == 0)){
								$bgcolor = "gray";
								$nolink = true;
							}
						}elseif(($employee->start_time <= $start_time_int) and ($employee->end_time >= $start_time_int)){
							//echo "4 ".$avail;
							//echo date("H:i", $start_time_int);
							//if($avail <= 0){
								$bgcolor = "gray";
								$nolink = true;
							//}
						}elseif($end_time_int <= $employee->start_time){
							if($bgcolor != "gray"){
								$bgcolor = $configClass['timeslot_background'];
								$nolink = false;
							}
						}else{
							if($bgcolor != "gray"){
								$bgcolor = $configClass['timeslot_background'];
								$nolink = false;
							}
						}
					}
				}
				if(($nolink && $configClass['show_occupied'] == 1) || !$nolink)
				{
					if($end_time_int <= $endtimetoday && $start_time_int >= $starttimetoday)
					{
						$j++;
						?>
						<div class="span6" style="border-bottom:1px solid #efefef !important;background-color:<?php echo $bgcolor?> !important;padding:2px;color:white;padding-left:10px;margin-left:1px;">
							<?php
							if(!$nolink && $avail > 0)
							{
								if($id > 0 && $booked_item->start_time == $start_time_int && $booked_item->end_time == $end_time_int)
								{
									$checked = "checked";
									$selected = "selected";
								}
								else
								{
									$checked = "";
									$selected = "";
								}
								$text = "Book this employee from [".date($configClass['date_time_format'],$start_time_int)."] to [".date($configClass['date_time_format'],$end_time_int)."]";
								?>
								<input type="<?php echo $type; ?>" <?php echo $checked; ?> name="<?php echo $eid?>[]" id="<?php echo $row->id?>" onclick="javascript:addBackendBooking(<?php echo $row->id?>,'<?php echo $start_time_int?>','<?php echo $end_time_int;?>');">
								<?php
								$str .= "<option value='".$start_time_int."-".$end_time_int."' ".$selected.">".$start_time_int."</option>";
							}
							else
							{
								?>
								<span color="White"><?php echo JText::_('OS_OCCUPIED')?></span>
								<?php
							}
							?>
							&nbsp;&nbsp;
							<?php
							$start_hour = $row->start_hour;
							if($start_hour < 10){
								$start_hour = "0".$start_hour;
							}
							//echo ":";
							$start_min = $row->start_min;
							if($start_min < 10){
								$start_min = "0".$start_min;
							}
							
							echo date($configClass['time_format'],strtotime(date("Y-m-d",$start_time_int)." ".$start_hour.":".$start_min.":00"));
							?>		
							&nbsp;-&nbsp;
							<?php
							$end_hour = $row->end_hour;
							if($end_hour < 10){
								$end_hour = "0".$end_hour;
							}
							$end_min = $row->end_min;
							if($end_min < 10){
								$end_min = "0".$end_min;
							}
							echo date($configClass['time_format'],strtotime(date("Y-m-d",$start_time_int)." ".$end_hour.":".$end_min.":00"));
							if($avail > 0)
							{
								?>	
								<BR />
								<?php
								echo JText::_('OS_NUMBER_SEATS').": ";
								?>
								<select name="nslots<?php echo $start_time_int."-".$end_time_int?>" id="nslots<?php echo $start_time_int."-".$end_time_int?>" class="input-mini form-select" style="display:inline;">
									<?php
									for($k=1;$k<=$avail;$k++){
										?>
										<option value="<?php echo $k?>"><?php echo $k?></option>
										<?php
									}
									?>
								</select>
							<?php } ?>
						</div>
						<?php
					}
					if($j == 2){
						$j = 0;
						?>
						</div><div class="row-fluid">
						<?php
					}
				}
			}
			if($j==1){
			?>
			</div>
			<?php
			?>
			<?php
		}
		if($j==0){
		?>
		</div>
		<?php
		}
		?>
        <div style="display: none;">
		<select name="selected_timeslots[]" id="selected_timeslots" multiple>
			<?php 
			echo $str;
			?>
		</select>
        </div>
		<?php
	}
	
	/**
	 * Get time zone
	 *
	 * @param unknown_type $remote_tz
	 * @param unknown_type $origin_tz
	 * @return unknown
	 */
	public static function get_timezone_offset($remote_tz, $origin_tz = null) {
	    if($origin_tz === null) {
	        if(!is_string($origin_tz = date_default_timezone_get())) {
	            return false; // A UTC timestamp was returned -- bail out!
	        }
	    }
	    $origin_dtz = new DateTimeZone($origin_tz);
	    $remote_dtz = new DateTimeZone($remote_tz);
	    $origin_dt = new DateTime("now", $origin_dtz);
	    $remote_dt = new DateTime("now", $remote_dtz);
	    $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	    return $offset;
	}


	/**
	 * Show time
	 *
	 * @param position $timezone
	 * @param int $timevalue
	 */
	static function showTime($timezone,$timevalue1,$timevalue2){
		global $configClass;
		$config = new JConfig();
		$rs = "";
		if($timezone  != ""){
			$timevalue1a    = date("Y-m-d H:i:s",$timevalue1);
			$timevalue2a   	= date("Y-m-d H:i:s",$timevalue2);
			
			$offset 		= $config->offset;
			$userTimezone 	= new DateTimeZone($offset);
			$gmtTimezone 	= new DateTimeZone('GMT');
			$myDateTime1 	= new DateTime($timevalue1a, $gmtTimezone);
			$myDateTime2 	= new DateTime($timevalue2a, $gmtTimezone);
			
			$offset 		= self::get_timezone_offset($timezone,$offset);
			
			$timevalue1 	-= $offset;
			$timevalue2 	-= $offset;
			$rs .= date($configClass['date_format'],$timevalue1).' : ';
			$rs .= date($configClass['time_format'],$timevalue1);
			$rs .= "-";
			$rs .= date($configClass['time_format'],$timevalue2);
		}
		return $rs;
	}

	/**
	 * Convert all img tags to use absolute URL
	 * @param string $html_content
	 */
	public static function convertImgTags($html_content)
	{
		$patterns = array();
		$replacements = array();
		$i = 0;
		$src_exp = "/src=\"(.*?)\"/";
		$link_exp = "[^http:\/\/www\.|^www\.|^https:\/\/|^http:\/\/]";
		$siteURL = JURI::root();
		preg_match_all($src_exp, $html_content, $out, PREG_SET_ORDER);
		foreach ($out as $val)
		{
			$links = preg_match($link_exp, $val[1], $match, PREG_OFFSET_CAPTURE);
			if ($links == '0')
			{
				$patterns[$i] = $val[1];
				$patterns[$i] = "\"$val[1]";
				$replacements[$i] = $siteURL . $val[1];
				$replacements[$i] = "\"$replacements[$i]";
			}
			$i++;
		}
		$mod_html_content = str_replace($patterns, $replacements, $html_content);
		
		return $mod_html_content;
	}
	
	public static function getUserTimeZone(){
		global $configClass;
		$config = new JConfig();
		$offset = $config->offset;
		if($configClass['allow_multiple_timezones'] == 1){
			$user = JFactory::getUser();
			if($user->id > 0){
				 $timezone = $user->getParam('timezone', $offset);
			}
		}
		return $timezone;
	}
	
	public static function getConfigTimeZone(){
		$config = new JConfig();
		$offset = $config->offset;
		return $offset;
	}
	
	public static function convertTimezone($int_time){
		$datetime = new DateTime(date("Y-m-d H:i:s",$int_time), new DateTimeZone(self::getConfigTimeZone()));
		$la_time = new DateTimeZone(self::getUserTimeZone());
		$datetime->setTimezone($la_time);
		return strtotime($datetime->format('Y-m-d H:i:s'));
		
	}
	
	public static function isOffDay($date_int)
	{
		$date  = date("N",$date_int);
		$dateformat = date("Y-m-d",$date_int);
		$db = JFactory::getDbo();
		$db->setQuery("Select `is_day_off` from #__app_sch_working_time where id = '$date'");
		$is_day_off = $db->loadResult();
		//echo $dateformat." : ".$is_day_off;
		if($is_day_off == 0)
		{
			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (`worktime_date` <= '$dateformat' and `worktime_date_to` >= '$dateformat')");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select `is_day_off` from #__app_sch_working_time_custom where (`worktime_date` <= '$dateformat' and `worktime_date_to` >= '$dateformat')");
				$vl = $db->loadResult();
				if($vl == 0)
				{
					$is_day_off = 0;
				}
				else
				{
					$is_day_off = 1;
				}
			}
		}
		else
		{
            $db->setQuery("Select count(id) from #__app_sch_working_time_custom where (`worktime_date` <= '$dateformat' and `worktime_date_to` >= '$dateformat')");
            $count = $db->loadResult();
            if($count > 0)
			{
                $db->setQuery("Select `is_day_off` from #__app_sch_working_time_custom where (`worktime_date` <= '$dateformat' and `worktime_date_to` >= '$dateformat')");
                $vl = $db->loadResult();
                if($vl == 0)
				{
                    $is_day_off = 0;
                }
				else
				{
                    $is_day_off = 1;
                }
            }
        }
		if($is_day_off == 0)
		{
			return false;
		}
		else 
		{
			return true;
		}
	}
	
	public static function getServices($category_id, $employee_id,$vid,$sid = 0)
	{
		$db = JFactory::getDbo();
		$catSql = "";
		if($category_id > 0)
		{
			$catSql = " and category_id = '$category_id' ";
			$db->setQuery("Select * from #__app_sch_categories where id = '$category_id'");
			$category = $db->loadObject();
		}
		
		if($employee_id > 0){
			$employeeSql = " and id in (Select service_id from #__app_sch_employee_service where employee_id = '$employee_id')";
		}else{
			$employeeSql = "";
		}
		
		if($vid > 0)
		{
			$vidSql = " and id in (Select sid from #__app_sch_venue_services where vid = '$vid')";
		}
		else
		{
			$vidSql = "";
		}
		if($sid > 0)
		{
			$sidSql = " and id = '$sid'";
		}
		else
		{
			$sidSql = "";
		}
		$db->setQuery("Select * from #__app_sch_services where published = '1' $catSql $sidSql $employeeSql $vidSql order by ordering");
		$services = $db->loadObjectList();
		
		return $services;
	}
	
	public static function loadEmployees($services,$employee_id,$tempdate,$vid)
	{
		$db = JFactory::getDbo();
		$return = 0;
		$day = strtolower(substr(date("D",$tempdate),0,2));
		$day1 = date("Y-m-d",$tempdate);
		foreach ($services as $service)
		{
			$sid = $service->id;
			if($vid > 0)
			{
				$vidSql = " and a.id IN (Select employee_id from #__app_sch_employee_service where service_id = '$sid' and vid = '$vid')";
			}
			else
			{
				$vidSql = "";
			}
			if($employee_id > 0)
			{
				$employeeSql = " and a.id = '$employee_id'";
			}
			else
			{
				$employeeSql = "";
			}
			$db->setQuery("Select a.* from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.published = '1' and b.service_id = '$sid' and b.".$day." = '1' and a.id NOT IN (Select eid from #__app_sch_employee_rest_days where rest_date <= '$day1' and rest_date_to >= '$day1') $vidSql $employeeSql order by b.ordering");
			$employees = $db->loadObjectList();
			if(count($employees) > 0)
			{
				$return = 1;
				
			}
		}
		
		if($return == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	static function checkDateInVenue($vid,$checkdate)
    {
		if(! isset($vid))
		{
			return 1;
		}
		else
		{
			$currentdate = HelperOSappscheduleCommon::getRealTime();
			
			$db = JFactory::getDbo();
			$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
			$venue = $db->loadObject();
			$disable_booking_before = $venue->disable_booking_before;
			switch ($disable_booking_before){
				case "2":
					$number_date_before = $venue->number_date_before;
					if($currentdate > $checkdate - ($number_date_before-1)*3600*24){
						return 0;
					}
				break;
				case "3":
					$disable_date_before = $venue->disable_date_before;
					$disable_date_before = strtotime($disable_date_before);
					//echo date("Y-m-d",$checkdate);
					if($disable_date_before > $checkdate){
						return 0;
						
					}
				break;
				case "4":
					$number_hour_before = $venue->number_hour_before;
					$number_date_before	= $number_hour_before / 24;
					if($number_date_before > 1){
						$mod = $number_hour_before % 24;
						$number_date_before = $number_hour_before / 24;
						if($currentdate > $checkdate - ($number_date_before-1)*3600*24){
							return 0;
						}
					}
				break;
			}
			
			
			$disable_booking_after = $venue->disable_booking_after;
			switch ($disable_booking_after){
				case "2":
					$number_date_after = $venue->number_date_after;
					if($currentdate + ($number_date_after-1)*3600*24 < $checkdate){
						return 0;
					}
				break;
				case "3":
					$disable_date_after = $venue->disable_date_after;
					$disable_date_after = strtotime($disable_date_after);
					//echo date("Y-m-d",$checkdate);
					if($disable_date_after < $checkdate){
						return 0;
						
					}
				break;
			}
		}
		return 1;
	}
	
	public static function isTheSameDate($date1,$date2){
		
		if(($date1 != "") && ($date2 != "") && ($date1 != "0") && ($date2 != "0")){
			$date1 = explode(" ",$date1);
			$date1 = $date1[0];
			$date2 = explode(" ",$date2);
			$date2 = $date2[0];

			
			if(strtotime($date1) == strtotime($date2)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function checkCommercialOptions($field){
		$db = JFactory::getDbo();
		$tempArr = array();
		if($field->field_type == 1){
			$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field->id'");
			$rows = $db->loadObjectList();
			if(count($rows) > 0){
				foreach ($rows as $row){
					if($row->additional_price > 0){
						if(!in_array("field_".$field->id."||1",$tempArr)){
							$tempArr[] = "field_".$field->id."||1";
						}
					}
				}
			}
		}elseif($field->field_type == 2){
			$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field->id'");
			$rows = $db->loadObjectList();
			if(count($rows) > 0){
				$i = 0;
				foreach ($rows as $row){
					if($row->additional_price > 0){
						$tempArr[] = "field_".$field->id."_checkboxes".$i."||2";
					}
					$i++;
				}
			}
		}
		return implode(",",$tempArr);
	}
	
	public static function encrypt_decrypt($action, $string) 
	{
		//$plain_txt = "This is my plain text";
		//
		//$encrypted_txt = encrypt_decrypt('encrypt', $plain_txt);
		//echo "Encrypted Text = $encrypted_txt\n";
		//echo "<br />";
		//$decrypted_txt = encrypt_decrypt('decrypt', $encrypted_txt);
		//echo "Decrypted Text = $decrypted_txt\n";
	   $output = false;
	   
	//	if(static function_exists( 'mcrypt_module_open' ) == false){
	//		logIt("Encryption module mcrypt is not enabled, some data is not being encrypted. For better security you should enable mcrypt.", "be_func2", "", "");
	//	}
		
		if(function_exists( 'mcrypt_module_open' ) == false || $string == "" || $string == null){
			return $string;
		}
	
	   $key = 'Sri}CU_BVD]X57v88RgNSGtM75xVX6';
	
	   // initialization vector 
	   $iv = md5(md5($key));
	
	   if( $action == 'encrypt' ) {
	       $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, $iv);
	       $output = base64_encode($output);
	   }
	   else if( $action == 'decrypt' ){
	       $output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, $iv);
	       $output = rtrim($output, "");
	   }
	   return $output;
	}
	
	
	/**
	 * Return Service Price
	 * @param unknown_type $sid
	 * @param unknown_type $date
	 * @return unknown
	 */
	public static function returnServicePriceShowing($sid,$date,$nslots = 1,$employee_id = 0)
	{
		global $configClass;
		$db = JFactory::getDbo();
		$additional_cost = 0;
		if($employee_id > 0){
			$db->setQuery("Select additional_price from #__app_sch_employee_service where employee_id = '$employee_id' and service_id = '$sid'");
			$additional_cost = $db->loadResult();
		}

		$amount = 0;
		$db->setQuery("Select service_price from #__app_sch_services where id = '$sid'");
		$price = $db->loadResult();

		$date_int = strtotime($date);
		$date_in_week = date("N",$date_int);

		$db->setQuery("Select count(id) from #__app_sch_service_price_adjustment where sid = '$sid' and date_in_week = '$date_in_week'");
		$count = $db->loadResult();

		if($count > 0)
		{

			$db->setQuery("Select * from #__app_sch_service_price_adjustment where sid = '$sid' and date_in_week = '$date_in_week'");
			$adjustment_price = $db->loadObject();
			if($adjustment_price->same_as_original == 1)
			{
				$price = $price;
			}
			else
			{
				$price = $adjustment_price->price;
			}
		}
		else
		{
			$price = $price;
		}
		
		$db->setQuery("Select count(id) from #__app_sch_service_custom_prices where sid = '$sid' and cstart <= '$date' and cend >= '$date'");
		$count = $db->loadResult();
		if($count > 0)
		{
			$db->setQuery("Select amount from #__app_sch_service_custom_prices where sid = '$sid' and cstart <= '$date' and cend >= '$date'");
			$amount = $db->loadResult();
			$amount += $additional_cost;
			$price_with_discount = self::checkEarlyBirdDiscount($sid,$date,$amount);//$amount;
			$price = self::discountBySlots($sid,$price_with_discount,$nslots);
			if($configClass['enable_tax'] && $configClass['show_service_cost_including_tax']){
				$price += $price*$configClass['tax_payment']/100;
			}
			return $price;
		}
		else
		{
			$price += $additional_cost;
			$price_with_discount = self::checkEarlyBirdDiscount($sid,$date,$price);//$price;
			$price = self::discountBySlots($sid,$price_with_discount,$nslots);
			if($configClass['enable_tax'] && $configClass['show_service_cost_including_tax']){
				$price += $price*$configClass['tax_payment']/100;
			}
			return $price;
		}
	}

	public static function returnServicePrice($sid, $date, $nslots = 1,$employee_id = 0, $discount_by_timeslots = false, $start_booking_time = '')
	{
		global $configClass;
		$config			= new JConfig();
		$offset			= $config->offset;
		date_default_timezone_set($offset);
		$db				= JFactory::getDbo();
		$additional_cost = 0;
		if($employee_id > 0)
		{
			$db->setQuery("Select additional_price from #__app_sch_employee_service where employee_id = '$employee_id' and service_id = '$sid'");
			$additional_cost = $db->loadResult();
		}

		$dateInt		= strtotime($date);
		$date_in_week	= date("N", $dateInt);
		$nullDate		= $db->quote($db->getNullDate());
		$nowDate		= $db->quote(JFactory::getDate()->toSql());

		$tmpSql			= "";
		if ($start_booking_time != '')
		{
			$bookedTime	= date("H:i:s", $start_booking_time);
			$tmpSql		= " ";
		}

		$db->setQuery("Select a.* from #__app_sch_special_prices as a inner join #__app_sch_special_price_services as b on a.id = b.price_id left join #__app_sch_special_price_weekdays as c on c.price_id = a.id where b.sid = '$sid' and c.weekday = '$date_in_week' AND ((a.publish_up is null  or  a.publish_up = ".$nullDate." or a.publish_up <= ".$nowDate."  or a.publish_up = '0000-00-00') AND (a.publish_down is null or a.publish_down = ".$nullDate." or a.publish_down >= ".$nowDate." or a.publish_down = '0000-00-00'))");
		$special_price  = $db->loadObjectList();
		$special_amount      = 0;
		if(count($special_price) > 0)
		{
			$special_price   = $special_price[0];
			$apply_from	     = $special_price->apply_from;
			$apply_to	     = $special_price->apply_to;
			$apply_from_int	 = 0;
			$apply_to_int    = 0;
			if($apply_from != "" && $apply_from != "00:00:00")
			{
				$apply_from_date = $date." ".$apply_from;
				$apply_from_int  = strtotime($apply_from_date);

			}
			if($apply_to != "" && $apply_to != "00:00:00")
			{
				$apply_to_date   = $date." ".$apply_to;
				$apply_to_int    = strtotime($apply_to_date);
			}

			$from			= false;
			if($apply_from_int > 0 && $start_booking_time >= $apply_from_int)
			{
				$from		= true;
			}
			elseif($apply_from_int == 0)
			{
				$from		= true;
			}
			
			$to				= false;
			if($apply_to_int > 0 && $start_booking_time <= $apply_to_int)
			{
				$to			= true;
			}
			elseif($apply_from_int == 0)
			{
				$to			= true;
			}
			if($from && $to)
			{
				$cost_type  = $special_price->cost_type;
				if($cost_type == 0)
				{
					$special_amount = (-1)*$special_price->cost;
				}
				else
				{
					$special_amount = $special_price->cost;
				}
			}
		}


		$amount			= 0;
		$db->setQuery("Select service_price from #__app_sch_services where id = '$sid'");
		$price			= $db->loadResult();

		//$date_int		= strtotime($date);
		//$date_in_week = date("N",$date_int);

		$db->setQuery("Select count(id) from #__app_sch_service_price_adjustment where sid = '$sid' and date_in_week = '$date_in_week'");
		$count = $db->loadResult();

		if($count > 0)
		{

			$db->setQuery("Select * from #__app_sch_service_price_adjustment where sid = '$sid' and date_in_week = '$date_in_week'");
			$adjustment_price = $db->loadObject();
			if($adjustment_price->same_as_original == 1)
			{
				$price = $price;
			}
			else
			{
				$price = $adjustment_price->price;
			}
		}
		else
		{
			$price = $price;
		}


		$db->setQuery("Select count(id) from #__app_sch_service_custom_prices where sid = '$sid' and cstart <= '$date' and cend >= '$date'");
		$count = $db->loadResult();

		if($count > 0)
		{
			$db->setQuery("Select amount from #__app_sch_service_custom_prices where sid = '$sid' and cstart <= '$date' and cend >= '$date'");
			$amount = $db->loadResult();
			$amount += $additional_cost;
			$amount += $special_amount;
			$price_with_discount = self::checkEarlyBirdDiscount($sid,$date,$amount);//$amount;
			//echo $price_with_discount;die();
            if($discount_by_timeslots)
            {
                $price = self::discountBySlotsWithoutCheckingNumberSlots($sid, $price_with_discount);
            }
            else
            {
                $price = $price_with_discount;
            }
			//echo $price;die();
			return $price;
		}
		else
		{
			$price += $additional_cost;
			$price += $special_amount;
			$price_with_discount = self::checkEarlyBirdDiscount($sid,$date,$price);//$price;
			///echo $price_with_discount;die();
            if($discount_by_timeslots)
            {
                $price = self::discountBySlotsWithoutCheckingNumberSlots($sid, $price_with_discount);
            }
            else
            {
                $price = $price_with_discount;
            }
			//echo $price;die();
			return $price;
		}
	}
	
	/**
	 * Discount by number slots
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $price
	 * @param unknown_type $nslots
	 */
	public static function discountBySlots($sid,$price,$nslots)
	{
		$db = JFactory::getDbo();
		$configClass = self::loadConfig();
		if($configClass['enable_slots_discount'] == 1)
		{
			$db->setQuery("Select service_time_type,discount_timeslots,discount_type,discount_amount from #__app_sch_services where id = '$sid'");
			$service = $db->loadObject();
			if(($service->discount_timeslots > 0) and ($service->discount_timeslots < $nslots) and ($service->service_time_type == 1))
			{
				$discount_type = $service->discount_type;
				$discount_amount = $service->discount_amount;
				if($discount_type == 0)
				{
					$discount = $discount_amount;
				}
				else
				{
					$discount = round($discount_amount*$price/100,2);
				}
			}
			else
			{
				return $price;
			}
			if($price - $discount < 0)
			{
				return 0;
			}
			else
			{
				return $price - $discount;
			}
		}
		else
		{
			return $price;
		}
	}

	/**
	 * Discount by number slots
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $price
	 * @param unknown_type $nslots
	 */
	public static function discountBySlotsWithoutCheckingNumberSlots($sid,$price)
	{
		$db = JFactory::getDbo();
		$configClass = self::loadConfig();
		if($configClass['enable_slots_discount'] == 1)
		{
			$db->setQuery("Select service_time_type,discount_timeslots,discount_type,discount_amount from #__app_sch_services where id = '$sid'");
			$service = $db->loadObject();
			if(($service->discount_timeslots > 0) && ($service->service_time_type == 1))
			{
				$discount_type = $service->discount_type;
				$discount_amount = $service->discount_amount;
				if($discount_type == 0)
				{
					$discount = $discount_amount;
				}
				else
				{
					$discount = round($discount_amount*$price/100,2);
				}
			}
			else
			{
				return $price;
			}
			if($price - $discount < 0)
			{
				return 0;
			}
			else
			{
				return $price - $discount;
			}
		}
		else
		{
			return $price;
		}
	}
	
	/**
	 * Check Early Bird Discount
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $date
	 */
	public static function checkEarlyBirdDiscount($sid,$date,$amount){
		$db = JFactory::getDbo();
		$configClass = OSBHelper::loadConfig();
		$enable_early_bird = $configClass['early_bird'];
		if($enable_early_bird == 1)
		{
			$db->setQuery("Select early_bird_amount, early_bird_type,early_bird_days from #__app_sch_services where id = '$sid'");
			$bird = $db->loadObject();
			$current_date = HelperOSappscheduleCommon::getRealTime();
			$date_int = strtotime($date);
			if($current_date + $bird->early_bird_days*3600*24 <= $date_int)
			{
				if($bird->early_bird_type == 0)
				{
					$discount = $bird->early_bird_amount;
				}
				else
				{
					$discount = round($bird->early_bird_amount*$amount/100,2);
				}
			}
			else
			{
				return $amount;
			}
			if($amount - $discount < 0){
				return 0;
			}else{
				return $amount - $discount;
			}
		}else{
			return $amount;
		}
	}
	
	/**
	 * Generate Decimal number
	 *
	 * @param unknown_type $value
	 * @return unknown
	 */
	public static function generateDecimal($value){
		return rtrim(rtrim($value,'0'),'.');
	}
	
	public static function customServicesDiscountChecking($sid){
		$configClass = self::loadConfig();
		if($configClass['enable_slots_discount'] == 1){
			$db = JFactory::getDbo();
			$db->setQuery("Select discount_timeslots,discount_type,discount_amount from #__app_sch_services where id = '$sid'");
			$service = $db->loadObject();
			if(($service->discount_amount > 0) and ($service->discount_timeslots > 0)){
				if($service->discount_type == 0){
					$discount = self::generateDecimal($service->discount_amount). " ". $configClass['currency_format']. " ".JText::_('OS_PER_SLOT');
				}else{
					$discount = self::generateDecimal($service->discount_amount) ." % ".JText::_('OS_PER_SLOT_COST');
				}
				?>
				<div class="clearfix"></div>
				<div class="noticeMsg">
					<?php echo JText::sprintf('OS_CUSTOM_TIMESLOTS_DISCOUNT_MSG',$discount, $service->discount_timeslots);?>
				</div>
				<?php 
			}else{
				//do nothing
			}
		}
	}
	
	/**
	 * Order status
	 *
	 * @param unknown_type $order_id
	 * @param unknown_type $status
	 */
	public static function orderStatus($order_id = 0,$status = '')
	{
		switch ($status){
			case "P":
				return JText::_('OS_PENDING');
			break;
			case "S":
				return JText::_('OS_COMPLETED');
			break;
			case "C":
				return JText::_('OS_CANCELED');
			break;
			case "A":
				return JText::_('OS_ATTENDED');
			break;
			case "R":
				return JText::_('OS_REFUNDED');
			break;
			case "D":
				return JText::_('OS_DECLINED');
			break;
			case "T":
				return JText::_('OS_TIMEOUT');
			break;
			default:
				return '';
			break;
		}
	}
	
	/**
	 * Build Order status dropdown list
	 *
	 * @param unknown_type $status
	 */
	public static function buildOrderStaticDropdownList($status,$onChangeScript,$firstoption,$name)
	{
		global $mapClass;
		$optionArr = array();
		$statusArr = array(JText::_('OS_PENDING'),JText::_('OS_COMPLETED'),JText::_('OS_CANCELED'),JText::_('OS_ATTENDED'),JText::_('OS_TIMEOUT'),JText::_('OS_DECLINED'),JText::_('OS_REFUNDED'));
		$statusVarriableCode = array('P','S','C','A','T','D','R');
		if($firstoption != ""){
			$optionArr[] = JHtml::_('select.option','',$firstoption);
		}
		for ($i=0;$i<count($statusArr);$i++){
			$optionArr[] = JHtml::_('select.option',$statusVarriableCode[$i],$statusArr[$i]);
		}
		return JHtml::_('select.genericlist',$optionArr,$name,'class="input-medium form-select ilarge" '.$onChangeScript,'value','text',$status);
	}
	
	/**
	 * Load OS Services Booking language file
	 */
	public static function loadLanguage()
	{
		static $loaded;
		if (!$loaded)
		{
			$lang = JFactory::getLanguage();
			$tag  = $lang->getTag();
			if (!$tag)
				$tag = 'en-GB';
			$lang->load('com_osservicesbooking', JPATH_ROOT, $tag);
			$loaded = true;
		}
	}


    public static function generateQrcode($order_id)
	{
		require_once JPATH_ROOT . '/components/com_osservicesbooking/helpers/phpqrcode/qrlib.php';
        jimport('joomla.filesystem.folder');
        if(!JFolder::exists(JPATH_ROOT . '/media/com_osservicesbooking')){
            JFolder::create(JPATH_ROOT . '/media/com_osservicesbooking');
            JFolder::create(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes');
        }
        if(!JFolder::exists(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes')){
            JFolder::create(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes');
        }
        $filename = $order_id . '.png';
        if (!file_exists(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes/' . $filename))
        {
            $checkinUrl = self::getSiteUrl() . 'index.php?option=com_osservicesbooking&task=default_checkin&id=' . $order_id;
            QRcode::png($checkinUrl, JPATH_ROOT . '/media/com_osservicesbooking/qrcodes/' . $filename);
        }

		$db = JFactory::getDbo();
		$db->setQuery("Select id from #__app_sch_order_items where order_id = '$order_id'");
		$items = $db->loadColumn(0);
		if(count($items))
		{
			foreach($items as $item)
			{
				$filename = $item . '.png';
				if (!file_exists(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes/item_' . $filename))
				{
					//$checkinUrl = self::getSiteUrl() . 'index.php?option=com_osservicesbooking&task=default_checkinitem&id=' . $item;
					$code = "OrderItem_".$item;
					QRcode::png($code, JPATH_ROOT . '/media/com_osservicesbooking/qrcodes/item_' . $filename);
				}
			}
		}
    }

    /**
     * Get URL of the site, using for Ajax request
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getSiteUrl()
    {
        $uri  = JUri::getInstance();
		$mainframe = JFactory::getApplication();
        $base = $uri->toString(array('scheme', 'host', 'port'));
        if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
        {
            $script_name = $_SERVER['PHP_SELF'];
        }
        else
        {
            $script_name = $_SERVER['SCRIPT_NAME'];
        }
        $path = rtrim(dirname($script_name), '/\\');
        if ($path)
        {
            $siteUrl = $base . $path . '/';
        }
        else
        {
            $siteUrl = $base . '/';
        }
        if ($mainframe->isClient('administrator'))
        {
            $adminPos = strrpos($siteUrl, 'administrator/');
            $siteUrl  = substr_replace($siteUrl, '', $adminPos, 14);
        }
        return $siteUrl;
    }

	public static function getUniqueCookie()
	{
		$session = JFactory::getSession();
		$unique_cookie = $session->get('unique_cookie');
		return $unique_cookie;
	}

    /**
     * This static function is used to check if this logged user already made the booking request
     * Return true if user already booking one timeslot this $booking_date
     * $booking_date is date ($date_type = 1) or int_date ($date_type = 0)
     *
     */
    public static function isAreadyBooked($booking_date,$date_type)
	{
        if($date_type == 0)
		{
            $booking_date = date("Y-m-d",$booking_date);
        }
        $user = JFactory::getUser();
        if($user->id > 0) 
		{
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('count(a.id)')->from('#__app_sch_temp_orders as a')->leftJoin('#__app_sch_temp_order_items as b on b.order_id = a.id')->where('a.user_id = "'.$user->id.'" and b.booking_date = "'.$booking_date.'"');
            $db->setQuery($query);
            $count = $db->loadResult();
            if($count > 0)
			{
                return true;
            }

            $query->clear();
            $query->select('count(a.id)')->from('#__app_sch_orders as a')->leftJoin('#__app_sch_order_items as b on b.order_id = a.id')->where('a.user_id = "'.$user->id.'" and b.booking_date = "'.$booking_date.'" and a.order_status in ("S","P")');
            $db->setQuery($query);
            $count = $db->loadResult();
            if($count > 0)
			{
                return true;
            }
        }
        return false;
    }

	public static function isPrepaidPaymentPublished()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('count(id)')->from('#__app_sch_plugins')->where('`name` = "os_prepaid" and published = "1"');
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


    /**
     * Access Dropdown
     * @param $access
     */
    public static function accessDropdown($name, $selected, $attribs = 'class="input-small form-select"', $params = true, $id = false)
	{
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.id AS value, a.title AS text')
            ->from('#__viewlevels AS a')
            ->group('a.id, a.title, a.ordering')
            ->order('a.ordering ASC')
            ->order($db->quoteName('title') . ' ASC');

        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();

        // If params is an array, push these options to the array
        if (is_array($params))
        {
            $options = array_merge($params, $options);
        }
        // If all levels is allowed, push it into the array.
        elseif ($params)
        {
            array_unshift($options, JHtml::_('select.option', '', JText::_('JOPTION_ACCESS_SHOW_ALL_LEVELS')));
        }

        return JHtml::_(
            'select.genericlist',
            $options,
            $name,
            array(
                'list.attr' => $attribs,
                'list.select' => $selected,
                'id' => $id
            )
        );
    }

    public static function returnAccessLevel($access){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title')
            ->from('#__viewlevels')
            ->where('id = '.$access);
        // Get the options.
        $db->setQuery($query);
        $accesslevel = $db->loadResult();
        return $accesslevel;
    }

	public static function showProgressBar($task,$passlogin){
		global $configClass,$mapClass;
		if($configClass['show_progress_bar'] == 1){
			if($configClass['progress_bar_background'] == ""){
				$configClass['progress_bar_background'] = "#1C67A9";
			}
			$user = JFactory::getUser();
			if($task == ""){
				$step = 1;
				$s1   = "current";
				$s2   = "next";
				$s3   = "next";
				$s4   = "next";
			}elseif($task == "form_step1"){
				if((($configClass['allow_registered_only'] == 1) or (($configClass['allow_registered_only'] == 2) and ($passlogin == 0))) and ($user->id == 0)){
					$step = 2;
					$s1   = "complete";
					$s2   = "current";
					$s3   = "next";
					$s4   = "next";
				}else{
					$step = 3;
					$s1   = "complete";
					$s2   = "complete";
					$s3   = "current";
					$s4   = "next";
				}
			}elseif($task == "form_step2"){
					$step = 4;
					$s1   = "complete";
					$s2   = "complete";
					$s3   = "complete";
					$s4   = "current";
			}

			?>
			<style>
			ol.vbo-stepbar li.vbo-step-complete::before {
				background-color: <?php echo $configClass['progress_bar_background'];?>;
			}
			ol.vbo-stepbar li.vbo-step-current::before {
				background-color: <?php echo $configClass['progress_bar_background'];?>;
			}
			ol.vbo-stepbar li.vbo-step-complete, ol.vbo-stepbar li.vbo-step-current {
				border-bottom: 4px solid <?php echo $configClass['progress_bar_background'];?>;
			}
			</style>
			<div class="vbstepsbarcont bookingformdiv <?php echo $mapClass['row-fluid'];?>">
				<ol class="vbo-stepbar span12" data-vbosteps="<?php echo $step?>">
					<li class="vbo-step vbo-step-<?php echo $s1?>">
						<span><?php echo JText::_('OS_SELECT_TIME_SLOT');?></span>
					</li>
					<?php
					if($configClass['allow_registered_only'] == 1 || $configClass['allow_registered_only'] == 2)
					{
					?>
						<li class="vbp-step vbo-step-<?php echo $s2?>">
							<span><?php echo JText::_('OS_LOGIN');?>
							<?php
							if($configClass['allow_registration'] == 1)
							{
							?>
							/ <?php echo JText::_('OS_REGISTER');?></span>
							<?php
							}	
							?>
						</li>
					<?php } ?>
					<li class="vbp-step vbo-step-<?php echo $s3?>">
						<span><?php echo JText::_('OS_CHECKIN');?></span>
					</li>
					<?php
					if($configClass['remove_confirmation_step'] == 0){
					?>
					<li class="vbp-step vbo-step-<?php echo $s4?>">
						<span><?php echo JText::_('OS_CONFIRM');?></span>
					</li>
					<?php } ?>
				</ol>
			</div>
			<?php
		}
	}

	static function sendWaitingNotification($order_id){
		global $configClass,$mainframe;
		if($order_id > 0){
			$config = new JConfig();
			$offset = $config->offset;
			date_default_timezone_set($offset);	
			$db = JFactory::getDbo();
			$db->setQuery("Select id from #__app_sch_order_items where order_id = '$order_id'");
			$items = $db->loadObjectList();
			if(count($items) > 0){
				foreach($items as $item){
					self::sendWaitingNotificationItem($item->id);
				}
			}
		}
	}

	static function sendWaitingNotificationItem($id){
		global $configClass,$mainframe;
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_order_items where id = '$id'");
		$item = $db->loadObject();

		$db->setQuery("Select * from #__app_sch_emails where email_key = 'new_free_slot'");
		$emailObj = $db->loadObject();

		$db->setQuery("Select * from #__app_sch_waiting_list where sid = '$item->sid' and eid = '$item->eid' and start_time = '$item->start_time' and end_time = '$item->end_time'");
		$waiting = $db->loadObjectList();
		$db->setQuery("Select service_name from #__app_sch_services where id = '$item->sid'");
		$service_name = $db->loadResult();

		$db->setQuery("Select employee_name from #__app_sch_employee where id = '$item->eid'");
		$employee_name = $db->loadResult();

		$date = date($configClass['date_format'],$item->start_time)." ".JText::_('OS_START').": ".date($configClass['time_format'],$item->start_time)." - ".JText::_('OS_END').": ".date($configClass['time_format'],$item->end_time);

		$details_url = JUri::root()."index.php?option=com_osservicesbooking&sid=".$item->sid."&employee_id=".$item->eid."&date_from=".$item->booking_date."&date_to=".$item->booking_date;

		$details_url = "<a href='".$details_url."' target='_blank'>".$details_url."</a>";
		if(count($waiting) > 0){
			$config = new JConfig();
			$mailfrom = $config->mailfrom;
			$fromname = $config->fromname;
			foreach($waiting as $wait){

				$unsub_url = JUri::root()."index.php?option=com_osservicesbooking&task=default_unsubwaitinglist&id=".$wait->id."&code=".md5($wait->id);
				$unsub_url = "<a href='".$unsub_url."' target='_blank'>".$unsub_url."</a>";

				$email = $wait->email;
				$email_title = $emailObj->email_subject;
				$email_body = $emailObj->email_content;
				$email_body = str_replace("{service}",$service_name,$email_body);
				$email_body = str_replace("{employee}",$employee_name,$email_body);
				$email_body = str_replace("{datetime}",$date,$email_body);
				$email_body = str_replace("{booking_url}",$details_url,$email_body);
				$email_body = str_replace("{unsub_url}",$unsub_url,$email_body);
				$mailer = JFactory::getMailer();
				if($email_title != "" && $email_body != "" && $mailfrom != "")
				{
					try
					{
						$mailer->Sendmail($mailfrom,$fromname,$email,$email_title,$email_body,1);
					}
					catch (Exception $e)
					{
						JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
					}
				}
			}
		}
	}

	static function cleanData(){
		$db = JFactory::getDbo();
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$current_time = time();
		$last_one_hour = $current_time - 3600;
		$db->setQuery("Select id from #__app_sch_temp_orders where created_on < '$last_one_hour'");
		//$db->execute();
		$temp_ids = $db->loadColumn(0);
		if(count($temp_ids) > 0){
			$db->setQuery("Delete from #__app_sch_temp_orders where id in (".implode(",",$temp_ids).")");
			$db->execute();
			$db->setQuery("Delete from #__app_sch_temp_order_items where order_id in (".implode(",",$temp_ids).")");
			$db->execute();
		}

		$db->setQuery("Delete from #__app_sch_waiting_list where start_time < '$current_time'");
		$db->execute();
	}

	static function getStringValue($name,$defaultvalue){
		global $jinput;
		$jinput = JFactory::getApplication()->input;
		$getdata = $jinput->get($name,$defaultvalue,'string');
		$badchars = array('#', '>', '<', '\\','%','\'','"');
        $getdata = trim(str_replace($badchars, '', $getdata));
		return $getdata;
	}

	/**
	 * Check is Photo file
	 * Return false : if it is not the JPEG photo
	 * Return true  : if it is JPEG photo
	 */
	static function checkIsPhotoFileUploaded($element_name){
		$file = $_FILES[$element_name];
		$fname = $file['name'];
		$ftype = end(explode('.', strtolower($fname)));
		$ftype = strtolower($ftype);
		$allowtype = array('jpg','jpeg','gif','png');
		if(!in_array($ftype,$allowtype)){
			return false;
		}else{
			//return true;
			$imageinfo = getimagesize($_FILES[$element_name]['tmp_name']);
			if(strtolower($imageinfo['mime']) != 'image/jpeg' && strtolower($imageinfo['mime']) != 'image/jpg' && strtolower($imageinfo['mime']) != 'image/png' && strtolower($imageinfo['mime']) != 'image/gif') {
			    return false;
			}else{
				return true;
			}
		}
	}

	/**
	 * Check is Custom field file
	 * Return false : if it is not the allowed file
	 * Return true  : if it is allowed file
	 */
	static function checkIsFileUploaded($element_name){
		$configClass = self::loadConfig();
		$file = $_FILES[$element_name];
		$fname = $file['name'];
		$ftype = end(explode('.', strtolower($fname)));
		$ftype = strtolower($ftype);
		$allowtype = $configClass['allowed_file_types'];
		if($allowtype == "")
		{
			$allowtype = "pdf,doc,docx,xls,xlsx";
		}
		$allowtype = explode(",", $allowtype);
		if(!in_array($ftype,$allowtype))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
     * Return the correct image name
     *
     * @param unknown_type $image_name
     * @return unknown
     */
    public static function processImageName($image_name)
    {
		jimport('joomla.filesystem.file');
		JFile::makeSafe($image_name);
        $image_name = str_replace(" ", "", $image_name);
        $image_name = str_replace("'", "", $image_name);
        $image_name = str_replace("\n", "", $image_name);
        $image_name = str_replace("\r", "", $image_name);
        $image_name = str_replace("\x00", "", $image_name);
        $image_name = str_replace("\x1a", "", $image_name);
		$image_nameArr = explode(".", $image_name);
		$file_extension = $image_nameArr[count($image_nameArr) - 1];
		$file_name1 = str_replace(".".$file_extension,"", $image_name);
		$image_name = $file_name1."_".time().".".$file_extension;
        return $image_name;
    }

    public static function generateBoostrapVariables()
	{
        global $configClass,$bootstrapHelper;
        $configClass = self::loadConfig();
		if((int)$configClass['bootstrap_version'] == 0)
		{
			$bootstrap_version = 2;
		}
		elseif((int)$configClass['bootstrap_version'] == 1)
		{
            $bootstrap_version = 3;
        }
		elseif((int)$configClass['bootstrap_version'] == 2)
		{
            $bootstrap_version = 4;
        }
		elseif((int)$configClass['bootstrap_version'] == 3)
		{
            $bootstrap_version = 5;
        }
		elseif((int)$configClass['bootstrap_version'] == 4)
		{
            $bootstrap_version = 6;
        }
		if(JFactory::getApplication()->isClient('administrator') && ! self::isJoomla4())
		{
			$bootstrap_version = 2;
		}
		elseif(JFactory::getApplication()->isClient('administrator') &&  self::isJoomla4())
		{
			$bootstrap_version = 4;
		}
        $bootstrapHelper = new OsbHelperBootstrap($bootstrap_version);
    }

	public static function generateMapClassNames(){
		global $bootstrapHelper,$mapClass;
		$mapClass = array();
		$mapClass['row-fluid']      = $bootstrapHelper->getClassMapping('row-fluid');
		$mapClass['span1']          = $bootstrapHelper->getClassMapping('span1');
		$mapClass['span2']          = $bootstrapHelper->getClassMapping('span2');
		$mapClass['span3']          = $bootstrapHelper->getClassMapping('span3');
		$mapClass['span4']          = $bootstrapHelper->getClassMapping('span4');
		$mapClass['span5']          = $bootstrapHelper->getClassMapping('span5');
		$mapClass['span6']          = $bootstrapHelper->getClassMapping('span6');
		$mapClass['span7']          = $bootstrapHelper->getClassMapping('span7');
		$mapClass['span8']          = $bootstrapHelper->getClassMapping('span8');
		$mapClass['span9']          = $bootstrapHelper->getClassMapping('span9');
		$mapClass['span10']         = $bootstrapHelper->getClassMapping('span10');
		$mapClass['span11']         = $bootstrapHelper->getClassMapping('span11');
		$mapClass['span12']         = $bootstrapHelper->getClassMapping('span12');
		$mapClass['control-group']  = $bootstrapHelper->getClassMapping('control-group');
		$mapClass['control-label']  = $bootstrapHelper->getClassMapping('control-label');
		$mapClass['controls']       = $bootstrapHelper->getClassMapping('controls');
        $mapClass['input-small']    = $bootstrapHelper->getClassMapping('input-small');
        $mapClass['input-medium']   = $bootstrapHelper->getClassMapping('input-medium');
        $mapClass['input-large']    = $bootstrapHelper->getClassMapping('input-large');
        $mapClass['icon-tag']       = $bootstrapHelper->getClassMapping('icon-tag');
        $mapClass['icon-phone']     = $bootstrapHelper->getClassMapping('icon-phone');
        $mapClass['icon-mail']      = $bootstrapHelper->getClassMapping('icon-mail');
	}

	public static function allowCancelOrder($order_id){
		global $configClass;
		if($configClass['allow_cancel_request'] == 0){
			return false;
		}else{
			$current_time = HelperOSappscheduleCommon::getRealTime();
			$cancel_before = $configClass['cancel_before'];
			$db = JFactory::getDbo();
			$db->setQuery("Select * from #__app_sch_order_items where order_id = '$order_id'");
			$items = $db->loadObjectList();
			if(count($items) > 0){
				foreach($items as $item){
					$start_time = $item->start_time;
					if($current_time + $cancel_before*3600 > $start_time){
						return false;
					}
				}
			}
			return true;
		}
	}

	public static function allowCancelOrderItem($order_item){
		global $configClass;
		if($configClass['allow_cancel_request'] == 0){
			return false;
		}else{
			$current_time = HelperOSappscheduleCommon::getRealTime();
			$cancel_before = $configClass['cancel_before'];
			$db = JFactory::getDbo();
			$db->setQuery("Select * from #__app_sch_order_items where id = '$order_item'");
			$item = $db->loadObject();
			if($current_time + $cancel_before*3600 > $item->start_time){
				return false;
			}
			return true;
		}
	}

	public static function getUserGroups(){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('group_id')->from('user_usergroup_map')->where('user_id="'.$user->id.'"');
		$db->setQuery($query);
		$userGroups = $db->loadColumn(0);
	}

	/**
	 * Check coupon available
	 *
	 * @param unknown_type $sid
	 */
	static function checkCouponAvailable(){
		global $mainframe;
		$db				= JFactory::getDbo();
		$user			= JFactory::getUser();
		$total			= OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem();
		$nullDate		= $db->quote($db->getNullDate());
		$nowDate		= $db->quote(JFactory::getDate()->toSql());
		$db->setQuery('Select count(a.id) from #__app_sch_coupons as a where a.published = 1 AND discount_by = 0 AND (start_time = '.$nullDate.' or start_time <= '.$nowDate.') AND (expiry_date = '.$nullDate.' or expiry_date >= '.$nowDate.') and (minimum_cost = "0" or (minimum_cost > 0 and minimum_cost <= "'.$total.'")) and `access` IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');
		//echo $db->getQuery();
		$ncoupons		= $db->loadResult();
		if($ncoupons > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	static function isAnyAvailableGroupDiscount($total)
	{
		$db             = JFactory::getDbo();
		$query          = $db->getQuery(true);
		$nullDate       = $db->quote($db->getNullDate());
		$nowDate        = $db->quote(JFactory::getDate()->toSql());
		$query->select('count(id)')->from('#__app_sch_coupons')->where('published = 1 AND (minimum_cost = 0 OR (minimum_cost > 0 and minimum_cost <= '.$total.')) AND discount_by = 1 AND (start_time = '.$nullDate.' or start_time <= '.$nowDate.') AND (expiry_date = '.$nullDate.' or expiry_date >= '.$nowDate.')');
		$db->setQuery($query);
		$count          = $db->loadResult();
		if($count > 0)
		{
		    //next, check to see if user has permission to get this discount
            $user       = JFactory::getUser();
            $usergroups = $user->groups;
            $query->clear();
            $query->select('applied_groups')->from('#__app_sch_coupons')->where('published = 1 AND (minimum_cost = 0 OR (minimum_cost > 0 and minimum_cost <= '.$total.')) AND discount_by = 1 AND (start_time = '.$nullDate.' or start_time <= '.$nowDate.') AND (expiry_date = '.$nullDate.' or expiry_date >= '.$nowDate.')');
            $db->setQuery($query);
            $applied_groups = $db->loadObjectList();
			foreach($applied_groups as $applied_group)
			{
				$applied_group = trim($applied_group->applied_groups);
				if($applied_group != '')
				{
					$applied_group = explode(",",$applied_group);
					foreach($applied_group as $g)
					{
						if(in_array($g,$usergroups))
						{
							return true;
						}
					}
					//return false;
				}
			}
			return false;
		}
		else
		{
			return false;
		}
	}

	static function getDiscount()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(JFactory::getDate()->toSql());
		$query->select('*')->from('#__app_sch_coupons')->where('published = 1 AND discount_by = 0 AND (start_time = '.$nullDate.' or start_time <= '.$nowDate.') AND (expiry_date = '.$nullDate.' or expiry_date >= '.$nowDate.')');
		$db->setQuery($query);
		$discounts = $db->loadObject();
		return $discount;
	}

	static function getGroupDiscountAmount()
	{
		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$nullDate		= $db->quote($db->getNullDate());
		$nowDate		= $db->quote(JFactory::getDate()->toSql());
		$query->select('*')->from('#__app_sch_coupons')->where('published = 1 AND discount_by = 1 AND (start_time = '.$nullDate.' or start_time <= '.$nowDate.') AND (expiry_date = '.$nullDate.' or expiry_date >= '.$nowDate.')');
		$db->setQuery($query);
		$discounts		= $db->loadObjectList();

		$user			= JFactory::getUser();
        $usergroups		= $user->groups;

		if(count($discounts))
		{
			foreach($discounts as $discount)
			{
				$applied_group = trim($discount->applied_groups);
				if($applied_group != '')
				{
					$applied_group = explode(",",$applied_group);
					foreach($applied_group as $g)
					{
						if(in_array($g,$usergroups))
						{
							return $discount;
						}
					}
				}
			}
		}
		return null;
	}

	static function getOrderGroupDiscount()
	{
		$user = JFactory::getUser();
		$total = OsAppscheduleAjax::getOrderCost();
		$discount_value = 0;
		if($total > 0 && $user->id > 0 && OSBHelper::isAnyAvailableGroupDiscount())
		{
			$discount			= OSBHelper::getGroupDiscountAmount();
			$discount_amount	= $discount->discount;
			$discount_type		= $discount->discount_type;
			if($discount_type == 0)
			{
				$discount_value = $total*$discount_amount/100;
			}
			else
			{
				$discount_value = $discount_amount;
			}
		}
		return $discount_value;
	}

    /**
     * Generate article selection box
     *
     * @param int    $fieldValue
     * @param string $fieldName
     *
     * @return string
     */
    public static function getArticleInput($fieldValue, $fieldName = 'article_id')
    {
        JHtml::_('jquery.framework');
        JFormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_content/models/fields');
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			JFormHelper::addFieldPrefix('Joomla\Component\Content\Administrator\Field');

		}
        $field = JFormHelper::loadFieldType('Modal_Article');
		if (version_compare(JVERSION, '4.2.0-dev', 'ge'))
		{
			$field->setDatabase(JFactory::getDbo());
		}
        $element = new SimpleXMLElement('<field />');
        $element->addAttribute('name', $fieldName);
        $element->addAttribute('select', 'true');
        $element->addAttribute('clear', 'true');

        $field->setup($element, $fieldValue);

        return $field->input;
    }

    public static function isAvailableDate($checkdate,$category,$employee_id,$vid)
	{
        $services  = OSBHelper::getServices($category,$employee_id,$vid);
        $employees = OSBHelper::loadEmployees($services,$employee_id,$checkdate,$vid);
        $venue_check = 1;
        if($vid > 0){
            $venue_check = OSBHelper::checkDateInVenue($vid,$checkdate);
        }
        if((OSBHelper::isOffDay($checkdate)) || count($services) == 0 || ! $employees || $venue_check == 0)
		{
            return false;
        }
		else
		{
			if(!self::checkTimeSlotsAvailables($services, $employees, date("Y-m-d",$checkdate), $vid))
			{
				return false;
			}
			else
			{
				return true;
			}
        }
    }

    static function returnFirstAvailableDate($checked_date,$category,$employee_id,$vid)
	{
        if(self::isAvailableDate($checked_date,$category,$employee_id,$vid))
		{
            return date("Y-m-d",$checked_date);
        }
		else
		{
            return '';
        }
    }

    /**
     * This static function is used to send Order email after saving Order
     * @param $id
     * @param $order_status
     * @param $old_status
     */
    static function sendEmailAfterSavingOrder($id,$order_status,$old_status)
    {
        global $mainframe;
		$db = JFactory::getDbo();
        $configClass = OSBHelper::loadConfig();
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		$row			= $db->loadObject();
        if($order_status == "S" && $old_status != "S")
        {
			if($row->send_email == 0)
			{
				//send notification email to user
				HelperOSappscheduleCommon::sendEmail("confirm",$id);
				HelperOSappscheduleCommon::sendSMS('confirm',$id);
			}
            HelperOSappscheduleCommon::sendEmployeeEmail('employee_notification_new',$id,0);
            OSBHelper::updateGoogleCalendar($id);
        }

        if($order_status == "C" && $old_status != "C")
        {
			HelperOSappscheduleCommon::sendCancelledEmail($id);
			HelperOSappscheduleCommon::sendSMS('cancel',$id);
			HelperOSappscheduleCommon::sendEmail('customer_cancel_order',$id);
			HelperOSappscheduleCommon::sendEmployeeEmail('employee_order_cancelled_new',$id,0);
            if($configClass['integrate_gcalendar'] == 1)
            {
                OSBHelper::removeEventOnGCalendar($id);
            }
            if($configClass['waiting_list'] == 1)
            {
                OSBHelper::sendWaitingNotification($id);
            }
        }

        if($order_status == "A" && $old_status != "A")
        {
            HelperOSappscheduleCommon::sendEmail('attended_thankyou_email',$id);
        }

        if(($order_status != "C") && ($order_status != "S") && ($order_status != $old_status))
        {
            HelperOSappscheduleCommon::sendEmail("order_status_changed_to_customer",$id);
            HelperOSappscheduleCommon::sendSMS('order_status_changed_to_customer',$id);
        }
    }

    static function getDiscountGroups($group_ids){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if(trim($group_ids) != ""){
            $groupArr = explode(",",$group_ids);
            if(count($groupArr)){
                $temp = array();
                foreach($groupArr as $group){
                    $query->clear();
                    $query->select('title')->from('#__usergroups')->where('id="'.$group.'"');
                    $db->setQuery($query);
                    $temp[] = $db->loadResult();
                }
                return implode(",",$temp);
            }
        }
        return '';
    }

    /**
     * This static function is used to check if actived employee is available in Employees list
     * @param $employees
     * @param $eid
     * @return bool
     */
    static function isExployeeExist($employees, $eid){
        foreach($employees  as $employee){
            if($employee->id == $eid){
                return true;
            }
        }
        return false;
    }

    /**
     * @param $time
     * @param string $format
     * @return string|void
     */
    static function convertToHoursMins($time, $format = '%02d:%02d') {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    /**
     * @return bool
     */
    public static function isEmptyCart()
    {
        $unique_cookie = OSBHelper::getUniqueCookie();
        $db = JFactory::getDbo();
        if($unique_cookie != "")
        {
            $db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
            $count_order = $db->loadResult();
            if ($count_order > 0)
            {
                $db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
                $order_id = $db->loadResult();
                $db->setQuery("SELECT count(id) FROM #__app_sch_temp_order_items WHERE order_id = '$order_id' order by booking_date");
                if($db->loadResult() > 0)
                {
                    return false;
                }
            }
        }
        return true;
    }


    public static function emptyCart()
    {
        $unique_cookie = OSBHelper::getUniqueCookie();
        $db = JFactory::getDbo();
        if($unique_cookie != "")
        {
            $db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
            $count_order = $db->loadResult();
            if ($count_order > 0)
            {
                $db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
                $order_id = $db->loadResult();
                $db->setQuery("Delete FROM #__app_sch_temp_order_items WHERE order_id = '$order_id'");
                $db->execute();
            }
        }
    }

    public static function checkOrderWithOneService($order_id)
    {
        $db = JFactory::getDbo();
        $db->setQuery("Select distinct `sid` from #__app_sch_order_items where order_id = '$order_id'");
        $items = $db->loadColumn(0);
        if(count($items) > 1)
        {
            return 0;
        }
        else
        {
            return $items[0];
        }
    }

	public static function cal_days_in_month($calendar, $month, $year) 
	{
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

	public static function getDialCode($dial_code)
	{
		$db = JFactory::getDbo();
		if((int)$dial_code > 0)
		{
			$db->setQuery("Select dial_code from #__app_sch_dialing_codes where id = '$dial_code'");
			$dial_code = $db->loadResult();
			$dial_code = $dial_code;
			return $dial_code;
		}
		return '';
	}
	
	public static function getDepositAmount($amount)
	{
		$configClass  = self::loadConfig();
		$deposit_type = $configClass['deposit_type'];
		if($deposit_type == 0)
		{
			return $amount*$configClass['deposit_payment']/100;
		}
		else
		{
			for($i = 1;$i<=5;$i++)
			{
				$from = (float)$configClass['from_'.$i];
				$to   = (float)$configClass['to_'.$i];
				$rate = (float)$configClass['rate_'.$i];
				if($from <= $amount && $amount <= $to && $to > 0)
				{
					return $rate;
				}
			}

			//can't find corresponding distance
			return $amount*$configClass['deposit_payment']/100;
		}
	}

	public static function orderHasOneService($order_id)
    {
        $db = JFactory::getDbo();
        $db->setQuery("Select distinct(sid) from #__app_sch_order_items where order_id = '$order_id'");
        $sids = $db->loadObjectList();
        if(count($sids) == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function getServiceIdOfOrder($order_id)
    {
        $db = JFactory::getDbo();
        $db->setQuery("Select distinct(sid) from #__app_sch_order_items where order_id = '$order_id'");
        $sid = $db->loadResult();
        $db->setQuery("Select * from #__app_sch_services where id = '$sid'");
        $service = $db->loadObject();
        return $service;
    }

	public static function colorbox()
	{
		static $loaded;

		if ($loaded === true)
		{
			return;
		}

		JHtml::_('jquery.framework');

		$rootUri  = JUri::root(true);
		$document = JFactory::getDocument();
		$document->addStyleSheet($rootUri . '/media/com_osservicesbooking/assets/js/colorbox/colorbox.min.css');
		$document->addScript($rootUri . '/media/com_osservicesbooking/assets/js/colorbox/jquery.colorbox.min.js');

		$activeLanguageTag   = JFactory::getLanguage()->getTag();
		$allowedLanguageTags = ['ar-AA', 'bg-BG', 'ca-ES', 'cs-CZ', 'da-DK', 'de-DE', 'el-GR', 'es-ES', 'et-EE',
			'fa-IR', 'fi-FI', 'fr-FR', 'he-IL', 'hr-HR', 'hu-HU', 'it-IT', 'ja-JP', 'ko-KR', 'lv-LV', 'nb-NO', 'nl-NL',
			'pl-PL', 'pt-BR', 'ro-RO', 'ru-RU', 'sk-SK', 'sr-RS', 'sv-SE', 'tr-TR', 'uk-UA', 'zh-CN', 'zh-TW',
		];

		/// English is bundled into the source therefore we don't have to load it.
		if (in_array($activeLanguageTag, $allowedLanguageTags))
		{
			$document->addScript($rootUri . '/media/com_osservicesbooking/assets/js/colorbox/i18n/jquery.colorbox-' . $activeLanguageTag . '.js');
		}

		$loaded = true;
	}

	public static function getLinkedService($sid)
    {
        $db = JFactory::getDbo();
        $db->setQuery("Select linked_service from #__app_sch_service_linked where sid = '$sid'");
        $linkedServices = $db->loadColumn(0);
        return $linkedServices;
    }

	public static function loadTooltip()
	{
		if (version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			JHtml::_('behavior.tooltip');
		}
		else
		{
			JHtml::_('bootstrap.tooltip', '.hasTooltip');
			$document = JFactory::getDocument();
			$document->addScriptDeclaration("
			document.addEventListener('DOMContentLoaded', function () {
				var tooltipOptions = {'html' : true, 'sanitize': false};      
					if (bootstrap.Tooltip) {
						var tooltipTriggerList = [].slice.call(document.querySelectorAll('.hastooltip'));
						var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
						  return new bootstrap.Tooltip(tooltipTriggerEl, tooltipOptions);
						});                                     
					}     
			});
			");
		}
	}

	public static function loadOrderDetailsSMS($orderId, $itemId, $employeeId)
    {
        global $configClass;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
		$query->select('*')->from('#__app_sch_orders')->where('id = '.$orderId);
		$db->setQuery($query);
		$order = $db->loadObject();
		$query->clear();
        $query->select('*')->from('#__app_sch_order_items')->where('order_id = '.$orderId);
        if($employeeId > 0)
        {
            $query->where('eid = '.$employeeId);
        }
        if($itemId > 0)
        {
            $query->where('id = '.$itemId);
        }
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $returnData = "";
        if(count($rows))
        {
            foreach($rows as $row)
            {
                $returnData .= JText::_('OS_SERVICE').": ".self::getServiceName($row->sid)." (" . date($configClass['time_format'], $row->start_time) . " - " . date($configClass['time_format'], $row->end_time) . " " . $row->booking_date . ")";
                //only load employee in sms to customer and administrator
                if((int) $employeeId == 0)
                {
                    $returnData .= " ".JText::_('OS_EMPLOYEE').": ".self::getEmployeeName($row->eid);
                }
				$returnData .= " ".JText::_('OS_CUSTOMER').": ".$order->order_name;
                $returnData .= "\n\r";
            }
        }
        return $returnData;
    }

    public static function getServiceName($sid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('service_name')->from('#__app_sch_services')->where('id = '.$sid);
        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function getEmployeeName($eid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('employee_name')->from('#__app_sch_employee')->where('id = '.$eid);
        $db->setQuery($query);
        return $db->loadResult();
    }

	public static function applyVenuFeature()
	{
		$configClass = self::loadConfig();
		if($configClass['apply_venue'] == 1)
		{
			$db = JFactory::getDbo();
			$db->setQuery("Select count(id) from #__app_sch_venues where published = '1'");
			$venues = $db->loadResult();
			if($venues == 1)
			{
				return true;
			}
		}
		return false;
	}

	public static function getVenueID()
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select id from #__app_sch_venues where published = '1'");
		return $db->loadResult();
	}

	public static function returnGoogleMapScript()
	{
		$config = self::loadConfig();
		if($config['google_key'] != "")
		{
			$keyScript = "&key=".$config['google_key'];
		}
		return "https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false".$keyScript;
	}

    public static function canRefundOrder($row)
    {
        if ($row
            && $row->order_upfront > 0
            && $row->order_payment
            && ($row->transaction_id != '' || ($row->transaction_id == '' && $row->order_payment == 'os_prepaid'))
            && $row->order_status == 'S'
            && $row->refunded == 0)
        {
            $method = os_payments::getPaymentMethod($row->order_payment);

            if ($method && method_exists($method, 'supportRefundPayment') && $method->supportRefundPayment())
            {
                return true;
            }
        }

        return false;
    }

	static function updateOrderCustomFieldUserProfile($orderId, $userId, $fieldMapping, $field)
	{
		global $configClass;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if($userId > 0)
		{
			if($configClass['field_integration'] == 1)
			{
				$userProfilePluginEnabled = JPluginHelper::isEnabled('user', 'profile');
				$profileFields            = ['address1', 'address2', 'city', 'region', 'country', 'postal_code', 'phone', 'website', 'favoritebook', 'aboutme', 'dob'];
				if ($userProfilePluginEnabled && in_array($fieldMapping, $profileFields))
				{
					$key = 'profile.' . $fieldMapping;

					$db->setQuery("Select profile_value from #__user_profiles where user_id = '$userId' and profile_key = '$key'");
					$fieldValue = $db->loadResult();
					$fieldValue = json_decode($fieldValue, true);

				
					$db->setQuery("Insert into #__app_sch_field_data (id, order_id, fid, fvalue) values (NULL, ".$orderId.", ".$field->id.",  '".$fieldValue."')");
					$db->execute();
				}
			}
			elseif($configClass['field_integration'] == 2)
			{
				$query->select('a.id')->from('#__jsn_users AS a')->where('a.id = ' . $userId);
				$db->setQuery($query);
				$profileId = $db->loadResult();

				// Get list of fields in #__jsn_users table
				//$mappingField = $configClass[$field.'_mapping'];
				if($mappingField != "")
				{
					$query = $db->getQuery(true);
					$query->select($fieldMapping)
						->from('#__jsn_users')
						->where('id=' . $userId);
					$db->setQuery($query);
					$fieldValue = $db->loadResult();
					if($fieldValue != "")
					{
						$db->setQuery("Insert into #__app_sch_field_data (id, order_id, fid, fvalue) values (NULL, ".$orderId.", ".$field->id.",  '".$fieldValue."')");
						$db->execute();
					}
				}
			}
		}
	}

	static function updateOrderFieldUserProfile($orderId, $userId, $fieldMapping, $field)
	{
		global $configClass;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if($userId > 0)
		{
			if($configClass['field_integration'] == 1)
			{
				$userProfilePluginEnabled = JPluginHelper::isEnabled('user', 'profile');
				$profileFields            = ['address1', 'address2', 'city', 'region', 'country', 'postal_code', 'phone', 'website', 'favoritebook', 'aboutme', 'dob'];
				if ($userProfilePluginEnabled && in_array($fieldMapping, $profileFields))
				{
					$key = 'profile.' . $fieldMapping;

					$db->setQuery("Select profile_value from #__user_profiles where user_id = '$userId' and profile_key = '$key'");
					$fieldValue = $db->loadResult();
					$fieldValue = json_decode($fieldValue, true);

					$db->setQuery("Update #__app_sch_orders set order_".$field." = '$fieldValue' where id = '$orderId'");
					$db->execute();
				}
			}
			elseif($configClass['field_integration'] == 2)
			{
				$query->select('a.id')->from('#__jsn_users AS a')->where('a.id = ' . $userId);
				$db->setQuery($query);
				$profileId = $db->loadResult();

				// Get list of fields in #__jsn_users table
				$mappingField = $configClass[$field.'_mapping'];
				if($mappingField != "")
				{
					$query = $db->getQuery(true);
					$query->select($mappingField)
						->from('#__jsn_users')
						->where('id=' . $userId);
					$db->setQuery($query);
					$fieldValue = $db->loadResult();
					$db->setQuery("Update #__app_sch_orders set order_".$field." = '$fieldValue' where id = '$orderId'");
					$db->execute();
				}
			}
		}
	}

	static function updateUserProfile($userId, $fieldMapping, $fieldValue)
	{
		global $configClass;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if($userId > 0)
		{
			if($configClass['field_integration'] == 1)
			{
				$userProfilePluginEnabled = JPluginHelper::isEnabled('user', 'profile');
				$profileFields            = ['address1', 'address2', 'city', 'region', 'country', 'postal_code', 'phone', 'website', 'favoritebook', 'aboutme', 'dob'];
				if ($userProfilePluginEnabled && in_array($fieldMapping, $profileFields))
				{
					$key = 'profile.' . $fieldMapping;
					$query->delete('#__user_profiles')
						->where('user_id = ' . $userId)
						->where('profile_key = "'.$key.'"');
					$db->setQuery($query);
					$db->execute();

					$db->setQuery("Insert into #__user_profiles ( user_id, profile_key, profile_value) values ('$userId','$key',".$db->quote(json_encode($fieldValue)).")");
					$db->execute();
				}
			}
			elseif($configClass['field_integration'] == 2)
			{
				$query->select('a.id')->from('#__jsn_users AS a')->where('a.id = ' . $userId);
				$db->setQuery($query);
				$profileId = $db->loadResult();

				// Get list of fields in #__jsn_users table
				$fieldList = array_keys($db->getTableColumns('#__jsn_users'));
				if ($fieldMapping && in_array($fieldMapping, $fieldList) && isset($fieldValue))
				{
					if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
					{
						$fieldValue = implode('|*|', json_decode($fieldValue));
					}
					if ($profileId)
					{
						// Update User
						$query = "Update #__jsn_users set `".$fieldMapping."` = ".$db->quote($fieldValue)." where id = '$userId'";
						$db->setQuery($query);
						$db->execute();
					}
					else
					{
						// New User
						$query = "INSERT INTO #__jsn_users(id," .$fieldMapping . ") VALUES(" . $userId . ", " . $db->quote($fieldValue) . ")";
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
	}

	public static function getChoicesJsSelect($html, $hint = '')
	{
		static $isJoomla4;

		if ($isJoomla4 === null)
		{
			$isJoomla4 = self::isJoomla4();
		}

		if ($isJoomla4)
		{
			Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
			Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

			Factory::getApplication()->getDocument()->getWebAssetManager()
				->usePreset('choicesjs')
				->useScript('webcomponent.field-fancy-select');

			$attributes = [];

			$hint = $hint ?: Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS');

			$attributes[] = 'placeholder="' . $hint . '""';
			$attributes[] = 'search-placeholder="' . $hint . '""';


			return '<joomla-field-fancy-select ' . implode(' ', $attributes) . '>' . $html . '</joomla-field-fancy-select>';
		}

		return $html;
	}

	/**
	 * Helper method to write data to a log file, for debuging purpose
	 *
	 * @param   string  $logFile
	 * @param   array   $data
	 * @param   string  $message
	 */
	public static function logData($logFile, $data = [], $message = null)
	{
		$text = '[' . gmdate('m/d/Y g:i A') . '] - ';

		foreach ($data as $key => $value)
		{
			$text .= "$key=$value, ";
		}

		$text .= $message;

		$fp = fopen($logFile, 'a');
		fwrite($fp, $text . "\n\n");
		fclose($fp);
	}

	/**
	 * Method to request user login before they can access to this page
	 *
	 * @param   string  $msg  The redirect message
	 *
	 * @throws Exception
	 */
	static function requestLogin($msg = 'OS_PLEASE_LOGIN')
	{
		if (Factory::getUser()->get('id'))
		{
			return;
		}

		$app    = Factory::getApplication();
		$active = $app->getMenu()->getActive();

		$option = isset($active->query['option']) ? $active->query['option'] : '';
		$view   = isset($active->query['view']) ? $active->query['view'] : '';

		if ($option == 'com_osservicesbooking' && $view == strtolower($this->getName()))
		{
			$returnUrl = 'index.php?Itemid=' . $active->id;
		}
		else
		{
			$returnUrl = Uri::getInstance()->toString();
		}

		$url = Route::_('index.php?option=com_users&view=login&return=' . base64_encode($returnUrl), false);

		$app->enqueueMessage(Text::_($msg));
		$app->redirect($url);
	}

	public static function banned()
	{
		global $configClass;
		if($configClass['banned_ipaddress'] != "")
		{
			$bannedIpAddress = $configClass['banned_ipaddress'];
			$bannedIpAddress = explode("\r\n",$bannedIpAddress);
			if(count($bannedIpAddress) > 0)
			{
				$myIPaddress = self::get_ip_address();
				if(in_array($myIPaddress, $bannedIpAddress))
				{
					throw new Exception(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
				}
			}
		}
		$user = JFactory::getUser();
		if($configClass['banned_users'] != "" && $user->id > 0)
		{
			$bannedUsers	= $configClass['banned_users'];
			$bannedUsers	= explode("\r\n",$bannedUsers);
			if(count($bannedUsers) > 0)
			{
				if(in_array($user->id, $bannedIpAddress) || in_array($user->username, $bannedIpAddress))
				{
					throw new Exception(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
				}
			}
		}
	}

	/**
     * Get IP address of customers
     *
     * @return unknown
     */
    public static function get_ip_address()
    {
        foreach (array(
                     'HTTP_CLIENT_IP',
                     'HTTP_X_FORWARDED_FOR',
                     'HTTP_X_FORWARDED',
                     'HTTP_X_CLUSTER_CLIENT_IP',
                     'HTTP_FORWARDED_FOR',
                     'HTTP_FORWARDED',
                     'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

	public static function allowRemainPayment($orderId)
	{
		global $configClass;
		$db			= JFactory::getDbo();
		$user		= JFactory::getUser();
		if((int)$orderId == 0)
		{
			return false;
		}
		$db->setQuery("Select * from #__app_sch_orders where id = '$orderId'");
		$order		= $db->loadObject();
		

		if($user->id == 0)
		{
			return false;
		}
		elseif($user->id != $order->user_id)
		{
			return false;
		}
		else
		{
			if($configClass['disable_payments'] == 0)
			{
				return false;
			}
			else
			{
				if($order->order_payment == "os_offline" && $order->make_remain_payment == 0)
				{
					return true;
				}
				else
				{
					if($order->order_status == "S" && $order->make_remain_payment == 0 && $order->order_upfront < $order->order_final_cost)
					{
						return true;
					}
				}
				return false;
			}
		}
	}

	public static function generateNotifyUrl($orderId, $isRemain, $paymentMethod)
	{
		$siteUrl = JURI::root();
		if($isRemain == 1)
		{
			return $siteUrl . "index.php?option=com_osservicesbooking&task=defaul_paymentconfirm&remainPayment=1&id=".$orderId."&payment_method=".$paymentMethod;
		}
		else
		{
			return $siteUrl . "index.php?option=com_osservicesbooking&task=defaul_paymentconfirm&id=".$orderId."&payment_method=".$paymentMethod;
		}
	}

	public static function generatePaymentCancelUrl($orderId, $isRemain, $Itemid)
	{
		$siteUrl = JURI::root();
		if($isRemain == 1)
		{
			return $siteUrl . "index.php?option=com_osservicesbooking&task=default_paymentcancel&remainPayment=1&id=".$orderId."&Itemid=".$Itemid;
		}
		else
		{
			return $siteUrl . "index.php?option=com_osservicesbooking&task=default_paymentcancel&id=".$orderId."&Itemid=".$Itemid;
		}
	}

	public static function generatePaymentReturnUrl($orderId, $isRemain, $Itemid)
	{
		$siteUrl = JURI::root();
		if($isRemain == 1)
		{
			return $siteUrl . "index.php?option=com_osservicesbooking&task=default_paymentreturn&remainPayment=1&id=".$orderId."&Itemid=".$Itemid;
		}
		else
		{
			return $siteUrl . "index.php?option=com_osservicesbooking&task=default_paymentreturn&id=".$orderId."&Itemid=".$Itemid;
		}
	}

	public static function isHavingCommercialFields()
	{
		global $configClass;
		$db = JFactory::getDbo();
		if($configClass['disable_payments'] == 1)
		{
			$query = "Select count(a.id) from #__app_sch_field_options as a inner join #__app_sch_fields as b on a.field_id = b.id where b.published = '1' and a.additional_price > 0 and b.field_area = '1'";
			$db->setQuery($query);
			$count = $db->loadResult();
			if($count > 0)
			{
				return true;
			}
		}
		return false;
	}

	public static function checkLimitBooking($email, $limit_by, $limit_booking, $db)
	{
		$config = JFactory::getConfig();
		$date	= JFactory::getDate('now', $config->get('offset'));
		switch($limit_by)
		{
			case "0":
				$date->setTime(0, 0, 0);
				$date->setTimezone(new DateTimeZone('UCT'));
				$fromDate = $date->toSql(true);
				$date     = JFactory::getDate('now', $config->get('offset'));
				$date->setTime(23, 59, 59);
				$date->setTimezone(new DateTimeZone('UCT'));
				$toDate = $date->toSql(true);
			break;
			case "1":
                $monday = clone $date->modify( 'Monday this week');
                $monday->setTime(0, 0, 0);
                $monday->setTimezone(new DateTimeZone('UCT'));
                $fromDate = $monday->toSql(true);
                $sunday   = clone $date->modify('Sunday this week');
                $sunday->setTime(23, 59, 59);
                $sunday->setTimezone(new DateTimeZone('UCT'));
                $toDate = $sunday->toSql(true);
			break;
			case "2":
				$date->setDate($date->year, $date->month, 1);
                $date->setTime(0, 0, 0);
                $date->setTimezone(new DateTimeZone('UCT'));
                $fromDate = $date->toSql(true);
                $date     = JFactory::getDate('now', $config->get('offset'));
                $date->setDate($date->year, $date->month, $date->daysinmonth);
                $date->setTime(23, 59, 59);
                $date->setTimezone(new DateTimeZone('UCT'));
                $toDate = $date->toSql(true);
			break;
		}
		$query = $db->getQuery(true);
		$query->select('count(id)')->from('#__app_sch_orders')->where('order_email='.$db->quote($email))->where('order_status in ("P","S","A")')->where('order_date >= ' . $db->quote($fromDate))->where('order_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count >= $limit_booking)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public static function checkLimitBookingOrderItems($email, $limit_by, $limit_booking, $db, $order, $unique_cookie)
	{
		$config = JFactory::getConfig();
		$bdate  = $order->booking_date;
		$date	= JFactory::getDate($bdate, $config->get('offset'));
		
		switch($limit_by)
		{
			case "0":
				$date->setTime(0, 0, 0);
				$date->setTimezone(new DateTimeZone('UCT'));
				$fromDate = $date->toSql(true);
				$date     = JFactory::getDate($bdate, $config->get('offset'));
				$date->setTime(23, 59, 59);
				$date->setTimezone(new DateTimeZone('UCT'));
				$toDate = $date->toSql(true);
			break;
			case "1":
                $monday = clone $date->modify( 'Monday this week');
                $monday->setTime(0, 0, 0);
                $monday->setTimezone(new DateTimeZone('UCT'));
                $fromDate = $monday->toSql(true);
                $sunday   = clone $date->modify('Sunday this week');
                $sunday->setTime(23, 59, 59);
                $sunday->setTimezone(new DateTimeZone('UCT'));
                $toDate = $sunday->toSql(true);
			break;
			case "2":
				$date->setDate($date->year, $date->month, 1);
                $date->setTime(0, 0, 0);
                $date->setTimezone(new DateTimeZone('UCT'));
                $fromDate = $date->toSql(true);
                $date     = JFactory::getDate($bdate, $config->get('offset'));
                $date->setDate($date->year, $date->month, $date->daysinmonth);
                $date->setTime(23, 59, 59);
                $date->setTimezone(new DateTimeZone('UCT'));
                $toDate = $date->toSql(true);
			break;
		}

		$query = "Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id where b.order_email=".$db->quote($email)." and order_status in ('P','S','A') and a.booking_date >= ".$db->quote($fromDate) ." and booking_date <= ".$db->quote($toDate);

		$db->setQuery($query);

		$count = $db->loadResult();

		$query = "Select count(a.id) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on a.order_id = b.id where b.unique_cookie=".$db->quote($unique_cookie)." and a.booking_date >= ".$db->quote($fromDate) ." and booking_date <= ".$db->quote($toDate);

		$db->setQuery($query);
		$count1 = $db->loadResult();

		if($count + $count1 > $limit_booking)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public static function countAppointments($name, $email)
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id where b.order_name like '$name' and b.order_email like '$email' and b.order_status = 'S'");
		return (int) $db->loadResult();
	}

	public static function loadWeekDays($arr)
	{
		$tmp = [];
		foreach($arr as $day)
		{
			switch ($day)
			{
				case "1":
					$tmp[] = JText::_('OS_MON');
				break;
				case "2":
					$tmp[] = JText::_('OS_TUE');
				break;
				case "3":
					$tmp[] = JText::_('OS_WED');
				break;
				case "4":
					$tmp[] = JText::_('OS_THU');
				break;
				case "5":
					$tmp[] = JText::_('OS_FRI');
				break;
				case "6":
					$tmp[] = JText::_('OS_SAT');
				break;
				case "7":
					$tmp[] = JText::_('OS_SUN');
				break;
			}
		}
		return $tmp;
	}

	public static function generateTip($tipContent)
	{
		?>
			<span class="hasTooltip hasTip" title="<?php echo $tipContent;?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
				  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
				</svg>
			</span>
		<?php
	}

	public static function isAvailableVenue()
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select count(id) from #__app_sch_venues where published = '1'");
		$countVenue = $db->loadResult();
		if($countVenue > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function showAdditionalPart($orderId)
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select count(id) from #__app_sch_fields where published = '1' and field_area = '0'");
		$count = $db->loadResult();
		if($count > 0)
		{
			return true;
		}
		else
		{
			$query = "Select count(a.id) from #__app_sch_order_items as a left join #__app_sch_services as b on b.id = a.sid where a.order_id = '$orderId' and b.service_time_type = '1'";
			$db->setQuery($query);
			$count = $db->loadResult();
			if($count > 0)
			{
				return true;
			}
		}
		return false;
	}

	public static function loadChartJS()
	{
		?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
		<?php
	}

	public static function isEmployee()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$db->setQuery("Select count(id) from #__app_sch_employee where user_id = '$user->id' and published = '1'");
		$count = $db->loadResult();
		if($count > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function logMail($email_type, $orderID = 0, $email, $subject, $body)
	{
		$db				= JFactory::getDbo();
		require_once JPATH_ROOT . '/administrator/components/com_osservicesbooking/tables/log.php';
		$row			= &JTable::getInstance('Log','OsAppTable');
		$row->id		= 0;
		$row->email_key = $email_type;
		$row->order_id	= $orderID;
		$row->received_email_address = $email;
		$row->subject   = $subject;
		$row->body		= $body;
		$row->sent_on   = JFactory::getDate()->format("Y-m-d H:i:s");
		$row->store();
	}

	public static function isJoomla4()
	{
		return version_compare(JVERSION, '4.0.0-dev', 'ge');
	}

}
?>