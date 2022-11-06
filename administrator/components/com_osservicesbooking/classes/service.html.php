<?php
/*------------------------------------------------------------------------
# service.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
class HTML_OSappscheduleService
{
	/**
	 * Install sample data confirm form
	 *
	 * @param unknown_type $option
	 */
	static function confirmInstallSampleDataForm($option)
    {
		global $mainframe;
		JToolBarHelper::title(JText::_('OS_INSTALLSAMPLEDATA'));
		JToolBarHelper::cancel();
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<script language="javascript">
		function activeContinueButton(){
			checkbox = document.getElementById('agree');
			startbutton = document.getElementById('startbutton');
			if(checkbox.value == 0){
				checkbox.value = 1;
				startbutton.disabled = false;
			}else{
				checkbox.value = 0;
				startbutton.disabled = true;
			}
		}
		</script>
		<form method="POST" action="index.php?option=com_osservicesbooking" name="adminForm" id="adminForm">
		<table 	  width="100%" class="admintable">
			<tr>
				<td width="100%" style="padding:20px;">
					
					<table   width="100%" style="border-bottom:1px solid #CCC;border-right:1px solid #CCC;background-color:#FFF;">
						<tr>
							<td width="100%" style="text-align:left;padding:20px;">
								<strong>
								    <?php echo JText::_('OS_NOTICE')?>:
                                </strong>
								<br />
								<br />
								To install new sample data, we should empty service, employee and custom fields data tables. So please backup those data before install sample data. 
							</td>
						</tr>
						<tr>
							<td style="padding:20px;text-align:center;border:1px solid red;background-color:pink;font-weight:bold;">
								<input type="checkbox" name="agree" id="agree" value="0" onclick="javascript:activeContinueButton()">&nbsp;
								<?php
									echo JText::_('OS_READ_AND_ACCEPTED');
								?>
								<BR><BR>
								<input type="submit" id="startbutton" class="btn btn-info" value="<?php echo JText::_('OS_START_INSTALL')?>" disabled="true">
								
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="com_osservicesbooking">
		<input type="hidden" name="task" value="service_installdata">
		<input type="hidden" name="boxchecked" value="0">
		</form>
		<?php
	}
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function service_list($option,$rows,$pageNav,$lists)
	{
		global $mainframe,$_jversion,$configClass,$mapClass;
		JHtml::_('behavior.multiselect');
		JToolBarHelper::title(JText::_('OS_MANAGE_SERVICES'),'folder');
		JToolBarHelper::addNew('service_add');
		if(count($rows) > 0){
			JToolBarHelper::editList('service_edit');
			JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'service_remove');
			JToolBarHelper::custom('service_duplicate','copy.png','copy.png',JText::_('OS_DUPLICATE_SERVICE'));
			JToolBarHelper::publish('service_publish');
			JToolBarHelper::unpublish('service_unpublish');
		}
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		$ordering = ($lists['order'] == 'ordering');

		$listOrder	= $lists['order'];
        $listDirn	= $lists['order_Dir'];

        $saveOrder	= $listOrder == 'ordering';
        $ordering	= ($listOrder == 'ordering');

        if ($saveOrder)
        {
            $saveOrderingUrl = 'index.php?option=com_osservicesbooking&task=service_saveorderAjax';
			if (OSBHelper::isJoomla4())
			{
				\Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
			}
        }

        $customOptions = array(
            'filtersHidden'       => true,
            'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
            'orderFieldSelector'  => '#filter_full_ordering'
        );

		if (count($rows))
        {
			$ordering = array();
            foreach ($rows as $item)
            {
                $ordering[0][] = $item->id;
            }
        }
		?>
		
		<form method="POST" action="index.php?option=<?php echo $option; ?>&task=service_list" name="adminForm" id="adminForm">
			<table width="100%">
				<tr>
					<td align="left">
						<input type="text" class="<?php echo $mapClass['input-medium']; ?> search-query" placeholder="<?php echo JText::_('OS_SEARCH');?>"	name="keyword" value="<?php echo  $lists['keyword']; ?>" />
                        <div class="btn-group">
                            <input type="submit" class="btn btn-warning" value="<?php echo JText::_('OS_SEARCH');?>" />
                            <input type="reset"  class="btn btn-info" value="<?php echo JText::_('OS_RESET');?>" onclick="this.form.keyword.value='';this.form.filter_state.value='';this.form.submit();" />
                        </div>
					</td>
					<td align="right" style="text-align:right;">
						<?php echo $lists['filter_state'];?>
					</td>
				</tr>
			</table>
	
			<table class="adminlist table table-striped" width="100%">
				<thead>
					<tr>
						<th width="3%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'ordering', @$lists['order_Dir'], @$lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
						<th width="2%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="20%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_SERVICE_NAME'), 'service_name', @$lists['order_Dir'], @$lists['order'] ,'service_list'); ?>
						</th>
						<th width="15%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_TIME_SLOT'), 'service_time_type', @$lists['order_Dir'], @$lists['order'] ,'service_list'); ?>
						</th>
						<th width="10%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_SERVICE_PRICE'), 'service_price', @$lists['order_Dir'], @$lists['order'] ,'service_list'); ?>
						</th>
						<th width="7%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_ACCESS'), 'access', @$lists['order_Dir'], @$lists['order'] ,'service_list'); ?>
						</th>
						<th width="10%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_PUBLISHED'), 'published', @$lists['order_Dir'], @$lists['order'] ,'service_list'); ?>
						</th>
						<th width="10%" style="text-align:center;">
							<?php echo JText::_('OS_AVAILABILITY');?>
						</th>
						<th width="5%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_ID'), 'id', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="12" style="text-align:center;">
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
				$db = JFactory::getDbo();
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
					$row = $rows[$i];
					$checked = JHtml::_('grid.id', $i, $row->id);
					$link 		= JRoute::_( 'index.php?option='.$option.'&task=service_edit&cid[]='. $row->id );
					$published 	= JHTML::_('jgrid.published', $row->published, $i, 'service_');
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
						<td align="left" style="width:20%;"><a href="<?php echo $link; ?>"><?php echo $row->service_name; ?></a>
							<br />
							<span style="font-size:11px;">
							<strong><?php echo JText::_('OS_CATEGORY')?>:</strong>
							<?php
							$db->setQuery("Select category_name from #__app_sch_categories where id = '$row->category_id'");
							$category_name = $db->loadResult();
							echo $category_name;
							
							$db->setQuery("Select concat(a.address,' ',a.city) as vname from #__app_sch_venues as a inner join #__app_sch_venue_services as b on a.id = b.vid where b.sid = '$row->id'");
							$venues = $db->loadColumn(0);
							if(count($venues) > 0){
								?>
								<BR />
								<strong><?php echo JText::_('OS_VENUE')?>:</strong>
								<?php 
								echo implode(", ",$venues);
							}
							?>
							<div class="clearfix"></div>
							<strong><?php echo JText::_('OS_ASSIGNED_EMPLOYEES');?>:</strong>
							<?php
							$employees = OSappscheduleService::getAssignedEmployees($row->id);
							if(count($employees))
							{
								echo implode(", ", $employees);
							}
							else
							{
								echo JText::_('OS_NO_EMPLOYEES_ASSIGNED');
							}
							?>
							</span>
						</td>
						<td align="center" style="text-align:center;width:15%;">
							<?php
							if($row->service_time_type == 0){
								echo JText::_('OS_NORMALLY_TIME_SLOT');
							}elseif($row->service_time_type == 1){
								echo JText::_('OS_CUSTOM_TIME_SLOT');
							}
							if($row->service_time_type == 1){?>
								<a href="index.php?option=com_osservicesbooking&task=service_managetimeslots&sid=<?php echo $row->id;?>" title="<?php echo JText::_('OS_MANAGE_CUSTOM_TIME_SLOTS');?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar-week-fill" viewBox="0 0 16 16">
									  <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM9.5 7h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm3 0h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zM2 10.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z"/>
									</svg>
								</a>
								<?php 
								$db->setQuery("Select count(id) from #__app_sch_custom_time_slots where sid = '$row->id'");
								$count = $db->loadResult();
								if($count == 0){
									?>
									<div class="clearfix"></div>
									<span class="notice"><?php echo JText::_('OS_NO_AVAILABLE_TIMESLOTS');?></span>
									<?php
								}
							} ?>
						</td>
						<td align="center" style="text-align:center;"><?php echo number_format($row->service_price,2,'.','');?> <?php echo $configClass['currency_format']?>
						</td>
						<!--
						<td align="center"><?php echo $row->service_length?></td>
						<td align="center"><?php echo $row->service_total?></td>
						-->
						<td align="center" style="text-align:center;">
							<?php
                            echo OSBHelper::returnAccessLevel($row->access);
							?>
						</td>					
						<td align="center" style="text-align:center;"><?php echo $published?></td>
						<td align="center" style="text-align:center;">
							<a href="index.php?option=com_osservicesbooking&task=service_manageavailability&id=<?php echo $row->id;?>" title="<?php echo JText::_('OS_MANAGE_AVAILABILITY');?>">
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
			<input type="hidden" name="task" value="service_list"  />
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
	static function service_modify($option,$row,$lists,$customs,$translatable)
	{
		global $mainframe, $_jversion,$configClass,$languages,$jinput,$mapClass;
		$db			= JFactory::getDbo();
		$controlGroupClass  = $mapClass['control-group'];
		$controlLabelClass  = $mapClass['control-label'];
		$controlsClass		= $mapClass['controls'];
		$editor		= JEditor::getInstance(JFactory::getConfig()->get('editor'));
		$version 	= new JVersion();
		$_jversion	= $version->RELEASE;		
		$mainframe 	= JFactory::getApplication();
		$jinput->set( 'hidemainmenu', 1 );
		if ($row->id){
			$title = ' ['.JText::_('OS_EDIT').']';
		}else{
			$title = ' ['.JText::_('OS_NEW').']';
		}
		JToolBarHelper::title(JText::_('OS_SERVICES').$title,'folder');
		JToolBarHelper::save('service_save');
		JToolBarHelper::apply('service_apply');
		JToolBarHelper::cancel('service_cancel');
		JText::script('OS_FILE_UPLOAD_IS_NOT_IMAGE');
		$document	= JFactory::getDocument();
		$document->addScript(JUri::root(true).'/media/com_osservicesbooking/assets/js/admin-service-default.js');
		JText::script('OS_PLEASE_ENTER_SERVICE_TITLE', true);
		JText::script('OS_PLEASE_ENTER_SERVICE_PRICE', true);
		JText::script('OS_PLEASE_ENTER_VALID_SERVICE_PRICE', true);

		if (OSBHelper::isJoomla4())
		{
			$tabApiPrefix = 'uitab.';

			Factory::getDocument()->getWebAssetManager()->useScript('showon');
		}
		else
		{
			$tabApiPrefix = 'bootstrap.';

			HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true));
		}
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
	
		function showDiv()
		{
			var service_time_type = document.getElementById('service_time_type');
			var normal_time_slot_div 	  = document.getElementById('normal_time_slot_div');
			var custom_time_slot_div 	  = document.getElementById('custom_time_slot_div');
			if(service_time_type.value == 0)
			{
				normal_time_slot_div.style.display = "block";
				custom_time_slot_div.style.display = "none";
			}
			else
			{
				normal_time_slot_div.style.display = "none";
				custom_time_slot_div.style.display = "block";
			}
		}
		
		function resetRow(id){
			var start_time   = document.getElementById('start_hour' + id);
			start_time.value = "";
			var start_min   = document.getElementById('start_min' + id);
			start_min.value = "";
			var end_time     = document.getElementById('end_hour' + id);
			end_time.value   = "";
			var end_min   = document.getElementById('end_min' + id);
			end_min.value = "";
			var nslots = document.getElementById('nslots' + id);
			nslots.value = "";
		}
		</script>

		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php 
		if ($translatable)
		{
			echo JHtml::_($tabApiPrefix.'startTabSet', 'translation', array('active' => 'general-page'));
				echo JHtml::_($tabApiPrefix.'addTab', 'translation', 'general-page', JText::_('OS_GENERAL', true));
		}
		if(OSBHelper::isJoomla4())
		{
			$extraClass = "joomla4";
		}
		?>
		<div class="<?php echo $mapClass['row-fluid'];?> <?php echo $extraClass; ?>">
			<div class="<?php echo $mapClass['span12'];?>">
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span6'];?>">
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('OS_DETAILS')?></legend>
							
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_SERVICE_NAME'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input class="<?php echo $mapClass['input-large']; ?> required ilarge" type="text" name="service_name" id="service_name" size="70" value="<?php echo $row->service_name?>" />
									<div id="service_name_invalid" style="display: none; color: red;"><?php echo JText::_('OS_THIS_FIELD_IS_REQUIRED')?></div>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_CATEGORY_NAME'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['category']?>
								</div>
							</div>
							<?php
							if(OSBHelper::isAvailableVenue())
							{
							?>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_VENUE'); ?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										echo $lists['venue_list'];
										?>
									</div>
								</div>
							<?php
							}	
							?>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_PHOTO'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php
									if($row->service_photo != "")
									{
										?>
										<img src="<?php echo JURI::root()?>images/osservicesbooking/services/<?php echo $row->service_photo?>" width="150">
										<div class="clr"></div>
										<input type="checkbox" name="remove_image" id="remove_image" value="0" onclick="javascript:changeValue('remove_image')"  /> Remove photo
										<div class="clr"></div>
										<?php
									}
									?>
									<input type="file" name="image" id="image" size="30" class="form-control input-medium ilarge" />
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_COLOR_OF_SERVICE'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php
									if($row->service_color != "")
									{
										$background = "background-color:".$row->service_color."; ";
									}
									else
									{
										$background = "";
									}
									?>
									<input type="text" style="<?php echo $background;?>" class="<?php echo $mapClass['input-small']; ?> ishort" name="service_color" id="service_color "value="<?php echo $row->service_color ; ?>" />
									<BR />
									<?php
									echo JText::_('OS_COLOR_OF_SERVICE_EXPLAIN');
									?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_SERVICE_DESCRIPTION'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php
									echo $editor->display( 'service_description',  stripslashes($row->service_description) , '95%', '250', '75', '20' ,false);
									?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_ORDER'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['ordering'];?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_ACCESS'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['access'];?>
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
						if($configClass['disable_payments'] == 1)
						{
							?>
							<fieldset class="form-horizontal options-form" id="slotdiscountfieldset">
								<legend><?php echo JText::_('OS_PAYMENT_INFORMATION')?></legend>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_PAYMENT_METHODS'); ?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										echo $lists['payment_methods'];
										?>
										<BR />
										<span style="font-style: italic;font-weight: normal; color:gray;">
											<?php echo JText::_('OS_PAYMENT_METHODS_EXPLAIN'); ?>
										</span>
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_PAYPAL_EMAIL'); ?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="text" class="<?php echo $mapClass['input-large']; ?> imedium" name="paypal_id" value="<?php echo $row->paypal_id ; ?>" />
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_API_LOGIN'); ?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="text" class="<?php echo $mapClass['input-large']; ?> imedium" name="authorize_api_login" value="<?php echo $row->authorize_api_login ; ?>" />
									</div>
								</div>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_TRANSACTION_KEY'); ?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<input type="text" class="<?php echo $mapClass['input-large']; ?> imedium" name="authorize_transaction_key" value="<?php echo $row->authorize_transaction_key ; ?>" />
									</div>
								</div>
							</fieldset>
							<?php					
						}
						?>
					</div>
					<div class="<?php echo $mapClass['span6'];?>">
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('OS_SERVICE_PRICE_INFORMATION')?></legend>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span12'];?>">
									<?php echo JText::_('OS_SERVICE_PRICE'); ?>:
									<input class="<?php echo $mapClass['input-small']; ?> ishort required"  type="text" name="service_price" id="service_price" size="5" value="<?php echo $row->service_price?>" /> <?php echo $configClass['currency_format'];?>
									<div id="service_price_invalid" style="display: none; color: red;"><?php echo JText::_('OS_THIS_FIELD_IS_REQUIRED')?></div>
									<div id="service_price_value_error" style="display: none; color: red;"><?php echo JText::_('OS_PLEASE_ENTER_A_VALID_NUMBER'); ?></div>
									<BR />
									<?php
									if($row->id > 0)
									{
										?>
										<BR />
										<h4>
											<?php echo JText::_('OS_PRICE_ADJUSTMENT')?>
										</h4>
										<strong>
											<?php echo JText::_('OS_BY_DATE_IN_WEEK'); ?>
										</strong>
										<div class="clearfix"></div>
										<table width="100%" style="border:1px solid #CCC;">
											<tr>
												<td width="30%" class="headerajaxtd">
													<?php echo JText::_('OS_DATE_IN_WEEK')?>
												</td>
												<td width="30%" class="headerajaxtd">
													<?php echo JText::_('OS_SAME_AS_ORIGINAL')?>
												</td>
												<td width="30%" class="headerajaxtd">
													<?php echo JText::_('OS_PRICE')?> <?php echo $configClass['currency_format'];?>
												</td>
											</tr>
											<?php
											$dateArr = array(JText::_('OS_MON'),JText::_('OS_TUE'),JText::_('OS_WED'),JText::_('OS_THU'),JText::_('OS_FRI'),JText::_('OS_SAT'),JText::_('OS_SUN'));
											for($i=1;$i<=7;$i++)
											{ 
												if($i % 2 == 0)
												{
													$bgcolor = "#efefef";
												}
												else
												{
													$bgcolor = "#FFF";
												}
												$db->setQuery("Select * from #__app_sch_service_price_adjustment where date_in_week = '$i' and sid = '$row->id'");
												$price = $db->loadObject();
												if(!isset($price->same_as_original) || $price->same_as_original == 1)
												{
													$checked = "checked";
													$disable = "disabled";
													$value   = $row->service_price;
												}
												else
												{
													$checked = "";
													$disable = "";
													$value   = $price->price;
												}
											?>
											<tr>
												<td width="30%" align="left" style="text-align:center;background-color:<?php echo $bgcolor;?>;">
													<?php
													echo $dateArr[$i-1];
													?>
												</td>
												<td width="30%" align="left" style="text-align:center;background-color:<?php echo $bgcolor;?>;">
													<input onClick="javascript:addCustomPricebyDate(<?php echo $i;?>);" id="same<?php echo $i?>" name="same<?php echo $i?>" type="checkbox" <?php echo $checked;?> id="date<?php echo $i?>" value="1" /> <span style="color:#CCC;">(<?php echo $row->service_price?>)</span>
												</td>
												<td width="30%" align="left" style="text-align:center;background-color:<?php echo $bgcolor;?>;">
													<input class="input-mini form-control imini"  <?php echo $disable;?> type="text" name="price<?php echo $i?>" id="price<?php echo $i?>" size="5" value="<?php echo $value;?>" /> 
												</td>
											</tr>
											<?php 
											}
											?>	
										</table>
										<BR /><BR />
										<strong>
											<?php echo JText::_('OS_BY_SPECIFIC_DATE_PERIOD'); ?>
										</strong>
										<div class="clearfix"></div>
										<div id="rest_div">
										<?php
										if(count($customs) > 0){
											?>
											<table width="100%" style="border:1px solid #CCC;">
												<tr>
													<td width="40%" class="headerajaxtd">
														<?php echo JText::_('OS_DATE_PERIOD')?>
													</td>
													<td width="20%" class="headerajaxtd">
														<?php echo JText::_('OS_PRICE')?> <?php echo $configClass['currency_format'];?>
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
															$timestemp = strtotime($rest->cstart);
															$timestemp1 = strtotime($rest->cend);
															echo date("D, jS M Y",  $timestemp);
															echo "&nbsp;-&nbsp;";
															echo date("D, jS M Y",  $timestemp1);
															?>
														</td>
														<td width="30%" align="left" style="text-align:center;">
															<?php
															echo $rest->amount;
															?>
														</td>
														<td width="30%" align="center">
															<a href="javascript:removeCustomPrice(<?php echo $rest->id?>,<?php echo $row->id?>,'<?php echo JUri::root();?>')" title="Remove price">
																<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
																  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
																</svg>
															</a>
														</td>
													</tr>
													<?php
												}
												?>
											</table>
											<BR /><BR />
											<?php 
										}
										?>
										</div>
										<?php 
										echo "<strong>".Jtext::_('OS_PRICE_ADJUSTMENT_BY_SPECIAL_PERIOD').'</strong>:&nbsp;';
										echo "<strong>".Jtext::_('OS_FROM')."</strong>&nbsp;&nbsp;".JHTML::_('calendar','', 'cstart', 'cstart', '%Y-%m-%d', array('class'=>'input-small ishort', 'size'=>'19',  'maxlength'=>'19'));
										echo "&nbsp;&nbsp;<strong>".Jtext::_('OS_TO')."</strong>&nbsp;&nbsp;".JHTML::_('calendar','', 'cend', 'cend', '%Y-%m-%d', array('class'=>'input-small ishort', 'size'=>'19',  'maxlength'=>'19'));
										echo "&nbsp;&nbsp;<strong>".Jtext::_('OS_PRICE')."</strong>&nbsp;&nbsp;";
										?>
										<input type="text" name="camount" id="camount" class="<?php echo $mapClass['input-small']; ?> imini"/>
										<input type="button" value="<?php echo JText::_('OS_SAVE');?>" class="btn btn-warning" onClick="javascript:saveCustomPrice('<?php echo JUri::root();?>');"/> 
										<input type="hidden" name="live_site" id="live_site" value="<?php echo JUri::root()?>" />
										<?php 
									}
									?>
								</div>
							</div>
						</fieldset>
						<?php
						if($configClass['early_bird'] == 1)
						{				
						?>
							<fieldset class="form-horizontal options-form" id="earlybirdfieldset">
								<legend><?php echo JText::_('OS_EARLY_BIRD')?></legend>

								<input type="text" name="early_bird_amount" id="early_bird_amount" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->early_bird_amount;?>" />
								<?php echo $lists['early_bird_type'];?>
								<input type="text" name="early_bird_days" id="early_bird_days" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->early_bird_days;?>" />
								&nbsp;
								<?php echo Jtext::_('OS_DAYS');?>
								<BR />
								<?php echo Jtext::_('OS_EARLY_BIRD_EXPLAIN');?>
							</fieldset>
						<?php
						}			
						?>
						<?php
						if($configClass['enable_slots_discount'] == 1 && $row->id > 0 && $row->service_time_type == 1)
						{				
						?>
							<fieldset class="form-horizontal options-form" id="slotdiscountfieldset">
								<legend><?php echo JText::_('OS_DISCOUNT_BY_NUMBERSLOTS')?></legend>

								<input type="text" name="discount_amount" id="discount_amount" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->discount_amount;?>" />
								<?php echo $lists['discount_type'];?>
								&nbsp;
								<?php echo JText::_('OS_WHEN_CUSTOMER_ADD_MORE_THAN');?>&nbsp;
								<input type="text" name="discount_timeslots" id="discount_timeslots" class="<?php echo $mapClass['input-small']; ?> imini" value="<?php echo $row->discount_timeslots;?>" />
								&nbsp;
								<?php echo Jtext::_('OS_SLOTS');?>
								<BR />
								<?php echo Jtext::_('OS_DISCOUNT_BY_NUMBERSLOTS_EXPLAIN');?>
							</fieldset>
						<?php
						}			
						?>
						<fieldset class="form-horizontal options-form" id="slotdiscountfieldset">
							<legend><?php echo JText::_('OS_BOOKING_INFORMATION')?></legend>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<?php echo JText::_('OS_SERVICE_LENGTH_MINUTES'); ?>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input class="<?php echo $mapClass['input-small']; ?> required calculatored imini" type="text" name="service_length" id="service_length" size="5" value="<?php echo $row->service_length?>" /><span style="color:red;"><?php echo JText::_('OS_ONLY_FOR_NORMAL_TIME_SLOTS')?></span>
									<div id="service_length_invalid" style="display: none; color: red;"><?php echo JText::_('OS_THIS_FIELD_IS_REQUIRED')?></div>
									<div id="service_length_value_error" style="display: none; color: red;"><?php echo JText::_('OS_PLEASE_ENTER_ONLY_DIGITS'); ?></div>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<span class="editlinktip hasTip" title="<?php echo JText::_('OS_REPEAT_BOOKING');?>::<?php echo JText::_('OS_REPEAT_BOOKING_EXPLAIN'); ?>">
										<?php echo JText::_('OS_REPEAT_BOOKING'); ?>: 
									</span>
								</div>
								<div class="<?php echo $controlsClass;?>" style="padding-left:15px;">
									<?php
									if($row->repeat_day == 1)
									{
										$daycheck = "checked";
									}
									else
									{
										$daycheck = "";
									}
									if($row->repeat_week == 1)
									{
										$weekcheck = "checked";
									}
									else
									{
										$weekcheck = "";
									}
									if($row->repeat_fortnight == 1)
									{
										$biweekcheck = "checked";
									}
									else
									{
										$biweekcheck = "";
									}
									if($row->repeat_month == 1)
									{
										$monthcheck = "checked";
									}
									else
									{
										$monthcheck = "";
									}
									?>
									<input type="checkbox" name="repeat_day" id="repeat_day" value="<?php echo $row->repeat_day?>" <?php echo $daycheck;?> onclick="javascript:changeValue('repeat_day')"/>  <?php echo JText::_('OS_REPEAT_DAY')?>
									<BR />
									<input type="checkbox" name="repeat_week" id="repeat_week" value="<?php echo $row->repeat_week;?>" <?php echo $weekcheck;?> onclick="javascript:changeValue('repeat_week')"/>  <?php echo JText::_('OS_REPEAT_WEEK')?>
									<BR />
									<input type="checkbox" name="repeat_fortnight" id="repeat_fortnight" value="<?php echo $row->repeat_fortnight;?>" <?php echo $biweekcheck;?> onclick="javascript:changeValue('repeat_fortnight')"/>  <?php echo JText::_('OS_REPEAT_FORTNIGHT')?>
									<BR />
									<input type="checkbox" name="repeat_month" id="repeat_month" value="<?php echo $row->repeat_month;?>" <?php echo $monthcheck;?> onclick="javascript:changeValue('repeat_month')"/>  <?php echo JText::_('OS_REPEAT_MONTH')?>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<span class="editlinktip hasTip" title="<?php echo JText::_('OS_TIME_SLOT_TYPE');?>::<?php echo JText::_('OS_TIME_SLOT_TYPE_EXPLAIN'); ?>"><?php echo JText::_('OS_TIME_SLOT_TYPE'); ?>: 
									</span>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<?php echo $lists['time_slot'];?>
									<?php
									if($row->service_time_type == 0)
									{
										$display = "block";
									}
									else
									{
										$display = "none";
									}
									?>
									<div id="normal_time_slot_div" style="display:<?php echo $display;?>;padding-top:10px;">
										<?php echo JText::_('OS_STEP_IN_MINUTES')?>: <?php echo $lists['step_in_minutes'];?>
										<span style="font-style:italic;color:gray;"><?php echo JText::_('OS_STEP_IN_MINUTES_EXPLAIN');?></span>
									</div>
									<?php
									if($row->service_time_type == 1){
										$display = "block";
									}else{
										$display = "none";
									}
									?>
									<div id="custom_time_slot_div" style="display:<?php echo $display;?>;padding-top:10px;">
										<?php echo JText::_('OS_MAX_SEATS_CAN_BOOK')?>: 
										<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" name="max_seats" id="max_seats" value="<?php echo $row->max_seats;?>"/>
										<span style="font-style:italic;color:gray;"><?php echo JText::_('OS_MAX_SEATS_CAN_BOOK_EXPLAIN');?></span>
									</div>
								</div>
							</div>
							<div class="<?php echo $controlGroupClass; ?>">
								<div class="<?php echo $controlLabelClass?>">
									<span class="editlinktip hasTip" title="<?php echo JText::_('OS_MAX_TIMESLOTS_EXPLAIN'); ?>"><?php echo JText::_('OS_MAX_TIMESLOTS'); ?>: 
									</span>
								</div>
								<div class="<?php echo $controlsClass;?>">
									<input type="text" class="<?php echo $mapClass['input-small']; ?> imini" name="max_timeslots" id="max_timeslots" value="<?php echo $row->max_timeslots;?>"/>
								</div>
							</div>
							<?php
							if($configClass['active_linked_service'] == 1)
							{
								?>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_LINKED_SERVICES'); ?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										if ($row->id == 0)
										{
											echo JText::_('OS_LINKED_SERVICES_WILL_ONLY_SHOW_AFTER_SAVING_SERVICE');
										}
										else
										{
											echo $lists['linked_services'];
										}
										?>
									</div>
								</div>
								<?php
							}
							if((file_exists(JPATH_ADMINISTRATOR . '/components/com_acym/acym.php') && JComponentHelper::isEnabled('com_acym', true)) || file_exists(JPATH_ADMINISTRATOR . '/components/com_acymailing/acymailing.php') && JComponentHelper::isEnabled('com_acymailing', true))
							{
								?>
								<div class="<?php echo $controlGroupClass; ?>">
									<div class="<?php echo $controlLabelClass?>">
										<?php echo JText::_('OS_ACYMAILING_LIST'); ?>
									</div>
									<div class="<?php echo $controlsClass;?>">
										<?php
										if(file_exists(JPATH_ADMINISTRATOR . '/components/com_acym/acym.php') && JComponentHelper::isEnabled('com_acym', true))
										{
											$acyLists = $lists['acyLists'];
											$optionArr = array();
											$optionArr[] = JHtml::_('select.option','0',JText::_('OS_USE_GLOBAL'),'id','name');
											$optionArr[] = JHtml::_('select.option','-1',JText::_('OS_NONE'),'id','name');
											$optionArr = array_merge($optionArr, $acyLists);
											echo JHtml::_('select.genericlist', $optionArr, 'acymailing_list_id', 'class="inputbox"', 'id', 'name', $row->acymailing_list_id);

										}
										elseif(file_exists(JPATH_ADMINISTRATOR . '/components/com_acymailing/acymailing.php') && JComponentHelper::isEnabled('com_acymailing', true)){
											?>
											<select name="acymailing_list_id" class="input-large">
												<option value="0" <?php if($row->acymailing_list_id == "0"){echo " selected='selected' ";}?> ><?php echo JText::_('OS_USE_GLOBAL');?></option>        
												<option value="-1" <?php if($row->acymailing_list_id == "-1"){echo " selected='selected' ";}?> ><?php echo JText::_('OS_NONE');?></option>
												<?php 
													foreach($lists['acyLists'] as $List){ ?>			
														<option value="<?php echo $List->listid;?>"<?php if($row->acymailing_list_id == $List->listid){echo " selected='selected' ";} ?>><?php echo $List->name;?></option>
												<?php } ?>          
											</select>
											<?php
										}	
										?>
									</div>
								</div>
								<?php
							}
							?>
						</fieldset>
						<fieldset class="form-horizontal options-form" id="employeeassigned">
							<legend><?php echo JText::_('OS_ASSIGNED_EMPLOYEES')?></legend>
							<div style="width:100%;" id="employeeassignedDiv">
								<?php
								if($row->id == 0)
								{
									echo JText::_('OS_ASSIGNED_EMPLOYEES_INFORMATION');
								}
								else
								{
									OSappscheduleService::generateEmployeeServiceForm($row->id);
								}
								?>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
		<?php
		if ($translatable)
		{
		?>
		<?php echo JHtml::_($tabApiPrefix.'endTab'); ?>
			<?php echo JHtml::_($tabApiPrefix.'addTab', 'translation', 'translation-page', JText::_('OS_TRANSLATION', true)); ?>		
				<div class="tab-content">			
					<?php	
						$i = 0;
						$activate_sef = $languages[0]->sef;
						echo JHtml::_($tabApiPrefix.'startTabSet', 'languagetranslation', array('active' => 'translation-page-'.$activate_sef));
						foreach ($languages as $language)
						{												
							$sef = $language->sef;
							echo JHtml::_($tabApiPrefix.'addTab', 'languagetranslation',  'translation-page-'.$sef, '<img src="'.JURI::root().'media/com_osservicesbooking/flags/'.$sef.'.png" />');
						?>
							<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">													
								<table width="100%" class="admintable" style="background-color:white;">
									<tr>
										<td class="key"><?php echo JText::_('OS_SERVICE_NAME'); ?>: </td>
										<td>
											<input class="inputbox form-control ilarge" type="text" name="service_name_<?php echo $sef; ?>" id="service_name_<?php echo $sef; ?>" size="70" value="<?php echo $row->{'service_name_'.$sef};?>" />
										</td>
									</tr>
									<tr>
										<td class="key" valign="top"><?php echo JText::_('OS_SERVICE_DESCRIPTION'); ?>: </td>
										<td>
											<?php
											echo $editor->display( 'service_description_'.$sef,  $row->{'service_description_'.$sef} , '95%', '250', '75', '20' ,false);
											?>
										</td>
									</tr>
								</table>
							</div>										
						<?php				
							
							$i++;		
						}
						echo JHtml::_($tabApiPrefix.'endTabSet');
					?>
				</div>	
			<?php
			echo JHtml::_($tabApiPrefix.'endTab');
			echo JHtml::_($tabApiPrefix.'endTabSet');
		}
		
		?>
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" id="id" value="<?php echo (int)$row->id?>" />
		<input type="hidden" name="field_require" value="service_name,service_price,service_length" />
		<input type="hidden" name="field_is_number" value="service_name,service_price,service_length" />
		<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JUri::root();?>" />
		</form>
		<script type="text/javascript">
			function addCustomPricebyDate(id){
				var checkbox = document.getElementById('same' + id);
				var price	 = document.getElementById('price' + id);
				if(checkbox.checked == false)
				{
					price.disabled = false;
				}
				else
				{
					price.disabled = true;
				}
			}
		</script>
		<?php
	}

	static function generateEmployeeServiceForm($lists)
	{
		global $mainframe,$configClass, $mapClass;
		if(count($lists['employees']) > 0)
		{
			$dateArr = array(JText::_('OS_MON'),JText::_('OS_TUE'),JText::_('OS_WED'),JText::_('OS_THU'),JText::_('OS_FRI'),JText::_('OS_SAT'),JText::_('OS_SUN'));
			?>
			<table width="100%" style="border:1px solid #CCC;">
				<tr>
					<td width="15%" class="headerajaxtd">
						<?php echo JText::_('OS_EMPLOYEE')?>
					</td>
					<?php
					if(OSBHelper::isAvailableVenue())
					{
						?>
						<td width="20%" class="headerajaxtd">
							<?php echo JText::_('OS_VENUE')?>
						</td>
						<?php
					}
					foreach($dateArr as $date)
					{
						?>
						<td width="6%" class="headerajaxtd">
							<?php echo $date;?>
						</td>
						<?php
					}
					?>
					<td width="15%" class="headerajaxtd">
						<?php echo JText::_('OS_EDIT')?>/ <?php echo JText::_('OS_REMOVE')?>
					</td>
				</tr>
				<?php
				$weekDate = ['mo','tu','we','th','fr','sa','su'];
				for($i = 0; $i<count($lists['employees']) ; $i++)
				{
					if($i % 2 == 0)
					{
						$bgcolor = "#efefef";
					}
					else
					{
						$bgcolor = "#FFF";
					}
					$employee = $lists['employees'][$i];
					?>
					<tr>
						<td width="20%" align="left" style="background-color:<?php echo $bgcolor;?>;padding:5px;">
							<?php
							echo $employee->employee_name;
							?>
						</td>
						<?php
						if(OSBHelper::isAvailableVenue())
						{
							?>
							<td width="20%" style="text-align:center;background-color:<?php echo $bgcolor;?>;padding:5px;">
								<?php
								if($employee->vid > 0 && $employee->venue_name != "")
								{
									echo $employee->venue_name;	
								}
								else
								{
									echo "N/A";
								}
								?>
							</td>
							<?php
						}
						foreach($weekDate as $d)
						{
							?>
							<td style="background-color:<?php echo $bgcolor;?>;text-align:center;">
							<?php
							if($employee->{$d} == 1)
							{
								?>
								<a href="javascript:void(0);" onClick="javascript:updateWorkingStatus(<?php echo $lists['sid']; ?>, <?php echo $employee->employee_id; ?>,'<?php echo $d; ?>', 0);" title="<?php echo JText::_('OS_MAKE_THIS_DATE_NO_WORKING'); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-check2-circle" viewBox="0 0 16 16">
									  <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
									  <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
									</svg>
								</a>
								<?php
							}
							else
							{
								?>
								<a href="javascript:void(0);" onClick="javascript:updateWorkingStatus(<?php echo $lists['sid']; ?>, <?php echo $employee->employee_id; ?>,'<?php echo $d; ?>', 1);" title="<?php echo JText::_('OS_MAKE_THIS_DATE_WORKING'); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
									  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
									  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
									</svg>
								</a>
								<?php
							}
							?>
							</td>
							<?php
						}
						?>
						<td style="background-color:<?php echo $bgcolor;?>;text-align:center;">
							<a href="index.php?option=com_osservicesbooking&task=employee_setupbreaktime&eid=<?php echo $employee->employee_id; ?>&sid=<?php echo $lists['sid']; ?>" title="<?php echo JText::_('OS_EDIT');?>">

								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
								  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
								  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
								</svg>
							</a>

							&nbsp;

							<a href="javascript:void(0);" onClick="javascript:removeWorking(<?php echo $employee->id; ?>, <?php echo $lists['sid']; ?>);" title="<?php echo JText::_('OS_UNASSIGN_THIS_EMPLOYEE_OUT_OF_SERVICE'); ?>">
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
			<script type="text/javascript">
			function removeWorking(id, sid)
			{
				var answer = confirm("<?php echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_UNASSIGN_THIS_EMPLOYEE_OUT_OF_SERVICE');?>");
				if(answer == 1)
				{
					removeWorkingAjax(id, sid)
				}
			}
			</script>
			<?php
		}
		//add employee
		if(count($lists['employeeAvail']) > 0)
		{
			?>
			<BR />
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<strong>
						<?php echo JText::_('OS_ADD_NEW_EMPLOYEE_TO_THIS_SERVICE');?>
					</strong>
				</div>
			</div>
			<div class="<?php echo $mapClass['control-group'];?>">
				<div class="<?php echo $mapClass['control-label'];?>">
					<?php echo JText::_('OS_EMPLOYEE');?>
				</div>
				<div class="<?php echo $mapClass['controls'];?>">
					<?php
					echo $lists['employeelist'];
					?>
				</div>
			</div>
			<?php
			if(count($lists['venues']) > 0)
			{
				?>
				<div class="<?php echo $mapClass['control-group'];?>">
					<div class="<?php echo $mapClass['control-label'];?>">
						<?php echo JText::_('OS_VENUE');?>
					</div>
					<div class="<?php echo $mapClass['controls'];?>">
						<?php
						echo $lists['venuelist'];
						?>
					</div>
				</div>
				<?php
			}
			?>
			<div class="<?php echo $mapClass['control-group'];?>">
				<div class="<?php echo $mapClass['control-label'];?>">
					<?php echo JText::_('OS_DATE_IN_WEEK');?>
				</div>
				<div class="<?php echo $mapClass['controls'];?>" style="padding-left:15px;">
					<?php
					$weekDate = ['mo','tu','we','th','fr','sa','su'];
					foreach($weekDate as $date)
					{
						?>
						<input type="checkbox" name="<?php echo $date; ?>" id="<?php echo $date; ?>" value="0" onClick="javascript:changeValue('<?php echo $date; ?>');" /> <?php echo $date; ?>
						<BR />
						<?php
					}
					?>
				</div>
			</div>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<a href="javascript:void(0);" class="btn btn-primary" title="Save" onClick="javascript:saveAssignment(<?php echo $lists['sid']; ?>);return false;"><?php echo JText::_('OS_SAVE_THIS_CHANGE')?></a>
				</div>
			</div>
			<script type="text/javascript">
				function saveAssignment(sid)
				{
					var employee_id = document.getElementById('employee_id').value;
					if(employee_id == '')
					{
						alert("<?php echo JText::_('OS_PLEASE_SELECT_EMPLOYEE');?>");
						document.getElementById('employee_id').focus();
						return false;
					}
					else
					{
						saveAssignmentAjax(sid)
					}
				}
				</script>
			<?php
		}
	}
	
	/**
	 * List services, dates
	 *
	 * @param unknown_type $option
	 * @param unknown_type $service
	 * @param unknown_type $dates
	 */
	static function manageAvailability($option,$service,$dates)
	{
		global $mainframe,$configClass, $mapClass;
		JToolBarHelper::title(JText::_('OS_MANAGE_AVAILABILITY_TIME')." [".$service->service_name."]");
		JToolBarHelper::cancel('service_gotolist');
		$controlGroupClass = $mapClass['control-group'];
		$controlLabelClass = $mapClass['control-label'];
		$controlsClass	   = $mapClass['controls'];
		?>
		<div class="<?php echo $mapClass['row-fluid']; ?>">
			<div class="<?php echo $mapClass['span6']; ?>">
				<table class="table table-striped">
					<thead>
						<tr>
							<th style="text-align:center;">
								<?php echo JText::_('OS_DATE'); ?>
							</th>
							<th style="text-align:center;">
								<?php echo JText::_('OS_UNAVAILABLE_TIME'); ?>
							</th>
							<th style="text-align:center;">
								<?php echo JText::_('OS_REMOVE'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if(count($dates) > 0){
							$k = 0;
							for($i=0;$i<count($dates);$i++){
								$date = $dates[$i];
								?>
								<tr class="<?php echo $row?>k">
									<td style="text-align:center;">
										<?php echo date($configClass['date_format'],strtotime($date->avail_date));?>
									</td>
									<td style="text-align:center;">
										<?php echo date($configClass['time_format'],strtotime($date->avail_date." ".$date->start_time));?>
									&nbsp;-&nbsp;
										<?php echo date($configClass['time_format'],strtotime($date->avail_date." ".$date->end_time));?>
									</td>
									<td style="text-align:center;">
										<a href="javascript:removeUnvailableTime(<?php echo $date->id?>,<?php echo $service->id; ?>);" title="<?php echo JText::_('OS_REMOVE_UNAVAILABLE_TIME');?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
											  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
											</svg>
										</a>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="<?php echo $mapClass['span6']; ?>" style="border-left:1px solid gray;">
				<form method="POST" action="index.php?option=com_osservicesbooking" name="adminForm" id="adminForm" class="form-horizontal">
					<strong>
						<?php echo JText::_('OS_ADD_UNVAILABLE_TIME');?>
					</strong>
					<BR /><BR />
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('OS_DATE');?></label>
						<div class="<?php echo $controlsClass; ?>">
							<?php echo JHtml::_('calendar','','avail_date','avail_date','%Y-%m-%d','placeholder="2014-01-01" class="input-small form-control ishort"')?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('OS_START_TIME');?> (hh:mm:ss)</label>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" name="start_time" id="start_time" class="input-small form-control ishort" placeholder="01:02:03"/>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('OS_END_TIME');?> (hh:mm:ss)</label>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" name="end_time" id="end_time" class="input-small form-control ishort" placeholder="01:02:03" />
						</div>
					</div>
					<div class="clearfix"></div>
					<input type="button" value="<?php echo JText::_('OS_ADD')?>" class="btn btn-info" onclick="javascript:submitForm();"/>
					<input type="hidden" name="option" value="com_osservicesbooking" />
					<input type="hidden" name="task" value="service_addunvailabletime" />
					<input type="hidden" name="id" value="<?php echo $service->id?>" />
				</form>
			</div>
		</div>
		<script language="javascript">
		function submitForm(){
			var form = document.adminForm;
			var avail_date = form.avail_date;
			var start_time = form.start_time;
			var end_time   = form.end_time;
			if(avail_date.value == ""){
				alert("<?php echo JText::_('OS_PLEASE_SELECT_DATE');?>");
				avail_date.focus();
				return false;
			}else if(start_time.value == ""){
				alert("<?php echo JText::_('OS_PLEASE_SELECT_START_TIME');?>");
				start_time.focus();
				return false;
			}else if(end_time.value == ""){
				alert("<?php echo JText::_('OS_PLEASE_SELECT_END_TIME');?>");
				end_time.focus();
				return false;
			}else{
				form.submit();
			}
		}
		
		function removeUnvailableTime(id,sid){
			var answer = confirm("<?php echo JText::_('OS_DO_YOU_WANT_TO_REMOVE_UNAVAILABLE_TIME');?>")	;
			if(answer == 1){
				location.href = "index.php?option=com_osservicesbooking&task=service_removeunvailabletime&id=" + id + "&sid=" + sid;
			}
		}
		</script>
		<?php
	}
	
	/**
	 * Manage Time Slots
	 *
	 * @param unknown_type $service
	 * @param unknown_type $slots
	 * @param unknown_type $pageNav
	 */
	static function manageTimeSlots($service,$slots,$pageNav)
	{
		global $mainframe,$configClass;
		JToolBarHelper::title($service->service_name." > ".JText::_('OS_MANAGE_CUSTOM_TIME_SLOTS'),'service.png');
		JToolbarHelper::custom('service_batchimportcustomtimeslots','upload.png', 'upload.png',JText::_('OS_BATCH_IMPORT'),false);
		JToolBarHelper::addNew('service_timeslotadd');
		JToolBarHelper::editList('service_timeslotedit');
		JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'service_removetimeslots');
		JToolBarHelper::cancel('service_gotolist');
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking&task=service_managetimeslots" name="adminForm" id="adminForm">
			<table class="adminlist table table-striped" width="100%">
				<thead>
					<tr>
						<th width="3%" style="text-align:center;">#</th>
						<th width="2%" style="text-align:center;">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="10%" style="text-align:center;">
							Start Time
						</th>
						<th width="10%" style="text-align:center;">
							End Time
						</th>
						<th width="10%" style="text-align:center;">
							Number Seats
						</th>
						<th width="8%" style="text-align:center;">
							Mon
						</th>
						<th width="8%" style="text-align:center;">
							Tue
						</th>
						<th width="8%" style="text-align:center;">
							Wed
						</th>
						<th width="8%" style="text-align:center;">
							Thu
						</th>
						<th width="8%" style="text-align:center;">
							Fri
						</th>
						<th width="8%" style="text-align:center;">
							Sat
						</th>
						<th width="8%" style="text-align:center;">
							Sun
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="14" style="text-align:center;">
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
				for ($i=0, $n=count($slots); $i < $n; $i++) {
					$row = $slots[$i];
					$checked = JHtml::_('grid.id', $i, $row->id);
					$link 	 = JRoute::_( 'index.php?option=com_osservicesbooking&task=service_timeslotedit&cid[]='. $row->id .'&sid='.$service->id);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $pageNav->getRowOffset( $i ); ?></td>
						<td align="center"><?php echo $checked; ?></td>
						<td align="center" style="text-align:center;">
							<a href="<?php echo $link?>">
								<?php echo $row->start_hour?>:<?php echo $row->start_min;?>
							</a>
						</td>
						<td align="center" style="text-align:center;">
							<a href="<?php echo $link?>">
								<?php echo $row->end_hour?>:<?php echo $row->end_min;?>
							</a>
						</td>
						<td align="center" style="text-align:center;">
							<?php echo $row->nslots;?>
						</td>
						<?php 
						for($j=1;$j<=7;$j++)
						{
							?>
							<td align="center" style="text-align:center;">
								<div id="date<?php echo $row->id?><?php echo $j?>">
								<?php 
								$db->setQuery("Select count(id) from #__app_sch_custom_time_slots_relation where time_slot_id = '$row->id' and date_in_week = '$j'");
								$count = $db->loadResult();
								if($count > 0)
								{
									?>
									<a href="javascript:changeTimeSlotDate(0,<?php echo $j?>,<?php echo $service->id?>,<?php echo $row->id?>,'<?php echo JUri::root();?>');" title="Unselect this day">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
										  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
										</svg>
									</a>
									<?php
								}else{
									?>
									<a href="javascript:changeTimeSlotDate(1,<?php echo $j?>,<?php echo $service->id?>,<?php echo $row->id?>,'<?php echo JUri::root();?>');" title="Select this day">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
										  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
										</svg>
									</a>
									<?php 
								}
								?>
								</div>
							</td>
							<?php 
						}
						?>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" value="service_managetimeslots"  />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="sid" id="sid" value="<?php echo $service->id;?>"/>
			<input type="hidden" name="live_site" id="live_site" value="<?php echo JURI::root()?>" />
			<input type="hidden" name="selected_item" id="selected_item" value="" />
			
		</form>
		<?php
	}
	
	static function editTimeSlot($slot,$lists,$sid)
	{
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		if($slot->id > 0){
			$edit = JText::_('OS_EDIT');
		}else{
			$edit = JText::_('OS_ADD');
		}
		JToolBarHelper::title(JText::_('OS_MANAGE_CUSTOM_TIME_SLOTS')." [$edit]",'service.png');
		JToolBarHelper::save('service_timeslotsave');
		JToolBarHelper::apply('service_timeslotapply');
		JToolbarHelper::save2new('service_timeslotsavenew');
		JToolBarHelper::cancel('service_gotolisttimeslot');
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<table class="admintable">
			<tr>
				<td class="key"><?php echo JText::_('OS_START'); ?>: </td>
				<td >
					<?php
					echo JHTML::_('select.genericlist',$lists['hours'],'start_hour','class="input-mini"','value','text',(int)$slot->start_hour);
					echo JHTML::_('select.genericlist',$lists['mins'],'start_min','class="input-mini"','value','text',(int)$slot->start_min);
					?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('OS_END'); ?>: </td>
				<td >
					<?php
					echo JHTML::_('select.genericlist',$lists['hours'],'end_hour','class="input-mini"','value','text',(int)$slot->end_hour);
					echo JHTML::_('select.genericlist',$lists['mins'],'end_min','class="input-mini"','value','text',(int)$slot->end_min);
					?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('OS_NUMBER_SEATS'); ?>: </td>
				<td>
					<input class="input-mini required" type="text" name="nslots" id="nslots"  value="<?php echo intval($slot->nslots);?>" />
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('Activate On'); ?>: </td>
				<td>
					<?php
					$date_array = array(JText::_('OS_MON'),JText::_('OS_TUE'),JText::_('OS_WED'),JText::_('OS_THU'),JText::_('OS_FRI'),JText::_('OS_SAT'),JText::_('OS_SUN'));
					for($j=1;$j<=7;$j++)
					{
						$db->setQuery("Select count(id) from #__app_sch_custom_time_slots_relation where time_slot_id = '$slot->id' and date_in_week = '$j'");
						$count = $db->loadResult();
						if($count > 0)
						{
							$check = "checked";
						}else{
							$check = "";
						}
						?>
						<input type="checkbox" name="date_in_week[]" id="date<?php echo $j?>" <?php echo $check?> value="<?php echo $j?>" />&nbsp; <?php echo $date_array[$j-1];?>
						<BR />
						<?php 
					}
					?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="com_osservicesbooking" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $slot->id?>" />
		<input type="hidden" name="sid" id="sid" value="<?php echo $sid;?>"/>
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}

	static function showBatchCustomTimeslot($service){
		global $mainframe;
		JToolBarHelper::title(JText::_('OS_CUSTOM_TIMESLOTS')." - ".JText::_('OS_BATCH_IMPORT')." [".$service->service_name."]",'upload');
		JToolBarHelper::custom('service_doimporttimeslots','upload.png','upload.png',JText::_('OS_IMPORT'),false);
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span12" style="padding:10px;text-align:center;border:1px solid #DDD;"> 
				<h2>
					<?php echo JText::_('OS_CUSTOM_TIMESLOTS');?> - <?php echo JText::_('OS_BATCH_IMPORT');?>
				</h2>
				<div class="clearfix"></div>
				<div class="row-fluid">
					<div class="span12">
						Please download CSV form <a href="<?php echo JUri::root()?>components/com_osservicesbooking/asset/sample_custom_timeslots.csv" title="Download CSV file">here</a>. Then, you can add your Time slots data into the CSV file and import it through the File inputbox below.
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12">
						<strong>
							Fields explanation
						</strong>
						<BR />
						<table class="table-bordered table-striped">
							<thead>
								<tr>
									<th>
										Start hour
									</th>
									<th>
										Start minute
									</th>
									<th>
										End hour
									</th>
									<th>
										End minute
									</th>
									<th>
										Available Seats
									</th>
									<th>
										Available Monday
									</th>
									<th>
										Available Tuesday
									</th>
									<th>
										Available Wednesday
									</th>
									<th>
										Available Thursday
									</th>
									<th>
										Available Friday
									</th>
									<th>
										Available Satuday
									</th>
									<th>
										Available Sunday
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										Enter Start hour (1->12)
									</td>
									<td>
										Enter Start minute (0->59)
									</td>
									<td>
										Enter End hour (1->12)
									</td>
									<td>
										Enter End minute (0->59)
									</td>
									<td>
										Available seats (Integer)
									</td>
									<td>
										Available on Monday (0: No, 1: Yes)
									</td>
									<td>
										Available on Tuesday (0: No, 1: Yes)
									</td>
									<td>
										Available on Wednesday (0: No, 1: Yes)
									</td>
									<td>
										Available on Thursday (0: No, 1: Yes)
									</td>
									<td>
										Available on Friday (0: No, 1: Yes)
									</td>
									<td>
										Available on Satuday (0: No, 1: Yes)
									</td>
									<td>
										Available on Sunday (0: No, 1: Yes)
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12" style="padding:20px;">
						<strong>Please select CSV file here</strong>
						<input type="file" name="csvfile" id="csvfile" class="input-large">
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="option" value="com_osservicesbooking" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="sid" id="sid" value="<?php echo $service->id; ?>" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="1" />
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000000"/>
		</form>
		<?php
	}

	static function listSpecialPrice($rows, $pageNav, $lists)
	{
		global $mainframe;
		$db = JFactory::getDbo();
		JToolBarHelper::title(JText::_('OS_MANAGE_SPECIAL_RATES'),'tags');
		JToolBarHelper::addNew('service_addrate');
		if(count($rows) > 0)
		{
			JToolBarHelper::editList('service_modifyrate');
			JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'service_removerate');
			JToolBarHelper::publish('service_ratepublish' , 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('service_rateunpublish', 'JTOOLBAR_UNPUBLISH', true);
		}
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osservicesbooking&task=service_specialrates" name="adminForm" id="adminForm">
		
			<table class="adminlist table table-striped" width="100%" id="ratesList">
				<thead>
					<tr>
						<th width="5%" style="text-align:center;">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="20%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_NAME'), 'name', @$lists['order_Dir'], @$lists['order'] ,'service_specialrates'); ?>
						</th>
						<th width="20%">
							<?php echo JText::_('OS_WEEKDAYS');?>
						</th>
						<th width="10%">
							<?php echo JText::_('OS_TIME');?>
						</th>
						<th width="10%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_COST'), 'cost', @$lists['order_Dir'], @$lists['order'] ,'service_specialrates'); ?>
						</th>
						<th width="8%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_PUBLISHED'), 'published', @$lists['order_Dir'], @$lists['order'] ,'service_specialrates'); ?>
						</th>
						<th width="4%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_ID'), 'a.id', @$lists['order_Dir'], @$lists['order'] ,'service_specialrates'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="8" style="text-align:center;">
							<?php
								echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$k = 0;
				$canChange = true;
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
					$row = $rows[$i];
					$checked = JHtml::_('grid.id', $i, $row->id);
					$link 		= JRoute::_( 'index.php?option=com_osservicesbooking&task=service_modifyrate&cid[]='. $row->id );
					$published 	= JHTML::_('jgrid.published', $row->published, $i, 'service_rate');
					$db->setQuery("Select weekday from #__app_sch_special_price_weekdays where price_id = '$row->id'");
					$weekday    = $db->loadColumn(0);
				?>
					<tr class="<?php echo "row$k"; ?>" sortable-group-id="0" item-id="<?php echo $row->id ?>" level="0">
						<td class="center"><?php echo $checked; ?></td>
						<td align="left">
							<a href="<?php echo $link; ?>">
								<?php echo $row->name; ?>
							</a>
						</td>
						<td align="left">
							<?php
							if(count($weekday))
							{
								$weekdayArr = OSBHelper::loadWeekDays($weekday);
								echo implode(", ", $weekdayArr);
							}
							else
							{
								echo JText::_('OS_ALLDAYS');
							}
							?>
						</td>
						<td align="left">
							<?php
							if($row->apply_from != "")
							{
								?>
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
								  <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
								  <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
								</svg>
								<?php
								echo $row->apply_from;
								?>
							<?php
							}
							else
							{
								echo "/";
							}
							if($row->apply_from != "")
							{
							?>
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
								  <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
								  <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
								</svg>
								<?php
								echo $row->apply_to;
								?>
							<?php
							}
							else
							{
								echo "/";
							}
							?>
						</td>
						<td class="center">
							<?php
							if($row->cost_type == 0)
							{
								echo "-".$row->cost;
							}
							else
							{
								echo $row->cost;
							}
							?>
						</td>
						<td align="center" style="text-align:center;"><?php echo $published?></td>
						<td align="center" style="text-align:center;"><?php echo $row->id; ?></td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" value="service_specialrates" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
			<input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
		</form>
		<?php
	}

	static function modifyRate($row,$lists,$services)
	{
		global $mainframe, $_jversion,$configClass,$jinput,$mapClass;
		OSBHelper::loadTooltip();
		$controlGroupClass  = $mapClass['control-group'];
		$controlLabelClass  = $mapClass['control-label'];
		$controlsClass		= $mapClass['controls'];
		$db = JFactory::getDbo();
		$jinput->set( 'hidemainmenu', 1 );
		if ($row->id)
		{
			$title = ' ['.JText::_('OS_EDIT').']';
		}
		else
		{
			$title = ' ['.JText::_('OS_NEW').']';
		}
		JToolBarHelper::title(JText::_('OS_SPECIAL_RATE').$title,'tag');
		JToolBarHelper::save('service_saverate');
		JToolBarHelper::apply('service_applyrate');
		JToolBarHelper::cancel('service_cancelrate');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="<?php echo $mapClass['row-fluid']?>">
			<div class="<?php echo $mapClass['span6']?>">
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_DETAILS')?></legend>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_NAME');
							?>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" name="name" class="<?php echo $mapClass['input-medium'];?>" value="<?php echo $row->name; ?>" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_AMOUNT');
							?>
							<span class="hasTooltip" title="<?php echo JText::_('OS_SPECIAL_RATE_AMOUNT_EXPLANATION');?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
								  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
								</svg>
							</span>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<?php
							echo $lists['cost_type'];	
							?>
							<input type="number" class="<?php echo $mapClass['input-small'];?> ishort" name="cost" value="<?php echo $row->cost; ?>" size="5" min="0" max="99999" step="any" style="display:inline;"/>
						</div>
					</div>
				</fieldset>
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_ASSIGNMENTS')?></legend>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_SELECT_SERVICES');
							?>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<?php
							echo $lists['services'];	
							?>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="<?php echo $mapClass['span6']?>">
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_PUBLISHING')?></legend>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_PUBLISHED');
							?>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<?php
							echo $lists['published'];	
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_START_PUBLISHING');
							?>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<?php echo JHtml::_('calendar',$row->publish_up,'publish_up','publish_up','%Y-%m-%d %H:%M:%S',array('placeholder' => JText::_('OS_FROM'),'onchange' => '', 'class' => 'input-medium imedium' , 'showTime' => 1));?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_END_PUBLISHING');
							?>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<?php echo JHtml::_('calendar',$row->publish_down,'publish_down','publish_down','%Y-%m-%d %H:%M:%S',array('placeholder' => JText::_('OS_TO'),'onchange' => '', 'class' => 'input-medium imedium' , 'showTime' => 1));?>
						</div>
					</div>
				</fieldset>
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_CONDITIONS')?></legend>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_WEEKDAYS');
							?>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<?php
							echo $lists['weekday'];	
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_FROM');
							?>
							(HH:mm)
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" name="apply_from" class="<?php echo $mapClass['input-small']; ?> ishort" value="<?php echo $row->apply_from; ?>" placeholder="00:00"/>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php
							echo JText::_('OS_TO');
							?>
							(HH:mm)
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" name="apply_to" class="<?php echo $mapClass['input-small']; ?> ishort" value="<?php echo $row->apply_to; ?>" placeholder="00:00"/>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<input type="hidden" name="option" value="com_osservicesbooking" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" id="id" value="<?php echo (int) $row->id?>" />
		<input type="hidden" name="boxchecked" value="0">
		</form>
		<?php
	}
}
?>