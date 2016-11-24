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
     * Create a new OrderRow for the credited amount and add it to the builder object using addCreditOrderRow():
     */
    $myCreditRow = WebPayItem::orderRow()
        ->setAmountIncVat(300.00)
        ->setVatPercent(25)
        ->setQuantity(1)
        ->setDescription("Credited order with new Order row");

    /**
     * If client wants to credit order with new order row, he can use code snippet above for that.
     */
    $creditOrderRowsBuilder->addCreditOrderRow($myCreditRow);


    /**
     * For crediting OrderRowIds look at credit-order-rows.php example
     */
    $response = $creditOrderRowsBuilder->creditCheckoutOrderWithNewOrderRow()->doRequest();

    var_dump($response);
} catch (\Exception $ex) {
    var_dump('Error message -> ' . $ex->getMessage());
    var_dump('Error code -> ' . $ex->getCode());
}
