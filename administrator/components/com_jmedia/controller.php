<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\Registry\Registry;
/**
 * JMedia Manager Component Controller
 *
 * @since  1.5
 */

class JMediaController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   bool     $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return JMediaController This object to support chaining.
	 *
	 * @since   1.5
	 */
	
	public function display($cachable = false, $urlparams = false)
	{
		JPluginHelper::importPlugin('content');

		$vType    = JFactory::getDocument()->getType();
		$vName    = $this->input->get('view', 'media');

		// Get/Create the view
		$view = $this->getView($vName, $vType, '', array('base_path' => JPATH_COMPONENT_ADMINISTRATOR));


		// Process the content plugins.
		JPluginHelper::importPlugin('content');
        JFactory::getApplication()->triggerEvent('onJMediaDisplay', array ('com_jmedia.'.$vName));
		
		// Display the view
		$view->display();

		return $this;
	}

	/**
	 * Validate FTP credentials
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function ftpValidate()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
	}

	/**
	 * updateConfig
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function updateConfig()
	{
		// Check for request forgeries
        $this->checkToken('request');

        $form = JFactory::getApplication()->input->get('jform', '', 'array');
        $username = $form['username'];
        $license = $form['license'];

        // now update #__extensions
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__extensions')
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_jmedia'));
            
        $db->setQuery($query);
        $result = $db->loadObject();

        $registry = new Registry;
		$registry->loadString($result->params);
		$registry->set('username', $username);
		$registry->set('license', $license);
		$params = $registry->toString();
		
		$fields = array($db->quoteName('params') . '=' . $db->quote($params));
		$query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($fields)
            ->where($db->quoteName('extension_id').'='.$db->quote($result->extension_id));
        $db->setQuery($query);
        $db->execute();
        
        
        // now update #__update_sites
        $extra_query = 'username=' . urlencode($username);
        $extra_query .='&amp;key=' . urlencode($license);

        
        $fields = array(
            $db->quoteName('extra_query') . '=' . $db->quote($extra_query),
            $db->quoteName('last_check_timestamp') . '=0'
        );

        // __update_sites
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__update_sites'))
            ->set($fields)
            ->where($db->quoteName('name').'='.$db->quote('JMedia Update Site'));
        $db->setQuery($query);
        $db->execute();
        // __update_sites
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__update_sites'))
            ->set($fields)
            ->where($db->quoteName('name').'='.$db->quote('JMedia Pro Update Site'));
        $db->setQuery($query);
        $db->execute();
        

        echo new JResponseJson(true);
        jexit();

	}

	
}
