<?php
/**
 * @version            2.3.0
 * @package            Joomla
 * @subpackage         OS Services Booking
 * @author             Dang Thuc Dam
 * @copyright          Copyright (C) 2010 - 2016 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class os_paypal_pro extends OSBPaymentOmnipay
{
    protected $omnipayPackage = 'PayPal_Pro';

    /**
     * Constructor
     *
     * @param JRegistry $params
     * @param array     $config
     */
    public function __construct($params, $config = array('type' => 1))
    {
        $config['params_map'] = array(
            'username'  => 'api_username',
            'password'  => 'api_password',
            'signature' => 'api_signature',
            'testMode'  => 'paypal_pro_mode',
        );

        parent::__construct($params, $config);
    }
}
