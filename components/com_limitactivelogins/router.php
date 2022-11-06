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

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;

/**
 * Class LimitactiveloginsRouter
 *
 */
class LimitactiveloginsRouter extends RouterView
{
	public function __construct($app = null, $menu = null)
	{
		$logs = new RouterViewConfiguration('logs');
		$this->registerView($logs);
			$log = new RouterViewConfiguration('log');
			$log->setKey('id')->setParent($logs);
			$this->registerView($log);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));

			$this->attachRule(new StandardRules($this));
			$this->attachRule(new NomenuRules($this));
	}

		/**
		 * Method to get the segment(s) for an log
		 *
		 * @param   string  $id     ID of the log to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getLogSegment($id, $query)
		{
			return array((int) $id => $id);
		}

		/**
		 * Method to get the segment(s) for an log
		 *
		 * @param   string  $segment  Segment of the log to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getLogId($segment, $query)
		{
			return (int) $segment;
		}
}
