<?php
/*------------------------------------------------------------------------
# default.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class OsAppscheduleDefault{
	/**
	 * Osproperty default
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task)
    {
		global $mainframe,$mapClass,$jinput,$configClass;
		$db				= JFactory::getDbo();
		$config			= new JConfig();
		$offset			= $config->offset;
		date_default_timezone_set($offset);
		//remove the temporarily orders before 1 hour
		$current_time	= time();
		
		if($configClass['temporarily_time'] == '')
		{
			$configClass['temporarily_time'] = 5;
		}
		$last_one_hour	= $current_time - (int)$configClass['temporarily_time']*60;
		//echo $last_one_hour;die();
		$db->setQuery("Select id from #__app_sch_temp_orders where created_on < '$last_one_hour'");
		//$db->execute();
		$temp_ids		= $db->loadColumn(0);
		if(count($temp_ids) > 0)
		{
			$db->setQuery("Delete from #__app_sch_temp_orders where id in (".implode(",",$temp_ids).")");
			$db->execute();
			$db->setQuery("Delete from #__app_sch_temp_order_items where order_id in (".implode(",",$temp_ids).")");
			$db->execute();
		}

		if($configClass['remove_pending_orders'] == 1)
		{
			if($configClass['pending_remove'] == '')
			{
				$configClass['pending_remove'] = 3;
			}
			$last_one_hour	= $current_time - (int)$configClass['pending_remove']*3600;
			$last_one_hour	= date("Y-m-d H:i:s", $last_one_hour);
			$db->setQuery("Update #__app_sch_orders set order_status = 'C' where order_date < '$last_one_hour' and order_status = 'P'");
			$db->execute();
		}
		
		OSBHelper::banned();

		$document = JFactory::getDocument();
		$order_id = $jinput->getInt('id',0);
		if($order_id == 0)
		{
			$order_id = $jinput->getInt('order_id',0);
		}
		$date_from = OSBHelper::getStringValue('date_from','');
		$date_to   = OSBHelper::getStringValue('date_to','');
		if($date_from == "0000-00-00 00:00:00")
		{
			$jinput->set('date_from','');
		}
		if($date_to == "0000-00-00 00:00:00")
		{
			$jinput->set('date_to','');
		}
		switch ($task)
        {
			default:
				OsAppscheduleDefault::defaultLayout($option);
			break;
			case "default_completeorder":
				OsAppscheduleDefault::completeOrder($option);
			break;
			case "default_payment":
				OsAppscheduleDefault::paymentProcess($option);
			break;
			case "default_completeremainpayment":
				OsAppscheduleDefault::completeremainpayment($option);
			break;
			case "default_paymentconfirm":
			case "defaul_paymentconfirm":
				OsAppscheduleDefault::paymentNotify();
			break;
			case "default_paymentcancel":
				OsAppscheduleDefault::cancelPayment($order_id);
			break;
			case "default_paymentreturn":
				OsAppscheduleDefault::returnPayment($order_id);
			break;
			case "default_paymentfailure":
				OsAppscheduleDefault::paymentFailure($order_id);
			break;
			case "default_cron":
				OsAppscheduleDefault::cron();
			break;
			case "default_cancelorder":
				OsAppscheduleDefault::cancelOrder();
			break;
			case "default_paymentComplete":
				$order_id = $jinput->getInt('order_id',0);
				OsAppscheduleDefault::paymentComplete($order_id);
			break;
			case "default_orderDetails":
				$eid = $jinput->getInt('eid',0);
				OsAppscheduleDefault::orderDetails($order_id,$eid, false);
			break;
			case "default_orderDetailsForm":
				$ref = OSBHelper::getStringValue('ref','');
				$order_id = $jinput->getInt('order_id',0);
				if(md5($order_id) != $ref)
				{
					//throw new Exception (JText::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'));
					$session = Factory::getSession();
					$session->set('errorReason', JText::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'));
					OsAppscheduleDefault::redirecterrorform();
				}
				OsAppscheduleDefault::orderDetailsForm($order_id);
			break;
			case "default_employeeworks":
				OsAppscheduleDefault::employeeWorks();
			break;
			case "default_employeesetting":
				OsAppscheduleDefault::employeesetting();
			break;
			case "default_saveemployeesetting":
				OsAppscheduleDefault::saveemployeesetting();
			break;
			case "default_removeRestday":
				OsAppscheduleDefault::removeRestday();
			break;
			case "default_removeBusytime":
				OsAppscheduleDefault::removeBusytime();
			break;
			
			case "default_setupbreaktime":
				OsAppscheduleDefault::setupBreakTime();
			break;
			case "default_savebreaktime":
				OsAppscheduleDefault::saveBreaktime(1);
			break;
			case "default_applybreaktime":
				OsAppscheduleDefault::saveBreaktime(0);
			break;
			case "default_employeeworksexport":
				OsAppscheduleDefault::exportEmployeeWorks();
			break;
			case "default_orderDetails":
				$order_id = $jinput->getInt('order_id',0);
                $eid      = $jinput->getInt('eid',0);
				OsAppscheduleDefault::orderDetails($order_id,$eid, false);
			break;
			case "default_calculateBookingDate":
				HelperOSappscheduleCalendar::calculateBookingDate($from_date,$to_date,$type);
			break;
			case "default_failure":
				OsAppscheduleDefault::failure();
			break;
			case "default_customer":
				OsAppscheduleDefault::orderHistory();
			break;
			case "default_balances":
				OsAppscheduleDefault::customerBalances();
			break;
			case "default_removeorder":
				OsAppscheduleDefault::removeOrder($order_id);
			break;
			case "default_showmap":
				OsAppscheduleDefault::showMap();
			break;
			case "default_writecomment":
				OsAppscheduleDefault::showCommentForm();
			break;
			case "default_submitcomment":
				OsAppscheduleDefault::submitComment();
			break;
			//test//
			case "default_testrepeat":
				OsAppscheduleDefault::testRepeat();
			break;
			case "default_testdate":
				OsAppscheduleDefault::testDate();
			break;
			case "default_addEventToGCalendar":
				OsAppscheduleDefault::addEventToGCalendar();
			break;
			case "default_updateGoogleCalendar":
				OsAppscheduleDefault::updateGoogleCalendar($order_id);
			break;
			case "default_testsms":
				OsAppscheduleDefault::testSMS();
			break;
            case "default_sms1":
                OsAppscheduleDefault::testSMS1();
            break;
			case "default_allemployees":
				OsAppscheduleDefault::listAllEmployees();
			break;
			case "default_acymailing":
				HelperOSappscheduleCommon::updateAcyMailing(69);
			break;
			case "default_addtowaitinglist":
				OsAppscheduleDefault::addtowaitinglist();
			break;
			case "default_doaddtowaitinglist":
				OsAppscheduleDefault::doaddtowaitinglist();
			break;
			case "default_unsubwaitinglist":
				OsAppscheduleDefault::unsubwaitinglist();
			break;
            case "default_checkin":
                OsAppscheduleDefault::checkIn();
            break;
			case "default_changeReminderStatus":
				OsAppscheduleDefault::changeReminderStatus($order_id);
			break;
			case "default_qrscan":
				OsAppscheduleDefault::qrScan();
			break;
			case "default_qrcodecheckin":
				OsAppscheduleDefault::qrcodecheckin();
			break;
			case "default_testemail":
				HelperOSappscheduleCommon::sendEmail('reminder',29);
			break;
			case "default_retrieveevents":
				OSBHelper::retrieveevents();
			break;
			case "default_redirecterrorform":
				OsAppscheduleDefault::redirecterrorform();
			break;
			case "default_errorform":
				OsAppscheduleDefault::showingErrorForm();
			break;
			case "default_testtime":
				OsAppscheduleDefault::testtime();
			break;
		}
	}

	public static function redirecterrorform()
	{
		global $mainframe, $jinput;
		$mainframe->redirect('index.php?option=com_osservicesbooking&task=default_errorform&Itemid='.$jinput->getInt('Itemid'));
	}

	public static function showingErrorForm()
	{
		$session = Factory::getSession();
		$error_message = $session->get('errorReason');
		?>
		<h2>
			<?php echo JText::_('OS_ERROR_FOUND');?>
		</h2>
		<?php
		echo $error_message;
	}

	public static function testtime()
	{
		$config = JFactory::getConfig();
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year - 1, 1, 1);
		$date->setTime(0, 0, 0);
		echo $date->format('c');
	}

	static function qrScan()
	{
		global $mainframe, $configClass, $mapClass;
		$user = JFactory::getUser();
		if (!$user->authorise('osservicesbooking.checkin_management', 'com_osservicesbooking'))
		{
			if ($user->get('guest'))
			{
				OSBHelper::requestLogin();
			}
			else
			{
				$mainframe->enqueueMessage(Text::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'), 'error');
				$mainframe->redirect(Uri::root(), 403);
			}
		}
		HTMLHelper::_('behavior.core');

		$rootUri         = Uri::root(true);
		$interval        = 15;

		JFactory::getDocument()->addScript($rootUri . '/media/com_osservicesbooking/assets/js/html5-qrcode/html5-qrcode.min.js')
			->addScript($rootUri . '/media/com_osservicesbooking/assets/js/site-checkin-default.js')
			->addScript($rootUri . '/media/com_osservicesbooking/assets/js/tingle/tingle.min.js')
			->addStyleSheet($rootUri . '/media/com_osservicesbooking/assets/js/tingle/tingle.min.css')
			->addScriptOptions('checkinUrl', $rootUri . '/index.php?option=com_osservicesbooking&task=default_qrcodecheckin&tmpl=component')
			->addScriptOptions('checkInInterval', $interval*1000)
			->addScriptOptions('btn', $mapClass['btn'])
			->addScriptOptions('btnPrimaryClass', $mapClass['btn-primary'])
			->addScriptOptions('textSuccessClass', $mapClass['text-success'])
			->addScriptOptions('textWarningClass', 'text-warning');
		?>
		<div id="eb-checkin-page" class="eb-container">
			<h1 class="component_heading"><?php echo JText::_('OS_CHECKIN_ORDER_ITEM');?></h1>
			<div id="reader"></div>
		</div>
		<?php
	}

	static function qrcodecheckin()
	{
		global $jinput, $configClass, $mainframe;
		$code = $jinput->getString('value');
		$code = explode("_", $code);
		$code = $code[1];
		list($success, $message) = self::processCheckin($code);

		$response = [
			'success' => $success,
			'message' => $message,
		];

		self::sendJsonResponse($response);
		$mainframe->close();
	}

	public static function processCheckin($id)
	{
		global $mainframe, $configClass;
		$success = false;
		$message = '';
		if((int) $id > 0)
		{
			$db = JFactory::getDbo();
			$db->setQuery("Select * from #__app_sch_order_items where id = '$id'");
			$item = $db->loadObject();
			if((int)$item->id == 0)
			{
				$message = Text::_('OS_INVALID_ORDER_ITEM');
			}
			elseif($item->checked_in == 1)
			{
				$message = Text::_('OS_ORDER_ITEM_ALREADY_CHECKED_IN');
			}
			else
			{
				$db->setQuery("Select * from #__app_sch_orders where id = '$item->order_id'");
				$order = $db->loadObject();
				if((int) $order->id == 0)
				{
					$message = Text::_('OS_INVALID_ORDER');
				}
				elseif($order->order_status != "S")
				{
					$message = Text::_('OS_THIS_ORDER_ITEM_CAN_NOT_CHECK_IN');
				}
				else
				{
					$db->setQuery("Update #__app_sch_order_items set checked_in = '1' where id = '$id'");
					$db->execute();
					$message = Text::_('OS_CHECKED_IN_SUCCESSFULLY');
					$success = true;
				}
			}

			$replaces = [
				'NAME'			=> $item->order_name,
				'ID'			=> $item->id,
			];

			foreach ($replaces as $key => $value)
			{
				$message = str_replace('[' . $key . ']', $value, $message);
			}
		}
		return [$success, $message];
	}

	/**
	 * Send json response
	 *
	 * @param   array  $response
	 */
	public static function sendJsonResponse($response)
	{
		global $mainframe;
		echo json_encode($response);

		$mainframe->close();
	}

	static function changeReminderStatus($order_id)
	{
		global $mainframe, $jinput;
		$db			= JFactory::getDbo();
		$itemid		= $jinput->getInt('Itemid',0);
		$status		= $jinput->getInt('status',0);
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		$order		= $db->loadObject();
		$user		= JFactory::getUser();
		if($user->id != $order->user_id)
		{
			throw new Exception (JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
		}
		$db->setQuery("Update #__app_sch_orders set receive_reminder = '$status' where id = '$order_id'");
		$db->execute();
		if($status == 0)
		{
			$msg = JText::_('OS_YOU_ALREADY_TURN_OF_RECEIVING_REMINDER');
		}
		else
		{
			$msg = JText::_('OS_YOU_ALREADY_TURN_ON_RECEIVING_REMINDER');
		}
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&view=customer&Itemid='.$itemid));
	}

	static function showCommentForm(){
		global $configClass,$jinput;
		$sid = $jinput->getInt('sid',0);
		$eid = $jinput->getInt('eid',0);
		HelperOSappscheduleCommon::reviewForm($sid,$eid);
	}

	static function submitComment(){
		global $configClass,$jinput,$mainframe;
		require_once JPATH_ROOT.'/administrator/components/com_osservicesbooking/tables/review.php';
        $db = JFactory::getDbo();
		$user = JFactory::getUser();
		$sid = $jinput->getInt('sid',0);
		$eid = $jinput->getInt('eid',0);
		if(HelperOSappscheduleCommon::canPostReview($sid,$eid)){
			$comment_content = $_POST['comment_content'];
			$row = &JTable::getInstance('Review','OsAppTable');
			$row->id = 0;
			$post = $jinput->post->getArray();
			$row->bind($post);
			$row->comment_date = date("Y-m-d",time());
			$row->ip_address = $_SERVER['REMOTE_ADDR'];
			$row->published = 0;
			$row->comment_content = $comment_content;
            $row->user_id = $user->id;
			$row->store();
            //send notification to administrator
            $emailfrom = JFactory::getConfig()->get('mailfrom');
            $fromname  = JFactory::getConfig()->get('fromname');

            $db->setQuery("Select service_name from #__app_sch_services where id = '$row->sid'");
            $service_name = $db->loadResult();

            $db->setQuery("Select employee_name from #__app_sch_employee where id = '$row->eid'");
            $employee_name = $db->loadResult();

            $db->setQuery("Select * from #__app_sch_emails where email_key like 'new_comment_added'");
            $email = $db->loadObject();
            $subject = $email->email_subject;
            $message = $email->email_content;

            $emailto = $configClass['value_string_email_address'];
            if($emailto == ""){
                $emailto = $email;
            }

            $message = str_replace("{author}",$row->name,$message);
            $message = str_replace("{service}",$service_name,$message);
            $message = str_replace("{employee}",$employee_name,$message);
			$message = str_replace("{username}",$user->username,$message);
            $message = str_replace("{title}",$row->comment_title,$message);
            $message = str_replace("{message}",$row->comment_content,$message);
            $message = str_replace("{created_date}",$row->comment_date,$message);
            $message = str_replace("{rate}",$row->rating,$message);
            $mailer = JFactory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$fromname,$emailto,$subject,$message,1);
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
            //complete
			$msg = JText::_('OS_YOUR_REVIEW_HAS_BEEN_STORED');
			$mainframe->enqueueMessage($msg,'message');

            ?>
            <script type="text/javascript">
                setTimeout(static function () { window.close();}, 5000);
            </script>
            <?php

		}else{
			if(($configClass['active_comment'] == 1) and ($user->id > 0)){
				if(HelperOSappscheduleCommon::alreadyPostComment($sid,$eid)){
					//JError::raiseError( 500 , JText::_('OS_YOU_ALREADY_SUBMITTED_A_COMMENT') );
					throw new Exception (JText::_('OS_YOU_ALREADY_SUBMITTED_A_COMMENT'));
				}else{
					//JError::raiseError( 500, JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA') );
					throw new Exception (JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
				}
			}else{
				//JError::raiseError( 500, JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA') );
				throw new Exception (JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			}
		}
	}
    /**
	 * Default layout
	 *
	 * @param unknown_type $option
	 */
	static function defaultLayout($option)
	{
		global $mainframe,$mapClass,$configClass,$languages,$jinput;
		$languages = OSBHelper::getLanguages();
		JPluginHelper::importPlugin('osservicesbooking');
		$results = [];
		$results = $mainframe->triggerEvent('onBeforeBookingFormGenerator', []);
		if (count($results) && (!in_array(true, $results, true)))
		{
			throw new Exception(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'), 404);
			//JError::raiseError(404, JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
		}

		$db = JFactory::getDbo();
		$vid = $jinput->getInt('vid',0);
		if($vid == 0 && OSBHelper::applyVenuFeature())
		{
			$vid = OSBHelper::getVenueID();
			$jinput->set('vid', $vid);
		}
		
		$realtime					= HelperOSappscheduleCommon::getRealTime();
		$current_hour				= date("H",$realtime);
		$current_min				= date("i",$realtime);
		$realtime_this_day			= $current_hour*3600 + $current_min*60;
		$remain_time				= 24*3600 - $realtime_this_day;
		//fix in case user click on Back button of browser to go to Booking page
        $using_cart = $configClass['using_cart'];
        if($using_cart == 0)
        {
            if(!OSBHelper::isEmptyCart())
            {
                //$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&task=form_step1'));
                //remove temporarily item and allow customer to make another timeslot
                OSBHelper::emptyCart();
            }
        }
		$category_id = $jinput->getInt('category_id',0);
		if($category_id > 0)
		{
			$catSql = " and category_id = '$category_id' ";
			$db->setQuery("Select * from #__app_sch_categories where id = '$category_id'");
			$category = $db->loadObject();
		}
		else
		{
			$catSql = "";
		}
		
		$employee_id = $jinput->getInt('employee_id',0);
		if($employee_id > 0){
			$employeeSql = " and id in (Select service_id from #__app_sch_employee_service where employee_id = '$employee_id')";
		}else{
			$employeeSql = "";
		}
		
		
		if($vid > 0)
		{
			$vidSql = " and id in (Select sid from #__app_sch_venue_services where vid = '$vid')";
		}
		else
		{
			$vidSql = "";
		}
		
		$sid = $jinput->getInt('sid',0);
		if($sid > 0)
		{
			$sidSql = " and id = '$sid'";
		}
		else
		{
			$sidSql = "";
		}

        $selected_datesArr = array();
        $menu			   = $mainframe->getMenu('site')->getActive();
        if (is_object($menu))
        {
            $params = $menu->getParams();
            $selected_dates = $params->get('selected_dates','');
            if($selected_dates != "")
            {
                $selected_datesArr = explode(",", $selected_dates);
            }
        }

        $lists['selected_dates'] = $selected_datesArr;

		$document = JFactory::getDocument();
		$menus = JFactory::getApplication()->getMenu();
        $menu = $menus->getActive();
		if (is_object($menu)) 
		{
			$query = $menu->query;
			if($query['view'] == 'default' || $query['task'] = 'default_layout')
			{
				$params = $menu->getParams();
				if($params->get('page_title') != "")
				{
					$document->setTitle($params->get('page_title'));
				}
				else
				{
					if($configClass['business_name'] != "") $document->setTitle($configClass['business_name']);
				}
				if($params->get('show_page_heading',0) == 1)
				{
					$lists['pageHeading'] = $params->get('page_heading','');
				}
				if($params->get('menu-meta_description','') != "")
				{
					$document->setMetaData( "description", $params->get('menu-meta_description',''));
				}
				else
				{
					if( $configClass['meta_desc'] != "" ) $document->setMetaData( "description", $configClass['meta_desc'] );
				}
				
				if($params->get('menu-meta_keywords','') != "")
				{
					$document->setMetaData( "keywords", $params->get('menu-meta_keywords',''));
				}
				else
				{
					if( $configClass['meta_keyword'] != "" ) $document->setMetaData( "keywords", $configClass['meta_keyword'] );
				}
			}
			else
			{
				if($configClass['business_name'] != "") $document->setTitle($configClass['business_name']);
				if( $configClass['meta_desc'] != "" ) $document->setMetaData( "description", $configClass['meta_desc'] );
				if( $configClass['meta_keyword'] != "" ) $document->setMetaData( "keywords", $configClass['meta_keyword'] );
			}
		}
		
        $orig_metakey = $document->getMetaData('keywords');
        //if( $configClass['meta_keyword'] != "" ) $document->setMetaData( "keywords", $configClass['meta_keyword'] );

        $orig_metadesc = $document->getMetaData('description');
        //if( $configClass['meta_desc'] != "" ) $document->setMetaData( "description", $configClass['meta_desc'] );
		
		$year		= date("Y",HelperOSappscheduleCommon::getRealTime());
		$month		= intval(date("m",HelperOSappscheduleCommon::getRealTime()));
		$day		= intval(date("d",HelperOSappscheduleCommon::getRealTime()));
		
		$date_from = OSBHelper::getStringValue('date_from','');
		$date_to   = OSBHelper::getStringValue('date_to','');
		if($date_from == '0')
		{
		    $date_from = '';
        }
        if($date_to == '0')
        {
		    $date_to = '';
        }

		if((int) $configClass['max_check_in'] > 0)
		{
			$date_to			= date("Y-m-d",$realtime + $configClass['max_check_in']*24*3600);
			$jinput->set('date_to', $date_to);
		}

		if($date_from != "")
		{
			$date_from_array = explode(" ",$date_from);
			$date_from_int = strtotime($date_from_array[0]);
			if($date_from_int > HelperOSappscheduleCommon::getRealTime())
			{
				$year = date("Y",$date_from_int);
				$month = intval(date("m",$date_from_int));
				$day = intval(date("d",$date_from_int));
			}
		}
		else
		{
		    if($configClass['skip_unavailable_dates'] == 1)
		    {
                $number_days_in_month   = HelperOSappscheduleCalendar::ndaysinmonth($month,$year);
                $today                  = strtotime($year."-".$month."-".$day);
				if((int) $configClass['min_check_in'] > 0)
				{
					$disable_time		= $realtime + ($configClass['min_check_in'] - 1) * 24 * 3600 + $remain_time;
					if($disable_time > $today)
                    {
                        $year			= date("Y", $disable_time);
                        $month			= date("m", $disable_time);
                        $day			= date("d", $disable_time);
                        $today			= $disable_time;
                    }
				}
				elseif($vid > 0)
                {
                    $db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
                    $venue							= $db->loadObject();
                    $disable_booking_before			= $venue->disable_booking_before;
                    $number_date_before				= $venue->number_date_before;
                    $number_hour_before				= $venue->number_hour_before;
                    $disable_date_before			= $venue->disable_date_before;
                    if ($disable_booking_before == 1)
                    {
                        $disable_time = strtotime(date("Y", $realtime) . "-" . date("m", $realtime) . "-" . date("d", $realtime) . " 23:59:59");
                    }
                    elseif ($disable_booking_before == 2)
                    {
                        $disable_time = $realtime + ($number_date_before - 1) * 24 * 3600 + $remain_time;
                    }
                    elseif ($disable_booking_before == 3)
                    {
                        $disable_time = strtotime($disable_date_before);
                    }
                    elseif ($disable_booking_before == 4)
                    {
                        $disable_time = $realtime + $number_hour_before * 3600;
                    }

                    if($disable_time > $today)
                    {
                        $year			= date("Y", $disable_time);
                        $month			= date("m", $disable_time);
                        $day			= date("d", $disable_time);
                        $today			= $disable_time;
                    }
                }

                $cdate                  = $day;
                if(!OSBHelper::isAvailableDate($today,$category_id,$employee_id,$vid))
                {
                    //find first available date
                    $cdate++;
                    $checked_date = strtotime($year."-".$month."-".$cdate);
					$date_from    = $year."-".$month."-".$cdate;
                    while((OSBHelper::returnFirstAvailableDate($checked_date,$category_id,$employee_id,$vid) == '') && ($cdate <= $number_days_in_month))
					{
                        $cdate++;
                        $checked_date	=  strtotime($year."-".$month."-".$cdate);
                        $date_from		= OSBHelper::returnFirstAvailableDate($checked_date,$category_id,$employee_id,$vid);
                    }
                }
				if($date_from == "")
				{
					$date_from = date("Y-m-d", $today);
				}
				//echo date("Y-m-d",$date_from);die();
                if($date_from != "")
                {
                    $date_from_array = explode(" ",$date_from);
					//print_r($date_from_array);die();
                    $date_from_int = strtotime($date_from_array[0]);
					//echo $date_from_array[0];die();
                    if($date_from_int > HelperOSappscheduleCommon::getRealTime())
                    {
                        $year		= date("Y",$date_from_int);
                        $month		= intval(date("m",$date_from_int));
                        $day		= intval(date("d",$date_from_int));
                    }
                }
            }
        }
		$db->setQuery("Select * from #__app_sch_services where published = '1' $sidSql $catSql $employeeSql $vidSql ".HelperOSappscheduleCommon::returnAccessSql('')." order by ordering");
		$services = $db->loadObjectList();
		
		$translatable = JLanguageMultilang::isEnabled() && count($languages);
		
		if(count($services) > 1){
			$optionArr = array();
			$optionArr[] = JHtml::_('select.option','0',JText::_('OS_SELECT_SERVICES'));
			foreach ($services as $service){
				if($translatable){
					$optionArr[] = JHtml::_('select.option',$service->id,OSBHelper::getLanguageFieldValue($service,'service_name'));
				}else{
					$optionArr[] = JHtml::_('select.option',$service->id,$service->service_name);
				}
			}
			$lists['services'] = JHtml::_('select.genericlist',$optionArr,'sid','class="input-large chosen"','value','text',$sid);
		}
		HTML_OsAppscheduleDefault::defaultLayoutHTML($option,$services,$year,$month,$day,$category,$employee_id,$vid,$sid,$date_from,$date_to,$lists);
	}
	
	static function exportEmployeeWorks()
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		if(!HelperOSappscheduleCommon::checkEmployee())
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root()."index.php");
		}
		$csv_separator 		= $configClass['csv_separator'];
		$eid				= HelperOSappscheduleCommon::getEmployeeId();
		$db					= JFactory::getDbo();
		$filter_service = $jinput->getInt('filter_service',0);

		$date1				= OSBHelper::getStringValue('date1','');
		$date2				= OSBHelper::getStringValue('date2','');
		$date				= "";
		if($date1 != "")
		{
			$date .= " and a.booking_date >= '$date1'";
		}
		if($date2 != "")
		{
			$date .= " and a.booking_date <= '$date2'";
		}
		if($filter_service > 0)
		{
			$serviceSql = " and a.sid = '$filter_service'";
		}
		$today = date("Y",HelperOSappscheduleCommon::getRealTime())."-".date("m",HelperOSappscheduleCommon::getRealTime())."-".date("d",HelperOSappscheduleCommon::getRealTime());
		//get the work of this employee
		$db->setQuery("Select a.*,b.order_name, b.order_email, b.order_phone, b.order_address, b.order_notes ,c.service_name from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id inner join #__app_sch_services as c on c.id = a.sid where b.order_status in ('P','S') and a.eid = '$eid' and a.booking_date >= '$today' $date $serviceSql order by a.start_time");
		$rows = $db->loadObjectList();
		
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee = $db->loadObject();

		$header = '"ID"'.$csv_separator.'"'.JText::_('OS_SERVICE_NAME').'"'.$csv_separator.'"'.JText::_('OS_DATE').'"'.$csv_separator.'"'.JText::_('OS_START').'"'.$csv_separator.'"'.JText::_('OS_END').'"'.$csv_separator.'"'.JText::_('OS_CUSTOMER').'"'.$csv_separator.'"'.JText::_('OS_EMAIL').'"'.$csv_separator.'"'.JText::_('OS_PHONE').'"'.$csv_separator.'"'.JText::_('OS_ADDRESS').'"'.$csv_separator.'"'.JText::_('OS_NOTES').'"'.$csv_separator.'"'.JText::_('OS_ADDITIONAL_INFORMATION').'"';

		$csv_content .= "\n";
		if(count($rows) > 0)
		{
			foreach($rows as $row)
			{
				$csv_content .= $row->order_id.$csv_separator.OSBHelper::getLanguageFieldValue($row,'service_name').$csv_separator.date($configClass['date_format'],$row->start_time).$csv_separator.date($configClass['time_format'],$row->start_time).$csv_separator.date($configClass['time_format'],$row->end_time).$csv_separator.$row->order_name.$csv_separator.$row->order_email.$csv_separator.$row->order_phone.$csv_separator.$row->order_address.$csv_separator.'"'.$row->order_notes.'"';

				$item_content = "";
				$db->setQuery("Select a.*,b.service_name,b.service_time_type,c.employee_name from #__app_sch_order_items as a inner join #__app_sch_services as b on b.id = a.sid inner join #__app_sch_employee as c on c.id = a.eid where a.order_id = '$row->order_id' order by b.service_name");
                $items = $db->loadObjectList();
                if(count($items) > 0)
                {
                    for($j=0;$j<count($items);$j++)
                    {
                        $item = $items[$j];
                        $pos  = $j+1;
                        //Additional information
                        $db->setQuery("Select a.* from #__app_sch_venues as a inner join #__app_sch_employee_service as b on b.vid = a.id where b.employee_id = '$item->eid' and b.service_id = '$item->sid'");
                        $venue = $db->loadObject();
                        if($venue->address != "")
                        {
                            $item_content .= JText::_('OS_VENUE').": ".$venue->address."|";
                        }
                        if($item->service_time_type == 1)
                        {
                            $item_content .= "| ".JText::_('OS_NUMBER_SLOT').": ".$item->nslots."|";
                        }
                        $db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' order by ordering");
                        $fields = $db->loadObjectList();
                        if(count($fields) > 0)
                        {
                            for($i1=0;$i1<count($fields);$i1++)
                            {
                                $field = $fields[$i1];
                                $db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$item->id' and field_id = '$field->id'");
                                $count = $db->loadResult();
                                if($count > 0)
                                {
                                    if($field->field_type == 1)
                                    {
                                        $db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$item->id' and field_id = '$field->id'");
                                        //echo $db->getQuery();
                                        $option_id = $db->loadResult();
                                        $db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
                                        $optionvalue = $db->loadObject();
                                        ?>
                                        <?php $item_content .= " ".OSBHelper::getLanguageFieldValueOrder($field,'field_label',$row->order_lang).": ";?>
                                        <?php
                                        $field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$row->order_lang);
                                        if($optionvalue->additional_price > 0){
                                            $field_data.= " - ".OSBHelper::showMoney($optionvalue->additional_price,0);
                                        }
                                        $item_content .= $field_data ."|";
                                    }
                                    elseif($field->field_type == 2)
                                    {
                                        $db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$item->id' and field_id = '$field->id'");
                                        $option_ids = $db->loadObjectList();
                                        $fieldArr = array();
                                        //$item_content .= " ".OSBHelper::getLanguageFieldValueOrder($field,'field_label',$row->order_lang).": ";
                                        for($j1=0;$j1<count($option_ids);$j1++)
                                        {
                                            $oid = $option_ids[$j1];
                                            $db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
                                            //echo $db->getQuery();
                                            $optionvalue = $db->loadObject();
                                            $field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$row->order_lang);
                                            if($optionvalue->additional_price > 0)
                                            {
                                                $field_data.= " - ".OSBHelper::showMoney($optionvalue->additional_price,0);
                                            }
                                            $fieldArr[] = $field_data;
                                        }
                                        ?>
                                        <?php $item_content .= OSBHelper::getLanguageFieldValueOrder($field,'field_label',$row->order_lang);?>:
                                        <?php
                                        $item_content .= " ".implode(", ",$fieldArr)."|";
                                    }
                                }
                            }
                        }
                        $item_content .= " | ";
                    }
                }

				$field_content = "";
                $db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1'");
                $fields = $db->loadObjectList();
                //print_r($fields);
                if(count($fields) > 0)
				{
                    $field_content_array = array();
                    for($i2=0;$i2<count($fields);$i2++)
					{
                        $field = $fields[$i2];
                        $field_value = OsAppscheduleDefault::orderFieldData($field,$row->order_id);
                        if($field_value != "")
						{
                            $field_content_array[] = OSBHelper::getLanguageFieldValueOrder($field,'field_label',$row->order_lang).": ".$field_value;
                        }
                    }
                }

                $csv_content .= $csv_separator.'"'.$item_content.'. '.implode(" | ",$field_content_array).'"';
				$csv_content .= "\n";			 
			}
		}

		$header = $header.$csv_content;
		//create the csv file
		$filename = "employeework".$eid.".csv";
		$csv_absoluted_link = JPATH_ROOT."/tmp".DS.$filename;
		//create the content of csv
		$csvf = fopen($csv_absoluted_link,'w');
		@fwrite($csvf,$header);
		@fclose($csvf);
		self::downloadfile2($csv_absoluted_link,$filename);
	}

	public static function downloadfile2($file_path,$filename){
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
	    self::readfile_chunked($file_path);
		exit();
    }
    
    
    public static function readfile_chunked($filename,$retbytes=true){
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

	static function employeesetting()
	{
		global $mainframe,$mapClass,$configClass,$jinput;

		if(!HelperOSappscheduleCommon::checkEmployee())
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root()."index.php");
		}
		if($configClass['employee_change_availability'] == 0)
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root()."index.php");
		}
		require_once JPATH_ADMINISTRATOR .'/components/com_osservicesbooking/classes/employee.php'; 
		$eid = HelperOSappscheduleCommon::getEmployeeId();
		$row = &JTable::getInstance('Employee','OsAppTable');
		$row->load((int) $eid);
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_services where published = '1'");
		$services = $db->loadObjectList();


		$db->setQuery("Select * from #__app_sch_employee_rest_days where eid = '$eid'");
		$rests = $db->loadObjectList();

        $db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$eid'");
        $busy = $db->loadObjectList();

		$lists['hours'] = OSappscheduleEmployee::generateHours();

		$optionArr = array();
		$optionArr[] = JHtml::_('select.option','0',JText::_('All dates'));
		$optionArr[] = JHtml::_('select.option','1',JText::_('OS_MON'));
		$optionArr[] = JHtml::_('select.option','2',JText::_('OS_TUE'));
		$optionArr[] = JHtml::_('select.option','3',JText::_('OS_WED'));
		$optionArr[] = JHtml::_('select.option','4',JText::_('OS_THU'));
		$optionArr[] = JHtml::_('select.option','5',JText::_('OS_FRI'));
		$optionArr[] = JHtml::_('select.option','6',JText::_('OS_SAT'));
		$optionArr[] = JHtml::_('select.option','7',JText::_('OS_SUN'));
		$lists['week_day'] = $optionArr;

		$db->setQuery("Select * from #__app_sch_employee_extra_cost where eid = '$eid'");
		$extra_costs = $db->loadObjectList();
		$lists['extra'] = $extra_costs;
		HTML_OsAppscheduleDefault::employeesetting($services, $row, $rests, $busy, $lists);
	}

	static function saveemployeesetting()
	{
		global $mainframe,$configClass,$jinput;
		$db = JFactory::getDbo();
		$id	= $jinput->getInt('eid',0);
		// save employee service
		for($i=1;$i<=5;$i++)
		{
			$rest_day = $jinput->get('date'.$i,'','string');
			$rest_day_to = $jinput->get('date_to_'.$i,'','string');
			if($rest_day != "" && $rest_day_to != "")
			{
				$db->setQuery("INSERT INTO #__app_sch_employee_rest_days (id,eid,rest_date,rest_date_to) VALUES (NULL,'$id','$rest_day','$rest_day_to')");
				$db->execute();
			}

			$busy_date = $jinput->get('busy_date'.$i,'','string');
			$busy_from = $jinput->get('busy_from'.$i,'','string');
            $busy_to = $jinput->get('busy_to'.$i,'','string');
            if($busy_date != '' && $busy_from != '' && $busy_to != '')
            {
                $db->setQuery("INSERT INTO #__app_sch_employee_busy_time (id,eid,busy_date,busy_from,busy_to) VALUES (NULL,'$id','$busy_date','$busy_from','$busy_to')");
                $db->execute();
            }
		}

		//save the additional cost
		$db->setQuery("Delete from #__app_sch_employee_extra_cost where eid = '$id'");
		$db->execute();
		for($i=0;$i<=15;$i++){
			$start_time = $jinput->get('start_time'.$i,'','string');
			$end_time   = $jinput->get('end_time'.$i,'','string');
			$extra_cost = $jinput->get('extra_cost'.$i,'','string');
			$week_day   = $jinput->get('week_day'.$i,'','string');
			if(($start_time != "") and ($end_time != "") and ($extra_cost != "")){
				$db->setQuery("Insert into #__app_sch_employee_extra_cost (id,eid,start_time,end_time,extra_cost,week_date) values (NULL,'$id','$start_time','$end_time','$extra_cost','$week_day')");
				$db->execute();
			}
		}

		$msg = JText::_('OS_EMPLOYEE_AVAILABILITY_INFORMATION_HAVE_BEEN_SAVED');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&view=employeesetting&Itemid='.$jinput->getInt('Itemid',0)));
	}

	static function removeRestday()
	{
		global $mainframe,$jinput;
		$rid = $jinput->getInt('rid',0);
		$db  = JFactory::getDbo();
		$db->setQuery("Select eid from #__app_sch_employee_rest_days where id = '$rid'");
		$eid = $db->loadResult();
		$db->setQuery("DELETE FROM #__app_sch_employee_rest_days WHERE id = '$rid'");
		$db->execute();		
		$db->setQuery("Select * from #__app_sch_employee_rest_days where eid = '$eid'");
		$rests = $db->loadObjectList();
		if(count($rests) > 0){
			?>
			<table width="100%" style="border:1px solid #CCC !important;">
				<tr>
					<td width="30%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC !important;">
						<?php echo JText::_('OS_DATE')?>
					</td>
					<td width="20%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC !important;">
						<?php echo JText::_('OS_REMOVE')?>
					</td>
				</tr>
				<?php
				for($i=0;$i<count($rests);$i++){
					$rest = $rests[$i];
					?>
					<tr>
						<td width="30%" align="left" style="padding-left:10px;">
							<?php
							$timestemp = strtotime($rest->rest_date);
							echo date("D, jS M Y",  $timestemp);
							?>
						</td>
						<td width="30%" align="center">
							<a href="javascript:removeBreakDate(<?php echo $rest->id?>)" title="<?php echo JText::_('OS_REMOVE_REST_DATE');?>">
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
			<?php
		}
		exit();
	}

	static function removeBusytime()
	{
		global $mainframe,$jinput;
        $rid = $jinput->getInt('rid',0);
        $db  = JFactory::getDbo();
        $db->setQuery("Select eid from #__app_sch_employee_busy_time where id = '$rid'");
        $eid = $db->loadResult();
        $db->setQuery("DELETE FROM #__app_sch_employee_busy_time WHERE id = '$rid'");
        $db->execute();
        $db->setQuery("Select * from #__app_sch_employee_busy_time where eid = '$eid'");
        $busy = $db->loadObjectList();
        if(count($busy) > 0){
            ?>
            <table width="100%" style="border:1px solid #CCC !important;">
                <tr>
                    <td width="30%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC !important;">
                        <?php echo JText::_('OS_DATE')?>
                    </td>
                    <td width="20%" style="text-align:center;font-weight:bold;border-bottom:1px solid #CCC !important;">
                        <?php echo JText::_('OS_REMOVE')?>
                    </td>
                </tr>
                <?php
                for($i=0;$i<count($busy);$i++)
                {
                    $b = $busy[$i];
                    ?>
                    <tr>
                        <td width="30%" align="left" style="padding-left:10px;">
                            <?php
                            $timestemp = strtotime($b->busy_date);
                            echo date("D, jS M Y",  $timestemp);
                            echo " - ".JText::_('OS_FROM').": ".$b->busy_from.". ".JText::_('OS_TO').": ".$b->busy_to;
                            ?>
                        </td>
                        <td width="30%" align="center">
                            <a href="javascript:removeBusyTime(<?php echo $b->id?>)" title="<?php echo JText::_('OS_REMOVE_BUSY_DATE');?>">
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
            <?php
        }
        exit();
	}

	static function setupBreakTime()
	{
		global $mainframe,$jinput;
		require_once JPATH_ADMINISTRATOR.'/components/com_osservicesbooking/helpers/checkboxservice.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_osservicesbooking/classes/employee.php';
		$db  = JFactory::getDbo();
		$sid = $jinput->getInt('sid',0);
		$eid = $jinput->getInt('eid',0);
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee = $db->loadObject();
		$db->setQuery("Select * from #__app_sch_services where id = '$sid'");
		$service = $db->loadObject();
		$lists['services'] = ServiceCheckbox::checkingBreaktime($sid,$eid);
		$db->setQuery("Select * from #__app_sch_custom_breaktime where eid = '$eid' and sid = '$sid' order by bdate,bstart,bend");
		$customs = $db->loadObjectList();
		HTML_OsAppscheduleDefault::setupBreakTime($service,$employee,$lists,$customs);
	}

	static function saveBreaktime($save)
	{
		global $mainframe,$jinput;
		if(!HelperOSappscheduleCommon::checkEmployee())
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root()."index.php");
		}
		$db = JFactory::getDbo();
		$serviceid = $jinput->getInt('sid',0);
		// save employee service
		$db = JFactory::getDbo();
		$employee_id = $jinput->getInt('eid',0);
		if ($employee_id){
			$db->setQuery("DELETE FROM #__app_sch_employee_service WHERE `employee_id` = '$employee_id' and service_id = '$serviceid'");
			$db->execute();
		}
		if ($employee_id)
		{
			$row = &JTable::getInstance('Empser','OsAppTable');
			$row->employee_id = $employee_id;
			$serviceids = $jinput->get('service_id',array(),'array');//JRequest::getVar('service_id',array(),'default','array');
			foreach ($serviceids as $serviceid) 
			{
				$row->id = null;
				$row->service_id = $serviceid;
				$additional_cost = $jinput->get('add_'.$serviceid,0,'string');
				$row->additional_price = (float)$additional_cost;
				$venue = $jinput->getInt('vid_'.$serviceid,0);
				$row->vid = $venue;
				$row->mo = $jinput->getInt('mo_'.$serviceid,0);
				$row->tu = $jinput->getInt('tu_'.$serviceid,0);
				$row->we = $jinput->getInt('we_'.$serviceid,0);
				$row->th = $jinput->getInt('th_'.$serviceid,0);
				$row->fr = $jinput->getInt('fr_'.$serviceid,0);
				$row->sa = $jinput->getInt('sa_'.$serviceid,0);
				$row->su = $jinput->getInt('su_'.$serviceid,0);
				$row->ordering = $row->getNextOrder(" `service_id` = '$serviceid'");
				if(!$row->store())
				{
					throw new Exception($row->getError(), 500);
				}
				$row->reorder(" `service_id` = '$serviceid'");
								
				$db->setQuery("Delete from #__app_sch_employee_service_breaktime where eid = '$employee_id' and sid = '$serviceid'");
				$db->execute();
				for($i=1;$i<=7;$i++)
				{
					for($j=0;$j<4;$j++)
					{
						$startname  = "start_from".$serviceid.$j."_".$i;
						$endname    = "end_to".$serviceid.$j."_".$i;
						$start_from = $jinput->get($startname,'','string');
						$end_to		= $jinput->get($endname,'','string');
						if(($start_from != "") and ($end_to != "")){
							$db->setQuery("Insert into #__app_sch_employee_service_breaktime (id,sid,eid,date_in_week,break_from,break_to) values (NULL,'$serviceid','$employee_id','$i','$start_from','$end_to')");
							$db->execute();
						}
					}
				}
			}
		}
		
		$db->setQuery("DELETE FROM #__app_sch_employee_extra_cost WHERE eid = '$employee_id'");
		$db->execute();
		for($i=0;$i<10;$i++){
			$start_time      = $jinput->get("start_time".$i,"","string");
			$end_time        = $jinput->get("end_time".$i,"","string");
			$extra_cost		 = $jinput->get("extra_cost".$i,"","string");
			if(($start_time != "") and ($end_time != "") and ($extra_cost != "")){
				$db->setQuery("INSERT INTO #__app_sch_employee_extra_cost (id, eid, start_time, end_time, extra_cost) VALUES (NULL,'$employee_id','$start_time','$end_time',$extra_cost)");
				$db->execute();
			}
		}
		
		if($save == 1)
		{
			$mainframe->enqueueMessage(JText::_('OS_WORKING_TIME_HAVE_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&view=employeesetting&Itemid=".$jinput->getInt('Itemid',0));
		}
		else 
		{
			$mainframe->enqueueMessage(JText::_('OS_WORKING_TIME_HAVE_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=default_setupbreaktime&eid=".$employee_id."&sid=".$serviceid."&Itemid=".$jinput->getInt('Itemid',0));
		}
	}

	/**
	 * Employee works
	 *
	 */
	static function employeeWorks(){
		global $mainframe,$mapClass,$configClass,$jinput;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		if(!HelperOSappscheduleCommon::checkEmployee())
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root()."index.php");
		}

		$limit		= $jinput->getInt('limit',10);
        $limitstart = $jinput->getInt('limitstart',0);

		$eid = HelperOSappscheduleCommon::getEmployeeId();
		$db = JFactory::getDbo();

		$filter_service = $jinput->getInt('filter_service',0);
		
		$query = " SELECT a.id AS value, a.service_name AS text"
				." FROM #__app_sch_services AS a"
				." INNER JOIN #__app_sch_employee_service AS b ON (a.id = b.service_id AND b.employee_id = '$eid')"
				." WHERE  a.published = '1' "
				." ORDER BY a.service_name, a.ordering";
		$db->setQuery($query);
		$options = $db->loadObjectlist();
		array_unshift($options,JHtml::_('select.option',0,JText::_('OS_FILTER_SERVICE')));
		$lists['filter_service']	= JHtml::_('select.genericlist',$options,'filter_service','class="input-medium form-select" onchange="javascript:submitFilterForm();" ','value','text',$filter_service);

		$date1 = OSBHelper::getStringValue('date1','');
		$date2 = OSBHelper::getStringValue('date2','');
		$date  = "";
		if($date1 != "")
		{
			$date .= " and a.booking_date >= '$date1'";
		}
		if($date2 != "")
		{
			$date .= " and a.booking_date <= '$date2'";
		}
		if($filter_service > 0)
		{
			$serviceSql = " and a.sid = '$filter_service'";
		}
		$today = date("Y",HelperOSappscheduleCommon::getRealTime())."-".date("m",HelperOSappscheduleCommon::getRealTime())."-".date("d",HelperOSappscheduleCommon::getRealTime());

		$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id inner join #__app_sch_services as c on c.id = a.sid where b.order_status in ('P','S') and a.eid = '$eid' and a.booking_date >= '$today' $date $serviceSql");
		$total = $db->loadResult();
		$pageNav 	= new OSBJPagination($total,$limitstart,$limit);

		//get the work of this employee
		$db->setQuery("Select a.*,b.order_name, b.order_email, b.order_phone, b.order_address, b.order_notes ,c.service_name from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id inner join #__app_sch_services as c on c.id = a.sid where b.order_status in ('P','S') and a.eid = '$eid' and a.booking_date >= '$today' $date $serviceSql order by a.start_time",$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee = $db->loadObject();
		HTML_OsAppscheduleDefault::listEmployeeWorks($employee,$rows,$lists,$pageNav);
	}
	
	/**
	 * Order form
	 *
	 * @param unknown_type $order_id
	 */
	public static function orderDetailsForm($order_id,$checkin = 0)
	{
		global $mainframe;
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		$order = $db->loadObject();
		if((int) $order->id == 0)
		{
			$mainframe->enqueueMessage('Order is not existing');
			$mainframe->redirect(JUri::root());
		}
		$db->setQuery("Select a.*,b.id,b.*,c.id,c.employee_name from #__app_sch_order_items as a inner join #__app_sch_services as b on b.id = a.sid inner join #__app_sch_employee as c on c.id = a.eid where a.order_id = '$order_id' order by a.booking_date");
		$rows = $db->loadObjectList();
		
		HTML_OsAppscheduleDefault::showOrderDetailsForm($order,$rows,$checkin);
	}
	
	/**
	 * Cancel the payment
	 *
	 * @param unknown_type $order_id
	 */
	static function cancelPayment($order_id){
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__app_sch_orders SET order_status = 'C' WHERE id = '$order_id'");
		$db->execute();
		HelperOSappscheduleCommon::sendCancelledEmail($order_id);
		HelperOSappscheduleCommon::sendSMS('cancel',$order_id);
        HelperOSappscheduleCommon::sendSMS('canceltoEmployee',$order_id);
		HelperOSappscheduleCommon::sendEmployeeEmail('employee_order_cancelled_new',$order_id,0);
		if($configClass['waiting_list'] == 1){
			OSBHelper::sendWaitingNotification($order_id);
		}
		?>
		<h2>
			<?php echo JText::_('OS_YOUR_BOOKING_REQUEST_HAS_BEEN_CANCELLED');?>
		</h2>
		<?php
	}
	
	/**
	 * Payment failure
	 *
	 * @param unknown_type $order_id
	 */
	static function paymentFailure($order_id)
	{
		global $mainframe, $jinput;
		$remainPayment = $jinput->getInt('remainPayment', 0);
		if($remainPayment == 0)
		{
			$db = JFactory::getDbo();
			$db->setQuery("UPDATE #__app_sch_orders SET order_status = 'C' WHERE id = '$order_id'");
			$db->execute();
			HelperOSappscheduleCommon::sendEmail('payment_failure',$order_id);
		}
		?>
		<h2>
			<?php echo JText::_('OS_YOUR_TRANSACTION_IS_FAILURE');?>
		</h2>
		<?php
		$reason = isset($_SESSION['reason']) ? $_SESSION['reason'] : '';
		if (empty($reason))
		{
			$reason = JFactory::getSession()->get('omnipay_payment_error_reason');
		}
		if (!$reason)
		{
			$reason = JFactory::getApplication()->input->getString('failReason', '');
		}
		echo $reason;
	}
	
	/**
	 * Payment return
	 *
	 * @param unknown_type $order_id
	 */
	static function returnPayment($order_id)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
        $msg	= $jinput->get('msg','','string');
		$remainPayment = $jinput->getInt('remainPayment', 0);
		if($remainPayment == 1)
		{
			$msg = JText::_('OS_REMAIN_PAYMENT_HAS_BEEN_MADE_SUCCESSFULLY');
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect(JURI::root()."index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=".$order_id."&ref=".md5($order_id)."&Itemid=".$jinput->getInt('Itemid'));
		}
		else
		{
			$db		= JFactory::getDbo();
			$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
			$order = $db->loadObject();
			if ($order->order_status != 'S' && $order->order_payment == 'os_ideal')
			{
				// Use online payment method and the payment is not success for some reason, we need to redirec to failure page
				$Itemid     = $jinput->getInt('Itemid', 0);
				$failureUrl = JRoute::_('index.php?option=com_osservicesbooking&task=default_paymentfailure&id=' . $order_id . '&Itemid=' . $Itemid, false, false);
				$mainframe->enqueueMessage('Something went wrong, you are NOT successfully registered');
				$mainframe->redirect($failureUrl);
			}
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect(JURI::root()."index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=".$order_id."&ref=".md5($order_id)."&Itemid=".$jinput->getInt('Itemid'));
		}
	}
	
	/**
	 * Paypal notification
	 *
	 * @param unknown_type $option
	 */
	static function paymentNotify()
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$paymentMethod	= OSBHelper::getStringValue('payment_method', '');
		$remainPayment  = $jinput->getInt('remainPayment', 0);
		$method			= os_payments::getPaymentMethod($paymentMethod) ;
		$method->verifyPayment();
	}
	
	/**
	 * Payment complete
	 *
	 * @param unknown_type $orderId
	 */
	static function paymentComplete($orderId)
    {
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__app_sch_orders SET order_status = 'S' WHERE id = '$orderId'");
		$db->execute();
		
		$db->setQuery("Select * from #__app_sch_orders where id = '$orderId'");
		$row = $db->loadObject();
		JPluginHelper::importPlugin('osservicesbooking');
		//$dispatcher = JDispatcher::getInstance();
		//$dispatcher->trigger('onOrderActive', array($row));
		$results = array();
        $results = $mainframe->triggerEvent('onOrderActive', array($row));	
		//send email to customer to inform the payment is completed
		if($configClass['value_enum_email_payment'] == 2)
		{
			HelperOSappscheduleCommon::sendEmail('payment',$orderId);
			HelperOSappscheduleCommon::sendSMS('payment',$orderId);
            HelperOSappscheduleCommon::sendSMS('paymenttoEmployee',$orderId);
		}
		//send confirm email 
		if($configClass['value_enum_email_confirmation'] == 3)
		{
			HelperOSappscheduleCommon::sendEmail('confirm',$orderId);
			HelperOSappscheduleCommon::sendEmail('admin',$orderId);
			HelperOSappscheduleCommon::sendEmployeeEmail('employee_notification_new',$orderId,0);
			HelperOSappscheduleCommon::sendSMS('confirm',$orderId);
			HelperOSappscheduleCommon::sendSMS('admin',$orderId);
			HelperOSappscheduleCommon::updateAcyMailing($orderId);
		}
		
		//update to Google Calendar
		include_once(JPATH_ADMINISTRATOR."/components/com_osservicesbooking/helpers/helper.php");
		// validate input
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		OSBHelper::updateGoogleCalendar($orderId);
	}

	static function remainPaymentComplete($orderId)
    {
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		HelperOSappscheduleCommon::sendEmail('remain_payment_notify_administrator',$orderId);
		HelperOSappscheduleCommon::sendEmail('remain_payment_notify_customer',$orderId);
		
	}

	static function completeremainpayment($option)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$Itemid								= $jinput->getint('Itemid',0);
		$order_id							= $jinput->getInt('order_id',0);
		$db									= JFactory::getDbo();
		require_once(JPATH_ROOT."/administrator/components/com_osservicesbooking/tables/order.php");
		$order								= JTable::getInstance('Order', 'OsAppTable');
		$order->load($order_id);
		$data['payment_method'] 			= $jinput->getString('payment_method','');
		$data['x_card_num'] 				= base64_decode($jinput->get('x_card_num','','string'));
		$data['x_card_code'] 				= $jinput->get('x_card_code','','string');
		$data['card_holder_name'] 			= $jinput->get('card_holder_name','','string');
		$data['exp_year'] 					= (int)$jinput->get('exp_year','','string');
		$data['exp_month'] 					= (int)$jinput->get('exp_month','','string');
		$data['card_type'] 					= $jinput->get('card_type','','string');
		$data['address'] 					= $order->order_address;
		$data['city'] 						= $order->order_city;
		$data['state'] 						= $order->order_state;
		$data['zip'] 						= $order->order_zip;
		$data['phone']						= $order->order_phone;
		$data['bank_id']					= $order->bank_id;
		$data['stripeToken']				= $jinput->getString('stripeToken','');
		$data['nonce']                      = $jinput->getString('nonce','');
        $data['TransactionToken']           = $jinput->getString('TransactionToken','');
		$data['isRemain']					= 1;
		$order_name 						= $order->order_name;
		$order_name							= explode(" ",$order_name);
		if(count($order_name) > 1)
		{
			$first_name = $order_name[0];
			$last_name  = "";
			for($i=1;$i<count($order_name);$i++)
			{
				$last_name = $order_name[$i]." ";
			}
		}
		$order_country						= $order->order_country;
		if($order_country == "")
		{
			$order_country = "US";
		}
		else
		{
			$db->setQuery("Select country_code from #__app_sch_countries where country_name like '$order_country'");
			$order_country = $db->loadResult();
		}
		$data['country']					= $order_country;
		$data['first_name'] 				= $first_name;
		$data['last_name'] 					= $last_name;
		$data['email'] 						= $order->order_email;
		if($data['payment_method'] == "os_offline")
		{
			$amount = $order->order_final_cost;
		}
		else
		{
			$amount = $order->order_final_cost - $order->order_upfront;
		}
		$data['amount']						= $amount;
		$order->remain_payment_amount		= $amount;
		$order->store();
		$data['currency']					= $configClass['currency_format'];
		$data['curr']						= $configClass['currency_format'];
		$data['item_name']					= JText::_('OS_REMAINING_PAYMENT');
		
		$order_payment						= $data['payment_method'];
		if($configClass['disable_payments'] == 1)
		{
			if($order_payment == "")
			{
				throw new Exception( JText::_('Opps, there is a problem with Payment Progress, please try to make booking again later!') );
			}
			else
			{
				require_once JPATH_COMPONENT.'/plugins/'.$order_payment.'.php';
				$sql = 'SELECT params FROM #__app_sch_plugins WHERE name="'.$order_payment.'"';
				$db->setQuery($sql) ;
				$plugin = $db->loadObject();
				$params = $plugin->params ;
				$params = new JRegistry($params) ;
				$paymentClass = new $order_payment($params) ;  
				$paymentClass->processPayment($order, $data);
			}
		}
		
	}
	
	/**
	 * Payment process
	 *
	 * @param unknown_type $option
	 */
	static function paymentProcess($option){
		global $mainframe,$mapClass,$configClass,$jinput;
		$Itemid = $jinput->getint('Itemid',0);
		$order_id = $jinput->getInt('order_id',0);
		$db = JFactory::getDbo();
		//$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		//$order = $db->loadObject();
		require_once(JPATH_ROOT."/administrator/components/com_osservicesbooking/tables/order.php");
		$order = JTable::getInstance('Order', 'OsAppTable');
		$order->load($order_id);
		$data['payment_method'] 			= $order->order_payment;
		$data['x_card_num'] 				= base64_decode($order->order_card_number);
		$data['x_card_code'] 				= $order->order_cvv_code;
		$data['card_holder_name'] 			= $order->order_card_holder;
		$data['exp_year'] 					= $order->order_card_expiry_year;
		$data['exp_month'] 					= $order->order_card_expiry_month;
		$data['card_type'] 					= $order->order_card_type;
		$data['address'] 					= $order->order_address;
		$data['city'] 						= $order->order_city;
		$data['state'] 						= $order->order_state;
		$data['zip'] 						= $order->order_zip;
		$data['phone']						= $order->order_phone;
		$data['bank_id']					= $order->bank_id;
		$data['stripeToken']				= $order->bank_id;
		$data['nonce']                      = $order->bank_id;
        $data['TransactionToken']           = $order->bank_id;
		$order_name 						= $order->order_name;
		$order_name							= explode(" ",$order_name);
		if(count($order_name) > 1)
		{
			$first_name = $order_name[0];
			$last_name  = "";
			for($i=1;$i<count($order_name);$i++)
			{
				$last_name = $order_name[$i]." ";
			}
		}
		$order_country						= $order->order_country;
		if($order_country == "")
		{
			$order_country = "US";
		}
		else
		{
			$db->setQuery("Select country_code from #__app_sch_countries where country_name like '$order_country'");
			$order_country = $db->loadResult();
		}
		$data['country']					= $order_country;
		$data['first_name'] 				= $first_name;
		$data['last_name'] 					= $last_name;
		$data['email'] 						= $order->order_email;
		$data['amount']						= $order->order_upfront;
		$data['currency']					= $configClass['currency_format'];
		$data['curr']						= $configClass['currency_format'];
		$data['item_name']					= JText::_('OS_PAYMENT_FOR_SERVICES_BOOKING_REQUEST');
		
		$order_payment = $order->order_payment;
		if($configClass['disable_payments'] == 1)
		{
			if($order_payment == "")
			{
				throw new Exception( JText::_('Opps, there is a problem with Booking Progress, please try to make booking again later!') );
			}
			else
			{
				require_once JPATH_COMPONENT.'/plugins/'.$order_payment.'.php';
				$sql = 'SELECT params FROM #__app_sch_plugins WHERE name="'.$order_payment.'"';
				$db->setQuery($sql) ;
				$plugin = $db->loadObject();
				$params = $plugin->params ;
				$params = new JRegistry($params) ;
				$paymentClass = new $order_payment($params) ;  
				$paymentClass->processPayment($order, $data);
			}
		}
		else
		{
			$db->setQuery("Update #__app_sch_orders set order_status  = 'P' where id = '$order->id'");
			$db->execute();
			OsAppscheduleDefault::paymentComplete($order->id);
			$mainframe->redirect(JURI::root()."index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=".$order_id."&ref=".md5($order_id)."&Itemid=".$Itemid);	
		}
	}

	
	/**
	 * Complete Order
	 *
	 * @param unknown_type $option
	 */
	static function completeOrder($option)
	{
		global $mainframe,$mapClass,$configClass,$languages,$jinput;
		$db                     = JFactory::getDbo();
		$user                   = JFactory::getUser();
		$userId					= $user->id;
		if($userId > 0)
		{
			if (($user->authorise('osservicesbooking.orders', 'com_osservicesbooking') && $user->authorise('core.manage', 'com_osservicesbooking')) || OSBHelper::isEmployee())
			{
				$userId			= $jinput->getInt('user_id', $userId);
			}
		}

		$order_email			= $jinput->getString('order_email','');
		if($userId > 0 || $order_email != "")
		{	
			if($userId > 0)
			{
				$email = $user->email;
			}
			elseif($order_email != "")
			{
				$email = $order_email;
			}

			//check limit booking
			if((int)$configClass['limit_booking'] > 0)
			{
				$limit_by   = $configClass['limit_by'];
				$limit_type = $configClass['limit_type'];
				if($limit_type == 0)
				{
					if(!OSBHelper::checkLimitBooking($email, $limit_by, $configClass['limit_booking'], $db))
					{
						switch ($limit_by)
						{
							case "0":
								$msg = JText::_('OS_YOU_CANNOT_MAKE_BOOKING_TODAY_ANYMORE');
							break;
							case "1":
								$msg = JText::_('OS_YOU_CANNOT_MAKE_BOOKING_THISWEEK_ANYMORE');
							break;
							case "2":
								$msg = JText::_('OS_YOU_CANNOT_MAKE_BOOKING_THISMONTH_ANYMORE');
							break;
						}
						//throw new Exception($msg, 500);
						$session = Factory::getSession();
						$session->set('errorReason', $msg);
						self::redirecterrorform();
					}
				}
			}
		}
		$lang                   = JFactory::getLanguage();
		$lang                   = $lang->getTag();
		//check Google reCaptcha
        //only check reCaptcha if recaptcha is enabled and by passcaptcha is no
        $employee_id            = $jinput->getInt('employee_id',0);
        $category_id            = $jinput->getInt('category_id',0);
        $vid		            = $jinput->getInt('vid',0);
        $service_id	            = $jinput->getInt('service_id',0);
        $passcaptcha            = 0;
        if($user->id > 0 && $configClass['pass_captcha'] == 1){
            $passcaptcha        = 1;
        }
        if($configClass['value_sch_include_captcha'] == 3 && $passcaptcha == 0 && $configClass['remove_confirmation_step'] == 1){
            $captchaPlugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
            $res           = JCaptcha::getInstance($captchaPlugin)->checkAnswer($jinput->post->get('recaptcha_response_field', '', 'string'));
            if (!$res)
            {
				$mainframe->enqueueMessage(JText::_('OS_CAPTCHA_IS_INVALID'));
                $mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&task=form_step1&employee_id='.$employee_id.'&vid='.$vid.'&service_id='.$service_id.'&category_id='.$category_id));
            }
        }
		//before create the order, checking in the table order first
		//$unique_cookie = $_COOKIE['unique_cookie'];
		$unique_cookie = $jinput->get('unique_cookie','','string');
		@setcookie('unique_cookie',$unique_cookie,time()+1000,'/');
		if($unique_cookie != "")
		{
			$db->setQuery("Select id from #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
			$temp_order_id = $db->loadResult();
			if((int)$temp_order_id == 0)
			{
				//return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
				throw new Exception (JText::_('JERROR_ALERTNOAUTHOR'));
			}
			$db->setQuery("Select * from #__app_sch_temp_order_items where order_id = '$temp_order_id'");
			$orders = $db->loadObjectList();
			if(count($orders) == 0)
			{
				throw new Exception (JText::_('JERROR_ALERTNOAUTHOR'));
			}
			else 
			{
				if($userId > 0 || $order_email != "")
				{	
					if($userId > 0)
					{
						$email = $user->email;
					}
					elseif($order_email != "")
					{
						$email = $order_email;
					}

					//check limit booking
					if((int)$configClass['limit_booking'] > 0)
					{
						$limit_by   = $configClass['limit_by'];
						$limit_type = $configClass['limit_type'];

						if($limit_type == 1)
						{
							foreach($orders as $order)
							{
								if(!OSBHelper::checkLimitBookingOrderItems($email, $limit_by, $configClass['limit_booking'], $db, $order, $unique_cookie))
								{
									$config = JFactory::getConfig();
									$bdate  = $order->booking_date;
									$tdate	= JFactory::getDate($bdate, $config->get('offset'));

									switch ($limit_by)
									{
										case "0":
											$msg = sprintf(JText::_('OS_YOU_CANNOT_MAKE_BOOKING_DATE_ANYMORE'), $order->booking_date);
										break;
										case "1":
											$monday = clone $tdate->modify( 'Monday this week');
											$monday->setTime(0, 0, 0);
											$monday->setTimezone(new DateTimeZone('UCT'));
											$fromDate = $monday->format($configClass['date_format']);
											$sunday   = clone $tdate->modify('Sunday this week');
											$sunday->setTime(23, 59, 59);
											$sunday->setTimezone(new DateTimeZone('UCT'));
											$toDate   = $sunday->format($configClass['date_format']);
											$msg = sprintf(JText::_('OS_YOU_CANNOT_MAKE_BOOKING_WEEK_ANYMORE'), JText::_('OS_FROM').': '.$fromDate. ' '.JText::_('OS_TO').': '.$toDate);
										break;
										case "2":
											//$tdate->setDate($tdate->year, $tdate->month, 1);
											$month = $tdate->format('M Y');
											$msg = sprintf(JText::_('OS_YOU_CANNOT_MAKE_BOOKING_MONTH_ANYMORE'), $month);
										break;
									}
									//throw new Exception($msg, 500);
									$session = Factory::getSession();
									$session->set('errorReason', $msg);
									self::redirecterrorform();
								}
							}
						}
					}
				}

				if($configClass['using_cart'] == 0)
				{
					$order			= $orders[0];
					$db->setQuery("Select * from #__app_sch_services where id = '$order->sid'");
					$service		= $db->loadObject();
					if($service->service_time_type == 0)
					{
						$db->setQuery("Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on a.order_id = b.id where a.sid = '$order->sid' and a.eid = '$order->eid' and a.start_time = '$order->start_time' and a.end_time = '$order->end_time' and a.booking_date = '$order->booking_date' and b.order_status in ('P','S')");
						$countItems = $db->loadResult();
						if($countItems > 0)
						{
							$db->setQuery("Delete from #__app_sch_temp_order_items where order_id = '$temp_order_id'");
							$db->execute();
							$db->setQuery("Delete from #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
							$db->execute();
							//throw new Exception ( JText::_('OS_THE_SELECTED_TIMESLOT_IS_NOT_AVAILABLE_ANYMORE'));
							$session = Factory::getSession();
							$session->set('errorReason', JText::_('OS_THE_SELECTED_TIMESLOT_IS_NOT_AVAILABLE_ANYMORE'));
							self::redirecterrorform();
						}
					}
					elseif($service->service_time_type == 1) //custom timeslots
					{
						$unique_cookie		= OSBHelper::getUniqueCookie();
						$start_time 		= $order->start_time;
						$end_time   		= $order->end_time;
						$booking_date 		= $order->booking_date;
						$sid				= $order->sid;
						$eid 				= $order->eid;
						$nslots 			= $order->nslots;
						$temp_start_min 	= intval(date("i",$start_time));
						$temp_start_hour  	= intval(date("H",$start_time));
						$temp_end_min   	= intval(date("i",$end_time));
						$temp_end_hour  	= intval(date("H",$end_time));

						$db->setQuery("Select nslots from #__app_sch_custom_time_slots where sid = '$sid' and start_hour = '$temp_start_hour' and start_min = '$temp_start_min' and end_hour = '$temp_end_hour' and end_min = '$temp_end_min'");
						$number_slots_in_db = $db->loadResult();
						
						//select in order_items
						$query = "Select sum(a.nslots) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where a.sid = '$sid' and a.eid = '$eid' and a.start_time = '$start_time' and a.end_time = '$end_time' and b.order_status IN ('P','S','A')";
						$db->setQuery($query);
						$remain_slots = $db->loadResult();

						//this code part are difference with checkCustomSlots
						$query = "Select sum(a.nslots) from #__app_sch_temp_order_items as a inner join #__app_sch_temp_orders as b on b.id = a.order_id where b.unique_cookie not like '$unique_cookie' and a.sid = '$sid' and a.eid = '$eid' and a.start_time = '$start_time' and a.end_time = '$end_time'";
						$db->setQuery($query);
						$count = $db->loadResult();
						
						$number_slots_available = $number_slots_in_db - $count - $remain_slots;
						if($number_slots_available < $nslots)
						{
							$db->setQuery("Delete from #__app_sch_temp_order_items where order_id = '$temp_order_id'");
							$db->execute();
							$db->setQuery("Delete from #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
							$db->execute();
							//throw new Exception ( JText::_('OS_THE_SELECTED_TIMESLOT_IS_NOT_AVAILABLE_ANYMORE'));
							$session = Factory::getSession();
							$session->set('errorReason', JText::_('OS_THE_SELECTED_TIMESLOT_IS_NOT_AVAILABLE_ANYMORE'));
							self::redirecterrorform();
						}
					}
				}
			}
			//create the order
            //updated
			$order_price = OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem();
			$db->setQuery("Select id from #__app_sch_fields where published = '1' and field_area = '1' order by ordering");
			$fields = $db->loadObjectList();
			if(count($fields)>0)
			{
				for($i=0;$i<count($fields);$i++)
				{
					$fid = $fields[$i]->id;
					$fieldvalue = OSBHelper::getStringValue('field_'.$fid,'');
					if($fieldvalue != "")
					{
						$db->setQuery("Select id,field_label,field_type from #__app_sch_fields where  id = '$fid'");
						$field = $db->loadObject();
						$field_type = $field->field_type;
						if($field_type == 1)
						{
							$db->setQuery("Select * from #__app_sch_field_options where id = '$fieldvalue'");
							$optionvalue = $db->loadObject();
							if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0))
							{
								$order_price += $optionvalue->additional_price;
							}
						}
						elseif($field_type == 2)
						{
							$fieldValueArr = explode(",",$fieldvalue);
							if(count($fieldValueArr) > 0){
								for($j=0;$j<count($fieldValueArr);$j++)
								{
									$temp = $fieldValueArr[$j];
									$db->setQuery("Select * from #__app_sch_field_options where id = '$temp'");
									$optionvalue = $db->loadObject();
									if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0))
									{
										$order_price += $optionvalue->additional_price;
									}
								}
							}
						}
					}
				}
			}
			
			//calculate price based on $order_price;
			$order_price_before_discount = $order_price;
			$groupDiscount = OSBHelper::getOrderGroupDiscount();
			$order_price  -= $groupDiscount;

			$coupon_id 							= $jinput->getInt('coupon_id',0);
			if($coupon_id > 0)
			{
				$db->setQuery("Select * from #__app_sch_coupons where id = '$coupon_id'");
				$coupon = $db->loadObject();
				$max_user_use = $coupon->max_user_use;
				$max_total_use = $coupon->max_total_use;
				if($max_total_use > 0)
				{
					$db->setQuery("Select count(id) from #__app_sch_coupon_used where coupon_id = '$coupon_id'");
					$nused = $db->loadResult();
					if($nused >= $max_total_use)
					{
						$coupon_id = 0;
					}
				}
				if($max_user_use > 0 && $coupon_id > 0)
				{
					if($user->id > 0)
					{
						$db->setQuery("Select count(id) from #__app_sch_coupon_used where user_id = '$user->id' and coupon_id = '$coupon_id'");
						$nused = $db->loadResult();
						if($nused >= $max_user_use)
						{
							$coupon_id = 0;
						}
					}
				}
			}
			$discount_amount = 0;
			if($coupon_id > 0)
			{
				$db->setQuery("Select * from #__app_sch_coupons where id = '$coupon_id'");
				$coupon = $db->loadObject();
				if($coupon->discount_type == 0)
				{
					$discount_amount = $order_price*$coupon->discount/100;
				}else{
					$discount_amount = $coupon->discount;
				}
			}
			$order_total_temp					= $order_price - $discount_amount;
			if($order_total_temp <= 0)
			{
				$discount_amount				= $order_price;
				$order_price					= 0;
			}
			else
			{
				$order_price					= $order_total_temp;
			}

			//$user								= JFactory::getUser();

			$tax								= round((float)$configClass['tax_payment']*$order_price/100,2);
			$order_total						= $order_price + $tax;

			$payfull							= $jinput->getInt('payfull',0);
			if($payfull == 1)
			{
				$deposit						= $order_total;
			}
			else
			{
				$deposit						= OSBHelper::getDepositAmount($order_total);
			}
			$row 								= &JTable::getInstance('Order','OsAppTable');
			$row->id = 0;
			$row->user_id						= $userId;
			$row->order_name 					= $jinput->get('order_name','','string');
			$row->order_email 					= $jinput->get('order_email','','string');
			$order_phone						= $jinput->get('order_phone','','string');
			if((substr($order_phone,0,1) == "0") && ($configClass['clickatell_showcodelist'] == 1))
			{
				$order_phone					= trim(substr($order_phone,1));
			}
			$row->order_phone					= $order_phone;
			$row->order_country 				= $jinput->get('order_country','','string');
			$row->order_state 					= $jinput->get('order_state','','string');
			$row->order_city					= $jinput->get('order_city','','string');
			$row->order_zip 					= $jinput->get('order_zip','','string');
			$row->order_address 				= $jinput->get('order_address','','string');
			$dial_code							= $jinput->getInt('dial_code',$configClass['clickatell_defaultdialingcode']);
			if($dial_code > 0)
			{
				$row->dial_code					= OSBHelper::getDialCode($dial_code);
			}
			else
			{
				$row->dial_code					= '';
			}
			$row->order_total					= $order_price_before_discount;
			$row->order_tax						= $tax;
			$row->order_final_cost				= $order_total;
			$row->receive_reminder				= $jinput->getInt('receive_reminder',0);

			if($configClass['disable_payments'] == 0)
			{
				$row->order_status				= $configClass['disable_payment_order_status'];
			}
			elseif($row->order_final_cost == 0)
			{
				$row->order_status				= 'S';
			}
			else
			{
				$row->order_status				= 'P';
			}
			$row->order_date					= date("Y-m-d H:i:s",HelperOSappscheduleCommon::getRealTime());
			$row->order_lang				 	= $lang;
			if($configClass['remove_confirmation_step'] == 1)
			{
				$row->order_payment = OSBHelper::getStringValue('payment_method', '');
			}
			else 
			{
				$row->order_payment = OSBHelper::getStringValue('select_payment', '');
			}

			if($row->order_payment != "")
			{
				$query = $db->getQuery(true);
				$query->select('params')
						->from('#__app_sch_plugins')
						->where('name=' . $db->quote($row->order_payment))
						->where('published = 1');
				$db->setQuery($query);
				$plugin = $db->loadObject();
				if (!$plugin)
				{
					throw new Exception(JText::sprintf('The payemnt method %s is not available', $paymentMethod), 403);
				}

				$params = new JRegistry($plugin->params);

				$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
				$paymentFeePercent = (float) $params->get('payment_fee_percent');

				if ($paymentFeeAmount > 0 || $paymentFeePercent > 0)
				{
					$payment_plugin_fee		= round($paymentFeeAmount + $deposit * $paymentFeePercent / 100, 2);
					$deposit				= round($deposit + $payment_plugin_fee, 2);
					$row->payment_fee		= $payment_plugin_fee;
				}

				if($row->order_payment == 'os_prepaid' && $user->id > 0 && $deposit > 0)
				{
					$user_balances = 0;
					$db->setQuery("Select count(id) from #__app_sch_user_balance where user_id = '$user->id'");
					$count = $db->loadResult();
					if($count > 0)
					{
						$db->setQuery("Select * from #__app_sch_user_balance where user_id = '$user->id'");
						$balance = $db->loadObject();
						$user_balances = $balance->amount;
					}
					if($user_balances < $deposit)
					{
						$mainframe->enqueueMessage(Jtext::_('OS_YOU_DONT_HAVE_ENOUGH_FUND_TO_COMPLETE_THE_ORDER'));
						$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&task=form_step1&Itemid='.$jinput->getInt('Itemid',0)));
					}
				}
			}
			$row->order_upfront					= $deposit;

			$notes								= $jinput->getString('notes','');
			$row->order_notes					= $notes;
			$row->order_card_number				= base64_encode($jinput->get('x_card_num','','string'));
			$row->order_cvv_code				= $jinput->get('x_card_code','','string');
			$row->order_card_holder				= $jinput->get('card_holder_name','','string');
			$row->order_card_expiry_year		= (int)$jinput->get('exp_year','','string');
			$row->order_card_expiry_month		= (int)$jinput->get('exp_month','','string');
			$row->order_card_type				= $jinput->get('card_type','','string');
			$row->coupon_id						= $coupon_id;
			$row->params						= '';
			$row->order_discount				= $discount_amount + $groupDiscount;
			if($row->order_payment == "os_stripe") 
			{
                $row->bank_id                   = $jinput->get('stripeToken', '', 'string');
            }
			elseif($row->order_payment == "os_squareup")
			{
                $row->bank_id                   = $jinput->get('nonce', '', 'string');
            }
			elseif($row->order_payment == "os_velocity")
			{
                $row->bank_id                   = $jinput->get('TransactionToken', '', 'string');
			}
			else
			{
				$row->bank_id					= $jinput->get('bank_id','','string');
			}
			$row->payment_fee					= (float) $row->payment_fee;
			
			if(!$row->store())
			{
				throw new Exception ($row->getError());
			}
			$order_id							= $db->insertID();

			//store core fields to profile
			$profile = JTable::getInstance('Profile','OsAppTable');
			if($userId > 0)
			{
				$db->setQuery("Select count(id) from #__app_sch_userprofiles where user_id = '$userId'");
				$count = $db->loadResult();
				if($count > 0)
				{
					$db->setQuery("Select id from #__app_sch_userprofiles where user_id = '$userId'");
					$id = $db->loadResult();
					$profile->id = $id;
				}
				else
				{
					$profile->id = 0;
				}
				$profile->user_id 		= $userId;
				$profile->order_name 	= $jinput->get('order_name','','string');
				$profile->order_email 	= $jinput->get('order_email','','string');
				$profile->order_phone 	= $jinput->get('order_phone','','string');
				$profile->order_country = $jinput->get('order_country','','string');
				$profile->order_address = $jinput->get('order_address','','string');
				$profile->order_state 	= $jinput->get('order_state','','string');
				$profile->order_city 	= $jinput->get('order_city','','string');
				$profile->order_zip 	= $jinput->get('order_zip','','string');
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
							OSBHelper::updateUserProfile($userId, $mappingField , $profile->{'order_'.$cfield});
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
							OSBHelper::updateUserProfile($userId, $mappingField , $profile->{'order_'.$cfield});
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
			//end update user profile

            //add qrcode
            if($configClass['use_qrcode'])
			{
                OSBHelper::generateQrcode($order_id);
            }

			if($row->coupon_id > 0)
			{
				//added into coupon used table
				$db->setQuery("Insert into #__app_sch_coupon_used (id,user_id,coupon_id,order_id) values (NULL,'$user->id','$row->coupon_id','$order_id')");
				$db->execute();
			}
			
			//update order items table
			
			if(count($orders) > 0)
			{
				for($i=0;$i<count($orders);$i++)
				{
					$orderdata = $orders[$i];
					$db->setQuery("INSERT INTO #__app_sch_order_items (id,order_id,sid,eid,vid,start_time,end_time,booking_date,nslots,total_cost,additional_information,gcalendar_event_id) VALUES (NULL,$order_id,'$orderdata->sid','$orderdata->eid','$orderdata->vid','$orderdata->start_time','$orderdata->end_time','$orderdata->booking_date','$orderdata->nslots','$orderdata->total_cost','','')");
					$db->execute();
					$order_item_id = $db->insertID();
					
					$db->setQuery("Delete from #__app_sch_temp_order_items where id = '$orderdata->id'");
					$db->execute();
					
					$db->setQuery("Select * from #__app_sch_temp_order_field_options where order_item_id = '$orderdata->id'");
					$addArr = $db->loadObjectList();
					
					$field_amount = 0;
					$field_data   = "";
					if(count($addArr) > 0)
					{
						for($i1=0;$i1<count($addArr);$i1++)
						{
							$addtemp = $addArr[$i1];
							$db->setQuery("INSERT INTO #__app_sch_order_field_options (id, order_item_id,field_id,option_id) VALUES (NULL,'$order_item_id','$addtemp->field_id','$addtemp->option_id')");
							$db->execute();
							$db->setQuery("Delete from #__app_sch_temp_order_field_options where id = '$addtemp->id'");
							$db->execute();
						}
					}
				}
				//break;
			}
			//break;
			//add custom fields into the table order booking
			$db->setQuery("Select id from #__app_sch_fields where published = '1' and field_area = '1' order by ordering");
			$fields = $db->loadObjectList();
			if(count($fields)>0)
			{
				for($i=0;$i<count($fields);$i++)
				{
					$fid = $fields[$i]->id;
					$fieldvalue = OSBHelper::getStringValue('field_'.$fid,'');
					$db->setQuery("Select id,field_label,field_type,field_mapping from #__app_sch_fields where  id = '$fid'");
					$field = $db->loadObject();
					$field_type = $field->field_type;
					if($fieldvalue != "")
					{
						if($field_type == 0)
						{
							$db->setQuery("Select count(id) from #__app_sch_field_data where fid = '$fid' and order_id = '$order_id'");
							$count	   = $db->loadResult();
							if((int) $count == 0)
							{
								$fielddata = &JTable::getInstance('FieldData','OsAppTable');
								$fielddata->id 			= 0;
								$fielddata->order_id 	= $order_id;
								$fielddata->fid			= $fid;
								$fielddata->fvalue 		= $fieldvalue;
								$fielddata->store();

								if(($configClass['field_integration'] == 1 || $configClass['field_integration'] == 2) && $field->field_mapping != "")
								{
									OSBHelper::updateUserProfile($user->id, $field->field_mapping, $fieldvalue);
								}
							}
						}
						elseif($field_type == 1)
						{
							$fielddata = &JTable::getInstance('OrderField','OsAppTable');
							$fielddata->id 			= 0;
							$fielddata->order_id 	= $order_id;
							$fielddata->field_id	= $fid;
							$fielddata->option_id 	= $fieldvalue;
							$fielddata->store();

							if(($configClass['field_integration'] == 1 || $configClass['field_integration'] == 2) && $field->field_mapping != "")
							{
								OSBHelper::updateUserProfile($user->id, $field->field_mapping, $fieldvalue);
							}
						}
						elseif($field_type == 2)
						{
							$fieldValueArr = explode(",",$fieldvalue);
							if(count($fieldValueArr) > 0)
							{
								for($j=0;$j<count($fieldValueArr);$j++)
								{
									$temp = $fieldValueArr[$j];
									$fielddata = &JTable::getInstance('OrderField','OsAppTable');
									$fielddata->id = 0;
									$fielddata->order_id 	= $order_id;
									$fielddata->field_id	= $fid;
									$fielddata->option_id 	= $temp;
									$fielddata->store();
								}
							}
						}
						elseif($field_type == 3 || $field_type == 4)
						{
							$fielddata = &JTable::getInstance('FieldData','OsAppTable');
							$fielddata->id 			= 0;
							$fielddata->order_id 	= $order_id;
							$fielddata->fid			= $fid;
							$fielddata->fvalue 		= $fieldvalue;
							$fielddata->store();
						}
					}

					if($configClass['remove_confirmation_step'] == 1)
					{
						if($field_type == 3)
						{
							$photo_name                     = "field_".$fid;
							$fvalue                         = "";
							$field_data                     = "";
							if(is_uploaded_file($_FILES[$photo_name]['tmp_name'])){
								if(OSBHelper::checkIsPhotoFileUploaded($photo_name)){
									$image_name             = $_FILES[$photo_name]['name'];
									$image_name             = OSBHelper::processImageName($image_name);
									$original_image_link    = JPATH_ROOT."/images/osservicesbooking/fields/".$image_name;
									move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
									$field_data             = "<img src='".JUri::root()."images/osservicesbooking/fields/".$image_name."' width='120'/>";
									$fieldvalue             = $image_name;
								}
							}
						}
						elseif($field_type == 4)
						{
							$photo_name                     = "field_".$fid;
							$fvalue                         = "";
							$field_data                     = "";
							if(is_uploaded_file($_FILES[$photo_name]['tmp_name'])){
								if(OSBHelper::checkIsFileUploaded($photo_name)){
									$image_name             = $_FILES[$photo_name]['name'];
									$image_name             = OSBHelper::processImageName($image_name);
									$original_image_link    = JPATH_ROOT."/images/osservicesbooking/fields/".$image_name;
									move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
									$field_data             = "<a href='".JUri::root()."images/osservicesbooking/fields/".$image_name."' target='_blank'>".$image_name."</a>";
									$fieldvalue             = $image_name;
								}
							}
						}

						$fielddata = &JTable::getInstance('FieldData','OsAppTable');
						$fielddata->id 			= 0;
						$fielddata->order_id 	= $order_id;
						$fielddata->fid			= $fid;
						$fielddata->fvalue 		= $fieldvalue;
						$fielddata->store();
					}
				}
			}
			//empty the cookie
			//@setcookie('','',HelperOSappscheduleCommon::getRealTime()-3600,'/');
			if($configClass['disable_payments'] == 1)
			{
				if($configClass['value_enum_email_confirmation'] == 2)
				{
					HelperOSappscheduleCommon::sendEmail('confirm',$order_id);
					HelperOSappscheduleCommon::sendEmployeeEmail('employee_notification_new',$order_id,0);
					HelperOSappscheduleCommon::sendEmail('admin',$order_id);
					HelperOSappscheduleCommon::sendSMS('confirm',$order_id);
					HelperOSappscheduleCommon::sendSMS('admin',$order_id);
                    HelperOSappscheduleCommon::sendSMS('confirmtoEmployee',$order_id);
					HelperOSappscheduleCommon::updateAcyMailing($order_id);
				}
				
				if($row->order_final_cost == 0 || $deposit == 0)
				{
					if($row->order_payment == "os_offline" || $row->order_payment == "os_offline1")
					{
						$mainframe->redirect($configClass['root_link']."index.php?option=com_osservicesbooking&task=default_payment&order_id=".$order_id."&Itemid=".$jinput->getInt('Itemid',0));
					}
					else
					{
						//make the order become completed automatically
						$db->setQuery("UPDATE #__app_sch_orders SET order_status = 'S' WHERE id = '$order_id'");
						$db->execute();
						
						JPluginHelper::importPlugin('osservicesbooking');

						$results = array();
						$results = $mainframe->triggerEvent('onOrderActive', array($row));	
		
						if($configClass['value_enum_email_confirmation'] == 3)
						{
							HelperOSappscheduleCommon::sendEmail('confirm',$order_id);
							HelperOSappscheduleCommon::sendEmployeeEmail('employee_notification_new',$order_id,0);
							HelperOSappscheduleCommon::sendEmail('admin',$order_id);
							HelperOSappscheduleCommon::sendSMS('confirm',$order_id);
							HelperOSappscheduleCommon::sendSMS('admin',$order_id);
							HelperOSappscheduleCommon::sendSMS('confirmtoEmployee',$order_id);
							HelperOSappscheduleCommon::updateAcyMailing($order_id);
						}
						// validate input
						$config = new JConfig();
						$offset = $config->offset;
						date_default_timezone_set($offset);
						OSBHelper::updateGoogleCalendar($order_id);
						$mainframe->redirect(JURI::root()."index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=".$order_id."&ref=".md5($order_id)."&Itemid=".$jinput->getInt('Itemid',0));
					}
				}
				else
				{
					$mainframe->redirect($configClass['root_link']."index.php?option=com_osservicesbooking&task=default_payment&order_id=".$order_id."&Itemid=".$jinput->getInt('Itemid',0));
				}
			}
			else
			{
				if(($configClass['value_enum_email_confirmation'] == 2) && ($configClass['disable_payments'] == 0)) 
				{
                    HelperOSappscheduleCommon::sendEmail('confirm', $order_id);
                }
                if($configClass['disable_payments'] == 0)
				{
					
					HelperOSappscheduleCommon::sendEmail('admin',$order_id);
					HelperOSappscheduleCommon::sendEmployeeEmail('employee_notification_new',$order_id,0);
					HelperOSappscheduleCommon::sendSMS('confirm',$order_id);
					HelperOSappscheduleCommon::sendSMS('admin',$order_id);
                    HelperOSappscheduleCommon::sendSMS('confirmtoEmployee',$order_id);
					// validate input
					$config = new JConfig();
					$offset = $config->offset;
					date_default_timezone_set($offset);
					OSBHelper::updateGoogleCalendar($order_id);
					HelperOSappscheduleCommon::updateAcyMailing($order_id);
				}
				//$mainframe->redirect($configClass['page_location']);
				$mainframe->redirect(JURI::root()."index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=".$order_id."&ref=".md5($order_id)."&Itemid=".$jinput->getInt('Itemid',0));
			}
		}
	}
	
	/**
	 * Process cron task
	 *
	 */
	static function cron(){
		global $mainframe,$mapClass, $configClass;
		$db = JFactory::getDbo();
		$current_time = HelperOSappscheduleCommon::getRealTime();
		$reminder = $configClass['value_sch_reminder_email_before'];
		$reminder = $current_time + $reminder*3600;
		$query = "Select a.* from #__app_sch_order_items as a"
				." inner join #__app_sch_orders as b on b.id = a.order_id"
				." where a.start_time <= '$reminder' and a.start_time > '$current_time' and b.order_status = 'S' and a.id not in (Select order_item_id from #__app_sch_cron)";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				HelperOSappscheduleCommon::sendEmail('reminder',$row->id);
				HelperOSappscheduleCommon::sendSMS('reminder',$row->order_id);
				//add into the cron table
				$db->setQuery("Insert into #__app_sch_cron (id,order_item_id) values (NULL,'$row->id')");
				$db->execute();
			}
		}
	}
	
	/**
	 * Cancel the order
	 *
	 */
	static function cancelOrder()
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$db					= JFactory::getDbo();
		$id					= $jinput->getInt('id',0);
		$user				= JFactory::getUser();
		if($user->id == 0)
		{
			OSBHelper::requestLogin();
		}
		else
		{
			if($id > 0)
			{
				$db->setQuery("Select id,user_id, order_status from #__app_sch_orders where id = '$id'");
				$order			= (int) $db->loadObject();

				if($order->id > 0 && $order->order_status == "C")
				{
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>" id="ordercancelError">
						<div class="<?php echo $mapClass['span12'];?>">
							<h2>
								<?php echo JText::_('OS_OPPS_ERROR');?>
							</h2>
							<?php echo JText::_('OS_THIS_ORDER_WAS_CANCELLED_BEFORE');?>
						</div>
					</div>
					<?php
				}
				else
				{
					if($order->id > 0 && ((int)$order->user_id == 0) || ((int)$order->user_id > 0 && $order->user_id == $user->id))
					{
						$retund			= 0;
						$ref			= OSBHelper::getStringValue('ref','');
						$ide			= md5($id);
						$cancel_before	= $configClass['cancel_before'];
						$db->setQuery("Select a.start_time from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.id = '$id' order by a.start_time");
						$earliest = $db->loadResult();
						
						$current_time	= HelperOSappscheduleCommon::getRealTime();
						if($current_time + $cancel_before*3600 < $earliest && $ide == $ref)
						{	
							$doCancel = $jinput->getInt('doCancel',0);
							if($doCancel == 0)
							{
								?>
								<div class="<?php echo $mapClass['row-fluid'];?>" id="ordercancelConfirm">
									<div class="<?php echo $mapClass['span12'];?>" style="text-align:center;">
										<h2>
											<?php echo JText::_('OS_DO_YOU_WANT_TO_CANCEL_THE_ORDER')?> (#<?php echo $id?>)
										</h2>
										<BR />
										<strong>
										<a href="<?php echo JUri::root();?>index.php?option=com_osservicesbooking&task=default_cancelorder&id=<?php echo $id; ?>&ref=<?php echo $ref;?>&doCancel=1" title="<?php echo JText::_('OS_I_WANT_TO_CANCEL_THIS_ORDER');?>"><?php echo JText::_('JYES');?></a>
										&nbsp;&nbsp;|&nbsp;&nbsp;
										<a href="<?php echo JUri::root(); ?>" title="<?php echo JText::_('OS_I_DO_NOT_WANT_TO_CANCEL_THIS_ORDER');?>"><?php echo JText::_('JNO');?></a>
										</strong>
									</div>
								</div>
								<?php
							}
							else
							{
								//process to refund money back
								$msg = "";
								if($configClass['disable_payments'] == 1)
								{
									$query  = $db->getQuery(true);
									$query->select('*')
										->from('#__app_sch_orders')
										->where('id = ' . $id);
									$db->setQuery($query);
									$row = $db->loadObject();
									if(OSBHelper::canRefundOrder($row))
									{
										$method = os_payments::getPaymentMethod($row->order_payment);

										if($method->refund($row))
										{
											$query = $db->getQuery(true)
													->update('#__app_sch_orders')
													->set('refunded = 1')
													->where('id = ' . $row->id);
											$db->setQuery($query)
													->execute();

											$refund = 1;
										}
									}
								}

								$db->setQuery("UPDATE #__app_sch_orders SET order_status = 'C' WHERE id = '$id'");
								$db->execute();
								
								//send notification email
								HelperOSappscheduleCommon::sendCancelledEmail($id);
								HelperOSappscheduleCommon::sendSMS('cancel',$id);
								HelperOSappscheduleCommon::sendEmail('customer_cancel_order',$id);
								//HelperOSappscheduleCommon::sendEmail('admin_order_cancelled',$id);
								HelperOSappscheduleCommon::sendEmployeeEmail('employee_order_cancelled_new',$id,$eid);
								if($configClass['integrate_gcalendar'] == 1)
								{
									OSBHelper::removeEventOnGCalendar($id);
								}
								if($configClass['waiting_list'] == 1)
								{
									OSBHelper::sendWaitingNotification($id);
								}
								?>
								<div class="<?php echo $mapClass['row-fluid'];?>" id="ordercancelComplete">
									<div class="<?php echo $mapClass['span12'];?>">
										<h2>
											<?php echo JText::_('OS_ORDER')?> (<?php echo $id?>) 
											<?php 
											if($refund == 0)
											{
												echo JText::_('OS_HAS_BEEN_CANCELLED');
											}
											else
											{
												echo JText::_('OS_HAS_BEEN_CANCELLED_AND_REFUNDED');
											}
											?>
										</h2>
									</div>
								</div>
								<?php
							}
						}
						else
						{
							?>
							<div class="<?php echo $mapClass['row-fluid'];?>" id="ordercancelFailure">
								<div class="<?php echo $mapClass['span12'];?>">
									<h2>
										<?php echo JText::_('OS_YOU_CANNOT_REQUEST_TO_CANCEL_BOOKING_REQUEST_ANYMORE');?>
									</h2>
								</div>
							</div>
						<?php
						}
					}
					else
					{
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>" id="ordercancelError">
							<div class="<?php echo $mapClass['span12'];?>">
								<h2>
									<?php echo JText::_('OS_OPPS_ERROR');?>
								</h2>
							</div>
						</div>
						<?php
					}
				}
			}
		}
	}
	
	/**
	 * Order fields in the booking form
	 *
	 * @param unknown_type $field
	 */
	static function orderFieldData($field,$order_id)
	{
		global $mainframe,$mapClass,$configClass;
		switch ($field->field_type){
			case "0":
				return OsAppscheduleDefault::orderInputboxData($field,$order_id);
			break;
			case "1":
				return OsAppscheduleDefault::orderSelectListData($field,$order_id);
			break;
			case "2":
				return OsAppscheduleDefault::orderCheckboxesData($field,$order_id);
			break;
			case "3":
            case "4":
				return OsAppscheduleDefault::orderFileUploadData($field,$order_id);
			break;
		}
	}
	
	/**
	 * Order fields in the booking form
	 *
	 * @param unknown_type $field
	 */
	static function orderField($field,$order_id)
	{
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$userId = 0;
		if($order_id > 0)
		{
			$db->setQuery("Select user_id from #__app_sch_orders where id = '$order_id'");
			$userId = $db->loadResult();
		}
		if($userId == 0 && !JFactory::getApplication()->isClient('administrator'))
		{
			$userId = $user->id;
		}
		
		if($configClass['field_integration'] == 1 && (int) $userId > 0 && $field->field_mapping != "")
		{
			$query = $db->getQuery(true);
			$query->select('profile_value')
				->from('#__user_profiles')
				->where('user_id=' . $userId)
				->where('profile_key = "profile.'.$field->field_mapping.'"');
			$db->setQuery($query);
			$field->value = $db->loadResult();
			$field->value = str_replace('"','',$field->value);
		}
		elseif($configClass['field_integration'] == 2 && (int) $userId > 0 && $field->field_mapping != "")
		{
			$query = $db->getQuery(true);
			$query->select($field->field_mapping)
				->from('#__jsn_users')
				->where('id=' . $userId);
			$db->setQuery($query);
			$field->value = $db->loadResult();
		}
		switch ($field->field_type){
			case "0":
				OsAppscheduleDefault::orderInputbox($field,$order_id);
			break;
			case "1":
				OsAppscheduleDefault::orderSelectList($field,$order_id);
			break;
			case "2":
				OsAppscheduleDefault::orderCheckboxes($field,$order_id);
			break;
			case "3":
				OsAppscheduleDefault::orderImage($field,$order_id);
			break;
            case "4":
                OsAppscheduleDefault::orderUpload($field,$order_id);
            break;
			case "5":
                OsAppscheduleDefault::showMessage($field,$order_id);
            break;
		}
	}

    static function orderUpload($field,$order_id)
    {
        global $configClass;
        $allowed_file_types = $configClass['allowed_file_types'];
        if($allowed_file_types == "")
        {
            $allowed_file_types = "pdf,doc,docx,xls,xlsx";
        }
        $preg = "";
        if($allowed_file_types != "")
        {
            $allowed_file_types = explode(",", $allowed_file_types);
            for($i= 0 ;$i<count($allowed_file_types);$i++)
            {
                $allowed_file_types[$i] = "\.".$allowed_file_types[$i];
            }
            $allowed_file_types = implode("|",$allowed_file_types);
            $preg = "/(".$allowed_file_types.")$/i";
        }
        $db = JFactory::getDbo();
        $db->setQuery("Select `fvalue` from #__app_sch_field_data where fid = '$field->id' and order_id = '$order_id'");
        $fvalue = $db->loadResult();
        if($fvalue != ""){
            if(file_exists(JPATH_ROOT.'/images/osservicesbooking/fields/'.$fvalue))
            {
                ?>
                <a href="<?php echo JUri::root();?>images/osservicesbooking/fields/<?php echo $fvalue;?>"><?php echo JText::_('OS_FILE_UPLOADED');?></a>
                <div class="clearfix"></div>
                <input type="checkbox" onClick="javascript:changeValue('remove_file_<?php echo $field->id;?>');" name="remove_file_<?php echo $field->id;?>" id="remove_file_<?php echo $field->id;?>" value="0" /> <?php echo JText::_('OS_REMOVE');?>
                <div class="clearfix"></div>
                <?php
            }
        }
        ?>
        <input type="hidden" value="<?php echo $fvalue;?>" name="old_field_<?php echo $field->id?>" id="old_field_<?php echo $field->id?>"/>
        <input type="file" onchange="return fileValidation<?php echo $field->id?>();" name="field_<?php echo $field->id?>" id="field_<?php echo $field->id?>" class="input-large form-control ilarge" />
        <input type="hidden" name="field_<?php echo $field->id?>_required" id="field_<?php echo $field->id?>_required" value="<?php echo $field->required?>" />
        <input type="hidden" name="field_<?php echo $field->id?>_label" id="field_<?php echo $field->id?>_label" value="<?php echo OSBHelper::getLanguageFieldValue($field,'field_label');?>" />
        <input type="hidden" name="field_<?php echo $field->id?>_type" id="field_<?php echo $field->id?>_type" value="image" />
        <script>
            function fileValidation<?php echo $field->id?>()
            {
                var fileInput = document.getElementById('field_<?php echo $field->id?>');

                var filePath = fileInput.value;

                // Allowing file type
                var allowedExtensions = <?php echo $preg;?>;

                if (!allowedExtensions.exec(filePath)) {
                    alert('<?php echo JText::_('OS_INVALID_FILE_TYPE');?>');
                    fileInput.value = '';
                    return false;
                }
            }
        </script>
        <?php
    }

	static function orderImage($field,$order_id){
		$db = JFactory::getDbo();
		$db->setQuery("Select `fvalue` from #__app_sch_field_data where fid = '$field->id' and order_id = '$order_id'");
		$fvalue = $db->loadResult();
		if($fvalue != ""){
			if(file_exists(JPATH_ROOT.'/images/osservicesbooking/fields/'.$fvalue)){
				?>
				<img src="<?php echo JUri::root();?>images/osservicesbooking/fields/<?php echo $fvalue;?>" width="120"/>
				<div class="clearfix"></div>
				<input type="checkbox" onClick="javascript:changeValue('remove_picture_<?php echo $field->id;?>');" name="remove_picture_<?php echo $field->id;?>" id="remove_picture_<?php echo $field->id;?>" value="0" /> <?php echo JText::_('OS_REMOVE');?>
				<div class="clearfix"></div>
				<?php
			}
		}
		?>
		<input type="hidden" value="<?php echo $fvalue;?>" name="old_field_<?php echo $field->id?>" id="old_field_<?php echo $field->id?>"/>
		<input type="file" name="field_<?php echo $field->id?>" id="field_<?php echo $field->id?>" class="input-large" />
		<input type="hidden" name="field_<?php echo $field->id?>_required" id="field_<?php echo $field->id?>_required" value="<?php echo $field->required?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_label" id="field_<?php echo $field->id?>_label" value="<?php echo OSBHelper::getLanguageFieldValue($field,'field_label');?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_type" id="field_<?php echo $field->id?>_type" value="image" />
		<?php
	}
	
	/**
	 * Show inputbox
	 *
	 * @param unknown_type $field
	 */
	static function orderInputbox($field,$order_id){
		$db = JFactory::getDbo();
		if($order_id > 0)
		{
			$db->setQuery("Select `fvalue` from #__app_sch_field_data where fid = '$field->id' and order_id = '$order_id'");
			$fvalue = $db->loadResult();
			if($fvalue == '' && $field->value != '')
			{
				$fvalue = $field->value;
			}
		}
		else
		{
			$fvalue = $field->value;
		}
		?>
		<input type="text" class="input-large form-control <?php echo $field->field_class; ?>" size="30" name="field_<?php echo $field->id?>" id="field_<?php echo $field->id?>" value="<?php echo $fvalue;?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_required" id="field_<?php echo $field->id?>_required" value="<?php echo $field->required; ?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_label" id="field_<?php echo $field->id?>_label" value="<?php echo OSBHelper::getLanguageFieldValue($field,'field_label');?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_type" id="field_<?php echo $field->id?>_type" value="input" />
		<?php
	}
	
	static function orderInputboxData($field,$order_id){
		$db = JFactory::getDbo();
		$db->setQuery("Select `fvalue` from #__app_sch_field_data where fid = '$field->id' and order_id = '$order_id'");
		//echo $db->getQuery();
		$fvalue = $db->loadResult();
		return $fvalue;
	}
	
	/**
	 * Show select list in booking form
	 *
	 * @param unknown_type $field
	 */
	static function orderSelectList($field,$order_id){
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		
		if($order_id > 0)
		{
			//find the option value of order in this field
			$query = $db->getQuery(true);
			$query->select('option_id');
			$query->from($db->quoteName('#__app_sch_order_options'));
			$query->where("order_id = '$order_id' and field_id = '$field->id'");
			$db->setQuery($query);
			$option_id = $db->loadResult();
		}
		else
		{
			if($field->value != "")
			{
				$db->setQuery("Select id from #__app_sch_field_options where field_id = '$field->id' and field_option like '$field->value'");
				$option_id = $db->loadResult();
			}
		}
		//echo $option_id;
		$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field->id'");
		$optionArr = $db->loadObjectList();
		//print_r($optionArr);
		?>
		<select name="field_<?php echo $field->id?>" id="field_<?php echo $field->id?>" class="input-small form-select <?php echo $field->field_class; ?>">
			<option value=""></option>
			<?php
			if(count($optionArr) > 0)
			{
				for($i=0;$i<count($optionArr);$i++)
				{
					$op = $optionArr[$i];
					$field_value = OSBHelper::getLanguageFieldValue($op,'field_option');
					if(!$mainframe->isClient('administrator'))
					{
						if($op->additional_price > 0 || $op->additional_price < 0)
						{
							$field_value .= " - (".OSBHelper::showMoney($op->additional_price,0).")";
						}
					}
					if($option_id == $optionArr[$i]->id)
					{
						$selected = "selected";
					}
					elseif($optionArr[$i]->option_default == 1)
					{
						$selected = "selected";
					}
					else
					{
						$selected = "";
					}
					?>
					<option value="<?php echo $optionArr[$i]->id?>" <?php echo $selected?>><?php echo $field_value?></option>
					<?php
				}
			}
			?>
		</select>
		<input type="hidden" name="field_<?php echo $field->id?>_required" id="field_<?php echo $field->id?>_required" value="<?php echo $field->required; ?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_label" id="field_<?php echo $field->id?>_label" value="<?php echo OSBHelper::getLanguageFieldValue($field,'field_label');?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_type" id="field_<?php echo $field->id?>_type" value="select" />
		<?php
	}
	
	static function orderSelectListData($field,$order_id){
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		if($order_id > 0){
			//find the option value of order in this field
			$query = $db->getQuery(true);
			$query->select('option_id');
			$query->from($db->quoteName('#__app_sch_order_options'));
			$query->where("order_id = '$order_id' and field_id = '$field->id'");
			$db->setQuery($query);
			$option_id = $db->loadResult();
			if($option_id > 0){
				$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
				$fieldvalue = $db->loadObject();
				//echo OSBHelper::getLanguageFieldValue($fieldvalue,'field_option');
				return OSBHelper::getLanguageFieldValue($fieldvalue,'field_option');
			}
		}
		return "";
	}
	
	
	/**
	 * Show checkboxes in booking form
	 *
	 * @param unknown_type $field
	 */
	static function orderCheckboxes($field,$order_id){
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		$options = [];
		if($order_id > 0){
			//find the option value of order in this field
			$query = $db->getQuery(true);
			$query->select('option_id');
			$query->from($db->quoteName('#__app_sch_order_options'));
			$query->where("order_id = '$order_id' and field_id = '$field->id'");
			$db->setQuery($query);
			$option_ids = $db->loadObjectList();
			
			$options = array();
			if(count($option_ids) > 0){
				for($i=0;$i<count($option_ids);$i++){
					$options[$i] = $option_ids[$i]->option_id;
				}
			}
			$query->clear();
		}
		$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field->id' order by ordering");
		$optionArr = $db->loadObjectList();
		?>
		<table width="100%">
			<tr>
				<?php
				$j = 0;
				$temp = array();
				for($i=0;$i<count($optionArr);$i++){
					$j++;
					$op = $optionArr[$i];
					$field_value = OSBHelper::getLanguageFieldValue($op,'field_option'); //$op->field_option;
					if(!$mainframe->isClient('administrator')){
						if(($op->additional_price > 0) || ($op->additional_price < 0))
						{
							$field_value .= " - (".OSBHelper::showMoney($op->additional_price,0).")";
						}
					}
					?>
					<td width="50%" style="padding:2px;text-align:left;padding-left:20px;">
						<?php
						if(in_array($op->id,$options))
						{
							$checked = "checked";
							$temp[] = $op->id;
						}
						else
						{
							$checked = "";
						}
						?>
						<input type="checkbox" name="field_<?php echo $field->id?>checkboxes" id="field_<?php echo $field->id?>_checkboxes<?php echo $i?>" value="<?php echo $op->id?>" onclick="javascript:updateCheckboxOrderForm(<?php echo $field->id?>)" <?php echo $checked?> class="<?php echo $field->field_class; ?>" /> &nbsp;&nbsp;<?php echo $field_value?>
					</td>
					<?php
					if($j==2){
						$j = 0;
						echo "</tr><tr>";
					}
				}
				?>
			</tr>
		</table>
		<input type="hidden" name="field_<?php echo $field->id?>_count" id="field_<?php echo $field->id?>_count" value="<?php echo count($optionArr)?>">
		<input type="hidden" name="field_<?php echo $field->id?>" id="field_<?php echo $field->id?>" value="<?php echo implode(",",$temp)?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_required" id="field_<?php echo $field->id?>_required" value="<?php echo $field->required; ?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_label" id="field_<?php echo $field->id?>_label" value="<?php echo OSBHelper::getLanguageFieldValue($field,'field_label');?>" />
		<input type="hidden" name="field_<?php echo $field->id?>_type" id="field_<?php echo $field->id?>_type" value="checkbox" />
		<?php
	}
	

	static function orderFileUploadData($field,$order_id){
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		
		if($order_id > 0){
			//find the option value of order in this field
			$query = $db->getQuery(true);
			$query->select('fvalue');
			$query->from($db->quoteName('#__app_sch_field_data'));
			$query->where("order_id = '$order_id' and fid = '$field->id'");
			//$query->order('ordering');
			$db->setQuery($query);
			$fieldvalue = $db->loadResult();
			
			$fieldvalue = JUri::root()."images/osservicesbooking/fields/".$fieldvalue;
			return $fieldvalue;
		}
		return "";
	}
	
	static function orderCheckboxesData($field,$order_id){
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		
		if($order_id > 0){
			//find the option value of order in this field
			$query = $db->getQuery(true);
			$query->select('option_id');
			$query->from($db->quoteName('#__app_sch_order_options'));
			$query->where("order_id = '$order_id' and field_id = '$field->id'");
			//$query->order('ordering');
			$db->setQuery($query);
			$option_ids = $db->loadObjectList();
			
			$options = array();
			if(count($option_ids) > 0){
				for($i=0;$i<count($option_ids);$i++){
					$options[$i] = $option_ids[$i]->option_id;
				}
				$db->setQuery("Select * from #__app_sch_field_options where id in (".implode(",",$options).")");
				$optionArr = $db->loadObjectList();
				$field_value_array = array();
				for($i=0;$i<count($optionArr);$i++){
					$op = $optionArr[$i];
					$field_value = OSBHelper::getLanguageFieldValue($op,'field_option'); //$op->field_option;
					if(($op->additional_price > 0) || ($op->additional_price < 0)){
						$field_value .= " - (".OSBHelper::showMoney($op->additional_price,0).")";
					}
					$field_value_array[] = $field_value;
				}
				return implode(", ",$field_value_array);
			}
		}
		return "";
	}

    public static function checkExtraFields($sid,$eid){
        global $mainframe;
        $mainframe = JFactory::getApplication();
        $db = JFactory::getDbo();
        $db->setQuery("Select count(id) from #__app_sch_fields where published = '1' and field_area = '0' and field_type in (1,2,5) and id in (Select field_id from #__app_sch_service_fields where service_id = '$sid') order by ordering");
        $count = $db->loadResult();
        if($count > 0){
            return true;
        }else{
            return false;
        }
    }
	/**
	 * Load extra fields in the employee forms
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 */
	static function loadExtraFields($sid,$eid,$order_item_id = 0)
	{
		global $mainframe,$mapClass,$configClass;
		$mainframe  = JFactory::getApplication();
		$user		= JFactory::getUser();
		$userId		= $user->id;
		$db			= JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_fields where published = '1' and field_area = '0' and field_type in (1,2,5) and id in (Select field_id from #__app_sch_service_fields where service_id = '$sid') order by ordering");
		//echo $db->getQuery();
		$fields = $db->loadObjectList();
		$fieldArr = array();
		if(count($fields) > 0)
		{
			?>
			<BR />
			<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv otherinformationform">
				<div class="<?php echo $mapClass['span12'];?> <?php echo $configClass['header_style']?>">
					<?php echo JText::_('OS_OTHER_INFORMATION')?>
				</div>
				<BR /><BR />
				<?php
				for($i=0;$i<count($fields);$i++)
				{
					$field = $fields[$i];
					$fieldArr[] = $field->id;

					if($configClass['field_integration'] == 1 && (int) $userId > 0 && $field->field_mapping != "")
					{
						$query = $db->getQuery(true);
						$query->select('profile_value')
							->from('#__user_profiles')
							->where('user_id=' . $userId)
							->where('profile_key = "profile.'.$field->field_mapping.'"');
						$db->setQuery($query);
						$field->value = $db->loadResult();
						$field->value = str_replace('"','',$field->value);
					}
					elseif($configClass['field_integration'] == 2 && (int) $userId > 0 && $field->field_mapping != "")
					{
						$query = $db->getQuery(true);
						$query->select($field->field_mapping)
							->from('#__jsn_users')
							->where('id=' . $userId);
						$db->setQuery($query);
						$field->value = $db->loadResult();
					}
					?>
					<div class="<?php echo $mapClass['span12'];?>">
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<?php
							if($field->field_type != 5)
							{
							?>
							<div class="<?php echo $mapClass['span4'];?>" style="font-weight:bold;">
								<?php echo OSBHelper::getLanguageFieldValue($field,'field_label');?>
							</div>
							<div class="<?php echo $mapClass['span8'];?>">
							<?php
							}
							else
							{
								?>
								<div class="<?php echo $mapClass['span12'];?>">
								<?php
							}
							?>
								<?php
									switch ($field->field_type)
									{
										case "0":
											OsAppscheduleDefault::showInputbox($field,$sid,$eid,$order_item_id);
										break;
										case "1":
											OsAppscheduleDefault::showSelectList($field,$sid,$eid,$order_item_id);
										break;
										case "2":
											OsAppscheduleDefault::showCheckboxes($field,$sid,$eid,$order_item_id);
										break;
										case "5":
											OsAppscheduleDefault::showMessage($field,$sid,$eid,$order_item_id);
										break;
									}
								?>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
		?>
		<input type="hidden" name="field_ids<?php echo $sid?>" id="field_ids<?php echo $sid?>" value="<?php echo implode(",",$fieldArr)?>">
		<?php
	}

	static function showMessage($field,$sid = 0,$eid = 0,$order_item_id = 0)
	{
		global $mainframe, $languages;
		$translatable	= JLanguageMultilang::isEnabled() && count($languages);
		$db				= JFactory::getDbo();
		$suffix			= OSBHelper::getFieldSuffix();
		if($field->field_class != "")
		{
		?>
			<div class="<?php echo $field->field_class;?>"><?php echo $field->{'message'.$suffix}; ?></div>
		<?php
		}
		else
		{
			echo $field->{'message'.$suffix}; 
		}
	}
	
	/**
	 * Show input box
	 *
	 * @param unknown_type $field
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 */
	static function showInputbox($field,$sid,$eid,$order_item_id = 0)
	{
		global $mainframe;
		?>
		<input type="text" class="input-large form-control <?php echo $field->field_class; ?>" size="30" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>" value="<?php echo $field->value; ?>" />
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_label" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_label" value="<?php echo $field->field_label?>" />
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_required" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_required" value="<?php echo $field->required;?>" />
		<?php
	}
	
	
	/**
	 * Show select list
	 *
	 * @param unknown_type $field
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 */
	static function showSelectList($field,$sid,$eid,$order_item_id = 0)
	{
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();

		if($order_item_id > 0)
		{
			$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$order_item_id' and field_id = '$field->id'");
			$selected_option_id = $db->loadResult();
		}
		else
		{
			if($field->value != "")
			{
				$db->setQuery("Select id from #__app_sch_field_options where field_id = '$field->id' and field_option like '$field->value'");
				$selected_option_id = $db->loadResult();
			}
		}

		if($configClass['allow_multiple_timeslots'] == 1)
		{
			$jsFunction = "updateSelectlistMultiple";
		}
		else
		{
			$jsFunction = "updateSelectlist";
		}
		?>
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_label" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_label" value="<?php echo $field->field_label?>">
		
		<select name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_selectlist" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_selectlist" class="input-medium form-select <?php echo $field->field_class; ?>" onChange="javascript:<?php echo $jsFunction; ?>(<?php echo $sid?>,<?php echo $eid;?>,<?php echo $field->id?>,'<?php echo JText::_('OS_SUMMARY');?>','<?php echo JText::_('OS_FROM');?>','<?php echo JText::_('OS_TO');?>')">
			<?php
			$db->setQuery("Select id from #__app_sch_field_options where field_id = '$field->id' and option_default = '1'");
			$option_default = $db->loadResult();
			$option_default = intval($option_default);

			$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field->id' order by ordering");
			$optionArr = $db->loadObjectList();

			$default_text = "";
			if($option_default == 0)
			{
				?>
				<option value=""></option>
				<?php
			}

			if(count($optionArr) > 0)
			{
				for($i=0;$i<count($optionArr);$i++)
				{
					$selected = "";
					$op = $optionArr[$i];
					$field_value = OSBHelper::getLanguageFieldValue($op,'field_option');
					if(!$mainframe->isClient('administrator'))
					{
						if(($op->additional_price > 0) || ($op->additional_price < 0)){
							$field_value .= " (".OSBHelper::showMoney($op->additional_price,0).")";
						}
					}
					if((int) $selected_option_id > 0 && $selected_option_id == $op->id)
					{
						$selected = "selected";
						$default_text = $field_value;
					}
					elseif($op->id == $option_default && $option_default > 0)
					{
						$selected = "selected";
						$default_text = $field_value;
					}
					?>
					<option value="<?php echo $op->id?>||<?php echo $field_value;?>" <?php echo $selected; ?>><?php echo $field_value;?></option>
					<?php
				}
			}
			?>
		</select>
		<?php
		if($option_default == 0)
		{
			$option_default = "";
		}
		if($selected_option_id > 0 )
		{
			$option_default = $selected_option_id;
		}
		?>
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_selected" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_selected" value="<?php echo $option_default; ?>" />

		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>" value="<?php echo $default_text; ?>" />
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_required" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_required" value="<?php echo $field->required;?>" />
		<?php
	}
	
	/**
	 * Show checkboxes
	 *
	 * @param unknown_type $field
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 */
	static function showCheckboxes($field,$sid,$eid,$order_item_id=0)
	{
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		$selected_options = [];
		if($order_item_id > 0)
		{
			$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$order_item_id' and field_id = '$field->id'");
			$selected_options = $db->loadColumn(0);
		}
		$selected_items = "";
		if(count($selected_options) > 0)
		{
			$selected_items = implode(",", $selected_options);
		}
		$options = $field->field_options;
		$optionArr = explode("\n",$options);
		if($configClass['allow_multiple_timeslots'] == 1)
		{
			$jsFunction = "updateCheckboxMultiple";
		}
		else
		{
			$jsFunction = "updateCheckbox";
		}
		?>
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<?php
			$j = 0;
			$db->setQuery("Select * from #__app_sch_field_options where field_id = '$field->id' order by ordering");
			$optionArr = $db->loadObjectList();
			for($i=0;$i<count($optionArr);$i++)
			{
				$j++;
				$op = $optionArr[$i];
				//$field_value = $op->field_option;
				$field_value = OSBHelper::getLanguageFieldValue($op,'field_option');
				if(!$mainframe->isClient('administrator'))
				{
					if(($op->additional_price > 0) || ($op->additional_price < 0))
					{
						$field_value .= " - (".OSBHelper::showMoney($op->additional_price,0).")";
					}
				}
				if(count($selected_options) && in_array($op->id, $selected_options))
				{
					$checked = "checked";
				}
				else
				{
					$checked = "";
				}
				?>
				<div class="<?php echo $mapClass['span6'];?>" style="margin-left:0px;">
					<input type="checkbox" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>checkboxes" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_checkboxes<?php echo $i?>" onclick="javascript:<?php echo $jsFunction; ?>(<?php echo $sid?>,<?php echo $eid?>,<?php echo $field->id?>,'<?php echo JText::_('OS_SUMMARY');?>','<?php echo JText::_('OS_FROM');?>','<?php echo JText::_('OS_TO');?>');" value="<?php echo $op->id?>||<?php echo $field_value;?>" <?php echo $checked;?> class="<?php echo $field->field_class; ?>"> <?php echo $field_value;?>
				</div>
				<?php
				if($j==2)
				{
					$j = 0;
					?>
					</div><div class="<?php echo $mapClass['row-fluid'];?>">
					<?php
				}
			}
			
			?>
		</div>
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_count" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_count" value="<?php echo count($optionArr)?>" />
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>" value="" />
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_label" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_label" value="<?php echo $field->field_label?>" />
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_selected" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_selected" value="<?php echo $selected_items; ?>" />
		<input type="hidden" name="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_required" id="field_<?php echo $sid?>_<?php echo $eid?>_<?php echo $field->id?>_required" value="<?php echo $field->required;?>" />
		<?php
		//}
	}
	
	/**
	 * Services details
	 *
	 * @param unknown_type $order_id
	 * @param unknown_type $eid
	 */
	static function orderDetails($order_id,$eid, $useLangOrder = true, $isEmail = false, $includeCustomFields = true)
	{
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		$order = $db->loadObject();
		$order_lang = $order->order_lang;
		if($useLangOrder && $order_lang != "")
		{
			JFactory::getLanguage()->load('com_osservicesbooking', JPATH_ROOT, $order_lang);
		}
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		//check extra fields
		if($includeCustomFields)
		{
			if($isEmail)
			{
				$extraEmailSql = " and show_in_email = '1'";
			}
			$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1' ".$extraEmailSql." order by ordering");
			$fields = $db->loadObjectList();
			if(count($fields) > 0)
			{
				for($i=0;$i<count($fields);$i++)
				{
					$field = $fields[$i];
					$db->setQuery("Select count(id) from #__app_sch_order_options where order_id = '$order_id' and field_id = '$field->id'");
					$count = $db->loadResult();
					if($field->field_type == 0)
					{
						$db->setQuery("Select fvalue from #__app_sch_field_data where order_id = '$order_id' and fid = '$field->id'");
						$fvalue = $db->loadResult();
						if($fvalue != "")
						{
							echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order_lang);?>:<?php echo $fvalue;?><BR /><?php
						}
					}
					elseif($field->field_type == 3)
					{
						$db->setQuery("Select fvalue from #__app_sch_field_data where order_id = '$order_id' and fid = '$field->id'");
						$fvalue = $db->loadResult();
						if($fvalue != "")
						{
							if(file_exists(JPATH_ROOT.'/images/osservicesbooking/fields/'.$fvalue))
							{
								echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order_lang);?>:
								<img src="<?php echo JUri::root();?>/images/osservicesbooking/fields/<?php echo $fvalue;?>" width="120" />
								<BR /><BR /><?php
							}
						}
					}	
					if($count > 0)
					{
						if($field->field_type == 1)
						{
							$db->setQuery("Select option_id from #__app_sch_order_options where order_id = '$order_id' and field_id = '$field->id'");
							$option_id = $db->loadResult();
							$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
							$optionvalue = $db->loadObject();
							echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order_lang).": ";
							$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order_lang); //$optionvalue->field_option;
							//if($optionvalue->additional_price > 0){
							if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0))
							{
								$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
							}
							echo $field_data;
							?>
							<BR />
							<?php
						}
						elseif($field->field_type == 2)
						{
							$db->setQuery("Select option_id from #__app_sch_order_options where order_id = '$order_id' and field_id = '$field->id'");
							$option_ids = $db->loadObjectList();
							$fieldArr = array();
							for($j=0;$j<count($option_ids);$j++)
							{
								$oid = $option_ids[$j];
								$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
								$optionvalue = $db->loadObject();
								//$field_data = $optionvalue->field_option;
								$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order_lang);
								//if($optionvalue->additional_price > 0){
								if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0))
								{
									$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
								}
								$fieldArr[] = $field_data;
							}
							echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order_lang);?>:
							<?php
							echo implode(", ",$fieldArr);
							?>
							<BR />
							<?php
						}
					}
				}
			}
		}
		
		$query = "Select a.*,b.id as bid,b.start_time,b.end_time,b.booking_date,b.additional_information,c.id as eid,c.employee_name,b.nslots from #__app_sch_services as a"
				." inner join #__app_sch_order_items as b on b.sid = a.id"
				." inner join #__app_sch_employee as c on c.id  = b.eid "
				." where b.order_id = '$order_id'";
				//. HelperOSappscheduleCommon::returnAccessSql('a');
		if($eid > 0){
			$query .= " and b.eid = '$eid'";
		}
		$query .= " order by b.booking_date, b.start_time";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			?>
			<table  width="100%">
			<tr>
				<?php
				if($configClass['use_qrcode'])
				{
					?>
					<td width="5%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					</td>
					<?php
				}
				?>
				<td width="25%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_SERVICE_NAME')?>
				</td>
				<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_BOOKING_DATE')?>
				</td>
				<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_START_TIME')?>
				</td>
				<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_END_TIME')?>
				</td>
				<td width="55%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
					<?php echo JText::_('OS_ADDITIONAL_INFORMATION')?>
				</td>
			</tr>
			<?php
			for($i1=0;$i1<count($rows);$i1++)
			{
				$config = new JConfig();
				$offset = $config->offset;
				date_default_timezone_set($offset);
				$row = $rows[$i1];
				?>
				<tr>
					<?php
					if($configClass['use_qrcode'])
					{
						if(!file_exists(JPATH_ROOT . '/media/com_osservicesbooking/qrcodes/item_'.$row->id.'.png'))
						{
							OSBHelper::generateQrcode($order_id);
						}
						?>
						<td width="5%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
							<img src="<?php echo JUri::root();?>media/com_osservicesbooking/qrcodes/item_<?php echo $row->id?>.png" border="0"/>
						</td>
						<?php
					}
					?>
					<td width="25%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<b><?php echo OSBHelper::getLanguageFieldValueOrder($row,'service_name',$order_lang); //$row->service_name;?></b>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<?php
							//echo intval(date("d",$row->start_time))."/".intval(date("m",$row->start_time))."/".intval(date("Y",$row->start_time));
							echo date($configClass['date_format'],$row->start_time);
						?>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<?php
							//echo date("H:i",$row->start_time);
							echo date($configClass['time_format'],$row->start_time);
						?>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<?php
							//echo date("H:i",$row->end_time);
							echo date($configClass['time_format'],$row->end_time);
						?>
					</td>
					<td width="55%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<?php
							if($row->service_time_type == 1){
								echo JText::_('OS_NUMBER_SLOT').": ".$row->nslots."<BR />";
							}
							echo JText::_('OS_EMPLOYEE').": <B>".$row->employee_name."</B>";
							$db->setQuery("Select a.* from #__app_sch_venues as a inner join #__app_sch_employee_service as b on b.vid = a.id where b.employee_id = '$row->eid' and b.service_id = '$row->id'");
							$venue = $db->loadObject();
							if(OSBHelper::getLanguageFieldValueOrder($venue,'address',$order_lang) != ""){
								echo "<BR />";
								echo JText::_('OS_VENUE').": <B>".OSBHelper::getLanguageFieldValueOrder($venue,'address',$order_lang)."</B>";
							}
						?>
						<BR />
						<?php
							//echo $row->additional_information;
							if($isEmail)
							{
								$extraEmailSql = " and show_in_email = '1'";
							}
							$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' ".$extraEmailSql." order by ordering");
							$fields = $db->loadObjectList();
							if(count($fields) > 0)
							{
								for($i=0;$i<count($fields);$i++)
								{
									$field = $fields[$i];
									$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$row->bid' and field_id = '$field->id'");
									$count = $db->loadResult();
									if($count > 0)
									{
										if($field->field_type == 1)
										{
											$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->bid' and field_id = '$field->id'");
											$option_id = $db->loadResult();
											$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
											$optionvalue = $db->loadObject();
											?>
											<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order_lang); //$field->field_label;?>:
											<?php
											$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order_lang); //$optionvalue->field_option;
											if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0)){
												$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
											}
											echo $field_data;
											echo "<BR />";
										}
										elseif($field->field_type == 2)
										{
											$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->bid' and field_id = '$field->id'");
											$option_ids = $db->loadObjectList();
											$fieldArr = array();
											for($j=0;$j<count($option_ids);$j++){
												$oid = $option_ids[$j];
												$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
												$optionvalue = $db->loadObject();
												$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order_lang); //$optionvalue->field_option;
												if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0)){
													$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
												}
												$fieldArr[] = $field_data;
											}
											echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order_lang); //$field->field_label;?>:
											<?php
											echo implode(", ",$fieldArr);
											echo "<BR />";
										}
									}
								}
							}
						?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
			<?php
            if($configClass['use_qrcode'])
			{
                if(!file_exists(JPATH_ROOT.'/media/com_osservicesbooking/qrcodes/'.$order_id.'.png'))
				{
                    OSBHelper::generateQrcode($order_id);
                }
                $imgTag = '<img src="'.JUri::root().'media/com_osservicesbooking/qrcodes/'.$order_id.'.png" border="0" />';
                echo "<BR />";
                echo $imgTag;
            }

		}
	}
	
	/**
	 * Services details
	 *
	 * @param unknown_type $order_id
	 * @param unknown_type $eid
	 */
	public static function orderItemDetails($order_id,$order_item_id, $isEmail = false)
	{
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
        $order = $db->loadObject();
		//check extra fields
		if($isEmail)
		{
			$extraEmailSql = " and show_in_email = '1'";
		}
		$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1' ".$extraEmailSql." order by ordering");
		$fields = $db->loadObjectList();
		if(count($fields) > 0)
		{
			for($i=0;$i<count($fields);$i++)
			{
				$field = $fields[$i];
				$db->setQuery("Select count(id) from #__app_sch_order_options where order_id = '$order_id' and field_id = '$field->id'");
				$count = $db->loadResult();
				if($count > 0)
				{
					if($field->field_type == 1)
					{
						$db->setQuery("Select option_id from #__app_sch_order_options where order_id = '$order_id' and field_id = '$field->id'");
						$option_id = $db->loadResult();
						$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
						$optionvalue = $db->loadObject();
						?>
						<?php echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);//$field->field_label;?>:
						<?php
						$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang); //$optionvalue->field_option;
						if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0)){
							$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
						}
						echo $field_data;
						?>
						<BR />
						<?php
					}
					elseif($field->field_type == 2)
					{
						$db->setQuery("Select option_id from #__app_sch_order_options where order_id = '$order_id' and field_id = '$field->id'");
						$option_ids = $db->loadObjectList();
						$fieldArr = array();
						for($j=0;$j<count($option_ids);$j++)
						{
							$oid = $option_ids[$j];
							$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
							$optionvalue = $db->loadObject();
							$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang); //$optionvalue->field_option;
							if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0)){
								$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
							}
							$fieldArr[] = $field_data;
						}
						echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);//$field->field_label;?>:
						<?php
						echo implode(", ",$fieldArr);
						?>
						<BR />
						<?php
					}
				}
			}
		}
		
		$query = "Select a.*,b.id as bid,b.start_time,b.end_time,b.booking_date,b.additional_information,c.id as eid, c.employee_name from #__app_sch_services as a"
				." inner join #__app_sch_order_items as b on b.sid = a.id"
				." inner join #__app_sch_employee as c on c.id  = b.eid "
				." where b.id = '$order_item_id'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0 && $rows[0]->start_time != "" && $rows[0]->end_time != "")
		{
			?>
			<table  width="100%">
				<tr>
					<td width="25%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
						<?php echo JText::_('OS_SERVICE_NAME')?>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
						<?php echo JText::_('OS_BOOKING_DATE')?>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
						<?php echo JText::_('OS_START_TIME')?>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
						<?php echo JText::_('OS_END_TIME')?>
					</td>
					<td width="55%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;font-weight:bold;">
						<?php echo JText::_('OS_ADDITIONAL_INFORMATION')?>
					</td>
				</tr>
			<?php
			for($i1=0;$i1<count($rows);$i1++)
			{
				$config = new JConfig();
				$offset = $config->offset;
				date_default_timezone_set($offset);
				$row = $rows[$i1];
				?>
				<tr>
					<td width="25%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<b><?php echo $row->service_name;?></b>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<?php
							//echo intval(date("d",$row->start_time))."/".intval(date("m",$row->start_time))."/".intval(date("Y",$row->start_time));
							echo date($configClass['date_format'],$row->start_time);
						?>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<?php
							//echo date("H:i",$row->start_time);
							echo date($configClass['time_format'],$row->start_time);
						?>
					</td>
					<td width="10%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<?php
							//echo date("H:i",$row->end_time);
							echo date($configClass['time_format'],$row->end_time);
						?>
					</td>
					<td width="55%" align="left" style="color:gray;border-bottom:1px dotted #D0C5C5 !important;">
						<?php
							echo JText::_('OS_EMPLOYEE').": <B>".$row->employee_name."</B>";
						?>
						<BR />
						<?php
							$db->setQuery("Select a.* from #__app_sch_venues as a inner join #__app_sch_employee_service as b on b.vid = a.id where b.employee_id = '$row->eid' and b.service_id = '$row->bid'");
							$venue = $db->loadObject();
							if($venue->address != ""){
								echo JText::_('OS_VENUE').": <B>".$venue->address."</B>";
								echo "<BR />";
							}
							if($isEmail)
							{
								$extraEmailSql = " and show_in_email = '1'";
							}
							$db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' ".$extraEmailSql." order by ordering");
							$fields = $db->loadObjectList();
							if(count($fields) > 0)
							{
								for($i=0;$i<count($fields);$i++)
								{
									$field = $fields[$i];
									$db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$row->bid' and field_id = '$field->id'");
									$count = $db->loadResult();
									if($count > 0)
									{
										if($field->field_type == 1)
										{
											$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->bid' and field_id = '$field->id'");
											$option_id = $db->loadResult();
											$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
											$optionvalue = $db->loadObject();
											echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);//$field->field_label;?>:
											<?php
											$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang);//$optionvalue->field_option;
											if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0)){
												$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
											}
											echo $field_data;
											echo "<BR />";
										}
										elseif($field->field_type == 2)
										{
											$db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->bid' and field_id = '$field->id'");
											$option_ids = $db->loadObjectList();
											$fieldArr = array();
											for($j=0;$j<count($option_ids);$j++)
											{
												$oid = $option_ids[$j];
												$db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
												$optionvalue = $db->loadObject();
												$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order->order_lang); //$optionvalue->field_option;
												if($optionvalue->additional_price > 0 || $optionvalue->additional_price < 0)
												{
													$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
												}
												$fieldArr[] = $field_data;
											}
											echo OSBHelper::getLanguageFieldValueOrder($field,'field_label',$order->order_lang);//$field->field_label;?>:
											<?php
											echo implode(", ",$fieldArr);
											echo "<BR />";
										}
									}
								}
							}
						?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
			<?php	
		}
	}
	
	/**
	 * Show payment failure information
	 *
	 */
	static function failure(){
		global $mainframe,$mapClass,$configClass,$jinput;
		$reason =  isset($_SESSION['reason']) ? $_SESSION['reason'] : '';
		if (!$reason) {
			$reason = $jinput->get('failReason', '','string') ;
		}
		HTML_OsAppscheduleDefault::failureHtml($reason);
	}
	
	
	/**
	 * List all orders history 
	 *
	 */
	static function orderHistory()
	{
		global $mainframe,$mapClass;
		$config			= new JConfig();
		$offset			= $config->offset;
		date_default_timezone_set($offset);
		$user			= JFactory::getUser();
		$db				= JFactory::getDbo();
		$document		= JFactory::getDocument();
		if(intval($user->id) == 0)
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root());
		}
		$menus          = JFactory::getApplication()->getMenu();
		$menu           = $menus->getActive();
		$params         = new JRegistry() ;
		$heading		= JText::_('OS_MY_ORDERS');
		if (is_object($menu))
		{
	        $params = $menu->getParams();
            if($params->get('page_title') != "")
            {
                $document->setTitle($params->get('page_title'));
            }
            else
            {
                $document->setTitle(JText::_('OS_MY_ORDERS'));
            }

			$heading = $params->get('page_heading','');
		}
		else
		{
            $document->setTitle(JText::_('OS_MY_ORDERS'));
        }		
		$date1				= OSBHelper::getStringValue('date1','');
		$date2				= OSBHelper::getStringValue('date2','');
		$date				= "";
		if($date1 != "")
		{
			$date .= " and b.booking_date >= '$date1'";
		}
		if($date2 != "")
		{
			$date .= " and b.booking_date <= '$date2'";
		}
		$lists['date1']		= $date1;
		$lists['date2']		= $date2;

		$db->setQuery("Select a.* from #__app_sch_orders as a inner join #__app_sch_order_items as b on a.id = b.order_id where a.user_id = '$user->id' $date group by a.id order by a.order_date desc");
		
		$orders = $db->loadObjectList();

		if(count($orders) > 0)
		{
			$lgs = OSBHelper::getLanguages();
			$translatable = JLanguageMultilang::isEnabled() && count($lgs);
			for($i=0;$i<count($orders);$i++)
			{
				$order = $orders[$i];
				$db->setQuery("Select * from #__app_sch_orders where id = '$order->id'");
				$orderdetails = $db->loadObject();
				$order_lang = $orderdetails->order_lang;
				$suffix = "";
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

				$db->setQuery("Select count(b.id) from #__app_sch_order_items as b where b.order_id = '$order->id' ".$date);
				$order->countItems = (int) $db->loadResult();
				$order->service = $service;
				
			}
		}
		HTML_OsAppscheduleDefault::listOrdersHistory($orders, $heading, $lists);
	}

	static function customerBalances(){
		global $mainframe,$mapClass,$configClass;
		$user = JFactory::getUser();
		$db   = JFactory::getDbo();
		if(intval($user->id) == 0)
		{
			$mainframe->enqueueMessage(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$mainframe->redirect(JURI::root());
		}
		$document		= JFactory::getDocument();
		$menus          = JFactory::getApplication()->getMenu();
		$menu           = $menus->getActive();
		$params         = new JRegistry() ;
		$heading		= JText::_('OS_MY_BALANCES');
		if (is_object($menu))
		{
	        $params = $menu->getParams();
            if($params->get('page_title') != "")
            {
                $document->setTitle($params->get('page_title'));
            }
            else
            {
                $document->setTitle(JText::_('OS_MY_BALANCES'));
            }

			$heading = $params->get('page_heading','');
		}
		else
		{
            $document->setTitle(JText::_('OS_MY_BALANCES'));
        }
		$query = $db->getQuery(true);
		$query->select('*')->from('#__app_sch_user_balance')->where('user_id = "'.$user->id.'"');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		HTML_OsAppscheduleDefault::listUserBalances($rows, $heading);
	}
	
	/**
	 * Get List of Order
	 *
	 * @param unknown_type $order_id
	 */
	static function getListOrderServices($order_id,$checkin=0, $date1 = '', $date2 = ''){
		global $mainframe;
		$db = JFactory::getDbo();
		$date = "";
		if($date1 != "")
		{
			$date .= " and a.booking_date >= '$date1'";
		}
		if($date2 != "")
		{
			$date .= " and a.booking_date <= '$date2'";
		}
		$db->setQuery("Select a.id as order_item_id,a.*,b.id as sid,b.*,c.id as eid,c.employee_name from #__app_sch_order_items as a inner join #__app_sch_services as b on b.id = a.sid inner join #__app_sch_employee as c on c.id = a.eid where a.order_id = '$order_id' ".$date);
		$rows = $db->loadObjectList();
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		$order = $db->loadObject();
		HTML_OsAppscheduleDefault::listOrderServices($rows,$order,$checkin);
	}
	
	/**
	 * Remove Order
	 *
	 * @param unknown_type $order_id
	 */
	static function removeOrder($order_id)
	{
		global $mainframe,$mapClass,$configClass,$jinput;
		$user = JFactory::getUser();
		if((int) $order_id == 0 || (int)$user->id == 0 )
		{
			throw new Exception (JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
		}
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_orders where id = '$order_id'");
		$order = $db->loadObject();

		if(($order->user_id > 0 && $order->user_id <> $user->id) || !$user->authorise('osservicesbooking.orders', 'com_osservicesbooking'))
		{
			throw new Exception (JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
		}

		$configClass = OSBHelper::loadConfig();
		
		$db->setQuery("Select * from #__app_sch_order_items where order_id = '$order_id'");
		$items = $db->loadObjectList();
		if(count($items) > 0){
			for($i=0;$i<count($items);$i++){
				$item = $items[$i];
				$db->setQuery("DELETE FROM #__app_sch_order_field_options WHERE order_item_id = '$item->id'");
				$db->execute();
			}
		}

		//send notification email
		HelperOSappscheduleCommon::sendCancelledEmail($order_id);
		HelperOSappscheduleCommon::sendSMS('cancel',$order_id);
		HelperOSappscheduleCommon::sendEmail('customer_cancel_order',$order_id);
		HelperOSappscheduleCommon::sendEmployeeEmail('employee_order_cancelled_new',$order_id,$eid);
		if($configClass['integrate_gcalendar'] == 1){
			OSBHelper::removeEventOnGCalendar($order_id);
		}
		if($configClass['waiting_list'] == 1){
			OSBHelper::sendWaitingNotification($order_id);
		}
		$db->setQuery("DELETE FROM #__app_sch_orders WHERE id = '$order_id'");
		$db->execute();
		$db->setQuery("DELETE FROM #__app_sch_order_options WHERE order_id = '$order_id'");
		$db->execute();
		$db->setQuery("DELETE FROM #__app_sch_order_items WHERE order_id = '$order_id'");
		$db->execute();
		
		
		$mainframe->redirect(JURI::root()."index.php?option=com_osservicesbooking&task=default_customer&Itemid=".$jinput->getInt('Itemid'));
	}
	
	/**
	 * Show Google Map
	 *
	 */
	static function showMap(){
		global $mainframe,$mapClass,$configClass,$jinput;
		$vid = $jinput->getInt('vid',0);
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_venues where id = '$vid'");
		$venue = $db->loadObject();
		HTML_OsAppscheduleDefault::showMap($venue);
	}

	
	static function listAllEmployees()
	{
		global $mainframe,$mapClass,$configClass;
		$document			= JFactory::getDocument();
		$menu				= JFactory::getApplication()->getMenu()->getActive();
		$pagetitle			= "";
		$params				= new JRegistry() ;
        if (is_object($menu)) 
		{
            $params = $menu->getParams();
            if($params->get('page_title') != "")
			{
                $document->setTitle($params->get('page_title'));
            }
			else
			{
                $document->setTitle($configClass['business_name'].' | '.JText::_('OS_LIST_ALL_EMPLOYEES'));
            }
            $list_type		= $params->get('list_type',0);
			$introtext		= $params->get('introtext','');
        }
		else
		{
            $document->setTitle($configClass['business_name'].' | '.JText::_('OS_LIST_ALL_EMPLOYEES'));
        }
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_employee where published = '1' order by employee_name");
		$employees = $db->loadObjectList();

		if(count($employees))
		{
			foreach($employees as $employee)
			{
				$db->setQuery("Select count(a.id) from #__app_sch_employee_service as a inner join #__app_sch_services as b on a.service_id = b.id where a.employee_id = '$employee->id' and b.published = '1'");
				$count = $db->loadResult();
				if($count == 1)
				{
					$db->setQuery("Select a.vid from #__app_sch_employee_service as a inner join #__app_sch_venues as b on a.vid = b.id where a.employee_id = '$employee->id' and b.published = '1'");
					$vid = $db->loadResult();
					$employee->vid = (int)$vid;
				}
			}
		}

		HTML_OsAppscheduleDefault::listEmployees($employees,$params,$list_type,$introtext);
	}

    /**
     * Check in
     */
    static function checkIn(){
		global $jinput;
        $user = JFactory::getUser();
        if ($user->authorise('osservicesbooking.checkin_management', 'com_osservicesbooking')) 
		{
            //check in for all item
            $id = $jinput->getInt('id',0);
            OsAppscheduleDefault::orderDetailsForm($id,1);
        }
		else
		{
            //throw new Exception(JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$session = Factory::getSession();
			$session->set('errorReason', JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			self::redirecterrorform();
        }
    }

	static function addtowaitinglist(){
		global $mainframe,$mapClass,$jinput;
		$db = JFactory::getDbo();
		$sid = $jinput->getInt('sid',0);
		$eid = $jinput->getInt('eid',0);
		$start = $jinput->getInt('start',0);
		$end   = $jinput->getInt('end',0);
		$data = array();
		$data['sid'] = $sid;
		$data['eid'] = $eid;
		$data['start'] = $start;
		$data['end'] = $end;
		HTML_OsAppscheduleDefault::waitingListForm($data);
	}

	static function doaddtowaitinglist(){
		global $mainframe,$mapClass,$jinput;
		$db = JFactory::getDbo();
		$sid = $jinput->getInt('sid',0);
		$eid = $jinput->getInt('eid',0);
		$start = $jinput->getInt('start',0);
		$end   = $jinput->getInt('end',0);
		$email = OSBHelper::getStringValue('email','');
		$query = $db->getQuery(true);
		$query->select("count(id)")->from("#__app_sch_waiting_list")->where("email = '".$email."' and sid = '".$sid."' and eid = '".$eid."' and start_time = '".$start."' and end_time = '".$end."'");
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count == 0){
			$db->setQuery("Insert into #__app_sch_waiting_list (id,sid,eid,start_time,end_time,email) values (NULL,'$sid','$eid','$start','$end','$email')");
			$db->execute();
			$msg = Jtext::_('OS_TIME_SLOT_HAS_BEEN_ADDED_TO_WAITING_LIST');
		}else{
			$msg = Jtext::_('OS_YOU_ALREADY_ADDED_THE_TIMESLOT_INTO_WAITING_LIST');
		}
		HTML_OsAppscheduleDefault::waitinglistResult($msg);
	}

	static function unsubwaitinglist(){
		global $mainframe,$mapClass,$jinput;
		$db = JFactory::getDbo();
		$id = $jinput->getInt('id',0);
		$code = $jinput->get('code','','string');
		if($code != ""){
			if($code == md5($id)){
				$db->setQuery("Delete from #__app_sch_waiting_list where id = '$id'");
				$db->execute();
				$mainframe->enqueueMessage(JText::_('OS_YOUR_WAITING_LIST_REQUEST_HAS_BEEN_REMOVED'));
				$mainframe->redirect(JURI::root());
			}
		}
		$mainframe->redirect(JUri::root());
	}


    //for testing

    static function testSMS(){
		global $mainframe,$mapClass,$configClass;
		if ($configClass['enable_clickatell'] == 1)
		{ //enable Clickatell sms
			if (($configClass['clickatell_username'] != "") && ($configClass['clickatell_password'] != "") && ($configClass['clickatell_api'] != ""))
			{
				$smscontent = "test by dev";
				$sms_phone  = "84976381495";
				if (($smscontent != "") && ($sms_phone != ""))
				{
					$sms_phone = str_replace("-", "", $sms_phone);
					$sms_phone = str_replace("+", "", $sms_phone);
					$sms_phone = str_replace(" ", "", $sms_phone);
					$to = $sms_phone;
					if((int)$configClass['clickatell_register'] == 0)
					{
						$baseurl = "https://api.clickatell.com";
						$url = $baseurl . "/http/sendmsg?api_id=" . $configClass['clickatell_api'] . "&password=" . $configClass['clickatell_password'] . "&user=" . $configClass['clickatell_username'];
						$url .= "&to=" . $to;
						if ($configClass['clickatell_senderid'] != "")
						{
							$sender = "&from=" . $configClass['clickatell_senderid'];
						}
						else
						{
							$sender = "";
						}
						$url .= $sender;
						if ($configClass['clickatell_enable_unicode'] == "0")
						{
							$url .= "&concat=3&text=" . $smscontent;
						}
						else
						{
							$url .= "&unicode=1&concat=3&text=" . HelperOSappscheduleCommon::utf16urlencode($smscontent);
						}
						//do sendmsg call
						$ret = file($url);
						$send = explode(":", $ret[0]);
						if ($send[0] == "ID")
						{
							$returnCode = $send[1];
						}
						else
						{
							$returnCode = $send[1];
						}
					}
					else
					{
						$message				= array();
						
						$message['to']			= $sms_phone;
						
						$return = self::sendClickAtellNewVersion(['to' => [$sms_phone], 'content' => $smscontent]);
						
					}
				}//sms content and sms phone is not empty
			}//config ready end
		}//end ClickAtell
	}

	public static function sendClickAtellNewVersion($message)
	{
		global $configClass;

		$headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $configClass['clickatell_api']
        ];

		$endpoint = 'https://platform.clickatell.com/messages';

        $curlInfo = curl_version();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ClickatellV2/1.0' . ' curl/' . $curlInfo['version'] . ' PHP/' . phpversion());

        // Specify the raw post data
        if ($message) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        }

        $result = curl_exec($ch);
		print_r($result);die();
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


		if (!in_array($httpCode, array(200, 201, 202))) 
		{
            // Decode JSON if possible, if this can't be decoded...something fatal went wrong
            // and we will just return the entire body as an exception.
            if ($error = json_decode($result, true)) {
                $error = $error['error'];
            } else {
                $error = $result;
            }

			return '';
        }
		else
		{
			return json_decode($result, true);
		}
	}



	
	static function updateGoogleCalendar($order_id){
		global $mainframe,$mapClass,$configClass;
		OSBHelper::updateGoogleCalendar($order_id);
	}
	
	/**
	 * Add Event to Google Calendar
	 *
	 */
	static function addEventToGCalendar(){
		global $mainframe,$mapClass,$jinput;
		$eid = $jinput->getInt('eid',1);
		$current = OSBHelper::getCurrentDate();
		$gmttime =  strtotime(JFactory::getDate('now'));
		$distance = round(($current - $gmttime)/3600);
		if($distance < 10){
			$distance = "0".$distance;
		}
		if($distance > 0){
			$distance =  "+".$distance;
		}
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_employee where id = '$eid'");
		$employee  = $db->loadObject();
		$gusername = $employee->gusername;
		$gusername.= "@gmail.com";
		$gpassword = $employee->gpassword;
		
		$path = JPATH_ROOT."/libraries/osgcalendar/src/Google";
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		//echo $path;
		if(!file_exists ( $path.DS.'Client.php' )){
			echo "ABPro set to use Google Calendar but the Google Library is not installed. See <a href='http://appointmentbookingpro.com/index.php?option=com_content&view=article&id=89&Itemid=190' target='_blank'>Tutorial</a>";
			exit;
		}	
		require_once $path."/Client.php";
	    require_once $path."/Service.php";
	    
		try {
	 	    $client = new Google_Client();
			$client->setApplicationName("Calendar Project");
			$client->setClientId("");
			$client->setAssertionCredentials( 
				new Google_Auth_AssertionCredentials(
					"",
					array("https://www.googleapis.com/auth/calendar"),
					file_get_contents(JPATH_COMPONENT_SITE."/Calendar Project-56943acdc616.p12"),
					'notasecret','http://oauth.net/grant_type/jwt/1.0/bearer',false,false
				)
			);
		}
		catch (RuntimeException $e) {
		    return 'Problem authenticating Google Calendar:'.$e->getMessage();
		}
		
		// validate input
		$title = "Having lunch with company";
		$start_date = "20";
		$start_month = "12";
		$start_year = "2014";
		
		$end_date = "20";
		$end_month = "12";
		$end_year = "2014";
		$where = "Hanoi";
		//$start = date(DATE_ATOM, mktime(14, 14, 0, $start_month,$start_date, $start_year));
		//$end = date(DATE_ATOM, mktime(15, 15, 0, $end_month, $end_date, $end_year));
		$start =  "2014-12-31T08:00:00+00:00";
		$end   =  "2014-12-31T09:00:00+00:00";
		
		$service = new Google_Service_Calendar($client);		
		$newEvent = new Google_Service_Calendar_Event();
		$newEvent->setSummary($title);
		$newEvent->setLocation($where);
		$newEvent->setDescription($desc);
		$event_start = new Google_Service_Calendar_EventDateTime();
		$event_start->setDateTime($start);
		$newEvent->setStart($event_start);
		$event_end = new Google_Service_Calendar_EventDateTime();
		$event_end->setDateTime($end);
		$newEvent->setEnd($event_end);
		
		$createdEvent = null;
		//if($this->cal_id != ""){
			try {
				$createdEvent = $service->events->insert("", $newEvent);
				$createdEvent_id= $createdEvent->getId();
			} catch (Google_ServiceException $e) {
				logIt("svgcal_v3,".$e->getMessage()); 
//				echo $e->getMessage();
//				exit;
			}			
			
//		$createdEvent = $gdataCal->insertEvent($newEvent, "http://www.google.com/calendar/feeds/".$this->cal_id."/private/full");
			
		echo  $createdEvent_id;
		
		
		
		// construct event object
		// save to server  
		/*    
		try {
			$event = $gcal->newEventEntry();        
			$event->title = $gcal->newTitle($title);        
			$when = $gcal->newWhen();
			$when->startTime = $start;
			$when->endTime = $end;
			$event->when = array($when);        
			$gcal->insertEvent($event);   
		} catch (Zend_Gdata_App_Exception $e) {
			echo "Error: " . $e->getResponse();
		}
		*/
		echo 'Event successfully added!';    
	}

	
	static function testRepeat(){
		$return = HelperOSappscheduleCalendar::calculateBookingDate('2013-02-21','2013-03-29',2);
		print_r($return);
	}

	public static function testEmail($order_id){
		//OSBHelper::generateOrderPdf($order_id);
		HelperOSappscheduleCommon::sendEmail('reminder',$row->id);
	}

	public static function testSMS1()
    {
        HelperOSappscheduleCommon::sendSMS('confirmtoEmployee',2);
    }

	public static function testdate()
	{
		$config = JFactory::getConfig();
		$bdate  = "2022-06-29";
		$date	= JFactory::getDate($bdate, $config->get('offset'));
		$monday = clone $date->modify( 'Monday this week');
		$monday->setTime(0, 0, 0);
		$monday->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $monday->toSql(true);
		$sunday   = clone $date->modify('Sunday this week');
		$sunday->setTime(23, 59, 59);
		$sunday->setTimezone(new DateTimeZone('UCT'));
		$toDate = $sunday->toSql(true);

		echo $fromDate." - ".$toDate;
	}
}
?>