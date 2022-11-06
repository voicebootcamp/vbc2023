<?php
/**
 * @package    Quix
 * @author     ThemeXpert http://www.themexpert.com
 * @copyright  Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @since      1.0.0
 */

defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;
/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_quix
 * @since       3.4
 */
class pkg_QuixInstallerScript
{
    public $migration = false;

    /**
     * @param $type
     * @param $parent
     *
     * @return false
     * @throws \Exception
     * @since 3.0.0
     */
    public function preflight($type, $parent)
    {
        // if ($type === 'update') {
        //     // JError::raiseWarning(null, 'Beta Package can\'t be installed on existing site.');
        //     JFactory::getApplication()->enqueueMessage('Beta Package can\'t be installed on existing site.', 'warning');
        //
        //     return false;
        // }

        return true;
    }

    /**
     * method to rename old tables to new name
     *
     * @return bool
     * @throws \Exception
     * @since 1.0.0
     */
    public function renameDB(): bool
    {
        $app    = JFactory::getApplication();
        $prefix = $app->get('dbprefix');

        $db     = JFactory::getDbo();
        $tables = JFactory::getDbo()->getTableList();

        if (in_array($prefix.'quicx', $tables)) {
            $db->setQuery('RENAME TABLE #__quicx TO #__quix');
            $db->execute();
        }

        if (in_array($prefix.'quicx_collections', $tables)) {
            $db->setQuery('RENAME TABLE #__quicx_collections TO #__quix_collections');
            $db->execute();
        }

        if (in_array($prefix.'quicx_collection_map', $tables)) {
            $db->setQuery('RENAME TABLE #__quicx_collection_map TO #__quix_collection_map');
            $db->execute();
        }

        return true;
    }

    /*
     * get a variable from the manifest file (actually, from the manifest cache).
     */
    public function getParam($name, $options = 'com_quix')
    {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "'.$options.'"');
        $result = $db->loadResult();
        if (isset($result) && ! empty($result)) {
            $manifest = json_decode($result, true);

            return $manifest[$name];
        }

        return false;
    }

    /*
     * get a variable from the manifest file (actually, from the manifest cache).
     */
    public function uninstallOldExtensions()
    {
        JModelLegacy::addIncludePath(JPATH_SITE.'/adminstrator/components/com_installer/models', 'InstallerModel');
        $model = JModelLegacy::getInstance('Manage', 'InstallerModel');
        $db    = JFactory::getDbo();
        $db->setQuery("SELECT * FROM `#__extensions` WHERE `name` LIKE '%quicx%'");
        $results = $db->loadObjectList();
        if (isset($results) && ! empty($results)) {
            // print_r($results);die;
            $ids = [];
            foreach ($results as $key => $value) {
                $ids[] = $value->extension_id;
            }
            ArrayHelper::toInteger($ids, []);
            $model->remove($ids);
        }

        return true;
    }

