<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access
use Joomla\CMS\Language\Text as JText;

defined('_JEXEC') or die;

class QuixHelperIcon
{


    /**
     * check and update icons list from server
     *
     * @return bool
     *
     * @throws \Exception
     * @since    3.0
     */
    public static function getUpdateIconsList()
    {
        // checked_flaticon_quix, quix_flatIcon_latest
        $session = JFactory::getSession();

        // test enable
        // $latest = $session->set('quix_flatIcon_latest', false);
        $latest = $session->get('quix_flatIcon_latest', false);

        if ( ! $latest) {
            if (JDEBUG) {
                $profiler = new JProfiler();
            }

            // do the operation
            // 1. get local hash from cache
            $cache   = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE.DIRECTORY_SEPARATOR.'cache']);
            $cacheid = 'quix_flaticons_hash';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000//
            // $cache = self::getCache();

            // get localhash
            $localhash = $cache->get($cacheid);
            // print_r($localhash);die;

            // now match the hash and get latest file
            if ( ! $localhash or empty($localhash)) {
                // update fonts file
                self::updateFlatIcons();

                //get serverHash and update locals
                $localhash = self::getServerHashForIcon();
                $cache->store($localhash, $cacheid);

                // we have latest version
                $session->set('quix_flatIcon_latest', true);
            } else {
                // we have local hash already
                //get serverHash
                $serverHash = self::getServerHashForIcon();

                // get serverHash and verify
                if ($serverHash == $localhash) {
                    //setSession  update about quix_flatIcon_latest
                    // we have latest version
                    $session->set('quix_flatIcon_latest', true);

                    return true;
                } else {
                    // update fonts file
                    self::updateFlatIcons();

                    // updateHash local with server hash
                    $cache->store($serverHash, $cacheid);

                    // we have latest version
                    $session->set('quix_flatIcon_latest', true);
                }
            }

            if (JDEBUG) {
                $profiler->mark('After icon generation');
            }

            $cache->setCaching(\JFactory::getApplication()->get('caching'));
        }

        return true;
    }

    /**
     * @since      3.0.0
     */
    public static function updateFlatIcons()
    {
        // need to update
        // so, get the icons list from server
        $icons = QuixFrontendHelper::getFlatIconsJSONfromServer();

        // store them
        QuixFrontendHelper::saveOutputIconsJSON($icons);

        return true;
    }

    /**
     * @since      3.0.0
     */
    public static function getServerHashForIcon()
    {
        $config    = JComponentHelper::getParams('com_quix');
        $api_https = $config->get('api_https', 1);

        // absolute url of list json
        $url = ($api_https ? 'https' : 'http').'://getquix.net/index.php?option=com_quixblocks&view=flaticons&format=json&hash=true';

        $process = true;
        // Get the handler to download the blocks
        try {
            $http   = new JHttp();
            $result = $http->get($url);

            if ($result->code != 200 && $result->code != 310) {
                $exception = new Exception(JText::_('COM_QUIX_SERVER_RESPONSE_ERROR'));
                echo new JResponseJSON($exception);
            }

            $json = json_decode($result->body);

            return $json->data;
        } catch (RuntimeException $e) {
            $exception = new Exception($e->getMessage());

            return new JResponseJSON($exception);
        }
    }

    /**
     * @since      3.0.0
     */
    public static function getUpdateGoogleFontsList()
    {
        $session = JFactory::getSession();
        $latest  = $session->get('quix_googlefonts_latest', false);

        if ( ! $latest) {
            // do the operation
            // 1. get local hash from cache
            $cache   = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE.DIRECTORY_SEPARATOR.'cache']);
            $cacheid = 'quix_googlefonts';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);

            // get localhash
            $localdata = $cache->get($cacheid);
            $result    = false;
            // now match the hash and get latest file
            if ( ! $localdata or empty($localdata)) {
                // update fonts file
                $result = QuixFrontendHelper::getGoogleFontsJSONfromServer();
            }

            // we have latest version
            $session->set('quix_googlefonts_latest', $result);
            $cache->setCaching(\JFactory::getApplication()->get('caching'));
        }

        return true;
    }
}
