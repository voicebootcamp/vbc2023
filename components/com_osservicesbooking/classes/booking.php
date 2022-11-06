<?php
/*------------------------------------------------------------------------
# booking.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author        Ossolution team
# copyright     Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites      https://www.joomdonation.com
# Technical     Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
class OsAppscheduleForm{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();

		OSBHelper::banned();

        $cid       = $jinput->get('cid',array(),'ARRAY');
        $cid       = \Joomla\Utilities\ArrayHelper::toInteger($cid);
		switch ($task)
        {
			case "form_step1":
				OsAppscheduleForm::checkout();
			break;
			case "form_step2":
 				OsAppscheduleForm::confirm();
			break;
			case "form_register":
				OsAppscheduleForm::registerUser();
			break;
			case "form_remainpayment":
				OsAppscheduleForm::remainpayment();
			break;
		}
	}
	
	/**
	 * Checkout 
	 * Step1 
	 *
	 */
	static function checkout()
	{
		global $mainframe,$configClass,$jinput, $mapClass;
		$session                = JFactory::getSession();
		$selected_date          = $session->get('selected_date');
		if($selected_date !="")
		{
		    $dateArr            = explode("-",$selected_date);
		    $year               = $dateArr[0];
		    $month              = $dateArr[1];
		    $day                = $dateArr[2];
        }
        $selected_datesArr = array();
        $menu = $mainframe->getMenu('site')->getActive();
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
		$employee_id 			= $jinput->getInt('employee_id',0);
		$category_id 			= $jinput->getInt('category_id',0);
		$vid		 			= $jinput->getInt('vid',0);
		$sid					= $jinput->getInt('sid',0);
		$date_from				= OSBHelper::getStringValue('date_from','');
		if($date_from != "")
		{
			$current_time = strtotime($date_from);
		}
		else
		{
			$current_time = HelperOSappscheduleCommon::getRealTime();
		}
		$lists['current_time']  = $current_time;
		$date_to				= OSBHelper::getStringValue('date_to','');
		$lists['employee_id'] 	= $employee_id;
		$lists['category'] 		= $category_id;
		$lists['vid'] 			= $vid;
		$lists['date_from'] 	= $date_from;
		$lists['date_to'] 		= $date_to;
		$lists['sid']			= $sid;
		$lists['year']          = $year;
		$lists['month']         = $month;
		$lists['day']           = $day;

		$services               = OsAppscheduleAjax::prepareLoadServicesObjects('com_osservicesbooking',$lists['year'],$lists['month'],$lists['day'],$lists['category'],$lists['vid'],$lists['employee_id'],$lists['sid'],$lists['employee_id']);

		$lists['services']      = $services;

		if(version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			require_once(JPATH_ROOT."/components/com_content/helpers/route.php");
		}

		$document  = JFactory::getDocument();
		$document->setTitle($configClass['business_name']." - ".JText::_('OS_CHECKOUT'));
		//$unique_cookie = $_COOKIE['unique_cookie'];
		$unique_cookie = $jinput->get('unique_cookie','','string');
		if($unique_cookie == "")
		{
			$unique_cookie = $_COOKIE['unique_cookie'];
		}
		setcookie('unique_cookie',$unique_cookie,time() + 3600);
		//get information from profile table
		$db					= JFactory::getDbo();
		$user				= JFactory::getUser();
		$userId				= $user->id;
		//get profile
		if($user->id > 0)
		{	
			//check limit booking 
			if((int)$configClass['limit_booking'] > 0)
			{
				$limit_by   = $configClass['limit_by'];
				if(!OSBHelper::checkLimitBooking($user->email, $limit_by, $configClass['limit_booking'], $db))
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
					OsAppscheduleDefault::redirecterrorform();
				}
			}
			
			$profile = new stdClass();
			$userProfilePluginEnabled = JPluginHelper::isEnabled('user', 'profile');
			if($configClass['field_integration'] == 1 && (int) $userId > 0 && $userProfilePluginEnabled)
			{
				$coreFields = array('address','city','country','zip','phone','state');
				foreach($coreFields as $cfield)
				{
					$mappingField = $configClass[$cfield.'_mapping'];
					if($mappingField != "")
					{
						$query = $db->getQuery(true);
						$query->select('profile_value')
							->from('#__user_profiles')
							->where('user_id=' . $userId)
							->where('profile_key = "profile.'.$mappingField.'"');
						$db->setQuery($query);
						$profile->{'order_'.$cfield} = json_decode($db->loadResult(), true);
					}
				}
			}
			elseif($configClass['field_integration'] == 2 && (int) $userId > 0)
			{
				$coreFields = array('address','city','country','zip','phone','state');
				foreach($coreFields as $cfield)
				{
					$mappingField = $configClass[$cfield.'_mapping'];
					if($mappingField != "")
					{
						$query = $db->getQuery(true);
						$query->select($mappingField)
							->from('#__jsn_users')
							->where('id=' . $userId);
						$db->setQuery($query);
						$profile->{'order_'.$cfield} = $db->loadResult();
					}
				}
			}
			if($profile->order_country == "")
			{
				$profile->order_country = $configClass['default_country'];
				$order_country			= $configClass['default_country'];
			}
			else
			{
				$order_country			= $profile->order_country;
			}
		}
		$countryArr[] = JHTML::_('select.option','','');
		$db->setQuery("Select country_name as value, country_name as text from #__app_sch_countries order by country_name");
		$countries = $db->loadObjectList();
		$countryArr = array_merge($countryArr,$countries);
		
		$lists['country'] = JHTML::_('select.genericlist',$countryArr,'order_country','class="'.$mapClass['input-large'].' form-select"','value','text',$order_country);

        $unique_cookie  = OSBHelper::getUniqueCookie();
        if($unique_cookie != "")
        {
            $db->setQuery("SELECT count(id) FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
            $count_order = $db->loadResult();
            if ($count_order > 0)
            {
                $db->setQuery("SELECT id FROM #__app_sch_temp_orders WHERE unique_cookie like '$unique_cookie'");
                $order_id = $db->loadResult();
                $db->setQuery("SELECT distinct sid FROM #__app_sch_temp_order_items WHERE order_id = '$order_id'");
                $count_services = $db->loadObjectList();
                if(count($count_services) == 1)
                {
                    $loadPaymentOfService = true;
                    $service = $count_services[0]->sid;
                }
                else
                {
                    $loadPaymentOfService = false;
                    $service = 0;
                }
            }
        }

		$db->setQuery("Select * from #__app_sch_fields where field_area = '1' and published = '1' and show_at_frontend = '1' order by ordering");
		$fields = $db->loadObjectList();
		if($configClass['disable_payments']  == 1)
		{
			$paymentMethod = $jinput->get('payment_method', os_payments::getDefautPaymentMethod($service), 'string');
			if (!$paymentMethod)
			    $paymentMethod = os_payments::getDefautPaymentMethod();
			
			###############Payment Methods parameters###############################
		
			//Creditcard payment parameters		
			$x_card_num         = $jinput->get('x_card_num', '', 'string');
			$expMonth           = $jinput->get('exp_month', date('m'), 'string') ;
			$expYear            = $jinput->get('exp_year', date('Y'), 'string') ;
			$x_card_code        = $jinput->get('x_card_code', '', 'string');
			$cardHolderName     = $jinput->get('card_holder_name', '', 'string') ;
			$lists['exp_month'] = JHTML::_('select.integerlist', 1, 12, 1, 'exp_month', ' id="exp_month" class="input-mini form-select ishort"  ', $expMonth, '%02d') ;
			$currentYear = date('Y') ;
			$lists['exp_year'] = JHTML::_('select.integerlist', $currentYear, $currentYear + 10 , 1, 'exp_year', ' id="exp_year" class="input-mini form-select ishort" ', $expYear) ;
			$options			=  array() ;
			$options[]			= JHTML::_('select.option', 'Visa', JText::_('OS_VISA_CARD')) ;			
			$options[]			= JHTML::_('select.option', 'MasterCard', JText::_('OS_MASTER_CARD')) ;
			$options[]			= JHTML::_('select.option', 'Discover', JText::_('OS_DISCOVER')) ;
			$options[]			= JHTML::_('select.option', 'Amex', JText::_('OS_AMEX')) ;
					
			$lists['card_type'] = JHTML::_('select.genericlist', $options, 'card_type', ' class="'.$mapClass['input-medium'].' form-select" ', 'value', 'text') ;
			//Echeck
					
			$x_bank_aba_code    = $jinput->get('x_bank_aba_code', '', 'string') ;
			$x_bank_acct_num    = $jinput->get('x_bank_acct_num', '', 'string') ;
			$x_bank_name        = $jinput->get('x_bank_name', '', 'string') ;
			$x_bank_acct_name   = $jinput->get('x_bank_acct_name', '', 'string') ;
			$options = array() ;
			$options[] = JHTML::_('select.option', 'CHECKING', JText::_('OS_BANK_TYPE_CHECKING')) ;
			$options[] = JHTML::_('select.option', 'BUSINESSCHECKING', JText::_('OS_BANK_TYPE_BUSINESSCHECKING')) ;
			$options[] = JHTML::_('select.option', 'SAVINGS', JText::_('OS_BANK_TYPE_SAVING')) ;
			$lists['x_bank_acct_type'] = JHTML::_('select.genericlist', $options, 'x_bank_acct_type', ' class="inputbox" ', 'value', 'text', $jinput->get('x_bank_acct_type','','string')) ;
			
			$methods = os_payments::getPaymentMethods(true, false, $service) ;

			$lists['x_card_num']        = $x_card_num;
			$lists['x_card_code']       = $x_card_code;
			$lists['cardHolderName']    = $cardHolderName;
			$lists['x_bank_acct_num']   = $x_bank_acct_num;
			$lists['x_bank_acct_name']  = $x_bank_acct_name;
			$lists['methods']           = $methods;
			$lists['idealEnabled']      = 0;

			$lists['paymentMethod']     = $paymentMethod;
		}
		
		$dialArr[] 	                    = JHTML::_('select.option','',Jtext::_('OS_SELECT_DIAL_CODE'));
		$db->setQuery("SELECT id as value, concat(country,'-',dial_code) as text FROM #__app_sch_dialing_codes ORDER BY country" );
		$dial_rows                      = $db->loadObjectList();
		$dialArr	                    = array_merge($dialArr,$dial_rows);
		$lists['dial']                  = JHTML::_('select.genericlist',$dialArr,'dial_code','class="'.$mapClass['input-small'].' form-select" style="width:120px;"','value','text',$configClass['clickatell_defaultdialingcode']);
		$total                          = OsAppscheduleAjax::getOrderCost();
		$lists['total']                 = $total;
		HTML_OsAppscheduleForm::checkoutLayout($lists,$fields,$profile);
	}
	
	/**
	 * Checkout Step2 
	 * Confirmation user information
	 *
	 */
	static function confirm()
    {
		global $mainframe,$configClass,$jinput;
		$session				= Factory::getSession();
		$db						= Factory::getDbo();
		$user					= Factory::getUser();
		$order_email			= $jinput->getString('order_email','');

		if($user->id > 0 || $order_email != "")
		{	
			if($user->id > 0)
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
						
						$session->set('errorReason', $msg);
						OsAppscheduleDefault::redirecterrorform();
					}
				}
				else
				{
					$unique_cookie = OSBHelper::getUniqueCookie();
					$db->setQuery("Select id from #__app_sch_temp_orders where unique_cookie like '$unique_cookie'");
					$temp_order_id = $db->loadResult();
					if((int)$temp_order_id == 0)
					{
						//return JError::raise(E_WARNING, 404, JText::_('JERROR_ALERTNOAUTHOR'));
						//throw new Exception (JText::_('JERROR_ALERTNOAUTHOR'));

						$session->set('errorReason', JText::_('JERROR_ALERTNOAUTHOR'));
						OsAppscheduleDefault::redirecterrorform();
					}
					$db->setQuery("Select * from #__app_sch_temp_order_items where order_id = '$temp_order_id'");
					$orders = $db->loadObjectList();
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
									$tdate->setDate($tdate->year, $tdate->month, 1);
									$month = $tdate->format('M Y');
									$msg = sprintf(JText::_('OS_YOU_CANNOT_MAKE_BOOKING_MONTH_ANYMORE'), $month);
								break;
							}
							//throw new Exception($msg, 500);

							$session->set('errorReason', $msg);
							OsAppscheduleDefault::redirecterrorform();
						}
					}
				}
			}
		}
		$employee_id            = $jinput->getInt('employee_id',0);
		$category_id            = $jinput->getInt('category_id',0);
		$vid		            = $jinput->getInt('vid',0);
		$service_id	            = $jinput->getInt('service_id',0);
		$lists['employee_id'] 	= $employee_id;
		$lists['category'] 		= $category_id;
		$lists['vid'] 			= $vid;
		$date_from				= OSBHelper::getStringValue('date_from','');
		$date_to				= OSBHelper::getStringValue('date_to','');
		if($date_from == '0')
		{
		    $date_from = '';
		}
        if($date_to == '0')
        {
            $date_to = '';
        }
		$lists['date_from'] 	= $date_from;
		if($date_from != "")
		{
			$current_time       = strtotime($date_from);
		}
		else
		{
			$current_time       = HelperOSappscheduleCommon::getRealTime();
		}
		$lists['current_time']  = $current_time;
		$lists['date_to'] 		= $date_to;
        $selected_datesArr      = array();
        $menu = $mainframe->getMenu('site')->getActive();
        if (is_object($menu))
        {
            $params = $menu->getParams();
            $selected_dates     = $params->get('selected_dates','');
            if($selected_dates != "")
            {
                $selected_datesArr = explode(",", $selected_dates);
            }
        }

        $lists['selected_dates'] = $selected_datesArr;
        $passcaptcha            = 0;
        if($user->id > 0 && $configClass['pass_captcha'] == 1)
        {
            $passcaptcha        = 1;
        }
        //only check reCaptcha if recaptcha is enabled and by passcaptcha is no
		if($configClass['value_sch_include_captcha'] == 3 && $passcaptcha == 0)
		{
			$captchaPlugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
			$res           = JCaptcha::getInstance($captchaPlugin)->checkAnswer($jinput->post->get('recaptcha_response_field', '', 'string'));
			if (!$res)
			{
				$mainframe->enqueueMessage(JText::_('OS_CAPTCHA_IS_INVALID'));
			    $mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&task=form_step1&employee_id='.$employee_id.'&vid='.$vid.'&service_id='.$service_id.'&category_id='.$category_id));
			}
		}
		
		$document  = JFactory::getDocument();
		$document->setTitle($configClass['business_name']." - ".JText::_('OS_CONFIRM_INFORMATION'));
		$coupon_id = $jinput->getInt('coupon_id',0);
		$user = JFactory::getUser();
		if($coupon_id > 0)
		{
			$db->setQuery("Select * from #__app_sch_coupons where id = '$coupon_id'");
			$coupon			= $db->loadObject();
			$max_user_use	= $coupon->max_user_use;
			$max_total_use	= $coupon->max_total_use;
			if($max_total_use > 0)
			{
				$db->setQuery("Select count(id) from #__app_sch_coupon_used where coupon_id = '$coupon_id'");
				$nused = $db->loadResult();
				if($nused >= $max_total_use)
				{
					$coupon_id = 0;
				}
			}
			if(($max_user_use > 0) and ($coupon_id > 0))
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
		$coupon = array();
		if($coupon_id > 0)
		{
			$db->setQuery("Select * from #__app_sch_coupons where id = '$coupon_id'");
			$coupon = $db->loadObject();
		}
		
		$tax = $configClass['tax_payment'];
		$total = OsAppscheduleAjax::getOrderCostUsingTotalCostInTempOrderItem();
		$fieldObj = array();
		$fields = OSBHelper::getStringValue('fields','');
		$fieldArr = explode("||",$fields);
		if(count($fieldArr) > 0)
		{
			$field_amount = 0;
			for($i=0;$i<count($fieldArr);$i++)
			{
				$field_data = "";
				$field  = $fieldArr[$i];
				$fArr   = explode("|",$field);
				$fid    = $fArr[0];
				$fvalue = $fArr[1];
				$fvalue = str_replace("(@)","&",$fvalue);
				$db->setQuery("Select * from #__app_sch_fields where id = '$fid'");
				$field 	= $db->loadObject();
				$field_type = $field->field_type;
				if($field_type == 0)
				{
					$field_data = $fvalue;
				}
				elseif($field_type == 1)
                {
					$db->setQuery("Select * from #__app_sch_field_options where id = '$fvalue'");
					$fieldOption = $db->loadObject();
					//if($fieldOption->additional_price > 0){
						$field_amount += $fieldOption->additional_price;
					//}
					$field_data .= OSBHelper::getLanguageFieldValue($fieldOption,'field_option');
					if(($fieldOption->additional_price > 0) || ($fieldOption->additional_price < 0))
					{
						$field_data.= " - (".OSBHelper::showMoney($fieldOption->additional_price,0).")";
					}
				}
				elseif($field_type == 2)
                {
					$fieldValueArr = explode(",",$fvalue);
					if(count($fieldValueArr) > 0)
					{
						for($j=0;$j<count($fieldValueArr);$j++)
						{
							$temp = $fieldValueArr[$j];
							$db->setQuery("Select * from #__app_sch_field_options where id = '$temp'");
							$fieldOption = $db->loadObject();
							//if($fieldOption->additional_price > 0){
								$field_amount += $fieldOption->additional_price;
							//}
							$field_data .= OSBHelper::getLanguageFieldValue($fieldOption,'field_option');
							if(($fieldOption->additional_price > 0) || ($fieldOption->additional_price < 0))
							{
								$field_data.= " - (".OSBHelper::showMoney($fieldOption->additional_price,0).")";
							}
							$field_data .= ",";
						}
						$field_data = substr($field_data,0,strlen($field_data)-1);
					}
				}
				elseif($field_type == 3)
                {
					$photo_name                     = "field_".$fid;
					$fvalue                         = "";
					$field_data                     = "";
					if(is_uploaded_file($_FILES[$photo_name]['tmp_name']))
					{
						if(OSBHelper::checkIsPhotoFileUploaded($photo_name))
						{
							$image_name             = $_FILES[$photo_name]['name'];
							$image_name             = OSBHelper::processImageName($id.time().$image_name);
							$original_image_link    = JPATH_ROOT."/images/osservicesbooking/fields/".$image_name;
							move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
							$field_data             = "<img src='".JUri::root()."images/osservicesbooking/fields/".$image_name."' width='120'/>";
							$fvalue                 = $image_name;
						}
					}
				}
				elseif($field_type == 4)
                {
					$photo_name                     = "field_".$fid;
					$fvalue                         = "";
					$field_data                     = "";
					if(is_uploaded_file($_FILES[$photo_name]['tmp_name']))
					{
						if(OSBHelper::checkIsFileUploaded($photo_name))
						{
							$image_name             = $_FILES[$photo_name]['name'];
							$image_name             = OSBHelper::processImageName($id.time().$image_name);
							$original_image_link    = JPATH_ROOT."/images/osservicesbooking/fields/".$image_name;
							move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
							$field_data             = "<a href='".JUri::root()."images/osservicesbooking/fields/".$image_name."' target='_blanl'>".$image_name."</a>";
							$fvalue                 = $image_name;
						}
					}
				}
				
				$count	= count($fieldObj);
				$tmp		     = new \stdClass();

				$tmp->field            = $field;
				$tmp->fvalue           = $field_data;
				$tmp->fieldoptions     = $fvalue;
				$fieldObj[$count]					= $tmp;
			}
		}

		$total += $field_amount;
		if($configClass['disable_payments'] == 1)
		{
			$select_payment 	= OSBHelper::getStringValue('payment_method','');
			if($select_payment !=  "")
			{
				$method = os_payments::getPaymentMethod($select_payment) ;
				$x_card_num			= $jinput->get('x_card_num','','string');
				$x_card_code		= $jinput->get('x_card_code','','string');
				$card_holder_name	= $jinput->get('card_holder_name','','string');
				$exp_year			= $jinput->get('exp_year','','string');
				$exp_month			= $jinput->get('exp_month','','string');
				$card_type			= $jinput->get('card_type','','string');
				$lists['method'] 			= $method;
				$lists['x_card_num'] 		= $x_card_num;
				$lists['x_card_code'] 		= $x_card_code;
				$lists['card_holder_name'] 	= $card_holder_name;
				$lists['exp_year'] 			= $exp_year;
				$lists['exp_month'] 		= $exp_month;
				$lists['card_type'] 		= $card_type;
				$lists['select_payment']	= $select_payment;
				$lists['card_holder_name']  = $card_holder_name;
			}
		}
		
		//Saving profile
		$profile = JTable::getInstance('Profile','OsAppTable');
		$user = JFactory::getUser();
		if($user->id > 0)
		{
			/*
			$db->setQuery("Select count(id) from #__app_sch_userprofiles where user_id = '$user->id'");
			$count = $db->loadResult();
			if($count > 0)
			{
				$db->setQuery("Select id from #__app_sch_userprofiles where user_id = '$user->id'");
				$id = $db->loadResult();
				$profile->id = $id;
			}
			else
			{
				$profile->id = 0;
			}
			$profile->user_id 		= $user->id;
            $profile->order_name 	= $jinput->get('order_name','','string');
            $profile->order_email 	= $jinput->get('order_email','','string');
            $profile->order_phone 	= $jinput->get('order_phone','','string');
            $profile->order_country = $jinput->get('order_country','','string');
            $profile->order_address = $jinput->get('order_address','','string');
            $profile->order_state 	= $jinput->get('order_state','','string');
            $profile->order_city 	= $jinput->get('order_city','','string');
            $profile->order_zip 	= $jinput->get('order_zip','','string');
			$profile->store();
			
			//check and update into User profile table
			
			if($configClass['integrate_user_profile'] == 1)
			{
				$newprofile = new stdClass();
				$profileArr = array('address1','city','country','postal_code','phone');
				$profileArr1 = array('order_address','order_city','order_country','order_zip','order_phone');
				for($i=0;$i<count($profileArr);$i++)
				{
					$userprofile = $profileArr[$i];
					$db->setQuery("Select count(user_id) from #__user_profiles where user_id = '$user->id' and profile_key like 'profile.".$userprofile."'");
					$count = $db->loadResult();
					if($count > 0)
					{
						$db->setQuery("Update #__user_profiles set profile_value = '".$profile->{$profileArr1[$i]}."' where user_id = '$user->id' and profile_key like 'profile.".$userprofile."'");
						$db->execute();
					}
					else
					{
						$db->setQuery("Insert into #__user_profiles (user_id,profile_key,profile_value) values ('$user->id','profile.".$userprofile."','".$profile->{$profileArr1[$i]}."')");
						$db->execute();
					}
				}
			}
			*/
		}

		if($configClass['value_sch_reminder_enable'] == 1 && $configClass['enable_reminder'] == 1)
		{
			$lists['receive_reminder'] = $jinput->getInt('receive_reminder',0);
		}
		HTML_OsAppscheduleForm::confirmInforFormHTML($total,$fieldObj,$lists,$coupon);
	}
	
	/**
	 * Register User
	 *
	 */
	static function registerUser()
	{
		global $mainframe,$configClass,$jinput;
		$session = JFactory::getSession();
		if ($configClass['active_privacy'] && $configClass['privacy_policy_article_id'] > 0)
		{
			$session->set('pass_privacy',1);
		}
		$lang = & JFactory::getLanguage() ;
		$tag = $lang->getTag();
		if (!$tag)
			$tag = 'en-GB' ;
			
		$lang->load('com_users', JPATH_ROOT, $tag);
        $order_name 		= $jinput->get("order_name","","string");
        $order_email 		= $jinput->get("order_email","","string");
        $order_username 	= $jinput->get("username","","string");
        $order_password 	= $jinput->get("password1","","string");
		
		$data['name'] 		= $order_name;
		$data['password'] 	= $order_password;
		$data['email'] 		= $order_email ;
		$data['email1'] 	= $order_email ;
		$data['username']   = $order_username;
		$data['password2']  = $order_password;
		
		$user = new JUser  ;
		$params	= JComponentHelper::getParams('com_users');
		$data['groups'] = array() ;
		$data['groups'][]= $params->get('new_usertype', 2) ;
		$useractivation = $params->get('useractivation');
		$sendActivationEmail = $configClass['sendActivationEmail'];

		$data['block'] = 0;
		if (!$user->bind($data)) {
			//JError::raiseError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			//return false;
			$msg = JText::sprintf('OS_COM_USERS_REGISTRATION_BIND_FAILED', $user->getError());
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&task=form_step1&category_id='.$jinput->getInt('category_id',0)."&vid=".$jinput->getInt('vid',0)."&employee_id=".$jinput->getInt('employee_id',0)));
		}
		// Store the data.
		if (!$user->save()) {
			$msg = JText::sprintf('OS_COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError());
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect(JRoute::_('index.php?option=com_osservicesbooking&task=form_step1&category_id='.$jinput->getInt('category_id',0)."&vid=".$jinput->getInt('vid',0)."&employee_id=".$jinput->getInt('employee_id',0)));
		}
		//}								
		//process login
		if($configClass['use_ssl'] == 1){
			$returnUrl = JRoute::_($configClass['root_link'].'index.php?option=com_osservicesbooking&task=form_step1&category_id='.$jinput->getInt('category_id',0).'&employee_id='.$jinput->getInt('employee_id',0).'&vid='.$jinput->getInt('vid',0).'&Itemid='.$jinput->getInt('Itemid'));
		}else{
			$returnUrl = JRoute::_(JURI::root().'index.php?option=com_osservicesbooking&task=form_step1&category_id='.$jinput->getInt('category_id',0).'&employee_id='.$jinput->getInt('employee_id',0).'&vid='.$jinput->getInt('vid',0).'&Itemid='.$jinput->getInt('Itemid'));
		}
		
		$options = array();
		$options['remember'] = 1;
		$options['return'] = $returnUrl;

		$credentials = array();
		$credentials['username'] = $order_username;
		$credentials['password'] = $order_password;
		
		//preform the login action
		//$error = $mainframe->login($credentials, $options);
		//end login
		if (true === $mainframe->login($credentials, $options)) {
			// Success
			//$app->setUserState('users.login.form.data', array());
			//$app->redirect(JRoute::_($app->getUserState('users.login.form.return'), false));
			$mainframe->redirect($returnUrl);
		} else {
			// Login failed !
			$data['remember'] = (int) $options['remember'];
			$mainframe->setUserState('users.login.form.data', $data);
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
	}


	static function remainpayment()
	{
		global $configClass, $jinput, $mainframe;
		$id		= $jinput->getInt('id', 0);
		if(!OSBHelper::allowRemainPayment($id))
		{
			//throw new Exception (JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			$session = Factory::getSession();
			$session->set('errorReason', JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
			OsAppscheduleDefault::redirecterrorform();
		}
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('OS_MAKE_REMAIN_PAYMENT'). ' #'.$id);
		$pathway  = $mainframe->getPathway();
		$pathway->addItem(JText::_('OS_MAKE_REMAIN_PAYMENT'). ' #'.$id,JUri::root().'index.php?option=com_osservicesbooking&task=form_remainpayment&id='.$$id.'&Itemid='.$jinput->getInt('Itemid',0));
		$db		  = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_orders where id = '$id'");
		$order  = $db->loadObject();
		$lists['order'] = $order;

		if($order->order_payment == "os_offline")
		{
			$amount = $order->order_final_cost;
		}
		else
		{
			$amount = $order->order_final_cost - $order->order_upfront;
		}
		$lists['amount']	= $amount;
		$paymentMethod		= $jinput->get('payment_method', os_payments::getDefautPaymentMethod($service), 'string');
		if (!$paymentMethod)
			$paymentMethod = os_payments::getDefautPaymentMethod();
		
		###############Payment Methods parameters###############################
	
		//Creditcard payment parameters		
		$x_card_num         = $jinput->get('x_card_num', '', 'string');
		$expMonth           = $jinput->get('exp_month', date('m'), 'string') ;
		$expYear            = $jinput->get('exp_year', date('Y'), 'string') ;
		$x_card_code        = $jinput->get('x_card_code', '', 'string');
		$cardHolderName     = $jinput->get('card_holder_name', '', 'string') ;
		$lists['exp_month'] = JHTML::_('select.integerlist', 1, 12, 1, 'exp_month', ' id="exp_month" class="input-mini form-select ishort"  ', $expMonth, '%02d') ;
		$currentYear = date('Y') ;
		$lists['exp_year'] = JHTML::_('select.integerlist', $currentYear, $currentYear + 10 , 1, 'exp_year', ' id="exp_year" class="input-mini form-select ishort" ', $expYear) ;
		$options			=  array() ;
		$options[]			= JHTML::_('select.option', 'Visa', JText::_('OS_VISA_CARD')) ;			
		$options[]			= JHTML::_('select.option', 'MasterCard', JText::_('OS_MASTER_CARD')) ;
		$options[]			= JHTML::_('select.option', 'Discover', JText::_('OS_DISCOVER')) ;
		$options[]			= JHTML::_('select.option', 'Amex', JText::_('OS_AMEX')) ;
				
		$lists['card_type'] = JHTML::_('select.genericlist', $options, 'card_type', ' class="'.$mapClass['input-medium'].' form-select" ', 'value', 'text') ;
		//Echeck
				
		$x_bank_aba_code    = $jinput->get('x_bank_aba_code', '', 'string') ;
		$x_bank_acct_num    = $jinput->get('x_bank_acct_num', '', 'string') ;
		$x_bank_name        = $jinput->get('x_bank_name', '', 'string') ;
		$x_bank_acct_name   = $jinput->get('x_bank_acct_name', '', 'string') ;
		$options			= array() ;
		$options[]			= JHTML::_('select.option', 'CHECKING', JText::_('OS_BANK_TYPE_CHECKING')) ;
		$options[]			= JHTML::_('select.option', 'BUSINESSCHECKING', JText::_('OS_BANK_TYPE_BUSINESSCHECKING')) ;
		$options[]			= JHTML::_('select.option', 'SAVINGS', JText::_('OS_BANK_TYPE_SAVING')) ;
		$lists['x_bank_acct_type'] = JHTML::_('select.genericlist', $options, 'x_bank_acct_type', ' class="inputbox" ', 'value', 'text', $jinput->get('x_bank_acct_type','','string')) ;
		
		$methods = os_payments::getPaymentMethods(true, false, $service) ;

		$lists['x_card_num']        = $x_card_num;
		$lists['x_card_code']       = $x_card_code;
		$lists['cardHolderName']    = $cardHolderName;
		$lists['x_bank_acct_num']   = $x_bank_acct_num;
		$lists['x_bank_acct_name']  = $x_bank_acct_name;
		$lists['methods']           = $methods;
		$lists['idealEnabled']      = 0;

		$lists['paymentMethod']     = $paymentMethod;

		HTML_OsAppscheduleForm::remainPaymentForm($lists);
	}
}
?>