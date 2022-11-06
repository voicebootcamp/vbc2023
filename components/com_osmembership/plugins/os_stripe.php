<?php
/**
 * @version            3.0.2
 * @package            Membership Pro
 * @subpackage         Payment Plugins
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2012 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Stripe\Exception\ApiErrorException;

class os_stripe extends MPFPayment
{
	/**
	 * Stripe error message
	 *
	 * @var array
	 */
	protected $stripeErrors = [];

	/**
	 * Constructors function
	 *
	 * @param   \Joomla\Registry\Registry  $params
	 * @param   array                      $config
	 */
	public function __construct($params, $config = ['type' => 1])
	{
		require_once __DIR__ . '/stripe/init.php';

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

		$document  = Factory::getDocument();
		$view      = Factory::getApplication()->input->getCmd('view');
		$publicKey = $params->get('stripe_public_key');

		if ($params->get('use_stripe_card_element', 0) && $view != 'card')
		{
			$version = OSMembershipHelper::getInstalledVersion();

			if (version_compare($version, '2.14.0', 'le'))
			{
				Factory::getApplication()->enqueueMessage(JText::_('OSM_STRIPE_CARD_ELEMENET_REQUIREMENT'), 'warning');
			}

			$document->addScript('https://js.stripe.com/v3/');
			$document->addScriptDeclaration(
				"   var stripe = Stripe('$publicKey');\n
					var elements = stripe.elements();\n
				"
			);

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

		$this->stripeErrors = [
			'invalid_number'       => JText::_('OSM_STRIPE_ERROR_INVALID_NUMBER'),
			'invalid_expiry_month' => JText::_('OSM_STRIPE_ERROR_INVALID_EXPIRY_MONTH'),
			'invalid_expiry_year'  => JText::_('OSM_STRIPE_ERROR_INVALID_EXPIRY_YEAR'),
			'invalid_cvc'          => JText::_('OSM_STRIPE_ERROR_INVALID_EXPIRY_CVC'),
			'incorrect_number'     => JText::_('OSM_STRIPE_ERROR_INVALID_NUMBER'),
			'expired_card'         => JText::_('OSM_STRIPE_ERROR_EXPIRED_CARD'),
			'incorrect_cvc'        => JText::_('OSM_STRIPE_ERROR_INCORRECT_CVC'),
			'incorrect_zip'        => JText::_('OSM_STRIPE_ERROR_INCORRECT_ZIP'),
			'card_declined'        => JText::_('OSM_STRIPE_ERROR_CARD_DECLINED'),
			'processing_error'     => JText::_('OSM_STRIPE_ERROR_PROCESS_ERROR'),
		];

		$document->addScriptDeclaration('var osmStripeErrors = ' . json_encode($this->stripeErrors) . ";\n");

		parent::__construct($params, $config);
	}


	/**
	 * Process Payment
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   array                        $data
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function processPayment($row, $data)
	{
		$app          = Factory::getApplication();
		$Itemid       = $app->input->getInt('Itemid', 0);
		$stripeClient = $this->getStripeClient();

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

		if ($data['currency'] == 'JPY')
		{
			$amount = (int) $data['amount'];
		}
		else
		{
			$amount = 100 * round($data['amount'], 2);
		}

		$request = [
			'amount'      => $amount,
			'currency'    => $data['currency'],
			'description' => $data['item_name'],
			'card'        => $card,
		];

		try
		{
			$charge = $stripeClient->charges->create($request);
			$this->onPaymentSuccess($row, $charge->id);
			$app->redirect($this->getPaymentCompleteUrl($row, $Itemid));
		}
		catch (ApiErrorException $e)
		{
			$stripeCode = $e->getStripeCode();

			if (!empty($this->stripeErrors[$stripeCode]))
			{
				$errorMessage = $this->stripeErrors[$stripeCode];
			}
			else
			{
				$errorMessage = $e->getMessage();
			}

			Factory::getSession()->set('omnipay_payment_error_reason', $errorMessage);
			$app->redirect($this->getPaymentFailureUrl($row, $Itemid));
		}
		catch (Exception $e)
		{
			Factory::getSession()->set('omnipay_payment_error_reason', $e->getMessage());
			$app->redirect($this->getPaymentFailureUrl($row, $Itemid));
		}
	}

	/**
	 * Process recurring subscription
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   array                        $data
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function processRecurringPayment($row, $data)
	{
		$app          = Factory::getApplication();
		$Itemid       = $app->input->getInt('Itemid', 0);
		$rowPlan      = OSMembershipHelperDatabase::getPlan((int) $row->plan_id);
		$stripeClient = $this->getStripeClient();

		// Create the stripe plan if it doesn't exist
		$stripePlanId = 'membership_plan_' . $rowPlan->id . '_' . (100 * $data['regular_price']);
		$frequency    = $rowPlan->subscription_length_unit;
		$length       = $rowPlan->subscription_length;

		switch ($frequency)
		{
			case 'D':
				$unit = 'day';
				break;
			case 'W':
				$unit = 'week';
				break;
			case 'M':
				$unit = 'month';
				break;
			case 'Y':
				$unit = 'year';
				break;
		}

		if ($data['currency'] == 'JPY')
		{
			$amount = (int) $data['regular_price'];
		}
		else
		{
			$amount = 100 * round($data['regular_price'], 2);
		}

		// Create a new stripe plan
		$request = [
			"product"        => [
				'name' => $rowPlan->title,
			],
			'id'             => $stripePlanId,
			'amount'         => $amount,
			'currency'       => $data['currency'],
			'interval'       => $unit,
			'interval_count' => $length,
		];

		$membershipProVersion = OSMembershipHelper::getInstalledVersion();

		if (version_compare($membershipProVersion, '2.5.0', 'ge'))
		{
			$trialDuration     = $data['trial_duration'];
			$trialDurationUnit = $data['trial_duration_unit'];
		}
		else
		{
			$trialDuration     = $rowPlan->trial_duration;
			$trialDurationUnit = $rowPlan->trial_duration_unit;
		}

		if ($trialDuration > 0)
		{
			// Calculate the amount
			switch ($trialDurationUnit)
			{
				case 'D':
					$trialDays = $trialDuration;
					break;
				case 'W':
					$trialDays = 7 * $trialDuration;
					break;
				case 'M':
					$trialDays = 30 * $trialDuration;
					break;
				case 'Y':
					$trialDays = 365 * $trialDuration;
					break;
			}
		}

		try
		{
			$stripeClient->plans->create($request);
		}
		catch (Exception $e)
		{
			// Assure that the plan exists already
		}

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
			$customer = $stripeClient->customers->create([
				"description" => rtrim($row->first_name . ' ' . $row->last_name),
				"source"      => $card,
				'email'       => $row->email,
			]);
		}
		catch (Exception $e)
		{
			Factory::getSession()->set('omnipay_payment_error_reason', 'Creating customer error :' . $e->getMessage());
			$app->redirect($this->getPaymentFailureUrl($row, $Itemid));

			return false;
		}


		$transactionId = '';

		// Make a charge in case there is trial amount
		if ($trialDuration > 0 && $data['trial_amount'] > 0)
		{

			if ($data['currency'] == 'JPY')
			{
				$amount = (int) $data['trial_amount'];
			}
			else
			{
				$amount = 100 * round($data['trial_amount'], 2);
			}

			$request = [
				'amount'        => $amount,
				'currency'      => $data['currency'],
				'description'   => Text::sprintf('OSM_PAYMENT_FOR_TRIAL_SUBSCRIPTION', $rowPlan->title),
				'receipt_email' => $row->email,
				'customer'      => $customer->id,
			];

			try
			{
				$charge        = $stripeClient->charges->create($request);
				$transactionId = $charge->id;
			}
			catch (Exception $e)
			{
				Factory::getSession()->set('omnipay_payment_error_reason',
					'Creating charge error:' . $e->getMessage());
				$app->redirect($this->getPaymentFailureUrl($row, $Itemid));

				return false;
			}
		}

		// Next, create subscription
		$request = [
			'customer' => $customer->id,
			'items'    => [
				[
					'plan' => $stripePlanId,
				],
			],
		];

		if (!empty($trialDays))
		{
			$request['trial_period_days'] = $trialDays;
		}

		try
		{
			$subscription = $stripeClient->subscriptions->create($request);

			if (property_exists($row, 'subscription_id'))
			{
				$row->subscription_id = $subscription->id;
			}

			if (property_exists($row, 'gateway_customer_id'))
			{
				$row->gateway_customer_id = $customer->id;
			}

			if ($trialDuration > 0)
			{
				if (!$row->is_free_trial)
				{
					$row->payment_made = 1;
				}

				$this->onPaymentSuccess($row, $transactionId);
			}
			else
			{
				// Just store the subscription id and leave the payment event to send email
				$row->published = 1;
				$row->store();
			}

			$app->redirect($this->getPaymentCompleteUrl($row, $Itemid));
		}
		catch (Exception $e)
		{
			Factory::getSession()->set('omnipay_payment_error_reason',
				'Creating subscription error ' . $e->getMessage());
			$app->redirect($this->getPaymentFailureUrl($row, $Itemid));
		}
	}

	/**
	 *
	 * @param $row
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function cancelSubscription($row)
	{
		$stripeClient = $this->getStripeClient();

		try
		{
			$stripeClient->subscriptions->retrieve($row->subscription_id)->cancel();

			return true;
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
	}

	public function verifyRecurringPayment()
	{
		\Stripe\Stripe::setApiKey($this->params->get('stripe_api_key'));
		$body       = @file_get_contents('php://input');
		$event_json = json_decode($body);
		$event_id   = $event_json->id;

		try
		{
			$event = \Stripe\Event::retrieve($event_id);
		}
		catch (Exception $e)
		{
			$event = null;
		}

		if ($event && $event->type == 'invoice.payment_succeeded'
			&& !empty($event->data->object->subscription)
			&& $event->data->object->amount_paid > 0)
		{
			$transactionId  = $event->data->object->charge;
			$subscriptionId = $event->data->object->subscription;

			if ($transactionId && OSMembershipHelper::isTransactionProcessed($transactionId))
			{
				return;
			}

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__osmembership_subscribers')
				->where('subscription_id = ' . $db->quote($subscriptionId))
				->order('id');
			$db->setQuery($query, 0, 1);
			$id = (int) $db->loadResult();

			$this->logGatewayData('invoice.payment_succeeded' . (string) $query . ' ' . $subscriptionId . ' ' . $transactionId);

			if ($id > 0)
			{
				/* @var OSMembershipTableSubscriber $row */
				$row = Table::getInstance('OsMembership', 'Subscriber');
				$row->load($id);

				// First payment, store Transaction ID and email customers
				if ($row->payment_made == 0 && !$row->is_free_trial)
				{
					$row->payment_made = 1;
					$this->onPaymentSuccess($row, $transactionId);
				}
				else
				{
					$version = OSmembershipHelper::getInstalledVersion();

					if (version_compare($version, '2.14.0', 'ge'))
					{
						/* @var OSMembershipModelApi $model */
						$model               = MPFModel::getInstance('Api', 'OSMembershipModel',
							['ignore_request' => true]);
						$renewedSubscription = $model->renewRecurringSubscription($id, $subscriptionId, $transactionId);

						// Cancel the recurring subscription
						$rowPlan = OSMembershipHelperDatabase::getPlan($row->plan_id);

						if ($rowPlan->number_payments > 0 && $rowPlan->number_payments <= ($row->payment_made + 1))
						{
							$this->cancelSubscription($row);

							if (property_exists($rowPlan,
									'set_lifetime_subscription') && $rowPlan->set_lifetime_subscription == 1)
							{
								$renewedSubscription->to_date = '2099-12-31 23:59:59';
								$renewedSubscription->store();
							}

							if (property_exists($rowPlan, 'last_payment_action'))
							{
								if ($rowPlan->last_payment_action == 1)
								{
									$renewedSubscription->to_date = '2099-12-31 23:59:59';
									$renewedSubscription->store();
								}
								elseif ($rowPlan->last_payment_action == 2 && $rowPlan->extend_duration > 0 && $rowPlan->extend_duration_unit)
								{
									$date = Factory::getDate($renewedSubscription->to_date);
									$date->add(new DateInterval('P' . $rowPlan->extend_duration . $rowPlan->extend_duration_unit));
									$renewedSubscription->to_date = $date->toSql();
									$renewedSubscription->store();
								}
							}
						}
					}
					else
					{
						OSMembershipHelper::extendRecurringSubscription($id, $transactionId, $subscriptionId);
					}
				}
			}
		}
	}

	/**
	 * Method to update customer credit card
	 *
	 * @param   array                        $data
	 * @param   OSMembershipTableSubscriber  $subscription
	 *
	 * @throws Exception
	 */
	public function updateCard($data, $subscription)
	{
		$stripeClient = $this->getStripeClient();

		try
		{
			if (!empty($subscription->gateway_customer_id))
			{
				$cu = $stripeClient->customers->retrieve($subscription->gateway_customer_id);
			}
			else
			{
				$sub = $stripeClient->subscriptions->retrieve($subscription->subscription_id);
				$cu  = $stripeClient->customers->retrieve($sub->customer);
			}

			if (!empty($data['stripeToken']))
			{
				$cu->source = $data['stripeToken']; // obtained with Checkout
			}
			else
			{
				$cu->source = [
					'number'    => $data['x_card_num'],
					'exp_month' => $data['exp_month'],
					'exp_year'  => $data['exp_year'],
					'cvc'       => $data['x_card_code'],
					'name'      => $data['card_holder_name'],
				];
			}

			$cu->save();
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
	 * Refund a transaction
	 *
	 * @param   OSMembershipTableSubscriber  $row
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
	 * Get Stripe Client Object
	 *
	 * @return \Stripe\StripeClient
	 */
	private function getStripeClient()
	{
		return new \Stripe\StripeClient($this->params->get('stripe_api_key'));
	}
}