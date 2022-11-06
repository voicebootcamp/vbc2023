<?php
/*------------------------------------------------------------------------
# venue.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2019 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class OSappscheduleVenue{
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
		switch ($task){
			default:
			case "venue_list":
				OSappscheduleVenue::venue_list($option);
			break;
			case "venue_add":
				OSappscheduleVenue::editVenue($option,0);
			break;
			case "venue_edit":
				OSappscheduleVenue::editVenue($option,$cid[0]);
			break;
			case "venue_save":
				OSappscheduleVenue::saveVenue($option,1);
			break;
			case "venue_apply":
				OSappscheduleVenue::saveVenue($option,0);
			break;
			case "venue_cancel":
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=venue_list");
			break;
			case "venue_unpublish":
				OSappscheduleVenue::venue_state($option,$cid,0);
			break;
			case "venue_publish":
				OSappscheduleVenue::venue_state($option,$cid,1);
			break;	
			case "venue_remove":
				OSappscheduleVenue::venue_remove($option,$cid);
			break;
		}
	}
	
	/**
	 * Edit Venue
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function editVenue($option,$id){
		global $mainframe,$configClass,$languages;
		OSBHelper::loadTooltip();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Venue','OsAppTable');
		if($id > 0)
		{
			$row->load((int)$id);
		}
		else
		{
			$row->published = 1;
		}
		
		// creat published
		$lists['published'] = JHtml::_('select.booleanlist','published','class="inputbox"',$row->published);
		
		$db->setQuery("Select country_name as value, country_name as text from #__app_sch_countries order by country_name");
		$countries = $db->loadObjectList();
		$countryArr[] = JHTML::_('select.option','',JText::_('OS_SELECT_COUNTRY'));
		$countryArr   =  array_merge($countryArr,$countries);
		$lists['country'] = JHTML::_('select.genericlist',$countryArr,'country','class="input-large form-select ilarge"  ','value','text',$row->country);
		
		$db->setQuery("Select id as value, service_name as text from #__app_sch_services where published = '1' order by service_name");
		$services = $db->loadObjectList();
		
		$db->setQuery("Select sid from #__app_sch_venue_services where vid = '$row->id'");
		$sids = $db->loadObjectList();
		$serviceArr = array();
		if(count($sids) > 0){
			for($j=0;$j<count($sids);$j++){
				$serviceArr[] = $sids[$j]->sid;
			}
		}
		$lists['service'] = JHTML::_('select.genericlist',$services,'sid[]','multiple style="height:150px;"','value','text',$serviceArr);

		$lists['services'] = $services;

		$lists['serviceArr'] = $serviceArr;

        $hourArr = array();
        $hourArr[] = JHtml::_('select.option','0','0');
        for($i=1;$i<24;$i++){
            $hourArr[] = JHtml::_('select.option',$i,$i);
        }
        $lists['hour'] = JHtml::_('select.genericlist',$hourArr,'opening_hour','class="input-mini form-select"','value','text',$row->opening_hour);

        $minuteArr = array();
        $minuteArr[] = JHtml::_('select.option','0','0');
        for($i=1;$i<60;$i++){
            $minuteArr[] = JHtml::_('select.option',$i,$i);
        }
        $lists['minute'] = JHtml::_('select.genericlist',$minuteArr,'opening_minute','class="input-mini form-select"','value','text',$row->opening_minute);

		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		HTML_OSappscheduleVenue::editVenueHtml($option,$row,$lists,$translatable);
	}
	
	/**
	 * Save venue
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	static function saveVenue($option,$save)
	{
		global $mainframe,$configClass,$languages,$jinput;
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Venue','OsAppTable');
		$id = $jinput->getInt('id',0);
		
		$remove_image = $jinput->getInt('remove_image',0);
		//$row->image = "";
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$photo_name = time()."_".str_replace(" ","_",$_FILES['image']['name']);
			move_uploaded_file($_FILES['image']['tmp_name'],JPATH_ROOT."/images/osservicesbooking/venue/".$photo_name);
			$row->image = $photo_name;
		}
		elseif($remove_image == 1)
		{
			$row->image = "";
		}
		elseif($id == 0)
		{
			$row->image = "";
		}
		
		$post = $jinput->post->getArray();
		$row->bind($post);
		$row->number_date_before = (int) $row->number_date_before;
		$row->number_date_after = (int) $row->number_date_after;
		$row->number_hour_before = (int) $row->number_hour_before;
		if($row->disable_date_before == "")
		{
			$row->disable_date_before = "0000-00-00";
		}
		if($row->disable_date_after == "")
		{
			$row->disable_date_after = "0000-00-00";
		}
		
		if (!$row->store()){
		 	$msg = JText::_('OS_ERROR_SAVING')." - ".$row->getError();  			 	
		 	$mainframe->enqueueMessage($msg,'message');
		}
		if($id == 0){
			$id = $db->insertID();
		}
		
		$lat_add  = $jinput->get('lat_add','','string');
		$long_add = $jinput->get('long_add','','string');
		if(($lat_add == "") and ($long_add == "")){
			$addressArr = array();
			$addressArr[] = $row->address;
			if($row->city != ""){
				$addressArr[] = $row->city;
			}
			if($row->state != ""){
				$addressArr[] = $row->state;
			}
			if($row->country != ""){
				$addressArr[] = $row->country;
			}
			$address = implode(" ",$addressArr);
			$return = OSBHelper::findAddress($address);
			if($return[2] == "OK"){
				$lat_add = $return[0];
				$long_add = $return[1];
			}
			$db->setQuery("Update #__app_sch_venues set lat_add = '$lat_add',long_add='$long_add' where id = '$id'");
			$db->execute();
		}
		
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language){	
				$sef = $language->sef;
				$address_language = $jinput->get('address_'.$sef,'','string');
				if($address_language == ""){
					$address_language = $row->address;
				}
				if($address_language != ""){
					$venue = &JTable::getInstance('Venue','OsAppTable');
					$venue->id = $id;
					$venue->{'address_'.$sef} = $address_language;
					$venue->store();
				}
				
				$city_language = $jinput->get('city_'.$sef,'','string');
				if($city_language == ""){
					$city_language = $row->city;
				}
				if($city_language != ""){
					$venue = &JTable::getInstance('Venue','OsAppTable');
					$venue->id = $id;
					$venue->{'city_'.$sef} = $city_language;
					$venue->store();
				}
				
				$state_language = $jinput->get('state_'.$sef,'','string');
				if($state_language == ""){
					$state_language = $row->state;
				}
				if($state_language != ""){
					$venue = &JTable::getInstance('Venue','OsAppTable');
					$venue->id = $id;
					$venue->{'state_'.$sef} = $state_language;
					$venue->store();
				}
				
			}
		}
		
		
		//update into #__app_sch_venue_services
		$sid = $jinput->get('sid',array(),'ARRAY');
		$db->setQuery("Delete from #__app_sch_venue_services where vid = '$id'");
		$db->execute();
		if(count($sid) > 0){
			for($j=0;$j<count($sid);$j++){
				$service_id = $sid[$j];
				$db->setQuery("Insert into #__app_sch_venue_services (id,vid,sid) values (NULL,'$id','$service_id')");
				$db->execute();
			}
		}
	
		
		if($save==1)
		{
			$mainframe->enqueueMessage(JText::_('OS_ITEM_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=venue_list");
		}
		else
		{
			$mainframe->enqueueMessage(JText::_('OS_ITEM_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=venue_edit&cid[]=".$id);
		}
	}
	
	/**
	 * Venue list
	 *
	 * @param unknown_type $option
	 */
	static function venue_list($option){
		global $mainframe,$configClass,$jinput;
		$db 				= JFactory::getDbo();
		$limit 				= $jinput->getInt('limit',20);
		$limitstart 		= $jinput->getInt('limitstart',0);
		$filter_order 		= $jinput->get('filter_order','a.address','string');
		$filter_order_Dir 	= $jinput->get('filter_order_Dir','asc','string');
		$keyword 	        = $db->escape(trim($jinput->get('keyword','','string')));
		$query 		= "Select count(id) from #__app_sch_venues where 1=1";
		if($keyword != ""){
			$query .= " and (address like '%$keyword%' or city like '%$keyword%' or state like '%$keyword%' or country like  '%$keyword%' or contact_email like '%$keyword%' or contact_name like '%$keyword%' or contact_phone like '%$keyword%')";
		}
		$db->setQuery($query);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$query 		= "Select a.* from #__app_sch_venues as a where 1=1 ";
		if($keyword != ""){
			$query .= " and (a.address like '%$keyword%' or a.city like '%$keyword%' or a.state like '%$keyword%' or a.country like  '%$keyword%' or a.contact_email like '%$keyword%' or a.contact_name like '%$keyword%' or a.contact_phone like '%$keyword%')";
		}
		$query .= " order by $filter_order $filter_order_Dir";
		$pageNav = new JPagination($total,$limitstart,$limit);
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		$lists['keyword'] 			= $keyword;
		$lists['order'] 			= $filter_order;
		$lists['order_Dir'] 		= $filter_order_Dir;
		HTML_OSappscheduleVenue::listVenues($option,$pageNav,$rows,$lists);
	}
	
	/**
	 * publish or unpublish agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function venue_state($option,$cid,$state){
		global $mainframe;
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();
		if(count($cid)>0)	{
			$cids 	= implode(",",$cid);
			$db->setQuery("UPDATE #__app_sch_venues SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
		OSappscheduleVenue::venue_list($option);
	}
	
	/**
	 * remove agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function venue_remove($option,$cid){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__app_sch_venues WHERE id IN ($cids)");
			$db->execute();
			
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OSappscheduleVenue::venue_list($option);
	}
}
?>