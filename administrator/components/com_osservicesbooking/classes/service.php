<?php
/*------------------------------------------------------------------------
# service.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
/**
 * Enter description here...
 *
 */
class OSappscheduleService{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
	{
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__app_sch_services')->set(array('`access` = 1'))->where(array('`access` = 0'));
        $db->setQuery($query);
        $db->execute();
		$cid        = $jinput->get('cid',array(),'ARRAY');
		\Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		switch ($task){
			default:
			case "service_list":
				OSappscheduleService::service_list($option);
			break;
			case "service_unpublish":
				OSappscheduleService::service_state($option,$cid,0);
			break;
			case "service_publish":
				OSappscheduleService::service_state($option,$cid,1);
			break;	
			case "service_remove":
				OSappscheduleService::service_remove($option,$cid);
			break;
			case "service_orderup":
				OSappscheduleService::service_order($option,$cid[0],-1);
			break;
			case "service_saveorderAjax":
                OSappscheduleService::saveorderAjax($option);
            break;
			case "service_orderdown":
				OSappscheduleService::service_order($option,$cid[0],1);
			break;
			case "service_saveorder":
				OSappscheduleService::service_saveorder($option,$cid);
			break;
			case "service_add":
				OSappscheduleService::service_modify($option,0);
			break;	
			case "service_edit":
				OSappscheduleService::service_modify($option,$cid[0]);
			break;
			case "service_apply":
				OSappscheduleService::service_save($option,0);
			break;
			case "service_save":
				OSappscheduleService::service_save($option,1);
			break;
			case "install_list":
				OSappscheduleService::confirmInstall($option);
			break;
			case "service_installdata":
				OSappscheduleService::installSampleData($option);
			break;
			case "goto_index":
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php");
			break;
			case "service_gotolist":
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_list");
			break;
			case "service_manageavailability":
				OSappscheduleService::manageAvailability($option);
			break;
			case "service_addunvailabletime":
				OSappscheduleService::addUnavailableTime($option);
			break;
			case "service_removeunvailabletime":
				OSappscheduleService::removeUnavailableTime($option);
			break;
			case "service_managetimeslots":
				OSappscheduleService::manageTimeSlots();
			break;
			case "service_timeslotadd":
				OSappscheduleService::editTimeSlot(0);
			break;
			case "service_timeslotedit":
				OSappscheduleService::editTimeSlot($cid[0]);
			break;
			case "service_timeslotsave":
				OSappscheduleService::saveTimeSlot(1);
			break;
			case "service_timeslotapply":
				OSappscheduleService::saveTimeSlot(0);
			break;
			case "service_timeslotsavenew":
				OSappscheduleService::saveTimeSlot(2);
			break;
			case "service_removetimeslots":
				OSappscheduleService::removeTimeSlots($cid);
			break;
			case "service_gotolisttimeslot":
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_managetimeslots&sid=".$jinput->getInt('sid',0));
			break;
			case "service_addcustomprice":
				OSappscheduleService::addcustomprice();
			break;
			case "service_removecustomprice":
				OSappscheduleService::removecustomprice();
			break;
			case "service_duplicate":
				OSappscheduleService::duplicateServices($cid[0]);
			break;
			case "service_batchimportcustomtimeslots":
				OSappscheduleService::batchimportcustomtimeslots();
			break;
			case "service_doimporttimeslots":
				OSappscheduleService::doimporttimeslots();
			break;
			case "service_specialrates":
				OSappscheduleService::specialrates();
			break;
			case "service_addrate":
				OSappscheduleService::modifyRate(0);
			break;
			case "service_modifyrate":
				OSappscheduleService::modifyRate($cid[0]);
			break;
			case "service_saverate":
				OSappscheduleService::saveRate(1);
			break;
			case "service_applyrate":
				OSappscheduleService::saveRate(0);
			break;
			case "service_removerate":
				OSappscheduleService::removeRates($cid);
			break;
			case "service_updateWorkingStatus":
				OSappscheduleService::updateWorkingStatus();
			break;
			case "service_removeWorking":
				OSappscheduleService::removeWorking();
			break;
			case "service_saveWorking":
				OSappscheduleService::saveWorking();
			break;
			case "service_cancelrate":
				$mainframe->redirect('index.php?option=com_osservicesbooking&task=service_specialrates');
			break;
			case "service_cancel":
				$mainframe->redirect('index.php?option=com_osservicesbooking&task=service_list');
			break;
		}
	}

	static function saveorderAjax($option)
	{
        global $jinput;
        $db				= JFactory::getDBO();
        $cid 			= $jinput->get( 'cid', array(), 'array' );
        $order			= $jinput->get( 'order', array(), 'array' );
        $row			= JTable::getInstance('Service','OsAppTable');
        $groupings		= array();
        // update ordering values
        $txt = "";
        for( $i=0; $i < count($cid); $i++ )
        {
            $row->load( $cid[$i] );
            // track parents
            if ($row->ordering != $order[$i])
            {
                $row->ordering = $order[$i];
                $txt .= $cid[$i]." - ".$row->ordering."     ";
                $row->store();
				//$row->reorder();
            } // if
        } // for
        //$myfile = fopen(JPATH_ROOT."/newfile.txt", "w") or die("Unable to open file!");
		//fwrite($myfile, $txt);
		//fclose($myfile);
		for( $i=0; $i < count($cid); $i++ )
        {
			$row->load( $cid[$i] );
			$row->reorder();
		}
    }
	
	/**
	 * Confirm install sample data
	 *
	 * @param unknown_type $option
	 */
	static function confirmInstall($option){
		global $mainframe;
		HTML_OSappscheduleService::confirmInstallSampleDataForm($option);
	}
	
	/**
	 * Install sample data
	 *
	 * @param unknown_type $option
	 */
	static function installSampleData($option)
	{
		global $mainframe;
		$db = JFactory::getDbo();
		$db->setQuery("DELETE FROM #__app_sch_employee");
		$db->execute();
		$db->setQuery("DELETE FROM #__app_sch_employee_service");
		$db->execute();
		$db->setQuery("DELETE FROM #__app_sch_field_data");
		$db->execute();
		$db->setQuery("DELETE FROM #__app_sch_fields");
		$db->execute();
		$db->setQuery("DELETE FROM #__app_sch_service_fields");
		$db->execute();
		$db->setQuery("DELETE FROM #__app_sch_services");
		$db->execute();
		$sampleSql = JPATH_COMPONENT_ADMINISTRATOR.'/sql/sample.osservicesbooking.sql' ;
		$sql = file_get_contents($sampleSql) ;
		$queries = $db->splitSql($sql);
		if (count($queries)) 
		{
			foreach ($queries as $query) 
			{
				$query = trim($query);
				if ($query != '' && $query[0] != '#')
				{
					$db->setQuery($query);
					$db->execute();						
				}	
			}
		}
		$mainframe->enqueueMessage("Sample data have been installed succesfully");
		$mainframe->redirect("index.php?option=com_osservicesbooking");
	}
	
