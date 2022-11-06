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

use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\IpHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.controllerform');

/**
 * Log controller class.
 *
 * @since  1.6
 */
class LimitactiveloginsControllerLog extends \Joomla\CMS\MVC\Controller\FormController
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'logs';
		parent::__construct();
	}

	/**
	 * Delete the User's session (logout)
	 *
	 * @return void
	 */
	public function deleteSessionAndLogoutTheUser() 
	{
		$this->checkToken('request');

		$db = JFactory::getDbo();
		$app = Factory::getApplication();
		$data = (object) $app->input->post->get('jform', [], 'ARRAY');
		
		if (empty($data->session_id) || empty($data->userid) || empty($data->user_agent) || empty($data->ip_address))
		{
			throw new Exception('Error. You cannot sign out from this device.');
		}

		// Get current session
		$session = Factory::getSession();
		$current_session = $session->getId();

		// Get the session IDs by userid, user_agent, and ip_address
		$session_ids = [];
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('a.session_id'));
		$query->from($db->quoteName('#__limitactivelogins_logs', 'a'));
		$query->join('LEFT', '#__session AS b ON b.session_id = a.session_id AND b.userid = a.userid');
		$query->where($db->quoteName('a.userid') . ' = '. (int) $data->userid);
		$query->where($db->quoteName('b.userid') . ' = '. (int) $data->userid);
		$query->where($db->quoteName('a.user_agent') . ' = '.  $db->quote($data->user_agent));
		$query->where($db->quoteName('a.ip_address') . ' = '.  $db->quote($data->ip_address));

		try
		{
			$db->setQuery($query);
			$session_ids = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, $e->getMessage());
			return false;
		}
		
		// Delete the rows that match the above sessions (in both tables)
		foreach ($session_ids as $session_id)
		{
			// Delete from #__limitactivelogins_logs
			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('session_id') . ' = ' . $db->quote($session_id)
			);
			$query->delete($db->quoteName('#__limitactivelogins_logs'));
			$query->where($conditions);
			$db->setQuery($query);
			$db->execute();

			// Delete from #__session
			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('session_id') . ' = ' . $db->quote($session_id)
			);
			$query->delete($db->quoteName('#__session'));
			$query->where($conditions);
			$db->setQuery($query);
			$db->execute();
		}

		// Redirect back
		$app->enqueueMessage(Text::_('The session has been deleted successfully.'));
		$app->redirect('index.php?option=com_limitactivelogins&view=logs');
	}

}
