<?php
/**
 * Part of the Ossolution Payment Package
 *
 * @copyright  Copyright (C) 2016 - 2016 Ossolution Team. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

/**
 * Abstract Payment Class
 *
 * @since  1.0
 */
trait MPFPaymentCommon
{
	/**
	 *  This method is called when payment for the registration is success, it needs to be used by all payment class
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   string                       $transactionId
	 */
	protected function onPaymentSuccess($row, $transactionId)
	{
		$config              = OSMembershipHelper::getConfig();
		$row->transaction_id = $transactionId;
		$row->payment_date   = gmdate('Y-m-d H:i:s');
		$row->published      = 1;
		$row->store();

		if ($row->act == 'upgrade')
		{
			OSMembershipHelper::callOverridableHelperMethod('Subscription', 'processUpgradeMembership', [$row]);
		}

		if (OSMembershipHelperSubscription::needToTriggerActiveEvent($row))
		{
			PluginHelper::importPlugin('osmembership');
			Factory::getApplication()->triggerEvent('onMembershipActive', [$row]);
		}
		else
		{
			$row->active_event_triggered = 0;
			$row->store();
		}

		if ($row->process_payment_for_subscription)
		{
			$row->payment_method = $this->name;
			$row->store();
			OSMembershipHelperMail::sendSubscriptionPaymentEmail($row, $config);
		}
		else
		{
			OSMembershipHelperMail::sendEmails($row, $config);
		}
	}

	/**
	 * Method to check if payment plugin support cancel recurring subscription
	 *
	 * @return bool
	 */
	public function supportCancelRecurringSubscription()
	{
		return method_exists($this, 'cancelSubscription');
	}

	/**
	 * Method to check if payment plugin support refund payment
	 *
	 * @return bool
	 */
	public function supportRefundPayment()
	{
		return method_exists($this, 'refund');
	}

	/**
	 * Method to check whether we need to show card type on form for this payment method. From now on, we don't have to
	 * show card type on form because it can be detected from card number. Keep it here for B/C reason only
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
	 * Get SEF return URL after processing payment
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   int                          $Itemid
	 * @param   bool                         $absolute
	 *
	 * @return string
	 */
	protected function getPaymentCompleteUrl($row, $Itemid, $absolute = false)
	{
		$langLink = OSMembershipHelper::getLangLink();

		if ($row->process_payment_for_subscription)
		{
			$Itemid = OSMembershipHelperRoute::getViewRoute('payment', $Itemid);

			$url = 'index.php?option=com_osmembership&view=payment&layout=complete&subscription_code=' . $row->subscription_code . '&Itemid=' . $Itemid . $langLink;
		}
		else
		{
			$url = OSMembershipHelperRoute::getViewRoute('complete', $Itemid) . '&subscription_code=' . $row->subscription_code . $langLink;
		}

		return Route::_($url, false, 0, $absolute);
	}

	/**
	 * Get payment failure URL
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   int                          $Itemid
	 * @param   bool                         $absolute
	 *
	 * @return string
	 */
	protected function getPaymentFailureUrl($row, $Itemid, $absolute = false)
	{
		$url = 'index.php?option=com_osmembership&view=failure&Itemid=' . $Itemid;

		return Route::_($url, false, 0, $absolute);
	}

	/**
	 * Get payment failure URL
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   int                          $Itemid
	 * @param   bool                         $absolute
	 *
	 * @return string
	 */
	protected function getPaymentCancelUrl($row, $Itemid, $absolute = false)
	{
		$url = 'index.php?option=com_osmembership&view=cancel&layout=default&id=' . $row->id . '&Itemid=' . $Itemid;

		return Route::_($url, false, 0, $absolute);
	}

	/**
	 * Store payment error message into session to have it displayed on payment failure page
	 *
	 * @param   string  $error
	 *
	 * @return void
	 */
	protected function setPaymentErrorMessage($error)
	{
		Factory::getSession()->set('omnipay_payment_error_reason', $error);
	}
}
