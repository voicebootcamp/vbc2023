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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\User\User;
/**
 * View to edit
 *
 * @since  1.6
 */
class LimitactiveloginsViewLog extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $state;

	protected $item;

	protected $form;

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
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->user = Factory::getUser();
		$this->session = Factory::getSession();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user  = Factory::getUser();
		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = LimitactiveloginsHelper::getActions();

		JToolbarHelper::title(JText::_('COM_LIMITACTIVELOGINS').': '.JText::_('COM_LIMITACTIVELOGINS_TITLE_LOG'), 'users');

		// Button for delete the session and logout the user
		// Of course ou can't delete the current session because you're gonna be out of Joomla! admin panel.
		if ($this->session->getId() === $this->item->session_id)
		{
			JToolBarHelper::custom('log.deleteSessionAndLogoutTheUser', 'trash.png', 'trash_f2.png', 'Delete <b>your</b> own session (logout yourself)', false);
		}
		else
		{
			JToolBarHelper::custom('log.deleteSessionAndLogoutTheUser', 'trash.png', 'trash_f2.png', 'Delete this session (logout User)', false);
		}
				
		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('log.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('log.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
