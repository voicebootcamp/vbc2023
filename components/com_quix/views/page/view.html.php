<?php
/**
 * @name    QuixViewPage
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 * @since      1.0.0
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Exception\ExceptionHandler;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\Registry\Registry;

/**
 * View to edit
 *
 * @since  1.0
 */
class QuixViewPage extends HtmlView
{
    public $app;

    public $document;

    public $metadata;

    protected $state;

    protected $item;

    protected $params;

    protected $config;

    protected $meta_title;

    protected $meta_desc;

    /**
     * Display the view
     *
     * @param  string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     * @since 1.0.0
     */
    public function display($tpl = null)
    {
        $this->app      = JFactory::getApplication();
        $this->document = JFactory::getDocument();
        $this->state    = $this->get('state');
        $this->item     = $this->get('data');
        $this->params   = $this->state->get('params');
        $this->config   = JComponentHelper::getComponent('com_quix')->params;

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        if ( ! isset($this->item->id) || ! $this->item->id) {
            $this->app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            $this->app->setHeader('status', 403, true);

            return;
        }

        // Check the view access to the article (the model has already computed the values).
        if ($this->item->params->get('access-view') === false
            && ($this->item->params->get('show_noauth', '0') === '0')
        ) {
            $error = new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
            return ExceptionHandler::render($error);
        }

        // count hits
        $this->get('Hit');

        // hardcode type for builder use, so we know its page
        $this->item->type = 'page';

        try {
            QuixAppHelper::renderQuixInstance($this->item);
        } catch (Exception $e) {
            ExceptionHandler::render($e);
        }

        // now prepare document for meta info
        $this->checkAmp();
        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * EnableAmp status and add head
     *
     * @return void
     *
     * @throws Exception
     * @since 2.0.0
     */
    protected function checkAmp(): void
    {
        $amp_default  = $this->config->get('amp_default', false);
        $itemMetadata = new Registry;
        $itemMetadata->loadString($this->item->metadata);
        $enable_amp = $itemMetadata->get('enable_amp', '');
        if ($enable_amp == 'false') {
            return;
        }

        if ($enable_amp == '' && ! $amp_default) {
            return;
        }

        if ( ! $amp_default) {
            return;
        }

        $uri = JUri::getInstance(true);
        $uri->setVar('format', 'amp');
        $this->document->addHeadLink(htmlspecialchars($uri->toString()), 'amphtml');
    }

    /**
     * Prepares the document
     *
     * @return void
     *
     * @throws Exception
     * @since 2.0.0
     */
    protected function _prepareDocument()
    {
        $menus = $this->app->getMenu();

        $registry   = new Registry;
        $itemParams = $registry->loadString($this->item->params);
        $code       = $itemParams->get('code', '');
        if ($code) {
            $this->document->addCustomTag($code);
        }

        // add js & css from v2 only
        $codeCSS = $itemParams->get('codecss', '');
        if ($codeCSS) {
            $this->document->addStyleDeclaration($codeCSS);
        }

        $codeJS = $itemParams->get('codejs', '');
        if ($codeJS) {
            $this->document->addScriptDeclaration($codeJS);
        }

        $registry = new Registry;
        if ( ! method_exists($registry, 'loadString')) {
            return;
        }

        // prepare meta info
        $this->metadata = $registry->loadString($this->item->metadata);

        //get title from quix
        $this->meta_title = $this->metadata->get('title', '');

        // Because the application sets a default page title,
        // We need to get it from the menu item itself
        // give Menu priority
        $menu = $menus->getActive();
        if (isset($menu->id) && $menu->id) {
            $title = $menu->getParams()->get('page_title', $this->meta_title);
        } else {
            $title = $this->meta_title;
        }


        if ($this->app->get('sitename_pagetitles', 0) === '1') {
            $title = JText::sprintf('JPAGETITLE', $this->app->get('sitename'), $title);
        } elseif ($this->app->get('sitename_pagetitles', 0) === '2') {
            $title = JText::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
        } elseif (empty($title)) {
            $title = $this->params->get('page_title', JText::_('COM_QUIX_DEFAULT_PAGE_TITLE'));
        }

        $this->document->setTitle($title);

        // set description
        $this->meta_desc = $this->metadata->get('desc', '');
        if (isset($menu->id) && $menu->id) {
            $description = $menu->getParams()->get('menu-meta_description', $this->meta_desc);
        } else {
            $description = $this->meta_desc;
        }

        $this->document->setDescription($description);

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        // Quix Meta
        $addOG = $this->metadata->get('addog');
        $addTW = $this->metadata->get('addtw');

        if ($addOG) {
            $this->addOpenGraph();
        }
        if ($addTW) {
            $this->addTwitterCard();
        }

        if ($this->config->get('generator_meta', 1) && QuixHelper::isFreeQuix()) {
            $this->document->setMetadata('application-name', 'Quix Page Builder');
        }
    }

    /**
     * @return bool
     * @throws \Exception
     * @since 2.0.0
     */
    public function addOpenGraph(): bool
    {
        $this->document->setMetadata('og:type', 'website', 'property');
        $this->document->setMetadata('og:site_name', $this->app->get('sitename'), 'property');
        $this->document->setMetadata('og:title', $this->meta_title, 'property');
        $this->document->setMetadata('og:description', $this->meta_desc, 'property');

        $this->document->setMetadata('title', $this->meta_title);
        $this->document->setMetadata('description', $this->meta_desc);

        if ( ! empty($this->metadata->get('image_intro'))) {
            $image_intro = $this->prepareImageUrl($this->metadata->get('image_intro'));
            // if ( ! preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|([a-z0-9-].)+(:[0-9]+)(\/.*)?$/', $image_intro)
            // ) {
            //     $image_intro = JURI::root().$this->deSlash('images/'.$image_intro);
            // }

            $this->document->setMetadata('og:image', $image_intro, 'property');
        }

        $this->document->setMetadata('og:url', JURI::current(), 'property');
        $this->document->setMetadata('fb:app_id', $this->metadata->get('fb_appid', ''));

        return true;
    }

    public function prepareImageUrl(string $src): string
    {
        $imagePath = QUIXNXT_IMAGE_PATH;

        if ( strpos($src, '//', 0) === 0 || strpos($src, 'http://', 0) === 0 || strpos($src, 'https://', 0) === 0) {

            /*
             * If getquix url, replace with cdn link
             * @since 3.0.0
             */
            if (strpos($src, 'https://getquix.net', 0) !== false || strpos($src, 'http://getquix.net', 0) !== false) {
                $src = str_replace('https://getquix.net', 'https://quix.b-cdn.net', $src);
                $src = str_replace('http://getquix.net', 'https://quix.b-cdn.net', $src);
            }

            return $src;
        }

        // new implementation
        /**
         * new checker for local image
         */
        if (substr($src, 0, 6) !== 'images') {
            $src = $imagePath.$src;
        }

        return JUri::root() . $src;

    }

    /**
     * @return bool
     * @since 2.0.0
     */
    public function addTwitterCard(): bool
    {
        $this->document->setMetadata('twitter:card', 'summary');
        $this->document->setMetadata('twitter:site', $this->metadata->get('twitter_username', ''));
        $this->document->setMetadata('twitter:title', $this->meta_title);
        $this->document->setMetadata('twitter:description', $this->meta_desc);

        return true;
    }

    /**
     * @param $url
     *
     * @return string
     * @since 2.0.0
     */
    public function deSlash($url): string
    {
        $url = str_replace('//', '/', $url);

        return $url;
    }
}
