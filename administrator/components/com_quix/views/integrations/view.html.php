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
 * View to edit messages user configuration.
 *
 * @since  1.6
 */
class QuixViewIntegrations extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
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
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}
		// print_r($this->item);die;
		// Bind the record to the form.
		$this->form->bind($this->item);

		QuixHelper::addSubmenu('integrations');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
        JToolBarHelper::title(JText::_('COM_QUIX_TITLE_INTEGRATIONS_MANAGER'), 'generic');

        // require_once JPATH_COMPONENT . '/helpers/quix.php';
		// $state = $this->get('State');
		// $canDo = QuixHelper::getActions($state->get('filter.category_id'));
        //
		//
		// $bar = JToolBar::getInstance('toolbar');
		// $layout = new JLayoutFile('toolbar.collapse');
		// $bar->appendButton('Custom', $layout->render(array()), 'collapse');
		//
		// if ($canDo->get('core.admin'))
		// {
		// 	JToolBarHelper::preferences('com_quix');
		// }
        //
		// if ($canDo->get('core.create'))
		// {
			// JToolBarHelper::addNew('page.add', 'COM_QUIX_TITLE_PAGE_NEW');
			// JToolBarHelper::addNew('collection.add', 'COM_QUIX_TITLE_COLLECTION_NEW');
			// JToolBarHelper::save('integrations.save', 'JTOOLBAR_APPLY');


		// 	$link = JRoute::_(JUri::root() . 'index.php?option=com_quix&task=page.add&quixlogin=true');
		// 	$toolbar = JToolBar::getInstance('toolbar');
		// 	$toolbar->appendButton('Custom', "<a href='".$link ."' target='_blank' class='btn hasTooltip' data-title='".JText::_('Visual Builder')."' data-content='".JText::_('With Visual Builder')."' data-placement='bottom'>".JText::_('JTOOLBAR_NEW_PAGE')."</a>", 'new');
		// }
        //
		// if ($canDo->get('core.edit.state'))
		// {
		// 	JToolbarHelper::divider();
		// 	$bar = JToolBar::getInstance('toolbar');
        //
		// 	// Instantiate a new JLayoutFile instance and render the layout
		// 	JHtml::_('behavior.modal', 'a.quixSettings');
		// 	$layout = new JLayoutFile('toolbar.mysettings');
        //
		// 	$bar->appendButton('Custom', $layout->render(array()), 'mysettings');
        //
		// 	$layout = new JLayoutFile('toolbar.clearcache');
		// 	$bar->appendButton('Custom', $layout->render(array()), 'clearcache');
        //
		// }

		// Set sidebar action - New in 3.0
		// JHtmlSidebar::setAction('index.php?option=com_quix&view=integrations');
		
	}
}
