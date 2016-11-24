<?php

require_once '../../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$testConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();

$sveaCheckoutOrderId = 204;

// Credit amount
try {
    $response = WebPayAdmin::creditAmount($testConfig)
        ->setCheckoutOrderId($sveaCheckoutOrderId)
        ->setDeliveryId(1)
        ->setAmountIncVat(20.00)
        ->creditCheckoutAmount()
        ->doRequest();

    var_dump($response);
} catch (\Exception $ex) {
    var_dump('Error message -> ' . $ex->getMessage());
    var_dump('Error code -> ' . $ex->getCode());
}
