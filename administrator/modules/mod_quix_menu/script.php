<?php
/**
 * @package     Quix
 * @author      ThemeXpert http://www.themexpert.com
 * @copyright   Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @since       1.0.0
 */

defined('_JEXEC') or die;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_Quix
 * @since       3.4
 */
class mod_Quix_menuInstallerScript
{


  /**
   * Method to run after an install/update/uninstall method
   * $parent is the class calling this method
   * $type is the type of change (install, update or discover_install)
   *
   * @param $type
   * @param $parent
   *
   * @return void
   * @throws \Exception
   * @since 3.0.0
   */
    public function postflight()
    {
        $module = JTable::getInstance('Module', 'JTable');
        $module->load(array('module'=>'mod_quix_menu'));
        $module->position = 'menu';
        $module->published = 1;
        $module->ordering = 1;
        $module->access = 3;
        $module->params = '{"show_quix_menu":"0"}';

        if (!$module->check()) {
            JFactory::getApplication()->enqueueMessage(
                JText::sprintf('MOD_QUIX_MENU_ERROR_PUBLISH_MODULE', $module->getError())
            );
        }

        // Now store the module
        if (!$module->store()) {
            JFactory::getApplication()->enqueueMessage(
                JText::sprintf('MOD_QUIX_MENU_ERROR_PUBLISH_MODULE', $module->getError())
            );
        }

        // Now we need to handle the module assignments
        self::assignMenu($module->id);
    }

    public static function assignMenu($pk)
    {
        // Now we need to handle the module assignments
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('moduleid'))
            ->from($db->quoteName('#__modules_menu'))
            ->where($db->quoteName('moduleid') . ' = ' . $pk);
        $db->setQuery($query);
        $menus = $db->loadObject();

        // Insert the new records into the table
        if (!$menus->moduleid) {
            $query->clear()
                ->insert($db->quoteName('#__modules_menu'))
                ->columns(array($db->quoteName('moduleid'), $db->quoteName('menuid')))
                ->values($pk . ', ' . 0);
            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }
}
