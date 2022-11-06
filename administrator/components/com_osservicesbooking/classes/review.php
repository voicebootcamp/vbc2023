<?php
/*------------------------------------------------------------------------
# review.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2019 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

/**
 * Enter description here...
 *
 */
class OSappscheduleReview
{
    /**
     * Default static function
     *
     * @param unknown_type $option
     */
    static function display($option, $task)
    {
        global $mainframe, $jinput;
        $mainframe = JFactory::getApplication();
        $cid = $jinput->get('cid', array(), 'ARRAY');
        \Joomla\Utilities\ArrayHelper::toInteger($cid, array(0));
        switch ($task) {
            default:
            case "review_list":
                OSappscheduleReview::review_list($option);
                break;
            case "review_unpublish":
                OSappscheduleReview::review_state($option, $cid, 0);
                break;
            case "review_publish":
                OSappscheduleReview::review_state($option, $cid, 1);
                break;
            case "review_remove":
                OSappscheduleReview::review_remove($option, $cid);
                break;
            case "review_add":
                OSappscheduleReview::review_modify($option, 0);
                break;
            case "review_edit":
                OSappscheduleReview::review_modify($option, $cid[0]);
                break;
            case "review_apply":
                OSappscheduleReview::review_save($option, 0);
                break;
            case "review_save":
                OSappscheduleReview::review_save($option, 1);
                break;
        }
    }

    /**
     * This static function is used to list all reviews
     * @param $option
     */
    static function review_list($option){
        global $configClass, $mainframe;
        $lists                      = array();
        $db                         = JFactory::getDbo();
        // filte sort
        $filter_order 				= $mainframe->getUserStateFromRequest($option.'.service.filter_order','filter_order','a.comment_date','string');
        $filter_order_Dir 			= $mainframe->getUserStateFromRequest($option.'.service.filter_order_Dir','filter_order_Dir','desc','string');
        $lists['order'] 			= $filter_order;
        $lists['order_Dir'] 		= $filter_order_Dir;
        $order_by 					= " ORDER BY $filter_order $filter_order_Dir";

        // Get the pagination request variables
        $limit						= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', 20, 'int' );
        $limitstart					= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

        // get data
        $count 						= "SELECT count(id) FROM #__app_sch_reviews WHERE 1=1";
        $db->setQuery($count);
        $total 						= $db->loadResult();
        jimport('joomla.html.pagination');
        $pageNav 					= new JPagination($total,$limitstart,$limit);

        $list  						= "SELECT a.*,b.username FROM #__app_sch_reviews as a inner join #__users as b on b.id = a.user_id"
                                    ."\n WHERE 1=1 ";
        $list 					   .= $order_by;
        $db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
        $rows 						= $db->loadObjectList();

        HTML_OSappscheduleReview::review_list($option,$rows,$pageNav,$lists);
    }

    static function review_modify($option,$id){
        global $mainframe,$configClass;
        $db = JFactory::getDbo();
        $row = &JTable::getInstance('Review','OsAppTable');
        if($id > 0){
            $row->load((int)$id);
            $seid = $row->sid."_".$row->eid;
        }else{
            $row->published = 1;
            $seid = "";
        }
        // creat published
        $lists['published'] = JHtml::_('select.booleanlist','published','class="inputbox"',$row->published);

        $optionArr = array();
        $optionArr[] = JHTML::_('select.option','1','1');
        $optionArr[] = JHTML::_('select.option','2','2');
        $optionArr[] = JHTML::_('select.option','3','3');
        $optionArr[] = JHTML::_('select.option','4','4');
        $optionArr[] = JHTML::_('select.option','5','5');
        $lists['rating'] = JHtml::_('select.genericlist',$optionArr,'rating','class="chosen input-mini"','value','text',$row->rating);


        $db->setQuery("Select id,service_name from #__app_sch_services where published = '1' order by ordering");
        $services = $db->loadObjectList();
        $db->setQuery("Select id, employee_name from #__app_sch_employee where published = '1'");
        $employees = $db->loadObjectList();

        $service_employee = "<select name='service_employee' id='service_employee' class='input-large chosen'>";
        $service_employee .= "<option value=''>".JText::_('OS_SELECT_OPTION')."</option>";
        foreach($services as $service){
            $service_employee .= '<optgroup label="'.$service->service_name.'">';
            foreach($employees as $employee) {
                $db->setQuery("Select count(id) from #__app_sch_employee_service where service_id = '$service->id' and employee_id = '$employee->id'");
                $count = $db->loadResult();
                if($count > 0){
                    if($seid == $service->id."_".$employee->id){
                        $checked = "checked";
                    }else{
                        $checked = "";
                    }
                    $service_employee .= '<option value="'.$service->id."_".$employee->id.'" '.$checked.'>'.$service->service_name."/ ".$employee->employee_name.'</option>';
                }
            }
            $service_employee .= '</optgroup>';
        }
        $lists['service'] = $service_employee;
        HTML_OSappscheduleReview::review_modify($row,$lists);
    }

