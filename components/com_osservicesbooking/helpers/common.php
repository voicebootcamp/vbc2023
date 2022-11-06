<?php
/*------------------------------------------------------------------------
# common.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Plugin\PluginHelper;
/**
 * Calendar class
 *
 */
class HelperOSappscheduleCommon
{

	public static function showDescription($desc)
	{
		$descArr = explode(" ",$desc);
		if(count($descArr) > 30)
		{
			for($i=0;$i<30;$i++)
			{
				echo $descArr[$i]." ";
			}
			echo "..";
		}
		else
		{
			echo $desc;
		}
	}

	/**
	 * Send Cancelled Email
	 *
	 * @param unknown_type $orderId
	 */
	static function sendCancelledEmail($orderId)
    {
        global $configClass;
        $db = JFactory::getDbo();
        $db->setQuery("Select * from #__app_sch_emails where email_key like 'admin_order_cancelled' and published = '1'");
        $email = $db->loadObject();
        $sbj = $email->email_subject;
        $body = $email->email_content;

        if ($sbj != "" && $body != "")
        {
            $body = stripslashes($body);
            $body = OSBHelper::convertImgTags($body);
            $db->setQuery("SELECT * FROM #__app_sch_orders WHERE id = '$orderId'");
            $order = $db->loadObject();

            $db->setQuery("SELECT * FROM #__app_sch_order_items WHERE order_id = '$orderId'");
            $items = $db->loadObjectList();

            ob_start();
            OsAppscheduleDefault::orderDetails($orderId, 0, true, true);
            $service = ob_get_contents();
            ob_end_clean();

			ob_start();
            OsAppscheduleDefault::orderDetails($orderId, 0, true, true, false);
            $serviceonly = ob_get_contents();
            ob_end_clean();

            $order_name		= $order->order_name;
            $order_phone	= $order->order_phone;
			if($order->dial_code != "")
			{
				$dial_code = $order->dial_code;
				if($dial_code != "")
				{
					$order_phone = "(".$dial_code.") ".$order->order_phone;
				}
			}
            $order_country	= $order->order_country;
            $order_state	= $order->order_state;
            $order_zip		= $order->order_zip;
            $order_city		= $order->order_city;
            $order_address	= $order->order_address;
            $order_notes	= $order->order_notes;
            $order_email	= $order->order_email;

            $deposit = $configClass['currency_symbol'] . " " . number_format($order->order_upfront, 2, '.', '') . " " . $configClass['currencyformat'];

            $tax = $configClass['currency_symbol'] . " " . number_format($order->order_tax, 2, '.', '') . " " . $configClass['currencyformat'];
            $total = $configClass['currency_symbol'] . " " . number_format($order->order_final_cost, 2, '.', '') . " " . $configClass['currencyformat'];

            $body = str_replace("{Name}", $order_name, $body);
            $body = str_replace("{Email}", $order_email, $body);
            $body = str_replace("{Phone}", $order_phone, $body);
            $body = str_replace("{Country}", $order_country, $body);
            $body = str_replace("{State}", $order_state, $body);
            $body = str_replace("{Zip}", $order_zip, $body);
            $body = str_replace("{City}", $order_city, $body);
            $body = str_replace("{Address}", $order_address, $body);
            $body = str_replace("{Notes}", $order_notes, $body);

            $body = str_replace("{BookingID}", $orderId, $body);
            $body = str_replace("{Services}", $service, $body);
			$body = str_replace("{ServicesOnly}", $serviceonly, $body);
            $body = str_replace("{Deposit}", $deposit, $body);
            $body = str_replace("{Tax}", $tax, $body);
            $body = str_replace("{Total}", $total, $body);

			$body = str_replace("{Canceller}", JFactory::getUser()->name, $body);

            if ($configClass['allow_cancel_request'] == 1)
            {

                $cancellink = JURI::root() . "index.php?option=com_osservicesbooking&task=default_cancelorder&id=" . $orderId . "&ref=" . md5($orderId);
                $cancellink = str_replace("components/com_osservicesbooking/", "", $cancellink);
                $cancellink = "<a href='$cancellink' title='" . JText::_('OS_CLICK_HERE_TO_CANCEL_THE_BOOKING_REQUEST') . "'>" . $cancellink . "</a>";
                $body = str_replace("{CancelURL}", $cancellink, $body);
            }
            else
            {
                $body = str_replace("{CancelURL}", "", $body);
            }
            //echo $body;
            //die();
            $config			= new JConfig();
            $mailfrom		= $config->mailfrom;
            $fromname		= $config->fromname;
            $order_email	= $configClass['value_string_email_address'];
            $mailer =		 JFactory::getMailer();
            if ($mailfrom != "" && $order_email != "")
            {
				try
				{
					$order_email = explode(",", $order_email);
					if(count($order_email) > 0)
					{
						foreach($order_email as $email)
						{
							$mailer->sendMail($mailfrom, $fromname, trim($email), $sbj, $body, 1);
						}
					}
				}
				catch (Exception $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
            }
        }
	}
	/**
	 * Send email
	 *
	 * @param unknown_type $email_type
	 */
	static function sendEmail($email_type,$orderId,$extradata="")
    {
		global $configClass;
		$config = new JConfig();
		//$offset = $config->offset;
		//date_default_timezone_set($offset);
		jimport('joomla.filesystem.folder');
		if(!JFolder::exists(JPATH_ROOT.'/media/com_osservicesbooking/icsfiles'))
        {
            JFolder::create(JPATH_ROOT.'/media/com_osservicesbooking/icsfiles');
        }
		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__app_sch_orders WHERE id = '$orderId'");
		$order              = $db->loadObject();
		$lang               = $order->order_lang;
		$email_key			= $email_type;
		switch ($email_type){
			case "order_status_changed_to_customer":
				$db->setQuery("Select * from #__app_sch_emails where email_key like 'order_status_changed_to_customer' and published = '1'");
				$email      = $db->loadObject();
				$sbj        = OSBHelper::getLanguageFieldValueOrder($email,'email_subject',$lang);
				$body       = OSBHelper::getLanguageFieldValueOrder($email,'email_content',$lang); //$email->email_content;
			break;
			case "confirm":
				if($order->order_payment == "os_offline" || $row->order_payment == "os_offline1")
				{
					if($order->order_status == "S") //already receive money
					{
						$email_key = "confirmation_email_offline_received";
					}
					else
					{
						$email_key = "confirmation_email_offline";
					}
				}
				else
				{
					$email_key = "confirmation_email";
				}
				$db->setQuery("Select * from #__app_sch_emails where email_key like '$email_key' and published = '1'");
				$email      = $db->loadObject();
				$sbj        = OSBHelper::getLanguageFieldValueOrder($email,'email_subject',$lang);
				$body       = OSBHelper::getLanguageFieldValueOrder($email,'email_content',$lang); //$email->email_content;
			break;
			case "payment":
				$db->setQuery("Select * from #__app_sch_emails where email_key like 'payment_message' and published = '1'");
				$email      = $db->loadObject();
				$sbj        = OSBHelper::getLanguageFieldValueOrder($email,'email_subject',$lang);
				$body       = OSBHelper::getLanguageFieldValueOrder($email,'email_content',$lang);
			break;
			case "reminder":
				$email_key	= "booking_reminder";
				
				$order_item_id = $orderId;
				$db->setQuery("Select order_id from #__app_sch_order_items where id = '$order_item_id'");
				$orderId    = $db->loadResult();

				$db->setQuery("SELECT * FROM #__app_sch_orders WHERE id = '$orderId'");
				$order      = $db->loadObject();
				$order_lang = $order->order_lang;
				if($order_lang == "")
				{
					$order_lang = OSBHelper::getDefaultLanguage();
				}
				$language   = JFactory::getLanguage();
				$language->load('com_osservicesbooking', JPATH_SITE, $order_lang, true);

				$db->setQuery("Select * from #__app_sch_emails where email_key like '$email_key' and published = '1'");
				$email      = $db->loadObject();
				$sbj        = OSBHelper::getLanguageFieldValueOrder($email,'email_subject',$order_lang);
				$body       = OSBHelper::getLanguageFieldValueOrder($email,'email_content',$order_lang);
			break;
			case "order_item_cancelled_to_administrator":
			case "order_item_cancelled_to_employee":
			case "order_item_cancelled_to_customer":
				
				
				$order_item_id = $orderId;
				$db->setQuery("Select order_id from #__app_sch_order_items where id = '$order_item_id'");
				$orderId    = $db->loadResult();

				$db->setQuery("Select eid from #__app_sch_order_items where id = '$order_item_id'");
				$eid		= $db->loadResult();
				$db->setQuery("Select employee_email from #__app_sch_employee where id = '$eid'");
				$employee_email = $db->loadResult();


				$db->setQuery("SELECT * FROM #__app_sch_orders WHERE id = '$orderId'");
				$order      = $db->loadObject();
				$order_lang = $order->order_lang;
				if($order_lang == "")
				{
					$order_lang = OSBHelper::getDefaultLanguage();
				}
				$language   = JFactory::getLanguage();
				$language->load('com_osservicesbooking', JPATH_SITE, $order_lang, true);

				$db->setQuery("Select * from #__app_sch_emails where email_key like '$email_key' and published = '1'");
				$email      = $db->loadObject();
				$sbj        = OSBHelper::getLanguageFieldValueOrder($email,'email_subject',$order_lang);
				$body       = OSBHelper::getLanguageFieldValueOrder($email,'email_content',$order_lang);
			break;
			case "admin":
				$email_key	= "admin_notification";
				$db->setQuery("Select * from #__app_sch_emails where email_key like '$email_key' and published = '1'");
				$email      = $db->loadObject();
				$sbj        = $email->email_subject;
				$body       = $email->email_content;
			break;
			case "remain_payment_notify_customer":
			case "order_updated_notification":
			case "admin_notification_offline_credit":
            case "attended_thankyou_email":
            case "customer_cancel_order":
			case "payment_failure":
                $db->setQuery("Select * from #__app_sch_emails where email_key like '$email_type' and published = '1'");
                $email      = $db->loadObject();
                $sbj        = OSBHelper::getLanguageFieldValueOrder($email,'email_subject',$lang);
				$body       = OSBHelper::getLanguageFieldValueOrder($email,'email_content',$lang);
            break;
		}
		if($sbj != "" && $body != "")
		{
            $body = stripslashes($body);
            $body = OSBHelper::convertImgTags($body);

            
            if ($email_type == "reminder" || $email_type == "order_item_cancelled_to_employee" || $email_type == "order_item_cancelled_to_customer" || $email_type == "order_item_cancelled_to_administrator")
            {
				ob_start();
                OsAppscheduleDefault::orderItemDetails($orderId, $order_item_id, true);
				$service = ob_get_contents();
				ob_end_clean();
            }
            else
            {
				ob_start();
                OsAppscheduleDefault::orderDetails($orderId, 0, true, true);
				$service = ob_get_contents();
				ob_end_clean();

				ob_start();
                OsAppscheduleDefault::orderDetails($orderId, 0, true, true,false);
				$serviceonly = ob_get_contents();
				ob_end_clean();
            }

            $order_name	    = $order->order_name;
            $order_email    = $order->order_email;
            $order_phone    = $order->order_phone;
			if($order->dial_code != "")
			{
				$dial_code = $order->dial_code;
				if($dial_code != "")
				{
					$order_phone = "(".$dial_code.") ".$order->order_phone;
				}
			}
            $order_country  = $order->order_country;
            $order_state    = $order->order_state;
            $order_zip      = $order->order_zip;
            $order_city     = $order->order_city;
            $order_address  = $order->order_address;
            $order_notes    = $order->order_notes;
            $order_status   = $order->order_status;
            $user_id        = $order->user_id;

            $deposit        = $configClass['currency_symbol'] . " " . number_format($order->order_upfront, 2, '.', '') . " " . $configClass['currencyformat'];
            $tax            = $configClass['currency_symbol'] . " " . number_format($order->order_tax, 2, '.', '') . " " . $configClass['currencyformat'];
            $total          = $configClass['currency_symbol'] . " " . number_format($order->order_final_cost, 2, '.', '') . " " . $configClass['currencyformat'];
			$amount         = $configClass['currency_symbol'] . " " . number_format($order->remain_payment_amount, 2, '.', '') . " " . $configClass['currencyformat'];

            $body = str_replace("{Name}", $order_name, $body);
            $body = str_replace("{Email}", $order_email, $body);
            $body = str_replace("{Phone}", $order_phone, $body);
            $body = str_replace("{Country}", $order_country, $body);
            $body = str_replace("{State}", $order_state, $body);
            $body = str_replace("{Zip}", $order_zip, $body);
            $body = str_replace("{City}", $order_city, $body);
            $body = str_replace("{Address}", $order_address, $body);
            $body = str_replace("{Notes}", $order_notes, $body);
			
            $body = str_replace("{BookingID}", $orderId, $body);
			$body = str_replace("{BookingId}", $orderId, $body);

			$sbj  = str_replace("{Name}", $order_name, $sbj);
			$sbj  = str_replace("{BookingID}", $orderId, $sbj);
			$sbj  = str_replace("{BookingId}", $orderId, $sbj);

            $body = str_replace("{Services}", $service, $body);
			$body = str_replace("{orderitem}", $service, $body);
			$body = str_replace("{ServicesOnly}", $serviceonly, $body);
            $body = str_replace("{Deposit}", $deposit, $body);
            $body = str_replace("{Tax}", $tax, $body);
            $body = str_replace("{Total}", $total, $body);
			$body = str_replace("{Amount}", $amount, $body);
			$body = str_replace("{TransactionID}", $order->transaction_id, $body);
			$body = str_replace("{remainpaymentTransactionID}", $order->remain_payment_transaction_id, $body);
			$body = str_replace("{Canceller}", JFactory::getUser()->name, $body);

			$currentUser = JFactory::getUser();		
			$body = str_replace("{User}", $currentUser->name ."(#".$currentUser->id.")", $body);

            $replaces = self::buildReplaceTags($orderId);
            foreach ($replaces as $key => $value)
            {
                $key     = strtoupper($key);
                $body    = str_replace("{$key}", $value, $body);
            }

			$order_payment = "";
			if($order->order_payment != "")
			{
				$db->setQuery("Select `title` from #__app_sch_plugins where `name` like '".$order->order_payment."'");
				$order_payment = $db->loadResult();
			}
			$body = str_replace("{Payment_method}", $order_payment, $body);

			if($email_type == "admin_notification_offline_credit")
			{
				$body = str_replace("{4lastdigits}", $extradata, $body);
			}

            $body = str_replace("{new_status}", OSBHelper::orderStatus(0, $order_status), $body);

			self::replaceCustomFields($body, $orderId);

			$root_link = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host'));
			if($root_link == "")
			{
				$root_link = JUri::root();
			}
			else
			{
				$root_link .= "/";
			}

            if ($configClass['allow_cancel_request'] == 1)
            {
				
                $cancellink = $root_link . "index.php?option=com_osservicesbooking&task=default_cancelorder&id=" . $orderId . "&ref=" . md5($orderId);
				$cancellink = str_replace("components/com_osservicesbooking","", $cancellink);
                $cancellink = "<a href='$cancellink' title='" . JText::_('OS_CLICK_HERE_TO_CANCEL_THE_BOOKING_REQUEST') . "'>" . $cancellink . "</a>";
                $body = str_replace("{CancelURL}", $cancellink, $body);
            }
            else
            {
                $body = str_replace("{CancelURL}", "", $body);
            }

			$orderDetailsLink = $root_link . "index.php?option=com_osservicesbooking&task=default_orderDetailsForm&order_id=" . $orderId . "&ref=" . md5($orderId);
			$body = str_replace("{OrderURL}", $orderDetailsLink, $body);

            $mailfrom = $config->mailfrom;
            $fromname = $config->fromname;
            if ($email_type == "admin" || $email_type == "admin_notification_offline_credit" || $email_type == "order_item_cancelled_to_administrator" || $email_type == "order_updated_notification" || $email_type == "remain_payment_notify_customer")
            {
                $order_email = $configClass['value_string_email_address'];
            }

            $attachment = array();
			/*
			echo $email_type;
			echo "<BR />";
			echo $configClass['send_invoice'];
			echo "<BR />";
			echo $order->order_status;
			die();
			*/

            if ($email_type == "confirm" || $email_type == "attended_thankyou_email")
            {
                if ($configClass['activate_invoice_feature'] == 1 && $configClass['send_invoice_to_customer'] == 1 && (($configClass['send_invoice'] == 0 && $order->order_status == "S" ) || ($configClass['send_invoice'] == 1 && $order->order_status == "A") || ($configClass['send_invoice'] == 2 && $order->order_status == "P")))
                {
                    //generate order pdf file
                    $return = OSBHelper::generateOrderPdf($orderId);
                    $attachment = array($return[0]);
                }
            }
            elseif ($email_type == "admin" || $email_type == "admin_notification_offline_credit")
            {
                if ($configClass['activate_invoice_feature'] == 1 && $configClass['send_invoice_to_admin'] == 1 && (($configClass['send_invoice'] == 0 && $order->order_status == "S" ) || ($configClass['send_invoice'] == 1 && $order->order_status == "A")))
                {
                    //generate order pdf file
                    $return = OSBHelper::generateOrderPdf($orderId);
                    $attachment = array($return[0]);
                }
            }
			elseif ($email_type == "payment" )
            {
                if ($configClass['activate_invoice_feature'] == 1 && $configClass['send_invoice_to_customer'] == 1 && $configClass['send_invoice_in_payment_email'] == 1)
                {
                    //generate order pdf file
                    $return = OSBHelper::generateOrderPdf($orderId);
                    $attachment = array($return[0]);
                }
            }

            $cc = array();
            $bcc = array();
            $mailer = JFactory::getMailer();
            //ics file
			$offset = $config->offset;
			date_default_timezone_set($offset);
            if($configClass['generate_ics'] == 1)
            {
				if($configClass['business_name'] == "")
				{
					$configClass['business_name'] = $fromname;
				}
				if($configClass['value_string_email_address'] == "")
				{
					$configClass['value_string_email_address'] = $mailfrom;
				}
                $db->setQuery("Select a.*, b.service_name, c.employee_name from #__app_sch_order_items as a inner join #__app_sch_services as b on b.id = a.sid inner join #__app_sch_employee as c on c.id = a.eid where a.order_id = '$orderId'");
                $orders = $db->loadObjectList();
                if(count($orders))
                {
                    foreach($orders as $orderitem)
                    {
                        $ics = new OSBHelperIcs();
						$gmttime =  strtotime(JFactory::getDate('now'));
						$current = OSBHelper::getCurrentDate();
						$distance = round(($current - $gmttime)/3600);
						$start_time = $orderitem->start_time + $distance*3600;
						$end_time	= $orderitem->end_time + $distance*3600;

						$customFieldValue = OSBHelper::getTimeslotFields($orderitem,'\n');
						$orderCustomFieldValue = OSBHelper::getOrderFields($order,'\n');

                        $description = $orderitem->employee_name. " ". $orderCustomFieldValue." ". $customFieldValue;
                        $db->setQuery("Select service_time_type from #__app_sch_services where id = '$orderitem->sid'");
                        $service_time_type = $db->loadResult();
                        if($service_time_type == 1)
                        {
                            $description .= JText::_('OS_NUMBER_SLOTS').": ".$orderitem->nslots;
                        }
                        $ics->setName($orderitem->service_name)
                            ->setDescription($description)
                            ->setStart(gmdate("Y-m-d H:i", $start_time))
                            ->setEnd(gmdate("Y-m-d H:i", $end_time))
							->setOrganizer($configClass['value_string_email_address'], $configClass['business_name']);
                        if($orderitem->vid > 0)
                        {
                            $db->setQuery("Select * from #__app_sch_venues where id = '$orderitem->vid'");
                            $venue = $db->loadObject();
                            $location = $venue->venue_name;
                            if($venue->address != "")
                            {
                                $location .= ", ".$venue->address;
                            }
                            if($venue->city != "")
                            {
                                $location .= ", ".$venue->city;
                            }
                            if($venue->state != "")
                            {
                                $location .= ", ".$venue->state;
                            }
                            if($venue->country != "")
                            {
                                $location .= ", ".$venue->country;
                            }
                            $ics->setLocation($location);
                        }

                        $fileName = "Booking_".$orderId."_".$orderitem->id.".ics";
                        if ((($email_type == "admin" || $email_type == "admin_notification_offline_credit") && $configClass['send_ics_to_administrator'] == 1) || $email_type == "confirm")
                        {
                            $mailer->addAttachment($ics->save(JPATH_ROOT . '/media/com_osservicesbooking/icsfiles/' , $fileName));
                            $attachment[] = JPATH_ROOT . '/media/com_osservicesbooking/icsfiles/' . $fileName;
                        }
                    }
                }
            }


            if ($mailfrom != "" && $order_email != "")
            {
                if ($email_type == "confirm" || $email_type == "attended_thankyou_email") 
                {
					try
					{
						if ($mailer->SendMail($mailfrom, $fromname, $order_email, $sbj, $body, 1, $cc, $bcc, $attachment))
						{
							OSBHelper::logMail($email_key, $order->id, $order_email, $sbj, $body);
							$db->setQuery("UPDATE #__app_sch_orders SET send_email = '1' WHERE id = '$orderId'");
							$db->execute();
						}
						else
						{
							$db->setQuery("UPDATE #__app_sch_orders SET send_email = '0' WHERE id = '$orderId'");
							$db->execute();
						}
					}
					catch (Exception $e)
					{
						JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
						$db->setQuery("UPDATE #__app_sch_orders SET send_email = '0' WHERE id = '$orderId'");
						$db->execute();
					}
					if($email_type == "attended_thankyou_email" && $configClass['send_invoice'] == 1 && $order->order_status == "A")
					{
						//send file to administrator
						$mailer = JFactory::getMailer();
						try
						{
							$mailer->SendMail($mailfrom, $fromname, $configClass['value_string_email_address'], $sbj, $body, 1, $cc, $bcc, $attachment);
							OSBHelper::logMail($email_key, $order->id,$configClass['value_string_email_address'], $sbj, $body);
						}
						catch (Exception $e)
						{
							JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
						}
					}
                }
				elseif($email_type == "admin" || $email_type == "admin_notification_offline_credit")
				{
					$order_emailArr = explode(",", $order_email);
					if(count($order_emailArr))
					{
						foreach($order_emailArr as $order_email)
						{
							$mailer = JFactory::getMailer();
							try
							{
								OSBHelper::logMail($email_key, $order->id, $order_email, $sbj, $body);
								$mailer->SendMail($mailfrom, $fromname, $order_email, $sbj, $body, 1, $cc, $bcc, $attachment);
							}
							catch (Exception $e)
							{
								JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
							}
						}
					}
				}
				elseif($email_type == "order_item_cancelled_to_employee" && $employee_email != "")
				{
					try
					{
						OSBHelper::logMail($email_key, $order->id, $employee_email, $sbj, $body);
						$mailer->SendMail($mailfrom, $fromname, $employee_email, $sbj, $body, 1);
					}
					catch (Exception $e)
					{
						JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
					}
				}
                else
                {
					try
					{
						OSBHelper::logMail($email_key, $order->id, $order_email, $sbj, $body);
						if($mailer->SendMail($mailfrom, $fromname, $order_email, $sbj, $body, 1, $cc, $bcc))
						{
							$db->setQuery("UPDATE #__app_sch_orders SET send_email = '1' WHERE id = '$orderId'");
							$db->execute();
						}
					}
					catch (Exception $e)
					{
						JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
					}
                }
            }
        }
	}

	static function sendPaymentRequestEmails($order)
	{
		global $configClass;
		$db                 = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_emails where email_key like 'payment_request' and published = '1'");
		$email              = $db->loadObject();

		$order_name     = $order->order_name;
		$order_email    = $order->order_email;
		$order_lang		= $order->order_lang;
		if($order_lang != "")
		{
			$lang_code  = explode("-", $order_lang);
			$lang_code  = $lang_code[0];
		}
		
		$default_lang	= OSBHelper::getDefaultLanguage();
		if($default_lang != "")
		{
			$default_lang  = explode("-", $default_lang);
			$default_lang  = $default_lang[0];
		}
		$suffix			= "";
		if($lang_code != "")
		{
			if($default_lang == $lang_code)
			{
				$suffix	= "";
			}
			else
			{
				$suffix	    = "_".$lang_code;
			}
		}
		if($suffix == "")
		{
			$sbj        = $email->email_subject;
			$body       = $email->email_content;
		}
		else
		{
			$sbj        = $email->{'email_subject'.$suffix};
			$body       = $email->{'email_content'.$suffix};
		}

		if($sbj != "" && $body != "" && JMailHelper::isEmailAddress($order_email))
		{
            $body           = stripslashes($body);
            $body           = OSBHelper::convertImgTags($body);

            $deposit        = $configClass['currency_symbol'] . " " . number_format($order->order_upfront, 2, '.', '') . " " . $configClass['currencyformat'];

            $tax            = $configClass['currency_symbol'] . " " . number_format($order->order_tax, 2, '.', '') . " " . $configClass['currencyformat'];
            $total          = $configClass['currency_symbol'] . " " . number_format($order->order_final_cost, 2, '.', '') . " " . $configClass['currencyformat'];

			$sbj			= str_replace("{BookingID}", $order->id, $sbj);
            $body           = str_replace("{Name}", $order_name, $body);
			$body			= str_replace("{Total}", $deposit, $body);
			$body			= str_replace("{BookingID}", $order->id, $body);

			$payment_link	= JUri::root()."index.php?option=com_osservicesbooking&task=default_payment&order_id=".$order->id;
			$body			= str_replace("{payment_link}", $payment_link, $body);
			$order_payment = "";
			if($order->order_payment != "")
			{
				$db->setQuery("Select `title` from #__app_sch_plugins where `name` like '".$order->order_payment."'");
				$order_payment = $db->loadResult();
			}
			$body			= str_replace("{Payment_method}", $order_payment, $body);
			$order_status   = $order->order_status;
			$body			= str_replace("{new_status}", OSBHelper::orderStatus(0, $order_status), $body);
			$mailer			= JFactory::getMailer();
			try
			{
				OSBHelper::logMail('payment_request', $order->id, $order_email, $sbj, $body);
				$mailer->sendMail(JFactory::getConfig()->get('mailfrom'), JFactory::getConfig()->get('fromname'), $order_email, $sbj, $body, 1);
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
        }
	}
	
	/**
	 * Send the notification email to employee
	 *
	 * @param unknown_type $order_id
	 */
	
	static function sendEmployeeEmailRemoveOneOrderItem($email_type,$orderItemId, $orderId,$eid = 0)
    {
		global $configClass;
		$db                 = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_emails where email_key like '$email_type' and published = '1'");
		$email              = $db->loadObject();
		$sbj                = $email->email_subject;
		$body               = $email->email_content;

		if($sbj != "" && $body != "")
		{
            $body           = stripslashes($body);
            $body           = OSBHelper::convertImgTags($body);
            $db->setQuery("SELECT * FROM #__app_sch_orders WHERE id = '$orderId'");
            $order          = $db->loadObject();

            $db->setQuery("SELECT * FROM #__app_sch_order_items WHERE id = '$orderItemId'");
            $item           = $db->loadObject();

            $order_name     = $order->order_name;
            $order_email    = $order->order_email;
            $order_phone    = $order->order_phone;
			if($order->dial_code != "")
			{
				$dial_code = $order->dial_code;
				if($dial_code != "")
				{
					$order_phone = "(".$dial_code.") ".$order->order_phone;
				}
			}
            $order_country  = $order->order_country;
            $order_state    = $order->order_state;
            $order_zip      = $order->order_zip;
            $order_city     = $order->order_city;
            $order_address  = $order->order_address;
            $order_notes    = $order->order_notes;
            $order_status   = $order->order_status;

            $deposit        = $configClass['currency_symbol'] . " " . number_format($order->order_upfront, 2, '.', '') . " " . $configClass['currencyformat'];

            $tax            = $configClass['currency_symbol'] . " " . number_format($order->order_tax, 2, '.', '') . " " . $configClass['currencyformat'];
            $total          = $configClass['currency_symbol'] . " " . number_format($order->order_final_cost, 2, '.', '') . " " . $configClass['currencyformat'];

            $body           = str_replace("{Name}", $order_name, $body);
            $body           = str_replace("{Email}", $order_email, $body);
            $body           = str_replace("{Phone}", $order_phone, $body);
            $body           = str_replace("{Country}", $order_country, $body);
            $body           = str_replace("{State}", $order_state, $body);
            $body           = str_replace("{Zip}", $order_zip, $body);
            $body           = str_replace("{City}", $order_city, $body);
            $body           = str_replace("{Address}", $order_address, $body);
            $body           = str_replace("{Notes}", $order_notes, $body);
            $body           = str_replace("{newstatus}", OSBHelper::orderStatus(0, $order_status), $body);
            //$body = str_replace("{Services}",$service,$body);

			$order_payment = "";
			if($order->order_payment != "")
			{
				$db->setQuery("Select `title` from #__app_sch_plugins where `name` like '".$order->order_payment."'");
				$order_payment = $db->loadResult();
			}
			$body = str_replace("{Payment_method}", $order_payment, $body);

			self::replaceCustomFields($body, $orderId);

            $query          = "Select * from #__app_sch_employee where id = '$eid' and employee_send_email = '1'";
            $db->setQuery($query);
            $row            = $db->loadObject();
			$body           = str_replace("{Employee}", $row->employee_name, $body);
			$sbj            = str_replace("{Employee}", $row->employee_name, $sbj);
            if ($row->id > 0)
            {
                $config     = new JConfig();
                $offset     = $config->offset;
                date_default_timezone_set($offset);
                $body1      = $body;
                $email      = $row->employee_email;
                $start_time = date($configClass['time_format'], $item->start_time);
                $end_time   = date($configClass['time_format'], $item->end_time);
                $booking_date = $item->booking_date;
                $body1      = str_replace("{Starttime}", $start_time, $body1);
                $body1      = str_replace("{Endtime}", $end_time, $body1);
                $body1      = str_replace("{Bookingdate}", $booking_date, $body1);
                $db->setQuery("Select service_name from #__app_sch_services where id = '$item->sid'");
                $service    = $db->loadResult();
                $body1      = str_replace("{Services}", $service, $body1);
				$body1      = str_replace("{ServicesOnly}", $service, $body1);
                $config     = new JConfig();
                $mailfrom   = $config->mailfrom;
                $fromname   = $config->fromname;
                if ($mailfrom != "" && $email != "")
                {
                    $mailer = JFactory::getMailer();
					try
					{
						OSBHelper::logMail($email_type, $orderId, $email, $sbj, $body);
						$mailer->sendMail($mailfrom, $fromname, $email, $sbj, $body1, 1);
					}
					catch (Exception $e)
					{
						JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
					}
                }
            }
        }
	}

    /**
     * @param $email_type
     * @param $orderId
     * @param int $eid
     */
	static function sendEmployeeEmail($email_type,$orderId,$eid = 0)
    {
		global $configClass;
		$db                 = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_emails where email_key like '$email_type' and published = '1'");
		$email              = $db->loadObject();
		$sbj                = $email->email_subject;
		$body               = $email->email_content;

		if($sbj != "" && $body != "")
		{
            $body           = stripslashes($body);
            $body           = OSBHelper::convertImgTags($body);
            $db->setQuery("SELECT * FROM #__app_sch_orders WHERE id = '$orderId'");
            $order          = $db->loadObject();

            $order_name     = $order->order_name;
            $order_email    = $order->order_email;
            $order_phone    = $order->order_phone;
			if($order->dial_code != "")
			{
				$dial_code = $order->dial_code;
				if($dial_code != "")
				{
					$order_phone = "(".$dial_code.") ".$order->order_phone;
				}
			}
            $order_country  = $order->order_country;
            $order_state    = $order->order_state;
            $order_zip      = $order->order_zip;
            $order_city     = $order->order_city;
            $order_address  = $order->order_address;
            $order_notes    = $order->order_notes;
            $order_status   = $order->order_status;

            $body           = str_replace("{Name}", $order_name, $body);
            $body           = str_replace("{Email}", $order_email, $body);
            $body           = str_replace("{Phone}", $order_phone, $body);
            $body           = str_replace("{Country}", $order_country, $body);
            $body           = str_replace("{State}", $order_state, $body);
            $body           = str_replace("{Zip}", $order_zip, $body);
            $body           = str_replace("{City}", $order_city, $body);
            $body           = str_replace("{Address}", $order_address, $body);
            $body           = str_replace("{Notes}", $order_notes, $body);
			$body           = str_replace("{BookingId}", $orderId, $body);
            $body           = str_replace("{newstatus}", OSBHelper::orderStatus(0, $order_status), $body);
			$order_payment = "";
			if($order->order_payment != "")
			{
				$db->setQuery("Select `title` from #__app_sch_plugins where `name` like '".$order->order_payment."'");
				$order_payment = $db->loadResult();
			}
			$body = str_replace("{Payment_method}", $order_payment, $body);

			self::replaceCustomFields($body, $orderId);

            $query = "Select distinct a.id from #__app_sch_employee as a"
                . " inner join #__app_sch_order_items as b on b.eid = a.id"
                . " where b.order_id = '$orderId'"
                . " and a.employee_send_email = '1'";
            if ($eid > 0) {
                $query .= " and a.id = '$eid'";
            }
            $db->setQuery($query);
            $employeeArr    = $db->loadColumn(0);

            if (count($employeeArr) > 0)
            {
                foreach ($employeeArr as $employee)
                {
                    $config = new JConfig();
                    $offset = $config->offset;
                    date_default_timezone_set($offset);
                    $query = "Select a.*,b.start_time,b.end_time,b.booking_date,b.sid from #__app_sch_employee as a"
                        . " inner join #__app_sch_order_items as b on b.eid = a.id"
                        . " where b.order_id = '$orderId'"
                        . " and a.employee_send_email = '1'"
                        . " and a.id = '" . $employee . "'";
                    $db->setQuery($query);
                    $rows = $db->loadObjectList();

                    if (count($rows) > 0)
                    {
                        $email = $rows[0]->employee_email;
                        $body1 = $body;
						$body1 = str_replace("{Employee}", $rows[0]->employee_name, $body1);
						$sbj1  = str_replace("{Employee}", $rows[0]->employee_name, $sbj);
                        $tempdata = array();
                        for ($i = 0; $i < count($rows); $i++)
                        {
                            $row = $rows[$i];
                            $db->setQuery("Select service_name, service_time_type from #__app_sch_services where id = '$row->sid'");
                            $service = $db->loadObject();
                            $service_name = $service->service_name;
                            $service_time_type = $service_name->service_time_type;
                            $row->service_name = $service_name;
                            $row->service_time_type = $service_time_type;
                            $tempvalue = $service_name . "(" . date($configClass['time_format'], $row->start_time) . " - " . date($configClass['time_format'], $row->end_time) . " " . $row->booking_date . ")";
                            if ($service_time_type == 1)
                            {
                                $tempvalue .= ". " . JText::_('OS_NUMBER_SLOT') . ": " . $row->nslots;
                            }
                            $db->setQuery("Select a.* from #__app_sch_venues as a inner join #__app_sch_employee_service as b on b.vid = a.id where b.employee_id = '$row->eid' and b.service_id = '$row->id'");
                            $venue = $db->loadObject();
                            if (OSBHelper::getLanguageFieldValueOrder($venue, 'address', $order_lang) != "")
                            {
                                $tempvalue .= ". " . JText::_('OS_VENUE') . ": " . OSBHelper::getLanguageFieldValueOrder($venue, 'address', $order_lang);
                            }
                            $db->setQuery("Select * from #__app_sch_fields where field_area = '0' and published = '1' order by ordering");
                            $fields = $db->loadObjectList();
                            if (count($fields) > 0)
                            {
                                for ($i1 = 0; $i1 < count($fields); $i1++)
                                {
                                    $field = $fields[$i1];
                                    $db->setQuery("Select count(id) from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
                                    $count = $db->loadResult();
                                    if ($count > 0)
                                    {
                                        if ($field->field_type == 1)
                                        {
                                            $db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
                                            $option_id = $db->loadResult();
                                            $db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
                                            $optionvalue = $db->loadObject();
                                            $tempvalue .= ". " . OSBHelper::getLanguageFieldValueOrder($field, 'field_label', $order_lang); //$field->field_label;
                                            $field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue, 'field_option', $order_lang); //$optionvalue->field_option;
                                            if (($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0))
                                            {
                                                $field_data .= " - (" . OSBHelper::showMoney($optionvalue->additional_price ,0). ")";
                                            }
                                            $tempvalue .= $field_data;
                                        }
                                        elseif ($field->field_type == 2)
                                        {
                                            $db->setQuery("Select option_id from #__app_sch_order_field_options where order_item_id = '$row->id' and field_id = '$field->id'");
                                            $option_ids = $db->loadObjectList();
                                            $fieldArr = array();
                                            for ($j = 0; $j < count($option_ids); $j++)
                                            {
                                                $oid = $option_ids[$j];
                                                $db->setQuery("Select * from #__app_sch_field_options where id = '$oid->option_id'");
                                                $optionvalue = $db->loadObject();
                                                $field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue, 'field_option', $order_lang); //$optionvalue->field_option;
                                                if (($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0))
                                                {
                                                    $field_data .= " - (" . OSBHelper::showMoney($optionvalue->additional_price ,0). ")";
                                                }
                                                $fieldArr[] = $field_data;
                                            }
                                            $tempvalue .= ". " . OSBHelper::getLanguageFieldValueOrder($field, 'field_label', $order_lang); //$field->field_label;
                                            $tempvalue .= implode(", ", $fieldArr);
                                        }
                                    }
                                }
                            }
                            $tempdata[] = $tempvalue;
                        }

                        $tempdata       = implode("<BR />", $tempdata);
                        $body1          = str_replace("{timeslots}", $tempdata, $body1);
                        $config         = new JConfig();
                        $mailfrom       = $config->mailfrom;
                        $fromname       = $config->fromname;

                        if ($mailfrom != "" && $email != "")
                        {
                            $attachment = array();
                            $mailer     = JFactory::getMailer();
                            if($configClass['send_ics_to_administrator'] == 1)
                            {
								if($configClass['business_name'] == "")
								{
									$configClass['business_name'] = $fromname;
								}
								if($configClass['value_string_email_address'] == "")
								{
									$configClass['value_string_email_address'] = $mailfrom;
								}
                                if(count($rows))
                                {
                                    foreach($rows as $row)
                                    {
                                        $ics			= new OSBHelperIcs();
										$gmttime		=  strtotime(JFactory::getDate('now'));
										$current		= OSBHelper::getCurrentDate();
										$distance		= round(($current - $gmttime)/3600);
										
										$start_time		= $row->start_time + $distance*3600;
										$end_time		= $row->end_time + $distance*3600;
										
										$offset			= $config->offset;
										date_default_timezone_set($offset);
								
										$customFieldValue = OSBHelper::getTimeslotFields($orderitem,'\n');
										$orderCustomFieldValue = OSBHelper::getOrderFields($order,'\n');

                                        $ics->setName($row->service_name);
                                        $description = JText::_('OS_FROM').": ".date($configClass['time_format'],$row->start_time)." ".JText::_('OS_TO').": ".date($configClass['time_format'],$row->end_time)." ". $orderCustomFieldValue ." " .$customFieldValue;
                                        if($row->service_time_type == 1)
                                        {
                                            $description .= ". ". JText::_('OS_NUMBER_SLOT') . ": " . $row->nslots;
                                        }
                                        $ics->setDescription($description)
                                            ->setStart(gmdate("Y-m-d H:i", $start_time))
											->setEnd(gmdate("Y-m-d H:i", $end_time))
											->setOrganizer($configClass['value_string_email_address'], $configClass['business_name']);
                                        if($row->vid > 0)
                                        {
                                            $db->setQuery("Select * from #__app_sch_venues where id = '$row->vid'");
                                            $venue = $db->loadObject();
                                            $location = $venue->venue_name;
                                            if($venue->address != "")
                                            {
                                                $location .= ", ".$venue->address;
                                            }
                                            if($venue->city != "")
                                            {
                                                $location .= ", ".$venue->city;
                                            }
                                            if($venue->state != "")
                                            {
                                                $location .= ", ".$venue->state;
                                            }
                                            if($venue->country != "")
                                            {
                                                $location .= ", ".$venue->country;
                                            }
                                            $ics->setLocation($location);
                                        }
                                        $fileName = "Booking_".$orderId."_".$row->id."_".$employee.".ics";
                                        //$ics->save(JPATH_ROOT.'/media/com_osservicesbooking/icsfiles/',$fileName);
                                        $mailer->addAttachment($ics->save(JPATH_ROOT.'/media/com_osservicesbooking/icsfiles/',$fileName));
                                        $attachment[] = JPATH_ROOT.'/media/com_osservicesbooking/icsfiles/'.$fileName;
                                    }
                                }
                            }
                            $cc = array();
                            $bcc = array();
							try
							{
								OSBHelper::logMail($email_type, $orderId, $email, $sbj1, $body1);
								$mailer->sendMail($mailfrom, $fromname, $email, $sbj1, $body1, 1, $cc, $bcc, $attachment);
							}
							catch (Exception $e)
							{
								JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
							}
                        }
                    }
                }
            }
        }
	}
	
	/**
	 * Send SMS
	 *
	 * @param unknown_type $key
	 * @param unknown_type $orderId
	 */
    static function sendSMS($key, $orderId, $orderItemId = 0)
	{
        global $mainframe,$mapClass,$configClass;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $employeePhones = array();
        //ready to prepare the sms content
        switch ($key)
		{
            case "confirm":
                if($configClass['sms_new_booking_to_customer_checkbox'] == 1){
                    $smscontent = $configClass['sms_new_booking_to_customer'];
                    $sms_phone	= self::getCustomerMobileNumber($orderId);
                    $employeePhones[] = $sms_phone;
                }
                $bookingDetails = OSBHelper::loadOrderDetailsSMS($orderId,0 ,0);
                break;
            case "payment": //for admin
                if($configClass['sms_payment_complete_to_admin_checkbox'] == 1){
                    $smscontent = $configClass['sms_payment_complete_to_admin'];
                    $sms_phone	= self::getAdminMobileNumber();
                    $employeePhones[] = $sms_phone;
                }
				
                $bookingDetails = OSBHelper::loadOrderDetailsSMS($orderId,0 ,0);
                break;
            case "reminder":
                if($configClass['sms_reminder_notification_checkbox'] == 1){
                    $smscontent = $configClass['sms_reminder_notification'];
                    $sms_phone	= self::getCustomerMobileNumber($orderId);
                    $employeePhones[] = $sms_phone;
                }
				if($orderItemId > 0)
				{
					$bookingDetails = OSBHelper::loadOrderDetailsSMS($orderId, $orderItemId , 0);
				}
				else
				{
					$bookingDetails = OSBHelper::loadOrderDetailsSMS($orderId,0 ,0);
				}
                break;
            case "admin":
                if($configClass['sms_new_booking_to_admin_checkbox'] == 1){
                    $smscontent = $configClass['sms_new_booking_to_admin'];
                    $sms_phone	= self::getAdminMobileNumber();
                    $employeePhones[] = $sms_phone;
                }
                $bookingDetails = OSBHelper::loadOrderDetailsSMS($orderId,0 ,0);
                break;
            case "cancel":
                if($configClass['sms_order_cancelled_notification_checkbox'] == 1){
                    $smscontent = $configClass['sms_order_cancelled_notification'];
                    $sms_phone	= self::getAdminMobileNumber();
                    $employeePhones[] = $sms_phone;
                }
                $bookingDetails = OSBHelper::loadOrderDetailsSMS($orderId,0 ,0);
                break;
            case "confirmtoEmployee":
                if($configClass['sms_new_booking_to_employee_checkbox'] == 1){
                    $smscontent = $configClass['sms_new_booking_to_employee'];
                    $query->clear();
                    $query->select('a.employee_phone')
                        ->from('#__app_sch_employee AS a')
                        ->innerJoin('#__app_sch_order_items AS b ON b.eid = a.id')
                        ->where('b.order_id = '.$orderId);
                    $db->setQuery($query);
                    $employeePhones = array_filter($db->loadColumn());

                    $query->clear();
                    $query->select('a.id')
                        ->from('#__app_sch_employee AS a')
                        ->innerJoin('#__app_sch_order_items AS b ON b.eid = a.id')
                        ->where('b.order_id = '.$orderId);
                    $db->setQuery($query);
                    $employeeIds = array_filter($db->loadColumn());
                }
                break;
            case "canceltoEmployee":
                if($configClass['sms_order_cancelled_notification_employee_checkbox'] == 1){
                    $smscontent = $configClass['sms_order_cancelled_notification_employee'];
                    $query->clear();
                    $query->select('a.employee_phone')
                        ->from('#__app_sch_employee AS a')
                        ->innerJoin('#__app_sch_order_items AS b ON b.eid = a.id')
                        ->where('b.order_id = '.$orderId);
                    $db->setQuery($query);
                    $employeePhones = array_filter($db->loadColumn());

                    $query->clear();
                    $query->select('a.id')
                        ->from('#__app_sch_employee AS a')
                        ->innerJoin('#__app_sch_order_items AS b ON b.eid = a.id')
                        ->where('b.order_id = '.$orderId);
                    $db->setQuery($query);
                    $employeeIds = array_filter($db->loadColumn());
                }
                break;
            case "paymenttoEmployee": //for admin
                if($configClass['sms_payment_complete_to_employee_checkbox'] == 1){
                    $smscontent = $configClass['sms_payment_complete_to_employee'];
                    $query->clear();
                    $query->select('a.employee_phone')
                        ->from('#__app_sch_employee AS a')
                        ->innerJoin('#__app_sch_order_items AS b ON b.eid = a.id')
                        ->where('b.order_id = '.$orderId);
                    $db->setQuery($query);
                    $employeePhones = array_filter($db->loadColumn());

                    $query->clear();
                    $query->select('a.id')
                        ->from('#__app_sch_employee AS a')
                        ->innerJoin('#__app_sch_order_items AS b ON b.eid = a.id')
                        ->where('b.order_id = '.$orderId);
                    $db->setQuery($query);
                    $employeeIds = array_filter($db->loadColumn());
                }
                break;
            case "order_status_changed_to_customer":
                if($configClass['order_status_changed_to_customer_checkbox'] == 1){
                    $smscontent = $configClass['order_status_changed_to_customer'];
                    $sms_phone	= self::getCustomerMobileNumber($orderId);
                    $employeePhones[] = $sms_phone;
                }
                $bookingDetails = OSBHelper::loadOrderDetailsSMS($orderId,0 ,0);
                break;
        }

        if($smscontent != '' && count($employeePhones) > 0) 
		{
            $query->clear();
            $query->select('a.employee_email')
                ->from('#__app_sch_employee AS a')
                ->innerJoin('#__app_sch_order_items AS b ON b.eid = a.id')
                ->where('b.order_id = ' . $orderId);
            $db->setQuery($query);
            $employeeEmails = array_filter($db->loadColumn());

            $db->setQuery("SELECT * FROM #__app_sch_orders WHERE id = '$orderId'");
            $order = $db->loadObject();

			if($order->dial_code != "")
			{
				$dial_code = $order->dial_code;
				if($dial_code != "")
				{
					$order_phone = "(".$dial_code.") ".$order->order_phone;
				}
				else
				{
					$order_phone = $order->order_phone;
				}
			}
			else
			{
				$order_phone = $order->order_phone;
			}

            $smscontent = str_replace("{OrderID}", $orderId, $smscontent);
            $smscontent = str_replace("{User}", $order->order_name, $smscontent);
            $smscontent = str_replace("{Email}", implode(',', $employeeEmails), $smscontent);
            $smscontent = str_replace("{business_name}", $configClass['business_name'], $smscontent);
            $smscontent = str_replace("{OrderStatus}", OSBHelper::orderStatus(0, $order->order_status), $smscontent);
            $smscontent = str_replace("{Name}", $order->order_name, $smscontent);
            $smscontent = str_replace("{Tel}", $order_phone, $smscontent);
            $smscontent = str_replace("{Address}", $order->order_address, $smscontent);
            $smscontent = str_replace("{Message}", $order->order_notes, $smscontent);
            $smscontent = str_replace("{Time}", $order->order_date, $smscontent);

            $tempArr    = array('confirmtoEmployee','canceltoEmployee','paymenttoEmployee','reminder');
            if(!in_array($key,$tempArr))
            {
                $smscontent = str_replace("{Orders_details}", $bookingDetails, $smscontent);
            }
			

            $smscontent1 = $smscontent;

            $e = 0;
            foreach ($employeePhones as $sms_phone)
            {
                if(isset($employeeIds) && count($employeeIds) > 0)
                {
                    $eid = $employeeIds[$e];
                    if($eid > 0)
                    {
                        $smscontent = $smscontent1;
                        $bookingDetails = OSBHelper::loadOrderDetailsSMS($orderId,0, $eid);
                        $smscontent = str_replace("{Orders_details}", $bookingDetails, $smscontent);
                    }
                }
				$sms_phone = str_replace("-", "", $sms_phone);
				$sms_phone = str_replace("+", "", $sms_phone);
				$sms_phone = str_replace(" ", "", $sms_phone);

				self::doSendSMS($sms_phone, $smscontent);
                $e++;
            }
        }
	}

	static function doSendSMS($sms_phone, $smscontent)
	{
		global $mainframe;
		PluginHelper::importPlugin('osservicesbooking');
		$mainframe->triggerEvent('onSmsSending', [$sms_phone, $smscontent]);
	}

	public static function doSendSMS_old($sms_phone, $smscontent)
    {
        global $configClass;
        if ($configClass['enable_clickatell'] == 1)
        { //enable Clickatell sms
            if ($configClass['clickatell_username'] != "" && $configClass['clickatell_password'] != "" && $configClass['clickatell_api'] != "")
            {
                // $smscontent = str_replace(" ", "+", $smscontent);
                if (($smscontent != "") && ($sms_phone != ""))
                {
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

                        $url .= "&concat=3&text=" . $smscontent;


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

        if ($configClass['enable_eztexting'] == 1)
        { //enable Clickatell sms
            if ($configClass['eztexting_username'] != "" && $configClass['eztexting_password'] != "")
            {
                if ($smscontent != "" && $sms_phone != "")
                {
                    $ch = curl_init('https://app.eztexting.com/api/sending');
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "user=" . $configClass['eztexting_username'] .
                        "&pass=" . trim($configClass['eztexting_password']) .
                        "&phonenumber=" . $sms_phone .
                        "&message=" . $smscontent .
                        "&express=1");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data = curl_exec($ch);
                    //print($data); /* result of API call*/
                    switch ($data)
                    {
                        case 1:
                            $returnCode = JText::_('OS_EZTEXTING_CODE_1');
                            break;
                        case -1:
                            $returnCode = JText::_('OS_EZTEXTING_CODE_ERR_1');
                            break;
                        case -2:
                            $returnCode = JText::_('OS_EZTEXTING_CODE_ERR_2');
                            break;
                        case -5:
                            $returnCode = JText::_('OS_EZTEXTING_CODE_ERR_5');
                            break;
                        case -7:
                            $returnCode = JText::_('OS_EZTEXTING_CODE_ERR_7');
                            break;
                        case -104:
                            $returnCode = JText::_('OS_EZTEXTING_CODE_ERR_104');
                            break;
                        case -106:
                            $returnCode = JText::_('OS_EZTEXTING_CODE_ERR_106');
                            break;
                        case -10:
                            $returnCode = JText::_('OS_EZTEXTING_CODE_ERR_10');
                            break;
                    }
                    if ($data == 1) {
                        //return true;
                    } else {
                        //return false;
                    }
                    //}//sess ok
                }//sms content and sms phone is not empty
            }//config ready end
        }//end EzTexing

        //textlocal
        if ($configClass['enable_textlocal'] == 1 && file_exists(JPATH_COMPONENT. '/helpers/textlocal.class.php'))
        {
            require_once JPATH_COMPONENT. '/helpers/textlocal.class.php';
            if ($configClass['textlocal_apikey'] != "" && $configClass['textlocal_sender'] != "" && $smscontent != "" && $sms_phone != "")
            {
                $sms_phone = str_replace("-", "", $sms_phone);
                $sms_phone = str_replace("+", "", $sms_phone);
                $sms_phone = str_replace(" ", "", $sms_phone);
                $client = new Textlocal(false, false, $configClass['textlocal_apikey']);
                $client->sendSms([$sms_phone], $smscontent, $configClass['textlocal_sender']);
            }
        }
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
	
	public static function utf16urlencode($str){
	    $str = mb_convert_encoding($str, 'UTF-16', 'UTF-8');
	    $out ='';
	    for ($i = 0; $i < mb_strlen($str, 'UTF-16'); $i++)
	    {
	        $out .= bin2hex(mb_substr($str, $i, 1, 'UTF-16'));
	    }
	    return $out;
	}
	
	public static function getCustomerMobileNumber($orderID){
		global $mainframe,$mapClass,$configClass;
		$db = JFactory::getDbo();
		$phone_mobile = "";
		$db->setQuery("Select dial_code,order_phone from #__app_sch_orders where id = '$orderID'");
		$phone_number = $db->loadObject();
		if($phone_number->dial_code != ""){
			//$db->setQuery("Select dial_code from #__app_sch_dialing_codes where id = '$phone_number->dial_code'");
			//$dial_code = $db->loadResult();
			$phone_mobile = $phone_number->dial_code.$phone_number->order_phone;
		}
		else
		{
			$db->setQuery("Select dial_code from #__app_sch_dialing_codes where id = '".$configClass['clickatell_defaultdialingcode']."'");
			$dial_code = $db->loadResult();
			$phone_mobile = $dial_code.$phone_number->order_phone;
		}
		return $phone_mobile;
	}
	
	public static function getAdminMobileNumber(){
		global $configClass;
		$db = JFactory::getDbo();
		$phone_mobile = "";
		if($configClass['mobile_notification'] != "")
		{
			$db->setQuery("Select dial_code from #__app_sch_dialing_codes where id = '".$configClass['clickatell_defaultdialingcode']."'");
			$dial_code = $db->loadResult();
			$phone_mobile = $dial_code.$configClass['mobile_notification'];
		}
		return $phone_mobile;
	}
	
	
	static function checkEmployee(){
		$user = JFactory::getUser();
		if(intval($user->id) == 0){
			return false;
		}else{
			$db = JFactory::getDbo();
			$db->setQuery("Select count(id) from #__app_sch_employee where user_id = '$user->id'");
			$count = $db->loadResult();
			if($count > 0){
				return true;
			}else{
				return false;
			}
		}
	}
	
	static function getEmployeeID(){
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$db->setQuery("Select id from #__app_sch_employee where user_id = '$user->id'");
		return $db->loadResult();
	}
	
	static function getRealTime(){
		$config = new JConfig();
		$offset = $config->offset;
		date_default_timezone_set($offset);
		return strtotime(date('Y-m-d H:i:s'));
	}
	
	static function removeTempSlots(){
		global $mainframe;
		$unique_cookie = OSBHelper::getUniqueCookie();//$_COOKIE['unique_cookie'];
		$db = JFactory::getDbo();
		$db->setQuery("DELETE FROM #__app_sch_temp_temp_order_items WHERE unique_cookie LIKE '$unique_cookie'");
		$db->execute();
	}
	
	/**
	 * Check to see whether the ideal payment plugin installed and activated
	 * @return boolean
	 */
	static function idealEnabled() {
		/*
		$db = & JFactory::getDBO();
		$sql = 'SELECT COUNT(id) FROM #__app_sch_plugins WHERE name="os_ideal" AND published=1';
		$db->setQuery($sql) ;
		$total = $db->loadResult() ;
		if ($total) {
			require_once JPATH_ROOT.'/components/com_osservicesbooking/plugins/ideal/ideal.class.php';
			return true ;
		} else {
			return false ;
		}
		*/
	}
	/**
	 * Get list of banks for ideal payment plugin
	 * @return array
	 */
	public static function getBankLists() {
		$idealPlugin = os_payments::loadPaymentMethod('os_ideal');		
		$params = new JRegistry($idealPlugin->params) ;		
		$partnerId = $params->get('partner_id');
		$mode = $params->get('ideal_mode',0);
		$ideal = new iDEAL_Payment($partnerId,$mode) ;
		$bankLists = $ideal->getBanks();
		return $bankLists ;
	}
	
	/**
	 * Load Venue information
	 *
	 * @param unknown_type $sid
	 * @param unknown_type $eid
	 */
	static function loadVenueInformation($sid,$eid)
	{
		global $mainframe,$mapClass,$configClass;
		//JHTML::_('behavior.modal','a.osmodal');
		if(version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			JHtml::_('behavior.modal', 'a.osmodal');
		}
		else
		{
			OSBHelperJquery::colorbox('osmodal');
		}
		$db = JFactory::getDbo();
		$db->setQuery("Select a.* from #__app_sch_venues as a inner join #__app_sch_employee_service as b on a.id = b.vid where b.employee_id = '$eid' and b.service_id = '$sid'");
		$row = $db->loadObject();
		if($row->id > 0){
		?>
		<tr>
			<td width="100%">
				<div class="<?php echo $mapClass['row-fluid'];?>" style="font-size:90%;">
					<?php if($row->image != ""){ 
						$span2 = $mapClass['span8'];
					?>
						<div class="<?php echo $mapClass['span4'];?>">
							<img src="<?php echo JURI::root()?>images/osservicesbooking/venue/<?php echo $row->image?>" class="img-polaroid"/>
						</div>
					<?php } else { $span2 = $mapClass['span12']; } ?>
					<div class="<?php echo $span2;?>">
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php
								if($row->venue_name != ""){
									echo "<strong>".$row->venue_name."</strong>";
								}else{
									$addressArr = array();
									$addressArr[] = OSBHelper::getLanguageFieldValue($row,'address');
									if($row->city != ""){
										$addressArr[] = OSBHelper::getLanguageFieldValue($row,'city');
									}
									if($row->state != ""){
										$addressArr[] = OSBHelper::getLanguageFieldValue($row,'state');
									}
									if($row->country != ""){
										$addressArr[] = $row->country;
									}
								}
								if($row->lat_add != "" && $row->long_add != "")
								{
									?>
									<a href="<?php echo JURI::root()?>index.php?option=com_osservicesbooking&task=default_showmap&vid=<?php echo $row->id?>&tmpl=component" class="osmodal" rel="{handler: 'iframe', size: {x: 600, y: 400}}" title="<?php echo JText::_('OS_VENUE_MAP');?>">
										<img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/location24.png" />
									</a>
									<?php
								}
								?>
							</div>
						</div>
						<?php if($row->venue_name != ""){ ?>
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span12'];?>">
									<?php
									$addressArr = array();
									$addressArr[] = OSBHelper::getLanguageFieldValue($row,'address');
									if($row->city != ""){
										$addressArr[] = OSBHelper::getLanguageFieldValue($row,'city');
									}
									if($row->state != ""){
										$addressArr[] = OSBHelper::getLanguageFieldValue($row,'state');
									}
									if($row->country != ""){
										$addressArr[] = $row->country;
									}
									echo implode(", ",$addressArr);
									?>
								</div>
							</div>
						<?php } 
						if($row->contact_name != "") {
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php echo JText::_('OS_CONTACT_NAME')?>: <?php echo $row->contact_name;?>
							</div>
						</div>
						<?php } 
						if($row->contact_email != "") {
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php echo JText::_('OS_CONTACT_EMAIL')?>: <?php echo $row->contact_email;?>
							</div>
						</div>
						<?php }
						if($row->contact_phone != "") {
						?>
						<div class="<?php echo $mapClass['row-fluid'];?>">
							<div class="<?php echo $mapClass['span12'];?>">
								<?php echo JText::_('OS_CONTACT_PHONE')?>: <?php echo $row->contact_phone;?>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
				<input type="hidden" name="venue_available" id="venue_available" value="1" />
			</td>
		</tr>
		<?php
		}else{
			?>
			<input type="hidden" name="venue_available" id="venue_available" value="0" />
			<?php
		}
	}
	
	public static function returnAccessSql($prefix = ""){
		$user = JFactory::getUser();
		if($prefix != ""){
			$prefix .= ".";
		}
        $access_sql = " AND ".$prefix."`access` IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")";
		return $access_sql;
	}
	
	public static function checkSpecial(){
		global $mainframe;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$specialArr = array("Super Users","Super Administrator","Administrator","Manager");
		$db->setQuery("Select b.title from #__user_usergroup_map as a inner join #__usergroups as b on b.id = a.group_id where a.user_id = '$user->id'");
		$usertype = $db->loadResult();
		if(in_array($usertype,$specialArr)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Update AcyMailing
	 *
	 * @param unknown_type $orderId
	 */
	public static function updateAcyMailing($orderId){
		global $mainframe;
		$configClass = OSBHelper::loadConfig();
		$db = JFactory::getDbo();
		if($configClass['enable_acymailing'] == 1)
		{	
			if(file_exists(JPATH_ADMINISTRATOR.'/components/com_acym/helpers/helper.php') && file_exists(JPATH_ADMINISTRATOR . '/components/com_acym/acym.php'))
			{
				$db->setQuery("Select * from #__app_sch_orders where id = '$orderId'");
				$row = $db->loadObject();
				$db->setQuery("Select a.* from #__app_sch_services as a inner join #__app_sch_order_items as b on b.sid = a.id where b.order_id = '$orderId'");
				$services = $db->loadObjectList();
				foreach ($services as $service){
					
					$acymailing_list_id = $service->acymailing_list_id;
					if($acymailing_list_id == -1)
					{
						$add_to_acymailing = 0;
					}
					else
					{
						$add_to_acymailing = 1;
						if($acymailing_list_id == 0)
						{
							$acymailing_list_id = $configClass['acymailing_default_list_id'];
						}
					}
					
					if($add_to_acymailing == 1)
					{
						require_once JPATH_ADMINISTRATOR.'/components/com_acym/helpers/helper.php';
						$userClass               = acym_get('class.user');
						$userClass->checkVisitor = false;
						if (method_exists($userClass, 'getOneByEmail'))
						{
							$subId = $userClass->getOneByEmail($row->order_email);
						}
						else
						{
							$subId = $userClass->getUserIdByEmail($row->order_email);
						}
						//Check to see whether the current users has been added as subscriber or not
						if(!$subId)
						{
							$myUser					= new stdClass();				
							$myUser->email			= $row->order_email ;				
							$myUser->name			= $row->order_name ;
							$myUser->cms_id			= $row->user_id ;
							$subId = $userClass->save($myUser);
						}
						$subscribe					= array($acymailing_list_id);
						if (is_object($subId))
						{
							$subId = $subId->id;
						}
						//$userClass->subscribe($subId,$subscribe);
						try
						{
							$userClass->subscribe($subId, $listIds);
						}
						catch (Exception $e)
						{
							// Ignore error
						}
					}
				}
			}
			elseif(file_exists(JPATH_ADMINISTRATOR.'/components/com_acymailing/helpers/helper.php'))
			{
				$db->setQuery("Select * from #__app_sch_orders where id = '$orderId'");
				$row = $db->loadObject();
				$db->setQuery("Select a.* from #__app_sch_services as a inner join #__app_sch_order_items as b on b.sid = a.id where b.order_id = '$orderId'");
				$services = $db->loadObjectList();
				foreach ($services as $service){
					
					$acymailing_list_id = $service->acymailing_list_id;
					if($acymailing_list_id == -1){
						$add_to_acymailing = 0;
					}else{
						$add_to_acymailing = 1;
						if($acymailing_list_id == 0)
						{
							$acymailing_list_id = $configClass['acymailing_default_list_id'];
						}
					}
					
					if($add_to_acymailing == 1){
						require_once JPATH_ADMINISTRATOR.'/components/com_acymailing/helpers/helper.php';
						$userClass = acymailing_get('class.subscriber');
						//Check to see whether the current users has been added as subscriber or not
								
						$myUser = new stdClass();				
						$myUser->email = $row->order_email ;				
						$myUser->name = $row->order_name ;
						$myUser->userid = $row->user_id ;	 				
						$subscriberClass = acymailing_get('class.subscriber');				
						$subid = $subscriberClass->save($myUser); //this				
						$subscribe = array($acymailing_list_id);
						$userClass = acymailing_get('class.subscriber');
						$newSubscription = array();
						if(!empty($subscribe)){
							foreach($subscribe as $listId){
								$newList = array();
								$newList['status'] = 1;
								$newSubscription[$listId] = $newList;
							}
						}
						$userClass->saveSubscription($subid,$newSubscription);
					}
				}
			}
		}
	}
	
	static function getServiceInformation($service,$year,$month,$day,$employee_id = 0)
    {
		global $configClass;
		$db = JFactory::getDbo();
		$db->setQuery("Select count(a.id) from #__app_sch_employee_service as a inner join #__app_sch_employee as b on a.employee_id = b.id where a.service_id = '$service->id' and b.published = '1'");
		$nstaff = $db->loadResult();
		$nstaff = intval($nstaff);
		
		if($configClass['disable_payments'] == 1)
		{
		?>
			<span id="servicePrice">
				<span class="editlinktip hasTip" title="<?php echo JText::_('OS_PRICE')?>">
					<img src="<?php echo JURI::root(true)?>/media/com_osservicesbooking/assets/css/images/money.png" />
				</span>
				<strong>
				<?php
					echo OSBHelper::showMoney(OSBHelper::returnServicePriceShowing($service->id,$year."-".$month."-".$day,1,$employee_id),1);
				?>
				</strong>&nbsp;|&nbsp;
			</span>
		<?php 
		}
		?>
		<span id="serviceTime">
			<span class="editlinktip hasTip" title="<?php echo JText::_('OS_LENGTH')?>">
				<img src="<?php echo JURI::root(true)?>/media/com_osservicesbooking/assets/css/images/time.png" width="14" />
			</span>
			
			<strong><?php echo ($service->service_total < 60) ? $service->service_total." ".JText::_('OS_MINS'):OSBHelper::convertToHoursMins($service->service_total)." ".JText::_('OS_HOURS');?></font></strong>
			&nbsp;|&nbsp;
		</span>
		<span id="serviceStaff">
			<span class="editlinktip hasTip" title="<?php echo JText::_('OS_NUMBER_STAFF')?>">
				<img src="<?php echo JURI::root(true)?>/media/com_osservicesbooking/assets/css/images/staff.png" />
			</span>
			<strong><?php echo $nstaff;?></strong>
		</span>
		<?php
		if($configClass['early_bird'] == 1)
		{
			if(($service->early_bird_amount > 0) && ($service->early_bird_days > 0))
			{
				?>
				<span id="serviceEarlyBird">
					&nbsp;|&nbsp;
					<span class="editlinktip hasTip" title="<?php echo JText::_('OS_EARLY_BIRD_PRICE');?>">
						<img src="<?php echo JURI::root(true)?>/components/com_osservicesbooking/asset/images/early_bird.png" />
					</span>
					<strong>
					 <?php
					 echo Jtext::_('OS_DISCOUNT').' ';
					 echo OSBHelper::generateDecimal($service->early_bird_amount);
					 if($service->early_bird_type == 0){
						echo ' '.$configClass['currency_format'];
					 }else{
						echo "% ";
						echo JText::_('OS_OF_SERVICE_PRICE');
					 }
					 echo JText::sprintf('OS_EARLY_BIRD_BOOKING_INFORM', $service->early_bird_days);
					 ?>
					 </strong>
				 </span>
				 <?php 
			}
		} 
	}
	
	public static function loadEmployees($date,$sid,$employee_id,$vid)
    {
		global $configClass;
		$db = JFactory::getDbo();
		$tempdate = strtotime($date[2]."-".$date[1]."-".$date[0]);
		$day = strtolower(substr(date("D",$tempdate),0,2));
		$day1 = date("Y-m-d",$tempdate);
		if($vid > 0)
		{
			if(!OSBHelper::applyVenuFeature())
			{
				$vidSql = " and a.id IN (Select employee_id from #__app_sch_employee_service where service_id = '$sid' and vid = '$vid')";
			}
		}
		else
		{
			$vidSql = "";
		}
		if($employee_id > 0){
			$employeeSql = " and a.id = '$employee_id'";
		}else{
			$employeeSql = "";
		}
		$db->setQuery("Select a.* from #__app_sch_employee as a inner join #__app_sch_employee_service as b on a.id = b.employee_id where a.published = '1' and b.service_id = '$sid' and b.".$day." = '1' and a.id NOT IN (Select eid from #__app_sch_employee_rest_days where rest_date <= '$day1' and rest_date_to >= '$day1') $vidSql $employeeSql order by a.ordering");
		$employees = $db->loadObjectList();
		return $employees;
	}

    static function getCategoryName($sid){
		global $jinput;
        $db = JFactory::getDbo();
        $db->setQuery("Select category_id from #__app_sch_services where id = '$sid'");
        $category_id = $db->loadResult();
        $db->setQuery("Select category_name from #__app_sch_categories where id = '$category_id'");
        $category_name = $db->loadResult();
        return "<a href='".Jroute::_('index.php?option=com_osservicesbooking&task=default_layout&category_id='.$category_id.'&Itemid='.$jinput->getInt('Itemid',0))."' title='".JText::_('OS_CATEGORY_DETAILS')."'>".$category_name."</a>";
    }

    static function getServiceNames($eid){
		global $jinput;
        $db = JFactory::getDbo();
        $db->setQuery("Select a.* from #__app_sch_services as a inner join #__app_sch_employee_service as b on b.service_id = a.id where b.employee_id = '$eid' and a.published = '1' order by a.ordering");
        $rows = $db->loadObjectList();
        $tempArr = array();
        if(count($rows) > 0){
            foreach($rows as $row){
                $tempArr[] = "<a href='".Jroute::_('index.php?option=com_osservicesbooking&task=default_layout&sid='.$row->id.'&Itemid='.$jinput->getInt('Itemid',0))."' title='".JText::_('OS_SERVICE_DETAILS')."'>".OSBHelper::getLanguageFieldValue($row,'service_name')."</a>";
            }
        }
        return implode(", ",$tempArr);
    }

    static function showCommentForm($sid,$eid){
		global $configClass,$mapClass;
		$user = JFactory::getUser();
		if($configClass['active_comment'] == 1){
			$db = JFactory::getDbo();
			$db->setQuery("Select * from #__app_sch_reviews where sid = '$sid' and eid = '$eid' and published = '1' order by comment_date desc");
			$reviews = $db->loadObjectList();
			?>
			<div class="<?php echo $mapClass['row-fluid'];?>">
				<div class="<?php echo $mapClass['span12'];?>">
					<h3>
						<?php echo JText::_('OS_REVIEWS'); ?> (<?php echo count($reviews); ?>)
					</h3>
				</div>
			</div>
			<?php
			if(count($reviews) > 0){
				?>
				<div class="<?php echo $mapClass['row-fluid'];?> reviewlist">
					<div class="<?php echo $mapClass['span12'];?>">
						<?php
							foreach($reviews as $review){
								?>
								<div class="<?php echo $mapClass['row-fluid'];?>">
									<div class="<?php echo $mapClass['span12'];?>">
										<strong>
											<?php
											echo $review->comment_title;
											?>
										</strong>
										<?php
										if($review->rating > 0){
											for($i=1;$i<= $review->rating;$i++){
												?>
												<i class="icon-star" style="color:orange;"></i>
												<?php
											}
										}
										for($j=$review->rating + 1;$j<=5;$j++){
											?>
											<i class="icon-star" style="color:#CCC;"></i>
											<?php
										}
										?>
										&nbsp;
										<span id="commentdate">
										<?php
											echo $review->comment_date;
										?>
										</span>
										<div class="clearfix"></div>
										<?php
										echo $review->comment_content;
										?>
									</div>
								</div>
								<?php
							}
						?>
					</div>
				</div>
				<?php
			}
			if(self::canPostReview($sid,$eid)) {
				$db = JFactory::getDbo();
				$db->setQuery("Select service_name from #__app_sch_services where id = '$sid'");
				$service_name = $db->loadResult();
				$db->setQuery("Select employee_name from #__app_sch_employee where id = '$eid'");
				$employee_name = $db->loadResult();
				$service_name .= "/ ".$employee_name;
				?>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<div class="<?php echo $mapClass['span12'];?>">
						<strong>
							<a href="javascript:void(0);" onclick="javascript:openCommentForm('<?php echo JUri::root();?>','<?php echo $sid?>','<?php echo $eid?>');">
								<?php echo sprintf(JText::_('OS_LEAVE_YOUR_COMMENT_HERE'),$service_name);?>
							</a>
						</strong>
					</div>
				</div>
				<?php
			}
		}
	}

	static function canPostReview($sid,$eid){
		global $configClass;
		$user = JFactory::getUser();
		if(($configClass['active_comment'] == 1) and ($user->id > 0)){
			$db = JFactory::getDbo();
			$query = "Select count(a.id) from #__app_sch_order_items as a inner join #__app_sch_orders as b on b.id = a.order_id where b.user_id = '$user->id' and b.order_status = 'S' and a.sid = '$sid' and a.eid = '$eid'";
			$db->setQuery($query);
			$count1 = $db->loadResult();
			//check to see if user already post the review for this service/ employee
			$db->setQuery("Select count(id) from #__app_sch_reviews where user_id = '$user->id' and sid = '$sid' and eid = '$eid'");
			$count2 = $db->loadResult();
			if(($count1 > 0) and ($count2 == 0)){
				return true;
			}
		}
		return false;
	}

	static function alreadyPostComment($sid,$eid){
		global $configClass;
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		if(($configClass['active_comment'] == 1) and ($user->id > 0)){
			//check to see if user already post the review for this service/ employee
			$db->setQuery("Select count(id) from #__app_sch_reviews where user_id = '$user->id' and sid = '$sid' and eid = '$eid'");
			$count = $db->loadResult();
			if($count > 0){
				return true;
			}
		}
		return false;
	}

	static function userrating($sid,$eid){
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        $db->setQuery("Select rating from #__app_sch_reviews where user_id = '$user->id' and sid = '$sid' and eid = '$eid' and published = '1'");
        return $db->loadResult();
    }

	static function reviewForm($sid,$eid){
		global $configClass;
		$user = JFactory::getUser();
		if(self::canPostReview($sid,$eid)){
			$db = JFactory::getDbo();
			$db->setQuery("Select service_name from #__app_sch_services where id = '$sid'");
			$service_name = $db->loadResult();
			$db->setQuery("Select employee_name from #__app_sch_employee where id = '$eid'");
			$employee_name = $db->loadResult();
			$service_name .= "/ ".$employee_name;
			$optionArr = array();
			$optionArr[] = JHTML::_('select.option','1','1');
			$optionArr[] = JHTML::_('select.option','2','2');
			$optionArr[] = JHTML::_('select.option','3','3');
			$optionArr[] = JHTML::_('select.option','4','4');
			$optionArr[] = JHTML::_('select.option','5','5');
			?>
			<form method="POST" action="<?php echo JUri::root();?>index.php?option=com_osservicesbooking&task=submitComment&tmpl=component" name="commentForm" id="commentForm">
			<div class="<?php echo $mapClass['row-fluid'];?> bookingformdiv" style="margin-top:10px;">
				<div class="<?php echo $mapClass['span12'];?>">
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<strong><?php echo sprintf(JText::_('OS_LEAVE_YOUR_COMMENT_HERE'),$service_name);?></strong>
						</div>
					</div>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<div class="control-group">
								<label class="control-label">
									<span class="hasTip" title="<?php echo JText::_('OS_YOUR_NAME');?>">
										<?php echo JText::_('OS_YOUR_NAME');?>
									</span>
								</label>
								<div class="controls">
									<input type="text" name="name" id="name" value="<?php echo $user->name;?>" class="input-large" placeholder="<?php echo JText::_('OS_YOUR_NAME');?>" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label">
									<span class="hasTip" title="<?php echo JText::_('OS_RATING');?>">
										<?php echo JText::_('OS_RATING');?>
									</span>
								</label>
								<div class="controls">
									<?php
										echo JHtml::_('select.genericlist',$optionArr,'rating','class="chosen input-mini"','value','text');
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label">
							<span class="hasTip" title="<?php echo JText::_('OS_COMMENT_TITLE');?>">
								<?php echo JText::_('OS_COMMENT_TITLE');?>
							</span>
								</label>
								<div class="controls">
									<input type="text" name="comment_title" id="comment_title" class="input-large" placeholder="<?php echo JText::_('OS_COMMENT_TITLE');?>" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label">
							<span class="hasTip" title="<?php echo JText::_('OS_COMMENT');?>">
								<?php echo JText::_('OS_COMMENT');?>
							</span>
								</label>
								<div class="controls">
									<textarea name="comment_content" id="comment_content" style="width:300px !important;"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<input type="button" value="<?php echo JText::_('OS_SUBMIT');?>" class="btn btn-primary" onClick="javascript:submitCommentForm();" />
							<input type="reset" value="<?php echo JText::_('OS_RESET');?>" class="btn btn-warning" />
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="option" id="option" value="com_osservicesbooking" />
			<input type="hidden" name="task"   id="task" value="default_submitcomment" />
			<input type="hidden" name="sid"	   id="sid" value="<?php echo $sid;?>" />
			<input type="hidden" name="eid"	   id="eid" value="<?php echo $eid;?>" />
			</form>
			<script type="text/javascript">
				function submitCommentForm(){
					var form = document.commentForm;
					var name = form.name;
					var comment_title = form.comment_title;
					if(name.value == ""){
						alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_NAME');?>");
						name.focus();
						return false;
					}else if(comment_title.value == ""){
						alert("<?php echo JText::_('OS_PLEASE_ENTER_YOUR_COMMENT_TITLE');?>");
						comment_title.focus();
						return false;
					}else {
						form.submit();
					}
					return false;
				}
			</script>
			<?php
		}
	}

	static function getServicesAndEmployees($services,$year,$month,$day,$category_id,$employee_id,$vid,$sid,$eid){
	    $return = "";
        $date = $year."-".$month."-".$day;
        $dateArr[0] = $day;
        $dateArr[1] = $month;
        $dateArr[2] = $year;
	    $serviceArr = array();
        foreach ($services as $service){
            $temp = "";
             $temp .= $service->id."&".OSBHelper::getLanguageFieldValue($service,'service_name');
            //get employee of services
            $employees = self::loadEmployees($dateArr,$service->id,$employee_id,$vid);
            $employeeArr = array();
            foreach($employees as $employee){
                $employeeArr[] = "pane".$service->id."_".$employee->id."%".$employee->employee_name;
            }
            $employee = implode("*",$employeeArr);
            $temp .= "-".$employee;
            $serviceArr[] = $temp;
        }
        return implode("|",$serviceArr);
    }


	public static function replaceCustomFields(& $body, $order_id)
	{
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1' order by ordering");
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
						$body = str_replace("{".$field->field_label."}",$fvalue, $body);
					}
					else
					{
						$body = str_replace("{".$field->field_label."}","", $body);
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
							$body = str_replace("{".$field->field_label."}",'<img src="'.JUri::root().'/images/osservicesbooking/fields/'.$fvalue.'" width="120" />', $body);
						}
					}
				}
				elseif($field->field_type == 4)
				{
					$db->setQuery("Select fvalue from #__app_sch_field_data where order_id = '$order_id' and fid = '$field->id'");
					$fvalue = $db->loadResult();
					if($fvalue != "")
					{
						if(file_exists(JPATH_ROOT.'/images/osservicesbooking/fields/'.$fvalue))
						{
							$body = str_replace("{".$field->field_label."}",'<a href="'.JUri::root().'/images/osservicesbooking/fields/'.$fvalue.'" target="_blank"/>'.$fvalue.'</a>', $body);
						}
					}
				}
				//if($count > 0)
				//{
					if($field->field_type == 1)
					{
						$db->setQuery("Select option_id from #__app_sch_order_options where order_id = '$order_id' and field_id = '$field->id'");
						$option_id = $db->loadResult();
						$db->setQuery("Select * from #__app_sch_field_options where id = '$option_id'");
						$optionvalue = $db->loadObject();
						
						$field_data = OSBHelper::getLanguageFieldValueOrder($optionvalue,'field_option',$order_lang); //$optionvalue->field_option;
						//if($optionvalue->additional_price > 0){
						if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0))
						{
							$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
						}
						//echo "{".$field->field_label."}";die();
						if($field_data != "")
						{
							$body = str_replace("{".$field->field_label."}",$field_data, $body);
						}
						else
						{
							$body = str_replace("{".$field->field_label."}","", $body);
						}
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
							if(($optionvalue->additional_price > 0) || ($optionvalue->additional_price < 0)){
								$field_data.= " - (".OSBHelper::showMoney($optionvalue->additional_price,0).")";
							}
							$fieldArr[] = $field_data;
						}
						if(count($fieldArr) > 0)
						{
							$body = str_replace("{".$field->field_label."}",implode(", ",$fieldArr), $body);
						}
						else
						{
							$body = str_replace("{".$field->field_label."}","", $body);
						}
					}
				//}
			}
		}
	}

	public static function alreadyHavingTimeslot()
	{
		global $configClass;
		$unique_cookie 			= OSBHelper::getUniqueCookie();
		$db						= JFactory::getDbo();
		if($configClass['limit_one_timeslot'] == 1)
		{
			$db->setQuery("Select id from #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
			$temp_order_id		= $db->loadResult();
			if((int)$temp_order_id > 0)
			{
				$db->setQuery("Select count(id) from #__app_sch_temp_order_items where order_id = '$temp_order_id'");
				$count = $db->loadResult();
				if($count > 0)
				{
					return true;
				}
			}
		}
		return false;
	}

	public static function buildReplaceTags($orderId)
    {
        $replaces    = array();
        $db = JFactory::getDbo();
        $db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1'");
        $fields = $db->loadObjectList();
        if(count($fields))
        {
            foreach($fields as $field)
            {
                $replaces['{FIELD_'.$field->id.'}'] = OsAppscheduleDefault::orderFieldData($field, $orderId);
            }
        }
        return $replaces;
    }


}

?>