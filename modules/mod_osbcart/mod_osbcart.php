<?php
/*------------------------------------------------------------------------
# mod_osbcart.php - OSB Search
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
require_once JPATH_ROOT.'/administrator/components/com_osservicesbooking/helpers/helper.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/common.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/bootstrap.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/classes/ajax.php';

global $jinput, $mapClass;
OSBHelper::generateBoostrapVariables();
$jinput                 = JFactory::getApplication()->input;
$document               = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true).'/media/com_osservicesbooking/assets/css/style.css');
$document->addScript(JUri::root(true).'/media/com_osservicesbooking/assets/js/ajax.js');
$document->addScript(JUri::root(true).'/media/com_osservicesbooking/assets/js/javascript.js');
$db                     = JFactory::getDbo();
$config                 = new JConfig();
$offset                 = $config->offset;
date_default_timezone_set($offset);
$unique_cookie		    = OSBHelper::getUniqueCookie();//$_COOKIE['unique_cookie'];
$lang                   = JFactory::getLanguage();
$lang->load('com_osservicesbooking', JPATH_SITE, $lang->getTag(), true);
$task                   = $jinput->get('task','','string');
$user                   = JFactory::getUser();
$configClass            = OSBHelper::loadConfig();
require( JModuleHelper::getLayoutPath( 'mod_osbcart' ) );
?>