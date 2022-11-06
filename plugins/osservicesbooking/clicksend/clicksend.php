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

class plgOsservicesBookingClicksend extends CMSPlugin
{
	const BASE_URI = 'https://rest.clicksend.com/v3/sms/send';

	public function onSmsSending($phone_number, $smscontent)
	{
		$api_key	= $this->params->get('api_key');
		$username	= $this->params->get('username');
		$sender		= $this->params->get('sender');

		if (!$username)
		{
			return;
		}

		try
		{
			$phone_number = trim(str_replace(" ", "", $phone_number));

			// if the length of the number is greater than 10 characters, then we assume that 
			// the country prefix is already included in the number
			if (substr($phone_number, 0, 1) != '+')
			{
				if (substr($phone_number, 0, 2) == '00')
				{
					$phone_number = '+' . substr($phone_number, 2);
				}
				else
				{
					if (strlen($phone_number) > 10)
					{
						$phone_number = '+' . $phone_number;
					}
					else
					{
						$phone_number = $this->params['prefix'] . $phone_number;
					}
				}
			}
			
			// double prefix prevention
			if (substr($phone_number, 0, 1) == '+' && substr($phone_number, 0, 3) == substr($phone_number, 3, 3))
			{
				$phone_number = substr($phone_number, 3);
			}
			else if (substr($phone_number, 0, 1) == '+' && substr($phone_number, 0, 2) == substr($phone_number, 2, 2))
			{
				$phone_number = substr($phone_number, 2);
			}
			else if (substr($phone_number, 0, 1) == '+' && substr($phone_number, 0, 4) == substr($phone_number, 4, 4))
			{
				$phone_number = substr($phone_number, 4);
			}

			// compose message
			$message = new stdClass;
			$message->to     = $phone_number;
			$message->body   = $smscontent;
			$message->from   = $sender;
			$message->source = 'OSB';
			// compose payload
			$payload = new stdClass;
			$payload->messages = array($message);
			
			$this->sendPost($payload);
		}
		catch (Exception $e)
		{
			OSBHelper::logData(__DIR__ . '/clicksend_error.txt', [ 'phone' => $phone_number, 'error' => $e->getMessage()]);
		}

		// Return true to tell the system that SMS were successfully sent so that it could update sms sending status for registrants
		return true;
	}

	private function sendPost($payload)
	{
		$username	= $this->params->get('username');
		$password	= $this->params->get('api_key');
		$result = array();
		
		// make the POST request through CURL
		$to_smsh = curl_init(self::BASE_URI);
		curl_setopt($to_smsh, CURLOPT_POST, true);
		curl_setopt($to_smsh, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($to_smsh, CURLOPT_POSTFIELDS, json_encode($payload)); 
		curl_setopt($to_smsh, CURLOPT_HTTPHEADER, array(
			'Content-Type:application/json',
			'Authorization: Basic '. base64_encode($username . ":" . $password)
		));
		
		$result['response'] = curl_exec($to_smsh);
		$result['status'] = curl_getinfo($to_smsh);
		
		curl_close($to_smsh);

		// set log
		//$this->log = print_r($result, true);

		return $result;
	}

	/**
	 * Evaluates the response retrieved after sending a message.
	 *
	 * @param 	array 	 $arr  The response to check.
	 *
	 * @return 	boolean  True if the message has been sent, otherwise false.
	 */
	public function validateResponse($arr)
	{
		if (empty($arr) || !is_array($arr) || empty($arr['response']))
		{
			return false;
		}

		if (is_string($arr['response']))
		{
			$arr['response'] = json_decode($arr['response']);
		}

		if (!is_object($arr['response']))
		{
			return false;
		}

		if (isset($arr['response']->http_code) && $arr['response']->http_code == 200)
		{
			return true;
		}

		if (isset($arr['response']->response_code) && $arr['response']->response_code == 'SUCCESS')
		{
			return true;
		}
		
		return false;
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
