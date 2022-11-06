<?php
/**
 * @package    Quix
 * @author     ThemeXpert http://www.themexpert.com
 * @copyright  Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @since      1.0.0
 */

use Joomla\CMS\Factory;
use QuixNxt\Elements\ElementBag;
use QuixNxt\Utils\Asset;

defined('_JEXEC') or die;

class plgSystemQuix extends JPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    public $configs = null;
    public $header = null;
    public $footer = null;
    public static $isBuilder = null;
    public static $isClassicBuilder = null;
    public static $canProceed = null;

    public function __construct($subject, $config)
    {
        plgSystemQuix::initQuix();

        parent::__construct($subject, $config);
    }

    public static function initQuix()
    {
        jimport('quixnxt.app.bootstrap');

        JLoader::register('QuixHelper', JPATH_ADMINISTRATOR.'/components/com_quix/helpers/quix.php');
        JLoader::register('QuixFrontendHelper', JPATH_SITE.'/components/com_quix/helpers/quix.php');

        /* all system plugin helper prefix: QuixSystemHelper */
        JLoader::registerPrefix('QuixSystemHelper', __DIR__.'/includes');

        /* all frontend helper prefix: QuixHelper */
        JLoader::registerPrefix('QuixFrontendHelper', JPATH_SITE.'/components/com_quix/helpers');

        /* all admin helper prefix: QuixHelper */
        JLoader::registerPrefix('QuixHelper', JPATH_ADMINISTRATOR.'/components/com_quix/helpers');

    }

    /**
     * @throws \Exception
     * @since 3.0.0
     */
    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        if ( ! $app->isClient('site')) {
            return;
        }

        if ($asset = $app->input->get(Asset::QUIX_ASSET_REQUEST_KEY, null, 'string')) {
            Asset::load($asset);
        }

        if ($path = $app->input->get('quix-image', null, 'string')) {
            $image = new QuixSystemHelperImage();
            $image->process($path);
        }

        if ($app->input->get('quixlogin', false)) {
            $login = new QuixSystemHelperLogin();
            $login->doLoginCheck();
        }
    }

    /**
     * Quix Theme Builder check as Quix Editor
     * ThemeBuilder concept: Quix Layout : Article, Digicom Product
     *
     * @since 3.0.0
     */
    public function onAfterRoute()
    {
        $editor = new QuixSystemHelperEditor();
        $editor->afterRoute();
    }

    /**
     * Load Quix Assets
     *
     * @throws \Exception
     * @since 3.0.0
     */
    public function onBeforeCompileHead()
    {
        // $isBuilder = $this->isBuilder();
        // if ($isBuilder) {
        //     $builder = new QuixSystemHelperBuilder();
        //     $builder->prepareBuilderView($this->params);
        // } else {
        //     // TODO: move this part to specific renderer
        //     $user       = JFactory::getUser();
        //     $authorised = $user->authorise('core.manage', 'com_quix')
        //                   ||
        //                   count($user->getAuthorisedCategories('com_quix', 'core.manage'));
        //     if ($authorised) {
        //         JHtml::_('bootstrap.framework');
        //         $js = JUri::root(true).'/media/quixnxt/js/edit-quix.js';
        //         JFactory::getDocument()->addScript($js, ['version' => 'auto']);
        //     }
        // }
    }

    /**
     * Listener for the `onAfterDispatch` event
     *
     * @return  void
     *
     * @throws \Exception
     * @since   1.0
     */
    public function onAfterDispatch()
    {

        // // update the post install manually
        // $db = JFactory::getDbo();
        // $query = $db->getQuery(true)
        //             ->select('*')
        //             ->from($db->qn('#__postinstall_messages'))
        //             ->where($db->qn('language_extension') . ' = ' . $db->q('plg_system_quix'));
        // $db->setQuery($query);
        // $data = $db->loadObject();
        // if(!$data){
        //     $this->install([]);
        // }

        $app = JFactory::getApplication();
        if ( ! $app->isClient('site')) {
            return;
        }
        if ($this->isDefaultJoomlaPage() && $app->input->get('tmpl') !== 'component' && $app->input->get('format') !== 'json') {
            $theme        = new QuixSystemHelperTheme();
            $this->header = $theme->getItem('header', $this->params);
            $this->footer = $theme->getItem('footer', $this->params);
            // @TODO: Template is not released yet
            // $checkTemplate = $theme->checkItem('mainbody');
            // if ($checkTemplate) {
            //     $app = \JFactory::getApplication();
            //     $app->setTemplate('go');
            // }
        }

        $isBuilder = $this->isBuilder();
        $builder   = new QuixSystemHelperBuilder();
        if ($isBuilder) {
            $builder->prepareBuilderView($this->params);
        } else {
            // $builder->loadCustomAssets($this->params);

            // TODO: move this part to specific renderer
            $user       = JFactory::getUser();
            $authorised = $user->authorise('core.manage', 'com_quix')
                          ||
                          count($user->getAuthorisedCategories('com_quix', 'core.manage'));
            if ($authorised) {
                JFactory::getDocument()->addScriptDeclaration('var quix = quix ?? {};quix.url = "'.JUri::root().'";');
                $js = JUri::root(true).'/media/quixnxt/js/edit-quix.js';
                JFactory::getDocument()->addScript($js, ['version' => 'auto'], ['defer' => 'defer']);
            }
        }
    }

    /**
     * Listener for the `onAfterRender` event
     *
     * @return  void
     *
     * @throws \Exception
     * @since   1.0
     */
    public function onAfterRender(): void
    {
        if ($this->isClassicBuilder()) {
            $builder = new QuixSystemHelperBuilder();
            $builder->prepareBuilderView($this->params);
        }

        if ($this->isDefaultJoomlaPage() && JFactory::getApplication()->isClient('site')) {
            /**
             * Load common assets globally
             * set preload and pre-connect
             */
            $builder = new QuixSystemHelperBuilder();
            $builder->forceQuixAssetsPreload($this->params);

            if ($this->header !== null || $this->footer !== null) {
                $theme = new QuixSystemHelperTheme();
                $theme->addHtml($this->header);
                $theme->addHtml($this->footer, 'after');

                if ($this->header) {
                    $theme->removeTemplateBlocks('header');
                }

                if ($this->footer) {
                    $theme->removeTemplateBlocks('footer');
                }
            }
        }
    }

    /**
     * Listener for the `onQuixLoadMainbody` event
     *
     * @return  string
     *
     * @throws \Exception
     * @since   3.0.0
     */
    public function onQuixLoadMainbody()
    {
        if ($this->isDefaultJoomlaPage()) {
            $registry = new Joomla\Registry\Registry();
            $theme    = new QuixSystemHelperTheme();
            $layout   = $theme->getItem('mainbody', $registry);

            if ($layout) {
                return $layout;
            }
        }
    }

    /**
     * JMedia Dependency for Pro feature
     *
     * @param $context
     *
     * @throws \Exception
     * @since   3.0.0
     * @version 3.0.0
     */
    public function onJMediaDisplayScript($context)
    {
        // $plugin    = JPluginHelper::getPlugin('system', 'jmediapro');
        // $jMediaPro = true;
        // if (isset($plugin->id) && $plugin->id) {
        //     $jMediaPro = true;
        // }

        $proQuix = QuixHelperLicense::isProActivated();
        $input   = JFactory::getApplication()->input;
        $source  = $input->get('source', '', 'string');
        if ($source === 'quix'
            &&
            ($context === 'com_jmedia.images' || $context === 'com_jmedia.media')
        ) {
            $url = JUri::root(true).'/?quix-asset=/css/qxi.css&ver=4.0.0-beta1';
            JFactory::getDocument()->addStylesheet($url, ['media' => 'all']);
            JFactory::getDocument()->addScriptDeclaration("Filemanager.Pluggable.registerPlugin('tx-icons', Filemanager.IconsFont);");

            if ($proQuix) {
                JFactory::getDocument()->addScriptDeclaration("var Unsplash = Filemanager.Pluggable.registerPlugin('tx-unsplash', Filemanager.Unsplash);");
                JFactory::getDocument()->addScriptDeclaration("Unsplash.accessor().setClientID('QWbovST3tlmma5EdQ9uH-1NXP6gYiGSRsEdPjhqT36I');");
            }
        }
    }

    /**
     * When builder mode, set cache policy to false
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public function onPageCacheSetCaching()
    {
        if ($this->isBuilder()) {
            return false;
        }
    }

    /**
     * determine is version 2
     *
     * @param  int  $id
     *
     * @return bool
     * @throws \Exception
     * @depecated It will be removed on next release
     * @since     3.0.0
     */
    public static function isV2($id = 0)
    {
        $input  = JFactory::getApplication()->input;
        $option = $input->get('option');
        $id     = $id ?: $input->get('id');
        $view   = $input->get('view', 'page');

        if ($option === 'com_quix' && $id) {
            $db  = JFactory::getDbo();
            $sql = sprintf(
                "SELECT builder FROM %s WHERE `id` = %s",
                $view === 'page' ? '`#__quix`' : '`#__quix_collections`',
                $id
            );

            $db->setQuery($sql);
            $result = $db->loadResult();

            if ($result === 'classic') {
                return false;
            }
        }

        return true;
    }

    /**
     * Method addQuixTrapCSS
     * load core assets on top of template, so template can override styles
     *
     * @throws \Exception
     * @since     1.0.0
     * @depecated will be remove on next release
     */
    public static function addQuixTrapCSS()
    {
        // Lets see now it works
        if (QuixAppHelper::checkQuixIsVersion2()) {
            self::addQuixTrapCSSFrontend();
        } else {
            self::addQuixTrapCSSClassic();
        }
    }

    /**
     * Method addQuixTrapCSS for Frontend
     *
     * @since 2.0.0
     */
    public static function addQuixTrapCSSFrontend()
    {
        $document     = JFactory::getDocument();
        $_styleSheets = $document->_styleSheets;

        $quixCore = Asset::getAssetUrl('/css/quix-core.css');

        if (QUIXNXT_DISABLED_CSS) {
            $stylesheetQuix = [
                $quixCore => [
                    'media'    => 'all',
                    'disabled' => 'true',
                    'type'     => 'text/css',
                    'options'  => ['version' => 'auto'],
                ],
            ];

            $document->addScriptDeclaration("document.querySelectorAll('link[disabled=\"true\"]').forEach(link => link.removeAttribute('disabled'));");

        } else {
            $stylesheetQuix = [
                $quixCore => ['media' => 'all', 'type' => 'text/css'],
            ];
        }

        $styleSheets            = array_replace($stylesheetQuix, $_styleSheets);
        $document->_styleSheets = $styleSheets;
    }

    /**
     * Method addQuixTrapCSS for Classic
     *
     * @since 3.0.0
     */
    public static function addQuixTrapCSSClassic()
    {
        $document     = JFactory::getDocument();
        $_styleSheets = $document->_styleSheets;
        $version      = 'ver='.QUIXNXT_VERSION;

        $quixTrap = JUri::root(true).'/libraries/quix/assets/css/quixtrap.css?'.$version;
        $quixCl   = JUri::root(true).'/libraries/quix/assets/css/quix-classic.css?'.$version;
        $quixMP   = JUri::root(true).'/libraries/quix/assets/css/magnific-popup.css?'.$version;

        $stylesheetQuix = [
            $quixTrap => ['defer' => 'true', 'media' => 'all'],
            $quixCl   => ['defer' => 'true', 'media' => 'all'],
            $quixMP   => ['defer' => 'true', 'media' => 'all']
        ];

        $_styleSheets           = array_replace($stylesheetQuix, $_styleSheets);
        $document->_styleSheets = $_styleSheets;
    }

    public function getConfigs()
    {
        if ( ! $this->configs) {
            $this->configs = JComponentHelper::getComponent('com_quix')->getParams();
        }

        return $this->configs;
    }

    public function onGetIcons($context)
    {
        if ($context === 'mod_quickicon') {
            return [
                [
                    'link'   => JRoute::_('index.php?option=com_quix'),
                    'image'  => 'home',
                    'icon'   => 'header/icon-48-home.png',
                    'text'   => JText::_('COM_QUIX_PAGES'),
                    'access' => ['core.manage', 'com_quix'],
                    'group'  => 'COM_QUIX',
                ],
                [
                    'link'   => JRoute::_('index.php?option=com_quix&view=collections'),
                    'image'  => 'puzzle',
                    'icon'   => 'header/icon-48-puzzle.png',
                    'text'   => JText::_('COM_QUIX_COLLECTIONS'),
                    'access' => ['core.manage', 'com_quix'],
                    'group'  => 'COM_QUIX',
                ]
            ];
        }
    }

    /**
     * fixAdminTools has security issue
     * adds firewall exception
     *
     * @return false|void
     * @since 2.0.0
     */
    public static function fixAdminTools()
    {
        if ( ! JFile::exists(JPATH_ADMINISTRATOR.'/components/com_admintools/version.php')) {
            return;
        }

        include_once JPATH_ADMINISTRATOR.'/components/com_admintools/version.php';

        $isPro = ADMINTOOLS_PRO ?: false;
        if ($isPro) {
            $db  = JFactory::getDbo();
            $sql = "SELECT `option` FROM `#__admintools_wafexceptions` WHERE `option` = 'com_quix'";
            $db->setQuery($sql);
            $result = $db->loadResult();

            if ($result === 'com_quix') {
                return false;
            }

            // create one
            $obj         = new stdClass();
            $obj->option = 'com_quix';
            $obj->view   = '';
            $obj->query  = '';
            JFactory::getDbo()->insertObject('#__admintools_wafexceptions', $obj);
        }
    }

    /**
     * Proceed to operate when the view is not builder
     *
     * @return bool|null
     * @throws \Exception
     * @version 3.0.0
     * @since   2.0.0
     */
    public function isDefaultJoomlaPage()
    {
        if (self::$canProceed !== null) {
            return self::$canProceed;
        }

        $canProceedCheck = $this->isBuilder() ? false : true;

        self::$canProceed = $canProceedCheck;

        return $canProceedCheck;
    }

    /**
     * Proceed to operate when the view is builder
     *
     * @return bool|null
     * @throws \Exception
     * @version 3.0.0
     * @since   2.0.0
     */
    public function isBuilder()
    {
        if (self::$isBuilder !== null) {
            return self::$isBuilder;
        }

        $isBuilderCheck = false;
        $app            = JFactory::getApplication();
        if ($app->isClient('site')) {
            $view   = $app->input->get('view', '', 'string');
            $option = $app->input->get('option', '', 'string');
            $layout = $app->input->get('layout', '', 'string');
            if ($option === 'com_quix'
                && $view === 'form'
                && ($layout === 'edit' || $layout === 'iframe')
            ) {
                $isBuilderCheck = true;
            }
        }

        self::$isBuilder = $isBuilderCheck;

        return $isBuilderCheck;
    }

    /**
     * Proceed to operate when the view is builder admin
     *
     * @return bool|null
     * @throws \Exception
     * @version 3.0.0
     * @since   2.0.0
     */
    public function isClassicBuilder()
    {
        if (self::$isClassicBuilder !== null) {
            return self::$isClassicBuilder;
        }

        $isBuilderCheck = false;
        $app            = JFactory::getApplication();
        if ($app->isClient('administrator')) {
            $view   = $app->input->get('view', '', 'string');
            $option = $app->input->get('option', '', 'string');
            $layout = $app->input->get('layout', '', 'string');
            if ($option === 'com_quix'
                && ($view === 'page' || $view === 'collection')
                && $layout === 'edit'
            ) {
                $isBuilderCheck = true;
            }
        }

        self::$isClassicBuilder = $isBuilderCheck;

        return $isBuilderCheck;
    }

    /**
     *
     * @throws \Exception
     * @since
     */
    public function onRegisterQuixElements()
    {
        $template = Factory::getApplication()->getTemplate();
        $path     = JPATH_THEMES.'/'.$template.'/quix/quixnxt/';

        if (is_dir($path)) {
            ElementBag::register($path);
        }
    }

    /**
     *
     * @throws \Exception
     * @since
     */
    public function onRegisterQuixElementsPathRegistry(array &$elementsPathRegistry)
    {
        $template = Factory::getApplication()->getTemplate();
        $path     = JPATH_THEMES.'/'.$template.'/quix/quixnxt/';

        if (is_dir($path)) {
            $elementsPathRegistry[] = $path;
        }
    }
}
