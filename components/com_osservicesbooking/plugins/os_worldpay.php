<?php
/**
 * @package            Joomla
 * @subpackage         OS Services Booking
 * @author             Dang Thuc Dam
 * @copyright          Copyright (C) 2010 - 2016 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class os_worldpay extends OSBPaymentOmnipay
{
	protected $omnipayPackage = 'WorldPay';

	/**
	 * Constructor
	 *
	 * @param JRegistry $params
	 * @param array     $config
	 */
	public function __construct($params, $config = array())
	{
		$config['params_map'] = array(
			'installationId'   => 'wp_installation_id',
			'callbackPassword' => 'wp_callback_password',
			'testMode'         => 'worldpay_mode',
		);

		parent::__construct($params, $config);
	}
}
