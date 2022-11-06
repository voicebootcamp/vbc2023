<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_quix
 * @since       1.3.0
 */
class QuixFrontendHelperEditor
{
    /*
    * add Condition
    */
    public static function log($context, $context_id, $collection_id)
    {
        // Create and populate an object.
        $obj                = new stdClass();
        $obj->id            = 0;
        $obj->context       = $context;
        $obj->context_id    = $context_id;
        $obj->collection_id = $collection_id;
        $obj->params        = '{}';


        return self::addCondition($obj);

    }

    /*
    * Check new
    */
    public static function getId($context, $context_id)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__quix_editor_map')
            // ->where('(context = ' . $db->quote($context) . ' OR context = ' . 0 . ')')
            ->where('(context = '.$db->quote($context).' OR context = '.$db->quote('0').')')
            ->where('context_id = "'.intval($context_id).'"');

        $db->setQuery($query);
        $result = $db->loadObject();

        // check if context is missing
        // since 2.7.2
        if (isset($result->id) && $result->id && ! $result->context) {
            $contextArray  = explode(".", $context);
            $originalTitle = ucfirst($contextArray[1]).':'.$context_id;
            $collection_id = $result->collection_id;
            $collection    = QuixAppHelper::qxGetCollectionById($collection_id);
            if ($originalTitle === $collection->title) {
                // found it
                $result->context = $context;
                self::updateCondition($result);
            }
        }

        // return id
        return $result->id ?? 0;
    }

    /*
    * Check new
    */
    public static function getInfo($context, $context_id)
    {
        if ($context === 'com_content.form') {
            $context = 'com_content.article';
        }

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__quix_editor_map')
            // ->where('context = ' . $db->quote($context))
            ->where('(context = '.$db->quote($context).' OR context = '.$db->quote('0').')')
            ->where('context_id = '.intval($context_id));

        $db->setQuery($query);
        $result = $db->loadObject();

        // check if context is missing
        // since 2.7.2
        if (isset($result->id) && $result->id) {

            //fix if had multiple value
            self::fixOldDuplicateValue($result);

            if ( ! $result->context) {
                $contextArray  = explode(".", $context);
                $originalTitle = ucfirst($contextArray[1]).':'.$context_id;
                $collection_id = $result->collection_id;
                $collection    = QuixAppHelper::qxGetCollectionById($collection_id);
                if ($originalTitle === $collection->title) {
                    // found it
                    $result->context = $context;
                    self::updateCondition($result);
                }
            }
        }

        return $result ?? null;
    }


    /*
    * add stats
    */
    public static function addCondition($obj)
    {
        $db = JFactory::getDbo();
        $db->insertObject('#__quix_editor_map', $obj);

        return $db->insertid();
    }

    /*
    * update stats
    */
    public static function updateCondition($obj)
    {
        $db = JFactory::getDbo();
        $db->updateObject('#__quix_editor_map', $obj, 'id');

        return $obj->id;
    }

    public static function disableEditor($id)
    {
        // Create an object for the record we are going to update.
        $object         = new stdClass();
        $object->id     = $id;
        $object->status = 0;

        return JFactory::getDbo()->updateObject('#__quix_editor_map', $object, 'id');
    }

    public static function enableEditor($id)
    {
        // Create an object for the record we are going to update.
        $object         = new stdClass();
        $object->id     = $id;
        $object->status = 1;

        return JFactory::getDbo()->updateObject('#__quix_editor_map', $object, 'id');
    }

    /*
    * update stats
    */
    public static function removeCondition($item_id, $item_type)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        // delete all custom keys for user 1001.
        $conditions = [
            $db->quoteName('item_id').' = '.$item_id,
            $db->quoteName('item_type').' = '.$item_type
        ];

        $query->delete($db->quoteName('#__quix_editor_map'));
        $query->where($conditions);

        $db->setQuery($query);

        return $db->execute();
    }

    public static function removeConditionsByIds($item_id, $ids)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        // delete all custom keys for user 1001.
        $conditions = [
            $db->quoteName('item_id').' = '.$item_id,
            $db->quoteName('id').' not in ('.implode(", ", $ids).')'
        ];

        $query->delete($db->quoteName('#__quix_editor_map'));
        $query->where($conditions);
        $db->setQuery($query);

        return $db->execute();
    }

    /*
     * Check new
     */
    public static function getAll($id, $type)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__quix_editor_map')
            ->where('item_id = '.(int) $id)
            ->where('item_type = "'.$type.'"');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public static function fixOldDuplicateValue($item)
    {

        // let's see if this collection id exists
        $db             = JFactory::getDbo();
        $query          = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__quix_collections')
            ->where('id = '.intval($item->collection_id));
        $db->setQuery($query);
        $collection = $db->loadObject();
        if ($collection === null) {
            // no collection with this id, so remove all
            $query = $db->getQuery(true);

            // delete all custom keys for user 1001.
            $conditions = [
                $db->quoteName('collection_id').' = '.$item->collection_id
            ];

            $query->delete($db->quoteName('#__quix_editor_map'));
            $query->where($conditions);
            // echo $query->__toString();die;

            $db->setQuery($query);

            return $db->execute();
        }


        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__quix_editor_map')
            ->where('collection_id = '.intval($item->collection_id));

        $db->setQuery($query);
        $results = $db->loadObjectList();
        if (count($results) > 1) {
            $idCollection = [];

            foreach ($results as $result) {
                if ($item->id === $result->id) {
                    continue;
                }
                $idCollection[] = $result->id;
            }

            $query = $db->getQuery(true);

            // delete all custom keys for user 1001.
            $conditions = [
                $db->quoteName('id').' in ('.implode(", ", $idCollection).')'
            ];

            $query->delete($db->quoteName('#__quix_editor_map'));
            $query->where($conditions);
            // echo $query->__toString();die;

            $db->setQuery($query);

            return $db->execute();
        }
    }
}
