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

class plgOsservicesBookingEztexting extends CMSPlugin
{
	public function onSmsSending($sms_phone, $smscontent)
	{

		$username = $this->params->get('ezusername');
		$password = $this->params->get('ezpassword');

		if (!$username)
		{
			return;
		}

		try
		{
			$sms_phone      = [$this->sanitize($sms_phone)];
			
			//$result = $clickatell->sendMessage($data);

			$ch = curl_init('https://app.eztexting.com/api/sending');
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "user=" . $username .
						"&pass=" . trim($password) .
						"&phonenumber=" . $sms_phone .
						"&message=" . $smscontent .
						"&express=1");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);

				switch ($data)
				{
					case 1:
						$returnCode = JText::_('OS_EZTEXTING_CODE_1');
						break;
					case -1:
						$returnCode = JText::_('OS_EZTEXTING_CODE_ERR_1');
						break;
					case -2:
						$returnCode = JText::_('OS_EZTEXTING_CODE_ERR_2');
						break;
					case -5:
						$returnCode = JText::_('OS_EZTEXTING_CODE_ERR_5');
						break;
					case -7:
						$returnCode = JText::_('OS_EZTEXTING_CODE_ERR_7');
						break;
					case -104:
						$returnCode = JText::_('OS_EZTEXTING_CODE_ERR_104');
						break;
					case -106:
						$returnCode = JText::_('OS_EZTEXTING_CODE_ERR_106');
						break;
					case -10:
						$returnCode = JText::_('OS_EZTEXTING_CODE_ERR_10');
						break;
				}

			if ($data == 1) 
			{
				//return true;
			} 
			else 
			{
				//return false;
				OSBHelper::logData(__DIR__ . '/eztexting_error.txt', [ 'phone' => $sms_phone, 'error' => $result['error'], 'errorDescription' => $returnCode]);
			}
		}
		catch (Exception $e)
		{
			OSBHelper::logData(__DIR__ . '/eztexting_error.txt', [ 'phone' => $sms_phone, 'error' => $e->getMessage()]);
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
