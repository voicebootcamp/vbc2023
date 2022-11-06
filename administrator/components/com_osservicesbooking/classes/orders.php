<?php
/*------------------------------------------------------------------------
# orders.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

/**
 * Enter description here...
 *
 */
class OSappscheduleOrders{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$jinput;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);	
		$option = $jinput->get('option','com_osservicesbooking','string');
		$mainframe = JFactory::getApplication();
		$cid        = $jinput->get('cid',array(),'ARRAY');
		\Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		require_once(JPATH_ROOT."/components/com_osservicesbooking/classes/default.php");
		require_once(JPATH_ROOT."/components/com_osservicesbooking/classes/default.html.php");
		require_once(JPATH_ROOT."/components/com_osservicesbooking/helpers/common.php");
		require_once(JPATH_ROOT."/components/com_osservicesbooking/helpers/ics.php");
		switch ($task){
			default:
			case "orders_list":
				OSappscheduleOrders::orders_list($option);
			break;
			case "orders_save":
				OSappscheduleOrders::orders_save($option,1);
			break;
			case "orders_apply":
				OSappscheduleOrders::orders_save($option,0);
			break;	
			case "orders_remove":
				OSappscheduleOrders::orders_remove($option,$cid);
			break;
			case "orders_detail":
				OSappscheduleOrders::orders_detail($option,$cid[0]);
			break;
			case "orders_export":
				OSappscheduleOrders::exportCsv($option,$cid);
			break;
            case "orders_exportcsv":
                OSappscheduleOrders::exportOrders();
            break;
			case "orders_exportpdf":
				OSappscheduleOrders::exportOrdersPdf();
			break;
			case "orders_dowloadInvoice" :
				OSappscheduleOrders::download_invoice ( $cid[0] );
			break;
			case "orders_addservice":
				OSappscheduleOrders::addServices($option);
			break;
			case "orders_editservice":
				OSappscheduleOrders::addServices($option);
			break;
			case "orders_saveservice":
				OsAppscheduleOrders::saveService($option, 1);
			break;
			case "orders_applyservice":
				OsAppscheduleOrders::saveService($option, 0);
			break;
			case "orders_removeservice":
				OsAppscheduleOrders::removeService($option);
			break;
			case "orders_sendnotify":
				OsAppscheduleOrders::sendnotifyEmails($cid);
			break;
			case "orders_addnew":
				OSappscheduleOrders::orders_detail($option,0);
			break;
			case "orders_gotoorderdetails":
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_detail&cid[]=".$jinput->getInt('order_id',0));
			break;
			case "orders_exportreport":
				OSappscheduleOrders::exportReport(1);
			break;
			case "orders_csvexportreport":
				OSappscheduleOrders::exportReport(0);
			break;
			case "orders_copyfolder":
				OSappscheduleOrders::copyFolder();
			break;
			case "orders_updateNewOrderStatus":
				OSappscheduleOrders::updateNewOrderStatus();
			break;
            case "orders_refund":
                OSappscheduleOrders::refundOrder();
            break;
			case "orders_disablereminders":
				OSappscheduleOrders::disablereminders();
			break;
			case "orders_sendpaymentrequest":
				OSappscheduleOrders::sendpaymentrequest();
			break;
			case "orders_changeCheckinstatus":
				OSappscheduleOrders::changeCheckinstatus();
			break;
			case "orders_customers":
				OSappscheduleOrders::manageCustomers();
			break;
			case "orders_removecustomer":
				OSappscheduleOrders::removecustomer($cid);
			break;
			case "orders_exportcustomers":
				OSappscheduleOrders::exportcustomers();
			break;
			case "orders_customerdetails":
				OSappscheduleOrders::customerdetails();
			break;
			case "orders_savecustomer":
				OSappscheduleOrders::saveCustomerInfo(1);
			break;
			case "orders_applycustomer":
				OSappscheduleOrders::saveCustomerInfo(0);
			break;
			case "orders_cancelcustomer":
				$mainframe->redirect('index.php?option=com_osservicesbooking&task=orders_customers');
			break;
		}
	}

	static function customerdetails()
	{
		global $mainframe, $configClass, $jinput;
		$id = $jinput->getInt('id', 0);
		$db = JFactory::getDbo();
		if($id > 0)
		{
			$db->setQuery("Select * from #__app_sch_userprofiles where id = '$id'");
			$row = $db->loadObject();

			if($row->user_id > 0)
			{
				$tmpSql = " b.user_id = '$row->user_id'";
			}
			else
			{
				$tmpSql = " b.order_name = '$row->order_name' and b.order_email = '$row->order_email'";
			}

			$query = "Select a.*, b.order_status, c.service_time_type, c.service_name, d.employee_name from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id inner join #__app_sch_services as c on c.id = a.sid inner join #__app_sch_employee as d on d.id = a.eid where ".$tmpSql;
			$db->setQuery($query);
			$orders = $db->loadObjectList();
			HTML_OSappscheduleOrders::customerDetails($row, $orders);
		}
		else
		{
			throw new Exception ('Customer is not exists');
		}
	}

	static function exportcustomers()
	{
		global $mainframe, $configClass;
		ini_set('memory_limit','999M');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		jimport('joomla.archive.archive');
		$db		= JFactory::getDbo();
		$query	= "Select * from #__app_sch_userprofiles";
		$db->setQuery($query);
		$rows	= $db->loadObjectList();

		$csv_separator 	= $configClass['csv_separator'];
		$labels = [];
		$labels[] = JText::_('OS_NAME');
		$labels[] = JText::_('OS_EMAIL');
		$labels[] = JText::_('OS_PHONE');
		$labels[] = JText::_('OS_ADDRESS');
		$labels[] = JText::_('OS_CITY');
		$labels[] = JText::_('OS_ZIP');
		$labels[] = JText::_('OS_STATE');
		$labels[] = JText::_('OS_COUNTRY');
		$labels[] = JText::_('OS_NOTES');
		$filecsv = JPATH_ROOT."/tmp/customers.csv";
		$fp = fopen($filecsv, 'w');
		fwrite($fp,"\xEF\xBB\xBF");
		fputcsv($fp, $labels, $csv_separator);

		foreach($rows as $row)
		{
			$tmp = [];
			$tmp[] = $row->order_name;
			$tmp[] = $row->order_email;
			$tmp[] = $row->order_phone;
			$tmp[] = $row->order_address;
			$tmp[] = $row->order_city;
			$tmp[] = $row->order_zip;
			$tmp[] = $row->order_state;
			$tmp[] = $row->order_country;
			$tmp[] = $row->notes;
			fputcsv($fp, $tmp, $csv_separator);
		}
		fclose($fp);
		OSappscheduleOrders::downloadfile2(JPATH_ROOT."/tmp/customers.csv","customers.csv");
		$mainframe->close();
	}

	static function removecustomer($cid)
	{
		global $mainframe;
		if(count($cid))
		{
			$db = JFactory::getDbo();
			$db->setQuery("Delete from #__app_sch_userprofiles where id in (".implode(",", $cid).")");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		$mainframe->redirect('index.php?option=com_osservicesbooking&task=orders_customers');
	}

	static function changeCheckinstatus()
	{
		global $mainframe, $jinput;
		$db = JFactory::getDbo();
		$id = $jinput->getInt('id',0);
		$status = $jinput->getInt('status',0);
		$db->setQuery("Update #__app_sch_order_items set checked_in = '$status' where id = '$id'");
		$db->execute();
		if($status == 1)
		{
			?>
			<a href="javascript:changeCheckinStatus(<?php echo $id;?>,0);" title="<?php echo JText::_('OS_CLICK_HERE_TO_CHANGE_CHECKIN_STATUS_OF_ITEM');?>">
				<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/publish.png" />
			</a>
			<?php
		}
		else
		{
			?>
			<a href="javascript:changeCheckinStatus(<?php echo $id;?>,1);" title="<?php echo JText::_('OS_CLICK_HERE_TO_CHANGE_CHECKIN_STATUS_OF_ITEM');?>">
				<img src="<?php echo JURI::base()?>components/com_osservicesbooking/asset/images/unpublish.png" />
			</a>
			<?php
		}
		exit();
	}

	public static function sendpaymentrequest()
	{
		global $mainframe, $jinput;
		$cid		= $jinput->post->get('cid', [], 'array');
		\Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		
		foreach ($cid as $id)
		{
			$row	= &JTable::getInstance('Order','OsAppTable');
			$row->load((int)$id);
			if($row->order_upfront > 0 && $row->order_status == 'P' && $row->refunded == 0)
			{
				HelperOSappscheduleCommon::sendPaymentRequestEmails($row);
			}
		}

		$msg		= JText::_('OS_REQUEST_PAYMENT_EMAIL_SENT_SUCCESSFULLY');
		$url		= 'index.php?option=com_osservicesbooking&task=orders_list';
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect($url);
	}

	public static function disablereminders()
	{
		global $mainframe, $jinput;
		$cid = $jinput->post->get('cid', [], 'array');

		if (count($cid))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__app_sch_orders')
				->set('receive_reminder = 0')
				->where('id IN (' . implode(',', $cid) . ')');
			$db->setQuery($query)
				->execute();
		}
		$mainframe->enqueueMessage(JText::_('OS_REMINDER_EMAILS_DISABLED_FOR_SELECTED_ORDERS'));
		$mainframe->redirect('index.php?option=com_osservicesbooking&task=orders_list');
	}
	
	static function copyFolder(){
		jimport('joomla.filesystem.folder');
		if(JFolder::exists(JPATH_ROOT."/Zend")){
			if(!JFolder::exists(JPATH_ROOT."/administrator/Zend")){	
				JFolder::copy(JPATH_ROOT."/Zend",JPATH_ROOT."/administrator/Zend");
			}
		}
	}


	public static function refundOrder()
    {
        global $mainframe, $jinput;
        $id         = $jinput->getInt('id',0);
        $comeback   = $jinput->getInt('comeback',0);
        if($id > 0)
        {
            $db     = JFactory::getDbo();
            $query  = $db->getQuery(true);
            $query->select('*')
                ->from('#__app_sch_orders')
                ->where('id = ' . $id);
            $db->setQuery($query);
            $row = $db->loadObject();
            if(OSBHelper::canRefundOrder($row))
            {
                $method = os_payments::getPaymentMethod($row->order_payment);

                $method->refund($row);
                $query = $db->getQuery(true)
                    ->update('#__app_sch_orders')
                    ->set('refunded = 1')
                    ->where('id = ' . $row->id);
                $db->setQuery($query)
                    ->execute();

                $msg = JText::_('OS_ORDER_REFUNDED');
                $mainframe->enqueueMessage($msg);
                if($comeback == 0)
                {
                    $mainframe->redirect('index.php?option=com_osservicesbooking&task=orders_list');
                }
                else
                {
                    $mainframe->redirect('index.php?option=com_osservicesbooking&task=orders_detail&cid[]='.$id);
                }
            }
            else
            {
                throw new InvalidArgumentException(JText::_('OS_CANNOT_PROCESS_REFUND'));
            }
        }
    }

	/**
	 * Adding new services
	 *
	 */
	static function addServices(){
		global $mainframe,$configClass,$jinput,$mapClass;
		$db 		= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$id			= $jinput->getInt('id',0);
		if($id > 0)
		{
			$query->select('*')->from('#__app_sch_order_items')->where('id = '.$id);
			$db->setQuery($query);
			$item	= $db->loadObject();
			$query->clear();
			$order_id	= $item->order_id;
			$sid		= $jinput->getInt('sid',$item->sid);
			$vid		= $jinput->getInt('vid',$item->vid);
			$eid		= $jinput->getInt('eid',$item->eid);

			$booking_date = $jinput->get('booking_date','','string');
			if($booking_date == '')
			{
				$booking_date = $item->booking_date;
			}
		}
		else
		{
			$order_id 	= $jinput->getInt('order_id',0);
			$sid		= $jinput->getInt('sid',0);
			$vid		= $jinput->getInt('vid',0);
			$eid 		= $jinput->getInt('eid',0);
			$booking_date = $jinput->get('booking_date','','string');
		}
		
		
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
		$lists['services'] = JHTML::_('select.genericlist',$optionArr,'sid','class="'.$mapClass['input-large'].' form-select ilarge" onChange="javascript:document.adminForm.submit();"','value','text',$sid);
		
		$query = "Select a.id as value, concat(a.address,',',a.city,',',a.state) as text from #__app_sch_venues as a inner join #__app_sch_venue_services as b on b.vid = a.id where a.published = '1'";
		if($sid > 0)
		{
			$query .= " and b.sid = '$sid'";
		}
		$query .= " group by a.id order by a.address";
		$db->setQuery($query);
		//echo $db->getQuery();
		$venues = $db->loadObjectList();
		$optionArr = array();
		$optionArr[] = JHTML::_('select.option','','');
		$optionArr = array_merge($optionArr,$venues);
		$lists['venues'] = JHTML::_('select.genericlist',$optionArr,'vid','class="'.$mapClass['input-large'].' form-select ilarge" onChange="javascript:document.adminForm.submit();"','value','text',$vid);
		
		$query = $db->getQuery(true);
		$query->select('id as value, employee_name as text');
		$query->from('#__app_sch_employee');
		$query->where("published = '1'");
		if($sid > 0)
		{
			$query->where("id in (Select employee_id from #__app_sch_employee_service where service_id = '$sid')");
		}
		if($vid > 0)
		{
			$query->where("id in (Select employee_id from #__app_sch_employee_service where vid = '$vid')");
		}
		$query->order('employee_name');
		$db->setQuery($query);
		$employees = $db->loadObjectList();
		$optionArr = array();
		$optionArr[] = JHTML::_('select.option','','');
		$optionArr = array_merge($optionArr,$employees);
		$lists['employees'] = JHTML::_('select.genericlist',$optionArr,'eid','class="'.$mapClass['input-large'].' form-select ilarge" onChange="javascript:document.adminForm.submit();"','value','text',$eid);
		
		if($sid > 0 && $eid > 0)
		{
			//show date
			$show_date = 1;
		}
		else
		{
			$show_date = 0;
		}
		
		if($sid > 0 && $eid > 0 && $booking_date != "")
		{
			if(OSBHelper::checkAvailableDate($sid,$eid,$booking_date))
			{
				
			}
		}
		HTML_OSappscheduleOrders::addServicesForm($id,$order_id,$lists,$show_date,$sid,$vid,$eid,$booking_date);
	}
	
	/**
	 * Download Invoice
	 * Step 1: Making PPF file 
	 * Step 2: Download the PDF file
	 *
	 * @param unknown_type $id
	 */
	static function download_invoice($id) 
	{
		global $configClass;
		require_once JPATH_ROOT . "/components/com_osservicesbooking/tcpdf/tcpdf.php";
		//require_once JPATH_ROOT . "/components/com_osservicesbooking/tcpdf/config/lang/eng.php";
		$return = OSBHelper::generateOrderPdf($id);
		while ( @ob_end_clean () );
		OsbInvoice::processDownload ( $return[0],$return[1]);
	}

	public static function generateExportData($isCsv)
	{
		global $mainframe,$configClass,$jinput;
		$sep	= ($isCsv == 1 ? '|' : '');
		$config = new JConfig();
        $offset = $config->offset;
        date_default_timezone_set($offset);
        $condition = '';
        $db = JFactory::getDbo();

		$filter_order 				= $jinput->get('filter_order','a.id','string');
        $filter_order_Dir 			= $jinput->get('filter_order_Dir','desc','string');
        $lists['order'] 			= $filter_order;
        $lists['order_Dir'] 		= $filter_order_Dir;
        $order_by 					= " ORDER BY $filter_order $filter_order_Dir";

        $keyword 			 		= $mainframe->getUserStateFromRequest($option.'.orders.keyword','keyword','','string');
        $lists['keyword']  			= $keyword;
        if($keyword != "")
        {
            $condition 			   .= " AND (";
            $condition 			   .= " a.order_name LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_email LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_phone LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_country LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_city LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_state LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_zip LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_address LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_upfront LIKE '%$keyword%'";
            $condition 			   .= " OR a.order_date LIKE '%$keyword%'";
            $condition 			   .= " )";
        }

        // filter state
        $filter_status 				= $mainframe->getUserStateFromRequest($option.'.orders.filter_status','filter_status','','string');
        // filter date
        $filter_date_from			= $mainframe->getUserStateFromRequest($option.'.orders.filter_date_from','filter_date_from',null,'string');
        
        $filter_date_to				= $mainframe->getUserStateFromRequest($option.'.orders.filter_date_to','filter_date_to',null,'string');
       
        // filter extra
        $add_query 					= '';
        $filter_service 			= $mainframe->getUserStateFromRequest($option.'.orders.filter_service','filter_service',0,'int');
        $filter_employee 			= $mainframe->getUserStateFromRequest($option.'.orders.filter_employee','filter_employee',0,'int');
		

		//new version
		$query	= "Select a.*, b.*, c.service_name, c.service_time_type, d.employee_name, e.address from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id";
		$query .= " inner join #__app_sch_services as c on a.sid = c.id";
		$query .= " inner join #__app_sch_employee as d on a.eid = d.id";
		$query .= " left join #__app_sch_venues as e on a.vid = e.id";
		$query .= " where 1=1 ";
		if($filter_status != "")
		{
			$query .= " and b.order_status = '$filter_status'";
		}
		if((int) $filter_service > 0)
		{
			$query .= " and a.sid = '$filter_service'";
		}
		if((int) $filter_employee > 0)
		{
			$query .= " and a.eid = '$filter_employee'";
		}
		if((int) $filter_venue > 0)
		{
			$query .= " and a.vid = '$filter_venue'";
		}
		if ($filter_date_from != '' )
		{
			$query .= " AND a.booking_date >= '".$filter_date_from." 00:00:00'";
		}
		
		if ($filter_date_to != '' )
		{
			$query .= " AND a.booking_date <= '".$filter_date_to." 00:00:00'";
		}
		$query	   .= " order by a.order_id desc";

        $db->setQuery($query);
        $rows 						= $db->loadObjectList();
		if(count($rows) > 0)
		{
			$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' order by ordering");
            $fields = $db->loadObjectList();
			foreach($rows as $row)
			{	
				if(count($fields) > 0)
				{
					for($i1=0;$i1<count($fields);$i1++)
					{
						$field = $fields[$i1];
						$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
						$count = $db->loadResult();
						if($count > 0)
						{
							if($field->field_type == 1)
							{
								$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
								$option_id			= $db->loadResult();
								$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
								$optionvalue		= $db->loadObject();
								
								$field_data			= OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$row->order_lang);
								if($optionvalue->additional_price > 0 && $configClass['disable_payments'] == 1)
								{
									$field_data     .= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
								}
								$row->{'field_'.$field->id} = $field_data;
							}
							elseif($field->field_type == 2)
							{
								$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
								$option_ids			= $db->loadObjectList();
								$fieldArr			= array();
								for($j1=0;$j1<count($option_ids);$j1++)
								{
									$oid			= $option_ids[$j1];
									$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
									$optionvalue	= $db->loadObject();
									$field_data		= OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$row->order_lang);
									if($optionvalue->additional_price > 0 && $configClass['disable_payments'] == 1)
									{
										$field_data.= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
									}
									$fieldArr[] = $field_data;
								}
								$row->{'field_'.$field->id} = implode(", ",$fieldArr);
							}
						}
					}
				}
			}
		}

		return $rows;
	}

	static function exportOrders()
	{
        global $mainframe,$configClass,$jinput;
        $csv_content	= "";
        $config			= new JConfig();
        $offset			= $config->offset;
        date_default_timezone_set($offset);
        $condition		= '';
        $db				= JFactory::getDbo();
        $csv_separator 	= $configClass['csv_separator'];
        
		$rows = self::generateExportData(1);

        $header = '"ID"'.$csv_separator.'"'.JText::_('OS_NAME').'"'.$csv_separator.'"'.JText::_('OS_EMAIL').'"'.$csv_separator.'"'.JText::_('OS_PHONE').'"'.$csv_separator.'"'.JText::_('OS_COUNTRY').'"'.$csv_separator.'"'.JText::_('OS_STATE').'"'.$csv_separator.'"'.JText::_('OS_CITY').'"'.$csv_separator.'"'.JText::_('OS_ADDRESS').'"'.$csv_separator.'"'.JText::_('OS_ZIP').'"'.$csv_separator.'"'.JText::_('OS_DATE').'"'.$csv_separator.'"'.JText::_('OS_SERVICES').'"'.$csv_separator.'"'.JText::_('OS_EMPLOYEE').'"'.$csv_separator.'"'.JText::_('OS_VENUE').'"'.$csv_separator.'"'.JText::_('OS_BOOKING_DATE').'"'.$csv_separator.'"'.JText::_('OS_FROM').'"'.$csv_separator.'"'.JText::_('OS_TO').'"'.$csv_separator.'"'.JText::_('OS_NUMBER_SLOTS').'"';

		$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' order by ordering");
		$extraFields = $db->loadObjectList();
		if(count($extraFields))
		{
			foreach($extraFields as $field)
			{
				$header .= $csv_separator .'"'.$field->field_label.'"'; 
			}
		}

		$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1'");
		$checkoutFields = $db->loadObjectList();
		if(count($checkoutFields))
		{
			foreach($checkoutFields as $f)
			{
				$header .= $csv_separator .'"'.$f->field_label.'"'; 
			}
		}
        if($configClass['disable_payments'] == 1)
		{
			if($configClass['enable_tax']==1 && (float) $configClass['tax_payment'] > 0)
			{
				$header .= $csv_separator.'"'.JText::_('OS_TAX').'"';
			}
            $header .= $csv_separator.'"'.JText::_('OS_PAYMENT').'"'.$csv_separator.'"'.JText::_('OS_TOTAL').'"';
        }
        $header .= $csv_separator.'"'.JText::_('OS_STATUS').'"';

        $csv_content .= "\n";
        if(count($rows) > 0)
        {
            for($i=0;$i<count($rows);$i++)
            {
                $row = $rows[$i];
                $id  = $row->order_id;
                if(strlen($id) < 5)
				{
                    for($j=strlen($id);$j<=5;$j++)
                    {
                        $id = "0".$id;
                    }
                }
                $csv_content .= '"'.$id.'"'.$csv_separator.'"'.$row->order_name.'"'.$csv_separator.'"'.$row->order_email.'"'.$csv_separator.'"'.$row->order_phone.'"'.$csv_separator.'"'.$row->order_country.'"'.$csv_separator.'"'.$row->order_state.'"'.$csv_separator.'"'.$row->order_city.'"'.$csv_separator.'"'.$row->order_address.'"'.$csv_separator.'"'.$row->order_zip.'"'.$csv_separator.'"'.$row->order_date.'"';
				$csv_content .= $csv_separator.'"'.$row->service_name.'"';
				$csv_content .= $csv_separator.'"'.$row->employee_name.'"';
				$csv_content .= $csv_separator.'"'.$row->address.'"';
				$csv_content .= $csv_separator.'"'.date($configClass['date_format'],$row->start_time).'"';
				$csv_content .= $csv_separator.'"'.date($configClass['time_format'],$row->start_time).'"';
				$csv_content .= $csv_separator.'"'.date($configClass['time_format'],$row->end_time).'"';
				$csv_content .= $csv_separator.'"'.$row->nslots.'"';
				if(count($extraFields))
				{
					foreach($extraFields as $field)
					{
						$csv_content .= $csv_separator .'"'.$row->{'field_'.$field->id}.'"'; 
					}
				}

				if(count($checkoutFields))
				{
					foreach($checkoutFields as $f)
					{
						$field_value = OsAppscheduleDefault::orderFieldData($f, $row->order_id);
						$csv_content .= $csv_separator .'"'.$field_value.'"';
					}
				}

                if($configClass['disable_payments'] == 1)
				{
                    $order_payment = $row->order_payment;
					if($configClass['enable_tax']==1 && (float) $configClass['tax_payment'] > 0)
					{
						$csv_content .= $csv_separator.'"'.$row->order_tax." ".$configClass['currency_format'].'"';
					}
                    if($order_payment != "")
					{
                        $csv_content .= $csv_separator.'"'.JText::_(os_payments::loadPaymentMethod($order_payment)->title).'"';
                        $csv_content .= $csv_separator.'"'.$row->order_total." ".$configClass['currency_format'].'"';
                    }
                }
                $csv_content .= $csv_separator.'"'.OSBHelper::orderStatus(0,$row->order_status).'"';
                $csv_content .= "\n";
            }
        }

        $header = $header.$csv_content;
        //create the csv file
        $filename = time().".csv";
        $csv_absoluted_link = JPATH_ROOT."/tmp".DS.$filename;
        //create the content of csv
        $csvf = fopen($csv_absoluted_link,'w');
        @fwrite($csvf,$header);
        @fclose($csvf);
        OSappscheduleOrders::downloadfile2($csv_absoluted_link,$filename);
    }

	/*
	//for the case the original download function doesn't work

	static function downloadfile2($path, $filename)
	{
		ob_clean();
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        header('Content-Type: ' . finfo_file($finfo, $path));
  
        $finfo = finfo_open(FILEINFO_MIME_ENCODING);
        header('Content-Transfer-Encoding: ' . finfo_file($finfo, $path)); 
       
        header('Content-disposition: attachment; filename="' . $filename . '"'); 
       
        readfile($path);
        die();
	}
	*/

	static function exportOrdersPdf()
	{
		global $mainframe,$configClass,$jinput;
        $config			= new JConfig();
        $offset			= $config->offset;
        date_default_timezone_set($offset);
        $condition		= '';
        $db				= JFactory::getDbo();
		$rows			= self::generateExportData(0);

		require_once JPATH_ROOT . "/components/com_osservicesbooking/tcpdf/tcpdf.php";
		//require_once JPATH_ROOT . "/components/com_osservicesbooking/tcpdf/config/lang/eng.php";
		require_once JPATH_ROOT . "/components/com_osservicesbooking/classes/template.class.php";
		$pdf			= new TCPDF ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->SetCreator ( PDF_CREATOR );
		$pdf->SetAuthor ( JFactory::getConfig()->get('sitename') );
		$pdf->SetTitle ( 'Orders list' );
		$pdf->SetSubject ( 'Orders list' );
		$pdf->SetKeywords ( 'Orders list' );
		$pdf->setHeaderFont ( Array (PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
		$pdf->setFooterFont ( Array (PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ) );
		$pdf->setPrintHeader ( false );
		$pdf->setPrintFooter ( false );
		$pdf->SetMargins ( PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT );
		$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
		$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );
		
		//set auto page breaks
		$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_BOTTOM );
		
		//set image scale factor
		$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );
		$font			= empty($configClass['pdf_font']) ? 'times' :$configClass['pdf_font'];
		// True type font
		if (substr($font, -4) == '.ttf')
		{
			$font		= TCPDF_FONTS::addTTFfont(JPATH_ROOT . '/components/com_osservicesbooking/tcpdf/fonts/' . $font, 'TrueTypeUnicode', '', 96);
		}
		$pdf->SetFont ( $font , '', 8 );
		$pdf->AddPage ();

		jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/orders_pdf.php'))
		{
            $tpl		= new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }
		else
		{
            $tpl		= new OSappscheduleTemplate(JPATH_ROOT.'/components/com_osservicesbooking/layouts/');
        }
		$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' order by ordering");
		$extraFields = $db->loadObjectList();
		$tpl->set('extraFields',$extraFields);
		$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1'");
		$checkoutFields = $db->loadObjectList();
		$tpl->set('checkoutFields',$checkoutFields);
        $tpl->set('mainframe',$mainframe);
        $tpl->set('rows',$rows);
		$tpl->set('configClass',$configClass);
        $html			= $tpl->fetch("orders_pdf.php");

		$pdf->writeHTML($html, true, false, false, false, '');
		$filePath = JPATH_ROOT . '/media/com_osservicesbooking/orders.pdf';
		$pdf->Output($filePath, 'F');
		while ( @ob_end_clean () );
		OsbInvoice::processDownload ($filePath,'orders.pdf');
	}

	/**
	 * Export csv
	 *
	 */
	static function exportCsv($option,$cid){
		global $mainframe,$configClass,$jinput;
        $csv_content = "";
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$condition = '';
		$db = JFactory::getDbo();
		if(count($cid) == 0)
        {
            $mainframe->enqueueMessage(JText::_('OS_NO_ORDERS_TO_EXPORT'));
            $mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_list");
        }
		$cids						= implode(",",$cid);
		$csv_separator 				= $configClass['csv_separator'];
		
		$query	= "Select a.*, b.*, c.service_name, c.service_time_type, d.employee_name, e.address from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id";
		$query .= " inner join #__app_sch_services as c on a.sid = c.id";
		$query .= " inner join #__app_sch_employee as d on a.eid = d.id";
		$query .= " left join #__app_sch_venues as e on a.vid = e.id";
		$query .= " where a.order_id in (".$cids.") order by a.order_id";

		$db->setQuery($query);
		$rows 						= $db->loadObjectList();
		
		 $header = '"ID"'.$csv_separator.'"'.JText::_('OS_NAME').'"'.$csv_separator.'"'.JText::_('OS_EMAIL').'"'.$csv_separator.'"'.JText::_('OS_PHONE').'"'.$csv_separator.'"'.JText::_('OS_COUNTRY').'"'.$csv_separator.'"'.JText::_('OS_STATE').'"'.$csv_separator.'"'.JText::_('OS_CITY').'"'.$csv_separator.'"'.JText::_('OS_ADDRESS').'"'.$csv_separator.'"'.JText::_('OS_ZIP').'"'.$csv_separator.'"'.JText::_('OS_DATE').'"'.$csv_separator.'"'.JText::_('OS_SERVICES').'"'.$csv_separator.'"'.JText::_('OS_EMPLOYEE').'"'.$csv_separator.'"'.JText::_('OS_VENUE').'"'.$csv_separator.'"'.JText::_('OS_BOOKING_DATE').'"'.$csv_separator.'"'.JText::_('OS_FROM').'"'.$csv_separator.'"'.JText::_('OS_TO').'"'.$csv_separator.'"'.JText::_('OS_NUMBER_SLOTS').'"';

		$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' order by ordering");
		$extraFields = $db->loadObjectList();
		if(count($extraFields))
		{
			foreach($extraFields as $field)
			{
				$header .= $csv_separator .'"'.$field->field_label.'"'; 
			}
		}

		$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1'");
		$checkoutFields = $db->loadObjectList();
		if(count($checkoutFields))
		{
			foreach($checkoutFields as $f)
			{
				$header .= $csv_separator .'"'.$f->field_label.'"'; 
			}
		}

		if($configClass['disable_payments'] == 1)
		{
			if($configClass['enable_tax']==1 && (float) $configClass['tax_payment'] > 0)
			{
				$header .= $csv_separator.'"'.JText::_('OS_TAX').'"';
			}
			$header .= $csv_separator.'"'.JText::_('OS_PAYMENT').'"'.$csv_separator.'"'.JText::_('OS_TOTAL').'"';
		}
		$header .= $csv_separator.'"'.JText::_('OS_STATUS').'"';
		
		$csv_content .= "\n";
		if(count($rows) > 0)
		{
			
			
			foreach($rows as $row)
			{	
				if(count($extraFields) > 0)
				{
					for($i1=0;$i1<count($extraFields);$i1++)
					{
						$field = $extraFields[$i1];
						$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
						$count = $db->loadResult();
						if($count > 0)
						{
							if($field->field_type == 1)
							{
								$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
								$option_id			= $db->loadResult();
								$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
								$optionvalue		= $db->loadObject();
								
								$field_data			= OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$row->order_lang);
								if($optionvalue->additional_price > 0 && $configClass['disable_payments'] == 1)
								{
									$field_data     .= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
								}
								$row->{'field_'.$field->id} = $field_data;
							}
							elseif($field->field_type == 2)
							{
								$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
								$option_ids			= $db->loadObjectList();
								$fieldArr			= array();
								for($j1=0;$j1<count($option_ids);$j1++)
								{
									$oid			= $option_ids[$j1];
									$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
									$optionvalue	= $db->loadObject();
									$field_data		= OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$row->order_lang);
									if($optionvalue->additional_price > 0 && $configClass['disable_payments'] == 1)
									{
										$field_data.= " - ".$optionvalue->additional_price." ".$configClass['currency_format'];
									}
									$fieldArr[] = $field_data;
								}
								$row->{'field_'.$field->id} = implode(", ",$fieldArr);
							}
						}
					}
				}
			}
			for($i=0;$i<count($rows);$i++)
			{
				$row = $rows[$i];
				$id  = $row->id;
				
				$order_id = $row->order_id;

				$csv_content .= '"'.$order_id.'"'.$csv_separator.'"'.$row->order_name.'"'.$csv_separator.'"'.$row->order_email.'"'.$csv_separator.'"'.$row->order_phone.'"'.$csv_separator.'"'.$row->order_country.'"'.$csv_separator.'"'.$row->order_state.'"'.$csv_separator.'"'.$row->order_city.'"'.$csv_separator.'"'.$row->order_address.'"'.$csv_separator.'"'.$row->order_zip.'"'.$csv_separator.'"'.$row->order_date.'"';

				$csv_content .= $csv_separator.'"'.$row->service_name.'"';
				$csv_content .= $csv_separator.'"'.$row->employee_name.'"';
				$csv_content .= $csv_separator.'"'.$row->address.'"';
				$csv_content .= $csv_separator.'"'.date($configClass['date_format'],$row->start_time).'"';
				$csv_content .= $csv_separator.'"'.date($configClass['time_format'],$row->start_time).'"';
				$csv_content .= $csv_separator.'"'.date($configClass['time_format'],$row->end_time).'"';
				$csv_content .= $csv_separator.'"'.$row->nslots.'"';
				if(count($extraFields))
				{
					foreach($extraFields as $field)
					{
						$csv_content .= $csv_separator .'"'.$row->{'field_'.$field->id}.'"'; 
					}
				}

              
				if(count($checkoutFields))
				{
					foreach($checkoutFields as $f)
					{
						$field_value = OsAppscheduleDefault::orderFieldData($f, $row->order_id);
						$csv_content .= $csv_separator .'"'.$field_value.'"';
					}
				}
								
				if($configClass['disable_payments'] == 1)
				{
					if($configClass['enable_tax']==1 && (float) $configClass['tax_payment'] > 0)
					{
						$csv_content .= $csv_separator.'"'.$row->order_tax." ".$configClass['currency_format'].'"';
					}
					$order_payment = $row->order_payment;
					if($order_payment != "")
					{
						$csv_content .= $csv_separator.'"'.JText::_(os_payments::loadPaymentMethod($order_payment)->title).'"';
						$csv_content .= $csv_separator.'"'.$row->order_total." ".$configClass['currency_format'].'"';
					}
				}
				$csv_content .= $csv_separator.'"'.OSBHelper::orderStatus(0,$row->order_status).'"';
				$csv_content .= "\n";
			}
		}
		
		$header = $header.$csv_content;
		//create the csv file
		$filename = time().".csv";
		$csv_absoluted_link = JPATH_ROOT."/tmp".DS.$filename;
		//create the content of csv
		$csvf = fopen($csv_absoluted_link,'w');
		@fwrite($csvf,$header);
		@fclose($csvf);
		OSappscheduleOrders::downloadfile2($csv_absoluted_link,$filename);
	}
	
	static function downloadfile2($file_path,$filename){
    	while (@ob_end_clean());
    	$len = @ filesize($file_path);
		$cont_dis ='attachment';

		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))  {
			ini_set('zlib.output_compression', 'Off');
		}
	
	    header("Pragma: public");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Expires: 0");
	
	    header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
			. ' filename="'.$filename.'";'
			. ' size=' . $len .';'
			); //RFC2183
	    header("Content-Length: "  . $len);
	    if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
		    @set_time_limit(0);
	    }
	    OSappscheduleOrders::readfile_chunked($file_path);
		exit();
    }
    
    
    static function readfile_chunked($filename,$retbytes=true){
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
   			return false;
		}
		while (!feof($handle)) {
	   		$buffer = fread($handle, $chunksize);
	   		echo $buffer;
			@ob_flush();
			flush();
	   		if ($retbytes) {
	       		$cnt += strlen($buffer);
	   		}
		}
   		$status = fclose($handle);
	    if ($retbytes && $status) {
   			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	public static function manageCustomers()
	{
		global $mainframe,$jinput,$mapClass;
		$db			= JFactory::getDbo();
		$config		= new JConfig();
		$list_limit	= $config->list_limit;
		$show_form  = 0;
		// filter sort
		$filter_order 		= $jinput->get('filter_order','order_name','string');
		$filter_order_Dir 	= $jinput->get('filter_order_Dir','asc','string');
		$lists['order'] 	= $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		$order_by 			= " ORDER BY $filter_order $filter_order_Dir";
		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $list_limit, 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$keyword 	= $mainframe->getUserStateFromRequest($option.'.customers.keyword','keyword','','string');
		$lists['keyword']  	= $keyword;

		$type 		= $mainframe->getUserStateFromRequest($option.'.customers.type','type','','int');

		$optionArr[] = JHtml::_('select.option','0',JText::_('OS_SELECT_TYPE'));
		$optionArr[] = JHtml::_('select.option','1',JText::_('OS_REGISTERED'));
		$optionArr[] = JHtml::_('select.option','2',JText::_('OS_GUEST'));
		$lists['type'] = JHtml::_('select.genericlist', $optionArr, 'type', 'onChange="javascript:document.adminForm.submit()" class="input-medium imedium form-select"','value','text', $type);

		jimport('joomla.html.pagination');

		$query		= "Select count(id) from #__app_sch_userprofiles where 1=1";
		if($keyword != '')
		{
			$show_form = 1;
			$query .= " (";
			$query .= " order_name like '%".$keyword."%' or";
			$query .= " order_name like '%".$keyword."%' or";
			$query .= " order_phone like '%".$keyword."%' or";
			$query .= " order_city like '%".$keyword."%' or";
			$query .= " order_zip like '%".$keyword."%' or";
			$query .= " order_state like '%".$keyword."%' or";
			$query .= " order_country like '%".$keyword."%'";
			$query .= " )";
		}
		if($type > 0)
		{
			$show_form = 1;
			switch ($type)
			{
				case "1":
					$query .= " and user_id > 0";
				break;
				case "2":
					$query .= " and user_id = 0";
				break;
			}
		}
		$db->setQuery($query);
		$total		= $db->loadResult();
		$pageNav	= new JPagination($total,$limitstart,$limit);
		$query		= "Select * from #__app_sch_userprofiles where 1=1";
		if($keyword != '')
		{
			$query .= " (";
			$query .= " order_name like '%".$keyword."%' or";
			$query .= " order_name like '%".$keyword."%' or";
			$query .= " order_phone like '%".$keyword."%' or";
			$query .= " order_city like '%".$keyword."%' or";
			$query .= " order_zip like '%".$keyword."%' or";
			$query .= " order_state like '%".$keyword."%' or";
			$query .= " order_country like '%".$keyword."%'";
			$query .= " )";
		}
		if($type > 0)
		{
			switch ($type)
			{
				case "1":
					$query .= " and user_id > 0";
				break;
				case "2":
					$query .= " and user_id = 0";
				break;
			}
		}
		$query     .= $order_by;
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows 		= $db->loadObjectList();

		$lists['show_form'] = $show_form;
		HTML_OSappscheduleOrders::manageCustomers($rows, $pageNav, $lists);
	}
	
	/**
	 * agent list
	 *
	 * @param unknown_type $option
	 */
	static function orders_list($option)
    {
		global $mainframe,$jinput,$mapClass;
		$mainframe = JFactory::getApplication();
		$config						= new JConfig();
		$list_limit					= $config->list_limit;
		$db = JFactory::getDBO();
		$lists = array();
		$condition = '';
		$show_form = 0;

		// filter sort
		$filter_order 				= $jinput->get('filter_order','a.id','string');
		$filter_order_Dir 			= $jinput->get('filter_order_Dir','desc','string');
		$lists['order'] 			= $filter_order;
		$lists['order_Dir'] 		= $filter_order_Dir;
		$order_by 					= " ORDER BY $filter_order $filter_order_Dir";
		
		// Get the pagination request variables
		$limit						= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $list_limit, 'int' );
		$limitstart					= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		// search 
		$keyword 			 		= $mainframe->getUserStateFromRequest($option.'.orders.keyword','keyword','','string');
		$lists['keyword']  			= $keyword;
		if($keyword != "")
		{
			$condition 			   .= " AND (";
			$condition 			   .= " a.order_name LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_email LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_phone LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_country LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_city LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_state LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_zip LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_address LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_upfront LIKE '%$keyword%'";
			$condition 			   .= " OR a.order_date LIKE '%$keyword%'";
			$condition 			   .= " OR a.id LIKE '%$keyword%'";
			$condition 			   .= " )";
		}
			
		// filter state
		$filter_status 				= $mainframe->getUserStateFromRequest($option.'.orders.filter_status','filter_status','','string');
		$condition 				   .= ($filter_status != '')? " AND a.order_status = '$filter_status'":"";
		
		$lists['filter_status'] 	= OSBHelper::buildOrderStaticDropdownList($filter_status,"onChange='javascript:submitOrdersForm();'",JText::_('OS_SELECT_ORDER_STATUS'),'filter_status');
		
		$lists['order_status']		= array('P'=>'<span color="orange">'.JText::_('OS_PENDING').'</span>', 'S'=>'<span color="green">'.JText::_('OS_COMPLETE').'</span>', 'C'=>'<span color="red">'.JText::_('OS_CANCEL').'</span>');
		
		// filter date
		//$filter_date_from			= $jinput->getString('filter_date_from','');
		$filter_date_from 			 = $mainframe->getUserStateFromRequest($option.'.orders.filter_date_from','filter_date_from','','string');
		$lists['filter_date_from']	= $filter_date_from;	
		if ($filter_date_from != '' )
		{
			$show_form				 = 1;	
			$condition 				.= " AND b.booking_date >= '".$filter_date_from." 00:00:00'";
		}
		//$filter_date_to				= $jinput->getString('filter_date_to','');
		$filter_date_to 			 = $mainframe->getUserStateFromRequest($option.'.orders.filter_date_to','filter_date_to','','string');
		$lists['filter_date_to']	 = $filter_date_to;	
		if ($filter_date_to != '' )
		{
			$show_form				 = 1;	
			$condition 				.= " AND b.booking_date <= '".$filter_date_to." 00:00:00'";
		}
		// filter extra
		$add_query 				    = '';
        $filter_venue 				= $mainframe->getUserStateFromRequest($option.'.orders.filter_venue','filter_venue',0,'int');
		$filter_service 		    = $mainframe->getUserStateFromRequest($option.'.orders.filter_service','filter_service',0,'int');
		$filter_employee 			= $mainframe->getUserStateFromRequest($option.'.orders.filter_employee','filter_employee',0,'int');
		if ($filter_service || $filter_employee || $filter_date_from || $filter_date_to || $filter_venue || $filter_status)
		{
			$show_form				= 1;	
			$add_query 				= " INNER JOIN #__app_sch_order_items AS b ON a.id = b.order_id ";
			$condition 			    .= $filter_service? " AND b.sid = '$filter_service' ":'';
			$condition 				.= $filter_employee? " AND b.eid = '$filter_employee' ":'';
			$condition              .= $filter_venue? " and b.sid in (Select sid from #__app_sch_venue_services where vid = '$filter_venue')":'';
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
		$lists['filter_service']	= JHtml::_('select.genericlist',$options,'filter_service','class="input-medium form-select ilarge" onchange="javascript:submitOrdersForm();" ','value','text',$filter_service);
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
		array_unshift($options,JHtml::_('select.option',0,JText::_('OS_FILTER_EMPLOYEE')));
		$lists['filter_employee']	= JHtml::_('select.genericlist',$options,'filter_employee','class="input-medium form-select ilarge" onchange="javascript:submitOrdersForm();" ','value','text',$filter_employee);

		if($filter_service)
        {
            $extraServiceSql        = " and id in (Select vid from #__app_sch_venue_services where sid = '$filter_service')";
        }

        $db->setQuery("Select id as value, concat(address,' ',city,' ',state) as text from #__app_sch_venues where published =  '1' $extraServiceSql order by address");
        $venues                     = $db->loadObjectList();
        if(count($venues) > 0)
        {
            $options                = array();
            $options[]              = JHtml::_('select.option',0,JText::_('OS_FILTER_VENUES'));
            $options                = array_merge($options,$venues);
            $lists['filter_venue']  = JHtml::_('select.genericlist', $options,'filter_venue','class="input-medium form-select ilarge" onchange="javascript:submitOrdersForm();" ','value','text',$filter_venue);
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
		$list 					   .= " group by a.id ".$order_by;
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		//echo $db->getQuery();
		$rows 						= $db->loadObjectList();
		$lists['show_form']			= $show_form;
		HTML_OSappscheduleOrders::orders_list($option,$rows,$pageNav,$lists);
	}
	
	
	
	/**
	 * * remove agent
	 * * @param unknown_type $option
	 * * @param unknown_type $cid
	 *
	 **/	
	static function orders_remove($option,$cid){
		global $mainframe,$configClass;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		if(count($cid)>0)	{
			if($configClass['integrate_gcalendar'] == 1){
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
		}
		
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OSappscheduleOrders::orders_list($option);
	}
	
	
	/**
	 * Service modify
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function orders_detail($option,$id){
		global $mainframe;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);	
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDbo();
		$row 		= &JTable::getInstance('Order','OsAppTable');
		
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
		$lists['country'] = JHTML::_('select.genericlist',$countryArr,'order_country','class="'.$mapClass['input-large'].' form-select"','value','text',$row->order_country);

		$db->setQuery("Select name as value, title as text from #__app_sch_plugins where published = '1'");
		$paymentplugins = $db->loadObjectList();
		$optionArr = array();
		$optionArr[] = JHTML::_('select.option','','Select payment method');
		$optionArr = array_merge($optionArr,$paymentplugins);
		$lists['payment'] = JHTML::_('select.genericlist',$optionArr,'order_payment','class="form-select"','value','text',$row->order_payment);

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
		$lists['order_date_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'order_date_hour', ' class="inputbox input-mini form-select" ', $selectedHour);
		$lists['order_date_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'order_date_minute', ' class="inputbox input-mini form-select" ', $selectedMinute, '%02d');

		HTML_OSappscheduleOrders::orders_detail($option,$row,$rows,$pageNav,$fields,$lists);
	}
	
	
	/**
	 * publish or unpublish agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function orders_save($option,$save)
	{
		global $mainframe,$jinput,$configClass;
		$db = JFactory::getDbo();
		require_once(JPATH_ROOT."/components/com_osservicesbooking/helpers/common.php");
		$mainframe 			= JFactory::getApplication();
		$id 				= $jinput->getInt('id',0);
		if($id == 0)
		{
			$newOrder		= true;
		}
		else
		{
			$newOrder		= false;
		}
		$old_status         = $jinput->get('old_status','P','string');
        $order_status       = $jinput->get('order_status','P','string');
		$row				= &JTable::getInstance('Order','OsAppTable');
		$row->load((int) $id);
		$post				= $jinput->post->getArray();
		$row->bind($post);
		$row->order_notes	= $_POST['notes'];
		$row->order_status	= $jinput->get('order_status','P','string');
		$row->order_date	= $jinput->get('order_date','0000-00-00','string')." ".$jinput->getInt('order_date_hour',0).":".$jinput->getInt('order_date_minute',0).":00";
		$row->receive_reminder	= $jinput->getInt('receive_reminder',0);
		$row->order_total	= $jinput->getFloat('order_total',0);
		$row->order_tax		= $jinput->getFloat('order_tax',0);
		$row->order_final_cost = $jinput->getFloat('order_final_cost',0);
		$row->order_upfront = $jinput->getFloat('order_upfront',0);
		$row->payment_fee	= $jinput->getFloat('payment_fee',0);
		$row->order_discount	= $jinput->getFloat('order_discount',0);
		$row->order_lang	= (string) $row->order_lang;
		$row->order_card_number = (string) $row->order_card_number;
		$row->order_card_type = (string) $row->order_card_type;
		$row->bank_id		= (string) $row->bank_id;
		$row->params		= (string) $row->params;
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
			$id				= $db->insertID();
		}

		$profile			= JTable::getInstance('Profile','OsAppTable');
		$userId				= $row->user_id;
		if($userId > 0)
		{
			$db->setQuery("Select count(id) from #__app_sch_userprofiles where user_id = '$userId'");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select id from #__app_sch_userprofiles where user_id = '$userId'");
				$profileId = $db->loadResult();
				$profile->id = $profileId;
			}
			else
			{
				$profile->id = 0;
			}
			$profile->user_id 		= $userId;
			$profile->order_name 	= $row->order_name;
			$profile->order_email 	= $row->order_email;
			$profile->order_phone 	= $row->order_phone;
			$profile->order_country = $row->order_country;
			$profile->order_address = $row->order_address;
			$profile->order_state 	= $row->order_state;
			$profile->order_city 	= $row->order_city;
			$profile->order_zip 	= $row->order_zip;
			$profile->store();

			$userProfilePluginEnabled = JPluginHelper::isEnabled('user', 'profile');
			if($configClass['field_integration'] == 1 && (int) $userId > 0 && $userProfilePluginEnabled)
			{
				$coreFields = array('address','city','country','zip','phone');
				foreach($coreFields as $cfield)
				{
					$mappingField = $configClass[$cfield.'_mapping'];
					if($mappingField != "")
					{
						if($profile->{'order_'.$cfield} != "")
						{
							OSBHelper::updateUserProfile($userId, $mappingField , $profile->{'order_'.$cfield});
						}
						else
						{
							OSBHelper::updateOrderFieldUserProfile($row->id, $userId, $mappingField , $cfield);
						}
					}
				}
			}
			elseif($configClass['field_integration'] == 2 && (int) $userId > 0)
			{
				$coreFields = array('address','city','country','zip','phone');
				foreach($coreFields as $cfield)
				{
					$mappingField = $configClass[$cfield.'_mapping'];
					if($mappingField != "")
					{
						if($profile->{'order_'.$cfield} != "")
						{
							OSBHelper::updateUserProfile($userId, $mappingField , $profile->{'order_'.$cfield});
						}
						else
						{
							OSBHelper::updateOrderFieldUserProfile($row->id, $userId, $mappingField , $cfield);
						}
					}
				}
			}
		}
		else
		{
			//in case this is non-registered user
			$db->setQuery("Select count(id) from #__app_sch_userprofiles where order_name like '$row->order_name' and order_email like '$row->order_email'");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select id from #__app_sch_userprofiles where order_name like '$row->order_name' and order_email like '$row->order_email'");
				$profileId = $db->loadResult();
				$profile->id = $profileId;
			}
			else
			{
				$profile->id = 0;
			}
			$profile->user_id 		= 0;
			$profile->order_name 	= $row->order_name;
			$profile->order_email 	= $row->order_email;
			$profile->order_phone 	= $row->order_phone;
			$profile->order_country = $row->order_country;
			$profile->order_address = $row->order_address;
			$profile->order_state 	= $row->order_state;
			$profile->order_city 	= $row->order_city;
			$profile->order_zip 	= $row->order_zip;
			$profile->store();
		}

		//save extra fields
		$db->setQuery("Delete from #__app_sch_order_options where order_id = '$id'");
		$db->execute();
		$db->setQuery("Delete from #__app_sch_field_data where order_id = '$id'");
		$db->execute();
		$db->setQuery("Select * from #__app_sch_fields where published = '1' and field_area = '1'");
		$fields = $db->loadObjectList();
		if(count($fields) > 0)
		{
			for($i=0;$i<count($fields);$i++)
			{
				$field = $fields[$i];
				$field_id = $field->id;
				$field_type = $field->field_type;
				$field_name = "field_".$field_id;
				if($field_type == 0)
				{
					$field_value = $jinput->get($field_name,'','string');
					if($field_value != "")
					{
						$db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$field_value')");
						$db->execute();
					}
					if(($configClass['field_integration'] == 1 || $configClass['field_integration'] == 2) && $field->field_mapping != "")
					{
						if($field_value != "")
						{
							OSBHelper::updateUserProfile($userId, $field->field_mapping, $field_value);
						}
						else
						{
							OSBHelper::updateOrderCustomFieldUserProfile($row->id, $userId, $field->field_mapping , $field);
						}
					}
				}
				elseif($field_type == 3)
				{
					$photo_name = "field_".$field_id;
					$old_photo_name = "old_field_".$field_id;
					$old_photo_name = $jinput->get($old_photo_name,'','string');
					$fvalue = "";
					$field_data = "";
					if(is_uploaded_file($_FILES[$photo_name]['tmp_name']))
					{
						if(OSBHelper::checkIsPhotoFileUploaded($photo_name))
						{
							$image_name = $_FILES[$photo_name]['name'];
							$image_name = OSBHelper::processImageName($id.time().$image_name);
							$original_image_link = JPATH_ROOT."/images/osservicesbooking/fields/".$image_name;
							move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
							$fvalue = $image_name;
						}
					}
					$remove_picture = "remove_picture_".$field_id;
					$remove_picture = $jinput->getInt($remove_picture,0);
					if($remove_picture == 1 && $fvalue != "")
					{
						$db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$fvalue')");
						$db->execute();
					}
					elseif($fvalue != "")
					{
						$db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$fvalue')");
						$db->execute();
					}
					elseif($remove_picture == 0 && $old_photo_name != "")
					{
						$db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$old_photo_name')");
						$db->execute();
					}
				}
				elseif($field_type == 4)
				{
					$photo_name = "field_".$field_id;
					$old_photo_name = "old_field_".$field_id;
					$old_photo_name = $jinput->get($old_photo_name,'','string');
					$fvalue = "";
					$field_data = "";
					if(is_uploaded_file($_FILES[$photo_name]['tmp_name']))
					{
						if(OSBHelper::checkIsFileUploaded($photo_name))
						{
							$image_name = $_FILES[$photo_name]['name'];
							$image_name = OSBHelper::processImageName($id.time().$image_name);
							$original_image_link = JPATH_ROOT."/images/osservicesbooking/fields/".$image_name;
							move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
							$fvalue = $image_name;
						}
					}
					$remove_picture = "remove_picture_".$field_id;
					$remove_picture = $jinput->getInt($remove_picture,0);
					if($remove_picture == 1 && $fvalue != "")
					{
						$db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$fvalue')");
						$db->execute();
					}
					elseif($fvalue != "")
					{
						$db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$fvalue')");
						$db->execute();
					}
					elseif(($remove_picture == 0) && ($old_photo_name != ""))
					{
						$db->setQuery("INSERT INTO #__app_sch_field_data (id,order_id, 	fid, fvalue) VALUES (NULL,'$id','$field_id','$old_photo_name')");
						$db->execute();
					}
				}
				else
				{
					$field_value = $jinput->get($field_name,'','string');
					if($field_value != "")
					{
						$field_value_array = explode(",",$field_value);
						//print_r($field_value_array);
						if(count($field_value_array) > 0)
						{
							for($j=0;$j<count($field_value_array);$j++)
							{
								$value = $field_value_array[$j];
								$db->setQuery("INSERT INTO #__app_sch_order_options (id,order_id,field_id,option_id) VALUES (NULL,'$id','$field_id','$value')");
								//echo $db->getQuery();
								$db->execute();
							}
						}
					}
				}
			}
		}

		if($newOrder == false)
		{
			OSBHelper::sendEmailAfterSavingOrder($id,$order_status,$old_status);
		}

		//add qrcode
		if($configClass['use_qrcode'])
		{
			OSBHelper::generateQrcode($id);
		}
		
		if ($save || !$id)
		{
			$msg = JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED");
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_list");
		}
		else
		{
			$msg = JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED");
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_detail&cid[]=".$id);
		}		
	}
	
	public static function updateNewOrderStatus()
    {
		global $configClass,$jinput,$mapClass,$mainframe;
		$configClass	= OSBHelper::loadConfig();
		require_once(JPATH_ROOT."/components/com_osservicesbooking/helpers/common.php");
		$db				= Jfactory::getDbo();
		$order_id		= $jinput->getInt('order_id',0);
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		$row			= $db->loadObject();
		$old_status		= $row->order_status;
		if($jinput->get('new_status','P','string') != $old_status)
		{
			$db->setQuery("Update #__app_sch_orders set order_status = '".$jinput->get('new_status','P','string')."' where id = '$order_id'");
			$db->execute();

			if(($jinput->get('new_status','P','string') == "S") && $old_status != "S")
			{
				if($row->send_email == 0)
				{
					HelperOSappscheduleCommon::sendEmail("confirm",$order_id);
					HelperOSappscheduleCommon::sendSMS('confirm',$order_id);
				}
				HelperOSappscheduleCommon::sendEmployeeEmail('employee_notification_new',$order_id,0);				
                HelperOSappscheduleCommon::sendSMS('confirmtoEmployee',$order_id);
				$results = array();
				$results = $mainframe->triggerEvent('onOrderActive', array($row));
				OSBHelper::updateGoogleCalendar($order_id);
			}
			
			if(($jinput->get('new_status','P','string') == "C") && $old_status != "C")
			{
				HelperOSappscheduleCommon::sendCancelledEmail($order_id);
				HelperOSappscheduleCommon::sendSMS('cancel',$order_id);
				HelperOSappscheduleCommon::sendEmail('customer_cancel_order',$order_id);
				HelperOSappscheduleCommon::sendEmployeeEmail('employee_order_cancelled_new',$order_id,0);
				if($configClass['integrate_gcalendar'] == 1)
				{
					OSBHelper::removeEventOnGCalendar($order_id);
				}
				if($configClass['waiting_list'] == 1)
				{
					OSBHelper::sendWaitingNotification($order_id);
				}
				
			}
			
			//Send alert email
			if($old_status != $jinput->get('new_status','P','string') && $jinput->get('new_status','P','string') != "C")
			{
				HelperOSappscheduleCommon::sendEmail("order_status_changed_to_customer",$order_id);
				//HelperOSappscheduleCommon::sendEmployeeEmail('order_status_changed_to_employee',$order_id,0);
				HelperOSappscheduleCommon::sendSMS('order_status_changed_to_customer',$order_id);
			}
			//send thank you email when customer attended the service
			//echo $jinput->get('new_status','P','string');
			//echo "<BR />";
			//echo $old_status;
			//die();
            if($jinput->get('new_status','P','string') == "A" && $old_status != "A")
            {
                HelperOSappscheduleCommon::sendEmail('attended_thankyou_email',$order_id);
            }
		}
		
		$optionArr = array();
		$statusArr = array(JText::_('OS_PENDING'),JText::_('OS_COMPLETED'),JText::_('OS_CANCELED'),JText::_('OS_ATTENDED'),JText::_('OS_TIMEOUT'),JText::_('OS_DECLINED'),JText::_('OS_REFUNDED'));
		$statusVarriableCode = array('P','S','C','A','T','D','R');
		for($j=0;$j<count($statusArr);$j++)
		{
			$optionArr[] = JHtml::_('select.option',$statusVarriableCode[$j],$statusArr[$j]);				
		}
		echo "<span style='color:gray;'>".JText::_('OS_CURRENT_STATUS').": <strong>".OSBHelper::orderStatus(0,$jinput->get('new_status','P','string'))."</strong></span>";
		echo "<BR />";
		echo "<span style='color:gray;font-size:11px;'>".JText::_('OS_CHANGE_STATUS')."</span>";
		echo JHtml::_('select.genericlist',$optionArr,'orderstatus'.$row->id,'class="'.$mapClass['input-small'].' form-select"','value','text',$jinput->get('new_status','P','string'));
		?>
		<a href="javascript:updateOrderStatusAjax(<?php echo $row->id;?>,'<?php echo JUri::root();?>')">
			<i class="icon-edit"></i>
		</a>
		<?php
		exit();
	}
	
	public static function getUserInput($user_id,$order_id)
	{
		if (JFactory::getApplication()->isClient('site') || version_compare(JVERSION, '3.5', 'le'))
		{
			// Initialize variables.
			$html = array();
			
			
			// Initialize some field attributes.
			$attr = ' class="input-medium form-control"';

			// Load the modal behavior script.
			if(!OSBHelper::isJoomla4())
			{
				JHtml::_('behavior.modal');
				JHtml::_('behavior.modal', 'a.modal_user_id');
				if(JFactory::getApplication()->isClient('administrator'))
				{
					$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=user_id';
				}
				else
				{
					$link = JUri::root().'index.php?option=com_osservicesbooking&amp;task=manage_users&amp;tmpl=component&amp;field=user_id';
				}
				// Build the script.
				$script = array();
				$script[] = '	function jSelectUser_user_id(id, title) {';
				$script[] = '		var old_id = document.getElementById("user_id").value;';
				$script[] = '		if (old_id != id) {';
				$script[] = '			document.getElementById("user_id").value = id;';
				$script[] = '			document.getElementById("user_id_id").value = id;';
				$script[] = '			document.getElementById("user_id_name").value = title;';
				$script[] = '			var order_name = document.getElementById("order_name");';
				$script[] = '			order_name.value = title ;';
				$script[] = '			populateUserData();';
				$script[] = '		}';
				$script[] = '		SqueezeBox.close();';
				$script[] = '	}';
			}
			else
			{
				OSBHelperJquery::colorbox('a.modal_user_id');
				OSBHelperJquery::colorbox('modal_user_id');
				$link = JUri::root().'index.php?option=com_osservicesbooking&amp;task=manage_users&amp;tmpl=component&amp;field=user_id';
				// Build the script.
				$script = array();
				$script[] = '	function jSelectUser_user_id(id, title) {';
				$script[] = '		var old_id = document.getElementById("user_id").value;';
				$script[] = '		if (old_id != id) {';
				$script[] = '			document.getElementById("user_id").value = id;';
				$script[] = '			document.getElementById("user_id_id").value = id;';
				$script[] = '			document.getElementById("user_id_name").value = title;';
				$script[] = '			var order_name = document.getElementById("order_name");';
				$script[] = '			order_name.value = title ;';
				$script[] = '			populateUserData();';
				$script[] = '		}';
				$script[] = '		parent.jQuery.colorbox.close(); return false;';
				$script[] = '	}';
			}

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			// Load the current username if available.
			$table = JTable::getInstance('user');
			
			if ($user_id)
			{
				$table->load($user_id);
			}
			else
			{
				$table->username = JText::_('OS_SELECT_USER');
			}

			// Create a dummy text field with the user name.
			$html[] = '<span class="input-append input-group" style="width:320px;">';
			$html[] = '<input type="text" class="input-medium form-control"  id="user_id_name" value="'.htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') .'" disabled="disabled" size="35" /><a class="modal_user_id btn btn-primary" title="'.JText::_('JLIB_FORM_CHANGE_USER').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JLIB_FORM_CHANGE_USER').'</a>';
			$html[] = '</span>';

			// Create the real field, hidden, that stored the user id.
			$html[] = '<input type="hidden" id="user_id" name="user_id" value="'.$user_id.'" />';
			$html[] = '<input type="hidden" id="user_id_id" name="user_id_id" value="'.$user_id.'" />';

			return implode("\n", $html);
		}
		else
		{
			JHtml::_('jquery.framework');
			$field = JFormHelper::loadFieldType('User');
			$element = new SimpleXMLElement('<field />');
			$element->addAttribute('name', 'user_id');
			$element->addAttribute('class', 'readonly');
			if ($order_id == 0)
			{
				$element->addAttribute('onchange', 'populateUserData();');
			}
			$field->setup($element, $user_id);
			return $field->input;
		}
	}
	
	
	/**
	 * Save services
	 *
	 * @param unknown_type $option
	 */
	static function saveService($option, $redirect)
	{
		global $mainframe,$configClass,$jinput;
		$config     = new JConfig();
		$offset     = $config->offset;
		date_default_timezone_set($offset);
		jimport('joomla.filesystem.file');
		$db 		= JFactory::getDbo();
		$id			= $jinput->getInt('id',0);
		$item_id	= $id;
		$order_id 	= $jinput->getInt('order_id',0);
		$sid 		= $jinput->getInt('sid',0);
		$eid		= $jinput->getInt('eid',0);
		if($sid == 0 || $eid == 0)
		{
			//save complete
			$msg = JText::_('Please make sure you selected Service and Employee before saving order details');
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_detail&cid[]=".$order_id);
		}
		$vid		= $jinput->getInt('vid',0);
		$start_time = $jinput->getInt('start_time',0);
		$end_time	= $jinput->getInt('end_time',0);
		$nslots 	= $jinput->getInt('nslots',1);
		if($nslots == 0)
		{
			$nslots = 1;
		}
		$field_ids  = $jinput->get('field_ids'.$sid,'','string');
		$booking_date = $jinput->getString('booking_date','');
		
		
		//OSB 2.3.3. add
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee	= $db->loadObject();
		$client_id	= $employee->client_id;
		$app_name	= $employee->app_name;
		$app_email_address = $employee->app_email_address;
		$p12_key_filename = $employee->p12_key_filename;
		$gcalendarid = $employee->gcalendarid;

		//we should remove all events on Google Calendar first	
		if($id > 0)
		{
			OSBHelper::removeOneEventOnGCalendar($id);
		}

		$db->setQuery("Select a.*,b.additional_price from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.id = '$eid' and b.service_id = '$sid'");
		$employee		= $db->loadObject();
		
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service		= $db->loadObject();
		$service_name	= $service->service_name;

		$repeat_type	= 0;
		$repeat_to		= 0;
		if($service->repeat_day == 1  || $service->repeat_week == 1  || $service->repeat_fortnight == 1 || $service->repeat_month == 1)
		{
			$repeat_type = $jinput->getInt('repeat_type', 0);
			$repeat_to   = $jinput->getInt('repeat_to', 0);
		}
		
		$selected_timeslots = $jinput->get('selected_timeslots',array(),'array');
		if(count($selected_timeslots) > 0)
		{
			for($t = 0;$t<count($selected_timeslots);$t++)
			{
				$timeslot		= $selected_timeslots[$t];
				$timeslotArr	= explode("-",$timeslot);
				$nslots			= $jinput->getInt("nslots".$timeslot,1);
				$start_time		= $timeslotArr[0];
				$end_time		= $timeslotArr[1];
				$row			= &JTable::getInstance('OrderItem','OsAppTable');
				$row->bind($_POST);			
				$row->id		= (int)$item_id;
				$row->vid		= $vid;
				$row->start_time = $start_time;
				$row->end_time	= $end_time;
				$row->nslots	= $nslots;
				$row->booking_date = date("Y-m-d", $start_time);
				$row->additional_information = '';
				$row->gcalendar_event_id = '';

				$row->total_cost = 0;

				if(!$row->store())
				{
					throw new Exception($row->getError());
				}
				
				if($id > 0)
				{
					$order_item_id = $id;
				}
				else
				{
					$order_item_id = $db->insertid();
					$id = $order_item_id;
				}
				
				if($configClass['integrate_gcalendar'] == 1 && $client_id != "" && $app_name != "" && $app_email_address != "" && $gcalendarid != "" && $p12_key_filename != "" && JFile::exists(JPATH_COMPONENT_SITE."/".$p12_key_filename) )
				{
					OSBHelper::addEventonGCalendar(trim($client_id),trim($app_name),trim($app_email_address),trim($p12_key_filename),trim($gcalendarid),$service_name,$start_time,$end_time,$booking_date,$order_item_id,$order_id);
				}
				
				if($id > 0)
				{
					$db->setQuery("Delete from #__app_sch_order_field_options where order_item_id = '$id'");
					$db->execute();
				}
				$extracost = 0 ;

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

										$db->setQuery("Select additional_price from #__app_sch_field_options where id = '".$field_value_array[$j]."'");
										$field_additional_price = (float) $db->loadResult();
										$extracost += $field_additional_price;
									}
								}
							}
						}
					}
				}
				$timeslots_cost = OSBHelper::returnServicePrice($sid,$booking_date,$nslots,$eid,false)*$nslots;
				$other_cost	= ($employee->additional_price + $extracost)*$nslots;
				$total_cost = $timeslots_cost + $other_cost;
				$db->setQuery("Update #__app_sch_order_items set `total_cost` = '$total_cost' where id = '$order_item_id'");
				$db->execute();

				if(($service->repeat_day == 1  || $service->repeat_week == 1  || $service->repeat_fortnight == 1 || $service->repeat_month == 1) && $repeat_type > 0 && $repeat_to > 0)
				{
					$repeatDate     = HelperOSappscheduleCalendar::calculateBookingDate($booking_date,$repeat_to,$repeat_type);
					if(count($repeatDate))
					{
						$rows = [];
						foreach($repeatDate as $date)
						{
							$row				= new \stdClass();
							$row->start_time	= strtotime($date." ".date("H:i:s", $start_time));
							$row->end_time		= strtotime($date." ".date("H:i:s", $end_time));
							$row->booking_date	= $date;
							$row->nslots		= $nslots;
							$row->sid			= $sid; 
							$row->eid			= $eid;
							$row->vid			= (int) $vid;
							if(HelperOSappscheduleCalendar::checkSlots($row))
							{
								$repeatRow				= &JTable::getInstance('OrderItem','OsAppTable');
								$repeatRow->bind($_POST);			
								$repeatRow->id			= (int)$item_id;
								$repeatRow->vid			= $vid;
								$repeatRow->start_time	= $row->start_time;
								$repeatRow->end_time	= $row->end_time;
								$repeatRow->nslots		= $nslots;
								$repeatRow->booking_date		= $date;
								$repeatRow->additional_information = '';
								$repeatRow->gcalendar_event_id = '';

								$repeatRow->total_cost	= 0;

								if(!$repeatRow->store())
								{
									throw new Exception($repeatRow->getError());
								}
								
								$repeat_order_item_id = $db->insertid();
								//$id = $repeat_order_item_id;

								if($configClass['integrate_gcalendar'] == 1 && $client_id != "" && $app_name != "" && $app_email_address != "" && $gcalendarid != "" && $p12_key_filename != "" && JFile::exists(JPATH_COMPONENT_SITE."/".$p12_key_filename) )
								{
									OSBHelper::addEventonGCalendar(trim($client_id),trim($app_name),trim($app_email_address),trim($p12_key_filename),trim($gcalendarid),$service_name,$start_time,$end_time,$booking_date,$repeat_order_item_id,$order_id);
								}
								
								if($repeat_order_item_id > 0)
								{
									$db->setQuery("Delete from #__app_sch_order_field_options where order_item_id = '$repeat_order_item_id'");
									$db->execute();
								}
								$extracost = 0 ;

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
														$db->setQuery("INSERT INTO #__app_sch_order_field_options (id, order_item_id,field_id, option_id) VALUES (NULL,'$repeat_order_item_id','$field','".$field_value_array[$j]."')");
														$db->execute();

														$db->setQuery("Select additional_price from #__app_sch_field_options where id = '".$field_value_array[$j]."'");
														$field_additional_price = (float) $db->loadResult();
														$extracost += $field_additional_price;
													}
												}
											}
										}
									}
								}
								$timeslots_cost = OSBHelper::returnServicePrice($sid,$booking_date,$nslots,$eid,false)*$nslots;
								$other_cost	= ($employee->additional_price + $extracost)*$nslots;
								$total_cost = $timeslots_cost + $other_cost;
								$db->setQuery("Update #__app_sch_order_items set `total_cost` = '$total_cost' where id = '$repeat_order_item_id'");
								$db->execute();
							}
						}
					}
				}
			}
		}
		
		self::updateOrderCost($order_id);
		
		//save complete
		$msg = JText::_('OS_NEW_SERVICE_HAS_BEEN_ADDED_TO_ORDER');
		$mainframe->enqueueMessage($msg);
		if($redirect == 1)
		{
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_detail&cid[]=".$order_id);
		}
		else
		{
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_editservice&id=".$id);
		}
	}
	
	/**
	 * Remove service
	 *
	 * @param unknown_type $option
	 */
	static function removeService($option)
	{
		global $mainframe,$configClass,$jinput;
		$db			= JFactory::getDbo();
		$order_id	= $jinput->getInt('order_id',0);
		$id			= $jinput->getInt('id',0);
		jimport('joomla.filesystem.file');
		$db->setQuery("Select eid from #__app_sch_order_items where id = '$id'");
		$eid		= $db->loadResult();
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee	= $db->loadObject();
		$client_id	= $employee->client_id;
		$app_name	= $employee->app_name;
		$app_email_address = $employee->app_email_address;
		$p12_key_filename = $employee->p12_key_filename;
		$gcalendarid = $employee->gcalendarid;
		if(($configClass['integrate_gcalendar'] == 1) and ($client_id != "") and ($app_name != "")and ($app_email_address != "") and ($gcalendarid != "") and ($p12_key_filename != "") and (JFile::exists(JPATH_COMPONENT_SITE."/".$p12_key_filename)) )
		{
			OSBHelper::removeOneEventOnGCalendar($id);
		}
		if($configClass['waiting_list'] == 1)
		{
			OSBHelper::sendWaitingNotificationItem($id);
		}
		HelperOSappscheduleCommon::sendEmail('order_item_cancelled_to_administrator',$id);
		HelperOSappscheduleCommon::sendEmail('order_item_cancelled_to_employee',$id);
		HelperOSappscheduleCommon::sendEmail('order_item_cancelled_to_customer',$id);

		$db->setQuery("Delete from #__app_sch_order_items where id = '$id'");
		$db->execute();
		$db->setQuery("Delete from #__app_sch_order_field_options where order_item_id = '$id'");
		$db->execute();

		self::updateOrderCost($order_id);

		//remove complete
		$msg = JText::_('OS_SERVICE_HAS_BEEN_REMOVED');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_detail&cid[]=".$order_id);
	}

	public static function updateOrderCost($order_id)
	{
		global $mainframe, $configClass;
		$db				= JFactory::getDbo();
		$order_price	= self::getOrderCostOrderItem($order_id);


		$query = "Select sum(a.additional_price) from #__app_sch_field_options as a left join #__app_sch_order_options as b on b.field_id = a.field_id where b.order_id = '$order_id'";
		$db->setQuery($query);
		$field_cost = (float) $db->loadResult();

		$order_price += $field_cost;

		$tax			= round((float)$configClass['tax_payment']*$order_price/100,2);
		$order_total	= $order_price + $tax;

		$db->setQuery("Update #__app_sch_orders set order_total = '$order_price', order_tax = '$tax', order_final_cost = '$order_total' where id = '$order_id'");
		$db->execute();
	}

	public static function getOrderCostOrderItem($order_id)
	{
		global $mainframe,$jinput;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$total = 0;
		
		$db->setQuery("SELECT * FROM #__app_sch_order_items WHERE order_id = '$order_id'");
		$rows = $db->loadObjectList();

		for($i1=0;$i1<count($rows);$i1++)
		{
			$total += $rows[$i1]->total_cost;
		}
		
		return $total;
	}
	
	/**
	 * Send the notification emails to customers
	 *
	 * @param unknown_type $cid
	 */
	static function sendnotifyEmails($cid){
		global $mainframe,$configClass;
		require_once(JPATH_ROOT."/components/com_osservicesbooking/helpers/common.php");
		if(count($cid) > 0){
			for($i=0;$i<count($cid);$i++){
				$order_id = $cid[$i];
				HelperOSappscheduleCommon::sendEmail('confirm',$order_id);
			}
		}
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=orders_list");
	}
	
	/**
	 * Export Report
	 *
	 */
	static function exportReport($html)
	{
		global $mainframe,$jinput, $configClass;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);	
		$db = JFactory::getDbo();
		$date_from = $jinput->get('date_from','','string');
		$date_to   = $jinput->get('date_to','','string');
		$sid	   = $jinput->get('sid',array(),'array');//$jinput->getInt('sid',0);
		$eid	   = $jinput->get('eid',array(),'array');
		$order_status = $jinput->get('order_status','','string');
		
		$query = "Select a.*,b.*,a.id as order_item_id,c.service_name,c.service_time_type,d.employee_name from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id inner join #__app_sch_services as c on c.id = a.sid  inner join #__app_sch_employee as d on d.id = a.eid where 1=1";
		if(count($sid) > 0){
			$query .= " and a.sid in (".implode(",", $sid).")";
		}
		if(count($eid) > 0){
			$query .= " and a.eid in (".implode(",", $eid).")";
		}
		if($date_from != ""){
			$query .= " and a.booking_date >= '".$date_from."'";
		}
		if($date_to != ""){
			$query .= " and a.booking_date <= '".$date_to."'";
		}
		if($order_status != ""){
			$query .= " and b.order_status = '$order_status'";
		}
		$query .= " order by a.start_time";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if($html == 0)
		{
			//export report to csv
			$csv_separator = $configClass['csv_separator'];
			$header = '"NUM"'.$csv_separator.'"'.JText::_('OS_SERVICE').'"'.$csv_separator.'"'.JText::_('OS_EMPLOYEE').'"'.$csv_separator.'"'.JText::_('OS_FROM').'"'.$csv_separator.'"'.JText::_('OS_TO').'"'.$csv_separator.'"'.JText::_('OS_BOOKING_DATE').'"'.$csv_separator.'"'.JText::_('OS_ORDER').'"'.$csv_separator.'"'.JText::_('OS_CUSTOMER').'"'.$csv_separator.'"'.JText::_('OS_OTHER_INFORMATION').'"'.$csv_separator.'"'.JText::_('OS_STATUS').'"';

			if($configClass['disable_payments'] == 1)
			{
				if($configClass['enable_tax']==1 && (float) $configClass['tax_payment'] > 0)
				{
					$header .= $csv_separator.'"'.JText::_('OS_TAX').'"';
				}
				$header .= $csv_separator.'"'.JText::_('OS_TOTAL').'"';
			}
			
			$csv_content .= "\n";
			if(count($rows) > 0)
			{
				$taxAmount = 0;
				$totalAmount = 0;
				for($i=0;$i<count($rows);$i++)
				{
					$csv_content_temp = "";
					$row	= $rows[$i];
					$id		= $row->id;
					
					$num = $i + 1;
					$csv_content .= '"'.$num.'"'.$csv_separator.'"'.$row->service_name.'"'.$csv_separator.'"'.$row->employee_name.'"'.$csv_separator.'"'.date($configClass['time_format'],$row->start_time).'"'.$csv_separator.'"'.date($configClass['time_format'],$row->end_time).'"'.$csv_separator.'"'.date($configClass['date_format'],$row->start_time).'"'.$csv_separator.'"'.$row->order_id.'"'.$csv_separator.'"'.$row->order_name." (".$row->order_email.") ".$row->order_phone.'"'.$csv_separator;
					
					if($row->service_time_type ==1)
					{
						$csv_content_temp .= JText::_('OS_NUMBER_SLOT').": ".$row->nslots." || ";
					}
					$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and show_in_email = '1' and published = '1'");
					$fields = $db->loadObjectList();
					if(count($fields) > 0)
					{
						for($i1=0;$i1<count($fields);$i1++)
						{
							$field = $fields[$i1];
							$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$row->order_item_id' and field_id = '$field->id'");
							$count = $db->loadResult();
							if($count > 0)
							{
								if($field->field_type == 1)
								{
									$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->order_item_id' and field_id = '$field->id'");
									$option_id			= $db->loadResult();
									$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
									$optionvalue		= $db->loadObject();
									if($optionvalue->field_option != "")
									{
										$csv_content_temp .= $field->field_label.":";
										$csv_content_temp .= $optionvalue->field_option;
										if($optionvalue->additional_price > 0)
										{
											$csv_content_temp .=  " - ".OSBHelper::showMoney($optionvalue->additional_price,0);
										}
										$csv_content_temp .= " | ";
									}
								}
								elseif($field->field_type == 2)
								{
									$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->order_item_id' and field_id = '$field->id'");
									$option_ids = $db->loadObjectList();
									$fieldArr = array();
									for($j=0;$j<count($option_ids);$j++)
									{
										$oid = $option_ids[$j];
										$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
										$optionvalue = $db->loadObject();
										$field_data = $optionvalue->field_option;
										if($optionvalue->additional_price > 0)
										{
											$field_data.= " - ".OSBHelper::showMoney($optionvalue->additional_price,0);
										}
										$fieldArr[] = $field_data;
									}
									if(implode(", ",$fieldArr) != "")
									{
										$csv_content_temp .= $field->field_label.":";
										$csv_content_temp .= implode(", ",$fieldArr);
										$csv_content_temp .= " | ";
									}
								}
							}
						}
					}
					
					$csv_content .= '"'.$csv_content_temp.'"'.$csv_separator;
					$csv_content .= '"'.OSBHelper::orderStatus(0,$row->order_status).'"'.$csv_separator;
					if($configClass['disable_payments'] == 1)
					{
						if($configClass['enable_tax']==1 && (float) $configClass['tax_payment'] > 0)
						{
							$taxAmount += $row->order_tax;
							$csv_content .= '"'.$row->order_tax." ".$configClass['currency_format'].'"'.$csv_separator;
						}
						$totalAmount += $row->order_final_cost;
						$csv_content .= '"'.$row->order_final_cost." ".$configClass['currency_format'].'"';
					}
					$csv_content .= "\n";
				}
			}
			$csv_content .= $csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator;
			$csv_content .= '"'.JText::_('OS_TOTAL_TAX').'"'.$csv_separator.'" '.$taxAmount.' '.$configClass['currency_format'].'"';
			$csv_content .= "\n";
			$csv_content .= $csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator.$csv_separator;
			$csv_content .= '"'.JText::_('OS_TOTAL').'"'.$csv_separator.'" '.$totalAmount.' '.$configClass['currency_format'].'"';
			$csv_content .= "\n";

			$header = $header.$csv_content;
			//create the csv file
			$filename = time().".csv";
			$csv_absoluted_link = JPATH_ROOT."/tmp".DS.$filename;
			//create the content of csv
			$csvf = fopen($csv_absoluted_link,'w');
			@fwrite($csvf,$header);
			@fclose($csvf);
			OSappscheduleOrders::downloadfile2($csv_absoluted_link,$filename);
		}
		else
		{
		
			$lists['date_from'] = $date_from;
			$lists['date_to'] = $date_to;
			$lists['sid'] = $sid;
			$lists['eid'] = $eid;
			$lists['order_status'] = $order_status;
			
			HTML_OSappscheduleOrders::exportReport($rows,$lists);
		}
	}

	static function saveCustomerInfo($save)
	{
		global $mainframe, $jinput;
		$db		 = JFactory::getDbo();
		$profile = JTable::getInstance('Profile','OsAppTable');
		$id		 = $jinput->getInt('id', 0);
		if($id > 0)
		{
			$profile->load($id);
			$profile->notes = $jinput->getString('notes','');
			
			if (!$profile->store())
			{
				throw new Exception($row->profile(), 500);
			}
			$msg  = JText::_('OS_ITEM_HAS_BEEN_SAVED');
			if($save == 1)
			{
				$mainframe->redirect('index.php?option=com_osservicesbooking&task=orders_customers');
			}
			else
			{
				$mainframe->redirect('index.php?option=com_osservicesbooking&task=orders_customerdetails&id='.$id);
			}
		}
	}
}
?>