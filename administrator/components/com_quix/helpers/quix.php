<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text as JText;

JLoader::discover('QuixHelper', __DIR__);

defined('_JEXEC') or die;

/**
 * Quix core helper from admin
 *
 * @since  1.6
 */
class QuixHelper extends QuixHelperLegacy
{

    /**
     * Configure the Link bar.
     *
     * @param  string  $vName  string
     *
     * @return void
     * @since 3.0.0
     */
    public static function addSubmenu($vName = '')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_PAGES'),
            'index.php?option=com_quix&view=pages',
            $vName == 'pages'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_COLLECTIONS'),
            'index.php?option=com_quix&view=collections',
            $vName == 'collections'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_INTEGRATIONS'),
            'index.php?option=com_quix&view=integrations',
            $vName == 'integrations'
        );

        if (self::isFreeQuix()) {
            JHtmlSidebar::addEntry(
                JText::_('COM_QUIX_SIDEBAR_QUIXRANK'),
                'index.php?option=com_quix&view=rank',
                $vName == 'rank'
            );
        }

        if (self::isFreeQuix()) {
            JHtmlSidebar::addEntry(
                JText::_('COM_QUIX_SIDEBAR_OPTIMIZE'),
                'index.php?option=com_quix&view=optimize',
                $vName == 'optimize'
            );
        }
        if (self::isFreeQuix()) {
            JHtmlSidebar::addEntry(
                JText::_('COM_QUIX_SIDEBAR_AMP'),
                'index.php?option=com_quix&view=amp',
                $vName == 'amp'
            );
        }

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_DASHBOARD'),
            'index.php?option=com_quix&view=dashboard',
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_QUIX_SIDEBAR_HELP'),
            'index.php?option=com_quix&view=help',
            $vName == 'help'
        );
    }

    /**
     * @return mixed|null
     * @since 3.0.0
     */
    public static function checkUpdate()
    {
        // Get a database object.
        $db = JFactory::getDbo();

        // get extension id
        $query = $db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where($db->quoteName('type').' = '.$db->quote('package'))
                    ->where($db->quoteName('element').' = '.$db->quote('pkg_quix'));

        $db->setQuery($query);

        $extensionId = $db->loadResult();

        // get update_site_id
        $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__updates')
                    ->where($db->quoteName('extension_id').' = '
                            .$db->quote($extensionId))
                    ->where($db->quoteName('element').' = '.$db->quote('pkg_quix'))
                    ->where($db->quoteName('type').' = '.$db->quote('package'));
        $db->setQuery($query);

        return $db->loadObject();
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
        $user   = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_quix';

        $actions = [
            'core.admin',
            'core.manage',
            'core.create',
            'core.edit',
            'core.edit.own',
            'core.edit.state',
            'core.delete',
        ];

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
     * Get group name using group ID
     *
     * @param  int|null  $group_id  User group ID
     *
     * @return mixed group name if the group was found, null otherwise
     * @since 3.0.0
     */
    public static function getGroupNameByGroupId($group_id = null)
    {
        static $groupNameByGroupId;
        // Function has already run
        if ($groupNameByGroupId !== null) {
            return $groupNameByGroupId;
        }

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('title')
            ->from('#__usergroups')
            ->where('id = '.intval($group_id));

        $db->setQuery($query);
        $groupNameByGroupId = $db->loadResult();

        return $groupNameByGroupId;
    }

    /**
     * @throws \Exception
     * @since 2.0.0
     */
    public static function checkSystemPlugin()
    {
        $session    = JFactory::getSession();
        $cleanCache = $session->get('quix_install_cleancache', 0);
        if ($cleanCache) {
            QuixHelperCache::cleanCache();
        }

        $plugin = JPluginHelper::getPlugin('system', 'quix');
        if (isset($plugin->id) and $plugin->id) {
            return;
        }

        JFactory::getApplication()->enqueueMessage(
            JText::_('QUIX_SYSTEM_PLUGIN_MISSING_DESC'),
            JText::_('QUIX_SYSTEM_PLUGIN_MISSING_TITLE')
        );
    }

    /**
     * Revert back page version to 2, some cases page didnt update and migrate.
     *
     * @param  int  $id
     * @param  string  $type
     *
     * @return bool
     * @since 4.0.3
     */
    public static function reverseVersion(int $id, string $type): bool
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $fields = [
            $db->quoteName('builder_version').' = '.$db->quote('2.7.0'),
        ];

        $conditions = [
            $db->quoteName('id').' = '.$db->quote($id),
        ];

        if ($type === 'pages') {
            $query->update($db->quoteName('#__quix'))
                  ->set($fields)
                  ->where($conditions);
        } elseif ($type === 'collections') {
            $query->update($db->quoteName('#__quix_collections'))
                  ->set($fields)
                  ->where($conditions);
        }

        $db->setQuery($query);

        try {
            // Clear relevant cache
            return $db->execute();
        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Copy elements icon from
     * libraries/quixnxt/visual-builder/elements
     *
     * @since 4.0.5
     * @todo  next release
     */
    public static function elementIconsForJoomla4(): bool
    {
        if (file_exists(JPATH_SITE.'/media/quixnxt/images/elements/accordion.svg')) {
            return true;
        }

        $elements = glob(JPATH_SITE
                         .'/libraries/quixnxt/visual-builder/elements/*');
        $destPath = self::getJ4ElementIconsPath();
        if ( ! $destPath) {
            return false;
        }

        foreach ($elements as $element) {
            $elementName = pathinfo($element, PATHINFO_BASENAME);
            $iconPath    = $element.'/element.svg';
            File::copy($iconPath, $destPath.'/'.$elementName.'.svg');
        }

        return true;
    }

    public static function getJ4ElementIconsPath(): ?string
    {
        $path = JPATH_SITE.'/media/quixnxt/images/elements';
        if ( ! file_exists($path)) {
            if (Folder::create($path)) {
                return $path;
            }
        } else {
            return $path;
        }

        return null;
    }

    public static function updateComponentParams($key = '', $value = '')
    {
        if ($key && $value) {
            $db         = JFactory::getDbo();
            $query      = $db->getQuery(true);
            $component  = JComponentHelper::getComponent('com_quix');
            $data       = $component->getParams()->toArray();
            $data[$key] = $value;

            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('extension_id').' = '.$component->id
            );
            // Fields to update.
            $json   = json_encode($data);
            $fields = array(
                $db->quoteName('params').' = '.$db->quote($json)
            );

            $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
            $db->setQuery($query);

            try {
                // Clear relevant cache
                QuixHelperCache::cachecleaner('com_config');

                $db->execute();

                return true;
            } catch (RuntimeException $e) {
                return false;
            }

        }
    }

}
