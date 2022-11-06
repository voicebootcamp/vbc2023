<?php
/*------------------------------------------------------------------------
# mod_osbsearch.php - OSB Search
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2014 joomdonation.com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
require_once JPATH_ROOT.'/administrator/components/com_osservicesbooking/helpers/helper.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/bootstrap.php';
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true).'/modules/mod_osbsearch/asset/style.css');
$document->addScript(JUri::root(true).'/media/com_osservicesbooking/assets/js/ajax.js');
$jinput      = JFactory::getApplication()->input;
$category_id = $jinput->getInt('category_id',0);
$vid		 = $jinput->getInt('vid',0);
$sid		 = $jinput->getInt('sid',0);
$employee_id = $jinput->getInt('employee_id',0);

$db = Jfactory::getDBO();

$language = JFactory::getLanguage();
$tag = $language->getTag();
if($tag == ""){
	$tag = "en-GB";
}
$language->load('com_osservicesbooking', JPATH_SITE, $tag, true);

OSBHelper::loadMedia();
if (version_compare(JVERSION, '3.0', 'le'))
{
	OSBHelper::loadBootstrap();
}
else
{
	if($configClass['load_bootstrap'] == 1)
	{
		OSBHelper::loadBootstrap();
	}
}
global $mapClass;
OSBHelper::generateBoostrapVariables();
OSBHelper::generateMapClassNames();

$moduleclass_sfx        = $params->get('moduleclass_sfx','');
$show_venue             = $params->get('show_venue',1);
$show_category          = $params->get('show_category',1);
$show_employee          = $params->get('show_employee',1);
$show_service           = $params->get('show_service',1);
$show_date              = $params->get('show_date',1);
$itemid					= $params->get('itemid',0);
$layout					= $params->get('layout',0);
$optionArr   = array();
$optionArr[] = JHtml::_('select.option','',JText::_('OS_SELECT_SERVICE'));

$db->setQuery("Select id as value, service_name as text from #__app_sch_services where published = '1' order by ordering");
$services = $db->loadObjectList();

$serviceArr = array();
$serviceArr = array_merge($optionArr,$services);
$lists['service'] = JHtml::_('select.genericlist',$serviceArr,'sid','class="input-large form-select form-control" onChange="javascript:updateSearchForm('.$layout.');"','value','text',$sid);

$optionArr   = array();
$optionArr[] = JHtml::_('select.option','',JText::_('OS_SELECT_CATEGORY'));

$db->setQuery("Select id as value, category_name as text from #__app_sch_categories where published = '1'  order by category_name");
$categories = $db->loadObjectList();

$categoryArr = array();
$categoryArr = array_merge($optionArr,$categories);
$lists['category'] = JHtml::_('select.genericlist',$categoryArr,'category_id','class="input-large form-select form-control" onChange="javascript:updateSearchForm('.$layout.');"','value','text',$category_id);

$optionArr   = array();
$optionArr[] = JHtml::_('select.option','',JText::_('OS_SELECT_VENUE'));

$db->setQuery("Select id as value, concat(venue_name, ' - ' , address) as text from #__app_sch_venues where published = '1'  order by address");
$venues = $db->loadObjectList();

$venueArr = array();
$venueArr = array_merge($optionArr,$venues);
$lists['venue'] = JHtml::_('select.genericlist',$venueArr,'vid','class="input-large form-select form-control" onChange="javascript:updateSearchForm('.$layout.');"','value','text',$vid);

$optionArr   = array();
$optionArr[] = JHtml::_('select.option','',JText::_('OS_SELECT_EMPLOYEE'));

$db->setQuery("Select id as value, employee_name as text from #__app_sch_employee where published = '1' order by employee_name");
$employees = $db->loadObjectList();

$employeeArr = array();
$employeeArr = array_merge($optionArr,$employees);
$lists['employee'] = JHtml::_('select.genericlist',$employeeArr,'employee_id','class="input-large form-select form-control" onChange="javascript:updateSearchForm('.$layout.');"','value','text',$employee_id);
if($layout == 0)
{
	$layout = "default";
}
else
{
	$layout = "horizontal";
}
require( JModuleHelper::getLayoutPath( 'mod_osbsearch', $layout ) );
?>