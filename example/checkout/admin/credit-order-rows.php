<?php

require_once '../../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$testConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();

$sveaCheckoutOrderId = 201;

try {
    /**
     * @var \Svea\WebPay\BuildOrder\CreditOrderRowsBuilder $creditOrderRowsBuilder
     */
    $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows($testConfig)
        ->setCheckoutOrderId($sveaCheckoutOrderId)
        ->setDeliveryId(1);

    /**
     * $creditOrderRowsBuilder->setRowsToCredit(array(1, 2)) // If you want to credited more then one order Row
     * $creditOrderRowsBuilder->setRowToCredit(3)// Credit just one order Row
     *
     * For crediting NewOrderRow look at credit-new-order-row.php example
     */
    $creditOrderRowsBuilder->setRowsToCredit(array(3));

    $response = $creditOrderRowsBuilder->creditCheckoutOrderRows()->doRequest();

    var_dump($response);
} catch (\Exception $ex) {
    var_dump('Error message -> ' . $ex->getMessage());
    var_dump('Error code -> ' . $ex->getCode());
}
