<?php
/**
 * @version		1.1.1
 * @package		Joomla
 * @subpackage	OS Services Booking
 * @author      Dang Thuc Dam
 * @copyright	Copyright (C) 2018 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;

class os_paypal extends OSBPayment
{
    /**
	 * Constructor functions, init some parameter
	 *
	 * @param   JRegistry  $params
	 */
	public function __construct($params, $config = [])
	{
		parent::__construct($params, $config);

		$this->mode = $params->get('paypal_mode');

		if ($this->mode)
		{
			$this->url = 'https://www.paypal.com/cgi-bin/webscr';
		}
		else
		{
			$this->url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}

		$this->setParameter('business', $this->params->get('paypal_id'));
		$this->setParameter('rm', 2);
		$this->setParameter('cmd', '_xclick');
		$this->setParameter('no_shipping', 1);
		$this->setParameter('no_note', 1);
		$this->setParameter('charset', 'utf-8');
		$this->setParameter('tax', 0);

		$locale = $params->get('paypal_locale');

		if (empty($locale))
		{
			if (JLanguageMultilang::isEnabled())
			{
				$locale = JFactory::getLanguage()->getTag();
				$locale = str_replace('-', '_', $locale);
			}
			else
			{
				$locale = 'en_US';
			}
		}

		$this->setParameter('lc', $locale);
	}


    /**
     * Process Payment
     *
     * @param object $row
     * @param array $params
     */
    function processPayment($row, $data)
    {
        require_once JPATH_ADMINISTRATOR.'/components/com_osservicesbooking/helpers/helper.php';
        $db     = JFactory::getDbo();
        $jinput = JFactory::getApplication()->input;
        $Itemid = $jinput->getInt('Itemid', 0);
        $siteUrl = JURI::base();

        if(OSBHelper::orderHasOneService($row->id))
        {
            $service = OSBHelper::getServiceIdOfOrder($row->id);
            if($service->paypal_id != "")
			{
                $this->setParameter('business', $service->paypal_id);
            }
        }

        $this->setParameter('item_name', $data['item_name']);
        $this->setParameter('amount', $data['amount']);
		$this->setParameter('currency_code', $data['currency']);
        $this->setParameter('custom', $row->id);

        $this->setParameter('return',			OSBHelper::generatePaymentReturnUrl($row->id,  $data['isRemain'], $Itemid));
        $this->setParameter('cancel_return',	OSBHelper::generatePaymentCancelUrl($row->id,  $data['isRemain'], $Itemid));
        $this->setParameter('notify_url',		OSBHelper::generateNotifyUrl($row->id, $data['isRemain'], 'os_paypal'));

        $this->setParameter('address1', $data['address']);
        $this->setParameter('address2', '');
        $this->setParameter('city', $data['city']);
        $this->setParameter('country', $data['country']);
        $this->setParameter('first_name', $data['first_name']);
        $this->setParameter('last_name', $data['last_name']);
        $this->setParameter('state', $data['state']);
        $this->setParameter('zip', $data['zip']);
        $this->setParameter('email', $row->order_email);

        $sid    = OSBHelper::checkOrderWithOneService($row->id);
        if($sid > 0)
        {
            $db->setQuery("Select paypal_id from #__app_sch_services where id = '$row->id'");
            $paypal_id = $db->loadResult();
            if($paypal_id != "")
            {
                $this->setParameter('business', $paypal_id);
            }
        }

        $this->renderRedirectForm();
    }

	public function verifyPayment()
	{
		$ret = $this->validate();
	
		if ($ret)
		{
			$remainPayment = JFactory::getApplication()->input->getInt('remainPayment', 0 );
			$configClass   = OSBHelper::loadConfig();
			$id            = $this->notificationData['custom'];
			$transactionId = $this->notificationData['txn_id'];
			$amount        = $this->notificationData['mc_gross'];
			$currency      = $this->notificationData['mc_currency'];
			if ($amount < 0)
			{
				return false;
			}

			require_once(JPATH_COMPONENT_ADMINISTRATOR."/tables/order.php");

			$row = JTable::getInstance('Order', 'OsAppTable');

			if (!$row->load($id))
			{
				return false;
			}

			if($remainPayment == 0)
			{

				if ($row->order_status == "S") 
				{
					return false;
				}

				if($currency == "" || strtoupper($currency) != strtoupper($configClass['currency_format']))
				{
					return false;
				}

				if(floatval($amount) < $row->order_upfront)
				{
					return false;
				}

				$this->onPaymentSuccess($row, $transactionId);

			}
			else
			{
				if($row->make_remain_payment == 1)
				{
					return false;
				}
				$this->onRemainPaymentSuccess($row, $transactionId);
			}	

			return true;
		}

		return false;
	}
	

	protected function validate()
	{
		JLoader::register('PaypalIPN', JPATH_ROOT . '/components/com_osservicesbooking/plugins/paypal/PayPalIPN.php');

		$ipn = new PaypalIPN;

		// Use sandbox URL if test mode is configured
		if (!$this->mode)
		{
			$ipn->useSandbox();
		}

		// Disable use custom certs
		if ($this->params->get('use_local_certs', 0) == 0)
		{
			// Disable use custom certs
			$ipn->usePHPCerts();
		}

		$this->notificationData = $_POST;

		try
		{
			$valid = $ipn->verifyIPN();
			$this->logGatewayData($ipn->getResponse());

			if (!$this->mode || $valid)
			{
				return true;
			}

			return false;
		}
		catch (Exception $e)
		{
			$this->logGatewayData($e->getMessage());

			return false;
		}
	}

	/**
	 * Method to check if API Credentials is entered into the payment plugin parameters
	 */
	public function supportRefundPayment()
	{
		list($apiUrl, $apiUser, $apiPassword, $apiSignature) = $this->getNvpApiParameters();

		return $apiUser && $apiPassword && $apiSignature;
	}

	/**
	 * Refund payment
	 *
	 * @param $row
	 *
	 * @return bool
	 * @throws Exception
	 *
	 * @since 1.0
	 */
	public function refund($row)
	{
		list($apiUrl, $apiUser, $apiPassword, $apiSignature) = $this->getNvpApiParameters();

		if (!$apiUser || !$apiPassword || !$apiSignature)
		{
			JFactory::getApplication()->enqueueMessage('You need to enter API parameters in Advanced tab of the payment plugin to be able to refund', 'error');

			return false;
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_URL, $apiUrl);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
			'USER'          => $apiUser,
			'PWD'           => $apiPassword,
			'SIGNATURE'     => $apiSignature,
			'VERSION'       => '108',
			'METHOD'        => 'RefundTransaction',
			'TRANSACTIONID' => $row->transaction_id,
			'REFUNDTYPE'    => 'Full',
		]));

		$response = curl_exec($curl);
		curl_close($curl);

		$nvp = $this->deformatNVP($response);

		if ($nvp['ACK'] == 'Success')
		{
			return true;
		}
		else
		{
			JFactory::getApplication()->enqueueMessage($nvp['L_LONGMESSAGE0'], 'error');

			return false;
		}
	}

	/**
	 * Get NvpApi Parameters
	 *
	 * @return array
	 */
	private function getNvpApiParameters()
	{
		if ($this->mode)
		{
			$apiUrl       = 'https://api-3t.paypal.com/nvp';
			$apiUser      = $this->params->get('paypal_api_user');
			$apiPassword  = $this->params->get('paypal_api_password');
			$apiSignature = $this->params->get('paypal_api_signature');
		}
		else
		{
			$apiUrl       = 'https://api-3t.sandbox.paypal.com/nvp';
			$apiUser      = $this->params->get('paypal_api_user_sandbox');
			$apiPassword  = $this->params->get('paypal_api_password_sandbox');
			$apiSignature = $this->params->get('paypal_api_signature_sandbox');
		}

		return [$apiUrl, $apiUser, $apiPassword, $apiSignature];
	}

	/**
	 * Extract response from PayPal into array
	 *
	 * @param $response
	 *
	 * @return array
	 */
	private function deformatNVP($response)
	{
		$nvp = [];

		parse_str(urldecode($response), $nvp);

		return $nvp;
	}
}