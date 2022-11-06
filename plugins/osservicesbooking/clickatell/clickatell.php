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

class plgOsservicesBookingClickatell extends CMSPlugin
{
	public function onSmsSending($sms_phone, $smscontent)
	{
		require_once JPATH_ROOT . '/plugins/osservicesbooking/clickatell/clickatell/vendor/autoload.php';

		$apiToken = $this->params->get('api_token');

		if (!$apiToken)
		{
			return;
		}

		$clickatell = new \Clickatell\Rest($apiToken);

		$data = [];

		if ($this->params->get('sender_id'))
		{
			$data['from'] = $this->sanitize($this->params->get('sender_id'));
		}

		
		try
		{
			$data['to']      = [$this->sanitize($sms_phone)];
			$data['content'] = $smscontent;
			
			$result = $clickatell->sendMessage($data);

			if ($result['error'])
			{
				OSBHelper::logData(__DIR__ . '/clickatell_error.txt', ['phone' => $sms_phone, 'error' => $result['error'], 'errorDescription' => $result['errorDescription']]);
			}
		}
		catch (Exception $e)
		{
			OSBHelper::logData(__DIR__ . '/clickatell_error.txt', ['phone' => $sms_phone, 'error' => $e->getMessage()]);
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
