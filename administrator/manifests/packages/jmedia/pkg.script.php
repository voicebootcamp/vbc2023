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
 * @subpackage  com_digicom
 * @since       3.4
 */
class Pkg_JMediaInstallerScript
{

    function preflight($type, $parent)
    {
        // return self::enablePlugins();
    }


    /**
     * Function to perform changes during install
     *
     * @param  JInstallerAdapterComponent  $parent  The class calling this method
     *
     * @return  void
     *
     * @since   3.4
     */
    public function postflight($parent)
    {
        self::enablePlugins();

        ob_start();
        ?>
      <div class="jmedia_success_message">
        <style>
            #system-message-container .alert-success {
                display: none;
            }

            .jmedia-wrap {
                background: #fff;
                color: #606060;
                padding: 20px;
                border-radius: 2px;
                box-shadow: 0 0 10px #ddd;
                font-size: 1rem;
                line-height: 1.8;
                margin: 10px 0 40px;
                font-family: "Open Sans", sans-serif;
            }

            .jmedia-wrap h2 {
                font-size: 32px;
                font-weight: bold;
                margin: 0 0 20px;
                line-height: 1.3;
            }

            .jmedia-wrap li {
                margin-bottom: 9px;
            }

            .jmedia-wrap .btn-link {
                background: #3134ee;
                color: #fff;
                display: inline-block;
                padding: 0.4rem 2rem;
                margin-right: 10px;
                text-align: center;
                text-decoration: none;
                transition: .2s ease-out;
                border-radius: 2px;
                box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
            }

            .jmedia-wrap .btn-link:hover {
                background: #327b32;
            }

