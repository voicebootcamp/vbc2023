<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_quix
 * @since       1.3.0
 */
class QuixHelperSchema
{

    public static function updateConfig()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__extensions')
            ->where('`element` = "com_quix"')
            ->where('`type` = "component"');

        $db->setQuery($query);
        $result = $db->loadObject();
        $params = $result->params;
        if($params) $params = json_decode($params);

        // print_r($params);die;
        if(isset($params->responsive_image)) return true;

        $responsive_image = '{"quality": "80","large_desktop": "1900","desktop": "1400","tablet": "1024","mobile": "786","mini": "400"}';
        $params->responsive_image = json_decode($responsive_image);
        $params = json_encode($params);

        // now update db
        $query = $db->getQuery(true);
        // Fields to update.
        $fields = array(
            $db->quoteName('params') . ' = ' . $db->quote($params)
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('element') . ' = ' . $db->quote('com_quix'),
            $db->quoteName('type') . ' = ' . $db->quote('component')
        );

        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
        $db->setQuery($query);

        try
        {
            // Clear relavent cache
            $db->execute();
            QuixHelper::cleanCache();
        }
        catch (RuntimeException $e)
        {
            return false;
        }
    }


    /*
    * update db structure
    */
    public static function updateDB()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from($db->quoteName('#__quix'));
        $db->setQuery($query);

        try{
            //echo "Table Exists";
            $db->execute();
        } catch (Exception $e) {
            // echo "Table doesn't exist";
            JFactory::getApplication()->enqueueMessage('Stop! Quix table does not exist!', 'error');
            return;
        }


        $query = "SHOW COLUMNS FROM `#__quix` LIKE 'created'";
        $db->setQuery($query);
        $column = $db->loadObject();
        if (!COUNT($column)) {
            $query = 'ALTER TABLE `#__quix` ';
            $query .= "ADD `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `access`, ";
            $query .= "ADD `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`, ";
            $query .= "ADD `modified_by` int(10) unsigned NOT NULL DEFAULT '0' AFTER `modified`";
            $db->setQuery($query);
            $db->execute();
        }


        $query = "SHOW COLUMNS FROM `#__quix_collections` LIKE 'created'";
        $db->setQuery($query);
        $column = $db->loadObject();
        if (!COUNT($column)) {
            $query = 'ALTER TABLE `#__quix_collections` ';
            $query .= "ADD `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `access`, ";
            $query .= "ADD `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`, ";
            $query .= "ADD `modified_by` int(10) unsigned NOT NULL DEFAULT '0' AFTER `modified`";
            $db->setQuery($query);
            $db->execute();
        }


        $query = "SHOW COLUMNS FROM `#__quix` LIKE 'builder'";
        $db->setQuery($query);
        $column = $db->loadObject();
        if (!COUNT($column)) {
            $query = 'ALTER TABLE `#__quix` ';
            $query .= "ADD `builder` ENUM('classic','frontend') NOT NULL DEFAULT 'classic' AFTER `catid`";
            $db->setQuery($query);
            $db->execute();
        }


        $query = "SHOW COLUMNS FROM `#__quix` LIKE 'builder_version'";
        $db->setQuery($query);
        $column = $db->loadObject();
        if (!COUNT($column)) {
            $query = 'ALTER TABLE `#__quix` ';
            $query .= "ADD `builder_version` VARCHAR(10) NOT NULL DEFAULT '' AFTER `builder`";
            $db->setQuery($query);
            $db->execute();
        }


        $query = "SHOW COLUMNS FROM `#__quix_collections` LIKE 'builder'";
        $db->setQuery($query);
        $column = $db->loadObject();
        if (!COUNT($column)) {
            $query = 'ALTER TABLE `#__quix_collections` ';
            $query .= "ADD `builder` ENUM('classic', 'frontend') NOT NULL DEFAULT 'classic' AFTER `catid`";
            $db->setQuery($query);
            $db->execute();
        }


        $query = "SHOW COLUMNS FROM `#__quix_collections` LIKE 'builder_version'";
        $db->setQuery($query);
        $column = $db->loadObject();
        if (!COUNT($column)) {
            $query = 'ALTER TABLE `#__quix_collections` ';
            $query .= "ADD `builder_version` VARCHAR(10) NOT NULL DEFAULT '' AFTER `builder`";
            $db->setQuery($query);
            $db->execute();
        }

        // now create new config table

        $sql = "
		CREATE TABLE IF NOT EXISTS `#__quix_configs` (
		  `name` varchar(255) NOT NULL,
		  `params` text NOT NULL
		) DEFAULT CHARSET=utf8 COMMENT='Store any configuration in key => params maps';
		";
        $db->setQuery($sql);
        $db->execute();

        // now update type of library type

        $sql = "ALTER TABLE `#__quix_collections` CHANGE `type` `type` VARCHAR (255) NOT NULL DEFAULT 'section';";
        $db->setQuery($sql);
        $db->execute();

        // now update type of library type

        $sql = "ALTER TABLE `#__quix_collections` CHANGE `type` `type` VARCHAR (255) NOT NULL DEFAULT 'section';";
        $db->setQuery($sql);
        $db->execute();

        // new imagify

        $sql = '
        CREATE TABLE IF NOT EXISTS `#__quix_imgstats` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `item_id` int(11) NOT NULL,
            `item_type` varchar(100) NOT NULL,
            `images_count` int(11) NOT NULL,
            `original_size` int(11) NOT NULL,
            `optimise_size` int(11) NOT NULL,
            `mobile_size` int(11) NOT NULL,
            `params` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;';
        $db->setQuery($sql);
        $db->execute();

        // conditions
        $sql = '
        CREATE TABLE IF NOT EXISTS `#__quix_conditions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `item_id` int(11) NOT NULL,
            `item_type` varchar(100) NOT NULL,
            `component` varchar(100) NOT NULL,
            `condition_type` varchar(100) NOT NULL COMMENT "articles categories menus",
            `condition_id` int(11) NOT NULL COMMENT "type id",
            `condition_info` varchar(100) NOT NULL COMMENT "type info direct to search",
            `params` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;';
        $db->setQuery($sql);
        $db->execute();

        // conditions
        $sql = '
		CREATE TABLE IF NOT EXISTS `#__quix_editor_map` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `context` varchar(100) NOT NULL,
            `context_id` int(11) NOT NULL,
            `collection_id` int(11) NOT NULL,
            `status` TINYINT NOT NULL DEFAULT "1",
            `params` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;';
        $db->setQuery($sql);
        $db->execute();

        /**
         * Field 'checked_out' doesn't have a default value
         * @since 4.1.6
         */
        $sql = "ALTER TABLE `#__quix` DROP `checked_out`;";
        $db->setQuery($sql);
        $db->execute();

        $query = "ALTER TABLE `#__quix` ADD `checked_out` INT(11)  NOT NULL DEFAULT 0 AFTER `modified_by`";
        $db->setQuery($query);
        $db->execute();

        $sql = "ALTER TABLE `#__quix_collections` DROP `checked_out`;";
        $db->setQuery($sql);
        $db->execute();

        $query = "ALTER TABLE `#__quix_collections` ADD `checked_out` INT(11)  NOT NULL DEFAULT 0 AFTER `modified_by`";
        $db->setQuery($query);
        $db->execute();

        // check config
        self::updateConfig();
    }
}
