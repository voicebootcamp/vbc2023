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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\IpHelper;
use \Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

/**
 * Joomla User plugin
 *
 * @since  1.5
 */
class plgUserLimitActiveLogins extends JPlugin
{
	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  3.2
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  3.2
	 */
	protected $db;

	/**
	 * This method should handle any login logic and report back to the subject - Basis is J3.4.2
	 *
	 * @param	array	$user		Holds the user data
	 * @param	array	$options	Array holding options (remember, autoregister, group)
	 *
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function onUserLogin($user, $options = array())
	{
		JLog::addLogger(array('text_file' => 'plg_user_limitactivelogins.log.php'), JLog::ALL, array('plg_user_limitactivelogins'));

		$instance = $this->_getUser($user, $options);

		// If _getUser returned an error, then pass it back.
		if ($instance instanceof Exception)
		{
			return false;
		}

		// If the user is blocked, redirect with an error
		if ($instance->block == 1)
		{
			$this->app->enqueueMessage(Text::_('JERROR_NOLOGIN_BLOCKED'), 'warning');

			return false;
		}

		// Authorise the user based on the group information
		if (!isset($options['group']))
		{
			$options['group'] = 'USERS';
		}

		// Check the user can login.
		$result = $instance->authorise($options['action']);

		if (!$result)
		{
			$this->app->enqueueMessage(Text::_('JERROR_LOGIN_DENIED'), 'warning');

			return false;
		}

		// Load component language file
		$lang = JFactory::getLanguage();
		$extension = 'com_limitactivelogins';
		$base_dir = JPATH_ADMINISTRATOR . '/components/' . $extension;
		$lang->load($extension, $base_dir);

		#####
		// Get Parameters from component
		$params = ComponentHelper::getComponent('com_limitactivelogins')->getParams();

		$login_logic = $params->get('login_logic', 0);
		$custom_error_message = $params->get('custom_error_message', Text::_('COM_LIMITACTIVELOGINS_CUSTOM_ERROR_MESSAGE_DEFAULT'));
		$forceLogout = $params->get('forceLogout', 0);
		$sharedSessions = $this->app->get('shared_session', '0');

		// max active logins
		JLoader::register('LimitactiveloginsHelper', JPATH_ADMINISTRATOR . '/components/com_limitactivelogins/helpers/limitactivelogins.php');
		$max_active_logins = LimitactiveloginsHelper::getMaxActiveLogins($instance);

		// Show error message
		$error_message = Text::sprintf($custom_error_message, $max_active_logins);

		// Load the language file
		Factory::getLanguage()->load('plg_user_limitactivelogins', JPATH_SITE.'/plugins/user/limitactivelogins/');

		// Get the user sessions
		$query = $this->db->getQuery(true);
		$query->select('DISTINCT a.ip_address, a.user_agent');
		$query->from($this->db->quoteName('#__limitactivelogins_logs', 'a'));
		$query->join('LEFT', '#__session AS b ON b.session_id = a.session_id AND b.userid = a.userid');
		$query->where($this->db->quoteName('b.guest') . ' = 0');
		$query->where('(' . $this->db->quoteName('b.client_id') . ' = 0' . ' OR ' . $this->db->quoteName('b.client_id') . ' IS NULL' . ')'); // 0 is for users, 1 is for admins
		$query->where($this->db->quoteName('a.userid') . ' = '. (int) $instance->id);
		$query->where($this->db->quoteName('b.userid') . ' = '. (int) $instance->id);
		$query->group('a.ip_address, a.user_agent, a.userid');

		try
		{ 
			$this->db->setQuery($query);
			$count_of_user_sessions = count($this->db->loadObjectlist());
		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, $e->getMessage());
			return false;
		}
		
		if (Factory::getApplication()->isClient('site'))
		{
			$count_of_user_sessions++;
		}

		if (Factory::getApplication()->isClient('site') && $count_of_user_sessions > $max_active_logins)
		{
			/**
			 * Do not allow new login if the limit is reached. Users needs to wait for the old login sessions to expire.<br><strong>Allow:</strong> Allow new login by terminating all old sessions when the limit is reached.
			 * 1: Allow
			 * 0: Block
			 */ 
			if($login_logic) 
			{
				// ALLOW
				// Purge the old session
				$query = $this->db->getQuery(true)
				->delete('#__session')
				->where($this->db->quoteName('guest') . ' = 0')
				->where('(' . $this->db->quoteName('client_id') . ' = 0' . ' OR ' . $this->db->quoteName('client_id') . ' IS NULL' . ')') // 0 is for users, 1 is for admins
				->where($this->db->quoteName('userid') . ' = ' . (int) $instance->id)
				->order($this->db->quoteName('time') . ' ASC')
				->setLimit('1');

				if (!$sharedSessions && isset($options['clientid']))
				{
					$query->where($this->db->quoteName('client_id') . ' = ' . (int) $options['clientid']);
				}

				try
				{
					$this->db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
					JError::raiseError(500, $e->getMessage());
					return false;
				}

				// Purge the old session from #__limitactivelogins_logs
				$query = $this->db->getQuery(true)
				->delete('#__limitactivelogins_logs')
				->where($this->db->quoteName('userid') . ' = ' . (int) $instance->id)
				->order($this->db->quoteName('datetime') . ' ASC')
				->setLimit('1');

				try
				{
					$this->db->setQuery($query)->execute();
				}
				catch (RuntimeException $e)
				{
					JError::raiseError(500, $e->getMessage());
					return false;
				}
			}
			else
			// BLOCK
			{	
				// BLOCK
				$count_of_user_sessions--;

				// return json result if is api
				if ($this->app->input->get('option') == 'com_jbackend')
				{
					$result = [];
					$result['status'] = 'login_failed';
					$result['message'] = strip_tags($error_message);
					header('Content-Type: application/json');
					echo json_encode($result);
					jexit();
				}
				else
				{
					Factory::getApplication()->setUserState('com_limitactivelogins.action.uid', (int) $instance->id);
					$redirect_to_url = URI::root().'index.php?option=com_limitactivelogins&view=action&'.Session::getFormToken().'=1';
					Factory::getApplication()->redirect($redirect_to_url);
				}
			}
		}
		#####

		// Mark the user as logged in
		$instance->guest = 0;

		$session = Factory::getSession();
		$session_id = $session->getId();

		// Grab the current session ID
		$oldSessionId = $session->getId();

		// Fork the session
		$session->fork();

		$session->set('user', $instance);

		// Ensure the new session's metadata is written to the database
		$this->app->checkSession();
		
		// Purge the old session
		$query = $this->db->getQuery(true)
			->delete('#__session')
			->where($this->db->quoteName('session_id') . ' = ' . $this->db->quote($oldSessionId));

		try
		{
			$this->db->setQuery($query)->execute();
		}
		catch (RuntimeException $e)
		{
			// The old session is already invalidated, don't let this block logging in
			JError::raiseError(500, $e->getMessage());
			return false;
		}

		// Hit the user last visit field
		$instance->setLastVisit();
	
		// Add "user state" cookie used for reverse caching proxies like Varnish, Nginx etc.
		if ($this->app->isClient('site'))
		{
			$this->app->input->cookie->set(
				'joomla_user_state',
				'logged_in',
				0,
				$this->app->get('cookie_path', '/'),
				$this->app->get('cookie_domain', ''),
				$this->app->isHttpsForced(),
				true
			);
		}

		return true;
	}

	public function onUserAfterLogin($options)
	{
		if (Factory::getApplication()->isClient('administrator'))
		{
			return false;
		}

		$loggedInUser = $options['user'];

		// get session_id
		$session = Factory::getSession();
		$session_id = $session->getId();
		$user_id = isset($loggedInUser->id) ? $loggedInUser->id : NULL;
		$username = isset($loggedInUser->username) ? $loggedInUser->username : NULL;

		if (empty($user_id))
		{
			$user_id = isset($options['id']) ? $options['id'] : NULL;
		}

		if (empty($username))
		{
			$username = isset($options['username']) ? $options['username'] : NULL;
		}

		// do not continue if user does not exists
		if (empty($user_id) || empty($username))
		{
			return false;
		}

		// BEGIN: Store the device in the database
		// Check if the session id already exists
		$query = $this->db->getQuery(true)
					->select('COUNT(*)')
					->from($this->db->qn('#__limitactivelogins_logs'))
					->where($this->db->qn('session_id') . ' = ' . $this->db->q($session_id));

		try
		{
			$this->db->setQuery($query);
			$count = $this->db->loadResult();
		}
		catch (\Exception $e)
		{
			return;
		}

		try
		{
			// Call the Web357 Framework Helper Class
			require_once(JPATH_PLUGINS.'/system/web357framework/web357framework.class.php');
			$w357frmwrk = new Web357FrameworkHelperClass;

			// get user agent
			$user_agent = getenv("HTTP_USER_AGENT");
			$country = $w357frmwrk->getCountry();
			$browser = $w357frmwrk->getBrowser();
			$operating_system = $w357frmwrk->getOS();

			// sql time (UTC)
			$jdate = new JDate;
			$sql_datetime = $jdate->toSql(); 

			// Get the correct IP address of the client
			$ip_address = IpHelper::getIp();
			if (!filter_var($ip_address, FILTER_VALIDATE_IP))
			{
				$ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
			}

			// if session does not exists insert into database or update if exists
			if (!$count)
			{	
				$insertObject = new stdClass();
				$insertObject->session_id = $session_id;
				$insertObject->user_agent = $user_agent;
				$insertObject->country = $country;
				$insertObject->browser = $browser;
				$insertObject->operating_system = $operating_system;
				$insertObject->ip_address = $ip_address;
				$insertObject->datetime = $sql_datetime;
				$insertObject->userid = $user_id;			
				$insertObject->username = $username;

				try
				{
					// Set the query using our newly populated query object and execute it.
					$this->db->insertObject('#__limitactivelogins_logs', $insertObject);
				}
				catch (\Exception $e)
				{
					echo Text::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()) . '<br>';
					JError::raiseError(500, $e->getMessage());
					return false;
				}
			}
			else
			{
				$updateObject = new stdClass();
				$updateObject->session_id = $session_id;
				$updateObject->user_agent = $user_agent;
				$updateObject->country = $country;
				$updateObject->browser = $browser;
				$updateObject->operating_system = $operating_system;
				$updateObject->ip_address = $ip_address;
				$updateObject->datetime = $sql_datetime;
				$updateObject->userid = $user_id;			
				$updateObject->username = $username;

				$this->db->updateObject('#__limitactivelogins_logs', $updateObject, 'session_id');
			}
		}
		catch (\Exception $e)
		{

			return false;
		}
		// END: Store the device in the database

		return true;
	}

	public function deleteOldSessions()
	{
		// Get User Sessions
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('s.session_id'));
		$query->from($this->db->quoteName('#__session', 's'));
		$this->db->setQuery($query);

		try
		{
			$sessions_arr = $this->db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, $e->getMessage());
			return false;
		}

		// Delete sessions from #__limitactivelogins_logs and keep only common
		if ($sessions_arr)
		{
			// Purge the old session from #__limitactivelogins_logs
			$query = $this->db->getQuery(true)
			->delete('#__limitactivelogins_logs')
			->where($this->db->quoteName('session_id') . ' NOT IN (' . implode(',', $this->db->Quote($sessions_arr)) . ')');

			try
			{
				$this->db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
				JError::raiseError(500, $e->getMessage());
				return false;
			}
		}

	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (client, ...).
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.5
	 */
	public function onUserLogout($user, $options = array())
	{
		$my      = Factory::getUser();
		$session = Factory::getSession();

		// Make sure we're a valid user first
		if ($user['id'] == 0 && !$my->get('tmp_user'))
		{
			return true;
		}

		$sharedSessions = $this->app->get('shared_session', '0');

		// Check to see if we're deleting the current session
		if ($my->id == $user['id'] && ($sharedSessions || (!$sharedSessions && $options['clientid'] == $this->app->getClientId())))
		{
			// Hit the user last visit field
			$my->setLastVisit();

			// Purge the old session from #__limitactivelogins_logs
			$query = $this->db->getQuery(true)
			->delete('#__limitactivelogins_logs')
			->where($this->db->quoteName('userid') . ' = ' . (int) $user['id'])
			->where($this->db->quoteName('session_id') . ' = ' . $this->db->quote($session->getId()));

			try
			{
				$this->db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
				JError::raiseError(500, $e->getMessage());
				return false;
			}

			// Destroy the php session for this user
			$session->destroy();
		}

		// Enable / Disable Forcing logout all users with same userid
		$params = ComponentHelper::getComponent('com_limitactivelogins')->getParams();
		$forceLogout = $params->get('forceLogout', 0);

		if ($forceLogout)
		{
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__session'))
				->where($this->db->quoteName('userid') . ' = ' . (int) $user['id']);

			if (!$sharedSessions)
			{
				$query->where($this->db->quoteName('client_id') . ' = ' . (int) $options['clientid']);
			}

			try
			{
				$this->db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
				JError::raiseError(500, $e->getMessage());
				return false;
			}
			
		}

		// Delete "user state" cookie used for reverse caching proxies like Varnish, Nginx etc.
		if ($this->app->isClient('site'))
		{
			$this->app->input->cookie->set('joomla_user_state', '', 1, $this->app->get('cookie_path', '/'), $this->app->get('cookie_domain', ''));
		}

		return true;
	}

	/**
	 * This method will return a user object
	 *
	 * If options['autoregister'] is true, if the user doesn't exist yet they will be created
	 *
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (remember, autoregister, group).
	 *
	 * @return  User
	 *
	 * @since   1.5
	 */
	protected function _getUser($user, $options = array())
	{
		$instance = User::getInstance();
		$id = (int) UserHelper::getUserId($user['username']);

		if ($id)
		{
			$instance->load($id);

			return $instance;
		}

		// TODO : move this out of the plugin
		$params = ComponentHelper::getComponent('com_users')->getParams();

		// Read the default user group option from com_users
		$defaultUserGroup = $params->get('new_usertype', $params->get('guest_usergroup', 1));

		$instance->id = 0;
		$instance->name = $user['fullname'];
		$instance->username = $user['username'];
		$instance->password_clear = $user['password_clear'];

		// Result should contain an email (check).
		$instance->email = $user['email'];
		$instance->groups = array($defaultUserGroup);

		// If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] : $this->params->get('autoregister', 1);

		if ($autoregister)
		{
			if (!$instance->save())
			{
				$this->app->enqueueMessage(Text::_('Error in autoregistration for user ' . $user['username'] . '.'), 'error');
				return false;
			}
		}
		else
		{
			// No existing user and autoregister off, this is a temporary user.
			$instance->set('tmp_user', true);
		}

		return $instance;
	}

}