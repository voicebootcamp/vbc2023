<?php

global $assetLoaded;
$assetLoaded = false;

class_alias('QuixNxt\Assets\Assets', 'Assets');

// booting assets loader
function bootAssetsLoader()
{
    // # Loading assets.
    // global $assetsLoaded;

    // if (!$assetsLoaded) {
    //     Assets::load();
    //     $assetsLoaded = true;
    // }
}

/**
 * Remove Joomla specific css/js files on classic builder page
 */
function removeJoomlaAssetsForClassicBuilder()
{
    $app = JFactory::getApplication();
    $document = JFactory::getDocument();
    $tmpl = $app->getTemplate();

    // removing bootstrap
    $bootstrap_css = JUri::root(true) . '/media/jui/css/bootstrap.css';
    $bootstrap_js = JUri::root(true) . '/media/jui/js/bootstrap.min.js';

    $template = JUri::root(true) . '/administrator/templates/' . $tmpl . '/css/template.css?' . $document->getMediaVersion();
    $templatej37 = JUri::root(true) . '/administrator/templates/' . $tmpl . '/css/template.css';
    // $template_js = JUri::root( true ) . '/administrator/templates/' . $tmpl . '/js/template.js?' . $document->getMediaVersion();
    $template_js = JUri::root(true) . '/administrator/templates/' . $tmpl . '/js/template.js';
    // var_dump($bootstrap_js);
    unset($document->_styleSheets[$bootstrap_css] , $document->_styleSheets[$template] , $document->_styleSheets[$templatej37] , $document->_scripts[$template_js]);

    // unset( $document->_scripts[$bootstrap_js] );
}

/**
 * Common Builder scripts used on v1 and v2
 */
function loadCommonBuilderScripts($frontend = false)
{
    $version = 'ver=' . QUIXNXT_VERSION;

    $document = JFactory::getDocument();

    if($frontend){
        // HTTP client
        // $document->addScript(QUIXNXT_URL . '/assets/js/axios.js', ['version' => $version]);
    }else{
        // Date Time
        // $document->addScript(QUIXNXT_URL . '/assets/js/moment.js', ['version' => 'auto']);

        // String search
        // $document->addScript(QUIXNXT_URL . '/assets/js/fuzzy.js', ['version' => $version]);

        // Date Time picker
        // $document->addScript(QUIXNXT_URL . '/assets/js/react-date-picker.js', ['defer' => 'true', 'version' => $version]);

        // Magnific popup
        // $document->addScript(QUIXNXT_URL . '/assets/js/jquery.magnific-popup.js', ['defer' => 'true', 'version' => $version]);
    }

}

/**
 * CSS & JS used on Joomla > Quix backend
 */
function loadAssetsForJoomlaBackend()
{
    $version = 'ver=' . QUIXNXT_VERSION;
    Assets::Css('admin', QUIXNXT_URL . '/assets/css/admin.css');
}

/********************************************
 * Load All Classic Builder scripts (v1)
 ********************************************/
/**
 * Load specific assets for classic builder
 */
