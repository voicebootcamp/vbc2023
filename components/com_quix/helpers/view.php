<?php

/**
 * @version    3.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
jimport('quixnxt.app.bootstrap');

//Renderer dependency
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use QuixNxt\AssetManagers\ScriptManager;
use QuixNxt\AssetManagers\StyleManager;
use QuixNxt\Elements\ElementBag;
use QuixNxt\Elements\QuixElement;
use QuixNxt\Engine\Builder\Renderer;
use QuixNxt\Engine\Builder\ViewEngine;
use QuixNxt\Engine\Quix;
use QuixNxt\Engine\Support\QuixNode;
use QuixNxt\QxData;
use QuixNxt\Renderers\TwigEngine;
use QuixNxt\Utils\Asset;
use QuixNxt\Utils\Schema;
use Twig\Cache\FilesystemCache;

//Element dependency

//Quix Data binding and render page

/**
 * Class QuixFrontendHelperView
 *
 * @since  3.0.0
 */
class QuixFrontendHelperView
{
    /**
     * Prepare common header and check for quix page and collection
     *
     * @param $item
     *
     * @return void
     * @throws \Twig\Error\LoaderError
     * @since 3.0.0
     */
    public static function prepareQuixPage(&$item): void
    {
        // validate data first
        $data = $item->data;
        if ($data === '[]' || $data === '') {
            return;
        }

        QuixFrontendHelperAssets::loadLiveBuilderAssets(false);
        plgSystemQuix::addQuixTrapCSSfrontend();

        $page = static::getRenderedQuixPage($item);
        $doc  = QuixAppHelper::getCurrentDocument();

        JHtml::_('jquery.framework', false);

        if ($page['assets']['css']) {
            foreach ($page['assets']['css'] as $css_file) {
                $doc->addStyleSheet($css_file, ['version' => 'ver='.QUIXNXT_VERSION]);
            }
        }

        if ($page['assets']['js']) {
            foreach ($page['assets']['js'] as $js_file) {
                $doc->addScript($js_file, ['version' => 'ver='.QUIXNXT_VERSION], ['defer' => true]);
            }
        }

        $doc->addStyleDeclaration($page['styles']);
        $doc->addScriptDeclaration($page['scripts']);
    }

    /**
     * @param $item
     *
     * @return array
     * @throws \Twig\Error\LoaderError
     * @throws \Exception
     *
     * @since 3.0.0
     */
    public static function getRenderedQuixPage(&$item): array
    {
        $type = $item->type ?: 'section';
        if ( ! QUIXNXT_DEBUG && Folder::exists(JPATH_SITE."/media/quixnxt/storage/view/{$type}/{$item->id}")) {
            return static::getCachedContents($item);
        }

        if (JDEBUG) {
            JProfiler::getInstance('QuixEngine')->mark("Starting render");
        }

        $twig = self::getTwig();

        $render_engine = new TwigEngine($twig);
        $renderer      = new Renderer($render_engine);
        $view_engine   = new ViewEngine($renderer);

        ScriptManager::getInstance()->addUrl(Asset::getAssetUrl('/js/quix.vendor.js'));

        $view_engine->after(static function (QuixNode $node) {
            $node->html = htmlspecialchars_decode($node->html, ENT_QUOTES);

            $node->styles  = StyleManager::getInstance()->compile();
            $node->scripts = ScriptManager::getInstance()->compile();

            $node->css_files = array_unique(array_merge($node->css_files, StyleManager::getInstance()->getUrls()));
            $node->js_files  = array_unique(array_merge($node->js_files, ScriptManager::getInstance()->getUrls()));

            return $node;
        });

        $elementBag = ElementBag::getInstance();
        $data       = new QxData($elementBag);

        $quixPageData = json_decode($item->data, true);
        if (array_key_exists('data', $quixPageData)) {
            $quixPageData = $quixPageData['data'];
        }

        if (Schema::_isAssoc($quixPageData)) {
            $quixPageData = [$quixPageData];
        }

        /* old versions data migration */
        if ($item->builder_version < '4.0.0') {
            $adapter      = Schema::getAdapter(Schema::QUIX_V2, Schema::QUIX_V3);
            $quixPageData = $adapter->transform($quixPageData);
        }

        //  QuixAppHelper::renderQuixInstance
        $quix = new Quix($view_engine);

        $output = null;

        foreach ($quixPageData as $section) {
            $data->page([$section]);
            $_output = $quix->render($data);
            if ( ! $output) {
                $output = $_output;
            } else {
                $output = $output->append($_output);
            }
        }
        unset($quixPageData);
        if(!$output) {
            return [
                'html'    => '',
                'styles'  => '',
                'scripts' => '',
                'assets'  => ['css' => '', 'js' => ''],
            ];
        }

        // throw error notice..
        if ($data->hasMissingElement()) {
            $message = sprintf('Some elements are missing to display this page properly. They are <b>%s</b>', implode(' ', $data->getMissingElements()));
            JFactory::getApplication()->enqueueMessage($message, 'error');
        }

        $output->scripts .= ScriptManager::getInstance()->getWebFonts();

        $wrapper    = "<div class='qx quix' data-qx='".$item->id."' data-type='".$item->type."'>";
        $wrapper    .= "<div class='qx-inner frontend qx-type-".$item->type." qx-item-".$item->id."'>";
        $item->text = $wrapper.$output->html.'</div></div>';

        // cache everything here
        $html         = $item->text;
        $styles       = $output->styles;
        $scripts      = $output->scripts;
        $js_files     = $output->js_files;
        $css_files    = $output->css_files;
        $storage_path = JPATH_SITE.'/media/quixnxt/storage/views';

        $assets = ['js' => $js_files, 'css' => $css_files];
        File::write("{$storage_path}/{$type}/{$item->id}/content.php", $html);
        File::write("{$storage_path}/{$type}/{$item->id}/styles.css", $styles);
        File::write("{$storage_path}/{$type}/{$item->id}/scripts.js", $scripts);
        File::write("{$storage_path}/{$type}/{$item->id}/assets.json", json_encode($assets));

        return static::getCachedContents($item);
    }


