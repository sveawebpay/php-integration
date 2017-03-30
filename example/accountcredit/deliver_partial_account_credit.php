<?php

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Config\ConfigurationService;

error_reporting( E_ALL );
ini_set('display_errors', 'On');

$svea_order_id = 1048734;

$response = WebPayAdmin::deliverOrderRows(ConfigurationService::getDefaultConfig())
    ->setOrderId($svea_order_id)
    ->setRowsToDeliver(array(8, 9))
    ->setCountryCode('SE')
    ->setInvoiceDistributionType(DistributionType::POST)
    ->deliverAccountCreditOrderRows()
    ->doRequest();

var_dump($response);