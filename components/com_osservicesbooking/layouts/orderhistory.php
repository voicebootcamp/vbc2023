<?php
$rowFluidClass		= $mapClass['row-fluid'];
$span12Class		= $mapClass['span12'];
$inputMediumClass	= $mapClass['input-medium'];
?>
<form method="POST" action="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=default_customer&Itemid='.$jinput->getInt('Itemid'))?>" name="ftForm">
<div class="<?php echo $rowFluidClass?>">
    <?php
    if($task != "manage_userinfo")
    {
        $class = $mapClass['span4'];
    }
    else
    {
        $class = $mapClass['span12'];
    }
    ?>
    <div class="<?php echo $class;?> osbheading">
		<h1>
			<?php
			if($task != "manage_userinfo")
			{
				echo $heading;
			}
			else
			{
				echo sprintf(JText::_('OS_USER_INFORMATION'), $user->name);
			}
			?>
		</h1>
    </div>
    <?php
    if($task != "manage_userinfo")
    {
        ?>
        <div class="<?php echo $mapClass['span8'] ?> alignright">
            <?php
            if (OSBHelper::isPrepaidPaymentPublished())
            {
                ?>
                <input type="button" class="btn btn-danger" value="<?php echo JText::_('OS_MY_BALANCES') ?>"
                       title="<?php echo JText::_('OS_MY_BALANCES') ?>"
                       onclick="javascript:customerbalances('<?php echo JURI::root() ?>','<?php echo $jinput->getInt('Itemid', 0) ?>')"/>
                <?php
            }
            ?>
            <input type="button" class="btn btn-info" value="<?php echo JText::_('OS_MY_BOOKING_CALENDAR') ?>"
                   title="<?php echo JText::_('OS_GO_TO_MY_WORKING_CALENDAR') ?>"
                   onclick="javascript:customercalendar('<?php echo JURI::root() ?>','<?php echo $jinput->getInt('Itemid', 0) ?>')"/>
        </div>
        <?php
    }
    ?>
</div>
<?php
if($task != "manage_userinfo")
{
    ?>
    <div class="<?php echo $rowFluidClass?>">
        <div class="<?php echo $span12Class;?> padding10">
            <div style="float:left;padding-right:10px;">
                <?php echo JHTML::_('calendar',$jinput->get('date1','','string'), 'date1', 'date1', '%Y-%m-%d', array('class'=> $inputMediumClass, 'size'=>'10',  'maxlength'=>'19', 'placeholder' => JText::_('OS_FROM'))); ?>
            </div>
            <div style="float:left;padding-right:10px;">
                <?php echo JHTML::_('calendar',$jinput->get('date2','','string'), 'date2', 'date2', '%Y-%m-%d', array('class'=> $inputMediumClass, 'size'=>'10',  'maxlength'=>'19' , 'placeholder' => JText::_('OS_TO'))); ?>
            </div>
            <div style="float:left;">
                <input type="submit" value="<?php echo JText::_('OS_FILTER');?>" class="btn btn-primary" />
            </div>
        </div>
    </div>
    <?php
}

