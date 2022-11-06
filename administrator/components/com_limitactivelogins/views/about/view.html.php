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

// No direct access to this file
defined('_JEXEC') or die;

// import Joomla view library
jimport('joomla.application.component.view');

class LimitactiveloginsViewAbout extends JViewLegacy
{
	function display($tpl = null) 
	{
		$form	= $this->get('Form');
		
		// Check for model errors.
		if ($errors = $this->get('Errors')) {
			JError::raiseWarning(500, implode('<br />', $errors));
			return false;
		}
		
		if (version_compare(JVERSION, '3.0', 'ge')) :
			
			// J3X
			// Include helper submenu
			LimitactiveloginsHelper::addSubmenu('about');
	
			// Show sidebar
			$this->sidebar = JHtmlSidebar::render();

		endif; 

		// mapping variables
		$this->form = $form;
		
		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	protected function addToolBar()
	{
		// Set ToolBar title
		JToolbarHelper::title(JText::_('COM_LIMITACTIVELOGINS_ABOUT_WEB357'), 'info-2');
	}
}