<?php
/*------------------------------------------------------------------------
# coupon.php - Ossolution emailss Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;


class OSappscheduleCoupon{
	static function display($option,$task){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
        $cid       = $jinput->get('cid',array(),'ARRAY');
        \Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		switch ($task){
			default:
			case "coupon_list":
				OSappscheduleCoupon::coupon_list($option);
			break;
			case "coupon_unpublish":
				OSappscheduleCoupon::coupon_state($option,$cid,0);
			break;
			case "coupon_publish":
				OSappscheduleCoupon::coupon_state($option,$cid,1);
			break;	
			case "coupon_remove":
				OSappscheduleCoupon::coupon_remove($option,$cid);
			break;
			case "coupon_add":
				OSappscheduleCoupon::coupon_modify($option,0);
			break;	
			case "coupon_edit":
				OSappscheduleCoupon::coupon_modify($option,$cid[0]);
			break;
			case "coupon_apply":
				OSappscheduleCoupon::coupon_save($option,0);
			break;
			case "coupon_save":
				OSappscheduleCoupon::coupon_save($option,1);
			break;
			case "goto_index":
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php");
			break;
		}
	}
	
	/**
	 * List coupons
	 *
	 * @param unknown_type $option
	 */
	static function coupon_list($option){
		global $mainframe, $configClass, $jinput;
		$db				= JFactory::getDbo();
		$limit			= $jinput->getInt('limit',20);
		$limitstart		= $jinput->getInt('limitstart',0);
		$keyword		= $db->escape(trim($jinput->get('keyword','','string')));
		$query			= "Select count(id) from #__app_sch_coupons where 1=1 ";
		if($keyword != ""){
			$query		.= " and coupon_name like '%".$keyword."%'";
		}
		$db->setQuery($query);
		$count = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($count,$limitstart,$limit);
		$query = "Select * from #__app_sch_coupons where 1=1 ";
		if($keyword != ""){
			$query .= " and coupon_name like '%".$keyword."%'";
		}
		$db->setQuery($query, $pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		if(count($rows)){
			foreach($rows as $row){
				$db->setQuery("Select count(id) from #__app_sch_orders where order_status = 'S' and coupon_id = '$row->id'");
				$count = $db->loadResult();
				$row->nuse = (int) $count;
			}
		}
		HTML_OsAppscheduleCoupon::listCoupons($option,$rows,$pageNav,$keyword);
	}
	
	/**
	 * Add/edit coupon
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function coupon_modify($option,$id){
		global $mainframe;
		//JHTML::_('behavior.tooltip');
		OSBHelper::loadTooltip();
		$db = JFactory::getDbo();
		$row = &JTable::getInstance('Coupon','OsAppTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
			$row->access = 1;
		}
		// creat published
		$lists['published'] = JHtml::_('select.booleanlist','published','class="inputbox"',$row->published);

		$optionArr = array();
		$optionArr[] = JHtml::_('select.option','0',JText::_('OS_COUPON_DISCOUNT'));
		$optionArr[] = JHtml::_('select.option','1',JText::_('OS_DISCOUNT_BY_GROUP'));
		$lists['discount_by'] = JHtml::_('select.genericlist',$optionArr,'discount_by','onChange="javascript:updateCouponForm()" class="input-medium form-select imedium"','value','text',$row->discount_by);
		
		$discountType = array();
		$discountType[] = JHTML::_('select.option','0',JText::_('OS_PERCENT'));
		$discountType[] = JHTML::_('select.option','1',JText::_('OS_FIXED'));
		$lists['discount_type'] = JHTML::_('select.genericlist',$discountType,'discount_type','class="input-small form-select ishort"','value','text',$row->discount_type);
		$lists['access'] = OSBHelper::accessDropdown('access',$row->access,'class="input-large form-select imedium"');
		HTML_OsAppscheduleCoupon::editCoupon($option,$row,$lists);
	}
	
	/**
	 * Coupon saving
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	static function coupon_save($option,$save){
		global $mainframe,$configClass,$jinput;
		$db						= JFactory::getDbo();
		$post					= $jinput->post->getArray();
		$row					= &JTable::getInstance('Coupon','OsAppTable');
		$row->bind($post);
		$row->expiry_date		= $jinput->getString('expiry_date',$db->getNullDate());
		$row->minimum_cost		= (float) $row->minimum_cost;
		$msg					= JText::_('OS_ITEM_HAS_BEEN_SAVED'); 
	 	if (!$row->store())
		{
		 	throw new Exception($row->getError(), 500);		 	
		}
		$id						= $jinput->getInt('id',0);
		if($id == 0)
		{
			$id = $db->insertid();
		}
		if($save == 1)
		{
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=coupon_list");
		}
		else
		{
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=coupon_edit&cid[]=$row->id");
		}
	}
	
	/**
	 * publish or unpublish agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function coupon_state($option,$cid,$state){
		global $mainframe;
		$db 		= JFactory::getDBO();
		if(count($cid)>0)	{
			$cids 	= implode(",",$cid);
			$db->setQuery("UPDATE #__app_sch_coupons SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
		OsAppscheduleCoupon::coupon_list($option);
	}
	
	/**
	 * remove agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function coupon_remove($option,$cid){
		global $mainframe;
		$db = JFactory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__app_sch_coupons WHERE id IN ($cids)");
			$db->execute();
			
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OsAppscheduleCoupon::coupon_list($option);
	}
}
?>