            <?php if(JVERSION >= 4):?>
            .jmedia-wrap .row-fluid{display: grid;grid-template-columns: 1fr 2fr;}
            <?php endif; ?>
        </style>
        <div class="jmedia-wrap">
          <div class="row-fluid">
            <div class="span3 text-center" style="margin-top: 150px;">
                          <span style="font-size: 90px; line-height: 1; margin-top: 16px; display: inline-block;">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 50" width="250">
                                <g fill="#454c55">
                                  <path d="M65.69 33.87a6.87 6.87 0 01-4.83 1.47q-3.26 0-6.91-.32v-4.3q2.66.16 5.46.16a1.75 1.75 0 001.25-.43 1.51 1.51 0 00.46-1.16V17.13h-4.44v-4.42h10.58v16.57a6 6 0 01-1.57 4.59zM91.83 22.59l-4.78 8h-4.77l-4.78-8v12.43h-6.14V12.71h6.48l6.83 11.79 6.82-11.79h6.48v22.31h-6.14zM118.45 34.7q-6.59.64-11.94.64a5.23 5.23 0 01-3.74-1.31 4.53 4.53 0 01-1.4-3.47v-7.65a4.87 4.87 0 011.47-3.78 5.64 5.64 0 014-1.35h6.83a5.64 5.64 0 014 1.35 4.87 4.87 0 011.45 3.75v5.9h-11.76v1.27a1 1 0 00.34.8 1.2 1.2 0 00.85.32q3.38 0 9.9-.48zm-9.68-12.91q-1.37 0-1.37 1.27v1.75h5.8v-1.75q0-1.27-1.37-1.27zM135 35.02l-.34-1.59a12.6 12.6 0 01-3.29 1.45 11.15 11.15 0 01-2.85.46h-1.54a5.23 5.23 0 01-3.72-1.31 4.53 4.53 0 01-1.4-3.47v-7.34a4.87 4.87 0 011.45-3.75 5.64 5.64 0 014-1.35h7v-5.41h6v22.31zm-4.78-4.3a15.41 15.41 0 004.09-.64v-7.65h-5.12q-1.37 0-1.37 1.27v5.9a1 1 0 00.34.8 1.2 1.2 0 00.85.32zM151.2 18.76v16.26h-6v-12h-2.53v-4.3zm-6-6.37h6v4.46h-6zM155.47 18.44a111.94 111.94 0 0111.3-.63 5.23 5.23 0 013.68 1.32 4.53 4.53 0 011.4 3.47v12.42h-5.29l-.34-1.59a12.6 12.6 0 01-3.29 1.45 11.14 11.14 0 01-2.85.46h-1.19a5.23 5.23 0 01-3.72-1.31 4.53 4.53 0 01-1.4-3.47v-1.43a4.53 4.53 0 011.4-3.47 5.23 5.23 0 013.72-1.31h7v-1.29a1 1 0 00-.34-.8 1.2 1.2 0 00-.85-.32q-2.22 0-4.9.19l-4.32.29zm6.3 12.43a15.42 15.42 0 004.09-.64v-1.91h-4.95a1.2 1.2 0 00-.85.32 1 1 0 00-.34.8v.32a1 1 0 00.34.8 1.2 1.2 0 00.85.32z"></path>
                                </g>
                                <path d="M8.5 38.83V4.9a3.78 3.78 0 013.77-3.77h26.39a7.56 7.56 0 017.54 7.54v30.16a1.87 1.87 0 01-1.88 1.88H10.39a1.87 1.87 0 01-1.89-1.88z"
                                      fill="#fff"></path>
                                <path d="M46.2 8.67h-3.77a3.78 3.78 0 01-3.77-3.77V1.13a7.56 7.56 0 017.54 7.54z" fill="#fff"></path>
                                <path d="M47.33 8.67v-.15A8.62 8.62 0 0038.66 0H12.27a4.89 4.89 0 00-4.9 4.9v2.64a1.13 1.13 0 002.26 0V4.9a2.62 2.62 0 012.64-2.64h25.26V4.9a4.89 4.89 0 004.9 4.9h3.77a1.23 1.23 0 001.06-.67 1.89 1.89 0 00.07-.46zM39.77 4.9V2.37a6.39 6.39 0 015.16 5.16h-2.5a2.62 2.62 0 01-2.66-2.63z"
                                      fill="#454c55"></path>
                                <path d="M39.3 17.34H26.67a6.29 6.29 0 01-5-2.45 6.29 6.29 0 00-5-2.45H6.62a1.87 1.87 0 00-1.85 1.88v24.51a1.87 1.87 0 001.88 1.88h34.54V19.22a1.89 1.89 0 00-1.89-1.88z"
                                      fill="#fff"></path>
                                <path d="M11.4 30.13a9.42 9.42 0 1010.58-8.11 9.42 9.42 0 00-10.58 8.11zM1.6 45.73a2.27 2.27 0 01.41-3.17l4.76-3.66a2.27 2.27 0 013.17.41 2.27 2.27 0 01-.41 3.17l-4.76 3.65a2.28 2.28 0 01-3.17-.4z"
                                      fill="#fff"></path>
                                <path d="M20.77 41.88c-.45 0-.94 0-1.39-.08a10.55 10.55 0 119.76-16.89 10.35 10.35 0 012.07 7.8 10.47 10.47 0 01-10.44 9.17zm0-18.85a8.29 8.29 0 00-1.09 16.51 8.22 8.22 0 006.09-1.65 8.13 8.13 0 003.17-5.5 8.22 8.22 0 00-1.66-6.14 8.13 8.13 0 00-5.51-3.18 9.53 9.53 0 00-1.05-.04z"
                                      fill="#7fbee8"></path>
                                <path d="M10.01 40.45a1.12 1.12 0 01-.9-.45 1.11 1.11 0 01.23-1.58l3-2.3a1.1320004 1.1320004 0 111.36 1.81l-3 2.3a1.22 1.22 0 01-.69.22z"
                                      fill="#7fbee8"></path>
                                <path d="M3.41 47.72a1.84 1.84 0 01-.45 0A3.39 3.39 0 01.7 46.44a3.38 3.38 0 01.64-4.75l4.79-3.66a3.4104435 3.4104435 0 014.18 5.39l-4.83 3.66a3.42 3.42 0 01-2.07.64zm-.9-2.68a1.16 1.16 0 00.75.41 1.08 1.08 0 00.83-.23l4.79-3.66a1.16 1.16 0 00.41-.75 1.08 1.08 0 00-.23-.83 1.16 1.16 0 00-.75-.41 1.08 1.08 0 00-.83.23l-4.79 3.66a1.17 1.17 0 00-.19 1.58z"
                                      fill="#7fbee8"></path>
                                <path d="M47.33 16.13v-2.56a1.11 1.11 0 00-1.13-1.13 1.13 1.13 0 00-1.13 1.13v24.62a1.395 1.395 0 11-2.79 0v-19a3 3 0 00-3-3H26.67a5.16 5.16 0 01-4.07-2 7.47 7.47 0 00-5.83-2.88H6.62a3 3 0 00-3 3v19.24a1.13 1.13 0 002.26 0V14.32a.76.76 0 01.75-.75h10.14a5.16 5.16 0 014.07 2 7.47 7.47 0 005.88 2.9H39.3a.76.76 0 01.75.75v19a4.36 4.36 0 00.26 1.39h-8.06a1.13 1.13 0 000 2.26h11.42a3.66 3.66 0 003.66-3.66V16.13z"
                                      fill="#454c55"></path>
                              </svg>
                          </span>
            </div>
            <div class="span9">
              <h2>JMedia - Best and Powerful Joomla Media Manager</h2>
              <p style="margin-bottom: 15px;">
                Empower your working experience using JMedia manager.
              </p>
              <ul style="margin: 0 0 15px 0;background: #f1f1f1;padding: 20px 20px 20px 50px;">
                <li>Comes with foldertree</li>
                <li>File upload</li>
                <li>Search &amp; filter by file type</li>
                <li>File preview</li>
                <li>Upload from folder and from remote url</li>
                <li>File permission</li>
              </ul>

