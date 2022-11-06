<?php
/* ======================================================
 # Limit Active Logins for Joomla! - v1.1.2 (pro version)
 # -------------------------------------------------------
 # For Joomla! CMS (v4.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2022 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://limitactivelogins.web357.com/
 # Support: support@web357.com
 # Last modified: Thursday 31 March 2022, 12:05:22 PM
 ========================================================= */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Factory;
use \Joomla\CMS\User\User;
use \Joomla\CMS\Component\ComponentHelper;

/**
 * View class for a list of Limitactivelogins.
 *
 * @since  1.6
 */
class LimitactiveloginsViewLogs extends \Joomla\CMS\MVC\View\HtmlView
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
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->user = Factory::getUser();
		$this->session = Factory::getSession();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Component params
		$params = ComponentHelper::getComponent('com_limitactivelogins')->getParams();
		$this->showGravatar = $params->get('showGravatar', 1);

		LimitactiveloginsHelper::addSubmenu('logs');

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
		$state = $this->get('State');
		$canDo = LimitactiveloginsHelper::getActions();

		if ($this->getLayout() == 'grouped_by_user')
		{
			JToolbarHelper::title(JText::_('COM_LIMITACTIVELOGINS').': '.JText::_('COM_LIMITACTIVELOGINS_LOGGED_IN_USERS_GROUPED_BY_USER'), 'user');
		}
		else
		{
			JToolbarHelper::title(JText::_('COM_LIMITACTIVELOGINS').': '.JText::_('COM_LIMITACTIVELOGINS_LOGGED_IN_USERS_DETAILED'), 'users');
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'logs.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($canDo->get('core.edit.state'))
			{
				if ($this->getLayout() == 'default')
				{
					JToolBarHelper::deleteList('', 'logs.delete', 'Delete selected sessions (Logout Users)');
				}
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_limitactivelogins');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_limitactivelogins&view=logs');
	}
}
