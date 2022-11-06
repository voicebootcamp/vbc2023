<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Class QuixFrontendHelper
 *
 * @since  1.6
 */
class QuixFrontendHelper
{
    /**
     * Get group name using group ID
     *
     * @param  integer  $group_id  Usergroup ID
     *
     * @return mixed group name if the group was found, null otherwise
     */
    public static function getGroupNameByGroupId($group_id)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('title')
            ->from('#__usergroups')
            ->where('id = '.intval($group_id));

        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     * Get an instance of the named model
     *
     * @param  string  $name  Model name
     *
     * @return null|object
     */
    public static function getModel($name)
    {
        $model = null;

        // If the file exists, let's
        if (file_exists(JPATH_SITE.'/components/com_quix/models/'.strtolower($name).'.php')) {
            require_once JPATH_SITE.'/components/com_quix/models/'.strtolower($name).'.php';
            $model = JModelLegacy::getInstance($name, 'QuixModel');
        }

        return $model;
    }

    /**
     * Get flat icons list from server
     *
     * @param  string  $name  Model name
     *
     * @return null|object
     */
    public static function getFlatIconsJSONfromServer()
    {
        $config    = JComponentHelper::getParams('com_quix');
        $api_https = $config->get('api_https', 1);

        // absolute url of list json
        $url = ($api_https ? 'https' : 'http').'://getquix.net/index.php?option=com_quixblocks&view=flaticons&format=json';

        $process = true;
        // Get the handler to download the blocks
        try {
            $http   = new JHttp();
            $result = $http->get($url);

            if ($result->code != 200 && $result->code != 310) {
                $exception = new Exception(JText::_('COM_QUIX_SERVER_RESPONSE_ERROR'));

                return new JResponseJSON($exception);
            }

            self::saveOutputIconsJSON($result->body, $localHash = '', $storeHash = true);

            return $result->body;
        } catch (RuntimeException $e) {
            $exception = new Exception($e->getMessage());

            return new JResponseJSON($exception);
        }
    }

    /**
     * Method getFlatIconsfromLocal
     *
     * @param  none
     *
     * @return json
     */
    public static function getFlatIconsfromLocal()
    {
        if (file_exists(JPATH_SITE.'/media/quix/json/icons.json')) {
            $json = file_get_contents(JPATH_SITE.'/media/quix/json/icons.json');

            return $json;
        } else {
            return json_encode(['success' => false]);
        }
    }

    public static function saveOutputIconsJSON($data, $localHash = '', $storeHash = false)
    {
        $path = JPATH_SITE.'/media/quix/flaticons';

        if ( ! JFolder::exists($path)) {
            JFolder::create($path, 0755);
        }

        // step 1, save icons json
        try {
            JFile::write($path.'/flaticons.json', $data);
        } catch (\JCacheException $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
        }

        // step 2, get and save hash
        // for now, dont store hash, can take longer time for site.
        // do it only on admin site
        if ($storeHash = false) {
            $serverHash = self::getServerHashForIcon();
            // print_r($serverHash);die;
            $cache   = new JCache(['defaultgroup' => 'lib_quix', 'cachebase' => JPATH_SITE.DIRECTORY_SEPARATOR.'cache']);
            $cacheid = 'quix_flaticons_hash';
            $cache->setCaching(true);
            $cache->setLifeTime(2592000);  //24 hours 86400// 30days 2592000//

            // save hash
            $cache->set($cacheid, $serverHash);
            $cache->setCaching(\JFactory::getApplication()->get('caching'));
        }

        return true;
    }

    public static function getServerHashForIcon()
    {
        $config    = JComponentHelper::getParams('com_quix');
        $api_https = $config->get('api_https', 1);

        // absolute url of list json
        $url = ($api_https ? 'https' : 'http').'://getquix.net/index.php?option=com_quixblocks&view=flaticons&format=json&hash=true';

        // Get the handler to download the blocks
        try {
            $http   = new JHttp();
            $result = $http->get($url);

            if ($result->code != 200 && $result->code != 310) {
                // $exception = new Exception(JText::_('COM_QUIX_SERVER_RESPONSE_ERROR'));
                // echo new JResponseJSON($exception);

                return false;
            }

            $json = json_decode($result->body);

            return $json->data;
        } catch (RuntimeException $e) {
            // $exception = new Exception($e->getMessage());
            // echo new JResponseJSON($exception);
            return false;
        }
    }

