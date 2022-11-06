<?php
/**
 * @version		1.1.1
 * @package		Joomla
 * @subpackage	OS Services Booking
 * @author      Tuan Pham Ngoc
 * @copyright	Copyright (C) 2019 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die ;

class os_payments {	
	/**
	 * Get list of payment methods
	 *
	 * @return array
	 */
	static function getPaymentMethods($loadOffline = true, $onlyRecurring = false, $sid = 0)
    {
		static $methods ;
        $db = & JFactory::getDBO() ;
		$jinput = JFactory::getApplication()->input;
        $extraSql = "";
		if($sid > 0)
        {
            $db->setQuery("Select payment_plugins from #__app_sch_services where id = '$sid'");
            $payment_plugins = $db->loadResult();
			if(substr($payment_plugins,0,1) == ",")
			{
				$payment_plugins = substr($payment_plugins,1);
			}
            if($payment_plugins != "")
            {
                $extraSql = " and `id` in (".$payment_plugins.")";
            }
        }
        $user = JFactory::getUser();
		if (!$methods)
		{
            $methods = array();
			define('JPAYMENT_METHODS_PATH', JPATH_ROOT.'/components/com_osservicesbooking/plugins/') ;
			if ($loadOffline)
			{
				$sql = 'SELECT * FROM #__app_sch_plugins WHERE published=1 '.$extraSql.' '.HelperOSappscheduleCommon::returnAccessSql('') ;
			}
			else
			{
				$sql = 'SELECT * FROM #__app_sch_plugins WHERE published=1 '.$extraSql.' AND `name` != "os_offline" '.HelperOSappscheduleCommon::returnAccessSql('') ;
			}			

			$sql .= " ORDER BY ordering " ;
			$db->setQuery($sql) ;
			$rows = $db->loadObjectList();					
			foreach ($rows as $row) 
			{
                //using Prepaid version
                if($row->name == "os_prepaid")
				{
                    if (file_exists(JPAYMENT_METHODS_PATH.$row->name.'.php')) 
					{
                        $params = new JRegistry($row->params);
						$enable_non_logged = $params->get('enable_non_logged',0);
						if($enable_non_logged == 1 && $user->id == 0)
						{
							require_once JPAYMENT_METHODS_PATH.$row->name.'.php';
							$method = new $row->name(new JRegistry($row->params));

							$link   = JRoute::_('index.php?option=com_users&view=login');
							$method->title = $row->title." ".sprintf(JText::_('OS_YOU_NEED_TO_LOGIN_BEFORE_YOU_CAN_USE_THIS_PAYMENT_OPTON'), $link);
							$methods[] = $method;
						}
						elseif($user->id > 0)
						{
							$db->setQuery("Select count(id) from #__app_sch_user_balance where user_id = '$user->id'");
							$count = $db->loadResult();
							if($count > 0)
							{
								$db->setQuery("Select * from #__app_sch_user_balance where user_id = '$user->id'");
								$balance = $db->loadObject();
								$user_balance = "(".JText::_('OS_AVAILABLE_BALANCE').": ".OSBHelper::showMoney($balance->amount,1).")";
								require_once JPAYMENT_METHODS_PATH.$row->name.'.php';
								$method = new $row->name(new JRegistry($row->params));
								$method->title = $row->title." ".$user_balance;
								$methods[] = $method;
							}
							else
							{
								$user_balance = "(".JText::_('OS_AVAILABLE_BALANCE').": 0)";
								require_once JPAYMENT_METHODS_PATH.$row->name.'.php';
								$method = new $row->name(new JRegistry($row->params));
								$method->title = $row->title." ".$user_balance;
								$methods[] = $method;
							}
						}
                    }
                }
				else 
				{
                    if (file_exists(JPAYMENT_METHODS_PATH . $row->name . '.php')) 
					{
                        require_once JPAYMENT_METHODS_PATH . $row->name . '.php';
                        $method = new $row->name(new JRegistry($row->params));
                        $method->title = $row->title;
                        $methods[] = $method;
                    }
                }
			}
		}
		return $methods ;
	}
	/**
	 * Write the javascript objects to show the page
	 *
	 * @return string
	 */		
	static function writeJavascriptObjects()
    {
		$methods =  os_payments::getPaymentMethods();
		$jsString = " methods = new PaymentMethods();\n" ;			
		if (count($methods))
		{
			foreach ($methods as $method)
			{
				$jsString .= " method = new PaymentMethod('".$method->getName()."',".$method->getCreditCard().",".$method->getCardType().",".$method->getCardCvv().",".$method->getCardHolderName().");\n" ;
				$jsString .= " methods.Add(method);\n";								
			}
		}
		echo $jsString ;
	}

	/**
	 * Load information about the payment method
	 *
	 * @param string $name Name of the payment method
	 */
	static function loadPaymentMethod($name)
    {
        $db = &JFactory::getDBO();
        $user = JFactory::getUser();

        if ($name == "os_prepaid") 
		{
            $db->setQuery("Select count(id) from #__app_sch_user_balance where user_id = '$user->id'");
            $count = $db->loadResult();
            if ($count > 0) 
			{
                $db->setQuery("Select * from #__app_sch_user_balance where user_id = '$user->id'");
                $balance = $db->loadObject();
                $user_balance = "(" . JText::_('OS_AVAILABLE_BALANCE') . ": " . OSBHelper::showMoney($balance->amount, 1) . ")";
            }
            $sql = 'SELECT * FROM #__app_sch_plugins WHERE name="' . $name . '"';
            $db->setQuery($sql);
            $method = $db->loadObject();
            $method->title .= " ".$user_balance;
        } 
		else 
		{
            $sql = 'SELECT * FROM #__app_sch_plugins WHERE name="' . $name . '"';
            $db->setQuery($sql);
            $method = $db->loadObject();
        }
		return $method;
	}
	/**
	 * Get default payment gateway
	 *
	 * @return string
	 */
    static function getDefautPaymentMethod($sid = 0)
    {
        $db = & JFactory::getDBO() ;
        $extraSql = "";
        if($sid > 0)
        {
            $db->setQuery("Select payment_plugins from #__app_sch_services where id = '$sid'");
            $payment_plugins = $db->loadResult();
			if(substr($payment_plugins,0,1) == ",")
			{
				$payment_plugins = substr($payment_plugins,1);
			}
            if($payment_plugins != "")
            {
                $extraSql = " and `id` in (".$payment_plugins.")";
            }
        }

        $sql = 'SELECT name FROM #__app_sch_plugins WHERE published=1 '.$extraSql.' '.HelperOSappscheduleCommon::returnAccessSql('').' ORDER BY `ordering` LIMIT 1';
		//echo $sql;die();
        $db->setQuery($sql) ;
        return $db->loadResult();
    }
	/**
	 * Get the payment method object based on it's name
	 *
	 * @param string $name
	 * @return object
	 */		
	static function getPaymentMethod($name) 
	{
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
		$methods = os_payments::getPaymentMethods() ;
		foreach ($methods as $method) 
		{
			if ($method->getName() == $name) 
			{
                if($method->getName() == "os_prepaid")
				{
                    $db->setQuery("Select count(id) from #__app_sch_user_balance where user_id = '$user->id'");
                    $count = $db->loadResult();
                    if($count > 0)
					{
                        $db->setQuery("Select * from #__app_sch_user_balance where user_id = '$user->id'");
                        $balance = $db->loadObject();
                        $user_balance = "(".JText::_('OS_AVAILABLE_BALANCE').": ".OSBHelper::showMoney($balance->amount,1).")";
                        $method->title .= " ".$user_balance;
                    }
                }
				return $method ;		
			}
		}
		return null ;
	}
}
?>