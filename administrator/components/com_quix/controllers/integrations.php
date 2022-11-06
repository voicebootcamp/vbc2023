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
 * Integrations controller class.
 *
 * @since  1.6
 */
class QuixControllerIntegrations extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'integrations';
		parent::__construct();
	}

	/**
	 * Method to save a record.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function update()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$model = $this->getModel('Integrations', 'QuixModel');
		$data  = $this->input->post->get('jform', array(), 'array');
		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		$data = $model->validate($form, $data);
		$msg  = '';

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation pages.
			$errors = $model->getErrors();

			// Push up to three validation pages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$msg .= $errors[$i]->getMessage();
				}
				else
				{
					$msg .= $errors[$i];
				}
			}

			$err = new Exception($msg);
			echo new JResponseJson($err);
			JFactory::getApplication()->close();
			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data))
		{
			$err = new Exception(JText::sprintf('JERROR_SAVE_FAILED', $model->getError()));
			echo new JResponseJson($err);
			JFactory::getApplication()->close();
			return false;
		}

		// checkin the id of collection		
		echo new JResponseJson(JText::_('COM_QUIX_CONFIG_SAVED'));
		JFactory::getApplication()->close();
		return true;
	}
}
