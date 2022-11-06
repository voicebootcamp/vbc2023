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

/**
 * Eway payment class
 */
class os_eway extends OSBPaymentOmnipay
{
    protected $omnipayPackage = 'Eway_Direct';

    /**
     * Constructor
     *
     * @param JRegistry $params
     * @param array     $config
     */
    public function __construct($params, $config = array('type' => 1))
    {
        $config['params_map'] = array(
            'customerId'        => 'eway_customer_id',
            'testMode'          => 'eway_mode',
        );

        parent::__construct($params, $config);
    }
}
