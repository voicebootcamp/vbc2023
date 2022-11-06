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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\Utilities\IpHelper;

/**
 * Limitactivelogins helper.
 *
 * @since  1.6
 */
class LimitactiveloginsHelper
{
	public static $displayed_text;
	public static $login_as_type;
	public static $login_as_type_characters_limit;
	
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		$layoutName = Factory::getApplication()->input->getCmd('layout', 'default');

		JHtmlSidebar::addEntry(JText::_('COM_LIMITACTIVELOGINS_LOGGED_IN_USERS_GROUPED_BY_USER'), 'index.php?option=com_limitactivelogins&view=logs&layout=grouped_by_user&filter[search]=', $vName == 'logs' && $layoutName == 'grouped_by_user');
		JHtmlSidebar::addEntry(JText::_('COM_LIMITACTIVELOGINS_LOGGED_IN_USERS_DETAILED'), 'index.php?option=com_limitactivelogins&view=logs&layout=default&filter[search]=', $vName == 'logs' && $layoutName == 'default');
		JHtmlSidebar::addEntry(JText::_('COM_LIMITACTIVELOGINS_CONFIGURATION'), 'index.php?option=com_config&view=component&component=com_limitactivelogins', $vName == 'settingscore');
		JHtmlSidebar::addEntry(JText::_('COM_LIMITACTIVELOGINS_ABOUT_LIMITACTIVELOGINS'), 'index.php?option=com_limitactivelogins&view=overv', $vName == 'overv');
		JHtmlSidebar::addEntry(JText::_('COM_LIMITACTIVELOGINS_ABOUT_WEB357'), 'index.php?option=com_limitactivelogins&view=about', $vName == 'about');
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = Factory::getUser();
		$result = new JObject;

		$assetName = 'com_limitactivelogins';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	public static function get_gravatar( $email, $s = 80, $r = 'g' ) 
	{
		$gravemail = md5( strtolower( trim( $email ) ) );
		$gravsrc = "https://www.gravatar.com/avatar/".$gravemail;
		$gravcheck = "https://www.gravatar.com/avatar/".$gravemail."?d=404&s=$s&r=$r";
		$response = get_headers($gravcheck);

		if ($response[0] != "HTTP/1.1 404 Not Found")
		{
			$url = $gravsrc;
		}
		else
		{
			$url = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDI2IDI2IiBpZD0i0KHQu9C+0LlfMSIgdmVyc2lvbj0iMS4xIiB2aWV3Qm94PSIwIDAgMjYgMjYiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPjxwYXRoIGQ9Ik0yNSwxM2MwLTYuNjE2Njk5Mi01LjM4MjgxMjUtMTItMTItMTJTMSw2LjM4MzMwMDgsMSwxM2MwLDMuMzgzNjA2LDEuNDEzMjA4LDYuNDM4NjU5NywzLjY3MzY0NSw4LjYyMjI1MzQgIGMwLjA1MjkxNzUsMC4wNjg5MDg3LDAuMTE1NjAwNiwwLjEyNDc1NTksMC4xODg5NjQ4LDAuMTcxODE0QzcuMDAzODQ1MiwyMy43NzY5MTY1LDkuODU4Mjc2NCwyNSwxMywyNSAgczUuOTk2MTU0OC0xLjIyMzA4MzUsOC4xMzczOTAxLTMuMjA1OTMyNmMwLjA3MzM2NDMtMC4wNDcwNTgxLDAuMTM2MDQ3NC0wLjEwMjkwNTMsMC4xODg5NjQ4LTAuMTcxODE0ICBDMjMuNTg2NzkyLDE5LjQzODY1OTcsMjUsMTYuMzgzNjA2LDI1LDEzeiBNMTMsMi41YzUuNzkwMDM5MSwwLDEwLjUsNC43MTA0NDkyLDEwLjUsMTAuNSAgYzAsMi40NTQ5NTYxLTAuODUzMjcxNSw0LjcxMDgxNTQtMi4yNzAyNjM3LDYuNTAwODU0NWMtMC42NTA1MTI3LTIuMDk3ODM5NC0yLjUwNzYyOTQtMy43NDAxMTIzLTUuMDI4MTM3Mi00LjQ5NTc4ODYgIGMxLjM3MzU5NjItMC45OTQwNzk2LDIuMjcyMDMzNy0yLjYwNDYxNDMsMi4yNzIwMzM3LTQuNDI0NDk5NWMwLTMuMDE0MTYwMi0yLjQ1NTA3ODEtNS40NjYzMDg2LTUuNDczNjMyOC01LjQ2NjMwODYgIHMtNS40NzM2MzI4LDIuNDUyMTQ4NC01LjQ3MzYzMjgsNS40NjYzMDg2YzAsMS44MTk4ODUzLDAuODk4NDM3NSwzLjQzMDQxOTksMi4yNzIwMzM3LDQuNDI0NDk5NSAgYy0yLjUyMDUwNzgsMC43NTU2NzYzLTQuMzc3NjI0NSwyLjM5Nzk0OTItNS4wMjgxMzcyLDQuNDk1Nzg4NkMzLjM1MzI3MTUsMTcuNzEwODE1NCwyLjUsMTUuNDU0OTU2MSwyLjUsMTMgIEMyLjUsNy4yMTA0NDkyLDcuMjA5OTYwOSwyLjUsMTMsMi41eiBNOS4wMjYzNjcyLDEwLjU4MDU2NjRjMC0yLjE4NzAxMTcsMS43ODIyMjY2LTMuOTY2MzA4NiwzLjk3MzYzMjgtMy45NjYzMDg2ICBzMy45NzM2MzI4LDEuNzc5Mjk2OSwzLjk3MzYzMjgsMy45NjYzMDg2UzE1LjE5MTQwNjMsMTQuNTQ2ODc1LDEzLDE0LjU0Njg3NVM5LjAyNjM2NzIsMTIuNzY3NTc4MSw5LjAyNjM2NzIsMTAuNTgwNTY2NHogICBNNi4wMzA3NjE3LDIwLjgzMTk3MDJDNi4yNTYyMjU2LDE4LjA4MjAzMTMsOS4xNzIzNjMzLDE2LjA0Njg3NSwxMywxNi4wNDY4NzVzNi43NDM3NzQ0LDIuMDM1MTU2Myw2Ljk2OTIzODMsNC43ODUwOTUyICBDMTguMTEzMDk4MSwyMi40ODU1MzQ3LDE1LjY3NTcyMDIsMjMuNSwxMywyMy41UzcuODg2OTAxOSwyMi40ODU1MzQ3LDYuMDMwNzYxNywyMC44MzE5NzAyeiIgZmlsbD0iIzFEMUQxQiIvPjwvc3ZnPg==';
		}

		return $url;
	}
	
