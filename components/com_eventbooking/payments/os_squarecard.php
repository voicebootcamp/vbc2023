<?php
/**
 * @version            4.1.1
 * @package            Events Booking
 * @subpackage         Payment Plugins
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2012 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Square\Environment;
use Square\Models;
use Square\SquareClient;

class os_squarecard extends RADPayment
{
	/**
	 * Constructors function
	 *
	 * @param   \Joomla\Registry\Registry  $params
	 * @param   array                      $config
	 */
	public function __construct($params, $config = [])
	{
		$document = Factory::getDocument();

		if ($params->get('mode', 1))
		{
			$document->addScript('https://web.squarecdn.com/v1/square.js');
		}
		else
		{
			$document->addScript('https://sandbox.web.squarecdn.com/v1/square.js');

			$keys = [
				'application_id',
				'access_token',
				'location_id',
			];

			foreach ($keys as $key)
			{
				if ($params->get('sandbox_' . $key))
				{
					$params->set($key, $params->get('sandbox_' . $key));
				}
			}
		}

		HTMLHelper::_('behavior.core');

		$document->addScriptOptions('squareAppId', $params->get('application_id'))
			->addScriptOptions('squareLocationId', $params->get('location_id'))
			->addScript(Uri::root(true) . '/components/com_eventbooking/payments/squarecard/js/squarecard.js');

		parent::__construct($params, $config);
	}


	/**
	 * Process Payment
	 *
	 * @param $row
	 * @param $data
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function processPayment($row, $data)
	{
		require_once JPATH_ROOT . '/components/com_eventbooking/payments/squarecard/square/vendor/autoload.php';

		$app    = Factory::getApplication();
		$Itemid = $app->input->getInt('Itemid', 0);

		if (empty($data['square_card_token']))
		{
			throw new Exception('Missing nonce data for Square up');
		}

		if ($data['currency'] != 'JPY')
		{
			$amount = 100 * round($data['amount'], 2);
		}
		else
		{
			$amount = $data['amount'];
		}

		$amount = (int) $amount;

		$client = new SquareClient([
			'accessToken' => $this->params->get('access_token'),
			'environment' => $this->mode ? Environment::PRODUCTION : Environment::SANDBOX,
		]);

		$paymentsApi = $client->getPaymentsApi();

		$body_amountMoney = new Models\Money;
		$body_amountMoney->setAmount($amount);
		$body_amountMoney->setCurrency($data['currency']);
		$body = new Models\CreatePaymentRequest(
			$data['square_card_token'],
			uniqid(),
			$body_amountMoney
		);

		$body->setAutocomplete(true);
		$body->setLocationId($this->params->get('location_id'));
		$body->setReferenceId($row->id);
		$body->setNote(substr($data['item_name'], 0, 60));
		$body->setBuyerEmailAddress($row->email);
		$body->setReferenceId($row->id);

		// Billing address
		$address = new Models\Address();
		$address->setAddressLine1($row->address);
		$address->setAddressLine2($row->address2);
		$address->setLocality($row->city);
		$address->setPostalCode($row->zip);
		$address->setCountry($data['country']);

		$body->setBillingAddress($address);


		if (!empty($data['square_card_verification_token']))
		{
			$body->setVerificationToken($data['square_card_verification_token']);
		}

		try
		{
			$apiResponse = $paymentsApi->createPayment($body);

			if ($apiResponse->isSuccess())
			{
				$createPaymentResponse = $apiResponse->getResult();
				$this->onPaymentSuccess($row, $createPaymentResponse->getPayment()->getId());
				$app->redirect($this->getPaymentCompleteUrl($row, $Itemid));
			}
			else
			{
				$errors = $apiResponse->getErrors();

				$errorMessages = [];

				foreach ($errors as $error)
				{
					$errorMessages[] = $error->getDetail();
				}

				Factory::getSession()->set('omnipay_payment_error_reason', implode("\r\n", $errorMessages));
				$app->redirect($this->getPaymentFailureUrl($row, $Itemid));
			}
		}
		catch (Exception $e)
		{
			Factory::getSession()->set('omnipay_payment_error_reason', $e->getMessage());;
			$app->redirect($this->getPaymentFailureUrl($row, $Itemid));
		}
	}
}