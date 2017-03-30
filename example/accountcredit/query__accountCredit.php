<?php

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;

error_reporting( E_ALL );
ini_set('display_errors', 'On');

$svea_order_id = 1048731;

$svea_query = WebPayAdmin::queryOrder(ConfigurationService::getTestConfig())
//$svea_query = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
    ->setOrderId($svea_order_id)
    ->setCountryCode('SE')
    ->queryAccountCreditOder()
    ->doRequest();

var_dump($svea_query);