<?php
/**
 * Part of the Ossolution Payment Package
 *
 * @copyright  Copyright (C) 2016 - 2016 Ossolution Team. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

//require_once JPATH_LIBRARIES . '/omnipay/vendor/autoload.php';

require_once JPATH_LIBRARIES . '/omnipay/vendor/autoload.php';
if (file_exists(JPATH_LIBRARIES . '/omnipay3/vendor/autoload.php'))
{
	require_once JPATH_LIBRARIES . '/omnipay3/vendor/autoload.php';
}
else
{
	require_once JPATH_LIBRARIES . '/omnipay/vendor/autoload.php';
}

use Ossolution\Payment\OmnipayPayment;

/**
 * Payment class which use Omnipay payment class for processing payment
 *
 * @since 1.0
 */
class OSBPaymentOmnipay extends OmnipayPayment
{
	/**
	 * Flag to determine whether this payment method has payment processing fee
	 *
	 * @var bool
	 */
	public $paymentFee;

	/**
	 * Method to check whether we need to show card type on form for this payment method.
	 * Always return false as when use Omnipay, we don't need card type parameter. It can be detected automatically
	 * from given card number
	 *
	 * @return bool|int
	 */
	public function getCardType()
	{
		return 0;
	}

	/**
	 * Method to check whether we need to show card holder name in the form
	 *
	 * @return bool|int
	 */
	public function getCardHolderName()
	{
		return $this->type;
	}

	/**
	 * Method to check whether we need to show card cvv input on form
	 *
	 * @return bool|int
	 */
	public function getCardCvv()
	{
		return $this->type;
	}

	/**
	 * This method need to be implemented by the payment plugin class. It needs to set url which users will be
	 * redirected to after a successful payment. The url is stored in paymentSuccessUrl property
	 *
	 * @param JTable $row
	 * @param array  $data
	 *
	 * @return void
	 */
	protected function setPaymentSuccessUrl($id, $data = array())
	{
		$Itemid = JFactory::getApplication()->input->get->getInt('Itemid',0);
		$siteUrl = JUri::base();
		$extraUrlVariables = "";
		if(isset($data) && $data['isRemain'] == 1)
		{
			$extraUrlVariables = "&remainPayment=1";
		}
		else
		{
			$remainPayment = JFactory::getApplication()->input->getInt('remainPayment', 0);
			if($remainPayment == 1)
			{
				$extraUrlVariables = "&remainPayment=1";
			}
		}
		$this->paymentSuccessUrl = JRoute::_('index.php?option=com_osservicesbooking&task=default_paymentreturn'.$extraUrlVariables.'&id='.$id.'&Itemid=' . $Itemid, false, false);
	}

	/**
	 * This method need to be implemented by the payment plugin class. It needs to set url which users will be
	 * redirected to when the payment is not success for some reasons. The url is stored in paymentFailureUrl property
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return void
	 */
	protected function setPaymentFailureUrl($id, $data = array())
	{
		if (empty($id))
		{
			$id = JFactory::getApplication()->input->getInt('id', 0);
		}
		$Itemid = JFactory::getApplication()->input->get->getInt('Itemid', 0 );
		$extraUrlVariables = "";
		if(isset($data) && $data['isRemain'] == 1)
		{
			$extraUrlVariables = "&remainPayment=1";
		}
		else
		{
			$remainPayment = JFactory::getApplication()->input->getInt('remainPayment', 0);
			if($remainPayment == 1)
			{
				$extraUrlVariables = "&remainPayment=1";
			}
		}
		$siteUrl = JUri::root();
		$this->paymentFailureUrl = $siteUrl.'index.php?option=com_osservicesbooking&task=default_paymentfailure'.$extraUrlVariables.'&id=' . $id . '&Itemid=' . $Itemid;
	}

