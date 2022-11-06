<?php
/*------------------------------------------------------------------------
# logs.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class OSappscheduleLogs
{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
	{
		global $mainframe,$languages,$jinput;
		$mainframe = JFactory::getApplication();
        $id		   = $jinput->getInt('id', 0);
		switch ($task)
		{
			default:
			case "log_list":
				OSappscheduleLogs::log_list($option);
			break;
			case "log_details":
				OSappscheduleLogs::log_details($option, $id);
			break;
			case "log_gotolist":
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=log_list");
			break;
		}
	}

    
	/**
	 * Emails list
	 *
	 * @param unknown_type $option
	 */
	static function log_list($option)
	{
		global $mainframe, $jinput;
		$mainframe	= JFactory::getApplication();
		$config		= new JConfig();
		$list_limit	= $config->list_limit;
		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $list_limit, 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		$db			= JFactory::getDbo();
		$email_type = $jinput->getCmd('email_key','');
		$sql		= "";
		if($email_type != "")
		{
			$sql = " and email_key like ".$db->quote($email_type);
		}
		
		$db->setQuery("Select count(id) from #__app_sch_email_logs where 1=1 ".$sql);
		$count		= $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav 	= new JPagination($count,$limitstart,$limit);
		$db->setQuery("Select * from #__app_sch_email_logs where 1=1 ".$sql ." order by id desc",$pageNav->limitstart,$pageNav->limit);
		$rows		= $db->loadObjectList();

		$option		= [];
		$option[]	= JHtml::_('select.option','', JText::_('OS_EMAIL_KEY'));
		$db->setQuery("Select email_key as value, email_key as text from #__app_sch_emails");
		$emails		= $db->loadObjectList();
		$option		= array_merge($option, $emails);
		$lists['email_key'] = JHtml::_('select.genericlist', $option, 'email_key', 'class="input-medium form-control form-select" onChange="document.adminForm.submit();"','value', 'text', $email_type);

		HTML_OSappscheduleLogs::logsList($option,$rows, $pageNav, $lists);
	}
	
	/**
	 * Email modify
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function log_details($option,$id)
	{
		global $mainframe,$languages;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Log','OsAppTable');
		if($id > 0){
			$row->load((int)$id);
		}

		HTML_OSappscheduleLogs::logDetailsForm($option,$row);
	}
}
?>