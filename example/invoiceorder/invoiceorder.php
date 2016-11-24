<?php
/**
 * example file, how to create an invoice order request
 *
 * @author Kristian Grossman-madsen for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Config\ConfigurationService;

error_reporting(E_ALL);
ini_set('display_errors', 'On');


// get config object
$myConfig = ConfigurationService::getTestConfig(); // add your Svea credentials into config_prod.php or config_test.php file

// We assume that you've collected the following information about the order in your shop:

// customer information:
$customerFirstName = "Tess T";
$customerLastName = "Persson";
$customerAddress = "Testgatan 1";
$customerZipCode = "99999";
$customerCity = "Stan";
$customerCountry = "Sverige";

// The customer has bought three items, one "Billy" which cost 700,99 kr excluding vat (25%) and two hotdogs for 5 kr (incl. vat).

// We'll also need information about the customer country, and the currency used for this order, etc., see below

// Start the order creation process by creating the order builder object by calling Svea\WebPay\WebPay::createOrder():
$myOrder = WebPay::createOrder($myConfig);

// You then add information to the order object by using the methods in the Svea\WebPay\BuildOrder\CreateOrderBuilder class.
// For an Invoice order, the following methods are required:
$myOrder->setCountryCode("SE");                         // customer country, we recommend basing this on the customer billing address
//$myOrder->setCurrency("SEK");                           // order currency
// You may also chain fluent methods together:
$myOrder
//        ->setClientOrderNumber("order #20140519-375")   // optional - use a not previously sent client side order identifier, i.e. "order #20140519-371"
//        ->setCustomerReference("customer #123")         // optional - This should contain a customer reference, as in "customer #123".
    ->setOrderDate("2014-05-28")                    // required - or use an ISO8601 date as produced by i.e. date('c')
;
// Then specify the items bought as order rows, using the methods in the Svea\WebPay\BuildOrder\RowBuilders\OrderRow class, and adding them to the order:
$firstBoughtItem = WebPayItem::orderRow();
$firstBoughtItem->setAmountExVat(10.99);
$firstBoughtItem->setVatPercent(25);
$firstBoughtItem->setQuantity(1);
$firstBoughtItem->setDescription("Billy");
$firstBoughtItem->setArticleNumber("123456789A");

// Add firstBoughtItem to order row
$myOrder->addOrderRow($firstBoughtItem);

// Add secondBoughtItem in a fluent fashion 
$myOrder->addOrderRow(
    WebPayItem::orderRow()
        ->setAmountIncVat(5.00)
        ->setVatPercent(12)
        ->setQuantity(2)
        ->setDescription("Korv med bröd")
);

// Next, we create a customer identity object, note that for invoice orders Svea overrides any given address w/verified credit report address in the response.
$myCustomerInformation = WebPayItem::individualCustomer(); // there's also a ::companyCustomer() method, used for non-person entities
$myCustomerInformation->setNationalIdNumber(194605092222); // required for invoice orders, used to determine the invoice address, see Svea\WebPay\WebPay::getAddress()

// Also, for card orders addCustomerDetails() is optional, but recommended -- we'll just add what info we have, but do remember to check the response address!

$myCustomerInformation->setName($customerFirstName, $customerLastName);
$sveaAddress = Helper::splitStreetAddress($customerAddress); // Svea requires an address and a house number
$myCustomerInformation->setStreetAddress($sveaAddress[0], $sveaAddress[1]);
$myCustomerInformation->setZipCode($customerZipCode)->setLocality($customerCity);

$myOrder->addCustomerDetails($myCustomerInformation);

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select a payment method.
// We'll use the invoice order method.
$myInvoiceOrderRequest = $myOrder->useInvoicePayment();

// Then send the request to Svea, and immediately receive the service response object
$myResponse = $myInvoiceOrderRequest->doRequest();

// Check the response attribute 'accepted' for true to see if the request succeeded, if not, see the attributes resultcode and/or errormessage
echo "<pre>Your request response:\n\n";
print_r($myResponse);

echo "</pre><font color='blue'><pre>
An example of a successful request response. The 'accepted' attribute is true (1), and resultcode/errormessage is not set. 
(Note that the customerIdentity received in the response indicates the Svea invoice address, which should normally match the order shipping address.):

Svea\WebPay\WebService\WebServiceResponse\CreateOrderResponse Object
(
    [sveaOrderId] => 503312
    [sveaWillBuyOrder] => 1
    [amount] => 23.73
    [expirationDate] => 2015-03-10T00:00:00+01:00
    [clientOrderNumber] => 
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

    [accepted] => 1
    [errormessage] => 
    [resultcode] => 0
)
</pre>";

