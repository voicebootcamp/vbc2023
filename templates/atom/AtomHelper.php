<?php
/**
 * @package     Quix - Joomla Page builder
 * @subpackage  Templates.Atom
 *
 * @copyright   Copyright (C) 2005 - 2020 ThemeXpert, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use QuixNxt\Utils\Asset;

defined('_JEXEC') or die;

/**
 * Class AtomHelper
 *
 * @since 1.0.0
 * @quix  3.0.0
 */
class AtomHelper
{
    static $_scripts = [];
    static $_script = [];
    static $isEditorLayout = null;
    static $loadBootstrap = false;
    static $loadCoreJs = false;
    static $reduceHttp = false;

    /**
     * unset jquery to embed later and add custom code to head
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public static function prepareHead(): bool
    {
        $app    = JFactory::getApplication();
        // if(JVERSION >= 4 && $app->input->get('option') !== 'com_quix'){
        //     JFactory::getApplication()->enqueueMessage(JText::_('COM_QUIX_ATOM_ONLY_J3_TEMP'), 'warning');
        // }

        if (self::isEditorLayout()) {
            return true;
        }

        $params = $app->getTemplate(true)->params;
        if ($params->get('reduceHttp', false)) {
            self::$reduceHttp = true;
        }

        $doc = JFactory::getDocument();

        if (self::$reduceHttp) {
            unset($doc->_styleSheets[JUri::root().'?quix-asset=/css/quix-core.css&ver='.QUIXNXT_VERSION]);
            unset($doc->_scripts[JUri::root().'?quix-asset=/js/quix.vendor.js&ver='.QUIXNXT_VERSION]);
        }

        unset($doc->_scripts[JUri::root(true).'/media/jui/js/jquery.js']);
        unset($doc->_scripts[JUri::root(true).'/media/jui/js/jquery.js?'.JFactory::getDocument()->getMediaVersion()]);
        unset($doc->_scripts[JUri::root(true).'/media/jui/js/jquery.min.js']);
        unset($doc->_scripts[JUri::root(true).'/media/jui/js/jquery.min.js?'.JFactory::getDocument()->getMediaVersion()]);
        unset($doc->_scripts[JUri::root(true).'/media/jui/js/jquery-migrate.js']);
        unset($doc->_scripts[JUri::root(true).'/media/jui/js/jquery-migrate.min.js']);
        unset($doc->_scripts[JUri::root(true).'/media/jui/js/jquery-noconflict.js']);

        if (isset($doc->_scripts[JUri::root(true).'/media/jui/js/bootstrap.js']) || isset($doc->_scripts[JUri::root(true).'/media/jui/js/bootstrap.min.js'])) {
            self::$loadBootstrap = true;
            if (self::$reduceHttp) {
                unset($doc->_scripts[JUri::root(true).'/media/jui/js/bootstrap.js']);
                unset($doc->_scripts[JUri::root(true).'/media/jui/js/bootstrap.min.js']);
            }
        }
        if (isset($doc->_scripts[JUri::root(true).'/media/system/js/core.js'])) {
            self::$loadCoreJs = true;
            if (self::$reduceHttp) {
                unset($doc->_scripts[JUri::root(true).'/media/system/js/core.js']);
            }
        }

        self::addCodes();

        return true;
    }

    /**
     * Preload quix, bootstrap and core js
     *
     * @throws \Exception
     * @since 4.0.0
     */
    public static function preloadAssets()
    {
        $app    = JFactory::getApplication();
        $params = $app->getTemplate(true)->params;

        $debug        = JFactory::getConfig()->get('debug');
        $root         = JUri::root();
        $qVersion     = QUIXNXT_VERSION;
        $bootstrap    = $root.'media/jui/js/bootstrap.js?'.JFactory::getDocument()->getMediaVersion();
        $bootstrapMin = $root.'media/jui/js/bootstrap.min.js?'.JFactory::getDocument()->getMediaVersion();
        $coreJs       = $root.'media/system/js/core.js?'.JFactory::getDocument()->getMediaVersion();
        $quixCore     = $root.'?quix-asset=/css/quix-core.css&ver='.$qVersion;

        $links = array();
        if ($params->get('http2_push', false)) {
            $links[] = "<link rel=\"preconnect\" href=\"$root\">";
            $links[] = "<link rel=\"dns-prefetch\" href=\"$root\">";
            $links[] = "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\">";
            $links[] = "<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">";
            $links[] = "<link rel=\"preconnect\" href=\"https://ajax.googleapis.com\">";
        }

        if ( ! self::$reduceHttp) {
            JFactory::getDocument()->addStyleSheet($quixCore);

            $links[] = "<link rel=\"preload\" href=\"$quixCore\" as=\"style\">";

            if (self::$loadBootstrap) {
                $links[] = $debug ? "<link rel=\"preload\" href=\"$bootstrap\" as=\"script\">" : "<link rel=\"preload\" href=\"$bootstrapMin\" as=\"script\">";
            }

            if (self::$loadCoreJs) {
                $links[] = "<link rel=\"preload\" href=\"$coreJs\" as=\"script\">";
            }
        } else {
            self::loadQuixCss();
        }

        echo implode("\n", $links);
    }


