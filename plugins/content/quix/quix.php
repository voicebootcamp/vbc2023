<?php
/**
 * @package    Quix
 * @author    ThemeXpert http://www.themexpert.com
 * @copyright  Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license  GNU General Public License version 3 or later; see LICENSE.txt
 * @since    1.0.0
 */

defined('_JEXEC') or die;

use QuixNxt\Shortcode\Shortcode;

class plgContentQuix extends JPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    public $configs = null;

    public $allowed_contexts = [];

    /**
     * Constructor.
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An optional associative array of configuration settings.
     *
     * @since   1.6
     */
    public function __construct(&$subject, $config)
    {
        jimport('quixnxt.app.bootstrap');

        $this->configs = $this->getConfigs();
        $this->prepareAllowedContext();

        parent::__construct($subject, $config);
    }

    public function prepareAllowedContext()
    {
        $this->allowed_contexts[] = 'text'; // Common context for prepare content

      // Article
        if ($this->configs->get('enable_com_content', 1)) {
            $this->allowed_contexts[] = 'com_content.article';
            $this->allowed_contexts[] = 'com_content.featured';
            $this->allowed_contexts[] = 'com_content.category';
        }

      // Module
        if ($this->configs->get('enable_mod_custom', 1)) {
            $this->allowed_contexts[] = 'mod_custom.content';
        }

      // K2
        if ($this->configs->get('enable_com_k2', 0)) {
            $this->allowed_contexts[] = 'com_k2.item';
        }

      // EasyBlog
        if ($this->configs->get('enable_com_easyblog', 0)) {
            $this->allowed_contexts[] = 'easyblog.blog';
            $this->allowed_contexts[] = 'easyblog.blog.view';
            $this->allowed_contexts[] = 'easyblog.blog.featured';
        }

      // Digicom
        if ($this->configs->get('enable_com_digicom', 0)) {
            $this->allowed_contexts[] = 'com_digicom.product';
            $this->allowed_contexts[] = 'com_digicom.category';
            $this->allowed_contexts[] = 'com_digicom.categories';
        }

      // Virtuemart
        if ($this->configs->get('enable_com_virtuemart', 0)) {
            $this->allowed_contexts[] = 'com_virtuemart.category';
            $this->allowed_contexts[] = 'com_virtuemart.productdetails';
        }

      // ZOO
      // require special permission
      // if($this->configs->get('enable_com_zoo', 0)){
      //   $this->allowed_contexts[] = 'com_zoo';
      // }

      // HikaShop
      // Common context allowed by default
      // if($this->configs->get('enable_com_hikashop', 0)){
      // $this->allowed_contexts[] = 'com_virtuemart.category';
      // $this->allowed_contexts[] = 'com_virtuemart.productdetails';
      // }

      // J2Store
        if ($this->configs->get('enable_com_j2store', 0)) {
            $this->allowed_contexts[] = 'com_content.article.productlist';
            $this->allowed_contexts[] = 'com_content.featured.productlist';
            $this->allowed_contexts[] = 'com_content.category.productlist';
        }

      // JU Directory
        if ($this->configs->get('enable_com_judirectory', 0)) {
            $this->allowed_contexts[] = 'com_judirectory.field';
            $this->allowed_contexts[] = 'com_judirectory.fieldgroup';
            $this->allowed_contexts[] = 'com_judirectory.category';
            $this->allowed_contexts[] = 'com_judirectory.categories';
            $this->allowed_contexts[] = 'com_judirectory.comment';
            $this->allowed_contexts[] = 'com_judirectory.criteria';
            $this->allowed_contexts[] = 'com_judirectory.criteriagroup';
            $this->allowed_contexts[] = 'com_judirectory.listing';
            $this->allowed_contexts[] = 'com_judirectory.listing_list';
            $this->allowed_contexts[] = 'com_judirectory.plugin';
            $this->allowed_contexts[] = 'com_judirectory.style';
            $this->allowed_contexts[] = 'com_judirectory.tag';
        }

      // Custom Context
        if ($this->configs->get('enable_custom_context', 0)
          &&
          !empty($this->configs->get('custom_context', ''))
        ) {
            $custom_context = $this->configs->get('custom_context', '');
            $custom_context = explode(',', $custom_context);

            foreach ($custom_context as $value) {
                $this->allowed_contexts[] = $value;
            }
        }
    }

  /**
   * Plugin that retrieves contact information for contact
   *
   * @param   string   $context  The context of the content being passed to the plugin.
   * @param            $article
   * @param   mixed    $params   Additional parameters. See {@see PlgContentContent()}.
   * @param   integer  $page     Optional page number. Unused. Defaults to zero.
   *
   * @return bool True on success.
   * @throws \Exception
   * @since    3.0.0
   * @internal param mixed $row An object with a 'text' property
   */
    public function onContentPrepare(string $context, &$article, &$params, $page = 0)
    {
        // work only on front-end
        if (JFactory::getApplication()->isClient('administrator')) {
            return false;
        }

        // check for quix editor item
        // this item was edited with quix
        // 1. renderItemEditedWithQuix
        $this->renderItemEditedWithQuix($context, $article);


        // now check for short codes in content and replace
        // 2. findShortcodeAndRender
        $this->findShortcodeAndRender($context, $article);
    }

    public function renderItemEditedWithQuix($context, &$article)
    {
        if ($context === 'com_virtuemart.productdetails') {
            $key = 'virtuemart_product_id';
        } else {
            $key = 'id';
        }

        // check for built with editor
        require_once JPATH_SITE . '/components/com_quix/helpers/editor.php';
        if (isset($article->$key) && $article->$key) {
            $mainContext = $context;
            if ($context === 'com_content.category') {
                $context = 'com_content.article';
            }

            $getQEditor = QuixFrontendHelperEditor::getInfo($context, $article->$key);

            if (isset($getQEditor->id) && $getQEditor->status) {
                // render main item
                $collection = QuixAppHelper::qxGetCollectionInfoById($getQEditor->collection_id);
                QuixAppHelper::renderQuixInstance($collection);
                $article->fulltext = $article->text = $collection->text;

                /**
                 * prepare content for intro
                 * removes style tags like icons and others
                 *
                 * @since 4.2.0
                 */
                $introText = preg_replace('#<style type="text/css">.*?</style>#s', '', $article->fulltext);

                /**
                 * prepare content for intro
                 * @since 2.0.0
                 */
                $article->introtext = strip_tags($introText);
                $article->introtext = trim(preg_replace('/<img[^>]*>/', '', $article->introtext));
                $article->introtext = preg_replace('/\s+/', ' ', $article->introtext);
                $article->introtext = JHtml::_('content.prepare', $article->introtext, '', $context);

                if ($mainContext === 'com_content.category') {
                    $article->text = $article->introtext;
                }
                return true;
            }
        }
    }

    public function findShortcodeAndRender($context, &$article)
    {
      // Match context
        if (! in_array($context, $this->allowed_contexts, true)) {
            // no match, check if zoo enabled
            if (! $this->configs->get('enable_com_zoo', 0)
                || strpos($context, 'com_zoo.') === false
            ) {
                return;
            }
        }

        // Simple performance check to determine whether bot should process further
        if (strpos($article->text, 'quix') === false) {
            return;
        }

        // short code replacer
        $shortcodeParser = new Shortcode();
        $content = $shortcodeParser->parse('quix', $article->text, [$this, 'renderShortcode']);

        $article->text = $content;
        $article->fulltext = $content;
    }

    public function onRenderModule(&$module, &$attribs)
    {
        // work only on front-end
        if (JFactory::getApplication()->isClient('administrator')) {
            return;
        }

        // check for built with editor
        require_once JPATH_SITE . '/components/com_quix/helpers/editor.php';

        if (isset($module->id) && $module->id) {
            $getQEditor = QuixFrontendHelperEditor::getInfo('com_modules.module', $module->id);

            if (!$getQEditor) {
                $getQEditor = QuixFrontendHelperEditor::getInfo('com_config.modules', $module->id);
            }

            $contexts = ['com_modules.module', 'com_config.modules', '0'];

            if (isset($getQEditor->id) && in_array($getQEditor->context, $contexts, true) && $getQEditor->status) {
                // render main item
                $collection = QuixAppHelper::qxGetCollectionInfoById($getQEditor->collection_id);
                QuixAppHelper::renderQuixInstance($collection);
                $module->content = $collection->text;
            }
        }
    }

    /**
     * collection $id might not exist
     * so load bootstrap after we are sure that $id exists
     * and we should not call jimport multiple times
     * it will remember if we already imported
     * @param $attributes
     * @return string
     * @since 3.0.0
     */
    public function renderShortCode($attributes)
    {
        $id = array_key_exists('id', $attributes) ? $attributes['id'] : false;
        if (!$id) {
            return '<p>invalid quix shortcode!</p >';
        }


        $collection = QuixAppHelper::qxGetCollectionInfoById($id);
        if (!$collection) {
            return '<p>invalid quix collection shortcode!</p >';
        }

        // render main item
        QuixAppHelper::renderQuixInstance($collection);
        return $collection->text;
    }

    public function getConfigs()
    {
        if (!$this->configs) {
            $config = JComponentHelper::getComponent('com_quix');
            $this->configs = $config->params;
        }

        return $this->configs;
    }
}
