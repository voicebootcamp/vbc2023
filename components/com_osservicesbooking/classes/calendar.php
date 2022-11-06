<?php
/*------------------------------------------------------------------------
# calendar.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 Joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class OsAppscheduleCalendar
{
	/**
	 * Osproperty default
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
	{
		global $mainframe;
		$document = JFactory::getDocument();
		switch ($task)
		{
			case "calendar_employee":
				OsAppscheduleCalendar::employee($option);
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
			case "calendar_monthly":
				OsAppscheduleCalendar::monthlyCalendar();
			break;
		}
	}

	static function monthlyCalendar()
	{
		global $mainframe,$configClass,$jinput;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);

		$sid	= $jinput->get('sid',array(),'array');
		$eid	= $jinput->get('eid',array(),'array');

		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('OS_CALENDAR'));

		$query	= $db->getQuery(true);
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

	static function addcustombreaktime(){
        global $jinput;
        $db				= JFactory::getDbo();
        $eid			= $jinput->getInt('eid',0);
        $sid			= $jinput->getInt('sid',0);
        $bdate			= $jinput->get('bdate','','string');
        $bstart			= $jinput->get('bstart','','string');
        $bend			= $jinput->get('bend','','string');
		$employee_area	= $jinput->getInt('employee_area',0);
        $db->setQuery("Insert into #__app_sch_custom_breaktime (id,eid,sid,bdate,bstart,bend) values (NULL,'$eid','$sid','$bdate','$bstart','$bend')");
        $db->execute();

        self::getCustomBreakTime($eid, $sid, $employee_area);
        exit();
    }

    static function removecustombreaktime(){
        global $jinput;
        $db = JFactory::getDbo();
        $eid = $jinput->getInt('eid',0);
        $sid = $jinput->getInt('sid',0);
        $id = $jinput->getInt('id',0);
		$employee_area	= $jinput->getInt('employee_area',0);
        $db->setQuery("Delete from #__app_sch_custom_breaktime where id = '$id'");
        $db->execute();

        self::getCustomBreakTime($eid,$sid , $employee_area);
        exit();
    }
	
	static function customer($option){
		global $mainframe,$configClass;
		$user = JFactory::getUser();
		$db   = JFactory::getDbo();
		if(intval($user->id) == 0)
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root()."index.php");
		}
		HTML_OsAppscheduleCalendar::customerCalendar();
	}
	
	/**
	 * Working calendar of Employee
	 *
	 * @param unknown_type $option
	 */
	static function employee($option)
	{
		global $mainframe,$configClass;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$user = JFactory::getUser();
		$db   = JFactory::getDbo();
		if(intval($user->id) == 0)
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root()."index.php");
		}
		$db->setQuery("SELECT COUNT(id) FROM #__app_sch_employee WHERE user_id = '$user->id' AND published = '1'");
		$count  = $db->loadResult();
		if($count == 0)
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root()."index.php");
		}
		else
		{
			$db->setQuery("SELECT id FROM #__app_sch_employee WHERE user_id = '$user->id' AND published = '1'");
			$eid = $db->loadResult();
		}
		$employee = new stdClass();
		$employee = Jtable::getInstance('Employee','OsAppTable');
		if($eid > 0)
		{
			$employee->load((int)$eid);
			HTML_OsAppscheduleCalendar::employeeCalendar($employee);
		}
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
			$mainframe->redirect(JURI::root()."index.php");
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
			$mainframe->redirect(JURI::root()."index.php");
		}
		$eid = HelperOSappscheduleCommon::getEmployeeId();
		$db  = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee = $db->loadObject();

		HTML_OsAppscheduleCalendar::availabilityCalendar($employee);
		
	}

	static function getCustomBreakTime($eid, $sid = 0, $employee_area = 0)
	{
	    global $mapClass;
	    $db = JFactory::getDbo();
        $db->setQuery("Select a.*,b.service_name from #__app_sch_custom_breaktime as a inner join #__app_sch_services as b on a.sid = b.id where a.eid = '$eid' order by a.bdate,a.bstart,a.bend");
        $customs = $db->loadObjectList();

        //get list service of current employee
		if($sid == 0)
		{
			$query = "Select a.id as value, a.service_name as text from #__app_sch_services as a inner join #__app_sch_employee_service as b on a.id = b.service_id where b.employee_id = '$eid'";
			$db->setQuery($query);
			$services = $db->loadObjectList();
			$optionArr = array();
			$optionArr[] = JHtml::_('select.option','',JText::_('OS_SELECT_SERVICE'));
			$optionArr = array_merge($optionArr,$services);
			$lists['services'] = JHtml::_('select.genericlist',$optionArr,'sid','class="'.$mapClass['input-medium'].'"','value','text');
		}
		$lists['employee_area'] = $employee_area;
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
		$tmp	   = new \stdClass();
        $tmp->value = "";
        $tmp->text = "";
		$returnArr[0] = $tmp;
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
				$tmp	   = new \stdClass();
				$tmp->value = $time.":00";
				$tmp->text = $time.":00";
				$returnArr[$count] = $tmp;
            }
        }
        return $returnArr;
    }
}
?>