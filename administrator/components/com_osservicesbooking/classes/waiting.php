<?php
/*------------------------------------------------------------------------
# worktime_custom.php - Ossolution Services Booking
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
class OSappscheduleWaiting{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$jinput,$configClass;
		if($configClass['waiting_list'] == 0)
		{
			$mainframe->enqueueMessage(JText::_('OS_THIS_FUNCTION_IS_BEING_DISABLED'));
			$mainframe->redirect('index.php?option=com_osservicesbooking');
		}
		$mainframe = JFactory::getApplication();
		
        $cid        = $jinput->get('cid',array(),'ARRAY');
        \Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		switch ($task){
			default:
			case "waiting_list":
				OSappscheduleWaiting::waiting_list($option);
			break;
			case "waiting_remove":
				OSappscheduleWaiting::waiting_remove($option,$cid);
			break;
		}
	}
	
	/**
	 * agent list
	 *
	 * @param unknown_type $option
	 */
	static function waiting_list($option){
		global $mainframe;
		$db							= JFactory::getDBO();
		$lists						= array();
		$condition					= '';
		
		// filte sort
		$filter_order 				= $mainframe->getUserStateFromRequest($option.'.waiting.filter_order','filter_order','start_time','string');
		$filter_order_Dir 			= $mainframe->getUserStateFromRequest($option.'.waiting.filter_order_Dir','filter_order_Dir','desc','string');
		$lists['order'] 			= $filter_order;
		$lists['order_Dir'] 		= $filter_order_Dir;
		$filter_service 		    = $mainframe->getUserStateFromRequest($option.'.waiting.filter_service','filter_service',0,'int');
		$filter_employee 			= $mainframe->getUserStateFromRequest($option.'.waiting.filter_employee','filter_employee',0,'int');
		$order_by 					= " ORDER BY $filter_order $filter_order_Dir";
		
		// Get the pagination request variables
		$limit						= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart					= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		// search
		// filter service
		$options 					= array();
		if ($filter_employee)
		{
			$query 					= " SELECT a.id AS value, a.service_name AS text"
                                    ." FROM #__app_sch_services AS a"
									." INNER JOIN #__app_sch_employee_service AS b ON (a.id = b.service_id AND b.employee_id = '$filter_employee')"					
                                    ." WHERE  a.published = '1' "
									." ORDER BY a.service_name, a.ordering";
		}
		else
		{
			$query 					= " SELECT `id` AS value, `service_name` AS text"
									." FROM #__app_sch_services"
                                    ." WHERE `published` = '1' "
									." ORDER BY service_name, ordering";
		}
		$db->setQuery($query);
		//echo $db->getQuery();die();
		$options					= $db->loadObjectlist();
		array_unshift($options,JHtml::_('select.option',0,JText::_('OS_FILTER_SERVICE')));
		$lists['filter_service']	= JHtml::_('select.genericlist',$options,'filter_service','class="input-medium" onchange="this.form.submit();" ','value','text',$filter_service);
		// filter employee
		$options 					= array();
			
		if ($filter_service)
		{
			$query 					= " SELECT a.id AS value, a.employee_name AS text"
									." FROM #__app_sch_employee AS a"
								    ." INNER JOIN #__app_sch_employee_service AS b ON (a.id = b.employee_id AND b.service_id = '$filter_service')"
																// ." WHERE a.published = '1' "
								    ." ORDER BY a.employee_name, b.ordering"
								    ;
		}
		else
		{
			$query 					= " SELECT `id` AS value, `employee_name` AS text"
									 ." FROM #__app_sch_employee "
									 // ." WHERE `published` = 1 "
									 ." ORDER BY employee_name "
									 ;
		}
		$db->setQuery($query);
		$options                    = $db->loadObjectlist();
		array_unshift($options,JHtml::_('select.option',0,JText::_('OS_FILTER_EMPLOYEE')));
		$lists['filter_employee']	= JHtml::_('select.genericlist',$options,'filter_employee','class="input-medium" onchange="this.form.submit();" ','value','text',$filter_employee);
		
		if($filter_service > 0)
		{
			$condition				.= " and a.sid = '$filter_service'";
		}
		if($filter_employee > 0)
		{
			$condition				.= " and a.eid = '$filter_employee'";
		}
		// get data	
		$count 						= "SELECT count(a.id) FROM #__app_sch_waiting_list as a inner join #__app_sch_services as b on a.sid = b.id inner join #__app_sch_employee as c on c.id = a.eid WHERE a.published = '0' ";
		$count 					   .= $condition;
		$db->setQuery($count);
		$total 						= $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav 					= new JPagination($total,$limitstart,$limit);
		
		$list  						= " SELECT a.*, b.service_name, c.employee_name FROM #__app_sch_waiting_list as a inner join #__app_sch_services as b on a.sid = b.id inner join #__app_sch_employee as c on c.id = a.eid"
										."\n WHERE a.published = '0' ";
		$list 					   .= $condition;
		$list 					   .= $order_by;
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		$rows 						= $db->loadObjectList();
		
		HTML_OSappscheduleWaiting::waiting_list($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * remove agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function waiting_remove($option,$cid){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		if(count($cid)>0){
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__app_sch_waiting_list WHERE id IN ($cids) ");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OSappscheduleWaiting::waiting_list($option);
	}
	
	/**
	 * Service modify
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function waiting_modify($option,$id){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('waiting','OsAppTable');
		if($id > 0){
			$row->load((int)$id);
			list($row->start_time_hour,$row->start_time_minutes) 	= explode(':',$row->start_time);
			list($row->end_time_hour,$row->end_time_minutes) 		= explode(':',$row->end_time);
		}else{
			$row->worktime_date			= null;
			$row->start_time_hour		= '00';
			$row->start_time_minutes	= '00';
			$row->end_time_hour			= '00';
			$row->end_time_minutes		= '00';
		}
		
		// start time
		$lists['start_time_hour'] 		= HelperDateTime::CreatDropHour('start_time_hour',(int)$row->start_time_hour,'class="input-mini"');
		$lists['start_time_minutes'] 	= HelperDateTime::CreatDropMinuste('start_time_minutes',(int)$row->start_time_minutes,'class="input-mini"');
		
		// end time
		$lists['end_time_hour'] 		= HelperDateTime::CreatDropHour('end_time_hour',(int)$row->end_time_hour,'class="input-mini"');
		$lists['end_time_minutes'] 		= HelperDateTime::CreatDropMinuste('end_time_minutes',(int)$row->end_time_minutes,'class="input-mini"');
		
		$db->setQuery("Select * from #__app_sch_services");
		$services = $db->loadObjectList();
			
		HTML_OSappscheduleWaiting::waiting_modify($option,$row,$lists,$services);
	}
	
	/**
	 * save service
	 *
	 * @param unknown_type $option
	 */
	static function waiting_save($option,$save){
		global $mainframe,$jinput;
		$db = JFactory::getDbo();
		$mainframe = JFactory::getApplication();
		$post 				= $jinput->post->getArray();//JRequest::get('post',JREQUEST_ALLOWHTML);
		$row 				= &JTable::getInstance('waiting','OsAppTable');
		$row->bind($post);
		$row->check();
		$row->start_time 	= $post['start_time_hour'].':'.$post['start_time_minutes'].':00';
		$row->end_time 		= $post['end_time_hour'].':'.$post['end_time_minutes'].':00';
		$row->is_day_off	= $jinput->getInt('is_day_off',0);
		$msg 				= JText::_('OS_ITEM_HAS_BEEN_SAVED'); 
	 	if (!$row->store())
		{
		 	$msg 			= JText::_('OS_ERROR_SAVING')." - ".$row->getError();
			throw new Exception($msg);
		}
		$mainframe->enqueueMessage($msg,'message');
		if($save)
		{
			OSappscheduleWaiting::waiting_list($option);
		}
		else
		{
			OSappscheduleWaiting::waiting_modify($option,$row->id);
		}
	}
}
?>