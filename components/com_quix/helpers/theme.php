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
class QuixFrontendHelperTheme
{
    /*
    * add Condition
    */
    public static function log($id, $type, $data = [])
    {
        if (!$id) {
            return;
        }

        $isExisting = self::checkNew($id, $type, $data['params']);
        if ($isExisting) {
            return $isExisting;
        }

        // Create and populate an object.
        $obj = new stdClass();
        $obj->id = 0;
        $obj->item_id = $id;
        $obj->item_type = $type;
        $obj->component = (isset($type) and $type == 'article') ? 'com_content' : 'core' ;
        $obj->condition_type = isset($data['condition_type']) ? $data['condition_type'] : 'articles';
        $obj->condition_id = isset($data['condition_id']) ? $data['condition_id'] : 0;
        $obj->condition_info = isset($data['condition_info']) ? $data['condition_info'] : '';
        $obj->params = isset($data['params']) ? json_encode($data['params']) : '{}';

        $result = self::addCondition($obj);

        return $result;
    }

    /*
    * Check new
    */
    public static function checkNew($id, $type, $params)
    {
        $params = json_encode($params);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('id')
            ->from('#__quix_conditions')
            ->where('item_id = ' . intval($id))
            ->where('item_type = "' . $type . '"')
            ->where("params = '" . $params . "'");
        // echo $query->__toString();die;
        $db->setQuery($query);
        return $db->loadResult();
    }

    /*
    * add stats
    */
    public static function addCondition($obj)
    {
        $db = JFactory::getDbo();
        $db->insertObject('#__quix_conditions', $obj);
        return $db->insertid();
    }

    /*
    * update stats
    */
    public static function updateCondition($obj)
    {
        $db = JFactory::getDbo();
        $db->updateObject('#__quix_conditions', $obj, 'id');
        return $obj->id;
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
            $db->quoteName('item_id') . ' = ' . $item_id,
            $db->quoteName('item_type') . ' = ' . $item_type
        ];

        $query->delete($db->quoteName('#__quix_conditions'));
        $query->where($conditions);

        $db->setQuery($query);

        return $db->execute();
    }

    public static function removeConditionsByIds($item_id, $ids)
    {
        if ($ids == null) {
            return;
        }
        $ids = implode(', ', $ids);
        if (!$ids) {
            return;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // delete all custom keys for user 1001.
        $conditions = [
            $db->quoteName('item_id') . ' = ' . $item_id,
            $db->quoteName('id') . ' not in (' . $ids . ')'
        ];

        $query->delete($db->quoteName('#__quix_conditions'));
        $query->where($conditions);
        // echo $query->__toString();die;
        $db->setQuery($query);

        return $db->execute();
    }

    /*
     * Check new
     */
    public static function getAll($id, $type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__quix_conditions')
            ->where('item_id = ' . intval($id))
            ->where('item_type = "' . $type . '"');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /*
     * Check new
     */
    public static function getByType($type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.*')
            ->from('#__quix_conditions AS a')
            ->where('a.item_type = "' . $type . '"');

        // Join over the item+id
        $query->select('c.state AS item_status');
        $query->join('LEFT', '#__quix_collections AS c ON c.id=a.item_id');
        $query->order('c.ordering ASC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getAllTypesMatch($item_type, $component, $condition_type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__quix_conditions')
            ->where('item_type = "' . $item_type . '"')
            ->where('component = "' . $component . '"')
            ->where('condition_type = "' . $condition_type . '"');

        $db->setQuery($query);
        return $db->loadObject();
    }
}
