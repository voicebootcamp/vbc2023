<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Methods supporting a list of Quix records.
 *
 * @since  1.6
 */
class QuixModelCollections extends JModelList
{
    /**
     * Constructor.
     *
     * @param  array  $config  An optional associative array of configuration settings.
     *
     * @see        JController
     * @since      1.6
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.`id`',
                'title', 'a.`title`',
                'type', 'a.`type`',
                'builder', 'a.`builder`',
                'builder_version', 'a.`builder_version`',
                'data', 'a.`data`',
                'ordering', 'a.`ordering`',
                'state', 'a.`state`',
                'access', 'a.`access`',
                'language', 'a.`language`',
                'created_by', 'a.`created_by`',
                'params', 'a.`params`',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param  null  $ordering   Elements order
     * @param  null  $direction  Order direction
     *
     * @return void
     *
     * @throws Exception
     * @since 3.0.0
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        // Filtering language
        // Language filters for all languages is a * make it empty
        if (JFactory::getApplication()->input->get('filter_language') == '*') {
            JFactory::getApplication()->input->set('filter_language', '');
        }
        $this->setState('filter.language', $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '', 'string'));

        $this->setState('filter.builder', $app->getUserStateFromRequest($this->context.'.filter.builder', 'filter_builder', '', 'string'));

        $this->setState('filter.collection', $app->getUserStateFromRequest($this->context.'.filter.collection', 'filter_collection', '', 'string'));

        // Load the parameters.
        $params = JComponentHelper::getParams('com_quix');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'desc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param  string  $id  A prefix for the store id.
     *
     * @return   string A store id.
     *
     * @since    1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':'.$this->getState('filter.search');
        $id .= ':'.$this->getState('filter.state');
        $id .= ':'.$this->getState('filter.builder');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return   JDatabaseQuery
     *
     * @since    1.6
     */
    protected function getListQuery()
    {
        $input = \JFactory::getApplication()->input;
        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'DISTINCT a.*'
            )
        );
        $query->from('`#__quix_collections` AS a');

        // Join over the users for the checked out user
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the user field 'created_by'
        $query->select('`created_by`.name AS `created_by`');
        $query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

        // Filter by published state
        $published = $this->getState('filter.state');

        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } elseif ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if ( ! empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            } else {
                $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('( a.`title` LIKE '.$search.'  OR  a.`access` LIKE '.$search.'  OR  a.`language` LIKE '.$search.' )');
            }
        }

        //Filtering language
        $filter_language = $this->state->get('filter.language');
        if ($filter_language) {
            $query->where("a.`language` = '".$db->escape($filter_language)."'");
        }

        //Filtering collection
        if ($input->get('layout', 'default') !== 'modal') {
            $filter_collection = $this->state->get('filter.collection', 'all');

            if ($filter_collection && $filter_collection !== 'all') {
                if ('default' === $filter_collection) {
                    $query->where("a.`type` in ('section', 'layout', 'header', 'footer')");
                } else {
                    $query->where("a.`type` = ".$db->quote($filter_collection));
                }
            } else{
                $query->where("a.`type` in ('section', 'layout')");
            }
        }


        //Filtering by builder
        $filter_collection = $this->state->get('filter.builder');
        if ($filter_collection && $filter_collection !== '*') {
            $query->where("a.`builder` = '".$db->escape($filter_collection)."'");
        }

        // Add the list ordering clause.
        $orderCol       = $this->state->get('list.ordering', 'a.id');
        $orderDirection = $this->state->get('list.direction', 'desc');

        if ($orderCol && $orderDirection) {
            $query->order($db->escape($orderCol.' '.$orderDirection));
        }

        return $query;
    }

    /**
     * Get an array of data items
     *
     * @return mixed Array of data items on success, false on failure.
     * @since 3.0.0
     */
    public function getItems()
    {
        $items = parent::getItems();
        if ($items === false) {
            return false;
        }

        foreach ($items as $oneItem) {
            if (isset($oneItem->access)) {
                // Get the title of that particular user group
                $title           = QuixHelper::getGroupNameByGroupId($oneItem->access);
                $oneItem->access = ! empty($title) ? $title : $oneItem->access;
            }
        }

        return $items;
    }
}
