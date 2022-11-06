<?php
/*------------------------------------------------------------------------
# calendar.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class OsAppscheduleCalendar{
	/**
	 * Osproperty default
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
	{
		global $mainframe;
		$document = JFactory::getDocument();
		require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/class.calendar.php';
		switch ($task){
			case "calendar_employee":
				OsAppscheduleCalendar::monthlyCalendar($option);
			break;
			case "calendar_customer":
				OsAppscheduleCalendar::customer($option);
			break;
			case "calendar_dateinfo":
				OsAppscheduleCalendar::dateinfo($option);
			break;
			case "calendar_gcalendar":
				OsAppscheduleCalendar::gCalendar($option);
			break;
			case "calendar_availability":
				OsAppscheduleCalendar::availability($option);
			break;
            case "calendar_addcustombreaktime":
                OsAppscheduleCalendar::addcustombreaktime();
                break;
            case "calendar_removecustombreaktime":
                OsAppscheduleCalendar::removecustombreaktime();
                break;
			case "calendar_weekday":
				OsAppscheduleCalendar::weekday();
			break;
			case "calendar_loadWeekyCalendar":
				OsAppscheduleCalendar::loadWeekyCalendar();
			break;
			case "calendar_loadCalendatDetails":
				OsAppscheduleCalendar::loadCalendatDetails();
			break;
			case "calendar_updateNewOrderStatus":
				OsAppscheduleCalendar::updateNewOrderStatus();
			break;
		}
	}

	static function updateNewOrderStatus()
	{
		global $configClass,$jinput,$mapClass;
		$configClass = OSBHelper::loadConfig();
		require_once(JPATH_ROOT."/components/com_osservicesbooking/helpers/common.php");
		require_once(JPATH_ROOT."/components/com_osservicesbooking/classes/default.php");
		$db = Jfactory::getDbo();
		$id = $jinput->getInt('id',0);
		$db->setQuery("Select order_id from #__app_sch_order_items where id = '$id'");
		$order_id = $db->loadResult();
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		$row = $db->loadObject();
		$old_status = $row->order_status;
		if($jinput->get('new_status','P','string') != $old_status)
		{
			$db->setQuery("Update #__app_sch_orders set order_status = '".$jinput->get('new_status','P','string')."' where id = '$order_id'");
			$db->execute();

			if(($jinput->get('new_status','P','string') == "S") and ($old_status != "S"))
			{
				HelperOSappscheduleCommon::sendEmail("confirm",$order_id);
				HelperOSappscheduleCommon::sendEmployeeEmail('employee_notification_new',$order_id,0);
				HelperOSappscheduleCommon::sendSMS('confirm',$order_id);
                HelperOSappscheduleCommon::sendSMS('confirmtoEmployee',$order_id);
				OSBHelper::updateGoogleCalendar($order_id);
			}
			
			if(($jinput->get('new_status','P','string') == "C") and ($old_status != "C"))
			{
				HelperOSappscheduleCommon::sendCancelledEmail($order_id);
				HelperOSappscheduleCommon::sendSMS('cancel',$order_id);
				HelperOSappscheduleCommon::sendEmail('customer_cancel_order',$order_id);
				HelperOSappscheduleCommon::sendEmployeeEmail('employee_order_cancelled_new',$order_id,0);
				if($configClass['integrate_gcalendar'] == 1)
				{
					OSBHelper::removeEventOnGCalendar($order_id);
				}
				if($configClass['waiting_list'] == 1)
				{
					OSBHelper::sendWaitingNotification($order_id);
				}
				
			}
			
			//Send alert email
			if($old_status != $jinput->get('new_status','P','string') && $jinput->get('new_status','P','string') != "C")
			{
				HelperOSappscheduleCommon::sendEmail("order_status_changed_to_customer",$order_id);
				//HelperOSappscheduleCommon::sendEmployeeEmail('order_status_changed_to_employee',$order_id,0);
				HelperOSappscheduleCommon::sendSMS('order_status_changed_to_customer',$order_id);
			}
			//send thank you email when customer attended the service
			//echo $jinput->get('new_status','P','string');
			//echo "<BR />";
			//echo $old_status;
			//die();
            if($jinput->get('new_status','P','string') == "A" && $old_status != "A")
            {
                HelperOSappscheduleCommon::sendEmail('attended_thankyou_email',$order_id);
            }
		}

		$db->setQuery("Select a.*, c.service_name, d.employee_name, b.order_status, b.order_name, b.order_email, b.order_phone, b.user_id from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id inner join #__app_sch_services as c on c.id = a.sid inner join #__app_sch_employee as d on d.id = a.eid where a.id = '$id'");
		$item = $db->loadObject();

		if($item->order_status == 'P')
		{
			$eClass = 'pending';
		}
		elseif($item->order_status == 'S')
		{
			$eClass = 'complete';
		}
		elseif($item->order_status == 'A')
		{
			$eClass = 'attended';
		}
		
		$return = "";
		$return.= '<div class="'.$eClass.'"><span class="hasTip" title="'.HTML_OsAppscheduleCalendar::bookingItemInformation($item).'">';
		$return.= $item->service_name."(".$item->employee_name.")";
		$return.= " [".date($configClass['time_format'], $item->start_time)." - ".date($configClass['time_format'], $item->end_time)."]";
		$optionArr = array();
		$statusArr = array(JText::_('OS_PENDING'),JText::_('OS_COMPLETED'),JText::_('OS_ATTENDED'));
		$statusVarriableCode = array('P','S','A');
		for($j=0;$j<count($statusArr);$j++)
		{
			$optionArr[] = JHtml::_('select.option',$statusVarriableCode[$j],$statusArr[$j]);				
		}
		$return.= "</span>";
		$return.= JHtml::_('select.genericlist',$optionArr,'orderstatus'.$item->id,'class="'.$mapClass['input-small'].' form-select" ','value','text',$item->order_status);
		$return.= '<a href="javascript:updateOrderStatusCalendarAjax('.$item->id.');"><i class="icon-edit"></i></a></div>';

		echo $return;
		exit();

	}

	static function loadCalendatDetails()
	{
		global $mainframe,$mapClass,$configClass,$jinput;
        $configClass    = OSBHelper::loadConfig();
        $month          = $jinput->getInt('month',0);
        $year           = $jinput->getInt('year',0);
        HTML_OsAppscheduleCalendar::initCalendar($year,$month);
        exit();
	}
	/**
	 * Working calendar of Employee
	 *
	 * @param unknown_type $option
	 */
	static function monthlyCalendar($option){
		global $mainframe,$configClass,$jinput;
		require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/common.php';
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);

		$sid  = $jinput->get('sid',array(),'array');
		$eid  = $jinput->get('eid',array(),'array');

		$user = JFactory::getUser();
		$db   = JFactory::getDbo();

		$query= $db->getQuery(true);
		$query->select('a.id as value,a.service_name as text');
		$query->from($db->quoteName('#__app_sch_services').' AS a');
		$query->where("a.published = '1'");
		$query->order($db->escape('a.service_name'));
		$db->setQuery($query);
		//echo $db->getQuery();
		$services = $db->loadObjectList();
		$lists['sid'] = JHTML::_('select.genericlist',$services,'sid[]','class="input-large chosen" multiple','value','text',$sid);

		$query = $db->getQuery(true);
		$query->select('id as value, employee_name as text');
		$query->from('#__app_sch_employee');
		$query->where("published = '1'");
		if(count($sid) > 0)
		{
			$query->where("id in (Select employee_id from #__app_sch_employee_service where service_id in (".implode(',',$sid)."))");
		}
		$query->order('employee_name');
		$db->setQuery($query);
		$employees = $db->loadObjectList();
		$optionArr = array();
		$lists['emp'] = JHTML::_('select.genericlist',$employees,'eid[]','class="input-large chosen" multiple','value','text',$eid);

		$employee = Jtable::getInstance('Employee','OsAppTable');
		$employee->load((int)$eid);
		HTML_OsAppscheduleCalendar::monthlyCalendar($employee,$lists,$sid,$eid);
	}

	public static function weekday()
	{
		global $mainframe,$configClass,$jinput, $mapClass;
		require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/common.php';
		$cal	= new calendar();
		
		$m		= $jinput->getInt('m', date("m"));
		$y		= $jinput->getInt('y', date("Y"));
		$w		= $jinput->getInt('w', $cal->getWeek(date("d"), $m, $y));

		$cal->setWeek($w);

		$cal->setMonth($m);

		$cal->setYear($y);

		$eventDay = array();

		$day = array(JText::_('OS_SUN'),JText::_('OS_MON'),JText::_('OS_TUE'),JText::_('OS_WED'),JText::_('OS_THU'),JText::_('OS_FRI'),JText::_('OS_SAT'));
		$class = array('colDay1', 'colDay2', 'colDay3', 'colDay4', 'colDay5', 'colDay6', 'colDay7');
		$monthTitle = array(JText::_('OS_JANUARY'),JText::_('OS_FEBRUARY'),JText::_('OS_MARCH'),JText::_('OS_APRIL'),JText::_('OS_MAY'),JText::_('OS_JUNE'),JText::_('OS_JULY'),JText::_('OS_AUGUST'),JText::_('OS_SEPTEMBER'),JText::_('OS_OCTOBER'),JText::_('OS_NOVEMBER'),JText::_('OS_DECEMBER'));

		$cal->setShowColor(true);

		$cal->setTableWidth('100%');

		$cal->setTodayClass('today');

		$cal->setBlankClass('colBlank');

		$cal->setMonthTitle($monthTitle);

		$cal->setThaiYear(true);

		$cal->setDayOfWeekTitle($day);

		$cal->setColumnClass($class);

		$cal->setEventDay($eventDay);

		$sid		= $jinput->get('sid',array(),'array');
		$eid		= $jinput->get('eid',array(),'array');

		$document	= JFactory::getDocument();
		
		$db			= JFactory::getDbo();

		$query= $db->getQuery(true);
		$query->select('a.id as value,a.service_name as text');
		$query->from($db->quoteName('#__app_sch_services').' AS a');
		$query->where("a.published = '1'");
		$query->order($db->escape('a.service_name'));
		$db->setQuery($query);
		//echo $db->getQuery();
		$services = $db->loadObjectList();
		$lists['sid'] = JHTML::_('select.genericlist',$services,'sid[]','class="input-large chosen" multiple onChange="javascript:uploadeEmployees();"','value','text',$sid);

		$query = $db->getQuery(true);
		$query->select('id as value, employee_name as text');
		$query->from('#__app_sch_employee');
		$query->where("published = '1'");
		if(count($sid) > 0)
		{
			$query->where("id in (Select employee_id from #__app_sch_employee_service where service_id in (".implode(',',$sid)."))");
		}
		$query->order('employee_name');
		$db->setQuery($query);
		//echo $db->getQuery();
		$employees = $db->loadObjectList();
		$optionArr = array();
		$lists['emp'] = JHTML::_('select.genericlist',$employees,'eid[]','class="input-large chosen" multiple','value','text',$eid);

		//HTML_OsAppscheduleCalendar::weeklyCalendar($cal, $w, $m, $y, $lists);

		HTML_OsAppscheduleCalendar::weekdayCalendarHtml($cal, $w, $m, $y, $lists);
	}

	static function loadWeekyCalendar()
	{
		global $mainframe,$configClass,$jinput;
		$eidArr = [];
		$sidArr = [];
		$m		= $jinput->getInt('month', date("m"));
		$y		= $jinput->getInt('year', date("Y"));
		$d		= $jinput->getInt('day', date("Y"));
		$selected_sid = $jinput->getString('selected_sid','');
		if($selected_sid != "")
		{
			$sidArr = explode(",", $selected_sid);
		}

		$selected_eid = $jinput->getString('selected_eid','');
		if($selected_eid != "")
		{
			$eidArr = explode(",", $selected_eid);
		}

		$cal	= new calendar();
		$w		= $cal->getWeek($d, $m, $y);
		$cal->setWeek($w);

		$cal->setMonth($m);

		$cal->setYear($y);

		$eventDay = array();

		$day = array(JText::_('OS_SUN'),JText::_('OS_MON'),JText::_('OS_TUE'),JText::_('OS_WED'),JText::_('OS_THU'),JText::_('OS_FRI'),JText::_('OS_SAT'));
		$class = array('colDay1', 'colDay2', 'colDay3', 'colDay4', 'colDay5', 'colDay6', 'colDay7');
		$monthTitle = array(JText::_('OS_JANUARY'),JText::_('OS_FEBRUARY'),JText::_('OS_MARCH'),JText::_('OS_APRIL'),JText::_('OS_MAY'),JText::_('OS_JUNE'),JText::_('OS_JULY'),JText::_('OS_AUGUST'),JText::_('OS_SEPTEMBER'),JText::_('OS_OCTOBER'),JText::_('OS_NOVEMBER'),JText::_('OS_DECEMBER'));

		$cal->setShowColor(true);

		$cal->setTableWidth('100%');

		$cal->setTodayClass('today');

		$cal->setBlankClass('colBlank');

		$cal->setMonthTitle($monthTitle);

		$cal->setThaiYear(true);

		$cal->setDayOfWeekTitle($day);

		$cal->setColumnClass($class);

		$cal->setEventDay($eventDay);

		echo $cal->calendarWeek($w, $m, $y, $sidArr, $eidArr);
		exit();
	}

	static function listItemsByHour($hour, $day, $month, $year, $sidArr = array(), $eidArr = array())
	{
		//print_r($sidArr);
		global $configClass, $mapClass;
		$config		= new JConfig();
		$offset		= $config->offset;
		date_default_timezone_set($offset);

		$booking_date = $year."-".$month."-".$day;
		//echo $booking_date;
		//echo $year. "_". $booking_date;
		//echo "<BR />";
		$startHour	= strtotime($year.'-'.$month.'-'.$day.' '. $hour.':00:00');
		$endHour	= strtotime($year.'-'.$month.'-'.$day.' '. $hour.':59:00');

		//$booking_date = $year.'-'.$month.'-'.$day;
		//echo $booking_date;

		if(count($sidArr) > 0)
		{
			$serviceSql = " and a.sid in (".implode(",", $sidArr).")";
		}
		if(count($eidArr) > 0)
		{
			$employeeSql = " and a.eid in (".implode(",", $eidArr).")";
		}
		$db			= JFactory::getDbo();
		$db->setQuery("Select a.*, c.service_name, d.employee_name, b.order_status, b.order_name, b.order_email, b.order_phone, b.user_id from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id inner join #__app_sch_services as c on c.id = a.sid inner join #__app_sch_employee as d on d.id = a.eid where a.start_time >= '$startHour' and a.start_time <= '$endHour' and b.order_status in ('P','S','A') and booking_date = '$booking_date' ".$serviceSql.$employeeSql. " order by a.start_time");
		//echo $db->getQuery();
		$items = $db->loadObjectList();
		
		$return = "";
		if(count($items))
		{
			foreach($items as $item)
			{
				$eClass  = '';
				if($item->order_status == 'P')
				{
					$eClass = 'pending';
				}
				elseif($item->order_status == 'S')
				{
					$eClass = 'complete';
				}
				elseif($item->order_status == 'A')
				{
					$eClass = 'attended';
				}
				$return.= "<div class='itemBooking' id='orderitem_".$item->id."'><div class='".$eClass."'>";
				$return.= '<span class="hasTip" title="'.HTML_OsAppscheduleCalendar::bookingItemInformation($item).'">';
				$return.= $item->service_name."(".$item->employee_name.")";
				$return.= " [".date($configClass['time_format'], $item->start_time)." - ".date($configClass['time_format'], $item->end_time)."]";
				$optionArr = array();
				$statusArr = array(JText::_('OS_PENDING'),JText::_('OS_COMPLETED'),JText::_('OS_ATTENDED'));
				$statusVarriableCode = array('P','S','A');
				for($j=0;$j<count($statusArr);$j++)
				{
					$optionArr[] = JHtml::_('select.option',$statusVarriableCode[$j],$statusArr[$j]);				
				}
				$return.= "</span>";
				$return.= JHtml::_('select.genericlist',$optionArr,'orderstatus'.$item->id,'class="'.$mapClass['input-small'].' form-select" ','value','text',$item->order_status);
				$return.= '<a href="javascript:updateOrderStatusCalendarAjax('.$item->id.');"><i class="icon-edit"></i></a>';
				$return.= "</div></div>";
			}
		}
		return $return;
	}

	static function addcustombreaktime(){
        global $jinput;
        $db = JFactory::getDbo();
        $eid = $jinput->getInt('eid',0);
        $sid = $jinput->getInt('sid',0);
        $bdate = $jinput->get('bdate','','string');
        $bstart = $jinput->get('bstart','','string');
        $bend = $jinput->get('bend','','string');
        $db->setQuery("Insert into #__app_sch_custom_breaktime (id,eid,sid,bdate,bstart,bend) values (NULL,'$eid','$sid','$bdate','$bstart','$bend')");
        $db->execute();

        self::getCustomBreakTime($eid);
        exit();
    }

    static function removecustombreaktime(){
        global $jinput;
        $db = JFactory::getDbo();
        $eid = $jinput->getInt('eid',0);
        $sid = $jinput->getInt('sid',0);
        $id = $jinput->getInt('id',0);
        $db->setQuery("Delete from #__app_sch_custom_breaktime where id = '$id'");
        $db->execute();

        self::getCustomBreakTime($eid,$sid);
        exit();
    }
	
	static function customer($option){
		global $mainframe,$configClass;
		$user = JFactory::getUser();
		$db   = JFactory::getDbo();
		if(intval($user->id) == 0)
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root());
		}
		HTML_OsAppscheduleCalendar::customerCalendar();
	}
	
	
	static function dateinfo($option){
		global $mainframe,$configClass,$jinput;
		$db = JFactory::getDbo();
		$day = $jinput->get('date','','string');
		$user = JFactory::getUser();
		$db->setQuery("Select id from #__app_sch_employee where user_id = '$user->id'");
		$eid = $db->loadResult();
		if($eid > 0){
			$db->setQuery("SELECT a.*,c.service_name,b.order_name FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON b.id = a.order_id INNER JOIN #__app_sch_services AS c ON c.id = a.sid WHERE a.eid = '$eid' AND b.order_status IN ('P','S') AND a.booking_date = '$day'");
			$rows = $db->loadObjectList();
			HTML_OsAppscheduleCalendar::workinglistinOneDay($day,$rows);
		}
		exit();
	}
	
	/**
	 * Google Calendar
	 *
	 * @param unknown_type $option
	 */
	static function gCalendar($option){
		global $mainframe,$configClass;
		if(!HelperOSappscheduleCommon::checkEmployee())
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root());
		}
		$eid = HelperOSappscheduleCommon::getEmployeeId();
		$db  = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee = $db->loadObject();
		$config = new JConfig();
		$offset = $config->offset;
		if($employee->gcalendarid != ""){
		?>
		<table width="100%">
			<tr>
				<td width="50%" align="left">
					<div style="font-size:15px;font-weight:bold;">
						<?php echo JText::_('OS_MY_GCALENDAR');?>
					</div>
				</td>
				<td	width="50%" style="text-align:right;">
					<input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_BACK')?>" title="<?php echo JText::_('OS_GO_BACK')?>" onclick="javascript:history.go(-1);"/>
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="2">
					<iframe src="https://www.google.com/calendar/embed?src=<?php echo $employee->gcalendarid?>&ctz=<?php echo $offset;?>" style="border: 0" width="<?php echo $configClass['gcalendar_width']?>" height="<?php echo $configClass['gcalendar_height']?>" frameborder="0" scrolling="no"></iframe>
				</td>
			</tr>
		</table>
		<?php
		}
	}
	
	static function availability($option){
		global $mainframe,$configClass,$mapClass;
		if(!HelperOSappscheduleCommon::checkEmployee())
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root());
		}
		$eid = HelperOSappscheduleCommon::getEmployeeId();
		$db  = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee = $db->loadObject();

		HTML_OsAppscheduleCalendar::availabilityCalendar($employee);
		
	}

	static function getCustomBreakTime($eid){
	    global $mapClass;
	    $db = JFactory::getDbo();
        $db->setQuery("Select a.*,b.service_name from #__app_sch_custom_breaktime as a inner join #__app_sch_services as b on a.sid = b.id where a.eid = '$eid' order by a.bdate,a.bstart,a.bend");
        $customs = $db->loadObjectList();

        //get list service of current employee
        $query = "Select a.id as value, a.service_name as text from #__app_sch_services as a inner join #__app_sch_employee_service as b on a.id = b.service_id where b.employee_id = '$eid'";
        $db->setQuery($query);
        $services = $db->loadObjectList();
        $optionArr = array();
        $optionArr[] = JHtml::_('select.option','',JText::_('OS_SELECT_SERVICE'));
        $optionArr = array_merge($optionArr,$services);
        $lists['services'] = JHtml::_('select.genericlist',$optionArr,'sid','class="'.$mapClass['input-medium'].'"','value','text');
        HTML_OsAppscheduleCalendar::customBreaktime($customs,$lists);
    }


    /**
     * Generate hours
     *
     */
    static function generateHoursIncludeSecond(){
        $start = 0;
        $end = 23;
        $returnArr = array();
        $returnArr[0]->value = "";
        $returnArr[0]->text = "";
        for($i=$start;$i<=$end;$i++){
            for($j=0;$j<60;$j++){
                if($i<10){
                    $time = "0".$i;
                }else{
                    $time = $i;
                }
                $time .= ":";
                if($j<10){
                    $time .= "0".$j;
                }else{
                    $time .= $j;
                }
                $j += 14;

                $count = count($returnArr);
                $returnArr[$count]->value = $time.":00";
                $returnArr[$count]->text = $time.":00";
            }
        }
        return $returnArr;
    }
}
?>