              <p>
                <a class="btn-link" href="index.php?option=com_jmedia">Explore Now</a>
              </p>
            </div>
          </div>
        </div>
      </div>

        <?php
    }

    /**
     * enable necessary plugins to avoid bad experience
     */
    function enablePlugins()
    {
        $db  = JFactory::getDBO();
        $sql = "SELECT `element`,`folder` from `#__extensions` WHERE `type` = 'plugin' AND `folder` in ('content', 'system') AND `name` like '%jmedia%' AND `enabled` = '0'";
        $db->setQuery($sql);
        $plugins = $db->loadObjectList();
        if (count($plugins)) {
            foreach ($plugins as $key => $value) {

                $query = $db->getQuery(true);
                $query->update($db->quoteName('#__extensions'));
                $query->set($db->quoteName('enabled').' = '.$db->quote('1'));
                $query->where($db->quoteName('type').' = '.$db->quote('plugin'));
                $query->where($db->quoteName('element').' = '.$db->quote($value->element));
                $query->where($db->quoteName('folder').' = '.$db->quote($value->folder));
                $db->setQuery($query);
                $db->execute();

            }
        }

        if (JVERSION >= 4) {
            return true;
        }

        // publish module
        $module = JTable::getInstance('Module', 'JTable');
        $module->load(array('module' => 'mod_jmediaicons', 'published' => 1));
        if ($module->id) {
            return true;
        }

        $object              = array();
        $object['title']     = 'JMedia Filemanager';
        $object['module']    = 'mod_jmediaicons';
        $object['position']  = 'cpanel';
        $object['published'] = 1;
        $object['ordering']  = '-1';
        $object['access']    = 1;
        $object['client_id'] = 1;

        // Now store the module
        if ( ! $module->save($object)) {
            JFactory::getApplication()->enqueueMessage(JText::sprintf('JMedia module publishing error: %s', $module->getError()));
        }

        Pkg_JMediaInstallerScript::assignMenu($module->id);

        return true;

    }

    public static function assignMenu($pk)
    {
        // Now we need to handle the module assignments
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
                    ->select($db->quoteName('moduleid'))
                    ->from($db->quoteName('#__modules_menu'))
                    ->where($db->quoteName('moduleid').' = '.$pk);
        $db->setQuery($query);
        $menus = $db->loadObject();

        // Insert the new records into the table
        if ( ! isset($menus->moduleid)) {
            $query->clear()
                  ->insert($db->quoteName('#__modules_menu'))
                  ->columns(array($db->quoteName('moduleid'), $db->quoteName('menuid')))
                  ->values($pk.', '. 0);
            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }

}