	/**
	 * Gets data from an IP address
	 * 1. 'country_name' => Country Name (e.g. Greece)
	 * 2. 'country_code' => Country Code (e.g. GR)
	 * 3. 'continent' => Continent (e.g. EU) 
	 *
	 * @param   string  $ip      The IP address to look up
	 * @param   string  $type  country_name, country_code, continent.
	 *
	 * @return  mixed  A string with the name or code of the country, or the continent
	 */
	private static function getDataFromGeoIP($type = 'country_name')
	{
		$result = 'XX';

		// If the GeoIP provider is not loaded return "XX" (no country detected)
		if (!class_exists('Web357LimitActiveLoginsGeoIp2'))
		{
			return $result;
		}

		// Get the correct IP address of the client
		//$ip = "31.186.104.0"; // Greece
		//$ip = "212.50.119.207"; // Cyprus
		$ip = IpHelper::getIp();

		if (!filter_var($ip, FILTER_VALIDATE_IP))
		{
			$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		}

		// Use GeoIP to detect the country
		$geoip = new \Web357LimitActiveLoginsGeoIp2();

		switch ($type) {
			case 'country_name':
				$result = $geoip->getCountryName($ip);
				break;
			case 'country_code':
				$result = $geoip->getCountryCode($ip);
				break;
			case 'continent':
				$result = $geoip->getContinent($ip);
				break;
			default:
				$result = $geoip->getCountryName($ip);
				break;
		}

		// If detection failed, return "XX" (no country detected)
		if (empty($result))
		{
			$result = 'XX';
		}

		return $result;
	}

	public static function getMaxActiveLogins($user)
	{
		$params = ComponentHelper::getComponent('com_limitactivelogins')->getParams();
		$max_active_logins = $params->get('max_active_logins', 1);
		$enableGeoIP2Webservice = $params->get('enableGeoIP2Webservice', 1);

		if ($enableGeoIP2Webservice)
		{
			// require geoip2 library
			require_once(JPATH_ROOT . '/administrator/components/com_limitactivelogins/lib/geoip2/geoip2.php');
			require_once(JPATH_ROOT . '/administrator/components/com_limitactivelogins/lib/vendor/autoload.php');

			// get IP's details
			$country_name = self::getDataFromGeoIP('country_name'); // Greece
			$country_code = self::getDataFromGeoIP('country_code'); // GR
			$continent_code = self::getDataFromGeoIP('continent'); // EU
		}

		// BEGIN: Customizable Maximum Active Logins
		$custom_limits_group = $params->get('custom_limits_group', '');
		$array_sum = 0;
		if (!empty($custom_limits_group) && is_object($custom_limits_group))
		{
			foreach($custom_limits_group as $group=>$obj)
			{
                if ($obj->cstmlim_status) {
					
					$conditions_arr = [];

                    // Check the User Group
                    if (empty($obj->cstmlim_usergroup)) {
                        $is_usergroup = 0;
                    } elseif (is_array($obj->cstmlim_usergroup) && count(array_intersect($user->groups, $obj->cstmlim_usergroup)) > 0) {
                        $is_usergroup = 1;
                    } else {
                        $is_usergroup = -10;
                    }

					// Check the User Group
                    if (empty($obj->cstmlim_user_id)) {
                        $is_user = 0;
                    } elseif ($obj->cstmlim_user_id > 0 && $user->id === $obj->cstmlim_user_id) {
                        $is_user = 1;
                    } else {
                        $is_user = -10;
					}
					
					$conditions_arr[] = $is_usergroup;
					$conditions_arr[] = $is_user;

					if ($enableGeoIP2Webservice)
					{
						// Check the Continent
						if (empty($obj->cstmlim_continents)) {
							$is_continent = 0;
						} elseif (in_array($continent_code, $obj->cstmlim_continents)) {
							$is_continent = 1;
						} else {
							$is_continent = -10;
						}

						// Check the Country
						if (empty($obj->cstmlim_countries)) {
							$is_country = 0;
						} elseif (in_array($country_code, $obj->cstmlim_countries)) {
							$is_country = 1;
						} else {
							$is_country = -10;
						}
						
						$conditions_arr[] = $is_continent;
						$conditions_arr[] = $is_country;
					}

                    $array_sum = array_sum($conditions_arr);

                    if ($array_sum > 0) {
                        $max_active_logins = $obj->cstmlim_max_active_logins;
                        break;
                    }
                }
			}
		}

		return $max_active_logins;
	}
}