    static function review_save($option, $save){
        global $mainframe,$jinput;
        $db = JFactory::getDbo();
        $id = $jinput->getInt('id',0);
        $post = $jinput->post->getArray();
        $row = &JTable::getInstance('Review','OsAppTable');
        $row->bind($post);

        $service_employee = $jinput->get('service_employee','','string');
        if($service_employee != ""){
            $service_employee_array = explode("_",$service_employee);
            $sid = $service_employee_array[0];
            $eid = $service_employee_array[1];
            $row->sid = $sid;
            $row->eid = $eid;
        }
        $created_on = $jinput->getString('comment_date','');
        if($created_on != ""){
            if($id == 0){
                $row->comment_date = $created_on;
            }
        }else{
            $row->comment_date = date("Y-m-d",time());
        }
        if($row->ip_address == ""){
            $row->ip_address = $_SERVER['REMOTE_ADDR'];
        }
        $row->check();
        $msg = JText::_('OS_ITEM_HAS_BEEN_SAVED');
        if (!$row->store()){
            $msg = JText::_('OS_ERROR_SAVING');
        }
        $mainframe->enqueueMessage($msg,'message');

        $approved = $jinput->getInt('approved',0);
        $published = $jinput->getInt('published',0);
        if(($approved == 0) and ($published == 1)){
            //send notification email to customer.
            $emailopt               = array();
            $emailopt['id']         = $row->id;
            $emailopt['author'] 	= $row->name;
            $emailopt['message']	= $row->comment_content;
            $emailopt['title'] 		= $row->comment_title;
            $emailopt['rate'] 		= $row->rate."/5";

            $db->setQuery("Select service_name from #__app_sch_services where id = '$row->sid'");
            $service_name = $db->loadResult();
            $emailopt['service']    = $service_name;
            $db->setQuery("Select employee_name from #__app_sch_employee where id = '$row->eid'");
            $employee_name = $db->loadResult();
            $emailopt['employee']   = $employee_name;
            self::sendCommentEmail($emailopt);

            $db->setQuery("Update #__app_sch_reviews set approved = '1' where id = '$row->id'");
            $db->execute();
        }

        if($save){
            OSappscheduleReview::review_list($option);
        }else{
            OSappscheduleReview::review_modify($option,$row->id);
        }
    }

