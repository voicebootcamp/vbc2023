<?php
/**
 * @version            3.14.0
 * @package            Events Booking
 * @subpackage         Payment Plugins
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2012 - 2020 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die();

use Square\SquareClient;
use Square\Environment;
use Square\Models;

class os_squareup extends RADPayment
{
	/**
	 * Constructors function
	 *
	 * @param   \Joomla\Registry\Registry  $params
	 * @param   array                      $config
	 */
	public function __construct($params, $config = ['type' => 1])
	{
		$document = JFactory::getDocument();

		if ($params->get('mode', 1))
		{
			$document->addScript('https://js.squareup.com/v2/paymentform');
		}
		else
		{
			$document->addScript('https://js.squareupsandbox.com/v2/paymentform');
		}

		$applicationId = $params->get('application_id');

		$layout = JFactory::getApplication()->input->get('layout', '', 'string');

		if ($layout == 'group')
		{
			$autoBuild = 'false';
		}
		else
		{
			$autoBuild = 'true';
		}

		$scriptText = <<<Javascript
			var sqPaymentForm = new SqPaymentForm({
			      // Replace this value with your application's ID (available from the merchant dashboard).
			      // If you're just testing things out, replace this with your _Sandbox_ application ID,
			      // which is also available there.
			      applicationId: '$applicationId',
			      autoBuild: $autoBuild,
			      inputClass: 'sq-input',
			      cardNumber: {
			        elementId: 'sq-card-number',
			        placeholder: "0000 0000 0000 0000"
			      },
			      cvv: {
			        elementId: 'sq-cvv',
			        placeholder: 'CVV'
			      },
			      expirationDate: {
			        elementId: 'sq-expiration-date',
			        placeholder: 'MM/YY'
			      },
			      postalCode: {
			        elementId: 'field_zip_input',
			        placeholder: 'Postal Code'
			      },
			      inputStyles: [
			        // Because this object provides no value for mediaMaxWidth or mediaMinWidth,
			        // these styles apply for screens of all sizes, unless overridden by another
			        // input style below.
			        {
			          fontSize: '14px',
			          padding: '3px'
			        },
			        // These styles are applied to inputs ONLY when the screen width is 400px
			        // or smaller. Note that because it doesn't specify a value for padding,
			        // the padding value in the previous object is preserved.
			        {
			          mediaMaxWidth: '400px',
			          fontSize: '18px',
			        }
			      ],
			      callbacks: {
			        cardNonceResponseReceived: function(errors, nonce, cardData) {
			          if (errors) {			           
			            errors.forEach(function(error) {			             
			              alert(error.message);			              
			            });
						
						document.getElementById('btn-submit').disabled = false;
			          } else {
			            // This alert is for debugging purposes only.
			            //alert('Nonce received! ' + nonce + ' ' + JSON.stringify(cardData));
			            // Assign the value of the nonce to a hidden form element
			            var nonceField = document.getElementById('card-nonce');
			            nonceField.value = nonce;			            
			            // Submit the form
			            document.getElementById('adminForm').submit();
			          }
			        },
			        unsupportedBrowserDetected: function() {
			          // Alert the buyer that their browser is not supported
			        }
			      }
			    });
			    function submitButtonClick(event) {
			      event.preventDefault();
			      sqPaymentForm.requestCardNonce();
			    }
Javascript;
		$document->addScriptDeclaration(
			$scriptText
		);

		$cssCode = <<<CSS
			.sq-input {
		      border: 1px solid #CCCCCC;
		      margin-bottom: 10px;
		      padding: 3px 1px 7px 1px;
		      width: 210px;
		    }
		    .sq-input--focus {
		      outline-width: 5px;
		      outline-color: #70ACE9;
		      outline-offset: -1px;
		      outline-style: auto;
		    }
		    .sq-input--error {
		      outline-width: 5px;
		      outline-color: #FF9393;
		      outline-offset: 0px;
		      outline-style: auto;
		    }
CSS;
		$document->addStyleDeclaration($cssCode);

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
		require_once JPATH_ROOT . '/components/com_eventbooking/payments/squareupapi/vendor/autoload.php';

		$app    = JFactory::getApplication();
		$Itemid = $app->input->getInt('Itemid', 0);

		if (empty($data['nonce']))
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
			$data['nonce'],
			uniqid(),
			$body_amountMoney
		);

		$body->setAutocomplete(true);
		$body->setLocationId($this->params->get('location_id'));
		$body->setReferenceId($row->id);
		$body->setNote(substr($data['item_name'], 0, 60));

		try
		{
			$apiResponse = $paymentsApi->createPayment($body);

			if ($apiResponse->isSuccess())
			{
				$createPaymentResponse = $apiResponse->getResult();
				$this->onPaymentSuccess($row, $createPaymentResponse->getPayment()->getId());
				$app->redirect(JRoute::_('index.php?option=com_eventbooking&view=complete&Itemid=' . $Itemid, false, false));
			}
			else
			{
				$errors = $apiResponse->getErrors();

				$errorMessages = [];

				foreach ($errors as $error)
				{
					$errorMessages[] = $error->getDetail();
				}

				JFactory::getSession()->set('omnipay_payment_error_reason', implode("\r\n", $errorMessages));
				$app->redirect(JRoute::_('index.php?option=com_eventbooking&view=failure&id=' . $row->id . '&Itemid=' . $Itemid, false, false));
			}
		}
		catch (Exception $e)
		{
			JFactory::getSession()->set('omnipay_payment_error_reason', $e->getMessage());;
			$app->redirect(JRoute::_('index.php?option=com_eventbooking&view=failure&id=' . $row->id . '&Itemid=' . $Itemid, false, false));
		}
	}
}