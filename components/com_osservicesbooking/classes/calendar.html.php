<?php
/*------------------------------------------------------------------------
# calendar.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

class HTML_OsAppscheduleCalendar
{
	static function customerCalendar()
	{
        global $configClass,$jinput;
		JHtml::_('behavior.core');
		HTMLHelper::_('bootstrap.tooltip');
		?>
		<table width="100%">
			<tr>
				<td width="30%">
					<div class="osbheading">
						<h1>
							<?php echo JText::_('OS_MY_WORKKING_LIST');?>
						</h1>
					</div>
				</td>
				<td	width="70%" align="right">
					<?php 
					if(OSBHelper::isPrepaidPaymentPublished()){
					?>
						<input type="button" class="btn btn-secondary" value="<?php echo JText::_('OS_MY_BALANCES')?>" title="<?php echo JText::_('OS_MY_BALANCES')?>" onclick="javascript:customerbalances('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
					<?php } ?>
					<input type="button" class="btn btn-secondary" value="<?php echo JText::_('OS_MY_ORDERS_HISTORY')?>" title="<?php echo JText::_('OS_GO_TO_MY_ORDERS_HISTORY')?>" onclick="javascript:customerorder('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
					<input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_BACK')?>" title="<?php echo JText::_('OS_GO_BACK')?>" onclick="javascript:history.go(-1);"/>
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="2">
					<?php
					$year = $jinput->getInt('year',date("Y",time()));
					$month =  intval($jinput->getInt('month',date("m",time())));
					OSBHelper::initCustomerCalendar($year,$month);
					?>
				</td>
			</tr>
		</table>
		<?php
		if($configClass['footer_content'] != ""){
			?>
			<div class="osbfootercontent">
				<?php echo $configClass['footer_content'];?>
			</div>
			<?php
		}
		?>
		<?php
	}
	/**
	 * List all the work of employee in calendar
	 * @param unknown_type $employee
	 */
	static function employeeCalendar($employee)
	{
		global $mainframe,$configClass,$jinput;
		if(!OSBHelper::isJoomla4())
		{
			JHTML::_('behavior.tooltip');
		}
		?>
		<form method="POST" action="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=default_employeeworks&Itemid='.$jinput->getInt('Itemid'))?>" name="ftForm">
		<table width="100%">
			<tr>
				<td width="50%">
					<div style="font-size:15px;font-weight:bold;">
						<?php echo JText::_('OS_MY_WORKKING_LIST');?>
					</div>
				</td>
				<td	width="50%" align="right">
					<?php
					if($configClass['employee_change_availability'] == 1){
						?>
						<input type="button" class="btn btn-info" value="<?php echo JText::_('OS_AVAILABILITY_STATUS')?>" title="<?php echo JText::_('OS_AVAILABILITY_STATUS')?>" onclick="javascript:workingavailabilitystatus('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
						<?php
					}
					?>
					<input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_BACK')?>" title="<?php echo JText::_('OS_GO_BACK')?>" onclick="javascript:history.go(-1);"/>
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="2">
					<?php
					$year = $jinput->getInt('year',date("Y",time()));
					$month =  intval($jinput->getInt('month',date("m",time())));
					OSBHelper::initEmployeeCalendar($employee->id,$year,$month);
					?>
				</td>
			</tr>
		</table>
		<?php
		if($configClass['footer_content'] != ""){
			?>
			<div class="osbfootercontent">
				<?php echo $configClass['footer_content'];?>
			</div>
			<?php
		}
		?>
		</form>
		<?php
	}
	
	
	static function workinglistinOneDay($day,$rows){
		global $mainframe,$configClass;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);	
		?>
		<link rel="stylesheet" href="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/bootstrap/css/bootstrap.css" type="text/css" />
		<strong><?php echo JText::_('OS_DAY')?></strong> &nbsp;<?php echo date($configClass['date_format'],strtotime($day));?>
		<BR /><BR />
		<table class="table table-striped">
			<thead>
				<tr>
                    <th class="success">
                        <?php echo JText::_('OS_CUSTOMER')?>
                    </th>
					<th class="success">
						<?php echo JText::_('OS_SERVICE')?>
					</th>
					<th class="success">
						<?php echo JText::_('OS_FROM')?>
					</th>
					<th class="success">
						<?php echo JText::_('OS_TO')?>
					</th>
					<th class="success">
						<?php echo JText::_('OS_ADDITIONAL_INFORMATION')?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				for($i=0;$i<count($rows);$i++){
					$row = $rows[$i];
					$data = OSBHelper::generateData($row);
					?>
					<tr class="rows<?php echo $k?>">
                        <td>
                            <?php echo $row->order_name;?>
                        </td>
						<td>
							<?php echo $data[0]->service_name;?>
						</td>
						<td>
							<?php echo date($configClass['time_format'],$data[5]);?>
						</td>
						<td>
							<?php echo date($configClass['time_format'],$data[6]);?>
						</td>
						<td>
							<?php
							if($data[7] > 0){
								echo JText::_('OS_NUMBER_SLOT').": ".$data[7];
								echo "<BR />";
							}
							?>
							<?php echo $data[4];?>
						</td>
					</tr>
					<?php
					$k = 1-$k;
				}
				?>
			</tbody>
		</table>
		<?php
	}

    /**
     * @param $employee
     */
	static function availabilityCalendar($employee){
        global $configClass,$jinput,$mapClass;
		?>

		<form method="POST" action="<?php echo JURI::root()?>index.php?option=com_oscalendar" name="adminForm" id="adminForm">
            <div class="<?php echo $mapClass['row-fluid'];?>">
                <div class="<?php echo $mapClass['span12'];?>">
                    <table class="admintable" width="100%">
                        <tr>
                            <td width="30%">
                                <div style="font-size:15px;font-weight:bold;">
                                    <?php echo JText::_('OS_MY_AVAILABILITY_STATUS');?>
                                </div>
                            </td>
                            <td	width="70%" style="text-align:right;" class="hidden-phone">
                                <?php
                                if($configClass['employee_change_availability'] == 1){
                                    ?>
                                    <input type="button" class="btn btn-info" value="<?php echo JText::_('OS_AVAILABILITY_STATUS')?>" title="<?php echo JText::_('OS_AVAILABILITY_STATUS')?>" onclick="javascript:workingavailabilitystatus('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
                                    <?php
                                }
                                ?>
                                <input type="button" class="btn btn-success" value="<?php echo JText::_('OS_MY_WORKING_CALENDAR')?>" title="<?php echo JText::_('OS_GO_TO_MY_WORKING_CALENDAR')?>" onclick="javascript:workingcalendar('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
                                <?php
                                if(($configClass['integrate_gcalendar'] == 1) and (JFolder::exists(JPATH_ROOT.DS."Zend")) and ($employee->gcalendarid != "")){
                                    ?>
                                    <input type="button" class="btn btn-info" value="<?php echo JText::_('OS_MY_GCALENDAR')?>" title="<?php echo JText::_('OS_MY_GCALENDAR')?>" onclick="javascript:gcalendar('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
                                    <?php
                                }
                                ?>
                                <input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_BACK')?>" title="<?php echo JText::_('OS_GO_BACK')?>" onclick="javascript:history.go(-1);"/>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" colspan="2">
                                <?php
                                $year = $jinput->getInt('year',date("Y",time()));
                                $month =  intval($jinput->getInt('month',date("m",time())));
                                OSBHelper::initCalendarInBackend($employee->id,$year,$month);
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
			<!--
            <div class="<?php echo $mapClass['row-fluid'];?>">
                <div class="<?php echo $mapClass['span12'];?>">
                    <h3><?php echo Jtext::_('OS_CUSTOM_BREAK_TIME');?></h3>
                    <div id="rest_div">
                        <?php
                        OsAppscheduleCalendar::getCustomBreakTime($employee->id);
                        ?>
                    </div>
                </div>
            </div>
			-->
		<input type="hidden" name="task"    	id="task" 	value=""/>
		<input type="hidden" name="option"  	id="option" value="com_osservicesbooking"/>
		<input type="hidden" name="boxchecked"				value="0" />
		<input type="hidden" name="year"    	id="year" 	value="<?php echo $year;?>" />
		<input type="hidden" name="month"   	id="month" 	value="<?php echo $month;?>" />
		<input type="hidden" name="live_site"   id="live_site" value="<?php echo JURI::root()?>" />
        <input type="hidden" name="eid"         id="eid"    value="<?php echo $employee->id; ?>" />
		</form>
		<?php
	}

	static function customBreaktime($customs,$lists){
	    global $mapClass;
        if(count($customs) > 0){
            ?>
            <table width="100%" id="employewordstable">
                <tr>
                    <td width="30%" class="osbtdheader">
                        <?php echo JText::_('OS_SERVICE')?>
                    </td>
                    <td width="30%" class="osbtdheader">
                        <?php echo JText::_('OS_DATE')?>
                    </td>
                    <td width="20%" class="osbtdheader">
                        <?php echo JText::_('OS_REMOVE')?>
                    </td>
                </tr>
                <?php
                for($i=0;$i<count($customs);$i++){
                    $rest = $customs[$i];
                    ?>
                    <tr>
                        <td width="30%" align="left" class="td_data">
                            <?php
                            echo $rest->service_name;
                            ?>
                        </td>
                        <td width="30%" align="left" class="td_data">
                            <?php
                            $timestemp = strtotime($rest->bdate);
                            echo date("D, jS M Y",  $timestemp);
                            echo "&nbsp;&nbsp;";
                            echo $rest->bstart." - ".$rest->bend;
                            ?>
                        </td>
                        <td width="30%" align="center" class="td_data">
                            <a href="javascript:removeCustomBreakDateFrontend(<?php echo $rest->id?>,'<?php echo JUri::root();?>')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
								  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
								</svg>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
            echo "<BR /><BR />";
        }
		if($lists['employee_area'] == 0)
		{
			echo "<strong>".Jtext::_('OS_ADD_BREAKTIME').'</strong>:&nbsp;';
			echo JHTML::_('calendar','', 'bdate', 'bdate', '%Y-%m-%d', array('class'=>$mapClass['input-medium'], 'size'=>'19',  'maxlength'=>'19'));
			$hourArray = OsAppscheduleCalendar::generateHoursIncludeSecond();
			echo "&nbsp;&nbsp;".Jtext::_('OS_FROM').':&nbsp;';
			echo JHTML::_('select.genericlist',$hourArray,'bstart','class="input-small"','value','text');
			echo "&nbsp;&nbsp;".Jtext::_('OS_TO').':&nbsp;';
			echo JHTML::_('select.genericlist',$hourArray,'bend','class="input-small"','value','text');
			echo "&nbsp;&nbsp;";
			echo $lists['services'];
			?>
			<input type="button" value="<?php echo Jtext::_('OS_SAVE');?>" class="btn btn-warning" onClick="javascript:saveCustomBreakTimeFrontend('<?php echo JUri::root();?>');" />
			<?php
		}
    }

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
        <form method="POST" action="index.php?option=com_osservicesbooking&task=calendar_monthly&Itemid=<?php echo $jinput->getInt('Itemid',0);?>" name="adminForm" id="ftForm">
		<div id="cal<?php echo intval($month)?><?php echo $year?>">
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<table  width="100%" class="apptable">
						<thead>
							<tr>
								<th width="30%" align="right" style="text-align:right;">
									<span onclick="ChangeMonth('pre')" class="applink" style="cursor: pointer;">
									<
									</span>
								</th>
								<th width="40%" align="center" style="text-align:center;">
									<?php
									echo $monthArr[$month-1];
									?>
									&nbsp;
									<?php echo $year;?>
								</th>
								<th width="30%" align="left" style="text-align:left;">
									<span onclick="ChangeMonth('next')" class="applink" style="cursor: pointer;">
									>
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="100%" colspan="3" style="text-align:center;">
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
									<input type="button" class="goBtn btn btn-primary" value="Go" onclick="javascript:ChangeMonth('keep');">
								</td>
							</tr>
						</tbody>
					</table>
					<BR />
					<table  width="100%" class="apptable">
						<tbody>
							<tr>
								<td width="100%" colspan="3" style="text-align:center;">
									<strong><?php echo JText::_('OS_SERVICE');?>: </strong>
									<?php echo $lists['sid'];?>
									&nbsp;&nbsp;&nbsp;
									<strong><?php echo JText::_('OS_EMPLOYEE');?>: </strong>
									&nbsp;&nbsp;&nbsp;
									<?php echo $lists['emp'];?>
									<input type="submit" class="goBtn calendarFilterBtn btn btn-primary" value="<?php echo JText::_('OS_FILTER');?>" />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
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
			<input type="hidden" name="task" id="task" value="calendar_monthly" />
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
}
?>