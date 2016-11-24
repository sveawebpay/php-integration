<?php
/**
 * example file, how to create a recurring card order request
 *
 * @author Kristian Grossman-madsen for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;

error_reporting( E_ALL );
ini_set('display_errors', 'On');


// get config object
$myConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig(); // add your Svea credentials into config_prod.php or config_test.php file

// Start the order creation process by creating the order builder object by calling Svea\WebPay\WebPay::createOrder():
$myOrder = WebPay::createOrder( $myConfig );

// You then add information to the order object by using the methods in the Svea\WebPay\BuildOrder\CreateOrderBuilder class.
// For a Card order, the following methods are required:
$myOrder->setCountryCode("SE");                         // customer country, we recommend basing this on the customer billing address
$myOrder->setCurrency("SEK");                           // order currency
$myOrder->setClientOrderNumber( "order #".date('c') );  // required - use a not previously sent client side order identifier, i.e. "order #20140519-371"

// Add order item in a fluent fashion
$myOrder->addOrderRow(
            WebPayItem::orderRow()
                ->setAmountExVat( 100.00 )
                ->setVatPercent( 25 )
                ->setQuantity( 1 )
                ->setDescription( "Månadsavgift" )
);

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select a payment method.
// For card orders, we recommend using the ->usePaymentMethod(Svea\WebPay\Constant\PaymentMethod::SVEACARDPAY), which processes card orders via SveaWebPay.
$myCardOrderRequest = $myOrder->usePaymentMethod(PaymentMethod::SVEACARDPAY);

// For recurring card payments, use setSubscriptionType() on the request object, one of the subscription types from HostedService\HostedPayment
$myCardOrderRequest->setSubscriptionType(\Svea\WebPay\HostedService\Payment\HostedPayment::RECURRINGCAPTURE);

// Then set any additional required request attributes as detailed below. (See Svea\PaymentMethodPayment and Svea\HostedPayment classes for details.)
$myCardOrderRequest
    ->setCardPageLanguage("SV")                                     // ISO639 language code, i.e. "SV", "EN" etc. Defaults to English.
    ->setReturnUrl("http://localhost/".getPath()."/landingpage_recur.php"); // The return url where we receive and process the finished request response

// Get a payment form object which we can use to send the payment request to Svea
$myCardOrderPaymentForm = $myCardOrderRequest->getPaymentForm();

// Then send the form to Svea, and receive the response on the landingpage after the customer has completed the card checkout at SveaCardPay
echo "<pre>";
echo "Press submit to send the inital card order request to Svea, and receive a subscription id for use in future recur order requests.";
print_r( $myCardOrderPaymentForm->completeHtmlFormWithSubmitButton );

/**
 * get the path to this file, for use in specifying the returnurl etc.
 */
function getPath() {
    $myURL = $_SERVER['SCRIPT_NAME'];
    $myPath = explode('/', $myURL);
    unset( $myPath[count($myPath)-1]);
    $myPath = implode( '/', $myPath);

    return $myPath;
}

?>
