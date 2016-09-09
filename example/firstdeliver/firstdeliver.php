<?php
/**
 * minimal deliver invoice order example
 *
 * @author Kristian Grossman-madsen for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

// get configuration object holding the Svea service login credentials
$myConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();

// We assume that you've previously run the firstorder.php file and successfully made a createOrder request to Svea using the invoice payment method.
// The svea order id returned in the request response was then written to the file sveaorderid.txt in the firstorder/ folder 

$mySveaOrderId = file_get_contents("../firstorder/sveaorderid.txt");
if (!$mySveaOrderId) {
    print_r("../firstorder/sveaorderid.txt not found, run firstorder.php first. aborting.");
    die();
}

// Begin the order creation process by creating an order builder object using the Svea\WebPay\WebPay::createOrder() method:
$myOrder = WebPay::deliverOrder($myConfig);

// We then add information to the order object by using the various methods in the Svea\WebPay\BuildOrder\DeliverOrderBuilder class.

// We begin by adding any additional information required by the payment method, which for an invoice order means:
$myOrder->setCountryCode("SE");
$myOrder->setOrderId($mySveaOrderId);
$myOrder->setInvoiceDistributionType(\Svea\WebPay\Constant\DistributionType::POST);

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select the invoice payment method:
$myDeliverOrderRequest = $myOrder->deliverInvoiceOrder();

// Then send the request to Svea using the doRequest method, and immediately receive the service response object
$myResponse = $myDeliverOrderRequest->doRequest();

// If the response attribute accepted is true, the order delivery succeeded.
echo "<pre>Your request response (the invoiceId attribute contains the id of the newly created invoice at Svea):\n\n";

print_r($myResponse);

echo "</pre><font color='blue'><pre>\n

An example of a successful request response. The 'accepted' attribute is true (1), and resultcode/errormessage is not set.

Svea\WebPay\AdminService\AdminServiceResponse\DeliverOrdersResponse Object
(
    [accepted] => 1
    [resultcode] => 0
    [errormessage] => 
    [amount] => 876.24
    [orderType] => Invoice
    [invoiceId] => 1028004
    [contractNumber] => 
)";

echo "</pre><font color='red'><pre>\n

An example of a rejected request response -- 'accepted' is false (0) and resultcode/errormessage indicates that the order has already been delivered (i.e. the order has status closed).   

Svea\WebPay\AdminService\AdminServiceResponse\DeliverOrdersResponse Object
(
    [accepted] => 0
    [resultcode] => 20000
    [errormessage] => Order is closed.
    [amount] => 
    [orderType] => 
    [invoiceId] => 
    [contractNumber] => 
)";

