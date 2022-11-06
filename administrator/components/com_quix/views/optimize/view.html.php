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
class QuixViewOptimize extends JViewLegacy
{

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

		QuixHelper::addSubmenu('optimize');

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
		JToolBarHelper::title(JText::_('COM_QUIX_TITLE_QUIXOPTIMIZE'), 'home');
		
		require_once JPATH_COMPONENT . '/helpers/quix.php';

		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.collapse');
		$bar->appendButton('Custom', $layout->render(array()), 'collapse');


		$state = $this->get('State');
		$canDo = QuixHelper::getActions('core.edit');

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

			$layout = new JLayoutFile('toolbar.schema');
			$bar->appendButton('Custom', $layout->render(array()), 'schema');

			$layout = new JLayoutFile('toolbar.clearcache');
			$bar->appendButton('Custom', $layout->render(array()), 'clearcache');

		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_quix&view=optimize');

	}
}