	/**
	 * agent list
	 *
	 * @param unknown_type $option
	 */
	static function service_list($option)
	{
		global $mainframe;
		$mainframe					= JFactory::getApplication();
		$db							= JFactory::getDBO();
		$lists						= array();
		$condition					= '';
		$config						= new JConfig();
		$list_limit					= $config->list_limit;

		// filte sort
		$filter_order 				= $mainframe->getUserStateFromRequest($option.'.service.filter_order','filter_order','ordering','string');
		$filter_order_Dir 			= $mainframe->getUserStateFromRequest($option.'.service.filter_order_Dir','filter_order_Dir','asc','string');
		$lists['order'] 			= $filter_order;
		$lists['order_Dir'] 		= $filter_order_Dir;
		$order_by 					= " ORDER BY $filter_order $filter_order_Dir";
		
		// Get the pagination request variables
		$limit						= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $list_limit, 'int' );
		$limitstart					= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		// search 
		$keyword 			 		= $db->escape(trim($mainframe->getUserStateFromRequest($option.'.service.keyword','keyword','','string')));
		$lists['keyword']  			= $keyword;
		if($keyword != "")
		{
			$condition 			   .= " AND (";
			$condition 			   .= " service_name LIKE '%$keyword%'";
			$condition 			   .= " OR service_description LIKE '%$keyword%'";
			$condition 			   .= " )";
		}
		// filter state
		$filter_state 				= $mainframe->getUserStateFromRequest($option.'.service.filter_state','filter_state','','string');				
		$lists['filter_state'] 		= JHtml::_('grid.state',$filter_state);
		$condition 				   .= ($filter_state == 'P')? " AND `published` = 1":(($filter_state == 'U')? " AND `published` = 0":"");

		// get data	
		$count 						= "SELECT count(id) FROM #__app_sch_services WHERE 1=1";
		$count 					   .= $condition;
		$db->setQuery($count);
		$total 						= $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav 					= new JPagination($total,$limitstart,$limit);
		
