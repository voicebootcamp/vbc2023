<?php
/**  
 * @package JAMP
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Installer\InstallerAdapter;

/** 
 * Script to manage install/update/uninstall for component. Follow class convention
 * @package JAMP
 */
class JAmpBaseInstallerScript {
	/*
	* Find mimimum required joomla version for this extension. It will be read from the version attribute (install tag) in the manifest file
	*/
	private $minimum_joomla_release = '4.0';
	
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight(string $type, InstallerAdapter $parent): bool {
		// Check for Joomla compatibility
		if(version_compare(JVERSION, $this->minimum_joomla_release, '<')) {
			Factory::getApplication()->enqueueMessage (Text::sprintf('PLG_JAMP_INSTALLING_VERSION_NOTCOMPATIBLE', JVERSION), 'error');
			
			if(version_compare(JVERSION, '3.10', '<')) {
				Factory::getApplication()->enqueueMessage (Text::sprintf('Error, installation aborted. Pay attention! You are attempting to install a component package for Joomla 4 that does not match your actual Joomla version. Download and install the correct package for your Joomla %s version.', JVERSION), 'error');
			}
			return false;
		} 
		
		return true;
	}
	
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install(InstallerAdapter $parent): bool {
		// Reset any previous messages queue, keep only strict installation messages since now on
		Factory::getApplication()->getMessageQueue(true);
		
		return true;
	}
	
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update(InstallerAdapter $parent): bool {
		return true;
	}
	
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight(string $type, InstallerAdapter $parent): bool {
		return true;
	}
	
	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall(InstallerAdapter $parent): bool {
		return true;
	}
}

// Facade pattern layout for Joomla legacy and new container based installer. Legacy installer up to 4.2, new container installer from 4.3+
if(version_compare(JVERSION, '4.3', '>=') && interface_exists('\\Joomla\\CMS\\Installer\\InstallerScriptInterface')) {
	return new class () extends JAmpBaseInstallerScript implements InstallerScriptInterface {
	};
} else {
	class PlgsystemjampInstallerScript extends JAmpBaseInstallerScript {
	}
}