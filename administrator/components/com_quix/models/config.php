<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

defined('_JEXEC') or die;

/**
 * Message configuration model.
 *
 * @since  1.6
 */
class QuixModelConfig extends JModelForm
{
  /**
   * @throws Exception
   * @since 3.0.0
   */
  public function generateState()
  {
    $this->populateState();
  }

  /**
   * Method to auto-populate the model state.
   *
   * This method should only be called once per instantiation and is designed
   * to be called on the first call to the getState() method unless the model
   * configuration flag to ignore the request is set.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return  void
   *
   * @throws Exception
   * @since   1.6
   */
  protected function populateState()
  {
    $db = $this->getDbo();
    // get extension id
    $query = $db->getQuery(true)
      ->select('extension_id')
      ->from('#__extensions')
      ->where($db->quoteName('type') . ' = ' . $db->quote('package'))
      ->where($db->quoteName('element') . ' = ' . $db->quote('pkg_quix'));

    $db->setQuery($query);

    $extensionId = $db->loadResult();

    if (!$extensionId)
    {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_QUIX_NO_EXTENSION_RECORD_FOUND'), 'warning');
    }

    $this->setState('extensionid', $extensionId);

    // get update_site_id
    $query = $db->getQuery(true)
      ->select('update_site_id')
      ->from('#__update_sites_extensions')
      ->where($db->quoteName('extension_id') . ' = ' . $db->quote($extensionId));

    $db->setQuery($query);

    $update_site_id = $db->loadResult();
    if (!$update_site_id)
    {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_QUIX_NO_UPDATE_SITE_FOUND'), 'error');
    }

    $this->setState('update_site_id', $update_site_id);

    // Load the parameters.
    $params = JComponentHelper::getParams('com_quix');
    $this->setState('params', $params);
  }

  /**
   * Method to get a single record.
   *
   * @return  mixed  Object on success, false on failure.
   *
   * @since   1.6
   */
  public function getItem()
  {
    $item = new JObject;

    $db    = $this->getDbo();
    $query = $db->getQuery(true)
      ->select('a.*')
      ->from($db->quoteName('#__update_sites', 'a'))
      ->where($db->quoteName('a.update_site_id') . ' = ' . (int) $this->getState('update_site_id'));

    $db->setQuery($query);

    try
    {
      $row = $db->loadObject();
    }
    catch (RuntimeException $e)
    {
      JErrorPage::render($e);

      return false;
    }
    if (isset($row->extra_query))
    {
      $extra_query = $row->extra_query;
      if (!empty($extra_query))
      {
        $extra_queryArray = explode('&', $extra_query);
        if (is_array($extra_queryArray))
        {
          foreach ($extra_queryArray as $key => $value)
          {
            $valueArray = explode('=', $value);
            $item->set($valueArray[0], $valueArray[1]);
          }
        }
      }
    }

    // dont allow empty request

    $query = $db->getQuery(true);
    $query->select('*')->from('#__quix_configs');
    // ->where("`name` = 'activated'");

    $db->setQuery($query);
    $params = $db->loadObjectList();
    foreach ($params as $key => $param)
    {
      $item->{$param->name} = $param->params;
    }

    if(!isset($item->activated)){
        $item->activated = 0;
    }

    $this->preprocessData('com_quix.config', $item);

    return $item;
  }

  /**
   * Method to get the record form.
   *
   * @param   array    $data      Data for the form.
   * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
   *
   * @return  JForm   A JForm object on success, false on failure
   *
   * @since   1.6
   */
  public function getForm($data = [], $loadData = true)
  {
    // Get the form.
    $form = $this->loadForm('com_quix.config', 'config', ['control' => 'jform', 'load_data' => $loadData]);

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Method to save the form data.
   *
   * @param   array  $data  The form data.
   *
   * @return  boolean  True on success.
   *
   * @throws Exception
   * @since   1.6
   */
  public function save(array $data)
  {
    $db = $this->getDbo();

    if ($extensionid = (int) $this->getState('extensionid'))
    {
      if (count($data))
      {
        // try to save them on config table
        $this->saveToConfig($data);

        $prefix      = '';
        $extra_query = '';
        foreach ($data as $key => $value)
        {
          $extra_query .= $prefix . $key . '=' . $value;
          $prefix      = '&';
        }

        $query = $db->getQuery(true);

        // Fields to update.
        $fields = [
          $db->quoteName('extra_query') . ' = ' . $db->quote($extra_query)
        ];

        // Conditions for which records should be updated.
        $conditions = [
          $db->quoteName('update_site_id') . ' = ' . $db->quote((int) $this->getState('update_site_id'))
        ];

        $query->update($db->quoteName('#__update_sites'))->set($fields)->where($conditions);

        $db->setQuery($query);

        try
        {
          // Clear relavent cache
          $this->cleanCache('com_installer');

          $db->execute();
        }
        catch (RuntimeException $e)
        {
          $this->setError($e->getMessage());

          return false;
        }
      }

      return true;
    }
    else
    {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_QUIX_ERR_INVALID_UPDATE_INFO'), 'error');

      return false;
    }
  }

  public function saveToConfig($data)
  {
    $result = [];
    // dont allow empty request
    $db    = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('*')
      ->from('#__quix_configs');
    $db->setQuery($query);
    $config = $db->loadObjectList();

    if (!count($config))
    {
      $result = [];
      // set all, insert
      $obj         = new stdClass();
      $obj->name   = 'username';
      $obj->params = $data['username'];
      // Insert the object into the user obj table.
      $result[] = JFactory::getDbo()->insertObject('#__quix_configs', $obj);

      $obj         = new stdClass();
      $obj->name   = 'key';
      $obj->params = $data['key'];

      // Insert the object into the user obj table.
      $result[] = JFactory::getDbo()->insertObject('#__quix_configs', $obj);

      $obj         = new stdClass();
      $obj->name   = 'activated';
      $obj->params = $data['activated'];

      // Insert the object into the user obj table.
      $result[] = JFactory::getDbo()->insertObject('#__quix_configs', $obj);
    }
    else
    {
      $keys = [];
      foreach ($config as $key => $item)
      {
        $keys[] = $item->name;
      }

      foreach ($data as $key => $value)
      {
        // Create an object for the record we are going to update.
        $obj         = new stdClass();
        $obj->name   = $key;
        $obj->params = $value;

        if (in_array($key, $keys))
        {
          // Update their details in the users table using id as the primary key.
          $result[] = JFactory::getDbo()->updateObject('#__quix_configs', $obj, 'name');
        }
        else
        {
          // Insert the object into the user obj table.
          $result[] = JFactory::getDbo()->insertObject('#__quix_configs', $obj);
        }
      }
    }

    if (in_array(false, $result))
    {
      return false;
    }

    return true;
  }

  /**
   * Custom clean the cache of com_content and content modules
   *
   * @param   null     $group      The cache group
   * @param   integer  $client_id  The ID of the client
   *
   * @return  void
   *
   * @since   1.6
   */
  protected function cleanCache($group = null, $client_id = 0)
  {
    //QuixHelper::cleanCache();
    QuixHelperCache::cleanCache();
    parent::cleanCache('com_quix');
    parent::cleanCache('mod_quix');
    parent::cleanCache('lib_quix');
  }
}
