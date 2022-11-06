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
use Joomla\Utilities\ArrayHelper;

/**
 * Quix model.
 *
 * @since  1.6
 */
class QuixModelPage extends JModelItem
{
  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return void
   *
   * @throws Exception
   * @since    1.6
   *
   */
  protected function populateState()
  {
    $app = \JFactory::getApplication();
    $id  = $app->input->get('id');
    $this->setState('com_quix.edit.page.id', $id);
    $this->setState('page.id', $id);

    // Load the parameters.
    $params = $app->getParams();
    $this->setState('params', $params);
  }

  /**
   * Method to get an object.
   *
   * @param   integer  $id  The id of the object to get.
   *
   * @return  mixed    Object on success, false on failure.
   * @since 3.0.0
   */
  public function &getData($id = null)
  {
    if ($this->_item === null)
    {
      $this->_item = false;

      if (empty($id))
      {
        $id = $this->getState('page.id');
      }

      // get users object
      $user = JFactory::getUser();

      // Get a level row instance.
      $table = $this->getTable();

      // Attempt to load the row.
      if ($table->load($id))
      {

        // Check published state.
        if ($published = $this->getState('filter.published', 1))
        {
          $isroot = $user->authorise('core.admin');
          if ($table->state != $published && !$isroot)
          {
            return $this->_item;
          }
        }

        // Convert the JTable to a clean JObject.
        $properties  = $table->getProperties(1);
        $this->_item = ArrayHelper::toObject($properties, 'JObject');
      }

      if (isset($this->_item->params))
      {
        // Convert the params field to an array.
        $registry            = new Registry;
        $this->_item->params = $registry->loadString($this->_item->params);

        // If no access filter is set, the layout takes some responsibility for display of limited information.
        $groups = $user->getAuthorisedViewLevels();

        if (in_array($this->_item->access, $user->groups) or in_array($this->_item->access, $groups))
        {
          $this->_item->params->set('access-view', true);
        }
        else
        {
          $this->_item->params->set('access-view', false);
        }

      }
    }

    if (!$this->_item->id)
    {
        JErrorPage::handleException(new Exception(JText::_('JERROR_PAGE_NOT_FOUND'), 404));
    }

    return $this->_item;
  }

    /**
     * Method to get a single record.
     *
     * @param  null  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since    1.6
     */
    public function getItem($pk = null)
    {
        return $this->getData();
    }

  /**
   * Get an instance of JTable class
   *
   * @param   string  $type    Name of the JTable class to get an instance of.
   * @param   string  $prefix  Prefix for the table class name. Optional.
   * @param   array   $config  Array of configuration values for the JTable object. Optional.
   *
   * @return  JTable|bool JTable if success, false on failure.
   */
  public function getTable($type = 'Page', $prefix = 'QuixTable', $config = array())
  {
    $this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_quix/tables');

    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Get the id of an item by alias
   *
   * @param   string  $alias  Item alias
   *
   * @return  mixed
   */
  public function getItemIdByAlias($alias)
  {
    $table = $this->getTable();

    $table->load(array('alias' => $alias));

    return $table->id;
  }

  /**
   * Method to check in an item.
   *
   * @param   integer  $id  The id of the row to check out.
   *
   * @return  boolean True on success, false on failure.
   *
   * @since    1.6
   */
  public function checkin($id = null)
  {
    // Get the id.
    $id = (!empty($id)) ? $id : (int) $this->getState('page.id');

    if ($id)
    {
      // Initialise the table
      $table = $this->getTable();

      // Attempt to check the row in.
      if (method_exists($table, 'checkin'))
      {
        if (!$table->checkin($id))
        {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Method to check out an item for editing.
   *
   * @param   integer  $id  The id of the row to check out.
   *
   * @return  boolean True on success, false on failure.
   *
   * @since    1.6
   */
  public function checkout($id = null)
  {
    // Get the user id.
    $id = (!empty($id)) ? $id : (int) $this->getState('page.id');

    if ($id)
    {
      // Initialise the table
      $table = $this->getTable();

      // Get the current user object.
      $user = JFactory::getUser();

      // Attempt to check the row out.
      if (method_exists($table, 'checkout'))
      {
        if (!$table->checkout($user->get('id'), $id))
        {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Get the name of a category by id
   *
   * @param   int  $id  Category id
   *
   * @return  Object|null  Object if success, null in case of failure
   */
  public function getCategoryName($id)
  {
    $db    = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
      ->select('title')
      ->from('#__categories')
      ->where('id = ' . $id);
    $db->setQuery($query);

    return $db->loadObject();
  }

  /**
   * Publish the element
   *
   * @param   int  $id     Item id
   * @param   int  $state  Publish state
   *
   * @return  boolean
   */
  public function publish($id, $state)
  {
    $table = $this->getTable();
    $table->load($id);
    $table->state = $state;

    return $table->store();
  }

  /**
   * Method to delete an item
   *
   * @param   int  $id  Element id
   *
   * @return  bool
   */
  public function delete($id)
  {
    $table = $this->getTable();

    return $table->delete($id);
  }

  /**
   * Increment the hit counter for the product.
   *
   * @param   integer  $pk  Optional primary key of the product to increment.
   *
   * @return  boolean  True if successful; false otherwise and internal error set.
   */
  public function getHit($pk = 0)
  {
    $input    = JFactory::getApplication()->input;
    $hitcount = $input->getInt('hitcount', 1);
    if ($hitcount)
    {
      $table = $this->getTable();

      if ($this->_item === null)
      {
        $pk    = (!empty($pk)) ? $pk : (int) $this->getState('page.id');
        $pk    = (!empty($pk)) ? $pk : (int) $this->getState('page.id');
        $table = $this->getTable();
        $table->load($pk);
        $table->hit($pk);
      }
      else
      {
        // $this->_item = JArrayHelper::fromObject($this->_item);
        // $table->bind($this->_item);
        $table->hit($this->_item->id);
      }


    }

    return true;
  }

}
