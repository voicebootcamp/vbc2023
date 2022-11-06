<?php
/*------------------------------------------------------------------------
# employee.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;


class HTML_OSappscheduleEmployee{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function employee_list($option,$rows,$pageNav,$lists)
	{
		global $mainframe,$_jversion,$mapClass;
		JHtml::_('behavior.multiselect');
		JToolBarHelper::title(JText::_('OS_EMPLOYEE_MANAGE'),'user');
		JToolBarHelper::addNew('employee_add');
		if(count($rows) > 0){
			JToolBarHelper::editList('employee_edit');
			JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'employee_remove');
			JToolBarHelper::custom('employee_duplicate','copy.png','copy.png',JText::_('OS_DUPLICATE_EMPLOYEE'));
			JToolBarHelper::publish('employee_publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('employee_unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		$ordering = ($lists['order'] == 'a.ordering');
		
		$listOrder	= $lists['order'];
        $listDirn	= $lists['order_Dir'];

        $saveOrder	= $listOrder == 'a.ordering';

		if ($saveOrder)
        {
            $saveOrderingUrl = 'index.php?option=com_osservicesbooking&task=employee_saveorderAjax';
			if (OSBHelper::isJoomla4())
			{
				\Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				JHtml::_('sortablelist.sortable', 'employeeList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
			}
        }
	?>
		<form method="POST" action="index.php?option=<?php echo $option; ?>&task=employee_list" name="adminForm" id="adminForm">
			<table  width="100%" border="0">
				<tr>
					<td align="left">
						<input type="text" placeholder="<?php echo JText::_('OS_SEARCH');?>"	class="<?php echo $mapClass['input-medium']; ?> search-query" name="keyword" value="<?php echo $lists['keyword']; ?>">
                        <div class="btn-group">
                            <input type="submit" class="btn btn-warning" value="<?php echo JText::_('OS_SEARCH');?>">
                            <input type="reset"  class="btn btn-info" value="<?php echo JText::_('OS_RESET');?>"  onclick="this.form.keyword.value='';this.form.filter_service.value=0;this.form.filter_state.value='';this.form.submit();">
                        </div>
					</td>
					<td align="right">
						<?php echo $lists['filter_service'];?>
						<?php echo $lists['filter_state'];?>
					</td>
				</tr>
			</table>
		
			<table class="adminlist table table-striped" width="100%" id="employeeList">
				<thead>
					<tr>
						<th width="3%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'ordering', @$lists['order_Dir'], @$lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
						<th width="2%">
							
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="15%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_EMPLOYEE_NAME'), 'a.employee_name', @$lists['order_Dir'], @$lists['order'] ,'employee_list'); ?>
						</th>
						<th width="10%">
							<?php echo JText::_('OS_USER');?>
						</th>
						<th width="20%"><?php echo JText::_('OS_SERVICES'); ?></th>
						<th width="12%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_EMAIL'), 'a.employee_email', @$lists['order_Dir'], @$lists['order'] ,'employee_list'); ?>
						</th>
						<th width="10%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_PHONE'), 'a.employee_phone', @$lists['order_Dir'], @$lists['order'] ,'employee_list'); ?>
						</th>
						<th width="8%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_PUBLISHED'), 'a.published', @$lists['order_Dir'], @$lists['order'] ,'employee_list'); ?>
						</th>
						<th width="4%"  style="text-align:center;">
							<?php echo JText::_('OS_AVAIABILITY'); ?>
						</th>
						<th width="4%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_ID'), 'a.id', @$lists['order_Dir'], @$lists['order'] ,'employee_list'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="10" style="text-align:center;">
							<?php
								echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['order_Dir']); ?>" <?php endif; ?>>
				<?php
				$k = 0;
				$canChange = true;
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
					$row = $rows[$i];
					$checked = JHtml::_('grid.id', $i, $row->id);
					$link 		= JRoute::_( 'index.php?option='.$option.'&task=employee_edit&cid[]='. $row->id );
					$published 	= JHTML::_('jgrid.published', $row->published, $i, 'employee_');
				?>
					<tr class="<?php echo "row$k"; ?>" sortable-group-id="0" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
						<td class="order nowrap center hidden-phone" style="text-align:center;">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu"></span>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none;" name="order[]" value="<?php echo $row->ordering; ?>" />
							<?php endif; ?>
						</td>
						<td align="center"><?php echo $checked; ?></td>
						<td align="left"><a href="<?php echo $link; ?>"><?php echo $row->employee_name; ?></a></td>
						<td align="left">
							<?php
							if($row->user_id > 0){
								$user = JFactory::getUser($row->user_id);
								echo $user->username;
							}else{
								echo "N/A";
							}
							?>
						</td>
						<td align="left" style="font-size:11px;"><?php echo $row->service_name; ?></td>
						<td align="left" style="padding-right: 10px;"><?php echo $row->employee_email; ?> </td>
						<td align="left" style="padding-right: 10px;"><?php echo $row->employee_phone; ?></td>
						<td align="center" style="text-align:center;"><?php echo $published?></td>
						<td align="center" style="text-align:center;">
							<a href="index.php?option=com_osservicesbooking&task=employee_availability&eid=<?php echo $row->id?>" title="<?php echo JText::_('OS_MANAGE_AVAILABILITY_CALENDAR')?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar3" viewBox="0 0 16 16">
								  <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/>
								  <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
								</svg>
							</a>
						</td>
						<td align="center" style="text-align:center;"><?php echo $row->id; ?></td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="employee_list" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
			<input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
		</form>
		<?php
	}
	
	
	/**
	 * Agent field
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function employee_modify($option,$row,$lists,$rests,$services,$busy)
    {
		global $mainframe, $_jversion,$configClass,$jinput,$mapClass;
		$editor				= JEditor::getInstance(JFactory::getConfig()->get('editor'));
		$controlGroupClass  = $mapClass['control-group'];
		$controlLabelClass  = $mapClass['control-label'];
		$controlsClass		= $mapClass['controls'];
		$db					= JFactory::getDbo();
		$version 			= new JVersion();
		$_jversion			= $version->RELEASE;		
		$mainframe 			= JFactory::getApplication();
		$jinput->set( 'hidemainmenu', 1 );
		if ($row->id)
		{
			$title = ' ['.JText::_('OS_EDIT').']';
		}
		else
		{
			$title = ' ['.JText::_('OS_NEW').']';
		}
		JToolBarHelper::title(JText::_('OS_EMPLOYEE').$title,'user');
		JToolBarHelper::save('employee_save');
		JToolBarHelper::apply('employee_apply');
		JToolBarHelper::cancel('employee_cancel');
		$document	= JFactory::getDocument();
		$document->addScript(JUri::root(true).'/media/com_osservicesbooking/assets/js/admin-employee-default.js');
		JText::script('OS_PLEASE_ENTER_EMPLOYEE_NAME', true);
		JText::script('OS_PLEASE_ENTER_VALID_EMAIL_ADDRESS', true);
		?>
		<script language="javascript">
		function changeValue(id){
			var temp = document.getElementById(id);
			if(temp.value == 0){
				temp.value = 1;
			}else{
				temp.value = 0;
			}
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
		if (version_compare(JVERSION, '3.5', 'ge') && file_exists(JPATH_ROOT.'/media/jui/js/fielduser.min.js'))
		{
		?>
			<script src="<?php echo JUri::root()?>media/jui/js/fielduser.min.js" type="text/javascript"></script>
		<?php } ?>
		<script language="javascript" src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/js/ajax.js"></script>
		<script language="javascript">
		function removeBreakDate(rid){
			removeBreakDateAjax(rid,"<?php echo JURI::root()?>");
		}
		function removeBusyTime(bid)
        {
            removeBusyTimeAjax(bid,"<?php echo JURI::root()?>");
        }
		</script>
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span6'];?>">
					<fieldset class="form-horizontal options-form">
						<legend><?php echo JText::_('OS_DETAILS')?></legend>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('Select user'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<?php //echo $lists['user_id'];
								echo OSappscheduleEmployee::getUserInput($row->user_id,$row->id);
								?>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_EMPLOYEE_NAME'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<input class="<?php echo $mapClass['input-large']; ?> required" type="text" name="employee_name" id="employee_name" size="40" value="<?php echo $row->employee_name?>" />
								<div id="employee_name_invalid" style="display: none; color: red;"><?php echo JText::_('OS_THIS_FIELD_IS_REQUIRED')?></div>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_EMAIL'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<input class="<?php echo $mapClass['input-large']; ?> email" type="text" name="employee_email" id="employee_email" size="40" value="<?php echo $row->employee_email?>" >
								<input class="inputbox" type="checkbox" name="employee_send_email" id="employee_send_email" <?php if ($row->employee_send_email == 1) echo 'checked="checked"'?> value="<?php echo $row->employee_send_email; ?>" onClick="javascript:changeValue('employee_send_email');" />
								<?php echo JText::_('OS_SEND_EMAIL_WHEN_NEW_BOOKING_IS_MADE')?>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_PHONE'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<input class="<?php echo $mapClass['input-medium']; ?> imedium" type="text" name="employee_phone" id="employee_phone" value="<?php echo $row->employee_phone?>" />
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('Photo'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<?php
								if($row->employee_photo != "")
								{
									?>
									<img src="<?php echo JURI::root()?>images/osservicesbooking/employee/<?php echo $row->employee_photo?>" width="150">
									<div class="clr"></div>
									<input type="checkbox" name="remove_image" id="remove_image" value="0" onclick="javascript:changeValue('remove_image')"> Remove photo
									<div class="clr"></div>
									<?php
								}
								?>
								<input type="file" name="image" id="image" size="30" onchange="javascript:checkUploadPhotoFiles('image');" class="input-large form-control"/>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_NOTES'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								
								<?php
								echo $editor->display( 'employee_notes',  $row->employee_notes , '95%', '250', '75', '20' ,false);
								?>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_PUBLISHED_STATE'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<?php OSappscheduleConfiguration::showCheckboxfield('published',(int)$row->published);?>
							</div>
						</div>
					</fieldset>
					<?php
					if($configClass['integrate_gcalendar'] == 1)
					{
						?>
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('OS_GCALENDAR_CREDENTIALS')?></legend>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('Google Client ID'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input class="<?php echo $mapClass['input-large']; ?>" type="text" name="client_id" id="client_id"  value="<?php echo $row->client_id?>" />
									Get this from your Google App Credentials page.
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('App Name'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input class="<?php echo $mapClass['input-medium']; ?>" type="text" name="app_name" id="app_name"  value="<?php echo $row->app_name?>" />
									This is the name of the App you create on Google. You need to create a Google `App` so that OSB is allowed to talk to your calendar(s)
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('App Email Address'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input type="text" class="<?php echo $mapClass['input-medium']; ?>" name="app_email_address" id="app_email_address"  value="<?php echo $row->app_email_address?>" />
									Get this from your Google App Credentials page. You will also need to share your calendar to this email address.
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('P12 Key filename'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input type="text" class="<?php echo $mapClass['input-medium']; ?>" name="p12_key_filename" id="p12_key_filename"  value="<?php echo $row->p12_key_filename;?>" />
									This is the key file provided by Google and uploaded to your site.
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_GCALENDAR_ID'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input type="text" class="<?php echo $mapClass['input-large']; ?>" name="gcalendarid" id="gcalendarid" value="<?php echo $row->gcalendarid?>" />
									This is obtained on the Google Calendar 'Calendar Settings' screen, Calendar Address section.
								</div>
							</div>
						</fieldset>
						<?php
					}
					?>
					<fieldset class="form-horizontal options-form">
						<legend><?php echo JText::_('OS_SERVICES')?></legend>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php //
								if($row->id == 0)
								{
									echo JText::_('OS_AFTER_SAVING_THIS_EMPLOYEE_YOU_WILL_BE_ABLE_TO_ASSIGN_EMPLOYEE_TO_SERVICES');
								}
								else
								{
								?>
									<table class="table table-striped">
										<thead>
											<th width="5%">
												#
											</th>
											<th width="15%">
												<?php echo JText::_('OS_SERVICE');?>
											</th>
											<th width="15%">
												<?php echo JText::_('OS_VENUE');?>
											</th>
											<th width="25%">
												<?php echo JText::_('OS_WORKING_DATE');?>
											</th>
											<th width="30%">
												<?php echo JText::_('OS_BREAK_TIME');?>
											</th>
											<th width="10%">
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
													if($relation->vid > 0){
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
													<td style="text-align:center;">
														<?php echo $i + 1;?>
													</td>
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
														if($count > 0){
															echo "<font color='green'>".implode(", ",$workingdateArr)."</font>";
														}else{
															echo "<font color='red'>".JText::_('OS_NO_WORKING_IN_THIS_SERVICE')."</font>";
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
														<a href="index.php?option=com_osservicesbooking&task=employee_setupbreaktime&eid=<?php echo $row->id?>&sid=<?php echo $service->id?>" title="<?php echo JText::_('OS_CONFIGURE_EMPLOYEE_WITH_THIS_SERVICE');?>">
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
									<?php
									}
									?>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="<?php echo $mapClass['span6'];?>">
					<fieldset class="form-horizontal options-form">
						<legend><?php echo JText::_('OS_REST_DAYS')?></legend>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php echo JText::_('OS_REST_DAYS_EXPLAIN'); ?>
								<BR />
								<div id="rest_div">
								<?php
								if(count($rests) > 0){
									?>
									<table width="100%" style="border:1px solid #CCC;">
										<tr>
											<td width="30%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC;">
												<?php echo JText::_('OS_DATE')?>
											</td>
											<td width="20%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC;">
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
													<a href="javascript:removeBreakDate(<?php echo $rest->id?>)">
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
								}
								?>
								</div>
								<BR />
								<B><?php echo JText::_('OS_ADD_REST_DAY')?></B>
								<BR />
								<?php
								for($i=1;$i<=5;$i++){
									echo JText::_('OS_DATE');
									echo " #".$i.": ";
									echo JText::_('OS_FROM').": ";
									echo JHTML::_('calendar','', 'date'.$i, 'date'.$i, '%Y-%m-%d', array('class'=>'input-small ishort', 'size'=>'19',  'maxlength'=>'19')); 
									echo " - ";
									echo JText::_('OS_TO').": ";
									echo JHTML::_('calendar','', 'date_to_'.$i, 'date_to_'.$i, '%Y-%m-%d', array('class'=>'input-small ishort', 'size'=>'19',  'maxlength'=>'19')); 
									echo "<BR />";
								}
								?>
							</div>
						</div>
					</fieldset>

					<fieldset class="form-horizontal options-form">
						<legend><?php echo JText::_('OS_BUSY_TIME')?></legend>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php echo JText::_('OS_BUSY_TIME_EXPLAIN'); ?>
								<BR />
								<div id="busy_div">
								<?php
								if(count($busy) > 0){
									?>
									<table width="100%" style="border:1px solid #CCC;">
										<tr>
											<td width="30%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC;">
												<?php echo JText::_('OS_DATE')?>
											</td>
											<td width="20%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC;">
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
													<a href="javascript:removeBusyTime(<?php echo $b->id?>)">
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

								echo JHTML::_('calendar','', 'busy_date'.$i, 'busy_date'.$i, '%Y-%m-%d', array('class'=>'input-small form-control ishort', 'size'=>'19',  'maxlength'=>'19'));
								echo " - ";
								echo JText::_('OS_FROM').": ";
								echo "<input type='text' name='busy_from".$i."' id='busy_from".$i."' placeholder='00:00' class='input-mini ishort form-control imini busyfield'> ";
								echo JText::_('OS_TO').": ";
								echo "<input type='text' name='busy_to".$i."' id='busy_to".$i."' placeholder='00:00' class='input-mini form-control imini busyfield'>";
								echo "<BR />";
							}
							?>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<fieldset class="form-horizontal options-form">
						<legend><?php echo JText::_('OS_ADDITIONAL_PRICE_BY_HOUR')?></legend>
						<table width="100%" class="table table-striped"> 
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
												echo JHTML::_('select.genericlist',$lists['week_day'],'week_day'.$i,'class="input-small form-select"','value','text',$rs->week_date);
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
												<input type="text" name="extra_cost<?php echo $i?>" id="extra_cost<?php echo $i?>" class="input-mini form-control" size="5" value="<?php echo $rs->extra_cost?>" /> <?php echo $configClass['currency_format'];?>
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
												echo JHTML::_('select.genericlist',$lists['week_day'],'week_day'.$i,'class="input-small form-select"','value','text','');
												?>
											</td>
											<td width="20%" align="center">
												<?php
												echo JHTML::_('select.genericlist',$lists['hours'],'start_time'.$i,'class="input-small form-select"','value','text');
												?>
											</td>
											<td width="20%" align="center">
												<?php
												echo JHTML::_('select.genericlist',$lists['hours'],'end_time'.$i,'class="input-small form-select"','value','text');
												?>
											</td>
											<td width="20%" align="center">
												<input type="text" name="extra_cost<?php echo $i?>" id="extra_cost<?php echo $i?>" class="input-mini form-control" size="5" /> <?php echo $configClass['currency_format'];?>
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
					</fieldset>
				</div>
			</div>
			
			<input type="hidden" name="option" value="<?php echo $option?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" id="id" value="<?php echo (int) $row->id?>" />
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="MAX_FILE_SIZE" value="9000000" />
		</form>
		<script type="text/javascript">
			var live_site = "<?php echo JUri::root(); ?>";
			function populateUserData(){
				var id = jQuery('#user_id_id').val();
				var eid = jQuery('#id').val();
				if(eid == ""){
					eid = 0;
				}
				populateEmployeeDataAjax(id,eid,live_site);
			}

			window.addEvent('domready',  function() {
				$$('.required').each( function(el) {					
					el.onblur= function(){
						if (this.value == ''){
							$$('#' + this.id + "_invalid").setStyle('display','');
							this.addClass("invalid");
						}
					}
					el.onkeyup= function(){
						if (this.value == ''){
							$$('#' + this.id + "_invalid").setStyle('display','');
							this.addClass("invalid");
						}else{
							$$('#' + this.id + "_invalid").setStyle('display','none');
							this.removeClass("invalid");
						}
					}
				});

				$$('.email').each( function(el){
					el.onblur= function(){
						var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
						if (this.value != '' && !filter.test(this.value)){
							this.addClass("invalid");
							$$('#employee_send_email').each( function(el) {
								el.checked = false;
							})
						}else{
							this.removeClass("invalid");
						}
					}
				});
			});		
			
			 function changeValue(id){
				var temp = document.getElementById(id);
				if(temp.value == 0){
					temp.value = 1;
				}else{
					temp.value = 0;
				}
			}
		</script>
		<?php
	}
	
	/**
	 * Calendar Manager
	 *
	 * @param unknown_type $employee
	 */
	static function calendarManage($employee){
		global $mainframe;
		JToolBarHelper::title( JText::_('OS_MANAGE_AVAIABILITY_CALENDAR')."[".$employee->employee_name."]");
		JToolBarHelper::cancel('employee_gotoemployeelist');
		?>
		<style>
		.header_calendar{
			font-weight:bold;
			text-align:center;
			padding:5px;
			font-size:14px;
		}
		.td_calendar_date{
			font-size:13px;
			text-align:center;
			vertical-align:middle;
			border:1px dotted #CCC !important;
			padding:5px;
			font-weight:bold;
		}
		</style>
		<form method="POST" action="index.php?option=com_oscalendar" name="adminForm" id="adminForm">
		<table class="admintable" width="100%">
			<tr>
				<td width="100%">
					<?php
					$year = JFactory::getApplication()->input->getString('year',date("Y",time()));
					$month =  intval(JFactory::getApplication()->input->getString('month',date("m",time())));
					OSBHelper::initCalendarInBackend($employee->id,$year,$month);
					?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="task"    	id="task" 	value=""/>
		<input type="hidden" name="option"  	id="option" value="com_osservicesbooking"/>
		<input type="hidden" name="boxchecked"				value="0" />
		<input type="hidden" name="year"    	id="year" 	value="<?php echo $year;?>">
		<input type="hidden" name="month"   	id="month" 	value="<?php echo $month;?>">
		</form>
		<?php
	}
	
	/**
	 * Break time form
	 *
	 * @param unknown_type $service
	 * @param unknown_type $employee
	 * @param unknown_type $lists
	 */
	static function breaktimeForm($service,$employee,$lists,$customs){
		global $mainframe;
		JToolbarHelper::title(JText::_('OS_SETUP_BREAKTIME_OF_EMPLOYEE')." [".$employee->employee_name."] ".JText::_('OS_OF')." ".JText::_('OS_SERVICE')." [".$service->service_name."]");
		JToolbarHelper::save('employee_savebreaktime');
		JToolbarHelper::apply('employee_applybreaktime');
		JToolbarHelper::cancel('employee_gotoemployeeedit');
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking" name="adminForm" id="adminForm">
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
				<table width="80%" style="border:1px solid #CCC;">
					<tr>
						<td width="30%" class="headerajaxtd">
							<?php echo JText::_('OS_DATE')?>
						</td>
						<td width="20%" class="headerajaxtd">
							<?php echo JText::_('OS_REMOVE')?>
						</td>
					</tr>
					<?php
					for($i=0;$i<count($customs);$i++){
						$rest = $customs[$i];
						?>
						<tr>
							<td width="30%" align="left" style="text-align:center;">
								<?php
								$timestemp = strtotime($rest->bdate);
								echo date("D, jS M Y",  $timestemp);
								echo "&nbsp;&nbsp;";
								echo $rest->bstart." - ".$rest->bend;
								?>
							</td>
							<td width="30%" align="center">
								<a href="javascript:removeCustomBreakDate(<?php echo $rest->id?>,'<?php echo JUri::root();?>')">
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
			?>
		</div>
		<?php
		echo "<strong>".Jtext::_('OS_ADD_BREAKTIME').'</strong>:&nbsp;';
		echo JHTML::_('calendar','', 'bdate', 'bdate', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'19',  'maxlength'=>'19'));
		$hourArray = OSappscheduleEmployee::generateHoursIncludeSecond();
		echo "&nbsp;&nbsp;".Jtext::_('OS_FROM').':&nbsp;';
		echo JHTML::_('select.genericlist',$hourArray,'bstart','class="input-small form-select" style="width:150px;display:inline;"','value','text');
		echo "&nbsp;&nbsp;".Jtext::_('OS_TO').':&nbsp;';
		echo JHTML::_('select.genericlist',$hourArray,'bend','class="input-small form-select" style="width:150px;display:inline;"','value','text');
		echo "&nbsp;&nbsp;";
		?>
		<input type="button" value="<?php echo Jtext::_('OS_SAVE');?>" class="btn btn-warning" onClick="javascript:saveCustomBreakTime('<?php echo JUri::root();?>');" />
		<input type="hidden" name="task" id="task" value=""/>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="eid" id="eid" value="<?php echo (int)$employee->id?>" />
		<input type="hidden" name="sid" id="sid" value="<?php echo (int)$service->id?>" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JUri::root()?>" />
		</form>
		<?php
	}
}
?>