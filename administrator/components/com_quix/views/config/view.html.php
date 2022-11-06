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
 * View to edit messages user configuration.
 *
 * @since  1.6
 */
class QuixViewConfig extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

  /**
   * Execute and display a template script.
   *
   * @param   null  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  mixed  A string if successful, otherwise a Error object.
   *
   * @throws Exception
   * @since   1.6
   */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
		  JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		// Bind the record to the form.
		$this->form->bind($this->item);

		parent::display($tpl);
	}
}
