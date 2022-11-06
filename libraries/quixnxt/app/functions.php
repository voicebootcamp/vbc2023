<?php

use Joomla\CMS\Exception\ExceptionHandler;
use Joomla\CMS\Http\HttpFactory;
use Joomla\Registry\Registry;

/**
 * Class QuixNxtBootstrap
 * Main helper file
 *
 * @since 3.0.0
 */
class QuixAppHelper
{
    /**
     * Check constant existence
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public static function setQuixConstants(): bool
    {
        $app = JFactory::getApplication();
        if ($app->isClient('administrator') && $app->input->get('view') === 'page' && $app->input->get('option') === 'com_quix') {
            return true;
        }

        if (defined('QUIX_PLATFORM_NAME')) {
            return true;
        }

        // Define constant
        define('QUIXNXT_PLATFORM_NAME', 'Joomla');
        define('QUIXNXT_ROOT_URI', JUri::root(true));
        define('QUIXNXT_ASSETS_DRIVER', 'Joomla');
        define('QUIXNXT_ELEMENTS_PATH', \QuixAppHelper::getQuixPath().'/visual-builder/elements');

        $componentInfo = \QuixAppHelper::qxGetComponentInfo();
        $isEditor      = array_get($_GET, 'layout') === 'edit';

        $params       = JComponentHelper::getParams('com_quix');
        $debug        = $params->get('dev_mode', false);
        $fast_loading = $params->get('fast_loading', true);
        $disabled_css = $params->get('disabled_css', false);

        define('QUIXNXT_EDITOR', $isEditor);
        define('QUIXNXT_DEBUG', $debug);
        define('QUIXNXT_FAST_LOADING', ! $debug && $fast_loading);
        define('QUIXNXT_DISABLED_CSS', $disabled_css);
        define('QUIXNXT_VERSION', filter_var($componentInfo['version'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        define('QUIXNXT_CACHE', ! $debug);

        define('QUIXNXT_SITE_URL', quix_untrailingslashit(QUIXNXT_ROOT_URI));
        define('QUIXNXT_URL', QUIXNXT_SITE_URL.'/media/quixnxt');
        define('QUIXNXT_PATH', dirname(__DIR__));

        // get default template
        $default_template = QuixAppHelper::quix_default_template();
        if (QUIXNXT_PLATFORM_NAME === 'Joomla') {
            define('QUIXNXT_TEMPLATE_PATH', JPATH_ROOT.'/templates/'.$default_template.'/quix');
            define('QUIXNXT_TEMPLATE_URL', QUIXNXT_SITE_URL.'/templates/'.$default_template.'/quix');
        }

        $config    = \JComponentHelper::getParams('com_media');
        $imagePath = $config->get('image_path', 'images');
        define('QUIXNXT_IMAGE_PATH', $imagePath);

        define('QUIXNXT_DEFAULT_ELEMENT_IMAGE', QUIXNXT_URL.'/assets/images/quix-logo.png');
        define('QUIXNXT_CACHE_PATH', QUIXNXT_PATH.'/app/cache');

        /*****************************
         *  FILE MANAGER LIB CONFIG
         *****************************/
        $config    = JComponentHelper::getParams('com_media');
        $imagePath = $config->get('image_path', 'images');

        defined('QUIXNXT_JMEDIA_PATH') or define('QUIXNXT_JMEDIA_PATH', JPATH_ROOT.'/'.$imagePath);

        if (QUIXNXT_DEBUG) {
            ini_set('display_errors', 1);
        }

