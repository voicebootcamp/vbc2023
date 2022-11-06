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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Router\Route;

/**
 * View class for a list of Limitactivelogins.
 *
 * @since  1.6
 */
class LimitactiveloginsViewAction extends \Joomla\CMS\MVC\View\HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		// App
		$app = Factory::getApplication();
		$user = Factory::getUser();

		// Get model
		$model = $this->getModel();

		// Get user id
		$this->user_id = $model->getState('uid');

		// get current session
		$this->session = Factory::getSession();

		// Get user devices
		$this->logged_in_devices = $model->getUserDevices($this->user_id);
		$this->total = count($this->logged_in_devices);
		$this->user = Factory::getUser();
		$this->session = Factory::getSession();
		$this->detect = new Mobile_Detect;
		
		// Password reset menu link
		$menu = Factory::getApplication()->getMenu()->getItems( 'link', 'index.php?option=com_users&view=reset', true );
		$this->password_reset_link = Route::_('index.php?option=com_users&view=reset&Itemid='.(isset($menu->id) ? $menu->id : 1));

		// Component params
		$params = $app->getParams('com_limitactivelogins');

		// max active logins
		$new_user = Factory::getUser($this->user_id);
		$max_active_logins = LimitactiveloginsHelper::getMaxActiveLogins($new_user);
	
		$custom_error_message = $params->get('custom_error_message', Text::_('COM_LIMITACTIVELOGINS_CUSTOM_ERROR_MESSAGE_DEFAULT'));
		$this->custom_error_message = Text::sprintf($custom_error_message, $max_active_logins);
		$this->show_logged_in_devices = $params->get('show_logged_in_devices', 1);

		// If there are not logged in devices and the user came here by mistake, redirect him to the login page
		if ($this->total === 0)
		{
			if (!$user->id)
			{
				$com_url = 'index.php?option=com_limitactivelogins&view=logs';
				$redirectUrl = urlencode(base64_encode($com_url));  
				$redirectUrl = '&return='.$redirectUrl;
				$joomlaLoginUrl = 'index.php?option=com_users&view=login';
				$finalUrl = $joomlaLoginUrl . $redirectUrl;
				$msg = JText::_("You can now sign in.");
				$app->redirect(JRoute::_($finalUrl, false, 2), $msg);
			}
		}

		parent::display($tpl);
	}

}
