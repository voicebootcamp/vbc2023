<?php
/*------------------------------------------------------------------------
# venue.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class HTML_OSappscheduleVenue{
	/**
	 * List venues
	 *
	 * @param unknown_type $option
	 * @param unknown_type $pageNav
	 * @param unknown_type $rows
	 */
	static function listVenues($option,$pageNav,$rows,$lists)
	{
		global $mainframe,$configClass, $mapClass;
		JHtml::_('behavior.multiselect');
		JToolBarHelper::title(JText::_('OS_MANAGE_VENUES'),'location');
		JToolBarHelper::addNew('venue_add');
		if(count($rows) > 0){
			JToolBarHelper::editList('venue_edit');
			JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'venue_remove');
			JToolBarHelper::publish('venue_publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('venue_unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=<?php echo $option; ?>&task=venue_list" name="adminForm" id="adminForm">
			<table width="100%">
				<tr>
					<td align="left">
						<div class="btn-group">
							<div class="input-group">
								<input type="text" 	class="<?php echo $mapClass['input-medium']; ?> search-query form-control"	name="keyword" value="<?php echo $lists['keyword']; ?>" placeholder="<?php echo JText::_('OS_SEARCH');?>" />
								<button type="submit" class="btn btn-warning" ><?php echo JText::_('OS_SEARCH');?></button>
								<button type="reset"  class="btn btn-info" onclick="this.form.keyword.value='';this.form.filter_state.value='';this.form.submit();" /><?php echo JText::_('OS_RESET');?></button>
							</div>
						</div>
					</td>
					<td align="right">
						<?php echo $lists['filter_state'];?>
					</td>
				</tr>
			</table>
			<table class="adminlist table table-striped" width="100%">
				<thead>
					<tr>
						<th width="3%">#</th>
						<th width="2%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="15%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_VENUE'), 'address', @$lists['order_Dir'], @$lists['order'] ,'service_list'); ?>
						</th>
						<th width="10%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_SERVICE'), 'service_time_type', @$lists['order_Dir'], @$lists['order'] ,'service_list'); ?>
						</th>
						<?php
						if(!OSBHelper::isJoomla4())
						{
						?>
                        <th width="10%">
                            <?php echo JText::_('OS_OPENING_TIME') ?>
                        </th>
						<?php
						}	
						?>
						<th width="10%">
							<?php echo JText::_('OS_DISABLE_BOOKING_BEFORE') ?>
						</th>
						<th width="10%">
							<?php echo JText::_('OS_DISABLE_BOOKING_AFTER') ?>
						</th>
						<?php
						if(!OSBHelper::isJoomla4())
						{
						?>
						<th width="10%">
							<?php echo JText::_('OS_CONTACT_INFORMATION') ?>
						</th>
						<?php
						}	
						?>
						<th width="5%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_PUBLISHED'), 'published', @$lists['order_Dir'], @$lists['order'] ,'service_list'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="9" style="text-align:center;">
							<?php
								echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<?php
				$k = 0;
				$db = JFactory::getDbo();
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$checked = JHtml::_('grid.id', $i, $row->id);
					$link 		= JRoute::_( 'index.php?option='.$option.'&task=venue_edit&cid[]='. $row->id );
					$published 	= JHTML::_('jgrid.published', $row->published, $i, 'venue_');
				?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $pageNav->getRowOffset( $i ); ?></td>
						<td align="center"><?php echo $checked; ?></td>
						<td align="left"><a href="<?php echo $link; ?>"><?php 
						$address = array();
						$address[] = $row->venue_name;
						$address[] = $row->address;
						if($row->city != ""){
							$address[] = $row->city;
						}
						if($row->state != ""){
							$address[] = $row->state;
						}
						if($row->country != ""){
							$address[] = $row->country;
						}
						echo implode(", ",$address);
						?></a>
						</td>
						<td align="left"><a href="<?php echo $link; ?>">
							<?php 
							$db->setQuery("Select service_name from #__app_sch_services where id in (Select sid from #__app_sch_venue_services where vid = '$row->id')");
							$services = $db->loadObjectList();
							if(count($services) > 0){
								$service_name = array();
								for($j=0;$j<count($services);$j++){
									$service_name[] = $services[$j]->service_name;
								}
								echo implode(", ",$service_name);
							}
							?></a>
						</td>
						<?php
						if(!OSBHelper::isJoomla4())
						{
						?>
                        <td align="left">
                            <?php
                            if($row->opening_hour > 0){
                                if($row->opening_hour < 10){
                                    echo "0".$row->opening_hour;
                                }else{
                                    echo $row->opening_hour;
                                }
                                echo ":";
                                if($row->opening_minute < 10){
                                    echo "0".$row->opening_minute;
                                }else{
                                    echo $row->opening_minute;
                                }
                            }else{
                                echo JText::_('OS_INHERIT_SYSTEM_WORKING_TIME');
                            }
                            ?>
                        </td>
						<?php } ?>
						<td align="left">
							<?php
							switch ($row->disable_booking_before){
								case "1":
									echo JText::_('OS_TODAY');
								break;
								case "2":
									echo $row->number_date_before." ".JText::_('OS_DAYS_FROM_NOW');
								break;
								case "3":
									echo JText::_('OS_BEFORE')." ".$row->disable_date_before;
								break;
								case "4":
									echo $row->number_hour_before." ".JText::_('OS_HOURS_FROM_NOW');
								break;
							}
							?>
						</td>
						<td align="left">
							<?php
							switch ($row->disable_booking_after){
								case "1":
									echo JText::_('OS_NOT_SET');
								break;
								case "2":
									echo $row->number_date_after." ".JText::_('OS_DAYS_FROM_NOW');
								break;
								case "3":
									echo JText::_('OS_AFTER')." ".$row->disable_date_after;
								break;
							}
							?>
						</td>
						<?php
						if(!OSBHelper::isJoomla4())
						{
						?>
						<td align="left" style="font-size:11px;">
							<?php
                            if($row->contact_phone != ""){
                                echo JText::_('OS_PHONE').": ".$row->contact_phone."<br />";
                            }
                            if($row->contact_email != ""){
                                echo JText::_('OS_EMAIL').": ".$row->contact_email."<br />";
                            }
                            ?>
						</td>
						<?php } ?>
						<td align="center" style="text-align:center;"><?php echo $published?></td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="venue_list"  />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
		</form>
		<?php
	}
	
	/**
	 * Edit venue
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editVenueHtml($option,$row,$lists,$translatable)
	{
		global $mainframe, $_jversion,$configClass,$languages,$jinput,$mapClass;
		$controlGroupClass  = $mapClass['control-group'];
		$controlLabelClass  = $mapClass['control-label'];
		$controlsClass		= $mapClass['controls'];
		$db					= JFactory::getDbo();
		$version 			= new JVersion();
		$_jversion			= $version->RELEASE;		
		$mainframe 			= JFactory::getApplication();
		$jinput->set( 'hidemainmenu', 1 );
		if ($row->id){
			$title = ' ['.JText::_('OS_EDIT').']';
		}else{
			$title = ' ['.JText::_('OS_NEW').']';
		}
		JToolBarHelper::title(JText::_('OS_VENUE').$title,'location');
		JToolBarHelper::save('venue_save');
		JToolBarHelper::apply('venue_apply');
		JToolBarHelper::cancel('venue_cancel');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php 
		if ($translatable)
		{
			echo JHtml::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo JHtml::_('bootstrap.addTab', 'translation', 'general-page', JText::_('OS_GENERAL', true));
		}
		?>
		<div class="<?php echo $mapClass['row-fluid'];?> <?php echo $extraClass; ?>">
			<div class="<?php echo $mapClass['span6'];?>">
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_LOCATION')?></legend>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php echo JText::_('OS_VENUE_NAME'); ?>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="input-large form-control ilarge" name="venue_name" id="venue_name" value="<?php echo $row->venue_name?>"/>
						</div>
					</div>

					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>">
							<?php echo JText::_('OS_ADDRESS'); ?>
						</div>
						<div class="<?php echo $controlsClass;?>">
							<input type="text" class="input-large form-control ilarge" name="address" id="address" value="<?php echo $row->address?>"/>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>"><?php echo JText::_('OS_CITY'); ?></div>
						<div class="<?php echo $controlsClass;?>">
								<input type="text" class="input-small form-control ilarge" name="city" id="city" value="<?php echo $row->city?>"/>
							</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>"><?php echo JText::_('OS_STATE'); ?></div>
						<div class="<?php echo $controlsClass;?>">
								<input type="text" class="input-small form-control ilarge" name="state" id="state" value="<?php echo $row->state?>"/>
							</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>"><?php echo JText::_('OS_COUNTRY'); ?></div>
						<div class="<?php echo $controlsClass;?>">
								<?php echo $lists['country'];?>
							</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>"><?php echo JText::_('OS_LAT_ADDRESS'); ?></div>
						<div class="<?php echo $controlsClass;?>">
								<input type="text" class="input-small form-control ilarge" name="lat_add" id="lat_add" value="<?php echo $row->lat_add?>"/>
							</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass?>"><?php echo JText::_('OS_LONG_ADDRESS'); ?></div>
						<div class="<?php echo $controlsClass;?>">
								<input type="text" class="input-small form-control ilarge" name="long_add" id="long_add" value="<?php echo $row->long_add?>"/>
							</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
							<?php
							if($configClass['map_type'] == 0)
							{
							?>
								<script src="<?php echo OSBHelper::returnGoogleMapScript();?>"></script>
								<script>
								  var position = new google.maps.LatLng(<?php echo $row->lat_add;?>, <?php echo $row->long_add;?>);
								  var parliament = new google.maps.LatLng(<?php echo $row->lat_add;?>, <?php echo $row->long_add;?>);
								  var marker;
								  var map;
							
								  function initialize() {
									var mapOptions = {
									  zoom: 13,
									  mapTypeId: google.maps.MapTypeId.ROADMAP,
									  center: position,
									  mapTypeControl: true,
									  mapTypeControlOptions: {
										style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
									  },
									  zoomControl: true,
									  zoomControlOptions: {
										style: google.maps.ZoomControlStyle.SMALL
									  }
									};
							
									map = new google.maps.Map(document.getElementById('map-canvas'),
											mapOptions);
							
									marker = new google.maps.Marker({
									  map:map,
									  draggable:false,
									  position: parliament
									});
								  }
								</script>
								<body onload="initialize()">
									 <div id="map-canvas" style="width: 500px; height: 400px;">map div</div>
								</body>
							<?php
							}
							else
							{
								$rootUri = JUri::root(true);
								$document = JFactory::getDocument()
									->addScript($rootUri . '/media/com_osservicesbooking/assets/js/leaflet/leaflet.js')
									->addStyleSheet($rootUri . '/media/com_osservicesbooking/assets/js/leaflet/leaflet.css');
								$zoomLevel   = 16;
								$coordinates = $row->lat_add . ',' . $row->long_add;
								$onPopup = false;
								?>
								<div id="googlemapdiv" style="height:300px;width:100%; position:relative;"></div>
								<script type="text/javascript">
									jQuery(document).ready(function(){
										var mymap = L.map('googlemapdiv').setView([<?php echo $row->lat_add; ?>, <?php echo $row->long_add; ?>], <?php echo $zoomLevel;?>);
										L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
											attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
											maxZoom: 18,
											id: 'mapbox.streets',
											zoom: <?php echo $zoomLevel;?>,
										}).addTo(mymap);

										
										var propertyIcon = L.icon({iconUrl: '<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/1.png',
																			iconSize:     [33, 44] // size of the icon
										});
										var marker = L.marker([<?php echo $row->lat_add ?>, <?php echo $row->long_add;?>],{icon: propertyIcon}, {draggable: false}).addTo(mymap);
										mymap.scrollWheelZoom.disable()
									});
								</script>
								<?php
							}
							?>
						</div>
					</fieldset>
				</div>
				<div class="<?php echo $mapClass['span6'];?>">
					<fieldset class="form-horizontal options-form">
						<legend><?php echo JText::_('OS_CONTACT_INFORMATION')?></legend>
								
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_CONTACT_NAME'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<input type="text" class="input-large form-control" name="contact_name" id="contact_name" value="<?php echo $row->contact_name?>"/>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_CONTACT_EMAIL'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<input type="text" class="input-large form-control" name="contact_email" id="contact_email" value="<?php echo $row->contact_email?>"/>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_CONTACT_PHONE'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<input type="text" class="input-small form-control" name="contact_phone" id="contact_phone" value="<?php echo $row->contact_phone?>"/>
							</div>
						</div>
					</fieldset>
					<fieldset class="form-horizontal options-form" id="venueInformation">
						<legend><?php echo JText::_('OS_OTHER_INFORMATION')?></legend>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_SERVICE'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>" style="padding-left:15px;">
								<?php
								//echo $lists['service'];
								$services = $lists['services'];
								if(count($services))
								{
									foreach($services as $service)
									{
										if(in_array($service->value, $lists['serviceArr']))
										{
											$checked = "checked";
										}
										else
										{
											$checked = "";
										}
										?>
										<input type="checkbox" name="sid[]" value="<?php echo $service->value?>" <?php echo $checked;?>>
										&nbsp;
										<?php echo $service->text;?>
										<BR />
										<?php
									}
								}
								?>
							</div>
						</div>
                        <div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_OPENING_TIME'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<div class="venueTime">
									<?php
									echo $lists['hour'].":".$lists['minute'];
									?>
								</div>
                           </div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_DISABLE_DATES_BEFORE'); ?>
								<?php OSBHelper::generateTip(JText::_('OS_DISABLE_DATES_BEFORE_EXPLAIN'));?>
							</div>
							<div class="<?php echo $controlsClass;?>" style="padding-left:15px;">
								
									<?php
									$check1 = "";
									$check2 = "";
									$check3 = "";
									$check4 = "";
									if($row->disable_booking_before == 1){
										$check1 = "checked";
									}elseif($row->disable_booking_before == 2){
										$check2 = "checked";
									}elseif($row->disable_booking_before == 3){
										$check3 = "checked";
									}elseif($row->disable_booking_before == 4){
										$check4 = "checked";
									}else{
										$check1 = "checked";
									}
									?>
									<input type="radio" name="disable_booking_before" id="disable_booking_before" value="1" <?php echo $check1;?> /> <?php echo JText::_('OS_TODAY');?>
									<div class="clearfix"></div>
									<input type="radio" name="disable_booking_before" id="disable_booking_before" value="4" <?php echo $check4;?> /> 
									<input type="text" class="input-mini form-control imini" name="number_hour_before" id="number_hour_before" value="<?php echo $row->number_hour_before?>" style="width:20px;"/> <?php echo JText::_('OS_HOURS_FROM_NOW');?>
									<div class="clearfix"></div>
									<input type="radio" name="disable_booking_before" id="disable_booking_before" value="2" <?php echo $check2;?> /> 
									<input type="text" class="input-mini form-control imini" name="number_date_before" id="number_date_before" value="<?php echo $row->number_date_before?>" style="width:20px;"/>
									<?php echo JText::_('OS_DAYS_FROM_NOW');?>
									<div class="clearfix"></div>
									<input type="radio" name="disable_booking_before" id="disable_booking_before" value="3" <?php echo $check3;?> />
									&nbsp;
									<?php echo JText::_('OS_SPECIFIC_DATE')?>:&nbsp;
									<?php 
									$disable_date_before = $row->disable_date_before;
									if($disable_date_before == "0000-00-00"){
										$disable_date_before = "";
									}
									echo JHTML::_('calendar',$disable_date_before, 'disable_date_before', 'disable_date_before', '%Y-%m-%d', array('class'=>'input-small ishort', 'size'=>'19',  'maxlength'=>'19')); ?>

							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_DISABLE_DATES_AFTER'); ?>
								<?php OSBHelper::generateTip(JText::_('OS_DISABLE_DATES_AFTER_EXPLAIN'));?>
							</div>
							<div class="<?php echo $controlsClass;?>" style="padding-left:15px;">
								<?php
								$check1 = "";
								$check2 = "";
								$check3 = "";
								if($row->disable_booking_after == 1){
									$check1 = "checked";
								}elseif($row->disable_booking_after == 2){
									$check2 = "checked";
								}elseif($row->disable_booking_after == 3){
									$check3 = "checked";
								}else{
									$check1 = "checked";
								}
								?>
								<input type="radio" name="disable_booking_after" id="disable_booking_after" value="1" <?php echo $check1;?> /> <?php echo JText::_('OS_NOT_SET');?>
								<div class="clearfix"></div>
								<input type="radio" name="disable_booking_after" id="disable_booking_after" value="2" <?php echo $check2;?> /> 
								<input type="text" class="input-mini form-control imini" name="number_date_after" id="number_date_after" value="<?php echo $row->number_date_after?>" style="width:20px;"/>
								<?php echo JText::_('OS_DAYS_FROM_NOW');?>
								<div class="clearfix"></div>
								<input type="radio" name="disable_booking_after" id="disable_booking_after" value="3" <?php echo $check3;?> /> 
								&nbsp;
								<?php echo JText::_('OS_SPECIFIC_DATE')?>:&nbsp;
								<?php 
								$disable_date_after = $row->disable_date_after;
								if($disable_date_after  == "0000-00-00"){
									$disable_date_after = "";
								}
								echo JHTML::_('calendar',$disable_date_after, 'disable_date_after', 'disable_date_after', '%Y-%m-%d', array('class'=>'input-small ishort', 'size'=>'19',  'maxlength'=>'19')); ?>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass?>">
								<?php echo JText::_('OS_VENUE_IMAGE'); ?>
							</div>
							<div class="<?php echo $controlsClass;?>">
								<?php
								if($row->image != "")
								{
									?>
									<img src="<?php echo JURI::root()?>images/osservicesbooking/venue/<?php echo $row->image?>" width="150" class="img-polaroid" />
									<div style="clear:both;"></div>
									<input type="checkbox" name="remove_photo" id="remove_photo" value="0" onclick="javascript:changeValue('remove_photo')"  /> <?php echo JText::_('OS_REMOVE');?>
									<?php
								}
								?>
								<input type="file" name="image" id="image" class="input-small form-control" />
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
				</div>
			</div>
		
		<?php 
		if ($translatable)
		{
		?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'translation', 'translation-page', JText::_('OS_TRANSLATION', true)); ?>		
				<div class="tab-content">			
					<?php	
						$i = 0;
						$activate_sef = $languages[0]->sef;
						echo JHtml::_('bootstrap.startTabSet', 'languagetranslation', array('active' => 'translation-page-'.$activate_sef));
						foreach ($languages as $language)
						{												
							$sef = $language->sef;
							echo JHtml::_('bootstrap.addTab', 'languagetranslation',  'translation-page-'.$sef, '<img src="'.JURI::root().'media/com_osservicesbooking/flags/'.$sef.'.png" />');
						?>
							<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">													
								<table width="100%" class="admintable" style="background-color:white;">
									<tr>
										<td class="key"><?php echo JText::_('OS_ADDRESS'); ?>: </td>
										<td >
											<input type="text" class="input-large form-control" name="address_<?php echo $sef; ?>" id="address_<?php echo $sef; ?>" value="<?php echo $row->{'address_'.$sef};?>"/>
										</td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_('OS_CITY'); ?>: </td>
										<td >
											<input type="text" class="input-small form-control" name="city_<?php echo $sef; ?>" id="city_<?php echo $sef; ?>" value="<?php echo $row->{'city_'.$sef};?>"/>
										</td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_('OS_STATE'); ?>: </td>
										<td >
											<input type="text" class="input-small form-control" name="state_<?php echo $sef; ?>" id="state_<?php echo $sef; ?>" value="<?php echo $row->{'state_'.$sef};?>"/>
										</td>
									</tr>
								</table>
							</div>										
						<?php				
							echo JHtml::_('bootstrap.endTabSet');
							$i++;		
						}
						echo JHtml::_('bootstrap.endTabSet');
					?>
				</div>	
			<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.endTabSet');
		}
		
		?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value=""  />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" id="id" value="<?php echo (int)$row->id?>" />
		<input type="hidden" name="MAX_FILE_SIZE" value="900000000"  />
		</form>
		<script language="javascript">
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
}
?>