    /**
     * Migrate old data as its already quix old data
     *
     * @param  \stdClass  $item
     *
     * @return \stdClass
     * @since 4.0.0
     */
    public static function checkOldDataAndMigrate(stdClass &$item): void
    {
        $quixPageDataParent = json_decode($item->data, true);

        if (array_key_exists('data', $quixPageDataParent)) {
            $quixPageData = $quixPageDataParent['data'];
        }else{
            $quixPageData = $quixPageDataParent;
        }

        if (Schema::_isAssoc($quixPageData)) {
            $quixPageData = [$quixPageData];
        }

        /* old versions data migration */
        if ($item->builder_version < '4.0.0') {
            $adapter      = Schema::getAdapter(Schema::QUIX_V2, Schema::QUIX_V3);
            $quixPageData = $adapter->transform($quixPageData);
        }

        $quixPageDataParent['data'] = $quixPageData;

        // prepare as json
        $item->data = json_encode($quixPageDataParent);
    }

    /**
     * @param $page
     *
     * @return array
     *
     * @since 3.0.0
     */
    private static function getCachedContents(&$page): array
    {
        $type         = $page->type ?: 'section';
        $storage_path = JPATH_SITE.'/media/quixnxt/storage/views';
        ob_start();
        include("{$storage_path}/{$type}/{$page->id}/content.php");
        $html = ob_get_clean();

        $styles  = file_get_contents("{$storage_path}/{$type}/{$page->id}/styles.css");
        $scripts = file_get_contents("{$storage_path}/{$type}/{$page->id}/scripts.js");
        $assets  = json_decode(file_get_contents("{$storage_path}/{$type}/{$page->id}/assets.json"), true);


        $page->text = $html;

        return [
            'html'    => $html,
            'styles'  => $styles,
            'scripts' => $scripts,
            'assets'  => $assets,
        ];
    }

    /**
     * @return \Twig\Environment
     * @throws \Twig\Error\LoaderError
     * @since 3.0.0
     */
    public static function getTwig(): Twig\Environment
    {
        $loader = new Twig\Loader\FilesystemLoader();

        $elementsPathRegistry = [
            QuixElement::QUIX_VISUAL_BUILDER_PATH,
            QuixElement::QUIX_VISUAL_BUILDER_PATH.'/../shared'
        ];

        \JPluginHelper::importPlugin('quix');
        \JFactory::getApplication()->triggerEvent('onRegisterQuixElementsPathRegistry', [&$elementsPathRegistry]);

        $loader->setPaths($elementsPathRegistry);

        $cache = QUIXNXT_DEBUG ? [] : [
            'cache' => new FilesystemCache(JPATH_CACHE.'/quix/templates'),
        ];

        $env = new Twig\Environment($loader, $cache);

        if (JDEBUG) {
            $env->addExtension(new Twig\Extension\DebugExtension);
        }

        return $env;
    }
}
