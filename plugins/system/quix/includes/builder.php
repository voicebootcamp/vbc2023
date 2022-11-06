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
 * Class QuixSystemHelperBuilder
 * Builder related preparation
 * @version 3.0.0
 * @since 3.0.0
 */
class QuixSystemHelperBuilder
{
    public $params;

  /**
   * prepareBuilderView
   * Called to prepare builder view
   *
   * @param $params
   *
   * @throws \Exception
   * @since 3.0.0
   */
    public function prepareBuilderView($params)
    {
        JFactory::getApplication()->input->set('jchbackend', 1);

        $fix_adminToolsFirewall = $params->get('fix_admintoolsfirewall', 1);
        if ($fix_adminToolsFirewall) {
            plgSystemQuix::fixAdminTools();
        }

        $fix_rocketLoader = $params->get('fix_rocketLoader', 1);
        if ($fix_rocketLoader) {
            $this->updateAllScriptAsyncFalse();
        }
    }

    public function loadCustomAssets($params)
    {
        if ($params->get('load_global', 0)) {
            plgSystemQuix::addQuixTrapCSSFrontend();
        }

        if ($params->get('init_wow', 1)) {
            JHtml::_('jquery.framework');
            $version = 'ver=' . QUIXNXT_VERSION;
            JFactory::getDocument()->addScript(JUri::root(true) . '/libraries/quixnxt/assets/js/wow.js?' . $version);
        }

        // apply gantry fix for offCanvas toggle
        if ($params->get('gantry_fix_offcanvas', 0) && class_exists('Gantry5\Loader')) {
            JFactory::getDocument()->addScriptDeclaration('function stopGantryQuixEvent(e){e.stopPropagation()}function preventGantryQuixDef(e){e.preventDefault()}document.addEventListener("DOMContentLoaded",function(e){var t=document.getElementsByClassName("g-offcanvas-toggle");/Mobi/.test(navigator.userAgent)?t[0].addEventListener("click",stopGantryQuixEvent,!1):t[0].addEventListener("click",preventGantryQuixDef,!1)});');
        }

        // apply apply fix for dropdown
        if ($params->get('fix_bootstrap_dropdown', 0)) {
            JFactory::getDocument()->addScriptDeclaration(
                "jQuery(document).ready(function(){jQuery('.dropdown-toggle').dropdown();});"
            );
        }
    }

    public function loadIECustomFix()
    {
        if ($params->get('fix_internetExplorer', 0)) {
            JFactory::getDocument()->addStyleDeclaration(
                '@media screen and (min-width: 0\0), screen\0 {
          .qx-column,.qx-col-wrap{flex: 1;}
          img {max-width: 100%;width:100%;width: auto\9;height: auto;}
          figure{display:block;}

          .qx-inner.classic .qx-row {overflow: hidden;}
          .qx-inner.classic .qx-element {animation-name: unset !important;}
          .qx-inner.classic .qx-element:hover {animation-name: unset !important;}
        }'
            );
        }
    }

    public function updateAllScriptAsyncFalse()
    {
        $body = JFactory::getApplication()->getBody();
        $body = str_replace('<script', '<script data-cfasync="false"', $body); // worked

        JFactory::getApplication()->setBody($body);
    }

    public function forceQuixAssetsPreload($params)
    {
        $version = 'ver=' . QUIXNXT_VERSION;
        $app = JFactory::getApplication();

        $format = $app->input->get('format', 'html', 'string');
        if ($format !== 'html') {
            return;
        }

        $preloadAssets = $params->get('preload', false);
        $rootTrue = JUri::root(true);
        $root = JUri::root();

        // preload scripts
        $preload = '';
        if ($preloadAssets) {
            $preload = <<<HTML
    <link rel="preconnect" href="$root">
    <link rel="dns-prefetch" href="$root">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://ajax.googleapis.com">
HTML;
        }

        $body = $app->getBody();
        $body = str_replace('</title>', '</title>' . $preload, $body);

        $app->setBody($body);
    }
}
