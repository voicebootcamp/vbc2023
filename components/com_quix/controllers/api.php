<?php

/**
 * @version    2.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @since      3.0.0
 */
defined('_JEXEC') or die;

use FileManager\FileManager;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Response\JsonResponse;
use MatthiasMullie\Minify\CSS;
use QuixNxt\Utils\Image\Download;
use QuixNxt\Utils\Image\Optimizer;
use QuixNxt\Utils\Schema;

/**
 * Handle App API request through one controller
 *
 * @since  2.0
 * @since  3.0.0
 */
class QuixControllerApi extends JControllerLegacy
{
    /**
     * Instance of image optimizer.
     *
     * @var \QuixNxt\Image\Optimizer
     * @since 3.0.0
     */
    protected $imageOptimizer = null;

    /**
     * Responsive sizes.
     *
     * @var array
     * @since 3.0.0
     */
    protected $responsiveSizes = [];

    /**
     * Responsive breakpoints.
     *
     * @var array
     * @since 3.0.0
     */
    protected $responsiveBreakPoints = [];

    /**
     * Image quality of responsive image.
     *
     * @var integer it should be 0 to 100
     * @since 3.0.0
     */
    protected $responsiveImageQuality = 100;

    /**
     * Create a new instance of TwigEngine.
     *
     * @since 3.0.0
     */
    public function __construct()
    {
        parent::__construct();

        $this->responsiveBreakPoints = [
            'large_desktop' => 1900,
            'desktop'       => 1400,
            'tablet'        => 1024,
            'mobile'        => 768,
            'mini'          => 400
        ];

        $config           = JComponentHelper::getParams('com_quix');
        $responsive_image = (array) $config->get(
            'responsive_image',
            ['quality' => 80, 'responsive_image' => ['large_desktop' => 1900, 'desktop' => 1400, 'tablet' => 1024, 'mobile' => 786, 'mini' => 400]]
        );
        if (!isset($responsive_image['quality'])) {
            return;
        }

        $this->responsiveImageQuality = (int) $responsive_image['quality'];
        unset($responsive_image['quality']);

        foreach ($responsive_image as $breakPoint => $size) {
            $this->responsiveSizes[$this->responsiveBreakPoints[$breakPoint]] = (int) $size;
        }
    }

    /*
    * Method to check the image
    * previous: hasImage
    * @since 3.0.0
  */
    public function checkImage()
    {
        // Reference global application object
        $app = JFactory::getApplication();

        // JInput object
        $input = $app->input;

        // Requested format passed via URL
        $format = strtolower($input->getWord('format', 'json'));

        // Requested element name
        $path = strtolower($input->get('path', '', 'string'));

        // check if path passed
        if (!$path) {
            $results = new InvalidArgumentException(JText::_('COM_QUIX_NO_ARGUMENT'), 403);
        }

        // first check if its from default template
        if (is_file(JPATH_ROOT . $path)) {
            $results = true;
        } else {
            $results = new InvalidArgumentException(JText::_('COM_QUIX_FILE_NOT_EXISTS'), 404);
        }

        // return result
        echo new JResponseJson($results, null, false, $input->get('ignoreMessages', true, 'bool'));

        $app->close();
    }

    /*
    * Method to encode image or data
    * previous name: base64EncodedJson
    * @since 3.0.0
  */
    public function encodeBase64Json()
    {
        // Reference global application object
        $app   = JFactory::getApplication();
        $input = $app->input;
        $input->post->getArray();
        // ssl header
        $arrContextOptions = [
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ];

        $post = $input->post->getArray();
        if (!count($post)) {
            $post = @file_get_contents('php://input');
        }

        // taking posted data
        $quix = json_decode($post, true)['quix'];

        // preg matching
        preg_match_all('/([-a-z0-9_\/:.]+\.(jpg|jpeg|png))/i', $quix, $matches);

        $base64EncodedImage = [];

        // looping throw all original images
        // and setuping base64 encoded image
        foreach ($matches[0] as $key => $image) {
            $type = $matches[2][$key];

            if (!isset($base64EncodedImage[$image])) {
                $base64EncodedImage[$image] = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents(
                        $this->getSrcLink($image),
                        false,
                        stream_context_create($arrContextOptions)
                    ));
            }
        }

