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

class QuixHelperLayout
{
    /**
     * to get update info
     * use layout to get alert structure
     *
     * @since 3.0.0
     */
    public static function getUpdateStatus(): string
    {
        $update = QuixHelper::checkUpdate();
        if (isset($update->update_id) && $update->update_id) {
            $credentials = QuixHelper::hasCredentials();
            // Instantiate a new JLayoutFile instance and render the layout
            $layout = new JLayoutFile('toolbar.update');

            return $layout->render(['info' => $update, 'credentials' => $credentials]);
        }

        return '';
    }

    /**
     * show warning
     * for free versions only
     *
     * @since 3.0.0
     */

    public static function getFreeWarning()
    {
        jimport('joomla.form.form');
        $form = simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR.'/quix.xml');
        if (isset($form->tag)) {
            if ($form->tag !== 'pro') {
                JFactory::getDocument()->addScriptDeclaration('(function($){window.QuixVersion = "free";})(jQuery);');
                $layout = new JLayoutFile('toolbar.freenotice');

                return $layout->render([]);
            }
        }

        return '';
    }

    /**
     * show warning
     * for free versions only
     *
     * @param  string  $medium
     * @param  string  $source
     *
     * @return string
     * @since 3.0.0
     */

    public static function getBuyPro($medium = 'button', $source = 'joomla-admin')
    {
        jimport('joomla.form.form');
        $form = simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR.'/quix.xml');
        if (isset($form->tag)) {
            if ($form->tag !== 'pro') {
                $layout = new JLayoutFile('toolbar.getpro');

                return $layout->render(['source' => $source, 'medium' => $medium]);
            }
        }

        return '';
    }

    public static function proActivationMessage()
    {
        $isFree = QuixHelperLicense::isPro() === false;
        if ($isFree) {
            return '';
        }

        $credentials = QuixHelperLicense::hasCredentials();

        if (empty($credentials) or empty($credentials->username) or empty($credentials->key) or ! $credentials->activated) {
            $layout = new JLayoutFile('toolbar.authorise');

            return $layout->render([]);
        }

        return '';
    }

    public static function getAuthInfo()
    {
        $update = QuixHelper::checkUpdate();
        if (isset($update->update_id) && $update->update_id) {
            $credentials = QuixHelper::hasCredentials();
            // Instantiate a new JLayoutFile instance and render the layout
            $layout = new JLayoutFile('toolbar.update');

            return $layout->render(['info' => $update, 'credentials' => $credentials]);
        }

        return '';
    }

    /**
     * name: getFooterLayout
     *
     * @return string
     * @since 3.0.0
     */
    public static function getFooterLayout()
    {
        $layout = new JLayoutFile('blocks.footer');

        return $layout->render(['free' => QuixHelperLicense::isPro() === false]);
    }

    /**
     * show pro elements
     * for free versions only
     *
     * @since 3.0.0
     */

    public static function getProElementBanner()
    {
        jimport('joomla.form.form');
        $form = simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR.'/quix.xml');
        if ( ! empty($form->tag)) {
            if ($form->tag !== 'pro') {
                $layout = new JLayoutFile('blocks.elements');

                return $layout->render([]);
            }
        }

        return '';
    }

    /**
     * show pro elements
     * for free versions only
     *
     * @since 3.0.0
     */

    public static function getWelcomeLayout()
    {
        $layout = new JLayoutFile('blocks.welcome');

        return $layout->render([]);
    }

    /**
     * Render toolbar with Notification
     *
     * @param  string  $currentPage
     *
     * @return string
     * @since 3.0.0
     */
    public static function getToolbar(string $currentPage): string
    {
        $layout = new JLayoutFile('blocks.toolbar');

        return $layout->render(['active' => $currentPage]);
    }

    /**
     * to get php warning
     * we require at least php 5.4
     *
     * @since 3.0.0
     */

    public static function getPHPWarning()
    {
        if (version_compare(phpversion(), '7.0', '<')) {
            // Instantiate a new JLayoutFile instance and render the layout
            $layout = new JLayoutFile('toolbar.phpwarning');

            return $layout->render([]);
        }

        return '';
    }

    public static function renderSysMessage()
    {
        $layout = new JLayoutFile('toolbar.message');

        return $layout->render([]);
    }

    public static function webpCheck()
    {
        if ( ! function_exists('imagewebp')) {
            $layout = new JLayoutFile('toolbar.webp');

            return $layout->render([]);
        }

        return '';
    }


    public static function renderGlobalMessage($webp = false): string
    {
        $html   = [];
        $html[] = '<div class="qx-position-relative qx-text-default"><div>';

        /**
         * new style
         */
        $html[] = self::getUpdateStatus();
        /**
         * new style
         * //  */
        $html[] = self::getPHPWarning();
        //
        // /**
        //  * new style
        //  */
        $html[] = self::proActivationMessage();
        //
        // $html[] = self::renderSysMessage();
        // $html[] = $webp ? self::webpCheck() : '';
        $html[] = '</div></div>';


        return implode(' ', $html);
    }


    /**
     * check fileManager status
     * some special permission required for fileManager
     *
     * @since 3.0.0
     */
    public static function checkFileManager()
    {
        try {
            // Create an instance of a default JHttp object.
            $http = new JHttp();
            // Invoke the HEAD request.
            $response = $http->head(JUri::root().'media/quix/filemanager/index.php');

            // The response code is included in the "code" property.
            if ($response->code == 403) {
                // show warning or fix guide:
                // Instantiate a new JLayoutFile instance and render the layout
                $layout = new JLayoutFile('toolbar.filemanagerguide');

                return $layout->render([]);
            }
        } catch (Exception $e) {
            // nothing to show now, lets ignore
            return '';
        }

        return '';
    }


    public static function getSystemInfo()
    {
        $info                    = [];
        $info['php_version']     = phpversion();
        $info['gd_info']         = function_exists('gd_info');
        $info['curl_support']    = extension_loaded('curl');
        $info['ctype_support']   = extension_loaded('ctype');
        $info['fileinfo']        = extension_loaded('fileinfo');
        $info['memory_limit']    = ini_get('memory_limit');
        $info['postSize']        = ini_get('post_max_size');
        $info['max_execution']   = ini_get('max_execution_time');
        $info['allow_url_fopen'] = ini_get('allow_url_fopen');
        $info['cache_writable']  = is_writable(JPATH_CACHE);

        return $info;
    }
}
