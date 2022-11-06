<?php
/*------------------------------------------------------------------------
# fields.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2019 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class OSappscheduleFields{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
		$cid        = $jinput->get('cid',array(),'ARRAY');
		\Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		$document = JFactory::getDocument();
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/ajax.js");
		switch ($task){
			default:
			case "fields_list":
				OSappscheduleFields::field_list($option);
			break;
			case "fields_saveorderAjax":
                OSappscheduleFields::saveorderAjax($option);
            break;
			case "fields_edit":
				OSappscheduleFields::field_edit($option,$cid[0]);
			break;
			case "fields_add":
				OSappscheduleFields::field_edit($option,0);
			break;
			case "fields_apply":
				OSappscheduleFields::fields_save($option,0);
			break;
			case "fields_save":
				OSappscheduleFields::fields_save($option,1);
			break;
			case "fields_publish":
				OSappscheduleFields::fields_state($option,$cid,1);
			break;	
			case "fields_unpublish":
				OSappscheduleFields::fields_state($option,$cid,0);
			break;
			case "fields_requiredpublish":
				OSappscheduleFields::fields_requiredstate($option,$cid,1);
			break;	
			case "fields_requiredunpublish":
				OSappscheduleFields::fields_requiredstate($option,$cid,0);
			break;	
			case "fields_remove":
				OSappscheduleFields::fields_remove($option,$cid);
			break;
			case "fields_addOption":
				OSappscheduleFields::addFieldOption();
			break;
			case "fields_removeFieldOption":
				OSappscheduleFields::removeFieldOption();
			break;
			case "fields_editOption":
				OSappscheduleFields::saveEditOption();
			break;
			case "fields_changeDefaultOptionAjax":
				OSappscheduleFields::changeDefaultOptionAjax();
			break;
			case "fields_saveorder":
				OSappscheduleFields::saveOrder($option);
			break;
			case "fields_orderdown":
				OSappscheduleFields::orderdown($option);
			break;
			case "fields_orderup":
				OSappscheduleFields::orderup($option);
			break;
			case "fields_cancel":
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=fields_list");
			break;
		}
	}

	static function saveorderAjax($option){
        global $jinput;
        $db				= JFactory::getDBO();
        $cid 			= $jinput->get( 'cid', array(), 'array' );
        $order			= $jinput->get( 'order', array(), 'array' );
        $row			= JTable::getInstance('Field','OsAppTable');
        $groupings		= array();
        // update ordering values
        $txt = "";
        for( $i=0; $i < count($cid); $i++ )
        {
            $row->load( $cid[$i] );
            // track parents
            $groupings[] = $row->field_area;
            if ($row->ordering != $order[$i])
            {
                $row->ordering = $order[$i];
                $txt .= $cid[$i]." ".$row->ordering."/n";
                $row->store();
            } // if
        } // for
        // execute updateOrder for each parent group
        $groupings = array_unique( $groupings );
        foreach ($groupings as $group)
        {
            $row->reorder(' field_area = '.(int) $group.' AND published = 1');
        }
    }
	
	/**
	 * Save order
	 *
	 * @param unknown_type $option
	 */
	static function saveorder($option){
		global $mainframe,$jinput;
		$db = JFactory::getDBO();
		$msg = JText::_( 'New ordering saved' );
		$cid        = $jinput->get('cid',array(),'ARRAY');
		$order        = $jinput->get('order',array(),'ARRAY');
		\Joomla\Utilities\ArrayHelper::toInteger($cid);
		\Joomla\Utilities\ArrayHelper::toInteger($order);

		$row = &JTable::getInstance('Field','OsAppTable');
		// update ordering values
		for( $i=0; $i < count($cid); $i++ ){
			$row->load( (int) $cid[$i] );
			$groupings[] = $row->field_area;
			if ($row->ordering != $order[$i]){
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$msg = $db->getErrorMsg();
					return false;
				}
			}
		}
		
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder(' field_area = '.(int) $group.' AND published = 1');
		}
		// execute updateOrder
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=fields_list");
	}
	
	/**
	 * Order down
	 *
	 * @param unknown_type $option
	 */
	static function orderdown($option){
		global $mainframe,$_jversion,$jinput;
		$cid        = $jinput->get('cid',array(),'ARRAY');
		\Joomla\Utilities\ArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect(
				'index.php?option=com_osservicesbooking&task=fields_list',
				JText::_('OS_NO_ITEM_SELECTED')
			);
			return false;
		}

		if (OSappscheduleFields::orderItem($id, 1)) {
			$msg = JText::_( 'OS_MENU_ITEM_MOVED_DOWN' );
		}
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=fields_list");
	}
	
	/**
	 * Order down
	 *
	 * @param unknown_type $option
	 */
	static function orderup($option){
		global $mainframe,$_jversion,$jinput;
		$cid        = $jinput->get('cid',array(),'ARRAY');
		\Joomla\Utilities\ArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect(
				'index.php?option=com_osservicesbooking&task=fields_list',
				JText::_('OS_NO_ITEM_SELECTED')
			);
			return false;
		}

		if (OSappscheduleFields::orderItem($id, -1)) {
			$msg = JText::_( 'OS_MENU_ITEM_MOVED_DOWN' );
		}
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=fields_list");
	}
	
	/**
	 * Order Item
	 *
	 * @param unknown_type $item
	 * @param unknown_type $movement
	 * @return unknown
	 */
	static function orderItem($item, $movement){
		global $mainframe;
		
		$row = &JTable::getInstance('Field','OsAppTable');
		$row->load( $item );
		if (!$row->move( $movement, ' field_area = '.(int) $row->field_area )) {
			$this->setError($row->getError());
			return false;
		}
		$row->reorder(' field_area = '.$row->field_area.' AND published = 1');
		return true;
	}
	
	static function saveEditOption()
	{
		global $mainframe,$jinput;
		$db						= JFactory::getDbo();
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS."tables".DS."fieldoption.php");
		$optionid				= $jinput->getInt('optionid',0);
		$field_id				= $jinput->getInt('field_id',0);
		$db->setQuery("Select * from #__app_sch_fields where id = '$field_id'");
		$fieldObject			= $db->loadObject();
		$field_option			= $jinput->get('field_option','','string');
		$additional_price		= $jinput->get('additional_price','','string');
		$ordering				= $jinput->getInt('ordering',0);
		$row					= &JTable::getInstance('FieldOption','OsAppTable');
		$row->id				= $optionid;
		$row->field_id			= $field_id;
		$row->field_option		= $field_option;
		$row->additional_price	= $additional_price;
		$row->ordering			= $ordering;
		$row->store();
		$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field_id' order by ordering");
		$fields					= $db->loadObjectList();
		OSappscheduleFields::showFieldOptions($fields,$fieldObject);
		exit();
	}
	
	/**
	 * Remove field option
	 *
	 */
	static function removeFieldOption()
	{
		global $mainframe,$jinput;
		$db						= JFactory::getDbo();
		$field_id				= $jinput->getInt('field_id',0);
		$db->setQuery("Select field_id from #__app_sch_field_options where id = '$field_id'");
		$fid					= $db->loadResult();
		$db->setQuery("Select * from #__app_sch_fields where id = '$fid'");
		$fieldObject			= $db->loadObject();
		$db->setQuery("Delete from #__app_sch_field_options where id = '$field_id'");
		$db->execute();
		$db->setQuery("Select * from #__app_sch_field_options where field_id = '$fid' order by ordering");
		$fields					= $db->loadObjectList();
		OSappscheduleFields::showFieldOptions($fields,$fieldObject);
		exit();
	}
	
	/**
	 * Add Field Option
	 *
	 */
	static function addFieldOption()
	{
		global $mainframe,$jinput;
		$db						= JFactory::getDbo();
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS."tables".DS."fieldoption.php");
		$field_id				= $jinput->getInt('field_id',0);
		$db->setQuery("Select * from #__app_sch_fields where id = '$field_id'");
		$fieldObject			= $db->loadObject();
		$db->setQuery("Select ordering from #__app_sch_field_options order by ordering desc");
		$ordering				= $db->loadResult();
		$ordering				= (int) $ordering + 1;
		$field_option			= $jinput->get('field_option','','string');
		$additional_price		= $jinput->get('additional_price','','string');
		$row					= &JTable::getInstance('FieldOption','OsAppTable');
		$row->id				= 0;
		$row->field_id			= $field_id;
		$row->field_option		= $field_option;
		$row->additional_price	= $additional_price;
		$row->ordering			= $ordering;
		$row->store();
		$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field_id' order by ordering");
		$fields					= $db->loadObjectList();
		OSappscheduleFields::showFieldOptions($fields,$fieldObject);
		exit();
	}
	
	static function showFieldOptions($fields,$fieldObject)
	{
		?>
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
					<th width="13%" align="center" style="text-align:center;">
						<?php echo JText::_('OS_ORDERING')?>
					</th>
					<th width="5%" align="center" style="text-align:center;">
						<?php echo JText::_('OS_REMOVE')?>
					</th>
					<th width="5%" align="center">
						<?php echo JText::_('OS_SAVE')?>
					</th>
					<?php if($fieldObject->field_type == 1){ ?>
					<th width="5%" align="center" style="text-align:center;">
						<?php echo JText::_('OS_DEFAULT_OPTION')?>
					</th>
					<?php }?>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				for($i=0;$i<count($fields);$i++){
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
						<?php if($fieldObject->field_type == 1){ ?>
						<td style="text-align:center;">
							<div id="select_default_option_<?php echo $field->id;?>">
								<?php
								if($field->option_default == 1){
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
						<?php } ?>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Manage Options
	 *
	 * @param unknown_type $field_id
	 */
	static function manageOptions($field_id){
		global $mainframe;
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field_id' order by ordering");
		$fields = $db->loadObjectList();
		HTML_OsAppscheduleFields::manageOptions($field_id,$fields);
	}
	
	/**
	 * Field list
	 *
	 * @param unknown_type $option
	 */
	static function field_list($option)
	{
		global $mainframe,$configClass,$jinput, $mapClass;
		$db							= JFactory::getDbo();
		$config						= new JConfig();
		$lists						= [];
		$sql						= [];
		$show_form					= 0;
		$list_limit					= $config->list_limit;
		$filter_order 				= $mainframe->getUserStateFromRequest($option.'.fields.filter_order','filter_order','field_area, ordering','string');
        $filter_order_Dir 			= $mainframe->getUserStateFromRequest($option.'.fields.filter_order_Dir','filter_order_Dir','asc','string');
		$lists['order'] 			= $filter_order;
		$lists['order_Dir'] 		= $filter_order_Dir;
		$order_by 					= " ORDER BY $filter_order $filter_order_Dir";
		$limit						= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $list_limit, 'int' );
		$limitstart					= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		$field_state				= $mainframe->getUserStateFromRequest($option.'.fields.field_state','field_state',-1,'int');
		$field_require				= $mainframe->getUserStateFromRequest($option.'.fields.field_require','field_require',-1,'int');
		$field_type					= $mainframe->getUserStateFromRequest($option.'.fields.field_type','field_type',-1,'int');
		$keyword 			 		= $mainframe->getUserStateFromRequest($option.'.fields.keyword','keyword','','string');
		$lists['keyword']			= $keyword;
		if($keyword != "")
		{
			$show_form				= 1;
			$sql[]					= " field_label like ".$db->quote('%'.$keyword.'%');
		}
		$field_area					= $jinput->get('field_area','','string');
		if($field_area != "")
		{
			$show_form				= 1;
			$sql[]					= " field_area = '$field_area'";
		}
		if($field_type >= 0)
		{
			$show_form				= 1;
			$sql[]					= " field_type = '$field_type'";
		}
		if($field_state >= 0)
		{
			$show_form				= 1;
			$sql[]					= " published = '$field_state'";
		}
		if($field_require >= 0)
		{
			$show_form				= 1;
			$sql[]					= " required = '$field_require'";
		}
		if(count($sql))
		{
			$sql					= " and ".implode(" and ", $sql);
		}
		else
		{
			$sql					= "";
		}
		$typeArr					= [];
		$typeArr[] = JHTML::_('select.option','-1',JText::_('OS_SELECT_FIELD_TYPE'));
		$typeArr[] = JHTML::_('select.option','0',JText::_('OS_TEXTFIELD'));
		$typeArr[] = JHTML::_('select.option','1',JText::_('OS_SELECTLIST'));
		$typeArr[] = JHTML::_('select.option','2',JText::_('OS_CHECKBOXES'));
		$typeArr[] = JHTML::_('select.option','3',JText::_('OS_IMAGE'));
        $typeArr[] = JHTML::_('select.option','4',JText::_('OS_FILEUPLOAD'));
		$typeArr[] = JHTML::_('select.option','5',JText::_('OS_MESSAGE'));
		$lists['field_type'] = JHTML::_('select.genericlist',$typeArr,'field_type','onChange="javascript:document.adminForm.submit();" class="'.$mapClass['input-medium'].' form-select imedium"','value','text',$field_type);

		$stateArr					= [];
		$stateArr[] = JHTML::_('select.option','-1',JText::_('OS_SELECT_STATE'));
		$stateArr[] = JHTML::_('select.option','0',JText::_('JNO'));
		$stateArr[] = JHTML::_('select.option','1',JText::_('JYES'));
		$lists['field_state'] = JHTML::_('select.genericlist',$stateArr,'field_state','onChange="javascript:document.adminForm.submit();" class="'.$mapClass['input-medium'].' form-select imedium"','value','text',$field_state);

		$stateArr					= [];
		$stateArr[] = JHTML::_('select.option','-1',JText::_('OS_SELECT_REQUIRE_STATE'));
		$stateArr[] = JHTML::_('select.option','0',JText::_('JNO'));
		$stateArr[] = JHTML::_('select.option','1',JText::_('JYES'));
		$lists['field_require'] = JHTML::_('select.genericlist',$stateArr,'field_require','onChange="javascript:document.adminForm.submit();" class="'.$mapClass['input-medium'].' form-select imedium"','value','text',$field_require);

		$db->setQuery("Select count(id) from #__app_sch_fields where 1=1 $sql");
		$total						= $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav					= new JPagination($total,$limitstart,$limit);
		$query						= "Select * from #__app_sch_fields where 1=1 $sql $order_by";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows						= $db->loadObjectList();
		
		if(count($rows) > 0)
		{
			for($i=0;$i<count($rows);$i++)
			{
				$row = $rows[$i];
				if($row->field_area == 0)
				{
					$query = "Select b.service_name from #__app_sch_service_fields as a inner join #__app_sch_services as b on b.id = a.service_id where a.field_id = '$row->id'";
					$db->setQuery($query);
					$serviceArr = array();
					$service = "";
					$services = $db->loadObjectList();
					if(count($services ) > 0)
					{
						for($j=0;$j<count($services);$j++)
						{
							$serviceArr[] = $services[$j]->service_name;
						}
						$service = implode(", ",$serviceArr);
					}
					$rows[$i]->service = $service;
				}
			}
		}
		
		$typeArea[] = JHTML::_('select.option','',JText::_('OS_FIELD_AREA'));
		$typeArea[] = JHTML::_('select.option','0',JText::_('OS_SERVICES'));
		$typeArea[] = JHTML::_('select.option','1',JText::_('OS_BOOKING_FORM'));
		$lists['field_area'] = JHTML::_('select.genericlist',$typeArea,'field_area','onChange="javascript:document.adminForm.submit();" class="'.$mapClass['input-medium'].' form-select imedium"','value','text',$field_area);

		$lists['show_form'] = $show_form;
		
		HTML_OsAppscheduleFields::listFields($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Field edit
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function field_edit($option,$id)
	{
		global $mainframe,$languages,$configClass,$mapClass;
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Field','OsAppTable');
		if($id > 0)
		{
			$row->load((int)$id);
		}
		else
		{
			$row->published			= 1;
			$row->show_in_email		= 1;
		}
		$typeArr[] = JHTML::_('select.option','0',JText::_('OS_TEXTFIELD'));
		$typeArr[] = JHTML::_('select.option','1',JText::_('OS_SELECTLIST'));
		$typeArr[] = JHTML::_('select.option','2',JText::_('OS_CHECKBOXES'));
		$typeArr[] = JHTML::_('select.option','3',JText::_('OS_IMAGE'));
        $typeArr[] = JHTML::_('select.option','4',JText::_('OS_FILEUPLOAD'));
		$typeArr[] = JHTML::_('select.option','5',JText::_('OS_MESSAGE'));
		$lists['field_type'] = JHTML::_('select.genericlist',$typeArr,'field_type','onChange="javascript:showOptions()" class="'.$mapClass['input-large'].' form-select ilarge"','value','text',$row->field_type);
		
		$typeArea = array();
		if(intval($id) > 0 || $row->field_type == 1 ||  $row->field_type == 2 || $row->field_type == 5)
		{
			$typeArea[] = JHTML::_('select.option','0',JText::_('OS_SERVICES'));
		}
		$typeArea[] = JHTML::_('select.option','1',JText::_('OS_BOOKING_FORM'));
		$lists['field_area'] = JHTML::_('select.genericlist',$typeArea,'field_area','onChange="javascript:showDiv()" class="'.$mapClass['input-large'].' form-select ilarge"','value','text',$row->field_area);
		
		$db->setQuery("Select id as value, service_name as text from #__app_sch_services order by service_name");
		$services = $db->loadObjectList();
		
		$db->setQuery("Select service_id from #__app_sch_service_fields where field_id = '$row->id'");
		$serviceids = $db->loadObjectList();
		$serviceArr = array();
		for($i=0;$i<count($serviceids);$i++)
		{
			$serviceArr[] = $serviceids[$i]->service_id;
		}
		
		$lists['services'] = JHTML::_('select.genericlist',$services,'service_id[]','multiple class="form-select ilarge" ','value','text',$serviceArr);
		
		$fieldOptions = array();
		if($id > 0){
			$db->setQuery("Select a.* from #__app_sch_field_options as a inner join #__app_sch_fields as b on b.id = a.field_id where a.field_id = '$id' and b.field_type in (1,2)");
			$fieldOptions = $db->loadObjectList();
		}
		
		$options = [];
		if($configClass['field_integration'] == 1)
		{
			$fields = ['address1', 'address2', 'city', 'region', 'country', 'postal_code', 'phone', 'website', 'favoritebook', 'aboutme', 'dob'];
			$options[]     = JHtml::_('select.option', '', JText::_('OS_SELECT_FIELD'));
			foreach ($fields as $field)
			{
				$options[] = JHtml::_('select.option', $field, $field);
			}
			foreach (self::getUserFields() as $field)
			{
				$options[] = JHtml::_('select.option', $field->name, $field->title);
			}
		}
		elseif($configClass['field_integration'] == 2)
		{
			$fields = array_keys($db->getTableColumns('#__jsn_users'));
			$fields = array_diff($fields, ['id', 'params']);

			$options[]     = JHtml::_('select.option', '', JText::_('OS_SELECT_FIELD'));

			foreach ($fields as $field)
			{
				$options[] = JHtml::_('select.option', $field, $field);
			}
		}
		
		$lists['field_mapping'] = JHtml::_('select.genericlist', $options, 'field_mapping', ' class="'.$mapClass['input-large'].' form-select ilarge" ', 'value', 'text',
			$row->field_mapping);
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		HTML_OSappscheduleFields::field_edit($option,$row,$lists,$fieldOptions,$translatable);
	}
	
	public static function getUserFields()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name, title')
			->from('#__fields')
			->where($db->quoteName('context') . '=' . $db->quote('com_users.user'))
			->where($db->quoteName('state') . ' = 1');
		$db->setQuery($query);

		return $db->loadObjectList('name');
	}
	
	static function fields_save($option,$save)
	{
		global $mainframe,$languages,$jinput,$configClass;
		$db = JFactory::getDbo();
		$field_area = $jinput->getInt('field_area',0);
		$post = $jinput->post->getArray();
		$row = &JTable::getInstance('Field','OsAppTable');
		$row->bind($post);
		$row->field_options = "";
		if(!$row->store())
		{
			throw new Exception($row->getError());
		}
		$id = $jinput->getInt('id',0);
		if($id == 0)
		{
			$id = $db->insertid();
		}
		
		$db->setQuery("Select a.* from #__app_sch_field_options as a inner join #__app_sch_fields as b on b.id = a.field_id where a.field_id = '$id' and b.field_type in (1,2)");
		$fields = $db->loadObjectList();
		
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language){	
				$sef = $language->sef;
				$field_label_language = $jinput->get('field_label_'.$sef,'','string');
				if($field_label_language == ""){
					$field_label_language = $row->field_label;
				}
				if($field_label_language != ""){
					$field = &JTable::getInstance('Field','OsAppTable');
					$field->id = $row->id;
					$field->{'field_label_'.$sef} = $field_label_language;
					$field->store();
				}
				
				foreach ($fields as $field){
					$option_id = $field->id;
					$option_name = "field_option_".$sef."_".$option_id;
					$option_value = $jinput->get($option_name,'','string');
					if($option_value == ""){
						$option_value = $field->field_option;
					}
					$option_name = "field_option_".$sef;
					$db->setQuery("Update #__app_sch_field_options set `$option_name` = '".htmlspecialchars($option_value)."' where id = '$option_id'");
					$db->execute();
				}
			}
		}


		$service_id = $jinput->get('service_id', array(), 'array');
		$db->setQuery("Delete from #__app_sch_service_fields where field_id = '$id'");
		$db->execute();
		if($field_area == 0)
		{
			if($row->field_area == 0)
			{
				if(count($service_id) > 0)
				{
					for($i=0;$i<count($service_id);$i++){
						$sid = $service_id[$i];
						$db->setQuery("Insert into #__app_sch_service_fields (id,field_id,service_id) values (NULL,'$id','$sid')");
						$db->execute();
					}
				}
			}
		}
		
		if($save)
		{
			$mainframe->enqueueMessage(JText::_('OS_FIELD_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=fields_list");
		}
		else
		{
			$mainframe->enqueueMessage(JText::_('OS_FIELD_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=fields_edit&cid[]=".$id);
		}
	}
	
	/**
	 * publish or unpublish agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function fields_state($option,$cid,$state){
		global $mainframe;
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();
		if(count($cid)>0)	{
			$cids 	= implode(",",$cid);
			$db->setQuery("UPDATE #__app_sch_fields SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
		OSappscheduleFields::field_list($option);
	}

	static function fields_requiredstate($option,$cid,$state){
		global $mainframe;
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();
		if(count($cid)>0)	{
			$cids 	= implode(",",$cid);
			$db->setQuery("UPDATE #__app_sch_fields SET `required` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
		OSappscheduleFields::field_list($option);
	}
	
	/**
	 * remove agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function fields_remove($option,$cid){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__app_sch_fields WHERE id IN ($cids)");
			$db->execute();
			
			$db->setQuery("DELETE FROM #__app_sch_service_fields WHERE field_id IN ($cids)");
			$db->execute();
			
			$db->setQuery("DELETE FROM #__app_sch_field_data WHERE fid IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OSappscheduleFields::field_list($option);
	}

	static function changeDefaultOptionAjax(){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$field_id = $jinput->getInt('field_id',0);
		$db->setQuery("Select * from #__app_sch_fields where id = '$field_id'");
		$fieldObject			= $db->loadObject();
		$optionid = $jinput->getInt('optionid',0);
		$new_status = $jinput->getInt('new_status',0);
		if($new_status == 1){
			$db->setQuery("Update #__app_sch_field_options set `option_default` = '0' where field_id = '$field_id'");
			$db->execute();
		}
		$db->setQuery("Update #__app_sch_field_options set `option_default` = '$new_status' where id = '$optionid'");
		$db->execute();

		$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field_id' order by ordering");
		$fields	= $db->loadObjectList();
		OSappscheduleFields::showFieldOptions($fields,$fieldObject);
		$mainframe->close();
	}
}
?>