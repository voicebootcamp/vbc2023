<?php
/*------------------------------------------------------------------------
# fields.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
class HTML_OSappscheduleFields{
	static function listFields($option,$rows,$pageNav,$lists)
	{
		global $mainframe, $mapClass;
		$rowFluidClass = $mapClass['row-fluid'];
		$span12Class   = $mapClass['span12'];
		$span10Class   = $mapClass['span10'];
		$span2Class	   = $mapClass['span2'];
		JToolBarHelper::title(JText::_('OS_CUSTOM_FIELDS_MANAGEMENT'));
		JToolBarHelper::addNew('fields_add');
		if(count($rows) > 0){
			JToolBarHelper::editList('fields_edit');
			JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'fields_remove');
			JToolBarHelper::publish('fields_publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('fields_unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);

		$listOrder	= $lists['order'];
        $listDirn	= $lists['order_Dir'];

        $saveOrder	= $listOrder == 'field_area, ordering';
        $ordering	= ($listOrder == 'field_area, ordering');

        if ($saveOrder)
        {
            $saveOrderingUrl = 'index.php?option=com_osservicesbooking&task=fields_saveorderAjax';
			if (OSBHelper::isJoomla4())
			{
				\Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				JHtml::_('sortablelist.sortable', 'fieldList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
			}
        }

        $customOptions = array(
            'filtersHidden'       => true,
            'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
            'orderFieldSelector'  => '#filter_full_ordering'
        );

        JHtml::_('searchtools.form', '#adminForm', $customOptions);
        if (count($rows))
        {
			$ordering = array();
            foreach ($rows as $item)
            {
                $ordering[$item->field_area][] = $item->id;
            }
        }
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
		<form method="POST" action="index.php?option=com_osservicesbooking&task=fields_list" name="adminForm" id="adminForm">
			<input type="hidden" name="open_search_from" id="open_search_from" value="<?php echo $lists['show_form'];?>" />
			<div class="js-stools clearfix">
				<div class="<?php echo $rowFluidClass; ?>" style="width:100%;">
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
				<div class="<?php echo $rowFluidClass; ?>" ID="search_param_div" style="display:<?php echo $display;?>;">
					<div class="<?php echo $span12Class; ?>">
						<div class="js-stools-container-filters hidden-phone clearfix shown">
							<div class="js-stools-field-filter">
								<?php echo $lists['field_area']; ?>
							</div>
							<div class="js-stools-field-filter">
								<?php echo $lists['field_type']; ?>
							</div>
							<div class="js-stools-field-filter">
								<?php echo $lists['field_state']; ?>
							</div>
							<div class="js-stools-field-filter">
								<?php echo $lists['field_require']; ?>
							</div>
							<button class="btn hasTooltip btn-primary" title="" type="button" onClick="javascript:submitOrdersForm();"  data-original-title="<?php echo JText::_('OS_SEARCH');?>">
								<i class="icon-search"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
			<table width="100%" class="adminlist table table-striped" id="fieldList">
				<thead>
					<tr>
						<th width="5%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'ordering', @$lists['order_Dir'], @$lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="3%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="20%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_CUSTOM_FIELD'), 'field_label', @$lists['order_Dir'], @$lists['order'],'fields_list' ); ?>
						</th>
						<th width="25%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_SERVICES'), 'service_id', @$lists['order_Dir'], @$lists['order'],'fields_list' ); ?>
						</th>
						<th width="10%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_FIELD_AREA'), 'field_area', @$lists['order_Dir'], @$lists['order'],'fields_list' ); ?>
						</th>
						<th width="10%">
							
							<?php echo JHTML::_('grid.sort',   JText::_('OS_FIELD_TYPE'), 'field_type', @$lists['order_Dir'], @$lists['order'],'fields_list' ); ?>
						</th>
						<th width="5%"  style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_STATUS'), 'published', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
						<th width="5%"  style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_REQUIRED'), 'required', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
                        <th width="3%"  style="text-align:center;">
                            <?php echo JHTML::_('grid.sort',   JText::_('ID'), 'id', @$lists['order_Dir'], @$lists['order'] ); ?>
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
				$db = JFactory::getDbo();
				$canChange = true;
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
					$row		= $rows[$i];
					$checked	= JHtml::_('grid.id', $i, $row->id);
					$link 		= JRoute::_( 'index.php?option='.$option.'&task=fields_edit&cid[]='. $row->id );
					$published 	= JHTML::_('jgrid.published', $row->published, $i , 'fields_');
					$required 	= JHTML::_('jgrid.published', $row->required, $i , 'fields_required');

					?>
					<tr class="<?php echo "row$k"; ?>" sortable-group-id="<?php echo $row->field_area; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
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
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" />
							<?php endif; ?>
						</td>
						<td align="center"><?php echo $checked; ?></td>
						<td align="left"><a href="<?php echo $link; ?>"><?php echo $row->field_label; ?></a></td>
						<td align="left" style="padding-right: 10px;"><?php echo $row->service;?></td>
						<td align="left" style="padding-right: 10px;">
						<?php
						switch ($row->field_area){
							case "0":
								echo JText::_('OS_SERVICES');
							break;
							case "1":
								echo JText::_('OS_BOOKING_FORM');
							break;
						}
						?>
						</td>
						<td align="center">
						<?php
						switch ($row->field_type)
                        {
							case "0":
								echo JText::_('OS_TEXTFIELD');
							break;
							case "1":
								echo JText::_('OS_SELECTLIST');
							break;
							case "2":
								echo JText::_('OS_CHECKBOXES');
							break;
							case "3":
								echo JText::_('OS_IMAGE');
							break;
                            case "4":
                                echo JText::_('OS_FILEUPLOAD');
                            break;
							case "5":
                                echo JText::_('OS_MESSAGE');
                            break;
						}
						?>
						</td>
						<td align="center" style="text-align:center;"><?php echo $published?></td>
						<td align="center" style="text-align:center;"><?php echo $required?></td>
                        <td align="center" style="text-align:center;"><?php echo $row->id; ?></td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" value="fields_list" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
			<input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
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
                jQuery("#field_type").val("-1");
                jQuery("#field_state").val("-1");
                jQuery("#field_require").val("-1");
                jQuery("#keyword").val("");
                document.getElementById('adminForm').submit();
            });
		</script>
		<?php
	}
	
	
	/**
	 * Edit field
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function field_edit($option,$row,$lists,$fields,$translatable)
	{
		global $mainframe, $languages,$configClass,$jinput,$mapClass;
		$editor		= JEditor::getInstance(JFactory::getConfig()->get('editor'));
		$version 	= new JVersion();
		$_jversion	= $version->RELEASE;		
		$mainframe 	= JFactory::getApplication();
		$jinput->set( 'hidemainmenu', 1 );
		if ($row->id > 0){
			$title = ' ['.JText::_('OS_EDIT').']';
		}else{
			$title = ' ['.JText::_('OS_NEW').']';
		}
		JToolBarHelper::title(JText::_('Custom field').$title);
		JToolBarHelper::save('fields_save');
		JToolBarHelper::apply('fields_apply');
		JToolBarHelper::cancel('fields_cancel');
		?>
		<script language="javascript">
		 function showDiv()
		{
			var field_area		= document.getElementById('field_area');
			var service_div		= document.getElementById('service_div');
			var form_div		= document.getElementById('form_div');
			if(field_area.value == 0)
			{
				service_div.style.display = "block";
				form_div.style.display = "none";
			}
			else
			{
				service_div.style.display = "none";
				form_div.style.display = "block";
			}
		}
		function showOptions()
		{
			var field_type = document.getElementById('field_type');
			var service_div = document.getElementById('other_info');
			var service_div1 = document.getElementById('service_div');
			if((field_type.value == 1) || (field_type.value == 2)){
				service_div.style.display = "block";
				service_div1.style.display = "block";
			}else{
				service_div.style.display = "none";
				service_div1.style.display = "none";
			}
			
			var field_area = document.getElementById('field_area');
			if((field_type.value == 0) || (field_type.value == 3) || (field_type.value == 4)){
				var len = field_area.options.length;
				field_area.options[0] = null;
				field_area.options[1] = null;
				field_area.options[0] = null;
				field_area.options[1] = null;
				
				var option = document.createElement("option");
				option.text = "<?php echo JText::_('OS_BOOKING_FORM');?>";
				option.value = "1";
				field_area.appendChild(option);
				
				//service_div.style.display = "none";
			}else{
				var len = field_area.options.length;
				field_area.options[0] = null;
				field_area.options[1] = null;
				field_area.options[0] = null;
				field_area.options[1] = null;
				
				var option = document.createElement("option");
				option.text = "<?php echo JText::_('OS_SERVICES');?>";
				option.value = "0";
				field_area.appendChild(option);
				var option = document.createElement("option");
				option.text = "<?php echo JText::_('OS_BOOKING_FORM');?>";
				option.value = "1";
				field_area.appendChild(option);
				
				//service_div.style.display = "block";
			}
		}
		</script>
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php 
		if ($translatable)
		{
			echo JHtml::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo JHtml::_('bootstrap.addTab', 'translation', 'general-page', JText::_('OS_GENERAL', true));
		}
		?>
			<table class="admintable">
				<tr>
					<td class="key"><?php echo JText::_('OS_FIELD'); ?>: </td>
					<td >
						<input type="text" class="<?php echo $mapClass['input-large'];?> ilarge" name="field_label" id="field_label" size="40" value="<?php echo $row->field_label?>" >
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('OS_FIELD_TYPE'); ?>: </td>
					<td >
						<?php echo $lists['field_type'];?>
					</td>
				</tr>
				<tr>
					<td class="key" valign="top"><?php echo JText::_('OS_SELECT_FIELD_AREA'); ?>: 
					<BR />
					<span style="font-weight:normal !important;color:red;font-size:11px;">
						<?php echo JText::_('OS_SELECT_FIELD_AREA_EXPLAIN'); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $lists['field_area'];?>
						<?php
						if($row->id == 0)
						{
							$display = "none";
						}
						else
						{
							if($row->field_area == 1 || $row->field_type == 0)
							{
								$display = "none";
								$display1 = "block";
							}
							else
							{
								$display = "block";
								$display1 = "none";
							}
						}
						?>
						<div id="service_div" style="display:<?php echo $display?>;">
							<?php echo Jtext::_('OS_SELECT_SERVICES');?>
							<br />
							<?php echo $lists['services']; ?>
						</div>
						<div id="form_div" style="display:<?php echo $display1?>;">
							<?php echo JText::_('OS_SHOW_IN_CHECKOUT_FORM');?>:
							<?php OSappscheduleConfiguration::showCheckboxfield('show_at_frontend',(int)$row->show_at_frontend);?>
						</div>
					</td>
				</tr>
				<?php
				if($row->id > 0 && $row->field_type != 5)
				{
				?>
				<tr>
					<td class="key"><?php echo JText::_('OS_SHOW_IN_EMAIL'); ?>: </td>
					<td width="80%"><?php OSappscheduleConfiguration::showCheckboxfield('show_in_email',(int)$row->show_in_email);?></td>
				</tr>
				<?php
				if($configClass['integrate_gcalendar'] == 1 || $configClass['generate_ics'] == 1)
				{
				?>
				<tr>
					<td class="key"><?php echo JText::_('OS_SHOW_IN_THIRD_PARTY_CALENDAR'); ?>: </td>
					<td width="80%"><?php OSappscheduleConfiguration::showCheckboxfield('show_in_calendar',(int)$row->show_in_calendar);?></td>
				</tr>
				<?php
				}
				if((int) $configClass['field_integration'] > 0)
				{
				?>
					<tr>
						<td class="key"><?php echo JText::_('OS_FIELD_MAPPING'); ?>: </td>
						<td width="80%"><?php echo $lists['field_mapping'];?></td>
					</tr>
				<?php
				}	
				?>
				<?php } ?>
				<tr>
					<td class="key"><?php echo JText::_('OS_FIELD_CLASS'); ?>: </td>
					<td >
						<input type="text" class="<?php echo $mapClass['input-large'];?> ilarge" name="field_class" id="field_class" size="40" value="<?php echo $row->field_class; ?>" >
					</td>
				</tr>
				<?php
				if($row->id > 0 && $row->field_type != 5)
				{
				?>
				<tr>
					<td class="key"><?php echo JText::_('OS_REQUIRED'); ?>: </td>
					<td width="80%"><?php OSappscheduleConfiguration::showCheckboxfield('required',(int)$row->required);?></td>
				</tr>
				<?php } ?>
				<tr>
					<td class="key"><?php echo JText::_('OS_PUBLISHED_STATE'); ?>: </td>
					<td width="80%"><?php OSappscheduleConfiguration::showCheckboxfield('published',(int)$row->published);?></td>
				</tr>
				<tr>
					<td class="key" valign="top"><?php echo JText::_('OS_OTHER_INFORMATION'); ?>:
					<BR />
					<span style="color:gray;font-weight:normal !important;font-size:11px;">
						<?php
							echo "[".JText::_('OS_SELECTLIST').",".JText::_('OS_CHECKBOXES').",".JText::_('OS_MESSAGE')."]";
						?>	
					</span>
					</td>
					<td width="80%">
						<?php
						if($row->field_type == 1 || $row->field_type == 2 || $row->field_type == 5)
						{
							$display = "block";
						}
						else
						{
							$display = "none";
						}
						?>
						<div id="other_info" style="display:<?php echo $display?>;">
							<table  width="100%">
								<tr>
									<td width="100%" valign="top" align="left">
										<?php
										if($row->id == 0)
										{
											echo JText::_('OS_AFTER_SAVING_YOU_CAN_MANAGE_OPTIONS_FOR_THIS_FIELD');
										}
										else
										{
											if($row->field_type == 5)
											{
												echo $editor->display( 'message',  $row->message , '95%', '250', '75', '20' ,false);
											}
											else
											{
												OSappscheduleFields::manageOptions($row->id);
											}
										}
										?>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
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
										<td class="key"><?php echo JText::_('OS_FIELD'); ?>: </td>
										<td >
											<input type="text" class="input-large form-control ilarge" name="field_label_<?php echo $sef; ?>" id="field_label_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'field_label_'.$sef};?>" />
										</td>
									</tr>
									<?php 
									if(count($fields) > 0)
									{
										foreach ($fields as $field)
										{
											?>
											<tr>
												<td class="key"><?php echo $field->field_option; ?>: </td>
												<td >
													<input type="text" class="input-large form-control" name="field_option_<?php echo $sef; ?>_<?php echo $field->id;?>" id="field_option_<?php echo $sef; ?>_<?php echo $field->id;?>" size="40" value="<?php echo $field->{'field_option_'.$sef};?>" />
												</td>
											</tr>
											<?php 
										}
									}
									if($row->field_type == 5)
									{
										?>
										<tr>
											<td class="key"><?php echo JText::_('OS_MESSAGE'); ?>: </td>
											<td >
												<?php
												echo $editor->display( 'message_'.$sef,  $row->{'message_'.$sef} , '95%', '250', '75', '20' ,false);
												?>
											</td>
										</tr>
										<?php
									}
									?>
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
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo (int) $row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo JURI::base()?>" />
		</form>
		<?php
	}
	
	/**
	 * Manage options of Field
	 *
	 * @param unknown_type $field_id
	 * @param unknown_type $fields
	 */
	static function manageOptions($field_id,$fields){
		global $mainframe,$configClass;
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_fields where id = '$field_id'");
		$fieldObj = $db->loadObject();
		OSBHelper::loadTooltip();
		?>
		<table width="100%" class="admintable">
			<tr>
				<td colspan="2" class="key" style="text-align:left;">
					<?php echo JText::_('OS_NEW_OPTION');?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span title="<?php echo JText::_('OS_FIELD_OPTION')?>::<?php echo JText::_('OS_FIELD_OPTION_EXPLAIN')?>" class="hasTip">
					<?php echo JText::_('OS_FIELD_OPTION')?>
					</span>
				</td>
				<td>
					<input type="text" class="input-large form-control" name="field_option" id="field_option">
				</td>
			</tr>
			<tr>
				<td class="key">
					<span title="<?php echo JText::_('OS_ADDITIONAL_PRICE')?>::<?php echo JText::_('OS_ADDITIONAL_PRICE_EXPLAIN')?>" class="hasTip">
					<?php echo JText::_('OS_ADDITIONAL_PRICE')?>
					</span>
				</td>
				<td>
					<input type="text" class="input-mini form-control" name="additional_price" id="additional_price" size="5"> <?php echo $configClass['currency_format'];?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="key" style="text-align:left;">
					<input type="button" class="btn btn-warning" value="<?php echo JText::_("OS_SAVE")?>" onclick="javascript:saveNewOption();" />
					<input type="button" class="btn btn-info" value="<?php echo JText::_("OS_RESET")?>" onclick="javascript:resetOption();" />
				</td>
			</tr>
			
		</table>
		<div id="field_option_div">
		<table width="100%" class="adminlist">
			<thead>
				<tr>
					<th width="2%" align="center">
						#
					</th>
					<th width="30%" align="center">
						<?php echo JText::_('OS_FIELD_OPTION')?>
					</th>
					<th width="25%" align="center">
						<?php echo JText::_('OS_ADDITIONAL_PRICE')?>
					</th>
					<th width="13%" align="center">
						<?php echo JText::_('OS_ORDERING')?>
					</th>
					<th width="5%" align="center" style="text-align:center;">
						<?php echo JText::_('OS_REMOVE')?>
					</th>
					<th width="5%" align="center" style="text-align:center;">
						<?php echo JText::_('OS_SAVE')?>
					</th>
					<?php if($fieldObj->field_type == 1){?>
					<th width="5%" align="center" style="text-align:center;">
						<?php echo JText::_('OS_DEFAULT_OPTION')?>
					</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				for($i=0;$i<count($fields);$i++)
				{
					$field = $fields[$i];
					?>
					<tr class="rows<?php echo $k?>">
						<td style="text-align:center;">
							<?php echo $i+1;?>
						</td>
						<td  style="text-align:left;">
							<input type="text" class="input-large form-control" name="field_option<?php echo $field->id?>" id="field_option<?php echo $field->id?>" value="<?php echo $field->field_option?>" />
						</td>
						<td style="text-align:left;">
							<input type="text" class="input-mini form-control" name="additional_price<?php echo $field->id?>" id="additional_price<?php echo $field->id?>" value="<?php echo $field->additional_price?>" size="5" /> <?php echo $configClass['currency_format'];?>
						</td>
						<td style="text-align:left;">
							<input type="text" class="input-mini form-control" name="ordering<?php echo $field->id?>" id="ordering<?php echo $field->id?>" value="<?php echo $field->ordering; ?>" size="5" />
						</td>
						<td style="text-align:center;">
							<a href="javascript:removeFieldOption(<?php echo $field->id?>)" title="<?php echo JText::_('OS_REMOVE_FIELD_OPTION');?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
								  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
								</svg>
							</a>
						</td>
						<td style="text-align:center;">
							<a href="javascript:saveFieldOption(<?php echo $field->id?>)" title="<?php echo JText::_('OS_SAVE_FIELD_OPTION');?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
								  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
								</svg>
							</a>
						</td>
						<?php 
						if($fieldObj->field_type == 1)
						{?>
						<td style="text-align:center;">
							<div id="select_default_option_<?php echo $field->id;?>">
								<?php
								if($field->option_default == 1)
								{
								?>
									<a href="javascript:changeDefaultOption(<?php echo $field->id?>,0)" title="<?php echo JText::_('OS_CHANGE_DEFAULT_OPTION_STATUS');?>">
										<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/ok.png" />
									</a>
									<?php } else { ?>
									<a href="javascript:changeDefaultOption(<?php echo $field->id?>,1)" title="<?php echo JText::_('OS_CHANGE_DEFAULT_OPTION_STATUS');?>">
										<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/notok.png" />
									</a>
								<?php } ?>
							</div>
						</td>
						<?php 
						}
						?>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<input type="hidden" name="select_default_option" id="select_default_option" value="" />
		</div>
		<script type="text/javascript">
		function saveFieldOption(field_id){
			var field_option     = document.getElementById('field_option' + field_id);
			var additional_price = document.getElementById('additional_price' + field_id);
			var ordering		 = document.getElementById('ordering' + field_id);

			if(field_option.value == ""){
				alert("<?php echo JText::_('OS_PLEASE_ENTER_FIELD_OPTION');?>");
				field_option.focus();
			}else{
				saveEditOptionAjax("<?php echo JURI::base()?>",field_option.value,additional_price.value,ordering.value,"<?php echo $field_id?>",field_id);
			}
		}
		function changeDefaultOption(field_id,new_status){
			changeDefaultOptionAjax("<?php echo JURI::base()?>","<?php echo $field_id?>",field_id,new_status);
		}
		function removeFieldOption(field_id)
		{
			var answer = confirm("<?php echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_FIELD_OPTION')?>");
			if(answer == 1){
				removeFieldOptionAjax("<?php echo JURI::base()?>",field_id);
			}
		}
		function saveNewOption()
		{
			var field_option = document.getElementById('field_option');
			var additional_price = document.getElementById('additional_price');
			if(field_option.value == ""){
				alert("<?php echo JText::_('OS_PLEASE_ENTER_FIELD_OPTION');?>");
				field_option.focus();
			}
			else
			{
				saveNewOptionAjax("<?php echo JURI::base()?>",field_option.value,additional_price.value,"<?php echo $field_id?>");
			}
		}
		function resetOption(){
			var field_option = document.getElementById('field_option');
			var additional_price = document.getElementById('additional_price');
			field_option.value = "";
			additional_price.value = "";
		}
		</script>
		<?php
	}
}
?>