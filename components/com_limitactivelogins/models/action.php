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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Layout\FileLayout;
use \Joomla\Utilities\ArrayHelper;

/**
 * Methods supporting a list of Limitactivelogins records.
 *
 * @since  1.6
 */
class LimitactiveloginsModelAction extends \Joomla\CMS\MVC\Model\ListModel
{
    /**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		parent::__construct($config);
    }
    
    /**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Get the user id.
		$uid = Factory::getApplication()->getUserState('com_limitactivelogins.action.uid');
        
        // Set the user id.
		$this->setState('uid', $uid);
	}

    public function getUserDevices($user_id)
	{
		if (empty($user_id))
		{
			throw new Exception("User does not exist.");
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('a.session_id'));
		$query->select($db->quoteName('a.user_agent'));
		$query->select($db->quoteName('a.country'));
		$query->select($db->quoteName('a.browser'));
		$query->select($db->quoteName('a.operating_system'));
		$query->select($db->quoteName('a.ip_address'));
		$query->select($db->quoteName('a.datetime'));
		$query->select($db->quoteName('a.userid'));
		$query->select($db->quoteName('a.username'));
		$query->from($db->quoteName('#__limitactivelogins_logs', 'a'));
		$query->join('LEFT', '#__session AS b ON b.session_id = a.session_id AND b.userid = a.userid');
		$query->where($db->quoteName('a.userid') . ' = '. (int) $user_id);
		$query->where($db->quoteName('b.userid') . ' = '. (int) $user_id);
		$query->group('a.ip_address, a.user_agent, a.userid');
		
		$query->order('a.datetime DESC');
		$db->setQuery($query);

		try
		{
			return $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, $e->getMessage());
			return false;
		}
    }
}