    /**
     * @param  bool  $raw  ;
     * @param  bool  $inline
     *
     * @since 3.0.0
     */
    public static function loadQuixCss(bool $raw = true, bool $inline = false)
    {
        if (!$raw && self::$reduceHttp && JFile::exists(JPATH_SITE.'/media/quixnxt/css/quix-core.css')) {
            $content = file_get_contents(JPATH_SITE.'/media/quixnxt/css/quix-core.css');
            if ($inline) {
                echo '<style>'.$content.'</style>';
            } else {
                JFactory::getDocument()->addStyleDeclaration($content);
            }
        } else {
            JFactory::getDocument()->addStyleSheet(JUri::root().'?quix-asset=/css/quix-core.css&ver='.QUIXNXT_VERSION);
        }
    }

    /**
     * loads quix vendor
     *
     * @param  bool  $force
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public static function loadQuixVendorJs(): bool
    {

        if (self::isEditorLayout()) { // ! self::$reduceHttp ||
            return '';
        }

        $app    = JFactory::getApplication();
        $params = $app->getTemplate(true)->params;

        if (self::$reduceHttp && JFile::exists(JPATH_SITE.'/media/quixnxt/js/quix.vendor.js')) {
            $content = file_get_contents(JPATH_SITE.'/media/quixnxt/js/quix.vendor.js');
            echo "<script type=\"text/javascript\" id=\"quix-vendor\" defer>$content</script>";
        } else {
            $root     = JUri::root();
            $qVersion = Asset::getVersion(); // QUIXNXT_VERSION;
            echo "<script type=\"text/javascript\" src=\"$root?quix-asset=/js/quix.vendor.js&ver=$qVersion\" defer></script>";
        }

        return true;
    }

    /**
     * print critical css
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public static function addCriticalCss(): bool
    {
        $content = file_get_contents(__DIR__.'/css/critical.min.css');
        echo "<style type=\"text/css\" id=\"quix-critical-css\">$content</style>";

        return true;
    }

    /**
     * Flush jquery, migrate,noConflict
     *
     * @since 4.0.0
     */
    public static function loadJQuery()
    {
        if (JFile::exists(JPATH_SITE.'/media/jui/js/jquery.min.js')) {
            $content  = file_get_contents(JPATH_SITE.'/media/jui/js/jquery.min.js');
            $content2 = file_get_contents(JPATH_SITE.'/media/jui/js/jquery-migrate.min.js');
            $content3 = "jQuery.noConflict();";
            echo "<script type=\"text/javascript\" id=\"go-jquery-min\">$content$content2$content3</script>\n";
        }
        if (self::$reduceHttp) {
            if (self::$loadBootstrap) {
                $content = file_get_contents(JPATH_SITE.'/media/jui/js/bootstrap.min.js');
                echo "<script type=\"text/javascript\" id=\"go-bootstrap-min\">$content</script>\n";
            }

            if (self::$loadCoreJs) {
                $content = file_get_contents(JPATH_SITE.'/media/system/js/core.js');
                echo "<script type=\"text/javascript\" id=\"go-core-min\">$content</script>";
            }
        }
    }

