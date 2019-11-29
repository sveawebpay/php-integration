<?php
/**
 * Example of Swish payment
 *
 * @author Fredrik Sundell / fre-sund
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;

error_reporting(E_ALL);
ini_set('display_errors', 'On');


// get config object
$myConfig = ConfigurationService::getTestConfig(); // add your Svea credentials into config_prod.php or config_test.php file

try // Handle validation exceptions by printing errors if we're unable to create the order
{
    // Start the order creation process by creating the order builder object by calling Svea\WebPay\WebPay::createOrder():
    $myOrder = WebPay::createOrder($myConfig);

    // You then add information to the order object by using the methods in the Svea\WebPay\BuildOrder\CreateOrderBuilder class.
    // For a Card order, the following methods are required:
    $myOrder->setCurrency("SEK");                               // order currency
    $myOrder->setClientOrderNumber("order #" . date('c'));  // required - use a not previously sent client side order identifier, i.e. "order #20140519-371"

    // You may also chain fluent methods together:
    $myOrder
        ->setCustomerReference("customer #123")         // optional - This should contain a customer reference, as in "customer #123".
        ->setOrderDate("2019-11-29")                           // optional - or use an ISO8601 date as produced by i.e. date('c')
        ->setPayerAlias("46707937643")                                 // required for Swish payments, ignored otherwise;
        ->setCountryCode("SE");                              // countryCode "SE" is required for Swish payments


    // Then specify the items bought as order rows, using the methods in the Svea\WebPay\BuildOrder\RowBuilders\OrderRow class, and adding them to the order:
    $firstBoughtItem = WebPayItem::orderRow();
    $firstBoughtItem->setAmountExVat(1.00);
    $firstBoughtItem->setVatPercent(25);
    $firstBoughtItem->setQuantity(1);
    $firstBoughtItem->setDescription("Yellow duck");
    $firstBoughtItem->setArticleNumber("yel-duck-01");

    // Add firstBoughtItem to order row
    $myOrder->addOrderRow($firstBoughtItem);

    // Add secondBoughtItem in a fluent fashion
    $myOrder->addOrderRow(
        WebPayItem::orderRow()
            ->setAmountIncVat(2.50)
            ->setVatPercent(12)
            ->setQuantity(1)
            ->setDescription("Blue duck")
    );

    // Add Swish as the payment method for the order
    $mySwishOrderRequest = $myOrder->usePaymentMethod(PaymentMethod::SWISH);


    // Then set any additional required request attributes as detailed below. (See Svea\PaymentMethodPayment and Svea\HostedPayment classes for details.)
    $mySwishOrderRequest
        ->setReturnUrl("http://localhost/" . getPath() . "/landingpage.php"); // The return url where we receive and process the finished request response

    // Get a payment form object which you can use to send the payment request to Svea
    $mySwishOrderRequest = $mySwishOrderRequest->getPaymentForm();

    // Then send the form to Svea, and receive the response on the landingpage after the customer has completed the card checkout SveaCardPay
    echo "<pre>";
    print_r("press submit to send the swish payment request to Svea");
    print_r($mySwishOrderRequest->completeHtmlFormWithSubmitButton);
}
catch (Exception $exception)
{
    echo $exception->getMessage();
}
/**
 * get the path to this file, for use in specifying the returnurl etc.
 */
function getPath()
{
    $myURL = $_SERVER['SCRIPT_NAME'];
    $myPath = explode('/', $myURL);
    unset($myPath[count($myPath) - 1]);
    $myPath = implode('/', $myPath);

    return $myPath;
}


