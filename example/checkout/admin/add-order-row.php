<?php

require_once '../../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$testConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();

$sveaCheckoutOrderId = 51955;

// Add order rows
try {
    $response = WebPayAdmin::addOrderRows($testConfig)
        ->setCheckoutOrderId($sveaCheckoutOrderId)
        ->addOrderRow(
            WebPayItem::orderRow()
                ->setArticleNumber('prod-01')
                ->setName('someProd1')
                ->setVatPercent(0)// required - 0, 6, 12, 25.
                ->setAmountIncVat(50.00)
                ->setQuantity(1)
                ->setUnit('pc')
        )
        ->addCheckoutOrderRows()
        ->doRequest();

    var_dump($response);
} catch (\Exception $ex) {
    var_dump('Error message -> ' . $ex->getMessage());
    var_dump('Error code -> ' . $ex->getCode());
}