        $originalImages = array_keys($base64EncodedImage);

        // replacing all original images with base64 encoded images
        $replacedImage = str_replace($originalImages, $base64EncodedImage, $quix);

        // return result
        echo new JResponseJson(['config' => $replacedImage], null, false, true);

        $app->close();
    }

    /*
    * Method to encode image or data
    * previous name: base64EncodedJson
    * @since 3.0.0
  */
    public function exportCollection()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType.'; charset='.$app->charSet);
        $app->sendHeaders();

        // ssl header
        $arrContextOptions = [
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ];

        // Check if user token is valid.
        if ( ! JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        $input = $app->input;
        $id    = $input->get('id', '', 'int');
        if (!$id) {
            echo new JResponseJSON('No id found!');
            $app->close();
        }

        $type = $input->get('type', 'collection', 'string');
        if ($type == 'collection') {
            $result                  = QuixAppHelper::qxGetCollectionById($id);
            $data                    = json_decode($result->data, true);
            $data['builder_version'] = $result->builder_version;

            // $result->data            = json_decode($data);
            // $dataResponse = json_encode($data);
            $dataResponse = $data;
        } else {
            $db    = JFactory::getDBo();
            $query = $db->getQuery(true);
            $query->select('*')->from('#__quix')->where('id = ' . $id);
            $db->setQuery($query);
            $result = $db->loadObject();

            $data         = [
                'title'           => $result->title,
                'type'            => 'layout',
                'groups'          => [],
                'builder_version' => $result->builder_version,
                'data'            => json_decode($result->data)
            ];

            // $result->data = json_encode($data);
            // $dataResponse = json_encode($data);
            $dataResponse = $data;
        }

        // taking posted data
        // $quix   = $result->data;
        $quix   = $dataResponse;
        $config = ComponentHelper::getComponent('com_quix')->params;

        if ($config->get('export_with_image', '0') === '0') {
            echo new JsonResponse(['config' => $quix], null, false, true);
            $app->close();
        }

        // preg matching
        preg_match_all('/([-a-z0-9_\/:.]+\.(jpg|jpeg|png))/i', $quix, $matches);

        $base64EncodedImage = [];

        // looping throw all original images
        // and setting up base64 encoded image
        foreach ($matches[0] as $key => $image) {
            $type = $matches[2][$key];

            if (!isset($base64EncodedImage[$image])) {
                $base64EncodedImage[$image] = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents(
                        $this->getSrcLink($image),
                        false,
                        stream_context_create($arrContextOptions)
                    ));
            }
        }

        $originalImages = array_keys($base64EncodedImage);

        // replacing all original images with base64 encoded images
        $replacedImage = str_replace($originalImages, $base64EncodedImage, $quix);

        // return result
        echo new JResponseJson(['config' => $replacedImage], null, false, true);

        $app->close();
    }

    /**
     * Get image source link
     *
     * @since 3.0.0
     */
    protected function getSrcLink($src)
    {
        if (preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|(libraries)|([a-z0-9-].)+(:[0-9]+)(\/.*)?$/', $src)) {
            return $src;
        }

        return JUri::root() . 'images' . $src;
    }

    /**
     * Gets the parent items of the menu location currently.
     *
     * @return  json encoded output and close app
     *
     * @since   2.0
     * @since   3.0.0
     */
    public function getParentItem()
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_menus/models');
        $app = JFactory::getApplication();

        $results  = [];
        $menutype = $this->input->get->get('menutype');

        if ($menutype) {
            $model = $this->getModel('Items', 'MenusModel', []);
            $model->getState();
            $model->setState('filter.menutype', $menutype);
            $model->setState('list.select', 'a.id, a.title, a.level');
            $model->setState('list.start', '0');
            $model->setState('list.limit', '0');

            /** @var  MenusModelItems $model * @since 3.0.0
             */
            $results = $model->getItems();

            // Pad the option text with spaces using depth level as a multiplier.
            for ($i = 0, $n = count($results); $i < $n; $i++) {
                $results[$i]->title = str_repeat(' - ', $results[$i]->level) . $results[$i]->title;
            }
        }

        // Output a JSON object
        echo json_encode($results);

        $app->close();
    }

    /**
     * Method to create menu.
     *
     * @return  json result
     *
     * @since   2.0
     * @since   3.0.0
     */
    public function createMenu()
    {
        // Check for request forgeries.
        // echo JSession::getFormToken();die;
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_menus/models');
        JTable::addIncludePath(JPATH_SITE . '/administrator/components/com_menus/tables');
        $app   = JFactory::getApplication();
        $title = $app->input->post->get('title', '', 'string');
        if (empty($title)) {
            echo new JResponseJson(new Exception('Title required'));
            $app->close();

            return;
        }

        $alias = $app->input->post->get('alias');

        $menu = $app->input->post->get('menu');
        if (empty($menu)) {
            echo new JResponseJson(new Exception('Menu selection is required!'));
            $app->close();

            return;
        }

        $parentid = $app->input->post->get('parentid');
        if (empty($parentid)) {
            echo new JResponseJson(new Exception('Select menu parant!'));
            $app->close();

            return;
        }

        $link         = $app->input->post->get('link', '', 'string', 'raw');
        $component_id = JComponentHelper::getComponent('com_quix')->id; // update it
        $language     = '*';
        $published    = 1;
        $type         = 'component';

        $data  = [
            'id'   => '', 'link' => $link, 'parent_id' => $parentid, 'menutype' => $menu, 'title' => $title, 'alias' => $alias,
            'type' => $type, 'published' => $published, 'language' => $language, 'component_id' => $component_id
        ];
        $model = $this->getModel('Item', 'MenusModel', []);

        try {
            if ($model->save($data)) {
                $Itemid = $model->getState('item.id');
                $link   = $link . (parse_url($link, PHP_URL_QUERY) ? '&' : '?') . 'Itemid=' . $Itemid;
                echo new JResponseJson(['Itemid' => $Itemid, 'link' => $link]);
            } else {
                echo new JResponseJson(new Exception($model->getError()));
            }
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }
        $app->close();
    }

    /**
     * Method to handle file manager operation
     *
     * @return  object
     *
     * @since   2.0
     * @since   3.0.0
     */
    public function uploadMedia()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        (new FileManager(__DIR__ . '/../filemanager/config.php'));
        exit;
    }

    /**
     * Prepare Joomla content
     *
     * @return  object
     *
     * @since   2.0
     * @since   3.0.0
     */
    public function prepareContent()
    {
        $app = JFactory::getApplication();
        $app->input->set('tmpl', 'component');
        $text = $app->input->get('content', '', 'raw');

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        echo JHtml::_('content.prepare', $text);

        jexit();
    }

    /**
     * get Icons pack, store it and return the content
     *
     * @return  object
     *
     * @since   2.0
     * @since   3.0.0
     */
    public function getIcons()
    {
        // $profiler = new JProfiler();

        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        $app->setHeader('Cache-Control', 'max-age=86400');
        $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        // now call the cache
        $cache   = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
        $cacheid = 'QuixFlatIcons30';
        $cache->setCaching(true);
        $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000

        // return from cache
        $output = $cache->get($cacheid);

        // if no cache, read from file
        if (empty($output)) {
            // this will check local files, if not found will call from server
            $output = QuixFrontendHelper::getFlatIconsfromLocal();
            // store to cache
            $cache->store($output, $cacheid);
        }
        $cache->setCaching(JFactory::getApplication()->get('caching'));

        // response json
        echo $output;

        // close the output
        $app->close();
    }

    public function getTemplates()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        $source  = $app->input->get('source', 'local');
        $type    = $app->input->get('type', "default");
        $details = $app->input->get('details', false);

        // return from cache

        if ($source === 'local') {
            $result_list = QuixAppHelper::qxGetCollections($details, 'frontend', $type);
            // dd($result_list);

            /**
             * checking builder version to ignore my element conflict.
             * Now older custom elements will be ignored.
             * 
             * Here, we ignore version maping. 
             * So that the user can back his older version properly. 
             */

            $filered_list = array();

            foreach ($result_list as $result) {
                $builderVersion = (int)$result->builder_version;

                if ($builderVersion <4) {
                    continue;
                }

                if($type === 'element'){
                    $result->data = QuixFrontendHelperAssets::processRawData($result->data, $result->builder_version);
                }


                array_push($filered_list, $result);
            }

            $output = $filered_list ? json_encode($filered_list) : json_encode([]);

        } else {
            $app->setHeader('Cache-Control', 'max-age=3600');
            // online from get quix
            $output = \QuixAppHelper::qxGetBlocks();
        }
        $app->sendHeaders();

        // response json
        echo $output;

        // close the output
        $app->close();
    }

    public function getFileContent()
    {
        $app = JFactory::getApplication();
        $app->setHeader('Cache-Control', 'max-age=3600');
        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        if ($app->input->get('file', '') == 'animation') {
            // now call the cache
            $cache   = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
            $cacheid = 'quix.animation';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000

            // return from cache
            $output = $cache->get($cacheid);

            // if no cache, read from file
            if (empty($output)) {
                $path = QUIXNXT_PATH . '/app/frontend/animation.twig';
                try {
                    $output = file_get_contents($path);
                    $cache->store($output, $cacheid);
                } catch (Exception $e) {
                    $output = 'Does not exist: ' . $path;
                }
                // store to cache
            }
            $cache->setCaching(JFactory::getApplication()->get('caching'));

            echo $output;
            jexit();
        } elseif ($app->input->get('file', '') == 'global') {
            // now call the cache
            $cache   = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
            $cacheid = 'quix.global';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000

            // return from cache
            $output = $cache->get($cacheid);

            // if no cache, read from file
            if (empty($output)) {
                $path = QUIXNXT_PATH . '/app/frontend/global.twig';
                try {
                    $output = file_get_contents($path);
                    $cache->store($output, $cacheid);
                } catch (Exception $e) {
                    $output = 'Does not exist: ' . $path;
                }
                // store to cache
            }
            $cache->setCaching(JFactory::getApplication()->get('caching'));

            echo $output;
            jexit();
        } else {
            $path = $app->input->get('path', '', 'base64');
            $ext  = $app->input->get('ext');
            $path = base64_decode($path);
            if ($ext == 'php') {
                $exception = new Exception(JText::_('Invalid File Extension'));
                echo new JResponseJSON($exception);
                jexit();
            } else {
                // $path = '/app/frontend/elements/alert/element.svg';
                try {
                    $content = file_get_contents(QUIXNXT_PATH . $path . '.' . $ext);
                } catch (Exception $e) {
                    $content = 'Does not exist: ' . QUIXNXT_PATH . $path . '.' . $ext;
                }
                echo new JResponseJSON($content);
                jexit();
            }
        }

        // close the output
        jexit();
    }

    public function getTemplate()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        // set cache
        $app->setHeader('Cache-Control', 'max-age=3600');

        $id   = $app->input->get('id');
        $item = QuixAppHelper::qxGetCollectionById($id);

        $item->data = QuixFrontendHelperAssets::processRawData($item->data, $item->builder_version);
        $output     = json_encode($item);
        echo $output;

        // close the output
        $app->close();
    }

    public function getJoomlaModules()
    {
        $app = JFactory::getApplication();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            jexit();
        }

        $items = QuixFrontendHelper::getJoomlaModules();

        echo new JResponseJSON($items);
        jexit();
    }

    public function getJoomlaCategories()
    {
        $options = JHtml::_('category.options', 'com_content');
        array_unshift($options, JHtml::_('select.option', 'root', JText::_('JGLOBAL_ROOT')));

        echo new JResponseJSON($options);
        jexit();
    }

    public function getJoomlaModule()
    {
        $app = JFactory::getApplication();
        $app->input->set('tmpl', 'component');

        // Send json mime type.
        // $app->mimeType = 'application/json';
        // $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        // $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            // $exception = new Exception(JText::_('JINVALID_TOKEN'));
            // echo new JResponseJSON($exception);
            echo '<p class="qx-alert qx-alert-warning qx-m-0">' . JText::_('JINVALID_TOKEN') . '</p>';
            $app->close();
        }

        $id    = $app->input->get('id');
        $style = $app->input->get('style');

        if (empty($id)) {
            echo '<p class="qx-alert qx-alert-warning qx-m-0">' . JText::_('Please select a module first!') . '</p>';
            $app->close();
        }

        $db    = JFactory::getDBo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__modules')
            ->where('published = ' . 1)
            ->where('id = ' . $id);
        $db->setQuery($query);
        $module = $db->loadObject();

        if (!isset($module->module) && empty($module->params)) {
            echo 'Sorry! Module not found or not published! please check your module.';
            $app->close();
        }

        $mparams = json_decode($module->params, true);
        $enabled = ModuleHelper::isEnabled($module->module);

        $result = '';
        if ($enabled) {
            // Load Jquery in case any module does not have it as we are loading backdoor way
            JHtml::_('jquery.framework');

            $moduleinfo = ModuleHelper::getModule($module->module, $module->title);
            $info       = (object) array_merge((array) $moduleinfo, (array) $module);

            $result = ModuleHelper::renderModule($info, $mparams);
        }
        // $output = json_encode($result);
        // response json
        // echo $output;
        echo $result;

        // close the output
        // $app->close();
    }

    /**
     * Load Quix Template
     *
     * @throws \Exception
     * @since 3.0.0
     */
    public function getQuixTemplate()
    {
        $app = JFactory::getApplication();
        $app->input->set('tmpl', 'component');

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            echo '<p class="qx-alert qx-alert-warning qx-m-0">' . JText::_('JINVALID_TOKEN') . '</p>';
            $app->close();
        }

        $id = $app->input->get('id');

        if (empty($id)) {
            echo '<p class="qx-alert qx-alert-warning qx-m-0">'.JText::_('Please select a template first!').'</p>';
            $app->close();
        }

        // lets render it
        // Include dependencies
        $collection = QuixAppHelper::qxGetCollectionInfoById($id);
        if (!$collection) {
            echo '<p>invalid quix template!</p >';
            $app->close();
        }

        // render main item
        QuixAppHelper::renderQuixInstance($collection);
        echo $collection->text;

        return true;
    }

    public function getWebFonts()
    {
        $app = JFactory::getApplication();
        // Send json mime type.
        $app->mimeType = 'application/json';
        $app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
        $app->setHeader('Cache-Control', 'max-age=3600');
        $app->sendHeaders();

        // Check if user token is valid.
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        // now call the cache
        $cache   = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE . DIRECTORY_SEPARATOR . 'cache']);
        $cacheid = 'QuixWebFonts30';
        $cache->setCaching(true);
        $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000

        // return from cache
        $output = $cache->get($cacheid);

        // if no cache, read from file
        if (empty($output)) {
            // this will check local files, if not found will call from server
            $output = QuixFrontendHelper::getGoogleFontsJSONfromLocal();
            // store to cache
            $cache->store($output, $cacheid);
        }

        // response json
        echo $output;

        // close the output
        $app->close();
    }

    public function storeCompiledCSs()
    {
        $app = JFactory::getApplication();
        if (!JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            $app->close();
        }

        $content     = $app->input->get('compiled_css', '', 'RAW');
        $fontContent = json_encode($app->input->get('font_families', '', 'RAW'));

        $compiledFilePath = JPATH_BASE . '/media/quix/frontend/css/' . $app->input->get('compiled_css_file_name');

        if (strlen($content)) {
            $cssMinifier = new CSS();
            $cssMinifier->add($content);

            file_put_contents($compiledFilePath, $cssMinifier->minify());
        }
        if (strlen($fontContent)) {
            file_put_contents($compiledFilePath . '.font-families', $fontContent);
        }

        echo new JResponseJSON('Compiled Data Saved');

        $app->close();
    }

    /**
     * Method to check users license
     *
     * @return  object
     *
     * @since   2.0
     * @since   3.0.0
     */
    public function validation()
    {
        require_once JPATH_COMPONENT . '/helpers/quix.php';

        // Reference global application object
        $app = JFactory::getApplication();

        // check pro version + activation
        $free = QuixHelper::isFreeQuix();
        $pro  = QuixHelper::isProActivated();
        if ($free or empty($pro) or $pro == null or ! $pro) {
            // echo new JResponseJson('Thank you. Valid Pro license has been found.'); // TODO:remove after testing
            $err = new Exception('No valid pro license has been found or license period has expired!.');
            echo new JResponseJson($err);
        } else {
            echo new JResponseJson('Thank you. Valid Pro license has been found.');
        }

        $app->close();
    }

    /**
     * Method to check users license
     *
     * @return  object
     *
     * @throws \Exception
     * @since   2.0
     * @since   3.0.0
     */
    public function licenseStatus()
    {
        require_once JPATH_COMPONENT . '/helpers/quix.php';

        // Reference global application object
        $app = JFactory::getApplication();

        // check pro version + activation
        $free = QuixHelper::isFreeQuix();
        $pro  = QuixHelper::isProActivated();

        if ($free) {
            echo new JResponseJson('free');
        } elseif ($pro) {
            echo new JResponseJson('pro');
        } else {
            echo new JResponseJson('inactive');
        }

        $app->close();
    }

    /**
     * Image optimization
     *
     * @param [type] $src
     *
     * @return void
     * @throws \Exception
     * @since 3.0.0
     */
    public function imageOptimization(): void
    {
        $app = JFactory::getApplication();

        try {
            $imagePath  = $app->input->json->get('image', null, 'string');
            $reOptimize = (bool) $app->input->json->get('reOptimize', false);

            Optimizer::optimize($imagePath, $reOptimize);

            echo new JResponseJSON([], 'Image Optimization Done.');
        } catch (Exception $e) {
            echo new JResponseJSON($e);
        }

        $app->close();
    }

    protected function sort($images)
    {
        $i = [];
        foreach ($images as $image) {
            if (strpos($image, 'large_desktop')) {
                $i[4] = $image;
            } elseif (strpos($image, 'desktop')) {
                $i[3] = $image;
            } elseif (strpos($image, 'tablet')) {
                $i[2] = $image;
            } elseif (strpos($image, 'mobile')) {
                $i[1] = $image;
            } elseif (strpos($image, 'mini')) {
                $i[0] = $image;
            }
        }

        return $i;
    }

    /**
     * Determine current user's license type.
     *
     * @return boolean
     * @since 3.0.0
     */
    protected function isProUser()
    {
        require_once JPATH_COMPONENT . '/helpers/quix.php';

        $free = QuixHelper::isFreeQuix();
        $pro  = QuixHelper::isProActivated();

        return ($free or empty($pro) or $pro == null or ! $pro) ? false : true;
    }

    public function getElementPath()
    {
        jimport('quixnxt.app.bootstrap');

        $app = JFactory::getApplication();

        $input = $app->input;
        $slug  = $input->get('slug');

        $element = array_find_by(quix()->getElements(), 'slug', $slug);

        if (empty($element)) {
            echo new JResponseJSON("Element {$slug} doesn't exists");
            $app->close();

            return;
        }

        echo new JResponseJSON([
            'element_path' => $element['element_path'],
            'element_url'  => $element['url'],
        ]);

        $app->close();
    }

    public function getEditor()
    {
        require_once JPATH_SITE . '/components/com_quix/helpers/editor.php';

        $app        = JFactory::getApplication();
        $input      = $app->input;
        $context    = $input->get('source', '', 'string');
        $context_id = $input->get('sid', '', 'int');

        $getId = QuixFrontendHelperEditor::getId($context, $context_id);
        if ($getId) {
            $app->redirect('index.php?option=com_quix&task=collection.edit&id=' . $getId . '&quixlogin=true');
        } else {
            // first create then go to edit
            $title                = explode('.', $context);

            // Create and populate a data object.
            $data        = new stdClass();
            $data->title = ucfirst($title[1]) . ':' . $context_id;
            $data->uid   = md5(uniqid(rand(), true));;
            $data->catid           = 0;
            $data->type            = 'editor';
            $data->state           = 1;
            $data->builder         = 'frontend';
            $data->builder_version = QUIXNXT_VERSION;
            $data->data            = '[]';
            $data->metadata        = '';
            $data->language        = '*';
            $data->checked_out     = '';
            $data->params          = '';
            $data->hits            = 0;
            $data->xreference      = '';
            $data->ordering        = 0;
            $data->access          = 0;

            // Insert the object into the user profile table.
            try {
                $result = JFactory::getDbo()->insertObject('#__quix_collections', $data);

                if ($result) {
                    $getId = JFactory::getDbo()->insertid();

                    QuixFrontendHelperEditor::log($context, $context_id, $getId);

                    $app->redirect('index.php?option=com_quix&task=collection.edit&id=' . $getId . '&quixlogin=true');
                }
            } catch (Exception $e) {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }
    }

    public function captchePublicKey()
    {
        $app            = JFactory::getApplication();
        $joomla_captcha = JFactory::getConfig()->get('captcha');

        if ($joomla_captcha != '0') {
            $params = new JRegistry(JPluginHelper::getPlugin('captcha')[0]->params);
            echo new JResponseJSON($params->get('public_key'));

            $app->close();
            jexit();
        }

        echo new JResponseJSON([]);
        jexit();
    }

    /**
     * Get Elements List
     * @response [name, slug, icon]
     *
     * get Elements Schema
     * @params [slug,slug]
     *
     * @return void [schema]
     *
     * @since 3.0.0
     * @since 3.0.0
     */
    public function getElements(): void
    {
    }

    public function getDataFromAPI($url)
    {
        try {
            $http   = new JHttp();
            $result = $http->get($url);

            if ($result->code != 200 && $result->code != 310) {
                $exception = new Exception(JText::_('COM_QUIX_SERVER_RESPONSE_ERROR'));

                return new JResponseJSON($exception);
            }

            return $result->body;
        } catch (RuntimeException $e) {
            $exception = new Exception($e->getMessage());

            return new JResponseJSON($exception);
        }
    }

    /**
     * Remote template details API
     *
     * @throws \Exception
     * @since 2.0.0
     */
    public function getRemoteTemplate(): void
    {
        $app     = JFactory::getApplication();
        $input   = $app->input;
        $version = $input->get('v');
        $id      = $input->get('id');

        $url = "https://getquix.net/index.php?option=com_quixblocks&view=item&id={$id}&format=json&format=json&code=1&auth=1";
        $tpl = $this->getDataFromAPI($url);

        $app->setHeader('Content-Type', 'application/json');

        // if ($version && +$version[0] === 2) {
        $json = json_decode($tpl, true);
        if (!is_array($json)) {
            echo json_encode([]);
            $app->close();
        }

        if (array_key_exists('data', $json)) {
            $json = $json['data'];
        }

        if (Schema::_isAssoc($json)) {
            $json = [$json];
        }

        if ($version && +$version[0] === 2) {
            $adapter = Schema::getAdapter(Schema::QUIX_V2, Schema::QUIX_V3);
            $json    = $adapter->transform($json);
        }

        $cleaner = Schema::getCleaner();
        $json    = $cleaner->mergeRecursive($json);


        echo json_encode($json);
        // } else {
        //     echo $tpl;
        // }

        $app->close();
    }

    public function downloadMedia()
    {
        // Check if user token is valid.
        $app = JFactory::getApplication();

        if ( ! JSession::checkToken('get')) {
            $exception = new Exception(JText::_('JINVALID_TOKEN'));
            echo new JResponseJSON($exception);
            JFactory::getApplication()->close();
        }


        $method = $_SERVER['REQUEST_METHOD'];
        $result = false;

        if ($method == 'POST') {
            $data = (array) json_decode($app->input->get('data', '', 'raw'));

            $current_domain = \JURI::base();
            $source         = $data['source'] ?? '';
            $base_domain    = $data['base_domain'] ?? '';

            if ($source != "" && $base_domain !="") {
                // Final url for download the image.
                $final_path    = $base_domain.$source;
                $default_image = \JURI::base().'media/quixnxt/images/placeholder.png';

                if ($current_domain != $base_domain) {
                    if (stripos(@get_headers($final_path, 1)[0], "200 OK")) {
                        // If the file is exists, go for download.
                        $result = Download::copyFile($final_path, $base_domain);

                        // if you dont want to change image path, please comment this area
                        // use case : if image download is failed.
                        if ( ! $result) {
                            Download::copyFile($default_image, \JURI::base());
                        }
                    } else {
                        // If the file is not exists, pass placeholder image.
                        Download::copyFile($default_image, \JURI::base());
                    }
                }
            }
        }

        echo new JResponseJson($result);
        $app->close();
    }
}
