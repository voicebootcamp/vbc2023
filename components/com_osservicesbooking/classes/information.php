<?php
/*------------------------------------------------------------------------
# information.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class OSappscheduleInformation{
	/**
	 * Default function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
        $cid       = $jinput->get('cid',array(),'ARRAY');
        \Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		switch ($task){
			
		}
	}
	
	/**
	 * Show error header 
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 * @param unknown_type $date
	 */
	static function showError($sid,$eid,$errorArr,$vid = 0)
    {
		global $mainframe,$configClass;
		$db                     = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service                = $db->loadObject();
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee               = $db->loadObject();
		$errorArr				= (array) $errorArr;
		$inforArr               = array();
		for($i=0;$i<count($errorArr);$i++)
		{
			$row                = $errorArr[$i];
			$date               = strtotime($row->booking_date); //return int
			$dateArr            = explode("-",$row->booking_date);
			$date               = date($configClass['date_format'],$date); //return date informat
			
			$temp_start_hour 	=  intval(date("H",$row->start_time));
			$temp_start_min 	=  intval(date("i",$row->start_time));
			$temp_end_hour 		=  intval(date("H",$row->end_time));
			$temp_end_min 		=  intval(date("i",$row->end_time));
			$optionArr   		=  array();
			$optionArr[] 		=  JHTML::_('select.option',0,JText::_('OS_SELECT_DIFFERENT_SLOTS'));

			for($j=1;$j<=$row->number_slots_available;$j++)
			{
				$optionArr[] 	=  JHTML::_('select.option',$j,$j);
			}
			$lists['optionArr'] =  JHTML::_('select.genericlist',$optionArr,'nslots_'.$sid.'_'.$eid.'_'.intval($dateArr[2]).'_'.intval($dateArr[1]).'_'.intval($dateArr[0]),'onChange="javascript:updateTempDate('.$sid.','.$eid.','.$row->start_time.','.$row->end_time.','.intval($dateArr[2]).','.intval($dateArr[1]).','.intval($dateArr[0]).')" style="width:180px;" class="inputbox"','value','text');
			
			$tmp									= new \stdClass();
			$tmp->id						= $row->id;
			$tmp->date 						= $date;
			$tmp->temp_start_hour 			= $temp_start_hour;
			$tmp->temp_start_min  			= $temp_start_min ;
			$tmp->temp_end_hour 			= $temp_end_hour;
			$tmp->temp_end_min 				= $temp_end_min;
			$tmp->list						= $lists['optionArr'];
			$tmp->nslots  		 			= $row->nslots;
			$tmp->number_slots_available 	= $row->number_slots_available;
			$tmp->start_time				= $row->start_time;
			$tmp->end_time					= $row->end_time;
			$tmp->return 					= $row->return;
			$inforArr[$i]					= $tmp;
		}
		HTML_OSappscheduleInformation::showErrorHtml($service,$employee,$inforArr,$vid,$dateArr);
	}
}
?>