	/**
	 * This method need to be implemented by the payment plugin class. It is called when a payment success. Usually,
	 * this method will update status of the order to success, trigger onPaymentSuccess event and send notification emails
	 * to administrator(s) and customer
	 *
	 * @param JTable $row
	 * @param string $transactionId
	 *
	 * @return void
	 */
	protected function onPaymentSuccess($row, $transactionId = '')
	{
		$config              = OSBHelper::loadConfig();
		if(($row->order_status == "S" && $row->order_upfront > 0 && $row->deposit_paid == 1 && $row->order_upfront < $row->order_final_cost) || ($row->order_payment == 'os_offline'))
		{
			$this->onRemainPaymentSuccess($row, $transactionId ) ;
		}
		else
		{
			$row->transaction_id	= $transactionId;
			$row->order_status		= "S";
			$row->deposit_paid		= 1;
			$row->store();
			OsAppscheduleDefault::paymentComplete($row->id);
		}
	}

	/**
	 *  This method is called when payment for the registration is success, it needs to be used by all payment class
	 *
	 * @param JTable $row
	 * @param string $transactionId
	 */
	protected function onRemainPaymentSuccess($row, $transactionId = '')
	{
		$config								= OSBHelper::loadConfig();
		$row->make_remain_payment			= 1;
		$row->remain_payment_transaction_id = $transactionId;
		$row->remain_payment_date			= gmdate('Y-m-d H:i:s');
		if(!$row->store())
		{
			throw new Exception ($row->getError());
		}
		OsAppscheduleDefault::remainPaymentComplete($row->id);
	}

	/**
	 * This method need to be implemented by the payment gateway class. It needs to init the JTable order record,
	 * update it with transaction data and then call onPaymentSuccess method to complete the order.
	 *
	 * @param int    $id
	 * @param string $transactionId
	 *
	 * @return mixed
	 */
	protected function onVerifyPaymentSuccess($id, $transactionId)
	{
        require_once(JPATH_ROOT."/administrator/components/com_osservicesbooking/tables/order.php");
        $row = JTable::getInstance('Order', 'OsAppTable');
		$row->load($id);

		if (!$row->id)
		{
			return false;
		}

		$remainPayment = JFactory::getApplication()->input->getInt('remainPayment', 0 );
		if($remainPayment == 1)
		{
			$this->onRemainPaymentSuccess($row, $transactionId );
		}
		else
		{
			$this->onPaymentSuccess($row, $transactionId);
		}
	}

	/**
	 * This method is usually called by payment method class to add additional data
	 * to the request message before that message is actually sent to the payment gateway
	 *
	 * @param \Omnipay\Common\Message\AbstractRequest $request
	 * @param JTable                                  $row
	 * @param array                                   $data
	 */
	protected function beforeRequestSend($request, $row, $data)
	{
		parent::beforeRequestSend($request, $row, $data);

		// Set return, cancel and notify URL
		$Itemid  = JFactory::getApplication()->input->getInt('Itemid', 0);
		$siteUrl = JUri::root();

		$request->setCancelUrl(OSBHelper::generatePaymentCancelUrl($row->id,  $data['isRemain'], $Itemid));
		$request->setReturnUrl(OSBHelper::generatePaymentReturnUrl($row->id,  $data['isRemain'], $Itemid));
		$request->setNotifyUrl(OSBHelper::generateNotifyUrl($row->id	   ,  $data['isRemain'], $this->name));
		$request->setAmount($data['amount']);
		$request->setCurrency($data['currency']);
		$request->setDescription($data['item_name']);

		if (empty($this->redirectHeading))
		{
			$language    = JFactory::getLanguage();
			$languageKey = 'OS_WAIT_' . strtoupper(substr($this->name, 3));
			if ($language->hasKey($languageKey))
			{
				$redirectHeading = JText::_($languageKey);
			}
			else
			{
				$redirectHeading = JText::sprintf('OS_REDIRECT_HEADING', $this->getTitle());
			}

			$this->setRedirectHeading($redirectHeading);
		}
	}
}
