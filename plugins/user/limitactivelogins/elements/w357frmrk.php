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

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport( 'joomla.form.form' );

class JFormFieldw357frmrk extends JFormField {
	
	protected $type = 'w357frmrk';

	protected function getLabel()
	{
		return '';	
	}

	protected function getInput() 
	{
		// Call the Web357 Framework Helper Class
		require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'web357framework'.DIRECTORY_SEPARATOR.'web357framework.class.php');
		$w357frmwrk = new Web357FrameworkHelperClass;

		// API Key Checker
		$w357frmwrk->apikeyChecker();

		// BEGIN: Check if Web357 Framework plugin exists
		jimport('joomla.plugin.helper');
		if(!JPluginHelper::isEnabled('system', 'web357framework')):
			$web357framework_required_msg = JText::_('<p>The <strong>"Web357 Framework"</strong> is required for this extension and must be active. Please, download and install it from <a href="http://downloads.web357.com/?item=web357framework&type=free">here</a>. It\'s FREE!</p>');
			JFactory::getApplication()->enqueueMessage($web357framework_required_msg, 'warning');
			return false;
		else:
			return '';	
		endif;
		// END: Check if Web357 Framework plugin exists
	}
	
}