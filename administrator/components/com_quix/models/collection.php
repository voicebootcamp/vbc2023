<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

/**
 * Quix model.
 *
 * @since  1.6
 */
class QuixModelCollection extends JModelAdmin
{
    /**
     * @var      string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $item;
    protected $text_prefix = 'COM_QUIX';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param  string  $type    The table type to instantiate
     * @param  string  $prefix  A prefix for the table class name. Optional.
     * @param  array  $config   Configuration array for model. Optional.
     *
     * @return    JTable    A database object
     *
     * @since    1.6
     */
    public function getTable($type = 'Collection', $prefix = 'QuixTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param  array  $data        An optional array of data for the form to interogate.
     * @param  boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm  A JForm object on success, false on failure
     *
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Initialise variables.
        $app = JFactory::getApplication();
        // Get the form.
        $form = $this->loadForm(
            'com_quix.collection', 'page',
            array(
                'control'   => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Override preprocessForm to load custom form from templates
     *
     * @param  JForm  $form    A JForm object.
     * @param  mixed  $data    The data expected for the form.
     * @param  string  $group  The name of the plugin group to import (defaults to "content").
     *
     * @return  void
     *
     * @throws    Exception if there is an error in the form event.
     *
     * @since   1.6
     */
    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/forms');
        $form->loadFile('collection', false);
        parent::preprocessForm($form, $data, $group);
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return   mixed  The data for the form.
     *
     * @since    1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_quix.edit.collection.data', array());

        if (empty($data)) {
            if ($this->item === null) {
                $this->item = $this->getItem();
            }

            $data = $this->item;

            //Support for multiple or not foreign key field: access
            $array = array();
            foreach ((array) $data->access as $value):
                if ( ! is_array($value)):
                    $array[] = $value;
                endif;
            endforeach;
            $data->access = $array;
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param  integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since    1.6
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            // Convert the metadata field to an array.
            $registry = new Registry;
            $registry->loadString($item->metadata);
            $item->metadata = $registry->toArray();
        }

        return $item;
    }

    /**
     * Method to get an object.
     *
     * @param  integer  $id  The id of the object to get.
     *
     * @return  mixed    Object on success, false on failure.
     * @since 3.0.0
     */
    public function &getData($id = null)
    {
        if ($id === null) {
            $empty = false;

            return $empty;
        }

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
              ->from($db->quoteName('#__quix_collections'))
              ->where($db->quoteName('id')." = ".(int) $id)
              ->where($db->quoteName('state')." = ". 1);

        $db->setQuery($query);
        $result = $db->loadObject();

        return $result;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param  JTable  $table  Table Object
     *
     * @return void
     *
     * @since    1.6
     */
    protected function prepareTable($table)
    {
        jimport('joomla.filter.output');

        if (empty($table->id)) {
            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = JFactory::getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__quix_collections');
                $max             = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    /**
     * Method to save the form data.
     *
     * @param  array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     */
    public function save($data)
    {
        $data = QuixHelperDatabase::populateCollectionDefaultData($data);

        // Clear relevant cache
        $this->cleanCache();

        if (parent::save($data)) {
            $this->getState('collection.id');
            $this->setState('collection.uid', $data['uid']);

            return true;
        }

        return false;

    }

    /**
     * Method to duplicate modules.
     *
     * @param  array  &$pks  An array of primary key IDs.
     *
     * @return  boolean  True if successful.
     *
     * @throws  Exception
     * @since   1.0.0
     */
    public function duplicate(&$pks)
    {
        $user = JFactory::getUser();
        $db   = $this->getDbo();

        // Access checks.
        if ( ! $user->authorise('core.create', 'com_quix.collection')) {
            throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
        }

        foreach ($pks as $pk) {
            $data = (array) $this->getItem($pk);
            $name = $this->NewTitle($data['title']);

            $data['id']    = '';
            $data['title'] = $name;
            $data['state'] = 0;

            if ( ! $this->save($data)) {
                return false;
            }
        }

        // Clear modules cache
        $this->cleanCache();

        return true;
    }

    /**
     * Method to change the name & alias.
     *
     * @param  string  $name  The name.
     *
     * @return  string  Contains the modified name and alias.
     *
     * @since   3.1
     */
    public function NewTitle($name)
    {
        // Alter the name
        return StringHelper::increment($name);
    }

    /**
     * Method to test whether a record can have its state edited.
     *
     * @param  object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canEditState($record)
    {
        // Check against the category.
        return JFactory::getUser()->authorise('core.edit.state', 'com_quix.collection.'.(int) $record->id);
    }
}
