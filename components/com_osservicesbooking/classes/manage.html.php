<?php
/*------------------------------------------------------------------------
# manage.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 Joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
class HTML_OSappscheduleManage
{
    static function listOrders($rows,$lists,$pageNav)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
        $db = JFactory::getDbo();
		?>
		<div class="<?php echo $mapClass['row-fluid'];?> manageorders">
			<div class="<?php echo $mapClass['span4'];?>">
				<div class="page-header">
					<h1><?php echo JText::_('OS_MANAGE_ORDERS');?></h1>
				</div>
			</div>
			<div class="<?php echo $mapClass['span8'];?> pull-right alignright" style="margin-top:15px;">
                <a class="btn btn-success" title="<?php echo JText::_('OS_ADD_ORDER')?>" href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=manage_editorder"/><?php echo JText::_('OS_ADD_ORDER')?></a>
                <input type="button" class="btn btn-info" value="<?php echo JText::_('OS_EXPORT_SELECTED_ORDERS')?>" title="<?php echo JText::_('OS_EXPORT_SELECTED_ORDERS')?>" onclick="javascript:exportSelectedOrders()"/>
                <input type="button" class="btn btn-info" value="<?php echo JText::_('OS_EXPORT_CSV')?>" title="<?php echo JText::_('OS_EXPORT_CSV')?>" onclick="javascript:exportCsv()"/>
                <input type="button" class="btn btn-info" value="<?php echo JText::_('OS_EXPORT_INVOICE')?>" title="<?php echo JText::_('OS_EXPORT_INVOICE')?>" onclick="javascript:exportInvoice()"/>
				<input type="button" class="btn btn-danger" value="<?php echo JText::_('OS_REMOVE_ORDER')?>" title="<?php echo JText::_('OS_REMOVE_ORDER')?>" onclick="javascript:removeOrders()"/>
				<input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_BACK')?>" title="<?php echo JText::_('OS_GO_BACK')?>" onclick="javascript:history.go(-1);"/>
			</div>
		</div>
		<form method="POST" action="<?php echo Jroute::_('index.php?option=com_osservicesbooking&view=manageallorders&Itemid='.$jinput->getInt('Itemid',0));?>" name="ftForm" id="ftForm">
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?> pull-right">
					<strong><?php echo JText::_('OS_FILTER')?>:</strong>
					<?php echo $lists['filter_venue']; ?>
					<?php echo $lists['filter_service']; ?>
					<?php echo $lists['filter_employee']; ?>
					<?php echo $lists['filter_status']; ?>
					<?php echo JHtml::_('calendar',$lists['filter_date_from'],'filter_date_from','filter_date_from','%Y-%m-%d',array('placeholder' => JText::_('OS_FROM'),'onchange' => '', 'class' => 'input-small'));?>
					<?php echo JHtml::_('calendar',$lists['filter_date_to'],'filter_date_to','filter_date_to','%Y-%m-%d',array('placeholder' => JText::_('OS_TO'),'', 'class' => 'input-small'))?>
                    <input type="text" class="input-medium form-control" placeholder="<?php echo JText::_('OS_KEYWORD');?>" name="keyword" id="keyword" value="<?php echo OSBHelper::getStringValue('keyword','');?>" />
					<button class="btn btn-secondary hasTooltip" title="" type="button" onClick="javascript:submitFilterForm();" data-original-title="<?php echo JText::_('OS_SEARCH');?>">
						<i class="icon-search"></i>
					</button>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<?php 
					$optionArr = array();
					$statusArr = array(JText::_('OS_PENDING'),JText::_('OS_COMPLETED'),JText::_('OS_CANCELED'),JText::_('OS_ATTENDED'),JText::_('OS_TIMEOUT'),JText::_('OS_DECLINED'),JText::_('OS_REFUNDED'));
					$statusVarriableCode = array('P','S','C','A','T','D','R');
					for($j=0;$j<count($statusArr);$j++){
						$optionArr[] = JHtml::_('select.option',$statusVarriableCode[$j],$statusArr[$j]);				
					}
					if(count($rows) > 0)
					{
					?>
					<table class="adminlist table table-striped" width="100%" id="ordersTable">
						<thead>
							<tr>
								<th width="2%">
									<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
								</th>
								<th width="3%">
									ID
								</th>
								<th width="15%">
									<?php echo JText::_('OS_CUSTOMER_DETAILS');?>
								</th>
								<th width="45%">
									<?php echo JText::_('OS_DETAILS');?>
								</th>
								<?php
								if($configClass['disable_payments'] == 1){
								?>
									<th width="25%">
										<?php echo JText::_('OS_ORDER_PAYMENT');?>
									</th>
								<?php
								} 
								?>
                                <th width="7%">
                                </th>
							</tr>
						</thead>
						<?php
						if($configClass['disable_payments'] == 1)
						{
							$cols = 6;
						}
						else
						{
							$cols = 5;
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

						for ($i=0, $n=count($rows); $i < $n; $i++) 
						{
							$row = $rows[$i];
							$checked = JHtml::_('grid.id', $i, $row->id);
							$link 		= JRoute::_( 'index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id='. $row->id.'&ref='.md5($row->id) );
							
							$db->setQuery("SELECT * FROM #__app_sch_order_items WHERE order_id = '$row->id'");
							$items = $db->loadObjectList();
							$servicesArr = array();
							for($j=0;$j<count($items);$j++)
							{
								$config = new JConfig();
								$offset = $config->offset;
								date_default_timezone_set($offset);	
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
								<td align="center" data-label=""><?php echo $checked; ?></td>
								<td align="left" data-label="ID"><a href="<?php echo $link; ?>"><?php echo str_pad($row->id,6,'000000',0); ?></a></td>
								<td align="left" data-label="<?php echo JText::_('OS_CUSTOMER_DETAILS');?>">
                                    <?php
                                    if($row->user_id > 0)
                                    {
                                        $link = JRoute::_('index.php?option=com_osservicesbooking&task=manage_userinfo&userId='.$row->user_id);
                                    }
                                    ?>
                                    <a href="<?php echo $link; ?>">
                                        <?php echo $row->order_name; ?>
                                    </a>
									<BR />
									<a href="mailto:<?php echo $row->order_email?>" target="_blank"><?php echo $row->order_email?></a></td>
								<td align="left" data-label="<?php echo JText::_('OS_DETAILS');?>" >
								<?php 
								echo "<span style='color:gray;'>".JText::_('OS_CURRENT_STATUS').": <strong>".OSBHelper::orderStatus(0,$row->order_status)."</strong>";
								?>
								<BR />
								<?php 
								echo JText::_('OS_ORDER_DATE')." <strong>".date($configClass["date_time_format"],strtotime($row->order_date));?></strong></span>
								<BR />
								<span style="font-size:12px;"><?php echo $service?></span></td>
								<?php
								if($configClass['disable_payments'] == 1)
								{
								?>
									<td align="left" data-label="<?php echo JText::_('OS_ORDER_PAYMENT');?>">
										<?php
											echo JText::_('OS_TOTAL').": ".OSBHelper::showMoney($row->order_final_cost,1);
										?>
										<br />
										<?php
											echo JText::_('OS_DISCOUNT').": ".OSBHelper::showMoney($row->order_discount,1);
										?>
										<br />
										<?php
											echo JText::_('OS_DEPOSIT').": ".OSBHelper::showMoney($row->order_upfront,1);
										?>
										<br />
										<?php 
										$order_payment = $row->order_payment;
										if($order_payment != ""){
											echo Jtext::_('OS_PAYMENT')." <strong>".JText::_(os_payments::loadPaymentMethod($order_payment)->title)."</strong>";
										}
									?>
									</td>
									<?php
								}
								?>
								<td class="center" data-label="">
									<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=manage_editorder&id=<?php echo $row->id;?>&Itemid=<?php echo $jinput->getInt('Itemid',0); ?>" title="<?php echo JText::_('OS_EDIT_ORDER');?>">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
										  <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
										</svg>
									</a>
									<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=manage_removeorders&cid[]=<?php echo $row->id;?>&Itemid=<?php echo $jinput->getInt('Itemid',0); ?>" title="<?php echo JText::_('OS_REMOVE_ORDER');?>">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
										  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
										</svg>
									</a>
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
					<input type="hidden" name="task" id="task" value="manage_orders" />
					<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
					<input type="hidden" name="live_site" id="live_site" value="<?php echo Juri::root();?>" />
					<input type="hidden" name="current_order_id" id="current_order_id" value="" />
					<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid',0); ?>" />
				</div>
			</div>
		</form>
        <script type="text/javascript">
            function exportInvoice()
            {
                jQuery("#task").val('manage_exportinvoice');
                document.ftForm.submit();
            }
            function exportSelectedOrders()
            {
				var flag = 0;
				for (var i = 0; i< <?php echo count($rows);?>; i++) 
				{
				  if(document.getElementById("cb" + i).checked)
				  {
						flag ++;
				  }
				}
				if (flag == 0) 
				{
					alert ("<?php echo JText::_('OS_NO_ORDERS_TO_EXPORT');?>");
					return false;
				}
				else
				{
					jQuery("#task").val('manage_exportSelectedOrders');
					document.ftForm.submit();
				}
            }
            function exportCsv()
            {
                jQuery("#task").val('manage_exportCsv');
                document.ftForm.submit();
            }
			function submitFilterForm()
            {
                jQuery("#task").val('manage_orders');
                document.ftForm.submit();
            }
        </script>
		<?php
	}

    /**
     * @param $option
     * @param $row
     * @param $rows
     * @param $pageNav
     * @param $fields
     * @param $lists
     * @throws Exception
     */
    static function orders_detail($option,$row,$rows,$pageNav,$fields,$lists){
        global $mainframe,$configClass,$jinput,$mapClass;
        $config         = new JConfig();
        $offset         = $config->offset;
        date_default_timezone_set($offset);
        $mainframe 	    = JFactory::getApplication();
        $jinput->set( 'hidemainmenu', 1 );
        if ($row->id){
            $title      = ' ['.JText::_('OS_EDIT').']';
        }else{
            $title      = ' ['.JText::_('OS_NEW').']';
        }
        $document       = JFactory::getDocument();
        $document->setTitle(JText::_('OS_ORDER_DETAIL').$title);
        ?>
        <div class="<?php echo $mapClass['row-fluid'];?>">
            <div class="<?php echo $mapClass['span6'];?>">
                <div class="page-header">
                    <h1><?php echo JText::_('OS_ORDER_DETAIL').$title;?></h1>
                </div>
            </div>
            <div class="<?php echo $mapClass['span6'];?> pull-right alignright" style="margin-top:15px;">
                <input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_SAVE')?>" title="<?php echo JText::_('OS_SAVE')?>" onclick="javascript:saveOrder(1);"/>
                <input type="button" class="btn btn-danger" value="<?php echo JText::_('OS_APPLY')?>" title="<?php echo JText::_('OS_APPLY')?>" onclick="javascript:saveOrder(0);"/>
                <input type="button" class="btn btn-secondary" value="<?php echo JText::_('OS_CANCEL')?>" title="<?php echo JText::_('OS_GO_BACK')?>" onclick="javascript:gotoOrdersList();"/>
            </div>
        </div>
        <?php
        if (version_compare(JVERSION, '3.5', 'ge') && !OSBHelper::isJoomla4())
		{
            ?>
			<script src="<?php echo JUri::root()?>media/com_osservicesbooking/assets/css/bootstrap/js/jquery.min.js" type="text/javascript"></script>
            <script src="<?php echo JUri::root()?>media/jui/js/fielduser.min.js" type="text/javascript"></script>
        <?php 
		} 
		?>
        <form method="POST" action="<?php echo JUri::root(); ?>index.php?option=com_osservicesbooking&task=orders_detail&cid[]=<?php echo $row->id;?>" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<fieldset class="form-horizontal options-form">
			<legend><?php echo JText::_('OS_GENERAL_INFORMATION');?></legend>
				<div class="<?php echo $mapClass['row-fluid'];?>" id="order_general_information">
					<div style="width:100%;padding:20px;" class="<?php echo $mapClass['span12'];?> form-horizontal">
						<fieldset>
							<?php
							if($row->id > 0)
							{
								?>
								<div class="<?php echo $mapClass['control-group'];?>">
									<div class="<?php echo $mapClass['control-label'];?>">
										<label title="<?php echo JText::_( 'OS_ORDER_NUMBER' );?>::<?php echo JText::_('OS_ORDER_NUMBER_DESC'); ?>" class="hasTip" ><?php echo JText::_("OS_ORDER_NUMBER"); ?></label>
									</div>
									<div class="<?php echo $mapClass['controls'];?>">
										<span class="readonly"><?php echo str_pad($row->id,6,'000000',0); ?></span>
									</div>
								</div>
								<?php
							}
							?>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_CUSTOMER') ;?>" class="hasTip"><?php echo JText::_( 'OS_CUSTOMER') ;?> (User ID)</label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<?php
									require_once JPATH_ADMINISTRATOR .'/components/com_osservicesbooking/classes/orders.php';
									echo OSappscheduleOrders::getUserInput($row->user_id,$row->id);
									?>
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_NAME') ;?>::<?php echo JText::_( 'OS_NAME_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_NAME') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-large form-control" value="<?php echo $row->order_name; ?>" name="order_name" id="order_name" />
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_EMAIL') ;?>::<?php echo JText::_( 'OS_EMAIL_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_EMAIL') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-large form-control" value="<?php echo $row->order_email; ?>" name="order_email" id="order_email" />
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_PHONE') ;?>::<?php echo JText::_( 'OS_PHONE_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_PHONE') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">

									<input type="text" class="input-mini form-control" value="<?php echo $row->dial_code; ?>" name="dial_code" />
									<input type="text" class="input-small form-control" value="<?php echo $row->order_phone; ?>" name="order_phone" />
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_COUNTRY') ;?>::<?php echo JText::_( 'OS_COUNTRY_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_COUNTRY') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<?php echo $lists['country'];?>
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_CITY') ;?>::<?php echo JText::_( 'OS_CITY_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_CITY') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-large form-control" value="<?php echo $row->order_city; ?>" name="order_city" />
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_STATE') ;?>::<?php echo JText::_( 'OS_STATE_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_STATE') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-large form-control" value="<?php echo $row->order_state; ?>" name="order_state" />
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_ZIP') ;?>::<?php echo JText::_( 'OS_ZIP_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_ZIP') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-mini form-control" value="<?php echo $row->order_zip; ?>" name="order_zip" />
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_ADDRESS') ;?>::<?php echo JText::_( 'OS_ADDRESS_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_ADDRESS') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-large form-control" value="<?php echo $row->order_address; ?>" name="order_address" />
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
									<div class="<?php echo $mapClass['control-group'];?>">
										<div class="<?php echo $mapClass['control-label'];?>">
											<label title="<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$row->order_lang); //$field->field_label ;?>" class="hasTip"><?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$row->order_lang); //$field->field_label;?></label>
										</div>
										<div class="<?php echo $mapClass['controls'];?>">
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
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_NOTES') ;?>" class="hasTip"><?php echo JText::_( 'OS_NOTES') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<textarea name="notes" class="input-medium form-control"><?php echo $row->order_notes; ?></textarea>
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_PAYMENT') ;?>::<?php echo JText::_( 'OS_PAYMENT_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_PAYMENT') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
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
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_TOTAL') ;?>::<?php echo JText::_( 'OS_TOTAL_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_TOTAL') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-small form-control" value="<?php echo $row->order_total; ?>" name="order_total" /> <?php echo $configClass['currency_format'];?>
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_DISCOUNT') ;?>" class="hasTip"><?php echo JText::_( 'OS_DISCOUNT') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-small form-control" value="<?php echo $row->order_discount; ?>" name="order_discount" /> <?php echo $configClass['currency_format'];?>
								</div>
							</div>
							<?php
							if($configClass['enable_tax']==1){
								?>
								<div class="<?php echo $mapClass['control-group'];?>">
									<div class="<?php echo $mapClass['control-label'];?>">
										<label title="<?php echo JText::_( 'OS_TAX') ;?>::<?php echo JText::_( 'OS_TAX_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_TAX') ;?></label>
									</div>
									<div class="<?php echo $mapClass['controls'];?>">
										<input type="text" class="input-small form-control" value="<?php echo $row->order_tax; ?>" name="order_tax" /> <?php echo $configClass['currency_format'];?>
									</div>
								</div>
							<?php } ?>

							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_GROSS_AMOUNT') ;?>::<?php echo JText::_( 'OS_FINAL_COST_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_GROSS_AMOUNT') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-small form-control" value="<?php echo $row->order_final_cost; ?>" name="order_final_cost" /> <?php echo $configClass['currency_format'];?>
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_UPFRONT') ;?>::<?php echo JText::_( 'OR_UPFRONT_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_UPFRONT') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<input type="text" class="input-small form-control" value="<?php echo $row->order_upfront; ?>" name="order_upfront" /> <?php echo $configClass['currency_format'];?>
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_DATE') ;?>::<?php echo JText::_( 'OS_DATE_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_DATE') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<?php
									echo JHTML::_('calendar',$row->order_date, 'order_date', 'order_date', '%Y-%m-%d', array('class'=>'input-medium form-control datefield', 'size'=>'19',  'maxlength'=>'19'));
									?>
									<?php echo $lists['order_date_hour'] . ' ' . $lists['order_date_minute']; ?>
								</div>
							</div>
							<div class="<?php echo $mapClass['control-group'];?>">
								<div class="<?php echo $mapClass['control-label'];?>">
									<label title="<?php echo JText::_( 'OS_STATUS') ;?>::<?php echo JText::_( 'OS_STATUS_DESC') ;?>" class="hasTip"><?php echo JText::_( 'OS_STATUS') ;?></label>
								</div>
								<div class="<?php echo $mapClass['controls'];?>">
									<span class="readonly"> <?php echo $row->order_status_select_list; ?></span>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</fieldset>
			<fieldset class="form-horizontal options-form">
				<legend><?php echo JText::_('OS_SERVICELIST')?></legend>
				<?php

				if($row->id > 0)
				{
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?> form-horizontal">
							<div class="navbar">
								<div class="navbar-inner" style="text-align:right;">
									<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=manage_addservice&order_id=<?php echo $row->id?>" style="color:white;font-weight:bold;" class="btn btn-info"><?php echo JText::_('OS_ADD_SERVICES');?></a>
								</div>
							</div>
							<table class="table table-striped">
								<thead>
								<tr>
									<th width="20" align="left">
										<?php echo JText::_( '#' ); ?>
									</th>
									<th class="title" width="15%">
										<?php echo JText::_('OS_SERVICES');?>
									</th>
									<th class="title" width="15%">
										<?php echo JText::_('OS_EMPLOYEE');?>
									</th>
									<th width="15%">
										<?php echo JText::_('OS_WORKTIME_START_TIME');?>
									</th>
									<th width="15%">
										<?php echo JText::_('OS_WORKTIME_END_TIME');?>
									</th>
									<th width="15%">
										<?php echo JText::_('OS_DATE');?>
									</th>
									<th width="25%">
										<?php echo JText::_('OS_OTHER_INFORMATION');?>
									</th>
									<th width="5%">
										<?php echo JText::_('OS_CHECKED_IN');?>
									</th>
									<th width="5%">
										<?php echo JText::_('OS_EDIT');?>/<?php echo JText::_('OS_REMOVE');?>
									</th>
								</tr>
								</thead>
								<tfoot>
								<tr>
									<td colspan="9">
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
												if(count($fields) > 0){
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
																	$field_data.= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
																}
																echo $field_data;
																echo "<BR />";
															}elseif($field->field_type == 2){
																$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$item->id' and field_id = '$field->id'");
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
											<td width="10%" style="text-align:center;">
												<?php
												if($item->checked_in == 1)
												{
													?>
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
													  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
													</svg>
													<?php
												}else{
													?>
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
													  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
													</svg>
													<?php
												}
												?>
											</td>
											<td width="10%" style="text-align:center;">
												<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=manage_editorderitem&id=<?php echo $item->id?>&Itemid=<?php echo $jinput->getInt('Itemid',0);?>" title="<?php echo JText::_('OS_EDIT_ORDER_ITEM');?>">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
													  <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
													</svg>
												</a>

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
						</div>
					</div>
					<?php
				}
				else
				{
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?> form-horizontal">
							<div class="alert alert-block alert-warning">
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
            <input type="hidden" name="option" value="<?php echo $option;?>" />
            <input type="hidden" name="task" id="task" value="orders_detail" />
            <input type="hidden" name="id" id="id" value="<?php echo $row->id?>" />
            <input type="hidden" name="old_status" value="<?php echo $row->order_status;?>" />
        </form>
        <script type="text/javascript">
            function removeService(id,order_id){
                var answer = confirm("<?php echo JText::_('OS_DO_YOU_WANT_TO_REMOVE_SERVICE')?>");
                if(answer == 1){
                    location.href = "index.php?option=com_osservicesbooking&task=manage_removeservice&id=" + id + "&order_id=" + order_id;
                }
            }

            var live_site = "<?php echo JUri::root(); ?>";
            function saveOrder(save){
                var answer = confirm("<?php echo JText::_('OS_SAVE_ORDER_QUESTION')?>");
                if(answer == 1){
                    if(save == "1") {
                        jQuery('#task').val('manage_saveorder');
                    }else{
                        jQuery('#task').val('manage_applyorder');
                    }
                    document.adminForm.submit();
                }
            }
			var live_site = "<?php echo JUri::root(); ?>";
			function populateUserData(){
				var id = jQuery('#user_id_id').val();
				var orderid = jQuery('#id').val();
				if(orderid == ""){
					orderid = 0;
				}
				populateUserDataAjax(id,orderid,live_site);
			}
            function gotoOrdersList(){
                location.href = "<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=manage_orders";
            }
        </script>
        <?php
    }

    /**
     * Add Order item
     * @param $order_id
     * @param $lists
     * @param $show_date
     * @param $sid
     * @param $vid
     * @param $eid
     * @param $booking_date
     */
    static function addServicesForm($id = 0,$order_id,$lists,$show_date,$sid,$vid,$eid,$booking_date){
        global $mainframe,$configClass,$jinput,$mapClass;
        JHtml::_('behavior.multiselect');
        $document = JFactory::getDocument();
        if($id > 0)
        {
            JToolBarHelper::title(JText::_('OS_EDIT_ORDER_ITEM'),'edit');
        }
        else
        {
            JToolBarHelper::title(JText::_('OS_ADD_ORDER_ITEM'),'add');
        }
        ?>
        <div class="<?php echo $mapClass['row-fluid'];?>">
            <div class="<?php echo $mapClass['span6'];?>">
                <div class="page-header">
                    <h1>
                        <?php
                        if($id > 0)
                        {
                            echo JText::_('OS_EDIT_ORDER_ITEM');
                        }
                        else
                        {
                            echo JText::_('OS_ADD_ORDER_ITEM');
                        }
                        ?>
                    </h1>
                </div>
            </div>
            <div class="<?php echo $mapClass['span6'];?> pull-right alignright" style="margin-top:15px;">
                <input type="button" class="btn btn-warning" value="<?php echo JText::_('OS_SAVE')?>" title="<?php echo JText::_('OS_SAVE')?>" onclick="javascript:saveOrderItem();"/>
                <input type="button" class="btn btn-danger" value="<?php echo JText::_('OS_GO_TO_ORDER_DETAILS')?>" title="<?php echo JText::_('OS_ADD_ORDER_ITEM')?>" onclick="javascript:gotoOrderDetails();"/>
				<?php
				if (JFactory::getUser()->authorise('osservicesbooking.orders', 'com_osservicesbooking'))
				{
					$link = JUri::root().'index.php?option=com_osservicesbooking&task=manage_editorder&id='.$order_id.'&Itemid='.$jinput->getInt('Itemid',0);
					?>
					<a href="<?php echo $link;?>" class="btn btn-secondary" title="<?php echo JText::_('OS_BACK')?>"><?php echo JText::_('OS_BACK')?></a>
					<?php
				}
				?>
            </div>
        </div>
        <form method="POST" action="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=<?php echo $jinput->getString('task','');?>&id=<?php echo $id; ?>&order_id=<?php echo $order_id;?>" name="adminForm" id="adminForm">
            <div class="<?php echo $mapClass['row-fluid'];?>" style="margin-top:10px;">
                <div class="<?php echo $mapClass['span2'];?> boldtext">
                    <?php echo JText::_('OS_FILTER_EMPLOYEE_FOR_SERVICE');?>
                </div>
                <div class="<?php echo $mapClass['span10'];?>">
                    <?php echo $lists['services'];?>
                </div>
            </div>
            <div class="<?php echo $mapClass['row-fluid'];?>" style="margin-top:10px;">
                <div class="<?php echo $mapClass['span2'];?> boldtext">
                    <?php echo JText::_('OS_SELECT_VENUE');?>
                </div>
                <div class="<?php echo $mapClass['span10'];?>">
                    <?php echo $lists['venues'];?>
                </div>
            </div>
            <div class="<?php echo $mapClass['row-fluid'];?>" style="margin-top:10px;">
                <div class="<?php echo $mapClass['span2'];?> boldtext">
                    <?php echo JText::_('OS_SELECT_EMPLOYEES')?>
                </div>
                <div class="<?php echo $mapClass['span10'];?>">
                    <?php echo $lists['employees'];?>
                </div>
            </div>

            <?php
            if($show_date == 1)
            {
                ?>
                <div class="<?php echo $mapClass['row-fluid'];?>" style="margin-top:10px;">
                    <div class="<?php echo $mapClass['span2'];?> boldtext">
                        <?php echo JText::_('OS_SELECT_BOOKING_DATE')?>
                    </div>
                    <div class="<?php echo $mapClass['span10'];?> joomla4calendar">
                        <?php
                        echo JHTML::_('calendar',$jinput->get('booking_date',$booking_date,'string'), 'booking_date', 'booking_date', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'19',  'maxlength'=>'19','style'=>'width:80px;'));
                        ?>
                    </div>
                </div>
                <div class="<?php echo $mapClass['row-fluid'];?>" style="margin-top:10px;">
                    <div class="<?php echo $mapClass['span12'];?>" style="text-align:center;padding:20px;">
                        <a href="javascript:document.adminForm.submit();" class="btn btn-danger" style="color:white;"><?php echo JText::_('OS_SHOW_TIME_SLOTS');?></a>
                    </div>
                </div>
                <?php
                if($sid > 0 && $eid > 0 && $booking_date != "")
                {
					$currentDate = date("Y-m-d",HelperOSappscheduleCommon::getRealTime());
					$date1		 = strtotime($booking_date);
					$date2	     = strtotime($currentDate);
					if($date1 < $date2)
					{
						?>
                        <div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
                            <div class="<?php echo $mapClass['span12'];?> btn btn-danger boldtext">
                                <?php echo JText::_('OS_YOU_CANNOT_SELECT_PASSED_DATE');?>
                            </div>
                        </div>
                        <?php
					}
                    elseif(OSBHelper::checkAvailableDate($sid,$eid,$booking_date))
                    {
                        ?>
                        <div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
                            <div class="<?php echo $mapClass['span12'];?> btn btn-danger boldtext">
                                <?php echo JText::_('OS_OFF_DATE_PLEASE_SELECT_ANOTHER_DATE');?>
                            </div>
                        </div>
                        <?php
                    }
                    elseif(OSBHelper::isEmployeeAvailableInSpecificDate($sid,$eid,$booking_date))
                    {
                        ?>

                        <div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv" style="margin-left:0px;">
                            <div class="<?php echo $mapClass['span12'];?>">
                                <div class="<?php echo $mapClass['row-fluid'];?>">
                                    <div class="<?php echo $mapClass['span12'];?> btn btn-warning boldtext">
                                        <?php echo JText::_('OS_PLEASE_SELECT_TIME_SLOTS_BELLOW');?>
                                    </div>
                                </div>
                                <div class="<?php echo $mapClass['row-fluid'];?>">
                                    <div class="<?php echo $mapClass['span7'];?>">
                                        <BR />
                                        <?php
                                        OSBHelper::loadTimeSlots($sid,$eid,$booking_date);
                                        ?>
                                    </div>
                                    <div class="<?php echo $mapClass['span5'];?>">
                                        <?php echo OsAppscheduleDefault::loadExtraFields($sid,$eid, $id);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv">
                            <div class="<?php echo $mapClass['span12'];?> btn btn-danger boldtext">
                                <?php echo JText::_('OS_UNAVAILABLE');?>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
            ?>
            <input type="hidden" name="option" value="com_osservicesbooking" />
            <input type="hidden" name="task" id="task" value="manage_addservice" />
            <input type="hidden" name="order_id" value="<?php echo $order_id?>" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="nslots" id="nslots" value="" />
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
        </form>
        <script language="javascript">
            function addBackendBooking(id,start_time,end_time){
                var select = document.getElementById('selected_timeslots');
				jQuery("#selected_timeslots option:selected").removeAttr("selected");
                for ( var i = 0, l = select.options.length, o; i < l; i++ ){
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
            function saveOrderItem(){
                jQuery("#task").val('manage_saveorderitem');
                document.adminForm.submit();
            }
            function gotoOrderDetails(){
                location.href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=manage_editorder&id=<?php echo $order_id?>";
            }
        </script>
        <?php
    }

    public static function showUserInfo($orders,$user, $balance)
    {
        global $mainframe, $jinput, $mapClass, $configClass;
        $document = JFactory::getDocument();
        $document->setTitle(sprintf(JText::_('OS_USER_INFORMATION'), $user->name));
        jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/orderhistory.php'))
        {
            $tpl = new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }
        else
        {
            $tpl = new OSappscheduleTemplate(JPATH_COMPONENT.'/components/com_osservicesbooking/layouts/');
        }
        $tpl->set('mainframe',$mainframe);
        $tpl->set('rows',$orders);
        $tpl->set('jinput',$jinput);
        $tpl->set('mapClass',$mapClass);
        $tpl->set('configClass',$configClass);
        $tpl->set('user', $user);
        $tpl->set('task','manage_userinfo');
        $tpl->set('balance', $balance);
        $body = $tpl->fetch("orderhistory.php");
        echo $body;
    }

	static function listUsers($rows, $pageNav, $lists)
	{
		global $jinput;
		if (!OSBHelper::isJoomla4())
		{
			JHtml::_('behavior.tooltip');
		}
		else
		{
			JHtml::_('bootstrap.tooltip', '.hasTooltip', ['placement' => 'left']);
		}

		$field		= 'user_id';
		$function	= 'jSelectUser_'.$field;
		?>
		<form action="" method="post" name="adminForm" id="adminForm">
			<table width="100%">
			<tr>
				<td align="left">
					<div class="filter-search btn-group pull-left">
						<div class="input-group">
							<input type="text" class="input-large form-control" name="filter_search" style="width: 200px !important"  id="filter_search" value="<?php echo $jinput->getString('filter_search',''); ?>" size="40" title="<?php echo JText::_('EDOCMAN_SEARCH_IN_NAME'); ?>" />
							<button type="submit" class="btn btn-primary"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
							<button class="btn btn-secondary" type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
							<button class="btn btn-secondary" type="button" onclick="if (window.parent) window.parent.<?php echo $function;?>('', '<?php echo JText::_('JLIB_FORM_SELECT_USER') ?>');"><?php echo JText::_('No user')?></button>
						</div>
					</div>
				</td>	
				<td style="float: right;">		
					<label for="filter_group_id">
						<?php echo JText::_('OS_FILTER_USER_GROUP'); ?>
					</label>
					<?php echo OSBHelper::getChoicesJsSelect(JHtml::_('access.usergroup', 'filter_group_id', $lists['filter_group_id'], 'onchange="this.form.submit();"')); ?>
				</td>
			</tr>
			</table>
			<table class="adminlist table" style="width:100%;">
				<thead>
					<tr>
						<th align="left">
							<?php echo JText::_('OS_NAME'); ?>
						</th>
						<th class="nowrap" width="25%" align="left">
							<?php echo JText::_('OS_USERNAME'); ?>
						</th>
						<th class="nowrap" width="25%">
							<?php echo JText::_('User groups'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
					$i = 0;
					if (count($rows))
					{
						foreach ($rows as $item)
						{
						?>
							<tr class="row<?php echo $i % 2; ?>">
								<td>
									<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $item->id; ?>', '<?php echo addslashes($item->name); ?>');">
										<?php echo $item->name; ?></a>
								</td>
								<td align="left">
									<?php echo $item->username; ?>
								</td>
								<td align="left">
									<?php echo nl2br($item->group_names); ?>
								</td>
							</tr>
						<?php
							$i++;
						}
					}
				?>
				</tbody>
			</table>
			<input type="hidden" name="task" value="manage_users" />
			<input type="hidden" name="field" value="<?php echo $field; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
		<?php
	}
}
?>