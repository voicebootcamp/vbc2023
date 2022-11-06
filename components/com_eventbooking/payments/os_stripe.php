<?php
/**
 * @version            4.1.2
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class os_stripe extends RADPayment
{
	/**
	 * Constructor
	 *
	 * @param   JRegistry  $params
	 * @param   array      $config
	 */
	public function __construct($params, $config = ['type' => 1])
	{
		// Use sandbox API keys if available
		if (!$params->get('mode', 1))
		{
			if ($params->get('sandbox_stripe_public_key'))
			{
				$params->set('stripe_public_key', $params->get('sandbox_stripe_public_key'));
			}

			if ($params->get('sandbox_stripe_api_key'))
			{
				$params->set('stripe_api_key', $params->get('sandbox_stripe_api_key'));
			}
		}

		$config['params_map'] = [
			'apiKey' => 'stripe_api_key',
		];

		$document  = Factory::getDocument();
		$publicKey = $params->get('stripe_public_key');

		if ($params->get('use_stripe_card_element', 0))
		{
			$languages = LanguageHelper::getLanguages('lang_code');
			$langTag   = Factory::getLanguage()->getTag();

			if (isset($languages[$langTag]))
			{
				$locale = $languages[$langTag]->sef;
			}
			else
			{
				$locale = '';
			}

			$supportedLocales = [
				'ar',
				'da',
				'de',
				'en',
				'es',
				'fi',
				'fr',
				'he',
				'it',
				'ja',
				'no',
				'nl',
				'pl',
				'sv',
				'zh',
			];

			$document->addScript('https://js.stripe.com/v3/');

			if ($locale && in_array($locale, $supportedLocales))
			{
				$document->addScriptDeclaration(
					"   var stripe = Stripe('$publicKey');\n
						var elements = stripe.elements({locale: '$locale'});\n
					"
				);
			}
			else
			{
				$document->addScriptDeclaration(
					"   var stripe = Stripe('$publicKey');\n
						var elements = stripe.elements();\n
					"
				);
			}

			$config['type'] = 0;
		}
		else
		{
			$document->addScript('https://js.stripe.com/v2/');
			$document->addScriptDeclaration(
				"   var stripePublicKey = '$publicKey';\n
					Stripe.setPublishableKey('$publicKey');\n
				"
			);
		}

		parent::__construct($params, $config);
	}

	/**
	 * Process Payment
	 *
	 * @param   EventbookingTableRegistrant  $row
	 * @param   array                        $data
	 */
	public function processPayment($row, $data)
	{
		$app    = Factory::getApplication();
		$Itemid = $app->input->getInt('Itemid', 0);

		$stripeClient = $this->getStripeClient();

		// Create customer
		if (!empty($data['stripeToken']))
		{
			$card = $data['stripeToken'];
		}
		else
		{
			$card = [
				'number'    => $data['x_card_num'],
				'exp_month' => $data['exp_month'],
				'exp_year'  => $data['exp_year'],
				'cvc'       => $data['x_card_code'],
				'name'      => $data['card_holder_name'],
			];
		}

		// Creating customer
		try
		{
			$customer = $stripeClient->customers->create(
				[
					'name'   => rtrim($row->first_name . ' ' . $row->last_name),
					'source' => $card,
					'email'  => $row->email,
				]
			);
		}
		catch (Exception $e)
		{
			Factory::getSession()->set('omnipay_payment_error_reason', 'Creating customer error :' . $e->getMessage());
			$app->redirect($this->getPaymentFailureUrl($row, $Itemid));

			return false;
		}

		// Charge the customer
		if ($data['currency'] == 'JPY')
		{
			$amount = (int) $data['amount'];
		}
		else
		{
			$amount = 100 * round($data['amount'], 2);
		}

		$request = [
			'amount'        => $amount,
			'currency'      => $data['currency'],
			'description'   => $data['item_name'],
			'receipt_email' => $row->email,
			'customer'      => $customer->id,
			'metadata'      => $this->getMetadata($row, $data),
		];

		try
		{
			$charge = $stripeClient->charges->create($request);
			$this->onPaymentSuccess($row, $charge->id);

			$app->redirect($this->getPaymentCompleteUrl($row, $Itemid));
		}
		catch (Exception $e)
		{
			Factory::getSession()->set('omnipay_payment_error_reason', 'Creating charge error:' . $e->getMessage());
			$app->redirect($this->getPaymentFailureUrl($row, $Itemid));

			return false;
		}
	}

	/**
	 * Add stripeToken to request message
	 *
	 * @param   \Omnipay\Stripe\Message\AbstractRequest  $request
	 * @param   EventbookingTableRegistrant              $row
	 * @param   array                                    $data
	 */
	protected function getMetadata($row, $data)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('name, title')
			->from('#__eb_fields')
			->where('published = 1');
		$db->setQuery($query);
		$fields = $db->loadObjectList('name');

		if ($row->first_name && isset($fields['first_name']))
		{
			$metaData[$fields['first_name']->title] = $row->first_name;
		}

		if ($row->last_name && isset($fields['last_name']))
		{
			$metaData[$fields['last_name']->title] = $row->last_name;
		}

		$metaData['Email']  = $row->email;
		$metaData['Source'] = 'Events Booking';

		if ($row->user_id > 0)
		{
			$metaData['User ID'] = $row->user_id;
		}

		$metaData['Registrant ID'] = $row->id;

		return $metaData;
	}

	/**
	 * Refund a transaction
	 *
	 * @param   EventbookingTableRegistrant  $row
	 *
	 * @throws Exception
	 */
	public function refund($row)
	{
		$stripeClient = $this->getStripeClient();

		try
		{
			$stripeClient->refunds->create(['charge' => $row->transaction_id]);
		}
		catch (ApiErrorException $e)
		{

			// Use the variable $error to save any errors
			// To be displayed to the customer later in the page
			$body  = $e->getJsonBody();
			$err   = $body['error'];
			$error = $err['message'];

			throw new Exception($error);
		}
	}

	/**
	 * Get Stripe Client object
	 *
	 * @return \Stripe\StripeClient
	 */
	private function getStripeClient()
	{
		if (!class_exists('\Stripe\Stripe'))
		{
			require_once __DIR__ . '/stripe/init.php';
		}

		return new \Stripe\StripeClient($this->params->get('stripe_api_key'));
	}
}