        return true;
    }

    /**
     * Proxy call to JFactory::getApplication()
     *
     * @return  \Joomla\CMS\Application\CMSApplication
     * @throws \Exception
     * @since 3.0.0
     */
    public static function getApplicationInstance(): \Joomla\CMS\Application\CMSApplication
    {
        $app = JFactory::getApplication();

        if ($app) {
            return $app;
        }

        throw new RuntimeException('Failed to get application from JFactory');
    }

    /**
     * Proxy call to JFactory::getDocument()
     *
     * @return \JDocument
     * @since 3.0.0
     */
    public static function getCurrentDocument(): JDocument
    {
        $document = JFactory::getDocument();
        if ($document) {
            return $document;
        }

        throw new RuntimeException('Could not get document from JFactory');
    }

    /**
     * Proxy call to JFactory::getConfig()
     *
     * @return \JConfig|\Joomla\Registry\Registry
     * @since 3.0.0
     */
    public static function getConfig()
    {
        $config = JFactory::getConfig();
        if ($config) {
            return $config;
        }
        throw new RuntimeException('Could not get config from JFactory');
    }

    /**
     * Proxy call to JFactory::getSession()
     *
     * @return \Joomla\CMS\Session\Session
     *
     * @since 3.0.0
     */
    public static function getSession(): \Joomla\CMS\Session\Session
    {
        $config = JFactory::getSession();
        if ($config) {
            return $config;
        }
        throw new RuntimeException('Could not get config from JFactory');
    }

    /**
     * Proxy call to JFactory::getDbo()
     *
     * @return \JDatabaseDriver
     * @throws \Exception
     *
     * @since 3.0.0
     */
    public static function getDbo(): JDatabaseDriver
    {
        $db = JFactory::getDbo();

        if ($db) {
            return $db;
        }

        throw new RuntimeException('Failed to get Dbo from JFactory');
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     *
     * @since 3.0.0
     */
    public static function checkQuixIsVersion2(): bool
    {
        static $checkedQuixIsVersionIDs;

        $app = QuixAppHelper::getApplicationInstance();
        if ($app->isClient('administrator')) {
            return false;
        }

        $input  = $app->input;
        $option = $input->get('option');
        $id     = $input->get('id');
        $view   = $input->get('view', 'page');
        $type   = $input->get('type', '');

        if ( ! is_array($checkedQuixIsVersionIDs)) {
            $checkedQuixIsVersionIDs = [];
        }

        if ( ! empty($checkedQuixIsVersionIDs[$id])) {
            return $checkedQuixIsVersionIDs[$id];
        }

        $checkedQuixIsVersionIDs[$id] = true;
        if ($option === 'com_quix' && $id) {
            if (($view === 'form' && $type === 'collection') || $view === 'collection') {
                $source = 'collections';
            } else {
                $source = 'page';
            }

            $db  = QuixAppHelper::getDbo();
            $sql = 'SELECT builder FROM '.($source === 'page' ? '`#__quix`' : '`#__quix_collections`').' WHERE `id` = '.$id;
            $db->setQuery($sql);
            $result = $db->loadResult();

            if ($result === 'classic') {
                $checkedQuixIsVersionIDs[$id] = false;
            }
        }

        return $checkedQuixIsVersionIDs[$id];
    }

    /**
     * Determine Builder mode or preview mode
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public static function checkQuixIsBuilderMode(): bool
    {
        $app = QuixAppHelper::getApplicationInstance();
        if ($app->isClient('administrator')) {
            return false;
        }

        $input   = $app->input;
        $option  = $input->get('option');
        $view    = $input->get('view', '');
        $layout  = $input->get('layout', '');
        $builder = $input->get('builder', '');

        return $option === 'com_quix' && $view === 'form' && $layout === 'iframe' && $builder === 'frontend';
    }

    /**
     * Proxy call to QUIXNXT_URL
     *
     * @param  string|null  $path
     *
     * @return string
     * @since 3.0.0
     */
    public static function getQuixMediaUrl(?string $path = null): string
    {
        return JUri::root(true).'/media/quixnxt'.($path ? '/'.ltrim($path, '/') : null);
    }

    /**
     * Joomla to Quix media Path
     *
     * @return string
     * @since 3.0.0
     */
    public static function getQuixMediaPath(): string
    {
        return JPATH_ROOT.'/media/quixnxt';
    }

    /**
     * path to image
     *
     * @return string
     * @since 3.0.0
     */
    public static function getJoomlaImagePath(): string
    {
        $config = \JComponentHelper::getParams('com_media');

        return $config->get('image_path', 'images');
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    public static function getQuixMediaVersion(): string
    {
        if ( ! QUIXNXT_DEBUG) {
            return QUIXNXT_VERSION;
        }

        return \JFactory::getDocument()->getMediaVersion();
    }

    /**
     * Proxy call to QUIXNXT_URL
     *
     * @param  string|null  $path
     *
     * @return string
     * @since 3.0.0
     */
    public static function getQuixUrl(?string $path = null): string
    {
        return JUri::root(true).'/libraries/quixnxt'.($path ? '/'.ltrim($path, '/') : null);
    }

    /**
     * Joomla to Quix Library Path
     *
     * @return string
     * @since 3.0.0
     */
    public static function getQuixPath(): string
    {
        return JPATH_BASE.'/libraries/quixnxt';
    }

    /**
     * Joomla Warning
     *
     * @param  string  $msg
     * @param  string  $type
     *
     * @return string
     * @throws \Exception
     * @since 3.0.0
     */
    public static function setJoomlaMessage(string $msg, string $type = 'message'): void
    {
        \JFactory::getApplication()->enqueueMessage($msg, $type);
    }

    /**
     * render the main quix items
     *
     *
     * @param $item
     *
     * @return false|string
     * @throws \Exception
     * @since 3.0.0
     */
    // try {
    // QuixAppHelper::renderQuixInstance($this->item);
    // } catch (Exception $e) {
    //     ExceptionHandler::render($e);
    // }
    public static function renderQuixInstance(&$item)
    {
        if ( ! $item) {
            return;
        }

        $item->text = '';

        if ($item->builder === 'classic') {

            if (JFile::exists(JPATH_LIBRARIES.'/quix/app/init.php')) {
                global $QuixBuilderType;
                $QuixBuilderType = 'classic';

                jimport('quix.app.init');
                jimport('quix.app.bootstrap');

                try {
                    $item->text = quixRenderItem($item);
                } catch (Exception $e) {
                    ExceptionHandler::render($e);
                }
            } else {
                JFactory::getApplication()->enqueueMessage('Quix classic renderer not found. Please install Classic renderer', 'error');
            }
        } else {
            global $QuixBuilderType;
            $QuixBuilderType = 'frontend';

            try {
                QuixFrontendHelperView::prepareQuixPage($item);
            } catch (Exception $e) {
                ExceptionHandler::render($e);
            }
        }
    }

    public static function qxGetCollectionById($id)
    {
        JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_quix/models', 'QuixModel');
        require_once JPATH_SITE.'/components/com_quix/helpers/quix.php';

        $app   = JFactory::getApplication();
        $model = JModelLegacy::getInstance('Collection', 'QuixModel', ['ignore_request' => true]);
        $model->setState('list.select', 'a.id, a.uid, a.title, a.state, a.type, a.builder,a.builder_version, a.data, a.metadata, a.params');

        // Retrieve Content
        try {
            $item = $model->getData($id); //!$app->isClient('administrator') was before. now global. make sure works.!
        } catch (Exception $e) {
            $item = $model->getItem($id); // was for site
        }

        return $item;
    }

    public static function qxGetCollectionInfoById($id)
    {
        JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_quix/models', 'QuixModel');
        $model = JModelLegacy::getInstance('Collection', 'QuixModel', ['ignore_request' => true]);

        // Retrieve Content
        return $model->getData($id);
    }

    /**
     * @return mixed
     */
    public static function qxGetComponentInfo()
    {
        $extension = JTable::getInstance('extension');
        $id        = $extension->find(['element' => 'com_quix']);
        $extension->load($id);
        $componentInfo = json_decode($extension->manifest_cache, true);

        return $componentInfo;
    }

    public static function qxGetBlocks($builder = 'frontend')
    {
        $input = JFactory::getApplication()->input;

        // filters params
        $license     = $input->get('license', '');
        $type        = $input->get('type', '');
        $min_version = $input->get('min_version', '');

        // absolute url of list json
        $uri    = \JUri::getInstance();
        $secure = $uri->isSsl();
        $url    = ($secure ? 'https' : 'http').'://getquix.net/index.php?option=com_quixblocks&view=category&format=json';

        if ($license) {
            $url .= '&license='.$license;
        }
        if ($type) {
            $url .= '&type='.$type;
        }
        if ($min_version) {
            $url .= '&min_version='.$min_version;
        }

        $result = \QuixAppHelper::getResponsefromAPI($url);

        return ($result ? $result : '{"success": false}');
    }

    public static function getResponsefromAPI($url)
    {
        // Get the handler to download the blocks
        try {
            $options = new Registry;
            $result  = HttpFactory::getHttp($options, ['curl'])->get($url);

            if ( ! $result || ($result->code != 200 && $result->code != 310)) {
                $exception = new Exception(JText::_('Server connection error!'));

                return new JResponseJSON($exception);
            }

            // json decode and test output for json error
            json_decode($result->body);
            if (json_last_error() == JSON_ERROR_NONE) {
                return $result->body;
            }
        } catch (RuntimeException $e) {
            $exception = new Exception($e->getMessage());

            return new JResponseJSON($exception);
        }
    }

    public static function quix_default_template()
    {
        $db    = JFactory::getDBO();
        $query = 'SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1';
        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     * Proxy call to JFolder::create()
     *
     * @param $path
     *
     * @since 3.0.0
     */
    public static function ensureFolderExists($path)
    {
        if ( ! file_exists($path)) {
            JFolder::create($path);
        }
    }

    /**
     * Get compiled assets file path.
     *
     * @return mixed|null
     * @since 3.0.0
     */
    public static function get_compiled_assets_path()
    {
        return array_reduce(['frontend'], static function ($path, $dir) {
            $path .= $dir.'/';

            QuixAppHelper::ensureFolderExists($path);

            return $path;
        }, JPATH_BASE.'/media/quix/');
    }

    /**
     * Check permission
     *
     * @param  int  $acl
     *
     * @return bool
     * @since 3.0.0
     */
    public static function __qxAcl($acl = 0)
    {
        // first check ACL
        $user = JFactory::getUser();

        // If no access filter is set, the layout takes some responsibility for display of limited information.
        $groups = $user->getAuthorisedViewLevels();

        return ! ( ! empty($acl) and ! in_array($acl, $user->groups, true) and ! in_array($acl, $groups, true));
    }

    /**
     * Determine if the block is dynamic
     *
     * @param $type
     * @param $filename
     *
     * @since 3.0.0
     */
    public static function __qxDynamic($type, $filename)
    {
        $data = file_get_contents(get_compiled_json_path().$filename);
        $data = unserialize($data);

        $data['__qxDynamic'] = true;
        $path                = \QuixAppHelper::getQuixPath().'/visual-builder/'.$type.'/view.php';
        require_once \QuixAppHelper::getQuixPath().'/visual-builder/'.$type.'/global.php';
        $engine = new QuixNxt\Renderers\TwigEngine();
        echo $engine->get($path, $data);
    }

    /**
     * @param        $bytes
     * @param  int  $precision
     *
     * @return string
     *
     * @since 3.0.0
     */
    public static function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * @param           $id
     * @param  string  $style
     *
     * @return false|string|void
     * @throws \Exception
     *
     * @since 3.0.0
     */
    public static function qxModuleById($id, $style = 'raw')
    {
        $renderer = QuixAppHelper::getCurrentDocument()->loadRenderer('module');

        $db    = QuixAppHelper::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
              ->from('#__modules')
              ->where('published = '. 1)
              ->where('id = '.$id);
        $db->setQuery($query);
        $module = $db->loadObject();

        // check if module not found
        if ( ! isset($module->id)) {
            return null;
        }
        $params = json_decode($module->params, true);

        ob_start();

        if ($module->id > 0) {
            echo $renderer->render($module, $params);
        }

        return ob_get_clean();
    }

    /**
     * @param  false  $details
     * @param  string  $builder
     * @param  string  $type
     *
     * @return array
     * @since 3.0.0
     */
    public static function qxGetCollections($details = false, $builder = '*', $type = ''): array
    {
        JModelLegacy::addIncludePath(JPATH_SITE.'/administrator/components/com_quix/models', 'QuixModel');

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Collections', 'QuixModel', ['ignore_request' => true]);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 999);

        if ( ! $details) {
            $model->setState('list.select', 'a.id, a.uid, a.title, a.type, a.builder, a.builder_version');
        }

        $model->setState('filter.state', 1);

        // set builder filter
        $model->setState('filter.builder', $builder);

        // set template types
        $model->setState('filter.collection', $type);

        // Access filter
        $access = ! JComponentHelper::getParams('com_quix')->get('show_noauth');
        $model->setState('filter.access', $access);

        // Retrieve Content
        $items = $model->getItems();

        if ( ! $items) {
            return [];
        }

        if ( ! $details) {
            return $items;
        }

        foreach ($items as $key => $item) {
            if ($item->builder_version < '4.0.0') {
                QuixFrontendHelperView::checkOldDataAndMigrate($item);
            }
        }

        return $items;
    }

    /**
     * @param $id
     *
     * @return null
     * @since 3.0.0
     */
    public static function renderQuixTemplate($id)
    {
        // Include dependencies
        $collection = QuixAppHelper::qxGetCollectionInfoById($id);
        if ( ! $collection) {
            $collection->text = '<p>invalid quix template!</p >';

            return $collection;
        }

        // render main item
        QuixAppHelper::renderQuixInstance($collection);

        return $collection;
    }

}

if ( ! function_exists('get_compiled_css_path')) {
    /**
     * Get compiled css file path.
     *
     * @return  array
     * @since 3.0.0
     */
    function get_compiled_css_path()
    {
        return array_reduce(['frontend', 'css'], static function ($path, $dir) {
            $path .= $dir.'/';

            QuixAppHelper::ensureFolderExists($path);

            return $path;
        }, JPATH_BASE.'/media/quix/');
    }
}


if ( ! function_exists('is_compiled_css_exists')) {
    /**
     * Determine compiled css file existence.
     *
     * @param $file
     *
     * @return bool
     *
     * @since 3.0.0
     */
    function is_compiled_css_exists($file)
    {
        return file_exists(get_compiled_css_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_css')) {
    /**
     * Get compiled css file path.
     *
     * @param $file
     *
     * @return false|string
     *
     * @since 3.0.0
     */
    function get_compiled_css($file)
    {
        return file_get_contents(get_compiled_css_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_js_path')) {
    /**
     * Get compiled js file path.
     *
     * @return mixed|null
     *
     * @since 3.0.0
     */
    function get_compiled_js_path()
    {
        return array_reduce(['frontend', 'js'], static function ($path, $dir) {
            $path .= $dir.'/';

            QuixAppHelper::ensureFolderExists($path);

            return $path;
        }, JPATH_BASE.'/media/quix/');
    }
}

if ( ! function_exists('is_compiled_js_exists')) {
    /**
     * Determine compiled js file existence.
     *
     * @param $file
     *
     * @return bool
     *
     * @since 3.0.0
     */
    function is_compiled_js_exists($file)
    {
        return file_exists(get_compiled_js_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_html_path')) {
    /**
     * Get compiled html file path.
     *
     * @return mixed|null
     * @since 3.0.0
     */
    function get_compiled_html_path()
    {
        return array_reduce(['frontend', 'html'], static function ($path, $dir) {
            $path .= $dir.'/';

            QuixAppHelper::ensureFolderExists($path);

            return $path;
        }, JPATH_BASE.'/media/quix/');
    }
}

if ( ! function_exists('is_compiled_html_exists')) {
    /**
     * Determine compiled html file existence.
     *
     * @param $file
     *
     * @return bool
     * @since 3.0.0
     */
    function is_compiled_html_exists($file)
    {
        return file_exists(get_compiled_html_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_html')) {
    /**
     * Get compiled html file.
     *
     * @param $file
     *
     * @return false|string
     * @since 3.0.0
     */
    function get_compiled_html($file)
    {
        ob_start();
        include get_compiled_html_path()."/{$file}";
        // This contains the output of your-template.php
        // Manipulate $output...
        // Clear the buffer.

        return ob_get_clean(); // Print everything.
    }
}

if ( ! function_exists('get_compiled_js')) {
    /**
     * Get compiled js file.
     *
     * @param $file
     *
     * @return false|string
     * @since 3.0.0
     */
    function get_compiled_js($file)
    {
        return file_get_contents(get_compiled_js_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_json_path')) {
    /**
     * Get compiled json file path.
     *
     * @return mixed|null
     * @since 3.0.0
     */
    function get_compiled_json_path()
    {
        return array_reduce(['frontend', 'json'], static function ($path, $dir) {
            $path .= $dir.'/';

            QuixAppHelper::ensureFolderExists($path);

            return $path;
        }, JPATH_BASE.'/media/quix/');
    }
}

if ( ! function_exists('is_compiled_json_exists')) {
    /**
     * Determine compiled json file existence.
     *
     * @param $file
     *
     * @return bool
     * @since 3.0.0
     */
    function is_compiled_json_exists($file)
    {
        return file_exists(get_compiled_json_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_json')) {
    /**
     * Get compiled json file.
     *
     * @param $file
     *
     * @return false|string
     * @since 3.0.0
     */
    function get_compiled_json($file)
    {
        return file_get_contents(get_compiled_json_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_builder_path')) {
    /**
     * Get compiled builder file path.
     *
     * @return mixed|null
     * @since 3.0.0
     */
    function get_compiled_builder_path()
    {
        return array_reduce(['frontend', 'builder'], static function ($path, $dir) {
            $path .= $dir.'/';

            QuixAppHelper::ensureFolderExists($path);

            return $path;
        }, JPATH_BASE.'/media/quix/');
    }
}

if ( ! function_exists('is_compiled_builder_exists')) {
    /**
     * Determine compiled builder file existence.
     *
     * @param $file
     *
     * @return bool
     * @since 3.0.0
     */
    function is_compiled_builder_exists($file)
    {
        return file_exists(get_compiled_builder_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_builder')) {
    /**
     * Get compiled builder file.
     *
     * @param $file
     *
     * @return false|string
     * @since 3.0.0
     */
    function get_compiled_builder($file)
    {
        return file_get_contents(get_compiled_builder_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_template_path')) {
    /**
     * Get compiled template file path.
     *
     * @return mixed|null
     * @since 3.0.0
     */
    function get_compiled_template_path()
    {
        return array_reduce(['frontend', 'builder'], static function ($path, $dir) {
            $path .= $dir.'/';

            QuixAppHelper::ensureFolderExists($path);

            return $path;
        }, JPATH_BASE.'/media/quix/');
    }
}

if ( ! function_exists('is_compiled_template_exists')) {
    /**
     * Determine compiled template file existence.
     *
     * @param $file
     *
     * @return bool
     * @since 3.0.0
     */
    function is_compiled_template_exists($file)
    {
        return file_exists(get_compiled_template_path()."/{$file}");
    }
}

if ( ! function_exists('get_compiled_template')) {
    /**
     * Get compiled template file.
     *
     * @param $file
     *
     * @return false|string
     * @since 3.0.0
     */
    function get_compiled_template($file)
    {
        return file_get_contents(get_compiled_template_path()."/{$file}");
    }
}

if ( ! function_exists('elementRequestedFromBuilder')) {
    /**
     *
     * Check if the requests came from builder mode
     *
     * @return bool
     * @throws \Exception
     *
     * @since 3.0.0
     */
    function elementRequestedFromBuilder()
    {
        return JFactory::getApplication()->input->get('task', null, 'string') === 'getElements';
    }
}

if ( ! function_exists('quixAppRenderItem')) {
    /**
     *
     * Check if the requests came from builder mode
     *
     * @param  null  $id
     * @param  string  $type
     *
     * @return null|string
     * @throws \Exception
     * @since 3.0.0
     */
    function quixAppRenderItem($id, string $type = 'collection'): ?string
    {
        if ( ! $id) {
            return '<p>'.JText::_('QUIX_INVALID_ID').'</p >';
        }

        switch ($type) {
            case "collection":
            default:
                $item = QuixAppHelper::qxGetCollectionById($id);
        }

        if ( ! $item) {
            return '<p>'.$id.' '.JText::_('QUIX_ITEM_NOT_FOUND').'</p >';
        }

        // render main item
        QuixAppHelper::renderQuixInstance($item);

        return $item->text;
    }
}

if ( ! function_exists('quixNxtRenderItem')) {
    /**
     * legacy function if needed to render
     * quix 2 method.
     *
     * @param $item
     *
     * @return string|null
     * @throws \Exception
     * @since 4.1.6
     */
    function quixNxtRenderItem($item): ?string
    {
        // render main item
        QuixAppHelper::renderQuixInstance($item);

        return $item->text;

    }
}

if ( ! function_exists('qxNxtGetCollectionInfoById')) {
    /**
     * legacy function if needed to render
     * quix 2 method.
     *
     * @param $id
     *
     * @throws \Exception
     * @since 4.1.6
     */
    function qxNxtGetCollectionInfoById($id)
    {
        return QuixAppHelper::qxGetCollectionInfoById($id);
    }
}
if ( ! function_exists('qxNxtGetCollectionById')) {
    /**
     * legacy function if needed to render
     * quix 2 method.
     *
     * @param $id
     *
     * @throws \Exception
     * @since 4.1.6
     */
    function qxNxtGetCollectionById($id)
    {
        return QuixAppHelper::qxGetCollectionById($id);
    }
}


