<?php
/**
 * @package    Quix
 * @author     ThemeXpert http://www.themexpert.com
 * @copyright  Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @since      1.0.0
 */

defined('_JEXEC') or die;


function com_quix_postinstall_condition()
{
    $db = JFactory::getDbo();
    $query = $db->getQuery(true)
                ->select('*')
                ->from($db->qn('#__extensions'))
                ->where($db->qn('type') . ' = ' . $db->q('plugin'))
                ->where($db->qn('enabled') . ' = ' . $db->q('1'))
                ->where($db->qn('folder') . ' = ' . $db->q('system'))
                ->where($db->qn('element') . ' = ' . $db->q('quix'));
    $db->setQuery($query);
    $enabled_plugin = $db->loadObject();
    
    return (bool) $enabled_plugin;
}

function com_quix_postinstall_action(){
    // Enable the plugin
    $db = JFactory::getDbo();
    $query = $db->getQuery(true)
                ->select('*')
                ->from($db->qn('#__extensions'))
                ->where($db->qn('type') . ' = ' . $db->q('plugin'))
                ->where($db->qn('enabled') . ' = ' . $db->q('0'))
                ->where($db->qn('folder') . ' = ' . $db->q('system'))
                ->where($db->qn('element') . ' = ' . $db->q('quix'));
    $db->setQuery($query);
    $enabled_plugins = $db->loadObjectList();

    $query = $db->getQuery(true)
                ->update($db->qn('#__extensions'))
                ->set($db->qn('enabled') . ' = ' . $db->q(1))
                ->where($db->qn('type') . ' = ' . $db->q('plugin'))
                ->where($db->qn('folder') . ' = ' . $db->q('system'))
                ->where($db->qn('element') . ' = ' . $db->q('quix'));
    $db->setQuery($query);
    $db->execute();

    //Redirect the user to the plugin configuration page
    // $url = 'index.php?option=com_plugins&task=plugin.edit&extension_id='
    //        .$enabled_plugins[0]->extension_id ;
    JFactory::getApplication()->redirect('');
}


