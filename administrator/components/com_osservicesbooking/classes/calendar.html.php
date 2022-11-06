<?php
/*------------------------------------------------------------------------
# calendar.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class HTML_OsAppscheduleCalendar{
	/**
	 * List all the work of employee in calendar
	 * @param unknown_type $employee
	 */
	static function monthlyCalendar($eIds,$lists = array(),$filterSid = 0, $filterEid = 0){
		global $mainframe,$configClass,$jinput;
		OSBHelper::loadTooltip();
		
		$year = $jinput->getInt('year',date("Y",time()));
		$month =  intval($jinput->getInt('month',date("m",time())));
		self::initEmployeeCalendar($eIds,$year,$month,$lists,$filterSid,$filterEid);
	}
	/**
	 * Init Availability calendar for Employee In Backend
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $year
	 * @param unknown_type $month
	 */
	public static function initEmployeeCalendar($eIds,$year,$month,$lists,$filterSid, $filterEid)
	{
		global $mainframe,$configClass,$jinput,$mapClass;
		$config = new JConfig();
		require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/jquery.php';
        JToolBarHelper::title(JText::_('OS_MONTHLY_CALENDAR'),'list');
        JToolbarHelper::custom('orders_list','list.png','list.png',JText::_('OS_MANAGE_ORDERS'), false);
        JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		$offset = $config->offset;
		date_default_timezone_set($offset);
		//JHTML::_('behavior.modal','a.osmodal');
		if(version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			JHtml::_('behavior.modal', 'a.osmodal');
		}
		else
		{
			OSBHelperJquery::colorbox('osmodal');
			JHtml::_('bootstrap.tooltip', '.hasTip');
		}
		$db = JFactory::getDbo();
		include_once(JPATH_COMPONENT_ADMINISTRATOR."/classes/ajax.php");
		$today						= OSBHelper::getCurrentDate();
		$current_month 				= intval(date("m",$today));
		$current_year				= intval(date("Y",$today));
		$current_date				= intval(date("d",$today));
		//set up the first date
		$start_date_current_month 	= strtotime($year."-".$month."-01");
		$start_date_in_week			= date("N",$start_date_current_month);
		$number_days_in_month		= cal_days_in_month(CAL_GREGORIAN,$month,$year);


		$monthArr = array(JText::_('OS_JANUARY'), JText::_('OS_FEBRUARY'), JText::_('OS_MARCH'), JText::_('OS_APRIL'), JText::_('OS_MAY'), JText::_('OS_JUNE'), JText::_('OS_JULY'), JText::_('OS_AUGUST'), JText::_('OS_SEPTEMBER'), JText::_('OS_OCTOBER'), JText::_('OS_NOVEMBER'), JText::_('OS_DECEMBER'));
		?>
        <form method="POST" action="index.php?option=com_osservicesbooking&task=calendar_employee" name="adminForm" id="ftForm">
		<div id="cal<?php echo intval($month)?><?php echo $year?>">
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span3'];?>">
					<table  width="100%" class="apptable">
						<thead>
							<tr>
								<th width="30%" align="right">
									<span onclick="ChangeMonth('pre')" class="applink" style="cursor: pointer;">
									<
									</span>
								</th>
								<th width="40%" align="center">
									<?php
									echo $monthArr[$month-1];
									?>
									&nbsp;
									<?php echo $year;?>
								</th>
								<th width="30%" align="left">
									<span onclick="ChangeMonth('next')" class="applink" style="cursor: pointer;">
									>
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="100%" colspan="3">
									<select name="ossm" class="input-small" id="ossm">
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
									<select name="ossy" class="input-small" id="ossy">
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
									<input type="button" class="goBtn" value="Go" onclick="javascript:ChangeMonth('keep');">
								</td>
							</tr>
						</tbody>
					</table>
					<BR />
					<table  width="100%" class="apptable">
						<thead>
							<tr>
								<th width="100%" align="center">
									<?php echo JText::_('OS_FILTER');?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="100%" colspan="3">
									<strong><?php echo JText::_('OS_SERVICE');?></strong>
									<div class="clearfix"></div>
									<?php echo $lists['sid'];?>
									<div class="clearfix"></div>
									<strong><?php echo JText::_('OS_EMPLOYEE');?></strong>
									<div class="clearfix"></div>
									<?php echo $lists['emp'];?>
									<input type="submit" class="goBtn calendarFilterBtn" value="<?php echo JText::_('OS_FILTER');?>">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="<?php echo $mapClass['span9'];?>">
					<table width="100%" class="mainTable">
						<thead>
							<tr>
								<th width="14%">
									
									<?php echo JText::_('OS_MON')?>
									
								</th>
								<th width="14%">
									
									<?php echo JText::_('OS_TUE')?>
									
								</th>
								<th width="14%">
									
									<?php echo JText::_('OS_WED')?>
									
								</td>
								<th width="14%">
									
									<?php echo JText::_('OS_THU')?>
									
								</th>
								<th width="14%">
								
									<?php echo JText::_('OS_FRI')?>
									
								</th>
								<th width="14%">
									<?php echo JText::_('OS_SAT')?>
								</th>
								<th width="14%">
									<?php echo JText::_('OS_SUN')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?php
								for($i=1;$i<$start_date_in_week;$i++){
									//empty
									?>
									<td style="background-color:#f9f9f9;">
									
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
								
								for($i=1;$i<=$number_days_in_month;$i++){
									$j++;
									$nolink = 0;
									//check to see if today
									if(($i == $current_date) and ($month == $current_month) and ($year == $current_year)){
										$bgcolor = "pink";
									}else{
										$bgcolor = "#F1F1F1";
									}
									
									if($i < 10){
										$day = "0".$i;
									}else{
										$day = $i;
									}
									$tempdate1 = strtotime($year."-".$month."-".$day);
									$tempdate2 = strtotime($current_year."-".$current_month."-".$current_date);
									
									if($tempdate1 < $tempdate2){
										$bgcolor = "#ABAAB2";
										$nolink = 4;
									}
									
									if($i < 10){
										$day = "0".$i;
									}else{
										$day = $i;
									}
									$date = $year."-".$month."-".$day;
									?>
									<td id="td_cal_<?php echo $i?>" class="td_date" valign="top">
										<div id="a<?php echo $i;?>">
											<?php
											self::calendarEmployeeItemAjax($i,$eIds,$date,$filterSid, $filterEid);
											?>
										</div>
									</td>
									<?php
									if($j >= 7){
										$j = 0;
										echo "</tr><tr>";
									}
									
								}
								?>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<input type="hidden" name="current_item_value" id="current_item_value" value="" />
			<input type="hidden" name="current_td" id="current_td" value="" />
			<input type="hidden" name="date" id="date" value="" />
			<input type="hidden" name="month" id="month" value="<?php echo $jinput->getInt('month',intval(date("m",HelperOSappscheduleCommon::getRealTime())));?>" />
			<input type="hidden" name="year" id="year" value="<?php echo $jinput->getInt('year',intval(date("Y",HelperOSappscheduleCommon::getRealTime())));?>" />
		</div>
        <input type="hidden" name="option" value="com_osservicesbooking" />
        <input type="hidden" name="task" id="task" value="calendar_employee" />
        <input type="hidden" name="boxchecked" value="0" />
        </form>
		<script type="text/javascript">
			function ChangeMonth(act){
				var month = document.getElementById('month');
				var year  = document.getElementById('year');
				month 	  = month.value;
				year	  = year.value;
				if(act == 'next'){
					if(month < 12){
					month = parseInt(month) + 1;
					}else{
						year  = parseInt(year) + 1;
						month = 1;
					}
				}else if(act == 'pre'){
					if(month > 1){
					month = parseInt(month) - 1;
					}else{
						year  = parseInt(year) - 1;
						month = 12;
					}
				}else{
					month = document.getElementById('ossm').value;
					year  = document.getElementById('ossy').value;
				}
				document.getElementById('month').value = month;
				document.getElementById('year').value = year;
				ftForm.submit();
			}
		</script>
		<?php
	}
	public static function calendarEmployeeItemAjax($i,$eIds,$day,$filterSid, $filterEid)
    {
		global $mainframe,$configClass;
		JHtml::_('formbehavior.chosen', 'select');
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$db  = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear()->select('a.*,c.service_name,c.service_color,b.order_status, b.order_name, b.order_email, b.order_phone')
			  ->from('#__app_sch_order_items AS a')	
			  ->innerJoin('#__app_sch_orders AS b ON b.id = a.order_id')
			  ->innerJoin('#__app_sch_services AS c ON c.id = a.sid')
			  ->where("(b.order_status = 'P' or b.order_status = 'S' or b.order_status = 'A' or b.order_status = 'C')")
			  ->where('a.booking_date = '.$db->quote($day))
			  ->order('a.start_time');
		if(count($filterSid) > 0)
		{
			 $query->where('a.sid in ('.implode(',',$filterSid).')');
		}
		if(count($filterEid) > 0)
		{
			$query->where('a.eid in ('.implode(',',$filterEid).')');
		}
		//echo($query->__toString());die();
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		//$query->clear()->select('id')->from('#__app_sch_employee')->where('id IN ('.implode(',',$eIds).')');
		//$db->setQuery($query);
		$colorCodes = ['#FFFF99','#66FFFF','#FFCC00','#CC0000','#FF6600'];
		?>
		<div style="float:left;width:50%;">
			<?php
			echo date($configClass['date_format'], strtotime($day));
			?>
		</div>
		<div style="clear: both;"></div>
		<div class="div-schedule">
		<?php
		if(count($rows) > 0)
		{
			for($k=0;$k<count($rows);$k++)
			{
				$config = new JConfig();
				$offset = $config->offset;
				date_default_timezone_set($offset);
				$row = $rows[$k];
				$index          = $row->sid % count($colorCodes);
				if($row->service_color != "")
				{
					$bgColor		= $row->service_color;
					if(substr($bgColor,0,1) != "#")
					{
						$bgcolor	= "#".$bgcolor;
					}
				}
				else
				{
					$bgColor		= ($colorCodes[$index])?$colorCodes[$index]:"#FFFFFF";
				}
				$Textcolor		= "#000000";
				?>
				<div style="background:<?php echo $bgColor; ?>;color:<?php echo $Textcolor; ?>;margin-bottom: 2px;">
					<span class="hasTip" title="<?php echo OSBHelper::generateBookingItem($row,0);?>">
					<?php
					echo $k + 1;
					echo ". ";
					echo date($configClass['time_format'],$row->start_time);
					echo "-";
					echo date($configClass['time_format'],$row->end_time);
					//echo "</a>";
					echo "  [".$row->service_name."]";
					echo "<BR />";
					echo "<span style='font-size:12px;'>";
					echo "<strong>".JText::_('OS_NAME')."</strong> ".$row->order_name.". ";
					echo "<strong>".JText::_('OS_EMAIL')."</strong> ".$row->order_email." . ";
					if($row->order_phone != "")
					{
						echo "<strong>".JText::_('OS_PHONE')."</strong> ".$row->order_phone.". ";
					}
					echo "</span>";
					echo '<BR /><span class="label">'.OSBHelper::orderStatus(0,$row->order_status).'</span>';
					echo "<BR />";
					?>
					</span>
				</div>
				<?php
			}
		}
		
		?>
		</div>
		<?php
	}

	static function weekdayCalendarHtml($cal, $w, $m, $y, $lists)
	{
		global $jinput, $mapClass;
		$sid		= $jinput->get('sid',array(),'array');
		$eid		= $jinput->get('eid',array(),'array');
		OSBHelper::loadTooltip();
		JToolBarHelper::title(JText::_('Weekly Calendar'),'list');
        JToolbarHelper::custom('orders_list','list.png','list.png',JText::_('OS_MANAGE_ORDERS'), false);
        JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<form method="post" action="index.php?option=com_osservicesbooking" name="adminForm" id="adminForm">
		<div class="<?php echo $mapClass['row-fluid']; ?>">
			<div class="<?php echo $mapClass['span3'];?>">
				<strong><?php echo JText::_('OS_SERVICE');?>: </strong>
				<div class="clearfix"></div>
				<?php
				echo OSBHelper::getChoicesJsSelect($lists['sid']);
				?>
				<BR />
				<strong><?php echo JText::_('OS_EMPLOYEE');?>: </strong>
				<div class="clearfix"></div>
				<div id="employeeFilterDiv">
				<?php
				echo OSBHelper::getChoicesJsSelect($lists['emp']);
				?>
				</div>
				<BR />
				<button class="btn btn-primary" onClick="javascript:convertData();return false;">Filter</button
				<BR /><BR />
				<div id="calendardetails">
				<?php
				self::initCalendar($y, $m);
				?>
				</div>
			</div>
			<div class="<?php echo $mapClass['span9'];?>" id="maincontentdiv">
				<?php echo $cal->calendarWeek($w, $m, $y, $sid, $eid); ?>
			</div>
		</div>
		<?php
		if((int) $d == 0)
		{
			$d = 1;
		}
		?>
		<input type="hidden" name="ossmh" id="ossmh" value="<?php echo $m; ?>" />
		<input type="hidden" name="ossyh" id="ossyh" value="<?php echo $y; ?>" />
		<input type="hidden" name="month" id="month" value="<?php echo $m; ?>" />
		<input type="hidden" name="year"  id="year" value="<?php echo $y; ?>" />
		<input type="hidden" name="sid"  id="sid" value="0" />
		<input type="hidden" name="eid"  id="eid" value="0" />
		<input type="hidden" name="m"	id="m" value="<?php echo $m;?>" />
		<input type="hidden" name="y"	id="y" value="<?php echo $y;?>" />
		<input type="hidden" name="d"	id="d" value="<?php echo $d;?>" />
		<input type="hidden" name="w"	id="w" value="<?php echo $w;?>" />
		<input type="hidden" name="selected_sid"	id="selected_sid" value="" />
		<input type="hidden" name="selected_eid"	id="selected_eid" value="" />
		<input type="hidden" name="task" id="task" value="calendar_weekday" />
		<input type="hidden" name="live_site"  id="live_site" value="<?php echo JUri::root(); ?>" />
		<input type="hidden" name="processItem" id="processItem" value="" />
		</form>
		<script type="text/javascript">
		function convertData()
		{
			var sidArr = [];
			for (var option of document.getElementById('sid').options)
			{
				if (option.selected) {
					sidArr.push(option.value);
				}
			}
			var selected_sid = sidArr.join();
			document.getElementById('selected_sid').value = selected_sid;

			var eidArr = [];
			for (var option of document.getElementById('eid').options)
			{
				if (option.selected) {
					eidArr.push(option.value);
				}
			}
			var selected_eid = eidArr.join();
			document.getElementById('selected_eid').value = selected_eid;

			var m = document.getElementById('m').value;
			var y = document.getElementById('y').value;
			var d = document.getElementById('d').value;

			loadWeeklyCalendar("<?php echo JUri::root(); ?>", y, m, d);
			
		}
		function uploadeEmployees()
		{
			var sid = document.getElementById('sid');
			var selected_sid = getSelectedOptions(sid);
			var selected_options = '';
			if(selected_sid.length > 0)
			{
				selected_options = selected_sid.join(',');
			}
			uploadeEmployeesAjax(selected_options,'<?php echo JUri::root();?>');
		}
		function getSelectedOptions(sel) {
		  var opts = [],
			opt;
		  var len = sel.options.length;
		  for (var i = 0; i < len; i++) {
			opt = sel.options[i];

			if (opt.selected) {
			  opts.push(opt.value);
			}
		  }

		  return opts;
		}
		</script>
		<?php
	}

	public static function bookingItemInformation($row)
	{
		global $configClass;
		$return = $row->service_name."(".$row->employee_name.")::";
		$return.= "<br />".JText::_('OS_NAME').": ".$row->order_name;
		$return.= "<br />".JText::_('OS_EMAIL').": ".$row->order_email;
		$return.= "<br />".JText::_('OS_PHONE').": ".$row->order_phone;
		if($row->user_id > 0)
		{
			$user = JFactory::getUser($row->user_id);
			$return.= "<br />".JText::_('Member ID').": ".$user->username;
		}
		$return.= "<br />".JText::_('Booking Number').": ".$row->order_id;
		$return.= "<br />".JText::_('OS_FROM').": ".date($configClass['time_format'],$row->start_time);
		$return.= "  <br />".JText::_('OS_TO').": ".date($configClass['time_format'],$row->end_time);
		$return.= "  <br />".JText::_('OS_DATE').": ".date($configClass['date_format'],strtotime($row->booking_date));
		return $return;
	}

	/**
	 * Init the calendar
	 *
	 * @param unknown_type $option
	 */
	public static function initCalendar($year,$month)
    {
		global $mainframe,$mapClass,$configClass;
		require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/common.php';
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
        
        $number_days_in_month		= HelperOSappscheduleCalendar::ndaysinmonth($month,$year);
        $selected_date              = $session->get('selected_date','');
        if($selected_date !=""){
            $dateArr                = explode("-",$selected_date);
            $select_year            = $dateArr[0];
            $select_month           = $dateArr[1];
            $select_day             = $dateArr[2];
        }
		if($month != $current_month || $year != $currenct_year)
		{
			$today					= $year."-".$month."-01";
			$date_from				= $year."-".$month."-01";
		}
		else
		{
			$today					= $currenct_year."-".$current_month."-".$current_date;
			$date_from				= $currenct_year."-".$current_month."-".$current_date;
		}
		
		//echo $current_month;
		//echo "Month ".$month;
		$date_from_int = strtotime($date_from);
		$date_to_int = strtotime($date_to);
		
		
		$start_date_current_month 	= strtotime($year."-".$month."-01");
		
		if($configClass['start_day_in_week'] == "monday")
		{
			$start_date_in_week		= date("N",$start_date_current_month);
		}
		else
		{
			$start_date_in_week		= date("w",$start_date_current_month);	
		}
		$monthArr = array(JText::_('OS_JANUARY'),JText::_('OS_FEBRUARY'),JText::_('OS_MARCH'),JText::_('OS_APRIL'),JText::_('OS_MAY'),JText::_('OS_JUNE'),JText::_('OS_JULY'),JText::_('OS_AUGUST'),JText::_('OS_SEPTEMBER'),JText::_('OS_OCTOBER'),JText::_('OS_NOVEMBER'),JText::_('OS_DECEMBER'));
		?>
        <div id="calendardetailsBackend">
            <div id="cal<?php echo intval($month)?><?php echo $year?>" style="display:<?php echo $display; ?>;" class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
                <div class="<?php echo $mapClass['span12'];?>">
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
										<a href="javascript:prev('<?php echo JUri::root();?>','<?php echo $category; ?>','<?php echo $employee_id;?>','<?php echo $vid;?>','<?php echo $sid;?>','<?php echo $date_from;?>','<?php echo $date_to;?>')" class="applink">
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
									<a href="javascript:prevBackend('<?php echo JUri::root();?>')" class="applink">
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
                                <a href="javascript:nextBackend('<?php echo JUri::root();?>')" class="applink">
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
                    </table>
                </div>
                <table width="100%" class="apptableContent">
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
                                    if(($i == $current_date) && ($month == $current_month) && ($year == $current_year))
                                    {
                                        $classname = $configClass['calendar_currentdate_style'];
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
								$onclick = "onclick=\"javascript:loadWeeklyCalendar('".JUri::root()."',".$year.",".$month.",'".$i1."');\"";
							}else{
								$onclick = "";
							}
							if(date("w",strtotime($year. "-". $month ."-".$i1)) == 0 || date("w",strtotime($year. "-". $month ."-".$i1)) == 6)
							{
								$extraClass = "weekend";
							}
							elseif((int) date("d",strtotime($year. "-". $month ."-".$i1)) == (int)date("d") && (int) date("m",strtotime($year. "-". $month ."-".$i1)) == (int)date("m") && (int) date("Y",strtotime($year. "-". $month ."-".$i1)) == (int)date("Y"))
							{
								$extraClass = "today";
							}
							else
							{
								$extraClass = "";
							}
                            ?>
                            <td id="td_cal_<?php echo $i1?>" class="<?php echo $extraClass;?>" align="center" style="padding:0px !important;padding-bottom:3px !important;padding-top:3px !important;">
                                <div class="buttonpadding10" style="" id="a<?php echo $year?><?php echo $month?><?php echo $i1;?>" <?php echo $onclick;?>>
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
}
?>