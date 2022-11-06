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

use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

// plugin helper
jimport('joomla.plugin.helper');

// Check if Web357 Framework plugin exists and is enabled
if(!JPluginHelper::isEnabled('system', 'web357framework'))
{
	$msg = JText::_('COM_LIMITACTIVELOGINS_WEB357FRAMEWORK_PLUGIN_REQUIRED');
	JFactory::getApplication()->enqueueMessage($msg, 'error');
	return false;
}

// Check the Joomla! version. The plugin is not working for Joomla! 2.5
if (version_compare(JVERSION, '3.0', 'lt'))
{
	$msg = JText::_('COM_LIMITACTIVELOGINS_J25_CHECKER');
	JFactory::getApplication()->enqueueMessage($msg, 'error');
	return false;
}

// Load Web357Framework's language
Web357Framework\Functions::loadWeb357FrameworkLanguage();

// Check if the plugin exists
if(!JPluginHelper::isEnabled('user', 'limitactivelogins'))
{
	// build url
	$w357_task_url = JUri::getInstance(JRoute::_(JURI::getInstance()->toString())); // current url
	$w357_task_url->setVar('w357_task', 'activate_plugin');

	// activate the plugin
	$w357_task = JFactory::getApplication()->input->get('w357_task', '', 'STRING');
	if ($w357_task === 'activate_plugin')
	{
		// Activate Plugin
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Fields to update.
		$fields = array(
			$db->quoteName('enabled') . ' = 1'
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('type') . ' = ' . $db->quote('plugin'),
			$db->quoteName('element') . ' = ' . $db->quote('limitactivelogins'),
			$db->quoteName('folder') . ' = ' . $db->quote('user')
		);

		$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
			return false;
		}

		// remove the var (w357_task) from the url and redirect to the previous page
		$uri = JUri::getInstance(JRoute::_(JURI::getInstance()->toString()));
		$uri->delVar('w357_task');
		JFactory::getApplication()->redirect($uri);
	}

	// message
	$msg = JText::sprintf(JText::_('COM_LIMITACTIVELOGINS_PLG_USER_LIMITACTIVELOGINS_PLUGIN_REQUIRED'), $w357_task_url);
	JFactory::getApplication()->enqueueMessage($msg, 'error');
	return false;
}

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_limitactivelogins'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Debugging
JLog::addLogger(array('text_file' => 'com_limitactivelogins_backend.log.php'), JLog::ALL, array('com_limitactivelogins_backend'));

// CSS
JFactory::getDocument()->addStyleSheet(JURI::root(true).'/media/com_limitactivelogins/css/backend-limitactivelogins.css?v=20220331120522');

// JavaScript
JHtml::_('jquery.framework', false);
JFactory::getDocument()->addScript(JURI::root(true).'/media/com_limitactivelogins/js/backend-limitactivelogins.js?v=20220331120522');

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Limitactivelogins', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('LimitactiveloginsHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'limitactivelogins.php');

$controller = BaseController::getInstance('Limitactivelogins');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
