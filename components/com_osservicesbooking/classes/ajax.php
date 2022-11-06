<?php
/*------------------------------------------------------------------------
# ajax.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;


class OsAppscheduleAjax
{
	/**
	 * Ajax default static function
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task)
	{
		global $jinput;
		switch ($task)
		{
			case "ajax_checkcouponcode":
				OsAppscheduleAjax::checkCouponCode();
			break;
			case "ajax_addtocart":
				OsAppscheduleAjax::addToCart($option);
			break;
			case "ajax_reselect":
				OsAppscheduleAjax::reselect($option);
			break;
			case "ajax_showinfor":
				OsAppscheduleAjax::showInforForm($option);
			break;
			case "ajax_captcha":
				OsAppscheduleAjax::captcha($option);
			break;
			case "ajax_confirminfo":
				OsAppscheduleAjax::confirmInforForm($option);
			break;
			case "ajax_loadServices":
				$year 				= $jinput->getInt('year',0);
				$month 				= $jinput->getInt('month',0);
				$day 				= $jinput->getInt('day',0);
				$category_id		= $jinput->getInt('category_id',0);
				$employee_id  		= $jinput->getInt('employee_id',0);
				$vid				= $jinput->getInt('vid',0);
				$eid				= $jinput->getInt('eid',0);
				$sid				= $jinput->getInt('sid',0);
				$count_services		= $jinput->getInt('count_services',0);
				OsAppscheduleAjax::prepareLoadServices($option,$year,$month,$day,$category_id,$employee_id,$vid,$sid,$eid,$count_services);
			break;
			case "ajax_selectEmployee":
				OsAppscheduleAjax::selectEmployee($option);
			break;
			case "ajax_removeItem":
				OsAppscheduleAjax::removeItem($option);
			break;
			case "ajax_removeAllItem":
				OsAppscheduleAjax::removeAllItem($option);
			break;
			case "ajax_updatenslots":
				OsAppscheduleAjax::updatenSlots($option);
			break;
			case "ajax_removetemptimeslot":
				OsAppscheduleAjax::removeTemporarityTimeSlot($option);
			break;
			case "ajax_removerestdayAjax":
				OsAppscheduleAjax::removerestdayAjax();
			break;
			case "ajax_addrestdayAjax":
				OsAppscheduleAjax::addrestdayAjax();
			break;
			case "ajax_removeOrderItem":
				OsAppscheduleAjax::removeOrderItem();
			break;
			case "ajax_removeOrderItemAjax":
				OsAppscheduleAjax::removeOrderItemAjax();
			break;
			case "ajax_removeOrderItemAjaxCalendar":
				OsAppscheduleAjax::removeOrderItemAjaxCalendar();
			break;
			case "ajax_changeTimeSlotDate":
				OsAppscheduleAjax::changeTimeSlotDate();
			break;
            case "ajax_loadCalendatDetails":
                OsAppscheduleAjax::loadCalendatDetails();
            break;
			case "ajax_checkingVersion":
				OsAppscheduleAjax::checkingVersion();
			break;
            case "ajax_changeCheckinOrderItemAjax":
                OsAppscheduleAjax::changeCheckinOrderItem();
            break;
			case "ajax_getprofiledata":
				 OsAppscheduleAjax::getprofiledata();
			break;
			case "ajax_getprofileemployee":
				OsAppscheduleAjax::getprofileemployee();
			break;
			case "ajax_generateSearchmodule":
				OsAppscheduleAjax::generateSearchmodule();
			break;
			case "ajax_showCalendarView":
				global $jinput;
				$id			= $jinput->getInt('id',0);
				$vid		= $jinput->getInt('vid',0);
				$month		= $jinput->getInt('month',date("m",time()));
				$year		= $jinput->getInt('year',date("Y",time()));
				OsAppscheduleAjax::showCalendarView($id, $vid, $month, $year);
				exit();
			break;
			case "ajax_showEmployeeTimeslots":
				$sid		= $jinput->getInt('sid',0);
				$eid		= $jinput->getInt('eid',0);
				$date		= $jinput->getString('date','');
				$vid		= $jinput->getInt('venue_id',0);
				OsAppscheduleAjax::showEmployeeTimeslots($sid,$vid,$eid,$date);
			break;
			case "ajax_sendTestSMS":
				OsAppscheduleAjax::sendTestSMS();
			break;
		}
	}

	

	static function showEmployeeTimeslots($sid,$vid,$eid,$date)
	{
		global $mainframe,$mapClass,$configClass;
		$date						= explode("-",$date);
		$date1						= $date[2]."-".$date[1]."-".$date[0];
		$date						= explode("-",$date1);
		$db							= JFactory::getDbo();
		$config						= new JConfig();
		$offset						= $config->offset;
		date_default_timezone_set($offset);
		$session					= JFactory::getSession();
		$realtime					= HelperOSappscheduleCommon::getRealTime();

		$current_hour				= date("H",$realtime);
		$current_min				= date("i",$realtime);
		$realtime_this_day			= $current_hour*3600 + $current_min*60;
		$remain_time				= 24*3600 - $realtime_this_day;
		if($sid	> 0){
			$db->setQuery("Select service_name from #__app_sch_services where id = '$sid'");
			$service_name			= $db->loadResult();
		}
		if($eid	> 0){
			$db->setQuery("Select employee_name from #__app_sch_employee where id = '$eid'");
			$employee_name			= $db->loadResult();
		}
		if($vid > 0){
			$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
			$venue = $db->loadObject();
			$disable_booking_before		= $venue->disable_booking_before;
			$number_date_before			= $venue->number_date_before;
			$number_hour_before			= $venue->number_hour_before;
			$disable_date_before		= $venue->disable_date_before;
			if($disable_booking_before == 1){
				$disable_time			= strtotime(date("Y",$realtime)."-".date("m",$realtime)."-".date("d",$realtime)." 23:59:59");
			}elseif($disable_booking_before == 2){
				$disable_time			= $realtime + ($number_date_before-1)*24*3600 + $remain_time;
			}elseif($disable_booking_before  == 3){
				$disable_time			= strtotime($disable_date_before);
			}elseif($disable_booking_before == 4){
				$disable_time			= $realtime + $number_hour_before*3600;
			}
			$disable_booking_after		= $venue->disable_booking_after;
			$number_date_after			= $venue->number_date_after;
			$disable_date_after			= $venue->disable_date_after ;
			if($disable_booking_after == 2){
				$disable_time_after		= $realtime + $number_date_after*24*3600;
			}elseif($disable_booking_after  == 3){
				$disable_time_after		= strtotime($disable_date_after);
			}
		}else{
			$disable_booking_after		= 1;
			$disable_booking_before		= 1;
		}

		$dateformat = $date[2]."-".$date[1]."-".$date[0];
		if($configClass['multiple_work']  == 1){
			$db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' AND a.sid = '$sid' and a.booking_date = '$dateformat' AND b.order_status IN ('P','S')");
		}else{
			$db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' and a.booking_date = '$dateformat' AND b.order_status IN ('P','S')");
		}
		//echo $db->getQuery();
		$employees = $db->loadObjectList();
		$tempEmployee = array();
		if(count($employees) > 0){
			for($i=0;$i<count($employees);$i++){
				$employee = $employees[$i];
				$count = count($tempEmployee);
				$tmp							  = new stdClass();					
				$tmp->start_time				  = $employees[$i]->start_time;
				$tmp->end_time					  = $employees[$i]->end_time;
				$tmp->show					      = 1;
				$tempEmployee[$count]			  = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_custom_breaktime where sid = '$sid' and eid = '$eid' and bdate = '$dateformat'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count = count($tempEmployee);
				$tmp			 = new stdClass();		
				$tmp->start_time = strtotime($dateformat." ".$custom->bstart);
				$tmp->end_time   = strtotime($dateformat." ".$custom->bend);
				$tmp->show		 = 0;
				$tempEmployee[$count]			  = $tmp;
			}
		}

		//print_r($tempEmployee);
		$db->setQuery("Select * from #__app_sch_service_availability where sid = '$sid' and avail_date = '$dateformat'");
		$unavailable_values = $db->loadObjectList();
		if(count($unavailable_values) > 0)
		{
			for($i=0;$i<count($unavailable_values);$i++)
			{
				$employee = $unavailable_values[$i];
				$count = count($tempEmployee);
				$tmp			 = new stdClass();	
				$tmp->start_time = strtotime($dateformat." ".$employee->start_time);
				$tmp->end_time   = strtotime($dateformat." ".$employee->end_time);
				$tmp->show		  = 0;
				$tempEmployee[$count]			  = $tmp;
			}
		}

		//print_r($tempEmployee);
		//check unique_cookie
		$unique_cookie = $session->get('unique_cookie');
		$db->setQuery("SELECT COUNT(id) FROM #__app_sch_temp_orders WHERE unique_cookie LIKE '$unique_cookie'");
		$count = $db->loadResult();
		if($count > 0){
			$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie LIKE '$unique_cookie'");
			$order_id = $db->loadResult();
			$db->setQuery("SELECT * FROM #__app_sch_temp_order_items WHERE order_id = '$order_id' and sid  = '$sid' and eid  = '$eid' and booking_date = '$dateformat'");
			$temp_orders = $db->loadObjectList();
			if(count($temp_orders) > 0){
				for($i=0;$i<count($temp_orders);$i++){
					$item = $temp_orders[$i];
					$counttempEmployee = count($tempEmployee);
					$tmp			 = new stdClass();	
					$tmp->start_time = $item->start_time;
					$tmp->end_time	 = $item->end_time;
					$tmp->show		 = 1;
					$tempEmployee[$counttempEmployee] = $tmp;
				}
			}
		}

		$breakTime = array();
		$db->setQuery("Select * from #__app_sch_employee_service_breaktime where sid = '$sid' and eid = '$eid' and date_in_week = '".date("N",strtotime($dateformat))."'");
		$breaks = $db->loadObjectList();
		for($i=0;$i<count($breaks);$i++){
			$break_time_start = $dateformat." ".$breaks[$i]->break_from;
			$break_time_sint  = strtotime($break_time_start);
			$break_time_end   = $dateformat." ".$breaks[$i]->break_to;
			$break_time_eint  = strtotime($break_time_end);
			$count = count($tempEmployee);
			$tmp			  = new stdClass();	
			$tmp->start_time  = $break_time_sint;
			$tmp->end_time    = $break_time_eint;
			$tmp->show        = 0;

			$tempEmployee[$count] = $tmp;

			$count = count($breakTime);
			$tmp							  = new stdClass();	
			$tmp->start_time				  = $break_time_sint;
			$tmp->end_time					  = $break_time_eint;
			$breakTime[$count]				  = $tmp;

		}

		$db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
		$service = $db->loadObject();
		$service_length  = $service->service_total;
		$service_total   = $service->service_total;
		$service_total_int = $service_total*60;

		$time = HelperOSappscheduleCalendar::getAvailableTime('com_osservicesbooking',$date);
		$starttimetoday  = strtotime($date[2]."-".$date[1]."-".$date[0]." ".$time->start_time);
		$endtimetoday    = strtotime($date[2]."-".$date[1]."-".$date[0]." ".$time->end_time);
		$cannotbookstart = $endtimetoday - $service_total_int;

		$step_in_minutes = $service->step_in_minutes;
		if($step_in_minutes == 0){
			$amount	 = $configClass['step_format']*60;
		}elseif($step_in_minutes == 1){
			$amount  = $service_total_int;
		}else{
			$amount  = $step_in_minutes*60;
		}

		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employeeDetails = $db->loadObject();

		$db->setQuery("Select additional_price from #__app_sch_employee_service where employee_id = '$eid' and service_id = '$sid'");
		$additional_price = $db->loadResult();

		$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '".date("N",strtotime($dateformat))."' or week_date = '0')");
		$extras = $db->loadObjectList();
		$show_no_timeslot_text = 1;

		?>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<strong>
					<?php 
					echo JText::_('OS_SERVICE');
					echo " ";
					echo $service_name;
					echo " - ";
					echo $employee_name;
					?>
				</strong>
			</div>
		</div>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<?php
				if($configClass['booking_theme'] == 0){
				?>
					<div style="max-height:300px;overflow-y:scroll;">
				<?php
				}else{
				?>
					<div>
				<?php
				}
				$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
				$service_details = $db->loadObject();
				if($configClass['booking_more_than_one'] == 0){
					$ableBook = 1;
				}elseif(($configClass['booking_more_than_one'] == 1) and (!OSBHelper::isAreadyBooked($starttimetoday,0))) {
					$ableBook = 1;
				}else {
					$ableBook = 0;
				}
				if($ableBook == 1){
					if($service_details->service_time_type == 0){
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
					<?php
					$j = 0;
					for($inctime = $starttimetoday;$inctime<=$endtimetoday;$inctime = $inctime + $amount){
						$start_booking_time = $inctime;
						$end_booking_time	= $inctime + $service_length*60;
						//Modify on 1st May to add the start time from break time
						foreach ($breakTime as $break){
							if(($inctime >= $break->start_time) and ($inctime <= $break->end_time)){
								$inctime = $break->end_time;
								$start_booking_time = $inctime;
								$end_booking_time	= $inctime + $service_length*60;
							}
						}

						$arr1 = array();
						$arr2 = array();
						$arr3 = array();
						if(count($tempEmployee) > 0){
							for($i=0;$i<count($tempEmployee);$i++){
								$employee = $tempEmployee[$i];
								$before_service = $employee->start_time - $service->service_total*60;
								$after_service  = $employee->end_time + $service->service_total*60;
								if(($employee->start_time < $inctime) and ($inctime < $employee->end_time) and ($inctime + $service->service_total*60 == $employee->end_time)){
									//echo "1";

									$arr1[] = $inctime;
									$bgcolor = $configClass['timeslot_background'];
									$nolink = true;
								}elseif(($employee->start_time > $inctime) and ($employee->start_time < $end_booking_time)){

									//echo "4";
									$arr2[] = $inctime;
									$bgcolor = "gray";
									$nolink = true;
								}elseif(($employee->end_time > $inctime) and ($employee->end_time < $end_booking_time)){
									//echo "5";

									$arr2[] = $inctime;
									$bgcolor = "gray";
									$nolink = true;
								}elseif(($employee->start_time > $inctime) and ($employee->end_time < $end_booking_time)){

									//echo "6";
									$arr2[] = $inctime;
									$bgcolor = "gray";
									$nolink = true;
								}elseif(($employee->start_time < $inctime) and ($employee->end_time > $end_booking_time)){
									//echo "7";

									$arr2[] = $inctime;
									$bgcolor = "gray";
									$nolink = true;
								}elseif(($employee->start_time == $inctime) or ($employee->end_time == $end_booking_time)){
									//echo "7";

									$arr2[] = $inctime;
									$bgcolor = "gray";
									$nolink = true;
								}else{
									//echo "8";
									$arr3[] = $inctime;
									$bgcolor = $configClass['timeslot_background'];
									$nolink = false;
								}
							}
						}else{
							$arr3[] = $inctime;
							$bgcolor = $configClass['timeslot_background'];
							$nolink = false;
						}

						//echo $bgcolor;
						$gray =  0;
						if($inctime + $service->service_total*60 > $endtimetoday){
							$bgcolor = "gray";
							$nolink  = true;
							$gray = 1;
						}
						if(($date[2] == date("Y",$realtime) and ($date[1] == intval(date("m",$realtime))) and ($date[0] == intval(date("d",$realtime))))){
							if($inctime <= $realtime){
								$bgcolor = "gray";
								$nolink  = true;
								$gray = 1;
							}
						}

						if($gray == 0){

							if(in_array($inctime,$arr2)){
								$bgcolor = "gray";
								$nolink = true;
							}elseif(in_array($inctime,$arr1)){
								$bgcolor = $configClass['timeslot_background'];
								$nolink = true;
							}else{
								$bgcolor = $configClass['timeslot_background'];
								$nolink = false;
							}
						}elseif($gray == 1){
							$bgcolor = "gray";
							$nolink  = true;
						}

						if($configClass['multiple_work'] == 0){
							if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_booking_time,$end_booking_time)){
								$bgcolor = "gray";
								$nolink  = true;
							}
							if(!OSBHelper::checkMultipleEmployeesInTempOrderTable($sid,$eid,$start_booking_time,$end_booking_time)){
								$bgcolor = "gray";
								$nolink  = true;
							}
						}

						if($configClass['disable_timeslot'] == 1){
							if(!OSBHelper::checkMultipleServices($sid,$eid,$start_booking_time,$end_booking_time)){
								$bgcolor = "gray";
								$nolink  = true;
							}
							if(!OSBHelper::checkMultipleServicesInTempOrderTable($sid,$eid,$start_booking_time,$end_booking_time)){
								$bgcolor = "gray";
								$nolink  = true;
							}
						}

						if($disable_booking_before >= 1){
							if($inctime < $disable_time){
								$bgcolor = "gray";
								$nolink  = true;
							}
						}
						if($disable_booking_after > 1){
							if($inctime > $disable_time_after){
								$bgcolor = "gray";
								$nolink  = true;
							}
						}

						if ($configClass['booking_theme'] == 0) {
							if((($nolink) and (($configClass['show_occupied'] == 1)) or (!$nolink)) and ($end_booking_time <= $endtimetoday)) {
								$j++;
								$show_no_timeslot_text = 0;
								?>
								<div class="<?php echo $mapClass['span6'];?> timeslots divtimeslots" style="background-color:<?php echo $bgcolor?> !important;">
									<label for="<?php echo $eid?>_<?php echo $inctime?>" class="timeslotlabel">
										<?php
											echo date($configClass['time_format'], $inctime);
										?>
									</label>
								</div>
								<?php
								}
								if($j==2){
									?>
									</div><div class="<?php echo $mapClass['row-fluid'];?>">
									<?php
									$j = 0;
								}
							}else{ //simple layout
								if((($nolink) and (($configClass['show_occupied'] == 1)) or (!$nolink)) and ($end_booking_time <= $endtimetoday)) {
									$j++;
									
									if (!$nolink) {
										$show_no_timeslot_text = 0;
										?>
										<div class="divtimeslots_simple"
											 id="timeslot<?php echo $sid ?>_<?php echo $eid ?>_<?php echo $inctime ?>"
											 style="background-color:<?php echo $bgcolor ?> !important;">
												<?php
												echo date($configClass['time_format'], $inctime);
												?>
										</div>
									<?php
									}
									if($j==2){ $j = 0; }
								}
							}
						}

					if($j == 1){
						?>
						</div>
						<?php
					}
					if($j==0){
						?>
						</div>
						<?php
					}
				}
				else
				{
					$dateformat_int = strtotime($dateformat);
					$date_in_week = date("N",$dateformat_int);
					$db->setQuery("Select * from #__app_sch_custom_time_slots where sid = '$sid' and id in (Select time_slot_id from #__app_sch_custom_time_slots_relation where date_in_week = '$date_in_week') order by start_hour,start_min");
					$rows = $db->loadObjectList();

					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<?php
						$j = 0;
						for($i=0;$i<count($rows);$i++){
							$bgcolor = "";
							$row = $rows[$i];

							$start_hour = $row->start_hour;
							if($start_hour < 10){
								$start_hour = "0".$start_hour;
							}
							$start_min = $row->start_min;
							if($start_min < 10){
								$start_min = "0".$start_min;
							}

							$start_time = $date[2]."-".$date[1]."-".$date[0]." ".$start_hour.":".$start_min.":00";
							//echo $start_time;
							$start_time_int = strtotime($start_time);

							$end_hour = $row->end_hour;
							if($end_hour < 10){
								$end_hour = "0".$end_hour;
							}
							$end_min = $row->end_min;
							if($end_min < 10){
								$end_min = "0".$end_min;
							}

							$end_time = $date[2]."-".$date[1]."-".$date[0]." ".$end_hour.":".$end_min.":00";
							$end_time_int = strtotime($end_time);

							$db->setQuery("Select SUM(a.nslots) as nslots from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status in ('P','S') and a.start_time =  '$start_time_int' and a.end_time = '$end_time_int' and a.sid = '$sid' and a.eid = '$eid'");
							//$count = $db->loadResult();
							$nslotsbooked = $db->loadObject();
							$count = intval($nslotsbooked->nslots);
							$temp_start_hour = $row->start_hour;
							$temp_start_min  = $row->start_min;
							$temp_end_hour 	 = $row->end_hour;
							$temp_end_min    = $row->end_min;

							$db->setQuery("Select nslots from #__app_sch_custom_time_slots where sid = '$service->id' and start_hour = '$temp_start_hour' and start_min = '$temp_start_min' and end_hour = '$temp_end_hour' and end_min = '$temp_end_min' and id in (Select time_slot_id from #__app_sch_custom_time_slots_relation where date_in_week = '$date_in_week')");
							//echo $db->getQuery();
							$nslots = $db->loadResult();

							//get the number count of the cookie table
							$query = "SELECT SUM(a.nslots) as bnslots FROM #__app_sch_temp_order_items AS a INNER JOIN #__app_sch_temp_orders AS b ON a.order_id = b.id WHERE a.sid = '$sid' AND a.eid = '$eid' AND a.start_time =  '$start_time_int' and a.end_time = '$end_time_int'";
							$db->setQuery($query);
							$bslots = $db->loadObject();
							$count_book = $bslots->bnslots;
							$avail = $nslots - $count - $count_book;
							if($avail <= 0){
								$bgcolor = $configClass['timeslot_background'];
								$nolink = true;
							}else{
								$bgcolor = $configClass['timeslot_background'];
								$nolink = false;
							}

							if(($date[2] == date("Y",$realtime) and ($date[1] == intval(date("m",$realtime))) and ($date[0] == intval(date("d",$realtime))))){
								//today
								if($start_time_int <= $realtime){
									$bgcolor = "gray";
									$nolink  = true;
								}
							}

							if($disable_booking_before > 1){
								if($start_time_int < $disable_time){
									$bgcolor = "gray";
									$nolink  = true;
								}
							}
							if($disable_booking_after > 1){
								if($start_time_int > $disable_time_after){
									$bgcolor = "gray";
									$nolink  = true;
								}
							}

							if($configClass['multiple_work'] == 0){
								if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_time_int,$end_time_int)){
									$bgcolor = "gray";
									$nolink  = true;
								}
								if(!OSBHelper::checkMultipleEmployeesInTempOrderTable($sid,$eid,$start_time_int,$end_time_int)){
									$bgcolor = "gray";
									$nolink  = true;
								}
							}

							if($configClass['disable_timeslot'] == 0){
								if(!OSBHelper::checkMultipleServices($sid,$eid,$start_time_int,$end_time_int)){
									$bgcolor = "gray";
									$nolink  = true;
								}
								if(!OSBHelper::checkMultipleServicesInTempOrderTable($sid,$eid,$start_time_int,$end_time_int)){
									$bgcolor = "gray";
									$nolink  = true;
								}
							}

							if(count($tempEmployee) > 0){
								for($k=0;$k<count($tempEmployee);$k++){
									$employee = $tempEmployee[$k];
									$before_service = $employee->start_time;
									$after_service  = $employee->end_time;
									if(($employee->start_time < $start_time_int) and ($end_time_int < $employee->end_time)){
										//echo "1";
										if(($avail <= 0) or ($employee->show == 0)){
											$bgcolor = "gray";
											$nolink = true;
										}
									}elseif(($employee->start_time > $start_time_int) and ($employee->start_time < $end_time_int)){
										//echo "2";
										if(($avail <= 0) or ($employee->show == 0)){
											$bgcolor = "gray";
											$nolink = true;
										}
									}elseif(($employee->end_time > $start_time_int) and ($employee->end_time < $end_time_int)){
										//echo "3";
										if(($avail <= 0) or ($employee->show == 0)){
											$bgcolor = "gray";
											$nolink = true;
										}
									}elseif(($employee->start_time <= $start_time_int) and ($employee->end_time >= $end_time_int)){
										if(($avail <= 0) or ($employee->show == 0)){
											$bgcolor = "gray";
											$nolink = true;
										}
									}elseif($end_time_int <= $employee->start_time){
										if($bgcolor != "gray"){
											$bgcolor = $configClass['timeslot_background'];
											$nolink = false;
										}
									}else{
										if($bgcolor != "gray"){
											$bgcolor = $configClass['timeslot_background'];
											$nolink = false;
										}
									}
								}
							}
							if($disable_booking_before > 1){
								if($start_time_int < $disable_time){
									$bgcolor = "gray";
									$nolink  = true;
								}
							}
							if($disable_booking_after > 1){
								if($start_time_int > $disable_time_after){
									$bgcolor = "gray";
									$nolink  = true;
								}
							}

							if($avail <= 0){
								$bgcolor = $configClass['timeslot_background'];
								$nolink = true;
							}

							if ($configClass['booking_theme'] == 0) {
								if((($nolink) and (($configClass['show_occupied'] == 1)) or (!$nolink))) {
									if (($end_time_int <= $endtimetoday) and ($start_time_int >= $starttimetoday)) {
										$j++;
										$show_no_timeslot_text = 0;
										?>
										<div class="<?php echo $mapClass['span6'];?> timeslots divtimeslots"
											 style="background-color:<?php echo $bgcolor?> !important;">
											
											&nbsp;&nbsp;

											<label for="<?php echo $eid?>_<?php echo $start_time_int;?>" class="timeslotlabel">
											<?php
											echo date($configClass['time_format'], strtotime(date("Y-m-d", $start_time_int) . " " . $start_hour . ":" . $start_min . ":00"));
											?>
											&nbsp;-&nbsp;
											<?php
											$end_hour = $row->end_hour;
											if ($end_hour < 10) {
												$end_hour = "0" . $end_hour;
											}
											$end_min = $row->end_min;
											if ($end_min < 10) {
												$end_min = "0" . $end_min;
											}
											echo date($configClass['time_format'], strtotime(date("Y-m-d", $start_time_int) . " " . $end_hour . ":" . $end_min . ":00"));
											?>
											</label>
											&nbsp;-&nbsp;
											<?php
											echo JText::_('OS_AVAIL') . ": ";
											echo $avail;
											?>
										</div>
									<?php
									}
									if ($j == 2) {
										$j = 0;
										?>
										</div><div class="<?php echo $mapClass['row-fluid'];?>">
									<?php
									}
								}
							}else{
								if((($nolink) and (($configClass['show_occupied'] == 1)) or (!$nolink))){
									if(($end_time_int <= $endtimetoday) and ($start_time_int >= $starttimetoday)){
										$j++;
										
										if(($avail > 0) && (!$nolink)){

										$show_no_timeslot_text = 0;
										?>
											<div class="divtimecustomslots_simple" style="background-color:<?php echo $bgcolor?> !important;" id="ctimeslots<?php echo $sid ?>_e<?php echo $eid ?>_<?php echo $start_time_int?>" >
										<?php
										}else{
										?>
											<div class="divtimecustomslots_simple" style="background-color:red !important;">
										<?php
										}
											$start_hour = $row->start_hour;
											if($start_hour < 10){
												$start_hour = "0".$start_hour;
											}
											$start_min = $row->start_min;
											if($start_min < 10){
												$start_min = "0".$start_min;
											}
											echo date($configClass['time_format'],strtotime(date("Y-m-d",$start_time_int)." ".$start_hour.":".$start_min.":00"));
											?>
											-
											<?php
											$end_hour = $row->end_hour;
											if($end_hour < 10){
												$end_hour = "0".$end_hour;
											}
											$end_min = $row->end_min;
											if($end_min < 10){
												$end_min = "0".$end_min;
											}
											echo date($configClass['time_format'],strtotime(date("Y-m-d",$start_time_int)." ".$end_hour.":".$end_min.":00"));

											echo JText::_('OS_AVAIL').": ";
											echo $avail;
											?>
										</div>
									<?php
									}
								}
							}
						}
						echo "</div>";
					}
				}
				if($show_no_timeslot_text == 1){
					?>
					<div class="no_available_time_slot">
						<?php echo JText::_('OS_NO_AVAILABLE_TIME_SLOTS');?>
					</div>
					<?php 
				}else{
					?>
					<div class="book_employee_link">
						<?php
						if(!OSBHelper::isJoomla4())
						{
						?>
							<a href="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=default_layout&sid='.$sid.'&employee_id='.$eid.'&date_from='.$dateformat.'&date_to='.$dateformat);?>" title="<?php echo sprintf(JText::_('OS_CLICK_HERE_TO_BOOK_EMPLOYEE_ON_DATE'),$employee_name,$dateformat);?>"><?php echo sprintf(JText::_('OS_CLICK_HERE_TO_BOOK_EMPLOYEE_ON_DATE'),$employee_name,$dateformat);?></a>
						<?php
						}
						else
						{
							$link = JRoute::_('index.php?option=com_osservicesbooking&task=default_layout&sid='.$sid.'&employee_id='.$eid.'&date_from='.$dateformat.'&date_to='.$dateformat);

						?>
							<a href="javascript:openLink('<?php echo $link;?>');" title="<?php echo sprintf(JText::_('OS_CLICK_HERE_TO_BOOK_EMPLOYEE_ON_DATE'),$employee_name,$dateformat);?>" id="bookingLink"><?php echo sprintf(JText::_('OS_CLICK_HERE_TO_BOOK_EMPLOYEE_ON_DATE'),$employee_name,$dateformat);?></a>
						<?php
						}
						?>
					</div>
					<?php
				}
				?>
				
				<input type="hidden" name="book_<?php echo $sid?>_<?php echo $eid?>" id="book_<?php echo $sid?>_<?php echo $eid?>" value="" />
				<input type="hidden" name="end_book_<?php echo $sid?>_<?php echo $eid?>" id="end_book_<?php echo $sid?>_<?php echo $eid?>" value="" />
				<input type="hidden" name="start_<?php echo $sid?>_<?php echo $eid?>" id="start_<?php echo $sid?>_<?php echo $eid?>" value="" /> 
				<input type="hidden" name="end_<?php echo $sid?>_<?php echo $eid?>" id="end_<?php echo $sid?>_<?php echo $eid?>" value="" />
				</div>
			</div>
		</div>
		<script type="text/javascript">
		function openLink(link)
		{
			//jQuery.colorbox.close();
			window.parent.location.href = link;
			//alert(link);
		}
		</script>
		<?php

	}
	
	/**
	 * Load Services information
	 *
	 * @param unknown_type $option
	 * @param unknown_type $year
	 * @param unknown_type $month
	 * @param unknown_type $day
	 * @param unknown_type $category_id
	 * @param unknown_type $employee_id
	 * @param unknown_type $vid
	 */
	static function prepareLoadServices($option,$year,$month,$day,$category_id,$employee_id,$vid,$sid,$eid,$count_services=0){
		$db 			= JFactory::getDbo();
		if($category_id > 0){
			$catSql = " and category_id = '$category_id' ";
		}else{
			$catSql =  "";
		}
		if($employee_id > 0){
			$employeeSql = " and id in (Select service_id from #__app_sch_employee_service where employee_id = '$employee_id')";
		}else{
			$employeeSql = "";
		}
		if(($sid > 0) and ($count_services == 1)){
			$sidSql = " and id = '$sid'";
		}else{
			$sidSql = "";
		}

		if($vid > 0){
			$vidSql = " and id in (Select sid from #__app_sch_venue_services where vid = '$vid')";
		}else{
			$vidSql = "";
		}
		$current_day 		= date("Y-m-d",HelperOSappscheduleCommon::getRealTime());
		$current_day_int 	= strtotime($current_day);
		
		$temp_day 			= $year."-".$month."-".$day;
		$temp_day_int    	= strtotime($temp_day);
		
		if($temp_day_int < $current_day_int){
			//return nothing
			$services = array();
		}else{
			$db->setQuery("Select * from #__app_sch_services where published = '1' $catSql $employeeSql $vidSql $sidSql ".HelperOSappscheduleCommon::returnAccessSql('')." order by ordering");
			$services = $db->loadObjectList();
		}
		OsAppscheduleAjax::loadServices($option,$services,$year,$month,$day,$category_id,$employee_id,$vid,$sid,$eid);

		echo "*4444*";
		echo HelperOSappscheduleCommon::getServicesAndEmployees($services,$year,$month,$day,$category_id,$employee_id,$vid,$sid,$employee_id);
		exit();
	}

	/**
	 * Load Services information
	 *
	 * @param unknown_type $option
	 * @param unknown_type $year
	 * @param unknown_type $month
	 * @param unknown_type $day
	 * @param unknown_type $category_id
	 * @param unknown_type $employee_id
	 * @param unknown_type $vid
	 */
	static function prepareLoadServicesObjects($option,$year,$month,$day,$category_id,$employee_id,$vid,$sid,$eid,$count_services=0){
		global $mainframe;
		$db 				= JFactory::getDbo();
		if($category_id > 0){
			$catSql = " and category_id = '$category_id' ";
		}else{
			$catSql =  "";
		}
		if($employee_id > 0){
			$employeeSql = " and id in (Select service_id from #__app_sch_employee_service where employee_id = '$employee_id')";
		}else{
			$employeeSql = "";
		}
		if(($sid > 0) and ($count_services == 1)){
			$sidSql = " and id = '$sid'";
		}else{
			$sidSql = "";
		}

		if($vid > 0){
			$vidSql = " and id in (Select sid from #__app_sch_venue_services where vid = '$vid')";
		}else{
			$vidSql = "";
		}
		$current_day 		= date("Y-m-d",HelperOSappscheduleCommon::getRealTime());
		$current_day_int 	= strtotime($current_day);
		
		$temp_day 			= $year."-".$month."-".$day;
		$temp_day_int    	= strtotime($temp_day);
		
		if($temp_day_int < $current_day_int){
			//return nothing
			$services = array();
		}else{
			$db->setQuery("Select * from #__app_sch_services where published = '1' $catSql $employeeSql $vidSql $sidSql ".HelperOSappscheduleCommon::returnAccessSql('')." order by ordering");
			$services = $db->loadObjectList();
		}
		return $services;
	}
	
	static function loadServices($option,$services,$year,$month,$day,$category_id,$employee_id,$vid,$sid,$eid)
	{
		global $mainframe,$mapClass,$configClass;
		$session        = JFactory::getSession();
		if(($year != '') && ($month != '') && ($day != ''))
		{
            $selected_date  = $year."-".$month."-".$day;
            $session->set('selected_date',$selected_date);
        }
        else
        {
		    $session->set('selected_date','');
        }

		$reason         = "";
		$unique_cookie  = OSBHelper::getUniqueCookie();// $_COOKIE['unique_cookie'];
		$db = JFactory::getDbo();
		//$db->setQuery("DELETE FROM #__app_sch_temp_temp_order_items WHERE unique_cookie LIKE '$unique_cookie'");
		//$db->execute();
		HelperOSappscheduleCommon::removeTempSlots();
		//check to see if this day is off
		if(intval($month) < 10)
		{
			$month		= "0".intval($month);
		}
		if(intval($day) < 10)
		{
			$day		= "0".intval($day);
		}
		$date = $year."-".$month."-".$day;
		$date_int = strtotime($date);
		$date_we  = date("N",$date_int);
		$db = JFactory::getDbo();
		$db->setQuery("Select `is_day_off` from #__app_sch_working_time where id = '$date_we'");
		$is_day_off = $db->loadResult();
		if($is_day_off == 0)
		{
			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (`worktime_date` <= '$date' and `worktime_date_to` >= '$date')");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select * from #__app_sch_working_time_custom where (`worktime_date` <= '$date' and `worktime_date_to` >= '$date')");
				$v = $db->loadObject();
				$is_day_off = (int)$v->is_day_off;
				$reason = $v->reason;
			}
		}
		else
		{
			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (`worktime_date` <= '$date' and `worktime_date_to` >= '$date')");
			$count = $db->loadResult();
			if($count > 0){
				$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (`worktime_date` <= '$date' and `worktime_date_to` >= '$date') and `is_day_off` = 1");
				$count = $db->loadResult();
				if($count > 0)
				{
					$is_day_off = 1;
				}
				else
				{
					$is_day_off = 0;
				}
			}
			else
			{
				$is_day_off = 1;
			}
		}
		HTML_OsAppscheduleAjax::loadServicesHTML($option,$services,$year,$month,$day,$is_day_off,$category_id,$employee_id,$vid,$sid,$eid,$reason);
	}
	
	
	static function selectEmployee($option)
	{
		global $mainframe,$mapClass,$jinput;
		$sid = $jinput->getInt('sid',0);
		$year = $jinput->getInt('year');
		$month = $jinput->getInt('month');
		$day = $jinput->getInt('day');
		$date[0] = $day;
		$date[1] = $month;
		$date[2] = $year;
		OsAppscheduleAjax::loadEmployees($option,$sid,$date,0,0,0);
		echo  "@@@@";
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service = $db->loadObject();
		if($day < 10){
			$day = "0".$day;
		}
		if($month < 10){
			$month= "0".$month;
		}
		$current_date = $year."-".$month."-".$day;
		$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where a.sid = '$sid' and b.order_status in ('P','S') and a.booking_date = '$current_date'");
		$nbook = $db->loadResult();
		HTML_OsAppscheduleAjax::showServiceDetails($service,$date,$nbook);
	}
	/**
	 * Load the time frame of one employee
	 *
	 * @param int $sid
	 * @param int $eid
	 * @param array $date array(day,month,year)
	 */
	static function loadEmployee($sid,$eid,$date,$vid){
		global $mainframe,$mapClass,$configClass;
		//print_r($date);
		//get start hour and end hour for this employee today
		HTML_OsAppscheduleAjax::loadEmployeeFrame($sid,$eid,$date,$vid);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $option
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 * @param unknown_type $date
	 */
	static function loadEmployees($option,$sid,$employee_id,$date,$vid,$service_id,$eid){
		global $mainframe;

		$employees = HelperOSappscheduleCommon::loadEmployees($date,$sid,$employee_id,$vid);
		HTML_OsAppscheduleAjax::loadEmployeeFrames($option,$employees,$sid,$date,$vid,$service_id,$eid);
	}
	
	/**
	 * Reselect item
	 *
	 * @param unknown_type $option
	 */
	static function reselect($option){
		global $mainframe,$mapClass,$jinput;
		$date 					= OSBHelper::getStringValue('date','');
		$sid					= $jinput->getInt('sid',0);
		$eid					= $jinput->getInt('eid',0);
		$category_id 			= $jinput->getInt('category_id',0);
		$date_from				= $jinput->getInt('date_from','');
		$date_to				= $jinput->getInt('date_to','');
		$vid		 			= $jinput->getInt('vid',0);
		
		$booking_date			= date("Y-m-d",$date);
		$date					= explode("-",$booking_date);
		echo "1111";
		OsAppscheduleAjax::cart($userdata,$vid, $category_id,$eid,$date_from,$date_to);
		echo "2222";
		OsAppscheduleAjax::loadEmployee($sid,$eid,$date,$vid);
		exit();
	}


	/**
	 * Add to cart
	 *
	 * @param unknown_type $option
	 */
	static function addToCart($option)
	{
		global $configClass,$jinput,$mapClass;
        $user                   = JFactory::getUser();
        $userdata               = "";
		$realtime 				= HelperOSappscheduleCommon::getRealTime();
		$db						= JFactory::getDbo();
		$update_temp_table		= $jinput->getInt('update_temp_table',0);
		if((int) $configClass['checkout_itemid'] > 0)
		{
			$itemid				= $configClass['checkout_itemid'];
		}
		else
		{
			$itemid				= $jinput->getInt('Itemid');
		}
		if($configClass['allow_multiple_timeslots'] == 1)
		{
            $select_items       = $jinput->getString('select_items','');
            $select_itemsArr    = explode("|",$select_items);
            if(count($select_itemsArr) > 0)
            {
                $item           = $select_itemsArr[0];
                $item           = explode("-",$item);
                $item           = $item[0];
                $booking_date	= date("Y-m-d",$item);
            }
            else
            {
                $booking_date	= date("Y-m-d",$realtime);
            }
		}
		else
        {
            $start_booking_time = OSBHelper::getStringValue('start_booking_time','');
		    $end_booking_time	= OSBHelper::getStringValue('end_booking_time','');
		    $booking_date		= date("Y-m-d",$start_booking_time);
        }

        if($booking_date == "")
        {
            $booking_date	    = date("Y-m-d",$realtime);
        }
		$sid					= $jinput->getInt('sid',0);
		$eid					= $jinput->getInt('eid',0);
		$vid					= $jinput->getInt('vid',0);
		$category_id			= $jinput->getInt('category_id',0);
		$employee_id			= $jinput->getInt('employee_id',0);
		$date_from				= OSBHelper::getStringValue('date_from','');
		$date_to				= OSBHelper::getStringValue('date_to','');
		$count_services			= $jinput->getInt('count_services',0);

		$date 					= array();
		$date[0]				= date("d",strtotime($booking_date));
		$date[1]				= date("m",strtotime($booking_date));
		$date[2]			 	= date("Y",strtotime($booking_date));
		$unique_cookie 			= OSBHelper::getUniqueCookie();
		//check to see if there is a timeslot added into the cart before
		if($configClass['limit_one_timeslot'] == 1)
		{
			$db->setQuery("Select id from #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
			$temp_order_id		= $db->loadResult();
			if((int)$temp_order_id > 0)
			{
				$db->setQuery("Select count(id) from #__app_sch_temp_order_items where order_id = '$temp_order_id'");
				$count = $db->loadResult();
				if($count > 0)
				{
					if($configClass['using_cart'] == 0) //already add timeslot to cart
					{
						 JFactory::getApplication()->redirect(Jroute::_('index.php?option=com_osservicesbooking&task=form_step1&vid='.$vid.'&category_id='.$category_id.'&employee_id='.$employee_id.'&date_from='.$date_from.'&date_to='.$date_to.'&Itemid='.$itemid));
					}
					else
					{
						echo "1111";
						if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
						{
							OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
						}
						echo "@3333";
						echo JText::_('OS_YOU_ONLY_CAN_ADD_ONE_TIMESLOT_TO_CART');
						echo "2222";
						OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
					}
				}
			}
		}
		$repeat					= $jinput->get('repeat','','string');
		$nslots 				= $jinput->getInt('nslots',0);
		$config                 = new JConfig();
		$offset                 = $config->offset;
		date_default_timezone_set($offset);
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service                = $db->loadObject();
		if($service->service_time_type == 0)
		{
			$nslots = 1;
		}
		if($update_temp_table == 1)
		{
			$additional_information	= $_GET['additional_information'];
			$extraFields = explode("@",$additional_information);
			$bookingData = array();

			if($configClass['allow_multiple_timeslots'] == 1)
		    {
                if($repeat != "")
                { //prepare data for the repeat booking
                    $repeatArr      = explode("|",$repeat);
                    $repeat_type    = $repeatArr[0];
                    $repeat_to      = $repeatArr[1];
                    $repeatDate     = HelperOSappscheduleCalendar::calculateBookingDate($booking_date,$repeat_to,$repeat_type);
                    if(count($repeatDate) > 0)
                    {
                        for($i=0;$i<count($repeatDate);$i++)
                        {
                            $rdate                              = $repeatDate[$i];
                            for($j=0; $j< count($select_itemsArr); $j++)
                            {
                                $count                          = count($bookingData);
                                $item                           = $select_itemsArr[$j];
                                $item                           = explode("-",$item);
                                $start_booking_time             = $item[0];
                                $end_booking_time               = $item[1];
                                
                                $stime                          = date("H:i:s",$start_booking_time);
                                $etime                          = date("H:i:s",$end_booking_time);
                                $tempsdate                      = $rdate." ".$stime;
                                $tempedate                      = $rdate." ".$etime;


								$tmp							= new \stdClass();
								$tmp->date      = $rdate;
                                $tmp->start_time= strtotime($tempsdate);
                                $tmp->end_time  = strtotime($tempedate);
                                $tmp->additional_information = $additional_information;
                                $tmp->nslots 	= $nslots;
                                $tmp->sid		= $sid;
                                $tmp->eid		= $eid;
                                $tmp->vid		= $vid;
								$bookingData[$count] = $tmp;

                            }
                        }
                    }
                }
                else
                {
                    for($i=0;$i< count($select_itemsArr); $i++)
                    {
                        $item                                   = $select_itemsArr[$i];
                        $item                                   = explode("-",$item);
                        $start_booking_time                     = $item[0];
                        $end_booking_time                       = $item[1];
						$tmp									= new \stdClass();
                        $tmp->date 								= date("Y-m-d",$start_booking_time);
                        $tmp->start_time 						= $start_booking_time;
                        $tmp->end_time 							= $end_booking_time;
                        $tmp->additional_information			= $additional_information;
                        $tmp->nslots 	 						= $nslots;
                        $tmp->sid		 						= $sid;
                        $tmp->eid		 						= $eid;
                        $tmp->vid								= $vid;
						$bookingData[$i]						= $tmp;
						
                    }
                }
            }
            else
            {
                if($repeat != "")
                { //prepare data for the repeat booking
                    $repeatArr      = explode("|",$repeat);
                    $repeat_type    = $repeatArr[0];
                    $repeat_to      = $repeatArr[1];
                    $repeatDate     = HelperOSappscheduleCalendar::calculateBookingDate($booking_date,$repeat_to,$repeat_type);
                    if(count($repeatDate) > 0)
                    {
                        for($i=0;$i<count($repeatDate);$i++)
                        {
                            $rdate                          = $repeatDate[$i];

							$tmp									= new \stdClass();
                            
                            //prepare hours
                            $stime                          = date("H:i:s",$start_booking_time);
                            $etime                          = date("H:i:s",$end_booking_time);

                            $tempsdate                      = $rdate." ".$stime;
                            $tempedate                      = $rdate." ".$etime;

							$tmp->date          = $rdate;
                            $tmp->start_time    = strtotime($tempsdate);
                            $tmp->end_time      = strtotime($tempedate);
                            $tmp->additional_information = $additional_information;
                            $tmp->nslots 	    = $nslots;
                            $tmp->sid		    = $sid;
                            $tmp->eid		    = $eid;
                            $tmp->vid		    = $vid;

							$bookingData[$i]    = $tmp;
                        }
                    }
                }
                else
                {
					$tmp									= new \stdClass();
                    $tmp->date 					= date("Y-m-d",$start_booking_time);
                    $tmp->start_time 			= $start_booking_time;
                    $tmp->end_time 				= $end_booking_time;
                    $tmp->additional_information = $additional_information;
                    $tmp->nslots 	 			= $nslots;
                    $tmp->sid		 			= $sid;
                    $tmp->eid		 			= $eid;
                    $tmp->vid		            = $vid;
					$bookingData[0]				= $tmp;

                }
            }
			if($configClass['limit_one_timeslot'] == 1)
			{
				if(count($bookingData) > 1)
				{
					echo "1111";
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
					{
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}
					echo "@3333";
					echo JText::_('OS_YOU_ONLY_CAN_ADD_ONE_TIMESLOT_TO_CART');
					echo "2222";
					OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
				}
			}
            //print_r($bookingData);
			$canbook = 1;
			//echo $service->service_time_type;die();
			if($service->service_time_type == 1)
			{
				//Booking data array
				for($i=0;$i<count($bookingData);$i++)
				{
					$book = $bookingData[$i];
					
					$book_start_booking_date = $book->start_time;
					$book_end_booking_time   = $book->end_time;
					
					//convert to GMT timezone
					//date_default_timezone_set('UTC');
					//$local_time = date("Y-m-d H:i:s", time());
					
					$temp_start_min 		 = intval(date("i",$book_start_booking_date));
					$temp_start_hour  		 = intval(date("H",$book_start_booking_date));
					$temp_end_min   		 = intval(date("i",$book_end_booking_time));
					$temp_end_hour  		 = intval(date("H",$book_end_booking_time));
					
					$sid					 = $bookingData[$i]->sid;
					$eid 					 = $bookingData[$i]->eid;
					$nslots					 = $bookingData[$i]->nslots;
					$additional_information  = $bookingData[$i]->additional_information;
					
					//insert into temp temp data table
					if($i==0)
					{
						$parent_id = 0;
					}
					else
					{
						$parent_id = 1;
					}
					$db->setQuery("Insert into #__app_sch_temp_temp_order_items (id,parent_id,unique_cookie,sid,eid,start_time,end_time,booking_date,nslots,params,vid) values (NULL,'$parent_id','$unique_cookie','$sid','$eid','$book_start_booking_date','$book_end_booking_time','$book->date','$nslots','$repeat', $vid)");
					$db->execute();
					$order_item_id = $db->insertID();
					if(count($extraFields) > 0)
					{
						for($j=0;$j<count($extraFields);$j++)
						{
							$field = $extraFields[$j];
							$field = explode("-",$field);
							$field_id = $field[0];
							if($field_id > 0)
							{
								$field_values = $field[1];
								$field_values  = explode(",",$field_values);
								if(count($field_values) > 0)
								{
									for($l=0;$l<count($field_values);$l++)
									{
										$value = $field_values[$l];
										$db->setQuery("INSERT INTO #__app_sch_temp_temp_order_field_options (id,order_item_id,field_id,option_id) 	VALUES (NULL,'$order_item_id','$field_id','$value')");
										$db->execute();
									}
								}
							}
						}
					}
				}
				//booking array
				//checking again
				//with each booking date need to check
				//if the custom time slot : check to see if is the any time slots available
				//if the normal time slot : check to see if someone book the slot already
				//check if it is offline date of service
				//check if it is the rest day
				//check if it isn't working day of employee
				$db->setQuery("Select * from #__app_sch_temp_temp_order_items where unique_cookie like '$unique_cookie'");
				$rows = $db->loadObjectList();
				if(count($rows) > 0)
				{
					$errorArr = array();
					for($i=0;$i<count($rows);$i++)
					{
						$row = $rows[$i];
						//check number of slots. 
						$config = new JConfig();
						$offset = $config->offset;
						date_default_timezone_set($offset);
						if(!HelperOSappscheduleCalendar::checkSlots($row))
						{
							$canbook = 0;
							$errorArr[count($errorArr)] = HelperOSappscheduleCalendar::returnSlots($row);
						}
					}
				}
				//can book ?
				if($canbook == 1)
				{
				    $db->setQuery("Select count(distinct start_time) from #__app_sch_temp_temp_order_items where unique_cookie = '$unique_cookie' and sid = '$sid' and booking_date = '$booking_date'");
				    $tempTimeslots = $db->loadResult();

				    $db->setQuery("Select distinct start_time from #__app_sch_temp_temp_order_items where unique_cookie = '$unique_cookie' and sid = '$sid' and booking_date = '$booking_date'");
				    $selectedTimeslots = $db->loadColumn(0);
				    

                    $db->setQuery("Select count(distinct a.start_time) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on a.order_id = b.id where b.unique_cookie = '$unique_cookie' and a.sid = '$sid' and a.booking_date = '$booking_date' $sqlTimeslot");
                    $temp1Timeslots = $db->loadResult();

                    $db->setQuery("Select distinct a.start_time from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on a.order_id = b.id where b.unique_cookie = '$unique_cookie' and a.sid = '$sid' and a.booking_date = '$booking_date' $sqlTimeslot");
                    $selectedTimeslots1 = $db->loadResult();
                    
                    if($user->id > 0)
                    {
                        $db->setQuery("Select count(distinct a.start_time) from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id where b.user_id = '$user->id' and a.sid = '$sid' and a.booking_date = '$booking_date' $sqlTimeslot $sqlTimeslot1");
                        $bookedTimeslots = $db->loadResult();
                    }

                    if($tempTimeslots + $temp1Timeslots + $bookedTimeslots > (int) $service->max_timeslots && (int)$service->max_timeslots > 0)
                    {
                          HelperOSappscheduleCommon::removeTempSlots();
                            echo "1111";
                            if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
                            {
                                OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
                            }
                            else
                            {
                                echo "#";
                            }
                            echo "@3333";
                            echo sprintf(JText::_('OS_YOU_CANNOT_ADD_TIMESLOTS_TO_CART'),$service->max_timeslots);
                            echo "2222";
                            OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
                            return false;
                    }

                    //calculate nslots from #__app_sch_temp_temp_order_items
                    $db->setQuery("Select sum(nslots) from #__app_sch_temp_temp_order_items where unique_cookie = '$unique_cookie' and sid = '$sid' ");
                    $nslots = (int)$db->loadResult();
				    $db->setQuery("Select sum(a.nslots) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on a.order_id = b.id where b.unique_cookie = '$unique_cookie' and a.sid = '$sid'");
                    $total_nslots = (int)$db->loadResult();
                    if($service->max_seats > 0)
                    {
                        if($total_nslots + $nslots > $service->max_seats)
                        {
                            HelperOSappscheduleCommon::removeTempSlots();
                            echo "1111";
                            if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
                            {
                                OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
                            }
                            else
                            {
                                echo "#";
                            }
                            echo "@3333";
                            echo JText::_('OS_YOU_CANNOT_ADD_MORE_TIMESLOTS_TO_CART');
                            echo "2222";
                            OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
                            return false;
                        }
                    }

					$db->setQuery("SELECT COUNT(id) FROM #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
					$count = $db->loadResult();
					if($count == 0)
					{
						//insert into order temp table
						$db->setQuery("INSERT INTO #__app_sch_temp_orders (id, unique_cookie,user_id,created_on) VALUES (NULL,'$unique_cookie','".$user->id."','".time()."')");
						$db->execute();
						$order_id = $db->insertID();
					}
					else
					{
						$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie LIKE '$unique_cookie'");
						$order_id = $db->loadResult();
					}
					
					//update employee and time
					for($j=0;$j<count($rows);$j++)
					{
						$extracost = 0;
						$row = $rows[$j];
						$start_booking_time = $row->start_time;
						$end_booking_time   = $row->end_time;
						$booking_date		= $row->booking_date;
						$nslots 			= $row->nslots;
						$db->setQuery("INSERT INTO #__app_sch_temp_order_items (id,order_id,sid,eid,vid,start_time,end_time,booking_date,nslots,params) VALUES (NULL,'$order_id','$sid','$eid','$vid','$start_booking_time','$end_booking_time','$booking_date','$nslots','$repeat')");
						$db->execute();
						$order_item_id = $db->insertID();
						//update fields
						if(count($extraFields) > 0)
						{
							for($i=0;$i<count($extraFields);$i++)
							{
								$field = $extraFields[$i];
								$field = explode("-",$field);
								$field_id = $field[0];
								if($field_id > 0)
								{
									$field_values = $field[1];
									$field_values  = explode(",",$field_values);
									if(count($field_values) > 0)
									{
										for($l=0;$l<count($field_values);$l++)
										{
											$value = $field_values[$l];
											$db->setQuery("INSERT INTO #__app_sch_temp_order_field_options (id,order_item_id,field_id,option_id) 	VALUES (NULL,'$order_item_id','$field_id','$value')");
											$db->execute();

											$db->setQuery("Select * from #__app_sch_field_options where id = '$value'");
											$fieldOption = $db->loadObject();
											//if($fieldOption->additional_price > 0){
												$extracost += $fieldOption->additional_price;
											//}
										}
									}
								}
							}
						}
						//calculate the cost of the item
					
						
						$db->setQuery("Select a.*,b.additional_price from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.id = '$eid' and b.service_id = '$sid'");
						$employee = $db->loadObject();
						$date_in_week		= date("N",$start_booking_time);
						//get extra cost
						$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '$date_in_week' or week_date = '0')");
						//echo $db->getQuery();
						$extras = $db->loadObjectList();
						if(count($extras) > 0)
						{
							for($j1=0;$j1<count($extras);$j1++)
							{
								$extra = $extras[$j1];
								$stime = $extra->start_time;
								$etime = $extra->end_time;
								$stime = date("Y-m-d",$start_booking_time)." ".$stime.":00";
								$etime = date("Y-m-d",$start_booking_time)." ".$etime.":00";
								$stime = strtotime($stime);
								$etime = strtotime($etime);
								if(($start_booking_time >= $stime) and ($start_booking_time <= $etime))
								{
									$extracost += $extra->extra_cost;
								}
							}
						}
						//incase you already setup to discount by number timeslot added
						$discount_by_timeslot = false;
						if($configClass['enable_slots_discount'] == 1 && $service->discount_timeslots > 0)
						{
						    //re calculate total number timeslots
						    $db->setQuery("Select sum(nslots) from #__app_sch_temp_order_items where order_id = '$order_id' and sid = '$sid'");
                            $total_nslots = (int)$db->loadResult();
							//find total timeslots of this service in current oder
                            if($total_nslots > $service->discount_timeslots) //having discount by timeslot yet?
                            {
                                $discount_by_timeslot = true;
                                //update cost of the existing item in this order
                                $db->setQuery("Select a.* from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on a.order_id = b.id where b.unique_cookie = '$unique_cookie' and a.sid = '$sid'");
                                $temp_order_items = $db->loadObjectList();
                                foreach($temp_order_items as $temp_order_item)
                                {
                                    $already_discounted = $temp_order_item->already_discounted;
                                    $timeslots_cost     = $temp_order_item->timeslots_cost;
                                    if($already_discounted == 0) //have not updated discount cost, avoid repeat update discount
                                    {
                                        $number_slots       = $temp_order_item->nslots;
                                        $cost_per_slot      = $timeslots_cost/$number_slots;
                                        $cost_per_slot_new  = OSBHelper::discountBySlotsWithoutCheckingNumberSlots($sid, $cost_per_slot);
                                        $timeslots_cost_new = $cost_per_slot_new*$nslots;
                                        $total_cost         = $timeslots_cost_new + $temp_order_item->other_cost;
										if($total_cost < 0)
										{
											$total_cost		= 0;
										}
                                        $db->setQuery("Update #__app_sch_temp_order_items set `already_discounted` = '1',`timeslots_cost`= '$timeslots_cost_new',`total_cost` = '$total_cost' where id = '$temp_order_item->id'");
                                        $db->execute();
                                    }
                                }
                            }
						}

						//come back to current item
                        $timeslots_cost = OSBHelper::returnServicePrice($service->id,date("Y-m-d",$start_booking_time),$nslots,0,$discount_by_timeslot,$start_booking_time)*$nslots;
						$other_cost	= ($employee->additional_price + $extracost)*$nslots;
						$total_cost = $timeslots_cost + $other_cost;
						if($discount_by_timeslot)
                        {
                            $already_discounted = 1;
                        }
                        else
                        {
                            $already_discounted = 0;
                        }
						if($total_cost < 0)
						{
							$total_cost		= 0;
						}
						$db->setQuery("Update #__app_sch_temp_order_items set `already_discounted`= '$already_discounted',`timeslots_cost`= '$timeslots_cost',`other_cost` = '$other_cost',`total_cost` = '$total_cost' where id = '$order_item_id'");
						$db->execute();
					}
					//empty from temp temp data table
					HelperOSappscheduleCommon::removeTempSlots();
				
					echo "1111";
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
					{
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}
					else
					{
						echo Jroute::_('index.php?option=com_osservicesbooking&task=form_step1&vid='.$vid.'&category_id='.$category_id.'&employee_id='.$employee_id.'&date_from='.$date_from.'&date_to='.$date_to.'&Itemid='.$itemid);
					}
					echo "@3333";
					//echo JText::_('OS_ITEM_HAS_BEEN_ADD_TO_CART');
					self::showInformPopup();
					echo "2222";
					OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
				}
				else
				{//cannot book
					echo "1111";
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2)){
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}else{
						echo "#";
					}
					echo "@3333";
					echo "2222";
					OSappscheduleInformation::showError($sid,$eid,$errorArr,$vid);
				}
			}
			else
			{ 
				//time slot == 0 [normal]
				$additional_information	= $_GET['additional_information'];
				$extraFields = explode("@",$additional_information);
				$bookingData = array();
				
				if($configClass['allow_multiple_timeslots'] == 1)
				{
					if($repeat != "")
					{ //prepare data for the repeat booking
						$repeatArr = explode("|",$repeat);
						$repeat_type  = $repeatArr[0];
						$repeat_to    = $repeatArr[1];
						$repeatDate   = HelperOSappscheduleCalendar::calculateBookingDate($booking_date,$repeat_to,$repeat_type);
						if(count($repeatDate) > 0){
							for($i=0;$i<count($repeatDate);$i++)
							{
								$rdate                              = $repeatDate[$i];
								for($j=0; $j< count($select_itemsArr); $j++)
								{
									$count                          = count($bookingData);
									$item                           = $select_itemsArr[$j];
									$item                           = explode("-",$item);
									$start_booking_time             = $item[0];
									$end_booking_time               = $item[1];

									
									//prepare hours
									$stime                          = date("H:i:s",$start_booking_time);
									$etime                          = date("H:i:s",$end_booking_time);
									$tempsdate                      = $rdate." ".$stime;
									$tempedate                      = $rdate." ".$etime;

									$tmp							= new \stdClass();
									$tmp->date						= $rdate;
									$tmp->start_time				= strtotime($tempsdate);
									$tmp->end_time					= strtotime($tempedate);
									$tmp->additional_information	= $additional_information;
									$tmp->nslots 					= $nslots;
									$tmp->sid						= $sid;
									$tmp->eid						= $eid;
									$tmp->vid						= $vid;

									$bookingData[$count]			= $tmp;
								}
							}
						}
					}
					else
					{
						for($i=0;$i< count($select_itemsArr); $i++)
						{
							$item                                   = $select_itemsArr[$i];
							$item                                   = explode("-",$item);
							$start_booking_time                     = $item[0];
							$end_booking_time                       = $item[1];
							$tmp									= new \stdClass();
							$tmp->date 								= date("Y-m-d",$start_booking_time);
							$tmp->start_time 						= $start_booking_time;
							$tmp->end_time 							= $end_booking_time;
							$tmp->additional_information			= $additional_information;
							$tmp->nslots 	 						= $nslots;
							$tmp->sid		 						= $sid;
							$tmp->eid		 						= $eid;
							$tmp->vid								= $vid;

							$bookingData[$i]						= $tmp;
						}
					}
				}
				else
				{
					if($repeat != "")
					{
					    //prepare data for the repeat booking
						$repeatArr      = explode("|",$repeat);
						$repeat_type    = $repeatArr[0];
						$repeat_to      = $repeatArr[1];
						$repeatDate     = HelperOSappscheduleCalendar::calculateBookingDate($booking_date,$repeat_to,$repeat_type);
						if(count($repeatDate) > 0)
						{
							for($i=0;$i<count($repeatDate);$i++)
							{
								$rdate                          = $repeatDate[$i];
								
								//prepare hours
								$stime                          = date("H:i:s",$start_booking_time);
								$etime                          = date("H:i:s",$end_booking_time);
								$tempsdate                      = $rdate." ".$stime;
								$tempedate                      = $rdate." ".$etime;
								$tmp							= new \stdClass();
								$tmp->date						= $rdate;
								$tmp->start_time				= strtotime($tempsdate);
								$tmp->end_time					= strtotime($tempedate);
								$tmp->additional_information	= $additional_information;
								$tmp->nslots 					= $nslots;
								$tmp->sid						= $sid;
								$tmp->eid						= $eid;
								$tmp->vid						= $vid;

								$bookingData[count($bookingData)]				= $tmp;
							}
						}
					}
					else
					{
						$tmp									= new \stdClass();
						$tmp->date 								= date("Y-m-d",$start_booking_time);
						$tmp->start_time 						= $start_booking_time;
						$tmp->end_time 							= $end_booking_time;
						$tmp->additional_information			= $additional_information;
						$tmp->nslots 	 						= $nslots;
						$tmp->sid		 						= $sid;
						$tmp->eid		 						= $eid;
						$tmp->vid								= $vid;
						$bookingData[0]							= $tmp;
					}
				}


				$canbook = 1;
				//Booking data array
				for($i=0;$i<count($bookingData);$i++)
				{
					$extracost = 0;
					$book = $bookingData[$i];
				
					$book_start_booking_date = $book->start_time;
					$book_end_booking_time   = $book->end_time;
					$temp_start_min 		 = intval(date("i",$book_start_booking_date));
					$temp_start_hour  		 = intval(date("H",$book_start_booking_date));
					$temp_end_min   		 = intval(date("i",$book_end_booking_time));
					$temp_end_hour  		 = intval(date("H",$book_end_booking_time));
					
					$sid					 = $bookingData[$i]->sid;
					$eid 					 = $bookingData[$i]->eid;
					$nslots					 = 1;
					$additional_information  = $bookingData[$i]->additional_information;
					
					//insert into temp temp data table
					if($i==0)
					{
						$parent_id          = 0;
					}else{
						$parent_id          = 1;
					}
					$db->setQuery("Insert into #__app_sch_temp_temp_order_items (id,parent_id,unique_cookie,sid,eid,vid,start_time,end_time,booking_date,nslots,params) values (NULL,'$parent_id','$unique_cookie','$sid','$eid','$vid','$book_start_booking_date','$book_end_booking_time','$book->date','$nslots','$repeat')");
					//echo $db->getQuery();
					//echo "<BR />";
					$db->execute();

					$order_item_id = $db->insertID();
					
					if(count($extraFields) > 0)
					{
						for($j=0;$j<count($extraFields);$j++)
						{
							$field          = $extraFields[$j];
							$field          = explode("-",$field);
							$field_id       = $field[0];
							if($field_id > 0)
							{
								$field_values = $field[1];
								$field_values = explode(",",$field_values);
								if(count($field_values) > 0)
								{
									for($l=0;$l<count($field_values);$l++)
									{
										$value = $field_values[$l];
										$db->setQuery("INSERT INTO #__app_sch_temp_temp_order_field_options (id,order_item_id,field_id,option_id) 	VALUES (NULL,'$order_item_id','$field_id','$value')");
										$db->execute();
									}
								}
							}
						}
					}
					//calculate the cost of the item
				}
				//die();
				//end booking array
				//checking again
				//with each booking date need to check
				//if the custom time slot : check to see if is the any time slots available
				//if the normal time slot : check to see if someone book the slot already
				//check if it is offline date of service
				//check if it is the rest day
				//check if it isn't working day of employee
				
				$db->setQuery("Select * from #__app_sch_temp_temp_order_items where unique_cookie like '$unique_cookie'");
				//echo $db->getQuery();
				$rows                       = $db->loadObjectList();
				//print_r($rows);
				//die();
				if(count($rows) > 0){
					$errorArr               = array();
					for($i=0;$i<count($rows);$i++)
					{
						$row                = $rows[$i];
						$config             = new JConfig();
						$offset             = $config->offset;
						date_default_timezone_set($offset);
						//check number of slots. 
						if(!HelperOSappscheduleCalendar::checkSlots($row))
						{
							$canbook        = 0;
							$errorArr[count($errorArr)] = $row;
						}
					}
				}
				//die();
				//can book ?
				if($canbook == 1)
				{
					$db->setQuery("Select count(distinct start_time) from #__app_sch_temp_temp_order_items where unique_cookie = '$unique_cookie' and sid = '$sid' and booking_date = '$booking_date'");
				    $tempTimeslots = $db->loadResult();

				    $db->setQuery("Select distinct start_time from #__app_sch_temp_temp_order_items where unique_cookie = '$unique_cookie' and sid = '$sid' and eid = '$eid' and booking_date = '$booking_date'");
				    $selectedTimeslots = $db->loadColumn(0);
				    

                    $db->setQuery("Select count(distinct a.start_time) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on a.order_id = b.id where b.unique_cookie = '$unique_cookie' and a.sid = '$sid' and a.booking_date = '$booking_date' $sqlTimeslot");
                    $temp1Timeslots = $db->loadResult();

                    $db->setQuery("Select distinct a.start_time from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on a.order_id = b.id where b.unique_cookie = '$unique_cookie' and a.sid = '$sid' and a.eid = '$eid' and a.booking_date = '$booking_date' $sqlTimeslot");
                    $selectedTimeslots1 = $db->loadResult();
                    

                    if($user->id > 0)
                    {
                        $db->setQuery("Select count(distinct a.start_time) from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id where b.user_id = '$user->id' and a.sid = '$sid' and a.booking_date = '$booking_date' $sqlTimeslot $sqlTimeslot1");
                        $bookedTimeslots = $db->loadResult();
                    }

                    if($tempTimeslots + $temp1Timeslots + $bookedTimeslots > (int)$service->max_timeslots && (int)$service->max_timeslots > 0)
                    {
                            HelperOSappscheduleCommon::removeTempSlots();
                            echo "1111";
                            if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
                            {
                                OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
                            }
                            else
                            {
                                echo "#";
                            }
                            echo "@3333";
                            echo sprintf(JText::_('OS_YOU_CANNOT_ADD_TIMESLOTS_TO_CART'),$service->max_timeslots);
                            echo "2222";
                            OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
                            return false;
                    }
					$db->setQuery("SELECT COUNT(id) FROM #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
					$count                  = $db->loadResult();
					if($count == 0)
					{
						//insert into order temp table
						$db->setQuery("INSERT INTO #__app_sch_temp_orders (id, unique_cookie,user_id,created_on) VALUES (NULL,'$unique_cookie','".$user->id."','".time()."')");
						$db->execute();
						$order_id           = $db->insertID();
					}else{
						$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie LIKE '$unique_cookie'");
						$order_id           = $db->loadResult();
					}
					//echo $order_id;die();
					//print_r($rows);die();
					//update employee and time
					for($j=0;$j<count($rows);$j++)
					{
						$extracost          = 0;
						$row                = $rows[$j];
						$start_booking_time = $row->start_time;
						$end_booking_time   = $row->end_time;
						$booking_date		= $row->booking_date;
						$nslots 			= 1;
						$db->setQuery("INSERT INTO #__app_sch_temp_order_items (id,order_id,sid,eid,vid,start_time,end_time,booking_date,nslots,params) VALUES (NULL,'$order_id','$sid','$eid','$vid','$start_booking_time','$end_booking_time','$booking_date','$nslots','$repeat')");
						$db->execute();
						$order_item_id = $db->insertID();
						//update fields
						if(count($extraFields) > 0)
						{
							for($i=0;$i<count($extraFields);$i++)
							{
								$field      = $extraFields[$i];
								$field      = explode("-",$field);
								$field_id   = $field[0];
								if($field_id > 0)
								{
									$field_values = $field[1];
									$field_values  = explode(",",$field_values);
									if(count($field_values) > 0)
									{
										for($l=0;$l<count($field_values);$l++)
										{
											$value = $field_values[$l];
											$db->setQuery("INSERT INTO #__app_sch_temp_order_field_options (id,order_item_id,field_id,option_id) 	VALUES (NULL,'$order_item_id','$field_id','$value')");
											$db->execute();

											$db->setQuery("Select * from #__app_sch_field_options where id = '$value'");
											$fieldOption = $db->loadObject();
											//if($fieldOption->additional_price > 0){
												$extracost += $fieldOption->additional_price;
											//}
										}
									}
								}
							}
						}
						//calculate the cost of the item
						$db->setQuery("Select a.*,b.additional_price from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.id = '$eid' and b.service_id = '$sid'");
						$employee = $db->loadObject();
						$date_in_week		= date("N",$start_booking_time);
						//get extra cost
						$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '$date_in_week' or week_date = '0')");
						//echo $db->getQuery();
						$extras = $db->loadObjectList();
						if(count($extras) > 0){
							for($j1=0;$j1<count($extras);$j1++)
							{
								$extra = $extras[$j1];
								$stime = $extra->start_time;
								$etime = $extra->end_time;
								$stime = date("Y-m-d",$start_booking_time)." ".$stime.":00";
								$etime = date("Y-m-d",$start_booking_time)." ".$etime.":00";
								$stime = strtotime($stime);
								$etime = strtotime($etime);
								if(($start_booking_time >= $stime) and ($start_booking_time <= $etime))
								{
									$extracost += $extra->extra_cost;
								}
							}
						}
						
						$total = (OSBHelper::returnServicePrice($service->id,date("Y-m-d",$start_booking_time),$nslots, $employee->id, false, $start_booking_time) + $employee->additional_price + $extracost)*$nslots;

						if($total < 0)
						{
							$total		= 0;
						}
						$db->setQuery("Update #__app_sch_temp_order_items set `total_cost` = '$total' where id = '$order_item_id'");
						$db->execute();
					}
					
					//empty from temp temp data table
					HelperOSappscheduleCommon::removeTempSlots();
					echo "1111";
					
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
					{
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}
					else
					{
						echo Jroute::_('index.php?option=com_osservicesbooking&task=form_step1&vid='.$vid.'&category_id='.$category_id.'&employee_id='.$employee_id.'&date_from='.$date_from.'&date_to='.$date_to.'&Itemid='.$itemid);
					}
					echo "@3333";
					//echo JText::_('OS_ITEM_HAS_BEEN_ADD_TO_CART');
					self::showInformPopup();
					echo "2222";
					//OsAppscheduleAjax::loadEmployee($sid,$eid,$date,$vid);
					OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
				}
				else
				{
				    //cannot book
					echo "1111";
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
					{
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}
					else
					{
						echo "#";
					}
					echo "@3333";
					echo "2222";
					OSappscheduleInformation::showError($sid,$eid,$errorArr,$vid);
					
				}
			}
		}
		else
		{
		    //only need to check update_temp_table = 0
		    $canbook = 1;
			if($service->service_time_type == 1)
			{
				//checking again
				$db->setQuery("Select * from #__app_sch_temp_temp_order_items where unique_cookie like '$unique_cookie'");
				$rows = $db->loadObjectList();
				if(count($rows) > 0)
				{
					$errorArr = array();
					for($i=0;$i<count($rows);$i++)
					{
						$row = $rows[$i];
						//check number of slots. 
						if(!HelperOSappscheduleCalendar::checkSlots($row))
						{
							$canbook = 0;
							$errorArr[count($errorArr)] = HelperOSappscheduleCalendar::returnSlots($row);
						}
					}
				}
				//can book ?
				if($canbook == 1)
				{
					$db->setQuery("SELECT COUNT(id) FROM #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
					$count = $db->loadResult();
					if($count == 0)
					{
						//insert into order temp table
						$db->setQuery("INSERT INTO #__app_sch_temp_orders (id, unique_cookie,user_id,created_on) VALUES (NULL,'$unique_cookie','".$user->id."','".time()."')");
						$db->execute();
						$order_id = $db->insertID();
					}
					else
					{
						$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie LIKE '$unique_cookie'");
						$order_id = $db->loadResult();
					}
					
					//update employee and time
					for($j=0;$j<count($rows);$j++)
					{
						$extracost = 0;
						$row = $rows[$j];
						$start_booking_time = $row->start_time;
						$end_booking_time   = $row->end_time;
						$booking_date		= $row->booking_date;
						$nslots 			= $row->nslots;
						$db->setQuery("INSERT INTO #__app_sch_temp_order_items (id,order_id,sid,eid,vid,start_time,end_time,booking_date,nslots,params) VALUES (NULL,'$order_id','$sid','$eid','$vid','$start_booking_time','$end_booking_time','$booking_date','$nslots','$repeat')");
						$db->execute();
						$order_item_id = $db->insertID();
						//update fields
						
						$db->setQuery("SELECT * FROM #__app_sch_temp_temp_order_field_options WHERE order_item_id = '$row->id'");
						$extraFields = $db->loadObjectList();
						if(count($extraFields) > 0)
						{
							for($i=0;$i<count($extraFields);$i++)
							{
								$field = $extraFields[$i];
								$db->setQuery("INSERT INTO #__app_sch_temp_order_field_options (id,order_item_id,field_id,option_id) 	VALUES (NULL,'$row->id','$field->field_id','$field->option_id')");
								$db->execute();

								$db->setQuery("Select * from #__app_sch_field_options where id = '$field->option_id'");
								$fieldOption = $db->loadObject();
								//if($fieldOption->additional_price > 0){
									$extracost += $fieldOption->additional_price;
								//}
							}
						}
						//calculate the cost of the item
						
						$db->setQuery("Select a.*,b.additional_price from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.id = '$eid' and b.service_id = '$sid'");
						$employee = $db->loadObject();
						$date_in_week		= date("N",$start_booking_time);
						//get extra cost
						$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '$date_in_week' or week_date = '0')");
						//echo $db->getQuery();
						$extras = $db->loadObjectList();
						if(count($extras) > 0)
						{
							for($j=0;$j<count($extras);$j++)
							{
								$extra = $extras[$j];
								$stime = $extra->start_time;
								$etime = $extra->end_time;
								$stime = date("Y-m-d",$start_booking_time)." ".$stime.":00";
								$etime = date("Y-m-d",$start_booking_time)." ".$etime.":00";
								$stime = strtotime($stime);
								$etime = strtotime($etime);
								if(($start_booking_time >= $stime) && ($start_booking_time <= $etime))
								{
									$extracost += $extra->extra_cost;
								}
							}
						}
						$total = (OSBHelper::returnServicePrice($service->id,date("Y-m-d",$start_booking_time), $nslots, $employee->id, false, $start_booking_time) + $employee->additional_price + $extracost)*$nslots;
						if($total < 0)
						{
							$total		= 0;
						}
						$db->setQuery("Update #__app_sch_temp_order_items set `total_cost` = '$total' where id = '$order_item_id'");
						$db->execute();
					}
					//empty from temp temp data table
					HelperOSappscheduleCommon::removeTempSlots();
				
					echo "1111";
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2))
					{
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}else{
						echo Jroute::_('index.php?option=com_osservicesbooking&task=form_step1&vid='.$vid.'&category_id='.$category_id.'&employee_id='.$employee_id.'&date_from='.$date_from.'&date_to='.$date_to.'&Itemid='.$itemid);
					}
					echo "@3333";
					//echo JText::_('OS_ITEM_HAS_BEEN_ADD_TO_CART');
					self::showInformPopup();
					echo "2222";
					//OsAppscheduleAjax::loadEmployee($sid,$eid,$date,$vid);
					//echo $employee_id;

					OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid);
					
				}
				else
				{//cannot book
					echo "1111";
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2)){
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}else{
						echo "#";
					}
					echo "@3333";
					echo "2222";
					OSappscheduleInformation::showError($sid,$eid,$errorArr,$vid);
					
				}
			}
			else
			{
			    $db->setQuery("Select * from #__app_sch_temp_temp_order_items where unique_cookie like '$unique_cookie'");
				$rows = $db->loadObjectList();
				if(count($rows) > 0)
				{
					$errorArr = array();
					for($i=0;$i<count($rows);$i++)
					{
						$row = $rows[$i];
						//check number of slots. 
						if(!HelperOSappscheduleCalendar::checkSlots($row))
						{
							$canbook = 0;
							$errorArr[count($errorArr)] = $row;
						}
					}
				}
				
				if($canbook == 1){
					$db->setQuery("SELECT COUNT(id) FROM #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
					$count = $db->loadResult();
					if($count == 0){
						//insert into order temp table
						$db->setQuery("INSERT INTO #__app_sch_temp_orders (id, unique_cookie,created_on) VALUES (NULL,'$unique_cookie','".time()."')");
						$db->execute();
						$order_id = $db->insertID();
					}else{
						$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie LIKE '$unique_cookie'");
						$order_id = $db->loadResult();
					}
					
					//update employee and time
					for($j=0;$j<count($rows);$j++){
						$extracost = 0;
						$row = $rows[$j];
						$start_booking_time = $row->start_time;
						$end_booking_time   = $row->end_time;
						$booking_date		= $row->booking_date;
						$nslots 			= 0;
						$db->setQuery("INSERT INTO #__app_sch_temp_order_items (id,order_id,sid,eid,vid,start_time,end_time,booking_date,nslots,params) VALUES (NULL,'$order_id','$sid','$eid','$vid','$start_booking_time','$end_booking_time','$booking_date','$nslots','$repeat')");
						$db->execute();
						$order_item_id = $db->insertID();
						//update fields
						
						$db->setQuery("SELECT * FROM #__app_sch_temp_temp_order_field_options WHERE order_item_id = '$row->id'");
						$extraFields = $db->loadObjectList();
						if(count($extraFields) > 0){
							for($i=0;$i<count($extraFields);$i++){
								$field = $extraFields[$i];
								$db->setQuery("INSERT INTO #__app_sch_temp_order_field_options (id,order_item_id,field_id,option_id) 	VALUES (NULL,'$row->id','$field->field_id','$field->option_id')");
								$db->execute();

								$db->setQuery("Select * from #__app_sch_field_options where id = '$field->option_id'");
								$fieldOption = $db->loadObject();
								//if($fieldOption->additional_price > 0){
									$extracost += $fieldOption->additional_price;
								//}
							}
						}

						$db->setQuery("Select a.*,b.additional_price from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.id = '$eid' and b.service_id = '$sid'");
						$employee = $db->loadObject();
						$date_in_week		= date("N",$start_booking_time);
						//get extra cost
						$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '$date_in_week' or week_date = '0')");
						//echo $db->getQuery();
						$extras = $db->loadObjectList();
						if(count($extras) > 0)
						{
							for($j=0;$j<count($extras);$j++)
							{
								$extra = $extras[$j];
								$stime = $extra->start_time;
								$etime = $extra->end_time;
								$stime = date("Y-m-d",$start_booking_time)." ".$stime.":00";
								$etime = date("Y-m-d",$start_booking_time)." ".$etime.":00";
								$stime = strtotime($stime);
								$etime = strtotime($etime);
								if($start_booking_time >= $stime && $start_booking_time <= $etime)
								{
									$extracost += $extra->extra_cost;
								}
							}
						}
						$total = (OSBHelper::returnServicePrice($service->id,date("Y-m-d",$start_booking_time), $nslots, $employee->id, false, $start_booking_time) + $employee->additional_price + $extracost)*$nslots;
						if($total < 0)
						{
							$total		= 0;
						}
						$db->setQuery("Update #__app_sch_temp_order_items set `total_cost` = '$total' where id = '$order_item_id'");
						$db->execute();

					}
					//empty from temp temp data table
					HelperOSappscheduleCommon::removeTempSlots();
				
					echo "1111";
					//echo $configClass['using_cart'];
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2)){
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}else{
						echo Jroute::_('index.php?option=com_osservicesbooking&task=form_step1&vid='.$vid.'&category_id='.$category_id.'&employee_id='.$employee_id.'&date_from='.$date_from.'&date_to='.$date_to.'&Itemid='.$itemid);
					}
					echo "@3333";
					self::showInformPopup();
					echo "2222";
					//OsAppscheduleAjax::loadEmployee($sid,$eid,$date,$vid);
					OsAppscheduleAjax::prepareLoadServices($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
					
				}else{//cannot book
					echo "1111";
					if(($configClass['using_cart'] == 1) || ($configClass['using_cart'] == 2)){
						OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
					}else{
						echo "#";
					}
					echo "@3333";
					echo "2222";
					OSappscheduleInformation::showError($sid,$eid,$errorArr,$vid);
					
				}
			}
		}
		//echo "4444";
		//$services = self::prepareLoadServicesObjects($option,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$eid,$count_services);
		//echo HelperOSappscheduleCommon::getServicesAndEmployees($services,intval($date[2]),intval($date[1]),intval($date[0]),$category_id,$employee_id,$vid,$sid,$employee_id);
		exit();
	}
	
	/**
	 * Show Inform Popup
	 *
	 */
	public static function showInformPopup()
	{
		global $configClass, $mapClass;
		$itemid = "";
		if((int) $configClass['checkout_itemid'] > 0)
		{
			$itemid				= "&Itemid=".$configClass['checkout_itemid'];
		}
		if($configClass['use_js_popup'] == 0)
		{
			?>
			<div class="<?php echo $mapClass['row-fluid'];?> msgDivInfoBox">
				<div class="<?php echo $mapClass['span12'];?>">
					<?php echo JText::_('OS_ITEM_HAS_BEEN_ADD_TO_CART'); ?>
					<a href="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=form_step1'.$itemid);?>" class="btn <?php echo $configClass['header_style']?>"><?php echo JText::_('OS_CHECKOUT')?></a>
				</div>
			</div>
			<?php
		}
		else
		{
		?>
			<p><?php echo JText::_('OS_ITEM_HAS_BEEN_ADD_TO_CART'); ?></p>
			<BR />
			<a href="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=form_step1'.$itemid);?>" class="btn <?php echo $configClass['header_style']?>"><?php echo JText::_('OS_CHECKOUT')?></a>
	    <?php
		}
	}
	
	static function removeItem($option)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		//$unique_cookie 		= $_COOKIE['unique_cookie'];
        $userdata           = "";
		$unique_cookie		= OSBHelper::getUniqueCookie();
		$sid 				= $jinput->getInt('sid');
		$start_time			= $jinput->getInt('start_time');
		$end_time			= $jinput->getInt('end_time');
		$eid				= $jinput->getInt('eid');
		$itemid				= $jinput->getInt('itemid',0);
		$category_id		= $jinput->getInt('category_id',0);
		$employee_id		= $jinput->getInt('employee_id',0);
		$vid				= $jinput->getInt('vid',0);
		$date_from		 	= OSBHelper::getStringValue('date_from','');
		$date_to			= OSBHelper::getStringValue('date_to','');
		$count_services     = $jinput->getInt('count_services',0);
		
		$db = JFactory::getDbo();
		$db->setQuery("Select id from #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
		$order_id = $db->loadResult();

		//get Information of this item
		if($configClass['enable_slots_discount'] == 1)
		{
		    $db->setQuery("Select sid, nslots, booking_date, start_time from #__app_sch_temp_order_items where id = '$itemid'");
		    $orderItem      = $db->loadObject();
		    $sid            = $orderItem->sid;
		    $nslots         = $orderItem->nslots;
		    $booking_date   = $orderItem->booking_date;
		    $start_time     = $orderItem->start_time;
		    $db->setQuery("Select service_time_type, discount_timeslots, discount_type, discount_amount from #__app_sch_services where id = '$sid'");
		    $service        = $db->loadObject();
		    $service_time_type = $service->service_time_type;
		    if($service_time_type == 1 && $service->discount_timeslots > 0)
            {
                $db->setQuery("Select sum(nslots) from #__app_sch_temp_order_items where order_id = '$order_id' and sid = '$sid'");
                $total_nslots = $db->loadResult();
                if($total_nslots - $nslots <= $service->discount_timeslots)
                {
                    //re-calculate price without discount
                    $db->setQuery("Select * from #__app_sch_temp_order_items where order_id = '$order_id' and sid = '$sid' and id <> '$itemid'");
                    $rows   = $db->loadObjectList();
                    if(count($rows) > 0)
                    {
                        foreach ($rows as $row)
                        {
                            $timeslot_price = OSBHelper::returnServicePrice($sid, $booking_date, $row->nslots, $row->eid, false, $start_time);
                            $total_cost     = $timeslot_price + $row->other_cost;
							if($total_cost < 0)
							{
								$total_cost	= 0;
							}
                            $db->setQuery("Update #__app_sch_temp_order_items set `timeslots_cost` = '$timeslot_price', `total_cost` = '$total_cost', `already_discounted` = 0 where order_id = '$order_id' and sid = '$sid'");
                            $db->execute();
                        }
                    }
                }
            }
		}

		$db->setQuery("Delete from #__app_sch_temp_order_items where id = '$itemid'");
		$db->execute();


		$today = date("d-m-Y" ,HelperOSappscheduleCommon::getRealTime());
		$date  = explode("-",$today);
		
		$select_day 		= $jinput->getInt('select_day',intval(date("d",HelperOSappscheduleCommon::getRealTime())));
		$select_month 		= $jinput->getInt('select_month',intval(date("m",HelperOSappscheduleCommon::getRealTime())));
		$select_year 		= $jinput->getInt('select_year',intval(date("Y",HelperOSappscheduleCommon::getRealTime())));
		if(intval($select_day) == 0)
		{
			$select_day = intval(date("d",HelperOSappscheduleCommon::getRealTime()));
		}
		if(intval($select_month) == 0)
		{
			$select_month   = intval(date("m",HelperOSappscheduleCommon::getRealTime()));
		}
		if(intval($select_year) == 0)
		{
			$select_year    = intval(date("Y",HelperOSappscheduleCommon::getRealTime()));
		}
		//$select_date		= strtotime()
		echo "1111";
		OsAppscheduleAjax::cart($userdata,$vid, $category_id,$employee_id,$date_from,$date_to);
		echo "@3333";
		echo "2222";
		//OsAppscheduleAjax::loadEmployee($sid,$eid,$date);
		OsAppscheduleAjax::prepareLoadServices($option,$select_year,$select_month,$select_day,$category_id,$employee_id,$vid,$sid,$eid,$count_services);
		exit();
	}

	static function removeAllItem($option)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		//$unique_cookie 		= $_COOKIE['unique_cookie'];
        $userdata           = "";
		$unique_cookie		= OSBHelper::getUniqueCookie();
		$sid 				= $jinput->getInt('sid');
		$start_time			= $jinput->getInt('start_time');
		$end_time			= $jinput->getInt('end_time');
		$eid				= $jinput->getInt('eid');
		$category_id		= $jinput->getInt('category_id',0);
		$employee_id		= $jinput->getInt('employee_id',0);
		$vid				= $jinput->getInt('vid',0);
		$date_from		 	= OSBHelper::getStringValue('date_from','');
		$date_to			= OSBHelper::getStringValue('date_to','');
		$count_services     = $jinput->getInt('count_services',0);
		
		$db = JFactory::getDbo();
		$db->setQuery("Select id from #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
		$order_id = $db->loadResult();

		//get Information of this item
		$db->setQuery("Delete from #__app_sch_temp_order_items where order_id = '$order_id'");
		$db->execute();


		$today = date("d-m-Y" ,HelperOSappscheduleCommon::getRealTime());
		$date  = explode("-",$today);
		
		$select_day 		= $jinput->getInt('select_day',intval(date("d",HelperOSappscheduleCommon::getRealTime())));
		$select_month 		= $jinput->getInt('select_month',intval(date("m",HelperOSappscheduleCommon::getRealTime())));
		$select_year 		= $jinput->getInt('select_year',intval(date("Y",HelperOSappscheduleCommon::getRealTime())));
		if(intval($select_day) == 0)
		{
			$select_day = intval(date("d",HelperOSappscheduleCommon::getRealTime()));
		}
		if(intval($select_month) == 0)
		{
			$select_month   = intval(date("m",HelperOSappscheduleCommon::getRealTime()));
		}
		if(intval($select_year) == 0)
		{
			$select_year    = intval(date("Y",HelperOSappscheduleCommon::getRealTime()));
		}
		//$select_date		= strtotime()
		echo "1111";
		OsAppscheduleAjax::cart($userdata,$vid, $category_id,$employee_id,$date_from,$date_to);
		echo "@3333";
		echo "2222";
		//OsAppscheduleAjax::loadEmployee($sid,$eid,$date);
		OsAppscheduleAjax::prepareLoadServices($option,$select_year,$select_month,$select_day,$category_id,$employee_id,$vid,$sid,$eid,$count_services);
		exit();
	}
	

	static function checkCart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to){
		global $mainframe,$mapClass,$configClass,$lang_suffix,$jinput;
		$db = JFactory::getDbo();
		$unique_cookie			= OSBHelper::getUniqueCookie();//$_COOKIE['unique_cookie'];
		//echo $unique_cookie;
		$task = $jinput->get('task','','string');
		if($unique_cookie != ""){
			$db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
			$count_order = $db->loadResult();
			if($count_order > 0){
				$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
				$order_id = $db->loadResult();
				$db->setQuery("SELECT * FROM #__app_sch_temp_order_items WHERE order_id = '$order_id' order by booking_date");
				$rows = $db->loadObjectList();
				if(count($rows) > 0){
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Cart static function
	 *
	 */
	static function cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to)
	{
		global $mainframe,$mapClass,$configClass,$lang_suffix,$jinput,$languages;
		$languages  = OSBHelper::getLanguages();
		$db			= JFactory::getDbo();
		$user		= JFactory::getUser();
		$config		= new JConfig();
		$offset		= $config->offset;
		date_default_timezone_set($offset);
		$unique_cookie			= OSBHelper::getUniqueCookie();
		//echo $unique_cookie;
		$task = OSBHelper::getStringValue('task','');
		$sid		= $jinput->getInt('sid',0);
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		//echo $task;
        if($unique_cookie != "")
		{
			if($translatable)
			{
				$params = JComponentHelper::getParams('com_languages');
				$defaultLanguage = $params->get('site', 'en-GB');

				$lang = JFactory::getLanguage();
				$tag = $lang->getTag();
				if (!$tag) {
					$tag = 'en-GB';
				}
				
				if($tag == $defaultLanguage)
				{
					if((int)$configClass['checkout_itemid'] > 0)
					{
						$itemid = $configClass['checkout_itemid'];
					}
					else
					{
						$itemid = $jinput->getInt('Itemid');
					}
				}
				else
				{
					$prefix_language = substr($tag, 0, 2);
					if((int)$configClass['checkout_itemid_'.$prefix_language] > 0)
					{
						$itemid = $configClass['checkout_itemid_'.$prefix_language];
					}
					else
					{
						$itemid = $jinput->getInt('Itemid');
					}
				}
			}
			else
			{
				if((int)$configClass['checkout_itemid'] > 0)
				{
					$itemid = $configClass['checkout_itemid'];
				}
				else
				{
					$itemid = $jinput->getInt('Itemid');
				}
			}

            $db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
            $count_order = $db->loadResult();
            if($count_order > 0)
			{
                $db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
                $order_id = $db->loadResult();
                $db->setQuery("SELECT * FROM #__app_sch_temp_order_items WHERE order_id = '$order_id' order by booking_date");
                $rows = $db->loadObjectList();
                if(count($rows) > 0)
				{
                    $total = 0;
                    ?>
                    <table width="100%" id="osbcarttable">
                        <?php if ($task  != "form_step2")
						{
						?>
                        <tr>
                            <td style="" class="removeall">
                                 <?php echo JText::_('OS_REMOVE_ALL');?>
								 <a href="javascript:removeAllItem(<?php echo $sid?>);" title="<?php echo JText::_('OS_REMOVE_ITEM');?>" class="applink">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
									  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
									</svg>
								 </a>
                            </td>
                        </tr>
                        <?php }?>
                    <?php
                    //print_r($userdata);
                    for($i1=0;$i1<count($rows);$i1++)
                    {
                        $row = $rows[$i1];
                        $sid = $row->sid;
                        if($sid > 0){
                            $field_amount = 0;
                            $field_data   = "";
                            $order_item_id		= $row->id;
                            $start_booking_date = $row->start_time;
                            $start_booking_date = $start_booking_date;
                            $end_booking_date   = $row->end_time;
                            $end_booking_date   = $end_booking_date;
                            $eid				= $row->eid;
                            $date_in_week		= date("N",$start_booking_date);
                            $db->setQuery("SELECT field_id FROM #__app_sch_temp_order_field_options WHERE order_item_id = '$order_item_id' GROUP BY field_id");
                            $fields = $db->loadObjectList();
                            //calculate option value and additional price
                            if(count($fields) > 0){
                                //prepare the field array
                                $fieldArr = array();
                                for($i=0;$i<count($fields);$i++)
								{
                                    $field = $fields[$i];
                                    if(!in_array($field->field_id,$fieldArr)){
                                        $fieldArr[count($fieldArr)] = $field->field_id;
                                    }
                                }
                                for($i=0;$i<count($fieldArr);$i++)
								{
                                    $fieldid = $fieldArr[$i];
                                    $db->setQuery("Select id,field_label$lang_suffix as field_label,field_type from #__app_sch_fields where id = '$fieldid'");
                                    $field = $db->loadObject();
                                    $field_type = $field->field_type;
                                    if($field_type == 1)
									{
                                        //get field value
                                        $db->setQuery("SELECT option_id FROM #__app_sch_temp_order_field_options WHERE order_item_id= '$order_item_id' and field_id = '$fieldid'");
                                        $fieldvalue = $db->loadResult();
                                        $db->setQuery("Select * from #__app_sch_field_options where id = '$fieldvalue'");
                                        $fieldOption = $db->loadObject();
                                        //if($fieldOption->additional_price > 0){
                                            $field_amount += $fieldOption->additional_price;
                                        //}

                                        $field_data .= "<strong>$field->field_label:</strong>: ".$fieldOption->field_option;
                                        if(($fieldOption->additional_price > 0) || ($fieldOption->additional_price < 0)){
                                            $field_data.= " - (".OSBHelper::showMoney($fieldOption->additional_price,0).")";
                                        }
                                        $field_data .= "<BR />";
                                    }
									elseif($field_type == 2)
									{
                                        $db->setQuery("SELECT option_id FROM #__app_sch_temp_order_field_options WHERE order_item_id= '$order_item_id' and field_id = '$fieldid'");
                                        $fieldValueArr = $db->loadObjectList();
                                        if(count($fieldValueArr) > 0)
										{
                                            $fieldValue = array();
                                            for($j=0;$j<count($fieldValueArr);$j++)
											{
                                                $fieldValue[$j] = $fieldValueArr[$j]->option_id;
                                            }
                                        }
                                        if(count($fieldValue) > 0){
                                            $field_data .= "<strong>$field->field_label:</strong>: ";
                                            for($j=0;$j<count($fieldValue);$j++){
                                                $temp = $fieldValue[$j];
                                                $db->setQuery("Select * from #__app_sch_field_options where id = '$temp'");
                                                $fieldOption = $db->loadObject();
                                                //if($fieldOption->additional_price > 0){
                                                    $field_amount += $fieldOption->additional_price;
                                                //}
                                                $field_data .= OSBHelper::getLanguageFieldValue($fieldOption,'field_option');
                                                if(($fieldOption->additional_price > 0) || ($fieldOption->additional_price < 0)){
                                                    $field_data.= " - (".OSBHelper::showMoney($fieldOption->additional_price,0).")";
                                                }
                                                $field_data .= ",";
                                            }
                                            $field_data = substr($field_data,0,strlen($field_data)-1);
                                            $field_data .= "<BR />";
                                        }
                                    }
                                }
                            }

                            $db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
                            $service = $db->loadObject();

                            $db->setQuery("Select a.*,b.additional_price from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.id = '$eid' and b.service_id = '$sid'");
                            $employee = $db->loadObject();

                            //get extra cost
                            $db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '$date_in_week' or week_date = '0')");
                            //echo $db->getQuery();
                            $extras = $db->loadObjectList();
                            $extra_cost = 0;
                            if(count($extras) > 0)
							{
                                for($j=0;$j<count($extras);$j++)
								{
                                    $extra = $extras[$j];
                                    $stime = $extra->start_time;
                                    $etime = $extra->end_time;
                                    $stime = date("Y-m-d",$start_booking_date)." ".$stime.":00";
                                    $etime = date("Y-m-d",$start_booking_date)." ".$etime.":00";
                                    $stime = strtotime($stime);
                                    $etime = strtotime($etime);
                                    if(($start_booking_date >= $stime) and ($start_booking_date < $etime))
									{
                                        $extra_cost += $extra->extra_cost;
                                    }
                                }
                            }
                            //echo $extra_cost;
                            ?>
                            <tr>
                                <td width="100%" class="cartitem" style="">
                                    <table width="100%">
                                        <tr>
                                            <td width="100%">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="100%" align="left" class="tdcart" colspan="4">
                                                            <a href="javascript:openDiv(<?php echo $i1;?>)" title="<?php echo JText::_('OS_CLICK_FOR_MORE_DETAILS');?>" id="href_<?php echo $i1?>">
                                                            [+]
                                                            </a>
                                                            <strong>
                                                                <?php echo OSBHelper::getLanguageFieldValue($service,'service_name');?>
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="25%" align="left" class="tdcart">
                                                            <?php
                                                                echo date($configClass['date_format'],$start_booking_date);
                                                            ?>
                                                        </td>
                                                        <td width="20%" align="left" class="tdcart">
                                                            <?php
                                                                echo date($configClass['time_format'],$start_booking_date);
                                                            ?>
                                                        </td>
                                                        <td width="20%" align="left" class="tdcart">
                                                            <?php
                                                                echo date($configClass['time_format'],$end_booking_date);
                                                            ?>
                                                        </td>
                                                        <?php
                                                        if($configClass['disable_payments'] == 1)
                                                        {
                                                        ?>
                                                        <td width="25%" align="left" class="tdcart">
                                                            <?php
																if($row->total_cost < 0)
																{
																	$row->total_cost = 0;
																}
                                                                echo OSBHelper::showMoney($row->total_cost,0);
                                                                if($service->service_time_type == 1)
                                                                {
                                                                    echo "(".$row->nslots." ".JText::_('OS_NSLOTS').")";
                                                                    $nslot = $row->nslots;
                                                                }else{
                                                                    $nslot = 1;
                                                                }
                                                                $total += $row->total_cost;
                                                            ?>
                                                        </td>
                                                        <?php
                                                        }
														else
														{
														?>
															<td width="25%" align="left" class="tdcart">
																<?php
																if($service->service_time_type == 1)
                                                                {
                                                                    echo "(".(int)$row->nslots." ".JText::_('OS_NSLOTS').")";
                                                                    $nslot = $row->nslots;
                                                                }
																?>
															</td>
														<?php
														}
                                                        ?>
                                                        <?php if ($task  != "form_step2"){?>
                                                        <td width="5%" align="center" style="padding:0px;">
                                                            <a href="javascript:removeItem(<?php echo $order_item_id?>,<?php echo $sid?>,<?php echo $start_booking_date?>,<?php echo $end_booking_date?>,<?php echo $eid?>);" title="<?php echo JText::_('OS_REMOVE_ITEM');?>" class="applink">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
																  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
																</svg>
                                                            </a>
                                                        </td>
                                                        <?php }?>
                                                    </tr>
                                                    <?php
                                                    $user = JFactory::getUser();
                                                    if(($configClass['allow_multiple_timezones'] == 1) and ($user->id > 0) and (OSBHelper::getConfigTimeZone() != OSBHelper::getUserTimeZone())){
                                                        ?>
                                                        <tr>
                                                            <td width="25%" align="left" class="tdcart1">
                                                                <?php
                                                                $usertimezone =  OSBHelper::getUserTimeZone();
                                                                $usertimezone = explode("/",$usertimezone);
                                                                echo $usertimezone[0].'/';
                                                                echo "<BR />";
                                                                echo $usertimezone[1];
                                                                ?>

                                                            </td>
                                                            <td width="20%" align="left" class="tdcart1" valign="top">
                                                                <?php
                                                                    echo date($configClass['time_format'],OSBHelper::convertTimezone($start_booking_date));
                                                                ?>
                                                            </td>
                                                            <td width="20%" align="left" class="tdcart1" valign="top">
                                                                <?php
                                                                    echo date($configClass['time_format'],OSBHelper::convertTimezone($end_booking_date));
                                                                ?>
                                                            </td>
                                                            <?php
                                                            if($configClass['disable_payments'] == 1){
                                                            ?>
                                                            <td width="25%" align="left" class="tdcart">

                                                            </td>
                                                            <?php
                                                            }
                                                            ?>
                                                            <td width="5%" align="center" style="padding:0px;">
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="tdcart2">
                                    <div style="padding:2px;border-bottom:1px dotted #D0C5C5 !important;display:none;" id="cartdetails_<?php echo $i1?>"  >
                                    <?php echo JText::_('OS_EMPLOYEE')?>:
                                    <strong>
                                    <?php
                                    echo $employee->employee_name
                                    ?>
                                    </strong>
                                    <br />
                                    <?php
                                    echo $field_data;
                                    ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    if($configClass['disable_payments'] == 1){
                    ?>
                    <tr>
                        <td align="right" style="padding-top:5px;text-align:right;">
                            <strong><?php echo JText::_('OS_AMOUNT')?>:</strong>
                             <?php
								if($total < 0)
								{
									$total = 0;
								}
                                echo OSBHelper::showMoney($total,1);
                             ?>
                        </td>
                    </tr>
                    <?php
                    }
                    if($total > 0 && OSBHelper::isAnyAvailableGroupDiscount($total) && $user->id > 0 )
                    {
                        $discount = OSBHelper::getGroupDiscountAmount();
                        $discount_amount = $discount->discount;
                        $discount_type = $discount->discount_type;
                        if($discount_type == 0)
                        {
                            $discount_value = $total*$discount_amount/100;
                        }
                        else
                        {
                            $discount_value = $discount_amount;
                        }
                        ?>
                        <tr>
                            <td align="right" class="discount_td" style="text-align:right;">
                                <strong><?php echo JText::_('OS_DISCOUNT_BY_GROUP')?>:</strong>
                                 <?php
                                    echo OSBHelper::showMoney($discount_value,1);
                                 ?>
                            </td>
                        </tr>
                        <?php
                        $total -= $discount_value;
                        if($total < 0){
                            $total = 0;
                        }
                    }
                    ?>
                    <?php
                    if($configClass['disable_payments'] == 1 && $configClass['show_tax_in_cart'] == 1 && $configClass['enable_tax'] == 1 && $configClass['tax_payment'] > 0){
                        $tax_amount = $total*$configClass['tax_payment']/100;
                        $total += $tax_amount;
                        ?>
                        <tr>
                            <td align="right" style="padding-top:5px;text-align:right;">
                                <strong><?php echo JText::_('OS_TAX')?>:</strong>
                                 <?php
                                    echo OSBHelper::showMoney($tax_amount,1);
                                 ?>
                            </td>
                        </tr>
                        <?php
                    }
                    if($configClass['disable_payments'] == 1){
                    ?>
                    <tr>
                        <td align="right" style="padding-top:5px;text-align:right;">
                            <strong><?php echo JText::_('OS_GROSS_AMOUNT')?>:</strong>
                             <?php
								if($total < 0)
								{
									$total = 0;
								}
                                echo OSBHelper::showMoney($total,1);
                             ?>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td align="left" style="padding-top:5px;">
                            <a href="<?php echo Jroute::_("index.php?option=com_osservicesbooking&task=form_step1&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$date_from."&date_to=".$date_to."&Itemid=".$itemid);?>" title="<?php echo JText::_('OS_CHECKOUT');?>" class="btn btn-primary" id="cartCheckoutBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
								  <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
								</svg>
                                <?php echo JText::_('OS_CHECKOUT');?>
                            </a>
                        </td>
                    </tr>
                    </table>
                    <?php
                }else{
                    echo JText::_('OS_YOUR_CART_IS_EMPTY');
                }
            }else{
                echo JText::_('OS_YOUR_CART_IS_EMPTY');
            }
        }else{
            echo JText::_('OS_YOUR_CART_IS_EMPTY');
        }
	}
	
	/**
	 * Cart in the top of main page
	 *
	 * @param unknown_type $userdata
	 */
	static function cart1($userdata){
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		//$userdata 			= $_COOKIE['userdata'];
		if(trim($userdata) != ""){
			$userdata			= explode("||",$userdata);
			$total = 0;
			?>
			<table  width="100%">
			<tr>
				<td width="45%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_SERVICE_NAME')?>
				</td>
				<td width="15%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_BOOKING_DATE')?>
				</td>
				<td width="10%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_START_TIME')?>
				</td>
				<td width="10%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_END_TIME')?>
				</td>
				<td width="10%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_PRICE')?>
				</td>
				<td width="10%" align="center" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_REMOVE')?>
				</td>
			</tr>
			<?php
			for($i=0;$i<count($userdata);$i++){
				$data = $userdata[$i];
				$data = explode("|",$data);
				$sid  = $data[0];
				if($sid > 0){
					$start_booking_date = $data[1];
					$end_booking_date   = $data[2];
					$eid				= $data[3];
					$add				= $data[4];
					$week_date			= date("N",$start_booking_date);
					$db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
					$service = $db->loadObject();
					
					$db->setQuery("Select a.*,b.additional_price from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.id = '$eid' and b.service_id = '$sid'");
					$employee = $db->loadObject();
					
					//get extra cost
					$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '$week_date' or week_date = '0')");
					//echo $db->getQuery();
					$extras = $db->loadObjectList();
					$extra_cost = 0;
					if(count($extras) > 0){
						for($j=0;$j<count($extras);$j++){
							$extra = $extras[$j];
							$stime = $extra->start_time;
							$etime = $extra->end_time;
							$stime = date("Y-m-d",$start_booking_date)." ".$stime.":00";
							$etime = date("Y-m-d",$start_booking_date)." ".$etime.":00";
							$stime = strtotime($stime);
							$etime = strtotime($etime);
							if(($start_booking_date >= $stime) and ($start_booking_date <= $etime)){
								$extra_cost += $extra->extra_cost;
							}
						}
					}
					?>
					<tr>
						<td width="45%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;">
							<strong><?php echo $service->service_name;?></strong>
						</td>
						<td width="15%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;">
							<?php
								echo intval(date("d",$start_booking_date))."/".intval(date("m",$start_booking_date))."/".intval(date("Y",$start_booking_date));
							?>
						</td>
						<td width="10%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;">
							<?php
								echo date("H:i",$start_booking_date);
							?>
						</td>
						<td width="10%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;">
							<?php
								echo date("H:i",$end_booking_date);
							?>
						</td>
						<td width="10%" align="left" style="font-size:11px;color:gray;border-bottom:1px dotted #D0C5C5 !important;">
							<?php
								echo OSBHelper::showMoney(OSBHelper::returnServicePrice($service->id,date("Y-m-d",$start_booking_date), 1, $employee->id, false, $start_booking_date) + $employee->additional_price + $extra_cost,0);
								$total += OSBHelper::returnServicePrice($service->id, date("Y-m-d",$start_booking_date), 1, $employee->id, false, $start_booking_date) + $employee->additional_price + $extra_cost;
							?>
						</td>
						<td width="10%" align="center" style="border-bottom:1px solid #D0C5C5 !important;">
							<a href="javascript:removeItem(<?php echo $sid?>,<?php echo $start_booking_date?>,<?php echo $end_booking_date?>,<?php echo $eid?>);" title="Remove item" class="applink">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
								  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
								</svg>
							</a>
						</td>
					</tr>
					<tr>
						<td colspan="6" style="padding:3px;background-color:#efefef;font-size:11px;">
							<?php echo JText::_('OS_EMPLOYEE')?>:
							<strong>
							<?php
							echo $employee->employee_name
							?>
							</strong>
							<br />
							<?php
							echo $add;
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>
			<tr>
				<td align="right" style="padding-top:5px;font-size:11px;color:gray;" colspan="6">
					<strong><?php echo JText::_('OS_TOTAL')?>:</strong>
					<?php
						echo OSBHelper::showMoney($total,1);
					 ?>
				</td>
			</tr>
			<tr>
			 	<td align="left" style="padding-top:5px;">
					<a href="javascript:showInforForm()">
						<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/continue.png">
					</a>
				</td>
			</tr>
			</table>
			<?php
		}else{
			echo JText::_('OS_YOUR_CART_IS_EMPTY');
		}
	}
	
	/**
	 * Show information form
	 *
	 * @param unknown_type $option
	 */
	static function showInforForm($option){
		global $mainframe,$mapClass,$configClass,$jinput;
		$db = JFactory::getDbo();
		$countryArr[] = JHTML::_('select.option','','');
		$db->setQuery("Select country_name as value, country_name as text from #__app_sch_countries order by country_name");
		$countries = $db->loadObjectList();
		$countryArr = array_merge($countryArr,$countries);
		$lists['country'] = JHTML::_('select.genericlist',$countryArr,'order_country','style="width:180px;" class="inputbox"','value','text');
		$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1' order by ordering");
		$fields = $db->loadObjectList();
		if($configClass['disable_payments']  == 1){
			$paymentMethod = $jinput->get('payment_method', os_payments::getDefautPaymentMethod(), 'string');
			if (!$paymentMethod)
			    $paymentMethod = os_payments::getDefautPaymentMethod();
			
			###############Payment Methods parameters###############################
		
			//Creditcard payment parameters		
			$x_card_num = $jinput->get('x_card_num', '', 'string');
			$expMonth =  $jinput->get('exp_month', date('m'), 'string') ;
			$expYear = $jinput->get('exp_year', date('Y'), 'string') ;
			$x_card_code = $jinput->get('x_card_code', '', 'string');
			$cardHolderName =$jinput->get('card_holder_name', '', 'string') ;
			$lists['exp_month'] = JHTML::_('select.integerlist', 1, 12, 1, 'exp_month', ' id="exp_month" ', $expMonth, '%02d') ;
			$currentYear = date('Y') ;
			$lists['exp_year'] = JHTML::_('select.integerlist', $currentYear, $currentYear + 10 , 1, 'exp_year', ' id="exp_year" ', $expYear) ;
			$options =  array() ;

			$cardTypes = explode(',', $configClass['enable_cardtypes']);
			if (in_array('Visa', $cardTypes)) {
				$options[] = JHTML::_('select.option', 'Visa', JText::_('OS_VISA_CARD')) ;			
			}
			if (in_array('MasterCard', $cardTypes)) {
				$options[] = JHTML::_('select.option', 'MasterCard', JText::_('OS_MASTER_CARD')) ;
			}
			
			if (in_array('Discover', $cardTypes)) {
				$options[] = JHTML::_('select.option', 'Discover', JText::_('OS_DISCOVER')) ;
			}		
			if (in_array('Amex', $cardTypes)) {
				$options[] = JHTML::_('select.option', 'Amex', JText::_('OS_AMEX')) ;
			}		
			$lists['card_type'] = JHTML::_('select.genericlist', $options, 'card_type', ' class="inputbox" ', 'value', 'text') ;
			//Echeck
					
			$x_bank_aba_code = $jinput->get('x_bank_aba_code', '', 'string') ;
			$x_bank_acct_num = $jinput->get('x_bank_acct_num', '', 'string') ;
			$x_bank_name = $jinput->get('x_bank_name', '', 'string') ;
			$x_bank_acct_name = $jinput->get('x_bank_acct_name', '', 'string') ;
			$options = array() ;
			$options[] = JHTML::_('select.option', 'CHECKING', JText::_('OS_BANK_TYPE_CHECKING')) ;
			$options[] = JHTML::_('select.option', 'BUSINESSCHECKING', JText::_('OS_BANK_TYPE_BUSINESSCHECKING')) ;
			$options[] = JHTML::_('select.option', 'SAVINGS', JText::_('OS_BANK_TYPE_SAVING')) ;
			$lists['x_bank_acct_type'] = JHTML::_('select.genericlist', $options, 'x_bank_acct_type', ' class="inputbox" ', 'value', 'text', $jinput->get('x_bank_acct_type','','string')) ;
			
			$methods = os_payments::getPaymentMethods(true, false) ;
			
			$lists['x_card_num'] = $x_card_num;
			$lists['x_card_code'] = $x_card_code;
			$lists['cardHolderName'] = $cardHolderName;
			$lists['x_bank_acct_num'] = $x_bank_acct_num;
			$lists['x_bank_acct_name'] = $x_bank_acct_name;
			$lists['methods'] = $methods;
			$lists['idealEnabled'] = 0;
			$lists['paymentMethod'] = $paymentMethod;
		}
		HTML_OsAppscheduleAjax::showInforFormHTML($option,$lists,$fields);
	}
	
	
	/**
	 * Confirm information
	 *
	 * @param unknown_type $option
	 */
	static function confirmInforForm($option){
		global $mainframe,$mapClass,$configClass,$jinput;
		$db = JFactory::getDbo();
		$userdata = $_COOKIE['userdata'];
		$tax = $configClass['tax_payment'];
		$total = 0;
		$total = OsAppscheduleAjax::getOrderCost($userdata);
		$fieldObj = array();
		$fields = $jinput->get('fields','','string');
		$fieldArr = explode("||",$fields);
		if(count($fieldArr) > 0){
			$field_amount = 0;
			for($i=0;$i<count($fieldArr);$i++){
				$field_data = "";
				$field  = $fieldArr[$i];
				$fArr   = explode("|",$field);
				$fid    = $fArr[0];
				$fvalue = $fArr[1];
				$fvalue = str_replace("(@)","&",$fvalue);
				$db->setQuery("Select * from #__app_sch_fields where id = '$fid'");
				$field 	= $db->loadObject();
				$field_type = $field->field_type;
				if($field_type == 1){
					$db->setQuery("Select * from #__app_sch_field_options where id = '$fvalue'");
					$fieldOption = $db->loadObject();
					//if($fieldOption->additional_price > 0){
						$field_amount += $fieldOption->additional_price;
					//}
					$field_data .= $fieldOption->field_option;
					if(($fieldOption->additional_price > 0) || ($fieldOption->additional_price < 0)){
						$field_data.= " - ".$fieldOption->additional_price." ".$configClass['currency_format'];
					}
				}elseif($field_type == 2){
					$fieldValueArr = explode(",",$fvalue);
					if(count($fieldValueArr) > 0){
						for($j=0;$j<count($fieldValueArr);$j++){
							$temp = $fieldValueArr[$j];
							$db->setQuery("Select * from #__app_sch_field_options where id = '$temp'");
							$fieldOption = $db->loadObject();
							//if($fieldOption->additional_price > 0){
								$field_amount += $fieldOption->additional_price;
							//}
							$field_data .= $fieldOption->field_option;
							if(($fieldOption->additional_price > 0) || ($fieldOption->additional_price < 0)){
								$field_data.= " - ".$fieldOption->additional_price." ".$configClass['currency_format'];
							}
							$field_data .= ",";
						}
						$field_data = substr($field_data,0,strlen($field_data)-1);
					}
				}
				
				$count	= count($fieldObj);
				$fieldObj[$count]->field = $field;
				$fieldObj[$count]->fvalue = $field_data;
				$fieldObj[$count]->fieldoptions = $fvalue;
			}
		}
		$total += $field_amount;
		
		if($configClass['disable_payments'] == 1){
			$select_payment 	= $jinput->get('select_payment','','string');
			if($select_payment !=  ""){
				$method = os_payments::getPaymentMethod($select_payment) ;
				$x_card_num			= $jinput->get('x_card_num','','string');
				$x_card_code		= $jinput->get('x_card_code','','string');
				$card_holder_name	= $jinput->get('card_holder_name','','string');
				$exp_year			= $jinput->get('exp_year','','string');
				$exp_month			= $jinput->get('exp_month','','string');
				$card_type			= $jinput->get('card_type','','string');
				$lists['method'] 			= $method;
				$lists['x_card_num'] 		= $x_card_num;
				$lists['x_card_code'] 		= $x_card_code;
				$lists['card_holder_name'] 	= $card_holder_name;
				$lists['exp_year'] 			= $exp_year;
				$lists['exp_month'] 		= $exp_month;
				$lists['card_type'] 		= $card_type;
				$lists['select_payment']	= $select_payment;
			}
		}
		HTML_OsAppscheduleAjax::confirmInforFormHTML($option,$total,$fieldObj,$lists);
	}

	static function isAnyItemsInCart(){
		global $jinput;
		$db = JFactory::getDbo();
		$unique_cookie = OSBHelper::getUniqueCookie();//$_COOKIE['unique_cookie'];
		$db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
		$count_order = $db->loadResult();
		
		if($count_order == 0){
			$unique_cookie = $jinput->get('unique_cookie','','string');
			$db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
			$count_order = $db->loadResult();
		}
		
		if($count_order > 0){
			$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
			$order_id = $db->loadResult();
			$db->setQuery("SELECT * FROM #__app_sch_temp_order_items WHERE order_id = '$order_id' order by booking_date");
			$rows = $db->loadObjectList();
		}
		if(count($rows) > 0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Get order cost
	 *
	 * @param unknown_type $userdata
	 */
	static function getOrderCost(){
		global $mainframe,$mapClass,$jinput;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$total = 0;
		//$unique_cookie = $_COOKIE['unique_cookie'];
		$unique_cookie = OSBHelper::getUniqueCookie();
		$db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
		
		$count_order = $db->loadResult();
		if($count_order == 0){
			$unique_cookie = $jinput->get('unique_cookie','','string');
			$db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
			$count_order = $db->loadResult();
		}
		if($count_order > 0){
			
			$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
			$order_id = $db->loadResult();
			$db->setQuery("SELECT * FROM #__app_sch_temp_order_items WHERE order_id = '$order_id' order by booking_date");
			$rows = $db->loadObjectList();
			
			for($i1=0;$i1<count($rows);$i1++)
			{
			    $field_amount		= 0;
				$row				= $rows[$i1];
				$order_item_id		= $row->id;
				$sid				= $row->sid;
				$eid				= $row->eid;
				$start_booking_date = $row->start_time;
				$week_date			= date("N",$start_booking_date);
				//get extra cost				
				$db->setQuery("Select additional_price from #__app_sch_employee_service where employee_id = '$eid' and service_id = '$sid'");
				$additional_price	= $db->loadResult();
				$db->setQuery("Select service_price,service_time_type from #__app_sch_services where id = '$sid'");
				$service			= $db->loadObject();
				$service_price		= OSBHelper::returnServicePrice($sid,date("Y-m-d",$start_booking_date),$row->nslots, $eid, false, $start_booking_date);
				//$service_price+= $extra_cost;
				$service_time_type	= $service->service_time_type;
				if($service_time_type == 1)
				{
					$service_price	= $service_price*$row->nslots;
				}
				
				//get extra cost
				
				$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid' and (week_date = '$week_date' or week_date = '0')");
				//echo $db->getQuery();
				$extras = $db->loadObjectList();
				$extra_cost = 0;
				if(count($extras) > 0){
					for($j=0;$j<count($extras);$j++){
						$extra = $extras[$j];
						$stime = $extra->start_time;
						$etime = $extra->end_time;
						$stime = date("Y-m-d",$start_booking_date)." ".$stime.":00";
						$etime = date("Y-m-d",$start_booking_date)." ".$etime.":00";
						$stime = strtotime($stime);
						$etime = strtotime($etime);
						if(($start_booking_date >= $stime) and ($start_booking_date <= $etime)){
							$extra_cost += $extra->extra_cost;
						}
					}
				}
				
				//$add				= $data[4];
				//calculate option value and additional price
				$db->setQuery("SELECT field_id FROM #__app_sch_temp_order_field_options WHERE order_item_id = '$order_item_id' GROUP BY field_id");
				$fields = $db->loadObjectList();
				//calculate option value and additional price
				if(count($fields) > 0){
					for($k=0;$k<count($fields);$k++){
						$field = $fields[$k];
						$fieldid = $field->field_id;
						$db->setQuery("Select id,field_type from #__app_sch_fields where  id = '$fieldid'");
						$field = $db->loadObject();
						$field_type = $field->field_type;
						if($field_type == 1){
							$db->setQuery("Select a.additional_price from #__app_sch_field_options as a inner join #__app_sch_temp_order_field_options as b on a.id = b.option_id where b.field_id = '$fieldid' and b.order_item_id = '$order_item_id'");
							$additional_price_fields = $db->loadResult();
							
							//if($additional_price_fields > 0){
								$field_amount += $additional_price_fields*$row->nslots;
							//}
						}elseif($field_type == 2){
							$db->setQuery("Select option_id from #__app_sch_temp_order_field_options where order_item_id = '$order_item_id' and field_id = '$fieldid'");
							$optionids = $db->loadObjectList();
							if(count($optionids) > 0){
								for($j=0;$j<count($optionids);$j++){
									$temp = $optionids[$j]->option_id;
									$db->setQuery("Select additional_price from #__app_sch_field_options where id = '$temp'");
									$additional_price_fields = $db->loadResult();
									//if($additional_price_fields > 0){
										$field_amount += $additional_price_fields*$row->nslots;
									//}
								}
							}
						}
					}
				}
				$total += $service_price + $additional_price + $extra_cost +$field_amount;
			}
		}
		return $total;
	}

	static function getOrderCostUsingTotalCostInTempOrderItem(){
		global $mainframe,$mapClass,$jinput;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$total = 0;
		//$unique_cookie = $_COOKIE['unique_cookie'];
		$unique_cookie = OSBHelper::getUniqueCookie();
		$db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");

		$count_order = $db->loadResult();
		if($count_order == 0){
			$unique_cookie = $jinput->get('unique_cookie','','string');
			$db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
			$count_order = $db->loadResult();
		}
		if($count_order > 0){

			$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
			$order_id = $db->loadResult();
			$db->setQuery("SELECT * FROM #__app_sch_temp_order_items WHERE order_id = '$order_id' order by booking_date");
			$rows = $db->loadObjectList();

			for($i1=0;$i1<count($rows);$i1++)
			{
				$total += $rows[$i1]->total_cost;
			}
		}
		return $total;
	}
	
	/**
	 * Captcha generetor
	 *
	 * @param unknown_type $option
	 */
	static function captcha($option){
		global $mainframe,$mapClass,$jinput;
		while (@ob_end_clean());
		$ResultStr = $jinput->get('resultStr','','string');
		$NewImage =imagecreatefromjpeg(JPATH_ROOT."/media/com_osservicesbooking/assets/css/images/img.jpg");//image create by existing image and as back ground 
		$LineColor = imagecolorallocate($NewImage,233,239,239);//line color 
		$TextColor = imagecolorallocate($NewImage, 255, 255, 255);//text color-white
		imageline($NewImage,1,1,40,40,$LineColor);//create line 1 on image 
		imageline($NewImage,1,100,60,0,$LineColor);//create line 2 on image 
		imagestring($NewImage, 5, 20, 10, $ResultStr, $TextColor);// Draw a random string horizontally 
		header("Content-type: image/jpeg");// out out the image 
		imagejpeg($NewImage);//Output image to browser 
		exit();
	}
	
	static function updatenSlots($option){
		global $mainframe,$mapClass,$jinput;
		$db = JFactory::getDbo();
		//$unique_cookie = $_COOKIE['unique_cookie'];
		$unique_cookie = OSBHelper::getUniqueCookie();
		$sid = $jinput->getInt('sid',0);
		$eid = $jinput->getInt('eid',0);
		$start_time =$jinput->getInt('start_time',0);
		$end_time = $jinput->getInt('end_time',0);
		$newvalue = $jinput->getInt('newvalue',0);
		$db->setQuery("UPDATE #__app_sch_temp_temp_order_items SET nslots = '$newvalue' WHERE unique_cookie LIKE '$unique_cookie' AND sid = '$sid' AND eid = '$eid' AND start_time  = '$start_time' AND end_time = '$end_time'");
		$db->execute();
		exit();
	}
	
	/**
	 * remove time slots from cart
	 *
	 * @param unknown_type $option
	 */
	static function removeTemporarityTimeSlot($option){
		global $mainframe,$mapClass,$jinput;
		$db = JFactory::getDbo();
		$unique_cookie = OSBHelper::getUniqueCookie();
		$id = $jinput->getInt('id',0);
		if($id > 0)
		{
			$db->setQuery("SELECT * FROM #__app_sch_temp_temp_order_items where id = '$id'");
			$row = $db->loadObject();
			$db->setQuery("DELETE FROM #__app_sch_temp_temp_order_items WHERE id = '$id'");
			$db->execute();
		}
		OsAppscheduleAjax::checkingErrorinCart($row->sid,$row->eid);
		exit();
	}
	
	/**
	 * Check error in adding time slots to cart. 
	 * Call in the show error page and in ajax loading (from error page)
	 *
	 */
	static function checkingErrorinCart($sid,$eid)
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service = $db->loadObject();
		$service_time_type = $service->service_time_type;
		$unique_cookie = OSBHelper::getUniqueCookie();
		$db->setQuery("Select * from #__app_sch_temp_temp_order_items where unique_cookie like '$unique_cookie'");
		//echo $db->getQuery();
		$rows = $db->loadObjectList();
		if(count($rows) > 0)
		{
			$errorArr = array();
			if($service_time_type == 1)
			{
				for($i=0;$i<count($rows);$i++)
				{
					$row = $rows[$i];
					//check number of slots. 
					if(!HelperOSappscheduleCalendar::checkSlots($row))
					{
						$canbook = 0;
						$errorArr[count($errorArr)] = HelperOSappscheduleCalendar::returnSlots($row);
					}
				}
			}
			else
			{
				for($i=0;$i<count($rows);$i++)
				{
					$row = $rows[$i];
					//check number of slots. 
					if(!HelperOSappscheduleCalendar::checkSlots($row))
					{
						$canbook = 0;
						$errorArr[count($errorArr)] = $row;
					}
				}
			}
		}
		OSappscheduleInformation::showError($sid,$eid,$errorArr);
	}
	
	/**
	 * Remove rest date
	 *
	 */
	static function removerestdayAjax(){
		global $mainframe,$mapClass,$jinput;
		$day = $jinput->get('day','','string');
		$eid = $jinput->getInt('eid',0);
		$db  = JFactory::getDbo();
		$i   = $jinput->getInt('item',0);
		$db->setQuery("Select * from #__app_sch_employee_rest_days where eid = '$eid' AND rest_date <= '$day' and rest_date_to >= '$day'");
		$rows = $db->loadObjectList();
		$db->setQuery("DELETE FROM #__app_sch_employee_rest_days WHERE eid = '$eid' AND rest_date <= '$day' and rest_date_to >= '$day'");
		$db->execute();

		if(count($rows) > 0)
		{
			foreach($rows as $row)
			{
				$start_rest		= $row->rest_date;
				$start_restInt	= strtotime($start_rest);
				$end_rest		= $row->rest_date_to;
				$end_restInt	= strtotime($end_rest);
				$dayInt			= strtotime($day);
				$yesterdayInt	= $dayInt - 3600*24;
				$yesterday		= date("Y-m-d", $yesterdayInt);
				$tomorrowInt    = $dayInt + 3600*24;
				$tomorrow		= date("Y-m-d", $tomorrowInt);
				if($yesterdayInt >= $start_restInt)
				{
					$db->setQuery("INSERT INTO #__app_sch_employee_rest_days (id,eid,rest_date,rest_date_to) VALUES  (NULL,'$eid','$start_rest','$yesterday')");
					$db->execute();
				}
				if($end_restInt >= $start_restInt)
				{
					$db->setQuery("INSERT INTO #__app_sch_employee_rest_days (id,eid,rest_date,rest_date_to) VALUES  (NULL,'$eid','$tomorrow','$end_rest')");
					$db->execute();
				}
			}
			
		}
		
		OSBHelper::calendarItemAjax($i,$eid,$day);
		exit();
	}
	
	static function addrestdayAjax(){
		global $mainframe,$mapClass,$jinput;
		$db  = JFactory::getDbo();
		$day = $jinput->get('day','','string');
		$eid = $jinput->getInt('eid',0);
		$i   = $jinput->getInt('item',0);
		$db->setQuery("INSERT INTO #__app_sch_employee_rest_days (id,eid,rest_date,rest_date_to) VALUES  (NULL,'$eid','$day','$day')");
		$db->execute();
		OSBHelper::calendarItemAjax($i,$eid,$day);
		exit();
	}

	/**
	 * Remove order item
	 *
	 */
	static function removeOrderItem()
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$db			= JFactory::getDbo();
		$id			= $jinput->getInt('id',0);
		$db->setQuery("Select order_id from #__app_sch_order_items where id = '$id'");
		$order_id   = $db->loadResult();
		self::processRemovingOrderItem($id, $order_id);
		$mainframe->enqueueMessage(JText::_('OS_SERVICE_HAS_BEEN_REMOVED'));
		$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&view=employee&Itemid='.$jinput->getInt('Itemid')));
	}
	
	/**
	 * Remove order item
	 *
	 */
	static function removeOrderItemAjax()
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$db			= JFactory::getDbo();
		$order_id	= $jinput->getInt('order_id',0);
		$id			= $jinput->getInt('id',0);
		self::processRemovingOrderItem($id, $order_id);
		OsAppscheduleDefault::getListOrderServices($order_id);
		exit();
	}
	
	static function removeOrderItemAjaxCalendar()
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$db			= JFactory::getDbo();
		$i			= $jinput->getInt('i',0);
		$date		= OSBHelper::getStringValue('date','');
		$order_id	= $jinput->getInt('order_id',0);
		$id			= $jinput->getInt('id',0);
		self::processRemovingOrderItem($id, $order_id);
		OSBHelper::calendarCustomerItemAjax($i,$date);
		exit();
	}

	public static function processRemovingOrderItem($id, $orderId)
	{
		global $configClass;
		$user = JFactory::getUser();

		$db = JFactory::getDbo();
		if($configClass['waiting_list'] == 1)
		{
			OSBHelper::sendWaitingNotificationItem($id);
		}
		if($configClass['integrate_gcalendar'] == 1)
		{
			OSBHelper::removeOneEventOnGCalendar($id);
		}
		HelperOSappscheduleCommon::sendEmail('order_item_cancelled_to_administrator',$id);
		HelperOSappscheduleCommon::sendEmail('order_item_cancelled_to_employee',$id);
		HelperOSappscheduleCommon::sendEmail('order_item_cancelled_to_customer',$id);

		$db->setQuery("Select user_id from #__app_sch_orders where id = '$orderId'");
		$userId = (int) $db->loadResult();

		if($userId > 0 && $userId == $user->id)
		{

			$db->setQuery("Select eid from #__app_sch_order_items where id = '$id'");
			$eid		= $db->loadResult();
			HelperOSappscheduleCommon::sendEmployeeEmailRemoveOneOrderItem('employee_order_cancelled',$id, $orderId,$eid);
		}

		$db->setQuery("DELETE FROM #__app_sch_order_field_options WHERE order_item_id = '$id'");
		$db->execute();
		$db->setQuery("DELETE FROM #__app_sch_order_items WHERE id = '$id'");
		$db->execute();
	}
	
	/**
	 * Check coupon code
	 *
	 */
	static function checkCouponCode()
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$db                 = JFactory::getDbo();
		$nullDate           = $db->quote($db->getNullDate());
		//$current_date       = HelperOSappscheduleCommon::getRealTime();
		$current_date       = $db->quote(JFactory::getDate()->toSql());
		$coupon_code        = $jinput->get('coupon_code','','string');
		$total				= OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem();
		$db->setQuery('Select count(a.id) from #__app_sch_coupons as a where a.published = 1 AND `coupon_code` = "'.$coupon_code.'" AND discount_by = 0 AND (start_time = '.$nullDate.' or start_time <= '.$current_date.') AND (expiry_date = '.$nullDate.' or expiry_date >= '.$current_date.') and (minimum_cost = "0" or (minimum_cost > 0 and minimum_cost <= "'.$total.'")) and `access` IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');
		$isCorrectCoupon    = $db->loadResult();
		if($isCorrectCoupon > 0)
		{
			//get the coupon
			//$coupon = OSBHelper::getDiscount();
			$db->setQuery('Select a.* from #__app_sch_coupons as a where a.published = 1 AND `coupon_code` = "'.$coupon_code.'" AND discount_by = 0 AND (start_time = '.$nullDate.' or start_time <= '.$current_date.') AND (expiry_date = '.$nullDate.' or expiry_date >= '.$current_date.') and (minimum_cost = "0" or (minimum_cost > 0 and minimum_cost <= "'.$total.'")) and `access` IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');
			$coupon = $db->loadObject();
			if($coupon->max_total_use > 0)
			{
				$db->setQuery("Select count(id) from #__app_sch_coupon_used where coupon_id = '$coupon->id'");
				$nused = $db->loadResult();
				if($nused >= $coupon->max_total_use)
				{
					$useCoupon = 9999;
				}
			}
			if($useCoupon != 9999)
			{
				//check user
				$user = JFactory::getUser();
				$user_id = $user->id;
				if(($user_id > 0) and ($coupon->max_user_use > 0))
				{
					$db->setQuery("Select count(id) from #__app_sch_coupon_used where user_id = '$user_id' and coupon_id = '$coupon->id'");
					$alreadyUsedCoupon = $db->loadResult();
					if($alreadyUsedCoupon >= $coupon->max_user_use)
					{
						$useCoupon = 9999;
					}
					else
					{
						$useCoupon = $coupon->id;
					}
				}
				else
				{
					$useCoupon = $coupon->id;
				}
			}
		}
		else
		{
			$useCoupon = 0;
		}
		
		echo "@return@";
		if($useCoupon != 0 && $useCoupon != 9999)
		{
			if($coupon->discount == 100 && $coupon->discount_type == 0)
			{
				echo $useCoupon."XXX1||";
			}
			else
			{
				echo $useCoupon."XXX0||";
			}
			?>
			<span style="color:green;font-weight:bold;">
				<?php echo JText::_('OS_CONGRATULATION');?>, <?php echo JText::_('OS_YOU_GET_THE_DISCOUNT')?> [<?php echo $coupon->coupon_name;?>] <?php echo JText::_('OS_WITH');?> <?php echo $coupon->discount?> 
				<?php 
				if($coupon->discount_type == 0){
					echo " ".JText::_('OS_PERCENT')." (%)";
					echo " ".JText::_('OS_OF_TOTAL_AMOUNT');
				}else{
					echo " ".$configClass['currency_format'];
					echo " ".JText::_('OS_DISCOUNT');
				}
				?>
			</span>
			<?php
		}elseif($useCoupon == 9999){
			echo "9999||";
			?>
			<span style="color:red;font-weight:bold;">
			<?php
			echo JText::_('OS_YOU_CANNOT_USE_THIS_COUPON_CODE_AGAIN');
			?>
			</span>
			<?php
		}else{
			echo "0||";
			?>
			<input type="text" class="input-small search-query" value="" size="10" name="coupon_code" id="coupon_code" />
			<input type="button" class="btn" value="<?php echo JText::_('OS_CHECK_COUPON');?>" onclick="javascript:checkCoupon();"/>
			<div class="clearfix"></div>
			<span style="color:red;font-weight:bold;">
				<?php
				echo JText::_('OS_COUPON_CODE_IS_NOT_CORRECT');
				?>
			</span>
			<?php
		}
	}
	
	static function changeTimeSlotDate(){
		global $mainframe,$mapClass,$jinput;
		$db = JFactory::getDbo();
		$tstatus = $jinput->getInt('tstatus',0);
		$date	 = $jinput->getInt('date',0);
		$tid	 = $jinput->getInt('tid',0);
		$sid	 = $jinput->getInt('sid',0);
		if($tstatus == 0){
			$db->setQuery("Delete from #__app_sch_custom_time_slots_relation where time_slot_id = '$tid' and date_in_week = '$date'");
			$db->execute();
			?>
			<a href="javascript:changeTimeSlotDate(1,<?php echo $date?>,<?php echo $sid?>,<?php echo $tid?>,'<?php echo JUri::root();?>');" title="Select this day">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
				  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
				</svg>
			</a>
			<?php 
		}else{
			$db->setQuery("Insert into #__app_sch_custom_time_slots_relation (id,time_slot_id,date_in_week) values (NULL,'$tid','$date')");
			$db->execute();
			?>
			<a href="javascript:changeTimeSlotDate(0,<?php echo $date?>,<?php echo $sid?>,<?php echo $tid?>,'<?php echo JUri::root();?>');" title="Select this day">
				

				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
				  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
				</svg>
			</a>
			<?php 
		}
		exit();
	}


    static function loadCalendatDetails(){
        global $mainframe,$mapClass,$configClass,$jinput;
        $configClass    = OSBHelper::loadConfig();
        $month          = $jinput->getInt('month',0);
        $year           = $jinput->getInt('year',0);
        $category_id    = $jinput->getInt('category_id',0);
        $vid            = $jinput->getInt('vid',0);
        $sid            = $jinput->getInt('sid',0);
        $employee_id    = $jinput->getInt('employee_id',0);
        $date_from      = OSBHelper::getStringValue('date_from','');
        $date_to        = OSBHelper::getStringValue('date_to','');
        HelperOSappscheduleCalendar::initCalendar($year,$month,$category_id,$employee_id,$vid,$sid,$date_from,$date_to,1,1);
        exit();
    }

	static function checkingVersion(){
		global $mainframe;
		// Get the caching duration.
		$component     = JComponentHelper::getComponent('com_installer');
		$params        = $component->params;
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;
		// Get the minimum stability.
		$minimum_stability = $params->get('minimum_stability', JUpdater::STABILITY_STABLE, 'int');
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');
		/** @var InstallerModelUpdate $model */
		//$model = JModelLegacy::getInstance('Update', 'InstallerModel');
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			//$model = new \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel;
			$model = $mainframe->bootComponent('com_installer')->getMVCFactory()
				->createModel('Update', 'Administrator', ['ignore_request' => true]);
		}
		else
		{
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');
			/** @var InstallerModelUpdate $model */
			$model = JModelLegacy::getInstance('Update', 'InstallerModel');
		}
		$model->purge();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where('`type` = "package"')
			->where('`element` = "pkg_osservicesbooking"');
		$db->setQuery($query);
		$eid = (int) $db->loadResult();
		$result['status'] = 0;
		if ($eid)
		{
			$ret = JUpdater::getInstance()->findUpdates($eid, $cache_timeout, $minimum_stability);
			if ($ret)
			{
				$model->setState('list.start', 0);
				$model->setState('list.limit', 0);
				$model->setState('filter.extension_id', $eid);
				$updates          = $model->getItems();
				$result['status'] = 2;
				if (count($updates))
				{
					?>
					<div class="icon"><a href='http://joomdonation.com/joomla-extensions/joomla-services-appointment-booking.html' target='_blank' title='Update latest OS Services Booking version'>
					<img src="<?php echo JUri::root();?>administrator/components/com_osservicesbooking/asset/images/noupdated.png" />
				
					<?php
					echo '<span style="color:red;">'.JText::sprintf('OS_UPDATE_CHECKING_UPDATE_FOUND', $updates[0]->version).'</span>';
					echo '</a>';
					echo '</div>';
				}
				else
				{
					?>
					<div class="icon"><a href='http://joomdonation.com/joomla-extensions/joomla-services-appointment-booking.html' target='_blank' title='Update latest OS Services Booking version'>
					<img src="<?php echo JUri::root();?>administrator/components/com_osservicesbooking/asset/images/noupdated.png" />
				
					<?php
					echo '<span style="color:red;">'.JText::sprintf('OS_UPDATE_CHECKING_UPDATE_FOUND', null).'</span>';
					echo '</a>';
					echo '</div>';
				}
			}
			else
			{
				?>
				<div class="icon"><a href='#'>
				<img src="<?php echo JUri::root();?>administrator/components/com_osservicesbooking/asset/images/updated.png" />
				<?php
				echo '<span style="color:green;">'.JText::_('OS_UPDATE_CHECKING_UP_TO_DATE').'</span>';
				echo '</a>';
				echo '</div>';
			}
		}
		//echo json_encode($result);
		JFactory::getApplication()->close();
	}

	/*

	static function checkingVersion(){
		global $mainframe;
		$current_version = JRequest::getVar('current_version','');
		if (static function_exists('curl_init'))
		{
			$url = 'http://joomdonation.com/images/osservicesbooking/version.txt';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$latestVersion = curl_exec($ch);
			curl_close($ch);
			$latestVersion = trim($latestVersion);
			if ($latestVersion)
			{
				if (version_compare($latestVersion, $current_version, 'gt'))
				{
					?>
					<div class="icon"><a href='http://joomdonation.com/joomla-extensions/joomla-services-appointment-booking.html' target='_blank' title='Update latest OS Services Booking version'>
					<img src="<?php echo JUri::root();?>administrator/components/com_osservicesbooking/asset/images/noupdated.png" />
				
					<?php
					echo '<span style="color:red;">'.JText::sprintf('OS_UPDATE_CHECKING_UPDATE_FOUND', $latestVersion).'</span>';
					echo '</a>';
					echo '</div>';
				}
				else
				{
					?>
					<div class="icon"><a href='#'>
					<img src="<?php echo JUri::root();?>administrator/components/com_osservicesbooking/asset/images/updated.png" />
					<?php
					echo '<span style="color:green;">'.JText::_('OS_UPDATE_CHECKING_UP_TO_DATE').'</span>';
					echo '</a>';
					echo '</div>';
				}
			}
		}
		exit();
	}
	*/

    static function changeCheckinOrderItem(){
		global $jinput;
        $db = JFactory::getDbo();
        $order_id = $jinput->getInt('order_id',0);
        $id = $jinput->getInt('id',0);
        $db->setQuery("Select checked_in from #__app_sch_order_items where id = '$id' and order_id = '$order_id'");
        $checked_in = $db->loadResult();
        ?>
        <a href="javascript:changeCheckin(''<?php echo $order_id ?>','<?php echo $id ?>','<?php echo JURI::root() ;?>');" title="<?php echo JText::_('OS_CLICK_HERE_TO_CHANGE_CHECK_IN_STATUS'); ?>"/>
        <?php
        if($checked_in == 0){
            $db->setQuery("Update #__app_sch_order_items set checked_in = '1' where id = '$id'");
            $db->execute();
            ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
				  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
				</svg>
            <?php
        }else{
            $db->setQuery("Update #__app_sch_order_items set checked_in = '0' where id = '$id'");
            $db->execute();
            ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
				  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
				</svg>
            <?php
        }
        ?>
        </a>
        <?php
        exit();
    }

	/**
	 * Get profile data of the subscriber, using for json format
	 *
	 */
	public static function getprofiledata()
	{
		$config			= OSBHelper::loadConfig();
		$input			= JFactory::getApplication()->input;
		$userId			= $input->getInt('user_id', 0);
		$orderId		= $input->getInt('order_id',0);
		$data			= array();
		if ($userId > 0 && $orderId == 0)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->select('*')
				->from('#__app_sch_orders')
				->where('user_id="' . $userId .'" order by id desc limit 1');
			$db->setQuery($query);
			$rowProfile = $db->loadObject();
			$data = array();
			if($rowProfile->id > 0)
			{
				$data['order_name']		= $rowProfile->order_name;
				$data['order_email']	= $rowProfile->order_email;
				$data['order_phone']	= $rowProfile->order_phone;
				$data['dial_code']		= $rowProfile->dial_code;
				$data['order_state']	= $rowProfile->order_state;
				$data['order_city']		= $rowProfile->order_city;
				$data['order_zip']		= $rowProfile->order_zip;
				$data['order_address']	= $rowProfile->order_address;
				$data['order_country']	= $rowProfile->order_country;
			}
			else
			{
				$user = JFactory::getUser($userId);
				$data['order_name']		= $user->name;
				$data['order_email']	= $user->email;
			}
		}
		echo json_encode($data);
		JFactory::getApplication()->close();
	}

	static function getprofileemployee(){
		$config			= OSBHelper::loadConfig();
		$input			= JFactory::getApplication()->input;
		$userId			= $input->getInt('user_id', 0);
		$eid			= $input->getInt('eid',0);
		$data			= array();
		if ($userId > 0)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->select('*')
				->from('#__app_sch_employee')
				->where('user_id="' . $userId .'" order by id desc limit 1');
			$db->setQuery($query);
			$rowProfile = $db->loadObject();
			$data = array();
			if($rowProfile->id > 0)
			{
				$data['employee_name']	= $rowProfile->employee_name;
				$data['employee_email']	= $rowProfile->employee_email;
			}else{
				$user = JFactory::getUser($userId);
				$data['employee_name']		= $user->name;
				$data['employee_email']	    = $user->email;
			}
		}
		echo json_encode($data);
		JFactory::getApplication()->close();
	}

	static function generateSearchmodule()
	{
		global $jinput;
		$db				= JFactory::getDbo();
		$jinput			= JFactory::getApplication()->input;
		$category_id	= $jinput->getInt('category_id',0);
		$vid			= $jinput->getInt('vid',0);
		$sid			= $jinput->getInt('sid',0);
		$employee_id	= $jinput->getInt('employee_id',0);
		$show_category  = $jinput->getInt('show_category',0);
		$show_employee  = $jinput->getInt('show_employee',0);
		$show_service   = $jinput->getInt('show_service',0);
		$show_date		= $jinput->getInt('show_date',0);
		$show_venue		= $jinput->getInt('show_venue',0);
		$layout			= $jinput->getInt('layout',0);
		$lists['show_category'] = $show_category;
		$lists['show_employee'] = $show_employee;
		$lists['show_service']	= $show_service;
		$lists['show_date']		= $show_date;
		$lists['show_venue']	= $show_venue;


		$query	= $db->getQuery(true);
		//Services
		$query->select('a.id as value,a.service_name as text');
		$query->from($db->quoteName('#__app_sch_services').' AS a');
		$query->where("a.published = '1'");
		if($vid > 0){
			$query->where("a.id in (Select sid from #__app_sch_venue_services where vid = '$vid')");
		}
		if($category_id > 0){
		    $query->where("a.category_id = '$category_id'");
		}
		$query->order($db->escape('a.ordering'));
		$db->setQuery($query);
		//echo $db->getQuery();
		$services = $db->loadObjectList();
		$optionArr[] = JHTML::_('select.option','',Jtext::_('OS_SELECT_SERVICE'));
		$optionArr = array_merge($optionArr,$services);
		$lists['service'] = JHtml::_('select.genericlist',$optionArr,'sid','class="input-large form-control form-select" onChange="javascript:updateSearchForm('.$layout.');"','value','text',$sid);

		//venue
		$query = "Select a.id as value, concat(a.address,',',a.city,',',a.state) as text from #__app_sch_venues as a inner join #__app_sch_venue_services as b on b.vid = a.id where a.published = '1'";
		if($sid > 0)
		{
			$query .= " and b.sid = '$sid'";
		}
		$query .= " group by a.id order by a.address";
		$db->setQuery($query);
		//echo $db->getQuery();
		$venues = $db->loadObjectList();
		$optionArr = array();
		$optionArr[] = JHTML::_('select.option','',Jtext::_('OS_SELECT_VENUE'));
		$optionArr = array_merge($optionArr,$venues);
		$lists['venue'] = JHTML::_('select.genericlist',$optionArr,'vid','class="input-large form-control form-select" onChange="javascript:updateSearchForm('.$layout.');"','value','text',$vid);

		//employee
		$query = $db->getQuery(true);
		$query->select('id as value, employee_name as text');
		$query->from('#__app_sch_employee');
		$query->where("published = '1'");
		if($category_id > 0){
		    $query->where("id in (Select employee_id from #__app_sch_employee_service where service_id in (Select id from #__app_sch_services where category_id = '$category_id'))");
		}
		if($sid > 0){
			$query->where("id in (Select employee_id from #__app_sch_employee_service where service_id = '$sid')");
		}
		if($vid > 0){
			$query->where("id in (Select employee_id from #__app_sch_employee_service where vid = '$vid')");
		}
		$query->order('employee_name');
		$db->setQuery($query);
		$employees = $db->loadObjectList();
		$optionArr = array();
		$optionArr[] = JHTML::_('select.option','',Jtext::_('OS_SELECT_EMPLOYEE'));
		$optionArr = array_merge($optionArr,$employees);
		$lists['employee'] = JHTML::_('select.genericlist',$optionArr,'employee_id','class="input-large form-control form-select" onChange="javascript:updateSearchForm('.$layout.');"','value','text',$employee_id);

		//category
		$query = $db->getQuery(true);
		$query->select('id as value, category_name as text');
		$query->from('#__app_sch_categories');
		$query->where("published = '1'");
		$query->order('category_name');
		$db->setQuery($query);
		$employees = $db->loadObjectList();
		$optionArr = array();
		$optionArr[] = JHTML::_('select.option','',Jtext::_('OS_SELECT_CATEGORY'));
		$optionArr = array_merge($optionArr,$employees);
		$lists['category'] = JHTML::_('select.genericlist',$optionArr,'category_id','class="input-large form-control form-select" onChange="javascript:updateSearchForm('.$layout.');"','value','text',$category_id);

		if($layout == 0)
		{
			HTML_OsAppscheduleAjax::showOSBSearchModule($lists);
		}
		else
		{
			echo "OSB Search ";
			if($show_venue == 1)
			{
				echo "Venue ".$lists['venue']. " EVe ";
			}
			if($show_service == 1)
			{
				echo "Service ".$lists['service']. " ESe ";
			}
			if($show_category == 1)
			{
				echo "Category ".$lists['category']. " ECa ";
			}
			if($show_employee == 1)
			{
				echo "Employee ".$lists['employee']. " EEm ";
			}
		}
		exit();
	}

	static function showCalendarView($id, $vid, $month, $year)
	{
		global $configClass;
		HTML_OsAppscheduleAjax::showCalendarViewHtml($id, $vid,$month,$year);
	}

	static function calendarViewItem($idnumber,$sid,$vid,$date,$nolink)
	{
		global $configClass;
		$db = JFactory::getDbo();
		$employees = HelperOSappscheduleCommon::loadEmployees(explode("-",$date),$sid,0,0);
		HTML_OsAppscheduleAjax::showCalendarViewItemHtml($idnumber,$sid,$vid,$date,$employees,$nolink);
	}

	public static function sendTestSMS()
	{
		global $mainframe, $configClass;
		$adminPhoneNumber = HelperOSappscheduleCommon::getAdminMobileNumber();
		if($adminPhoneNumber == '')
		{
			echo JText::_('OS_YOU_SHOULD_ENTER_NOTIFICATION_MOBILE_NUMBER_TO_SEND_TEST_SMS');
		}
		else
		{
			$message = "Test SMS from OS Services Booking";
			HelperOSappscheduleCommon::doSendSMS($adminPhoneNumber, $message);
			echo sprintf(JText::_('OS_THE_SMS_HAS_BEEN_SENT_TO'), $adminPhoneNumber);
		}
		exit();
	}


}
?>