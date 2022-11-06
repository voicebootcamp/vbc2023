<?php
/* ======================================================
 # Limit Active Logins for Joomla! - v1.1.2 (pro version)
 # -------------------------------------------------------
 # For Joomla! CMS (v4.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2022 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://limitactivelogins.web357.com/
 # Support: support@web357.com
 # Last modified: Thursday 31 March 2022, 12:05:22 PM
 ========================================================= */

defined('_JEXEC') or die;

// Debugging
JLog::addLogger(array('text_file' => 'com_limitactivelogins_frontend.log.php'), JLog::ALL, array('com_limitactivelogins_frontend'));

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Limitactivelogins', JPATH_COMPONENT);
JLoader::register('LimitactiveloginsController', JPATH_COMPONENT . '/controller.php');
JLoader::register('LimitactiveloginsHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'limitactivelogins.php');

// CSS
JFactory::getDocument()->addStyleSheet(JURI::root(true).'/media/com_limitactivelogins/css/uikit.limitactivelogins.theme.min.css?v=20220331120522');

// JavaScript
JHtml::_('jquery.framework', false);
JFactory::getDocument()->addScript(JURI::root(true).'/media/com_limitactivelogins/js/uikit.min.js?v=20220331120522');
JFactory::getDocument()->addScript(JURI::root(true).'/media/com_limitactivelogins/js/uikit-icons.min.js?v=20220331120522');

// Mobile Detect
require_once JPATH_SITE . '/components/com_limitactivelogins/helpers/Mobile_Detect.php';

// Execute the task.
$controller = BaseController::getInstance('Limitactivelogins');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();