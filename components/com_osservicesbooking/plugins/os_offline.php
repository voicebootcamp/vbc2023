<?php
/**
 * @version		1.1.1
 * @package		Joomla
 * @subpackage	OS Services Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die ;

class os_offline extends OSBPayment {
	/**
	 * Order Status
	 *
	 * @var unknown_type
	 */
	var $order_status = null;
	/**
	 * Constructor functions, init some parameter
	 *
	 * @param object $params
	 */
	public function __construct($params, $config = [])
	{
		parent::__construct($params, $config);
		$this->order_status = $params->get('order_status');
	}
	/**
	 * Process payment 
	 *
	 */
	function processPayment($row, $data) 
	{
		$mainframe = & JFactory::getApplication() ;
		$input	   = $mainframe->input;
		$Itemid	   = $input->getInt('Itemid');
		if($this->order_status == 0)
		{
			//do nothing		
		}
		else
		{
			$configClass              = OSBHelper::loadConfig();
			//$this->onPaymentSuccess($row, $transactionId);
			if($configClass['value_enum_email_confirmation'] == 2 && $row->send_email == 0)
			{
				$orderId			  = $row->id;
				HelperOSappscheduleCommon::sendEmail('confirm',$orderId);
				HelperOSappscheduleCommon::sendEmail('admin',$orderId);
				HelperOSappscheduleCommon::sendEmployeeEmail('employee_notification_new',$orderId,0);
				HelperOSappscheduleCommon::sendSMS('confirm',$orderId);
				HelperOSappscheduleCommon::sendSMS('admin',$orderId);
				HelperOSappscheduleCommon::updateAcyMailing($orderId);
			}
			$row->transaction_id = $transactionId;
			$row->order_status   = "S";
			$row->deposit_paid   = 1;
			if(!$row->store())
			{
				throw new Exception ($row->getError());
			}
			OsAppscheduleDefault::paymentComplete($row->id);
			
		}
		$url = JRoute::_(JURI::root()."index.php?option=com_osservicesbooking&task=default_paymentreturn&id=".$row->id."&Itemid=".$Itemid, false, false);		
		$mainframe->redirect($url);				    
	}		
}