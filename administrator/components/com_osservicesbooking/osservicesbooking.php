<?php

/*------------------------------------------------------------------------
# osservicesbooking.php - OS Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

error_reporting(E_ERROR | E_PARSE);
//error_reporting(E_ALL);
define("DS",DIRECTORY_SEPARATOR);
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
jimport('joomla.filesystem.folder');
//Include files from classes folder
$dir = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR."/classes",'.php');
if(count($dir) > 0)
{
	for($i=0;$i<count($dir);$i++)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/classes/".$dir[$i]);
	}
}
//Include files from classes folder
$dir = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR."/helpers",'.php');
if(count($dir) > 0)
{
	for($i=0;$i<count($dir);$i++)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/helpers/".$dir[$i]);
	}
}
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/payment/omnipay.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/payment/payment.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/plugins/os_payment.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/plugins/os_payments.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/downloadInvoice.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/bootstrap.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/ics.php';
require_once(JPATH_COMPONENT_ADMINISTRATOR."/elements/osmcurrency.php");
require_once(JPATH_ROOT."/components/com_osservicesbooking/helpers/calendar.php");

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root()."administrator/components/com_osservicesbooking/asset/css/style.css");
if(OSBHelper::isJoomla4())
{
	$document->addStyleSheet(JURI::root()."media/com_osservicesbooking/assets/css/style4.css");
}
$document->addScript(JURI::root()."administrator/components/com_osservicesbooking/asset/javascript/javascript.js");
$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/javascript.js");
$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/ajax.js");

global $_jversion,$configs,$configClass,$symbol,$mainframe,$languages,$jinput, $mapClass, $bootstrapHelper;
OSBHelper::generateBoostrapVariables();
OSBHelper::generateMapClassNames();
$jinput = JFactory::getApplication()->input;
$languages = OSBHelper::getLanguages();
$mainframe = JFactory::getApplication();
$db = JFactory::getDBO();
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
global $mainframe,$mapClass;
$mainframe = JFactory::getApplication();

OSBHelper::generateBoostrapVariables();
OSBHelper::generateMapClassNames();
JHtml::_('jquery.framework');
/**
 * Multiple languages processing
 */
if (JLanguageMultilang::isEnabled() && !OSBHelper::isSyncronized()){
	OSBHelper::setupMultilingual();
}
OSBHelper::cleanData();

$option = $jinput->get('option','com_osservicesbooking','string');
$task = $jinput->get('task','cpanel_list','string');
if($task != ""){
	$taskArr = explode("_",$task);
	$maintask = $taskArr[0];
}else{
	//cpanel
	$maintask = "";
}

OSappscheduleCpanel::zendChecking();

if($maintask != "ajax" && $maintask != "configuration")
{
	$blacktaskarry = array('fields_addOption','fields_removeFieldOption','fields_editOption','service_addcustomprice','service_removecustomprice','employee_addcustombreaktime','employee_removecustombreaktime','employee_removeRestday','ajax_removetemptimeslot','ajax_removerestdayAjax','ajax_addrestdayAjax','ajax_removeOrderItemAjax','orders_updateNewOrderStatus','fields_changeDefaultOptionAjax','employee_removeBusytime','orders_exportreport','orders_changeCheckinstatus','orders_editservice','orders_addservice','orders_detail','service_edit','fields_edit','employee_edit','venue_edit','category_edit','plugin_edit','coupon_edit','emails_edit','worktimecustom_edit','cpanel_revenue','orders_customerdetails','service_addrate','service_modifyrate','service_updateWorkingStatus','service_removeWorking','service_saveWorking','configuration_list','analytics_serviceRevenue','service_apply','log_details','calendar_loadCalendatDetails','calendar_loadWeekyCalendar');
	$from = $jinput->get('from','','string');
    $tmpl = $jinput->get('tmpl','','string');
	$fromarray = [];
	if(!in_array($task,$blacktaskarry) && !in_array($from,$fromarray)) 
	{
		OSBHelper::renderSubmenu($task);	
	}
}


switch ($maintask)
{
	case "analytics":
		OSappscheduleAnalytics::display($option,$task);
	break;
	default:
	case "cpanel":
		if (JFactory::getUser()->authorise('core.manage', 'com_osservicesbooking')) {
			OSappscheduleCpanel::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
    case "review":
        OSappscheduleReview::display($option,$task);
    break;
    case "balance":
		if (JFactory::getUser()->authorise('userbalance', 'com_osservicesbooking')) {
			OSappscheduleBalance::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
    break;
	case "employee":
		if (JFactory::getUser()->authorise('employees', 'com_osservicesbooking')) {
			OSappscheduleEmployee::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case "worktime":
		if (JFactory::getUser()->authorise('workingtime', 'com_osservicesbooking')) {
			OSappscheduleWorktime::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case "worktimecustom":
		if (JFactory::getUser()->authorise('customworkingtime', 'com_osservicesbooking')) {
			OSappscheduleWorktimecustom::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case 'configuration':
		if (!JFactory::getUser()->authorise('configuration', 'com_osservicesbooking')) {
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		OSappscheduleConfiguration::display($option,$task);
	break;	
	case 'orders':
		if (JFactory::getUser()->authorise('orders', 'com_osservicesbooking')) {
			OSappscheduleOrders::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case 'fields':
		if (JFactory::getUser()->authorise('customfields', 'com_osservicesbooking')) {
			OsAppscheduleFields::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case 'emails':
		if (JFactory::getUser()->authorise('emails', 'com_osservicesbooking')) {
			OsAppscheduleEmails::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case 'translation':
		if (JFactory::getUser()->authorise('translation', 'com_osservicesbooking')) {
			OsAppscheduleTranslation::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case 'category':
		if (JFactory::getUser()->authorise('categories', 'com_osservicesbooking')) {
			OSappscheduleCategory::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case 'plugin':
		if (JFactory::getUser()->authorise('payment', 'com_osservicesbooking')) {
			OSappschedulePlugin::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case 'venue':
		if (JFactory::getUser()->authorise('venues', 'com_osservicesbooking')) {
			OsAppscheduleVenue::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
	case 'coupon':
		if (JFactory::getUser()->authorise('coupons', 'com_osservicesbooking')) {
			OSappscheduleCoupon::display($option,$task);
		}else{
			return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
	break;
    case 'waiting':
        if (JFactory::getUser()->authorise('core.manage', 'com_osservicesbooking')) {
            OSappscheduleWaiting::display($option,$task);
        }else{
            return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
        }
        break;
    case 'calendar':
        OsAppscheduleCalendar::display($option,$task);
        break;
	case 'install':
	case 'service':
		OSappscheduleService::display($option,$task);
	break;
	case 'log':
		OSappscheduleLogs::display($option,$task);
	break;
}
if (version_compare(JVERSION, '3.0', 'le')){
	OSBHelper::loadBootstrap();
}else{
	OSBHelper::loadBootstrapStylesheet();
}

OSBHelper::displayCopyright();
?>