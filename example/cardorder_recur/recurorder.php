<?php
/**
 * example file, how to create a recurring card order request
 *
 * @author Kristian Grossman-madsen for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;

error_reporting(E_ALL);
ini_set('display_errors', 'On');


// get config object
$myConfig = ConfigurationService::getTestConfig(); // add your Svea credentials into config_prod.php or config_test.php file

// Start the order creation process by creating the order builder object by calling Svea\WebPay\WebPay::createOrder():
$myOrder = WebPay::createOrder($myConfig);

// You then add information to the order object by using the methods in the Svea\CreateOrderBuilder class.
// For a Card order, the following methods are required:
$myOrder->setCountryCode("SE");                         // customer country, we recommend basing this on the customer billing address
$myOrder->setCurrency("SEK");                           // order currency
$myOrder->setClientOrderNumber("order #" . date('c'));  // required - use a not previously sent client side order identifier, i.e. "order #20140519-371"

// Add order item in a fluent fashion
$myOrder->addOrderRow(
    WebPayItem::orderRow()
        ->setAmountExVat(100.00)
        ->setVatPercent(25)
        ->setQuantity(1)
        ->setDescription("Monthly recurring fee")
);

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select a payment method.
$myRecurOrderRequest = $myOrder->usePaymentMethod(PaymentMethod::SVEACARDPAY);

// We now need to setSubscriptionId() on the request object, using the subscription id from the initial request response
$mySubscriptionId = file_get_contents("subscription.txt");

if ($mySubscriptionId)
{
    $myRecurOrderRequest->setSubscriptionId($mySubscriptionId);
}
else // or, abort if subscription.txt is missing
{
    echo "<pre>Error: subscription.txt not found, first run cardorder_recur.php to set up the card order subscription. aborting.";
    die;
}

// Send the recur payment request to Svea
$myRecurOrderResponse = $myRecurOrderRequest->doRecur();

echo "<pre>";
print_r("the recur order response");
print_r($myRecurOrderResponse);

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

