<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    4.1.0
 */

// No direct access
use QuixNxt\Elements\ElementBag;

defined('_JEXEC') or die;

class QuixFrontendHelperAjax
{
    /**
     * Method to perform ajax call
     *
     * @return void output, like: json or other format
     * url example: index.php?option=com_quix&task=ajax&element=simple-contact&format=json&method=get
     * @throws \Exception
     * @since    4.1.1
     */
    public static function doAjax()
    {
        // Reference global application object
        $app = JFactory::getApplication();

        // JInput object
        $input = $app->input;

        // Requested format passed via URL
        $format = strtolower($input->getWord('format', 'json'));

        // request builder version
        $builder = strtolower($input->getWord('builder', 'classic'));

        // Requested element name
        $element = strtolower($input->get('element', '', 'string'));
        $element_path = JPATH_ROOT . '/libraries/quix';

        // Check for valid element
        if ( ! $element) {
            $results = new InvalidArgumentException(JText::_('COM_QUIX_SPECIFY_ELEMENT'), 404);
        } // Now we have element name, load the class
        else {
            // check if element found
            $elementFound = false;
            if ($builder === 'classic') {
                jimport('quix.app.bootstrap');

                // load element
                $default_template = quix_default_template();
                // first check if its from default template
                $QUIXNXT_TEMPLATE_PATH = JPATH_ROOT.'/templates/'.$default_template.'/quix';
                $QUIXNXT_PATH = JPATH_ROOT . '/libraries/quix';
                if (is_file($QUIXNXT_TEMPLATE_PATH."/elements/$element/helper.php")) {
                    $elementFound = true;
                    $element_path = $QUIXNXT_TEMPLATE_PATH.'/elements';
                    require_once $QUIXNXT_TEMPLATE_PATH."/elements/$element/helper.php";
                } // check  if its from core then
                elseif (is_file($QUIXNXT_PATH."/app/elements/$element/helper.php")) {
                    $elementFound = true;
                    $element_path = $QUIXNXT_PATH.'/app/elements';
                    require_once $QUIXNXT_PATH."/app/elements/$element/helper.php";
                }
            } else {
                //$elementInfo = array_find_by(quix()->getElements(), 'slug', $element);
                $elementInfo = ElementBag::getInstance()->get($element);
                if ( ! empty($elementInfo) && $elementInfo->getElementHelper()) {
                    $elementFound = true;
                    $element_path = $elementInfo->getElementHelper();

                    require_once $element_path;
                }
            }

            if ( ! $elementFound) {
                $results = new InvalidArgumentException(JText::sprintf('COM_QUIX_ELEMENT_NOT_FOUND', $element), 404);
            } else {
                ///////////////////////////////////////////////////
                //
                // Generate class name from element name
                //
                //////////////////////////////////////////////////
                $className = str_replace('-', ' ', $element);

                $className = ucwords($className);

                $className = str_replace(' ', '', $className);

                $elementClassName = "Quix{$className}Element";

                // Get the method name
                $method = $input->get('method') ?: 'get';

                ///////////////////////////////////////////////////
                //
                // Dynamically calling user required method of
                // the element for ajax.
                //
                //////////////////////////////////////////////////
                // first check method exist
                if (method_exists($elementClassName, $method.'Ajax')) {
                    // Load language file for module
                    $elementLang = str_replace('-', '_', $element);
                    $lang        = $app->getLanguage();
                    $lang->load('quix_'.$elementLang, $element_path."/$element", null, false, true);

                    try {
                        $results = call_user_func($elementClassName.'::'.$method.'Ajax');
                    } catch (Exception $e) {
                        $results = $e;
                    }
                } // Method does not exist
                else {
                    $results = new LogicException(JText::sprintf('COM_QUIX_METHOD_NOT_EXISTS', $method.'Ajax'), 404);
                }
            }
        }

        // Return the results in the desired format
        switch ($format) {
            // JSON format response
            case 'json':
                if ($results instanceof Exception) {
                    echo new JResponseJson($results, null, true, $input->get('ignoreMessages', true, 'bool'));
                }else{
                    echo new JResponseJson($results, null, false, $input->get('ignoreMessages', true, 'bool'));
                }

                break;

            // Handle as raw format
            default:
                // Output exception
                if ($results instanceof Exception) {
                    // Log an error
                    JLog::add($results->getMessage(), JLog::ERROR);

                    // Set status header code
                    $app->setHeader('status', $results->getCode(), true);

                    // Echo exception type and message
                    $out = get_class($results).': '.$results->getMessage();
                } // Output string/ null
                elseif (is_scalar($results)) {
                    $out = (string) $results;
                } // Output array/ object
                else {
                    $out = implode((array) $results);
                }

                echo $out;

                break;
        }

        $app->close();
    }


    /**
     * @throws \Exception
     * @since 2.0.0
     */
    public static function updateAjax(): void
    {
        $cache   = new JCache(['default' => 'lib_quix', 'cachebase' => JPATH_SITE.DIRECTORY_SEPARATOR.'cache']);
        $cacheID = 'quix.updateAjax';
        $cache->setCaching(true);
        $cache->setLifeTime(86400);  //24 hours 86400// 30days 2592000

        // return from cache
        $latest = $cache->get($cacheID);

        if ( ! $latest) {
            require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/quix.php';

            // update fonts google
            QuixHelperIcon::getUpdateGoogleFontsList();

            $cache->store(true, $cacheID);
        }

        $cache->setCaching(\JFactory::getApplication()->get('caching'));

        echo new JResponseJson('Quix');
        jexit();
    }
}
