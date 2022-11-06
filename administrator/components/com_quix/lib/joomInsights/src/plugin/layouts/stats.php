<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.stats
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

extract($displayData);

/**
 * Layout variablesurl
 * -----------------
 * @var  array  $statsData  Array containing the data that will be sent to the stats server
 */

$versionFields = array('php_version', 'db_version', 'cms_version');
?>
<div class="js-jinsights-data-details"  style="display:none;">
	<p>
	Server environment details (php, mysql, server, WordPress versions), Number of users in your site, Site language, Number of active and inactive plugins, Site name and url, Your name and email address. No sensitive data is tracked.
	</p>
</div>