		$list  						= "SELECT * FROM #__app_sch_services "
										."\n WHERE 1=1 ";
		$list 					   .= $condition;
		$list 					   .= $order_by;
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		$rows 						= $db->loadObjectList();
		
		
		HTML_OSappscheduleService::service_list($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * publish or unpublish agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function service_state($option,$cid,$state){
		global $mainframe;
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();
		if(count($cid)>0)	{
			$cids 	= implode(",",$cid);
			$db->setQuery("UPDATE #__app_sch_services SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
		OSappscheduleService::service_list($option);
	}
	
	/**
	 * remove agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function service_remove($option,$cid){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__app_sch_services WHERE id IN ($cids)");
			$db->execute();
			
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OSappscheduleService::service_list($option);
	}
	
	/**
	 * change order price group
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $direction
	 */
	static function service_order($option,$id,$direction){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$row = &JTable::getInstance('Service','OsAppTable');
		$row->load($id);
		$row->move( $direction);
		$row->reorder();
		$mainframe->enqueueMessage(JText::_("OS_NEW_ORDERING_SAVED"),'message');
		OSappscheduleService::service_list($option);
	}
	
	/**
	 * save new order
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function service_saveorder($option,$cid){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
		$msg = JText::_("OS_NEW_ORDERING_SAVED");
		//$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		$order      = $jinput->get('order',array(),'ARRAY');
		//JArrayHelper::toInteger($order);
		\Joomla\Utilities\ArrayHelper::toInteger($order);
		$row = &JTable::getInstance('Service','OsAppTable');
		
		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) 
				{
					$msg = JText::_("OS_ERROR_SAVING_ORDERING");
					break;
				}
			}
		}
		// execute updateOrder
		$row->reorder();
		$mainframe->enqueueMessage($msg,'message');
		OSappscheduleService::service_list($option);
	}
	
	
	/**
	 * Service modify
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function service_modify($option,$id)
	{
		global $languages, $configClass, $mapClass;
		OSBHelper::loadTooltip();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Service','OsAppTable');
		if($id > 0)
		{
			$row->load((int)$id);
		}
		else
		{
			$row->published = 1;
			$row->service_before=0;
			$row->service_after=0;
			$row->service_total=0;
		}
		
		// creat published
		$lists['published'] = JHtml::_('select.booleanlist','published','class="inputbox"',$row->published);
			
		// build the html select list for ordering
		$query = " SELECT ordering AS value, service_name AS text "
				.' FROM #__app_sch_services '
				." WHERE `published` = '1'"
				." ORDER BY ordering";
		//$lists['ordering'] = JHTML::_('list.specificordering',  $row, $row->id, $query );
		$lists['ordering'] = JHTML::_('list.ordering', 'ordering', $query ,'class="input-large form-select imedium"',$row->ordering);
		
		if($id > 0){
			$db->setQuery("Select * from #__app_sch_fields where id in (Select field_id from #__app_sch_service_fields where service_id = '$id')");
			$fields = $db->loadObjectList();
			$lists['fields'] = $fields;
		}
		
		$timeArr[] = JHTML::_('select.option','0',JText::_('OS_NORMALLY_TIME_SLOT'));
		$timeArr[] = JHTML::_('select.option','1',JText::_('OS_CUSTOM_TIME_SLOT'));
		$lists['time_slot'] = JHTML::_('select.genericlist',$timeArr,'service_time_type','class="input-large form-select ilarge" onChange="javascript:showDiv();"','value','text',$row->service_time_type);
		
		$hourArr =  array();
		$hourArr[] = JHTML::_('select.option','','');
		for($i=0;$i<24;$i++){
			if($i<10){
				$value = "0".$i;
			}else{
				$value = $i;
			}
			$hourArr[] = JHTML::_('select.option',$i,$value);
		}
		$lists['hours'] = $hourArr;
		$minArr = array();
		$minArr[] = JHTML::_('select.option','','');
		for($i=0;$i<60;$i=$i+5){
			if($i<10){
				$value = "0".$i;
			}else{
				$value = $i;
			}
			$minArr[] = JHTML::_('select.option',$i,$value);
		}
		$lists['mins'] = $minArr;
		
		$db->setQuery("Select * from #__app_sch_custom_time_slots where sid = '$row->id'");
		$lists['custom_time'] = $db->loadObjectList();
		
		$db->setQuery("Select *, `id` as value, `category_name` as text, `parent_id` as parent, `category_name` as treename from #__app_sch_categories where published = '1' order by category_name");
        $mitems = $db->loadObjectList();

        // establish the hierarchy of the menu
        $children = array();
        if ($mitems)
        {
            // first pass - collect children
            foreach ($mitems as $v) {
                $pt = $v->parent_id;
                if ($v->treename == "") {
                    $v->treename = $v->category_name;
                }
                if ($v->title == "") {
                    $v->title = $v->category_name;
                }
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        // second pass - get an indent list of the items
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

        // assemble menu items to the array
        $parentArr = array();
		$categorySelect = "<select name='category_id' class='".$mapClass['input-large']." form-select ilarge'><option value='0'>".JText::_('OS_SELECT_CATEGORY')."</option>";

        foreach ($list as $item)
        {
            $var = explode("*", $item->treename);

            if (count($var) > 0)
            {
                $treename = "";
                for ($i = 0; $i < count($var) - 1; $i++)
                {
                    $treename .= " _ ";
                }
            }
            $text = $item->treename;
            $db->setQuery("Select count(id) from #__app_sch_categories where parent_id = '$item->id' and published = '1'");
            $count = $db->loadResult();

            if($count > 0)
            {
                //$parentArr[] = JHTML::_('select.optgroup', $text);

				$categorySelect .= "<optgroup label='".$text."'>";
            }
            else
            {
               // $parentArr[] = JHTML::_('select.option', $item->id, $text);

				if((int) $row->category_id == $item->id)
				{
					$categorySelect .= " <option value='".$item->id."' selected>".$text."</option>";
				}
				else
				{
					$categorySelect .= " <option value='".$item->id."'>".$text."</option>";
				}
            }
        }
		$categorySelect .= "</select>";
		$lists['category'] = $categorySelect;
        $categoryArr    = array();
		$categoryArr[]  = JHTML::_('select.option',0,JText::_('OS_SELECT_CATEGORY'));
		$categoryArr    = array_merge($categoryArr,$parentArr);
		//$lists['category'] = JHTML::_('select.genericlist',$categoryArr,'category_id','class="input-large"','value','text',(int)$row->category_id);
		$optionArr		= array();
		$optionArr[]	= JHTML::_('select.option','0',JText::_('OS_INHERIT_FROM_CONFIGURATION'));
		$optionArr[]	= JHTML::_('select.option','1',JText::_('OS_IS_SERVICE_TIME_LENGTH'));
		//$format_steps = explode('|','5|10|15|20|25|30|35|40|45|50|55|60|90||100|120|180|240|300|360|420|480');
		$start			= 5;
		$end			= 480;
		for($i = $start; $i <= $end ; $i = $i + 5)
		{
			$optionArr[] = JHTML::_('select.option', $i, $i." ".JText::_('OS_MINUTES'));
		}
		//foreach ($format_steps as $format_step) 
		//{
			//$optionArr[] = JHTML::_('select.option', $format_step, $format_step." ".JText::_('OS_MINUTES'));
		//}
		$lists['step_in_minutes'] = JHTML::_('select.genericlist',$optionArr,'step_in_minutes','class="'.$mapClass['input-large'].' form-select ilarge"','value','text',$row->step_in_minutes);
		$translatable = JLanguageMultilang::isEnabled() && count($languages);

        $lists['access'] = OSBHelper::accessDropdown('access',$row->access,'class="input-large form-select imedium"');

		$acyLists = null;
		if(file_exists(JPATH_ADMINISTRATOR . '/components/com_acym/acym.php') && JComponentHelper::isEnabled('com_acym', true))
		{
			if(include_once(rtrim(JPATH_ADMINISTRATOR,DS).'/components/com_acym/helpers/helper.php')){
				$listClass = acym_get('class.list');
				$acyLists  = $listClass->getAllWithIdName();
				$lists['acyLists'] = $acyLists;
			}
		}
		elseif(file_exists(JPATH_ADMINISTRATOR . '/components/com_acymailing/acymailing.php') && JComponentHelper::isEnabled('com_acymailing', true))
		{
			if(include_once(rtrim(JPATH_ADMINISTRATOR,DS).'/components/com_acymailing/helpers/helper.php'))
			{
				$listClass = acymailing_get('class.list');
				$acyLists = $listClass->getLists();	
				$lists['acyLists'] = $acyLists;
			 }
		}
		
		$db->setQuery("Select * from #__app_sch_service_custom_prices where sid = '$row->id' order by cstart");
		$customs = $db->loadObjectList();
		
		$optionArr = array();
		$optionArr[] = JHtml::_('select.option',0,JText::_('OS_FIXED_AMOUNT_DISCOUNTED'));
		$optionArr[] = JHtml::_('select.option',1,JText::_('OS_PERCENTAGE_DISCOUNT'));
		$lists['early_bird_type'] = JHtml::_('select.genericlist',$optionArr,'early_bird_type','class="'.$mapClass['input-large'].' form-select ilarge"','value','text',$row->early_bird_type);
		
		$optionArr = array();
		$optionArr[] = JHtml::_('select.option',0,JText::_('OS_FIXED_AMOUNT_DISCOUNTED'));
		$optionArr[] = JHtml::_('select.option',1,JText::_('OS_PERCENTAGE_DISCOUNT'));
		$lists['discount_type'] = JHtml::_('select.genericlist',$optionArr,'discount_type','class="'.$mapClass['input-large'].' form-select ilarge"','value','text',$row->discount_type);


        $options   = [];
        $options[] = JHtml::_('select.option', '', JText::_('OS_ALL_PAYMENT_METHODS'), 'id', 'title');

        $query = $db->getQuery(true);
        $query->clear()
            ->select('id, title')
            ->from('#__app_sch_plugins')
            ->where('published=1');
        $db->setQuery($query);
        $lists['payment_methods'] = JHtml::_('select.genericlist', array_merge($options, $db->loadObjectList()), 'payment_methods[]', ' class="inputbox form-control" multiple="multiple" ', 'id', 'title', explode(',', $row->payment_plugins));

        if($id > 0 && $configClass['active_linked_service'] == 1)
        {
            $query->clear();
            $query->select('id as value, service_name as text')->from('#__app_sch_services');
            $query->where('service_time_type = '.$row->service_time_type);
            $query->where('id <> '.$id);
            $query->order('ordering');
            $db->setQuery($query);
            $services = $db->loadObjectList();

            $query->clear();
            $query->select('linked_service')->from('#__app_sch_service_linked')->where('sid = '.$row->id);
            $db->setQuery($query);
            $linkedServices = $db->loadColumn(0);

            $lists['linked_services'] = JHtml::_('select.genericlist', $services, 'linked_services[]', 'multiple', 'value', 'text', $linkedServices);
        }

		if(OSBHelper::isAvailableVenue())
		{
			$db->setQuery("Select id as value, concat(venue_name, ' - ' , address) as text from #__app_sch_venues where published = '1' order by address");
			$venues = $db->loadObjectList();
			$vids = [];
			if($id > 0)
			{
				$db->setQuery("Select vid from #__app_sch_venue_services where sid = '$id'");
				$vids = $db->loadColumn(0);
			}

			if(count($venues) > 0)
			{
				$lists['venue_list'] = JHTML::_('select.genericlist',$venues,'venues[]','class="input-large ilarge form-control" multiple','value','text', $vids);
			}
		}
		
		HTML_OSappscheduleService::service_modify($option,$row,$lists,$customs,$translatable);
	}
	
	/**
	 * save service
	 *
	 * @param unknown_type $option
	 */
	static function service_save($option,$save)
    {
		global $mainframe,$languages,$jinput, $configClass;
		$db                         = JFactory::getDbo();
		
		$post                       = $jinput->post->getArray();
		$row                        = &JTable::getInstance('Service','OsAppTable');
		$row->bind($post);
		
		$row->service_name			= str_replace("-","", $row->service_name);
		$row->service_name			= str_replace("|","", $row->service_name);
		$row->service_name			= str_replace("&","", $row->service_name);
		$row->category_id			= $jinput->getInt('category_id',0);
		$repeat_day                 = $jinput->getInt('repeat_day',0);
		$repeat_week                = $jinput->getInt('repeat_week',0);
		$repeat_fortnight           = $jinput->getInt('repeat_fortnight',0);
		$repeat_month               = $jinput->getInt('repeat_month',0);
		$row->repeat_day            = $repeat_day;
		$row->repeat_week           = $repeat_week;
		$row->repeat_fortnight      = $repeat_fortnight;
		$row->repeat_month          = $repeat_month;
		
		$remove_image               = $jinput->getInt('remove_image',0);
		
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$photo_name             = time()."_".str_replace(" ","_",$_FILES['image']['name']);
			move_uploaded_file($_FILES['image']['tmp_name'],JPATH_ROOT.DS."images/osservicesbooking/services".DS.$photo_name);
			$row->service_photo		= $photo_name;
		}
		elseif($remove_image == 1)
        {
			$row->service_photo     = "";
		}
		// if new item, order last in appropriate group
		if (!$row->id)
		{
			$row->ordering          = $row->getNextOrder();
		}
		$payment_methods            = $jinput->get('payment_methods',array(),'array');
		$payment_methods            = implode(",", $payment_methods);
		$row->payment_plugins       = $payment_methods;
		$service_description        = $_POST['service_description'];
		$row->service_description   = $service_description;
		$row->max_seats				= (int)$row->max_seats;
		$row->early_bird_amount		= (int)$row->early_bird_amount;
		$row->early_bird_days		= (int)$row->early_bird_days;
		$row->discount_amount		= (int)$row->discount_amount;

		$row->check();
		$row->service_total         = $row->service_length + (int)$row->service_before + (int)$row->service_after;
		$msg                        = JText::_('OS_ITEM_HAS_BEEN_SAVED');
	 	if (!$row->store())
	 	{
		 	$msg                    = JText::_('OS_ERROR_SAVING')." - ".$row->getError();
		}
		$mainframe->enqueueMessage($msg,'message');
		$row->reorder();
		$translatable               = JLanguageMultilang::isEnabled() && count($languages);
		if($translatable)
		{
			foreach ($languages as $language)
			{
				$sef                = $language->sef;
				$service_language   = $jinput->get('service_name_'.$sef,'','string');//JRequest::getVar('service_name_'.$sef,'');
				if($service_language == "")
				{
					$address_language = $row->service_name;
				}
				
				if($address_language != "")
				{
					$address_language			= str_replace("-","", $address_language);
					$address_language			= str_replace("|","", $address_language);
					$address_language			= str_replace("&","", $address_language);

					$service = &JTable::getInstance('Service','OsAppTable');
					$service->id = $row->id;
					$service->{'service_name_'.$sef} = $address_language;
                    $service->access = $row->access;
					$service->store();
				}
				
				$service_description_language = $_POST['service_description_'.$sef];
				if($service_description_language == "")
				{
					$service_description_language = $row->service_description;
				}
				if($service_description_language != "")
				{
					$service = &JTable::getInstance('Service','OsAppTable');
					$service->id = $row->id;
                    $service->access = $row->access;
					$service->{'service_description_'.$sef} = $service_description_language;
					$service->store();
				}
			}
		}
		
		//update adjustment price
		$db->setQuery("Delete from #__app_sch_service_price_adjustment where sid = '$row->id'");
		$db->execute();
		for($i=1;$i<=7;$i++)
		{
			$same                   = $jinput->getInt('same'.$i,0);
			$price                  = $jinput->getFloat('price'.$i,0);
			if($same == 1)
			{
				$db->setQuery("Insert into #__app_sch_service_price_adjustment (id,sid,date_in_week,same_as_original,price) values (NULL,'$row->id','$i','1','0.00')");
				$db->execute();
			}
			else
			{
				$db->setQuery("Insert into #__app_sch_service_price_adjustment (id,sid,date_in_week,same_as_original,price) values (NULL,'$row->id','$i','0','$price')");
				$db->execute();
			}
		}

		//save linked services
        if($configClass['active_linked_service'] == 1)
        {
            $db->setQuery("Delete from #__app_sch_service_linked where sid = '$row->id' or linked_service = '$row->id'");
            $db->execute();

            $linked_services = $jinput->get('linked_services', array(), 'array');
            if (count($linked_services) > 0) 
			{
                foreach ($linked_services as $service) 
				{
                    $db->setQuery("Insert into #__app_sch_service_linked (id, sid , linked_service) values (NULL, '$row->id', '$service')");
                    $db->execute();
                    $db->setQuery("Insert into #__app_sch_service_linked (id, sid , linked_service) values (NULL, '$service', '$row->id')");
                    $db->execute();
                }
            }
        }

		if(OSBHelper::isAvailableVenue())
		{
			$db->setQuery("Delete from #__app_sch_venue_services where sid = '$row->id'");
			$db->execute();

			$venues = $jinput->get('venues', [], 'array');
			if(count($venues))
			{
				foreach($venues as $v)
				{
					$db->setQuery("Insert into #__app_sch_venue_services (id , sid, vid) values (NULL, $row->id, $v)");
					$db->execute();
				}
			}
		}

		if($save)
		{
			OSappscheduleService::service_list($option);
		}
		else
		{
			OSappscheduleService::service_modify($option,$row->id);
		}
	}
	
	/**
	 * Manage availability
	 *
	 * @param unknown_type $option
	 */
	static function manageAvailability($option)
	{
		global $mainframe,$configClass,$jinput;
		$id = $jinput->getInt('id',0);
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_services where id = '$id'");
		$service = $db->loadObject();
		$db->setQuery("Select * from #__app_sch_service_availability where sid = '$id' order by avail_date desc");
		$dates = $db->loadObjectList();
		HTML_OSappscheduleService::manageAvailability($option,$service,$dates);
	}
	
	/**
	 * Add unavailable time
	 *
	 * @param unknown_type $option
	 */
	static function addUnavailableTime($option){
		global $mainframe,$configClass,$jinput;
		$id = $jinput->getInt('id',0);
		$db = JFactory::getDbo();
		$avail_date = $jinput->get('avail_date','','string');
		$start_time = $jinput->get('start_time','','string');
		$end_time   = $jinput->get('end_time','','string');
		$db->setQuery("INSERT INTO #__app_sch_service_availability (id,sid,avail_date,start_time,end_time) VALUES (NULL,'$id','$avail_date','$start_time','$end_time')");
		$db->execute();
		$mainframe->enqueueMessage(JText::_('OS_UNAVAILABILITY_TIME_HAS_BEEN_ADDED'));
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_manageavailability&id=".$id);
	}
	
	/**
	 * Remove unvailable time
	 *
	 * @param unknown_type $option
	 */
	static function removeUnavailableTime($option){
		global $mainframe,$configClass,$jinput;
		$id = $jinput->getInt('id',0);
		$sid = $jinput->getInt('sid',0);
		$db = JFactory::getDbo();
		$db->setQuery("Delete from #__app_sch_service_availability where id = '$id'");
		$db->execute();
		$mainframe->enqueueMessage(JText::_('OS_UNAVAILABILITY_TIME_HAS_BEEN_REMOVED'));
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_manageavailability&id=".$sid);
	}
	
	static function manageTimeSlots(){
		global $mainframe,$configClass,$jinput;
		$document = JFactory::getDocument();
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/ajax.js");
		$id = $jinput->getInt('sid',0);
		$db = JFactory::getDbo();
		$limit = $jinput->getInt('limit',20);
		$limitstart =$jinput->getInt('limitstart',0);
		$db->setQuery("Select * from #__app_sch_services where id = '$id'");
		$service = $db->loadObject();
		$db->setQuery("Select count(a.id) from #__app_sch_custom_time_slots as a where a.sid = '$id'");
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total,$limitstart,$limit);
		$query = "Select a.* from #__app_sch_custom_time_slots as a where a.sid = '$id' order by a.start_hour";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$slots = $db->loadObjectList();
		HTML_OSappscheduleService::manageTimeSlots($service,$slots,$pageNav);
	}
	
	static function editTimeSlot($id){
		global $mainframe,$configClass,$jinput;
		$sid = $jinput->getInt('sid');
		$db = JFactory::getDbo();
		if($id > 0){
			$db->setQuery("Select * from #__app_sch_custom_time_slots where id = '$id'");
			$slot = $db->loadObject();
		}
		$hourArr =  array();
		$hourArr[] = JHTML::_('select.option','','');
		for($i=0;$i<24;$i++){
			if($i<10){
				$value = "0".$i;
			}else{
				$value = $i;
			}
			$hourArr[] = JHTML::_('select.option',$i,$value);
		}
		$lists['hours'] = $hourArr;
		$minArr = array();
		$minArr[] = JHTML::_('select.option','','');
		for($i=0;$i<60;$i=$i+1)
		{
			if($i<10)
			{
				$value = "0".$i;
			}
			else
			{
				$value = $i;
			}
			$minArr[] = JHTML::_('select.option',$i,$value);
		}
		$lists['mins'] = $minArr;
		HTML_OSappscheduleService::editTimeSlot($slot,$lists,$sid);
	}
	
	static function saveTimeSlot($save){
		global $mainframe,$jinput;
		$db = JFactory::getDbo();
		$id = $jinput->getInt('id',0);
		$sid = $jinput->getInt('sid',0);
		$start_hour = $jinput->getInt('start_hour',0);
		$start_min  = $jinput->getInt('start_min',0);
		$end_hour	= $jinput->getInt('end_hour',0);
		$end_min	= $jinput->getInt('end_min',0);
		$nslots 	= $jinput->getInt('nslots',0);
		
		if($id == 0){//add new
			$db->setQuery("Insert into #__app_sch_custom_time_slots (id,sid,start_hour,start_min,end_hour,end_min,nslots) values (NULL,'$sid','$start_hour','$start_min','$end_hour','$end_min','$nslots')");
			$db->execute();
			$id = $db->insertid();
		}else{
			$db->setQuery("Update #__app_sch_custom_time_slots set start_hour = '$start_hour',start_min = '$start_min',end_hour = '$end_hour',end_min = '$end_min',nslots = '$nslots' where id = '$id'");
			$db->execute();
		}
		//update date relation
		$db->setQuery("Delete from #__app_sch_custom_time_slots_relation where time_slot_id = '$id'");
		$db->execute();
		$date_in_week = $jinput->get('date_in_week',array(),'ARRAY');//JRequest::getVar('date_in_week',NULL,array());
		if(count($date_in_week) > 0){
			for($i=0;$i<count($date_in_week);$i++){
				$date = $date_in_week[$i];
				$db->setQuery("Insert into #__app_sch_custom_time_slots_relation (id,time_slot_id,date_in_week) values (NULL,'$id','$date')");
				$db->execute();
			}
		}
		$msg = JText::_('OS_ITEM_HAS_BEEN_SAVED');
		$mainframe->enqueueMessage($msg);
		switch ($save){
			case "0":
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_timeslotedit&cid[]=$id&sid=".$sid);
			break;
			case "1":
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_managetimeslots&sid=".$sid);
			break;
			case "2":
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_timeslotadd&sid=".$sid);
			break;
		}
	}
	
	static function removeTimeSlots($cid){
		global $mainframe,$jinput;
		$db = JFactory::getDbo();
		$sid = $jinput->getInt('sid',0);
		if($cid){
			$cids = implode(",",$cid);
			$db->setQuery("Delete from #__app_sch_custom_time_slots where id in ($cids)");
			$db->execute();
			$db->setQuery("Delete from #__app_sch_custom_time_slots_relation where time_slot_id in ($cids)");
			$db->execute();
		}
		$msg = JText::_('OS_ITEMS_HAVE_BEEN_REMOVED');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_managetimeslots&sid=".$sid);
	}
	
	static function addcustomprice(){
		global $jinput;
		$db = JFactory::getDbo();
		$sid = $jinput->getInt('sid',0);
		$cstart = $jinput->get('cstart','','string');
		$cend = $jinput->get('cend','','string');
		$camount = $jinput->get('camount','','string');
		$db->setQuery("Insert into #__app_sch_service_custom_prices (id,sid,cstart,cend,amount) values (NULL,'$sid','$cstart','$cend','$camount')");
		$db->execute();
		self::getCustomPrice($sid);
		exit();
	}
	
	static function removecustomprice(){
		global $jinput;
		$db = JFactory::getDbo();
		$id = $jinput->getInt('id',0);
		$sid = $jinput->getInt('sid',0);
		$db->setQuery("Delete from #__app_sch_service_custom_prices where id = '$id'");
		$db->execute();
		self::getCustomPrice($sid);
		exit();
	}
	
	public static function getCustomPrice($sid){
		global $jinput;
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_service_custom_prices where sid = '$sid' order by cstart");
		$customs = $db->loadObjectList();
		?>
		<table width="80%" style="border:1px solid #CCC;">
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
						<a href="javascript:removeCustomPrice(<?php echo $rest->id?>,<?php echo $sid?>,'<?php echo JUri::root();?>')" title="Remove price">
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
	
	/**
	 * Duplicate Service Information
	 *
	 * @param unknown_type $id
	 */
	public static function duplicateServices($id){
		global $languages,$mainframe;
		$db                         = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_services where id = '$id'");
		$service                    = $db->loadObject();
		$row = &JTable::getInstance('Service','OsAppTable');
		$row->id                    = 0;
		$row->category_id           = $service->category_id;
		$row->service_name          = JText::_('OS_COPIED')." ".$service->service_name;
		$row->service_price         = $service->service_price;
		$row->service_length        = $service->service_length;
		$row->service_total         = $service->service_total;
		$row->service_description   = $service->service_description;
		$row->service_photo         = $service->service_photo;
		$row->service_time_type     = $service->service_time_type;
		$row->early_bird_amount     = $service->early_bird_amount;
		$row->early_bird_type       = $service->early_bird_type;
		$row->early_bird_days       = $service->early_bird_days;
		$row->discount_timeslots    = $service->discount_timeslots;
		$row->discount_type         = $service->discount_type;
		$row->discount_amount       = $service->discount_amount;
		$row->step_in_minutes       = $service->step_in_minutes;
		$row->repeat_day            = $service->repeat_day;
		$row->repeat_week           = $service->repeat_week;
		$row->repeat_month          = $service->repeat_month;
		$row->max_seats				= $service->max_seats;
		$row->published             = 0;
		$row->access                = $service->access;
		$row->acymailing_list_id    = $service->acymailing_list_id;
		$db->setQuery("Select ordering from #__app_sch_services order by ordering desc limit 1");
		$ordering = $db->loadResult();
		$ordering = $ordering + 1;
		$row->ordering = $ordering;		
		
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		if ($translatable){
			$i = 0;
			foreach ($languages as $language) {						
				$sef = $language->sef;
				$row->{'service_name_'.$sef} = $service->{'service_name_'.$sef};
				$row->{'service_description_'.$sef} = $service->{'service_description_'.$sef};
			}
		}
		if(!$row->store())
		{
			throw new Exception($row->getError(), 500);
		}
		$service_id = $db->insertid();
		
		#__app_sch_service_availability
		$db->setQuery("Select * from #__app_sch_service_availability where sid = '$id'");
		$availabilities = $db->loadObjectList();
		
		if(count($availabilities) > 0){
			foreach ($availabilities as $avail){
				$db->setQuery("Insert into #__app_sch_service_availability (id,sid,avail_date,start_time,end_time) values (NULL,'$service_id','$avail->avail_date','$avail->start_time','$avail->end_time')");
				$db->execute();
			}
		}
		
		#__app_sch_service_custom_prices
		$db->setQuery("Select * from #__app_sch_service_custom_prices where sid = '$id'");
		$custom_prices = $db->loadObjectList();
		
		if(count($custom_prices) > 0){
			foreach ($custom_prices as $custom_price){
				$db->setQuery("Insert into #__app_sch_service_custom_prices (id,sid,cstart,cend,amount) values (NULL,'$service_id','$custom_price->cstart','$custom_price->cend','$custom_price->amount')");
				$db->execute();
			}
		}
		
		#__app_sch_service_fields
		$db->setQuery("Select * from #__app_sch_service_fields where service_id = '$id'");
		$fields = $db->loadObjectList();
		
		if(count($fields) > 0){
			foreach ($fields as $field){
				$db->setQuery("Insert into #__app_sch_service_fields (id,service_id,field_id) values (NULL,'$service_id','$field->field_id')");
				$db->execute();
			}
		}
		
		#__app_sch_service_price_adjustment
		$db->setQuery("Select * from #__app_sch_service_price_adjustment where sid = '$id'");
		$prices = $db->loadObjectList();
		
		if(count($prices) > 0){
			foreach ($prices as $price){
				$db->setQuery("Insert into #__app_sch_service_price_adjustment (id,sid,date_in_week,same_as_original,price) values (NULL,'$service_id','$price->date_in_week','$price->same_as_original','$price->price')");
				$db->execute();
			}
		}
		
		#__app_sch_service_time_custom_slots
		$db->setQuery("Select * from #__app_sch_service_time_custom_slots where sid = '$id'");
		$custom_slots = $db->loadObjectList();
		
		if(count($custom_slots) > 0){
			foreach ($custom_slots as $custom_slot){
				$db->setQuery("Insert into #__app_sch_service_time_custom_slots (id,custom_id,sid,service_slots) values (NULL,'$custom_slot->custom_id','$service_id','$custom_slot->service_slots')");
				$db->execute();
			}
		}
		
		#__app_sch_service_time_custom_slots
		$db->setQuery("Select * from #__app_sch_service_time_custom_slots where sid = '$id'");
		$custom_slots = $db->loadObjectList();
		
		if(count($custom_slots) > 0){
			foreach ($custom_slots as $custom_slot){
				$db->setQuery("Insert into #__app_sch_service_time_custom_slots (id,custom_id,sid,service_slots) values (NULL,'$custom_slot->custom_id','$service_id','$custom_slot->service_slots')");
				$db->execute();
			}
		}
		
		#__app_sch_custom_time_slots
		$db->setQuery("Select * from #__app_sch_custom_time_slots where sid = '$id'");
		$custom_slots = $db->loadObjectList();
		
		if(count($custom_slots) > 0){
			foreach ($custom_slots as $custom_slot){
				$db->setQuery("Insert into #__app_sch_custom_time_slots (id,sid,start_hour,start_min,end_hour,end_min,nslots) values (NULL,'$service_id','$custom_slot->start_hour','$custom_slot->start_min','$custom_slot->end_hour','$custom_slot->end_min','$custom_slot->nslots')");
				$db->execute();

				$new_custom_timeslots = $db->insertID();

				$old_custom_timeslots = $custom_slot->id;

				#__app_sch_custom_time_slots_relation
				$db->setQuery("Select * from #__app_sch_custom_time_slots_relation where time_slot_id = '$old_custom_timeslots'");
				$relation = $db->loadObjectList();
				
				if(count($relation) > 0){
					foreach ($relation as $r){
						$db->setQuery("Insert into #__app_sch_custom_time_slots_relation (id,time_slot_id,date_in_week) values (NULL,'$new_custom_timeslots','$r->date_in_week')");
						$db->execute();
					}
				}
			}
		}

		
		$mainframe->enqueueMessage(JText::_('OS_SERVICE_HAS_BEEN_DUPLICATED'));
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_list");
	}


	static function batchimportcustomtimeslots(){
		global $mainframe,$jinput;
		$sid = $jinput->getInt('sid',0);
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service = $db->loadObject();
		HTML_OSappscheduleService::showBatchCustomTimeslot($service);
	}

	static function doimporttimeslots()
	{
		global $mainframe,$jinput,$configClass;
		$db = JFactory::getDbo();
		$sid = $jinput->getInt('sid',0);
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		if(is_uploaded_file($_FILES['csvfile']['tmp_name']))
		{
			$filename = time().str_replace(" ","_",$_FILES['csvfile']['name']);
			move_uploaded_file($_FILES['csvfile']['tmp_name'],JPATH_ROOT.'/tmp/'.$filename);
			if (version_compare(PHP_VERSION, '7.2.0', '<'))
			{
				include(JPATH_ROOT."/components/com_osservicesbooking/helpers/csv/FileReader.php");
				include(JPATH_ROOT."/components/com_osservicesbooking/helpers/csv/CSVReader.php");
				$reader = new CSVReader( new FileReader(JPATH_ROOT.'/tmp/'.$filename));
				$reader->setSeparator( $configClass['csv_separator'] );
				$rs = 0;
				$j = 0;
				while( false != ( $cell = $reader->next() ) )
				{
					if($rs > 0)
					{
						$date_in_week		= array();
						$start_hour			= (int)$cell[0];
						$start_minute		= (int)$cell[1];
						$end_hour			= (int)$cell[2];
						$end_minute			= (int)$cell[3];
						$available_seats	= (int)$cell[4];
						$date_in_week[0]	= (int)$cell[5];
						$date_in_week[1]	= (int)$cell[6];
						$date_in_week[2]	= (int)$cell[7];
						$date_in_week[3]	= (int)$cell[8];
						$date_in_week[4]	= (int)$cell[9];
						$date_in_week[5]	= (int)$cell[10];
						$date_in_week[6]	= (int)$cell[11];
						//$date_in_week[0]	= $mon;

						$db->setQuery("Select count(id) from #__app_sch_custom_time_slots where sid = '$sid' and start_hour = '$start_hour' and start_min = '$start_minute' and end_hour = '$end_hour' and end_min = '$end_minute'");
						$count = $db->loadResult();
						if($count > 0)
						{
							$db->setQuery("Select id from #__app_sch_custom_time_slots where sid = '$sid' and start_hour = '$start_hour' and start_min = '$start_minute' and end_hour = '$end_hour' and end_min = '$end_minute'");
							$time_slot_id = $db->loadResult();
							$db->setQuery("Update #__app_sch_custom_time_slots set nslots = '$available_seats'");
							$db->execute();
							$db->setQuery("Delete from #__app_sch_custom_time_slots_relation where time_slot_id ='$time_slot_id'");
							$db->execute();
						}
						else
						{
							$db->setQuery("Insert into  #__app_sch_custom_time_slots (id,sid,start_hour,start_min,end_hour,end_min,nslots) values (NULL,'$sid','$start_hour','$start_minute','$end_hour','$end_minute','$available_seats')");
							$db->execute();
							$time_slot_id = $db->insertID();
						}

						$i = 0;
						foreach ($date_in_week as $date)
						{
							$i++;
							if($date == 1)
							{
								$db->setQuery("Insert into #__app_sch_custom_time_slots_relation (id,time_slot_id,date_in_week) values (NULL,'$time_slot_id','$i')");
								$db->execute();
							}
						}
					}
					$rs++;
				}
			}
			else  //for php8
			{
				require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/csv8/vendor/autoload.php';
				
				
				$reader = ReaderEntityFactory::createReaderFromFile($filename);

				if ($reader instanceof Box\Spout\Reader\CSV\Reader)
				{
					$reader->setFieldDelimiter($configClass['csv_separator']);
				}

				$reader->open(JPATH_ROOT.'/tmp/'.$filename);
				$headers = [];
				$rows    = [];
				$count   = 0;
				foreach ($reader->getSheetIterator() as $sheet)
				{
					foreach ($sheet->getRowIterator() as $row)
					{
						$cells = $row->getCells();

						if ($count === 0)
						{
							//do nothing
							$count++;
						}
						else
						{
							$date_in_week		= array();
							$start_hour			= (int)$cells[0]->getValue();
							$start_minute		= (int)$cells[1]->getValue();
							$end_hour			= (int)$cells[2]->getValue();
							$end_minute			= (int)$cells[3]->getValue();
							$available_seats	= (int)$cells[4]->getValue();
							$date_in_week[0]	= (int)$cells[5]->getValue();
							$date_in_week[1]	= (int)$cells[6]->getValue();
							$date_in_week[2]	= (int)$cells[7]->getValue();
							$date_in_week[3]	= (int)$cells[8]->getValue();
							$date_in_week[4]	= (int)$cells[9]->getValue();
							$date_in_week[5]	= (int)$cells[10]->getValue();
							$date_in_week[6]	= (int)$cells[11]->getValue();
							//$date_in_week[0]	= $mon;

							$db->setQuery("Select count(id) from #__app_sch_custom_time_slots where sid = '$sid' and start_hour = '$start_hour' and start_min = '$start_minute' and end_hour = '$end_hour' and end_min = '$end_minute'");
							$count1 = $db->loadResult();
							if($count1 > 0)
							{
								$db->setQuery("Select id from #__app_sch_custom_time_slots where sid = '$sid' and start_hour = '$start_hour' and start_min = '$start_minute' and end_hour = '$end_hour' and end_min = '$end_minute'");
								$time_slot_id = $db->loadResult();
								$db->setQuery("Update #__app_sch_custom_time_slots set nslots = '$available_seats'");
								$db->execute();
								$db->setQuery("Delete from #__app_sch_custom_time_slots_relation where time_slot_id ='$time_slot_id'");
								$db->execute();
							}
							else
							{
								$db->setQuery("Insert into  #__app_sch_custom_time_slots (id,sid,start_hour,start_min,end_hour,end_min,nslots) values (NULL,'$sid','$start_hour','$start_minute','$end_hour','$end_minute','$available_seats')");
								$db->execute();
								$time_slot_id = $db->insertID();
							}

							$i = 0;
							foreach ($date_in_week as $date)
							{
								$i++;
								if($date == 1)
								{
									$db->setQuery("Insert into #__app_sch_custom_time_slots_relation (id,time_slot_id,date_in_week) values (NULL,'$time_slot_id','$i')");
									$db->execute();
								}
							}
						}
					}
				}
				$reader->close();	
			}
		}
		$mainframe->enqueueMessage("Custom Timeslots have been imported");
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=service_managetimeslots&sid=".$sid);
	}

	static function getAssignedEmployees($sid)
	{
		$db = JFactory::getDbo();
		$employees = [];
		$query = "Select a.employee_name from #__app_sch_employee as a inner join #__app_sch_employee_service as b on b.employee_id = a.id where b.service_id = '$sid'";
		$db->setQuery($query);
		$employees = $db->loadColumn(0);
		return $employees;
	}

	static function specialrates()
	{
		global $mainframe, $jinput;
		$db							= JFactory::getDbo();
		$filter_order 				= $mainframe->getUserStateFromRequest($option.'.specialrates.filter_order','filter_order','id','string');
        $filter_order_Dir 			= $mainframe->getUserStateFromRequest($option.'.specialrates.filter_order_Dir','filter_order_Dir','desc','string');
        $lists['order'] 			= $filter_order;
        $lists['order_Dir'] 		= $filter_order_Dir;
        $order_by 					= " ORDER BY $filter_order $filter_order_Dir";
		
		// Get the pagination request variables
        $limit						= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart					= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$count = "Select count(id) from #__app_sch_special_prices";
		$db->setQuery($count);
        $total 						= $db->loadResult();
        jimport('joomla.html.pagination');
        $pageNav 					= new JPagination($total,$limitstart,$limit);

		$query = "Select * from #__app_sch_special_prices";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
        $rows 						= $db->loadObjectList();
		HTML_OSappscheduleService::listSpecialPrice($rows, $pageNav, $lists);
	}

	static function modifyRate($id)
	{
		global $mainframe,$mapClass;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Rate','OsAppTable');
		if($id > 0)
		{
			$row->load((int)$id);
		}
		else
		{
			$row->published = 1;
		}
		
		$lists['published'] = JHtml::_('select.booleanlist','published','class="'.$mapClass['input-medium'].' form-select"',$row->published);
			
		// create service
		$db->setQuery("Select id as value, service_name as text from #__app_sch_services");
		$services = $db->loadObjectList();
		
		$sid = [];
		if($row->id > 0)
		{
			$db->setQuery("Select sid from #__app_sch_special_price_services where price_id = '$row->id'");
			$sid = $db->loadColumn(0);
		}

		$lists['services'] = JHtml::_('select.genericlist', $services, 'services[]' , 'class="chosen input-large ilarge form-control" multiple','value','text', $sid);
		
		$lists['hours'] = OSappscheduleEmployee::generateHours();

		$optionArr = array();
		$optionArr[] = JHtml::_('select.option','1',JText::_('OS_MON'));
		$optionArr[] = JHtml::_('select.option','2',JText::_('OS_TUE'));
		$optionArr[] = JHtml::_('select.option','3',JText::_('OS_WED'));
		$optionArr[] = JHtml::_('select.option','4',JText::_('OS_THU'));
		$optionArr[] = JHtml::_('select.option','5',JText::_('OS_FRI'));
		$optionArr[] = JHtml::_('select.option','6',JText::_('OS_SAT'));
		$optionArr[] = JHtml::_('select.option','7',JText::_('OS_SUN'));
		$weekday = [];
		if($row->id > 0)
		{
			$db->setQuery("Select weekday from #__app_sch_special_price_weekdays where price_id = '$row->id'");
			$weekday = $db->loadColumn(0);
		}

		$lists['weekday'] = JHtml::_('select.genericlist', $optionArr, 'weekday[]' , 'class="chosen input-large ilarge form-control" multiple','value','text', $weekday);

		$optionArr = array();
		$optionArr[] = JHtml::_('select.option','0','-');
		$optionArr[] = JHtml::_('select.option','1','+');
		$lists['cost_type'] = JHtml::_('select.genericlist', $optionArr, 'cost_type', 'class="'.$mapClass['input-mini'].' imini form-select" style="display:inline;"','value','text', $row->cost_type);
		
		HTML_OSappscheduleService::modifyRate($row,$lists,$services);
	}

	static function saveRate($save)
	{
		global $mainframe,$configClass,$jinput;
		$db		= JFactory::getDbo();
		$id		= $jinput->getInt('id',0);
		
		$post	= $jinput->post->getArray();
		$row	= &JTable::getInstance('Rate','OsAppTable');
		$row->bind($post);
		
		$row->check();
		

	 	if (!$row->store())
		{
		 	throw new Exception ($row->getError());	 			 	
		}

		if($id == 0)
		{
			$id = $db->insertID();
		}

		$services = $jinput->get('services', array(), 'array');
		if(count($services))
		{
			$db->setQuery("Delete from #__app_sch_special_price_services where price_id = '$id'");
			$db->execute();
			foreach($services as $sid)
			{
				$db->setQuery("Insert into #__app_sch_special_price_services (id, price_id, sid) values (NULL,'$id', '$sid')");
				$db->execute();
			}
		}

		$weekday = $jinput->get('weekday', array(), 'array');
		if(count($weekday))
		{
			$db->setQuery("Delete from #__app_sch_special_price_weekdays where price_id = '$id'");
			$db->execute();
			foreach($weekday as $day)
			{
				$db->setQuery("Insert into #__app_sch_special_price_weekdays (id, price_id, weekday) values (NULL,'$id', '$day')");
				$db->execute();
			}
		}

		$msg	= JText::_('OS_ITEM_HAS_BEEN_SAVED'); 
		$mainframe->enqueueMessage($msg);
		if($save == 0)
		{
			$mainframe->redirect('index.php?option=com_osservicesbooking&task=service_modifyrate&cid[]='.$id);
		}
		else
		{
			$mainframe->redirect('index.php?option=com_osservicesbooking&task=service_specialrates');
		}
	}

	static function removeRates($cid)
	{
		global $mainframe;
		$db = JFactory::getDbo();
		if(count($cid))
		{
			$db->setQuery("Delete from #__app_sch_special_prices where id in (".implode(',', $cid).")");
			$db->execute();
			$msg	= JText::_('OS_ITEMS_HAVE_BEEN_REMOVED'); 
			$mainframe->enqueueMessage($msg);
		}
		$mainframe->redirect('index.php?option=com_osservicesbooking&task=service_specialrates');
	}

	static function generateEmployeeServiceForm($id)
	{
		global $mainframe, $mapClass;
		$db = JFactory::getDbo();
		$id = (int) $id;
		$lists = [];
		$lists['employees'] = [];
		if($id > 0)
		{
			$db->setQuery("Select a.id, a.employee_name, c.venue_name, b.* from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id left join #__app_sch_venues as c on c.id = b.vid where b.service_id = '$id'");
			$lists['employees'] = $db->loadObjectList();
		}

		$db->setQuery("Select id as value, employee_name as text from #__app_sch_employee where published = '1' and id not in (Select employee_id from #__app_sch_employee_service where service_id = '$id')");
		$employees		= $db->loadObjectList();
		$employeeArr	= [];
		$employeeArr[]	= JHtml::_('select.option', '', JText::_('OS_SELECT_EMPLOYEE'));
		$employeeArr	= array_merge($employeeArr, $employees);
		$lists['employeelist'] = JHtml::_('select.genericlist', $employeeArr, 'employee_id', 'class="ilarge form-select input-large"', 'value','text');
		$lists['employeeAvail'] = $employees;
		$lists['venues'] = [];
		if(OSBHelper::isAvailableVenue())
		{
			$db->setQuery("Select id as value, concat(venue_name, ' - ' , address) as text from #__app_sch_venues where published = '1' and id in (Select vid from #__app_sch_venue_services where sid = '$id') order by address");
			$venues = $db->loadObjectList();
			$venue  = "";
			if(count($venues) > 0)
			{
				$venueArr 	 = array();
				$venueArr[]  = JHTML::_('select.option','',JText::_('OS_SELECT_VENUE'));
				$venueArr    = array_merge($venueArr,$venues);
				$lists['venuelist'] = JHTML::_('select.genericlist',$venueArr,'venue_id','class="input-large form-select ilarge"','value','text');
				$lists['venues'] = $venues;
			}
		}

		$lists['sid'] = $id;

		HTML_OSappscheduleService::generateEmployeeServiceForm($lists);
	}

	static function updateWorkingStatus()
	{
		global $mainframe,$configClass,$jinput, $mapClass;
		$db		= JFactory::getDbo();
		$sid	= $jinput->getInt('sid', 0);
		$eid	= $jinput->getInt('eid', 0);
		$date   = $jinput->getString('date','');
		$status = $jinput->getInt('status', 0);

		$db->setQuery("Update #__app_sch_employee_service set `".$date."` = '$status' where employee_id = '$eid' and service_id = '$sid'");
		$db->execute();

		OSappscheduleService::generateEmployeeServiceForm($sid);
		$mainframe->close();
	}

	static function removeWorking()
	{
		global $mainframe,$configClass,$jinput, $mapClass;
		$db		= JFactory::getDbo();
		$sid	= $jinput->getInt('sid', 0);
		$id		= $jinput->getInt('id', 0);
		$db->setQuery("Delete from #__app_sch_employee_service where id = '$id'");
		$db->execute();

		OSappscheduleService::generateEmployeeServiceForm($sid);
		$mainframe->close();
	}

	static function saveWorking()
	{
		global $mainframe,$configClass,$jinput, $mapClass;
		$db		= JFactory::getDbo();
		$sid	= $jinput->getInt('sid', 0);
		$eid	= $jinput->getInt('employee_id', 0);
		$vid    = $jinput->getInt('vid', 0);
		$weekDate = ['mo','tu','we','th','fr','sa','su'];
		$fieldSql = "(id, employee_id, service_id, vid, additional_price";
		foreach($weekDate as $date)
		{
			$fieldSql .= ",`".$date."`";
		}
		$fieldSql .= ")";

		$fieldValueSql = "(NULL, $eid, $sid, $vid, 0";
		foreach($weekDate as $date)
		{
			$dateValue = $jinput->getInt($date, 0);
			$fieldValueSql .= ",".$dateValue;
		}
		$fieldValueSql .= ")";
		$query = "Insert into #__app_sch_employee_service $fieldSql values $fieldValueSql";
		$db->setQuery($query);
		$db->execute();

		OSappscheduleService::generateEmployeeServiceForm($sid);
		$mainframe->close();

	}
}
?>