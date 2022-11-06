<?php
/**
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @author Joomla! Extensions Store
 * @copyright (C)2015 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ();

use Joomla\CMS\Uri\Uri;

class JAmpUri extends Uri {
	public static function setInstance($uri, $instanceName = 'SERVER') {
		self::$instances[$instanceName] = $uri;
	}
}
