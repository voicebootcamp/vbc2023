<?php
/*------------------------------------------------------------------------
# emails.php - Ossolution emailss Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class OSappscheduleEmails{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$languages,$jinput;
		$mainframe = JFactory::getApplication();
        $cid        = $jinput->get('cid',array(),'ARRAY');
        \Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		switch ($task){
			default:
			case "emails_list":
				OSappscheduleEmails::emails_list($option);
			break;
			case "emails_edit":
				OSappscheduleEmails::email_modify($option,$cid[0]);
			break;
			case "emails_apply":
				OSappscheduleEmails::email_save($option,0);
			break;
			case "emails_save":
				OSappscheduleEmails::email_save($option,1);
			break;
            case "emails_unpublish":
                OSappscheduleEmails::email_state($option,$cid,0);
                break;
            case "emails_publish":
                OSappscheduleEmails::email_state($option,$cid,1);
                break;
			case "emails_gotolist":
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=emails_list");
			break;
		}
	}

    /**
     * publish or unpublish email
     *
     * @param unknown_type $option
     * @param unknown_type $cid
     * @param unknown_type $state
     */
    static function email_state($option,$cid,$state){
        global $mainframe;
        $db 		= JFactory::getDBO();
        if(count($cid)>0)	{
            $cids 	= implode(",",$cid);
            $db->setQuery("UPDATE #__app_sch_emails SET `published` = '$state' WHERE id IN ($cids)");
            $db->execute();
        }
        $mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
        OSappscheduleEmails::emails_list($option);
    }
	/**
	 * Emails list
	 *
	 * @param unknown_type $option
	 */
	static function emails_list($option){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_emails");
		$rows = $db->loadObjectList();
		HTML_OSappscheduleEmails::emailListForm($option,$rows);
	}
	
	/**
	 * Email modify
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function email_modify($option,$id){
		global $mainframe,$languages;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Emails','OsAppTable');
		if($id > 0){
			$row->load((int)$id);
		}
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		HTML_OSappscheduleEmails::editEmailTemplate($option,$row,$translatable);
	}
	
	/**
	 * Email save
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	static function email_save($option,$save){
		global $mainframe,$languages, $jinput;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Emails','OsAppTable');
		$post = $jinput->post->getArray();
		$row->bind($post);
		$email_content = $_POST['email_content'];
		$row->email_content = $email_content;
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language){												
				$sef = $language->sef;
				$email_subject_name 		= 'email_subject_'.$sef;
				$email_subject_value		= $jinput->get($email_subject_name,'','string');
				if($email_subject_value == ""){
					$email_subject_value = $row->email_subject;
				}
				$row->{$email_subject_name} = $email_subject_value;
				$email_content_name    		= 'email_content_'.$sef;
				$email_content_value   		= $_POST[$email_content_name];
				if($email_content_value == ""){
					$email_content_value = $row->email_content;
				}
				$row->{$email_content_name} = $email_content_value;
			}
		}
		$row->store();
		if($save == 1)
		{
			$mainframe->enqueueMessage("Email template has been saved");
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=emails_list");
		}
		else
		{
			$mainframe->enqueueMessage("Email template has been saved");
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=emails_edit&cid[]=$row->id");
		}
	}
}
?>