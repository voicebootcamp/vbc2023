<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the JMedia component
 *
 * @since  1.0
 */
class JMediaViewMedia extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$config = JComponentHelper::getParams('com_jmedia');

		if (!$app->isClient('administrator'))
		{
			return $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
		}

		// Set the toolbar
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		// Get the toolbar object instance
		$bar  = JToolbar::getInstance('toolbar');
		$user = JFactory::getUser();

		// Set the titlebar text
		JToolbarHelper::title(JText::_('COM_JMEDIA'), 'images mediamanager');

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.logo');
		$bar->appendButton('Custom', $layout->render(array()), 'logo');
		
		JToolbarHelper::divider();

		// Add a preferences button
		if ($user->authorise('core.admin', 'com_jmedia') || $user->authorise('core.options', 'com_jmedia'))
		{
			JToolbarHelper::preferences('com_jmedia');
			JToolbarHelper::divider();
		}

		JToolbarHelper::help('JHELP_CONTENT_MEDIA_MANAGER');
        JToolbarHelper::modal("aboutModal", 'icon-checkbox-partial', 'About');

    }
}
