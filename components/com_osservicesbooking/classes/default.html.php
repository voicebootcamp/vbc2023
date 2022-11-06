<?php
/*------------------------------------------------------------------------
# default.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 Joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
class HTML_OsAppscheduleDefault{
	/**
	 * Default layout of OS Services Booking component
	 *
	 * @param unknown_type $option
	 * @param unknown_type $services
	 * @param unknown_type $year
	 * @param unknown_type $month
	 * @param unknown_type $day
	 * @param unknown_type $category
	 * @param unknown_type $employee_id
	 * @param unknown_type $vid
	 */
	static function defaultLayoutHTML($option,$services,$year,$month,$day,$category,$employee_id,$vid,$sid,$date_from,$date_to, $lists)
    {
		global $mainframe,$mapClass,$configClass,$deviceType,$jinput;
		if(count($lists['selected_dates']) > 0)
		{
			$selected_dates = $lists['selected_dates'];
			$first_date		= $selected_dates[0];
			$first_date_arr = explode("-", $first_date);
			$year			= $first_date_arr[0];
			$month			= $first_date_arr[1];
			$day			= $first_date_arr[2];
		}
        if(intval($month) < 10){
            $month1 = "0".$month;
        }else{
            $month1 = $month;
        }
        if(intval($day) < 10){
            $day1 = "0".$day;
        }else{
            $day1 = $day;
        }
		$methods = os_payments::getPaymentMethods(true, false) ;
		OSBHelper::showProgressBar('',0);
		?>
		<form method="POST" action="<?php echo JRoute::_('index.php?option=com_osservicesbooking');?>" name="appform">
		
		<div id="osbcontainer" class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<?php
				if($lists['pageHeading'] != "")
				{
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<h1 class="component_heading"><?php echo $lists['pageHeading'];?></h1>
						</div>
					</div>
					<?php
				}
				?>
				<?php
				//show Venue information
				if($vid > 0 && $configClass['show_venue'] == 1)
				{
					$db = JFactory::getDbo();
					$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
					$venue = $db->loadObject();
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?> div_category_details">
							<div class="div_category_name">
								<?php
								echo OSBHelper::getLanguageFieldValue($venue,'venue_name');
								?>
							</div>
							<?php
							if($venue->image != "")
							{
								if(file_exists(JPATH_ROOT.'/images/osservicesbooking/venue/'.$venue->image))
								{
									?>
									<div style="float:left;">
										<img src="<?php echo JUri::root();?>images/osservicesbooking/venue/<?php echo $venue->image; ?>" style="max-width:170px;margin-right:10px;" /> 
									</div>
									<?php 
								}
							}
							$addressArr = array();
							$addressArr[] = OSBHelper::getLanguageFieldValue($venue,'address');
							if($row->city != ""){
								$addressArr[] = OSBHelper::getLanguageFieldValue($venue,'city');
							}
							if($row->state != ""){
								$addressArr[] = OSBHelper::getLanguageFieldValue($venue,'state');
							}
							if($row->country != ""){
								$addressArr[] = $venue->country;
							}
							echo implode(", ",$addressArr);
							?>
							<?php 
							if($venue->contact_name != "") {
							?>
								<BR />
								<?php echo JText::_('OS_CONTACT_NAME')?>: <?php echo $venue->contact_name;?>
								
								
							<?php } 
							if($venue->contact_email != "") {
							?>
								<BR />
								<?php echo JText::_('OS_CONTACT_EMAIL')?>: <?php echo $venue->contact_email;?>
							<?php }
							if($venue->contact_phone != "") {
							?>
								<BR />
								<?php echo JText::_('OS_CONTACT_PHONE')?>: <?php echo $venue->contact_phone;?>
							<?php } ?>
							<?php
							if($venue->lat_add != "" && $venue->long_add != "")
							{	
							?>
								<div class="clearfix"></div>
								<div class="<?php echo $mapClass['row-fluid'];?>" style="margin-top:20px;">
									<div class="<?php echo $mapClass['span12'];?>">
									<?php
									self::showMap($venue);
									?>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				<?php
				}
				//end showing venue information
				if($category->id > 0 && $category->show_desc == 1)
				{
				?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?> div_category_details">
							<div class="div_category_name">
								<?php
								echo OSBHelper::getLanguageFieldValue($category,'category_name');
								?>
							</div>
							<?php
							if($category->category_photo != "")
							{
								if(file_exists(JPATH_ROOT.'/images/osservicesbooking/category/'.$category->category_photo))
								{
									?>
									<div style="float:left;">
										<img src="<?php echo JUri::root();?>images/osservicesbooking/category/<?php echo $category->category_photo; ?>" style="max-width:170px;margin-right:10px;" /> 
									</div>
									<?php 
								}
							}
							$desc = OSBHelper::getLanguageFieldValue($category,'category_description');
							echo JHtml::_('content.prepare', $desc);
							?>
						</div>
					</div>
				<?php
				}
				if((count($services) == 1) && ($configClass['show_service_info_box'] == 1))
				{
					$sid = $services[0]->id;
					$jinput->set('sid',$sid);
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?> div_service_details">
							<div class="div_service_name">
								<?php
								echo OSBHelper::getLanguageFieldValue($services[0],'service_name');
								?>
							</div>
							
							<?php
							if($services[0]->service_photo != "")
							{
								if(file_exists(JPATH_ROOT.'/images/osservicesbooking/services/'.$services[0]->service_photo))
								{
									?>
									<div style="float:left;">
										<img src="<?php echo JUri::root();?>images/osservicesbooking/services/<?php echo $services[0]->service_photo; ?>" style="max-width:170px;margin-right:10px;" /> 
									</div>
									<?php 
								}
							}
							$desc = OSBHelper::getLanguageFieldValue($services[0],'service_description');
							echo JHtml::_('content.prepare', $desc);
							if($configClass['show_service_info_box'] == 1)
							{
							?>
								<div class="div_service_information_box_phone">
									<?php HelperOSappscheduleCommon::getServiceInformation($services[0],$year,$month1,$day1);?>
								</div>
								<div class="div_service_information_box hidden-phone">
									<?php HelperOSappscheduleCommon::getServiceInformation($services[0],$year,$month1,$day1);?>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php 
				}
				?>
				<div id="msgDiv" style="width:100%;">
				</div>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?> noleftrightmargin">
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<?php
							if($configClass['using_cart'] == 1 || !OSBHelper::isTheSameDate($date_from,$date_to) || count($lists['selected_dates']) > 0)
							{
								
								$secondDiv = $mapClass['span8'];
								if($configClass['calendar_position'] == 0)
								{
									?>
									<div class="<?php echo $mapClass['span4'];?>" id="calendardivleft" class="hidden-phone">
										<?php
										if(count($lists['selected_dates']) > 0)
										{
											$selected_dates = $lists['selected_dates'];
											$date_from		= $selected_dates[0];
											?>
											<div class="<?php echo $mapClass['row-fluid'];?>">
												<div class="<?php echo $mapClass['span12'];?>">
													<?php
													HelperOSappscheduleCalendar::listDates($lists['selected_dates']);
													?>
												</div>
											</div>
											<?php
										}
										elseif(!OSBHelper::isTheSameDate($date_from,$date_to))
										{
										?>
											<div class="<?php echo $mapClass['row-fluid'];?>">
												<div class="<?php echo $mapClass['span12'];?>">
													<?php
													HelperOSappscheduleCalendar::initCalendarForSeveralYear(intval(date("Y",HelperOSappscheduleCommon::getRealTime())),$category->id,$employee_id,$vid, $sid,$date_from,$date_to);
													?>
													<input type="hidden" name="ossmh" id="ossmh" value="<?php echo $month; ?>" />
													<input type="hidden" name="ossyh" id="ossyh" value="<?php echo $year; ?>" />
												</div>
											</div>
										<?php
										}
										if($configClass['using_cart'] == 1 && $deviceType != "mobile" && $deviceType != "tablet")
										{
											?>
											<div class="clearfix" style="height:10px;"></div>
											<div class="<?php echo $mapClass['row-fluid'];?>">
												<div class="<?php echo $mapClass['span12'];?>">
													<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv" id="cartbox">
														<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
															<?php
															if($configClass['disable_payments'] == 1)
															{
																?>
																<div style="float:left;margin-right:5px;">
																	<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png">
																</div>
																<div style="float:left;padding-top:4px;">
																	<?php echo JText::_('OS_CART')?>
																</div>
																<?php
															}else{
																?>
																<div style="float:left;padding-top:4px;">
																	<?php echo JText::_('OS_BOOKING_INFO');?>
																</div>
																<?php
															}
															?>
														</div>
														<table  width="100%">
															<tr>
																<td width="100%" style="padding:5px;" valign="top">
																	<div id="cartdiv">
																		<?php
																		$userdata = $_COOKIE['userdata'];
																		OsAppscheduleAjax::cart($userdata,$vid,$category->id,$employee_id,$date_from,$date_to);
																		?>
																	</div>
																</td>
															</tr>
														</table>
													</div>
													<div id="servicebox" style="display:none;">

													</div>
												</div>
											</div>
											<?php
										}
										elseif($configClass['using_cart'] == 0 && $deviceType != "mobile" && $deviceType != "tablet")
										{
											if(OsAppscheduleAjax::checkCart($userdata,$vid,$category->id,$employee_id,$date_from,$date_to))
											{
												?>
												<div class="clearfix" style="height:10px;"></div>
												<div class="<?php echo $mapClass['row-fluid'];?>">
													<div class="<?php echo $mapClass['span12'];?>">
														<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
															<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
																<div style="float:left;padding-top:4px;">
																	<?php echo JText::_('OS_BOOKING_INFO');?>
																</div>
															</div>
															<table  width="100%">
																<tr>
																	<td width="100%" style="padding:5px;text-align:center;" valign="top">
																		<div id="cartdiv">
																			<?php
																			echo JText::_('OS_YOU_ALREADY_ADDED_TIMESLOT_TO_CART');
																			?>
																			<BR />
																			<a href="<?php echo JRoute::_("index.php?option=com_osservicesbooking&task=form_step1&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$date_from."&date_to=".$date_to);?>" title="<?php echo JText::_('OS_CHECKOUT');?>" class="btn">
																				<i class="icon-checkedout"></i>
																				<?php echo JText::_('OS_CHECKOUT');?>
																			</a>
																		</div>
																	</td>
																</tr>
															</table>
														</div>
													</div>
												</div>
												<?php
											}
										}
										?>
										<div class="clearfix"></div>
									</div>
									<?php
								}
							}
							else
							{
								$secondDiv = $mapClass['span12'];
							}
							?>
							<div class="<?php echo $secondDiv;?>" id="maincontentdiv">
								<?php
								OsAppscheduleAjax::loadServices($option,$services,$year,$month,$day,$category->id,$employee_id,$vid,$sid,$employee_id);
								?>
							</div>
							<?php
							if($configClass['using_cart'] == 1 || !OSBHelper::isTheSameDate($date_from,$date_to) || count($lists['selected_dates']) > 0)
							{
								
								$secondDiv = $mapClass['span8'];
								if($configClass['calendar_position'] == 1)
								{
									?>
									<div class="<?php echo $mapClass['span4'];?>" id="calendardivleft" class="hidden-phone">
										<?php
										if(count($lists['selected_dates']) > 0)
										{
											$selected_dates = $lists['selected_dates'];
											$date_from		= $selected_dates[0];
											?>
											<div class="<?php echo $mapClass['row-fluid'];?>">
												<div class="<?php echo $mapClass['span12'];?>">
													<?php
													HelperOSappscheduleCalendar::listDates($lists['selected_dates']);
													?>
												</div>
											</div>
											<?php
										}
										elseif(!OSBHelper::isTheSameDate($date_from,$date_to))
										{
										?>
											<div class="<?php echo $mapClass['row-fluid'];?>">
												<div class="<?php echo $mapClass['span12'];?>">
													<?php
													HelperOSappscheduleCalendar::initCalendarForSeveralYear(intval(date("Y",HelperOSappscheduleCommon::getRealTime())),$category->id,$employee_id,$vid, $sid,$date_from,$date_to);
													?>
													<input type="hidden" name="ossmh" id="ossmh" value="<?php echo $month; ?>" />
													<input type="hidden" name="ossyh" id="ossyh" value="<?php echo $year; ?>" />
												</div>
											</div>
										<?php
										}
										if($configClass['using_cart'] == 1 && $deviceType != "mobile" && $deviceType != "tablet")
										{
											?>
											<div class="clearfix" style="height:10px;"></div>
											<div class="<?php echo $mapClass['row-fluid'];?>">
												<div class="<?php echo $mapClass['span12'];?>">
													<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv" id="cartbox">
														<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
															<?php
															if($configClass['disable_payments'] == 1)
															{
																?>
																<div style="float:left;margin-right:5px;">
																	<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png">
																</div>
																<div style="float:left;padding-top:4px;">
																	<?php echo JText::_('OS_CART')?>
																</div>
																<?php
															}else{
																?>
																<div style="float:left;padding-top:4px;">
																	<?php echo JText::_('OS_BOOKING_INFO');?>
																</div>
																<?php
															}
															?>
														</div>
														<table  width="100%">
															<tr>
																<td width="100%" style="padding:5px;" valign="top">
																	<div id="cartdiv">
																		<?php
																		$userdata = $_COOKIE['userdata'];
																		OsAppscheduleAjax::cart($userdata,$vid,$category->id,$employee_id,$date_from,$date_to);
																		?>
																	</div>
																</td>
															</tr>
														</table>
													</div>
													<div id="servicebox" style="display:none;">

													</div>
												</div>
											</div>
											<?php
										}
										elseif($configClass['using_cart'] == 0 && $deviceType != "mobile" && $deviceType != "tablet")
										{
											if(OsAppscheduleAjax::checkCart($userdata,$vid,$category->id,$employee_id,$date_from,$date_to))
											{
												?>
												<div class="clearfix" style="height:10px;"></div>
												<div class="<?php echo $mapClass['row-fluid'];?>">
													<div class="<?php echo $mapClass['span12'];?>">
														<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
															<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
																<div style="float:left;padding-top:4px;">
																	<?php echo JText::_('OS_BOOKING_INFO');?>
																</div>
															</div>
															<table  width="100%">
																<tr>
																	<td width="100%" style="padding:5px;text-align:center;" valign="top">
																		<div id="cartdiv">
																			<?php
																			echo JText::_('OS_YOU_ALREADY_ADDED_TIMESLOT_TO_CART');
																			?>
																			<BR />
																			<a href="<?php echo JRoute::_("index.php?option=com_osservicesbooking&task=form_step1&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$date_from."&date_to=".$date_to);?>" title="<?php echo JText::_('OS_CHECKOUT');?>" class="btn">
																				<i class="icon-checkedout"></i>
																				<?php echo JText::_('OS_CHECKOUT');?>
																			</a>
																		</div>
																	</td>
																</tr>
															</table>
														</div>
													</div>
												</div>
												<?php
											}
										}
										?>
										<div class="clearfix"></div>
									</div>
									<?php
								}
							}	
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php
			if(($configClass['using_cart'] == 1) and (($deviceType == "mobile") or ($deviceType == "tablet"))){
			?>
			<div class="clearfix" style="height:10px;"></div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv" id="cartbox">
						<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
							<?php
							if($configClass['disable_payments'] == 1){
							?>
							<div style="float:left;margin-right:5px;">
								<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png">
							</div>
							<div style="float:left;padding-top:4px;">
								<?php echo JText::_('OS_CART')?>
							</div>
							<?php
							}else{
							?>
							<div style="float:left;padding-top:4px;">
								<?php echo JText::_('OS_BOOKING_INFO');?>
							</div>
							<?php
							}
							?>
						</div>
						<table  width="100%">
							<tr>
								<td width="100%" style="padding:5px;" valign="top">
									<div id="cartdiv">
										<?php
										$userdata = $_COOKIE['userdata'];
										OsAppscheduleAjax::cart($userdata,$vid,$category->id,$employee_id,$date_from,$date_to);
										?>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id="servicebox" style="display:none;">
						
					</div>
				</div>
			</div>
			<?php 
			}elseif(($configClass['using_cart'] == 0) and (($deviceType == "mobile") or ($deviceType == "tablet"))){
				if(OsAppscheduleAjax::checkCart($userdata,$vid,$category->id,$employee_id,$date_from,$date_to)){
					?>
					<div class="clearfix" style="height:10px;"></div>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
									<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
										<a href="<?php echo JRoute::_("index.php?option=com_osservicesbooking&task=form_step1&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$date_from."&date_to=".$date_to);?>" title="<?php echo JText::_('OS_CHECKOUT');?>" class="btn">
											<i class="icon-checkedout"></i>
											<?php echo JText::_('OS_CHECKOUT');?>
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php
				}
			}

			if($configClass['show_footer'] == 1){
				if($configClass['footer_content'] != ""){
					?>
					<div class="osbfootercontent">
						<?php echo $configClass['footer_content'];?>
					</div>
					<?php
				}
			}

			if($configClass['using_cart'] == 0){
				?>
				<div class="<?php echo $mapClass['row-fluid'];?>" style="display:none;">
					<div class="<?php echo $mapClass['span12'];?>">
						<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv" id="cartbox">
							<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
								<?php
								if($configClass['disable_payments'] == 1){
								?>
								<div style="float:left;margin-right:5px;">
									<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png">
								</div>
								<div style="float:left;padding-top:4px;">
									<?php echo JText::_('OS_CART')?>
								</div>
								<?php
								}else{
								?>
								<div style="float:left;padding-top:4px;">
									<?php echo JText::_('OS_BOOKING_INFO');?>
								</div>
								<?php
								}
								?>
							</div>
							<table  width="100%">
								<tr>
									<td width="100%" style="padding:5px;" valign="top">
										<div id="cartdiv">
											<?php
											$userdata = $_COOKIE['userdata'];
											OsAppscheduleAjax::cart($userdata,$vid,$category->id,$employee_id,$date_from,$date_to);
											?>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<div id="servicebox" style="display:none;">
							
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
        <input name="tab_fields" id="tab_fields" value="<?php echo HelperOSappscheduleCommon::getServicesAndEmployees($services,$year,$month,$day,$category->id,$employee_id,$vid,$sid,$employee_id);?>" type="hidden" />
		<input type="hidden" name="option" value="com_osservicesbooking"  />
		<input type="hidden" name="task" value="">
		<input type="hidden" name="month"  id="month" value="<?php echo $month; ?>" />
		<input type="hidden" name="year"  id="year" value="<?php echo $year; ?>" />
		<input type="hidden" name="day"  id="day" value="<?php echo intval(date("d",HelperOSappscheduleCommon::getRealTime()));?>" />
		<input type="hidden" name="select_day" id="select_day" value="<?php echo $day;?>" />
		<input type="hidden" name="select_month" id="select_month" value="<?php echo $month;?>" />
		<input type="hidden" name="select_year" id="select_year" value="<?php echo $year;?>" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JURI::root()?>"  />
		<input type="hidden" name="order_id" id="order_id" value="" />
		<input type="hidden" name="current_date" id="current_date" value=""  />
		<input type="hidden" name="use_captcha" id="use_captcha" value="<?php echo $configClass['value_sch_include_captcha'];?>" />
		<input type="hidden" name="category_id" id="category_id" value="<?php echo intval($category->id);?>" />
		<input type="hidden" name="employee_id" id="employee_id" value="<?php echo intval($employee_id);?>" />
		<input type="hidden" name="vid" id="vid" value="<?php echo intval($vid);?>" />
		<input type="hidden" name="selected_item" id="selected_item" value="" />
        <?php
        if((int) $sid == 0){
            $sid = $services[0]->id;
        }
        ?>
		<input type="hidden" name="sid" id="sid" value="<?php echo $sid;?>" />
		<input type="hidden" name="eid" id="eid" value="" />
		<input type="hidden" name="current_link" id="current_link" value="<?php echo $configClass['current_link']?>" />
		<input type="hidden" name="calendar_normal_style" id="calendar_normal_style" value="<?php echo $configClass['calendar_normal_style'];?>" />
		<input type="hidden" name="calendar_currentdate_style" id="calendar_currentdate_style" value="<?php echo $configClass['calendar_currentdate_style'];?>" />
		<input type="hidden" name="calendar_activate_style" id="calendar_activate_style" value="<?php echo $configClass['calendar_activate_style'];?>" />
		<input type="hidden" name="booked_timeslot_background" id="booked_timeslot_background" value="<?php echo ($configClass['booked_timeslot_background'] != '') ? $configClass['booked_timeslot_background']:'red';?>" />
		<input type="hidden" name="use_js_popup" id="use_js_popup" value="<?php echo $configClass['use_js_popup'];?>" />
		<input type="hidden" name="using_cart" id="using_cart" value="<?php echo $configClass['using_cart'];?>" />
		<input type="hidden" name="date_from" id="date_from" value="<?php echo $date_from?>" />
		<input type="hidden" name="date_to" id="date_to" value="<?php echo $date_to?>" />
		<input type="hidden" name="unique_cookie" id="unique_cookie" value="<?php echo OSBHelper::getUniqueCookie();?>" />
        <input type="hidden" name="temp_item" id="temp_item" value="" />
		<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
		<input type="hidden" name="count_services" id="count_services" value="<?php echo count($services);?>" />
		<?php
		if(OSBHelper::isJoomla4())
		{
			$j4 = 1;
		}
		else
		{
			$j4 = 0;
		}
		?>
		<input type="hidden" name="j4" id="j4" value="<?php echo $j4;?>" />
		<?php
		$temp = array();
		foreach($services as $s){
			$temp[] = $s->id;
		}
		$temp = implode(",",$temp);
		?>
		<input type="hidden" name="services" id="services" value="<?php echo $temp;?>" />
		</form>
		<div  id="divtemp" style="width:1px;height:1px;"></div>
		<script type="text/javascript">
		<?php
		os_payments::writeJavascriptObjects();
		?>
		function addtoCart(sid,eid,time_length)
        {
			var form			= document.appform;
			var category_id		= document.getElementById('category_id');
			var employee_id     = document.getElementById('employee_id');
			<?php
            if($configClass['allow_multiple_timeslots'] == 1)
            {
                ?>
                var selectedItem = new Array();
                jQuery("#multiple_" + sid + "_" + eid + " option:selected").each( function () {
                    selectedItem.push(jQuery(this).val());
                });
                <?php
            }
            ?>
			var bookitem		= document.getElementById('book_' + sid +  '_' + eid);
			var end_bookitem 	= document.getElementById('end_book_' + sid +  '_' + eid);
			end_bookitem		= end_bookitem.value;
			var startitem 		= document.getElementById('start_' + sid +  '_' + eid);
			var enditem 		= document.getElementById('end_' + sid +  '_' + eid);
			var summary 		= document.getElementById('summary_' + sid +  '_' + eid);
			var str             = "";
			var selected_item   = document.getElementById('selected_item');
			selected_item.value = 'employee' + sid + '_' + eid;

			var repeat_name     = sid + "_"+ eid;
			var repeat_type		= document.getElementById('repeat_type_' + repeat_name);
			var repeat_amount   = document.getElementById('repeat_to_' + repeat_name);
			var rtype		  	= "";
			var ramount			= "";
			var repeat          = "";
			if(repeat_amount != null)
			{
				ramount = repeat_amount.value;
			}
			if(repeat_type != null)
			{
				rtype = repeat_type.value;
			}
			if((ramount != "") && (repeat_type != ""))
			{
				repeat_to		= ramount;
				repeat  		= "" + rtype + "|" + repeat_to;
			}
			
			var vidElement = document.getElementById('vid');
			if(vidElement != null)
			{
				vid = vidElement.value;
			}else{
				vid =  0;
			}
			
			var hasValue = 0;
            <?php
            if($configClass['allow_multiple_timeslots'] == 1)
            {
            ?>
                if(selectedItem.length == "")
            <?php
            }
            else
            {
            ?>
                if(bookitem.value == "")
            <?php
            }
            ?>
			{
				alert("<?php echo JText::_('OS_PLEASE_SELECT_START_TIME');?>");
				return false;
			}
			else
			{
				var field_ids   = document.getElementById('field_ids' + sid);
                if(field_ids != null)
                {
                    field_ids = field_ids.value;
                    if (field_ids != "")
                    {
                        var fieldArr = new Array();
                        fieldArr = field_ids.split(",");
                        var temp;
                        var label;
                        if (fieldArr.length > 0)
                        {
                            for (i = 0; i < fieldArr.length; i++)
                            {
                                temp = fieldArr[i];
                                var element		= document.getElementById('field_' + sid + '_' + eid + '_' + temp + '_selected');
								var required	= document.getElementById('field_' + sid + '_' + eid + '_' + temp + '_required');
								var label		= document.getElementById('field_' + sid + '_' + eid + '_' + temp + '_label');
                                if (element != null) {
                                    if (element.value != "")
                                    {
                                        hasValue = 1;
                                        str += temp + "-" + element.value + "@@";
                                    }
                                    else if(required.value == "1")
                                    {
										alert(label.value + "<?php echo JText::_('OS_IS_MANDATORY_FIELD');?>");
										return false;
									}
                                }
                            }
                            //summary.innerHTML = str;
                            if (hasValue == 1)
                            {
                                str = str.substring(0, str.length - 1);
                            }
                        }
                    }
                }
				var service_time_type = document.getElementById('service_time_type_' + sid);
				service_time_type = service_time_type.value;
				if(service_time_type == "1")
				{
					var nslots = document.getElementById('nslots_' + sid + '_' + eid);
					nslots = nslots.value;
					if(nslots == "")
					{
						alert("<?php echo JText::_('OS_INVALID_NUMBER');?>");
						document.getElementById('nslots_' + sid + '_' + eid).focus();
						return false;
					}
					else if(isNaN (nslots))
					{
						alert("<?php echo JText::_('OS_INVALID_NUMBER');?>");
						document.getElementById('nslots_' + sid + '_' + eid).focus();
						return false;
					}
					nslots = parseInt(nslots);
					var max_seats = document.getElementById('max_seats_' + sid);
					max_seats_value = max_seats.value;
					max_seats_value = parseInt(max_seats_value);
					if(max_seats_value > 0)
					{
						if(nslots > max_seats_value)
						{
							alert("<?php echo JText::_('OS_PLEASE_CHANGE_YOUR_NUMBER_SLOTS_TO');?> " + max_seats.value);
							document.getElementById('nslots_' + sid + '_' + eid).focus();
							return false;
						}
					}
				}
				<?php if($configClass['use_js_popup'] == 1){?>
				var answer = confirm("<?php echo JText::_('OS_ARE_YOU_SURE_TO_BOOK')?>");
				<?php }else{ ?>
				var answer = 1;
				<?php } ?>
				var end_booking_time = parseInt(bookitem.value) + parseInt(time_length);
				if(answer == 1)
				{
					var live_site = document.getElementById('live_site');
					var x = document.getElementsByName("addtocartbtn");
					var i;
					//disable all buttons in the form
					for (i = 0; i < x.length; i++) {
							x[i].disabled = true;
					}
                    <?php
                    if($configClass['allow_multiple_timeslots'] == 1)
                    {
                    ?>
                        addtoCartAjaxMultiple(selectedItem,sid,eid,live_site.value,str,repeat,vid,category_id.value,employee_id.value);
                    <?php
                    }
                    else
                    {
                    ?>
					    addtoCartAjax(bookitem.value,end_bookitem,sid,eid,live_site.value,str,repeat,vid,category_id.value,employee_id.value);
					<?php
					}
					?>
				}
			}
		}

		function removeItem(itemid,sid,start_time,end_time,eid)
        {
			<?php if($configClass['use_js_popup'] == 1){?>
			var answer = confirm("<?php  echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_BOOKING')?>");
			<?php }else{ ?>
			var answer = 1;
			<?php } ?>
			if(answer == 1)
			{
				var category_id		= document.getElementById('category_id');
				var employee_id     = document.getElementById('employee_id');
				var vid				= document.getElementById('vid');
				var live_site		= document.getElementById('live_site');
				var count_services  = document.getElementById('count_services');
				removeItemAjax(itemid,live_site.value,sid,start_time,end_time,eid, category_id.value, employee_id.value,vid.value,count_services.value);
			}
		}

		function removeAllItem(sid)
        {
			<?php if($configClass['use_js_popup'] == 1){?>
			var answer = confirm("<?php  echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_BOOKING')?>");
			<?php }else{ ?>
			var answer = 1;
			<?php } ?>
			if(answer == 1)
			{
				var category_id		= document.getElementById('category_id');
				var employee_id     = document.getElementById('employee_id');
				var vid				= document.getElementById('vid');
				var live_site		= document.getElementById('live_site');
				var count_services  = document.getElementById('count_services');
				removeAllItemAjax(live_site.value,sid,category_id.value, employee_id.value,vid.value,count_services.value);
			}
		}
		
		var screenWidth = jQuery(window).width();
		if(screenWidth < 350){
			jQuery(".buttonpadding10").removeClass("buttonpadding10").addClass("buttonpadding5");
			jQuery("#maincontentdiv").removeClass("<?php echo $mapClass['span6']?>").removeClass("<?php echo $mapClass['span7']?>").addClass("<?php echo $mapClass['span12']?>");
		}else{
			jQuery(".buttonpadding5").removeClass("buttonpadding5").addClass("buttonpadding10");
			if(document.getElementById('calendardivleft') != null){
				var leftwidth = jQuery("#calendardivleft").width();
				if(leftwidth > 250){
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span5']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span4']?>");
					jQuery("#maincontentdiv").removeClass("<?php echo $mapClass['span7']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span8']?>");
				}else if(leftwidth < 210){
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span5']?>").removeClass("<?php echo $mapClass['span4']?>").addClass("<?php echo $mapClass['span6']?>");
					jQuery("#maincontentdiv").removeClass("<?php echo $mapClass['span7']?>").removeClass("<?php echo $mapClass['span8']?>").addClass("<?php echo $mapClass['span6']?>");
				}else{
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span4']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span5']?>");
					jQuery("#maincontentdiv").removeClass("<?php echo $mapClass['span8']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span7']?>");
				}
			}
		}

        function changingEmployee(sid)
		{
            var select_item = jQuery("#employeeslist_" + sid).val();
            var existing_services = jQuery("#employeeslist_ids" + sid).val();
            existing_services = existing_services.split("|");
            if(existing_services.length > 0)
			{
                for(i=0;i<existing_services.length;i++)
				{
					jQuery("#pane" + sid + '_' +  existing_services[i]).css('display','none');
                }
            }
			jQuery("#pane" + sid + '_'  +  select_item).css('display','block');
        }

        function changingService()
		{
            var select_item = jQuery("#serviceslist").val();
			jQuery("#sid").val(select_item);
            var existing_services = jQuery("#serviceslist_ids").val();
            existing_services = existing_services.split("|");
            if(existing_services.length > 0)
			{
                for(i=0;i<existing_services.length;i++)
				{
					jQuery("#pane" + existing_services[i]).css('display','none');
                }
            }
            //jQuery("#pane" + select_item).addClass("active");
			jQuery("#pane" + select_item).css('display','block');
        }

		function changingServiceTab(sid){
			jQuery("#sid").val(sid);
        }

		function changingEmployeeTab(eid){
			//jQuery("#employee_id").val(eid);
        }

		jQuery("#closeDialogBtn").click( function(){
			jQuery( "#dialogstr4" ).dialog("close");
		});

		//alert(jQuery("#osbcontainer").width());
		//article only
		flexScreen = jQuery("#osbcontainer").width();
		if(flexScreen < 600){
			jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span6']?>").removeClass("<?php echo $mapClass['span5']?>").removeClass("<?php echo $mapClass['span4']?>").addClass("span12");
			jQuery("#maindivright").removeClass("<?php echo $mapClass['span6']?>").removeClass("<?php echo $mapClass['span7']?>").removeClass("<?php echo $mapClass['span8']?>").addClass("span12");
			jQuery(".timeslots").removeClass("<?php echo $mapClass['span6']?>").addClass("span12");
			jQuery("#maindivright").attr("style","margin-left:0px !important;margin-top:10px !important");
		}

		remembertabs();
		</script>
		<?php
	}

	/**
	 * Show failure Payment
	 *
	 * @param unknown_type $reason
	 */
	static function failureHtml($reason){
		global $mainframe,$mapClass;
        jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/failure.php')){
            $tpl = new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }else{
            $tpl = new OSappscheduleTemplate(JPATH_COMPONENT.'/components/com_osservicesbooking/layouts/');
        }
        $tpl->set('mainframe',$mainframe);
        $tpl->set('reason',$reason);
        $tpl->set('mapClass',$mapClass);
        $body = $tpl->fetch("failure.php");
        echo $body;
	}

	/**
	 * List all orders history
	 *
	 * @param unknown_type $orders
	 */
	static function listOrdersHistory($rows, $heading, $lists)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$document = JFactory::getDocument();
		jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/orderhistory.php'))
		{
            $tpl = new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }
		else
		{
            $tpl = new OSappscheduleTemplate(JPATH_COMPONENT.'/layouts/');
        }

        $tpl->set('mainframe',$mainframe);
        $tpl->set('rows',$rows);
        $tpl->set('jinput',$jinput);
        $tpl->set('mapClass',$mapClass);
        $tpl->set('configClass',$configClass);
		$tpl->set('heading', $heading);
		$tpl->set('lists', $lists);

        $body = $tpl->fetch("orderhistory.php");
        echo $body;
        ?>
		<script type="text/javascript">
		<?php
		if($configClass['bootstrap_version'] == 1){
		?>
		jQuery(".icon-calendar").removeClass("icon-calendar").addClass("fa fa-calendar");
		<?php } ?>
		</script>
		<?php
	}


    /**
     * This static function is used to show User Balances
     * @param $rows
     */
	static function listUserBalances($rows , $heading)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		?>
		<form method="POST" action="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=default_customer&Itemid='.$jinput->getInt('Itemid'))?>" name="ftForm" id="ftForm">

        <div class="<?php echo $mapClass['row-fluid']?>">
            <div class="<?php echo $mapClass['span4']?> osbheading">
                <?php echo $heading;?>
            </div>
            <div class="<?php echo $mapClass['span8']?> alignright">
                <input type="button" class="btn" value="<?php echo JText::_('OS_MY_ORDERS_HISTORY')?>" title="<?php echo JText::_('OS_GO_TO_MY_ORDERS_HISTORY')?>" onclick="javascript:customerorder('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
                <input type="button" class="btn" value="<?php echo JText::_('OS_MY_BOOKING_CALENDAR')?>" title="<?php echo JText::_('OS_GO_TO_MY_WORKING_CALENDAR')?>" onclick="javascript:customercalendar('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
                <input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_BACK')?>" title="<?php echo JText::_('OS_GO_BACK')?>" onclick="javascript:history.go(-1);"/>
            </div>
        </div>
        <div class="<?php echo $mapClass['row-fluid']?>">
            <div class="<?php echo $mapClass['span12']?>">
			<?php
			if(count($rows) > 0)
			{
			?>
                <table  width="100%">
                    <tr>
                        <td width="3%" class="osbtdheader">
                            #
                        </td>
                        <td width="15%" class="osbtdheader">
                            <?php echo JText::_('OS_AMOUNT');?>
                        </td>
                        <td width="10%" class="osbtdheader hidden-phone">
                            <?php echo JText::_('OS_DATE');?>
                        </td>
                        <td width="3%" class="osbtdheader hidden-phone">
                            <?php echo JText::_('OS_NOTES');?>
                        </td>
                    </tr>
                    <?php
                    for($i=0;$i<count($rows);$i++){
                        $row = $rows[$i];
                        if($i % 2 == 0){
                            $bgcolor = "#efefef";
                        }else{
                            $bgcolor = "#fff";
                        }
                        ?>
                        <tr>
                            <td class="td_data" style="background-color:<?php echo $bgcolor?>;">
                                <?php echo $i + 1;?>
                            </td>
                            <td class="td_data" style="background-color:<?php echo $bgcolor?>;">
                                <?php echo $row->amount;?>
                            </td>
                            <td class="td_data hidden-phone" style="background-color:<?php echo $bgcolor?>;">
                                <?php echo date(str_replace("H:i","",str_replace(", ","",$configClass['date_time_format'])),strtotime($row->created_date));?>
                            </td>
                            <td class="td_data hidden-phone" style="background-color:<?php echo $bgcolor?>;text-align:center;">
                                <?php echo $row->note;?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
			<?php
			}else{
			?>
                <strong><?php echo JText::_('OS_YOUR_BALANCES_IS_EMPTY');?></strong>
			<?php
			}
			?>
            </div>
        </div>
		<?php
		if($configClass['footer_content'] != ""){
			?>
			<div class="osbfootercontent">
				<?php echo $configClass['footer_content'];?>
			</div>
			<?php
		}
		?>
		<input type="hidden" name="option" value="com_osservicesbooking"  />
		<input type="hidden" name="task" value="default_customer" />
		<input type="hidden" name="oid" id="oid" value="" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid')?>" />
		</form>
		<?php
	}

	static function employeesetting($services, $row , $rests, $busy , $lists)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$db = JFactory::getDbo();
		$inputSmallClass	= $mapClass['input-small'];
		?>
		<div class="<?php echo $mapClass['row-fluid'];?>" id="employeeSettingDiv">
			<div class="<?php echo $mapClass['span12'];?>">
				<div class="<?php echo $mapClass['row-fluid'];?>" >
					<div class="<?php echo $mapClass['span12'];?>">
						<h2>
							<?php
							echo JText::_('OS_EMPLOYEE_AVAILABILITY');
							?>
						</h2>
					</div>
				</div>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?>" style="text-align:right;">
						<a href="javascript:submitEmployeeAvailForm();" class="btn btn-primary" title="<?php echo JText::_('OS_SAVE');?>"><?php echo JText::_('OS_SAVE');?></a>
					</div>
				</div>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?>">
						<form method="post" name="employeeAvailForm" id="employeeAvailForm" action="<?php echo JUri::root();?>index.php?option=com_osservicesbooking">
							<strong><?php echo JText::_('OS_REST_DAYS'); ?></strong>
							<div class="clearfix"></div>
							<?php echo JText::_('OS_REST_DAYS_EXPLAIN'); ?>
							<div id="rest_div">
								<?php
								if(count($rests) > 0)
								{
									?>
									<table width="100%" style="border:1px solid #CCC !important;">
										<tr>
											<td width="30%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC !important; ">
												<?php echo JText::_('OS_DATE')?>
											</td>
											<td width="20%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC !important;">
												<?php echo JText::_('OS_REMOVE')?>
											</td>
										</tr>
										<?php
										for($i=0;$i<count($rests);$i++){
											$rest = $rests[$i];
											?>
											<tr>
												<td width="30%" align="left" style="padding-left:10px;">
													<?php
													$timestemp = strtotime($rest->rest_date);
													echo date("D, jS M Y",  $timestemp);
													$timestemp = strtotime($rest->rest_date_to);
													echo " - ";
													echo date("D, jS M Y",  $timestemp)
													?>
												</td>
												<td width="30%" align="center">
													<a href="javascript:removeBreakDate(<?php echo $rest->id?>)" title="<?php echo JText::_('OS_REMOVE_REST_DATE');?>">
														<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
														  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
														</svg>
													</a>
												</td>
											</tr>
											<?php
										}
										?>
									</table>
									<?php
								}
								?>
							</div>
							<BR />
							<B><?php echo JText::_('OS_ADD_REST_DAY')?></B>
							<BR />
							<?php
							for($i=1;$i<=5;$i++)
								{
								echo JText::_('OS_DATE');
								echo " #".$i.": ";
								echo JText::_('OS_FROM').": ";
								echo JHTML::_('calendar','', 'date'.$i, 'date'.$i, '%Y-%m-%d', array('class'=> $inputSmallClass .' ishort' , 'size'=>'19',  'maxlength'=>'19')); 
								echo " - ";
								echo JText::_('OS_TO').": ";
								echo JHTML::_('calendar','', 'date_to_'.$i, 'date_to_'.$i, '%Y-%m-%d', array('class'=> $inputSmallClass .' ishort', 'size'=>'19',  'maxlength'=>'19')); 
								echo "<BR />";
							}
							?>
							
							<BR />
							<BR />
							<strong><?php echo JText::_('OS_BUSY_TIME'); ?></strong>
							<div class="clearfix"></div>
							<?php echo JText::_('OS_BUSY_TIME_EXPLAIN'); ?>
							<div id="busy_div">
								<?php
								if(count($busy) > 0){
									?>
									<table width="100%" style="border:1px solid #CCC !important;">
										<tr>
											<td width="30%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC !important;">
												<?php echo JText::_('OS_DATE')?>
											</td>
											<td width="20%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC !important;">
												<?php echo JText::_('OS_REMOVE')?>
											</td>
										</tr>
										<?php
										for($i=0;$i<count($busy);$i++)
										{
											$b = $busy[$i];
											?>
											<tr>
												<td width="30%" align="left" style="padding-left:10px;">
													<?php
													$timestemp = strtotime($b->busy_date);
													echo date("D, jS M Y",  $timestemp);
													echo " - ".JText::_('OS_FROM').": ".$b->busy_from.". ".JText::_('OS_TO').": ".$b->busy_to;
													?>
												</td>
												<td width="30%" align="center">
													<a href="javascript:removeBusyTime(<?php echo $b->id?>)" title="<?php echo JText::_('OS_REMOVE_BUSY_DATE');?>">
														<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
														  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
														</svg>
													</a>
												</td>
											</tr>
											<?php
										}
										?>
									</table>
									<?php
								}
								?>
							</div>
							<BR />
							<B><?php echo JText::_('OS_ADD_BUSY_TIME')?></B>
							<BR />
							<?php
							for($i=1;$i<=5;$i++)
							{
								echo JText::_('OS_DATE');
								echo " #".$i.": ";

								echo JHTML::_('calendar','', 'busy_date'.$i, 'busy_date'.$i, '%Y-%m-%d', array('class'=> $inputSmallClass . ' ishort', 'size'=>'19',  'maxlength'=>'19'));
								echo " - ";
								echo JText::_('OS_FROM').": ";
								echo "<input type='text' name='busy_from".$i."' id='busy_from".$i."' placeholder='00:00' class='".$inputSmallClass." ishort'> ";
								echo JText::_('OS_TO').": ";
								echo "<input type='text' name='busy_to".$i."' id='busy_to".$i."' placeholder='00:00' class='".$inputSmallClass."  ishort'>";
								echo "<BR />";
							}
							?>
							<BR />
							<BR />
							<strong>
							<?php
							echo JText::_('OS_ADDITIONAL_PRICE_BY_HOUR');
							?>
							</strong>
							<div class="clearfix"></div>
							<table style="width:90% !important;" class="table table-striped"> 
								<thead>
									<tr>
										<th width="20%">
											<?php echo JText::_('Week day');?>
										</th>
										<th width="20%" align="center">
											<?php
												echo JText::_('OS_WORKTIME_START_TIME');
											?>
										</th>
										<th width="20%" align="center">
											<?php
												echo JText::_('OS_WORKTIME_END_TIME');
											?>
										</th>
										<th width="20%" align="center">
											<?php echo JText::_('OS_ADDITIONAL_PRICE');?>
										</th>
										<th width="20%" align="center">
											<?php echo JText::_('OS_RESET');?>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 0;
									if(count($lists['extra']) > 0){
										$k = 0;
										for($i=0;$i<count($lists['extra']);$i++){
											$rs = $lists['extra'][$i];
											
											?>
											<tr class="row<?php echo $k?>">
												<td width="20%" align="center">
													<?php
													echo JHTML::_('select.genericlist',$lists['week_day'],'week_day'.$i,'class="input-medium form-select"','value','text',$rs->week_date);
													?>
												</td>
												<td width="20%" align="center">
													<?php
													echo JHTML::_('select.genericlist',$lists['hours'],'start_time'.$i,'class="input-small form-select"','value','text',$rs->start_time);
													?>
												</td>
												<td width="20%" align="center">
													<?php
													echo JHTML::_('select.genericlist',$lists['hours'],'end_time'.$i,'class="input-small form-select"','value','text',$rs->end_time);
													?>
												</td>
												<td width="20%" align="center">
													<input type="text" name="extra_cost<?php echo $i?>" id="extra_cost<?php echo $i?>" class="input-mini" size="5" value="<?php echo $rs->extra_cost?>" /> <?php echo $configClass['currency_format'];?>
												</td>
												<td width="20%" align="center">
													<input type="button" class="btn btn-info" value="<?php echo JText::_('OS_RESET')?>" onClick="javascript:resetRow(<?php echo $i?>);" />
												</td>
											</tr>
											<?php
											$k = 1 - $k;
										}
									}
									if($i<10){
										if($i > 0){
											$j = $i + 1;
										}else{
											$j = 0;
										}
										$k = 0;
										for($i=$j;$i<15;$i++){
											?>
											<tr class="row<?php echo $k?>">
												<td width="20%" align="center">
													<?php
													echo JHTML::_('select.genericlist',$lists['week_day'],'week_day'.$i,'class="input-medium  form-select"','value','text','');
													?>
												</td>
												<td width="20%" align="center">
													<?php
													echo JHTML::_('select.genericlist',$lists['hours'],'start_time'.$i,'class="input-small  form-select"','value','text');
													?>
												</td>
												<td width="20%" align="center">
													<?php
													echo JHTML::_('select.genericlist',$lists['hours'],'end_time'.$i,'class="input-small  form-select"','value','text');
													?>
												</td>
												<td width="20%" align="center">
													<input type="text" name="extra_cost<?php echo $i?>" id="extra_cost<?php echo $i?>" class="input-mini form-control imini" size="5" /> <?php echo $configClass['currency_format'];?>
												</td>
												<td width="20%" align="center">
													<input type="button" class="btn btn-info" value="<?php echo JText::_('OS_RESET')?>" onClick="javascript:resetRow(<?php echo $i?>);" />
												</td>
											</tr>
											<?php
											$k = 1 - $k;
										}
									}
									?>
								</tbody>
							</table>
							<input type="hidden" name="option" value="com_osservicesbooking" />
							<input type="hidden" name="task" value="default_saveemployeesetting" />
							<input type="hidden" name="eid" id="eid" value="<?php echo $row->id;?>" />
							<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
						</form>
						<BR />
						<BR />
						<strong>
						<?php
						echo JText::_('OS_SETUP_WORKINGTIME_ON_EXISTING_SERVICES');
						?>
						</strong>
						<table class="table table-striped" style="width:90% !important;">
							<thead>
								
								<th width="15%">
									<?php echo JText::_('OS_SERVICE');?>
								</th>
								<th width="15%">
									<?php echo JText::_('OS_VENUE');?>
								</th>
								<th width="20%">
									<?php echo JText::_('OS_WORKING_DATE');?>
								</th>
								<th width="20%">
									<?php echo JText::_('OS_BREAK_TIME');?>
								</th>
								<th width="15%" style="text-align:center;">
									<?php echo JText::_('OS_SETUP');?>
								</th>
							</thead>
							<tbody>
								<?php
								$k = 0;
								for($i=0;$i<count($services);$i++)
								{
									$k = 1 - $k;
									$service = $services[$i];
									$db->setQuery("Select count(id) from #__app_sch_employee_service where employee_id = '$row->id' and service_id = '$service->id'");
									$count = $db->loadResult();
									$workingdateArr = array();
									if($count > 0)
									{
										$db->setQuery("Select * from #__app_sch_employee_service where employee_id = '$row->id' and service_id = '$service->id'");
										$relation = $db->loadObject();
										if($relation->vid > 0)
										{
											$db->setQuery("Select address from #__app_sch_venues where id = '$relation->vid'");
											$address = $db->loadResult();
										}
										if($relation->mo == 1){
											$workingdateArr[] = JText::_('OS_MON');
											$db->setQuery("Select count(id) from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '1'");
											$countMonday = $db->loadResult();
											$breakMonday = array();
											if($countMonday > 0){
												$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '1'");
												$mondays = $db->loadObjectList();
												if(count($mondays) > 0){
													for($j=0;$j<count($mondays);$j++){
														$breakMonday[$j] = $mondays[$j]->break_from." - ".$mondays[$j]->break_to;
														//$breakMonday[$j]->break_to   = $mondays[$j]->break_to;
													}
												}
											}
										}
										if($relation->tu == 1){
											$workingdateArr[] = JText::_('OS_TUE');
											$db->setQuery("Select count(id) from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '2'");
											$countTuesday = $db->loadResult();
											$breakTuesday = array();
											if($countTuesday > 0){
												$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '2'");
												$tuesdays = $db->loadObjectList();
												if(count($tuesdays) > 0){
													for($j=0;$j<count($tuesdays);$j++){
														$breakTuesday[$j] = $tuesdays[$j]->break_from." - ".$tuesdays[$j]->break_to;
														//$breakTuesday[$j]->break_to   = $tuesdays[$j]->break_to;
													}
												}
											}
										}
										if($relation->we == 1){
											$workingdateArr[] = JText::_('OS_WED');
											$db->setQuery("Select count(id) from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '3'");
											$countWednesday = $db->loadResult();
											$breakWednesday = array();
											if($countWednesday > 0){
												$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '3'");
												$wednesday = $db->loadObjectList();
												if(count($wednesday) > 0){
													for($j=0;$j<count($wednesday);$j++){
														$breakWednesday[$j] = $wednesday[$j]->break_from." - ".$wednesday[$j]->break_to;
														//$breakWednesday[$j]->break_to   = $wednesday[$j]->break_to;
													}
												}
											}
										}
										if($relation->th == 1){
											$workingdateArr[] = JText::_('OS_THU');
											$db->setQuery("Select count(id) from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '4'");
											$countThursday = $db->loadResult();
											$breakThursday = array();
											if($countThursday > 0){
												$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '4'");
												$thursdays = $db->loadObjectList();
												if(count($thursdays) > 0){
													for($j=0;$j<count($thursdays);$j++){
														$breakThursday[$j] = $thursdays[$j]->break_from." - ".$thursdays[$j]->break_to;
														//$breakThursday[$j]->break_to   = $thursdays[$j]->break_to;
													}
												}
											}
										}
										if($relation->fr == 1){
											$workingdateArr[] = JText::_('OS_FRI');
											$db->setQuery("Select count(id) from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '5'");
											$countFriday = $db->loadResult();
											$breakFriday = array();
											if($countFriday > 0){
												$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '5'");
												$fridays = $db->loadObjectList();
												if(count($fridays) > 0){
													for($j=0;$j<count($fridays);$j++){
														$breakFriday[$j] = $fridays[$j]->break_from." - ".$fridays[$j]->break_to;
														//$breakFriday[$j]->break_to   = $fridays[$j]->break_to;
													}
												}
											}
										}
										if($relation->sa == 1){
											$workingdateArr[] = JText::_('OS_SAT');
											$db->setQuery("Select count(id) from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '6'");
											$countSatuday = $db->loadResult();
											$breakSatuday = array();
											if($countSatuday > 0){
												$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '6'");
												$satudays = $db->loadObjectList();
												if(count($satudays) > 0){
													for($j=0;$j<count($satudays);$j++){
														$breakSatuday[$j] = $satudays[$j]->break_from." - ".$satudays[$j]->break_to;
														//$breakSatuday[$j]->break_to   = $satudays[$j]->break_to;
													}
												}
											}
										}
										if($relation->su == 1){
											$workingdateArr[] = JText::_('OS_SUN');
											$db->setQuery("Select count(id) from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '7'");
											$countSunday = $db->loadResult();
											$breakSunday = array();
											if($countSunday > 0){
												$db->setQuery("Select * from #__app_sch_employee_service_breaktime where eid = '$row->id' and sid = '$service->id' and date_in_week = '7'");
												$sundays = $db->loadObjectList();
												if(count($sundays) > 0){
													for($j=0;$j<count($sundays);$j++){
														$breakSunday[$j] = $sundays[$j]->break_from." - ".$sundays[$j]->break_to;
														//$breakSunday[$j]->break_to   = $sundays[$j]->break_to;
													}
												}
											}
										}
									}
									?>
									<tr class="row<?php echo $k?>">
										
										<td>
											<?php echo $service->service_name;?>
										</td>
										<td>
											<?php
											if($count > 0){
												if($relation->vid > 0){
													echo $address;
												}else{
													echo "N/A";
												}
											}else{
												echo "N/A";
											}
											?>
										</td>
										<td>
											<?php
											if($count > 0)
											{
												echo "<span color='green'>".implode(", ",$workingdateArr)."</span>";
											}
											else
											{
												echo "<span color='red'>".JText::_('OS_NO_WORKING_IN_THIS_SERVICE')."</span>";
											}
											?>
										</td>
										<td>
											<?php
											if($count > 0){
												if($countMonday > 0){
													echo JText::_('OS_MON').": ".implode(", ",$breakMonday)."<br />";
												}
												if($countTuesday > 0){
													echo JText::_('OS_TUE').": ".implode(", ",$breakTuesday)."<br />";
												}
												if($countWednesday > 0){
													echo JText::_('OS_WED').": ".implode(", ",$breakWednesday)."<br />";
												}
												if($countThursday > 0){
													echo JText::_('OS_THU').": ".implode(", ",$breakThursday)."<br />";
												}
												if($countFriday > 0){
													echo JText::_('OS_FRI').": ".implode(", ",$breakFriday)."<br />";
												}
												if($countSatuday > 0){
													echo JText::_('OS_SAT').": ".implode(", ",$breakSatuday)."<br />";
												}
												if($countSunday > 0){
													echo JText::_('OS_SUN').": ".implode(", ",$breakSunday)."<br />";
												}
											}
											?>
										</td>
										<td style="text-align:center;">
											<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=default_setupbreaktime&eid=<?php echo $row->id?>&sid=<?php echo $service->id?>&Itemid=<?php echo $jinput->getInt('Itemid',0);?>" title="<?php echo JText::_('OS_CONFIGURE_EMPLOYEE_WITH_THIS_SERVICE');?>">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-date" viewBox="0 0 16 16">
												  <path d="M6.445 11.688V6.354h-.633A12.6 12.6 0 0 0 4.5 7.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61h.675zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82h-.684zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23z"/>
												  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
												</svg>
											</a>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		function submitEmployeeAvailForm()
		{
			document.employeeAvailForm.submit();
		}
		function removeBreakDate(rid)
		{
			removeBreakDateAjaxFrontend(rid,"<?php echo JURI::root()?>");
		}
		function removeBusyTime(bid)
        {
            removeBusyTimeAjaxFrontend(bid,"<?php echo JURI::root()?>");
        }
		function resetRow(id){
			var start_time   = document.getElementById('start_time' + id);
			start_time.value = "";
			var end_time     = document.getElementById('end_time' + id);
			end_time.value   = "";
			var extra_cost   = document.getElementById('extra_cost' + id);
			extra_cost.value = "";
		}
		</script>
		<?php
	}

	static function setupBreakTime($service,$employee,$lists,$customs)
	{
		global $jinput, $mapClass;
		$inputSmallClass = $mapClass['input-small'];
		?>
		<form method="POST" action="<?php echo JUri::root()?>index.php?option=com_osservicesbooking" id="breaktimeForm">
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<h2>
						<?php
						echo JText::_('OS_SETUP_BREAKTIME_OF_EMPLOYEE')." [".$employee->employee_name."] ".JText::_('OS_OF')." ".JText::_('OS_SERVICE')." [".$service->service_name."]";
						?>
					</h2>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>" style="text-align:right;">
					<a href="javascript:applyBreakTimeForm();" class="btn btn-primary"><?php echo JText::_('OS_APPLY');?></a>
					<a href="javascript:saveBreakTimeForm();" class="btn btn-success"><?php echo JText::_('OS_SAVE');?></a>
					<a href="javascript:cancelBreakTimeForm();" class="btn btn-secondary"><?php echo JText::_('OS_CANCEL');?></a>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<table class="admintable" width="100%">
						<tr>
							<td>
								<?php
								echo $lists['services'];
								?>
							</td>
						</tr>
					</table>
					<bR />
					<h3><?php echo Jtext::_('OS_CUSTOM_BREAK_TIME');?></h3>
					<div id="rest_div">
						<?php
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
											echo $service->service_name;
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
												<img src="<?php echo JURI::root(true)?>/administrator/templates/hathor/images/menu/icon-16-delete.png">
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
						?>
					</div>
					<?php
					echo "<strong>".Jtext::_('OS_ADD_BREAKTIME').'</strong>:&nbsp;';
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span3']?>">
							<?php
							echo JHTML::_('calendar','', 'bdate', 'bdate', '%Y-%m-%d', array('class'=> $inputSmallClass . ' ishort', 'size'=>'19',  'maxlength'=>'19'));
							?>
						</div>
						<?php
						$hourArray = OSappscheduleEmployee::generateHoursIncludeSecond();
						?>
						<div class="<?php echo $mapClass['span3']?>">
							<?php
							echo Jtext::_('OS_FROM').':&nbsp;';
							echo JHTML::_('select.genericlist',$hourArray,'bstart','class="'.$inputSmallClass.' ishort form-select"','value','text');
							?>
						</div>
						<div class="<?php echo $mapClass['span3']?>">
							<?php
							echo Jtext::_('OS_TO').':&nbsp;';
							echo JHTML::_('select.genericlist',$hourArray,'bend','class="'.$inputSmallClass.' ishort form-select"','value','text');
							?>
						</div>
						<div class="<?php echo $mapClass['span3']?>">
							<input type="button" value="<?php echo Jtext::_('OS_SAVE_CUSTOM_BREAKTIME');?>" class="btn btn-warning" onClick="javascript:saveCustomBreakTimeFrontend('<?php echo JUri::root();?>');" />
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" id="task" value="" />
			<input type="hidden" name="eid" id="eid" value="<?php echo $employee->id; ?>" />
			<input type="hidden" name="sid" id="sid" value="<?php echo $service->id; ?>" />
			<input type="hidden" name="employee_area" id="employee_area" value="1" />
			<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid',0); ?>" />
		</form>
		<script type="text/javascript">
		function applyBreakTimeForm()
		{
			jQuery("#task").val('default_applybreaktime');
			var form = document.getElementById('breaktimeForm');
			form.submit();
		}
		function saveBreakTimeForm()
		{
			jQuery("#task").val('default_savebreaktime');
			var form = document.getElementById('breaktimeForm');
			form.submit();
		}
		function cancelBreakTimeForm()
		{
			location.href="<?php echo JRoute::_('index.php?option=com_osservicesbooking&view=employeesetting&Itemid='.$jinput->getInt('Itemid',0));?>";
		}
		</script>
		<?php
	}
    /**
     * This static function is used to list employee work
     * @param $employee
     * @param $rows
     */
	static function listEmployeeWorks($employee,$rows,$lists,$pageNav)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$inputMediumClass	= $mapClass['input-medium'];
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		jimport('joomla.filesystem.folder');
		?>
		<form method="POST" action="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=default_employeeworks&Itemid='.$jinput->getInt('Itemid'))?>" name="ftForm">
		<table width="100%" class="table table-stripped">
			<tr>
				<td width="30%">
					<div class="osbheading">
						<?php echo JText::_('OS_MY_WORKKING_LIST');?>
					</div>
				</td>
				<td	width="70%" class="hidden-phone alignright">
					<input type="button" class="btn btn-danger" value="<?php echo JText::_('OS_EXPORT_WORK_TO_CSV')?>" title="<?php echo JText::_('OS_EXPORT_WORK_TO_CSV')?>" onclick="javascript:exportCSV()"/>
					<?php
					if($configClass['employee_change_availability'] == 1)
					{
						?>
						<input type="button" class="btn btn-info" value="<?php echo JText::_('OS_AVAILABILITY_STATUS')?>" title="<?php echo JText::_('OS_AVAILABILITY_STATUS')?>" onclick="javascript:workingavailabilitystatus('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
						<?php
					}
					?>
					<a href="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=calendar_employee&Itemid='.$jinput->getInt('Itemid',0));?>" class="btn btn-primary" title="<?php echo JText::_('OS_GO_TO_MY_WORKING_CALENDAR')?>"><?php echo JText::_('OS_MY_WORKING_CALENDAR')?></a>
					<?php
					if(($configClass['integrate_gcalendar'] == 1) and (JFolder::exists(JPATH_ROOT.DS."Zend")) and ($employee->gcalendarid != "")){
						?>
						<input type="button" class="btn btn-info" value="<?php echo JText::_('OS_MY_GCALENDAR')?>" title="<?php echo JText::_('OS_MY_GCALENDAR')?>" onclick="javascript:gcalendar('<?php echo JURI::root()?>','<?php  echo $jinput->getInt('Itemid',0)?>')"/>
						<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="2">
					<div style="float:left;padding-right:10px;">
						<?php echo $lists['filter_service']; ?>
					</div>
					<div style="float:left;padding-right:10px;">
					<?php echo JHTML::_('calendar',$jinput->get('date1','','string'), 'date1', 'date1', '%Y-%m-%d', array('class'=> $inputMediumClass,  'maxlength'=>'19','placeholder' => JText::_('OS_WORK_FROM'))); ?>
					</div>
					<div style="float:left;padding-right:10px;">
					<?php echo JHTML::_('calendar',$jinput->get('date2','','string'), 'date2', 'date2', '%Y-%m-%d', array('class'=> $inputMediumClass,  'maxlength'=>'19' ,'placeholder' => JText::_('OS_WORK_TO'))); ?>
					</div>
					<div style="float:left;">
					<button onClick="javascript:submitFilterForm();" title="<?php echo JText::_('OS_FILTER');?>" class="btn btn-primary">
						<?php echo JText::_('OS_FILTER');?>
					</button>
					</div>
				</td>
			</tr>
			<?php
			if(count($rows) > 0){
			?>
			<tr>
				<td width="100%" style="padding-top:20px;" colspan="2">
					<table width="100%" id="employewordstable">
						<thead>
							<tr>
								<td width="3%" class="osbtdheader hidden-phone">
									#
								</td>
								<td width="15%" class="osbtdheader hidden-phone">
									<?php echo JText::_('OS_SERVICE_NAME');?>
								</td>
								<td width="10%" class="osbtdheader hidden-phone">
									<?php echo JText::_('OS_DATE');?>
								</td>
								<td width="10%" class="osbtdheader hidden-phone">
									<?php echo JText::_('OS_START');?>
								</td>
								<td width="10%" class="osbtdheader hidden-phone">
									<?php echo JText::_('OS_END');?>
								</td>
								<td width="15%" class="osbtdheader hidden-phone">
									<?php echo JText::_('OS_CUSTOMER');?>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php
							for($i=0;$i<count($rows);$i++)
							{
								$row = $rows[$i];
								if($i % 2 == 0){
									$bgcolor = "#efefef";
								}else{
									$bgcolor = "#fff";
								}
								$config = new JConfig();
								$offset = $config->offset;
								date_default_timezone_set($offset);
								?>
								<tr>
									<td class="td_data" style="background-color:<?php echo $bgcolor?>;" data-label="">
										<?php echo $i + 1;?>
									</td>
									<td class="td_data" style="background-color:<?php echo $bgcolor?>;" data-label="<?php echo JText::_('OS_SERVICE_NAME');?>">
										<?php echo OSBHelper::getLanguageFieldValue($row,'service_name');?>
										<?php
										if (JFactory::getUser()->authorise('osservicesbooking.orders', 'com_osservicesbooking')) 
										{
											?>
											<a href="<?php echo JUri::root();?>index.php?option=com_osservicesbooking&task=ajax_removeOrderItem&id=<?php echo $row->id;?>&Itemid=<?php echo $jinput->getInt('Itemid', 0);?>" title="<?php echo JText::_('OS_CANCEL_ORDER_ITEM');?>">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
												  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
												</svg>
											</a>
											<?php
										}
										?>
									</td>
									<td class="td_data" style="background-color:<?php echo $bgcolor?>;" data-label="<?php echo JText::_('OS_DATE');?>">
										<?php echo date($configClass['date_format'],$row->start_time);?>
									</td>
									<td class="td_data" style="background-color:<?php echo $bgcolor?>;" data-label="<?php echo JText::_('OS_START');?>">
										<?php echo date($configClass['time_format'],$row->start_time);?>
									</td>
									<td class="td_data" style="background-color:<?php echo $bgcolor?>;" data-label="<?php echo JText::_('OS_END');?>">
										<?php echo date($configClass['time_format'],$row->end_time);?>
									</td>
									<td class="td_data" style="background-color:<?php echo $bgcolor?>;" data-label="<?php echo JText::_('OS_CUSTOMER');?>">
										<?php echo $row->order_name;?> 
										<?php
										if($row->order_phone != ""){
											echo "(".$row->order_phone.")";
										}
										?>
										<BR />
										<strong><?php echo JText::_('OS_EMAIL');?></strong>:&nbsp;<a href="mailto:<?php echo $row->order_email;?>" target="_blank"><?php echo $row->order_email;?></a>
										<BR />
										<strong><?php echo JText::_('OS_ADDRESS');?></strong>:&nbsp;<?php echo $row->order_address;?>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<td width="100%" colspan="6" style="text-align:center;">
									<?php
										echo $pageNav->getListFooter();
									?>
									
								</td>
							</tr>
						</tfoot>
					</table>
				</td>
			</tr>
			<?php
			}else{
			?>
			<tr>
				<td width="100%" align="center" style="padding:20px;" colspan="2">
					<strong><?php echo JText::_('OS_NO_WORK');?></strong>
				</td>
			</tr>
			<?php
			}
			?>
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
		<input type="hidden" name="option" value="com_osservicesbooking"  />
		<input type="hidden" name="task" value="default_employeeworks" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
		</form>
		<script type="text/javascript">
		<?php
		if($configClass['bootstrap_version'] == 1){
		?>
		jQuery(".icon-calendar").removeClass("icon-calendar").addClass("fa fa-calendar");
		<?php } ?>
		
		function exportCSV()
		{
			document.ftForm.task.value = 'default_employeeworksexport';
			document.ftForm.submit();
		}
		function submitFilterForm()
		{
			document.ftForm.task.value = 'default_employeeworks';
			document.ftForm.submit();
		}
		</script>
		<?php
	}
	static function showOrderDetailsForm($order,$rows,$checkin)
	{
		global $mainframe,$mapClass,$configClass, $jinput;
		?>
        <h2 class="orderdetailsheading">
            <?php echo JText::_('OS_ORDER');?> #<?php echo $order->id?>
        </h2>
		<table width="100%" class="orderdetailsheader">
            <?php
            if(($configClass['show_details_and_orders'] == 1) and ($checkin == 0)){
                ?>
                <tr>
                    <td width="100%" style="border:1px solid #CCC !important;padding:5px;" colspan="2"
                        class="hidden-phone">
                        <?php echo JText::_('OS_ORDER');?> URL: <a
                            href="<?php echo JURI::root()?>index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=<?php echo $order->id?>&ref=<?php echo md5($order->id);?>"
                             id="order_details_link"><?php echo JURI::root()?>
                            index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=<?php echo $order->id?>
                            &ref=<?php echo md5($order->id);?></a>
							&nbsp;
							<a href="javascript:void(0);" title="<?php echo JText::_('OS_COPY_LINK');?>" id="copy_order_details_link">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
								  <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
								  <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
								</svg>
							</a>
							<?php if ($configClass['allow_cancel_request'] == 1) { ?>
                            <BR/>
                            <?php echo JText::_('OS_CANCEL_BOOKING_URL');
                            $cancellink = JURI::root() . "index.php?option=com_osservicesbooking&task=default_cancelorder&id=" . $order->id . "&ref=" . md5($order->id); ?>
                            <a href="<?php echo $cancellink ?>" id="cancel_link"><?php echo $cancellink ?></a>
							&nbsp;
							<a href="javascript:void(0);" title="<?php echo JText::_('OS_COPY_LINK');?>" id="copy_cancel_link">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
								  <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
								  <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
								</svg>
							</a>
                        <?php } ?>
						<script type="text/javascript">
						jQuery('#copy_order_details_link').click(function (e){
						   e.preventDefault();
						   var copyText = jQuery('#order_details_link').attr('href');
						   document.addEventListener('copy', function(e) {
							  e.clipboardData.setData('text/plain', copyText);
							  e.preventDefault();
						   }, true);

						   document.execCommand('copy');  
						   alert("<?php echo JText::_('OS_ORDER_DETAILS_LINK_HAS_JUST_BEEN_COPIED_TO_YOUR_CLIPBOARD');?>");
						});
						jQuery('#copy_cancel_link').click(function (e){
						   e.preventDefault();
						   var copyText = jQuery('#cancel_link').attr('href');
						   document.addEventListener('copy', function(e) {
							  e.clipboardData.setData('text/plain', copyText);
							  e.preventDefault();
						   }, true);

						   document.execCommand('copy');  
						   alert("<?php echo JText::_('OS_ORDER_CANCELLATION_LINK_HAS_JUST_BEEN_COPIED_TO_YOUR_CLIPBOARD');?>");
						});
						</script>
                    </td>
                </tr>
            <?php
            }
            ?>
			<tr>
				<td width="100%" colspan="2">
					<table  width="100%" id="orderdetailstable">
						<?php
                        if($configClass['use_qrcode'] == 1){
                            ?>
                            <tr>
                                <td width="30%" class="infor_left_col">
                                    <?php echo JText::_('OS_QRCODE')?>
                                </td>
                                <td class="infor_right_col">
                                    <?php
                                    if(!file_exists(JPATH_ROOT.'/media/com_osservicesbooking/qrcodes/'.$order->id.'.png')){
                                        OSBHelper::generateQrcode($order->id);
                                    }
                                    ?>
                                    <img src="<?php echo JUri::root()?>media/com_osservicesbooking/qrcodes/<?php echo $order->id?>.png" />
                                </td>
                            </tr>
                        <?php
                        }
						if($configClass['disable_payments'] == 1){
						?>
							<tr>
								<td width="30%" class="infor_left_col">
									<?php echo JText::_('OS_PRICE')?>
								</td>
								<td class="infor_right_col">
									<?php
										echo OSBHelper::showMoney($order->order_total,1);
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
									echo OSBHelper::showMoney($order->order_tax,1);
									 ?>
								</td>
							</tr>
							<?php
							}
							if($order->coupon_id > 0){
							?>
								<tr>
									<td width="30%" class="infor_left_col">
										<?php echo JText::_('OS_DISCOUNT')?>
									</td>
									<td class="infor_right_col">
										<?php
											echo OSBHelper::showMoney($order->order_discount,1);
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
										echo OSBHelper::showMoney($order->order_final_cost,1);
									 ?>
								</td>
							</tr>
							<tr>
								<td width="30%" class="infor_left_col">
									<?php echo JText::_('OS_PAID_AMOUNT')?>
								</td>
								<td class="infor_right_col">
									<?php
										$paidAmount = 0;
										if($order->order_payment == "os_offline")
										{
										 	if($order->deposit_paid == 1)
											{
												$paidAmount = $order->order_upfront;
											}
										}
										elseif($order->deposit_paid == 1)
										{
											$paidAmount = $order->order_upfront;
										}

										if($order->make_remain_payment == 1)
										{
											$paidAmount += $order->remain_payment_amount;
										}
										echo OSBHelper::showMoney($paidAmount,1);
									 	if(OSBHelper::allowRemainPayment($order->id))
										{
											echo " - <a href='".JUri::root()."index.php?option=com_osservicesbooking&task=form_remainpayment&id=".$order->id."&Itemid=".$jinput->getInt('Itemid',0)."' title='".JText::_('OS_MAKE_REMAIN_PAYMENT')."'>".JText::_('OS_MAKE_REMAIN_PAYMENT')."</a>";
										}
									 ?>
								</td>
							</tr>
							<?php
							if($order->order_upfront > 0)
							{			 
							?>
							<tr>
								<td width="30%" class="infor_left_col">
									<?php echo JText::_('OS_PAYMENT')?>
								</td>
								<td class="infor_right_col">
									<?php
									echo JText::_(os_payments::loadPaymentMethod($order->order_payment)->title);
									?>

								</td>
							</tr>
							<?php
							}
						}
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_NAME')?>
							</td>
							<td class="infor_right_col">
								<a href="mailto:<?php echo $order->order_email;?>" target="_blank">
									<?php
									echo $order->order_name;
									?>
								</a>
							</td>
						</tr>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_EMAIL')?>
							</td>
							<td class="infor_right_col">
								<a href="mailto:<?php echo $order->order_email;?>" target="_blank">
									<?php
									echo $order->order_email;
									?>
								</a>
							</td>
						</tr>
						<?php

						if(($configClass['value_sch_include_phone']) and ($order->order_phone != "")){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_PHONE')?>
							</td>
							<td class="infor_right_col">
								<?php
								if($order->dial_code != ""){
									echo $order->dial_code."-";
								}
								echo $order->order_phone;
								?>
							</td>
						</tr>
						<?php
						}
						if(($configClass['value_sch_include_country']) and ($order->order_country != "")){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_COUNTRY')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $order->order_country;
								?>
							</td>
						</tr>
						<?php
						}
						if(($configClass['value_sch_include_address']) and ($order->order_address != "")){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_ADDRESS')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $order->order_address;
								?>
							</td>
						</tr>
						<?php
						}
						if(($configClass['value_sch_include_city']) and ($order->order_city != "")){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_CITY')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $order->order_city;
								?>
							</td>
						</tr>
						<?php
						}
						if(($configClass['value_sch_include_state']) and ($order->order_state != "")){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_STATE')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $order->order_state;
								?>
							</td>
						</tr>
						<?php
						}
						if(($configClass['value_sch_include_zip']) and ($order->order_zip != "")){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_ZIP')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $order->order_zip;
								?>
							</td>
						</tr>
						<?php
						}
						$db = JFactory::getDbo();
						$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1' order by ordering");
						$fields = $db->loadObjectList();
						if(count($fields) > 0){
							for($i=0;$i<count($fields);$i++){
								$field = $fields[$i];
								$db->setQuery("Select count(id) from #__app_sch_order_options where order_id = '$order->id' and field_id = '$field->id'");
								$count = $db->loadResult();
								if($field->field_type == 0){
									$db->setQuery("Select fvalue from #__app_sch_field_data where order_id = '$order->id' and fid = '$field->id'");
									$fvalue = $db->loadResult();
									if($fvalue != ""){
										?>
										<tr>
											<td width="30%" class="infor_left_col" valign="top" style="padding-top:5px;">
												<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);?>
											</td>
											<td class="infor_right_col">
												<?php
												echo $fvalue;
												?>
											</td>
										</tr>
										<?php
									}
								}elseif($field->field_type == 3){
									$db->setQuery("Select fvalue from #__app_sch_field_data where order_id = '$order->id' and fid = '$field->id'");
									$fvalue = $db->loadResult();
									if(($fvalue != "") && (file_exists(JPATH_ROOT.'/images/osservicesbooking/fields/'.$fvalue))){
										?>
										<tr>
											<td width="30%" class="infor_left_col" valign="top" style="padding-top:5px;">
												<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);?>
											</td>
											<td class="infor_right_col">
												<img src="<?php echo JUri::root()?>images/osservicesbooking/fields/<?php echo $fvalue;?>" width="120"/>
											</td>
										</tr>
										<?php
									}
								}elseif($field->field_type == 4){
									$db->setQuery("Select fvalue from #__app_sch_field_data where order_id = '$order->id' and fid = '$field->id'");
									$fvalue = $db->loadResult();
									if(($fvalue != "") && (file_exists(JPATH_ROOT.'/images/osservicesbooking/fields/'.$fvalue))){
										?>
										<tr>
											<td width="30%" class="infor_left_col" valign="top" style="padding-top:5px;">
												<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);?>
											</td>
											<td class="infor_right_col">
												<a href="<?php echo JUri::root()?>images/osservicesbooking/fields/<?php echo $fvalue;?>" target="_blank"><?php echo $fvalue;?></a>
											</td>
										</tr>
										<?php
									}
								}
								if($count > 0){
									if($field->field_type == 1){
										$db->setQuery("Select option_id from #__app_sch_order_options where order_id = '$order->id' and field_id = '$field->id'");
										$option_id = $db->loadResult();
										$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
										$optionvalue = $db->loadObject();
										?>
										<tr>
											<td width="30%" class="infor_left_col" valign="top" style="padding-top:5px;">
												<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);?>
											</td>
											<td class="infor_right_col">
												<?php
												$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang); //$optionvalue->field_option;
												if($optionvalue->additional_price > 0){
													$field_data.= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
												}
												echo $field_data;
												?>
											</td>
										</tr>
										<?php
									}elseif($field->field_type == 2){
										$db->setQuery("Select option_id from #__app_sch_order_options where order_id = '$order->id' and field_id = '$field->id'");
										$option_ids = $db->loadObjectList();
										$fieldArr = array();
										for($j=0;$j<count($option_ids);$j++){
											$oid = $option_ids[$j];
											$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
											$optionvalue = $db->loadObject();
											$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang);//$optionvalue->field_option;
											if($optionvalue->additional_price > 0){
												$field_data.= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
											}
											$fieldArr[] = $field_data;
										}
										?>
										<tr>
											<td width="30%" class="infor_left_col" valign="top" style="padding-top:5px;">
												<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);?>
											</td>
											<td class="infor_right_col">
												<?php
												echo implode(", ",$fieldArr);
												?>
											</td>
										</tr>
										<?php
									}
								}
							}
						}
                        if($order->order_notes != ""){
						?>
						<tr>
							<td width="30%" class="infor_left_col">
								<?php echo JText::_('OS_NOTES')?>
							</td>
							<td class="infor_right_col">
								<?php
								echo $order->order_notes;
								?>
							</td>
						</tr>
                        <?php } ?>
					</table>
				</td>
			</tr>
			<tr>
				<td width="100%" style="border:1px solid #CCC !important;padding:5px;" colspan="2">
					<input type="hidden" name="oid" id="oid" value="<?php echo $order->id;?>">
					<div id="order<?php echo $order->id;?>">
						<?php
						OsAppscheduleDefault::getListOrderServices($order->id,$checkin,'','');
						?>
					</div>
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
	 * List Services / Employees / Start time / End time / extra fields Orders
	 *
	 * @param unknown_type $rows
	 */
	static function listOrderServices($rows,$order,$checkin){
		global $mainframe,$mapClass,$configClass,$jinput;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$db = JFactory::getDbo();
		if(count($rows) > 0){
		?>
		<div id="listOrderServices">
		<table width="100%" class="table table-bordered orderdetailstable" id="listServicesTable">
			<thead>
				<tr class="success">
					<?php
					if($checkin == 0) 
					{
						if ($configClass['allow_remove_items'] == 1) 
						{ 
						?>
							<td width="5%" align="center">
								<?php echo JText::_('OS_REMOVE') ?>
							</td>
						<?php 
						}
					}
					else
					{
						?>
						<td width="5%" align="center">
							<?php echo JText::_('OS_CHECKIN') ?>
						</td>
						<?php
					}
					?>
					<?php
					if($configClass['use_qrcode'])
					{
						?>
						<td align="center" width="5%">
						</td>
						<?php
					}
					?>
					<td width="20%" align="left">
						<?php echo JText::_('OS_SERVICE_NAME')?>/<?php echo JText::_('OS_EMPLOYEE')?>
					</td>
					<td width="20%" align="left">
						<?php echo JText::_('OS_BOOKING_DATE')?>
					</td>
					<?php
					if(OSBHelper::showAdditionalPart($order->id))
					{
					?>
						<td width="30%" align="left" class="hidden-phone"> 
							<?php echo JText::_('OS_ADDITIONAL')?>
						</td>
					<?php
					}
					if($configClass['active_comment'] == 1)
					{
						?>
						<td width="12%" align="center" class="hidden-phone">
							<?php echo JText::_('OS_COMMENT')?>
						</td>
						<?php

					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$current_time = HelperOSappscheduleCommon::getRealTime();
				$cancel_before = $configClass['cancel_before'];
				for($i1=0;$i1<count($rows);$i1++)
				{
					$row = $rows[$i1];
					?>
					<tr>
						<?php
						if($checkin == 0) 
						{	
							if ($configClass['allow_remove_items'] == 1)
							{
							?>
								<td class="orderdetailstabletd" width="5%" align="center">
								<?php
								if ($current_time + $cancel_before*3600 < $row->start_time) 
								{ 
								?>
								
                                    <a href="javascript:removeOrderItem(<?php echo $row->order_id ?>,<?php echo $row->order_item_id ?>,'<?php echo JText::_('OS_DO_YOU_WANT_T0_REMOVE_ORDER_ITEM') ?>','<?php echo JURI::root() ?>','<?php echo $jinput->getInt('Itemid', 0); ?>');"
                                       title="<?php echo JText::_('OS_CLICK_HERE_TO_REMOVE_ITEM'); ?>"/>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
										  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
										</svg>
                                    </a>
								
								<?php
								}
								?>
								</td>
								<?php
							}
						}
						else
						{
							?>
							<td width="5%" align="center" class="orderdetailstabletd">
								<div id="order<?php echo $row->order_item_id;?>">
									<a href="javascript:changeCheckin(<?php echo $row->order_id ?>,<?php echo $row->order_item_id ?>,'<?php echo JURI::root() ?>');" title="<?php echo JText::_('OS_CLICK_HERE_TO_CHANGE_CHECK_IN_STATUS'); ?>"/>
										<?php
										if($row->checked_in == 1)
										{
										?>
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
											  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
											</svg>
										<?php 
										} 
										else
										{
											?>
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
											  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
											</svg>
											<?php
										}?>
									</a>
								</div>
							</td>
							<?php
						}
						if($configClass['use_qrcode'])
						{
							if(!file_exists(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes/item_'.$row->order_item_id.'.png'))
							{
								OSBHelper::generateQrcode($order->id);
							}
							?>
							<td width="10%" align="center" class="orderdetailstabletd">
								<img src="<?php echo JUri::root();?>media/com_osservicesbooking/qrcodes/item_<?php echo $row->order_item_id ?>.png" border="0"/>
							</td>
							<?php
						}
						?>
						<td width="15%" align="left" class="orderdetailstabletd">
							<strong><?php echo OSBHelper::getLanguageFieldValueOrder($row,'service_name',$order->order_lang);?></strong>
							/
							<?php
							echo $row->employee_name;
							?>
						</td>
						<td width="10%" align="left" class="orderdetailstabletd">
							<?php
							echo date($configClass['date_format'],$row->start_time);
							?>
							<?php
							echo date($configClass['time_format'],$row->start_time);
							?>
							-
							<?php
							echo date($configClass['time_format'],$row->end_time);
							?>
						</td>
						<?php
						if(OSBHelper::showAdditionalPart($order->id))
						{
						?>
							<td width="30%" align="left" class="hidden-phone orderdetailstabletd">
								<?php
								$db->setQuery("Select a.* from #__app_sch_venues as a inner join #__app_sch_employee_service as b on b.vid = a.id where b.employee_id = '$row->eid' and b.service_id = '$row->sid'");
								$venue = $db->loadObject();
								if($venue->address != ""){
									echo JText::_('OS_VENUE').": <B>".$venue->address."</B>";
								}
								if($row->service_time_type == 1){
									echo JText::_('OS_NUMBER_SLOT').": ".$row->nslots."<BR />";
								}
								$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' order by ordering");
								$fields = $db->loadObjectList();
								if(count($fields) > 0){
									for($i=0;$i<count($fields);$i++){
										$field = $fields[$i];
										//echo $field->id;
										$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$row->order_item_id' and field_id = '$field->id'");
										//echo $db->getQuery();
										$count = $db->loadResult();
										if($count > 0){
											if($field->field_type == 1){
												$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->order_item_id' and field_id = '$field->id'");
												//echo $db->getQuery();
												$option_id = $db->loadResult();
												$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
												$optionvalue = $db->loadObject();
												?>
												<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);?>:
												<?php
												$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang);
												if($optionvalue->additional_price > 0){
													$field_data.= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
												}
												echo $field_data;
												echo "<BR />";
											}elseif($field->field_type == 2){
												$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->order_item_id' and field_id = '$field->id'");
												$option_ids = $db->loadObjectList();
												$fieldArr = array();
												for($j=0;$j<count($option_ids);$j++){
													$oid = $option_ids[$j];
													$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
													//echo $db->getQuery();
													$optionvalue = $db->loadObject();
													$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang);
													if($optionvalue->additional_price > 0){
														$field_data.= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
													}
													$fieldArr[] = $field_data;
												}
												?>
												<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);?>:
												<?php
												echo implode(", ",$fieldArr);
												echo "<BR />";
											}
										}
									}
								}
								?>
							</td>
						<?php
						}
						if($configClass['active_comment'] == 1){
							?>
							<td width="12%" style="text-align:center;" class="hidden-phone orderdetailstabletd">
								<?php
								if(HelperOSappscheduleCommon::canPostReview($row->sid,$row->eid)) {
									?>
									<a href="javascript:void(0);" onclick="javascript:openCommentForm(<?php echo $row->sid;?>,<?php echo $row->eid; ?>);">
										<i class="icon-comment"></i>
									</a>
									<?php
								}else{
									if(HelperOSappscheduleCommon::alreadyPostComment($row->sid,$row->eid)){
										$rating = HelperOSappscheduleCommon::userrating($row->sid,$row->eid);
										if($rating > 0) {
											for($j=1;$j<=$rating;$j++) {
												?>
												<i class="icon-star" style="color:orange;margin:0px; width:10px;"></i>
												<?php
											}
										}
										for($j=$rating + 1;$j<=5;$j++){
											?>
											<i class="icon-star" style="color:gray;margin:0px; width:10px;"></i>
											<?php
										}
									}
								}
								?>
							</td>
							<?php
						}
						?>
					</tr>
					<?php
				}
				?>
			</tbody>
            <input type="hidden" name="order_item_id" id="order_item_id" value="" />
		</table>
		</div>
		<script type="text/javascript">
			function openCommentForm(sid,eid){
				var myWindow = window.open('<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=default_writecomment&sid=' + sid + '&eid=' + eid + '&tmpl=component','commentWindow','width=380,height=450,location=no,menubar=no,status=no,toolbar=no,left=400,top=100');
			}
		</script>
		<?php
		}else{
			?>
			<div style="padding:5px;">
				<?php  echo JText::_('OS_NO_ITEM');?>
			</div>
			<?php
		}
	}
	
	/**
	 * Show Google map for the venue
	 *
	 * @param unknown_type $venue
	 */
	public static function showMap($venue){
		global $mainframe,$mapClass,$configClass;
		if($configClass['map_type'] == 0)
		{
		?>
			<script src="<?php echo OSBHelper::returnGoogleMapScript();?>"></script>
			<script>
			  function initialize() {
				var mapOptions = {
				  zoom: 19,
				  center: new google.maps.LatLng(<?php echo $venue->lat_add?>, <?php echo $venue->long_add?>),
				  mapTypeControl: true,
				  mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				  },
				  zoomControl: true,
				  zoomControlOptions: {
					style: google.maps.ZoomControlStyle.SMALL
				  },
				  mapTypeId: google.maps.MapTypeId.SATELLITE
				}
				var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
				
				 marker = new google.maps.Marker({
				  map:map,
				  draggable:false,
				  animation: google.maps.Animation.DROP,
				  position: new google.maps.LatLng(<?php echo $venue->lat_add?>, <?php echo $venue->long_add?>)
				});
			  }
			</script>
			<body onload="initialize()">
				<div id="map-canvas" style="width:100%;height:370px;"></div>
			</body>
			<?php
		}
		else
		{
			HTMLHelper::_('jquery.framework');
			$rootUri = JUri::root(true);
			$document = JFactory::getDocument()
				->addScript($rootUri . '/media/com_osservicesbooking/assets/js/leaflet/leaflet.js')
				->addStyleSheet($rootUri . '/media/com_osservicesbooking/assets/js/leaflet/leaflet.css');
			$zoomLevel   = 16;
			$coordinates = $venue->lat_add . ',' . $venue->long_add;
			$onPopup = false;
			?>
			<div id="googlemapdiv" style="height:300px;width:100%; position:relative;"></div>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					var mymap = L.map('googlemapdiv').setView([<?php echo $venue->lat_add; ?>, <?php echo $venue->long_add; ?>], <?php echo $zoomLevel;?>);
					L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
						attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
						maxZoom: 18,
						id: 'mapbox.streets',
						zoom: <?php echo $zoomLevel;?>,
					}).addTo(mymap);

					
					var propertyIcon = L.icon({iconUrl: '<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/1.png',
														iconSize:     [33, 44] // size of the icon
					});
					var marker = L.marker([<?php echo $venue->lat_add ?>, <?php echo $venue->long_add;?>],{icon: propertyIcon}, {draggable: false}).addTo(mymap);
					mymap.scrollWheelZoom.disable()
				});
			</script>
			<?php
		}
	}

    /**
     * @param $employees
     * @param $params
     * @param $list_type
     */
	static function listEmployees($employees,$params,$list_type,$introtext){
		global $mainframe,$mapClass,$jinput;
		if(version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			JHTML::_('behavior.modal','osmodal');
		}
		else
		{
			OSBHelperJquery::colorbox('osmodal');
		}
		jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/employees.php'))
		{
            $tpl = new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }
		else
		{
            $tpl = new OSappscheduleTemplate(JPATH_COMPONENT.'/components/com_osservicesbooking/layouts/');
        }
        $tpl->set('mainframe',$mainframe);
        $tpl->set('employees',$employees);
        $tpl->set('params',$params);
        $tpl->set('list_type',$list_type);
        $tpl->set('mapClass',$mapClass);
        $tpl->set('jinput',$jinput);
		$tpl->set('introtext',$introtext);
        $body = $tpl->fetch("employees.php");
        echo $body;
	}

	static function waitingListForm($data){
		global $mainframe,$mapClass,$configClass;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);	
		$user = JFactory::getUser();

		$db = JFactory::getDbo();

		$db->setQuery("Select service_name from #__app_sch_services where id = '".$data['sid']."'");
		$service_name = $db->loadResult();
		$date = date($configClass['date_format'],$data['start'])." ".JText::_('OS_START').": ".date($configClass['time_format'],$data['start'])." - ".JText::_('OS_END').": ".date($configClass['time_format'],$data['end']);
		?>
		<div class="<?php echo $mapClass['row-fluid'];?> waitinglistform">
			<div class="<?php echo $mapClass['span12'];?>">
				<div class="page-header">
                    <h1>
                        <?php echo JText::_('OS_ADD_ME_IN_WAITING_LIST');?>
                    </h1>
                </div>
				<div class="clearfix"></div>
				<div class="img-rounded img-polaroid warning">
					<?php echo sprintf(JText::_('OS_ADD_WAITING_LIST_DESCRIBE'),$service_name, $date);?>
				</div>
				<div class="clearfix" style="height:25px;"></div>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?>">
						<form id="waitingform" method="POST" action="<?php echo JUri::root();?>index.php?option=com_osservicesbooking&task=default_doaddtowaitinglist&tmpl=component">
						<div class="control-group">
							<label class="control-label" ><?php echo JText::_('OS_EMAIL')?></label>
							<div class="controls">
								<input type="text" name="email" id="email" value="<?php echo $user->email?>" size="20" class="input-large" placeholder="<?php echo JText::_('OS_EMAIL')?>" />
							</div>
						</div>
						<div class="control-group">
							<input type="button" class="btn btn-inverse" value="<?php echo JText::_('OS_SUBMIT');?>" id="submitwatingform" />
							<input type="reset"  class="btn" value="<?php echo JText::_('OS_RESET');?>" />
						</div>
						<input type="hidden" name="option"	value="com_osservicesbooking" />
						<input type="hidden" name="task"	value="default_doaddtowaitinglist" />
						<input type="hidden" name="sid"		value="<?php echo $data['sid']; ?>" />
						<input type="hidden" name="eid"		value="<?php echo $data['eid']; ?>" />
						<input type="hidden" name="start"	value="<?php echo $data['start']; ?>" />
						<input type="hidden" name="end"		value="<?php echo $data['end']; ?>" />
						<input type="hidden" name="tmpl"	value="component" />
						</form>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		jQuery( document ).ready( function() {
			jQuery( "#submitwatingform" ).click( function() {
				var email = jQuery("#email").val();
				if(email == ""){
					alert("<?php echo JText::_('OS_PLEASE_ENTER_EMAIL_ADDRESS');?>");
					jQuery( "#email" ).focus();
					return false;
				}else{
					jQuery( "#waitingform" ).submit();
				}
			});
		});
		</script>
		<?php
	}

	static function waitinglistResult($msg){
		?>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?> page-header">
				<h1>
					<?php echo $msg;?>
				</h1>
			</div>
		</div>
		<?php
	}
}
?>