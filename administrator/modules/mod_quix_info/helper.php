<?php
/**
 * @package     Quix
 * @subpackage  Quix Info module
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_sampledata
 *
 * @since  3.8.0
 */
abstract class ModQuixInfoHelper
{
    public static function isPro()
    {
        jimport('joomla.form.form');

        $form = simplexml_load_string(file_get_contents(JPATH_ADMINISTRATOR.'/components/com_quix/quix.xml'));
        $tag = (string) $form->tag;

        if ($tag === 'pro' || $tag === '##QUIXNXT_VERSION_TAG##') {
            return true;
        }

        return false;
    }

    public static function isProAuthinticated()
    {
        $credentials = self::checkUpdate();

        if (empty($credentials) || empty($credentials->username) || empty($credentials->key) || !$credentials->activated) {
            return false;
        }

        return true;
    }

  /*
  * to get update info
  * use layout to get alert structure
  */

    public static function checkUpdate()
    {
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_quix/models');

        $config = JModelLegacy::getInstance('Config', 'QuixModel', array('ignore_request' => false));
        $config->generateState();

        return $config->getItem();
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
            $menuExcludedUrl = $params->get('menuexcludedurl', array());

          // exclude all assets from this url
            if (! in_array('index.php?option=com_quix', $menuExcludedUrl, true)) {
                $menuExcludedUrl[] = 'index.php?option=com_quix';

                $params->set('menuexcludedurl', $menuExcludedUrl);

                $object               = new stdClass();
                $object->extension_id = $plugin->id;
                $object->params       = $params->toString();

                JFactory::getDbo()->updateObject('#__extensions', $object, 'extension_id');
            }

          // exclude js from this url
            $excludeJsComponents_peo = $params->get('excludeJsComponents_peo', array());
            if (! in_array('quix', $excludeJsComponents_peo, true)) {
                $excludeJsComponents_peo[] = 'quix';

                $params->set('excludeJsComponents_peo', $excludeJsComponents_peo);

                $object               = new stdClass();
                $object->extension_id = $plugin->id;
                $object->params       = $params->toString();

                JFactory::getDbo()->updateObject('#__extensions', $object, 'extension_id');
            }

            return true;
        }

      return false;
    }
}
