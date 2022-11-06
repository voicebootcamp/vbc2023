<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access

defined('_JEXEC') or die;

class QuixHelperCache
{

    /**
     * @since      3.0.0
     */
    public static function cleanCache()
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.path');

        if (JFolder::exists(JPATH_ROOT.'/media/quix/css')) {
            $cssFiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/css');
            array_map(
                function ($file) {
                    if ($file === 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT.'/media/quix/css/'.$file);
                },
                $cssFiles
            );
        }

        if (JFolder::exists(JPATH_ROOT.'/media/quix/js')) {
            $jsFiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/js');
            array_map(
                function ($file) {
                    if ($file === 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT.'/media/quix/js/'.$file);
                },
                $jsFiles
            );
        }

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/builder')) {
            $jsFiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/builder');
            array_map(
                function ($file) {
                    if ($file == 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT.'/media/quix/frontend/builder/'.$file);
                },
                $jsFiles
            );
        }

        // Clear relevant cache
        QuixHelperCache::cachecleaner('com_quix');
        QuixHelperCache::cachecleaner('mod_quix');
        QuixHelperCache::cachecleaner('lib_quix');
        QuixHelperCache::cachecleaner('quix-twig');
    }

    /**
     * @since      3.0.0
     */
    public static function purgePageCache()
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.path');

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/css')) {
            $cssfiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/css');
            array_map(
                function ($file) {
                    if ($file == 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT.'/media/quix/frontend/css/'.$file);
                },
                $cssfiles
            );
        }

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/js')) {
            $jsfiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/js');
            array_map(
                function ($file) {
                    if ($file == 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT.'/media/quix/frontend/js/'.$file);
                },
                $jsfiles
            );
        }

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/html')) {
            $jsfiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/html');
            array_map(
                function ($file) {
                    if ($file == 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT.'/media/quix/frontend/html/'.$file);
                },
                $jsfiles
            );
        }

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/json')) {
            $jsfiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/json');
            array_map(
                function ($file) {
                    if ($file == 'index.html') {
                        return;
                    }
                    JFile::delete(JPATH_ROOT.'/media/quix/frontend/json/'.$file);
                },
                $jsfiles
            );
        }
    }

    /**
     * @param $type
     * @param $id
     *
     * @since      3.0.0
     */
    public static function purgePageCacheByID($type, $id)
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.path');

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/css')) {
            $cssfiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/css');
            array_map(
                function ($file) use ($type, $id) {
                    if (strpos($file, "$type-$id-frontend") !== false) {
                        JFile::delete(JPATH_ROOT.'/media/quix/frontend/css/'.$file);
                    }
                },
                $cssfiles
            );
        }

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/js')) {
            $jsfiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/js');
            array_map(
                function ($file) use ($type, $id) {
                    if (strpos($file, "$type-$id-frontend") !== false) {
                        JFile::delete(JPATH_ROOT.'/media/quix/frontend/js/'.$file);
                    }
                },
                $jsfiles
            );
        }

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/html')) {
            $jsfiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/html');
            array_map(
                function ($file) use ($type, $id) {
                    if (strpos($file, "$type-$id-frontend") !== false) {
                        JFile::delete(JPATH_ROOT.'/media/quix/frontend/html/'.$file);
                    }
                },
                $jsfiles
            );
        }

        if (JFolder::exists(JPATH_ROOT.'/media/quix/frontend/json')) {
            $jsfiles = (array) JFolder::files(JPATH_ROOT.'/media/quix/frontend/json');
            array_map(
                function ($file) use ($type, $id) {
                    if (strpos($file, "$type-$id-frontend") !== false) {
                        JFile::delete(JPATH_ROOT.'/media/quix/frontend/json/'.$file);
                    }
                },
                $jsfiles
            );
        }
    }

    /**
     * @param  string  $group
     * @param  int  $client_id
     *
     * @throws \Exception
     * @since      3.0.0
     */
    public static function cachecleaner($group = 'com_quix', $client_id = 0)
    {
        $conf = \JFactory::getConfig();

        try {
            $options = [
                'defaultgroup' => $group,
                'cachebase'    => JPATH_ADMINISTRATOR.'/cache',
                'result'       => true,
            ];
            /** @var \JCacheControllerCallback $cache */
            $cache = \JCache::getInstance('callback', $options);
            $cache->clean();

            $options = [
                'defaultgroup' => $group,
                'cachebase'    => $conf->get('cache_path', JPATH_SITE.'/cache'),
                'result'       => true,
            ];

            $cache = \JCache::getInstance('callback', $options);
            $cache->clean();
        } catch (\JCacheException $exception) {
            $options['result'] = false;
        }

        // Trigger the onContentCleanCache event.
        \JFactory::getApplication()->triggerEvent('onContentCleanCache', $options);
    }
}