    /**
     * update db structure
     *
     * @lang  mysqli
     * @since 3.0.0
     */
    public function updateDBfromOLD()
    {
        $app    = JFactory::getApplication();
        $prefix = $app->get('dbprefix');

        $db     = JFactory::getDbo();
        $tables = JFactory::getDbo()->getTableList();

        if ( ! in_array($prefix.'quix', $tables)) {
            return;
        }

        $query = "SHOW COLUMNS FROM `#__quix` LIKE 'catid'";
        $db->setQuery($query);
        $column = (object) $db->loadObject();
        if (empty($column) or empty($column->Field)) {
            $query = /** @lang text */
                "
				ALTER TABLE  `#__quix`
				ADD `catid` int(11) NOT NULL AFTER  `title`,
				ADD `version` int(10) unsigned NOT NULL DEFAULT '1' AFTER `params`,
				ADD `hits` int(11) NOT NULL AFTER `version`,
				ADD `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.' AFTER `hits`,
				ADD INDEX `idx_access` (`access`),
				ADD INDEX `idx_catid` (`catid`),
				ADD INDEX `idx_state` (`state`),
				ADD INDEX `idx_createdby` (`created_by`),
				ADD INDEX `idx_xreference` (`xreference`);
				";
            $db->setQuery($query);
            $db->execute();
        }
    }

    public function cleanQuixCache()
    {
        $session = JFactory::getSession();
        $session->set('quix_install_cleancache', 1);
    }

    /**
     * Function to perform changes during install
     *
     * @param  JInstallerAdapterComponent  $parent  The class calling this method
     *
     * @return  void
     *
     * @since   3.4
     */
    public function postflight($parent)
    {
        self::enablePlugins();
        self::insertMissingUcmRecords();

        // clean quix cache
        self::cleanQuixCache();

        if ($this->migration) {
            // now uninstall all the extensions
            $this->uninstallOldExtensions();
        }

        $this->updateDBfromOLD();

        ob_start(); ?>
      <div id="qx-welcome-v3-wrapper" style="position: relative;background: #fff;">
        <style>
            #qx-welcome-v3-wrapper button {
                position: absolute;
                right: 0px;
                top: 0px;
                background: #fff;
                border: none;
                padding: 5px 10px;
            }

            #qx-welcome-v3 {
                display: flex;
                justify-content: center;
                background: linear-gradient(45deg, rgba(98, 61, 218, 1) 0, rgba(2, 72, 140, 1) 100%);
                color: #fff;
                align-items: center;
                margin: 0px auto 30px;
            }

            #qx-welcome-v3 h3,
            #qx-welcome-v3 h4,
            #qx-welcome-v3 a {
                color: #fff
            }

            #qx-welcome-v3 h5 {
                font-size: 16px;
            }

            #qx-welcome-v3 h5 {
                color: yellow;
                padding: 25px 0 0;
            }

            #qx-welcome-v3 span {
                width: 50px;
                height: 50px;
                display: inline-block;
            }

            #qx-welcome-v3 img {
                border: 3px solid white;
            }

            #qx-welcome-v3 > div {
                padding: 30px 10px;
            }

            #qx-welcome-v3 span svg {
                fill: yellow;
            }
            #qx-welcome-v3 .bannerBox .content{
                padding: 0px;
            }
            #qx-welcome-v3 iframe, #qx-welcome-v3 svg {
                max-width: 100%;
            }
        </style>
        <div id="qx-welcome-v3">
          <div class="bannerBox" style="max-width: 25%;flex: 25%;padding: 10px 10px 10px 20px;">
            <span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50px"
                       height="50px">
                    <image x="0px" y="0px" width="50px" height="50px"
                           xlink:href="data:img/png;base64,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" />
                </svg></span>
            <h3>Welcome to Quix 4</h3>
            <h4>Experience the powerful Joomla page builder</h4>
            <a class="btn btn-large" href="index.php?option=com_quix&view=pages"
               style="background-color: green;margin-top: 15px;border: 1px solid #1e500b;border-radius: 4px;">Get Started</a>
          </div>
          <div class="bannerBox" style="max-width: 25%;flex: 30%;text-align: center;padding-right: 40px;">
            <img title="welcome video" alt="welcome video" width="220" height="151"
                 src="<?php echo Juri::root(); ?>/media/quixnxt/images/banners/thumb_video.jpg">
          </div>
          <div class="bannerBox" style="max-width: 15%;flex: 15%;">
            <span style="width:40px;height:40px;"><svg id="Capa_1" enable-background="new 0 0 511.997 511.997" width="50px"
                                                       height="50px"
                                                       viewBox="0 0 511.997 511.997" xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <path
                                d="m226.554 166.843v48.838c0 11.685 12.791 18.746 22.659 12.804l40.542-24.42c9.657-5.819 9.634-19.802-.001-25.606l-40.541-24.419c-9.937-5.986-22.659 1.188-22.659 12.803zm55.384 24.419-40.385 24.323.002-48.647z" />
                        <path
                                d="m275.351 114.867c-4.017-1.022-8.097 1.401-9.119 5.416s1.402 8.097 5.416 9.119c28.363 7.225 48.172 32.662 48.172 61.86 0 35.19-28.63 63.82-63.82 63.82s-63.82-28.63-63.82-63.82c0-29.198 19.809-54.636 48.172-61.86 4.014-1.022 6.438-5.104 5.416-9.119s-5.11-6.438-9.119-5.416c-35.015 8.918-59.469 40.333-59.469 76.396 0 43.462 35.358 78.82 78.82 78.82s78.82-35.358 78.82-78.82c-.001-36.063-24.455-67.478-59.469-76.396z" />
                        <path
                                d="m477.692 28.32h-443.387c-18.916 0-34.305 15.388-34.305 34.305v257.275c0 18.916 15.389 34.305 34.305 34.305h32.868c-.807 13.721 4.223 26.389 13.008 35.678h-.499c-23.601 0-42.801 19.2-42.801 42.801v34.138c0 9.295 7.562 16.856 16.856 16.856h122.366c3.459 0 6.677-1.05 9.355-2.845 2.679 1.795 5.897 2.845 9.356 2.845h122.366c3.46 0 6.677-1.05 9.356-2.845 2.679 1.795 5.897 2.845 9.356 2.845h122.366c9.295 0 16.856-7.562 16.856-16.856v-34.138c0-23.601-19.2-42.801-42.801-42.801h-.5c8.764-9.266 13.816-21.923 13.009-35.678h32.867c18.916 0 34.305-15.389 34.305-34.305v-111.138c0-4.143-3.357-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v111.138c0 10.645-8.66 19.305-19.305 19.305h-36.218c-2.193-5.468-5.468-10.618-9.612-15h42.635c4.143 0 7.5-3.357 7.5-7.5v-250.885c0-4.143-3.357-7.5-7.5-7.5h-436.994c-4.143 0-7.5 3.357-7.5 7.5v250.885c0 4.143 3.357 7.5 7.5 7.5h42.635c-4.058 4.291-7.347 9.355-9.612 15h-36.218c-10.645 0-19.305-8.661-19.305-19.305v-257.275c0-10.645 8.66-19.305 19.305-19.305h443.388c10.645 0 19.305 8.66 19.305 19.305v111.138c0 4.143 3.357 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-111.138c-.001-18.917-15.39-34.305-34.306-34.305zm-299.733 438.501c0 1.023-.832 1.856-1.855 1.856h-122.367c-1.023 0-1.856-.833-1.856-1.856v-34.138c0-15.329 12.472-27.801 27.801-27.801h70.478c15.329 0 27.8 12.472 27.8 27.801v34.138zm-95.878-109.778c0-18.032 14.645-32.839 32.839-32.839 18.138 0 32.839 14.76 32.839 32.839 0 18.107-14.731 32.839-32.839 32.839s-32.839-14.731-32.839-32.839zm103.378 51.473c-7.721-11.242-20.661-18.634-35.3-18.634h-.5c8.764-9.266 13.816-21.923 13.009-35.678h45.583c-.807 13.746 4.239 26.405 13.009 35.678h-.5c-14.639 0-27.58 7.391-35.301 18.634zm-35.754-84.311h71.509c-4.136 4.374-7.415 9.522-9.612 15h-52.284c-2.266-5.646-5.555-10.71-9.613-15zm169.333 142.616c0 1.023-.833 1.856-1.856 1.856h-122.367c-1.023 0-1.856-.833-1.856-1.856v-34.138c0-15.329 12.472-27.801 27.801-27.801h70.478c15.329 0 27.801 12.472 27.801 27.801v34.138zm-95.879-109.778c0-18.145 14.77-32.839 32.84-32.839 18.075 0 32.839 14.697 32.839 32.839 0 18.107-14.731 32.839-32.839 32.839s-32.84-14.731-32.84-32.839zm103.379 51.473c-7.721-11.242-20.662-18.634-35.301-18.634h-.499c8.764-9.266 13.816-21.923 13.009-35.678h45.583c-.807 13.747 4.239 26.406 13.009 35.678h-.499c-14.64 0-27.581 7.391-35.302 18.634zm-35.754-84.311h71.509c-4.198 4.44-7.447 9.603-9.613 15h-52.284c-2.264-5.646-5.554-10.71-9.612-15zm141.532 80.677c15.329 0 27.801 12.472 27.801 27.801v34.138c0 1.023-.833 1.856-1.856 1.856h-122.366c-1.023 0-1.856-.833-1.856-1.856v-34.138c0-15.329 12.472-27.801 27.801-27.801zm-68.078-47.839c0-18.098 14.72-32.839 32.839-32.839 18.11 0 32.84 14.733 32.84 32.839 0 18.107-14.731 32.839-32.84 32.839-18.107 0-32.839-14.731-32.839-32.839zm-319.238-47.838v-235.885h421.997v235.885z" />
                    </g>
                </svg></span>
            <div class="content text-white-50">
              <h5>Tutorials</h5>
              <p><a href="https://www.themexpert.com/video-tutorials" target="_blank">Quix Videos</a></p>
              <p><a href="https://www.udemy.com/course/quix-joomla-page-builder-create-pixel-perfect-websites-p2/" target="_blank">Udemy Courses</a></p>
              <p><a href="https://www.themexpert.com/docs" target="_blank">Documentation</a></p>
            </div>
          </div>
          <div class="bannerBox" style="max-width: 15%;flex: 15%;">
            <span style="width:40px;height:40px;"><svg version="1.1" id="Capa_2" xmlns="http://www.w3.org/2000/svg" width="50px"
                                                       height="50px"
                                                       xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 480.004 480.004"
                                                       style="enable-background:new 0 0 480.004 480.004;" xml:space="preserve">
                    <g>
                        <g>
                            <path
                                    d="M423.618,85.472l-16-48c-1.386-4.195-5.911-6.472-10.106-5.086c-0.375,0.124-0.741,0.276-1.094,0.454l-48,24           c-3.954,1.975-5.559,6.782-3.584,10.736s6.782,5.559,10.736,3.584l26.144-13.072C312.914,183.912,187.442,264,56.002,264v16         c139.024,0,271.504-85.472,342.472-219.28l9.944,29.808c1.396,4.197,5.931,6.468,10.128,5.072S425.014,89.669,423.618,85.472z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path
                                    d="M472.002,0h-464c-4.418,0-8,3.582-8,8v360c0,4.418,3.582,8,8,8H149.17l-28.8,93.648c-1.299,4.224,1.072,8.701,5.296,10         c4.224,1.299,8.701-1.072,10-5.296L151.146,424h177.712l15.496,50.352c1.299,4.224,5.776,6.595,10,5.296            c4.224-1.299,6.595-5.776,5.296-10L330.834,376h141.168c4.418,0,8-3.582,8-8V8C480.002,3.582,476.42,0,472.002,0z M156.066,408          l9.848-32H314.09l9.848,32H156.066z M464.002,360h-448V16h448V360z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path
                                    d="M96.002,296h-32c-4.418,0-8,3.582-8,8v32c0,4.418,3.582,8,8,8h32c4.418,0,8-3.582,8-8v-32         C104.002,299.582,100.42,296,96.002,296z M88.002,328h-16v-16h16V328z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path
                                    d="M176.002,280h-32c-4.418,0-8,3.582-8,8v48c0,4.418,3.582,8,8,8h32c4.418,0,8-3.582,8-8v-48            C184.002,283.582,180.42,280,176.002,280z M168.002,328h-16v-32h16V328z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path
                                    d="M256.002,248h-32c-4.418,0-8,3.582-8,8v80c0,4.418,3.582,8,8,8h32c4.418,0,8-3.582,8-8v-80            C264.002,251.582,260.42,248,256.002,248z M248.002,328h-16v-64h16V328z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path
                                    d="M336.002,200h-32c-4.418,0-8,3.582-8,8v128c0,4.418,3.582,8,8,8h32c4.418,0,8-3.582,8-8V208           C344.002,203.582,340.42,200,336.002,200z M328.002,328h-16V216h16V328z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path
                                    d="M416.002,112h-32c-4.418,0-8,3.582-8,8v216c0,4.418,3.582,8,8,8h32c4.418,0,8-3.582,8-8V120           C424.002,115.582,420.42,112,416.002,112z M408.002,328h-16V128h16V328z" />
                        </g>
                    </g>
                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></span>
            <div class="content text-white-50">
              <h5>Tips & Tricks</h5>
              <p><a href="#">SEO Settings</a></p>
              <p><a href="#">QuixRank Guide</a></p>
              <p><a href="#">Speed up website</a></p>
            </div>
          </div>
          <div class="bannerBox" style="max-width:15%;flex:15%;">
            <span style="width:40px;height:40px;"><svg id="Capa_3" enable-background="new 0 0 512.099 512.099"
                                                       viewBox="0 0 512.099 512.099" width="50px"
                                                       height="50px" xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <path
                                d="m393.84 300.609c-2.214-3.501-6.849-4.543-10.348-2.326-3.5 2.215-4.542 6.848-2.326 10.348 9.065 14.324 13.857 30.896 13.857 47.922v140.447h-26.938l16.58-88.947c1.255-6.736-.536-13.622-4.915-18.893-4.379-5.27-10.819-8.292-17.671-8.292h-.856v-8.795c0-8.972-7.3-16.271-16.271-16.271h-33.708c-8.973 0-16.272 7.3-16.272 16.271v8.795h-144.953c-6.852 0-13.293 3.022-17.672 8.293s-6.17 12.156-4.914 18.892l7.191 38.576c.76 4.073 4.684 6.763 8.747 5.999 4.072-.759 6.758-4.676 5.999-8.747l-7.191-38.576c-.436-2.338.186-4.729 1.706-6.558 1.52-1.83 3.756-2.879 6.134-2.879h212.061c2.378 0 4.613 1.049 6.133 2.878 1.521 1.83 2.143 4.221 1.707 6.559l-17.093 91.695h-193.555l-4.313-23.132c-.76-4.072-4.684-6.757-8.747-5.999-4.072.759-6.758 4.676-5.999 8.747l3.8 20.384h-26.938v-140.447c0-20.544 7.119-40.621 20.045-56.532 11.87-14.612 28.143-25.18 46.163-30.076.917.931 1.851 1.845 2.803 2.741l28.645 81.75c2.691 7.686 12.08 10.603 18.656 5.806l22.662-16.539 22.662 16.538c6.557 4.787 15.952 1.919 18.657-5.804l28.643-81.75c.95-.894 1.883-1.807 2.799-2.736 12.446 3.382 24.159 9.482 34.083 17.801 1.405 1.178 3.114 1.752 4.814 1.752 2.142 0 4.269-.912 5.752-2.682 2.66-3.175 2.244-7.905-.93-10.566-9.827-8.236-21.158-14.609-33.242-18.768 3.107-4.357 5.875-8.969 8.279-13.795h8.156c11.25 0 20.403-9.152 20.403-20.402v-19.295c14.616-3.407 25.541-16.533 25.541-32.173v-2.859c0-13.021-7.572-24.302-18.542-29.683v-62.063c0-42.578-34.64-77.218-77.218-77.218h-91.818c-4.143 0-7.5 3.357-7.5 7.5s3.357 7.5 7.5 7.5h91.818c34.307 0 62.218 27.911 62.218 62.218v58.703h-9.919c-.534-27.439-27.896-45.073-53.2-36.616-30.628 10.246-58.865 5.012-86.324-15.988-26.176-20.022-64.806-2.539-64.806 32.999v27.509h-7.681c-.091 0-.181.006-.271.007-1.187-43.056 3.119-54.194-5.091-70.393l-9.002-17.757c-.422-.835.154-1.805 1.089-1.835l22.699-.692c5.198-.158 9.714-3.415 11.504-8.299 1.79-4.883.45-10.288-3.416-13.771l-14.456-13.014c-1.216-1.094-.456-3.071 1.18-3.071h33.671c4.143 0 7.5-3.357 7.5-7.5s-3.357-7.5-7.5-7.5h-33.671c-6.999 0-13.142 4.222-15.649 10.757-2.508 6.533-.768 13.78 4.434 18.463l10.13 9.119-16.882.516c-5.593.17-10.636 3.145-13.491 7.956s-3.05 10.663-.52 15.654l9.001 17.756c2.271 4.479 3.471 9.501 3.471 14.521v53.93c-6.826 4.813-11.297 12.752-11.297 21.72 0 14.649 11.919 26.568 26.568 26.568h7.681c.176 1.097-2.25 30.824 18.942 60.533-18.419 6.342-34.943 17.809-47.338 33.067-15.092 18.577-23.403 42.014-23.403 65.991v140.609c0 8.182 6.656 14.838 14.838 14.838 15.168.008 249.939.218 278.272 0 8.182 0 14.838-6.656 14.838-14.838v-140.607c-.001-19.871-5.597-39.216-16.185-55.944zm-83.868 80.259v-8.795c0-.701.571-1.271 1.272-1.271h33.708c.701 0 1.271.57 1.271 1.271v8.795zm-82.347-34.992-20.236-57.755c5.578 3.034 11.472 5.558 17.613 7.521l21.183 36.69zm28.424-26.458-11.302-19.577c3.713.411 7.482.628 11.302.628s7.59-.218 11.302-.628zm28.425 26.458-18.561-13.544 21.183-36.69c6.141-1.963 12.035-4.487 17.613-7.521zm71.248-117.184h-2.115c2.374-7.602 3.874-15.585 4.399-23.828h3.118v18.426c.001 2.979-2.423 5.402-5.402 5.402zm30.944-56.87c0 9.948-8.093 18.042-18.041 18.042h-10.401v-38.943h10.401c9.948 0 18.041 8.094 18.041 18.042zm-240.472 10.139c-6.379 0-11.568-5.189-11.568-11.568s5.189-11.568 11.568-11.568h7.681v23.137h-7.681zm22.681-65.646c0-22.772 24.102-33.775 40.692-21.085 31.274 23.919 64.981 30.075 100.195 18.3 16.119-5.386 33.461 5.822 33.461 23.222v61.543c0 10.687-1.937 20.928-5.472 30.397h-20.231c-2.318-6.432-8.465-11.052-15.686-11.052h-19.634c-8.227 0-15.066 5.992-16.424 13.837-7.52 3.667-14.68 3.151-22.304-1.569-3.521-2.181-8.145-1.093-10.325 2.429-2.181 3.522-1.093 8.145 2.429 10.325 6.697 4.146 13.585 6.219 20.473 6.219 3.918 0 7.835-.695 11.718-2.036 2.889 4.984 8.27 8.352 14.433 8.352h19.634c7.391 0 13.666-4.835 15.852-11.505h12.757c-15.335 25.037-42.945 41.777-74.394 41.777-48.067 0-87.174-39.106-87.174-87.175zm134.645 122.196c0 .93-.756 1.687-1.686 1.687h-19.634c-.93 0-1.686-.757-1.686-1.687v-4.185c0-.93.756-1.686 1.686-1.686h19.634c.93 0 1.686.756 1.686 1.686z" />
                        <path
                                d="m212.626 175.954c4.143 0 7.5-3.357 7.5-7.5v-10.443c0-4.143-3.357-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v10.443c0 4.143 3.357 7.5 7.5 7.5z" />
                        <path
                                d="m299.472 175.954c4.143 0 7.5-3.357 7.5-7.5v-10.443c0-4.143-3.357-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v10.443c0 4.143 3.357 7.5 7.5 7.5z" />
                        <path
                                d="m284.858 465.49c0-15.886-12.924-28.81-28.81-28.81s-28.81 12.924-28.81 28.81 12.924 28.81 28.81 28.81 28.81-12.924 28.81-28.81zm-42.619 0c0-7.614 6.195-13.81 13.81-13.81 7.614 0 13.81 6.195 13.81 13.81s-6.195 13.81-13.81 13.81c-7.614 0-13.81-6.196-13.81-13.81z" />
                    </g>
                </svg>
            </span>
            <div class="content text-white-50">
              <h5>Support</h5>
              <p><a target="_blank" href="https://www.themexpert.com/blog/categories/quix-resources">Resource</a></p>
              <p><a target="_blank" href="https://www.facebook.com/groups/QuixUserGroup">FB Channels</a></p>
              <p><a target="_blank" href="https://www.themexpert.com/support">Ask a question</a></p>
            </div>
          </div>
        </div>
      </div>
        <?php
    }

    /**
     * enable necessary plugins to avoid bad experience
     *
     * @since 3.0.0
     */
    public function enablePlugins()
    {
        $db  = JFactory::getDBO();
        $sql = /** @lang text */
            "SELECT `element`,`folder` from `#__extensions` WHERE `type` = 'plugin' AND `folder` in ('quix', 'finder', 'system', 'content', 'editors-xtd', 'quickicon') AND `name` like '%quix%' AND `enabled` = '0'";
        $db->setQuery($sql);
        $plugins = $db->loadObjectList();
        if (count($plugins)) {
            foreach ($plugins as $key => $value) {
                if ($value->folder == 'finder' or $value->folder == 'system' or $value->folder == 'editors-xtd') {
                    $query = $db->getQuery(true);
                    $query->update($db->quoteName('#__extensions'));
                    $query->set($db->quoteName('enabled').' = '.$db->quote('1'));
                    $query->where($db->quoteName('type').' = '.$db->quote('plugin'));
                    $query->where($db->quoteName('element').' = '.$db->quote($value->element));
                    $query->where($db->quoteName('folder').' = '.$db->quote($value->folder));
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }

        $sql = /** @lang text */
            "SELECT `element`,`folder`, `enabled` from `#__extensions` WHERE `type` = 'plugin' AND `folder` ='system' AND `element` = 'seositeattributes' AND `enabled` = '0'";
        $db->setQuery($sql);
        $plugins = $db->loadObjectList();
        if ( ! count($plugins)) {
            return false;
        }
        foreach ($plugins as $key => $value) {
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__extensions'));
            $query->set($db->quoteName('enabled').' = '.$db->quote('1'));
            $query->where($db->quoteName('type').' = '.$db->quote('plugin'));
            $query->where($db->quoteName('element').' = '.$db->quote($value->element));
            $query->where($db->quoteName('folder').' = '.$db->quote($value->folder));
            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }

    /**
     * Method to insert missing records for the UCM tables
     *
     * @return bool
     *
     * @since   3.4.1
     */
    public function insertMissingUcmRecords()
    {
        // Insert the rows in the #__content_types table if they don't exist already
        $db = JFactory::getDbo();

        // Get the type ID for a xDoc
        $query = $db->getQuery(true);
        $query->select($db->quoteName('type_id'))
              ->from($db->quoteName('#__content_types'))
              ->where($db->quoteName('type_alias').' = '.$db->quote('com_quix.page'));
        $db->setQuery($query);

        $docTypeId = $db->loadResult();

        // Set the table columns to insert table to
        $columnsArray = [
            $db->quoteName('type_title'),
            $db->quoteName('type_alias'),
            $db->quoteName('table'),
            $db->quoteName('rules'),
            $db->quoteName('field_mappings'),
            $db->quoteName('router'),
            $db->quoteName('content_history_options'),
        ];

        // If we have no type id for com_xdocs.doc insert it
        if ( ! $docTypeId) {
            // Insert the data.
            $query->clear();
            $query->insert($db->quoteName('#__content_types'));
            $query->columns($columnsArray);
            $query->values(
                $db->quote('Quix Page').', '
                .$db->quote('com_quix.page').', '
                .$db->quote('{"special":{"dbtable":"#__quix","key":"id","type":"Page","prefix":"QuixTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}').', '
                .$db->quote('').', '
                .$db->quote('{"common":{"core_content_item_id":"id","core_title":"title","core_state":"state","core_body":"description", "core_hits":"hits","core_access":"access", "core_params":"params", "core_metadata":"metadata", "core_language":"language", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_xreference":"xreference", "asset_id":"null"}, "special":{}}').', '
                .$db->quote('QuixFrontendHelperRoute::getPageRoute').', '
                .$db->quote('{"formFile":"administrator\\/components\\/com_quix\\/models\\/forms\\/page.xml", "hideFields":["asset_id","checked_out","checked_out_time"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time", "version", "hits"], "convertToInt":["publish_up", "publish_down", "featured", "ordering"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ]}')
            );

            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }
}
