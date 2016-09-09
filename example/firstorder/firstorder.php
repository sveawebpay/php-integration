<?php
/**
 * minimal invoice payment example
 *
 * @author Kristian Grossman-madsen for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Config\ConfigurationService;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

// get configuration object holding the Svea service login credentials
$myConfig = ConfigurationService::getTestConfig();

// We assume that you've collected the following information about the order in your shop: 
// The shop cart contains one item "Billy" which cost 700,99 kr excluding vat (25%).
// When selecting to pay using the invoice payment method, the customer has also provided their social security number, which is required for invoice orders.

// Begin the order creation process by creating an order builder object using the Svea\WebPay\WebPay::createOrder() method:
$myOrder = WebPay::createOrder($myConfig);

// We then add information to the order object by using the various methods in the Svea\CreateOrderBuilder class.

// We begin by adding any additional information required by the payment method, which for an invoice order means:
$myOrder->setCountryCode("SE");
$myOrder->setOrderDate(date('c'));

// To add the cart contents to the order we first create and specify a new orderRow item using methods from the Svea\WebPay\BuildOrder\RowBuilders\OrderRow class:
$boughtItem = WebPayItem::orderRow();
$boughtItem->setDescription("Billy");
$boughtItem->setAmountExVat(700.99);
$boughtItem->setVatPercent(25);
$boughtItem->setQuantity(1);

// Add the order rows to the order: 
$myOrder->addOrderRow($boughtItem);

// Next, we create a customer identity object, for invoice orders Svea will look up the customer address et al based on the social security number
$customerInformation = WebPayItem::individualCustomer();
$customerInformation->setNationalIdNumber("194605092222");

// Add the customer to the order: 
$myOrder->addCustomerDetails($customerInformation);

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select the invoice payment method:
$myInvoiceOrderRequest = $myOrder->useInvoicePayment();

// Then send the request to Svea using the doRequest method, and immediately receive a service response object back
$myResponse = $myInvoiceOrderRequest->doRequest();

// Check the response attribute 'accepted' for true to see if the request succeeded, if not, see the attributes resultcode and/or errormessage
if ($myResponse->accepted == true) {
    // save the sveaOrderId to a file, for use in firstdeliver.php
    $myFirstOrderId = $myResponse->sveaOrderId;
    file_put_contents("sveaorderid.txt", $myFirstOrderId);
};

echo "<pre>Your request response (the customerIdentity contains the verified invoice address, which should match the order shipping address used):\n\n";

print_r($myResponse);

echo "</pre><font color='blue'><pre>\n

An example of a successful request response. The 'accepted' attribute is true (1), and resultcode/errormessage is not set.

Svea\WebPay\WebService\WebServiceResponse\CreateOrderResponse Object
(
    [sveaOrderId] => 362168
    [sveaWillBuyOrder] => 1
    [amount] => 876.24
    [expirationDate] => 2014-08-16T00:00:00+02:00
    [accepted] => 1
    [errormessage] => 
    [resultcode] => 0
    [orderType] => Invoice
    [customerIdentity] => Svea\WebPay\WebService\WebServiceResponse\CustomerIdentity\CreateOrderIdentity Object
        (
            [email] => 
            [ipAddress] => 
            [countryCode] => SE
            [houseNumber] => 
            [customerType] => Individual
            [nationalIdNumber] => 194605092222
            [phoneNumber] => 
            [fullName] => Persson, Tess T
            [street] => Testgatan 1
            [coAddress] => c/o Eriksson, Erik
            [zipCode] => 99999
            [locality] => Stan
        )

)";

