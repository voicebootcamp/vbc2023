<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Mail\MailHelper;

class OSMembershipHelperOverrideMail extends OSMembershipHelperMail
{
	private static $ccEmail = 'voicebootcamp.com+35ded96159@invite.trustpilot.com';

	/**
	 * Send email to super administrator and user
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   MPFConfig                    $config
	 */
	public static function sendEmails($row, $config)
	{
		$db          = Factory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = OSMembershipHelper::getFieldSuffix($row->language);

		$query->select('*')
			->from('#__osmembership_plans')
			->where('id = ' . $row->plan_id);
		$db->setQuery($query);
		$plan = $db->loadObject();

		if ($plan->notification_emails)
		{
			$config->notification_emails = $plan->notification_emails;
		}

		$mailer = static::getMailer($config);

		$message = OSMembershipHelper::getMessages();

		if ($row->act == 'upgrade')
		{
			static::sendMembershipUpgradeEmails($mailer, $row, $plan, $config, $message, $fieldSuffix);

			return;
		}

		if ($row->act == 'renew')
		{
			static::sendMembershipRenewalEmails($mailer, $row, $plan, $config, $message, $fieldSuffix);

			return;
		}

		$logEmails = static::loggingEnabled('new_subscription_emails', $config);

		$rowFields    = OSMembershipHelper::getProfileFields($row->plan_id);
		$emailContent = OSMembershipHelper::getEmailContent($config, $row, false, 'register');
		$replaces     = OSMembershipHelper::callOverridableHelperMethod('Helper', 'buildTags', [$row, $config]);

		$replaces['SUBSCRIPTION_DETAIL'] = $emailContent;

		// New Subscription Email Subject
		if ($fieldSuffix && trim($plan->{'user_email_subject' . $fieldSuffix}))
		{
			$subject = $plan->{'user_email_subject' . $fieldSuffix};
		}
		elseif ($fieldSuffix && trim($message->{'user_email_subject' . $fieldSuffix}))
		{
			$subject = $message->{'user_email_subject' . $fieldSuffix};
		}
		elseif (trim($plan->user_email_subject))
		{
			$subject = $plan->user_email_subject;
		}
		else
		{
			$subject = $message->user_email_subject;
		}

		// New Subscription Email Body
		if (strpos($row->payment_method, 'os_offline') !== false && $row->published == 0)
		{
			$offlineSuffix = str_replace('os_offline', '', $row->payment_method);

			if ($offlineSuffix && $fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_email_body_offline' . $offlineSuffix . $fieldSuffix}))
			{
				$body = $message->{'user_email_body_offline' . $offlineSuffix . $fieldSuffix};
			}
			elseif ($offlineSuffix && OSMembershipHelper::isValidMessage($message->{'user_email_body_offline' . $offlineSuffix}))
			{
				$body = $message->{'user_email_body_offline' . $offlineSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($plan->{'user_email_body_offline' . $fieldSuffix}))
			{
				$body = $plan->{'user_email_body_offline' . $fieldSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_email_body_offline' . $fieldSuffix}))
			{
				$body = $message->{'user_email_body_offline' . $fieldSuffix};
			}
			elseif (OSMembershipHelper::isValidMessage($plan->user_email_body_offline))
			{
				$body = $plan->user_email_body_offline;
			}
			else
			{
				$body = $message->user_email_body_offline;
			}
		}
		else
		{
			if ($fieldSuffix && OSMembershipHelper::isValidMessage($plan->{'user_email_body' . $fieldSuffix}))
			{
				$body = $plan->{'user_email_body' . $fieldSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_email_body' . $fieldSuffix}))
			{
				$body = $message->{'user_email_body' . $fieldSuffix};
			}
			elseif (OSMembershipHelper::isValidMessage($plan->user_email_body))
			{
				$body = $plan->user_email_body;
			}
			else
			{
				$body = $message->user_email_body;
			}
		}

		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$value   = (string) $value;
			$subject = str_ireplace("[$key]", $value, $subject);
			$body    = str_ireplace("[$key]", $value, $body);
		}

		$invoicePath = '';

		if ($row->invoice_number > 0
			&& ($config->send_invoice_to_customer || $config->send_invoice_to_admin))
		{
			$invoicePath = OSMembershipHelper::generateAndReturnInvoicePath($row);
		}

		if ($config->send_invoice_to_customer && $invoicePath)
		{
			$mailer->addAttachment($invoicePath);
		}

		// Generate and send member card to subscriber email
		if ($config->send_member_card_via_email && $row->published == 1)
		{
			$path = OSMembershipHelperSubscription::generatePlanMemberCard($row, $config);
			$mailer->addAttachment($path);
		}

		// Add documents from plan to subscription confirmation email if subscription is active
		if ($row->published == 1)
		{
			static::addSubscriptionDocuments($mailer, $row);
		}

		if (MailHelper::isEmailAddress($row->email))
		{
			static::send($mailer, array_merge([$row->email, self::$ccEmail], self::getEmailsFromSubscriptionData($rowFields, $replaces)), $subject,
				$body, $logEmails,
				2, 'new_subscription_emails');

			$mailer->clearAllRecipients();
		}

		$mailer->clearAttachments();

		if ($config->send_invoice_to_admin && $invoicePath)
		{
			$mailer->addAttachment($invoicePath);
		}

		$emails = explode(',', $config->notification_emails);

		if ($fieldSuffix && strlen($message->{'admin_email_subject' . $fieldSuffix}))
		{
			$subject = $message->{'admin_email_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->admin_email_subject;
		}

		$subject = str_replace('[PLAN_TITLE]', $plan->title, $subject);

		if ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'admin_email_body' . $fieldSuffix}))
		{
			$body = $message->{'admin_email_body' . $fieldSuffix};
		}
		elseif (OSMembershipHelper::isValidMessage($plan->admin_email_body))
		{
			$body = $plan->admin_email_body;
		}
		else
		{
			$body = $message->admin_email_body;
		}

		$emailContent = OSMembershipHelper::getEmailContent($config, $row, true, 'register');

		$body = str_replace('[SUBSCRIPTION_DETAIL]', $emailContent, $body);

		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$value   = (string) $value;
			$subject = str_ireplace("[$key]", $value, $subject);
			$body    = str_ireplace("[$key]", $value, $body);
		}

		if ($config->send_attachments_to_admin)
		{
			self::addAttachments($mailer, $rowFields, $replaces);
		}

		static::send($mailer, $emails, $subject, $body, $logEmails, 1, 'new_subscription_emails');

		//After sending email, we can empty the user_password of subscription was activated
		if ($row->published == 1 && $row->user_password)
		{
			$query->clear()
				->update('#__osmembership_subscribers')
				->set('user_password = ""')
				->where('id = ' . $row->id);
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Send email when subscriber upgrade their membership
	 *
	 * @param   JMail                        $mailer
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   stdClass                     $plan
	 * @param   MPFConfig                    $config
	 * @param   MPFConfig                    $message
	 * @param   string                       $fieldSuffix
	 */
	public static function sendMembershipRenewalEmails($mailer, $row, $plan, $config, $message, $fieldSuffix)
	{
		if ($row->renew_option_id == OSM_DEFAULT_RENEW_OPTION_ID)
		{
			$numberDays = $plan->subscription_length;
		}
		else
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('number_days')
				->from('#__osmembership_renewrates')
				->where('id = ' . $row->renew_option_id);
			$db->setQuery($query);
			$numberDays = $db->loadResult();
		}

		$logEmails = static::loggingEnabled('subscription_renewal_emails', $config);

		// Get list of fields
		$rowFields = OSMembershipHelper::getProfileFields($row->plan_id);

		$emailContent = OSMembershipHelper::getEmailContent($config, $row, false, 'register');
		$replaces     = OSMembershipHelper::callOverridableHelperMethod('Helper', 'buildTags', [$row, $config]);

		$replaces['number_days']         = $numberDays;
		$replaces['SUBSCRIPTION_DETAIL'] = $emailContent;

		// Subscription Renewal Email Subject
		if ($fieldSuffix && trim($plan->{'user_renew_email_subject' . $fieldSuffix}))
		{
			$subject = $plan->{'user_renew_email_subject' . $fieldSuffix};
		}
		elseif ($fieldSuffix && trim($message->{'user_renew_email_subject' . $fieldSuffix}))
		{
			$subject = $message->{'user_renew_email_subject' . $fieldSuffix};
		}
		elseif (trim($plan->user_renew_email_subject))
		{
			$subject = $plan->user_renew_email_subject;
		}
		else
		{
			$subject = $message->user_renew_email_subject;
		}

		// Subscription Renewal Email Body
		if (strpos($row->payment_method, 'os_offline') !== false && $row->published == 0)
		{
			$offlineSuffix = str_replace('os_offline', '', $row->payment_method);

			if ($offlineSuffix && $fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_renew_email_body_offline' . $offlineSuffix . $fieldSuffix}))
			{
				$body = $message->{'user_renew_email_body_offline' . $offlineSuffix . $fieldSuffix};
			}
			elseif ($offlineSuffix && OSMembershipHelper::isValidMessage($message->{'user_renew_email_body_offline' . $offlineSuffix}))
			{
				$body = $message->{'user_renew_email_body_offline' . $offlineSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($plan->{'user_renew_email_body_offline' . $fieldSuffix}))
			{
				$body = $plan->{'user_renew_email_body_offline' . $fieldSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_renew_email_body_offline' . $fieldSuffix}))
			{
				$body = $message->{'user_renew_email_body_offline' . $fieldSuffix};
			}
			elseif (OSMembershipHelper::isValidMessage($plan->user_renew_email_body_offline))
			{
				$body = $plan->user_renew_email_body_offline;
			}
			else
			{
				$body = $message->user_renew_email_body_offline;
			}
		}
		else
		{
			if ($fieldSuffix && OSMembershipHelper::isValidMessage($plan->{'user_renew_email_body' . $fieldSuffix}))
			{
				$body = $plan->{'user_renew_email_body' . $fieldSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_renew_email_body' . $fieldSuffix}))
			{
				$body = $message->{'user_renew_email_body' . $fieldSuffix};
			}
			elseif (OSMembershipHelper::isValidMessage($plan->user_renew_email_body))
			{
				$body = $plan->user_renew_email_body;
			}
			else
			{
				$body = $message->user_renew_email_body;
			}
		}

		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$value   = (string) $value;
			$subject = str_ireplace("[$key]", $value, $subject);
			$body    = str_ireplace("[$key]", $value, $body);
		}

		$invoicePath = '';

		if ($row->invoice_number > 0
			&& ($config->send_invoice_to_customer || $config->send_invoice_to_admin))
		{
			$invoicePath = OSMembershipHelper::generateAndReturnInvoicePath($row);
		}

		if ($config->send_invoice_to_customer && $invoicePath)
		{
			$mailer->addAttachment($invoicePath);
		}

		// Generate and send member card to subscriber email
		if ($config->send_member_card_via_email && $row->published == 1)
		{
			$path = OSMembershipHelperSubscription::generatePlanMemberCard($row, $config);
			$mailer->addAttachment($path);
		}

		if ($row->published)
		{
			static::addSubscriptionDocuments($mailer, $row);
		}

		if (MailHelper::isEmailAddress($row->email))
		{
			static::send($mailer, array_merge([$row->email, self::$ccEmail], self::getEmailsFromSubscriptionData($rowFields, $replaces)), $subject,
				$body, $logEmails,
				2, 'subscription_renewal_emails');

			$mailer->clearAllRecipients();
		}

		$mailer->clearAttachments();

		if ($config->send_invoice_to_admin && $invoicePath)
		{
			$mailer->addAttachment($invoicePath);
		}


		$emails = explode(',', $config->notification_emails);

		if (strlen($message->{'admin_renw_email_subject' . $fieldSuffix}))
		{
			$subject = $message->{'admin_renw_email_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->admin_renw_email_subject;
		}

		$subject = str_replace('[PLAN_TITLE]', $plan->title, $subject);

		if ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'admin_renew_email_body' . $fieldSuffix}))
		{
			$body = $message->{'admin_renew_email_body' . $fieldSuffix};
		}
		elseif (OSMembershipHelper::isValidMessage($plan->admin_renew_email_body))
		{
			$body = $plan->admin_renew_email_body;
		}
		else
		{
			$body = $message->admin_renew_email_body;
		}

		if ($row->payment_method == 'os_offline_creditcard')
		{
			$emailContent                    = OSMembershipHelper::getEmailContent($config, $row, true, 'register');
			$replaces['SUBSCRIPTION_DETAIL'] = $emailContent;
		}

		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$value   = (string) $value;
			$subject = str_ireplace("[$key]", $value, $subject);
			$body    = str_ireplace("[$key]", $value, $body);
		}

		//We will need to get attachment data here
		if ($config->send_attachments_to_admin)
		{
			static::addAttachments($mailer, $rowFields, $replaces);
		}

		static::send($mailer, $emails, $subject, $body, $logEmails, 1, 'subscription_renewal_emails');
	}

	/**
	 * Send email when someone upgrade their membership
	 *
	 * @param   JMail                        $mailer
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   stdClass                     $plan
	 * @param   MPFConfig                    $config
	 * @param   MPFConfig                    $message
	 * @param   string                       $fieldSuffix
	 */
	public static function sendMembershipUpgradeEmails($mailer, $row, $plan, $config, $message, $fieldSuffix)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('b.title' . $fieldSuffix, 'title'))
			->from('#__osmembership_upgraderules AS a')
			->innerJoin('#__osmembership_plans AS b ON a.from_plan_id = b.id')
			->where('a.id = ' . $row->upgrade_option_id);
		$db->setQuery($query);
		$planTitle = $db->loadResult();

		$logEmails = static::loggingEnabled('subscription_upgrade_emails', $config);

		$rowFields = OSMembershipHelper::getProfileFields($row->plan_id);

		$emailContent = OSMembershipHelper::getEmailContent($config, $row, false, 'register');
		$replaces     = OSMembershipHelper::callOverridableHelperMethod('Helper', 'buildTags', [$row, $config]);

		$replaces['SUBSCRIPTION_DETAIL'] = $emailContent;

		$replaces['plan_title']    = $planTitle;
		$replaces['to_plan_title'] = $plan->title;

		// Subscription Upgrade Email Subject
		if ($fieldSuffix && $message->{'user_upgrade_email_subject' . $fieldSuffix})
		{
			$subject = $message->{'user_upgrade_email_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->user_upgrade_email_subject;
		}

		// Subscription Renewal Email Body
		if (strpos($row->payment_method, 'os_offline') !== false && $row->published == 0)
		{
			$offlineSuffix = str_replace('os_offline', '', $row->payment_method);

			if ($offlineSuffix && $fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_upgrade_email_body_offline' . $offlineSuffix . $fieldSuffix}))
			{
				$body = $message->{'user_upgrade_email_body_offline' . $offlineSuffix . $fieldSuffix};
			}
			elseif ($offlineSuffix && OSMembershipHelper::isValidMessage($message->{'user_upgrade_email_body_offline' . $offlineSuffix}))
			{
				$body = $message->{'user_upgrade_email_body_offline' . $offlineSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($plan->{'user_upgrade_email_body_offline' . $fieldSuffix}))
			{
				$body = $plan->{'user_upgrade_email_body_offline' . $fieldSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_upgrade_email_body_offline' . $fieldSuffix}))
			{
				$body = $message->{'user_upgrade_email_body_offline' . $fieldSuffix};
			}
			elseif (OSMembershipHelper::isValidMessage($plan->user_upgrade_email_body_offline))
			{
				$body = $plan->user_upgrade_email_body_offline;
			}
			elseif (OSMembershipHelper::isValidMessage($message->user_upgrade_email_body_offline))
			{
				$body = $message->user_upgrade_email_body_offline;
			}
			// The conditions below is for keep backward compatible
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_upgrade_email_body' . $fieldSuffix}))
			{
				$body = $message->{'user_upgrade_email_body' . $fieldSuffix};
			}
			elseif (OSMembershipHelper::isValidMessage($plan->user_upgrade_email_body))
			{
				$body = $plan->user_upgrade_email_body;
			}
			else
			{
				$body = $message->user_upgrade_email_body;
			}
		}
		else
		{
			if ($fieldSuffix && OSMembershipHelper::isValidMessage($plan->{'user_upgrade_email_body' . $fieldSuffix}))
			{
				$body = $plan->{'user_upgrade_email_body' . $fieldSuffix};
			}
			elseif ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'user_upgrade_email_body' . $fieldSuffix}))
			{
				$body = $message->{'user_upgrade_email_body' . $fieldSuffix};
			}
			elseif (OSMembershipHelper::isValidMessage($plan->user_upgrade_email_body))
			{
				$body = $plan->user_upgrade_email_body;
			}
			else
			{
				$body = $message->user_upgrade_email_body;
			}
		}

		$subject = str_replace('[TO_PLAN_TITLE]', $plan->title, $subject);
		$subject = str_replace('[PLAN_TITLE]', $planTitle, $subject);

		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$value   = (string) $value;
			$subject = str_ireplace("[$key]", $value, $subject);
			$body    = str_ireplace("[$key]", $value, $body);
		}

		$invoicePath = '';

		if ($row->invoice_number > 0
			&& ($config->send_invoice_to_customer || $config->send_invoice_to_admin))
		{
			$invoicePath = OSMembershipHelper::generateAndReturnInvoicePath($row);
		}

		if ($config->send_invoice_to_customer && $invoicePath)
		{
			$mailer->addAttachment($invoicePath);
		}

		// Generate and send member card to subscriber email
		if ($config->send_member_card_via_email && $row->published == 1)
		{
			$path = OSMembershipHelperSubscription::generatePlanMemberCard($row, $config);
			$mailer->addAttachment($path);
		}

		if ($row->published)
		{
			static::addSubscriptionDocuments($mailer, $row);
		}

		if (MailHelper::isEmailAddress($row->email))
		{
			static::send($mailer, array_merge([$row->email, self::$ccEmail], self::getEmailsFromSubscriptionData($rowFields, $replaces)), $subject,
				$body, $logEmails,
				2, 'subscription_upgrade_emails');

			$mailer->clearAllRecipients();
		}

		$mailer->clearAttachments();

		if ($config->send_invoice_to_admin && $invoicePath)
		{
			$mailer->addAttachment($invoicePath);
		}

		//Send emails to notification emails

		$emails = explode(',', $config->notification_emails);

		if ($fieldSuffix && strlen($message->{'admin_upgrade_email_subject' . $fieldSuffix}))
		{
			$subject = $message->{'admin_upgrade_email_subject' . $fieldSuffix};
		}
		else
		{
			$subject = $message->admin_upgrade_email_subject;
		}

		$subject = str_replace('[TO_PLAN_TITLE]', $plan->title, $subject);
		$subject = str_replace('[PLAN_TITLE]', $planTitle, $subject);

		if ($fieldSuffix && OSMembershipHelper::isValidMessage($message->{'admin_upgrade_email_body' . $fieldSuffix}))
		{
			$body = $message->{'admin_upgrade_email_body' . $fieldSuffix};
		}
		elseif (OSMembershipHelper::isValidMessage($plan->admin_upgrade_email_body))
		{
			$body = $plan->admin_upgrade_email_body;
		}
		else
		{
			$body = $message->admin_upgrade_email_body;
		}

		if ($row->payment_method == 'os_offline_creditcard')
		{
			$emailContent                    = OSMembershipHelper::getEmailContent($config, $row, true, 'register');
			$replaces['SUBSCRIPTION_DETAIL'] = $emailContent;
		}

		foreach ($replaces as $key => $value)
		{
			$key     = strtoupper($key);
			$value   = (string) $value;
			$subject = str_ireplace("[$key]", $value, $subject);
			$body    = str_ireplace("[$key]", $value, $body);
		}

		// Add attachments which subscriber upload to notification emails
		if ($config->send_attachments_to_admin)
		{
			static::addAttachments($mailer, $rowFields, $replaces);
		}

		static::send($mailer, $emails, $subject, $body, $logEmails, 2, 'subscription_upgrade_emails');
	}
}