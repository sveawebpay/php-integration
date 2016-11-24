<?php

require_once '../../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;

error_reporting(E_ALL);
ini_set('display_errors', 'On');


$testConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();

$sveaCheckoutOrderId = 51955;

// Update order rows
try {
    $response = WebPayAdmin::updateOrderRows($testConfig)
        ->setCheckoutOrderId($sveaCheckoutOrderId)
        ->updateOrderRow(
            WebPayItem::numberedOrderRow()
                ->setRowId(4)
                ->setName('someProd')
                ->setVatPercent(6)
                ->setDiscountPercent(50)
                ->setAmountIncVat(123.9876)
                ->setQuantity(4)
                ->setUnit('pc')
        )
        ->updateCheckoutOrderRows()
        ->doRequest();

    var_dump($response);
} catch (\Exception $ex) {
    var_dump('Error message -> ' . $ex->getMessage());
    var_dump('Error code -> ' . $ex->getCode());
}