    /**
     * Get google fonts list
     *
     * @param  string  $name  Model name
     *
     * @return null|object
     */
    public static function getGoogleFontsJSONfromServer()
    {
        /**
         * new api key: AIzaSyBx4wIzrdYz0fKQzlhlsygDBFXJ8_b6Gzc
         *
         * @generated by ahba on 5th march 2021
         * @under     emial: ahba@themexpert.com under getQuix project
         *
         * previous api key: AIzaSyBme3ryhPMclA04TFNDv1jwbwe0VJYyKnc
         * replaced by ahba
         * @reason    : Someone removed the app and key went invalid
         */
        $url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBx4wIzrdYz0fKQzlhlsygDBFXJ8_b6Gzc';
        try {
            $http = new JHttp();
            $str  = $http->get($url);
            if ($str->code != 200 && $str->code != 310) {
                return false;
            }

            $path = JPATH_SITE.'/media/quix/json';
            if (JFile::write($path.'/webfonts.json', $str->body)) {
                return true;
            } else {
                return false;
            }
        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Get google fonts list
     *
     * @param  string  $name  Model name
     *
     * @return null|object
     */
    public static function getGoogleFontsJSONfromLocal()
    {
        if (file_exists(JPATH_SITE.'/media/quix/json/webfonts.json')) {
            $json = file_get_contents(JPATH_SITE.'/media/quix/json/webfonts.json');

            return $json;
        } else {
            // get from server
            $result = QuixFrontendHelper::getGoogleFontsJSONfromServer();
            if ($result) {
                $json = file_get_contents(JPATH_SITE.'/media/quix/json/webfonts.json');

                return $json;
            }

            return '{}';
        }
    }

    public static function getBuilderTemplates()
    {
        echo "QuixFrontendHelper::getBuilderTemplates()";
        die;
        // $elements = quix()->getElements();
        // $nodes = quix()->getNodes();

        // foreach ($elements as $key => $element) {
        //     echo Quix()->getTemplateRenderer()->renderElementNode($element);
        // }
        // foreach ($nodes as $key => $node) {
        //     echo Quix()->getTemplateRenderer()->renderNodeNode($node);
        // }
    }

    public static function getSharedTemplates()
    {
        $path = JPATH_LIBRARIES.'/quixnxt/visual-builder/shared/animation.twig';

        $animation = file_get_contents($path);

        $path   = JPATH_LIBRARIES.'/quixnxt/visual-builder/shared/global.twig';
        $global = file_get_contents($path);

        $output = <<<WRAP
<QuixTemplate id="common-animation-template">
$animation
</QuixTemplate>
<QuixTemplate id="common-global-template">
$global
</QuixTemplate>
WRAP;
        echo $output;
    }

    public static function getHints()
    {
        $array = [
            JText::_('COM_QUIX_HINTS_TYPO'),
            JText::_('COM_QUIX_HINTS_MY_ELEMENTS')
        ];
        $k     = array_rand($array);

        return $array[$k];
    }

    public static function getJoomlaModules()
    {
        JModelLegacy::addIncludePath(JPATH_SITE.'/administrator/components/com_modules/models', 'ModulesModel');

        // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Modules', 'ModulesModel', ['ignore_request' => true]);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 9999);

        // Access filter
        // $access = ! JComponentHelper::getParams( 'com_modules' )->get( 'show_noauth' );
        // $model->setState( 'filter.access', $access );
        $model->setState('filter.state', 1);

        // Set ordering
        $model->setState('list.ordering', 'a.ordering');

        $model->setState('list.direction', 'ASC');

        // Retrieve Content
        return $model->getItems();
    }

    /*
    * to get update info
    * use layout to get alert structure
    */
    public static function fixJCH()
    {
        $plugin = JPluginHelper::getPlugin('system', 'jch_optimize');

        if (isset($plugin->id) && $plugin->id) {
            $params          = new JRegistry($plugin->params);
            $menuexcludedurl = $params->get('menuexcludedurl', []);
            $continue        = false;
            // exclude all assets from this url
            if ( ! in_array('format=amp', $menuexcludedurl)) {
                $menuexcludedurl[] = 'format=amp';
                $continue          = true;
            }
            if ( ! in_array('preview=true', $menuexcludedurl)) {
                $menuexcludedurl[] = 'preview=true';
                $continue          = true;
            }

            if ($continue) {
                $params->set('menuexcludedurl', $menuexcludedurl);

                $object               = new stdClass();
                $object->extension_id = $plugin->id;
                $object->params       = $params->toString();

                JFactory::getDbo()->updateObject('#__extensions', $object, 'extension_id');
            }
        }

        return true;
    }

}
