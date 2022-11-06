<?php
/*------------------------------------------------------------------------
# orders.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HTML_OSappscheduleOrders{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function orders_list($option,$rows,$pageNav,$lists)
	{
		global $mainframe,$_jversion,$configClass, $mapClass;
		$rowFluidClass = $mapClass['row-fluid'];
		$span12Class   = $mapClass['span12'];
		$span10Class   = $mapClass['span10'];
		$span2Class	   = $mapClass['span2'];

		JHtml::_('behavior.multiselect');
		JToolBarHelper::title(JText::_('OS_MANAGE_ORDERS'),'list');
		JToolBarHelper::addNew('orders_addnew');
		if(! OSBHelper::isJoomla4())
		{
			JToolBarHelper::custom('orders_sendnotify','envelope','envelope',JText::_('OS_SEND_NOTIFY_EMAIL'));
			JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'orders_remove');
			JtoolBarHelper::custom('orders_export','download.png','download.png',JText::_('OS_EXPORT_SELECTED_ORDERS'),true);
			JtoolBarHelper::custom('orders_exportcsv','download.png','download.png',JText::_('OS_EXPORT_CSV'),false);
			JtoolBarHelper::custom('orders_exportpdf','download.png','download.png',JText::_('OS_EXPORT_PDF'),false);
			if($configClass['activate_invoice_feature'] == 1)
			{
				JtoolBarHelper::custom('orders_dowloadInvoice','download.png','download.png',JText::_('OS_EXPORT_INVOICE'),true);
			}
			if($configClass['value_sch_reminder_enable'] == 1)
			{
				JtoolBarHelper::custom('orders_disablereminders','delete','delete',JText::_('OS_DISABLE_REMINDER'),true);
			}
			if($configClass['disable_payments'] == 1)
			{
				JtoolBarHelper::custom('orders_sendpaymentrequest','envelope','envelope',JText::_('OS_SEND_REQUEST_PAYMENT'),true);
			}
			
		}
		else
		{
			$toolbar = Toolbar::getInstance('toolbar');
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
			$childBar->standardButton('orders_detail')
					->text('OS_EDIT')
				    ->icon('fa fa-edit')
					->task('orders_detail')
					->listCheck(true);
			$childBar->standardButton('orders_sendnotify')
					->text('OS_SEND_NOTIFY_EMAIL')
				    ->icon('fa fa-envelope')
					->task('orders_sendnotify')
					->listCheck(true);
			$childBar->delete('orders_remove')
					->message('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS')
					->task('orders_remove')
					->listCheck(true);
			$childBar->standardButton('orders_export')
					->text('OS_EXPORT_SELECTED_ORDERS')
				    ->icon('fa fa-download')
					->task('orders_export')
					->listCheck(true);

			JtoolBarHelper::custom('orders_exportcsv','download.png','download.png',JText::_('OS_EXPORT_CSV'),false);
			JtoolBarHelper::custom('orders_exportpdf','download.png','download.png',JText::_('OS_EXPORT_PDF'),false);
			if($configClass['activate_invoice_feature'] == 1)
			{
				$childBar->standardButton('orders_dowloadInvoice')
						->text('OS_EXPORT_INVOICE')
						->icon('fa fa-download')
						->task('orders_dowloadInvoice')
						->listCheck(false);
			}
			if($configClass['value_sch_reminder_enable'] == 1)
			{
				$childBar->standardButton('orders_disablereminders')
						->text('OS_DISABLE_REMINDER')
						->icon('icon-delete')
						->task('orders_disablereminders')
						->listCheck(true);
			}
			if($configClass['disable_payments'] == 1)
			{
				$childBar->standardButton('orders_sendpaymentrequest')
						->text('OS_SEND_REQUEST_PAYMENT')
						->icon('fa fa-envelope')
						->task('orders_sendpaymentrequest')
						->listCheck(true);
			}
		}
		JToolbarHelper::custom('calendar_employee','calendar.png', 'calendar.png', JText::_('OS_CALENDAR'), false);
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		if($lists['show_form'] == 1){
            $class = "btn-primary";
            $display = "block";
        }else{
            $class ="";
            $display = "none";
        }
		if(!OSBHelper::isJoomla4())
		{
			JHtml::_('jquery.framework');
			JHtml::_('script', 'jui/jquery.searchtools.min.js', array('version' => 'auto', 'relative' => true));
			JHtml::_('stylesheet', 'jui/jquery.searchtools.css', array('version' => 'auto', 'relative' => true));
		}
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking&task=orders_list" name="adminForm" id="adminForm">
			<input type="hidden" name="open_search_from" id="open_search_from" value="<?php echo $lists['show_form'];?>" />
			<div class="js-stools clearfix">
				<div class="<?php echo $rowFluidClass; ?>">
					<div class="<?php echo $span10Class; ?> js-stools-container-bar">
						<div class="btn-wrapper btn-group">
							<div class="input-group input-append">
								<input placeholder="<?php echo Jtext::_('OS_SEARCH');?>" type="text" id="keyword" name="keyword" value="<?php echo  $lists['keyword']; ?>" class="input-medium form-control" />
								<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Jtext::_('OS_SEARCH');?>">
									<i class="icon-search"></i>
								</button>
							</div>
						</div>
						<?php
						if(OSBHelper::isJoomla4())
						{
							?>
							<div class="btn-group">
								<button type="button" id="filter_search_button" class="btn btn-primary js-stools-btn-filter <?php echo $class;?>" title="Filter the list items">
									<?php echo JText::_('JFILTER_OPTIONS'); ?>
									<span class="icon-angle-down" aria-hidden="true"></span>
								</button>
								<button type="button" id="clear_search_button" class="btn btn-warning js-stools-btn-clear" title="Clear">
									<?php echo Jtext::_('OS_CLEAR');?>
								</button>
							</div>
							<?php
						}
						else
						{
						?>
						<div class="btn-wrapper hidden-phone">
							<button type="button" id="filter_search_button" class="btn btn-success hasTooltip js-stools-btn-filter <?php echo $class;?>" title="Filter the list items">
								<?php echo Jtext::_('OS_SEARCH_TOOLS');?> <i class="caret"></i>
							</button>
						</div>
						<div class="btn-wrapper hidden-phone">
							<button type="button" id="clear_search_button" class="btn btn-warning hasTooltip js-stools-btn-clear" title="Clear">
								<?php echo Jtext::_('OS_CLEAR');?>
							</button>
						</div>
						<?php
						}	
						?>
					</div>
					<div class="<?php echo $span2Class; ?> js-stools-container-list hidden-phone hidden-tablet shown">
						<div class="js-stools-field-list">
							<?php
							echo $pageNav->getLimitBox();
							?>
						</div>
					</div>
				</div>
				
				<div class="js-stools-container-filters hidden-phone clearfix shown" ID="search_param_div" style="display:<?php echo $display;?>;">
					<div class="js-stools-field-filter">
						<?php echo $lists['filter_venue']; ?>
					</div>
					<div class="js-stools-field-filter">
						<?php echo $lists['filter_service']; ?>
					</div>
					<div class="js-stools-field-filter">
						<?php echo $lists['filter_employee']; ?>
					</div>
					<div class="js-stools-field-filter">
						<?php echo $lists['filter_status']; ?>
					</div>
					<div class="js-stools-field-filter">
						<?php echo JHtml::_('calendar',$lists['filter_date_from'],'filter_date_from','filter_date_from','%Y-%m-%d',array('placeholder' => JText::_('OS_FROM'),'onchange' => '', 'class' => 'input-small ishort'));?>
					</div>
					<div class="js-stools-field-filter">
						<?php echo JHtml::_('calendar',$lists['filter_date_to'],'filter_date_to','filter_date_to','%Y-%m-%d',array('placeholder' => JText::_('OS_TO'),'', 'class' => 'input-small ishort'))?>
					</div>
					<div class="js-stools-field-filter">
						<?php echo $lists['cities']?>
					</div>
					<button class="btn hasTooltip btn-primary" title="" type="button" onClick="javascript:submitOrdersForm();"  data-original-title="<?php echo JText::_('OS_SEARCH');?>">
						<i class="icon-search"></i>
					</button>
				</div>
			</div>

			<?php 
			$optionArr = array();
			$statusArr = array(JText::_('OS_PENDING'),JText::_('OS_COMPLETED'),JText::_('OS_CANCELED'),JText::_('OS_ATTENDED'),JText::_('OS_TIMEOUT'),JText::_('OS_DECLINED'),JText::_('OS_REFUNDED'));
			$statusVarriableCode = array('P','S','C','A','T','D','R');
			for($j=0;$j<count($statusArr);$j++){
				$optionArr[] = JHtml::_('select.option',$statusVarriableCode[$j],$statusArr[$j]);				
			}
			if(count($rows) > 0){
			?>
			<table class="adminlist table table-striped" width="100%">
				<thead>
					<tr>
						<th width="2%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="3%">
							<?php echo JHTML::_('grid.sort',   JText::_('ID'), 'a.id', @$lists['order_Dir'], @$lists['order'] ,'orders_list'); ?>
						</th>
						<th width="10%">
							<?php echo JText::_('OS_CUSTOMER_DETAILS');?>
						</th>
						<th width="25%">
							<?php echo JText::_('OS_SERVICES');?>
						</th>
						<?php
						if($configClass['disable_payment'] == 0){
						?>
							<th width="15%">
								<?php echo JText::_('OS_ORDER_PAYMENT');?>
							</th>
						<?php
						} 
						?>
						<th width="18%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_STATUS'), 'a.order_status', @$lists['order_Dir'], @$lists['order'] ,'orders_list'); ?>
						</th>
						<?php
						if(!OSBHelper::isJoomla4())
						{
						?>
						<th width="10%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_DATE'), 'a.order_date', @$lists['order_Dir'], @$lists['order'],'orders_list' ); ?>
						</th>
						<?php } ?>
						<th width="8%" style="text-align:center;">
							<?php echo JText::_('OS_SENDMAIL');?>
						</th>
					</tr>
				</thead>
				<?php
				if($configClass['disable_payment'] == 0){
					$cols = 13;
				}else{
					$cols = 12;
				}
				?>
				<tfoot>
					<tr>
						<td width="100%" colspan="<?php echo $cols?>" style="text-align:center;">
							<?php
								echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$k = 0;
				$db = JFactory::getDbo();
				$config = new JConfig();
				$offset = $config->offset;
				date_default_timezone_set($offset);	
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
					$row = $rows[$i];
					$checked = JHtml::_('grid.id', $i, $row->id);
					$link 		= JRoute::_( 'index.php?option=com_osservicesbooking&task=orders_detail&cid[]='. $row->id );
					
					$db->setQuery("SELECT * FROM #__app_sch_order_items WHERE order_id = '$row->id'");
					$items = $db->loadObjectList();
					$servicesArr = array();
					for($j=0;$j<count($items);$j++){
						$item = $items[$j];
						$db->setQuery("Select * from #__app_sch_services where id = '$item->sid'");
						$s = $db->loadObject();
						$service_name = $s->service_name;
						$service_time_type = $s->service_time_type;
						
						$db->setQuery("Select id,employee_name from #__app_sch_employee where id = '$item->eid'");
						$employee_name = $db->loadObject();
						$employee_name = $employee_name->employee_name;
						$temp = $j + 1;
						$temp .= ". ".$service_name." [".date($configClass['date_format'],$item->start_time)." ".date($configClass['time_format'],$item->start_time)." - ".date($configClass['date_format'],$item->end_time)." ".date($configClass['time_format'],$item->end_time)."] ".JText::_('OS_EMPLOYEE').": ".$employee_name."";
						if($service_time_type == 1){
							$temp .= ". ".JText::_('OS_NUMBER_SLOT').": ".$item->nslots;
						}
						$servicesArr[] = $temp;
						
					}
					$service = implode("<BR />",$servicesArr);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $checked; ?></td>
						<td align="left"><a href="<?php echo $link; ?>"><?php echo str_pad($row->id,6,'000000',0); ?></a></td>
						<td align="left"><a href="<?php echo $link; ?>"><?php echo $row->order_name; ?></a>
							<BR />
							<a href="mailto:<?php echo $row->order_email?>" target="_blank"><?php echo $row->order_email?></a></td>
						<td align="left" class="orderServices"><?php echo $service?></td>
						<?php
						if($configClass['disable_payment'] == 0){
						?>
							<td align="left" style="font-size:11px;">
							<?php
								echo JText::_('OS_TOTAL').": ".OSBHelper::showMoney($row->order_total,1);
							?>
							<br />
							<?php
								echo JText::_('OS_DISCOUNT').": ".OSBHelper::showMoney($row->order_discount,1);
							?>
							<br />
							<?php
							echo JText::_('OS_GROSS_AMOUNT').": ".OSBHelper::showMoney($row->order_final_cost,1);
							?>
							<br />
							<?php
								$paidAmount = 0;
								if($row->order_payment == "os_offline")
								{
									if($row->deposit_paid == 1)
									{
										$paidAmount = $row->order_upfront;
									}
								}
								elseif($row->deposit_paid == 1)
								{
									$paidAmount = $row->order_upfront;
								}

								if($row->make_remain_payment == 1)
								{
									$paidAmount += $row->remain_payment_amount;
								}
								echo JText::_('OS_PAID_AMOUNT').": ".OSBHelper::showMoney($paidAmount,1);
							?>
							<br />
							<?php 
							$order_payment = $row->order_payment;
							if($order_payment != "")
							{
								echo Jtext::_('OS_PAYMENT')." <strong>".JText::_(os_payments::loadPaymentMethod($order_payment)->title)."</strong>";
                                if(OSBHelper::canRefundOrder($row))
                                {
                                    ?>
                                    <BR />
                                    <a href="javascript:refundOrder(<?php echo $row->id;?>)"" title="<?php echo JText::_('OS_CLICK_HERE_TO_REFUND_THE_ORDER');?>"><?php echo JText::_('OS_REFUND_ORDER');?></a>
                                    <?php
                                }
                                if($row->refunded == 1)
                                {
                                    ?>
                                    <BR />
                                    <span style="color:red;font-weight: bold;"><?php echo JText::_('OS_REFUNDED');?></span>
                                    <?php
                                }
							}
						?></td>
						<?php
						}
						?>
						<td class="order_update">
							<div id="div_orderstatus<?php echo $row->id;?>">
								<?php 
								$extraCss = "";
								if(OSBHelper::isJoomla4())
								{
									$extraCss = 'style="width:130px;"';
								}
								echo "<span style='color:gray;'>".JText::_('OS_CURRENT_STATUS').": <strong>".OSBHelper::orderStatus(0,$row->order_status)."</strong></span>";
								echo "<BR />";
								echo "<span style='color:gray;font-size:11px;'>".JText::_('OS_CHANGE_STATUS')."</span>";
								echo JHtml::_('select.genericlist',$optionArr,'orderstatus'.$row->id,'class="'.$mapClass['input-small'].' form-select" '.$extraCss,'value','text',$row->order_status);
								?>
								<a href="javascript:updateOrderStatusAjax(<?php echo $row->id;?>,'<?php echo JUri::root();?>')" title="<?php echo JText::_('OS_UPDATE_ORDER_STATUS');?>" id="orderstatus<?php echo $row->id;?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
									  <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5H3z"/>
									  <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
									</svg>
								</a>
							</div>	
						</td>
						<?php
						if(!OSBHelper::isJoomla4())
						{
						?>
						<td align="center" style="text-align:center;"><span style="font-size:11px;"><?php
						echo date($configClass["date_time_format"],strtotime($row->order_date));?></span></td>
						<?php } ?>
						<td style="text-align:center;">
							<?php
							if($row->send_email == 1)
							{
								?>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="green" class="bi bi-check2-circle" viewBox="0 0 16 16">
								  <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
								  <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
								</svg>
								<?php
							}else{
								?>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
								  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
								  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
								</svg>
								<?php
							}
							?>
						</td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<?php 
			}else{
				?>
				<div class="alert alert-no-items"><?php echo Jtext::_('OS_NO_MATCHING_RESULTS');?></div>
				<?php 
			}?>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" value="orders_list" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
			<input type="hidden" name="live_site" id="live_site" value="<?php echo Juri::root();?>" />
			<input type="hidden" name="current_order_id" id="current_order_id" value="" />
		</form>
        <script type="text/javascript">
            jQuery( "#filter_search_button" ).click( function() {
                var open_search_from = jQuery("#open_search_from").val();
                if(open_search_from == 0){
                    jQuery('#search_param_div').slideDown('slow');
                    jQuery("#open_search_from").val("1");
                    jQuery("#filter_search_button").addClass('btn-primary');
                }else{
                    jQuery('#search_param_div').slideUp('slow');
                    jQuery("#open_search_from").val("0");
                    jQuery("#filter_search_button").removeClass('btn-primary');
                }
            });
            jQuery( "#clear_search_button" ).click( function() {
                jQuery("#filter_venue").val("0");
                jQuery("#filter_service").val("0");
                jQuery("#filter_employee").val("0");
                jQuery("#filter_status").val("");
                jQuery("#filter_date_from").val("");
                jQuery("#filter_date_to").val("");
                jQuery("#keyword").val("");
                document.getElementById('adminForm').submit();
            });

            function refundOrder(id)
            {
                var answer = confirm("<?php echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_REFUND_THE_ORDER')?>");
                if(answer == 1)
                {
                    location.href = "index.php?option=com_osservicesbooking&task=orders_refund&comeback=0&id=" + id;
                }
            }
			function submitOrdersForm()
			{
				document.adminForm.task.value = "orders_list";
				document.adminForm.submit();
			}
        </script>
		<?php
	}

	static function manageCustomers($rows, $pageNav, $lists)
	{
		global $mainframe,$_jversion,$configClass, $mapClass;
		$rowFluidClass = $mapClass['row-fluid'];
		$span12Class   = $mapClass['span12'];
		$span10Class   = $mapClass['span10'];
		$span2Class	   = $mapClass['span2'];
		JToolBarHelper::title(JText::_('OS_CUSTOMERS'),'list');
		JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'orders_removecustomer');
		JtoolBarHelper::custom('orders_exportcustomers','download.png','download.png',JText::_('OS_EXPORT_CSV'),false);
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);

		if($lists['show_form'] == 1)
		{
            $class = "btn-primary";
            $display = "block";
        }
		else
		{
            $class ="";
            $display = "none";
        }
		if(!OSBHelper::isJoomla4())
		{
			JHtml::_('jquery.framework');
			JHtml::_('script', 'jui/jquery.searchtools.min.js', array('version' => 'auto', 'relative' => true));
			JHtml::_('stylesheet', 'jui/jquery.searchtools.css', array('version' => 'auto', 'relative' => true));
		}
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking&task=orders_customers" name="adminForm" id="adminForm">
			<input type="hidden" name="open_search_from" id="open_search_from" value="<?php echo $lists['show_form'];?>" />
			<div class="js-stools clearfix">
				<div class="<?php echo $rowFluidClass; ?>">
					<div class="<?php echo $span10Class; ?> js-stools-container-bar">
						<div class="btn-wrapper btn-group">
							<div class="input-group input-append">
								<input placeholder="<?php echo Jtext::_('OS_SEARCH');?>" type="text" id="keyword" name="keyword" value="<?php echo  $lists['keyword']; ?>" class="input-medium form-control" />
								<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Jtext::_('OS_SEARCH');?>">
									<i class="icon-search"></i>
								</button>
							</div>
						</div>
						<?php
						if(OSBHelper::isJoomla4())
						{
							?>
							<div class="btn-group">
								<button type="button" id="filter_search_button" class="btn btn-primary js-stools-btn-filter <?php echo $class;?>" title="Filter the list items">
									<?php echo JText::_('JFILTER_OPTIONS'); ?>
									<span class="icon-angle-down" aria-hidden="true"></span>
								</button>
								<button type="button" id="clear_search_button" class="btn btn-warning js-stools-btn-clear" title="Clear">
									<?php echo Jtext::_('OS_CLEAR');?>
								</button>
							</div>
							<?php
						}
						else
						{
						?>
						<div class="btn-wrapper hidden-phone">
							<button type="button" id="filter_search_button" class="btn btn-success hasTooltip js-stools-btn-filter <?php echo $class;?>" title="Filter the list items">
								<?php echo Jtext::_('OS_SEARCH_TOOLS');?> <i class="caret"></i>
							</button>
						</div>
						<div class="btn-wrapper hidden-phone">
							<button type="button" id="clear_search_button" class="btn btn-warning hasTooltip js-stools-btn-clear" title="Clear">
								<?php echo Jtext::_('OS_CLEAR');?>
							</button>
						</div>
						<?php
						}	
						?>
					</div>
					<div class="<?php echo $span2Class; ?> js-stools-container-list hidden-phone hidden-tablet shown">
						<div class="js-stools-field-list">
							<?php
							echo $pageNav->getLimitBox();
							?>
						</div>
					</div>
				</div>
				
				<div class="js-stools-container-filters hidden-phone clearfix shown" ID="search_param_div" style="display:<?php echo $display;?>;">
					<div class="js-stools-field-filter">
						<?php echo $lists['type']; ?>
					</div>
					<button class="btn hasTooltip btn-primary" title="" type="button" onClick="javascript:submitOrdersForm();"  data-original-title="<?php echo JText::_('OS_SEARCH');?>">
						<i class="icon-search"></i>
					</button>
				</div>
			</div>
			<table class="adminlist table table-striped" width="100%">
				<thead>
					<tr>
						<th width="2%" style="text-align:center;">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="20%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_CUSTOMER_NAME'), 'order_name', @$lists['order_Dir'], @$lists['order'] ,'orders_customers'); ?>
						</th>
						<th width="25%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_CUSTOMER_CONTACT'), 'order_email', @$lists['order_Dir'], @$lists['order'] ,'orders_customers'); ?>
						</th>
						<th width="20%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_CUSTOMER_ADDRESS'), 'order_address', @$lists['order_Dir'], @$lists['order'] ,'orders_customers'); ?>
						</th>
						<th width="10%">
							<?php echo JText::_('OS_APPOINTMENTS');?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="6" style="text-align:center;">
							<?php
								echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$k = 0;
				$addressArr = ['order_address','order_zip','order_city','order_state','order_country'];
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
					$row = $rows[$i];
					$checked = JHtml::_('grid.id', $i, $row->id);
					$link = 'index.php?option=com_osservicesbooking&task=orders_customerdetails&id='.$row->id;
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $checked; ?></td>
						<td align="left">
							<a href='<?php echo $link?>' title='<?php echo $row->order_name; ?>'>
								<?php echo $row->order_name; ?>
							</a>
						</td>
						<td align="left">
							<?php
							echo $row->order_email;
							?>
							<?php
							if($row->order_phone != "")
							{
							?>
								<BR />
							<?php
								echo $row->order_phone;
							}
							?>
						</td>
						<td align="left">
							<?php
							$tmp = [];
							foreach($addressArr as $address)
							{
								if($row->$address != '')
								{
									$tmp[] = $row->$address;
								}
							}
							if(count($tmp))
							{
								echo implode(", ", $tmp);
							}
							?>
						</td>
						<td style="text-align:center;">
							<?php
							echo OSBHelper::countAppointments($row->order_name, $row->order_email);
							?>
						</td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" value="orders_customers" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
			<input type="hidden" name="live_site" id="live_site" value="<?php echo Juri::root();?>" />
			<input type="hidden" name="current_order_id" id="current_order_id" value="" />
		</form>
		<script type="text/javascript">
		jQuery( "#filter_search_button" ).click( function() {
			var open_search_from = jQuery("#open_search_from").val();
			if(open_search_from == 0){
				jQuery('#search_param_div').slideDown('slow');
				jQuery("#open_search_from").val("1");
				jQuery("#filter_search_button").addClass('btn-primary');
			}else{
				jQuery('#search_param_div').slideUp('slow');
				jQuery("#open_search_from").val("0");
				jQuery("#filter_search_button").removeClass('btn-primary');
			}
		});
		jQuery( "#clear_search_button" ).click( function() {
			jQuery("#type").val("0");
			jQuery("#keyword").val("");
			document.getElementById('adminForm').submit();
		});
		</script>
		<?php
	}
	
	
	/**
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function orders_detail($option,$row,$rows,$pageNav,$fields,$lists)
	{
		global $mainframe, $_jversion,$configClass,$jinput, $mapClass;
		$rowFluidClass	= $mapClass['row-fluid'];
		$span12Class    = $mapClass['span12'];
		$controlGroupClass = $mapClass['control-group'];
		$controlLabelClass = $mapClass['control-label'];
		$controlsClass	   = $mapClass['controls'];

		$config			= new JConfig();
		$offset			= $config->offset;
		date_default_timezone_set($offset);	
		$version 		= new JVersion();
		$_jversion		= $version->RELEASE;		
		$mainframe 		= JFactory::getApplication();
		$jinput->set( 'hidemainmenu', 1 );
		if ($row->id){
			$title = ' ['.JText::_('OS_EDIT').']';
		}else{
			$title = ' ['.JText::_('OS_NEW').']';
		}
		JToolBarHelper::title(JText::_('OS_ORDER_DETAIL').$title,'list');
		JToolBarHelper::save('orders_save');
		JToolBarHelper::apply('orders_apply');
		JToolBarHelper::cancel('orders_cancel');
		if(OSBHelper::canRefundOrder($row))
        {
            JToolBarHelper::custom('orders_refund','unpublish','unpublish',JText::_('OS_REFUND_ORDER'));
        }
		?>
		<?php
		if (version_compare(JVERSION, '3.5', 'ge') && file_exists(JPATH_ROOT.'/media/jui/js/fielduser.min.js'))
		{
		?>
			<script src="<?php echo JUri::root()?>media/jui/js/fielduser.min.js" type="text/javascript"></script>
		<?php } 
		if(OSBHelper::isJoomla4())
		{
			$extraClass = "osb-joomla4";
		}
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking&task=orders_detail&cid[]=<?php echo $row->id;?>" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="<?php echo $mapClass['row-fluid']; ?> <?php echo $extraClass; ?>">
			<div style="padding:20px;" class="<?php echo $mapClass['span6']; ?> form-horizontal">
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_ORDER_DETAIL')?></legend>
					<?php
					if($row->id > 0){
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<label title="<?php echo JText::_( 'OS_ORDER_NUMBER' );?>::<?php echo JText::_('OS_ORDER_NUMBER_DESC'); ?>" class="hasTip" ><?php echo JText::_("OS_ORDER_NUMBER"); ?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<span class="readonly"><?php echo str_pad($row->id,6,'000000',0); ?></span>
						</div>
					</div>
					<?php
					}
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">					
							<label title="<?php echo JText::_( 'OS_CUSTOMER') ;?>" class="hasTip"><?php echo JText::_( 'OS_CUSTOMER') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<?php 
							echo OSappscheduleOrders::getUserInput($row->user_id,$row->id);
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">					
							<label title="<?php echo JText::_( 'OS_NAME') ;?>::<?php echo JText::_( 'OS_NAME_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_NAME') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $row->order_name; ?>" name="order_name" id="order_name" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_EMAIL') ;?>::<?php echo JText::_( 'OS_EMAIL_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_EMAIL') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $row->order_email; ?>" name="order_email" id="order_email" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_PHONE') ;?>::<?php echo JText::_( 'OS_PHONE_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_PHONE') ;?></label>
							</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-small']; ?>" style="width:50px;" value="<?php echo $row->dial_code; ?>" name="dial_code" />
							<input type="text" class="<?php echo $mapClass['input-small']; ?> ishort" value="<?php echo $row->order_phone; ?>" name="order_phone" id="order_phone" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_COUNTRY') ;?>::<?php echo JText::_( 'OS_COUNTRY_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_COUNTRY') ;?></label>
							</div>
						<div class="<?php echo $controlsClass;?>">
							<?php echo $lists['country'];?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_CITY') ;?>::<?php echo JText::_( 'OS_CITY_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_CITY') ;?></label>
							</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $row->order_city; ?>" name="order_city" id="order_city" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_STATE') ;?>::<?php echo JText::_( 'OS_STATE_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_STATE') ;?></label>
							</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $row->order_state; ?>" name="order_state" id="order_state" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_ZIP') ;?>::<?php echo JText::_( 'OS_ZIP_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_ZIP') ;?></label>
							</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="input-mini form-control" value="<?php echo $row->order_zip; ?>" name="order_zip" id="order_zip" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_ADDRESS') ;?>::<?php echo JText::_( 'OS_ADDRESS_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_ADDRESS') ;?></label>
							</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-large']; ?>" value="<?php echo $row->order_address; ?>" name="order_address" id="order_address" />
						</div>
					</div>
					<?php
					$db = JFactory::getDbo();
					$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1'");
					$fields = $db->loadObjectList();
					if(count($fields) > 0){
						for($i=0;$i<count($fields);$i++){
							$field = $fields[$i];
							?>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">	
									<label title="<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$row->order_lang); //$field->field_label ;?>" class="hasTip"><?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$row->order_lang); //$field->field_label;?></label>
									</div>
								<div class="<?php echo $controlsClass;?>">
									<span class="readonly" style="font-weight:normal !important;"> 
										<?php
										OsAppscheduleDefault::orderField($field,$row->id);
										?>
									</span>
								</div>
							</div>
							<?php
						}
					}
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_NOTES') ;?>" class="hasTip"><?php echo JText::_( 'Notes') ;?></label>
							</div>
						<div class="<?php echo $controlsClass;?>">
							<textarea name="notes" class="input-large form-control ilarge"><?php echo $row->order_notes; ?></textarea>
						</div>
					</div>
				</fieldset>
			</div>
			<div style="padding:20px;" class="<?php echo $mapClass['span6']; ?> form-horizontal">
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_PAYMENT')?></legend>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_PAYMENT') ;?>::<?php echo JText::_( 'OS_PAYMENT_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_PAYMENT') ;?></label>
							</div>
						<div class="<?php echo $controlsClass;?>">
							<?php 
							$order_payment = $row->order_payment;
							echo $lists['payment'];
							if($row->refunded == 1)
                            {
                                ?>
                                <span style="color:red;font-weight: bold;"><?php echo JText::_('OS_REFUNDED');?></span>
                                <?php
                            }
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<label title="<?php echo JText::_( 'OS_TOTAL') ;?>::<?php echo JText::_( 'OS_TOTAL_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_TOTAL') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->order_total; ?>" name="order_total" /> <?php echo $configClass['currency_format'];?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<label title="<?php echo JText::_( 'OS_DISCOUNT') ;?>" class="hasTip"><?php echo JText::_( 'OS_DISCOUNT') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->order_discount; ?>" name="order_discount" /> <?php echo $configClass['currency_format'];?>
						</div>
					</div>
					<?php
					if($configClass['enable_tax']==1){
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_TAX') ;?>::<?php echo JText::_( 'OS_TAX_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_TAX') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->order_tax; ?>" name="order_tax" /> <?php echo $configClass['currency_format'];?>
						</div>
					</div>
					<?php } ?>
					
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_GROSS_AMOUNT') ;?>::<?php echo JText::_( 'OS_FINAL_COST_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_GROSS_AMOUNT') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->order_final_cost; ?>" name="order_final_cost" /> <?php echo $configClass['currency_format'];?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_UPFRONT') ;?>::<?php echo JText::_( 'OR_UPFRONT_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_UPFRONT') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->order_upfront; ?>" name="order_upfront" /> <?php echo $configClass['currency_format'];?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_PAYMENT_FEE') ;?>" class="hasTip"><?php echo JText::_( 'OS_PAYMENT_FEE') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->payment_fee; ?>" name="payment_fee" /> <?php echo $configClass['currency_format'];?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">	
							<label title="<?php echo JText::_( 'OS_DATE') ;?>::<?php echo JText::_( 'OS_DATE_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_DATE') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<?php
							echo JHTML::_('calendar',$row->order_date, 'order_date', 'order_date', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'19',  'maxlength'=>'19','style'=>'width:80px;'));
							?>
							<?php echo $lists['order_date_hour'] . ' ' . $lists['order_date_minute']; ?>
						</div>
					</div>
					<?php 
					if($row->order_payment == 'os_osb_offline_creditcard' && $row->params)
					{	
						$params  = new JRegistry($row->params);
						?>
						<div class="<?php echo $controlGroupClass; ?>">
							<label class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_AUTH_CARD_NUMBER'); ?>
							</label>
							<div class="<?php echo $controlsClass;?>">
								<?php echo $params->get('card_number'); ?>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<label class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_AUTH_CARD_EXPIRY_DATE'); ?>
							</label>
							<div class="<?php echo $controlsClass;?>">
								<?php echo $params->get('exp_date'); ?>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<label class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_AUTH_CVV_CODE'); ?>
							</label>
							<div class="<?php echo $controlsClass;?>">
								<?php echo $params->get('cvv'); ?>
							</div>
						</div>
						<?php
					}
					
					$paidAmount = 0;
					if($row->order_payment == "os_offline")
					{
						if($row->deposit_paid == 1)
						{
							$paidAmount = $row->order_upfront;
						}
					}
					elseif($row->deposit_paid == 1)
					{
						$paidAmount = $row->order_upfront;
					}

					if($row->make_remain_payment == 1)
					{
						$paidAmount += $row->remain_payment_amount;
					}
					
					if($configClass['disable_payments'] == 1)
					{
						?>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<label title="<?php echo JText::_( 'OS_PAID_AMOUNT') ;?>" class="hasTip"><?php echo JText::_( 'OS_PAID_AMOUNT') ;?></label>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<span class="readonly"> <?php echo OSBHelper::showMoney($paidAmount,1); ?></span>
							</div>
						</div>
						<?php
					}
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<label title="<?php echo JText::_( 'OS_STATUS') ;?>::<?php echo JText::_( 'OS_STATUS_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_STATUS') ;?></label>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<span class="readonly"> <?php echo $row->order_status_select_list; ?></span>
						</div>
					</div>
					<?php
					if($configClass['value_sch_reminder_enable'] == 1)
					{
						?>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<label title="<?php echo JText::_( 'OS_RECEIVE_REMINDER') ;?>" class="hasTip"><?php echo JText::_( 'OS_RECEIVE_REMINDER') ;?></label>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<?php
								if($row->id == 0 || $row->receive_reminder == 1)
								{
									$checked = "checked";
								}
								else
								{
									$checked = "";
								}
								?>
								<input type="checkbox" name="receive_reminder" value="1" <?php echo $checked;?>/>
							</div>
						</div>
						<?php
					}
					?>
				</fieldset>
			</div>
		</div>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo JText::_('OS_SERVICELIST');?></legend>
				<?php
				
				if($row->id > 0){
				?>
				<div class="<?php echo $rowFluidClass;?>">
					<div class="<?php echo $span12Class; ?> form-horizontal">
						<?php echo JText::_('OS_ORDER_DETAILS');?>:
						<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=<?php echo $row->id?>&ref=<?php echo md5($row->id);?>" target="_blank" id="order_details_link">
							<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=<?php echo $row->id?>&ref=<?php echo md5($row->id);?>
						</a>
						&nbsp;
						<a href="javascript:void(0);" title="<?php echo JText::_('OS_COPY_LINK');?>" id="copy_order_details_link">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
							  <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
							  <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
							</svg>
						</a>
						<BR />
						<?php echo JText::_('OS_CANCEL_LINK');?>:
						<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=default_cancelorder&order_id=<?php echo $row->id?>&ref=<?php echo md5($row->id);?>" target="_blank" id="cancel_link">
							<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=default_cancelorder&order_id=<?php echo $row->id?>&ref=<?php echo md5($row->id);?>
						</a>
						&nbsp;
						<a href="javascript:void(0);" title="<?php echo JText::_('OS_COPY_LINK');?>" id="copy_cancel_link">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
							  <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
							  <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
							</svg>
						</a>
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
					</div>
				</div>
				<div class="<?php echo $rowFluidClass;?>">
					<div class="<?php echo $span12Class; ?> form-horizontal" style="padding-top:15px;">
						<fieldset>
							<legend><strong><?php echo JText::_('OS_SERVICELIST')?></strong></legend>
							<div class="navbar">
								<div class="navbar-inner" style="text-align:right;">
									<a href="index.php?option=com_osservicesbooking&task=orders_addservice&order_id=<?php echo $row->id?>" style="color:white;font-weight:bold;" class="btn btn-info">Add service</a>
								</div>
							</div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="20" align="left">
											<?php echo JText::_( '#' ); ?>
										</th>
										<?php
										if($configClass['use_qrcode'])
										{
											?>
											<th class="title" width="5%">
											</th>
											<?php
										}
										?>
										<th class="title" width="15%">
											<?php echo JText::_('OS_SERVICES');?>
										</th>
										<th class="title" width="15%">
											<?php echo JText::_('OS_EMPLOYEE');?>
										</th>
										<th width="7%">
											<?php echo JText::_('OS_WORKTIME_START_TIME');?>
										</th>
										<th width="7%">
											<?php echo JText::_('OS_WORKTIME_END_TIME');?>
										</th>
										<th width="7%">
											<?php echo JText::_('OS_DATE');?>
										</th>
										<th width="20%">
											<?php echo JText::_('OS_OTHER_INFORMATION');?>
										</th>
										<th width="5%">
											<?php echo JText::_('OS_CHECKED_IN');?>
										</th>
										<th width="5%">
											<?php echo JText::_('OS_EDIT');?>
										</th>
										<th width="5%">
											<?php echo JText::_('OS_REMOVE');?>
										</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<td colspan="11">
											<?php echo $pageNav->getListFooter(); ?>
										</td>
									</tr>
								</tfoot>
								<tbody>
									<?php
									$config = new JConfig();
									$offset = $config->offset;
									date_default_timezone_set($offset);
									$k = 0;
									if( count( $rows ) ) {
									for ($i=0, $n=count( $rows ); $i < $n; $i++) 
									{
										$item = &$rows[$i];
									?>
										<tr class="<?php echo "row$k"; ?>">
											<td >
												<?php echo $pageNav->getRowOffset( $i ); ?>
											</td>
											<?php
											if($configClass['use_qrcode'])
											{
												if(!file_exists(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes/item_'.$item->id.'.png'))
												{
													OSBHelper::generateQrcode($row->id);
												}
												?>
												<td style="text-align:center;">
													<img src="<?php echo JUri::root();?>media/com_osservicesbooking/qrcodes/item_<?php echo $item->id?>.png" border="0"/>
												</td>
												<?php
											}
											?>
											<td style="padding-left:10px;text-align:left;"><?php echo $item->service_name?></td>
											<td style="padding-left:10px;text-align:left;"><?php echo $item->employee_name?></td>
											<td align="center"><?php echo date($configClass['time_format'],$item->start_time); ?></td>
											<td align="center"><?php echo date($configClass['time_format'],$item->end_time); ?></td>
											<td align="center"><?php echo date($configClass['date_format'],strtotime($item->booking_date)) ; ?></td>
											<td align="left">
												<?php
												if($item->service_time_type ==1)
												{
													echo JText::_('OS_NUMBER_SLOT').": ".$item->nslots."<BR />";
												}
												$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1'");
												$fields = $db->loadObjectList();
												if(count($fields) > 0)
												{
													for($i1=0;$i1<count($fields);$i1++)
													{
														$field = $fields[$i1];
														$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$item->id' and field_id = '$field->id'");
														$count = $db->loadResult();
														if($count > 0)
														{
															if($field->field_type == 1)
															{
																$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$item->id' and field_id = '$field->id'");
																$option_id = $db->loadResult();
																$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
																$optionvalue = $db->loadObject();
																?>
																<?php echo $field->field_label;?>:
																<?php
																$field_data = $optionvalue->field_option;
																if($optionvalue->additional_price > 0){
																	$field_data.= " - ".OSBHelper::showMoney($optionvalue->additional_price,0);
																}
																echo $field_data;
																echo "<BR />";
															}
															elseif($field->field_type == 2)
															{
																$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$item->id' and field_id = '$field->id'");
																$option_ids = $db->loadObjectList();
																$fieldArr = array();
																for($j=0;$j<count($option_ids);$j++)
																{
																	$oid = $option_ids[$j];
																	$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
																	$optionvalue = $db->loadObject();
																	$field_data = $optionvalue->field_option;
																	if($optionvalue->additional_price > 0)
																	{
																		$field_data.= " - ".OSBHelper::showMoney($optionvalue->additional_price,0);
																	}
																	$fieldArr[] = $field_data;
																}
																?>
																<?php echo $field->field_label;?>:
																<?php
																echo implode(", ",$fieldArr);
																echo "<BR />";
															}
														}
													}
												}
												?>
											</td>
											<td width="10%" style="text-align:center;">
												<div id="checkin<?php echo $item->id; ?>">
												<?php
												if($item->checked_in == 1)
												{
													?>
													<a href="javascript:changeCheckinStatus(<?php echo $item->id;?>,0);" title="<?php echo JText::_('OS_CLICK_HERE_TO_CHANGE_CHECKIN_STATUS_OF_ITEM');?>">
														<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/publish.png" />
													</a>
													<?php
												}
												else
												{
													?>
													<a href="javascript:changeCheckinStatus(<?php echo $item->id;?>,1);" title="<?php echo JText::_('OS_CLICK_HERE_TO_CHANGE_CHECKIN_STATUS_OF_ITEM');?>">
														<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/unpublish.png" />
													</a>
													<?php
												}
												?>
												</div>
											</td>
											<td width="5%" style="text-align:center;">
												<a href="index.php?option=com_osservicesbooking&task=orders_editservice&id=<?php echo $item->id?>" title="<?php echo JText::_('OS_EDIT_ORDER_ITEM');?>">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
													  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
													  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
													</svg>
												</a>
											</td>
											<td width="5%" style="text-align:center;">
												<a href="javascript:removeService(<?php echo $item->id?>,<?php echo $row->id?>);" title="<?php echo JText::_('OS_REMOVE_ORDER_ITEM');?>">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
													  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
													</svg>
												</a>
											</td>
										</tr>
									<?php }
									}
									?>
								</tbody>
							</table>
						</fieldset>
					</div>
				</div>
				<?php
				}
				else
				{
					?>
					<div class="<?php echo $rowFluidClass;?>">
						<div class="<?php echo $span12Class; ?> form-horizontal">
							<div class="alert alert-block">
								<h4><?php echo JText::_('OS_NOTICE')?></h4>
								<?php
									echo JText::_('OS_YOU_CAN_ONLY_ADD_SERVICES_AFTER_SAVING_ORDER_DETAILS');
								?>
							</div>
						</div>
					</div>
					<?php
				}
				?>
		</fieldset>
		<input type="hidden" name="option" value="<?php echo $option?>" /> 
		<input type="hidden" name="task" value="orders_detail" />
		<input type="hidden" name="id" id="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="old_status" value="<?php echo $row->order_status;?>" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JUri::base(); ?>" />
		<input type="hidden" name="processItem" id="processItem" value="" />
		</form>
		<div style="clear:both;"></div>
		<script type="text/javascript">
		function removeService(id,order_id){
			var answer = confirm("<?php echo JText::_('OS_DO_YOU_WANT_TO_REMOVE_SERVICE')?>");
			if(answer == 1){
				location.href = "index.php?option=com_osservicesbooking&task=orders_removeservice&id=" + id + "&order_id=" + order_id;
			}
		}
		
		var live_site = "<?php echo JUri::root(); ?>";
		function populateUserData(){
			var id = jQuery('#user_id_id').val();
			var orderid = jQuery('#id').val();
			if(orderid == "")
			{
				orderid = 0;
			}
			populateUserDataAjax(id,orderid,live_site);
		}
		function changeCheckinStatus(item,status)
		{
			var answer = confirm("<?php echo JText::_('OS_DO_YOU_WANT_TO_CHANGE_CHECKIN_STATUS_OF_ORDER_ITEM');?>");
			if(answer == 1)
			{
				jQuery('#processItem').val(item);
				changeCheckinStatusAjax(item,status);
			}
		}
		</script>
		<?php
	}
	
	/**
	 * Add services Form
	 *
	 * @param unknown_type $order_id
	 * @param unknown_type $lists
	 */
	static function addServicesForm($id = 0,$order_id,$lists,$show_date,$sid,$vid,$eid,$booking_date)
	{
		global $mainframe,$_jversion,$configClass,$jinput, $mapClass;
		JHtml::_('behavior.multiselect');
		if($id > 0)
		{
			JToolBarHelper::title(JText::_('OS_EDIT_ORDER_ITEM'),'edit');
		}
		else
		{
			JToolBarHelper::title(JText::_('OS_ADD_ORDER_ITEM'),'add');
		}
		JToolBarHelper::save('orders_saveservice');
		JToolBarHelper::apply('orders_applyservice');
		JToolBarHelper::cancel('orders_gotoorderdetails');
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
	
		$db = JFactory::getDbo();
		
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking&task=orders_addservice" name="adminForm" id="adminForm">
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span2'];?> boldtext">
					<?php echo JText::_('OS_FILTER_EMPLOYEE_FOR_SERVICE');?>
				</div>
				<div class="<?php echo $mapClass['span10'];?>">
					<?php echo $lists['services'];?>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span2'];?> boldtext">
					<?php echo JText::_('OS_SELECT_VENUE');?>
				</div>
				<div class="<?php echo $mapClass['span10'];?>">
					<?php echo $lists['venues'];?>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span2'];?> boldtext">
					<?php echo JText::_('OS_SELECT_EMPLOYEES')?>
				</div>
				<div class="<?php echo $mapClass['span10'];?>">
					<?php echo $lists['employees'];?>
				</div>
			</div>
			<?php
			if($show_date == 1){
				?>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span2'];?> boldtext">
						<?php echo JText::_('OS_SELECT_BOOKING_DATE')?>
					</div>
					<div class="<?php echo $mapClass['span10'];?>">
						<?php
						echo JHTML::_('calendar',$booking_date, 'booking_date', 'booking_date', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'19',  'maxlength'=>'19','style'=>'width:80px;'));
						?>
					</div>
				</div>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?>" style="text-align:center;padding:20px;">
						<a href="javascript:document.adminForm.submit();" class="btn btn-danger" style="color:white;"><?php echo JText::_('OS_SHOW_TIME_SLOTS');?></a>
					</div>
				</div>
				<?php
				if($sid > 0 && $eid > 0 && $booking_date != "")
				{
					if(OSBHelper::checkAvailableDate($sid,$eid,$booking_date))
					{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
							<div class="span12 btn btn-danger boldtext">
								<?php echo JText::_('OS_OFF_DATE_PLEASE_SELECT_ANOTHER_DATE');?>
							</div>
						</div>
						<?php
					}elseif(OSBHelper::isEmployeeAvailableInSpecificDate($sid,$eid,$booking_date)){
						?>
						<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv" style="margin-left:0px;">
							<div class="<?php echo $mapClass['span12'];?>">
								<span class="btn btn-warning boldtext" style="width:100%;margin-bottom:5px;">
									<?php echo JText::_('OS_PLEASE_SELECT_TIME_SLOTS_BELLOW');?>
								</span>
								<div class="<?php echo $mapClass['row-fluid'];?>">
									<div class="<?php echo $mapClass['span7'];?>">
										<?php
										OSBHelper::loadTimeSlots($sid,$eid,$booking_date,$vid,$id);
										?>
									</div>
									<div class="<?php echo $mapClass['span5'];?>">
										<div class="<?php echo $mapClass['row-fluid'];?>">
											<div class="<?php echo $mapClass['span12'];?>">
												<?php echo OsAppscheduleDefault::loadExtraFields($sid,$eid,$id);?>	
											</div>
										</div>
										<div class="<?php echo $mapClass['row-fluid'];?>">
											<div class="<?php echo $mapClass['span12'];?>">
												<?php
												$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
												$service = $db->loadObject();
												if($service->repeat_day == 1  || $service->repeat_week == 1  || $service->repeat_fortnight == 1 || $service->repeat_month == 1)
												{
												?>
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
																	<select name="repeat_type" id="repeat_type" class="input-medium form-select" >
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
																	<select name="repeat_to" class="input-mini form-select imini" id="repeat_to">
																		<option value=""></option>
																		<?php
																		for($m=1;$m<=10;$m++)
																		{
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
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
					}else{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
							<div class="span12 btn btn-danger boldtext">
								<?php echo JText::_('OS_UNAVAILABLE');?>
							</div>
						</div>
						<?php
					}
				}
			}
			?>
		<input type="hidden" name="option" value="com_osservicesbooking" />
		<input type="hidden" name="task" value="<?php echo $jinput->getString('task','');?>" />
		<input type="hidden" name="order_id" value="<?php echo (int) $order_id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="nslots" id="nslots" value="" />
		<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
		</form>
		<script language="javascript">
		function addBackendBooking(id,start_time,end_time)
		{
			var select = document.getElementById('selected_timeslots');
			for ( var i = 0, l = select.options.length, o; i < l; i++ )
			{
			   o = select.options[i];
			   if ( o.value == start_time + "-" + end_time ){
			   	   if(o.selected == true){
			       	   o.selected = false;
			   	   }else{
			   	   	   o.selected = true;
			   	   }
			   }
			}
		}
		function updateNslots(id){
			var temp = document.getElementById(id);
			if(temp.checked == true){
				var nslots = document.getElementById('nslots' + id);
				if(nslots != null){
					document.getElementById('nslots').value = nslots.value;
				}
			}
		}
		Joomla.submitbutton =  function(pressbutton){
			var form = document.adminForm;
			if(pressbutton == "orders_saveservice"){
				Joomla.submitform(pressbutton);
			}else{
				Joomla.submitform(pressbutton);
			}
		}
		</script>
		<?php
	}
	
	static function exportReport($rows,$lists){
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		?>
		<style>
		.header_td{
			font-weight:bold;
			background:#394D84;
			border:1px solid black;
			text-align:center;
			color:white;
			height:35px;
		}
		.data_td{
			text-align:left;
			padding-left:10px;
			padding-top:5px;
			padding-bottom:5px;
			border-right:1px solid black;
			border-bottom:1px solid black;
		}
		.data_first{
			border-left:1px solid black;
		}
		</style>
		<table width="100%">
			<tr>
				<td width="100%" style="padding:10px;" colspan="2">
					<font style="font-size:22px;font-weight:bold;color:#8496CE;font-family:Tahoma;">
						<?php echo JText::_('OS_REPORT');?>
					</font>
				</td>
			</tr>
			<tr>
				<td width="50%" valign="top">
					<?php
					if(($lists['sid'] > 0) or ($lists['eid'] > 0) or ($lists['order_status'] != "")){
						?>
						<table width="100%">
						<?php
						if($lists['sid'] > 0){
							$db->setQuery("Select id,service_name from #__app_sch_services where id = '".$lists['sid']."'");
							$service = $db->loadObject();
							$service_name = $service->service_name;
							?>
							<tr>
								<td width="20%" style="font-size:14px;text-align:left;padding:10px;font-weight:bold;">
									<?php echo JText::_('OS_SERVICE');?>:
								</td>
								
								<td width="80%" style="font-size:14px;text-align:left;padding:10px;border-bottom:1px dotted gray;">
									<?php echo $service_name;?>
								</td>
							</tr>
							<?php
						}
						?>
						<?php
						if($lists['eid'] > 0){
							$db->setQuery("Select id,employee_name from #__app_sch_employee where id = '".$lists['eid']."'");
							$employee = $db->loadObject();
							$employee_name = $employee->employee_name;
							?>
							<tr>
								<td width="20%" style="font-size:14px;text-align:left;padding:10px;font-weight:bold;">
									<?php echo JText::_('OS_EMPLOYEE');?>:
								</td>
								
								<td width="80%" style="font-size:14px;text-align:left;padding:10px;border-bottom:1px dotted gray;">
									<?php echo $employee_name;?>
								</td>
							</tr>
							<?php
						}
						?>
						<?php
						if($lists['order_status'] != ""){
							?>
							<tr>
								<td width="20%" style="font-size:14px;text-align:left;padding:10px;font-weight:bold;">
									<?php echo JText::_('OS_STATUS');?>:
								</td>
								
								<td width="80%" style="font-size:14px;text-align:left;padding:10px;border-bottom:1px dotted gray;">
									<?php 
									echo OSBHelper::orderStatus(0,$lists['order_status']);
									?>
								</td>
							</tr>
							<?php
						}
						?>
						</table>
						<?php
					}
					?>
				</td>
				<td width="50%" style="padding:10px;text-align:right;" valign="top">
					<?php
					if(($lists['date_from'] != "") or ($lists['date_to'] != "")){
						?>
						
						<table width="100%">
							<tr>
								<td style="font-size:14px;border:1px solid #000;background:#394D84;color:white;font-weight:bold;text-align:center;padding:10px;" colspan="2">
									<?php echo JText::_('OS_PERIOD');?>
								</td>
							</tr>
							<?php
							if($lists['date_from'] != ""){
							?>
							<tr>
								<td width="40%" style="font-size:14px;background:#E7EBF7;text-align:right;border-bottom:1px solid black;padding:10px;">
									<?php echo JText::_('OS_FROM')?>: 
								</td>
								<td align="center" style="font-size:14px;border-bottom:1px solid black;padding:10px;">
									<?php echo $lists['date_from']; ?>
								</td>
							</tr>
							<?php
							}
							?>
							<?php
							if($lists['date_to'] != ""){
							?>
							<tr>
								<td width="40%" style="font-size:14px;background:#E7EBF7;text-align:right;border-bottom:1px solid black;padding:10px;">
									<?php echo JText::_('OS_TO')?>: 
								</td>
								<td align="center" style="font-size:14px;border-bottom:1px solid black;padding:10px;">
									<?php echo $lists['date_to']; ?>
								</td>
							</tr>
							<?php
							}
							?>
						</table>
						<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="2" style="padding:20px;">
					<table width="100%">
						<tr>
							<td class="header_td" width="2%">
								#
							</td>
							<td class="header_td" width="10%">
								<?php echo JText::_('OS_SERVICE');?>
							</td>
							<td class="header_td" width="10%">
								<?php echo JText::_('OS_EMPLOYEE');?>
							</td>
							<td class="header_td" width="6%">
								<?php echo JText::_('OS_FROM');?>
							</td>
							<td class="header_td" width="6%">
								<?php echo JText::_('OS_TO');?>
							</td>
							<td class="header_td" width="6%">
								<?php echo JText::_('OS_BOOKING_DATE');?>
							</td>
							<td class="header_td" width="14%">
								<?php echo JText::_('OS_ORDER');?>
							</td>
							<td class="header_td" width="18%">
								<?php echo JText::_('OS_CUSTOMER');?>
							</td>
							<td class="header_td" width="20%">
								<?php echo JText::_('OS_OTHER_INFORMATION');?>
							</td>
							<td class="header_td" width="10%">
								<?php echo JText::_('OS_STATUS');?>
							</td>
						</tr>
						<?php
						for($i=0;$i<count($rows);$i++){
							$row = $rows[$i];
							if($i % 2 == 0){
								$bgcolor = "#fff";
							}else{
								$bgcolor = "#efefef";
							}
							
							?>
							<tr>
								<td class="data_td data_first" style="padding-left:0px;background:<?php echo $bgcolor?>;text-align:center;">
									<?php echo $i + 1;?>
								</td>
								<td class="data_td" style="background:<?php echo $bgcolor?>;">
									<?php echo $row->service_name;?>
								</td>
								<td class="data_td" style="background:<?php echo $bgcolor?>;">
									<?php echo $row->employee_name;?>
								</td>
								<td class="data_td" style="padding-left:0px;background:<?php echo $bgcolor?>;text-align:center;">
									<?php echo date($configClass['time_format'],$row->start_time);?>
								</td>
								<td class="data_td" style="padding-left:0px;background:<?php echo $bgcolor?>;text-align:center;">
									<?php echo date($configClass['time_format'],$row->end_time);?>
								</td>
								<td class="data_td" style="padding-left:0px;background:<?php echo $bgcolor?>;text-align:center;">
									<?php echo date($configClass['date_format'],$row->start_time);?>
								</td>
								<td class="data_td" style="padding-left:0px;background:<?php echo $bgcolor?>;text-align:center;">
									<?php echo $row->order_id;?> (<?php echo $row->order_date;?>)
								</td>
								<td class="data_td" style="background:<?php echo $bgcolor?>;">
									<?php 
									echo $row->order_name." (".$row->order_email.") ".$row->order_phone;
									?>
								</td>
								<td class="data_td" style="background:<?php echo $bgcolor?>;">
								<?php
								if($row->service_time_type ==1){
									echo JText::_('OS_NUMBER_SLOT').": ".$row->nslots."<BR />";
								}
								$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1'");
								$fields = $db->loadObjectList();
								if(count($fields) > 0){
									for($i1=0;$i1<count($fields);$i1++){
										$field = $fields[$i1];
										$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$row->order_item_id' and field_id = '$field->id'");
										$count = $db->loadResult();
										if($count > 0){
											if($field->field_type == 1){
												$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->order_item_id' and field_id = '$field->id'");
												$option_id = $db->loadResult();
												$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
												$optionvalue = $db->loadObject();
												?>
												<?php echo $field->field_label;?>:
												<?php
												$field_data = $optionvalue->field_option;
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
													$optionvalue = $db->loadObject();
													$field_data = $optionvalue->field_option;
													if($optionvalue->additional_price > 0){
														$field_data.= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
													}
													$fieldArr[] = $field_data;
												}
												?>
												<?php echo $field->field_label;?>:
												<?php
												echo implode(", ",$fieldArr);
												echo "<BR />";
											}
										}
									}
								}
								?>
								</td>
								<td class="data_td" style="padding-left:0px;background:<?php echo $bgcolor?>;text-align:center;">
									<?php
									/*
									switch ($row->order_status){
										case "P":
											echo JText::_('OS_PENDING');
										break;
										case "S":
											echo JText::_('OS_COMPLETE');
										break;
										case "C":
											echo JText::_('OS_CANCEL');
										break;
									}
									*/
									echo OSBHelper::orderStatus(0,$row->order_status);
									?>
									
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</td>
			</tr>
		</table>
		<script language="javascript">
		window.print();
		</script>
		<?php
	}

	public static function customerDetails($row, $orders)
	{
		global $mainframe, $mapClass, $configClass;
		$controlGroupClass  = $mapClass['control-group'];
		$controlLabelClass  = $mapClass['control-label'];
		$controlsClass		= $mapClass['controls'];
		JToolBarHelper::title(JText::_('OS_CUSTOMER').' ['.$row->order_name.']','user');
		JToolBarHelper::save('orders_savecustomer');
		JToolBarHelper::apply('orders_applycustomer');
		JToolBarHelper::cancel('orders_cancelcustomer');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
			<?php
			echo JHtml::_('bootstrap.startTabSet', 'customer', array('active' => 'general-page'));
				echo JHtml::_('bootstrap.addTab', 'customer', 'general-page', JText::_('OS_CUSTOMER_INFORMATION', true));
				?>
					<div class="<?php echo $mapClass['row-fluid']; ?>">
						<div class="<?php echo $mapClass['span6']; ?>">
							<fieldset class="form-horizontal options-form">
								<legend><?php echo JText::_('OS_GENERAL')?></legend>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php
										echo JText::_('OS_CUSTOMER_TYPE');
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										if($row->user_id == 0)
										{
											echo JText::_('OS_GUEST');
										}
										else
										{
											echo JText::_('OS_REGISTERED');
											$link = "index.php?option=com_users&task=user.edit&id=".$row->user_id;
											?>
											&nbsp;
											<a href="<?php echo $link; ?>" title="Edit User <?php echo $row->order_name; ?>" target="_blank">#<?php echo $row->user_id;?></a>
											<?php
										}
										?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php
										echo JText::_('OS_NAME');
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										echo $row->order_name;
										?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php
										echo JText::_('OS_EMAIL');
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										echo $row->order_email;
										?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php
										echo JText::_('OS_PHONE');
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										echo $row->order_phone;
										?>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php
										echo JText::_('OS_ADDRESS');
										?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										$addressArr = ['order_address','order_zip','order_city','order_state','order_country'];
										$tmp = [];
										foreach($addressArr as $address)
										{
											if($row->$address != '')
											{
												$tmp[] = $row->$address;
											}
										}
										if(count($tmp))
										{
											echo implode(", ", $tmp);
										}
										?>
									</div>
								</div>
								

							</fieldset>
						</div>
						<div class="<?php echo $mapClass['span6']; ?>">
							<fieldset class="form-horizontal options-form">
								<legend><?php echo JText::_('OS_NOTES')?></legend>
								<textarea name="notes" style="width:100%; height:150px;" class="form-control"><?php echo $row->notes;?></textarea>
							</fieldset>
						</div>
					</div>
				<?php
				echo JHtml::_('bootstrap.endTab');
				echo JHtml::_('bootstrap.addTab', 'customer', 'order-item-page', JText::_('OS_APPOINTMENTS', true));
				?>
				<div class="<?php echo $mapClass['row-fluid']; ?>">
					<div class="<?php echo $mapClass['span12']; ?>">
						<table class="adminlist table table-striped customertable">
							<thead>
								<tr>
									<th class="nowrap center" width="30%">
										<?php
										echo JText::_('OS_DATE');
										?>
									</th>
									<th class="nowrap center" width="15%">
										<?php
										echo JText::_('OS_STATUS');
										?>
									</th>
									<th class="nowrap center" width="30%">
										<?php
										echo JText::_('OS_SERVICE');
										?>
									</th>
									<th class="nowrap center" width="25%">
										<?php
										echo JText::_('OS_MORE_INFORMATION');
										?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$k = 0;
								for ($i=0, $n=count($orders); $i < $n; $i++) 
								{
									$order = $orders[$i];
									?>
									<tr class="<?php echo "row$k"; ?>">
										<td>
											<div class="td-primary">
												<?php
												echo date($configClass['date_format'], $order->start_time);
												?>
											</div>
											<div class="td-secondary">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
												  <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
												  <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
												</svg>
												<?php
												echo date($configClass['time_format'], $order->start_time);
												?>
												&nbsp;
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
												  <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
												  <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
												</svg>
												<?php
												echo date($configClass['time_format'], $order->end_time);
												?>
											</div>
										</td>
										<td class="center">
											<div class="td-upper">
												<?php
												echo OSBHelper::orderStatus(0,$order->order_status);		
												?>
											</div>
										</td>
										<td>
											<div class="td-primary">
												<?php
												echo $order->service_name;	
												?>
											</div>
											<div class="td-secondary">
												<?php
												echo $order->employee_name;	
												?>
											</div>
										</td>
										<td>
											<?php
											if($order->service_time_type == 1)
											{
												echo JText::_('OS_SEATS').": ".$order->nslots;
											}
											if($order->checked_in == 1)
											{
												?>
												<div class="td-upper">
													<?php
													echo JText::_('OS_CHECKED_IN');
													?>
												</div>
												<?php
											}
											?>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?php
				echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.endTabSet');
			?>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" id="id" value="<?php echo (int)$row->id?>" />
		</form>
		<?php
	}
	
}
?>