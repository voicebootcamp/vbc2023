<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel as JModelList;

/**
 * Methods supporting a list of Quix records.
 *
 * @since  1.6
 */
class QuixModelPages extends JModelList
{
/**
    * Constructor.
    *
    * @param   array  $config  An optional associative array of configuration settings.
    *
    * @see        JController
    * @since      1.6
    */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.`id`',
                'title', 'a.`title`',
                'ordering', 'a.`ordering`',
                'state', 'a.`state`',
                'access', 'a.`access`',
                'language', 'a.`language`',
                'created_by', 'a.`created_by`',
                'params', 'a.`params`',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   Elements order
     * @param   string  $direction  Order direction
     *
     * @return void
     *
     * @throws Exception
     * @since 1.0.0
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        // Filtering language
        // Language filters for all languages is a * make it empty
        if (JFactory::getApplication()->input->getVar('filter_language') == '*') {
            JFactory::getApplication()->input->set('filter_language', '');
        }
        $this->setState('filter.language', $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '', 'string'));


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
     * @param   string  $id  A prefix for the store id.
     *
     * @return   string A store id.
     *
     * @since    1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');

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
        $query->from('`#__quix` AS a');

        // Join over the users for the checked out user
        $query->select("uc.name AS editor");
        $query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

        // Join over the asset groups.
        $query->select('ag.title AS access_level')
            ->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the language
        $query->select('l.title AS language_title, l.image AS language_image')
            ->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

        // Join over the user field 'created_by'
        // $query->select('`created_by`.name AS `created_by`');
        // $query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

        // Filter by published state
        $published = $this->getState('filter.state');

        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.`title` LIKE ' . $search . '  OR  a.`access` LIKE ' . $search . '  OR  a.`language` LIKE ' . $search . ' )');
            }
        }


        //Filtering language
        $filter_language = $this->state->get("filter.language");
        if ($filter_language) {
            $query->where("a.`language` = '".$db->escape($filter_language)."'");
        }
        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }
}
