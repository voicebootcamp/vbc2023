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

class plgOsservicesBookingSmshosting extends CMSPlugin
{
	const BASE_URI = 'https://api.smshosting.it/rest/api';

	public function onSmsSending($sms_phone, $smscontent)
	{
		$api_key	= $this->params->get('api_key');
		$api_secret = $this->params->get('api_secret');
		$sender		= $this->params->get('sender');

		if (!$api_key)
		{
			return;
		}

		try
		{
			$sms_phone      = [$this->sanitize($sms_phone)];
			
			//$result = $clickatell->sendMessage($data);
			$this->_send('/sms/send', $sms_phone, $smscontent);
		}
		catch (Exception $e)
		{
			OSBHelper::logData(__DIR__ . '/smshosting_error.txt', [ 'phone' => $sms_phone, 'error' => $e->getMessage()]);
		}

		// Return true to tell the system that SMS were successfully sent so that it could update sms sending status for registrants
		return true;
	}

	
	/**
	 * Tries to estimate the remaining balance of an SmsHosting account.
	 *
	 * @param 	string 	$phone_number 	The destination phone number.
	 * @param 	string 	$message 		The plain message to send.
	 *
	 * @return 	object  The response caught.
	 *
	 * @uses 	_send() Provides a cURL connection with smshosting.it.
	 */
	public function estimate($phone_number, $msg_text)
	{
		return $this->_send('/sms/estimate', $phone_number, $msg_text);
	}

	private function _send($dir_uri, $phone_number, $msg_text, $when = NULL)
	{
		$this->log = '';
		
		//$unicode = $this->containsUnicode($msg_text);
		
		if (strlen($this->params->get('sender')) > 11)
		{
			$start = 0;
			if (substr($this->params->get('sender'), 0, strlen($this->params->get('prefix'))) == $this->params->get('prefix'))
			{
				$start = strlen($this->params->get('prefix'));
			}

			$this->params->set('sender', trim(substr($this->params->get('sender'), $start, 11)));
		}
		
		$phone_number = $this->sanitize($phone_number);
		
		$post = array(
			'to' => urlencode($phone_number),
			'from' => urlencode($this->params->get('sender')),
			'group' => urlencode(NULL),
			'text' => urlencode($msg_text),
			'date' => urlencode($when),
			'transactionId' => urlencode(NULL),
			'sandbox' => 0,
			'statusCallback' => urlencode(NULL),
			/**
			 * For unicode chars, like with Greek, we now need to pass "encoding=AUTO"
			 * as updated on their official documentation at https://apirest.cloud/en/api.pdf.
			 * Available options: 7BIT (default if param is omitted), UCS2, AUTO.
			 * Do not use the type=[unicode|text] anymore, as encoding=AUTO seems the best solution.
			 * 
			 * @since 1.7
			 */
			'encoding' => 'AUTO',
			// 'type' => $unicode ? 'unicode' : 'text',
		);
		
		$complete_uri = self::BASE_URI.$dir_uri;
		
		$array_result = $this->sendPost($complete_uri, $post);
		
		if ($array_result['from_smsh'])
		{
			return $this->parseResponse($array_result);
		}
		else
		{
			return false;
		}
	} 
	
	private function sendPost($complete_uri, $data)
	{
		$post = '';
		foreach ($data as $k => $v)
		{
			$post .= "&$k=$v";
		}
		
		$array_result = array();
		
		// If available, use CURL
		if (function_exists('curl_version'))
		{	
			$to_smsh = curl_init( $complete_uri );
			curl_setopt($to_smsh, CURLOPT_POST, true);
			curl_setopt($to_smsh, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($to_smsh, CURLOPT_USERPWD, $this->params['apikey'] . ":" . $this->params['apisecret']);
			curl_setopt($to_smsh, CURLOPT_POSTFIELDS, $post);
			
			$array_result['from_smsh'] = curl_exec($to_smsh);
			
			$array_result['smsh_response_status'] = curl_getinfo($to_smsh, CURLINFO_HTTP_CODE);
			
			curl_close($to_smsh);
		}
		else if (ini_get('allow_url_fopen'))
		{
			// No CURL available so try the awesome file_get_contents
			
			$opts = array(
				'http' => array(
					'method' => 'POST',
					'ignore_errors' => true,
					'header' => "Authorization: Basic ".base64_encode($this->params->get('api_key') . ":" . $this->params->get('api_secret')) . "\r\nContent-type: application/x-www-form-urlencoded",
					'content' => $post 
				) 
			);
			$context = stream_context_create($opts);
			$array_result['from_smsh'] = file_get_contents($complete_uri, false, $context);
			
			list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
			
			$array_result['smsh_response_status'] = $status_code;
			
		}
		else
		{
			// No way of sending a HTTP post
			$array_result['from_smsh'] = false; 
		}

		return $array_result;
	}

	private function parseResponse($arr)
	{	
		$response = json_decode($arr['from_smsh']);
		
		$response_obj;
		
		if (is_array($response))
		{
			$response_obj = new stdClass;
			$response_obj->response = $response; 
		}
		else
		{
			$response_obj = $response;	 
		}
		
		if ($arr['smsh_response_status'] == 200)
		{
			$response_obj->errorCode = 0;
		}
		
		$this->log .= '<pre>'.print_r($response_obj, true)."</pre>\n\n";
		
		if ($response_obj)
		{
			return $response_obj;
		} 
		
		return false;
	}
	
	public function validateResponse($response_obj)
	{
		return ($response_obj === NULL || $response_obj->errorCode == 0);
	}
	
	///// UTILS /////
	
	public function getLog()
	{
		return $this->log;
	}
	
	private function containsUnicode($msg_text)
	{
		return max(array_map('ord', str_split($msg_text))) > 127;
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
