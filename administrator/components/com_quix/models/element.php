<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Quix model.
 *
 * @since  1.6
 */
class QuixModelElement extends JModelAdmin
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
   * @param   string  $type    The table type to instantiate
   * @param   string  $prefix  A prefix for the table class name. Optional.
   * @param   array   $config  Configuration array for model. Optional.
   *
   * @return    JTable    A database object
   *
   * @since    1.6
   */
  public function getTable($type = 'Element', $prefix = 'QuixTable', $config = [])
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the record form.
   *
   * @param   array    $data      An optional array of data for the form to interogate.
   * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
   *
   * @return  false  A JForm object on success, false on failure
   *
   * @since    1.6
   */
  public function getForm($data = [], $loadData = true)
  {
    // Initialise variables.
    // Get the form.
    $form = $this->loadForm(
      'com_quix.element',
      'page',
      ['control'   => 'jform',
       'load_data' => $loadData
      ]
    );

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Override preprocessForm to load custom form from templates
   *
   * @param   JForm   $form   A JForm object.
   * @param   mixed   $data   The data expected for the form.
   * @param   string  $group  The name of the plugin group to import (defaults to "content").
   *
   * @return  void
   *
   * @throws  Exception if there is an error in the form event.
   *
   * @since   1.6
   */
  protected function preprocessForm(JForm $form, $data, $group = 'content')
  {
    JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms');
    $form->loadFile('element', false);
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
    $data = JFactory::getApplication()->getUserState('com_quix.edit.element.data', []);

    if (empty($data))
    {
      if ($this->item === null)
      {
        $this->item = $this->getItem();
      }

      $data = $this->item;

      //Support for multiple or not foreign key field: access
      $array = [];
      foreach ((array) $data->access as $value):
        if (!is_array($value)):
          $array[] = $value;
        endif;
      endforeach;
      $data->access = $array;
    }

    return $data;
  }

  /**
   * Prepare and sanitise the table prior to saving.
   *
   * @param   JTable  $table  Table Object
   *
   * @return void
   *
   * @since    1.6
   */
  protected function prepareTable($table)
  {
    jimport('joomla.filter.output');

    if (empty($table->id))
    {
      // Set ordering to the last item if not set
      if (@$table->ordering === '')
      {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT MAX(ordering) FROM #__quix_elements');
        $max             = $db->loadResult();
        $table->ordering = $max + 1;
      }
    }
  }

  /**
   * Method to save the form data.
   *
   * @param   array  $data  The form data.
   *
   * @return  boolean  True on success.
   *
   * @since   1.6
   */
  public function save($data)
  {
    $input  = JFactory::getApplication()->input;
    $filter = JFilterInput::getInstance();

    if (isset($data['params']))
    {
      $registry = new Registry;
      $registry->loadArray($data['params']);
      $data['params'] = (string) $registry;
    }

    // Clear relavent cache
    $this->cleanCache();

    if (parent::save($data))
    {
      $this->getState('element.id');

      return true;
    }

    return false;
  }

  /**
   * Custom clean the cache of com_content and content modules
   *
   * @param   string   $group      The cache group
   * @param   integer  $client_id  The ID of the client
   *
   * @return  void
   *
   * @since   1.6
   */
  protected function cleanCache($group = null, $client_id = 0)
  {
    QuixHelper::cleanCache();
    parent::cleanCache('com_quix');
    parent::cleanCache('mod_quix');
    parent::cleanCache('lib_quix');
  }
}
