<?php
/*------------------------------------------------------------------------
# ajax.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 Joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;


class HTML_OsAppscheduleAjax{

	static function showCalendarViewHtml($id, $vid ,$month,$year){
		global $mainframe,$mapClass,$configClass;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		?>
		<div class="<?php echo $mapClass['row-fluid'];?>" id="calendarView_div">
			<div class="<?php echo $mapClass['span12'];?>">
				<?php
				$today						= OSBHelper::getCurrentDate();
				$current_month 				= intval(date("m",$today));
				$current_year				= intval(date("Y",$today));
				$current_date				= intval(date("d",$today));
				//set up the first date
				$start_date_current_month 	= strtotime($year."-".$month."-01");
				//$start_date_in_week			= date("N",$start_date_current_month);
				if($configClass['start_day_in_week'] == "monday")
				{
					$start_date_in_week		= date("N",$start_date_current_month);
				}
				else
				{
					$start_date_in_week		= date("w",$start_date_current_month);	
				}
				$number_days_in_month		= cal_days_in_month(CAL_GREGORIAN,$month,$year);
				$monthArr 					= array( JText::_('OS_JANUARY'), JText::_('OS_FEBRUARY'), JText::_('OS_MARCH'), JText::_('OS_APRIL'), JText::_('OS_MAY'), JText::_('OS_JUNE'), JText::_('OS_JULY'), JText::_('OS_AUGUST'), JText::_('OS_SEPTEMBER'), JText::_('OS_OCTOBER'), JText::_('OS_NOVEMBER'), JText::_('OS_DECEMBER'));
				$suffix 					= "";
				
				?>
				<div id="cal<?php echo intval($month)?><?php echo $year?>">
					<table  width="100%" class="apptable">
						<tr>
							<td width="40%" align="right" style="font-weight:bold;span-size:15px;text-align:left !important;">
								<a href="javascript:previousCalendarViewNonAjax(<?php echo $id;?>);" class="applink">
									<strong><</strong>
								</a>
							</td>
							<td width="20%" align="center" style="height:25px;font-weight:bold;">
								<?php
								echo $monthArr[$month-1];
								?>
								&nbsp;
								<?php echo $year;?>
							</td>
							<td width="40%" align="left" style="font-weight:bold;span-size:15px;text-align:right !important;">
								<a href="javascript:nextCalendarViewNonAjax(<?php echo $id;?>);" class="applink">
									<strong>></strong>
								</a>
							</td>
						</tr>
						<tr>
							<td width="100%" colspan="3" style="padding:3px;text-align:center;">
								<div class="<?php echo $mapClass['row-fluid'];?>">
									<div class="<?php echo $mapClass['span12'];?> input-append">
										<select name="ossm" class="input-medium form-select" id="ossm" onchange="javascript:updateMonthCalendarView(this.value)">
											<?php
											for($i=0;$i<count($monthArr);$i++){
												if(intval($month) == $i + 1){
													$selected = "selected";
												}else{
													$selected = "";
												}
												?>
												<option value="<?php echo $i + 1?>" <?php echo $selected?>><?php echo $monthArr[$i]?></option>
												<?php
											}
											?>
										</select>
										<select name="ossy" class="input-small form-select" id="ossy" onchange="javascript:updateYearCalendarViewNonAjax(this.value)">
											<?php
											for($i=date("Y",$today);$i<=date("Y",$today)+3;$i++){
												if(intval($year) == $i){
													$selected = "selected";
												}else{
													$selected = "";
												}
												?>
												<option value="<?php echo $i?>" <?php echo $selected?>><?php echo $i?></option>
												<?php
											}
											?>
										</select>
										<a href="javascript:void(0);" onClick="javascript:movingCalendarViewNonAjax('<?php echo $id;?>');" class="btn btn-secondary"><?php echo JText::_('OS_GO');?></a>
									</div>
								</div>

							</td>
						</tr>
					</table>
					<div id="calendarView_divreturn">
						<?php
						self::showCalendarViewMain($start_date_in_week,$number_days_in_month,$current_month,$current_year,$current_date,$date,$id,$vid,$month, $year);
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	static function showCalendarViewMain($start_date_in_week,$number_days_in_month,$current_month,$current_year,$current_date,$date,$id,$vid,$month, $year){
		global $configClass;
		?>
		<table  width="100%">
			<tr>
				<?php
				if($configClass['start_day_in_week'] == "sunday")
				{
				?>
					<td width="14%" class="header_calendarview">
						<?php echo JText::_('OS_SUN')?>
					</td>
				<?php
				}
				?>
				<td  width="14%" class="header_calendarview">
					<?php echo JText::_('OS_MON')?>
				</td>
				<td  width="14%" class="header_calendarview">
					<?php echo JText::_('OS_TUE')?>
				</td>
				<td width="14%" class="header_calendarview">
					<?php echo JText::_('OS_WED')?>
				</td>
				<td width="14%" class="header_calendarview">
					<?php echo JText::_('OS_THU')?>
				</td>
				<td width="14%" class="header_calendarview">
					<?php echo JText::_('OS_FRI')?>
				</td>
				<td width="14%" class="header_calendarview">
					<?php echo JText::_('OS_SAT')?>
				</td>
				<?php
				if($configClass['start_day_in_week'] == "monday")
				{
				?>
					<td width="14%" class="header_calendarview">
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
				for($i=$start;$i<$start_date_in_week;$i++)
				{
					?>
					<td>

					</td>
					<?php
				}
				$j = $start_date_in_week-1;

				$m = "";
				if(intval($month) < 10){
					$m = "0".$month;
				}else{
					$m = $month;
				}
				$month = $m;

				for($i=1;$i<=$number_days_in_month;$i++)
				{
					$j++;
					$nolink = 0;
					//check to see if today
					$today = strtotime($current_year."-".$current_month."-".$current_date);
					$extra_classname = "";
					$checkdate = strtotime($year."-".$month."-".$i);
					$services  = OSBHelper::getServices($category,$employee_id,$vid);
					$employees = OSBHelper::loadEmployees($services,$employee_id,$checkdate,$vid);
					$venue_check = 1;
					if($vid > 0)
					{
						//$venue_check = OSBHelper::checkDateInVenue($vid,$checkdate);
					}
					if(($i == $current_date) and ($month == $current_month) and ($year == $current_year))
					{
						$extra_classname = "current_date";
					}
					elseif(OSBHelper::isOffDay($checkdate))
					{
						$nolink = 1;
						$extra_classname = "disabled";
					}
					elseif(count($services) == 0)
					{
						$nolink = 1;
						$extra_classname = "disabled";
					}
					elseif(! $employees)
					{
						$nolink = 1;
						$extra_classname = "disabled";
					}
					elseif($venue_check == 0)
					{
						$nolink = 1;
						$extra_classname = "disabled";
					}

					if($i < 10)
					{
						$day = "0".$i;
					}
					else
					{
						$day = $i;
					}
					$tempdate1 = strtotime($year."-".$month."-".$day);
					$tempdate2 = strtotime($current_year."-".$current_month."-".$current_date);

					if((int)$tempdate1 < (int)$tempdate2)
					{
						$extra_classname = "disabled";
						$nolink = 1;
					}

					if($i < 10)
					{
						$day = "0".$i;
					}
					else
					{
						$day = $i;
					}
					$date = $year."-".$month."-".$day;

					?>
					<td id="td_cal_<?php echo $i?>" class="calendarview_td_date">
						<div id="a<?php echo $i;?>" style="border:1px solid #efefef;" class="calendarview-div-rounded <?php echo $extra_classname;?>">
							<?php
							OsAppscheduleAjax::calendarViewItem($i,$id,$vid,$date,$nolink);
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
		<?php
	}

	static function showCalendarViewItemHtml($idnumber,$sid,$vid,$date,$employees,$nolink)
	{
		?>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<div class="calendarView-date">
					<?php
					$dateArr = explode("-",$date);
					echo $dateArr[2];
					?>
				</div>
				<?php
				if($nolink == 0)
				{
				?>
				<div class="calendarView-employees">
					<?php
					foreach($employees as $employee)
					{
						?>
						<div class="calendarView-employee">
							<span title="" id="employee_<?php echo $employee->id?>_<?php echo $date;?>">
								<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=ajax_showEmployeeTimeslots&tmpl=component&sid=<?php echo $sid;?>&eid=<?php echo $employee->id;?>&date=<?php echo $date;?>&venue_id=<?php echo $vid;?>" class="osmodal" rel="{size: {x: 400, y: 200}}">
									<?php
									echo $employee->employee_name;
									?>
								</a>
							</span>
						</div>
						<?php
					}
					?>
				</div>
				<?php 
				} 
				?>
			</div>
		</div>
		<?php
	}
	
	static function loadServicesHTML($option,$services,$year,$month,$day,$is_day_off,$category_id,$employee_id,$vid,$sid1,$eid,$reason = "")
	{
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		//jimport('joomla.html.pane');
        $pane =& JPane::getInstance('tabs');
		if($is_day_off == 1)
		{
			?>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>" style="border:1px solid #B3BED3;">
					<div class="sub_header">
						<?php
						$tday = strtotime($year."-".$month."-".$day);
						echo date($configClass['date_format'],$tday);
						?>
					</div>
					<div class="clearfix"></div>
					<div style="padding:10px;">
						<?php 
						if($reason != "")
						{
							echo $reason;
						}
						else{
							echo JText::_('OS_DAY_OFF');
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		else
		{
			if(count($services) > 0)
			{
			?>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
                    <?php
                    if(($configClass['hidetabs'] == 1) && (count($services) == 1)){
                        //nothing show
                    }
                    else
                    {
                        if($configClass['usingtab'] == 1)
                        {
                            if(count($services) > 1)
                            {
                                ?>
                                <div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv norightleftmargin" style="text-align:center;">
                                    <div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>" style="margin-bottom:10px;">
                                        <strong><?php echo JText::_('OS_SELECT_SERVICE');?></strong>
                                    </div>
                                    <select name="serviceslist" id="serviceslist" class="input-large form-select" onChange="javascript:changingService();">
                                    <?php
                                    $tempArr = array();
                                    for($i=0;$i<count($services);$i++)
                                    {
                                        $service = $services[$i];
										if($sid1 > 0)
										{
											if($sid1 == $service->id)
											{
												$selected = "selected";
												$service->display = "display:block";
											}else{
												$selected = "";
											$service->display = "display:none";
											}
										}
										else
										{
											if($i==0)
											{
												$selected = "selected";
												$service->display = "display:block";
											}else{
												$selected = "";
												$service->display = "display:none";
											}
										}
                                        ?>
                                        <option <?php echo $selected; ?> value="<?php echo $service->id;?>"><?php echo OSBHelper::getLanguageFieldValue($service,'service_name'); ?></option>
                                        <?php
                                        $tempArr[] = $service->id;
                                    }
                                    ?>
                                    </select>
                                </div>
                                <?php
                                $temp = implode("|",$tempArr);
                                ?>
                                <input type="hidden" name="serviceslist_ids" id="serviceslist_ids" value="<?php echo $temp; ?>" />
                                <?php
                            }
                        }
                    }
                    ?>
					<div class="<?php echo $mapClass['row-fluid'];?> noleftrightpadding noleftrightmargin">
						<?php
						//setup main bootstrap
                        //print_r($services);
						if((count($services) >1) && ($configClass['usingtab'] == 0))
						{
                            if($sid1 > 0)
                            {
                                $class = 'pane'.$sid1;
                            }else{
                                $class = 'pane'.$services[0]->id;
                            }
                            //echo $class;
							echo JHtml::_('bootstrap.startTabSet', 'services', array('active' => $class));
						}

                        for($i=0;$i<count($services);$i++){

                            $service = $services[$i];

                            $date = $year."-".$month."-".$day;
                            $dateArr[0] = $day;
                            $dateArr[1] = $month;
                            $dateArr[2] = $year;
                            $db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where a.sid = '$service->id' and b.order_status like 'P'");
                            $count_pending = $db->loadResult();
                            $count_pending = intval($count_pending);

                            $db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where a.sid = '$service->id' and b.order_status like 'S'");
                            $count_success = $db->loadResult();
                            $count_success = intval($count_success);

                            $userdata 			= $_COOKIE['userdata'];
                            $temp 				= explode("||",$userdata);
                            $count_select  = 0;
                            for($j=0;$j<count($temp);$j++){
                                $data 				= $temp[$j];
                                $data 				= explode("|",$data);
                                $sid  				= $data[0];
                                $start_booking_date = $data[1];
                                $end_booking_date   = $data[2];

                                if($month < 10){
                                    $m = "0".$month;
                                }else{
                                    $m = $month;
                                }

                                if($day < 10){
                                    $d = "0".$day;
                                }else{
                                    $d = $day;
                                }
                                if(($sid == $service->id) and (date("Y-m-d",$start_booking_date) == $year."-".$m."-".$d)){
                                    $count_select++;
                                }
                            }

                            if($sid1 > 0){
                                if($sid1 == $service->id){
                                    $class = ' active';
                                }else{
                                    $class = '';
                                }
                            }else{
                                if($i==0){
                                    $class = ' active';
                                }else{
                                    $class = '';
                                }
                            }
                            ?>
                            <?php
                            if((count($services) >1) && ($configClass['usingtab'] == 0)){
                                echo JHtml::_('bootstrap.addTab', 'services', 'pane'.$service->id, OSBHelper::getLanguageFieldValue($service,'service_name'));
                            }
                            ?>
							<input type="hidden" name="service_time_type_<?php echo $service->id; ?>" id="service_time_type_<?php echo $service->id ;?>" value="<?php echo $service->service_time_type ;?>" />
                            <div id="pane<?php echo $service->id;?>" class="<?php echo $mapClass['span12'];?> bookingformdiv noleftrightmargin" style="<?php echo $service->display;?>">
                                <div class="<?php echo $mapClass['row-fluid'];?>">
                                    <?php
                                    if(count($services) > 1){
                                    ?>
                                        <div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
                                            <?php
                                            echo OSBHelper::getLanguageFieldValue($service,'service_name');
                                            ?>
                                            (<?php
                                            $tday = strtotime($year."-".$month."-".$day);
                                            echo date($configClass['date_format'],$tday);
                                            ?>)
                                        </div>
                                        <div class="clearfix"></div>
                                    <?php
                                    }
                                    if(((($configClass['show_service_photo'] == 1) or ($configClass['show_service_description'] == 1) or ($configClass['show_service_info_box'] == 1)) and (count($services) > 1))){
                                    ?>

                                    <div class="<?php echo $mapClass['row-fluid'];?> servicetab">
                                        <div class="<?php echo $mapClass['span12'];?>">
                                            <?php
                                            if($configClass['show_service_photo'] == 1){
                                            ?>
                                                <div class="service_photo">
                                                    <?php
                                                    if( $service->service_photo != ""){
                                                    ?>
                                                        <img src="<?php echo JURI::root()?>images/osservicesbooking/services/<?php echo $service->service_photo?>" width="120" style="border:1px solid #CCC;padding:2px;">
                                                    <?php
                                                    }else{
                                                    ?>
                                                        <img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/noimage.png" width="120" style="border:1px solid #CCC;padding:2px;">
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_service_info_box'] == 1){
                                                echo '<div class="service_information_box">';
                                                    HelperOSappscheduleCommon::getServiceInformation($service,$year,$month,$day,$employee_id);
                                                echo '</div>';
                                            }
                                            if($configClass['show_service_description'] == 1)
											{
                                                $desc = OSBHelper::getLanguageFieldValue($service,'service_description');
												echo JHtml::_('content.prepare', $desc);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="clearfix"></div>
                                    <div class="<?php echo $mapClass['span12'];?> employeetabs">
                                        <?php
                                        //echo $eid;
                                        OsAppscheduleAjax::loadEmployees($option,$service->id,$employee_id,$dateArr,$vid,$sid1,$eid);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if((count($services) >1) && ($configClass['usingtab'] == 0))
                            {
                                echo JHtml::_('bootstrap.endTab');
                            }
                        }
						if((count($services) >1 ) && ($configClass['usingtab'] == 0))
						{
							echo JHtml::_('bootstrap.endTabSet');
						}
						//echo $pane->endPane();
						?>
					</div> <!-- Tab content -->
				</div>
			</div>
			<?php
			}
			else
			{
				?>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?>" style="border:1px solid #B3BED3;">
						<div class="sub_header">
							<?php
							$tday = strtotime($year."-".$month."-".$day);
							echo date($configClass['date_format'],$tday);
							?>
						</div>
						<div class="clearfix"></div>
						<div style="padding:10px;">
							<?php echo JText::_('OS_UNAVAILABLE')?>
						</div>
					</div>
				</div>
				<?php
			}
		}
		
	}
	
	
	/**
	 * Load Employees frames
	 *
	 * @param unknown_type $option
	 * @param unknown_type $employees
	 */
	static function loadEmployeeFrames($option,$employees,$sid,$date,$vid,$service_id,$eid)
    {
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		if(count($employees) > 1)
		{
		?>
            <div class="<?php echo $mapClass['row-fluid'];?> makebooking">
                <div class="<?php echo $mapClass['span12'];?>">
                    <strong><?php echo JText::_('OS_SELECT_EMPLOYEE_AND_MAKE_A_BOOKING');?></strong>
                </div>
            </div>
		<?php
		}
		?>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<?php
				if(count($employees) > 0)
				{
                    if(($configClass['hidetabs'] == 1) && (count($employees) == 1))
					{
                        //nothing show
                        $class = ' active';
                    }
					else
					{
                        if($configClass['usingtab'] == 0) 
						{
                            //do nothing
                        }
						elseif($configClass['usingtab'] == 1)
						{
                            if(count($employees) > 1)
							{
                            ?>
                                <select name="employeeslist_<?php echo $sid;?>" id="employeeslist_<?php echo $sid;?>" class="input-large form-select" onChange="javascript:changingEmployee(<?php echo $sid;?>);">
                                    <?php
                                    $tempArr = array();
                                    for($i=0;$i<count($employees);$i++)
									{
                                        $employee = $employees[$i];
										if($eid > 0)
										{
											if($employee->id == $eid)
											{
												$selected = "selected";
												$employee->display = "display:block;";
											}
											else
											{
												$employee->display = "display:none;";
												$selected = "";
											}
										}
										else
										{
											if($i == 0)
											{
												$selected = "selected";
												$employee->display = "display:block;";
											}
											else
											{
												$employee->display = "display:none;";
												$selected = "";
											}
										}
                                        ?>
                                        <option <?php echo $selected;?> value="<?php echo $i;?>"><?php echo $employee->employee_name; ?></option>
                                        <?php
                                        $tempArr[] = $i;
                                    }
                                    ?>
                                </select>
                            <?php
                            $temp = implode("|",$tempArr);
                            ?>
                            <input type="hidden" name="employeeslist_ids<?php echo $sid;?>" id="employeeslist_ids<?php echo $sid;?>" value="<?php echo $temp; ?>" />
                            <?php
                            }
                        }
                    }
                    ?>
                    <div class="tab-content <?php echo $mapClass['row-fluid'];?>">
                        <?php
                        if((count($employees) > 1) && ($configClass['usingtab'] == 0))
						{
                            if(($eid > 0) && (OSBHelper::isExployeeExist($employees,$eid)))
							{
                                $class = 'pane'.$sid.'_'.$eid;
                            }
							else
							{
                                $class = 'pane'.$sid.'_'.$employees[0]->id;
                            }
                            echo JHtml::_('bootstrap.startTabSet', 'employees'.$sid, array('active' => $class));
                        }
                        //check to see if eid is in employees array
                        $employee_exists = false;
                        for($i=0;$i<count($employees);$i++)
						{
                            $employee = $employees[$i];
                            if($eid > 0)
							{
                                if($eid == $employee->id)
								{
                                    $employee_exists = true;
                                }
                            }
                        }
                        for($i=0;$i<count($employees);$i++)
						{
                            $employee = $employees[$i];
                            if($employee_exists)
							{
                                if($eid == $employee->id)
								{
                                    $class = ' active';
                                }
								else
								{
                                    $class = '';
                                }
                            }
							else
							{
                                if($i==0)
								{
                                    $class = ' active';
                                }
								else
								{
                                    $class = '';
                                }
                            }
							if($configClass['usingtab'] == 1) 
							{
								$id = "id='pane".$sid."_".$i."'";
							}
							else
							{
								$id = "";
							}

                            if((count($employees) >1) && ($configClass['usingtab'] == 0)){
                                echo JHtml::_('bootstrap.addTab', 'employees'.$sid, 'pane'.$sid.'_'.$employee->id, $employee->employee_name);
                            }
                            ?>
                            <div <?php echo $id; ?> class="<?php echo $mapClass['span12'];?> norightleftmargin" style="<?php echo $employee->display;?>">
                                <?php
                                $db->setQuery("Select a.id from #__app_sch_venues as a inner join #__app_sch_employee_service as b on a.id = b.vid where b.employee_id = '$employee->id' and b.service_id = '$sid'");
                                $venue_id = $db->loadResult();
                                if($venue_id > 0 && $configClass['show_venue'] == 1)
								{
                                    ?>
                                    <div class="<?php echo $mapClass['row-fluid'];?> venueinformationbooking">
                                        <div class="<?php echo $mapClass['span12'];?>">
                                            <?php
                                            HelperOSappscheduleCommon::loadVenueInformation($sid,$employee->id);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                <?php } ?>
                                <?php
                                if($configClass['employee_bar'] == 1){
                                    ?>
                                    <div class="<?php echo $mapClass['row-fluid'];?> employeeinformation">
                                        <div class="<?php echo $mapClass['span12'];?>">
                                            <div class="sub_header">
                                                <?php
                                                echo $employee->employee_name;
                                                ?>
                                                <?php
                                                if($configClass['employee_phone_email'] == 1) {
                                                    ?>
                                                    <div class="employee_information">
                                                        <table width="100%">
                                                            <tr>
                                                                <?php
                                                                if ($employee->employee_email != "") {
                                                                    ?>
                                                                    <td width="16">
                                                                        <img src="<?php echo JURI::root() ?>media/com_osservicesbooking/assets/css/images/email.png"/>
                                                                    </td>
                                                                    <td class="employee-email-td">
                                                                        <a href="mailto:<?php echo $employee->employee_email; ?>"
                                                                           target="_blank">
                                                                            <?php echo $employee->employee_email; ?>
                                                                        </a>
                                                                    </td>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <?php
                                                                if ($employee->employee_phone != "") {
                                                                    ?>
                                                                    <td width="16">
                                                                        <img src="<?php echo JURI::root() ?>media/com_osservicesbooking/assets/css/images/telephone.png">
                                                                    </td>
                                                                    <td class="employee-email-td">
                                                                        <?php echo $employee->employee_phone; ?>
                                                                    </td>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if((($employee->employee_notes != "") or (($employee->employee_photo != "") and (file_exists(JPATH_ROOT.'/images/osservicesbooking/employee/'.$employee->employee_photo)))) and ($configClass['employee_information'] == 1))
									{
										if(!OSBHelper::isJoomla4())
										{
											$extraCss = 'style="padding:3px;"';
										}
                                        ?>
                                        <div class="<?php echo $mapClass['row-fluid'];?>">
                                            <div class="<?php echo $mapClass['span12'];?> employeesubinfo" <?php echo $extraCss;?>>
                                                <?php
                                                if(($employee->employee_photo != "") and (file_exists(JPATH_ROOT.'/images/osservicesbooking/employee/'.$employee->employee_photo))){
                                                    ?>
                                                    <div class="employee_photo">
                                                        <a href="<?php echo JURI::root()?>images/osservicesbooking/employee/<?php echo $employee->employee_photo?>" target="_blank">
                                                            <img src="<?php echo JURI::root()?>images/osservicesbooking/employee/<?php echo $employee->employee_photo?>" class="img-polaroid" width="100" />
                                                        </a>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <?php if($employee->employee_notes != "") { echo nl2br($employee->employee_notes); }?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                <?php } ?>
                                <div class="<?php echo $mapClass['row-fluid'];?> employeeloadingform">
                                    <div class="<?php echo $mapClass['span12'];?>">
                                        <?php
                                        OsAppscheduleAjax::loadEmployee($sid,$employee->id,$date,$vid);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if((count($employees) >1) && ($configClass['usingtab'] == 0)){
                                echo JHtml::_('bootstrap.endTab');
                            }
                        }
                        if((count($employees) >1) && ($configClass['usingtab'] == 0)){
                            echo JHtml::_('bootstrap.endTabSet');
                        }
                        ?>
                    </div>
					<?php
				}else{
					?>
					<div class="<?php echo $mapClass['row-fluid'];?> nostaffavailable">
						<div class="<?php echo $mapClass['span12'];?>">
							<?php echo Jtext::_('OS_NO_STAFF_AVAILABLE'); ?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}
	
	static function loadEmployeeFrame($sid,$eid,$date,$vid){
		global $mainframe;
		HelperOSappscheduleCalendar::getAvaiableTimeFrameOfOneEmployee($date,$eid,$sid,$vid);
	}
	
	static function showInforFormHTML($option,$lists,$fields){
		global $mainframe,$mapClass,$configClass;
		$user = JFactory::getUser();
		$methods = $lists['methods'];
		?>
		<table  width="100%" style="border:1px solid #B3BED3 !important;">
			<tr>
				<td width="100%" class="header">
					<?php echo JText::_('OS_BOOKING_FORM')?>
					<div style="float:right;padding-right:10px;">
					<a href="javascript:closeForm(<?php echo intval(date("d",HelperOSappscheduleCommon::getRealTime()));?>,<?php echo intval(date("m",HelperOSappscheduleCommon::getRealTime()));?>,<?php echo intval(date("Y",HelperOSappscheduleCommon::getRealTime()));?>);">
						<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/close.png" border="0">
					</a>
					</div>
				</td>
			</tr>
			<tr>
				<td width="100%" style="padding:5px;">
					<table  width="100%">
						<tr>
							<td width="100%" colspan="2" style="color:gray;font-size:11px;padding:5px;">
								<?php echo JText::_('OS_PLEASE_FILL_THE_FORM_BELLOW')?>
							</td>
						</tr>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_NAME')?>
							</td>
							<td class="infor_right_col">
								<input type="text" class="input-large" size="20" name="order_name" id="order_name" value="<?php echo $user->name?>">
							</td>
						</tr>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_EMAIL')?>
							</td>
							<td class="infor_right_col">
								<input type="text" class="input-large" value="<?php echo $user->email?>" size="20" name="order_email" id="order_email">
							</td>
						</tr>
						<?php
						if($configClass['value_sch_include_phone']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_PHONE')?>
							</td>
							<td class="infor_right_col">
								<input type="text" class="input-small" value="" size="10" name="order_phone" id="order_phone">
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_country']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_COUNTRY')?>
							</td>
							<td class="infor_right_col">
								<?php echo $lists['country'];?>
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_address']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_ADDRESS')?>
							</td>
							<td class="infor_right_col">
								<input type="text" class="inputbox" value="" size="20" name="order_address" id="order_address">
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_city']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_CITY')?>
							</td>
							<td class="infor_right_col">
								<input type="text" class="inputbox" value="" size="20" name="order_city" id="order_city">
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_state']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_STATE')?>
							</td>
							<td class="infor_right_col">
								<input type="text" class="inputbox" value="" size="10" name="order_state" id="order_state">
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_zip']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_ZIP')?>
							</td>
							<td class="infor_right_col">
								<input type="text" class="inputbox" value="" size="10" name="order_zip" id="order_zip">
							</td>
						</tr>
						<?php
						}
						?>
						<?php
						$fieldArr = array();
						for($i=0;$i<count($fields);$i++){
							$field = $fields[$i];
							$fieldArr[] = $field->id;
							?>
							<tr>
								<td width="30%" class="infor_left_col">
									<?php echo $field->field_label;?>
								</td>
								<td class="infor_right_col">
									<?php
									OsAppscheduleDefault::orderField($field);
									?>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td width="30%" class="infor_left_col" valign="top">
								<?php echo JText::_('OS_NOTES')?>
							</td>
							<td class="infor_right_col">
								<textarea name="notes" id="notes" cols="40" rows="4" class="inputbox"></textarea>
							</td>
						</tr>
						<?php
						if($configClass['value_sch_include_captcha'] == 2){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_CAPCHA')?>
							</td>
							<td class="infor_right_col">
								<?php
								$resultStr = md5(HelperOSappscheduleCommon::getRealTime());// md5 to generate the random string
								$resultStr = substr($resultStr,0,5);//trim 5 digit 
								?>
								<img src="<?php echo JURI::root()?>index.php?option=com_osservicesbooking&no_html=1&task=ajax_captcha&resultStr=<?php echo $resultStr?>"> 
								<input type="text" class="inputbox" id="security_code" name="security_code" maxlength="5" style="width: 50px; margin: 0;" class="inputbox"/>
								<input type="hidden" name="resultStr" id="resultStr" value="<?php echo $resultStr?>">
							</td>
						</tr>
						<?php
						}
						?>
						<input type="hidden" name="field_ids" id="field_ids" value="<?php echo implode(",",$fieldArr)?>">
						<input type="hidden" name="nmethods" id="nmethods" value="<?php echo count($methods)?>">
						<?php
						if($configClass['disable_payments'] == 1){
							if(count($methods) > 0){
							?>
								<tr>
									<td class="infor_left_col" valign="top">
										<?php echo JText::_('OS_PAYMENT_OPTION'); ?>
										<span class="required">*</span>						
									</td>
									<td class="infor_right_col">
										<?php
											$method = null ;
											for ($i = 0 , $n = count($methods); $i < $n; $i++) {
												$paymentMethod = $methods[$i];
												if ($paymentMethod->getName() == $lists['paymentMethod']) {
													$checked = ' checked="checked" ';
													$method = $paymentMethod ;
												}										
												else 
													$checked = '';	
											?>
												<input onclick="changePaymentMethod();" type="radio" name="payment_method" id="pmt<?php echo $i?>" value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> /><?php echo JText::_($paymentMethod->title) ; ?> <br />
											<?php		
											}	
										?>
									</td>						
								</tr>				
							<?php					
							} else {
								$method = $methods[0] ;
							}		
						
							if ($method->getCreditCard()) {
								$style = '' ;	
							} else {
								$style = 'style = "display:none"';
							}			
							?>			
							<tr id="tr_card_number" <?php echo $style; ?>>
								<td class="infor_left_col"><?php echo  JText::_('OS_AUTH_CARD_NUMBER'); ?><span class="required">*</span></td>
								<td class="infor_right_col">
									<input type="text" name="x_card_num" id="x_card_num" class="osm_inputbox inputbox" onkeyup="checkNumber(this)" value="<?php echo $x_card_num; ?>" size="20" />
								</td>					
							</tr>
							<tr id="tr_exp_date" <?php echo $style; ?>>
								<td class="infor_left_col">
									<?php echo JText::_('OS_AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
								</td>
								<td class="infor_right_col">	
									<?php echo $lists['exp_month'] .'  /  '.$lists['exp_year'] ; ?>
								</td>					
							</tr>
							<tr id="tr_cvv_code" <?php echo $style; ?>>
								<td class="infor_left_col">
									<?php echo JText::_('OS_AUTH_CVV_CODE'); ?><span class="required">*</span>
								</td>
								<td class="infor_right_col">
									<input type="text" name="x_card_code" id="x_card_code" class="osm_inputbox inputbox" onKeyUp="checkNumber(this)" value="<?php echo $x_card_code; ?>" size="20" />
								</td>					
							</tr>
							<?php
								if ($method->getCardType()) {
									$style = '' ;
								} else {
									$style = ' style = "display:none;" ' ;										
								}
							?>
								<tr id="tr_card_type" <?php echo $style; ?>>
									<td class="infor_left_col">
										<?php echo JText::_('OS_CARD_TYPE'); ?><span class="required">*</span>
									</td>
									<td class="infor_right_col">
										<?php echo $lists['card_type'] ; ?>
									</td>						
								</tr>					
							<?php
								if ($method->getCardHolderName()) {
									$style = '' ;
								} else {
									$style = ' style = "display:none;" ' ;										
								}
							?>
								<tr id="tr_card_holder_name" <?php echo $style; ?>>
									<td class="infor_left_col">
										<?php echo JText::_('OS_CARD_HOLDER_NAME'); ?><span class="required">*</span>
									</td>
									<td class="infor_right_col">
										<input type="text" name="card_holder_name" id="card_holder_name" class="osm_inputbox inputbox"  value="<?php echo $cardHolderName; ?>" size="40" />
									</td>						
								</tr>
							<?php									
								if ($method->getName() == 'os_echeck') {
									$style = '';												
								} else {
									$style = ' style = "display:none;" ' ;
								}
							?>
							    <tr id="tr_bank_rounting_number" <?php echo $style; ?>>
							      <td class="infor_left_col"  class="infor_left_col"><?php echo JText::_('OSM_BANK_ROUTING_NUMBER'); ?><span class="required">*</span></td>
							      <td class="infor_right_col"><input type="text" name="x_bank_aba_code" class="osm_inputbox inputbox"  value="<?php echo $x_bank_aba_code; ?>" size="40" onKeyUp="checkNumber(this);" /></td>
							    </tr>
							    <tr id="tr_bank_account_number" <?php echo $style; ?>>
							      <td class="infor_left_col" class="infor_left_col"><?php echo JText::_('OSM_BANK_ACCOUNT_NUMBER'); ?><span class="required">*</span></td>
							      <td class="infor_right_col"><input type="text" name="x_bank_acct_num" class="osm_inputbox inputbox"  value="<?php echo $x_bank_acct_num; ?>" size="40" onKeyUp="checkNumber(this);" /></td>
							    </tr>
							    <tr id="tr_bank_account_type" <?php echo $style; ?>>
							      <td class="infor_left_col"  class="infor_left_col"><?php echo JText::_('OSM_BANK_ACCOUNT_TYPE'); ?><span class="required">*</span></td>
							      <td class="infor_right_col"><?php echo $lists['x_bank_acct_type']; ?></td>
							    </tr>
							    <tr id="tr_bank_name" <?php echo $style; ?>>
							      <td class="infor_left_col" class="infor_left_col"><?php echo JText::_('OSM_BANK_NAME'); ?><span class="required">*</span></td>
							      <td class="infor_right_col"><input type="text" name="x_bank_name" class="osm_inputbox inputbox"  value="<?php echo $x_bank_name; ?>" size="40" /></td>
							    </tr>
							    <tr id="tr_bank_account_holder" <?php echo $style; ?>>
							      <td class="infor_left_col" class="infor_left_col"><?php echo JText::_('OSM_ACCOUNT_HOLDER_NAME'); ?><span class="required">*</span></td>
							      <td class="infor_right_col"><input type="text" name="x_bank_acct_name" class="osm_inputbox inputbox"  value="<?php echo $x_bank_acct_name; ?>" size="40" /></td>
							    </tr>	
							<?php			
							if ($idealEnabled) {
						        if ($method->getName() == 'os_ideal') {
									$style = '' ;
								} else {
									$style = ' style = "display:none;" ' ;
								}	
							?>
								<tr id="tr_bank_list" <?php echo $style; ?>>
									<td class="infor_left_col">
										<?php echo JText::_('OS_BANK_LIST'); ?><span class="required">*</span>
									</td>
									<td class="infor_right_col">
										<?php echo $lists['bank_id'] ; ?>
									</td>
								</tr>
							<?php	
						    }						        			
						}
						?>
						<tr>
							<td colspan="2">
								<input type="button" class="btn btn-primary" value="Submit" onclick="javascript:confirmBooking()">
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
	}
	
	static function confirmInforFormHTML($option,$total,$fieldObj,$lists){
		global $mainframe,$mapClass,$configClass,$jinput;
		?>
		<table  width="100%" style="border:1px solid #B3BED3 !important;">
			<tr>
				<td width="100%" class="header">
					<?php echo JText::_('OS_CONFIRM_INFORMATION')?>
					<div style="float:right;padding-right:10px;">
					<a href="javascript:closeForm()">
						<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/close.png" border="0">
					</a>
					</div>
				</td>
			</tr>
			<tr>
				<td width="100%" style="padding:5px;">
					<table  width="100%">
						<?php
						if($configClass['disable_payments'] == 1){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_PRICE')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo OSBHelper::showMoney($total,1);
								?>
							</td>
						</tr>
						<?php
						if($configClass['enable_tax']==1){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_TAX')?>
							</td>
							<td class="infor_right_col">
								
								<?php
								$tax = round($total*intval($configClass['tax_payment'])/100);
								echo OSBHelper::showMoney($tax,1);
								?>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_TOTAL')?>
							</td>
							<td class="infor_right_col">
								<?php
								$final = $total + $tax;
								echo OSBHelper::showMoney($final,1);
								?>
							</td>
						</tr>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_DEPOSIT')?>
							</td>
							<td class="infor_right_col">
								<?php
								$deposit_payment = $configClass['deposit_payment'];
								$deposit_payment = $deposit_payment*$final/100;
								?>
								<?php 
								echo OSBHelper::showMoney($deposit_payment,1);
								?>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_NAME')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $jinput->get('order_name','','string');
								?>
							</td>
						</tr>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_EMAIL')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $jinput->get('order_email','','string');
								?>
							</td>
						</tr>
						<?php
						
						if($configClass['value_sch_include_phone']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_PHONE')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $jinput->get('order_phone','','string');
								?>
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_country']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_COUNTRY')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $jinput->get('order_country','','string');
								?>
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_address']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_ADDRESS')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $jinput->get('order_address','','string');
								?>
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_city']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_CITY')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $jinput->get('order_city','','string');
								?>
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_state']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_STATE')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $jinput->get('order_state','','string');
								?>
							</td>
						</tr>
						<?php
						}
						if($configClass['value_sch_include_zip']){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_ZIP')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $jinput->get('order_zip','','string');
								?>
							</td>
						</tr>
						<?php
						}
						
						if(count($fieldObj) > 0){
							for($i=0;$i<count($fieldObj);$i++){
								$f = $fieldObj[$i];
								?>
								<tr>
									<td width="30%" class="infor_left_col" valign="top" style="padding-top:5px;">
										<?php echo $f->field->field_label;?>
									</td>
									<td class="infor_right_col">
										<?php
										echo $f->fvalue;
										?>
									</td>
								</tr>
								<?php
							}
						}
						?>
						<?php
						$note = $jinput->get('notes','','string');
						$note = str_replace("(@)","&",$note);
						//$note = str_replace("@r@","\r",$note);
						//$note = str_replace("@n@","\n",$note);
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_NOTES');?>
							</td>
							<td class="infor_right_col">
								<?php
								echo nl2br($note);
								?>
							</td>
						</tr>
						<?php
						if($configClass['disable_payments'] == 1){
							$method = $lists['method'];
							?>
							<tr>
								<td width="30%" class="infor_left_col">
									<?php echo JText::_('OS_SELECT_PAYMENT')?>
								</td>
								<td class="infor_right_col">
									<?php echo  JText::_(os_payments::loadPaymentMethod($lists['select_payment'])->title); ?>
								</td>
							</tr>
							<?php
						}
						$method = $lists['method'] ;
						if($lists['select_payment'] != ""){
							if ($method->getCreditCard()) {
							?>	
								<tr>
									<td class="infor_left_col"><?php echo  JText::_('OS_AUTH_CARD_NUMBER'); ?>
									<td class="infor_right_col">
										<?php
											$len = strlen($lists['x_card_num']) ;
											$remaining =  substr($lists['x_card_num'], $len - 4 , 4) ;
											echo str_pad($remaining, $len, '*', STR_PAD_LEFT) ;
										?>												
									</td>
								</tr>
								<tr>
									<td class="infor_left_col">
										<?php echo JText::_('OS_AUTH_CARD_EXPIRY_DATE'); ?>
									</td>
									<td class="infor_right_col">						
										<?php echo $lists['exp_month'] .'/'.$lists['exp_year'] ; ?>
									</td>
								</tr>
								<tr>
									<td class="infor_left_col">
										<?php echo JText::_('OS_AUTH_CVV_CODE'); ?>
									</td>
									<td class="infor_right_col">
										<?php echo $lists['x_card_code'] ; ?>
									</td>
								</tr>
								<?php
									if ($method->getCardType()){
									?>
										<tr>
											<td class="infor_left_col">
												<?php echo JText::_('OS_CARD_TYPE'); ?>
											</td>
											<td class="infor_right_col">
												<?php echo $lists['card_type'] ; ?>
											</td>
										</tr>
									<?php	
									}
								?>
							<?php				
							}						
							if ($method->getCardHolderName()) {
							?>
								<tr>
									<td class="infor_left_col">
										<?php echo JText::_('OSM_CARD_HOLDER_NAME'); ?>
									</td>
									<td class="infor_right_col">
										<?php echo $lists['cardHolderName'];?>
									</td>
								</tr>
							<?php												
							}
						}
						?>
						<tr>
							<td colspan="2">
								<input type="button" class="btn btn-primary" value="Confirm" onclick="javascript:createBooking()">
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<!-- hidden tags -->
		<input type="hidden" name="order_name" 			id="order_name" 		value="<?php echo $jinput->get('order_name','','string');?>">
		<input type="hidden" name="order_email" 		id="order_email" 		value="<?php echo $jinput->get('order_email','','string')?>">
		<input type="hidden" name="order_phone" 		id="order_phone" 		value="<?php echo $jinput->get('order_phone','','string')?>">
		<input type="hidden" name="order_country" 		id="order_country" 		value="<?php echo $jinput->get('order_country','','string')?>">
		<input type="hidden" name="order_address" 		id="order_address" 		value="<?php echo $jinput->get('order_address','','string')?>">
		<input type="hidden" name="order_state" 		id="order_state" 		value="<?php echo $jinput->get('order_state','','string')?>">
		<input type="hidden" name="order_city" 			id="order_city" 		value="<?php echo $jinput->get('order_city','','string')?>">
		<input type="hidden" name="order_zip" 			id="order_zip" 			value="<?php echo $jinput->get('order_zip','','string')?>">
		
		<input type="hidden" name="x_card_num" 			id="x_card_num" 		value="<?php echo $lists['x_card_num']?>">
		<input type="hidden" name="x_card_code" 		id="x_card_code" 		value="<?php echo $lists['x_card_code']?>">
		<input type="hidden" name="card_holder_name" 	id="card_holder_name" 	value="<?php echo $lists['card_holder_name']?>">
		<input type="hidden" name="exp_year" 			id="exp_year" 			value="<?php echo $lists['exp_year']?>">
		<input type="hidden" name="exp_month" 			id="exp_month" 			value="<?php echo $lists['exp_month']?>">
		<input type="hidden" name="card_type" 			id="card_type" 			value="<?php echo $lists['card_type']?>">
		
		<div style="display:none;">
		<input type="hidden" name="select_payment" 		id="select_payment" 	value="<?php echo $jinput->get('select_payment','','string');?>">
		<textarea name="notes" id="notes" cols="40" rows="4" class="inputbox"><?php echo $note?></textarea>
		</div>
		<?php
		if(count($fieldObj) > 0){
			for($i=0;$i<count($fieldObj);$i++){
				$f = $fieldObj[$i];
				?>
				<input type="hidden" name="field_<?php echo $f->field->id?>" id="field_<?php echo $f->field->id?>" value="<?php echo $f->fieldoptions?>">
				<?php
			}
		}
		?>
		
		<?php
	}
	
	/**
	 * Show service details 
	 *
	 * @param unknown_type $service
	 */
	static function showServiceDetails($service,$date,$nbook)
	{
		global $mainframe,$mapClass,$configClass;
		?>
		<table  width="100%">
		<tr>
			<td class="header" style="padding:0px;">
				
				<div style="float:left;margin-right:5px;">
					<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/details.png" />
				</div>
				<div style="float:left;padding-top:4px;">
					<?php echo JText::_('OS_SELECTED_SERVICES')?>
				</div>
			</td>
		</tr>
		<tr>
			<td width="100%" class="service-details-td">
				<strong>
					<?php echo $service->service_name?> <span color='gray'>(<?php echo $date[0]?>/<?php echo $date[1]?>/<?php echo $date[2]?>)</span>
				</strong>
				<BR />
				<span style="font-size:11px;color:gray;">
				<div style="padding-top:5px;padding-bottom:5px;">
				<?php echo JText::_('OS_LENGTH')?>: <strong><?php echo $service->service_total?> <?php echo JText::_('OS_MINS')?></strong>
				&nbsp;&nbsp;
				<?php echo JText::_('OS_PRICE')?>: <strong>
				<?php 
				echo OSBHelper::showMoney($service->service_price,1);
				?>
				</strong>
				
				<BR />
				<?php
				if($nbook > 0)
				{
					?>
					<span color='red'><?php echo JText::_('OS_THERE_IS')?> <?php echo $nbook?> <?php echo JText::_('OS_BOOKS_ALREADY')?></span>
					<?php
				}else{
					?>
					<span color='green'><?php echo JText::_('OS_AVAILABLE_FOR_BOOKING')?></span>
					<?php
				}
				?></div>
				
				<?php
					echo stripslashes(strip_tags($service->service_description));
				?>
				</span>
				<BR />
				<div style="padding-top:5px;text-align:center;width:100%;">
					<a href="javascript:selectEmployee(<?php echo $service->id?>,<?php echo $date[2]?>,<?php echo $date[1]?>,<?php echo $date[0]?>);" class="applink">
						<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/availability.png" border="0" />
					</a>
				</div>
			</td>
		</tr>
		</table>
		<?php
	}

	static function showOSBSearchModule($lists)
	{
		global $jinput,$mapClass;
		if($lists['show_venue'] == 1){
		?>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
					<?php echo JText::_('OS_VENUE');?>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
					<?php echo $lists['venue'];?>
				</div>
			</div>
		<?php } ?>
		<?php 
		if($lists['show_category'] == 1){
		?>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
					<?php echo JText::_('OS_CATEGORY');?>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
					<?php echo $lists['category'];?>
				</div>
			</div>
		<?php } ?>
		<?php
		if($lists['show_service'] == 1){
			?>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
					<?php echo JText::_('OS_SERVICE');?>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
				<?php echo $lists['service'];?>
				</div>
			</div>
		<?php } ?>
		<?php 
		if($lists['show_employee'] == 1){
		?>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
					<?php echo JText::_('OS_EMPLOYEE');?>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
					<?php echo $lists['employee'];?>
				</div>
			</div>
		<?php }
	}
}

?>