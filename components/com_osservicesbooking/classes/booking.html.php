<?php
/*------------------------------------------------------------------------
# booking.html.php: Ossolution Services Booking
# ------------------------------------------------------------------------
# author:           Ossolution team
# copyright:        Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license:         https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites:         https://www.joomdonation.com
# Technical Support https://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class HTML_OsAppscheduleForm{
	/**
	 * Confirm information form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $total
	 * @param unknown_type $fieldObj
	 * @param unknown_type $lists
	 */
	static function confirmInforFormHTML($total,$fieldObj,$lists,$coupon)
    {
		global $mainframe,$mapClass,$configClass,$deviceType,$jinput;
		//jimport('joomla.html.pane');
		$pane           =& JPane::getInstance('tabs');
		$methods        = os_payments::getPaymentMethods(true, false) ;
		OSBHelper::showProgressBar('form_step2',0);
		?>
		<div id="msgDiv" style="width:100%;">
		</div>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<?php 
			$show_calendar = 0;
			if($configClass['show_calendar_box'] == 1)
			{ //show all page
				if(!OSBHelper::isTheSameDate($lists['date_from'],$lists['date_to']))
				{
					$show_calendar = 1;
				}
			}

			if($configClass['using_cart'] == 1 || $show_calendar == 1 || count($lists['selected_dates']) > 0)
			{
				$secondDiv = $mapClass['span8'];
				if($configClass['calendar_position'] == 0)
				{
					?>
					<div class="<?php echo $mapClass['span4'];?>" id="calendardivleft">
						<?php
						if(count($lists['selected_dates']) > 0 && $configClass['show_calendar_box'] == 1)
						{
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
						elseif((!OSBHelper::isTheSameDate($lists['date_from'],$lists['date_to'])) && ($configClass['show_calendar_box'] == 1))
						{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?> norightleftmargin">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php
								HelperOSappscheduleCalendar::initCalendarForSeveralYear(intval(date("Y",HelperOSappscheduleCommon::getRealTime())),$lists['category'],$lists['employee_id'],$lists['vid'], $lists['sid'],$lists['date_from'],$lists['date_to']);
								?>
								<input type="hidden" name="ossmh" id="ossmh" value="<?php echo intval(date("m",$lists['current_time']))?>" />
								<input type="hidden" name="ossyh" id="ossyh" value="<?php echo intval(date("Y",$lists['current_time']))?>" />
							</div>
						</div>
						<div class="clearfix" style="height:10px;"></div>
						<?php }
						if(($configClass['using_cart'] == 1) && ($deviceType != "mobile") && ($deviceType != "tablet"))
						{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv confirmationform">
									<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
										<?php
										if($configClass['disable_payments'] == 1)
										{
										?>
											<div style="float:left;margin-right:5px;">
												<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png" />
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
													OsAppscheduleAjax::cart($userdata,$lists['vid'],$lists['category'],$lists['employee_id'],$lists['date_from'],$lists['date_to']);
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
						<?php } ?>
						<div class="clearfix"></div>
					</div>
				<?php
				}
			}
			else
			{
				$secondDiv = "span12";
			}
			?>
			<div class="<?php echo $secondDiv;?>" id="maindivright">
				<div id="maincontentdiv">
					<?php
					HTML_OsAppscheduleForm::showConfirmFormHTML($total,$fieldObj,$lists,$coupon);
					?>
				</div>
				<div  style="display:none;">
					<?php
					echo JHTML::_('calendar','', 'calendarvl', 'calendarvl', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'19',  'maxlength'=>'19','style'=>'width:80px;'));
					?>
				</div>
			</div>
			<?php
			if($configClass['using_cart'] == 1 || $show_calendar == 1 || count($lists['selected_dates']) > 0)
			{
				$secondDiv = $mapClass['span8'];
				if($configClass['calendar_position'] == 1)
				{
					?>
					<div class="<?php echo $mapClass['span4'];?>" id="calendardivleft">
						<?php
						if(count($lists['selected_dates']) > 0 && $configClass['show_calendar_box'] == 1)
						{
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
						elseif((!OSBHelper::isTheSameDate($lists['date_from'],$lists['date_to'])) && ($configClass['show_calendar_box'] == 1))
						{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?> norightleftmargin">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php
								HelperOSappscheduleCalendar::initCalendarForSeveralYear(intval(date("Y",HelperOSappscheduleCommon::getRealTime())),$lists['category'],$lists['employee_id'],$lists['vid'], $lists['sid'],$lists['date_from'],$lists['date_to']);
								?>
								<input type="hidden" name="ossmh" id="ossmh" value="<?php echo intval(date("m",$lists['current_time']))?>" />
								<input type="hidden" name="ossyh" id="ossyh" value="<?php echo intval(date("Y",$lists['current_time']))?>" />
							</div>
						</div>
						<div class="clearfix" style="height:10px;"></div>
						<?php }
						if(($configClass['using_cart'] == 1) && ($deviceType != "mobile") && ($deviceType != "tablet"))
						{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv confirmationform">
									<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
										<?php
										if($configClass['disable_payments'] == 1)
										{
										?>
											<div style="float:left;margin-right:5px;">
												<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png" />
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
													OsAppscheduleAjax::cart($userdata,$lists['vid'],$lists['category'],$lists['employee_id'],$lists['date_from'],$lists['date_to']);
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
						<?php } ?>
						<div class="clearfix"></div>
					</div>
				<?php
				}
			}			
			?>
		</div>
		<div class="clearfix"></div>
		<?php
		if(($configClass['using_cart'] == 1) && (($deviceType == "mobile") || ($deviceType == "tablet")))
		{
		?>
		<div class="clearfix" style="height:10px;"></div>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv confirmationform">
					<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
						<?php
						if($configClass['disable_payments'] == 1){
						?>
                            <div style="float:left;margin-right:5px;">
                                <img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png" />
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
					<table width="100%">
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
		<?php }
		if($configClass['show_footer'] == 1){
			if($configClass['footer_content'] != ""){
				?>
				<div class="osbfootercontent">
					<?php echo $configClass['footer_content'];?>
				</div>
				<?php
			}
		}
		?>
		<input type="hidden" name="option" value="com_osservicesbooking" /> 
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="month"  id="month" value="<?php echo intval(date("m",$lists['current_time']))?>" />
		<input type="hidden" name="year"  id="year" value="<?php echo date("Y",$lists['current_time'])?>" />
		<input type="hidden" name="day"  id="day" value="<?php echo intval(date("d",$lists['current_time']));?>" />
		<input type="hidden" name="select_day" id="select_day" value="<?php echo $day;?>" />
		<input type="hidden" name="select_month" id="select_month" value="<?php echo $month;?>" />
		<input type="hidden" name="select_year" id="select_year" value="<?php echo $year;?>" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JURI::root()?>"  />
		<input type="hidden" name="order_id" id="order_id" value="" />
		<input type="hidden" name="current_date" id="current_date" value=""  />
		<input type="hidden" name="use_captcha" id="use_captcha" value="<?php echo $configClass['value_sch_include_captcha'];?>" />
		<input type="hidden" name="category_id" id="category_id" value="<?php echo $jinput->getInt('category_id',0)?>" />
		<input type="hidden" name="employee_id" id="employee_id" value="<?php echo $jinput->getInt('employee_id',0)?>" />
		<input type="hidden" name="vid" id="vid" value="<?php echo $jinput->getInt('vid',0)?>" />
		<input type="hidden" name="selected_item" id="selected_item" value="" />
		<input type="hidden" name="sid" id="sid" value="<?php echo $jinput->getInt('sid',0);?>" />
		<input type="hidden" name="eid" id="eid" value="" />
		<input type="hidden" name="coupon_id" id="coupon_id" value="" />
		<input type="hidden" name="current_link" id="current_link" value="<?php echo $configClass['current_link']?>" />
		<input type="hidden" name="calendar_normal_style" id="calendar_normal_style" value="<?php echo $configClass['calendar_normal_style'];?>" />
		<input type="hidden" name="calendar_currentdate_style" id="calendar_currentdate_style" value="<?php echo $configClass['calendar_currentdate_style'];?>" />
		<input type="hidden" name="calendar_activate_style" id="calendar_activate_style" value="<?php echo $configClass['calendar_activate_style'];?>" />
		<input type="hidden" name="booked_timeslot_background" id="booked_timeslot_background" value="<?php echo ($configClass['booked_timeslot_background'] != '') ? $configClass['booked_timeslot_background']:'red';?>" />
		<input type="hidden" name="use_js_popup" id="use_js_popup" value="<?php echo $configClass['use_js_popup'];?>" />
		<input type="hidden" name="using_cart" id="using_cart" value="<?php echo $configClass['using_cart'];?>" />
		<input type="hidden" name="date_from" id="date_from" value="<?php echo $lists['date_from'];?>" />
		<input type="hidden" name="date_to" id="date_to" value="<?php echo $lists['date_to'];?>" />
		<input type="hidden" name="temp_item" id="temp_item" value="" />
		<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
		<input type="hidden" name="count_services" id="count_services" value="" />
		<input type="hidden" name="services" id="services" value="" />
		
		<div  id="divtemp" style="width:1px;height:1px;"></div>
		<script language="javascript">
		<?php
			os_payments::writeJavascriptObjects();
		?>
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
		}else{
			jQuery(".buttonpadding5").removeClass("buttonpadding5").addClass("buttonpadding10");
			if(document.getElementById('calendardivleft') != null){
				var leftwidth = jQuery("#calendardivleft").width();
				if(leftwidth > 250){
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span5']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span4']?>");
					jQuery("#maindivright").removeClass("<?php echo $mapClass['span7']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span8']?>");
				}else if(leftwidth < 210){
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span5']?>").removeClass("<?php echo $mapClass['span4']?>").addClass("<?php echo $mapClass['span6']?>");
					jQuery("#maindivright").removeClass("<?php echo $mapClass['span7']?>").removeClass("<?php echo $mapClass['span8']?>").addClass("<?php echo $mapClass['span6']?>");
				}else{
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span4']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("span5");
					jQuery("#maindivright").removeClass("<?php echo $mapClass['span8']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span7']?>");
				}
			}
		}

		function changingEmployee(sid){
            var select_item = jQuery("#employeeslist_" + sid).val();
			//jQuery("#employee_id").val(select_item);
            var existing_services = jQuery("#employeeslist_ids" + sid).val();
            existing_services = existing_services.split("|");
            if(existing_services.length > 0){
                for(i=0;i<existing_services.length;i++){
                    //jQuery("#pane" + sid + '_' +  existing_services[i]).removeClass("active");
					jQuery("#pane" + sid + '_' +  existing_services[i]).css('display','none');
                }
            }
            //jQuery("#pane" + sid + '_'  +  select_item).addClass("active");
			jQuery("#pane" + sid + '_'  +  select_item).css('display','block');
        }

        function changingService(){
            var select_item = jQuery("#serviceslist").val();
			jQuery("#sid").val(select_item);
            var existing_services = jQuery("#serviceslist_ids").val();
            existing_services = existing_services.split("|");
            if(existing_services.length > 0){
                for(i=0;i<existing_services.length;i++){
                    //jQuery("#pane" + existing_services[i]).removeClass("active");
					
					jQuery("#pane" + existing_services[i]).css('display','none');
                }
            }
            //jQuery("#pane" + select_item).addClass("active");
			jQuery("#pane" + select_item).css('display','block');
        }
		</script>
		<?php
	}

	static function showConfirmFormHTML($total,$fieldObj,$lists,$coupon)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$user = JFactory::getUser();

		$year = intval(date("Y",HelperOSappscheduleCommon::getRealTime()));
		$month = intval(date("m",HelperOSappscheduleCommon::getRealTime()));
		$day = intval(date("d",HelperOSappscheduleCommon::getRealTime()));
		$date_from = $lists['date_from'];
		if($date_from != "")
		{
			$date_from_array = explode(" ",$date_from);
			$date_from_int = strtotime($date_from_array[0]);
			if($date_from_int > HelperOSappscheduleCommon::getRealTime())
			{
				$year = date("Y",$date_from_int);
				$month = intval(date("m",$date_from_int));
				$day = intval(date("d",$date_from_int));
			}
		}

		$methods = $lists['methods'];
		?>
		<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
			<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
				<?php echo JText::_('OS_BOOKING_FORM')?>
			</div>
			<?php
			if($configClass['use_ssl'] == 1){
			?>
			<form method="POST" action="<?php echo $configClass['root_link'].'index.php?option=com_osservicesbooking&Itemid='.$jinput->getInt('Itemid',0);?>" name="appform" id="bookingForm" class="padding10">
			<?php
			}else{
			?>
			<form method="POST" class="padding10" action="<?php echo JURI::root().'index.php?option=com_osservicesbooking&Itemid='.$jinput->getInt('Itemid',0);?>" name="appform" id="bookingForm">
			<?php
			}
			?>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="clearfix"></div>
					<?php
					if($configClass['disable_payments'] == 1)
					{
						if($total > 0){
						?>
						<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
							<?php echo JText::_('OS_AMOUNT')?>
						</div>
						<div class="<?php echo $mapClass['span8'];?> confirmelements">
							<?php
								echo OSBHelper::showMoney($total,1);
							 ?>
						</div>
						<div class="clearfix"></div>
						<?php
						$orderGroupDiscount = OSBHelper::getOrderGroupDiscount();
						$total_withgroupdiscount = $total - $orderGroupDiscount;
						$discount_amount = 0;
						if(($coupon->id > 0) or ($orderGroupDiscount > 0)){
						?>
						<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
							<?php echo JText::_('OS_DISCOUNT')?>
						</div>
						<div class="<?php echo $mapClass['span8'];?> confirmelements">
							<?php
								if($coupon->discount_type == 0){
									$discount_amount = $total_withgroupdiscount*$coupon->discount/100;
								}else{
									$discount_amount = $coupon->discount;
									if($discount_amount > $total_withgroupdiscount){
										$discount_amount = $total_withgroupdiscount;
									}
								}
								$discount_amount += $orderGroupDiscount;
								echo OSBHelper::showMoney($discount_amount,1);
							 ?>
						</div>
						<div class="clearfix"></div>
						<?php
						}
						$total = $total - $discount_amount;
						?>
						<?php
						$tax = 0;
						if($configClass['enable_tax']==1)
						{
							
						?>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_TAX')?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">
								<?php
								$tax = $total*intval($configClass['tax_payment'])/100;
								?>
								<?php
									echo OSBHelper::showMoney($tax,1);
								?> 
								<span class="tax_explain">(<?php echo $configClass['tax_payment']." %"?> <?php echo JText::_('OS_OF_TOTAL');?>)</span>
							</div>
							<div class="clearfix"></div>
						<?php
						}
						$final = $total + $tax;
						?>
						<div class="<?php echo $mapClass['span3'];?> confirmelements boldtext">
							<?php echo JText::_('OS_TOTAL')?>
						</div>
						<div class="<?php echo $mapClass['span8'];?> confirmelements">
							<?php
								echo OSBHelper::showMoney($final,1);
							 ?>
						</div>
						<div class="clearfix"></div>
						<div class="<?php echo $mapClass['span3'];?> confirmelements boldtext" id="deposit_label">
							<?php echo JText::_('OS_DEPOSIT')?>
						</div>
						<div class="<?php echo $mapClass['span8'];?> confirmelements" id="deposit_value">
							<?php
							//$deposit_payment = $configClass['deposit_payment'];
							//$deposit_payment = $deposit_payment*$final/100;
							$deposit_payment = OSBHelper::getDepositAmount($final);
							//
							if($lists['select_payment'] == 'os_prepaid' && $user->id > 0 && $deposit_payment > 0)
							{
								$db = JFactory::getDbo();
								$user_balances = 0;
								$db->setQuery("Select count(id) from #__app_sch_user_balance where user_id = '$user->id'");
								$count = $db->loadResult();
								if($count > 0)
								{
									$db->setQuery("Select * from #__app_sch_user_balance where user_id = '$user->id'");
									$balance = $db->loadObject();
									$user_balances = $balance->amount;
								}
								if($user_balances < $deposit_payment)
								{
									$mainframe->enqueueMessage(Jtext::_('OS_YOU_DONT_HAVE_ENOUGH_FUND_TO_COMPLETE_THE_ORDER'));
									$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&task=form_step1&Itemid='.$jinput->getInt('Itemid',0)));
								}
							}
							?>
							<?php
							echo OSBHelper::showMoney($deposit_payment,1);
							if($configClass['allow_full_payment'] == 1 && $configClass['deposit_payment'] < 100)
							{
							 ?>
								<BR />
								&nbsp;&nbsp;&nbsp;
								<a href="javascript:updateDeposit('<?php echo OSBHelper::showMoney($final,1);?>','<?php echo JText::_('OS_PAYAMOUNT');?>')" title="<?php echo JText::_('OS_PAY_FULL_DESC');?>">
									<?php echo JText::_('OS_OR_PAY_FULL')." ".OSBHelper::showMoney($final,1);?> 
								</a>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<?php
						}
					}
					?>
					<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
						<?php echo JText::_('OS_NAME')?>
					</div>
					<div class="<?php echo $mapClass['span8'];?> confirmelements">
						<?php
						echo $jinput->get('order_name','','string');
						?>
					</div>
					<div class="clearfix"></div>
					<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
						<?php echo JText::_('OS_EMAIL')?>
					</div>
					<div class="<?php echo $mapClass['span8'];?> confirmelements">
						<?php
						echo $jinput->get('order_email','','string');
						?>
					</div>
					<div class="clearfix"></div>
					<?php
					
					if($configClass['value_sch_include_phone'])
					{
						if($jinput->get('order_phone','','string')!= "")
						{
						?>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_PHONE')?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">
								<?php
								$dial_code = $jinput->getInt('dial_code','','0');
								if($dial_code > 0)
								{
									echo "(".OSBHelper::getDialCode($dial_code).") ";
								}
								echo $jinput->get('order_phone','','string');
								?>
							</div>
							<div class="clearfix"></div>
						<?php
						}
					}
					if($configClass['value_sch_include_country'])
					{
						if($jinput->get('order_country','','string')!= "")
						{
							?>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_COUNTRY')?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">
								<?php
								echo $jinput->get('order_country','','string');
								?>
							</div>
							<div class="clearfix"></div>
							<?php
						}
					}
					if($configClass['value_sch_include_address'])
					{
						if($jinput->get('order_address','','string')!= "")
						{
						?>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_ADDRESS')?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">
								<?php
								echo $jinput->get('order_address','','string');
								?>
							</div>
							<div class="clearfix"></div>
						<?php
						}
					}
					if($configClass['value_sch_include_city'])
					{
						if($jinput->get('order_city','','string')!= "")
						{
						?>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_CITY')?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">
								<?php
								echo $jinput->get('order_city','','string');
								?>
							</div>
							<div class="clearfix"></div>
						<?php
						}
					}
					if($configClass['value_sch_include_state']){
						if($jinput->get('order_state','','string')!= ""){
					?>
						<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
							<?php echo JText::_('OS_STATE')?>
						</div>
						<div class="<?php echo $mapClass['span8'];?> confirmelements">
							<?php
							echo $jinput->get('order_state','','string');
							?>
						</div>
						<div class="clearfix"></div>
					<?php
						}
					}
					if($configClass['value_sch_include_zip']){
						if($jinput->get('order_zip','','string')!= ""){
					?>
						<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
							<?php echo JText::_('OS_ZIP')?>
						</div>
						<div class="<?php echo $mapClass['span8'];?> confirmelements">
							<?php
							echo $jinput->get('order_zip','','string');
							?>
						</div>
						<div class="clearfix"></div>
					<?php
						}
					}
					
					if(count($fieldObj) > 0)
					{
						for($i=0;$i<count($fieldObj);$i++)
						{
							$f = $fieldObj[$i];
							if($f->fvalue != "")
							{
							?>
								<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
									<?php echo OSBHelper::getLanguageFieldValue($f->field,'field_label');?>
								</div>
								<div class="<?php echo $mapClass['span8'];?> confirmelements">
									<?php
									echo $f->fvalue;
									?>
								</div>
								<div class="clearfix"></div>
							<?php
							}
						}
					}
					if($configClass['value_sch_include_notes'] == 1)
					{
						$note = $jinput->get('notes','','string');
						if($note != "")
						{
							$note = str_replace("(@)","&",$note);
							?>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_NOTES');?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">
									<?php
									echo nl2br($note);
									?>
							</div>
							<div class="clearfix"></div>
							<?php
						}
					}
					if($configClass['disable_payments'] == 1 && $deposit_payment > 0)
					{
						$method = $lists['method'];
						?>
						<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
							<?php echo JText::_('OS_SELECT_PAYMENT')?>
						</div>
						<div class="<?php echo $mapClass['span8'];?> confirmelements">
							<?php echo  JText::_(os_payments::loadPaymentMethod($lists['select_payment'])->title); ?>
						</div>
						<div class="clearfix"></div>
						<?php
					}
					$method = $lists['method'] ;
					if($lists['select_payment'] != "" && $lists['select_payment'] != 'os_squareup' && $deposit_payment > 0)
					{
						if ($method->getCreditCard()) {
						?>	
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo  JText::_('OS_AUTH_CARD_NUMBER'); ?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">
								<?php
									$len = strlen($lists['x_card_num']) ;
									$remaining =  substr($lists['x_card_num'], $len - 4 , 4) ;
									echo str_pad($remaining, $len, '*', STR_PAD_LEFT) ;
								?>												
							</div>
							<div class="clearfix"></div>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_AUTH_CARD_EXPIRY_DATE'); ?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">						
								<?php echo $lists['exp_month'] .'/'.$lists['exp_year'] ; ?>
							</div>
							<div class="clearfix"></div>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_AUTH_CVV_CODE'); ?>
							</div>
							<div class="<?php echo $mapClass['span8'];?> confirmelements">
								<?php echo $lists['x_card_code'] ; ?>
							</div>
							<div class="clearfix"></div>
							<?php
								if ($method->getCardType()){
								?>
									<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
										<?php echo JText::_('OS_CARD_TYPE'); ?>
									</div>
									<div class="<?php echo $mapClass['span8'];?> confirmelements">
										<?php echo $lists['card_type'] ; ?>
									</div>
								<div class="clearfix"></div>
								<?php	
								}
							?>
						<?php				
						}						
						if ($method->getCardHolderName()) {
						?>
							<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
								<?php echo JText::_('OS_CARD_HOLDER_NAME'); ?>
							</div>
								<div class="<?php echo $mapClass['span8'];?> confirmelements">
								<?php echo $lists['card_holder_name'];?>
							</div>
							<div class="clearfix"></div>
						<?php												
						}
					}
					if($configClass['value_sch_reminder_enable'] == 1 && $configClass['enable_reminder'] == 1)
					{
						?>
						<div class="<?php echo $mapClass['span3'];?> boldtext confirmelements">
							<?php echo JText::_('OS_RECEIVE_REMINDER'); ?>
						</div>
						<div class="<?php echo $mapClass['span8'];?> confirmelements">
							<?php
							if($lists['receive_reminder'] == 1)
							{
								echo JText::_('JYES');
							}
							else
							{
								echo JText::_('JNO');
							}
							?>	
						</div>
						<div class="clearfix"></div>
						<?php
					}
					if(OsAppscheduleAjax::isAnyItemsInCart()){
					?>
					<div class="<?php echo $mapClass['span12'];?>">
						<input type="button" id="confirmSubmit" class="btn btn-success" value="<?php echo JText::_('OS_CONFIRM')?>" />
						<?php
						if($configClass['show_calendar_box'] == 1){
							$back_link = JRoute::_("index.php?option=com_osservicesbooking&task=default_layout&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$jinput->getInt('date_from','')."&date_to=".$jinput->getInt('date_to',''));
							?>
								<a href="<?php echo $back_link;?>" class="btn btn-warning">
									<?php echo JText::_('OS_CLOSE');?>
								</a>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			<!-- hidden tags -->
			<input type="hidden" name="order_name" 			id="order_name" 		value="<?php echo $jinput->get('order_name','','string')?>"   />
			<input type="hidden" name="order_email" 		id="order_email" 		value="<?php echo $jinput->get('order_email','','string')?>" />
			<input type="hidden" name="dial_code" 			id="dial_code" 			value="<?php echo $jinput->getInt('dial_code','','0')?>" />
			<input type="hidden" name="order_phone" 		id="order_phone" 		value="<?php echo $jinput->get('order_phone','','string')?>" />
			<input type="hidden" name="order_country" 		id="order_country" 		value="<?php echo $jinput->get('order_country','','string')?>" />
			<input type="hidden" name="order_address" 		id="order_address" 		value="<?php echo $jinput->get('order_address','','string')?>" />
			<input type="hidden" name="order_state" 		id="order_state" 		value="<?php echo $jinput->get('order_state','','string')?>" />
			<input type="hidden" name="order_city" 			id="order_city" 		value="<?php echo $jinput->get('order_city','','string')?>"  />
			<input type="hidden" name="order_zip" 			id="order_zip" 			value="<?php echo $jinput->get('order_zip','','string')?>" />
			<input type="hidden" name="select_payment" 		id="select_payment" 	value="<?php echo $jinput->get('payment_method','','string')?>" />
			<input type="hidden" name="stripeToken" 		id="stripeToken" 		value="<?php echo $jinput->get('stripeToken','','string')?>" />
			<input type="hidden" name="x_card_num" 			id="x_card_num" 		value="<?php echo $lists['x_card_num']?>" />
			<input type="hidden" name="x_card_code" 		id="x_card_code" 		value="<?php echo $lists['x_card_code']?>"  />
			<input type="hidden" name="card_holder_name" 	id="card_holder_name" 	value="<?php echo $lists['card_holder_name']?>" />
			<input type="hidden" name="exp_year" 			id="exp_year" 			value="<?php echo $lists['exp_year']?>" />
			<input type="hidden" name="exp_month" 			id="exp_month" 			value="<?php echo $lists['exp_month']?>" />
			<input type="hidden" name="card_type" 			id="card_type" 			value="<?php echo $lists['card_type']?>" />
			<input type="hidden" name="bank_id" 			id="bank_id" 			value="<?php echo $jinput->get('bank_id');?>" />
			<input type="hidden" name="coupon_id"			id="coupon_id" 			value="<?php echo $coupon->id?>" />
			<input type="hidden" name="unique_cookie"		id="unique_cookie" 		value="<?php echo OSBHelper::getUniqueCookie();?>" />
			<input type="hidden" name="use_js_popup" 		id="use_js_popup" 		value="<?php echo $configClass['use_js_popup'];?>" />
			<input type="hidden" name="using_cart" 			id="using_cart" 		value="<?php echo $configClass['using_cart'];?>" />
			<input type="hidden" name="date_from" 			id="date_from" 			value="<?php echo $date_from?>" />
			<input type="hidden" name="date_to" 			id="date_to" 			value="<?php echo $date_to?>" />
			<input type="hidden" name="receive_reminder" 	id="receive_reminder"	value="<?php echo $lists['receive_reminder'];?>" />
			<input type="hidden" name="temp_item"			id="temp_item" value="" />
			<input type="hidden" name="Itemid" 				id="Itemid" 			value="<?php echo $jinput->getInt('Itemid',0);?>" />
			<input type="hidden" name="nonce" 				id="card-nonce" 	    value="<?php echo $jinput->getString('nonce','');?>" />
			<input type="hidden" name="TransactionToken" 	id="TransactionToken" 	value="<?php echo $jinput->getString('TransactionToken','');?>" />
			<input type="hidden" name="user_id"				id="user_id"			value="<?php echo $jinput->getInt('user_id', 0); ?>" />
			<div style="display:none;">
				<textarea name="notes" id="notes" cols="40" rows="4" class="inputbox"><?php echo $note?></textarea>
			</div>
			<?php
			if(count($fieldObj) > 0){
				for($i=0;$i<count($fieldObj);$i++){
					$f = $fieldObj[$i];
					?>
					<input type="hidden" name="field_<?php echo $f->field->id?>" id="field_<?php echo $f->field->id?>" value="<?php echo $f->fieldoptions;?>" />
					<?php
				}
			}
			?>
			<input type="hidden" name="option"										value="com_osservicesbooking" />
			<input type="hidden" name="task"										value="default_completeorder" />
			<input type="hidden" name="payfull"				id="payfull"			value="0"/>
			</form>
		</div>
		<script type="text/javascript">
		jQuery("#confirmSubmit").click( function()
        {
			jQuery("#confirmSubmit").attr('disabled','disabled');
            jQuery("#confirmSubmit").attr('disabled',true);
			document.getElementById("bookingForm").submit();
        });
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
            var repeat_type1	= document.getElementById('repeat_type_' + repeat_name + '1');
            var repeat_amount   = document.getElementById('repeat_to_' + repeat_name);
            var rtype		  	= "";
            var rtype1		  	= "";
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
            if(repeat_type1 != null)
            {
                rtype1 = repeat_type1.value;
            }
            if((ramount != "") && (repeat_type != "") && (repeat_type1 != ""))
            {
                repeat_to		= ramount + "|" + rtype1;
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
		</script>
		<?php
	}
	/**
	 * Show Checkout form - Step1
	 *
	 * @param unknown_type $option
	 * @param unknown_type $lists
	 * @param unknown_type $fields
	 */
	static function showCheckoutFormHTML($lists,$fields,$profile)
	{
		global $mainframe,$mapClass,$configClass,$deviceType,$jinput;
		$passlogin = $jinput->getInt('passlogin',0);

		$date_from = $lists['date_from'];
		if($date_from != ""){
			$date_from_array = explode(" ",$date_from);
			$date_from_int = strtotime($date_from_array[0]);
			if($date_from_int > HelperOSappscheduleCommon::getRealTime()){
				$year = date("Y",$date_from_int);
				$month = intval(date("m",$date_from_int));
				$day = intval(date("d",$date_from_int));
			}
		}
		if(version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			JHTML::_('behavior.modal','a.osmodal');
		}
		else
		{
			OSBHelperJquery::colorbox('osmodal');
		}
		$user  = JFactory::getUser();
		$name = "";
		$email = "";
		$show_booking_form = 1;
		if($user->id > 0)
		{
			$name  = ($profile->order_name != "" ? $profile->order_name : $user->name);
			$email = ($profile->order_email != "" ? $profile->order_email : $user->email);
		}else{
			//check the option "allow_regitered_only"
			if($configClass['allow_registered_only'] == 1 || $configClass['allow_registered_only'] == 2)
			{
				$show_booking_form = 0;
			}
		}
		$methods = $lists['methods'];
        $stripePaymentMethod = null;

		if(OSBHelper::isJoomla4())
		{
			$extraCss = "joomla4";
		}
		?>
		<?php
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{	
		?>
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
			<script type="text/javascript" src="<?php echo JUri::root(true)?>/media/com_osservicesbooking/assets/js/colorbox/jquery.colorbox.min.js"></script>
			<script type="text/javascript" src="<?php echo JUri::root(true)?>/media/com_osservicesbooking/assets/js/colorbox/jquery.colorbox.min.js"></script>
			<link rel="stylesheet" href="<?php echo JUri::root()?>media/com_osservicesbooking/assets/js/colorbox/colorbox.min.css" type="text/css" media="screen" />
			<script type="text/javascript">
			  jQuery(document).ready( function(){
				  jQuery(".osb-modal").colorbox({rel:'colorbox',maxWidth:'95%', maxHeight:'95%'});
			  });
			</script>
		<?php } ?>
		<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv authorizeform">
			<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
				<?php echo JText::_('OS_BOOKING_FORM')?>
			</div>
            <div id="errors">
            </div>
			<?php
			$msg = '';
			if($configClass['disable_payments'] == 1)
			{
				if(OSBHelper::getDepositAmount(OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem()) == 0)
				{
					$msg = JText::_('OS_YOU_DONT_NEED_TO_MAKE_ANY_PAYMENT_TO_COMPLETE_THE_BOOKING');
				}
				else
				{
					if(OSBHelper::isHavingCommercialFields())
					{
						$msg = sprintf(JText::_('OS_YOU_WILL_NEED_TO_PAY_TO_COMPLETE_THE_BOOKING'),OSBHelper::getDepositAmount(OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem()).'++');
					}
					else
					{
						$msg = sprintf(JText::_('OS_YOU_WILL_NEED_TO_PAY_TO_COMPLETE_THE_BOOKING'),OSBHelper::showMoney(OSBHelper::getDepositAmount(OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem()),1));
					}
				}
			}
			if($msg != "" && $configClass['show_total_amount'] == 1)
			{
				?>
				<div class="<?php echo $mapClass['row-fluid'];?> depositwarning">
					<div class="<?php echo $mapClass['span12'];?>" style="padding-top:15px;">
						<?php
						echo $msg;
						?>
					</div>
				</div>
				<?php
			}
			?>
			<div class="<?php echo $mapClass['row-fluid'];?> loginform">
				<div class="<?php echo $mapClass['span12'];?>" style="padding-top:15px;">
					<?php
					if($user->id == 0)
					{ //show login form and registration form
						if(($configClass['allow_registered_only'] == 1) or (($configClass['allow_registered_only'] == 2) and ($passlogin == 0)))
						{
							$actionUrl = JRoute::_('index.php?option=com_users&task=user.login');
							$returnUrl = JRoute::_(JURI::root().'index.php?option=com_osservicesbooking&task=form_step1&category_id='.$jinput->getInt('category_id',0).'&employee_id='.$jinput->getInt('employee_id',0).'&vid='.$jinput->getInt('vid',0).'&sid='.$jinput->getInt('sid',0).'&Itemid='.$jinput->getInt('Itemid')."&date_from=".$lists['date_from']."&date_to=".$lists['date_to']);
						?>
						<!-- Login form-->
						<form id="osbloginForm" class="form form-horizontal padding10" name="osbloginForm" method="POST" action="<?php echo $actionUrl;?>">
							<div class="<?php echo $mapClass['control-group'];?>">
								<strong>
									<?php echo  JText::_('OS_EXISTING_USERS_LOGIN');?>
								</strong>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<label class="<?php echo $mapClass['control-label'];?>">
									<?php echo JText::_('OS_USERNAME')?>
								</label>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text"  class="<?php echo $mapClass['input-medium']; ?>" size="20" name="username" id="username" value="" />
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<label class="<?php echo $mapClass['control-label'];?>">
									<?php echo JText::_('OS_PASSWORD')?>
								</label>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="password" class="<?php echo $mapClass['input-medium']; ?>" size="20" name="password" id="password" value="" />
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<input type="submit" value="<?php echo JText::_('OS_LOGIN')?>" class="btn btn-info" onclick="javascript:checkLoginForm();" style="width:auto;" />
								<?php
								$back_link = JRoute::_("index.php?option=com_osservicesbooking&task=default_layout&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$jinput->get('date_from','','string')."&date_to=".$jinput->get('date_to','','string'));
								?>
								<a href="<?php echo $back_link;?>" class="btn btn-warning" style="width:auto;">
									<?php echo JText::_('OS_CLOSE');?>
								</a>
							</div>
						<input type="hidden" name="remember" value="0" />
						<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.login" />
						<input type="hidden" name="return" value="<?php echo base64_encode($returnUrl) ; ?>" />
						<?php echo JHTML::_( 'form.token' ); ?>	
						</form>
						<?php
						}
					}
					if($user->id == 0)
					{
						if((($configClass['allow_registered_only'] == 1) || (($configClass['allow_registered_only'] == 2) && ($passlogin == 0))) && ($configClass['allow_registration'] == 1)){
							?>
							<form method="POST" action="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=form_register');?>" name="osregisterForm" id="osregisterForm" class="form form-horizontal padding10">
								<div class="<?php echo $mapClass['control-group'];?>">
									<strong>
										<?php echo  JText::_('OS_NEW_USER_REGISTER');?>
									</strong>
								</div>
								<div class="<?php echo $mapClass['control-group'];?>">
									<label class="<?php echo $mapClass['control-label'];?>">
										<?php echo JText::_('OS_USERNAME')?>
									</label>
									<div class="<?php echo $mapClass['controls'];?>">
										<input type="text"  class="<?php echo $mapClass['input-medium']; ?>" size="20" name="username" id="username" value="" />
									</div>
								</div>
								<div class="<?php echo $mapClass['control-group'];?>">
									<label class="<?php echo $mapClass['control-label'];?>">
										<?php echo JText::_('OS_PASSWORD')?>
									</label>
									<div class="<?php echo $mapClass['controls'];?>">
										<input type="password" class="<?php echo $mapClass['input-medium']; ?>" size="20" name="password1" id="password1" value="" />
									</div>
								</div>
								<div class="<?php echo $mapClass['control-group'];?>">
									<label class="<?php echo $mapClass['control-label'];?>">
										<?php echo JText::_('OS_REPASSWORD')?>
									</label>
									<div class="<?php echo $mapClass['controls'];?>">
										<input type="password" class="<?php echo $mapClass['input-medium']; ?>" size="20" name="password2" id="password2" value="" />
									</div>
								</div>
								<div class="<?php echo $mapClass['control-group'];?>">
									<label class="<?php echo $mapClass['control-label'];?>">
										<?php echo JText::_('OS_NAME')?>
									</label>
									<div class="<?php echo $mapClass['controls'];?>">
										<input type="text"  class="<?php echo $mapClass['input-medium']; ?>" size="20" name="order_name" id="order_name" value="<?php echo $name?>" />
									</div>
								</div>
								<div class="<?php echo $mapClass['control-group'];?>">
									<label class="<?php echo $mapClass['control-label'];?>">
										<?php echo JText::_('OS_EMAIL')?>
									</label>
									<div class="<?php echo $mapClass['controls'];?>">
										<input type="text"  class="<?php echo $mapClass['input-medium']; ?>" value="<?php echo $email?>" size="20" name="order_email" id="order_email" />
									</div>
								</div>
								<?php
								if ($configClass['active_privacy'] && $configClass['show_privacy_in_registration_form'])
								{
								    $activate_privacy = 1;
									if ($configClass['privacy_policy_article_id'] > 0)
									{
										$privacyArticleId = $configClass['privacy_policy_article_id'];

										if (JLanguageMultilang::isEnabled())
										{
											$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
											$langCode     = JFactory::getLanguage()->getTag();
											if (isset($associations[$langCode]))
											{
												$privacyArticle = $associations[$langCode];
											}
										}

										if (!isset($privacyArticle))
										{
											$db    = JFactory::getDbo();
											$query = $db->getQuery(true);
											$query->select('id, catid')
												->from('#__content')
												->where('id = ' . (int) $privacyArticleId);
											$db->setQuery($query);
											$privacyArticle = $db->loadObject();
										}

										JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

										//$link = JRoute::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid).'&tmpl=component&format=html');
										$link = JUri::root().'index.php?option=com_content&view=article&id='.$privacyArticle->id.'&tmpl=component';
									}
									else
									{
										$link = '';
									}
									?>
									<div class="<?php echo $mapClass['row-fluid']; ?> privacyPolicy">
										<div class="<?php echo $mapClass['span3'];?> boldtext">
											<?php
											if ($link)
											{
												$extra = ' class="osmodal" ' ;
												?>
												<a href="<?php echo $link; ?>" <?php echo $extra;?> class="eb-colorbox-privacy-policy"><?php echo JText::_('OS_PRIVACY_POLICY');?></a>
												<?php
											}
											else
											{
												echo JText::_('OS_PRIVACY_POLICY');
											}
											?>
										</div>
										<div class="<?php echo $mapClass['span8'];?>">
											<input type="checkbox" name="agree_privacy_policy" id="agree_privacy_policy" value="1" data-errormessage="<?php echo JText::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
											<?php
											$agreePrivacyPolicyMessage = JText::_('OS_AGREE_PRIVACY_POLICY_MESSAGE');

											if (strlen($agreePrivacyPolicyMessage))
											{
												?>
												<div class="eb-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
												<?php
											}
											?>
										</div>
									</div>
									<?php
								}
								else
                                {
                                    $activate_privacy = 0;
                                }
								?>
								<div class="<?php echo $mapClass['control-group'];?>">
									<input type="button" value="<?php echo JText::_('OS_REGISTER')?>" class="btn btn-info" onclick="javascript:submitRegisterForm();" style="width:auto;"/>
									<?php
									$back_link = JRoute::_("index.php?option=com_osservicesbooking&task=default_layout&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$jinput->get('date_from','','string')."&date_to=".$jinput->get('date_to','','string')."&Itemid=".$jinput->getInt('Itemid'));
									?>
									<a href="<?php echo $back_link;?>" class="btn btn-warning" style="width:auto;"> 
										<?php echo JText::_('OS_CLOSE');?>
									</a>
								</div>
								<input type="hidden" name="active_privacy" id="active_privacy" value="<?php echo $activate_privacy;?>" />
								<input type="hidden" name="option" value="com_osservicesbooking" />
								<input type="hidden" name="task" value="form_register" />
								<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
								<input type="hidden" name="category_id" id="category_id" value="<?php echo $jinput->getInt('category_id',0)?>" />
								<input type="hidden" name="employee_id" id="employee_id" value="<?php echo $jinput->getInt('employee_id',0)?>" />
								<input type="hidden" name="vid" id="vid" value="<?php echo $jinput->getInt('vid',0)?>" />
							</form>
							<script language="javascript">
							 function submitRegisterForm(){
								var form = document.osregisterForm
								var active_privacy = document.getElementById('active_privacy');
								var pass_privacy = 1;
								if(active_privacy.value == 1)
								{
									if(! document.getElementById('agree_privacy_policy').checked)
									{
										pass_privacy = 0;
									}
								}
								if (form.username.value == "") {
									alert("<?php echo JText::_('OS_ENTER_USERNAME'); ?>");
									form.username.focus();
									return ;
								}
								if (form.password1.value == "") {
									alert("<?php echo JText::_('OS_ENTER_PASSWORD'); ?>");
									form.password1.focus();
									return ;
								}
								if (form.password2.value != form.password1.value) {
									alert("<?php echo JText::_('OS_PASSWORD_DOES_NOT_MATCH'); ?>");
									form.password1.focus();
									return ;
								}
								if(form.order_email.value == ""){
									alert("<?php echo JText::_('OS_ENTER_EMAIL'); ?>");
									form.order_email.focus();
									return ;
								}
								if(form.order_name.value == ""){
									alert("<?php echo JText::_('OS_ENTER_NAME'); ?>");
									form.order_name.focus();
									return ;
								}
								if(pass_privacy == 0)
								{
									alert("<?php echo JText::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>");
									return ;
								}
								
								form.submit();
							}
							</script>
							<?php
						}
						//show skip login/ register
						if(($configClass['allow_registered_only'] == 2) and ($passlogin != 1))
						{
							?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span12'];?> boldtext">
									<a href='<?php echo JRoute::_("index.php?option=com_osservicesbooking&task=form_step1&passlogin=1&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$jinput->get('date_from','','string')."&date_to=".$jinput->get('date_to','','string')."&Itemid=".$jinput->getInt('Itemid'));?>'>
										<?php echo JText::_('OS_SKIP_AUTHENTICATION')?>
									</a>
								</div>
							</div>
							<?php
						}
					}
					if($configClass['allow_registered_only']==0 || $user->id > 0 || ($configClass['allow_registered_only'] == 2) && ($passlogin == 1)) {
					
						if($configClass['remove_confirmation_step'] == 1)
						{
							if($configClass['use_ssl'] == 1)
							{
							?>
							<form method="POST" action="<?php echo $configClass['root_link'].'index.php?option=com_osservicesbooking&task=default_completeorder';?>" name="appform" id="bookingForm" enctype="multipart/form-data" class="padding10 <?php echo $extraCss;?>">
							<?php
							}else{
							?>
							<form method="POST" action="<?php echo JURI::root().'index.php?option=com_osservicesbooking&task=default_completeorder';?>" name="appform" id="bookingForm" enctype="multipart/form-data" class="padding10 <?php echo $extraCss;?>">
							<?php
							}
						}
						else
						{
							if($configClass['use_ssl'] == 1)
							{
							?>
								<form method="POST" action="<?php echo JRoute::_($configClass['root_link'].'index.php?option=com_osservicesbooking&task=form_step2&vid='.$jinput->getInt('vid',0).'&sid='.$jinput->getInt('sid',0).'&category_id='.$jinput->getInt('category_id',0).'&employee_id='.$jinput->getInt('employee_id',0).'&date_from='.$jinput->getInt('date_from','').'&date_to='.$jinput->getInt('date_to','').'&Itemid='.$jinput->getInt('Itemid'));?>" name="appform" id="bookingForm" enctype="multipart/form-data" class="padding10 <?php echo $extraCss;?>">
							<?php
							}else{
							?>
								<form method="POST" action="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=form_step2&vid='.$jinput->getInt('vid',0).'&category_id='.$jinput->getInt('category_id',0).'&sid='.$jinput->getInt('sid',0).'&employee_id='.$jinput->getInt('employee_id',0).'&date_from='.$jinput->getInt('date_from','').'&date_to='.$jinput->getInt('date_to','').'&Itemid='.$jinput->getInt('Itemid'));?>" name="appform" id="bookingForm" enctype="multipart/form-data" class="padding10 <?php echo $extraCss;?>">
							<?php
							}
						}
						?>
						<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
						<?php
						if($user->id > 0)
						{
							if ((JFactory::getUser()->authorise('osservicesbooking.orders', 'com_osservicesbooking') && JFactory::getUser()->authorise('core.manage', 'com_osservicesbooking')) || OSBHelper::isEmployee())
							{
								require_once JPATH_ADMINISTRATOR . '/components/com_osservicesbooking/classes/orders.php';
								?>
								<div class="<?php echo $mapClass['row-fluid'];?>">
									<div class="<?php echo $mapClass['span3'];?> boldtext">
										<?php echo JText::_('OS_CUSTOMER');?>
									</div>
									<div class="<?php echo $mapClass['span8'];?>">
										<?php 
										echo OSappscheduleOrders::getUserInput($user->id,0);
										?>
									</div>
								</div>
								<?php
							}
						}
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
								<?php echo JText::_('OS_NAME')." (*)";?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
								<input type="text"  class="<?php echo $mapClass['input-large']; ?>" size="20" name="order_name" id="order_name" value="<?php echo $name?>" />
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
								<?php echo JText::_('OS_EMAIL')." (*)";?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
								<input type="text"  class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $email?>" size="20" name="order_email" id="order_email" />
							</div>
						</div>
						<?php
						if($configClass['value_sch_include_phone'])
						{
						?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span3'];?> boldtext">
									<?php echo JText::_('OS_PHONE')?>
									<?php
									if($configClass['value_sch_include_phone'] == 2){
										echo "(*)";
									}
									?>
								</div>
								<div class="<?php echo $mapClass['span8'];?>">
									<?php
									if($configClass['clickatell_showcodelist'] == 1)
									{
									?>
									<?php echo $lists['dial']?>
									<?php
									}
									?>
									
									<input type="text"  class="<?php echo $mapClass['input-medium']; ?>" value="<?php echo $profile->order_phone;?>" size="10" name="order_phone" id="order_phone" style="width:180px;"/>
									<input type="hidden" value="<?php echo $configClass['value_sch_include_phone'];?>" name="order_phone_required" id="order_phone_required" />
								</div>
							</div>
						<?php
						}
						if($configClass['value_sch_include_country']){
						?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span3'];?> boldtext">
									<?php echo JText::_('OS_COUNTRY')?>
									<?php
									if($configClass['value_sch_include_country'] == 2){
										echo "(*)";
									}
									?>
								</div>
								<div class="<?php echo $mapClass['span8'];?>">
									<?php echo $lists['country'];?>
								</div>
							</div>
						<?php
						}
						if($configClass['value_sch_include_address']){
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
								<?php echo JText::_('OS_ADDRESS')?>
								<?php
								if($configClass['value_sch_include_address'] == 2){
									echo "(*)";
								}
								?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
								<input type="text"  class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $profile->order_address;?>" size="20" name="order_address" id="order_address" />
								<input type="hidden" value="<?php echo $configClass['value_sch_include_address'];?>" name="order_address_required" id="order_address_required" />
							</div>
						</div>
						<?php
						}
						if($configClass['value_sch_include_city']){
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
								<?php echo JText::_('OS_CITY')?>
								<?php
								if($configClass['value_sch_include_city'] == 2){
									echo "(*)";
								}
								?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
								<input type="text"  class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $profile->order_city;?>" size="20" name="order_city" id="order_city" />
							</div>
						</div>
						<?php
						}
						if($configClass['value_sch_include_state']){
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
								<?php echo JText::_('OS_STATE')?>
								<?php
								if($configClass['value_sch_include_state'] == 2){
									echo "(*)";
								}
								?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
								<input type="text"  class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $profile->order_state;?>" size="10" name="order_state" id="order_state" />
							</div>
						</div>
						<?php
						}
						if($configClass['value_sch_include_zip']){
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
								<?php echo JText::_('OS_ZIP')?>
								<?php
								if($configClass['value_sch_include_zip'] == 2){
									echo "(*)";
								}
								?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
								<input type="text"  class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $profile->order_zip;?>" size="10" name="order_zip" id="order_zip" />
							</div>
						</div>
						<?php
						}
						?>
						<?php
						$fieldArr = array();
						$commercial_ids = array();
						for($i=0;$i<count($fields);$i++)
						{
							$field = $fields[$i];
							$fieldArr[] = $field->id;
							$commercial_ids[] = OSBHelper::checkCommercialOptions($field);
							?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<?php
								if($field->field_type != 5)
								{
								?>
								<div class="<?php echo $mapClass['span3'];?> boldtext">
									<?php echo OSBHelper::getLanguageFieldValue($field,'field_label');?>
									<?php
									if($field->required == 1)
									{
										echo " (*)";
									}
									?>
								</div>
								<div class="<?php echo $mapClass['span8'];?>">
									<?php
									OsAppscheduleDefault::orderField($field,0);
									?>
								</div>
								<?php
								}
								else
								{
								//for showing message
								?>
								<div class="<?php echo $mapClass['span12'];?>">
									<?php
									OsAppscheduleDefault::orderField($field,0);
									?>
								</div>
								<?php
								}
								?>
							</div>
							<?php
						}
						?>
						<input type="hidden" name="commercial_ids" id="commercial_ids" value="<?php echo implode(",",$commercial_ids)?>" />
						<?php
						if($configClass['value_sch_include_notes'] == 1)
						{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
								<?php echo JText::_('OS_NOTES')?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
								<textarea name="notes" id="notes" cols="40" rows="4" class="inputbox form-control"></textarea>
							</div>
						</div>
						<?php
						}
						if(OSBHelper::checkCouponAvailable())
						{
							?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
									<?php echo JText::_('OS_COUPON_CODE');?>
								</div>
								<div class="<?php echo $mapClass['span8'];?>" id="couponcodediv">
									<input type="text" class="input-small search-query" value="" size="10" name="coupon_code" id="coupon_code" style="display:inline;"/>
									<input type="button" class="btn" value="<?php echo JText::_('OS_CHECK_COUPON');?>" onclick="javascript:checkCoupon();" style="display:inline;"/>
								</div>
							</div>
							<?php
						}
						?>
							
						<input type="hidden" name="field_ids" id="field_ids" value="<?php echo implode(",",$fieldArr)?>"  />
						<input type="hidden" name="nmethods" id="nmethods" value="<?php echo count((array)$methods)?>" />
						<?php

						if($configClass['disable_payments'] == 1 && count($methods) > 0 && (OSBHelper::getDepositAmount(OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem()) > 0 || OSBHelper::isHavingCommercialFields()))
						{
							self::showCreditCardForm($methods, $stripePaymentMethod, $lists);
						}
						$passcaptcha = 0;
						if($user->id > 0 && $configClass['pass_captcha'] == 1)
						{
							$passcaptcha = 1;
						}
						if($configClass['value_sch_include_captcha'] == 3 && $passcaptcha == 0)
						{
						?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span3'];?> boldtext">
									<?php echo JText::_('OS_CAPCHA')?>
								</div>
								<div class="<?php echo $mapClass['span8'];?>">
									<?php
									$captchaPlugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
									if ($captchaPlugin)
									{
										$showCaptcha = 1;
										echo JCaptcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
									}
									else
									{
										JFactory::getApplication()->enqueueMessage(JText::_('OS_CAPTCHA_NOT_ACTIVATED_IN_YOUR_SITE'), 'error');
									}
									?>
									<div id="dynamic_recaptcha_1"></div>
								</div>
							</div>
						<?php
						}
						elseif($configClass['value_sch_include_captcha'] == 2 && $passcaptcha == 0)
						{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span3'];?> boldtext">
								<?php echo JText::_('OS_CAPCHA')?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
								<?php
								$resultStr = md5(HelperOSappscheduleCommon::getRealTime());// md5 to generate the random string
								$resultStr = substr($resultStr,0,5);//trim 5 digit 
								?>
								<img src="<?php echo JURI::root()?>index.php?option=com_osservicesbooking&no_html=1&task=ajax_captcha&resultStr=<?php echo $resultStr?>" />  
								<input type="text" class="input-small" id="security_code" name="security_code" maxlength="5" style="width: 50px; margin: 0;"/>
								<input type="hidden" name="resultStr" id="resultStr" value="<?php echo $resultStr?>" />
							</div>
						</div>
						<?php
						}
						?>
						<input type="hidden" name="passcaptcha" id="passcaptcha" value="<?php echo $passcaptcha;?>" />
						<?php
						$session = JFactory::getSession();
						$pass_privacy = $session->get('pass_privacy',0);
						if($user->id > 0 && $configClass['show_privacy_with_logged_users'] == 0)
						{
							$pass_privacy = 1;
						}
						if ($configClass['active_privacy'] && $pass_privacy == 0)
						{
							if ($configClass['privacy_policy_article_id'] > 0)
							{
								$privacyArticleId = $configClass['privacy_policy_article_id'];

								if (JLanguageMultilang::isEnabled())
								{
									$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
									$langCode     = JFactory::getLanguage()->getTag();
									if (isset($associations[$langCode]))
									{
										$privacyArticle = $associations[$langCode];
									}
								}

								if (!isset($privacyArticle))
								{
									$db    = JFactory::getDbo();
									$query = $db->getQuery(true);
									$query->select('id, catid')
										->from('#__content')
										->where('id = ' . (int) $privacyArticleId);
									$db->setQuery($query);
									$privacyArticle = $db->loadObject();
								}

								JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

								$link = JRoute::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid).'&tmpl=component&format=html');
							}
							else
							{
								$link = '';
							}
							?>
							<div class="<?php echo $mapClass['row-fluid']; ?> privacyPolicy">
								<div class="<?php echo $mapClass['span3'];?> boldtext">
									<?php
									if ($link)
									{
										$extra = ' class="osmodal" ' ;
										?>
										<a href="<?php echo $link; ?>" <?php echo $extra;?> class="eb-colorbox-privacy-policy"><?php echo JText::_('OS_PRIVACY_POLICY');?></a>
										<?php
									}
									else
									{
										echo JText::_('OS_PRIVACY_POLICY');
									}
									?>
								</div>
								<div class="<?php echo $mapClass['span8'];?>">
									<input type="checkbox" name="agree_privacy_policy" id="agree_privacy_policy" value="1" data-errormessage="<?php echo JText::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" />
									<?php
									$agreePrivacyPolicyMessage = JText::_('OS_AGREE_PRIVACY_POLICY_MESSAGE');

									if (strlen($agreePrivacyPolicyMessage))
									{
										?>
										<div class="eb-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
										<?php
									}
									?>
								</div>
							</div>
							<?php
						}
						if($configClass['value_sch_reminder_enable'] == 1 && $configClass['enable_reminder'] == 1)
						{
							?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span3'];?>">
									<?php echo JText::_( 'OS_RECEIVE_REMINDER') ;?>
								</div>
								<div class="<?php echo $mapClass['span8'];?>">
									<input type="checkbox" name="receive_reminder" value="1" checked/>
								</div>
							</div>
							<?php
						}
						if($configClass['enable_termandcondition'] == 1 && $configClass['article_id'] > 0)
						{
							if (!isset($article))
							{
								$db    = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->select('id, catid')
									->from('#__content')
									->where('id = ' . (int) $configClass['article_id']);
								$db->setQuery($query);
								$article = $db->loadObject();
							}
							JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');
							//$termLink = ContentHelperRoute::getArticleRoute($article->id, $article->catid) . '&tmpl=component&format=html';
							$termLink = JUri::root().'index.php?option=com_content&view=article&id='.$article->id.'&tmpl=component';
							?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span3'];?>">
								</div>
								<div class="<?php echo $mapClass['span8'];?>">
									<input type="checkbox" name="term_and_condition" id="term_and_condition" value="0" style="margin:0px !important;" onclick="javascript:changeValue('term_and_condition');"/>
									<strong>
										<a href="<?php echo $termLink;?>" class="osmodal" rel="{handler: 'iframe', size: {x: 500, y: 400}}">
											<?php echo JText::_('OS_I_AGREE_WITH_THE_TERM_AND_CONDITION');?>
										</a>
									</strong>
								</div>
							</div>
							<?php
						}
						if(OsAppscheduleAjax::isAnyItemsInCart())
						{
							?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span12'];?>" style="text-align:center;">
									<input type="button" class="btn btn-success" value="<?php echo JText::_('OS_SUBMIT')?>" onclick="javascript:confirmBooking()" />
									<?php
									if($configClass['show_calendar_box'] == 1){
										$back_link = JRoute::_("index.php?option=com_osservicesbooking&task=default_layout&category_id=".$jinput->getInt('category_id',0)."&employee_id=".$jinput->getInt('employee_id',0)."&vid=".$jinput->getInt('vid',0)."&sid=".$jinput->getInt('sid',0)."&date_from=".$jinput->getInt('date_from','')."&date_to=".$jinput->getInt('date_to',''));
										?>
											<a href="<?php echo $back_link;?>" class="btn btn-warning">
												<?php echo JText::_('OS_CLOSE');?>
											</a>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
						<input type="hidden" name="fields" id="fields" value="" />
						<input type="hidden" name="option" value="com_osservicesbooking" />
						<?php
						if($configClass['remove_confirmation_step']  == 1)
						{
							$task = "default_completeorder";
						}
						else
						{
							$task = "form_step2";
						}
						?>
						<input type="hidden" name="task" value="<?php echo $task;?>" />
						<input type="hidden" name="category_id" id="category_id" value="<?php echo $jinput->getInt('category_id',0)?>" />
						<input type="hidden" name="employee_id" id="employee_id" value="<?php echo $jinput->getInt('employee_id',0)?>" />
						<input type="hidden" name="vid" id="vid" value="<?php echo $jinput->getInt('vid',0)?>" />
						<input type="hidden" name="enable_termandcondition" id="enable_termandcondition" value="<?php echo $configClass['enable_termandcondition'];?>" />
						<input type="hidden" name="article_id" id="article_id" value="<?php echo (int) $configClass['article_id'];?>" />
						<input type="hidden" name="active_privacy" id="active_privacy" value="<?php echo $configClass['active_privacy'];?>" />
						<input type="hidden" name="coupon_id" id="coupon_id" value=""/>
						<input type="hidden" name="discount_100" id="discount_100" value="0" />
						<input type="hidden" name="final_cost" id="final_cost" value="<?php echo $lists['total'];?>" />
						<input type="hidden" name="unique_cookie" id="unique_cookie" value="<?php echo OSBHelper::getUniqueCookie();?>" />
						<input type="hidden" id="card-nonce" name="nonce" />
						<input type="hidden" id="velocitySessionToken" name="velocitySessionToken" />
						<input type="hidden" id="identitytoken" name="identitytoken" />
						<input type="hidden" id="applicationprofileid" name="applicationprofileid" />
						<input type="hidden" id="merchantprofileid" name="merchantprofileid" />
						<input type="hidden" id="workflowid" name="workflowid" />
						<input type="hidden" name="count_services" id="count_services" value="<?php echo count($lists['services']);?>" />
						<input type="hidden" id="pass_privacy" name="pass_privacy" value="<?php echo (int) $pass_privacy;?>" />
						<?php
						$temp = array();
						foreach($lists['services'] as $s){
							$temp[] = $s->id;
						}
						$temp = implode(",",$temp);
						?>
						<input type="hidden" name="services" id="services" value="<?php echo $temp;?>" />
					</form>			
					<?php
					}
					?>
				</div>
			</div>
		</div>
		<script type="text/javascript">

        jQuery(document).ready( function(){
            if (typeof stripe !== 'undefined')
            {

                var style = {
					hidePostalCode: true,
                    base: {
                        // Add your base input styles here. For example:
                        fontSize: '16px',
                        color: "#32325d",
                    }
                };
                // Create an instance of the card Element.
                //card = elements.create('card', {hidePostalCode: true, style: style});
				card = elements.create('card', {style: style});
                // Add an instance of the card Element into the `card-element` <div>.
                card.mount('#stripe-card-element');
            }
        });
		var live_site = "<?php echo JUri::root(); ?>";
		function populateUserData()
		{
			var id = jQuery('#user_id_id').val();
			populateUserDataAjax(id,0,live_site);
		}
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
            var repeat_type1	= document.getElementById('repeat_type_' + repeat_name + '1');
            var repeat_amount   = document.getElementById('repeat_to_' + repeat_name);
            var rtype		  	= "";
            var rtype1		  	= "";
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
            if(repeat_type1 != null)
            {
                rtype1 = repeat_type1.value;
            }
            if((ramount != "") && (repeat_type != "") && (repeat_type1 != ""))
            {
                repeat_to		= ramount + "|" + rtype1;
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
		 function confirmBooking(){
			var form					=   document.appform;
			var order_name 				= 	document.getElementById('order_name');
			var order_email 			= 	document.getElementById('order_email');
			var order_phone 			= 	document.getElementById('order_phone');
			var order_phone_required 	= 	document.getElementById('order_phone_required');
			var order_country 			= 	document.getElementById('order_country');
			var order_city 				= 	document.getElementById('order_city');
			var order_state 			= 	document.getElementById('order_state');
			var order_zip				= 	document.getElementById('order_zip');
			var order_address			=   document.getElementById('order_address');
			var order_address_required	=   document.getElementById('order_address_required');
			var live_site 				= 	document.getElementById('live_site');
			var resultStr 				=   document.getElementById('resultStr');
			var use_captcha				= 	document.getElementById('use_captcha');
			var field_ids				= 	document.getElementById('field_ids');
			
			var enable_termandcondition =   document.getElementById('enable_termandcondition');
			var article_id				=   document.getElementById('article_id');
			var active_privacy          =   document.getElementById('active_privacy');
			var privacy_passed			=	document.getElementById('pass_privacy');
			<?php
			if($configClass['value_sch_include_notes'] == 1)
			{
			?>
				var notes		 			= 	document.getElementById('notes');
				notes						= 	notes.value;
				notes						= 	notes.replace("&","(@)");
				notes						= 	notes.replace("\"","'");
			<?php
			}
			?>
			
			var commercial_ids			= document.getElementById('commercial_ids');
			commercial_ids				= commercial_ids.value;
			commercial_ids				= commercial_ids.split(',');
			var fieldtype				= "";
			var objid					= "";
			var objitem					= "";
			var check					= 0;
			if(commercial_ids.length > 0){
				for(i=0;i<commercial_ids.length;i++){
					temp = commercial_ids[i];
					temp = temp.split("||");
					fieldtype = temp[1];
					objid     = temp[0];
					objitem   = document.getElementById(objid);
					if(fieldtype == "1"){
						if(objitem.selected == true){
							check = 1;
						}
					}else if(fieldtype == "2"){
						if(objitem.checked == true){
							check = 1;
						}
					}
				}
			}
			var coupon_code	 			= document.getElementById('coupon_code');
			if(coupon_code != null){
				if(coupon_code.value != ""){
					var answer = confirm("<?php echo JText::_('OS_YOU_ENTER_COUPON_CODE')?>");
					if(answer == 1){
						alert("<?php echo JText::_('OS_CLICK_CHECK_COUPON');?>");
						coupon_code.focus();
						return false;
					}else{
						coupon_code.value = "";
					}
				}
			}
			var methodpass				= 1;
			var paymentMethod 			= "";
			var x_card_num				= "";
			var x_card_code				= "";
			var card_holder_name		= "";
			var exp_month				= "";
			var exp_year				= "";
			var card_type				= "";
			<?php
			if($configClass['disable_payments'] == 1 && count($methods) > 0 && (OSBHelper::getDepositAmount(OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem()) > 0 || OSBHelper::isHavingCommercialFields()))
			{
				
					if (count($methods) > 1) 
					{
					?>
						var paymentValid = false;
						var nmethods = document.getElementById('nmethods');
						var methodtemp;
						for (var i = 0 ; i < nmethods.value; i++) {
							methodtemp = document.getElementById('pmt' + i);
							if(methodtemp.checked == true){
								paymentValid = true;
								paymentMethod = methodtemp.value;
								break;
							}
						}
						if (!paymentValid) {
							alert("<?php echo JText::_('OS_REQUIRE_PAYMENT_OPTION'); ?>");
							methodpass = 0;
						}		
					<?php	
					} else {
					?>
						paymentMethod = "<?php echo $methods[0]->getName(); ?>";
					<?php	
					}				
					?>
					var discount_100	= document.getElementById('discount_100');
					method = methods.Find(paymentMethod);	
					if ((method.getCreditCard()) && (discount_100.value == "0") && (paymentMethod != 'os_squareup')) {
						var x_card_nume = document.getElementById('x_card_num');
						if (x_card_nume.value == "") {
							alert("<?php echo  JText::_('OS_ENTER_CARD_NUMBER'); ?>");
							x_card_nume.focus();
							methodpass	= 0;
							return 0;
						}else{
							x_card_num	= x_card_nume.value;
						}
						
						var x_card_codee = document.getElementById('x_card_code');
						if (x_card_codee.value == "") {
							alert("<?php echo JText::_('OS_ENTER_CARD_CODE'); ?>");
							x_card_codee.focus();
							methodpass	= 0;
							return 0;
						}else{
							x_card_code = x_card_codee.value;
						}
					}
					if (method.getCardHolderName() && (discount_100.value == "0") && (paymentMethod != 'os_squareup')) {
						card_holder_namee = document.getElementById('card_holder_name');
						if (card_holder_namee.value == '') {
							alert("<?php echo JText::_('OS_ENTER_CARD_HOLDER_NAME') ; ?>");
							card_holder_namee.focus();
							methodpass = 0;
							return 0;
						}else{
							card_holder_name = card_holder_namee.value;
						}
					}
					if(paymentMethod != 'os_squareup') {
                        var exp_year = jQuery('#exp_year').val();
                        var exp_month = jQuery('#exp_month').val();
                        var card_type = jQuery('#card_type').val();
                    }
				<?php
			}
			?>
			field_ids					= 	field_ids.value;
			var fieldArr				= 	new Array();
			fieldArr					= 	field_ids.split(",");
			var str						=	"";
			var temp;
			var element;
			if(fieldArr.length > 0){
				for(i=0;i<fieldArr.length;i++){
					temp = fieldArr[i];
					element				= document.getElementById('field_' + temp);
					required			= document.getElementById('field_' + temp + '_required');
					label				= document.getElementById('field_' + temp + '_label');
					typeElement			= document.getElementById('field_' + temp + '_type');
					if(element != null){
						if(element.value != ""){
							str += temp + "|" + element.value + "||";
						}else if(required.value == "1"){
							if(typeElement.value == "image"){
								old_picture = document.getElementById('old_field_' + temp);
								if(old_picture.value == ""){
									alert(label.value + "<?php echo JText::_('OS_IS_MANDATORY_FIELD');?>");
								}
							}else{
								alert(label.value + "<?php echo JText::_('OS_IS_MANDATORY_FIELD');?>");
							}
							return false;
						}
					}
				}
				if(str != ""){
					str					= str.substring(0,str.length - 2);
				}
				str						= str.replace("\"","'");
				document.getElementById('fields').value = str;
			}
			if(order_name != null)
			{
				order_name				= order_name.value;
			}
			else
			{
				order_name				= "";
			}
			if(order_email != null)
			{
				order_email				= order_email.value;
			}
			else
			{
				order_email				= "";
			}
			if(order_phone != null)
			{
				order_phone				= order_phone.value;
			}
			else
			{
				order_phone				= "";
			}
			if(order_country != null)
			{
				order_country			= order_country.value;
			}
			else
			{
				order_country			= "";
			}
			if(order_city != null)
			{
				order_city				= order_city.value;
			}
			else
			{
				order_city				= "";
			}
			if(order_state != null)
			{
				order_state				= order_state.value;
			}
			else
			{
				order_state				= "";
			}
			if(order_address != null)
			{
				order_address			= order_address.value;
			}
			else
			{
				order_address			= "";
			}
			if(order_zip != null)
			{
				order_zip				= order_zip.value;
			}
			else
			{
				order_zip				= "";
			}
			var check_captcha			= 0;
			var captcha_pass			= 0;
			if(use_captcha.value == "2")
			{
				check_captcha			= 1;
				var security_code		= document.getElementById('security_code');
				var passcaptcha         = document.getElementById('passcaptcha');
				if(passcaptcha.value == 1)
				{
				    captcha_pass = 1;
                }
                else
                {
                    if (security_code.value == "")
                    {
                        captcha_pass = 0;
                    }
                    else if (security_code.value != resultStr.value)
                    {
                        captcha_pass = 0;
                    }
                    else
                    {
                        captcha_pass = 1;
                    }
                }
			}
			var pass_term = 1;
			if(enable_termandcondition.value == 1 && article_id.value > 0)
			{
				var term_and_condition	= document.getElementById('term_and_condition');
                if(! document.getElementById('term_and_condition').checked)
                {
                    pass_term = 0;
                }
			}

			var pass_privacy = 1;
			if(active_privacy.value == 1 && privacy_passed.value == 0)
			{
                if(! document.getElementById('agree_privacy_policy').checked)
                {
                    pass_privacy = 0;
                }
			}


			if(methodpass == 1){
				if((check_captcha == 1) && (captcha_pass == 0))
				{
					var security_code   =   document.getElementById('security_code');
					alert("<?php echo Jtext::_('OS_CAPTCHA_IS_NOT_VALID');?>");
					security_code.focus();
				}
				else if(order_name == "")
				{
					alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_NAME')?>");
					document.getElementById('order_name').focus();
				}
				else if(order_email == "")
				{
					alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_EMAIL')?>");
					document.getElementById('order_email').focus();
				}
				else if(validateEmailAddress(jQuery('#order_email').val()) == false)
				{
					alert("<?php echo JText::_('OS_EMAIL_IS_NOT_VALID')?>");
					document.getElementById('order_email').focus();
				<?php
				if($configClass['value_sch_include_address'] == 2)
				{
					?>
					}else if(order_address == ""){
						alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_ADDRESS')?>");
						document.getElementById('order_address').focus();
					<?php
				}
				?>
				<?php
				if($configClass['value_sch_include_phone'] == 2)
				{
					?>
					}else if(order_phone == ""){
						alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_PHONE_NUMBER')?>");
						document.getElementById('order_phone').focus();
					<?php
				}
                if($configClass['value_sch_include_country'] == 2)
                {
                    ?>
                    }else if(order_country == ""){
                        alert("<?php echo JText::_('OS_PLEASE_SELECT_COUNTRY')?>");
                        document.getElementById('order_country').focus();
                        <?php
                }
                if($configClass['value_sch_include_city'] == 2)
                {
                    ?>
                    }else if(order_city == ""){
                        alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_CITY')?>");
                        document.getElementById('order_city').focus();
                        <?php
                }
                if($configClass['value_sch_include_state'] == 2)
                {
                    ?>
                }
                else if(order_state == "")
                {
                    alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_STATE')?>");
                    document.getElementById('order_state').focus();
                    <?php
				}
				if($configClass['value_sch_include_zip'] == 2)
				{
                    ?>
                }
                else if(order_zip == "")
                {
                    alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_ZIP_CODE')?>");
                    document.getElementById('order_zip').focus();
                    <?php
				}
				?>

				}
				else if(pass_term == 0)
				{
					alert("<?php echo JText::_('OS_PLEASE_AGREE_TERM_AND_CONDITION');?>");
				}
				else if(pass_privacy == 0)
				{
				    alert("<?php echo JText::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>");
				}
				else
				{
					//check to see if you are using
					if(paymentMethod == "os_stripe")
					{
                        if (typeof stripe !== 'undefined' && paymentMethod.indexOf('os_stripe') == 0 && jQuery('#stripe-card-form').is(":visible"))
                        {
                            stripe.createToken(card).then( function(result) {
                                if (result.error) {
                                    // Inform the customer that there was an error.
                                    //var errorElement = document.getElementById('card-errors');
                                    //errorElement.textContent = result.error.message;
                                    alert(result.error.message);
                                    form.find('#btn-submit').prop('disabled', false);
                                }
                                else
                                {
                                    // Send the token to your server.
                                    stripeTokenHandler(result.token);
                                }
                            });

                            return false;
                        }
                        else if (typeof stripePublicKey !== 'undefined' && paymentMethod.indexOf('os_stripe') == 0 && $('#tr_card_number').is(':visible'))
                        {
                            Stripe.card.createToken({
                                number: jQuery('#x_card_num').val(),
                                cvc: jQuery('#x_card_code').val(),
                                exp_month: jQuery('#exp_month').val(),
                                exp_year: jQuery('#exp_year').val(),
                                name: jQuery('#card_holder_name').val()
                            }, stripeResponseHandler);
                        }
                    }
                    else if(paymentMethod == "os_squareup")
                    {
                        sqPaymentForm.requestCardNonce();
                    }
					else if(paymentMethod == "os_velocity")
                    {
						/*
						var applicationprofileid			= jQuery('#applicationprofileid').val();
						var merchantprofileid				= jQuery('#merchantprofileid').val();
						var workflowid						= jQuery('#workflowid').val();
						var sessionToken					= jQuery('#velocitySessionToken').val();
						var exp_year						= jQuery("#exp_year").val();
						var exp_month						= jQuery("#exp_month").val();
						if(exp_year.length == 4)
						{
							exp_year = exp_year.substring(2,4);
						}
						if(exp_month.length == 1)
						{
							exp_month = "0" + exp_month;
						}
						var card = {
					        CardholderName: jQuery("#card_holder_name").val(), cardtype: jQuery("#card_type").val(), number: jQuery("#x_card_num").val(), 
							cvc: jQuery("#x_card_code").val(), expMonth: exp_month, expYear: exp_year
						};
                                 
						var address = {
                            Street: jQuery("#order_address").val(),
							City: jQuery("#order_city").val(),
							StateProvince: jQuery("#order_state").val(),
							Phone: jQuery("#order_phone").val(),
							PostalCode: jQuery("#order_zip").val()
						};
						Velocity.tokenizeForm(sessionToken, card, address, applicationprofileid, merchantprofileid, workflowid, responseHandler);
						*/
                    }
                    else
                    {
						<?php if($configClass['use_js_popup'] == 1){?>
							var answer = confirm('<?php echo JText::_("OS_ARE_YOU_SURE_TO_SUBMIT_THE_CHECKOUT_FORM")?>');
							if(answer)
							{
								form.submit();
							}
						<?php
						}
						else
						{
							?>
							form.submit();
							<?php
						}
						?>
					}
				}
			}
		}

         function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var $form = jQuery('#bookingForm');
            //var hiddenInput = document.createElement('input');
            //hiddenInput.setAttribute('type', 'ext');
            //hiddenInput.setAttribute('name', 'stripeToken');
            //hiddenInput.setAttribute('value', token.id);
            //$form.appendChild(hiddenInput);
            // Submit the form
            //$form.submit();
            //var token = response.id;
            // Insert the token into the form so it gets submitted to the server
            if (token.error)
            {
                // Show the errors on the form
                alert(response.error.message);
            }
            else
            {
                $form.append(jQuery('<input type="hidden" name="stripeToken" />').val(token.id));
                $form.submit();
            }
        }

		 function responseHandler(result) 
		{
			var $form = jQuery('#bookingForm');
			var returnMsg = "";
			if (result['code'] == 0) 
			{
				// Request was successful. Insert hidden field into the form before submitting.
				// Continue to submit the form to the action, where we will read the decode and extract POST data.
				$form.append(jQuery('<input type="hidden" name="TransactionToken" />').val(result.text));
				$form.submit();
			}
			else 
			{
				for (var i in result) 
				{
					returnMsg += result[i];
				}
				alert(returnMsg);
				return false;
			}
		}

		 function stripeResponseHandler(status, response) 
		{
			var $form = jQuery('#bookingForm');
			if (response.error) 
			{
				// Show the errors on the form
				alert(response.error.message);
			} 
			else 
			{
				// token contains id, last4, and card type
				var token = response.id;
				// Insert the token into the form so it gets submitted to the server
				$form.append(jQuery('<input type="hidden" name="stripeToken" />').val(token));
				$form.submit();
			}
		};
		</script>
		<?php
	}

	public static function showCreditCardForm($methods, $stripePaymentMethod, $lists)
	{
		global $mapClass, $jinput;
		$user = JFactory::getUser();
		if(count($methods) > 0)
		{
		?>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span3'];?> boldtext">
				<?php echo JText::_('OS_PAYMENT_OPTION'); ?>
				<span class="required">*</span>						
			</div>
			<div class="<?php echo $mapClass['span8'];?>">
				<?php
					$method = null ;
					for ($i = 0 , $n = count($methods); $i < $n; $i++)
					{
						$paymentMethod = $methods[$i];
						if ($paymentMethod->getName() == $lists['paymentMethod'])
						{
							$checked = ' checked="checked" ';
							$method = $paymentMethod ;
						}										
						else
						{
							$checked = '';
						}
						if (strpos($paymentMethod->getName(), 'os_stripe') !== false)
						{
							$stripePaymentMethod = $paymentMethod;
						}
						if (strpos($paymentMethod->getName(), 'os_prepaid') !== false && (int) $user->id == 0)
						{
							$disabled = "disabled";
						}
						else
						{
							$disabled = "";
						}
					?>
						<input onclick="changePaymentMethod();" type="radio" name="payment_method" id="pmt<?php echo $i?>" value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> <?php echo $disabled; ?>/><label for="pmt<?php echo $i?>" class="payment_plugin_label"><?php echo JText::_($paymentMethod->title) ; ?></label> <br />
					<?php		
					}	
				?>
			</div>
		</div>				
		<?php					
		} 
		else 
		{
			$method = $methods[0] ;
			if (strpos($method->getName(), 'os_stripe') !== false)
			{
				$stripePaymentMethod = $method;
			}
		}
		//print_r($methods);die();

		if ($method->getName() == 'os_squareup')
		{
			$style = '';
		}
		else
		{
			$style = 'style = "display:none"';
		}
		?>
			<div class="<?php echo $mapClass['row-fluid'];?> payment_information width100" id="sq_field_zipcode" <?php echo $style; ?>>
				<div class="<?php echo $mapClass['span3']?>">
					<label class="boldtext" for="sq_billing_zipcode">
						<?php echo JText::_('OS_SQUAREUP_ZIPCODE'); ?><span class="required">*</span>
					</label>
				</div>
				<div class="<?php echo $mapClass['span8'];?>">
					<div id="field_zip_input">
						<input type="text" id="sq_billing_zipcode" name="sq_billing_zipcode" class="input-large" value="<?php echo $jinput->getString('sq_billing_zipcode'); ?>" />
					</div>
				</div>
			</div>
			<?php
			if ($method->getCreditCard())
			{
				$style = '' ;
			}
			else
			{
				$style = 'style = "display:none"';
			}
			?>
			<div class="<?php echo $mapClass['row-fluid'];?> width100" id="tr_card_number" <?php echo $style; ?>>
				<div class="<?php echo $mapClass['span3']?>">
					<label class="boldtext"><?php echo  JText::_('OS_AUTH_CARD_NUMBER'); ?><span class="required">*</span></label>
				</div>
				
				<div class="<?php echo $mapClass['span8'];?>">
					<div id="sq-card-number">
						<input type="text" name="x_card_num" id="x_card_num" class="osm_inputbox input-medium form-control ilarge" onkeyup="checkNumber(this,'<?php echo JText::_('OS_ONLY_NUMBER'); ?>')" value="<?php echo $lists['x_card_num']; ?>" size="20" />
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="<?php echo $mapClass['row-fluid'];?> width100" id="tr_exp_date" <?php echo $style; ?>>
				<div class="<?php echo $mapClass['span3']?>">
					<label class="boldtext">
						<?php echo JText::_('OS_AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
					</label>
				</div>
				<div class="<?php echo $mapClass['span8'];?>">
					<div id="sq-expiration-date">
						<?php echo $lists['exp_month'] .'  /  '.$lists['exp_year'] ; ?>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="<?php echo $mapClass['row-fluid'];?> width100" id="tr_cvv_code" <?php echo $style; ?>>
				<div class="<?php echo $mapClass['span3']?>">
					<label class="boldtext">
						<?php echo JText::_('OS_AUTH_CVV_CODE'); ?><span class="required">*</span>
					</label>
				</div>
				<div class="<?php echo $mapClass['span8'];?>">
					<div id="sq-cvv">
						<input type="text" name="x_card_code" id="x_card_code" class="osm_inputbox input-mini form-control ilarge" onKeyUp="checkNumber(this,'<?php echo JText::_('OS_ONLY_NUMBER'); ?>')" value="<?php echo $lists['x_card_code']; ?>" size="20" />
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php
			if ($method->getCardType())
			{
				$style = '' ;
			}
			else
			{
				$style = ' style = "display:none;" ' ;
			}
			?>
			<div class="<?php echo $mapClass['row-fluid'];?> width100" id="tr_card_type" <?php echo $style; ?>>
				<div class="<?php echo $mapClass['span3']?>">
					<label class="boldtext">
						<?php echo JText::_('OS_CARD_TYPE'); ?><span class="required">*</span>
					</label>
				</div>
				<div class="<?php echo $mapClass['span8'];?>">
					<?php echo $lists['card_type'] ; ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php
			if ($method->getCardHolderName())
			{
				$style = '' ;
			}
			else
			{
				$style = ' style = "display:none;" ' ;
			}
			?>
			<div class="<?php echo $mapClass['row-fluid'];?> width100" id="tr_card_holder_name" <?php echo $style; ?>>
				<div class="<?php echo $mapClass['span3']?>">
					<label class="boldtext">
						<?php echo JText::_('OS_CARD_HOLDER_NAME'); ?><span class="required">*</span>
					</label>
				</div>
				<div class="<?php echo $mapClass['span8'];?>">
					<input type="text" name="card_holder_name" id="card_holder_name" class="osm_inputbox input-medium form-control ilarge"  value="<?php echo $lists['cardHolderName']; ?>" size="40" />
				</div>
			</div>
			<div class="clearfix"></div>

			<?php
			if ($stripePaymentMethod !== null && method_exists($stripePaymentMethod, 'getParams'))
			{
				/* @var os_stripe $stripePaymentMethod */
				$params = $stripePaymentMethod->getParams();
				//$useStripeCardElement = $params->get('use_stripe_card_element', 0);

				//if ($useStripeCardElement)
				//{
					if ($method->getName() === 'os_stripe')
					{
						$style = '';
					}
					else
					{
						$style = ' style = "display:none;" ';
					}
					?>
					<div class="<?php echo $mapClass['row-fluid'];?> payment_information width100" <?php echo $style; ?> id="stripe-card-form">
						<div class="<?php echo $mapClass['span3']?>" for="stripe-card-element">
							<label class="boldtext">
								<?php echo JText::_('OS_CREDIT_OR_DEBIT_CARD'); ?><span class="required">*</span>
							</label>
						</div>
						<div class="<?php echo $mapClass['span8'];?>" id="stripe-card-element">

						</div>
					</div>
					<?php
				//}
			}
			?>
			<div class="clearfix"></div>
			<?php
			if ($method->getName() == 'os_echeck') 
			{
				$style = '';
			} 
			else 
			{
				$style = ' style = "display:none;" ' ;
			}
				?>

			<div class="<?php echo $mapClass['control-group'];?>" id="tr_bank_rounting_number" <?php echo $style; ?>>
				<label class="<?php echo $mapClass['control-label'];?> boldtext"><?php echo JText::_('OS_BANK_ROUTING_NUMBER'); ?><span class="required">*</span>
				</label>
				<div class="<?php echo $mapClass['controls'];?>">
					<input type="text" name="x_bank_aba_code" class="input-large validate[required,custom[number]]" value="<?php echo $jinput->get('x_bank_aba_code', '', 'none'); ?>" size="40"/>
				</div>
			</div>
			<div class="<?php echo $mapClass['control-group'];?>" id="tr_bank_account_number" <?php echo $style; ?>>
				<label class="<?php echo $mapClass['control-label'];?> boldtext"><?php echo JText::_('OS_BANK_ACCOUNT_NUMBER'); ?><span class="required">*</span>
				</label>
				<div class="<?php echo $mapClass['controls'];?>">
					<input type="text" name="x_bank_acct_num" class="input-large validate[required,custom[number]]" value="<?php echo $jinput->get('x_bank_acct_num', '', 'none');; ?>" size="40"/>
				</div>
			</div>
			<div class="<?php echo $mapClass['control-group'];?>" id="tr_bank_account_type" <?php echo $style; ?>>
				<label class="<?php echo $mapClass['control-label'];?> boldtext"><?php echo JText::_('OS_BANK_ACCOUNT_TYPE'); ?><span class="required">*</span></label>
				<div class="<?php echo $mapClass['controls'];?>"><?php echo $lists['x_bank_acct_type']; ?></div>
			</div>
			<div class="<?php echo $mapClass['control-group'];?>" id="tr_bank_name" <?php echo $style; ?>>
				<label class="<?php echo $mapClass['control-label'];?> boldtext">
					<?php echo JText::_('OS_BANK_NAME'); ?><span class="required">*</span>
				</label>
				<div class="<?php echo $mapClass['controls'];?>">
					<input type="text" name="x_bank_name" class="input-large validate[required]" value="<?php echo $jinput->get('x_bank_name', '', 'none'); ?>" size="40"/>
				</div>
			</div>
			<div class="<?php echo $mapClass['control-group'];?>" id="tr_bank_account_holder" <?php echo $style; ?>>
				<label class="<?php echo $mapClass['control-label'];?> boldtext">
					<?php echo JText::_('OS_ACCOUNT_HOLDER_NAME'); ?><span class="required">*</span>
				</label>
				<div class="<?php echo $mapClass['controls'];?>">
					<input type="text" name="x_bank_acct_name" class="input-large validate[required]" value="<?php echo $jinput->get('x_bank_acct_name', '', 'none'); ?>" size="40"/>
				</div>
			</div>
			<div class="clearfix"></div>
		<?php
	}

    /**
     * Checkout layout
     * @param $lists
     * @param $fields
     * @param $profile
     */
	static function checkoutLayout($lists,$fields,$profile)
    {
		global $mainframe,$mapClass,$configClass,$jinput,$deviceType;
		//jimport('joomla.html.pane');
		$pane           =& JPane::getInstance('tabs');
		$methods        = os_payments::getPaymentMethods(true, false) ;
		$passlogin      = $jinput->getInt('passlogin',0);
		OSBHelper::showProgressBar('form_step1',$passlogin);
		$vid = $lists['vid'];
		$category_id    = $lists['category'];
		$employee_id    = $lists['employee_id'];
		$date_from      = $lists['date_from'];
		$date_to        = $lists['date_to'];
		$sid            = $lists['sid'];
		?>
		<div id="msgDiv" style="width:100%;">
		</div>
		<div class="<?php echo $mapClass['row-fluid'];?>">
            <?php
            $show_calendar = 0;
            if($configClass['show_calendar_box'] != 2){
                if(!OSBHelper::isTheSameDate($lists['date_from'],$lists['date_to'])){
                    $show_calendar = 1;
                }
            }
            if($configClass['using_cart'] == 1 || $show_calendar == 1 || count($lists['selected_dates']) > 0)
            {
				$secondDiv = $mapClass['span8'];
				if($configClass['calendar_position'] == 0)
				{
				?>
					<div class="<?php echo $mapClass['span4'];?>" id="calendardivleft">
						<?php
						if(count($lists['selected_dates']) > 0 && $configClass['show_calendar_box'] != 2)
						{
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
						elseif((!OSBHelper::isTheSameDate($lists['date_from'],$lists['date_to'])) && $configClass['show_calendar_box'] != 2)
						{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php
								HelperOSappscheduleCalendar::initCalendarForSeveralYear(intval(date("Y",HelperOSappscheduleCommon::getRealTime())),$lists['category'],$lists['employee_id'],$lists['vid'], $lists['sid'],$lists['date_from'],$lists['date_to']);
								?>
								<input type="hidden" name="ossmh" id="ossmh" value="<?php echo intval(date("m",$lists['current_time']))?>">
								<input type="hidden" name="ossyh" id="ossyh" value="<?php echo intval(date("Y",$lists['current_time']))?>">
							</div>
						</div>
						<?php }
						if($configClass['using_cart'] == 1 && $deviceType != "mobile" && $deviceType != "tablet")
						{
						?>
						<div class="clearfix" style="height:10px;"></div>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv cartdivbox">
									<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
										<?php
										if($configClass['disable_payments'] == 1)
										{
										?>
										<div style="float:left;margin-right:5px;">
											<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png" />
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
													OsAppscheduleAjax::cart($userdata,$lists['vid'],$lists['category'],$lists['employee_id'],$lists['date_from'],$lists['date_to']);
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
						<?php } ?>
						<div class="clearfix"></div>
					</div>
				<?php
				}					
				?>
            <?php 
			}
			else
			{
                $secondDiv = $mapClass['span12'];
            }
            ?>
            <div class="<?php echo $secondDiv;?>" id="maindivright">
                <div id="maincontentdiv">
					<?php
					HTML_OsAppscheduleForm::showCheckoutFormHTML($lists,$fields,$profile);
					?>
				</div>
				<div  style="display:none;">
					<?php
					echo JHTML::_('calendar','', 'calendarvl', 'calendarvl', '%Y-%m-%d', array('class'=>'input-medium', 'size'=>'19',  'maxlength'=>'19','style'=>'width:80px;'));
					?>
				</div>
            </div>
			<?php
			if($configClass['using_cart'] == 1 || $show_calendar == 1 || count($lists['selected_dates']) > 0)
            {
				$secondDiv = $mapClass['span8'];
				if($configClass['calendar_position'] == 1)
				{
				?>
					<div class="<?php echo $mapClass['span4'];?>" id="calendardivleft">
						<?php
						if(count($lists['selected_dates']) > 0 && $configClass['show_calendar_box'] != 2)
						{
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
						elseif((!OSBHelper::isTheSameDate($lists['date_from'],$lists['date_to'])) && $configClass['show_calendar_box'] != 2)
						{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php
								HelperOSappscheduleCalendar::initCalendarForSeveralYear(intval(date("Y",HelperOSappscheduleCommon::getRealTime())),$lists['category'],$lists['employee_id'],$lists['vid'], $lists['sid'],$lists['date_from'],$lists['date_to']);
								?>
								<input type="hidden" name="ossmh" id="ossmh" value="<?php echo intval(date("m",$lists['current_time']))?>" />
								<input type="hidden" name="ossyh" id="ossyh" value="<?php echo intval(date("Y",$lists['current_time']))?>" />
							</div>
						</div>
						<?php }
						if($configClass['using_cart'] == 1 && $deviceType != "mobile" && $deviceType != "tablet")
						{
						?>
						<div class="clearfix" style="height:10px;"></div>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv cartdivbox">
									<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
										<?php
										if($configClass['disable_payments'] == 1)
										{
										?>
											<div style="float:left;margin-right:5px;">
												<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/arttocart.png" />
											</div>
											<div style="float:left;padding-top:4px;">
												<?php echo JText::_('OS_CART')?>
											</div>
										<?php
										}
										else
										{
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
													OsAppscheduleAjax::cart($userdata,$lists['vid'],$lists['category'],$lists['employee_id'],$lists['date_from'],$lists['date_to']);
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
						<?php } ?>
						<div class="clearfix"></div>
					</div>
				<?php
				}					
				?>
            <?php 
			}				
			?>
            <div class="clearfix"></div>
            <?php
            if(($configClass['using_cart'] == 1) and (($deviceType == "mobile") or ($deviceType == "tablet")))
            {
			?>
			<div class="clearfix" style="height:10px;"></div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv cartdivbox">
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
										OsAppscheduleAjax::cart($userdata,$vid,$category_id,$employee_id,$date_from,$date_to);
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
			<?php }
				if($configClass['show_footer'] == 1){
					if($configClass['footer_content'] != ""){
						?>
						<div class="osbfootercontent">
							<?php echo $configClass['footer_content'];?>
						</div>
						<?php
					}
				}
				?>
		</div>
		<input type="hidden" name="option" value="com_osservicesbooking" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="month"  id="month" value="<?php echo intval(date("m",$lists['current_time']))?>" />
		<input type="hidden" name="year"  id="year" value="<?php echo date("Y",$lists['current_time'])?>" />
		<input type="hidden" name="day"  id="day" value="<?php echo intval(date("d",$lists['current_time']));?>" />
		<input type="hidden" name="select_day" id="select_day" value="<?php echo $lists['day'];?>" />
		<input type="hidden" name="select_month" id="select_month" value="<?php echo $lists['month'];?>" />
		<input type="hidden" name="select_year" id="select_year" value="<?php echo $lists['year'];?>" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JURI::root()?>"  />
		<input type="hidden" name="order_id" id="order_id" value="" />
		<input type="hidden" name="current_date" id="current_date" value=""  />
		<input type="hidden" name="use_captcha" id="use_captcha" value="<?php echo $configClass['value_sch_include_captcha'];?>" />
		<input type="hidden" name="category_id" id="category_id" value="<?php echo $jinput->getInt('category_id',0)?>" />
		<input type="hidden" name="employee_id" id="employee_id" value="<?php echo $jinput->getInt('employee_id',0)?>" />
		<input type="hidden" name="vid" id="vid" value="<?php echo $jinput->getInt('vid',0)?>" />
		<input type="hidden" name="selected_item" id="selected_item" value="" />
		<input type="hidden" name="sid" id="sid" value="<?php echo $jinput->getInt('sid',0);?>" />
		<input type="hidden" name="eid" id="eid" value="" />
		<input type="hidden" name="current_link" id="current_link" value="<?php echo $configClass['current_link']?>" />
		<input type="hidden" name="calendar_normal_style" id="calendar_normal_style" value="<?php echo $configClass['calendar_normal_style'];?>" />
		<input type="hidden" name="calendar_currentdate_style" id="calendar_currentdate_style" value="<?php echo $configClass['calendar_currentdate_style'];?>" />
		<input type="hidden" name="calendar_activate_style" id="calendar_activate_style" value="<?php echo $configClass['calendar_activate_style'];?>" />
		<input type="hidden" name="booked_timeslot_background" id="booked_timeslot_background" value="<?php echo ($configClass['booked_timeslot_background'] != '') ? $configClass['booked_timeslot_background']:'red';?>" />
		<input type="hidden" name="use_js_popup" id="use_js_popup" value="<?php echo $configClass['use_js_popup'];?>" />
		<input type="hidden" name="using_cart" id="using_cart" value="<?php echo $configClass['using_cart'];?>" />
		<input type="hidden" name="date_from" id="date_from" value="<?php echo $lists['date_from'];?>" />
		<input type="hidden" name="date_to" id="date_to" value="<?php echo $lists['date_to'];?>" />
        <input type="hidden" name="temp_item" id="temp_item" value="" />
		<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
		<input type="hidden" name="tab_fields" id="tab_fields" value="<?php echo HelperOSappscheduleCommon::getServicesAndEmployees($lists['services'],$lists['year'],$lists['month'],$lists['day'],$lists['category'],$lists['employee_id'],$lists['vid'],$lists['sid'],$lists['employee_id']);?>" />
		<div  id="divtemp" style="width:1px;height:1px;"></div>
		<script language="javascript">
		<?php
			os_payments::writeJavascriptObjects();
		?>
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
		
		 function checkCoupon(){
			var coupon_code = document.getElementById('coupon_code');
			if(coupon_code.value == ""){
				alert("<?php echo JText::_('OS_PLEASE_ENTER_COUPON_CODE');?>");
			}else{
				checkCouponCodeAjax(coupon_code.value,"<?php echo JURI::root();?>");
			}
		}
		
		var screenWidth = jQuery(window).width();
		if(screenWidth < 350){
			jQuery(".buttonpadding10").removeClass("buttonpadding10").addClass("buttonpadding5");
		}else{
			jQuery(".buttonpadding5").removeClass("buttonpadding5").addClass("buttonpadding10");
			if(document.getElementById('calendardivleft') != null){
				var leftwidth = jQuery("#calendardivleft").width();
				if(leftwidth > 250){
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span5']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span4']?>");
					jQuery("#maindivright").removeClass("<?php echo $mapClass['span7']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span8']?>");
				}else if(leftwidth < 210){
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span5']?>").removeClass("<?php echo $mapClass['span4']?>").addClass("<?php echo $mapClass['span6']?>");
					jQuery("#maindivright").removeClass("<?php echo $mapClass['span7']?>").removeClass("<?php echo $mapClass['span8']?>").addClass("<?php echo $mapClass['span6']?>");
				}else{
					jQuery("#calendardivleft").removeClass("<?php echo $mapClass['span4']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("span5");
					jQuery("#maindivright").removeClass("<?php echo $mapClass['span8']?>").removeClass("<?php echo $mapClass['span6']?>").addClass("<?php echo $mapClass['span7']?>");
				}
			}
		}
		 function changingEmployee(sid){
            var select_item = jQuery("#employeeslist_" + sid).val();
			//jQuery("#employee_id").val(select_item);
            var existing_services = jQuery("#employeeslist_ids" + sid).val();
            existing_services = existing_services.split("|");
            if(existing_services.length > 0){
                for(i=0;i<existing_services.length;i++){
                    //jQuery("#pane" + sid + '_' +  existing_services[i]).removeClass("active");
					jQuery("#pane" + sid + '_' +  existing_services[i]).css('display','none');
                }
            }
            //jQuery("#pane" + sid + '_'  +  select_item).addClass("active");
			jQuery("#pane" + sid + '_'  +  select_item).css('display','block');
        }

         function changingService(){
            var select_item = jQuery("#serviceslist").val();
			jQuery("#sid").val(select_item);
            var existing_services = jQuery("#serviceslist_ids").val();
            existing_services = existing_services.split("|");
            if(existing_services.length > 0){
                for(i=0;i<existing_services.length;i++){
                    //jQuery("#pane" + existing_services[i]).removeClass("active");
					
					jQuery("#pane" + existing_services[i]).css('display','none');
                }
            }
            //jQuery("#pane" + select_item).addClass("active");
			jQuery("#pane" + select_item).css('display','block');
        }
		</script>
		<?php
	}


	static function remainPaymentForm($lists)
	{
		global $mainframe,$mapClass,$configClass,$deviceType,$jinput;
		$order = $lists['order'];
		if(OSBHelper::isJoomla4())
		{
			$extraCss = "joomla4";
		}
		$methods = $lists['methods'];
        $stripePaymentMethod = null;
		?>
		<form method="POST" action="<?php echo JUri::root().'index.php?option=com_osservicesbooking&task=default_completeremainpayment';?>" name="appform" id="bookingForm" enctype="multipart/form-data" class="padding10 <?php echo $extraCss;?>">
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?>">
						<h2 class="orderdetailsheading">
							<?php echo JText::_('OS_MAKE_REMAIN_PAYMENT');?> #<?php echo $order->id?>
						</h2>
					</div>
				</div>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span3'];?> boldtext">
						<?php echo JText::_('OS_NAME');?>
					</div>
					<div class="<?php echo $mapClass['span8'];?>">
						<?php
						echo $order->order_name;
						?>
					</div>
				</div>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span3'];?> boldtext">
						<?php echo JText::_('OS_EMAIL');?>
					</div>
					<div class="<?php echo $mapClass['span8'];?>">
						<?php
						echo $order->order_email;
						?>
					</div>
				</div>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span3'];?> boldtext">
						<?php echo JText::_('OS_AMOUNT');?>
					</div>
					<div class="<?php echo $mapClass['span8'];?>">
						<?php
						echo OSBHelper::showMoney($lists['amount'], 1);
						?>
					</div>
				</div>
				<input type="hidden" name="nmethods" id="nmethods" value="<?php echo count((array)$methods)?>" />
				<?php
				self::showCreditCardForm($methods, $stripePaymentMethod, $lists);		
				?>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?>">
						<input type="button" class="btn btn-success" value="<?php echo JText::_('OS_SUBMIT')?>" onclick="javascript:confirmBooking();" />
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" id="card-nonce" name="nonce" />
		<input type="hidden" id="velocitySessionToken" name="velocitySessionToken" />
		<input type="hidden" id="identitytoken" name="identitytoken" />
		<input type="hidden" id="applicationprofileid" name="applicationprofileid" />
		<input type="hidden" id="merchantprofileid" name="merchantprofileid" />
		<input type="hidden" id="workflowid" name="workflowid" />
		<input type="hidden" id="live_site" name="live_site" value="<?php echo JURI::root()?>"  />
		<input type="hidden" id="order_id" name="order_id" value="<?php echo $order->id; ?>" />
		</form>
		<script type="text/javascript">
		<?php
			os_payments::writeJavascriptObjects();
		?>
        jQuery(document).ready( function(){
            if (typeof stripe !== 'undefined')
            {

                var style = {
					hidePostalCode: true,
                    base: {
                        // Add your base input styles here. For example:
                        fontSize: '16px',
                        color: "#32325d",
                    }
                };
                // Create an instance of the card Element.
                //card = elements.create('card', {hidePostalCode: true, style: style});
				card = elements.create('card', {style: style});
                // Add an instance of the card Element into the `card-element` <div>.
                card.mount('#stripe-card-element');
            }
        });

		 function confirmBooking()
		 {
			var form					=   document.appform;
			var live_site 				= 	document.getElementById('live_site');
			
			var methodpass				= 1;
			var paymentMethod 			= "";
			var x_card_num				= "";
			var x_card_code				= "";
			var card_holder_name		= "";
			var exp_month				= "";
			var exp_year				= "";
			var card_type				= "";
			<?php
			if($configClass['disable_payments'] == 1)
			{
				if (count($methods) > 0) 
				{
					if (count($methods) > 1) 
					{
					?>
						var paymentValid = false;
						var nmethods = document.getElementById('nmethods');
						var methodtemp;
						for (var i = 0 ; i < nmethods.value; i++) {
							methodtemp = document.getElementById('pmt' + i);
							if(methodtemp.checked == true){
								paymentValid = true;
								paymentMethod = methodtemp.value;
								break;
							}
						}
						if (!paymentValid) {
							alert("<?php echo JText::_('OS_REQUIRE_PAYMENT_OPTION'); ?>");
							methodpass = 0;
						}		
					<?php	
					} else {
					?>
						paymentMethod = "<?php echo $methods[0]->getName(); ?>";
					<?php	
					}				
					?>
					
					method = methods.Find(paymentMethod);	
					if ((method.getCreditCard()) && (paymentMethod != 'os_squareup')) 
					{
						var x_card_nume = document.getElementById('x_card_num');
						if (x_card_nume.value == "") 
						{
							alert("<?php echo  JText::_('OS_ENTER_CARD_NUMBER'); ?>");
							x_card_nume.focus();
							methodpass	= 0;
							return 0;
						}
						else
						{
							x_card_num	= x_card_nume.value;
						}
						
						var x_card_codee = document.getElementById('x_card_code');
						if (x_card_codee.value == "") 
						{
							alert("<?php echo JText::_('OS_ENTER_CARD_CODE'); ?>");
							x_card_codee.focus();
							methodpass	= 0;
							return 0;
						}
						else
						{
							x_card_code = x_card_codee.value;
						}
					}
					if (method.getCardHolderName() && (paymentMethod != 'os_squareup')) 
					{
						card_holder_namee = document.getElementById('card_holder_name');
						if (card_holder_namee.value == '') 
						{
							alert("<?php echo JText::_('OS_ENTER_CARD_HOLDER_NAME') ; ?>");
							card_holder_namee.focus();
							methodpass = 0;
							return 0;
						}
						else
						{
							card_holder_name = card_holder_namee.value;
						}
					}
					if(paymentMethod != 'os_squareup') 
					{
                        var exp_year  = jQuery('#exp_year').val();
                        var exp_month = jQuery('#exp_month').val();
                        var card_type = jQuery('#card_type').val();
                    }
				<?php
				}
			}
			?>


			if(methodpass == 1)
			{
				//check to see if you are using
				if(paymentMethod == "os_stripe")
				{
					if (typeof stripe !== 'undefined' && paymentMethod.indexOf('os_stripe') == 0 && jQuery('#stripe-card-form').is(":visible"))
					{
						stripe.createToken(card).then( function(result) {
							if (result.error) {
								// Inform the customer that there was an error.
								//var errorElement = document.getElementById('card-errors');
								//errorElement.textContent = result.error.message;
								alert(result.error.message);
								form.find('#btn-submit').prop('disabled', false);
							}
							else
							{
								// Send the token to your server.
								stripeTokenHandler(result.token);
							}
						});

						return false;
					}
					else if (typeof stripePublicKey !== 'undefined' && paymentMethod.indexOf('os_stripe') == 0 && $('#tr_card_number').is(':visible'))
					{
						Stripe.card.createToken({
							number: jQuery('#x_card_num').val(),
							cvc: jQuery('#x_card_code').val(),
							exp_month: jQuery('#exp_month').val(),
							exp_year: jQuery('#exp_year').val(),
							name: jQuery('#card_holder_name').val()
						}, stripeResponseHandler);
					}
				}
				else if(paymentMethod == "os_squareup")
				{
					sqPaymentForm.requestCardNonce();
				}
				else if(paymentMethod == "os_velocity")
				{
					/*
					var applicationprofileid			= jQuery('#applicationprofileid').val();
					var merchantprofileid				= jQuery('#merchantprofileid').val();
					var workflowid						= jQuery('#workflowid').val();
					var sessionToken					= jQuery('#velocitySessionToken').val();
					var exp_year						= jQuery("#exp_year").val();
					var exp_month						= jQuery("#exp_month").val();
					if(exp_year.length == 4)
					{
						exp_year = exp_year.substring(2,4);
					}
					if(exp_month.length == 1)
					{
						exp_month = "0" + exp_month;
					}
					var card = {
						CardholderName: jQuery("#card_holder_name").val(), cardtype: jQuery("#card_type").val(), number: jQuery("#x_card_num").val(), 
						cvc: jQuery("#x_card_code").val(), expMonth: exp_month, expYear: exp_year
					};
							 
					var address = {
						Street: jQuery("#order_address").val(),
						City: jQuery("#order_city").val(),
						StateProvince: jQuery("#order_state").val(),
						Phone: jQuery("#order_phone").val(),
						PostalCode: jQuery("#order_zip").val()
					};
					Velocity.tokenizeForm(sessionToken, card, address, applicationprofileid, merchantprofileid, workflowid, responseHandler);
					*/
				}
				else
				{
					var answer = confirm('<?php echo JText::_("OS_ARE_YOU_SURE_TO_SUBMIT_THE_CHECKOUT_FORM")?>');
					if(answer)
					{
						form.submit();
					}
				}
			}
		}

         function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var $form = jQuery('#bookingForm');
           
            // Insert the token into the form so it gets submitted to the server
            if (token.error)
            {
                // Show the errors on the form
                alert(response.error.message);
            }
            else
            {
                $form.append(jQuery('<input type="hidden" name="stripeToken" />').val(token.id));
                $form.submit();
            }
        }

		 function responseHandler(result) 
		{
			var $form = jQuery('#bookingForm');
			var returnMsg = "";
			if (result['code'] == 0) 
			{
				// Request was successful. Insert hidden field into the form before submitting.
				// Continue to submit the form to the action, where we will read the decode and extract POST data.
				$form.append(jQuery('<input type="hidden" name="TransactionToken" />').val(result.text));
				$form.submit();
			}
			else 
			{
				for (var i in result) 
				{
					returnMsg += result[i];
				}
				alert(returnMsg);
				return false;
			}
		}

		 function stripeResponseHandler(status, response) 
		{
			var $form = jQuery('#bookingForm');
			if (response.error) 
			{
				// Show the errors on the form
				alert(response.error.message);
			} 
			else 
			{
				// token contains id, last4, and card type
				var token = response.id;
				// Insert the token into the form so it gets submitted to the server
				$form.append(jQuery('<input type="hidden" name="stripeToken" />').val(token));
				$form.submit();
			}
		};
		</script>
		<?php
	}
}
?>