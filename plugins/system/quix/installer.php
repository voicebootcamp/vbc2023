<?php
/**
 * @package		Quix
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class plgSystemQuixInstallerScript
{
    public function postflight($type, $parent)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $fields = [
            $db->quoteName('enabled') . ' = ' . (int) 1,
            $db->quoteName('ordering') . ' = ' . (int) 9999
        ];

        $conditions = [
            $db->quoteName('element') . ' = ' . $db->quote('quix'),
            $db->quoteName('type') . ' = ' . $db->quote('plugin')
        ];

        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

        $db->setQuery($query);
        $db->execute();


        // update the post install manually
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                    ->select('*')
                    ->from($db->qn('#__postinstall_messages'))
                    ->where($db->qn('language_extension') . ' = ' . $db->q('plg_system_quix'));
        $db->setQuery($query);
        $data = $db->loadObject();
        if(!$data){
            $this->install([]);
        }

        return true;
    }

    /*
    * $parent is the class calling this method.
    * install runs after the database scripts are executed.
    * If the extension is new, the install method is run.
    * If install returns false, Joomla will abort the install and undo everything already done.
    */
    function install($parent)
    {
        $db    = JFactory::getDbo();
        $query = 'INSERT INTO '.$db->quoteName('#__postinstall_messages').
                 ' ( `extension_id`, 
                  `title_key`, 
                  `description_key`, 
                  `action_key`, 
                  `language_extension`, 
                  `language_client_id`, 
                  `type`, 
                  `action_file`, 
                  `action`, 
                  `condition_file`, 
                  `condition_method`, 
                  `version_introduced`, 
                  `enabled`) VALUES '
                 .'( 700,
               "PLG_SYSTEM_QUIX_POSTINSTALL_TITLE", 
               "PLG_SYSTEM_QUIX_POSTINSTALL_BODY", 
               "PLG_SYSTEM_QUIX_POSTINSTALL_ACTION",
               "plg_system_quix",
                1,
               "action", 
               "site://plugins/system/quix/postinstall/actions.php",
               "com_quix_postinstall_action", 
               "site://plugins/system/quix/postinstall/actions.php", 
               "com_quix_postinstall_condition", 
               "4.0.0", 
               1)';

        $db->setQuery($query);
        $db->execute();
    }

    /*
     * $parent is the class calling this method
     * uninstall runs before any other action is taken (file removal or database processing).
     */
    function uninstall($parent)
    {
        $db    = JFactory::getDbo();
        $query = 'DELETE FROM '.$db->quoteName('#__postinstall_messages').
                 ' WHERE '.$db->quoteName('language_extension').' = '.$db->quote('plg_system_quix');
        $db->setQuery($query);
        $db->execute();
    }


}
