<?php //
/**
 * example file, how to create an checkout order request
 *
 * @author Savo Garovic and Janko Stevanovic  for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Config\ConfigurationService;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

// get config object
$myConfig = ConfigurationService::getTestConfig();

$orderBuilder = WebPay::checkout($myConfig);

$orderBuilder->setCheckoutOrderId(7479)
    //->setMerchantData("merchantData")
    ->setCountryCode('SE');

// create and add items to order
$firstBoughtItem = WebPayItem::orderRow()
    ->setAmountIncVat(10.99)
    ->setVatPercent(25)
//    ->setAmountExVat(12.32)   // - this action is not allowed for checkout
    ->setQuantity(1)
    ->setDescription("Billy")
    ->setArticleNumber("123456789A")
    ->setName('Fork');

$secondBoughtItem = WebPayItem::orderRow()
    ->setAmountIncVat(5.00)
    ->setVatPercent(12)
//    ->setAmountExVat(12.32)   // - this action is not allowed for checkout
    ->setQuantity(2)
    ->setDescription("Korv med bröd")
    ->setArticleNumber("123456789B")
    ->setName('Fork');

$orderBuilder->addOrderRow($firstBoughtItem);
$orderBuilder->addOrderRow($secondBoughtItem);

try {
    $response = $orderBuilder->updateOrder();
    print_r($response);
} catch (\Exception $e) {
    print_r($e->getMessage());
}

