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

class QuixHelperLegacy
{
    /**
     * to get update info
     * use layout to get alert structure
     *
     * @since      2.0.0
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getUpdateStatus() instead.
     */

    public static function getUpdateStatus()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getUpdateStatus() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getUpdateStatus();
    }

    /**
     * show warning
     * for free versions only
     *
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getFreeWarning() instead.
     * @since      2.0.0
     */

    public static function getFreeWarning()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getFreeWarning() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getFreeWarning();
    }

    /**
     * show warning
     * for free versions only
     *
     * @param  string  $medium
     * @param  string  $source
     *
     * @return string
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getBuyPro() instead.
     * @since      3.0.0
     */

    public static function getBuyPro($medium = 'button', $source = 'joomla-admin')
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getBuyPro instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getBuyPro($medium = 'button', $source = 'joomla-admin');
    }

    /**
     * @return string
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::proActivationMessage() instead.
     * @since      2.0.0
     */
    public static function proActivationMessage()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::proActivationMessage instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::proActivationMessage();
    }

    /**
     * @return string
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getAuthInfo() instead.
     * @since      2.0.0
     */
    public static function getAuthInfo()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getAuthInfo instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getAuthInfo();
    }

    /**
     * name: getFooterLayout
     *
     * @return string
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getFooterLayout() instead.
     * @since      2.0.0
     */
    public static function getFooterLayout()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getFooterLayout instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getFooterLayout();
    }

    /**
     * show pro elements
     * for free versions only
     *
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getProElementBanner() instead.
     * @since      3.0.0
     */
    public static function getProElementBanner()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getProElementBanner instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getProElementBanner();

    }

    /**
     * show pro elements
     * for free versions only
     *
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getWelcomeLayout() instead.
     *
     * @since      3.0.0
     */

    public static function getWelcomeLayout()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getWelcomeLayout instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getWelcomeLayout();
    }

    /**
     * to get php warning
     * we require at least php 5.4
     *
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getPHPWarning() instead.
     *
     * @since      2.0.0
     */
    public static function getPHPWarning()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getPHPWarning instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getPHPWarning();
    }


    /**
     * check filemanager status
     * some special permission required for filemanager
     *
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::checkFileManager() instead.
     *
     * @since      3.0.0
     */

    public static function checkFileManager()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::checkFileManager instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::checkFileManager();
    }

    /**
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::renderSysMessage() instead.
     * @since      3.0.0
     */
    public static function randerSysMessage()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::renderSysMessage instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::renderSysMessage();
    }

    /**
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::webpCheck() instead.
     * @since      2.0.0
     */
    public static function webpCheck()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::webpCheck instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::webpCheck();
    }

    /**
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::getSystemInfo() instead.
     * @since      2.0.0
     */
    public static function getSystemInfo()
    {
        \JLog::add('The method is deprecated, use QuixHelperLayout::getSystemInfo instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::getSystemInfo();
    }

    /**
     * @param  bool  $webp
     *
     * @return string
     * @deprecated 3.0  This method is deprecated, use QuixHelperLayout::renderGlobalMessage() instead.
     * @since      2.0.0
     */
    public static function renderGlobalMessage($webp = false): string
    {
        \JLog::add('This method is deprecated, use QuixHelperLayout::renderGlobalMessage() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLayout::renderGlobalMessage();
    }

    /**
     * @deprecated 3.0  This method is deprecated, use QuixHelperCache::cleanCache() instead.
     * @since      1.0.0
     */
    public static function cleanCache()
    {
        \JLog::add('The method is deprecated, use QuixHelperCache::cleanCache() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperCache::cleanCache();
    }

    /**
     * @deprecated 3.0  This method is deprecated, use QuixHelperCache::purgePageCache() instead.
     * @since      1.0.0
     */
    public static function purgePageCache()
    {
        \JLog::add('The method is deprecated, use QuixHelperCache::purgePageCache() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperCache::purgePageCache();
    }

    /**
     * @param $type
     * @param $id
     *
     * @deprecated 3.0  This method is deprecated, use QuixHelperCache::purgePageCacheByID() instead.
     * @since      1.0.0
     */
    public static function purgePageCacheByID($type, $id)
    {
        \JLog::add('The method is deprecated, use QuixHelperCache::purgePageCacheByID($type, $id) instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperCache::purgePageCacheByID($type, $id);
    }

    /**
     * @param  string  $group
     * @param  int  $client_id
     *
     * @throws \Exception
     * @deprecated 3.0  This method is deprecated, use QuixHelperCache::cachecleaner($group = 'com_quix', $client_id = 0) instead.
     * @since      1.0.0
     */
    public static function cachecleaner($group = 'com_quix', $client_id = 0)
    {
        \JLog::add('The method is deprecated, use QuixHelperCache::cachecleaner($group = "com_quix", $client_id = 0) instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperCache::cachecleaner($group = 'com_quix', $client_id = 0);
    }

    /**
     * check and update icons list from server
     *
     * @return    void
     * @throws \Exception
     * @deprecated 3.0  This method is deprecated, use QuixHelperIcon::getUpdateIconsList() instead.
     *
     * @since      2.0
     */
    public static function getUpdateIconsList()
    {
        \JLog::add('This method is deprecated, use QuixHelperIcon::getUpdateIconsList() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperIcon::getUpdateIconsList();
    }

    /**
     * @deprecated 3.0  This method is deprecated, use QuixHelperIcon::updateFlatIcons() instead.
     * @since      2.0.0
     */
    public static function updateFlatIcons()
    {
        \JLog::add('This method is deprecated, use QuixHelperIcon::updateFlatIcons() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperIcon::updateFlatIcons();
    }

    /**
     * @deprecated 3.0  This method is deprecated, use QuixHelperIcon::getServerHashForIcon() instead.
     * @since      2.0.0
     */
    public static function getServerHashForIcon()
    {
        \JLog::add('This method is deprecated, use QuixHelperIcon::getServerHashForIcon() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperIcon::getServerHashForIcon();
    }

    /**
     * @deprecated 3.0  This method is deprecated, use QuixHelperIcon::getUpdateGoogleFontsList() instead.
     * @since      2.0.0
     */
    public static function getUpdateGoogleFontsList()
    {
        \JLog::add('This method is deprecated, use QuixHelperIcon::getUpdateGoogleFontsList() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperIcon::getUpdateGoogleFontsList();
    }

    /**
     * autoVerifyLicense
     * Method never used, seems duplicate check. kept to remove in future
     *
     * @return boolean
     * @deprecated 3.0  This method is deprecated, use QuixHelperLicense::verifyApiKey() instead.
     * @since      2.0.0
     */
    public static function autoVerifyLicense()
    {
        $free = self::isFreeQuix();
        $pro  = self::isProActivated();
        if ( ! $free && ! empty($pro) && $pro) {
            // its activated already, now recheck
            self::hasCredentials();
        }

        return false;
    }

    /**
     * Verifies the api key
     *
     * @param $username
     * @param $key
     *
     * @return false|mixed
     * @since      2.1.0
     * @deprecated 3.0  This method is deprecated, use QuixHelperLicense::verifyApiKey() instead.
     */
    public static function verifyApiKey($username, $key)
    {
        \JLog::add('The method is deprecated, use QuixHelperLicense::verifyApiKey() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLicense::verifyApiKey($username, $key);
    }

    /**
     * @return \Exception|string
     * @throws \Exception
     * @deprecated 3.0  This method is deprecated, use QuixHelperLicense::parseLicenseResponse() instead.
     * @since      2.0.0
     */
    public static function verifyLicense()
    {
        \JLog::add('The method is deprecated, use QuixHelperLicense::parseLicenseResponse() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLicense::parseLicenseResponse();
    }

    /**
     * @param $data
     *
     * @return array|false[]
     * @deprecated 3.0  This method is deprecated, use QuixHelperLicense::validateLicense() instead.
     * @since      2.0.0
     */
    public static function getValidLicense($data)
    {
        \JLog::add('The method is deprecated, use QuixHelperLicense::validateLicense() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLicense::validateLicense($data);
    }

    /**
     * @return mixed|null
     * @deprecated 3.0  This method is deprecated, use QuixHelperLicense::isProActivated() instead.
     * @since      2.0.0
     */
    public static function isProActivated()
    {
        \JLog::add('The method is deprecated, use QuixHelperLicense::isProActivated() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLicense::isProActivated();
    }

    /**
     * Method isFreeQuix
     *
     * @return boolean
     * @deprecated 3.0  This method is deprecated, use QuixHelperLicense::isPro() instead.
     * @since      2.0.0
     */
    public static function isFreeQuix()
    {
        \JLog::add('The method is deprecated, use QuixHelperLicense::isPro() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLicense::isPro() === false;
    }

    /**
     * get version
     *
     * @deprecated 3.0  This method is deprecated, use QuixHelperLicense::getVersion() instead.
     * @since      2.0.0
     */
    public static function getQuixVersion()
    {
        \JLog::add('The method is deprecated, use QuixHelperLicense::getVersion() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLicense::getVersion();
    }

    /**
     * check for Credentials
     *
     * @throws \Exception
     * @since      2.0.0
     * @deprecated 3.0  This method is deprecated, use QuixHelperLicense::isPro() instead.
     */
    public static function hasCredentials()
    {
        \JLog::add('The method is deprecated, use QuixHelperLicense::hasCredentials() instead.', \JLog::WARNING, 'deprecated');

        return QuixHelperLicense::hasCredentials();
    }

    /**
     * askreview
     *
     * @throws \Exception
     * @since      2.0.0
     * @deprecated 4.2 removed
     */
    public static function askreview()
    {
        //
    }
}
