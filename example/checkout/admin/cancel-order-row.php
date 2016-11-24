<?php

require_once '../../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;

error_reporting(E_ALL);
ini_set('display_errors', 'On');


$testConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();

$sveaCheckoutOrderId = 201;

// cancel order rows
try {
    $response = WebPayAdmin::cancelOrderRows($testConfig)
        ->setCheckoutOrderId($sveaCheckoutOrderId)
        ->setRowToCancel(1)
        ->cancelCheckoutOrderRows()
        ->doRequest();

    var_dump($response);
} catch (\Exception $ex) {
    var_dump('Error message -> ' . $ex->getMessage());
    var_dump('Error code -> ' . $ex->getCode());
}
