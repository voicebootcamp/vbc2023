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
 * Message configuration model.
 *
 * @since  1.6
 */
class QuixModelFilemanager extends JModelForm
{
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
	 * @since   1.6
	 */
	protected function populateState()
	{
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
		$item = new stdClass(); 
		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm	 A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quix.config', 'config', array('control' => 'jform', 'load_data' => $loadData));

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
	 * @since   1.6
	 */
	// public function save($data)
	// {
		// $db = $this->getDbo();
		// $params = $this->getItem();		
		// $data = array_merge($params, $data);
		
		// if (count($data))
		// {
			
		// 	$query = $db->getQuery(true);
		// 	$component = JComponentHelper::getComponent('com_quix');	
		// 	// Conditions for which records should be updated.
		// 	$conditions = array(
		// 	    $db->quoteName('extension_id') . ' = ' . $component->id
		// 	);
		// 	// Fields to update.
		// 	$data = json_encode($data);
		// 	$fields = array(
		// 	    $db->quoteName('params') . ' = ' . $db->quote($data)
		// 	);
			 
		// 	$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

		// 	$db->setQuery($query);

		// 	try
		// 	{
		// 		$db->execute();
		// 	}
		// 	catch (RuntimeException $e)
		// 	{
		// 		$this->setError($e->getMessage());

		// 		return false;
		// 	}
		// }
		// else
		// {
		// 	$this->setError('COM_QUIX_ERR_INVALID_UPDATE_INFO');

		// 	return false;
		// }
	// }
}
