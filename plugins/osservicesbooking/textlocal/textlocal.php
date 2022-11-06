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

class plgOsservicesBookingTextlocal extends CMSPlugin
{
	public function onSmsSending($sms_phone, $smscontent)
	{
		require_once JPATH_ROOT . '/plugins/osservicesbooking/textlocal/textlocal/textlocal.class.php';

		$api_key = $this->params->get('api_key');
		$sender = $this->params->get('sender');

		if (!$api_key)
		{
			return;
		}

		try
		{
			$sms_phone      = [$this->sanitize($sms_phone)];
			
			//$result = $clickatell->sendMessage($data);

			$client = new Textlocal(false, false, $api_key);
            $client->sendSms([$sms_phone], $smscontent, $sender);
		}
		catch (Exception $e)
		{
			OSBHelper::logData(__DIR__ . '/textlocal_error.txt', [ 'phone' => $sms_phone, 'error' => $e->getMessage()]);
		}

		// Return true to tell the system that SMS were successfully sent so that it could update sms sending status for registrants
		return true;
	}

	/**
	 * Helper method used to sanitize phone numbers.
	 *
	 * @param   string  $phone  The phone number to sanitize.
	 *
	 * @return    string    The cleansed number.
	 */
	protected function sanitize($phone)
	{
		$phone = trim(str_replace(" ", "", $phone));

		if (substr($phone, 0, 1) != '+')
		{
			if (substr($phone, 0, 2) == '00')
			{
				$phone = '+' . substr($phone, 2);
			}
			else
			{
				$phone = $this->params->get('prefix') . $phone;
			}
		}

		return $phone;
	}
}