    static function getUserInput($user_id)
    {
        if (version_compare(JVERSION, '3.5', 'le')){
            // Initialize variables.
            $html = array();
            $link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=user_id';
            // Initialize some field attributes.
            $attr = ' class="inputbox"';

            // Load the modal behavior script.
            JHtml::_('behavior.modal');
            JHtml::_('behavior.modal', 'a.modal_user_id');

            // Build the script.
            $script = array();
            $script[] = '	static function jSelectUser_user_id(id, title) {';
            $script[] = '		var old_id = document.getElementById("user_id").value;';
            $script[] = '		if (old_id != id) {';
            $script[] = '			document.getElementById("user_id").value = id;';
            $script[] = '			document.getElementById("user_id_name").value = title;';
            $script[] = '			var agent_name = document.getElementById("name");';
            $script[] = '			if(agent_name.value == ""){';
            $script[] = '				agent_name.value = title ;';
            $script[] = '			}';
            $script[] = '		}';
            $script[] = '		SqueezeBox.close();';
            $script[] = '	}';

            // Add the script to the document head.
            JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

            // Load the current username if available.
            $table = JTable::getInstance('user');

            if ($user_id)
            {
                $table->load($user_id);
            }
            else
            {
                $table->username = JText::_('OS_SELECT_USER');
            }

            // Create a dummy text field with the user name.
            $html[] = '<span class="input-append">';
            $html[] = '<input type="text" class="input-medium" id="user_id_name" value="'.htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') .'" disabled="disabled" size="35" /><a class="modal btn" title="'.JText::_('JLIB_FORM_CHANGE_USER').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JLIB_FORM_CHANGE_USER').'</a>';
            $html[] = '</span>';

            // Create the real field, hidden, that stored the user id.
            $html[] = '<input type="hidden" id="user_id" name="user_id" value="'.$user_id.'" />';

            return implode("\n", $html);
        }else{
            $field = JFormHelper::loadFieldType('User');
            $element = new SimpleXMLElement('<field />');
            $element->addAttribute('name', 'user_id');
            $element->addAttribute('class', 'readonly');
            $field->setup($element, $user_id);
            return $field->input;
        }
    }

    /**
     * remove comment
     *
     * @param unknown_type $option
     * @param unknown_type $cid
     */
    static function review_remove($option,$cid){
        global $jinput, $mainframe;
        $db = JFactory::getDBO();
        if(count($cid)>0)	{
            $cids = implode(",",$cid);
            $db->setQuery("DELETE FROM #__app_sch_reviews WHERE id IN ($cids)");
            $db->execute();
        }
        $mainframe->redirect("index.php?option=com_osservicesbooking&task=review_list");
    }

    /**
     * publish or unpublish comment
     *
     * @param unknown_type $option
     * @param unknown_type $cid
     * @param unknown_type $state
     */
    static function review_state($option,$cid,$state){
        global $jinput, $mainframe;
        $db = JFactory::getDBO();
        if(count($cid)>0)	{
            $cids = implode(",",$cid);
            $db->setQuery("UPDATE #__app_sch_reviews SET `published` = '$state' WHERE id IN ($cids)");
            $db->execute();

            if($state == 1){
                for($i=0;$i<count($cid);$i++){
                    $id = $cid[$i];
                    $db->setQuery("Select * from #__app_sch_reviews where id = '$id'");
                    $comment = $db->loadObject();
                    $alreadyPublished = $comment->approved;

                    if($alreadyPublished == 0){ //the first time publish the comment, send information email
                        //send email to property's onwer
                        $emailopt               = array();
                        $emailopt['id']         = $comment->id;
                        $emailopt['author'] 	= $comment->name;
                        $emailopt['message']	= $comment->comment_content;
                        $emailopt['title'] 		= $comment->comment_title;
                        $emailopt['rate'] 		= $comment->rate."/5";

                        $db->setQuery("Select service_name from #__app_sch_services where id = '$comment->sid'");
                        $service_name = $db->loadResult();
                        $emailopt['service']    = $service_name;
                        $db->setQuery("Select employee_name from #__app_sch_employee where id = '$comment->eid'");
                        $employee_name = $db->loadResult();
                        $emailopt['employee']   = $employee_name;
                        self::sendCommentEmail($emailopt);

                        $db->setQuery("Update #__app_sch_reviews set approved = '1' where id = '$comment->id'");
                        $db->execute();
                    }
                }
            }//end state 0/1
        }
		$mainframe->enqueueMessage(JText::_('OS_COMMENT_HAS_BEEN_UPDATED'));
        $mainframe->redirect("index.php?option=com_osservicesbooking&task=review_list");
    }

    static function sendCommentEmail($emailopt){
        global $jinput, $mainframe;

        $emailfrom = JFactory::getConfig()->get('mailfrom');
        $fromname  = JFactory::getConfig()->get('fromname');

        $db = JFactory::getDbo();
        $db->setQuery("Select * from #__app_sch_reviews where id = '".$emailopt['id']."'");
        $review = $db->loadObject();
        $user_id = $review->user_id;
        if($user_id > 0){
            $user = JFactory::getUser($user_id);
            $user_email = $user->email;
        }

        $db->setQuery("Select * from #__app_sch_emails where email_key like 'comment_approved'");
        $email = $db->loadObject();
        $subject = $email->email_subject;
        $message = $email->email_content;

        $message = str_replace("{username}",$emailopt['author'],$message);
        $message = str_replace("{service}",$emailopt['service'],$message);
        $message = str_replace("{employee}",$emailopt['employee'],$message);
        $message = str_replace("{created_date}",$review->comment_date,$message);
        $mailer = JFactory::getMailer();
		try
		{
			$mailer->sendMail($emailfrom,$fromname,$user_email,$subject,$message,1);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}
    }
}
?>