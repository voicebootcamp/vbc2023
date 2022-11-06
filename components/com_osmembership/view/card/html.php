<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class OSMembershipViewCardHtml extends MPFViewHtml
{
	/**
	 * Flag to mark this view does not have an associate model
	 *
	 * @var bool
	 */
	public $hasModel = false;

	/**
	 * Bootstrap Helper
	 *
	 * @var OSMembershipHelperBootstrap
	 */
	protected $bootstrapHelper;

	/**
	 * Contains select lists
	 *
	 * @var array
	 */
	protected $lists;

	/**
	 * Component config
	 *
	 * @var MPFConfig
	 */
	protected $config;

	/**
	 * Display the view
	 *
	 * @throws Exception
	 */
	public function display()
	{
		// Add necessary javascript files
		OSMembershipHelper::addLangLinkForAjax();
		$document = Factory::getDocument();
		$rootUri  = Uri::root(true);
		OSMembershipHelperJquery::loadjQuery();
		$document->addScript($rootUri . '/media/com_osmembership/assets/js/paymentmethods.min.js');

		$customJSFile = JPATH_ROOT . '/media/com_osmembership/assets/js/custom.js';

		if (file_exists($customJSFile) && filesize($customJSFile) > 0)
		{
			$document->addScript($rootUri . '/media/com_osmembership/assets/js/custom.js');
		}

		$config         = OSMembershipHelper::getConfig();
		$subscriptionId = $this->input->getString('subscription_id');
		$subscription   = OSMembershipHelperSubscription::getSubscription($subscriptionId);

		if (!$subscription)
		{
			throw new Exception(Text::sprintf('Subscription ID %s not found', $subscriptionId));
		}

		if ($subscription->payment_method)
		{
			$method = OSMembershipHelper::loadPaymentMethod($subscription->payment_method);
		}

		// Payment Methods parameters
		$currentYear        = date('Y');
		$expMonth           = $this->input->post->getInt('exp_month', date('n'));
		$expYear            = $this->input->post->getInt('exp_year', $currentYear);
		$lists['exp_month'] = HTMLHelper::_('select.integerlist', 1, 12, 1, 'exp_month', 'id="exp_month" class="input-medium form-select"', $expMonth, '%02d');
		$lists['exp_year']  = HTMLHelper::_('select.integerlist', $currentYear, $currentYear + 10, 1, 'exp_year', 'class="id="exp_year" input-medium form-select"', $expYear);

		$this->bootstrapHelper = OSMembershipHelperBootstrap::getInstance();
		$this->lists           = $lists;
		$this->subscription    = $subscription;
		$this->config          = $config;

		parent::display();
	}
}
