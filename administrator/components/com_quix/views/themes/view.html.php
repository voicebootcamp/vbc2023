<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * View class for a list of Quix.
 *
 * @since  1.6
 */
class QuixViewThemes extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		QuixHelper::addSubmenu('themes');

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
		require_once JPATH_COMPONENT . '/helpers/quix.php';
		$app = JFactory::getApplication();

		$state = $this->get('State');
		$canDo = QuixHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_QUIX_TITLE_THEMES'), 'generic');

		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.collapse');
		$bar->appendButton('Custom', $layout->render(array()), 'collapse');
		
		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/collection';

		// if($app->input->get('legacy') == true){
		// 	if (file_exists($formPath))
		// 	{
		// 		if ($canDo->get('core.create'))
		// 		{
		// 			JToolBarHelper::addNew('collection.add', 'COM_QUIX_JTOOLBAR_NEW_LIB_OLD');
		// 		}
		// 	}
		// }
		if ($canDo->get('core.create'))
		{
			$link = JRoute::_(JUri::root() . 'index.php?option=com_quix&task=collection.add&quixlogin=true');
			$toolbar = JToolBar::getInstance('toolbar');
			
			$toolbar->appendButton('Custom', "<a href='#' data-toggle='modal' class='btn hasTooltip' data-target='#newLibraryModal' data-title='".JText::_('Visual Builder')."'><i class='icon-new'></i> ".JText::_('JTOOLBAR_NEW')."</a>", 'new-visual');
			
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('themes.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('themes.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'themes.delete', 'JTOOLBAR_DELETE');
			}

			// if (isset($this->items[0]->state))
			// {
			// 	JToolBarHelper::divider();
			// 	JToolBarHelper::archiveList('themes.archive', 'JTOOLBAR_ARCHIVE');
			// }

			// if (isset($this->items[0]->checked_out))
			// {
			// 	JToolBarHelper::custom('themes.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			// }

		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'themes.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('themes.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_quix');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::divider();
			$bar = JToolBar::getInstance('toolbar');

			// Instantiate a new JLayoutFile instance and render the layout
			// JHtml::_('behavior.modal', 'a.quixSettings');
			$layout = new JLayoutFile('toolbar.mysettings');

			$bar->appendButton('Custom', $layout->render(array()), 'mysettings');

			$layout = new JLayoutFile('toolbar.clearcache');
			$bar->appendButton('Custom', $layout->render(array()), 'clearcache');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_quix&view=themes');

		$this->extra_sidebar = '';
		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);
		
	}

	/**
	 * Method to order fields
	 *
	 * @return void
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`title`' => JText::_('JGLOBAL_TITLE'),
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => JText::_('JSTATUS'),
			// 'a.`access`' => JText::_('COM_QUIX_COLLECTIONS_ACCESS'),
			// 'a.`language`' => JText::_('JGRID_HEADING_LANGUAGE'),
		);
	}
}