function loadClassicBuilderAssets()
{
    $version = 'ver=' . QUIXNXT_VERSION;
  JFactory::getApplication()->registerEvent('onBeforeRender', 'removeJoomlaAssetsForClassicBuilder');

    JHtml::_('jquery.framework');
    JHtml::_('bootstrap.framework');

    $document = \JFactory::getDocument();

    // load common assets
    loadCommonBuilderScripts();
    // React Color Picker
    $document->addScript(QUIXNXT_URL . "/assets/js/react-color.js?$version");

    // adding fontawesome icons json file
    $fontAwesomeJSON = file_get_contents(__DIR__ . '/json/fa4.json');
    $document->addScriptDeclaration('window.fontAwesomeJSON = ' . $fontAwesomeJSON);

    // init quix builder required js variables
    $document->addScriptDeclaration("window.quixElementsURL = '/libraries/quixnxt/visual-builder/elements';");
    $document->addScriptDeclaration("window.quixTemplateURL = '" . QUIXNXT_TEMPLATE_URL . "'");
    $document->addScriptDeclaration("window.jRoot = '" . JUri::root() . "'");

    // var quix ( REQUIRED )
    quix_js_data('admin');

    // tinymace
    Assets::Js('tinymce', JUri::root(true) . '/media/editors/tinymce/tinymce.min.js');
    // materials
    Assets::Js('materialize-js', QUIXNXT_URL . '/assets/js/materialize.js');
    Assets::Css('materialize-css', QUIXNXT_URL . '/assets/css/materialize.css');
    // spiner
    Assets::Css('spinner', QUIXNXT_URL . '/assets/css/spinner.css');
    // image picker
    Assets::Js('image-picker', QUIXNXT_URL . '/assets/js/image-picker.js');
    // scrollbar
    Assets::Js('mousewheel', QUIXNXT_URL . '/assets/js/jquery.mousewheel.js');
    Assets::Css('mCustomScrollbar-css', QUIXNXT_URL . '/assets/css/jquery.mCustomScrollbar.css');
    Assets::Js('mCustomScrollbar-js', QUIXNXT_URL . '/assets/js/jquery.mCustomScrollbar.js');

    // font awesome
    Assets::Css('font-awesome', QUIXNXT_URL . '/assets/css/font-awesome.css');
    Assets::Css('magnific-popup-css', QUIXNXT_URL . '/assets/css/magnific-popup.css');

    //hide navbar if from an iframe modal
    $document->addScriptDeclaration("
    if(parent !== window){
      document.styleSheets[0].insertRule(\".navbar.navbar-inverse.navbar-fixed-top{display:none}\", 0);
    }
    (function($){ $(window).on('load',function(){
      $('.blocks-container .blocks').mCustomScrollbar({
        theme:\"dark\"
      });
    });})
    (jQuery);
  ");

    // joomla admin
    loadAssetsForJoomlaBackend();

    // Boot the asset loader
    bootAssetsLoader();
}
/**
 * Load builder js file (React) for classic builder
 */
function loadClassicBuilderReactScripts()
{
    // $MediaVersion = JFactory::getDocument()->getMediaVersion();
    $version = 'ver=' . QUIXNXT_VERSION; // . '&' . $MediaVersion;

    $config = \JComponentHelper::getParams('com_quix');
    $async = $config->get('async_builderjs', false);
    $dataAsync = ($async ? ' defer data-cfasync="false"' : '');

    // return '<script' . $dataAsync . ' src="' . QUIXNXT_URL . '/assets/builder/bundle.js?' . $version . '"></script>';
    return '<script defer data-cfasync="false" src="' . QUIXNXT_URL . '/assets/builder/bundle.js?' . $version . '"></script>';
}
/**
 * Load scripts for preview pages
 */
function loadClassicBuilderPreviewAssets()
{
    $document = \JFactory::getDocument();
    $version = QUIXNXT_VERSION;
    // Load Jquery
    JHtml::_('jquery.framework');
    // Load Bootstrap 3
    JHtml::_('bootstrap.framework');
    // Get config
    $config = JComponentHelper::getComponent('com_quix')->params;

    // jQuery easing
    Assets::Js('jQuery-easing', QUIXNXT_URL . '/assets/js/jquery.easing.js');

    // FontAwesome
    if ($config->get('load_fontawosome', 1)) {
        Assets::Css('font-awesome', QUIXNXT_URL . '/assets/css/font-awesome.css');
    }

    // Quix
    Assets::Js('quix-classic-js', QUIXNXT_URL . '/assets/js/quix.js', [], [], 1001);
    Assets::Css('quix-classic-css', QUIXNXT_URL . '/assets/css/quix-classic.css');

    // WOW + Animation
    Assets::Css('animate', QUIXNXT_URL . '/assets/css/animate.css');
    Assets::Js('wow', QUIXNXT_URL . '/assets/js/wow.js');

    // Magnific popup
    // TODO : Compress + minify with own enque script
    Assets::Css('magnific-popup', QUIXNXT_URL . '/assets/css/magnific-popup.css');
    Assets::Js('magnific-popup', QUIXNXT_URL . '/assets/js/jquery.magnific-popup.js');

    // Boot the asset loader
    bootAssetsLoader();
}

/********************************************
 * Load All Live Builder scripts (v2)
 ********************************************/
/**
 * Load assets for Live builder ( Builder mode )
 */
function loadLiveBuilderAssets()
{
    loadCommonBuilderScripts(true);

    // Load preview assets
    loadLiveBuilderPreviewAssets(false);
}
/**
 * Main builder script (react)
 */
function loadLiveBuilderReactScripts()
{
    // $MediaVersion = JFactory::getDocument()->getMediaVersion();
    $version = 'ver=' . QUIXNXT_VERSION;

    $config = \JComponentHelper::getParams('com_quix');
    $async = $config->get('async_builderjs', false);
    $dataAsync = ($async ? ' defer data-cfasync="false"' : '');
    $quixData = quix_js_data('site');

    $showDebug = true; //QUIXNXT_DEBUG; //true;
    $devtools = $showDebug ? '<script>window.__REACT_DEVTOOLS_GLOBAL_HOOK__ = window.parent.window.__REACT_DEVTOOLS_GLOBAL_HOOK__</script>' : '';
    $QUIXNXT_URL = \QuixAppHelper::getQuixMediaUrl();
    $builderScript = <<<JS
$devtools
    $quixData
    <script $dataAsync src="$QUIXNXT_URL/assets/builder/vendor.js?$version"></script>
    <script $dataAsync src="$QUIXNXT_URL/assets/builder/qxfb.js?$version"></script>
JS;

    return $builderScript; //$devtools . $quixData . $qxfb ;;
}
/**
 * Load preview page scripts
 * @since 3.0.0
 */

function loadLiveBuilderPreviewAssets($loadTemplateHelper = true)
{
    $version = 'ver=' . QUIXNXT_VERSION;
    $document = \JFactory::getDocument();
    // Asset Helper
    if ($loadTemplateHelper) {
        // we are loading builder
        JFactory::getApplication()->input->set('jchbackend', 1);

        $document->addScript(\QuixAppHelper::getQuixMediaUrl().'/builder/quix-helper.js', ['version' => $version], []);
    }

    // Load uikit js
    Assets::Js('quix-kit', \QuixAppHelper::getQuixMediaUrl().'/js/qxkit.js');

    // quix js
    Assets::Js('quix-fb', \QuixAppHelper::getQuixMediaUrl().'/js/quix-fb.js');
}
