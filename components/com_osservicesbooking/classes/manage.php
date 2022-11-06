<?php
/*------------------------------------------------------------------------
# manage.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class OSappscheduleManage{
	/**
	 * Osproperty default
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
    {
		switch ($task)
        {
			case "manage_orders":
                OSappscheduleManage::manageAllOrders();
			break;
            case "manage_editorder":
                OSappscheduleManage::editorder();
            break;
            case "manage_saveorder":
                OSappscheduleManage::saveOrder(1);
            break;
            case "manage_applyorder":
                OSappscheduleManage::saveOrder(0);
            break;
            case "manage_addservice":
                OSappscheduleManage::addOrderItems(0);
            break;
            case "manage_editorderitem":
                OSappscheduleManage::addOrderItems(1);
            break;
            case "manage_saveorderitem":
                OSappscheduleManage::saveOrderItem();
            break;
			case "manage_removeorders":
				OSappscheduleManage::removeOrders();
			break;
            case "manage_removeservice":
                OSappscheduleManage::removeService();
            break;
            case "manage_exportinvoice":
                OSappscheduleManage::exportInvoice();
            break;
            case "manage_exportSelectedOrders":
                OSappscheduleManage::manage_exportSelectedOrders();
            break;
            case "manage_exportCsv":
                OSappscheduleManage::manage_exportCsv();
            break;
			case "manage_userinfo":
				OSappscheduleManage::userInfo();
			break;
			case "manage_users":
				OSappscheduleManage::manageUsers();
			break;
		}
	}

	static function userInfo()
	{
		global $mainframe, $configClass, $jinput;
        $userId         = $jinput->getInt('userId',0);
        if($userId > 0)
        {
            $user       = JFactory::getUser($userId);
            $db         = JFactory::getDbo();
            $db->setQuery("Select * from #__app_sch_orders where user_id = '$userId' order by order_date desc");
            $orders = $db->loadObjectList();

            if(count($orders) > 0)
            {
                for($i=0;$i<count($orders);$i++)
                {
                    $order = $orders[$i];
                    $db->setQuery("Select * from #__app_sch_orders where id = '$order->id'");
                    $orderdetails = $db->loadObject();
                    $order_lang = $orderdetails->order_lang;
                    $suffix = "";
                    $lgs = OSBHelper::getLanguages();
                    $translatable = JLanguageMultilang::isEnabled() && count($lgs);
                    $default_language = OSBHelper::getDefaultLanguage();
                    if($order_lang == "")
                    {
                        $order_lang = $default_language;
                    }
                    if($translatable)
                    {
                        if($default_language != $order_lang)
                        {
                            $langugeArr = explode("-",$order_lang);
                            $suffix = "_".$langugeArr[0];
                        }
                    }
                    //get services information
                    $db->setQuery("SELECT a.id,a.service_name$suffix as service_name FROM #__app_sch_services AS a INNER JOIN #__app_sch_order_items AS b ON a.id = b.sid WHERE b.order_id = '$order->id'");
                    $rows = $db->loadObjectList();
                    $service = "";
                    if(count($rows) > 0)
                    {
                        for($j=0;$j<count($rows);$j++)
                        {
                            $row = $rows[$j];
                            $service .= $row->service_name.", ";
                        }
                        $service = substr($service,0,strlen($service)-2);
                    }

                    $db->setQuery("Select count(id) from #__app_sch_order_items where order_id = '$order->id'");
                    $order->countItems = (int) $db->loadResult();
                    $order->service = $service;
                }
            }

            $balance = 0;
            $db->setQuery("Select count(id) from #__app_sch_user_balance where user_id = '$user->id'");
            $count = $db->loadResult();
            if($count > 0){
                $db->setQuery("Select `amount` from #__app_sch_user_balance where user_id = '$user->id'");
                $balance = $db->loadResult();
            }
            HTML_OSappscheduleManage::showUserInfo($orders, $user, $balance);
        }
	}

    /**
     * List All orders
     *
     */
    static function manageAllOrders()
    {
        global $mainframe,$configClass,$jinput;
        $document = JFactory::getDocument();
        $menu = JFactory::getApplication()->getMenu()->getActive();
        $list_type = 0;
        if (is_object($menu))
        {
            $params = $menu->getParams();
            if($params->get('page_title') != "")
            {
                $document->setTitle($params->get('page_title'));
            }
            else
            {
                $document->setTitle($configClass['business_name'].' | '.JText::_('OS_LIST_ALL_ORDERS'));
            }
            $list_type = $params->get('list_type',0);
        }
        else
        {
            $document->setTitle($configClass['business_name'].' | '.JText::_('OS_LIST_ALL_ORDERS'));
        }
        $db = JFactory::getDbo();

        $limit = $jinput->getInt('limit',10);
        $limitstart = $jinput->getInt('limitstart',0);

        // filter state
        $condition                  = "";
        $filter_status 				= OSBHelper::getStringValue('filter_status','');
        $condition 				   .= ($filter_status != '')? " AND a.order_status = '$filter_status'":"";
        $keyword                    = OSBHelper::getStringValue('keyword','');
        $lists['filter_status'] 	= OSBHelper::buildOrderStaticDropdownList($filter_status,"onChange='javascript:submitFilterForm();'",JText::_('OS_SELECT_ORDER_STATUS'),'filter_status');
        $lists['order_status']		= array('P'=>'<span style="color:orange;">'.JText::_('OS_PENDING').'</span>', 'S'=>'<span style="color:green;">'.JText::_('OS_COMPLETE').'</span>', 'C'=>'<span style="color:red;">'.JText::_('OS_CANCEL').'</span>');
        //$service_filter				= $jinput->getInt('service_filter',0);
        //$employee_filter			= $jinput->getInt('employee_filter',0);

        $filter_date_from			= $jinput->getString('filter_date_from','');
        $lists['filter_date_from']	= $filter_date_from;
        if ($filter_date_from != '' ){
            $condition 				.= " AND b.booking_date >= '".$filter_date_from." 00:00:00'";
        }
        $filter_date_to				= $jinput->getString('filter_date_to','');
        $lists['filter_date_to']	= $filter_date_to;
        if ($filter_date_to != '' ){
            $condition 				.= " AND b.booking_date <= '".$filter_date_to." 00:00:00'";
        }

        if($keyword != "")
        {
            $condition             .= " and (a.order_name like '%$keyword%' or a.order_phone like '%$keyword%' or a.order_email like '%$keyword%')";
        }

		$filter_venue 				= $jinput->getInt('filter_venue',0);
		$filter_service 		    = $jinput->getInt('filter_service',0);
		$filter_employee 			= $jinput->getInt('filter_employee',0);

        if ($filter_service > 0|| $filter_employee > 0 || $filter_date_from != '' || $filter_date_to != '' || $filter_venue > 0)
        {
            $add_query 				= " INNER JOIN #__app_sch_order_items AS b ON a.id = b.order_id ";
            $condition 			   .= $filter_service? " AND b.sid = '$filter_service' ":'';
            $condition 			   .= $filter_employee? " AND b.eid = '$filter_employee' ":'';
			$condition             .= $filter_venue? " and b.sid in (Select sid from #__app_sch_venue_services where vid = '$filter_venue')":'';
        }

        // filter service
		$options 					= array();
        if($filter_venue)
        {
            $extraVenueSql          = " and a.id in (Select sid from #__app_sch_venue_services where vid = '$filter_venue')";
			$extraVenueSql1          = " and id in (Select sid from #__app_sch_venue_services where vid = '$filter_venue')";
        }
		if ($filter_employee)
		{
			$query 					= " SELECT a.id AS value, a.service_name AS text"
                                    ." FROM #__app_sch_services AS a"
									." INNER JOIN #__app_sch_employee_service AS b ON (a.id = b.service_id AND b.employee_id = '$filter_employee')"					
                                    ." WHERE  a.published = '1' "
                                    .$extraVenueSql
									." ORDER BY a.service_name, a.ordering";
		}
		else
		{
			$query 					= " SELECT `id` AS value, `service_name` AS text"
									." FROM #__app_sch_services"
                                    ." WHERE `published` = '1' "
                                    .$extraVenueSql1
									." ORDER BY service_name, ordering";
		}
		$db->setQuery($query);
		//echo $db->getQuery();die();
		$options = $db->loadObjectlist();
		array_unshift($options,JHtml::_('select.option',0,JText::_('OS_FILTER_SERVICE')));
		$lists['filter_service']	= JHtml::_('select.genericlist',$options,'filter_service','class="input-medium form-select" onchange="javascript:submitFilterForm();" ','value','text',$filter_service);
		// filter employee
		$options 					= array();
			
		if ($filter_service)
		{
			$query 					= " SELECT a.id AS value, a.employee_name AS text"
									." FROM #__app_sch_employee AS a"
								    ." INNER JOIN #__app_sch_employee_service AS b ON (a.id = b.employee_id AND b.service_id = '$filter_service')"
																// ." WHERE a.published = '1' "
								    ." ORDER BY a.employee_name, b.ordering"
								    ;
		}
		else
		{
			$query 					= " SELECT `id` AS value, `employee_name` AS text"
									 ." FROM #__app_sch_employee "
									 // ." WHERE `published` = 1 "
									 ." ORDER BY employee_name "
									 ;
		}
		$db->setQuery($query);
		$options                    = $db->loadObjectlist();
		array_unshift($options,JHtml::_('select.option',0,JText::_('OS_SELECT_EMPLOYEE')));
		$lists['filter_employee']	= JHtml::_('select.genericlist',$options,'filter_employee','class="input-medium form-select" onchange="javascript:submitFilterForm();" ','value','text',$filter_employee);

		if($filter_service)
        {
            $extraServiceSql        = " and id in (Select vid from #__app_sch_venue_services where sid = '$filter_service')";
        }

        $db->setQuery("Select id as value, concat(address,' ',city,' ',state) as text from #__app_sch_venues where published =  '1' $extraServiceSql order by address");
        $venues                     = $db->loadObjectList();
        if(count($venues) > 0)
        {
            $options                = array();
            $options[]              = JHtml::_('select.option',0,JText::_('OS_SELECT_VENUE'));
            $options                = array_merge($options,$venues);
            $lists['filter_venue']  = JHtml::_('select.genericlist', $options,'filter_venue','class="input-medium form-select" onchange="javascript:submitFilterForm();" ','value','text',$filter_venue);
        }

		// get data	
		$count 						= " SELECT count(distinct a.id) FROM #__app_sch_orders AS a" 
		."\n $add_query "
		."\n WHERE 1=1";
		$count 					   .= $condition;
		//$count					   .= " group by a.id";
		$db->setQuery($count);
		$total 						= $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav 					= new JPagination($total,$limitstart,$limit);
		$list  						= " SELECT distinct(a.id),a.* FROM #__app_sch_orders AS a"
		.$add_query
		."\n WHERE 1=1 ";
		$list 					   .= $condition;
		$list 					   .= " group by a.id order by a.id desc";
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		//echo $db->getQuery();
		$rows 						= $db->loadObjectList();

        HTML_OSappscheduleManage::listOrders($rows,$lists,$pageNav);
    }

    /**
     * Remove orders
     * @throws Exception
     */
	static function removeOrders(){
		global $mainframe,$configClass,$jinput;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$cid = $jinput->get('cid',array(),'array');
		$cid = \Joomla\Utilities\ArrayHelper::toInteger($cid);
		if(count($cid)>0)	
		{
			foreach ($cid as $id)
			{
				HelperOSappscheduleCommon::sendCancelledEmail($id);
				HelperOSappscheduleCommon::sendSMS('cancel',$id);
				HelperOSappscheduleCommon::sendEmail('customer_cancel_order',$id);
				HelperOSappscheduleCommon::sendEmployeeEmail('employee_order_cancelled_new',$id,0);
			}
			if($configClass['integrate_gcalendar'] == 1)
			{
				foreach ($cid as $id){
					OSBHelper::removeEventOnGCalendar($id);
				}
			}
			if($configClass['waiting_list'] == 1){
				foreach ($cid as $id){
					OSBHelper::sendWaitingNotification($id);
				}
			}
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__app_sch_orders WHERE id IN ($cids)");
			$db->execute();
			
			$db->setQuery("DELETE FROM #__app_sch_order_items WHERE order_id IN ($cids)");
			$db->execute();

			$msg = count($cid) . " " .JText::_("OS_ITEMS_HAS_BEEN_DELETED");
		}else{
            $msg = JText::_("OS_SELECT_ORDER_ITEMS_YOU_WANT_TO_REMOVE");
        }
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&view=manageallorders&Itemid='.$jinput->getInt('Itemid',0)));
	}

	static function editorder(){
        global $mainframe,$jinput;
        $option             = $jinput->getString('option','');
        $id                 = $jinput->getInt('id',0);
        $db                 = JFactory::getDbo();
        $config             = new JConfig();
        $offset             = $config->offset;
        date_default_timezone_set($offset);
		$document			= JFactory::getDocument();
		$document->addScript('//code.jquery.com/ui/1.8.24/jquery-ui.min.js');
		$document->addStyleSheet('//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.min.css');

        require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/order.php';
        $row 		        = &JTable::getInstance('Order','OsAppTable');

        $row->load((int)$id);

        $options = array();
        $options[]			= JHtml::_('select.option','P',JText::_('OS_PENDING'));
        $options[]			= JHtml::_('select.option','S',JText::_('OS_COMPLETE'));
        $options[]			= JHtml::_('select.option','C',JText::_('OS_CANCEL'));
        $row->order_status_select_list = OSBHelper::buildOrderStaticDropdownList($row->order_status,'','','order_status');
        // list detail
        // limit page
        $limit					= $mainframe->getUserStateFromRequest( 'order.global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart				= $mainframe->getUserStateFromRequest( $option.'.order.limitstart', 'limitstart', 0, 'int' );
        // get database
        $count = " SELECT count(a.id) FROM #__app_sch_order_items AS a "
            ."\n INNER JOIN #__app_sch_employee AS b ON a.eid = b.id"
            ."\n INNER JOIN #__app_sch_services AS c ON a.sid = c.id"
            ."\n WHERE a.order_id = '$row->id'";
        $db->setQuery($count);
        $total = $db->loadResult();
        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total,$limitstart,$limit);

        $list = " SELECT a.*, c.service_name, c.service_time_type, b.employee_name FROM #__app_sch_order_items AS a "
            ."\n INNER JOIN #__app_sch_employee AS b ON a.eid = b.id"
            ."\n INNER JOIN #__app_sch_services AS c ON a.sid = c.id"
            ."\n WHERE a.order_id = '$row->id' order by a.booking_date"
        ;

        $db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
        $rows = $db->loadObjectList();
        $db->setQuery("Select a.*,b.fvalue from #__app_sch_fields as a inner join #__app_sch_field_data as b on b.fid = a.id  where b.order_id = '$id'");
        $fields = $db->loadObjectList();

        $countryArr[] = JHTML::_('select.option','','');
        $db->setQuery("Select country_name as value, country_name as text from #__app_sch_countries order by country_name");
        $countries = $db->loadObjectList();
        $countryArr = array_merge($countryArr,$countries);
        $lists['country'] = JHTML::_('select.genericlist',$countryArr,'order_country','class="input-medium form-select"','value','text',$row->order_country);

        $db->setQuery("Select name as value, title as text from #__app_sch_plugins where published = '1'");
        $paymentplugins = $db->loadObjectList();
        $optionArr = array();
        $optionArr[] = JHTML::_('select.option','',JText::_('OS_SELECT_PAYMENT_METHOD'));
        $optionArr = array_merge($optionArr,$paymentplugins);
        $lists['payment'] = JHTML::_('select.genericlist',$optionArr,'order_payment','class="input-medium form-select"','value','text',$row->order_payment);

        if ($row->order_date != $db->getNullDate()){
            $selectedHour   = date('G', strtotime($row->order_date));
            $selectedMinute = date('i', strtotime($row->order_date));
        }else{
            $selectedHour   = date('G');
            $selectedMinute = date('i');
            $row->order_date= date("Y-m-d",time());
        }
        if((int) $id == 0){
            $config = new JConfig();
            $offset = $config->offset;
            date_default_timezone_set($offset);
            $selectedHour   = date('G');
            $selectedMinute = date('i');
            $row->order_date= date("Y-m-d",time());
        }
        $lists['order_date_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'order_date_hour', ' class="inputbox input-small form-select" ', $selectedHour);
        $lists['order_date_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'order_date_minute', ' class="inputbox input-small form-select" ', $selectedMinute, '%02d');

        HTML_OSappscheduleManage::orders_detail($option,$row,$rows,$pageNav,$fields,$lists);
    }

    /**
     * Save order details
     * @param $save
     * @throws Exception
     */
    static function saveOrder($save){
        global $mainframe,$jinput,$configClass;
        $db                 = JFactory::getDbo();
        $mainframe 			= JFactory::getApplication();
        $id 				= $jinput->getInt('id',0);
        $old_status         = $jinput->get('old_status','P','string');
        $order_status       = $jinput->get('order_status','P','string');
        $row				= &JTable::getInstance('Order','OsAppTable');
        $row->load((int) $id);
        $post				= $jinput->post->getArray();
        $row->bind($post);
        $row->order_notes	= $_POST['notes'];
        $row->order_status	= $jinput->get('order_status','P','string');
        $row->order_date	= $jinput->get('order_date','0000-00-00','string');
		$row->order_date	= str_replace(" 00:00:00", "", $row->order_date);
		$row->order_date	.= " ".$jinput->getInt('order_date_hour',0).":".$jinput->getInt('order_date_minute',0).":00";

		$row->order_total	= $jinput->getFloat('order_total',0);
		$row->order_tax		= $jinput->getFloat('order_tax',0);
		$row->order_final_cost = $jinput->getFloat('order_final_cost',0);
		$row->order_upfront = $jinput->getFloat('order_upfront',0);
		$row->payment_fee	= $jinput->getFloat('payment_fee',0);
		$row->order_discount	= $jinput->getFloat('order_discount',0);
		$row->order_lang	= (string) $row->order_lang;
		$row->order_card_number = (string) $row->order_card_number;
		$row->order_card_type = (string) $row->order_card_type;
		$row->bank_id = (string) $row->bank_id;
		$row->params = (string) $row->params;
		$row->order_card_expiry_month = (int) $row->order_card_expiry_month;
		$row->order_card_expiry_year = (int) $row->order_card_expiry_year;
		$row->order_card_holder = (string) $row->order_card_holder;
		$row->order_cvv_code = (string) $row->order_cvv_code;
        if(!$row->store())
		{
			throw new Exception ($row->getError());
		}

        if($id == 0)
		{
            $newOrder = true;
            $id = $db->insertID();
        }
		else
		{
            $newOrder = false;
        }
        //save extra fields
        $db->setQuery("Delete from #__app_sch_order_options where order_id = '$id'");
        $db->execute();
        $db->setQuery("Delete from #__app_sch_field_data where order_id = '$id'");
        $db->execute();
        $db->setQuery("Select * from #__app_sch_fields where published = '1' and field_area = '1'");
        $fields = $db->loadObjectList();
        if(count($fields) > 0){
            for($i=0;$i<count($fields);$i++){
                $field = $fields[$i];
                $field_id = $field->id;
                $field_type = $field->field_type;
                $field_name = "field_".$field_id;
                if($field_type == 0){
                    $field_value = $jinput->get($field_name,'','string');
                    if($field_value != ""){
                        $db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$field_value')");
                        $db->execute();
                    }
                }elseif($field_type == 3){
                    $photo_name = "field_".$field_id;
                    $old_photo_name = "old_field_".$field_id;
                    $old_photo_name = $jinput->get($old_photo_name,'','string');
                    $fvalue = "";
                    $field_data = "";
                    if(is_uploaded_file($_FILES[$photo_name]['tmp_name'])){
                        if(OSBHelper::checkIsPhotoFileUploaded($photo_name)){
                            $image_name = $_FILES[$photo_name]['name'];
                            $image_name = OSBHelper::processImageName($id.time().$image_name);
                            $original_image_link = JPATH_ROOT."/images/osservicesbooking/fields/".$image_name;
                            move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
                            $fvalue = $image_name;
                        }
                    }
                    $remove_picture = "remove_picture_".$field_id;
                    $remove_picture = $jinput->getInt($remove_picture,0);
                    if(($remove_picture == 1) && ($fvalue != "")){
                        $db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$fvalue')");
                        $db->execute();
                    }elseif($fvalue != ""){
                        $db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$fvalue')");
                        $db->execute();
                    }elseif(($remove_picture == 0) && ($old_photo_name != "")){
                        $db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$old_photo_name')");
                        $db->execute();
                    }
                }else{
                    $field_value = $jinput->get($field_name,'','string');
                    if($field_value != ""){
                        $field_value_array = explode(",",$field_value);
                        //print_r($field_value_array);
                        if(count($field_value_array) > 0){
                            for($j=0;$j<count($field_value_array);$j++){
                                $value = $field_value_array[$j];
                                $db->setQuery("INSERT INTO #__app_sch_order_options (id,order_id,field_id,option_id) VALUES (NULL,'$id','$field_id','$value')");
                                $db->execute();
                            }
                        }
                    }
                }
            }
        }
        //send email to notify customers and employees
		if(!$newOrder)
		{
			OSBHelper::sendEmailAfterSavingOrder($id,$order_status,$old_status);
			HelperOSappscheduleCommon::sendEmail('order_updated_notification',$id);
		}
        if($newOrder)
		{
            $msg = JText::_("OS_PLEASE_ADD_ORDER_ITEMS");
			$mainframe->enqueueMessage($msg);
            $mainframe->redirect(JUri::root()."index.php?option=com_osservicesbooking&task=manage_addservice&order_id=".$id);
        }
        if ($save || !$id){
            $msg = JText::_("OS_ORDER_HAS_BEEN_CHANGED");
			$mainframe->enqueueMessage($msg);
            $mainframe->redirect(JUri::root()."index.php?option=com_osservicesbooking&task=manage_orders");
        }else{
            $msg = JText::_("OS_ORDER_HAS_BEEN_CHANGED");
			$mainframe->enqueueMessage($msg);
            $mainframe->redirect(JUri::root()."index.php?option=com_osservicesbooking&task=manage_editorder&id=".$id);
        }
    }


    /**
     * Add order items
     */
    static function addOrderItems($edit)
    {
        global $mainframe,$configClass,$jinput;
        $db 		    = JFactory::getDbo();
        $id				= $jinput->getInt('id',0);
        $query	        = $db->getQuery(true);
        if($edit == 1)
        {
            $query->select('*')->from('#__app_sch_order_items')->where('id = '.$id);
            $db->setQuery($query);
            $item	= $db->loadObject();
            $query->clear();
            $order_id	= $item->order_id;
            $sid		= $item->sid;
            $vid		= $item->vid;
            $eid		= $item->eid;
            $booking_date = $item->booking_date;
        }
        else
        {
            $order_id = $jinput->getInt('order_id', 0);
            $sid = $jinput->getInt('sid', 0);
            $vid = $jinput->getInt('vid', 0);
            $eid = $jinput->getInt('eid', 0);
            $booking_date = $jinput->get('booking_date', '', 'string');
        }
        $query	        = $db->getQuery(true);
        $query->select('a.id as value,a.service_name as text');
        $query->from($db->quoteName('#__app_sch_services').' AS a');
        $query->where("a.published = '1'");
        if($vid > 0){
            $query->where("a.id in (Select sid from #__app_sch_venue_services where vid = '$vid')");
        }
        $query->order($db->escape('a.service_name'));
        $db->setQuery($query);
        //echo $db->getQuery();
        $services = $db->loadObjectList();
        $optionArr[] = JHTML::_('select.option','','');
        $optionArr = array_merge($optionArr,$services);
        $lists['services'] = JHTML::_('select.genericlist',$optionArr,'sid','class="input-large form-select" onChange="javascript:document.adminForm.submit();"','value','text',$sid);

        $query = "Select a.id as value, concat(a.address,',',a.city,',',a.state) as text from #__app_sch_venues as a inner join #__app_sch_venue_services as b on b.vid = a.id where a.published = '1'";
        if($sid > 0){
            $query .= " and b.sid = '$sid'";
        }
        $query .= " group by a.id order by a.address";
        $db->setQuery($query);
        //echo $db->getQuery();
        $venues = $db->loadObjectList();
        $optionArr = array();
        $optionArr[] = JHTML::_('select.option','','');
        $optionArr = array_merge($optionArr,$venues);
        $lists['venues'] = JHTML::_('select.genericlist',$optionArr,'vid','class="input-large form-select" onChange="javascript:document.adminForm.submit();"','value','text',$vid);

        $query = $db->getQuery(true);
        $query->select('id as value, employee_name as text');
        $query->from('#__app_sch_employee');
        $query->where("published = '1'");
        if($sid > 0){
            $query->where("id in (Select employee_id from #__app_sch_employee_service where service_id = '$sid')");
        }
        if($vid > 0){
            $query->where("id in (Select employee_id from #__app_sch_employee_service where vid = '$vid')");
        }
        $query->order('employee_name');
        $db->setQuery($query);
        $employees = $db->loadObjectList();
        $optionArr = array();
        $optionArr[] = JHTML::_('select.option','','');
        $optionArr = array_merge($optionArr,$employees);
        $lists['employees'] = JHTML::_('select.genericlist',$optionArr,'eid','class="input-large form-select" onChange="javascript:document.adminForm.submit();"','value','text',$eid);

        if(($sid > 0) and ($eid > 0)){
            //show date
            $show_date = 1;
        }else{
            $show_date = 0;
        }

        if(($sid > 0) and ($eid > 0) and ($booking_date != "")){
            if(OSBHelper::checkAvailableDate($sid,$eid,$booking_date)){

            }
        }
        HTML_OSappscheduleManage::addServicesForm($id, $order_id,$lists,$show_date,$sid,$vid,$eid,$booking_date);
    }

    /**
     * Save order item
     */
    static function saveOrderItem()
    {
        global $mainframe,$configClass,$jinput;
        jimport('joomla.filesystem.file');
        require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/orderitem.php';
        $db 		= JFactory::getDbo();
        $id			= $jinput->getInt('id',0);
        $order_id 	= $jinput->getInt('order_id',0);
        $sid 		= $jinput->getInt('sid',0);
        $eid		= $jinput->getInt('eid',0);
        $vid		= $jinput->getInt('vid',0);
        $start_time = $jinput->getInt('start_time',0);
        $end_time	= $jinput->getInt('end_time',0);
        $nslots 	= $jinput->getInt('nslots',0);
        $field_ids  = $jinput->get('field_ids'.$sid,'','string');
        $booking_date = $jinput->getString('booking_date','');


        //OSB 2.3.3. add
        $db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
        $employee               = $db->loadObject();
        $client_id              = $employee->client_id;
        $app_name               = $employee->app_name;
        $app_email_address      = $employee->app_email_address;
        $p12_key_filename       = $employee->p12_key_filename;
        $gcalendarid            = $employee->gcalendarid;

        //we should remove all events on Google Calendar first
        if($id > 0)
        {
            OSBHelper::removeOneEventOnGCalendar($id);
        }

        $db->setQuery("Select service_name from #__app_sch_services where id = '$sid'");
        $service_name           = $db->loadResult();

        $selected_timeslots     = $jinput->get('selected_timeslots',array(),'array');
        if(count($selected_timeslots) > 0)
        {
            for($t = 0;$t<count($selected_timeslots);$t++)
            {
                $timeslot       = $selected_timeslots[$t];
                $timeslotArr    = explode("-",$timeslot);
                $nslots         = $jinput->getInt("nslots".$timeslot,1);
                $start_time     = $timeslotArr[0];
                $end_time       = $timeslotArr[1];
                $row            = &JTable::getInstance('OrderItem','OsAppTable');
				$post			= $jinput->post->getArray();
                $row->bind($post);
                $row->id        = (int) $id;
                $row->start_time = $start_time;
                $row->end_time  = $end_time;
				$row->booking_date = date("Y-m-d", $start_time);
                $row->nslots    = $nslots;
				$row->additional_information = '';
				$row->gcalendar_event_id = '';

				$row->total_cost = 0;
				$row->vid		= (int) $row->vid;
                if(!$row->store())
				{
					throw new Exception($row->getError(), 500);
				}
                if($id > 0)
                {
                    $order_item_id = $id;
                }
                else
                {
                    $order_item_id = $db->insertid();
                }

                if(($configClass['integrate_gcalendar'] == 1) and ($client_id != "") and ($app_name != "")and ($app_email_address != "") and ($gcalendarid != "") and ($p12_key_filename != "") and (JFile::exists(JPATH_COMPONENT_SITE."/".$p12_key_filename)) ){
                    OSBHelper::addEventonGCalendar(trim($client_id),trim($app_name),trim($app_email_address),trim($p12_key_filename),trim($gcalendarid),$service_name,$start_time,$end_time,$booking_date,$order_item_id,$order_id);
                }

                if($id > 0)
                {
                    $db->setQuery("Delete from #__app_sch_order_field_options where order_item_id = '$id'");
                    $db->execute();
                }
                if($field_ids != "")
                {
                    $fieldArr = explode(",",$field_ids);
                    if(count($fieldArr) > 0)
                    {
                        for($i=0;$i<count($fieldArr);$i++)
                        {
                            $field = trim($fieldArr[$i]);
                            $field_name = "field_".$sid."_".$eid."_".$field."_selected";
                            $field_value = $jinput->get($field_name,'','string');
                            if($field_value != "")
                            {
                                $field_value_array = explode(",",$field_value);
                                if(count($field_value_array) > 0)
                                {
                                    for($j=0;$j<count($field_value_array);$j++)
                                    {
                                        $db->setQuery("INSERT INTO #__app_sch_order_field_options (id, order_item_id,field_id, option_id) VALUES (NULL,'$order_item_id','$field','".$field_value_array[$j]."')");
                                        $db->execute();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //save complete
        $msg = JText::_('OS_NEW_SERVICE_HAS_BEEN_ADDED_TO_ORDER');
        $mainframe->enqueueMessage($msg);
        $mainframe->redirect(JUri::root()."index.php?option=com_osservicesbooking&task=manage_editorder&id=".$order_id);
    }

    /**
     * This static function is used to remove order item
     */
    static function removeService(){
        global $mainframe,$configClass,$jinput;
        $db             = JFactory::getDbo();
        $order_id       = $jinput->getInt('order_id',0);
        $id             = $jinput->getInt('id',0);
        jimport('joomla.filesystem.file');
        $db->setQuery("Select eid from #__app_sch_order_items where id = '$id'");
        $eid            = $db->loadResult();
        $db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
        $employee       = $db->loadObject();
        $client_id      = $employee->client_id;
        $app_name       = $employee->app_name;
        $app_email_address = $employee->app_email_address;
        $p12_key_filename = $employee->p12_key_filename;
        $gcalendarid    = $employee->gcalendarid;
        if(($configClass['integrate_gcalendar'] == 1) and ($client_id != "") and ($app_name != "")and ($app_email_address != "") and ($gcalendarid != "") and ($p12_key_filename != "") and (JFile::exists(JPATH_COMPONENT_SITE."/".$p12_key_filename)) ){
            OSBHelper::removeOneEventOnGCalendar($id);
        }
        $db->setQuery("Delete from #__app_sch_order_items where id = '$id'");
        $db->execute();
        $db->setQuery("Delete from #__app_sch_order_field_options where order_item_id = '$id'");
        $db->execute();
        //remove complete
        $msg = JText::_('OS_SERVICE_HAS_BEEN_REMOVED');
		$mainframe->enqueueMessage($msg);
        $mainframe->redirect("index.php?option=com_osservicesbooking&task=manage_editorder&id=".$order_id);
    }

    /**
     * Export invoice
     */
    static function exportInvoice(){
        global $configClass,$jinput;
        $cid = $jinput->get('cid',array(),'array');
        $cid = \Joomla\Utilities\ArrayHelper::toInteger($cid);
		if(count($cid) == 0)
		{
			$itemid = JFactory::getApplication()->input->getInt('Itemid',0);
			JFactory::getApplication()->enqueueMessage(JText::_('OS_PLEASE_SELECT_ORDER_TO_EXPORT_THE_INVOICE'));
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_osservicesbooking&task=manageAllOrders&Itemid='.$itemid));
		}
        $id = $cid[0];
        require_once JPATH_ROOT . "/components/com_osservicesbooking/tcpdf/tcpdf.php";
        //require_once JPATH_ROOT . "/components/com_osservicesbooking/tcpdf/config/lang/eng.php";
        $return = OSBHelper::generateOrderPdf($id);
        while ( @ob_end_clean () );
        OsbInvoice::processDownload ( $return[0],$return[1]);
    }

    static function manage_exportSelectedOrders()
    {
        global $configClass,$jinput;
        $cid = $jinput->get('cid',array(),'array');
        $cid = \Joomla\Utilities\ArrayHelper::toInteger($cid);
        require_once JPATH_ADMINISTRATOR.'/components/com_osservicesbooking/classes/orders.php';
        OSappscheduleOrders::exportCsv('com_osservicesbooking',$cid);
    }

    static function manage_exportCsv()
    {
        require_once JPATH_ADMINISTRATOR.'/components/com_osservicesbooking/classes/orders.php';
        OSappscheduleOrders::exportOrders();
    }

	static function manageUsers()
	{
		global $jinput;
		$db					= JFactory::getDbo();
		$filter_group_id	= $jinput->getInt('filter_group_id',0);
		$limitstart			= $jinput->getInt('limitstart',0);
		$limit				= $jinput->getInt('limit',20);
		$filter_search		= $jinput->getString('filter_search','');
		$query				= "Select count(tbl.id) from #__users as tbl where tbl.block = 0";
		if($filter_group_id > 0)
		{
			$lists['filter_group_id'] = $filter_group_id;
			$query			.= " and tbl.id IN (SELECT user_id FROM #__user_usergroup_map WHERE group_id=" . (int) $filter_group_id . ")";
		}
		if($filter_search != "")
		{
			$query			.= " and (tbl.name like '%".$filter_search."%' or tbl.username like '%".$filter_search."%' or tbl.email like '%".$filter_search."%')";
		}
		$db->setQuery($query);
		$total				= $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav 					= new JPagination($total,$limitstart,$limit);

		$query				= "Select tbl.* from #__users as tbl where tbl.block = 0";
		if($filter_group_id > 0)
		{
			$query			.= " and tbl.id IN (SELECT user_id FROM #__user_usergroup_map WHERE group_id=" . (int) $filter_group_id . ")";
		}
		if($filter_search != "")
		{
			$query			.= " and (tbl.name like '%".$filter_search."%' or tbl.username like '%".$filter_search."%' or tbl.email like '%".$filter_search."%')";
		}
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows				= $db->loadObjectList();
		if (count($rows))
		{
			foreach ($rows as $row)
			{
				$row->group_names = self::_getUserDisplayedGroups($row->id);
			}
		}
		HTML_OSappscheduleManage::listUsers($rows, $pageNav, $lists);
	}

	public static function _getUserDisplayedGroups($userId)
	{
		$db    = JFactory::getDbo();
		$query = "SELECT title FROM " . $db->quoteName('#__usergroups') . " ug left join " .
			$db->quoteName('#__user_usergroup_map') . " map on (ug.id = map.group_id)" .
			" WHERE map.user_id=" . $userId;

		$db->setQuery($query);
		$result = $db->loadColumn();

		return implode("\n", $result);
	}
}
?>