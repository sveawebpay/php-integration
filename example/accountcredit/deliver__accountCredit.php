<?php

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Config\ConfigurationService;

error_reporting( E_ALL );
ini_set('display_errors', 'On');

$svea_order_id = 1045662;

$svea_delivery_request = \Svea\WebPay\WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
    ->setOrderId($svea_order_id)
    ->setOrderDate(date('c'))
    ->setCountryCode('SE')
    ->setInvoiceDistributionType(DistributionType::POST)
    ->deliverAccountCreditOrder()
    ->doRequest();

var_dump($svea_delivery_request);