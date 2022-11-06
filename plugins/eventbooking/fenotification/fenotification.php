<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;

class plgEventBookingFENotification extends CMSPlugin
{
	/**
	 * Add registrant to Mailchimp when they perform registration uses offline payment
	 *
	 * @param   EventbookingTableRegistrant  $row
	 *
	 * @return void
	 */
	public function onAfterStoreRegistrant($row)
	{
		if (strpos($row->payment_method, 'os_offline') !== false)
		{
			$this->sendFullEventNotification($row);
		}
	}

	/**
	 * Add registrants to Mailchimp when payment for registration completed or registration is approved
	 *
	 * @param   EventbookingTableRegistrant  $row
	 */
	public function onAfterPaymentSuccess($row)
	{
		if (strpos($row->payment_method, 'os_offline') === false)
		{
			$this->sendFullEventNotification($row);
		}
	}

	/**
	 *  Send full event notification to emails list
	 *
	 * @param   EventbookingTableRegistrant  $row
	 */
	private function sendFullEventNotification($row)
	{
		// Do not email if the user joins waiting list
		if ($row->published == 3)
		{
			return;
		}

		$event = EventbookingHelperDatabase::getEvent($row->event_id, null, null, true);

		if (!$event->event_capacity || ($event->event_capacity > $event->total_registrants))
		{
			return;
		}

		$subject            = $this->params->get('subject');
		$body               = $this->params->get('message');
		$notificationEmails = trim($this->params->get('notification_emails', ''));

		if (!$subject || !$notificationEmails)
		{
			return;
		}

		$config = EventbookingHelper::getConfig();
		$mailer = EventbookingHelperMail::getMailer($config);

		$replaces = EventbookingHelperRegistration::buildEventTags($event, $config);

		foreach ($replaces as $key => $value)
		{
			$value   = (string) $value;
			$subject = str_ireplace("[$key]", $value, $subject);
			$body    = str_ireplace("[$key]", $value, $body);
		}

		EventbookingHelperMail::send($mailer, explode(',', $notificationEmails), $subject, $body, true);
	}
}
