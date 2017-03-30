<?php

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;

error_reporting( E_ALL );
ini_set('display_errors', 'On');

$svea_order_id = 1045661;

$svea_cancel_request = WebPayAdmin::cancelOrder(ConfigurationService::getDefaultConfig())
    ->setOrderId($svea_order_id)
    ->setCountryCode('SE')
    ->cancelAccountCreditOrder()
    ->doRequest();

var_dump($svea_cancel_request);