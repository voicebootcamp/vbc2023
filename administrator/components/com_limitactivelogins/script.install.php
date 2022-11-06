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

use Joomla\Database\Exception\ExecutionFailureException;

require_once __DIR__ . '/script.install.helper.php';

class Com_LimitactiveloginsInstallerScript extends Com_LimitactiveloginsInstallerScriptHelper
{
	public $name           	= 'Limit Active Logins (Pro version)';
	public $alias          	= 'limitactivelogins';
	public $extension_type 	= 'component';

    public function onAfterInstall($route)
	{
		$this->createTable();
	}

	public function uninstall($adapter)
	{
		$this->dropTable();
	}

	private function createTable()
	{
        $query = "CREATE TABLE IF NOT EXISTS `#__limitactivelogins_logs` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`ordering` INT(11) DEFAULT '0' NOT NULL,
			`state` TINYINT(1) NOT NULL DEFAULT 1,
			`checked_out` INT(11) NOT NULL DEFAULT 0,
			`checked_out_time` DATETIME NULL DEFAULT NULL,
			`created_by` INT(11) NOT NULL DEFAULT 0,
			`modified_by` INT(11) NOT NULL DEFAULT 0,
            `session_id` VARCHAR(255)  NOT NULL,
            `user_agent` VARCHAR(255)  NOT NULL,
            `country` VARCHAR(255)  NOT NULL,
            `browser` VARCHAR(255)  NOT NULL,
            `operating_system` VARCHAR(255)  NOT NULL,
            `ip_address` VARCHAR(255)  NOT NULL,
			`datetime` DATETIME NOT NULL,
			`userid` INT(11) NOT NULL,
            `username` VARCHAR(255)  NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;";
		$this->db->setQuery($query);
		$this->db->execute();
	}

	private function dropTable()
	{
		// connect to db
		$db = JFactory::getDBO();

		// Check first if the table exists
		$query = "SHOW TABLES LIKE '" . $db->getPrefix() . "limitactivelogins_logs'";
		$db->setQuery($query);
		$table_exist = $db->loadResult();
		if ($table_exist)
		{
			$query = "DROP TABLE `#__limitactivelogins_logs`";
			$db->setQuery($query);
			$db->execute();
		}
    }
}