if($task == "manage_userinfo" && OSBHelper::isPrepaidPaymentPublished())
{
    //show user balances
    ?>
    <div class="<?php echo $rowFluidClass?> ">
        <div class="<?php echo $span12Class?>">
            <div class="userbalances">
				<h2>
					<?php
					echo JText::_('OS_USER_BALANCES');
					?>
					:
					<?php
					echo $balance;
					?>
				</h2>
            </div>
        </div>
    </div>
    <?php
}
?>
<div class="<?php echo $rowFluidClass?>">
    <div class="<?php echo $span12Class?>">
        <?php
        if(count($rows) > 0){
            ?>
            <h2>
                <?php
                    echo JText::_('OS_ORDERS_HISTORY');
                ?>
            </h2>
            <table width="100%" id="orderhistorytable">
                <thead>
                <tr>
                    <td width="3%" class="osbtdheader">
                        #
                    </td>
                    <td width="25%" class="osbtdheader">
                        <?php echo JText::_('OS_SERVICE');?>
                    </td>
                    <td width="15%" class="osbtdheader">
                        <?php echo JText::_('OS_DATE');?>
                    </td>
                    <td width="10%" class="osbtdheader">
                        <?php echo JText::_('OS_STATUS');?>
                    </td>
                    <td width="25%" class="osbtdheader center">
                        <?php echo JText::_('OS_ORDER_DETAILS');?>
                    </td>
                    <?php if($configClass['allow_cancel_request'] == 1){?>
                        <td width="15%" class="osbtdheader center">
                            <?php echo JText::_('OS_REMOVE_ORDER');?>
                        </td>
                    <?php }?>
					<?php
					if($configClass['value_sch_reminder_enable'] == 1 && $configClass['enable_reminder'] == 1)
					{
					?>
						<td width="10%" class="osbtdheader">
							<?php echo JText::_('OS_RECEIVE_REMINDER');?>
						</td>
					<?php
					}
					?>
                    <td width="2%" class="osbtdheader">
                        ID
                    </td>
                </tr>
                </thead>
                <tbody>
                <?php
                for($i=0;$i<count($rows);$i++)
                {
                    $row = $rows[$i];
                    ?>
                    <tr>
                        <td class="td_data" style="" data-label="">
                            <?php echo $i + 1;?>
                        </td>
                        <td class="td_data" style="" data-label="<?php echo JText::_('OS_SERVICE');?>">
                            <?php
                            $services = $row->service;
                            $servicesArr = explode(" ",$services);
                            if(count($servicesArr) > 5){
                                for($s=0;$s<5;$s++){
                                    echo $servicesArr[$s]." ";
                                }
                                echo "..";
                            }else{
                                echo $services;
                            }
                            ?>
                        </td>
                        <td class="td_data hidden-phone" style="" data-label="<?php echo JText::_('OS_DATE');?>">
                            <?php
                                echo date($configClass['date_time_format'],strtotime($row->order_date));
                            ?>
                        </td>
                        <td class="td_data" style="" data-label="<?php echo JText::_('OS_STATUS');?>">
                            <?php
                            if($row->order_status == "P"){
                                ?>
                                <span class="text-warning">
                                    <?php echo JText::_('OS_PENDING');?>
                                </span>
                                <?php
                            }elseif($row->order_status == "C"){
                                ?>
                                <span class="text-success">
                                    <?php echo JText::_('OS_CANCEL');?>
                                </span>
                                <?php
                            }else{
                                ?>
                                <span class="text-success">
                                    <?php
                                    echo OSBHelper::orderStatus(0,$row->order_status);?>
                                </span>
                                <?php
                            }
                            ?>
                        </td>
                        <td class="td_data" style="text-align:center;" data-label="<?php echo JText::_('OS_ORDER_DETAILS');?>">
                            <a href="<?php echo JRoute::_("index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=".$row->id."&ref=".md5($row->id)."&Itemid=".$jinput->getInt('Itemid',0));?>" title="<?php echo JText::_('OS_CLICK_HERE_TO_VIEW_ORDER_DETAILS');?>" />
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-square" viewBox="0 0 16 16">
								  <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
								  <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
								</svg>
                            </a>
                        </td>
                        <?php if($configClass['allow_cancel_request'] == 1){
                         ?>
                            <td class="td_data" style="text-align:center;" data-label="<?php echo JText::_('OS_REMOVE_ORDER');?>">
                                <a href="javascript:removeOrder(<?php echo $row->id?>,'<?php echo JText::_('OS_DO_YOU_WANT_T0_REMOVE_ORDER')?>','<?php echo JURI::root()?>','<?php echo $jinput->getInt('Itemid',0);?>');" title="<?php echo JText::_('OS_CLICK_HERE_TO_REMOVE_ORDER_DETAILS');?>" />
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
									  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
									</svg>
                                </a>
                            </td>
                        <?php }
                        ?>
						
						<?php
						if($configClass['value_sch_reminder_enable'] == 1 && $configClass['enable_reminder'] == 1)
						{
							if($row->receive_reminder == 1)
							{
								?>
								<td class="td_data" style="text-align:center;" data-label="ID">
									<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=default_changeReminderStatus&id=<?php echo $row->id?>&status=0&Itemid=<?php echo $jinput->getInt('Itemid',0); ?>" title="<?php echo JText::_('OS_I_DONT_WANT_TO_RECEIVE_REMINDER');?>">
										<span style="color:green;">
											<?php
											echo JText::_('JYES');
											?>
										</span>
									</a>
								</td>
								<?php
							}
							else
							{
								?>
								<td class="td_data" style="text-align:center;" data-label="ID">
									<a href="<?php echo JUri::root()?>index.php?option=com_osservicesbooking&task=default_changeReminderStatus&id=<?php echo $row->id?>&status=1&Itemid=<?php echo $jinput->getInt('Itemid',0); ?>" title="<?php echo JText::_('OS_I_WANT_TO_RECEIVE_REMINDER');?>">
										<span style="color:red;">
											<?php
											echo JText::_('JNO');
											?>
										</span>
									</a>
								</td>
								<?php
							}
						}
						?>
                        <td class="td_data" style="text-align:center;" data-label="ID">
                            <?php echo $row->id;?>
                        </td>
                    </tr>
					<?php
					if($row->countItems > 0)
					{
					?>
						<tr class="warning">
							<td colspan="7" style="width:100%;" data-label="">
								<a href="javascript:openOtherInformation(<?php echo $row->id;?>,'<?php echo JText::_('OS_OTHER_INFORMATION');?>');" id="href<?php echo $row->id;?>">
									[+]&nbsp;<?php echo JText::_('OS_OTHER_INFORMATION');?>
								</a>
								<div style="display:none;" id="order<?php echo $row->id?>">
									<?php
									OsAppscheduleDefault::getListOrderServices($row->id,0, $lists['date1'], $lists['date2']);
									?>
								</div>
							</td>
						</tr>
                    <?php
					}
                }
                ?>
                </tbody>
            </table>
            <?php
        }else{
            ?>
            <strong><?php echo JText::_('OS_NO_BOOKING_REQUEST');?></strong>
            <?php
        }
        ?>
    </div>
</div>
<input type="hidden" name="option" value="com_osservicesbooking"  />
<input type="hidden" name="task" value="default_customer" />
<input type="hidden" name="oid" id="oid" value="" />
<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid')?>" />
<input type="hidden" name="boxchecked" value="0" />
</form>
<?php
if($configClass['footer_content'] != ""){
    ?>
    <div class="osbfootercontent">
        <?php echo $configClass['footer_content'];?>
    </div>
    <?php
}
?>