    /**
     * @return null
     * @throws \Exception
     * @since 3.0.0
     */
    public static function isEditorLayout(): ?bool
    {
        if (self::$isEditorLayout !== null) {
            return self::$isEditorLayout;
        }

        return self::$isEditorLayout = defined('QUIX_BUILDER_VIEW');
    }

    /**
     * @return string
     * @throws \Exception
     * @since 3.0.0
     */
    public static function getSiteLogo(): string
    {
        $app    = JFactory::getApplication();
        $params = $app->getTemplate(true)->params;

        if ($params->get('logo_type', 'image') === 'image' && $params->get('logoFile')) {
            $logo = '<img data-width="100" data-height="100" data-qx-img="" data-src="'.htmlspecialchars(JUri::root().$params->get('logoFile'),
                    ENT_QUOTES).'" alt="'.self::getSiteName().'" class="lazyload blur-up"/>';
        } elseif ($params->get('logo_type', 'image') === 'svg' && $params->get('logoSvg')) {
            $logo = $params->get('logoSvg');
        } elseif ($params->get('sitetitle')) {
            $logo = '<span class="site-title" title="'.self::getSiteName().'">'.htmlspecialchars($params->get('sitetitle'), ENT_COMPAT, 'UTF-8').'</span>';
        } else {
            $logo = '<span class="site-title" title="'.self::getSiteName().'">'.self::getSiteName().'</span>';
        }

        return $logo;

    }

    /**
     * Get template color
     * @since 3.0.0
     */
    public static function getTemplateColor()
    {
        $app    = JFactory::getApplication();
        $params = $app->getTemplate(true)->params;
        $doc    = JFactory::getDocument();

        if ($params->get('layout') == 1) {
            if ($params->get('layoutBackgroundcolor')) {
                $doc->addStyleDeclaration(
                    '.qx-page-container {background-color: '.$params->get('layoutBackgroundcolor').';}@media (min-width: 1200px) {.qx-boxed-layout {max-width: '.$params->get('layoutWidth').'%;}}'
                );
            }
        }

        if ($params->get('primaryColor')) {
            $doc->addStyleDeclaration('h1, h2, h3, h4, h5, h6 {color: '.$params->get('primaryColor').';}');
        }

        if ($params->get('secondaryColor')) {
            $doc->addStyleDeclaration('body {color: '.$params->get('secondaryColor').';}');
        }
    }

    /**
     * @return string
     * @throws \Exception
     * @since 3.0.0
     */
    public static function getSiteName(): string
    {
        $app = JFactory::getApplication();

        return htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
    }

    public static function addCodes()
    {
        $app    = JFactory::getApplication();
        $params = $app->getTemplate(true)->params;

        $css = $params->get('custom_css');
        JFactory::getDocument()->addStyleDeclaration($css);

        $js = $params->get('custom_js');
        JFactory::getDocument()->addScriptDeclaration($js);

    }

    /**
     * @return string
     * @throws \Exception
     * @since 3.0.0
     */
    public static function getBodyClass(): string
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();

        // Detecting Active Variables
        $option = $app->input->getCmd('option', '');
        $view   = $app->input->getCmd('view', '');
        $layout = $app->input->getCmd('layout', '');
        $task   = $app->input->getCmd('task', '');
        $itemid = $app->input->getCmd('Itemid', '');

        return $option
               .' view-'.$view
               .($layout ? ' layout-'.$layout : ' no-layout')
               .($task ? ' task-'.$task : ' no-task')
               .($itemid ? ' itemid-'.$itemid : '')
               .($doc->getDirection() === 'rtl' ? ' rtl' : '');
    }

    /**
     * Load a template file
     *
     * @param  string  $directory  The name of the template
     * @param  string  $filename   The actual filename
     *
     * @return  string  The contents of the template
     *
     * @since   1.7.0
     */
    public function loadTemplate($directory, $filename)
    {
        $contents  = '';
        $directory = __DIR__.'/'.$directory;

        // Check to see if we have a valid template file
        if (file_exists($directory.'/'.$filename)) {
            // Get the file content
            ob_start();
            require $directory.'/'.$filename;
            $contents = ob_get_contents();
            ob_end_clean();
        }

        return $contents;
    }

}
