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

use Joomla\CMS\Factory;

jimport('joomla.plugin.plugin');

class plgSystemLimitActiveLogins extends JPlugin
{
	public function onAfterInitialise()
	{
		if (Factory::getApplication()->isClient('administrator')){
			return;
		}

		$this->deleteOldSessions();
	}	

	private function deleteOldSessions()
	{
		$user = Factory::getUser();

		if (!$user->id)
		{
			// return;
		}

		// Arrays
		$joomla_sessions_arr = [];
		$limitactivelogin_sessions_arr = [];

		// Activate Plugin
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Get User Sessions
		$query = $db->getQuery(true);
		$query->select($db->quoteName('s.session_id'));
		$query->from($db->quoteName('#__session', 's'));
		$query->where($db->quoteName('s.userid') . ' > 0');
		// $query->where($db->quoteName('s.userid') . ' = '. (int) $user->id);
		$db->setQuery($query);

		try
		{
			$joomla_sessions_arr = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, $e->getMessage());
			return false;
		}

		// Get sessions from #__limitactivelogins_logs
		$query = $db->getQuery(true);
		$query->select($db->quoteName('a.session_id'));
		$query->from($db->quoteName('#__limitactivelogins_logs', 'a'));
		$db->setQuery($query);

		try
		{
			$limitactivelogin_sessions_arr = $db->loadColumn();

		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, $e->getMessage());
			return false;
		}

		// Delete sessions from #__limitactivelogins_logs and keep only common
		if (!empty($limitactivelogin_sessions_arr) && !empty($joomla_sessions_arr))
		{
			foreach ($limitactivelogin_sessions_arr as $limitactivelogin_session)
			{
				if (!in_array($limitactivelogin_session, $joomla_sessions_arr))
				{
					// If the session from #__limitactivelogins_logs doesn't exist in joomla sessions table, delete it.
					$query = $db->getQuery(true)
					->delete('#__limitactivelogins_logs')
					->where($db->quoteName('session_id') . ' = ' . $db->Quote($limitactivelogin_session));

					try
					{
						$db->setQuery($query)->execute();
					}
					catch (RuntimeException $e)
					{
						JError::raiseError(500, $e->getMessage());
						return false;
					}

				}
			}
		}
	}
}