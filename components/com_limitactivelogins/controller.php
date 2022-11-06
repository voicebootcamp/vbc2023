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

use Joomla\CMS\Router\Route;

jimport('joomla.application.component.controller');

use \Joomla\CMS\Factory;

/**
 * Class LimitactiveloginsController
 *
 * @since  1.6
 */
class LimitactiveloginsController extends \Joomla\CMS\MVC\Controller\BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   mixed   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
     * @throws Exception
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$app  = Factory::getApplication();
		$view = $app->input->getCmd('view', '');

		if ($view == 'action')
		{
			// Check the request token.
			$this->checkToken('request');

			parent::display($cachable, $urlparams);

			return $this;
		}

		// login first and then redirect
		$user = Factory::getUser();
		if (!$user->id)
		{
			$com_url = 'index.php?option=com_limitactivelogins&view=logs';
			$redirectUrl = urlencode(base64_encode($com_url));  
			$redirectUrl = '&return='.$redirectUrl;
			$joomlaLoginUrl = 'index.php?option=com_users&view=login';
			$finalUrl = $joomlaLoginUrl . $redirectUrl;
			$msg = JText::_("Please, sign in.");

			$app->redirect(Route::_($finalUrl));
		}
		else
		{
			$view = $app->input->getCmd('view', 'logs');
			$app->input->set('view', $view);

			parent::display($cachable, $urlparams);

			return $this;
		}
	}
}
