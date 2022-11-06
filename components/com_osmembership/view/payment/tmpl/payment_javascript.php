<?php
/**
 * @package            Joomla
 * @subpackage         Membership Pro
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2012 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');

OSMembershipHelperPayments::writeJavascriptObjects();

if ($this->plan->price > 0 || $this->plan->setup_fee > 0 || $hasFeeFields)
{
	$paymentNeeded = true;
}
else
{
	$paymentNeeded = false;
}

Factory::getDocument()->addScriptOptions('hasStripePaymentMethod', $this->hasStripe)
	->addScriptOptions('paymentNeeded', $paymentNeeded)
	->addScriptOptions('selectedState', $selectedState)
	->addScriptOptions('currencyCode', $this->plan->currency ?: $this->config->currency_code)
	->addScript(Uri::root(true) . '/media/com_osmembership/js/site-payment-default.min.js');