<?php
/**
 * @package        Joomla
 * @subpackage     OS Services Booking
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class os_authnet extends OSBPaymentOmnipay
{
    protected $omnipayPackage = 'AuthorizeNet_AIM';

    /**
     * Constructor
     *
     * @param JRegistry $params
     * @param array     $config
     */
    public function __construct($params, $config = array('type' => 1))
    {
        $config['params_map'] = array(
            'apiLoginId'     => 'x_login',
            'transactionKey' => 'x_tran_key',
            'developerMode'  => 'authnet_mode',
        );

        parent::__construct($params, $config);
    }

    /**
     * Pass additional gateway data to payment gateway
     *
     * @param AbstractRequest $request
     * @param JTable          $row
     * @param array           $data
     */
    protected function beforeRequestSend($request, $row, $data)
    {
        require_once JPATH_ADMINISTRATOR.'/components/com_osservicesbooking/helpers/helper.php';
        $db     = JFactory::getDbo();
        $sid    = OSBHelper::checkOrderWithOneService($row->id);
        if($sid > 0)
        {
            $db->setQuery("Select authorize_api_login, authorize_transaction_key from #__app_sch_services where id = '$sid'");
            $authorize = $db->loadObject();
            if ($authorize->authorize_api_login && $authorize->authorize_transaction_key)
            {
                $request->setApiLoginId($authorize->authorize_api_login);
                $request->setTransactionKey($authorize->authorize_transaction_key);
            }
        }
        parent::beforeRequestSend($request, $row, $data);
    }
}
