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

// No direct access
defined('_JEXEC') or die;

/**
 * Calendar class
 *
 */
class HelperOSappscheduleCalendar{
	/**
	 * Return number days in month
	**/
	public static function ndaysinmonth($month, $year)
    {
		if(checkdate($month, 31, $year)) return 31;
		if(checkdate($month, 30, $year)) return 30;
		if(checkdate($month, 29, $year)) return 29;
		if(checkdate($month, 28, $year)) return 28;
		return 0; // error
	}

	static function listDates($selected_dates)
    {
        global $configClass, $mapClass;
        ?>
        <div id="calendardetails">
            <div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
                <div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
                    <?php
                    echo JText::_('OS_SELECT_DATES');
                    ?>
                </div>
                <table  width="100%">
                    <tr>
                        <td width="100%">
                            <?php
                            if(count($selected_dates))
                            {
                                $bgcolor = $configClass['timeslot_background'];
                                foreach($selected_dates as $date)
                                {
                                    $dateArr = explode("-", $date);
                                    $dateInt = strtotime($date);
                                    $dateString = date($configClass['date_format'], $dateInt);
                                    ?>
                                    <div class="divtimeslots_simple <?php echo $mapClass['span4'];?> divtimeslots" style="background-color: <?php echo $bgcolor;?>">
                                        <a href="javascript:loadServices('<?php echo $dateArr[0]?>','<?php echo $dateArr[1]?>','<?php echo $dateArr[2]?>')" title="<?php echo $dateString; ?>">
                                            <?php
                                            echo $dateString;
                                            ?>
                                        </a>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
	/**
	 * Init the calendar
	 *
	 * @param unknown_type $option
	 */
	static function initCalendar($year,$month,$category,$employee_id,$vid,$sid, $date_from,$date_to,$ajax=0, $direction = 0)
    {
		global $mainframe,$mapClass,$configClass;
		$db                         = JFactory::getDbo();
		$session                    = JFactory::getSession();
		$realtime					= HelperOSappscheduleCommon::getRealTime();
		$current_month 				= intval(date("m",$realtime));
		$current_year				= intval(date("Y",$realtime));
		$current_date				= intval(date("d",$realtime));
        $current_hour				= date("H",$realtime);
        $current_min				= date("i",$realtime);
        $realtime_this_day			= $current_hour*3600 + $current_min*60;
        $remain_time				= 24*3600 - $realtime_this_day;
		$date_from1					= "";
        if($ajax == 0)
        {
            if($current_year >  0)
            {
                $year				= $current_year;
            }
            if($current_month >  0)
            {
                $month				= $current_month;
            }
        }
        $number_days_in_month		= self::ndaysinmonth($month,$year);
        $selected_date              = $session->get('selected_date','');
        if($selected_date !=""){
            $dateArr                = explode("-",$selected_date);
            $select_year            = $dateArr[0];
            $select_month           = $dateArr[1];
            $select_day             = $dateArr[2];
        }
		
		if($date_from != "" && $date_from != "0000-00-00 00:00:00" && $date_from != "0000-00-00" && $direction == 0)
		{
			$date_from1				= $date_from;
			$date_from_array		= explode(" ",$date_from);
			$date_from_int			= strtotime($date_from_array[0]);
			if($date_from_int > HelperOSappscheduleCommon::getRealTime())
			{
				$current_year		= date("Y",$date_from_int);
				$current_month		= intval(date("m",$date_from_int));
				$current_date		= intval(date("d",$date_from_int));
				$year				= $current_year;
				$month				= $current_month;
			}
		}
		else
		{
            //find start_date
			//echo $month . " - " . $current_month;
			if($month != $current_month || $year != $current_year)
			{
				$today					= $year."-".$month."-01";
				$date_from				= $year."-".$month."-01";
			}
			else
			{
				$today					= $current_year."-".$current_month."-".$current_date;
				$date_from				= $current_year."-".$current_month."-".$current_date;
			}
			//echo $date_from;
            if($configClass['skip_unavailable_dates'] == 1 && $direction == 0)
            {
				$disable_time = 0;
                if((int) $configClass['min_check_in'] > 0)
				{
					$disable_time		= $realtime + ($configClass['min_check_in'] - 1) * 24 * 3600 + $remain_time;
					if($disable_time > $today)
                    {
                        $year			= date("Y", $disable_time);
                        $month			= date("m", $disable_time);
                        $day			= date("d", $disable_time);
                        $today			= $disable_time;
                    }
				}
				elseif($vid > 0)
                {
                    $db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
                    $venue = $db->loadObject();
                    $disable_booking_before		= $venue->disable_booking_before;
                    $number_date_before			= $venue->number_date_before;
                    $number_hour_before			= $venue->number_hour_before;
                    $disable_date_before		= $venue->disable_date_before;
                    if ($disable_booking_before == 1)
                    {
                        $disable_time = strtotime(date("Y", $realtime) . "-" . date("m", $realtime) . "-" . date("d", $realtime) . " 23:59:59");
                    }
                    elseif ($disable_booking_before == 2)
                    {
                        $disable_time = $realtime + ($number_date_before - 1) * 24 * 3600 + $remain_time;
                    }
                    elseif ($disable_booking_before == 3)
                    {
                        $disable_time = strtotime($disable_date_before);
                    }
                    elseif ($disable_booking_before == 4)
                    {
                        $disable_time = $realtime + $number_hour_before * 3600;
                    }
                    if($disable_time > strtotime($today))
                    {
                        $current_year  = date("Y", $disable_time);
                        $current_month = date("m", $disable_time);
                        $current_date  = date("d", $disable_time);
                        $today         = date("Y-m-d", $disable_time);
                    }
					else
					{
						$current_year  = date("Y", strtotime($today));
                        $current_month = date("m", strtotime($today));
                        $current_date  = date("d", strtotime($today));
					}
                }
				//echo $today;
                $cdate				= $current_date;
                if (!OSBHelper::isAvailableDate(strtotime($today), $category, $employee_id, $vid))
                {
                    //find first available date
					//echo "1";
                    $cdate++;
                    $checked_date	= strtotime($current_year . "-" . $current_month . "-" . $cdate);
					$date_from      = $current_year . "-" . $current_month . "-" . $cdate;
                    while ((OSBHelper::returnFirstAvailableDate($checked_date, $category, $employee_id, $vid) == '') && ($cdate <= $number_days_in_month))
                    {
						//echo $current_year . "-" . $current_month . "-" . $cdate;
						// "<BR />";
                        $cdate++;
                        $checked_date = strtotime($current_year . "-" . $current_month . "-" . $cdate);
                        $date_from	= OSBHelper::returnFirstAvailableDate($checked_date, $category, $employee_id, $vid);
                    }
                }
				if($date_from == "")
				{
					$date_from      = $today;
				}
				$date_from_int		= strtotime($date_from);
				$year				= intval(date("Y", $date_from_int));
				$month				= intval(date("m", $date_from_int));
				$day				= intval(date("d", $date_from_int));
				//die();
				//echo $date_from;
                if ($date_from != '')
                {
                    $date_from_array = explode(" ", $date_from);
                    $date_from_int	= strtotime($date_from_array[0]);
                    if ($date_from_int > HelperOSappscheduleCommon::getRealTime() && ($disable_time == 0 || ($disable_time > 0 && $disable_time > $date_from_int)))
                    {
                        $current_year = date("Y", $date_from_int);
                        $current_month = intval(date("m", $date_from_int));
                        $current_date = intval(date("d", $date_from_int));
                    }
					elseif($disable_time > 0 && (int)date("m",$disable_time) <= (int)date("m",$date_from_int) && (int)date("Y",$disable_time) <= (int)date("Y",$date_from_int))
					{
						$current_year  = date("Y", $disable_time);
                        $current_month = date("m", $disable_time);
                        $current_date  = date("d", $disable_time);
					}					
                }
            }
			//echo $date_from;
        }
		//echo $current_month;
		//echo "Month ".$month;
		$date_from_int		= strtotime($date_from);

		if($date_to != "")
		{
			$date_to_int	= strtotime($date_to);
		}
		else
		{
			$date_to_int	= 0;
		}

		//set up the first date
		if($configClass['skip_unavailable_dates'] == 1 && $day >= 1)
		{
			$start_date_current_month 	= strtotime($year."-".$month."-01");
		}
		else
		{
			$start_date_current_month 	= strtotime($year."-".$month."-01");
		}
		if($configClass['start_day_in_week'] == "monday")
		{
			$start_date_in_week		= date("N",$start_date_current_month);
		}
		else
		{
			$start_date_in_week		= date("w",$start_date_current_month);	
		}
		$monthArr = array(JText::_('OS_JANUARY'),JText::_('OS_FEBRUARY'),JText::_('OS_MARCH'),JText::_('OS_APRIL'),JText::_('OS_MAY'),JText::_('OS_JUNE'),JText::_('OS_JULY'),JText::_('OS_AUGUST'),JText::_('OS_SEPTEMBER'),JText::_('OS_OCTOBER'),JText::_('OS_NOVEMBER'),JText::_('OS_DECEMBER'));
		if($display == "")
		{
			$display = "block";
		}
		?>
        <div id="calendardetails">
            <div id="cal<?php echo intval($month)?><?php echo $year?>" style="display:<?php echo $display; ?>;" class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
                <div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
                    <table width="100%" class="apptable">
                        <tr>
                            <td width="20%" class="headercalendar">
                                <?php
                                if(($date_from1 != "") && ($year == date("Y",$date_from_int)) && ($month == intval(date("m",$date_from_int))))
								{
                                }
                                elseif($year == $current_year && $month == $current_month)
								{
									$real_month = date("m", HelperOSappscheduleCommon::getRealTime());
									$real_year  = date("Y", HelperOSappscheduleCommon::getRealTime());

									if ((int) date("m",$date_from_int) > $real_month || (int) date("Y",$date_from_int) > $real_year)
									{
										?>
										<a href="javascript:osbprev('<?php echo JUri::root();?>','<?php echo $category; ?>','<?php echo $employee_id;?>','<?php echo $vid;?>','<?php echo $sid;?>','<?php echo $date_from;?>','<?php echo $date_to;?>')" class="applink">
										<?php
										if($configClass['calendar_arrow'] != "")
										{
											?>
											<img src="<?php echo JURI::root(true)?>/components/com_osservicesbooking/asset/images/icons/previous_<?php echo $configClass['calendar_arrow'];?>.png" style="border:0px;" />
											<?php
										}
										else
										{
											?>
											<
										<?php 
										} 
										?>
										</a>
									<?php
									}
                                }
								else
								{
                                ?>
									<a href="javascript:osbprev('<?php echo JUri::root();?>','<?php echo $category; ?>','<?php echo $employee_id;?>','<?php echo $vid;?>','<?php echo $sid;?>','<?php echo $date_from;?>','<?php echo $date_to;?>')" class="applink">
									<?php
									if($configClass['calendar_arrow'] != "")
									{
										?>
										<img src="<?php echo JURI::root(true)?>/components/com_osservicesbooking/asset/images/icons/previous_<?php echo $configClass['calendar_arrow'];?>.png" style="border:0px;" />
										<?php
									}
									else
									{
										?>
										<
									<?php 
									} 
									?>
									</a>
                                <?php
                                }
                                ?>
                            </td>
                            <td width="60%" class="headercalendar">
                                <?php
                                echo $monthArr[$month-1];
                                ?>
                                &nbsp;
                                <?php echo $year;?>
                            </td>
                            <td width="20%" class="headercalendar">
                                <?php
                                if(($date_to != "") && ($year == date("Y",$date_to_int)) && ($month == intval(date("m",$date_to_int)))){
                                }elseif(($year == $current_year + 2) && ($month == 12)){
                                }else{
                                ?>
                                <a href="javascript:osbnext('<?php echo JUri::root();?>','<?php echo $category; ?>','<?php echo $employee_id;?>','<?php echo $vid;?>','<?php echo $sid;?>','<?php echo $date_from;?>','<?php echo $date_to;?>')" class="applink">
                                <?php
                                if($configClass['calendar_arrow'] != "")
								{
                                    ?>
                                    <img src="<?php echo JURI::root(true)?>/components/com_osservicesbooking/asset/images/icons/next_<?php echo $configClass['calendar_arrow'];?>.png" style="border:0px;" />
                                    <?php
                                }else{
                                    ?>
                                    >
                                <?php } ?>
                                </a>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php 
						if($configClass['show_dropdown_month_year'] == 1)
						{
						?>
                        <tr>
                            <td width="100%" colspan="3" style="padding:3px;text-align:center;">
                                <select name="ossm" class="input-small form-select ishort" id="ossm" onchange="javascript:updateMonth(this.value)">
                                    <?php
                                    for($i=0;$i<count($monthArr);$i++)
									{
                                        if(intval($month) == $i + 1)
										{
                                            $selected = "selected";
                                        }
										else
										{
                                            $selected = "";
                                        }
                                        ?>
                                        <option value="<?php echo $i + 1?>" <?php echo $selected?>><?php echo $monthArr[$i]?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <select name="ossy" class="input-small form-select imini" id="ossy" onchange="javascript:updateYear(this.value)">
                                    <?php
                                    for($i=date("Y",$realtime);$i<=date("Y",$realtime)+3;$i++)
									{
                                        if(intval($year) == $i)
										{
                                            $selected = "selected";
                                        }
										else
										{
                                            $selected = "";
                                        }
                                        ?>
                                        <option value="<?php echo $i?>" <?php echo $selected?>><?php echo $i?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <input type="button" class="<?php echo $configClass['calendar_normal_style'];?>" value="<?php echo JText::_('OS_GO');?>" onclick="javascript:calendarMovingSmall('<?php echo JUri::root();?>','<?php echo $category; ?>','<?php echo $employee_id;?>','<?php echo $vid;?>','<?php echo $sid;?>','<?php echo $date_from;?>','<?php echo $date_to;?>');">
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <table  width="100%">
                    <tr>
                        <?php
                        if($configClass['start_day_in_week'] == "sunday")
						{
                        ?>
							<td class="header_calendar">
								<?php echo JText::_('OS_SUN')?>
							</td>
                        <?php
                        }
                        ?>
                        <td class="header_calendar">
                            <?php echo JText::_('OS_MON')?>
                        </td>
                        <td class="header_calendar">
                            <?php echo JText::_('OS_TUE')?>
                        </td>
                        <td class="header_calendar">
                            <?php echo JText::_('OS_WED')?>
                        </td>
                        <td class="header_calendar">
                            <?php echo JText::_('OS_THU')?>
                        </td>
                        <td class="header_calendar">
                            <?php echo JText::_('OS_FRI')?>
                        </td>
                        <td class="header_calendar">
                            <?php echo JText::_('OS_SAT')?>
                        </td>
                        <?php
                        if($configClass['start_day_in_week'] == "monday")
						{
                        ?>
							<td class="header_calendar">
								<?php echo JText::_('OS_SUN')?>
							</td>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        if($configClass['start_day_in_week'] == "sunday")
						{
                            $start = 0;
                        }
                        else
                        {
                            $start = 1;
                        }
                        for($i=$start;$i<$start_date_in_week;$i++){
                            //empty
                            ?>
                            <td>
                            </td>
                            <?php
                        }
                        $j				   = $start_date_in_week-1;
                        for($i=1;$i<=$number_days_in_month;$i++)
						{
                            $j++;

                            //check to see if today
                            $today = strtotime($current_year."-".$current_month."-".$current_date);
                            $checkdate = strtotime($year."-".$month."-".$i);
                            //echo $today."-".$checkdate;
                            //echo "<BR />";
							//echo date("Y-m-d", $today);
                            if($today > $checkdate)
                            {
                                $classname = $configClass['calendar_inactivate_style'];
                                $show_link = 0;
                            }
                            elseif(($date_from != "") && ($date_from_int > $checkdate))
                            {
                                $classname = $configClass['calendar_inactivate_style'];
                                $show_link = 0;
                            }
                            elseif(($date_to != "") && ($date_to_int < $checkdate))
                            {
                                $classname = $configClass['calendar_inactivate_style'];
                                $show_link = 0;
                            }
							else
							{
                                $show_link = 1;
                                if($configClass['disable_calendar_in_off_date'] == 1)
                                {
                                    $services  = OSBHelper::getServices($category,$employee_id,$vid, $sid);
                                    if($sid > 0)
                                    {
                                        $temp = array();
										$tmp  = new \stdClass();
										$tmp->id = $sid;
                                        $temp[0] = $tmp;
                                        $employees = OSBHelper::loadEmployees($temp,$employee_id,$checkdate,$vid);
                                    }
                                    else
                                    {
                                        $employees = OSBHelper::loadEmployees($services,$employee_id,$checkdate,$vid);
                                    }

                                    $venue_check = 1;
                                    if($vid > 0)
									{
                                        $venue_check = OSBHelper::checkDateInVenue($vid,$checkdate);
                                    }
                                    if(($i == $current_date) && ($month == $current_month) && ($year == $current_year))
                                    {
                                        $classname = $configClass['calendar_currentdate_style'];
                                    }
                                    elseif(OSBHelper::isOffDay($checkdate))
                                    {
                                        $classname = $configClass['calendar_inactivate_style'];
                                        $show_link = 0;
                                    }
                                    elseif(count($services) == 0)
                                    {
                                        $classname = $configClass['calendar_inactivate_style'];
                                        $show_link = 0;
                                    }
                                    elseif(! $employees)
                                    {
                                        $classname = $configClass['calendar_inactivate_style'];
                                        $show_link = 0;
                                    }
                                    elseif($venue_check == 0)
                                    {
                                        $classname = $configClass['calendar_inactivate_style'];
                                        $show_link = 0;
                                    }
                                    elseif($employee_id > 0 && $sid > 0 && !OSBHelper::checkTimeSlotsAvailable($sid,$employee_id,$year."-".$month."-".$i, $vid))
                                    {
                                        $classname = $configClass['non_available_timeslots'];
                                        $show_link = 0;
                                    }
									elseif(!OSBHelper::checkTimeSlotsAvailables($services, $employees, $year."-".$month."-".$i, $vid))
									{
										$classname = $configClass['non_available_timeslots'];
                                        $show_link = 0;
									}
                                    else
                                    {
                                        $classname = $configClass['calendar_normal_style'];
                                    }
                                }
                                else
                                {
                                    if(($i == $current_date) && ($month == $current_month) && ($year == $current_year))
                                    {
                                        $classname = $configClass['calendar_currentdate_style'];
                                    }
                                    elseif(($i == $select_day) && ($month == $select_month) && ($year == $select_year))
                                    {
                                        $classname = $configClass['calendar_activate_style'];
                                    }
									elseif(OSBHelper::isOffDay($checkdate))
                                    {
                                        $classname = $configClass['calendar_inactivate_style'];
                                        $show_link = 0;
                                    }
                                    else
                                    {
                                        $classname = $configClass['calendar_normal_style'];
                                    }
                                }
                            }

                            if($i < 9)
                            {
                                $i1 = "0".$i;
                            }else{
                                $i1 = $i;
                            }
							if($show_link == 1)
							{
								$onclick = "onclick=\"javascript:loadServices(".$year.",".$month.",'".$i1."');\"";
							}else{
								$onclick = "";
							}
							/*
							Hide sunday 
							if(date("D",strtotime($year."-".$month."-".$i)) == "Sun"){
								$additional_style = "display:none;";
							}else{
								$additional_style = "";
							}
							*/
                            ?>
                            <td id="td_cal_<?php echo $i1?>"  align="center" style="padding:0px !important;padding-bottom:3px !important;padding-top:3px !important;">
                                <div class="<?php echo $classname; ?> buttonpadding10" style="" id="a<?php echo $year?><?php echo $month?><?php echo $i1;?>" <?php echo $onclick;?>>
									<?php
									if($i > 9){
										echo $i;
									}else{
										echo "0".$i;
									}
									?>
                                </div>
                            </td>
                            <?php
                            if($configClass['start_day_in_week'] == "sunday")
                            {
                                if($j >= 6)
                                {
                                    $j = -1;
                                    echo "</tr><tr>";
                                }
                            }
                            else
                            {
                                if($j >= 7)
                                {
                                    $j = 0;
                                    echo "</tr><tr>";
                                }
                            }
                        }
                        ?>
                    </tr>
                </table>
            </div>
        </div>
		<?php
	}

	/**
	 * Set up calendar for 12 months in year
	 *
	 * @param unknown_type $year
	 */
	static function initCalendarForYear($year,$category,$employee_id,$vid, $sid ,$date_from,$date_to)
    {
        $realtime					= HelperOSappscheduleCommon::getRealTime();
        $current_month 				= intval(date("m",$realtime));
		HelperOSappscheduleCalendar::initCalendar($year,$current_month,$category,$employee_id,$vid, $sid,$date_from, $date_to);
	}

	/**
	 * Set up calendar for months of year from -> year to
	 *
	 * @param unknown_type $yearfrom
	 * @param unknown_type $yearto
	 */
	static function initCalendarForSeveralYear($yearfrom,$category,$employee_id,$vid, $sid,$date_from,$date_to)
    {
        HelperOSappscheduleCalendar::initCalendarForYear($yearfrom,$category,$employee_id,$vid, $sid,$date_from,$date_to);
	}


	/**
	 * Get avaiable time 
	 *
	 * @param unknown_type $option
	 * @param array $date (day, month, year)
	 */
	static function getAvailableTime($option,$date, $vid = 0)
	{
		global $mainframe;
		$db = JFactory::getDbo();
		$time = $date[2]."-".$date[1]."-".$date[0];
		$db->setQuery("Select count(id) from #__app_sch_working_time_custom where `worktime_date` <= '$time' and `worktime_date_to` >= '$time'");
		$count = $db->loadResult();
		if($count > 0)
		{
			$db->setQuery("Select start_time,end_time from #__app_sch_working_time_custom where `worktime_date` <= '$time' and `worktime_date_to` >= '$time'");
			$time = $db->loadObject();
		}
		else
		{
			$start_time_venue = "";
			if($vid > 0)
			{
				$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
				$venue = $db->loadObject();
				$start_hour  = $venue->opening_hour;
				$start_min   = $venue->opening_minute;
				if($start_hour > 0)
				{
					$start_time_venue = $start_hour.":".$start_min.":00";
				}
			}
			$time = strtotime($time);
			$date_int_week = date("N",$time);
			//
			$db->setQuery("Select start_time,end_time from #__app_sch_working_time where id = '$date_int_week'");
			$time = $db->loadObject();
			if($start_time_venue != "")
			{
				$time->start_time = $start_time_venue;
			}
		}
		return $time;
	}


	static function getAvaiableTimeFrameOfOneEmployee($date,$eid,$sid,$vid)
	{
		global $mainframe,$mapClass,$configClass;
		if($configClass['booked_timeslot_background'] == "")
		{
			$configClass['booked_timeslot_background'] = "#e65789"; 
		}
		$session					= & JFactory::getSession();
        $option						= "com_osservicesbooking";
		$db							= JFactory::getDbo();
		$config						= new JConfig();
		$offset						= $config->offset;
		date_default_timezone_set($offset);

		$realtime					= HelperOSappscheduleCommon::getRealTime();

		$current_hour				= date("H",$realtime);
		$current_min				= date("i",$realtime);
		$realtime_this_day			= $current_hour*3600 + $current_min*60;
		$remain_time				= 24*3600 - $realtime_this_day;
        $dateformat                 = $date[2]."-".$date[1]."-".$date[0];

		if((int) $configClass['max_check_in'] > 0)
		{
			$disable_time_after		= $realtime + (int)$configClass['max_check_in']*24*3600;
		}
		if((int) $configClass['min_check_in'] > 0)
		{
			$disable_time_after		= $realtime + (int)$configClass['max_check_in']*24*3600;
		}
		
		if($vid > 0 && (int) $configClass['max_check_in'] == 0 && (int) $configClass['min_check_in'] == 0)
		{
			$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
			$venue = $db->loadObject();
			$disable_booking_before		= $venue->disable_booking_before;
			$number_date_before			= $venue->number_date_before;
			$number_hour_before			= $venue->number_hour_before;
			$disable_date_before		= $venue->disable_date_before;
			if($disable_booking_before == 1)
			{
				$disable_time = strtotime(date("Y",$realtime)."-".date("m",$realtime)."-".date("d",$realtime)." 23:59:59");
			}
			elseif($disable_booking_before == 2)
            {
				$disable_time = $realtime + ($number_date_before-1)*24*3600 + $remain_time;
			}
			elseif($disable_booking_before  == 3)
            {
				$disable_time = strtotime($disable_date_before);
			}
			elseif($disable_booking_before == 4)
            {
				$disable_time = $realtime + $number_hour_before*3600;

			}

            if($disable_time > (int) strtotime($dateformat))
            {
				if($configClass['skip_unavailable_dates'] == 1)
				{
					$date[2]     = date("Y", $disable_time);
					$date[1]     = date("m", $disable_time);
					$date[0]     = date("d", $disable_time);
				}
                $dateformat  = date("Y-m-d", $disable_time);
            }

			$disable_booking_after	= $venue->disable_booking_after;
			$number_date_after		= $venue->number_date_after;
			$disable_date_after		= $venue->disable_date_after ;
			if($disable_booking_after == 2)
			{
				$disable_time_after = $realtime + $number_date_after*24*3600;
			}
			elseif($disable_booking_after  == 3)
			{
				$disable_time_after = strtotime($disable_date_after);
			}
		}
		else
		{

			$disable_booking_after		= 1;
			$disable_booking_before		= 1;
		}

		if($configClass['multiple_work']  == 1)
		{
			$db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' AND a.sid = '$sid' and a.booking_date = '$dateformat' AND b.order_status IN ('P','S','A')");
		}
		else
		{
			$db->setQuery("SELECT a.* FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON a.order_id = b.id WHERE a.eid = '$eid' and a.booking_date = '$dateformat' AND b.order_status IN ('P','S','A')");
		}
		//echo $db->getQuery();
		$employees = $db->loadObjectList();
		$tempEmployee = array();
		if(count($employees) > 0)
		{
			for($i=0;$i<count($employees);$i++)
			{
				$employee = $employees[$i];
				$count = count($tempEmployee);
				$tmp					= new \stdClass();
				
				$tmp->start_time		= $employees[$i]->start_time;
				$tmp->end_time			= $employees[$i]->end_time;
				$tmp->show				= 1;
				$tmp->priorirty			= 0;

				$tempEmployee[$i]		= $tmp;
			}
		}
		//end checking

		$db->setQuery("Select * from #__app_sch_custom_breaktime where sid = '$sid' and eid = '$eid' and bdate = '$dateformat'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count = count($tempEmployee);

				$tmp									= new \stdClass();
				$tmp->start_time		= strtotime($dateformat." ".$custom->bstart);
				$tmp->end_time			= strtotime($dateformat." ".$custom->bend);
				$tmp->show				= 0;
				$tmp->priorirty			= 1;
				$tempEmployee[$count]	= $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$eid' and busy_date = '$dateformat'");
		$customs = $db->loadObjectList();
		if(count($customs) > 0)
		{
			foreach ($customs as $custom)
			{
				$count = count($tempEmployee);
				$tmp									= new \stdClass();


				$tmp->start_time = strtotime($dateformat." ".$custom->busy_from);
				$tmp->end_time   = strtotime($dateformat." ".$custom->busy_to);
				$tmp->show		  = 0;
				$tmp->priorirty  = 1;
				$tempEmployee[$count] = $tmp;
			}
		}

		$db->setQuery("Select * from #__app_sch_service_availability where sid = '$sid' and avail_date = '$dateformat'");
		$unavailable_values = $db->loadObjectList();
		if(count($unavailable_values) > 0)
		{
			for($i=0;$i<count($unavailable_values);$i++)
			{
				$employee = $unavailable_values[$i];
				$count = count($tempEmployee);
				$tmp									= new \stdClass();
				$tmp->start_time = strtotime($dateformat." ".$employee->start_time);
				$tmp->end_time   = strtotime($dateformat." ".$employee->end_time);
				$tmp->show		  = 0;
				$tmp->priorirty  = 1;
				$tempEmployee[$count] = $tmp; 
			}
		}

		//print_r($tempEmployee);
		//check unique_cookie
		$unique_cookie = $session->get('unique_cookie');
		$db->setQuery("SELECT COUNT(id) FROM #__app_sch_temp_orders WHERE unique_cookie LIKE '$unique_cookie'");
		$count = $db->loadResult();
		if($count > 0)
		{
			$db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie LIKE '$unique_cookie'");
			$order_id = $db->loadResult();
			$db->setQuery("SELECT * FROM #__app_sch_temp_order_items WHERE order_id = '$order_id' and sid  = '$sid' and eid  = '$eid' and booking_date = '$dateformat'");
			$temp_orders = $db->loadObjectList();
			if(count($temp_orders) > 0)
			{
				for($i=0;$i<count($temp_orders);$i++)
				{
					$item = $temp_orders[$i];
					$counttempEmployee	= count($tempEmployee);
					$tmp				= new \stdClass();
					$tmp->start_time	= $item->start_time;
					$tmp->end_time		= $item->end_time;
					$tmp->show			= 1;
					$tmp->priorirty		= 0;

					$tempEmployee[$counttempEmployee] = $tmp;
				}
			}
		}

		if($configClass['multiple_work']  == 1)
		{
			$db->setQuery("SELECT a.* FROM #__app_sch_temp_order_items AS a inner join #__app_sch_temp_orders as b on a.order_id = b.id WHERE a.eid = '$eid' AND a.sid = '$sid' and a.booking_date = '$dateformat' and b.unique_cookie NOT LIKE '$unique_cookie'");
		}
		else
		{
			$db->setQuery("SELECT a.* FROM #__app_sch_temp_order_items AS a inner join #__app_sch_temp_orders as b on a.order_id = b.id WHERE a.eid = '$eid' and a.booking_date = '$dateformat' and b.unique_cookie NOT LIKE '$unique_cookie'");
		}

		$employees = $db->loadObjectList();
		//$tempEmployee = array();
		if(count($employees) > 0)
		{
			for($i=0;$i<count($employees);$i++)
			{
				//$count					= count($tempEmployee);
				$employee				= $employees[$i];
				$count					= count($tempEmployee);
				$tmp					= new \stdClass();
				
				$tmp->start_time		= $employees[$i]->start_time;
				$tmp->end_time			= $employees[$i]->end_time;
				$tmp->show				= 1;
				$tmp->priorirty			= 0;

				$tempEmployee[$count]	= $tmp;
			}
		}

		//echo $dateformat;
		//echo date("N",strtotime($dateformat));
		$breakTime = array();
		$db->setQuery("Select * from #__app_sch_employee_service_breaktime where sid = '$sid' and eid = '$eid' and date_in_week = '".date("N",strtotime($dateformat))."'");
		$breaks = $db->loadObjectList();
		for($i=0;$i<count($breaks);$i++)
		{
			$break_time_start = $dateformat." ".$breaks[$i]->break_from;
			$break_time_sint  = strtotime($break_time_start);
			$break_time_end   = $dateformat." ".$breaks[$i]->break_to;
			$break_time_eint  = strtotime($break_time_end);
			$count = count($tempEmployee);
			$tmp			  = new \stdClass();

			$tmp->start_time  = $break_time_sint;
			$tmp->end_time    = $break_time_eint;
			$tmp->show		  = 0;
			$tmp->priorirty   = 1;
			$tempEmployee[$count] = $tmp;

			$count = count($breakTime);
			$tmp			  = new \stdClass();
			$tmp->start_time    = $break_time_sint;
			$tmp->end_time	  = $break_time_eint;
			$breakTime[$count] = $tmp;

		}
		//print_r($tempEmployee);
		//print_r($breaks);

		$db->setQuery("SELECT * FROM #__app_sch_services WHERE id = '$sid'");
		$service = $db->loadObject();
		$service_length  = $service->service_total;
		$service_total   = $service->service_total;
		$service_total_int = $service_total*60;

		$time = HelperOSappscheduleCalendar::getAvailableTime($option,$date, $vid);
		/*
		if($vid > 0)
		{
		    $start_hour  = $venue->opening_hour;
		    $start_min   = $venue->opening_minute;
		    if($start_hour > 0)
		    {
		        $time->start_time = $start_hour.":".$start_min.":00";
            }
        }
		*/
		$starttimetoday  = strtotime($date[2]."-".$date[1]."-".$date[0]." ".$time->start_time);
		$endtimetoday    = strtotime($date[2]."-".$date[1]."-".$date[0]." ".$time->end_time);
		$cannotbookstart = $endtimetoday - $service_total_int;

		//remove the case break time from start time today
		foreach($tempEmployee as $t)
		{
			if(($starttimetoday == $t->start_time) && ($t->show == 0))
			{
				$starttimetoday = $t->end_time;
			}
		}

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
		<div class="<?php echo $mapClass['row-fluid'];?> employee_timeslots" id="employee<?php echo $sid?>_<?php echo $eid?>">
			<div class="<?php echo $mapClass['span12'];?>">
			<?php
			if($configClass['disable_payments']  == 1 && $configClass['show_employee_cost'] == 1)
			{
			?>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div style="padding-bottom:5px;" class="<?php echo $mapClass['span12'];?> available_information">
					<?php echo JText::_('OS_SERVICES_COST_WITH_THIS_EMPLOYEE')?>:
					<?php
					$service_price = OSBHelper::returnServicePriceShowing($service->id,$date[2]."-".$date[1]."-".$date[0],1,$eid);
					?>
					<?php echo OSBHelper::showMoney($service_price,1);?>
					<?php
					if(($additional_price > 0) || ($additional_price < 0))
					{
						?>
						(<?php echo JText::_('OS_ADDITIONAL_COST1')?>: <?php echo $configClass['currency_symbol']?> <?php echo $additional_price;?> <?php echo $configClass['currency_format'];?>)
						<?php
					}
					if(count($extras) > 0)
					{
					?>
					<BR />
					<?php
						for($i=0;$i<count($extras);$i++)
						{
							$extra = $extras[$i];
							echo JText::_('OS_FROM').": ".$extra->start_time;
							echo " ".JText::_('OS_TO').": ".$extra->end_time;
							echo " + ".$extra->extra_cost." ".$configClass['currency_format'];
							echo "<BR />";
						}
					}
					?>
				</div>
			</div>
			<?php
			}
			if($configClass['show_booked_information']== 1)
			{
				if(count($tempEmployee) > 0)
				{
				?>
                    <div class="<?php echo $mapClass['row-fluid'];?>">
                        <div style="padding-bottom:5px;" class="<?php echo $mapClass['span12'];?> available_information">
                            <?php echo JText::_('OS_NOT_AVAILABLE_TIME')?>: <BR />
                            <?php
                            for($i=0;$i<count($tempEmployee);$i++){
                                if($tempEmployee[$i]->show == 1){
                                    echo $i + 1;
                                    echo ". ";
                                    echo date($configClass['time_format'],$tempEmployee[$i]->start_time)." - ".date($configClass['time_format'],$tempEmployee[$i]->end_time);
                                    echo " (".date($configClass['date_format'],$tempEmployee[$i]->start_time).")";
                                    echo "<BR />";
                                }
                            }
                            ?>
                        </div>
                    </div>
				<?php
				}
			}

			$timezone1 = $configClass['timezone1'];
			$timezone2 = $configClass['timezone2'];
			$timezone3 = $configClass['timezone3'];
			$timezone4 = $configClass['timezone4'];
			$timezone5 = $configClass['timezone5'];

			$timezone   = array();
			$timezone[] = $timezone1;
			$timezone[] = $timezone2;
			$timezone[] = $timezone3;
			$timezone[] = $timezone4;
			$timezone[] = $timezone5;
			?>
			<div class="<?php echo $mapClass['row-fluid'];?>">
                <?php
                if($configClass['booking_theme'] == 0)
				{
                ?>
                    <div class="<?php echo $mapClass['span12'];?> timeslotdiv">
                <?php
                }
				else
				{
                    ?>
                    <div class="<?php echo $mapClass['span12'];?>">
                        <?php
                        if(($configClass['hidetabs'] == 1) and ($configClass['employee_bar'] == 0)){
                        ?>
							<div class="<?php echo $mapClass['row-fluid'];?> " style="margin-bottom:10px;">
								<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
									<?php echo Jtext::_('OS_SELECT_TIME_SLOT'); ?>
								</div>
							</div>
                    <?php } ?>
                <?php
                }
                $db->setQuery("Select * from #__app_sch_services where id = '$sid'");
                $service_details = $db->loadObject();
                if($configClass['booking_more_than_one'] == 0)
                {
                    $ableBook = 1;
                }elseif(($configClass['booking_more_than_one'] == 1) && (!OSBHelper::isAreadyBooked($starttimetoday,0))) {
                    $ableBook = 1;
                }else {
                    $ableBook = 0;
                }
                if($ableBook == 1)
                {
                    if($service_details->service_time_type == 0)
                    {
                    ?>
                    <div class="<?php echo $mapClass['row-fluid'];?> timeslotrow">
                    <?php
                    $j = 0;
                    $stroption = array();
                    for($inctime = $starttimetoday;$inctime<=$endtimetoday;$inctime = $inctime + $amount)
                    {
                        $start_booking_time = $inctime;
                        $end_booking_time	= $inctime + $service_length*60;
                        //Modify on 1st May to add the start time from break time
                        foreach ($breakTime as $break)
                        {
                            if(($inctime >= $break->start_time) and ($inctime <= $break->end_time))
                            {
                                $inctime = $break->end_time;
                                $start_booking_time = $inctime;
                                $end_booking_time	= $inctime + $service_length*60;
                            }
                        }

                        $arr1 = array();
                        $arr2 = array();
                        $arr3 = array();

                        if(count($tempEmployee) > 0)
                        {
                            for($i=0;$i<count($tempEmployee);$i++)
                            {
                                $employee = $tempEmployee[$i];
                                $before_service = $employee->start_time - $service->service_total*60;
                                $after_service  = $employee->end_time + $service->service_total*60;
                                if(($employee->start_time < $inctime) && ($inctime < $employee->end_time) && ($inctime + $service->service_total*60 == $employee->end_time))
                                {
                                    //echo "1";
                                    $arr1[] = $inctime;
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink = true;
                                }elseif(($employee->start_time > $inctime) && ($employee->start_time < $end_booking_time)){

                                    //echo "4";
                                    $arr2[] = $inctime;
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink = true;
                                }elseif(($employee->end_time > $inctime) && ($employee->end_time < $end_booking_time)){
                                    //echo "5";
                                    $arr2[] = $inctime;
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink = true;
                                }elseif(($employee->start_time > $inctime) && ($employee->end_time < $end_booking_time)){

                                    //echo "6";
                                    $arr2[] = $inctime;
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink = true;
                                }elseif(($employee->start_time < $inctime) && ($employee->end_time > $end_booking_time)){
                                    //echo "7";

                                    $arr2[] = $inctime;
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink = true;
                                }elseif(($employee->start_time == $inctime) || ($employee->end_time == $end_booking_time)){
                                    //echo "7";

                                    $arr2[] = $inctime;
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink = true;
                                }else{
                                    //echo "8";
                                    $arr3[] = $inctime;
                                    $bgcolor = $configClass['timeslot_background'];
                                    $nolink = false;
                                }
                            }
                        }
                        else
                        {
                            $arr3[] = $inctime;
                            $bgcolor = $configClass['timeslot_background'];
                            $nolink = false;
                        }
                        //echo $bgcolor;
                        $gray =  0;
                        if($inctime + $service->service_total*60 > $endtimetoday)
						{
                            $bgcolor = $configClass['booked_timeslot_background'];
                            $nolink  = true;
                            $gray = 1;
                        }

                        if(($date[2] == date("Y",$realtime) && ($date[1] == intval(date("m",$realtime))) && ($date[0] == intval(date("d",$realtime)))))
                        {
                            if($inctime <= $realtime)
							{
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;

                                $gray = 1;
                            }
                        }

                        if($gray == 0)
						{
                            if(in_array($inctime,$arr2))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink = true;
                            }
                            elseif(in_array($inctime,$arr1))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink = true;
                            }
                            else
                            {
                                $bgcolor = $configClass['timeslot_background'];
                                $nolink = false;
                            }
                        }
                        elseif($gray == 1)
                        {
                            $bgcolor = $configClass['booked_timeslot_background'];
                            $nolink  = true;
                        }

                        $tipcontent = "";
                        $tipcontentArr = array();
                        for($k=0;$k<count($timezone);$k++)
                        {
                            if($timezone[$k] != "")
                            {
                                $tipcontentArr[] = $timezone[$k].": ".OSBHelper::showTime($timezone[$k],$inctime,$end_booking_time);
                            }
                        }
                        if(count($tipcontentArr) > 0)
                        {
                            $tipcontent = implode(" | ",$tipcontentArr);
                        }

                        if($configClass['multiple_work'] == 0)
                        {
                            if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_booking_time,$end_booking_time))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                            if(!OSBHelper::checkMultipleEmployeesInTempOrderTable($sid,$eid,$start_booking_time,$end_booking_time))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                        }

                        if($configClass['disable_timeslot'] == 1)
                        {
                            if(!OSBHelper::checkMultipleServices($sid,$eid,$start_booking_time,$end_booking_time))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                            if(!OSBHelper::checkMultipleServicesInTempOrderTable($sid,$eid,$start_booking_time,$end_booking_time))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                        }
						
                        if($configClass['active_linked_service'] == 1)
                        {
                            if(!OSBHelper::checkLinkedServices($sid,$start_booking_time,$end_booking_time))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                            if(!OSBHelper::checkLinkedServicesInTempOrderTable($sid,$start_booking_time,$end_booking_time))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                        }

                        if($configClass['disable_venuetimeslot'] == 1)
                        {
                            if(!OSBHelper::checkMultipleVenues($eid,$vid,$start_booking_time,$end_booking_time))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                            if(!OSBHelper::checkMultipleVenuesInTempOrderTable($eid,$vid,$start_booking_time,$end_booking_time))
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                        }
						
                        //echo $bgcolor;

                        if($disable_booking_before >= 1)
                        {
                            if($inctime < $disable_time)
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                        }
						//echo date("H:i", $inctime). " - ".$bgcolor;
                        if($disable_booking_after > 1)
                        {
                            if($inctime > $disable_time_after){
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink  = true;
                            }
                        }

                        //multiple timeslots addable
                        if($configClass['allow_multiple_timeslots'] == 1)
                        {
                            $configClass['booking_theme'] = 0;
                        }
                        if ($configClass['booking_theme'] == 0)
                        {
                            if((($nolink) && (($configClass['show_occupied'] == 1)) || (!$nolink)) && ($end_booking_time <= $endtimetoday)) 
							{
                                $j++;
                                $show_no_timeslot_text = 0;
                                ?>
                                <div class="<?php echo $mapClass['span6'];?> timeslots divtimeslots" style="background-color:<?php echo $bgcolor?>;">
                                    <?php
                                    if (!$nolink)
                                    {
                                        $text = JText::_('OS_BOOK_THIS_EMPLOYEE_FROM') . "[" . date($configClass['date_time_format'], $inctime) . "] to [" . date($configClass['date_time_format'], $end_booking_time) . "]";
                                        if($configClass['allow_multiple_timeslots'] == 1)
                                        {
                                            ?>
                                            <input type="checkbox" name="<?php echo $eid?>[]"
                                                   id="<?php echo $sid?>_<?php echo $eid?>_<?php echo $inctime?>"
                                                   onclick="javascript:addBookingMultiple('<?php echo $inctime?>','<?php echo $end_booking_time;?>','<?php echo date($configClass['date_format'], $inctime);?> <?php echo date($configClass['time_format'], $inctime);?>','<?php echo date($configClass['date_format'], $inctime + $service_total_int)?> <?php echo date($configClass['time_format'], $inctime + $service_total_int)?>',<?php echo $eid?>,<?php echo $sid?>,'<?php echo JText::_('OS_SUMMARY');?>','<?php echo JText::_('OS_FROM');?>','<?php echo JText::_('OS_TO');?>',1);" />
                                            <?php
                                            $stroption[] = "<option value='".$inctime."-".$end_booking_time."'>".date($configClass['date_time_format'], $inctime)." - ".date($configClass['date_time_format'], $inctime + $service_total_int)."</option>";
                                        }
                                        else
                                        {
                                            ?>
                                            <input type="radio" name="<?php echo $eid?>[]"
                                                   id="<?php echo $sid?>_<?php echo $eid?>_<?php echo $inctime?>"
                                                   onclick="javascript:addBooking('<?php echo $inctime?>','<?php echo $end_booking_time;?>','<?php echo date($configClass['date_format'], $inctime);?> <?php echo date($configClass['time_format'], $inctime);?>','<?php echo date($configClass['date_format'], $inctime + $service_total_int)?> <?php echo date($configClass['time_format'], $inctime + $service_total_int)?>',<?php echo $eid?>,<?php echo $sid?>,'<?php echo JText::_('OS_SUMMARY');?>','<?php echo JText::_('OS_FROM');?>','<?php echo JText::_('OS_TO');?>',1);" />
                                        <?php
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <?php if($configClass['waiting_list'] == 1 && $inctime > $realtime)
                                        {
											?>
											<a href="javascript:openWaitingList('<?php echo JUri::root(); ?>index.php?option=com_osservicesbooking&task=default_addtowaitinglist&tmpl=component&sid=<?php echo $sid ?>&eid=<?php echo $eid ?>&start=<?php echo $inctime; ?>&end=<?php echo $end_booking_time; ?>');"
											   title="<?php echo JText::_('OS_CLICK_HERE_TO_ADD_TIMESLOT_TO_WAITING_LIST');?>" class="addtowaitinglistlink">
												[<?php echo JText::_('OS_ADD_TO_WAITING_LIST'); ?>]
											</a>
											<?php
                                        }
                                        ?>
                                    <?php
                                    }
                                    ?>
                                    &nbsp;&nbsp;&nbsp;
                                    <?php
                                    if ($tipcontent != "")
                                    {
                                    ?>
                                        <span class="hasTip" title="<?php echo $tipcontent?>" for="<?php echo $sid?>_<?php echo $eid?>_<?php echo $inctime?>">
                                    <?php
                                    }
                                    ?>
                                    <label for="<?php echo $sid?>_<?php echo $eid?>_<?php echo $inctime?>" class="timeslotlabel">
                                    <?php
                                    echo date($configClass['time_format'], $inctime);
                                    ?>
									<?php
									if($configClass['show_end_time'] == 1){	
									?>
										&nbsp;-&nbsp;
										<?php
										echo date($configClass['time_format'], $end_booking_time);
									}
                                    $user = JFactory::getUser();
                                    if (($configClass['allow_multiple_timezones'] == 1) and ($user->id > 0) and (OSBHelper::getConfigTimeZone() != OSBHelper::getUserTimeZone())) {
                                        echo "<BR />";
                                        echo "<span class='additional_timezone'>";
                                        echo OSBHelper::getUserTimeZone() . ": ";
                                        echo date($configClass['time_format'], OSBHelper::convertTimezone($inctime));
                                        ?>
                                        &nbsp;-&nbsp;
                                        <?php
                                        echo date($configClass['time_format'], OSBHelper::convertTimezone($end_booking_time));
                                        echo "</span>";
                                    }
                                    echo "</label>";
                                    if ($tipcontent != ""){
                                    ?>
                                        </span>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                if($j==2){
                                    ?>
                                    </div><div class="<?php echo $mapClass['row-fluid'];?> timeslotrow">
                                    <?php
                                    $j = 0;
                                }
                            }
                            else
                            { //simple layout
                                if(($nolink && (($configClass['show_occupied'] == 1)) || !$nolink) && ($end_booking_time <= $endtimetoday))
                                {
                                    $j++;
                                    $show_no_timeslot_text = 0;
                                    if (!$nolink)
                                    {
                                        ?>
                                        <div class="divtimeslots_simple"
                                             id="timeslot<?php echo $sid ?>_<?php echo $eid ?>_<?php echo $inctime ?>"
                                             style="background-color:<?php echo $bgcolor ?>;"
                                             onclick="javascript:addBookingSimple('<?php echo $inctime ?>','<?php echo $end_booking_time; ?>','<?php echo date($configClass['date_format'], $inctime); ?> <?php echo date($configClass['time_format'], $inctime); ?>','<?php echo date($configClass['date_format'], $inctime + $service_total_int) ?> <?php echo date($configClass['time_format'], $inctime + $service_total_int) ?>',<?php echo $eid ?>,<?php echo $sid ?>,'<?php echo JText::_('OS_SUMMARY'); ?>','<?php echo JText::_('OS_FROM'); ?>','<?php echo JText::_('OS_TO'); ?>','timeslot<?php echo $sid ?>_<?php echo $eid ?>_<?php echo $inctime ?>','<?php echo $bgcolor ?>',1);" ontouchstart="javascript:addBookingSimple('<?php echo $inctime ?>','<?php echo $end_booking_time; ?>','<?php echo date($configClass['date_format'], $inctime); ?> <?php echo date($configClass['time_format'], $inctime); ?>','<?php echo date($configClass['date_format'], $inctime + $service_total_int) ?> <?php echo date($configClass['time_format'], $inctime + $service_total_int) ?>',<?php echo $eid ?>,<?php echo $sid ?>,'<?php echo JText::_('OS_SUMMARY'); ?>','<?php echo JText::_('OS_FROM'); ?>','<?php echo JText::_('OS_TO'); ?>','timeslot<?php echo $sid ?>_<?php echo $eid ?>_<?php echo $inctime ?>','<?php echo $bgcolor ?>',1);">
                                            <?php
                                            if ($tipcontent != "")
                                            {
                                                ?>
                                                <span class="hasTip" title="<?php echo $tipcontent?>">
                                                <?php
                                            }
                                            echo date($configClass['time_format'], $inctime);

                                            $user = JFactory::getUser();
                                            if (($configClass['allow_multiple_timezones'] == 1) && ($user->id > 0) && (OSBHelper::getConfigTimeZone() != OSBHelper::getUserTimeZone()))
                                            {
                                                echo "<BR />";
                                                echo "<span class='additional_timezone'>";
                                                echo OSBHelper::getUserTimeZone() . ": ";
                                                echo date($configClass['time_format'], OSBHelper::convertTimezone($inctime));
                                                ?>
												<?php
												if($configClass['show_end_time'] == 1){	
													?>
													&nbsp;-&nbsp;
													<?php
													echo date($configClass['time_format'], OSBHelper::convertTimezone($end_booking_time));
												}
                                                echo "</span>";
                                            }
                                            if ($tipcontent != "")
                                            {
                                                ?>
                                                </span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    }
											/*
									else
									{
										?>
										<div class="divtimeslots_simple"
                                             id="timeslot<?php echo $sid ?>_<?php echo $eid ?>_<?php echo $inctime ?>"
                                             style="background-color:<?php echo $configClass['booked_timeslot_background']; ?>;">
                                            <?php
                                            if ($tipcontent != "")
                                            {
                                                ?>
                                                <span class="hasTip" title="<?php echo $tipcontent?>">
                                                <?php
                                            }
                                            echo date($configClass['time_format'], $inctime);

                                            $user = JFactory::getUser();
                                            if (($configClass['allow_multiple_timezones'] == 1) and ($user->id > 0) and (OSBHelper::getConfigTimeZone() != OSBHelper::getUserTimeZone()))
                                            {
                                                echo "<BR />";
                                                echo "<span class='additional_timezone'>";
                                                echo OSBHelper::getUserTimeZone() . ": ";
                                                echo date($configClass['time_format'], OSBHelper::convertTimezone($inctime));
                                                ?>
												<?php
												if($configClass['show_end_time'] == 1){	
													?>
													&nbsp;-&nbsp;
													<?php
													echo date($configClass['time_format'], OSBHelper::convertTimezone($end_booking_time));
												}
                                                echo "</span>";
                                            }
                                            if ($tipcontent != "")
                                            {
                                                ?>
                                                </span>
                                                <?php
                                            }
                                            ?>
                                        </div>
										<?php
									}
									*/
                                    if($j==2)
									{ 
										$j = 0; 
									}
                                }
                            }
                        }

                    if($j == 1)
                    {
                        ?>
                        </div>
                        <?php
                    }
                    if($j==0)
                    {
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
                        for($i=0;$i<count($rows);$i++)
                        {
                            $bgcolor = "";
                            $row = $rows[$i];

                            $start_hour = $row->start_hour;
                            if($start_hour < 10)
                            {
                                $start_hour = "0".$start_hour;
                            }
                            $start_min = $row->start_min;
                            if($start_min < 10)
                            {
                                $start_min = "0".$start_min;
                            }

                            $start_time = $date[2]."-".$date[1]."-".$date[0]." ".$start_hour.":".$start_min.":00";
                            //echo $start_time;
                            $start_time_int = strtotime($start_time);

                            $end_hour = $row->end_hour;
                            if($end_hour < 10)
                            {
                                $end_hour = "0".$end_hour;
                            }
                            $end_min = $row->end_min;
                            if($end_min < 10)
                            {
                                $end_min = "0".$end_min;
                            }

                            $end_time = $date[2]."-".$date[1]."-".$date[0]." ".$end_hour.":".$end_min.":00";
                            $end_time_int = strtotime($end_time);

                            $tipcontent = "";
                            $tipcontentArr = array();
                            for($k=0;$k<count($timezone);$k++)
                            {
                                if($timezone[$k] != "")
                                {
                                    $tipcontentArr[] = $timezone[$k].": ".OSBHelper::showTime($timezone[$k],$start_time_int,$end_time_int);
                                }
                            }
                            if(count($tipcontentArr) > 0)
                            {
                                $tipcontent = implode(" | ",$tipcontentArr);
                            }

                            $db->setQuery("Select SUM(a.nslots) as nslots from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status IN ('P','S','A') and a.start_time =  '$start_time_int' and a.end_time = '$end_time_int' and a.sid = '$sid' and a.eid = '$eid'");
                            $nslotsbooked = $db->loadObject();
                            $count = intval($nslotsbooked->nslots);
                            $temp_start_hour = $row->start_hour;
                            $temp_start_min  = $row->start_min;
                            $temp_end_hour 	 = $row->end_hour;
                            $temp_end_min    = $row->end_min;

                            $db->setQuery("Select nslots from #__app_sch_custom_time_slots where sid = '$service->id' and start_hour = '$temp_start_hour' and start_min = '$temp_start_min' and end_hour = '$temp_end_hour' and end_min = '$temp_end_min' and id in (Select time_slot_id from #__app_sch_custom_time_slots_relation where date_in_week = '$date_in_week')");
                            $nslots = $db->loadResult();

                            //get the number count of the cookie table
                            $query = "SELECT SUM(a.nslots) as bnslots FROM #__app_sch_temp_order_items AS a INNER JOIN #__app_sch_temp_orders AS b ON a.order_id = b.id WHERE a.sid = '$sid' AND a.eid = '$eid' AND a.start_time =  '$start_time_int' and a.end_time = '$end_time_int'";
                            $db->setQuery($query);
                            $bslots = $db->loadObject();
                            $count_book = $bslots->bnslots;
                            $avail = $nslots - $count - $count_book;


                            //linked service
                            if($configClass['active_linked_service'] == 1)
                            {
                                $linkedservices = OSBHelper::getLinkedService($sid);
                                if(count($linkedservices) > 0)
                                {
                                    $db->setQuery("Select SUM(a.nslots) as nslots from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.order_status IN ('P','S','A') and ((a.start_time > '$start_time_int' and a.end_time < '$end_time_int') or (a.start_time < '$start_time_int' and a.end_time > '$end_time_int') or (a.start_time < '$end_time_int' and a.start_time > '$start_time_int') or (a.end_time < '$end_time_int' and a.end_time > '$start_time_int') or (a.start_time > '$start_time_int' and a.end_time < '$end_time_int') or (a.start_time = '$start_time_int' and a.end_time = '$end_time_int')) and a.sid in (".implode(',', $linkedservices).")");
                                    $nslotsbooked = $db->loadObject();
                                    $countLinkedSlotsBooked = intval($nslotsbooked->nslots);

                                    $query = "SELECT SUM(a.nslots) as bnslots FROM #__app_sch_temp_order_items AS a INNER JOIN #__app_sch_temp_orders AS b ON a.order_id = b.id WHERE a.sid in (".implode(',', $linkedservices).") and ((a.start_time > '$start_time_int' and a.end_time < '$end_time_int') or (a.start_time < '$start_time_int' and a.end_time > '$end_time_int') or (a.start_time < '$end_time_int' and a.start_time > '$start_time_int') or (a.end_time < '$end_time_int' and a.end_time > '$start_time_int') or (a.start_time > '$start_time_int' and a.end_time < '$end_time_int') or (a.start_time = '$start_time_int' and a.end_time = '$end_time_int'))";
                                    $db->setQuery($query);
                                    $bslots = $db->loadObject();
                                    $count_book_linked = $bslots->bnslots;

                                    $avail = $avail - $countLinkedSlotsBooked - $count_book_linked;
                                }
                            }

                            if($avail <= 0)
                            {
                                $bgcolor = $configClass['booked_timeslot_background'];
                                $nolink = true;
                            }
							else
							{
                                $bgcolor = $configClass['timeslot_background'];
                                $nolink = false;
                            }

                            if(($date[2] == date("Y",$realtime) and ($date[1] == intval(date("m",$realtime))) and ($date[0] == intval(date("d",$realtime)))))
							{
                                //today
                                if($start_time_int <= $realtime)
                                {
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                            }

                            if($disable_booking_before > 1)
                            {
                                if($start_time_int < $disable_time){
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                            }
                            if($disable_booking_after > 1)
                            {
                                if($start_time_int > $disable_time_after)
                                {
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                            }

                            if($configClass['multiple_work'] == 0)
                            {
                                if(!OSBHelper::checkMultipleEmployees($sid,$eid,$start_time_int,$end_time_int))
                                {
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                                if(!OSBHelper::checkMultipleEmployeesInTempOrderTable($sid,$eid,$start_time_int,$end_time_int))
                                {
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                            }

                            if($configClass['disable_timeslot'] == 1)
                            {
                                if(!OSBHelper::checkMultipleServices($sid,$eid,$start_time_int,$end_time_int))
                                {
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                                if(!OSBHelper::checkMultipleServicesInTempOrderTable($sid,$eid,$start_time_int,$end_time_int))
                                {
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                            }

							if($configClass['disable_venuetimeslot'] == 1)
							{
								if(!OSBHelper::checkMultipleVenues($eid,$vid,$start_time_int,$end_time_int))
								{
									$bgcolor = $configClass['booked_timeslot_background'];
									$nolink  = true;
								}
								if(!OSBHelper::checkMultipleVenuesInTempOrderTable($eid,$vid,$start_time_int,$end_time_int))
								{
									$bgcolor = $configClass['booked_timeslot_background'];
									$nolink  = true;
								}
							}

                            if(count($tempEmployee) > 0)
                            {
                                for($k=0;$k<count($tempEmployee);$k++)
                                {
                                    $employee = $tempEmployee[$k];
                                    $before_service = $employee->start_time;
                                    $after_service  = $employee->end_time;
                                    if(($employee->start_time < $start_time_int) && ($end_time_int < $employee->end_time))
                                    {
                                        //echo "1";
                                        if($avail <= 0 || $employee->show == 0 || $employee->priority == 1)
                                        {
                                            $bgcolor = $configClass['booked_timeslot_background'];
                                            $nolink = true;
                                        }
                                    }
									elseif(($employee->start_time > $start_time_int) && ($employee->start_time < $end_time_int))
                                    {
                                        //echo "2";
                                        if($avail <= 0 || $employee->show == 0 || $employee->priority == 1)
                                        {
                                            $bgcolor = $configClass['booked_timeslot_background'];
                                            $nolink = true;
                                        }
                                    }
									elseif(($employee->end_time > $start_time_int) && ($employee->end_time < $end_time_int))
                                    {
                                        //echo "3";
                                        if($avail <= 0 || $employee->show == 0 || $employee->priority == 1)
                                        {
                                            $bgcolor = $configClass['booked_timeslot_background'];
                                            $nolink = true;
                                        }
                                    }
									elseif(($employee->start_time <= $start_time_int && $employee->end_time > $end_time_int) || ($employee->start_time < $start_time_int && $employee->end_time >= $end_time_int))
                                    {
                                        if($avail <= 0 || $employee->show == 0 || $employee->priority == 1)
                                        {
                                            $bgcolor = $configClass['booked_timeslot_background'];
                                            $nolink = true;
                                        }
                                    }
									elseif($end_time_int <= $employee->start_time)
                                    {
                                        if($bgcolor != $configClass['booked_timeslot_background'])
                                        {
                                            $bgcolor = $configClass['timeslot_background'];
                                            $nolink = false;
                                        }
                                    }
									else
									{
                                        if($bgcolor != $configClass['booked_timeslot_background'])
                                        {
                                            $bgcolor = $configClass['timeslot_background'];
                                            $nolink = false;
                                        }
                                    }
                                }
                            }
                            if($disable_booking_before > 1)
                            {
                                if($start_time_int < $disable_time)
                                {
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                            }
                            if($disable_booking_after > 1)
                            {
                                if($start_time_int > $disable_time_after)
                                {
                                    $bgcolor = $configClass['booked_timeslot_background'];
                                    $nolink  = true;
                                }
                            }

                            if($avail <= 0)
                            {
                                //$bgcolor = $configClass['timeslot_background'];
								$bgcolor = $configClass['booked_timeslot_background'];
                                $nolink = true;
                            }

                            if ($configClass['booking_theme'] == 0)
                            {
                                if((($nolink) and (($configClass['show_occupied'] == 1)) or (!$nolink)))
                                {
                                    if (($end_time_int <= $endtimetoday) and ($start_time_int >= $starttimetoday))
                                    {
                                        $j++;
                                        $show_no_timeslot_text = 0;
                                        ?>
                                        <div class="<?php echo $mapClass['span6'];?> timeslots divtimeslots"
                                             style="background-color:<?php echo $bgcolor?>;">
                                            <?php
                                            if (!$nolink)
                                            {
                                                $text = "Book this employee from [" . date($configClass['date_time_format'], $start_time_int) . "] to [" . date($configClass['date_time_format'], $end_time_int) . "]";
                                                if($configClass['allow_multiple_timeslots'] == 1)
                                                {
                                                    ?>
                                                    <input type="checkbox" name="<?php echo $eid?>[]"
                                                           id="<?php echo $sid?>_<?php echo $eid?>_<?php echo $start_time_int?>"
                                                           onclick="javascript:addBookingMultiple('<?php echo $start_time_int?>','<?php echo $end_time_int;?>','<?php echo date($configClass['date_format'], $start_time_int);?> <?php echo date($configClass['time_format'], $start_time_int);?>','<?php echo date($configClass['date_format'], $end_time_int)?> <?php echo date($configClass['time_format'], $end_time_int)?>',<?php echo $eid?>,<?php echo $sid?>,'<?php echo JText::_('OS_SUMMARY');?>','<?php echo JText::_('OS_FROM');?>','<?php echo JText::_('OS_TO');?>','<?php echo $avail;?>');" />
                                                    <?php
                                                    $stroption[] = "<option value='".$start_time_int."-".$end_time_int."'>".date($configClass['date_time_format'], $start_time_int)." - ".date($configClass['date_time_format'], $end_time_int)."</option>";
                                                }
                                                else
                                                {
                                                    ?>
                                                    <input type="radio" name="<?php echo $eid?>[]"
                                                           id="<?php echo $sid?>_<?php echo $eid?>_<?php echo $start_time_int;?>"
                                                           onclick="javascript:addBooking('<?php echo $start_time_int?>','<?php echo $end_time_int;?>','<?php echo date($configClass['date_format'], $start_time_int);?> <?php echo date($configClass['time_format'], $start_time_int);?>','<?php echo date($configClass['date_format'], $end_time_int)?> <?php echo date($configClass['time_format'], $end_time_int)?>',<?php echo $eid?>,<?php echo $sid?>,'<?php echo JText::_('OS_SUMMARY');?>','<?php echo JText::_('OS_FROM');?>','<?php echo JText::_('OS_TO');?>','<?php echo $avail;?>');" />
                                                <?php } ?>
                                            <?php
                                            } 
											else 
											{
                                               
												if($configClass['waiting_list'] == 1 && $start_time_int > $realtime)
												{
													
													?>
													<a href="javascript:openWaitingList('<?php echo JUri::root(); ?>index.php?option=com_osservicesbooking&task=default_addtowaitinglist&tmpl=component&sid=<?php echo $sid ?>&eid=<?php echo $eid ?>&start=<?php echo $start_time_int; ?>&end=<?php echo $end_time_int; ?>');"
													   title="<?php echo JText::_('OS_CLICK_HERE_TO_ADD_TIMESLOT_TO_WAITING_LIST');?>" class="addtowaitinglistlink">
														[<?php echo JText::_('OS_ADD_TO_WAITING_LIST'); ?>]
													</a>
													<?php
												}
                                            }
                                            ?>
                                            &nbsp;&nbsp;
                                            <?php
                                            if ($tipcontent != "")
                                            {
                                            ?>
												<span class="hasTip" title="<?php echo $tipcontent?>" for="<?php echo $sid?>_<?php echo $eid?>_<?php echo $start_time_int;?>">
                                            <?php
                                            }
                                            $start_hour = $row->start_hour;
                                            if ($start_hour < 10)
                                            {
                                                $start_hour = "0" . $start_hour;
                                            }
                                            //echo ":";
                                            $start_min = $row->start_min;
                                            if ($start_min < 10)
                                            {
                                                $start_min = "0" . $start_min;
                                            }
                                            ?>
                                            <label for="<?php echo $sid?>_<?php echo $eid?>_<?php echo $start_time_int;?>" class="timeslotlabel">
                                            <?php
                                            echo date($configClass['time_format'], strtotime(date("Y-m-d", $start_time_int) . " " . $start_hour . ":" . $start_min . ":00"));
                                            ?>
											<?php
											if($configClass['show_end_time'] == 1){	
											?>
                                                &nbsp;-&nbsp;
                                                <?php
                                                $end_hour = $row->end_hour;
                                                if ($end_hour < 10) 
												{
                                                    $end_hour = "0" . $end_hour;
                                                }
                                                $end_min = $row->end_min;
                                                if ($end_min < 10) 
												{
                                                    $end_min = "0" . $end_min;
                                                }

                                                echo date($configClass['time_format'], strtotime(date("Y-m-d", $start_time_int) . " " . $end_hour . ":" . $end_min . ":00"));
											}
                                                ?>
                                                </label>
                                                <?php
                                                if ($tipcontent != "")
                                                {
                                                ?>
                                            </span>
                                        <?php
                                        }
										if($configClass['show_avail_slots'] == 1)
										{
                                        ?>
                                            &nbsp;-&nbsp;
                                            <?php
                                            echo JText::_('OS_AVAIL') . ": ";
                                            echo $avail;
										}
										$user = JFactory::getUser();
										if (($configClass['allow_multiple_timezones'] == 1) and ($user->id > 0) and (OSBHelper::getConfigTimeZone() != OSBHelper::getUserTimeZone())) {
											echo "<BR />";
											echo "<span class='additional_timezone'>";
											echo OSBHelper::getUserTimeZone() . ": ";
											echo date($configClass['time_format'], OSBHelper::convertTimezone(strtotime(date("Y-m-d", $start_time_int) . " " . $start_hour . ":" . $start_min . ":00")));
											?>
											&nbsp;-&nbsp;
											<?php
											echo date($configClass['time_format'], OSBHelper::convertTimezone(strtotime(date("Y-m-d", $start_time_int) . " " . $end_hour . ":" . $end_min . ":00")));
											echo "</span>";
										}
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
                            }
							else
							{
                                if((($nolink) and (($configClass['show_occupied'] == 1)) or (!$nolink)))
                                {
                                    if($end_time_int <= $endtimetoday && $start_time_int >= $starttimetoday)
                                    {
                                        $j++;
                                        $show_no_timeslot_text = 0;
                                        if($avail > 0 && !$nolink)
                                        {
                                        ?>
                                            <div class="divtimecustomslots_simple" style="background-color:<?php echo $bgcolor?>;"
                                                 onclick="javascript:addBookingSimple('<?php echo $start_time_int?>','<?php echo $end_time_int;?>','<?php echo date($configClass['date_format'],$start_time_int);?> <?php echo date($configClass['time_format'],$start_time_int);?>','<?php echo date($configClass['date_format'],$end_time_int)?> <?php echo date($configClass['time_format'],$end_time_int)?>',<?php echo $eid?>,<?php echo $sid?>,'<?php echo JText::_('OS_SUMMARY');?>','<?php echo JText::_('OS_FROM');?>','<?php echo JText::_('OS_TO');?>','ctimeslots<?php echo $sid ?>_e<?php echo $eid ?>_<?php echo $start_time_int?>','<?php echo $bgcolor ?>','<?php echo $avail;?>');"  ontouchstart="javascript:addBookingSimple('<?php echo $start_time_int?>','<?php echo $end_time_int;?>','<?php echo date($configClass['date_format'],$start_time_int);?> <?php echo date($configClass['time_format'],$start_time_int);?>','<?php echo date($configClass['date_format'],$end_time_int)?> <?php echo date($configClass['time_format'],$end_time_int)?>',<?php echo $eid?>,<?php echo $sid?>,'<?php echo JText::_('OS_SUMMARY');?>','<?php echo JText::_('OS_FROM');?>','<?php echo JText::_('OS_TO');?>','ctimeslots<?php echo $sid ?>_e<?php echo $eid ?>_<?php echo $start_time_int?>','<?php echo $bgcolor ?>','<?php echo $avail;?>');"
                                                 id="ctimeslots<?php echo $sid ?>_e<?php echo $eid ?>_<?php echo $start_time_int?>" >
                                                 <script type="text/javascript">
                                                 $("#ctimeslots<?php echo $sid ?>_e<?php echo $eid ?>_<?php echo $start_time_int?>").touchstart( function(){
                                                 });
                                                 </script>
                                        <?php
                                        }
										else
										{
                                        ?>
                                            <div class="divtimecustomslots_simple" style="background-color:<?php echo ($configClass['booked_timeslot_background'] != '') ? $configClass['booked_timeslot_background']:'red'; ?>;">
                                        <?php
                                        }
                                        ?>
                                            <?php
                                            if($tipcontent != "")
                                            {
                                            ?>
                                                <span class="hasTip" title="<?php echo $tipcontent?>">
                                            <?php
                                            }
                                            $start_hour = $row->start_hour;
                                            if($start_hour < 10){
                                                $start_hour = "0".$start_hour;
                                            }
                                            //echo ":";
                                            $start_min = $row->start_min;
                                            if($start_min < 10){
                                                $start_min = "0".$start_min;
                                            }

                                            echo date($configClass['time_format'],strtotime(date("Y-m-d",$start_time_int)." ".$start_hour.":".$start_min.":00"));
                                            ?>
											<?php
											if($configClass['show_end_time'] == 1){	
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
											}
                                            ?>

                                            <?php
                                            if($tipcontent != "")
                                            {
                                            ?>
                                                </span>
                                            <?php
                                            }
											if($configClass['show_avail_slots'] == 1)
											{
                                            ?>
                                            &nbsp;
                                            <?php
												echo JText::_('OS_AVAIL').": ";
												echo $avail;
                                            }
                                            $user = JFactory::getUser();
                                            if(($configClass['allow_multiple_timezones'] == 1) and ($user->id > 0) and (OSBHelper::getConfigTimeZone() != OSBHelper::getUserTimeZone())){
                                                echo "<BR />";
                                                echo "<span class='additional_timezone'>";
                                                echo OSBHelper::getUserTimeZone().": ";
                                                echo date($configClass['time_format'],OSBHelper::convertTimezone(strtotime(date("Y-m-d",$start_time_int)." ".$start_hour.":".$start_min.":00")));
                                                ?>
                                                &nbsp;-&nbsp;
                                                <?php
                                                echo date($configClass['time_format'],OSBHelper::convertTimezone(strtotime(date("Y-m-d",$start_time_int)." ".$end_hour.":".$end_min.":00")));
                                                echo "</span>";
                                            }
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

                if($show_no_timeslot_text == 1)
                {
                    ?>
                    <div class="no_available_time_slot">
                        <?php echo JText::_('OS_NO_AVAILABLE_TIME_SLOTS');?>
                    </div>
                    <?php
                }

                if($configClass['allow_multiple_timeslots'] == 1)
                {
                    ?>
                    <div style="display: none;" id="multipleoptionsdiv">
                    <select name="multiple_<?php echo $sid ?>_<?php echo $eid ?>[]" id="multiple_<?php echo $sid ?>_<?php echo $eid ?>" multiple>
                        <?php
                        echo implode("", (array)$stroption);
                        ?>
                    </select>
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
		
			<?php
			if(($service->repeat_day == 1  || $service->repeat_week == 1  || $service->repeat_fortnight == 1 || $service->repeat_month == 1) && $show_no_timeslot_text == 0)
			{
			?>
                <BR />
                <div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv repeatform">
                    <div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
                        <?php
                        echo JText::_('OS_REPEAT_BOOKING');
                        ?>
                    </div>
                    <div class="<?php echo $mapClass['span12'];?>" style="padding-top:10px;">
                        <div class="<?php echo $mapClass['row-fluid'];?>">
                            <div class="<?php echo $mapClass['span6'];?>">
                                <?php
                                echo JText::_('OS_REPEAT_BY');
                                ?>
                                <BR />
                                <select name="repeat_type_<?php echo $sid?>_<?php echo $eid?>" id="repeat_type_<?php echo $sid?>_<?php echo $eid?>" class="input-medium form-select" >
                                <option value=""></option>
                                <?php
                                if($service->repeat_day  == 1)
                                {
                                    ?>
                                    <option value="1"><?php echo JText::_('OS_REPEAT_BY_DAY');?></option>
                                    <?php
                                }
                                if($service->repeat_week  == 1)
                                {
                                    ?>
                                    <option value="2"><?php echo JText::_('OS_REPEAT_BY_WEEK');?></option>
                                    <?php
                                }
                                if($service->repeat_fortnight  == 1)
                                {
                                    ?>
                                    <option value="4"><?php echo JText::_('OS_REPEAT_BY_FORTNIGHTLY');?></option>
                                    <?php
                                }
                                if($service->repeat_month  == 1)
                                {
                                    ?>
                                    <option value="3"><?php echo JText::_('OS_REPEAT_BY_MONTH');?></option>
                                    <?php
                                }
                                ?>
                                </select>
                            </div>
                            <div class="<?php echo $mapClass['span6'];?>">
                                <?php
                                echo JText::_('OS_FOR_NEXT');
                                ?>
                                <BR />
                                <select name="repeat_to_<?php echo $sid?>_<?php echo $eid?>" class="input-mini form-select imini" id="repeat_to_<?php echo $sid?>_<?php echo $eid?>">
                                    <option value=""></option>
                                    <?php
                                    for($m=1;$m<=10;$m++){
                                        ?>
                                        <option value="<?php  echo $m?>"><?php echo $m?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php echo JText::_('OS_TIMES'); ?>
                            </div>
                        </div>
                    </div>
                </div>
			
			<?php
			}
			?>
			<?php
			if($service->service_time_type == 1)
			{
				if($configClass['show_number_timeslots_booking'] == 1)
				{
					?>
					<BR />
					<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv nslotform">
						<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
							
								<?php
								echo JText::_('OS_YOUR_NUMBER_SLOTS_WHICH_YOU_NEED');
								?>
							
						</div>
						<div class="<?php echo $mapClass['span12'];?>" style="padding-top:10px;">
							<?php
							
							if($service->max_seats > 0)
							{
								$maxValueHtml = 'max="'.$service->max_seats.'"';
							}
							echo JText::_('OS_HOW_MANY_SLOTS_WHICH_YOU_WANT_TO_BOOK').":";
							?>
							<input class="inlinedisplay <?php echo $mapClass['input-small']?> imini" type="number" name="nslots_<?php echo $sid?>_<?php echo $eid?>" id="nslots_<?php echo $sid?>_<?php echo $eid?>" value="1" <?php echo $maxValueHtml;?> step="1" min="1" onBlur="javascript:checkNumberSlotsSelected(<?php echo $sid?>,<?php echo $eid?>);" />
							<?php 
							OSBHelper::customServicesDiscountChecking($sid);
							?>
							<div class="clearfix"></div>
							<?php
							if($service->max_seats > 0)
							{
								echo "<span class='noticeMsg'>".JText::_('OS_LIMIT_NUMBER_SLOTS_IS')." ".$service->max_seats."</span>";
							}
							?>
						</div>
					</div>
					<?php
				}
				else
				{
					?>
					<input type="hidden" name="nslots_<?php echo $sid?>_<?php echo $eid?>" id="nslots_<?php echo $sid?>_<?php echo $eid?>" value="1"/>
					<?php 
				}
				?>
				<input type="hidden" name="max_seats_<?php echo $sid?>" id="max_seats_<?php echo $sid?>" value="<?php echo intval($service->max_seats);?>"/>
				<input type="hidden" name="max_allowed_seats_<?php echo $sid?>" id="max_allowed_seats_<?php echo $sid?>" value=""/>
				<input type="hidden" name="alrmsg" id="alrmsg" value="<?php echo JText::_('OS_INVALID_NUMBER');?>|<?php echo JText::_('OS_NOT_ENOUGH_AVAIALBLE_SEATS');?>|<?php echo JText::_('OS_EXCEED_ALLOWED_SEATS');?>|<?php echo JText::_('OS_PLEASE_SELECT_START_TIME');?>" />
				<?php
			}
			?>
			<?php
            if(OsAppscheduleDefault::checkExtraFields($sid,$eid))
            {
                echo OsAppscheduleDefault::loadExtraFields($sid, $eid);
                echo "<Br />";
            }
            ?>
			<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv addtocartform">
				<div class="<?php echo $mapClass['span12'];?>" style="text-align:center;">
					<div id="summary_<?php echo $sid?>_<?php echo $eid?>" style="padding:2px;text-align:left;" class="sumarry_div">
					</div>
					<?php
					if(HelperOSappscheduleCommon::alreadyHavingTimeslot())
					{
						echo JText::_('OS_YOU_CANNOT_ADD_MORE_THAN_ONE_TIMESLOT');
					}
					else
					{
					?>
						<input type="button" name="addtocartbtn" class="btn <?php echo $configClass['header_style']?>" value="<?php echo JText::_('OS_ADD_TO_CART')?>" onclick="javascript:addtoCart(<?php echo $sid?>,<?php echo $eid?>,<?php echo $service_total_int;?>)" />
					<?php
					}	
					?>
				</div>
			</div>
			</div>
		</div>
		<?php
		//show comment form
		HelperOSappscheduleCommon::showCommentForm($sid,$eid);
	}

	/**
	 * Check duplicate order item 
	 *
	 * @param unknown_type $userdata
	 */
	static function checkDuplicateOrderItem(){
		global $mainframe;
		$db = JFactory::getDbo();
		//$userdata = explode("||",$userdata);
		$unique_cookie = OSBHelper::getUniqueCookie();
		$return   = array();
		$sids	  = array();
		if(count($userdata) > 0){
			for($i=0;$i<count($userdata);$i++){
				$data 				= explode("|",$userdata[$i]);
				$sid 				= $data[0];
				$start_booking_date = $data[1];
				$end_booking_date 	= $data[2];
				$eid				= $data[3];
				$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
				$service = $db->loadObject();

				$service_before 	= intval($service->service_before);
				$service_after  	= intval($service->service_after);

				$start_time 		= $start_booking_date - $service_before;
				$end_time			= $end_booking_date + $service_after;

				$booking_date		= date("Y-m-d",$start_booking_date);

				//check to see if this employee is free in this time
				$query = "SELECT count(a.id) FROM #__app_sch_order_items AS a"
				." INNER JOIN #__app_sch_orders AS b ON b.id = a.order_id"
				." WHERE a.sid = '$sid' AND a.eid = '$eid' AND b.order_status in ('S','P') "
				." AND a.booking_date = '$booking_date' AND (((a.start_time <= '$start_time') AND (a.end_time >= '$start_time')) OR ((a.end_time >= '$end_time') AND (a.start_time <= '$end_time')) OR ((a.end_time >= '$end_time') AND (a.start_time <= '$start_time')))";
				$db->setQuery($query);
				$count = $db->loadResult();
				if($count > 0){
					$sids[count($sids)] = $sid;
				}
			}
		}
		if(count($sids) > 0){
			$return[0]->canCreateOrder = 0;
			$return[0]->sid			   = $sids;
		}else{
			$return[0]->canCreateOrder = 1;
		}

		return $return;
	}

	static function days_in_month($month, $year){
		// calculate number of days in a month
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
	}
	

	/**
	 * static function checkSlots
	 * if the custom time slot : check to see if is the any time slots available
	 * if the normal time slot : check to see if someone book the slot already
	 * check if it is offline date of service
	 * check if it is the rest day
	 * check if it isn't working day of employee
	 *
	 * @param unknown_type $row
	 */
	static function checkSlots($row)
	{
		global $mainframe,$mapClass,$configClass;
		$config 			= new JConfig();
		$offset 			= $config->offset;
		date_default_timezone_set($offset);
		$returnArr			= array();
		$db 				= JFactory::getDbo();
		$unique_cookie		= OSBHelper::getUniqueCookie();
		$start_time 		= $row->start_time;
		$end_time   		= $row->end_time;
		$booking_date 		= $row->booking_date;
		$sid				= $row->sid;
		$db->setQuery("Select service_time_type from #__app_sch_services where id = '$sid'");
		$service_time_type  = $db->loadResult();
		$eid 				= $row->eid;
		$nslots 			= $row->nslots;
		$booking_date_in_week = date("N",$start_time);
		$temp_start_min 	= intval(date("i",$start_time));
		$temp_start_hour  	= intval(date("H",$start_time));
		$temp_end_min   	= intval(date("i",$end_time));
		$temp_end_hour  	= intval(date("H",$end_time));
		//echo "1";
		//check multiple work
		if($configClass['multiple_work'] == 0)
		{
			if($service_time_type == 0)
			{
				$query = "Select count(b.id) from #__app_sch_temp_order_items as b inner join #__app_sch_temp_orders as a on a.id = b.order_id where unique_cookie not like '$unique_cookie' and b.eid = '$eid' and b.booking_date = '$booking_date' and ((b.start_time > '$start_time' and b.start_time < '$end_time') or (b.end_time > '$start_time' and b.end_time < '$end_time') or (b.start_time <= '$start_time' and b.end_time >= '$end_time') or (b.start_time >= '$start_time' and b.end_time <= '$end_time'))";
				$db->setQuery($query);
				//echo $db->getQuery();
				$count = $db->loadResult();
				//echo $count;
				if($count > 0)
				{
					if($count == 1)
					{
						$query = "Select count(b.id) from #__app_sch_temp_order_items as b inner join #__app_sch_temp_orders as a on a.id = b.order_id where unique_cookie like '$unique_cookie' and b.eid = '$eid' and b.sid = '$sid' and b.booking_date = '$booking_date' and ((b.start_time > '$start_time' and b.start_time < '$end_time') or (b.end_time > '$start_time' and b.end_time < '$end_time') or (b.start_time <= '$start_time' and b.end_time >= '$end_time') or (b.start_time >= '$start_time' and b.end_time <= '$end_time'))";
						$db->setQuery($query);
						$count1 = $db->loadResult();
						if($count1 > 0)
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}
			}
		}


		//
		//echo "3";
		//die();
		//echo "2";

		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service = $db->loadObject();
		//echo $booking_date."  : ".date("H:i",$start_time)." - ".date("H:i",$end_time);
		//is off day?
		$date_in_week       = date("N",$start_time);
		$db->setQuery("Select is_day_off from #__app_sch_working_time where id = '$date_in_week'");
		$is_day_off = $db->loadResult();
		if($is_day_off == 1)
		{
			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date')");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date') and is_day_off = '1'");
				$count = $db->loadResult();
				if($count > 0)
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		//echo "Off day";
		//echo "<BR />";
		//is off day in custom working time, for normal time slot only
		if($service->service_time_type == 1)
		{
			//check to see if the timeslot is available
			$db->setQuery("Select count(a.id) from #__app_sch_custom_time_slots as a left join #__app_sch_custom_time_slots_relation as b on a.id = b.time_slot_id where a.sid = '$sid' and a.start_hour = '$temp_start_hour' and a.start_min = '$temp_start_min' and a.end_hour = '$temp_end_hour' and a.end_min = '$temp_end_min' and a.nslots > '0' and b.date_in_week = '$booking_date_in_week'");
			//echo $db->getQuery();die();
			$count = $db->loadResult();
			//echo $count;die();
			if((int) $count == 0)
			{
				return false;
			}

			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date') and is_day_off = '1'");
			$count = $db->loadResult();
			if($count > 0)
			{
				return false;
			}
			else
			{ //book outtime of working time
				$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date')");
				$count = $db->loadResult();
				if($count > 0)
				{
					$db->setQuery("Select * from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date')");
					$working_time_custom = $db->loadObject();
					$start_working_time  = strtotime($booking_date." ".$working_time_custom->start_time);
					$end_working_time    = strtotime($booking_date." ".$working_time_custom->end_time);
					if(($start_time < $start_working_time) or  ($end_time > $end_working_time))
					{
						return false;
					}
				}
			}
		}
		//echo "Custom working";
		//echo "<BR />";
		//check rest day

		$db->setQuery("Select count(id) from #__app_sch_employee_rest_days where rest_date <= '$booking_date' and rest_date_to >= '$booking_date' and eid = '$eid'");
		$count = $db->loadResult();
		if($count > 0)
		{
			return false;
		}
		//echo "Rest";
		//echo "<BR />";
		//check if it isn't working day of employee
		$date_week       = substr(strtolower(date("l",$start_time)),0,2);
		$db->setQuery("Select count(id) from #__app_sch_employee_service where employee_id = '$eid' and service_id = '$sid' and  `$date_week` = '0'");
		$count = $db->loadResult();
		if($count > 0)
		{
			return false;
		}

		//check breaktime
		$sid				= $row->sid;
		$eid 				= $row->eid;
		$db->setQuery("Select * from #__app_sch_custom_breaktime where eid = '$eid' and sid = '$sid' and bdate = '$booking_date'");
		$custom_breaktimes  = $db->loadObjectList();
		if(count($custom_breaktimes) > 0)
		{
			foreach($custom_breaktimes as $breaktime)
			{
				$breaktimestart         = $breaktime->bdate." ".$breaktime->bstart.":00";
				$breaktimeend           = $breaktime->bdate." ".$breaktime->bend.":00";
				$breaktimestartint      = strtotime($breaktimestart);
				$breaktimeendint        = strtotime($breaktimeend);
				if(($start_time < $breaktimestartint && $end_time > $breaktimestartint) || ($start_time < $breaktimeendint && $end_time > $breaktimeendint) || ($start_time > $breaktimestartint && $end_time < $breaktimeendint) || ($start_time < $breaktimestartint && $end_time > $breaktimeendint))
				{
					return false;
				}
			}
		}

		$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$eid' and sid = '$sid' and `date_in_week` = '$booking_date_in_week'");
		$breaktimes = $db->loadObjectList();
		if(count($breaktimes) > 0)
		{
			foreach($breaktimes as $breaktime)
			{
				$breaktimestart = $booking_date." ".$breaktime->break_from;
				$breaktimeend   = $booking_date." ".$breaktime->break_to;
				$breaktimestartint = strtotime($breaktimestart);
				$breaktimeendint   = strtotime($breaktimeend);
				if(($start_time < $breaktimestartint && $end_time > $breaktimestartint) || ($start_time < $breaktimeendint && $end_time > $breaktimeendint) || ($start_time > $breaktimestartint && $end_time < $breaktimeendint) || ($start_time < $breaktimestartint && $end_time > $breaktimeendint))
				{
					return false;
				}
			}
		}

		if($service->service_time_type == 1)
		{
			if(!HelperOSappscheduleCalendar::checkCustomSlots($row))
			{
				return false;
			}
		}
		else
		{ //normal time slots
			if(!HelperOSappscheduleCalendar::checkNormalSlots($row))
			{
				return false;
			}
		}
		//echo "Time slot";
		//echo "<BR />";
		return true;
	}

	/**
	 * Return slots
	 *
	 * @param unknown_type $row
	 */
	static function returnSlots($row){
		global $mainframe,$mapClass,$configClass;
		$config 			= new JConfig();
		$offset 			= $config->offset;
		date_default_timezone_set($offset);
		$returnArr			= array();
		$db 				= JFactory::getDbo();
		$unique_cookie		= OSBHelper::getUniqueCookie();
		$start_time 		= $row->start_time;
		$end_time   		= $row->end_time;
		$booking_date 		= $row->booking_date;
		$sid				= $row->sid;
		$eid 				= $row->eid;
		$nslots 			= $row->nslots;
		$temp_start_min 	= intval(date("i",$start_time));
		$temp_start_hour  	= intval(date("H",$start_time));
		$temp_end_min   	= intval(date("i",$end_time));
		$temp_end_hour  	= intval(date("H",$end_time));
		
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service = $db->loadObject();
		//echo $booking_date."  : ".date("H:i",$start_time)." - ".date("H:i",$end_time);
		//is off day?
		$date_in_week       = date("N",$start_time);
		$db->setQuery("Select is_day_off from #__app_sch_working_time where id = '$date_in_week'");
		$is_day_off = $db->loadResult();
		if($is_day_off == 1)
		{
			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date')");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date') and is_day_off = '1'");
				$count = $db->loadResult();
				if($count > 0)
				{
					$row->return = 0;
					return $row;
				}
			}
			else
			{
				$row->return = 0;
				return $row;
			}
		}
		//echo "Off day";
		//echo "<BR />";
		//is off day in custom working time, for normal time slot only
		if($service->service_time_type == 1)
		{
			$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date') and is_day_off = '1'");
			$count = $db->loadResult();
			if($count > 0)
			{
				$row->return = 0;
			}
			else
			{ //book outtime of working time
				$db->setQuery("Select count(id) from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date')");
				$count = $db->loadResult();
				if($count > 0)
				{
					$db->setQuery("Select * from #__app_sch_working_time_custom where (worktime_date <= '$booking_date' and worktime_date_to >= '$booking_date')");
					$working_time_custom = $db->loadObject();
					$start_working_time  = strtotime($booking_date." ".$working_time_custom->start_time);
					$end_working_time    = strtotime($booking_date." ".$working_time_custom->end_time);
					if(($start_time < $start_working_time) or  ($end_time > $end_working_time))
					{
						$row->return = 0;
					}
				}
			}
		}
		//echo "Custom working";
		//echo "<BR />";
		//check rest day
		$db->setQuery("Select count(id) from #__app_sch_employee_rest_days where rest_date <= '$booking_date' and rest_date_to >= '$booking_date' and eid = '$eid'");
		$count = $db->loadResult();
		if($count > 0)
		{
			$row->return = 0;
			return $row;
		}
		//echo "Rest";
		//echo "<BR />";
		//check if it isn't working day of employee
		$date_week       = substr(strtolower(date("l",$start_time)),0,2);
		$db->setQuery("Select count(id) from #__app_sch_employee_service where employee_id = '$eid' and service_id = '$sid' and  `$date_week` = '0'");
		$count = $db->loadResult();
		if($count > 0){
			$row->return = 0;
			return $row;
		}
		//echo "Isnt working";
		//echo "<BR />";
		//custom time slot
		
		if($service->service_time_type == 1)
		{
			if(!HelperOSappscheduleCalendar::checkCustomSlots($row))
			{
				$row->return 	= 1;
				if(HelperOSappscheduleCalendar::returnCustomSlots($row) > 0)
				{
					$row->number_slots_available =  HelperOSappscheduleCalendar::returnCustomSlots($row);
				}
				else
				{
					$row->return = 0;	
				}
				return $row;
			}
		}
		//echo "Time slot";
		//echo "<BR />";
		return $row;
	}
	
	static function returnCustomSlots($row){
		global $mainframe,$mapClass,$configClass;
		$config				= new JConfig();
		$offset 			= $config->offset;
		date_default_timezone_set($offset);
		$db 				= JFactory::getDbo();
		$unique_cookie		= OSBHelper::getUniqueCookie();
		$start_time 		= $row->start_time;
		$end_time   		= $row->end_time;
		$booking_date 		= $row->booking_date;
		$sid				= $row->sid;
		$eid 				= $row->eid;
		$nslots 			= $row->nslots;
		$temp_start_min 	= intval(date("i",$start_time));
		$temp_start_hour  	= intval(date("H",$start_time));
		$temp_end_min   	= intval(date("i",$end_time));
		$temp_end_hour  	= intval(date("H",$end_time));

		$db->setQuery("Select nslots from #__app_sch_custom_time_slots where sid = '$sid' and start_hour = '$temp_start_hour' and start_min = '$temp_start_min' and end_hour = '$temp_end_hour' and end_min = '$temp_end_min'");
		$number_slots_in_db = $db->loadResult();

		$query = "Select sum(a.nslots) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid = '$row->sid' and a.eid = '$row->eid;' and a.start_time = '$start_time' and a.end_time = '$end_time'";
		$db->setQuery($query);
		$count = $db->loadResult();
		$number_slots_available = $number_slots_in_db - $count;
		return $number_slots_available;
	}
	
	/**
	 * Check one time slot if it is available
	 *
	 * @param unknown_type $row
	 */
	static function checkNormalSlots($row)
	{
		global $mainframe,$mapClass,$configClass;
		
		$db 				= JFactory::getDbo();
		$unique_cookie		= OSBHelper::getUniqueCookie();
		$start_time 		= $row->start_time;
		$end_time   		= $row->end_time;
		$booking_date 		= $row->booking_date;
		$sid				= $row->sid;
		$eid 				= $row->eid;
		$temp_start_min 	= intval(date("i",$start_time));
		$temp_start_hour  	= intval(date("H",$start_time));
		$temp_end_min   	= intval(date("i",$end_time));
		$temp_end_hour  	= intval(date("H",$end_time));
		
		//check in the table order_itesm first
		$query = "SELECT COUNT(a.id) FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON b.id = a.order_id WHERE b.order_status IN ('P','S','A') AND a.sid = '$sid' AND a.eid = '$eid' AND a.booking_date = '$booking_date' AND ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time')) ";
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count > 0)
		{
			return false;
		}

		$query = "SELECT COUNT(a.id) FROM #__app_sch_order_items AS a INNER JOIN #__app_sch_orders AS b ON b.id = a.order_id WHERE b.order_status IN ('P','S','A') AND a.sid = '$sid' AND a.eid = '$eid' AND a.booking_date = '$booking_date' AND (a.start_time = '$start_time' AND a.end_time = '$end_time') ";
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count > 0)
		{
			return false;
		}

		//checck in the temp table to see if user already book this time slots
		$query = "SELECT COUNT(a.id) FROM #__app_sch_temp_order_items AS a INNER JOIN #__app_sch_temp_orders AS b ON b.id = a.order_id WHERE a.booking_date = '$booking_date' AND a.sid = '$sid' AND a.eid = '$eid' AND b.unique_cookie = '$unique_cookie' AND ((a.start_time > '$start_time' and a.end_time < '$end_time') or (a.start_time < '$start_time' and a.end_time > '$end_time') or (a.start_time < '$end_time' and a.start_time > '$start_time') or (a.end_time < '$end_time' and a.end_time > '$start_time') or (a.start_time = '$start_time' and a.end_time = '$end_time') or (a.start_time = '$start_time' and a.end_time < '$end_time') or (a.start_time = '$start_time' and a.end_time > '$end_time') or (a.start_time > '$start_time' and a.end_time = '$end_time') or (a.start_time < '$start_time' and a.end_time = '$end_time')) ";
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count > 0)
		{
			return false;
		}

		$query = "SELECT COUNT(a.id) FROM #__app_sch_temp_order_items AS a INNER JOIN #__app_sch_temp_orders AS b ON b.id = a.order_id WHERE a.booking_date = '$booking_date' AND a.sid = '$sid' AND a.eid = '$eid' AND (a.start_time = '$start_time' AND a.end_time = '$end_time') ";
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count > 0)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Check number of available slots of custom time slots
	 *
	 * @param unknown_type $row
	 */
	static function checkCustomSlots($row)
	{
		global $mainframe,$mapClass,$configClass;
		$db 				= JFactory::getDbo();
		$unique_cookie		= OSBHelper::getUniqueCookie();
		$start_time 		= $row->start_time;
		$end_time   		= $row->end_time;
		$booking_date 		= $row->booking_date;
		$sid				= $row->sid;
		$eid 				= $row->eid;
		$nslots 			= $row->nslots;
		$temp_start_min 	= intval(date("i",$start_time));
		$temp_start_hour  	= intval(date("H",$start_time));
		$temp_end_min   	= intval(date("i",$end_time));
		$temp_end_hour  	= intval(date("H",$end_time));

		$db->setQuery("Select nslots from #__app_sch_custom_time_slots where sid = '$sid' and start_hour = '$temp_start_hour' and start_min = '$temp_start_min' and end_hour = '$temp_end_hour' and end_min = '$temp_end_min'");
		$number_slots_in_db = $db->loadResult();
		
		//select in order_items
		$query = "Select sum(a.nslots) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where a.sid = '$sid' and a.eid = '$eid' and a.start_time = '$start_time' and a.end_time = '$end_time' and b.order_status IN ('P','S','A')";
		$db->setQuery($query);
		$remain_slots = $db->loadResult();
		$query = "Select sum(a.nslots) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie like '$unique_cookie' and a.sid = '$sid' and a.eid = '$eid' and a.start_time = '$start_time' and a.end_time = '$end_time'";
		$db->setQuery($query);
		$count = $db->loadResult();
		
		$number_slots_available = $number_slots_in_db - $count - $remain_slots;
		if($number_slots_available < $nslots)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Calculate booking date for repeat booking static function
	 *
	 * @param unknown_type $from_date
	 * @param unknown_type $to_date
	 * @param unknown_type $type /day,week,month:  1,2,3
	 */
	static function calculateBookingDate($from_date,$to_date,$type){
		global $mainframe;
		switch ($type){
			case "1":
				$returnArr = HelperOSappscheduleCalendar::calculateBookingDateFollowingDay($from_date,$to_date);
				break;
			case "2":
				$returnArr = HelperOSappscheduleCalendar::calculateBookingDateFollowingWeek($from_date,$to_date);
				break;
			case "3":
				$returnArr = HelperOSappscheduleCalendar::calculateBookingDateFollowingMonth($from_date,$to_date);
				break;
            case "4":
                $returnArr = HelperOSappscheduleCalendar::calculateBookingDateFollowingFortnightly($from_date, $to_date);
                break;
		}
		return $returnArr;
	}

	static function calculateBookingDateFollowingDay($from_date,$to_date)
    {
		$returnArr          = array();
		$from_date_int      = strtotime($from_date);
		$to_date_int        = $from_date_int + 24*3600*$to_date;
        $i                  = $from_date_int;
		while($i < $to_date_int)
        {
            if(OSBHelper::isOffDay($i))
            {
                $to_date_int = $to_date_int + 24*3600;
            }
            else
            {
                $returnArr[count($returnArr)] = date("Y-m-d",$i);
            }
            $i              = $i+24*3600;
        }
		return $returnArr;
	}

	static function calculateBookingDateFollowingWeek($from_date,$to_date)
    {
		$returnArr          = array();
		$config             = new JConfig();
		$offset             = $config->offset;
		date_default_timezone_set($offset);
		$returnArr          = array();
		$from_date_int      = strtotime($from_date);
		$from_date          = date("Y-m-d",$from_date_int);
		$from_date          .= " 12:00:00";
		$from_date_int      = strtotime($from_date);

		$to_date_int        = $from_date_int + 7*24*3600*$to_date;
		$i                  = $from_date_int;
		//for($i = $from_date_int;$i < $to_date_int;$i = $i+24*3600*7)
		//{
			//$returnArr[count($returnArr)] = date("Y-m-d",$i);
		//}

		while($i < $to_date_int)
        {
            if(OSBHelper::isOffDay($i))
            {
                $to_date_int = $to_date_int + 24*3600*7;
            }
            else
            {
                $returnArr[count($returnArr)] = date("Y-m-d",$i);
            }
            $i              = $i+24*3600*7;
        }

		return $returnArr;
	}

    static function calculateBookingDateFollowingFortnightly($from_date,$to_date)
    {
        $returnArr          = array();
        $config             = new JConfig();
        $offset             = $config->offset;
        date_default_timezone_set($offset);
        $returnArr          = array();
        $from_date_int      = strtotime($from_date);
        $from_date          = date("Y-m-d",$from_date_int);
        $from_date          .= " 12:00:00";
        $from_date_int      = strtotime($from_date);

        $to_date_int        = $from_date_int + 2*7*24*3600*$to_date;

        for($i = $from_date_int;$i < $to_date_int;$i = $i + 2*24*3600*7)
        {
            $returnArr[count($returnArr)] = date("Y-m-d",$i);
        }
        return $returnArr;
    }

	static function calculateBookingDateFollowingMonth($from_date,$to_date)
    {
		$returnArr      = array();
		$from_date_int  = strtotime($from_date);

        $current_month  = intval(date("m",$from_date_int));
        if($current_month + $to_date > 12)
        {
            $month      = $current_month + $to_date - 12;
            $year       = date("Y",$from_date_int) + 1;
        }
        else
        {
            $month      = $current_month + $to_date;
            $year       = date("Y",$from_date_int);
        }
        $day            = date("d",$from_date_int);
        $to_date_int    = strtotime($year."-".$month."-".$day);

		$i              = $from_date_int;
		while ($i < $to_date_int)
        {
			if(OSBHelper::isOffDay($i))
            {
                $d = intval(date("d",$i));
				$m = intval(date("m",$i));
				$y = intval(date("Y",$i));
				if($m == 12)
				{
					$y = $y + 1;
					$m = 1;
				}
				else
				{
					$m =  $m + 1;
				}
				$to_date_int = strtotime($y."-".$m."-".$d);
            }
            else
            {
				$returnArr[count($returnArr)] = date("Y-m-d",$i);
				$d = intval(date("d",$i));
				$m = intval(date("m",$i));
				$y = intval(date("Y",$i));
				if($m == 12)
				{
					$y = $y + 1;
					$m = 1;
				}
				else
				{
					$m =  $m + 1;
				}
				$i = strtotime($y."-".$m."-".$d);
			}
		}
		return $returnArr;
	}
}
?>