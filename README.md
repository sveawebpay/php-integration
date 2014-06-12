# PHP Integration Package API for SveaWebPay

## Version 2.0.0
The Svea WebPay Integration package uses semantic versioning (http://semver.org). This means that you can expect your integrations to remain backwards compatible during a major version release cycle. Previous versions of the package can be accessed through the github releases view (https://github.com/sveawebpay/php-integration/releases).

### Current build status
| Branch                            | Build status                               |
|---------------------------------- |------------------------------------------- |
| master (latest release)           | [![Build Status](https://travis-ci.org/sveawebpay/php-integration.png?branch=master)](https://travis-ci.org/sveawebpay/php-integration) |
| develop                           | [![Build Status](https://travis-ci.org/sveawebpay/php-integration.png?branch=develop)](https://travis-ci.org/sveawebpay/php-integration) |

## Index
* [Introduction](https://github.com/sveawebpay/php-integration#introduction)
* [Configuration](https://github.com/sveawebpay/php-integration#configuration)
* [1. CreateOrder](https://github.com/sveawebpay/php-integration#1-createorder)
    * [Specify order](https://github.com/sveawebpay/php-integration#12-specify-order)
    * [Customer identity](https://github.com/sveawebpay/php-integration#13-customer-identity)
    * [Other values](https://github.com/sveawebpay/php-integration#14-other-values)
    * [Choose payment](https://github.com/sveawebpay/php-integration#15-choose-payment)
* [2. GetPaymentPlanParams](https://github.com/sveawebpay/php-integration#2-getpaymentplanparams)
    *  [PaymentPlanPricePerMonth](https://github.com/sveawebpay/php-integration#21-paymentplanpricepermonth)
* [3. GetAddresses](https://github.com/sveawebpay/php-integration#3-getaddresses)
* [4. DeliverOrder](https://github.com/sveawebpay/php-integration#4-deliverorder)
    * [Specify order](https://github.com/sveawebpay/php-integration#42-specify-order)
    * [Other values](https://github.com/sveawebpay/php-integration#43-other-values)
* [5. CreditInvoice](https://github.com/sveawebpay/php-integration#5-creditInvoice)
* [6. CloseOrder](https://github.com/sveawebpay/php-integration#6-closeorder)
* [7. Response handler](https://github.com/sveawebpay/php-integration#7-response-handler)
* [8. GetPaymentMethods](https://github.com/sveawebpay/php-integration#8-getpaymentmethods)
* [9. AdditionalDeveloperResources](https://github.com/sveawebpay/php-integration#9-additional-developer-resources)
* [APPENDIX](https://github.com/sveawebpay/php-integration#appendix)


## Introduction
The Svea WebPay integration package is built to help developers in the integration of the various Svea payment services. Using the integration package will aid in making your implementation maintainable and simplifies the way you consume the Svea services. 

The Svea WebPay integration package provides a set of entrypoint classes and methods that unify how the various Svea payment services are accessed. 

Use the WebPay class methods to implement web payments using Svea services. The WebPayAdmin class can then be used to administer the orders placed using Svea payment services from within your integration. 

The package also provides service request classes to use the Svea services directly, without needing to build well-formed request data et al.

### Package design philosophy:
In general, a service request starts out with creating an order object using one of the WebPay or WebPayAdmin class entrypoint methods. 

The order is then built up with data using fluid method calls on the order object. At a certain point, a method is used to select 
which service the request will be directed against. This method then returns an object of a different class, which handles the actual 
request to the service chosen. 

An example of this workflow is the following invoice payment, where we wish to perform an invoice order. Assume that we have already 
prepared objects containing the ordered items (with price, article number info, et al) and customer information (name, address, et al), 
along with our Svea account credentials.

```php
...

$order = WebPay::createOrder( $myConfig );  // create the order object, passing in my Svea account credentials

$order->addOrderRow( $orderedItem );        // add order items objects to the order (see below for details)
$order->addOrderRow( $anotherItem ); 

$order->addCustomerDetails( $customer );    // add customer information object to the order (see below for details)

$order->setOrderDate("2014-06-10")          // add various additional required information to the order object
      ->setCountryCode("SE")                // here, methods are chained together in a fluid fashion            
      ->setCustomerReference("1701")
      ->setClientOrderNumber("");

$request = $order->useInvoicePayment()      // we wish to place the order using the Svea Invoice payment method

$response = $request->doRequest();          // send the request to Svea and receive a response object

...
```

Above, we start out by calling the API method WebPay::createOrder(), which returns an instance of the CreateOrderBuilder class. 

Then, the class methods addOrderRow(), addCustomerDetails(), setOrderDate(), setCountryCode(), setCustomerReference(), and setClientOrderNumber() 
are used to populate the orderbuilder object with all required order information needed for an invoice order.

Then, the useInvoicePayment() method is called, returning an instance of the WebService\InvoicePayment class. We then call the doRequest() method, 
which validates the provided order information, and makes the request to Svea, returning an instance of the WebService\CreateOrderResponse class.

To determine the outcome of the payment request, we can then inspect the response attributes, i.e. check if( $response->accepted == true ).

### Bypassing the WebPay and WebPayAdmin entrypoint methods
The above structure enables the WebPay and WebPayAdmin entrypoint methods to confine themselves to the order domain, and pushes the various service 
request details lower into the package stack, away from the immediate viewpoint of the integrator. Thus all payment methods and services are accessed 
in a uniform way, with the package doing the main work of massaging the order data to fit the selected payment method or service request. 

This also provides future compatibility, as the main WebPay and WebPayAdmin entrypoint methods stay stable whereas the details of how the services
are bering called by the package may change in the future.

That being said, there are no additional prohibitions on using the various service call wrapper classes to access the Svea services directly, while
still not having to worry about the details on how to i.e. build the various SOAP calls or format the XML data structures.

It is therefore possible to instantiate the service request classes directly, making sure to set all relevant methods before finishing with a method 
to perform the request to the service. In general, we validate that all required attributes are present, and if not, an exception 
will be thrown stating what attributes are missing for the service request in question. 

See further the Svea, Svea\WebService, Svea\AdminService and Svea\HostedService namespaces for further information. All service classes are documented by generated documentation included in the apidoc folder. 

### Namespaces
The package makes use of PHP namespaces, grouping most classes under the namespace Svea. The entrypoint classes WebPay, WebPayAdmin and associated support classes are excluded from the Svea namespace. See the generated documentation for available classes and methods.

For compatibility with existing integrations built with version 1.x. of the package, we have provided a file "_NamespaceSvea.php" which may be included,
which maps references to the classes now sorted under Svea\WebService and Svea\HostedService to their old location in the Svea namespace. TODO

See the PHP documentation for more information on [namespaces in PHP](http://php.net/manual/en/language.namespaces.php).

### Fluidity
The WebPay and WebPayAdmin entrypoint methods are built as a fluent API so you can use method chaining when implementing it in your code. We recommend making sure that your IDE code completion of available integration package methods is enabled to make full use of this feature.

### Development environment
The Svea WebPay PHP integration package is developed and tested using NetBeans IDE 7.3.1 with the phpunit 3.7.24 plugin.

## Installing

### Requirements
The integration package requires PHP 5.3 or higher to use. 

To run the package test suite, phpunit 3.7 is needed. To regenerate the apidoc documentation, phpdocumentor 2.3 or higher is needed.

### Package installation
The integration package files are located under the src/ folder. Copy the contents of the src/ folder to a folder in your project, we suggest the name "svea".
Then include the package file *Includes.php* in your integration. You should now be able to access the package classes.

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## Configuration
In order to make use of the Svea services you need to supply your account credentials to authorize yourself against the Svea services. For the Invoice and Payment Plan payment methods, the credentials consist of a set of Username, Password and Client number (one set for each country and service type). For Card and Direct Bank payment methods, the credentials consist of a (single) set of Merchant id and Secret Word.

You should have received the above credentials from Svea when creating a service account. If not, please contact your Svea account manager.

### Using your account credentials with the package
The WebPay and WebPayAdmin entrypoint methods all require a config object when called. The easiest way to get such an object is to use the SveaConfig::getDefaultConfig() method. Per default, it returns a config object with the Svea test account credentials as used by the integration package test suite. 

In order to use your own account credentials, either edit the SveaConfig.php file with your actual account credentials, or implement the ConfigurationProvider interface in a class of your own -- your implementation could for instance fetch the needed credentials from a database in place of the SveaConfig.php file.

See further the ConfigurationProvider interface and the SveaConfig.php file.

See also the provided example MyConfig.php under example/config_getaddresses for an example of a customised SveaConfig.php file and how to use it.

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)


## WebPayAdmin entrypoint methods
The WebPayAdmin:: methods are used to administrate orders after they have been accepted by Svea. They include methods to update, deliver, cancel and credit
orders.


WebPayAdmin:: 
  cancelOrder -- cancel non-delivered invoice or payment plan orders, or annul non-confirmed card orders
  **creditOrder -- credit delivered invoice or payment plan orders, or credit confirmed card orders
  **updateOrder -- change order row contents of a non-delivered invoice or payment plan order, or lower amount of non-confirmed card orders
  **queryOrder -- get information about an order
  **listPaymentMethods -- WPA equivalent of WP::getPaymentMethods

The following methods are provided in WebPayAdmin as a stopgap measure to perform administrative functions for card orders.
These entrypoints will be removed from the package in the 2.0 release, but will still be available in the Svea namespace.

WebPay: 
  createOrder creates BuildOrder/orderBuilder objects containing order data
    -- useInvoicePayment creates an instance of WebService/Payment/InvoicePayment which does request to Svea Europe Web Service SOAP service
    -- useCardPayment creates and instance of HostedRequest/Payment/CardPayment which returns the xml request to send to the SveaWebPay service 
WebPayAdmin:
  cancelOrder creates a BuildOrder/cancelOrderBuilder object populated with data about the order to cancel
    -- cancelInvoiceOrder creates an instance of WebService/HandleOrder/CloseOrder
    -- cancelCardOrder creates an instance of HostedRequests/HandleOrder/AnnulTransaction

COMPATIBILTIY:
To create and administrate orders the WebPay class functions remain compatible
with 1.x of the integration package. Some methods have been marked as 
deprecated and/or moved into the new WebPayAdmin class. These will remain for
now, but new integrations are naturally advised to avoid using them. Alternate
methods are provided for most.
```


##  WebPay class methods

The WebPay:: class methods contains the functions needed to create orders and
perform payment requests using Svea payment methods. It contains methods to
define order contents, send order requests, as well as support methods 
needed to do this.

###WebPay method overview
(methods in parenthesises are deprecated), *starred methods are new to 2.0, **double starred methods are not yet implemented in release 2.0b

*  createOrder  -- create order and pay via invoice, payment plan, card, or direct bank payment methods
*  deliverOrder (with orderRows)-- partially deliver, change or credit invoice, payment plan orders depending on set options
*  *deliverOrder (without orderRows) -- deliver in full invoice, payment plan orders, confirms card orders 
*  (closeOrder) -- cancel non-delivered invoice or payment plan
*  getAddresses -- fetch addresses connected with a provided customer identity
*  getPaymentMethods -- fetch available payment methods for a clientid, used by i.e. direct bank orders
*  getPaymentPlanParams -- fetch current campaigns (payment plan params) for a clientid, used by paymentplan orders
*  getPaymentPlanPricePerMonth -- calculates price per month over all available campaigns for a specified amount 

The underlying services and methods are contained in the Svea namespace, and may be accessed, though they may be subject to change.

## 1.1 WebPay::createOrder()
createOrder() -- create order and pay via invoice, payment plan, card, or direct bank payment methods.

The following is a minimal example of how to place an order using the invoice payment method:

```php
<?php
// include the Svea PHP integration package files
require( "Includes.php" ); 

// get configuration object holding the Svea service login credentials
$myConfig = Svea\SveaConfig::getTestConfig(); 

// We assume that you've collected the following information about the order in your shop: 
// The shop cart contains one item "Billy" which cost 700,99 kr excluding vat (25%).
// When selecting to pay using the invoice payment method, the customer has also provided their social security number, which is required for invoice orders.

// Begin the order creation process by creating an order builder object using the WebPay::createOrder() method:
$myOrder = WebPay::createOrder( $myConfig );

// We then add information to the order object by using the various methods in the Svea\CreateOrderBuilder class.

// We begin by adding any additional information required by the payment method, which for an invoice order means:
$myOrder->setCountryCode("SE");                         
$myOrder->setOrderDate( date('c') );

// To add the cart contents to the order we first create and specify a new orderRow item using methods from the Svea\OrderRow class:
$boughtItem = WebPayItem::orderRow();
$boughtItem->setDescription( "Billy" );
$boughtItem->setAmountExVat( 700.99 );
$boughtItem->setVatPercent( 25 );
$boughtItem->setQuantity( 1 );

// Add the order rows to the order: 
$myOrder->addOrderRow( $boughtItem ); 

// Next, we create a customer identity object, for invoice orders Svea will look up the customer address et al based on the social security number
$customerInformation = WebPayItem::individualCustomer();
$customerInformation->setNationalIdNumber("194605092222");

// Add the customer to the order: 
$myOrder->addCustomerDetails( $myCustomerInformation );

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select the invoice payment method:
$myInvoiceOrderRequest = $myOrder->useInvoicePayment();

// Then send the request to Svea using the doRequest method, and immediately receive the service response object
$myResponse = $myInvoiceOrderRequest->doRequest();

// If the response attribute accepted is true, the payment succeeded.
if( $myResponse->accepted == true ) { echo "invoice payment succeeded"; };

// The response also contains a customerIdentity object containing the invoice address of the customer, which should match the order shipping address.
print_r( $myResponse->customerIdentity );
?>
```

The above example can be found in the examples/firstinvoice folder.

Another complete, runnable example of a synchronous (invoice) order can be found in the examples/invoiceorder folder.

Another complete, runnable example of an asynchronous (card) order can be found in the examples/cardorder folder. 

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 1.2 Specify order items
Order row, fee and discount items are added to the order. Together they add up the order total amount. 

Use the WebPayItem class methods to instantiate and specify row items. Then add the items to the order object.

The following is code excerpt example of how to instantiate and add the an order row item to an order:

```php
...
$myOrderRow = WebPayItem::orderRow();       // create the order row object

$myOrderRow->setQuantity(1);                // required
$myOrderRow->setAmountExVat(10.00)          // recommended to specify price using AmountExVat & VatPercent
$myOrderRow->setVatPercent(12)              // recommended to specify price using AmountExVat & VatPercent

$myOrder->addOrderRow( $myOrderRow );       // add order row to the order
...

/* the same code expressed in a more fluent style:
$myOrder
    ->addOrderRow( 
        WebPayItem::orderRow()
            ->setQuantity(1)
            ->setAmountExVat(10.00)
            ->setVatPercent(12) 
    )
;
*/

/* or, still fluent, but more compact: 
$myOrder->addOrderRow( WebPayItem::orderRow()->setQuantity(1)->setAmountExVat(10.00)->setVatPercent(12) );
*/
```

You can also add an array of several order row items at once using the addOrderRows() method.

```php
...
$orderRows = array( WebPayItem::orderRow()->setXX()->setXX()->setXX(), WebPayItem::orderRow()->setXX()->setXX()->setXX() );
$order->addOrderRow($orderRows);
...
```

You can add row items of the classes OrderRow, ShippingFee, InvoiceFee, FixedDiscount and RelativeDiscount to the order. 

### 1.2.1 OrderRow row item class
Use the OrderRow class for all kinds of products and other items. It is required to have a minimum of one order row.

Specify the price using precisely two of these methods in order to specify the item price and tax rate: 
setAmountExVat(), setAmountIncVat() and setVatPercent().

We recommend specifying price using setAmountExVat() and setVatPercentage(). If not, make sure not retain as much precision as
possible, i.e. use no premature rounding (87.4875 is a "better" PriceIncVat than 87.49).

If you use setAmountIncVat(), note that this may introduce a cumulative rounding error when ordering large
quantities of an item, as the package bases the total order sum on a calculated price ex. vat.

```php
...
$order->
    addOrderRow(
        WebPayItem::orderRow()
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setAmountIncVat(125.00)               // optional, need to use two out of three of the price specification methods
            ->setQuantity(2)                        // required
            ->setUnit("st")                         // optional
            ->setName('Prod')                       // optional
            ->setDescription("Specification")       // optional
            ->setArticleNumber("1")                 // optional
            ->setDiscountPercent(0)                 // optional
    )
;
...
```

### 1.2.2 ShippingFee row item class
Use this class to add shipping to the order.

Specify the price using precisely two of these methods in order to specify the item price and tax rate: 
setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend specifying price using setAmountExVat() and setVatPercentage().

```php
...
$order->
    addFee(
        WebPayItem::shippingFee()
            ->setShippingId('33')                   // optional
            ->setName('shipping')                   // optional
            ->setDescription("Specification")       // optional
            ->setAmountExVat(50)                    // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setAmountIncVat(62.50)                // optional, need to use two out of three of the price specification methods
            ->setUnit("st")                         // optional
            ->setDiscountPercent(0)                 // optional
    )
;
...
```

### 1.2.3 InvoiceFee row item class
Use this class to add fees associated with a payment method (i.e. invoice fee) to the order.

Specify the price using precisely two of these methods in order to specify the item price and tax rate: 
setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend specifying price using setAmountExVat() and setVatPercentage().

```php
...
$order->
    addFee(
        WebPayItem::invoiceFee()
            ->setName('Svea fee')                   // optional
            ->setDescription("Fee for invoice")     // optional
            ->setAmountExVat(50)                    // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setAmountIncVat(62.50)                // optional, need to use two out of three of the price specification methods
            ->setUnit("st")                         // optional
            ->setDiscountPercent(0)                 // optional
    )
...
```

### 1.2.4 FixedDiscount row item class
Use this class when the discount or coupon is expressed as a percentage of the total product amount.

If only AmountIncVat is given, we calculate the discount split across the tax (vat) rates present in the order. This will
ensure that the correct discount vat is applied to the order.

Otherwise, it is required to use at least two of the functions setAmountExVat(), setAmountIncVat() and setVatPercent().
If two of these three attributes are specified, we respect the amount indicated and include a discount with the appropriate tax rate.

```php
...
$order->
    addDiscount(
        WebPayItem::fixedDiscount()
            ->setAmountIncVat(100.00)               // recommended, see info above
            ->setAmountExVat(1.0)                   // optional, see info above
            ->setVatPercent(25)                     // optional, see info above
            ->setDiscountId("1")                    // optional
            ->setUnit("st")                         // optional
            ->setDescription("FixedDiscount")       // optional
            ->setName("Fixed")                      // optional
    )
;
...
```

### 1.2.5 RelativeDiscount row item class
Use this class when the discount or coupon is expressed as a percentage of the total product amount.

```php
...
$order->
    addDiscount(
        WebPayItem::relativeDiscount()
        ->setDiscountPercent(50.5)              // required
        ->setDiscountId("1")                    // optional
        ->setUnit("st")                         // optional
        ->setName('Relative')                   // optional
        ->setDescription("RelativeDiscount")    // optional
    )
;
...
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 1.3 Customer Identity
Create a customer identity object using the WebPayItem::individualCustomer() or WebPayItem::companyCustomer() methods. Use the addCustomerDetails() method to add the customer information to the order. 

Set customer identity attributes using the setXX() customer class methods, respectively. Required attributes varies depending on country and customer type, as well as payment method chosen. See below for an overview and usage examples.

Adding a customer identity to the order is required for Invoice and Payment plan orders. For Card and Direct bank orders it is optional but recommended.

### 1.3.1 IndividualCustomer class
Read "required" below as a requirement when the IndividualCustomer is used to identify the customer when using the invoice or payment plan payment methods. 
(For card and direct bank orders, adding customer information to the order is optional.)

```php
...
$order->
    addCustomerDetails(
        WebPayItem::individualCustomer()
            ->setNationalIdNumber(194605092222) // required for individual customers in SE, NO, DK, FI
            ->setInitials("SB")                 // required for individual customers in NL
            ->setBirthDate(1923, 12, 20)        // required for individual customers in NL and DE
            ->setName("Tess", "Testson")        // required for individual customers in NL and DE
            ->setStreetAddress("Gatan", 23)     // required in NL and DE
            ->setZipCode(9999)                  // required in NL and DE
            ->setLocality("Stan")               // required in NL and DE
            ->setEmail("test@svea.com")         // optional but desirable
            ->setIpAddress("123.123.123")       // optional but desirable
            ->setCoAddress("c/o Eriksson")      // optional
            ->setPhoneNumber(999999)            // optional
    )
;
...
```

### 1.3.2 CompanyCustomer class
Read "required" below as a requirement when the CompanyCustomer is used to identify the customer when using the invoice payment methods. 
(For card and direct bank orders, adding customer information to the order is optional.)

```php
...
$order->
    addCustomerDetails(
        WebPayItem::companyCustomer()
            ->setNationalIdNumber(2345234)      // required in SE, NO, DK, FI
            ->setVatNumber("NL2345234")         // required in NL and DE
            ->setCompanyName("TestCompagniet")  // required in NL and DE
            ->setStreetAddress("Gatan", 23)     // required in NL and DE
            ->setZipCode(9999)                  // required in NL and DE
            ->setLocality("Stan")               // required in NL and DE
            ->setEmail("test@svea.com")         // optional but desirable
            ->setIpAddress("123.123.123")       // optional but desirable
            ->setCoAddress("c/o Eriksson")      // optional
            ->setPhoneNumber(999999)            // optional
            ->setAddressSelector("7fd7768")     // optional, string recieved from WebPay::getAddress() request
    )
;
...
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 1.4 Additional order attributes

```php
...
$order
    ->setCountryCode("SE")              // required
    ->setCurrency("SEK")                // required for card payment, direct payment and PayPage payment.
    ->setClientOrderNumber("14050626")  // required for card payment, direct payment, PaymentMethod payment and PayPage payments.
    ->setCustomerReference("att: kgm")  // optional
    ->setOrderDate("2012-12-12")        // required for invoice and payment plan payments
;
...
```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 1.5 Choose the order payment method to use
Finish the order specification process by choosing a payment method with the order useXX() methods.

Invoice and Payment plan payment methods will perform a synchronous request to Svea and return a response object.

Hosted payment methods, like Card, Direct bank and payment methods accessed via the PayPage, are asynchronous. They will return an html form with formatted message. You then send the form to Svea, and the customer is redirected, complete the payment, and the payment response is sent back to the provided return url. The response may also be sent to the url specified with setCallbackUrl() in case the customer doesn't return to the store after the transaction has concluded at the bank/card payment page. Process the response via the SveaResponse class, and you will receive a formatted response object.

### Which useXX() method do I use?
I am using the invoice and/or payment plan payment methods in my integration.

>The best way is to use [`->useInvoicePayment()`] (https://github.com/sveawebpay/php-integration#154-invoicepayment) and
>[`->usePaymentPlanPayment()`] (https://github.com/sveawebpay/php-integration#154-paymentplanpayment).
>These payments are synchronous and will give you an instant response.

I am using the card and/or direct bank payment methods in my integration.
>You can go by *PayPage* by using [`->usePayPageCardOnly()`] (https://github.com/sveawebpay/php-integration#151-paypage-with-card-payment-options)
>and [`->usePayPageDirectBankOnly()`] (https://github.com/sveawebpay/php-integration#152-paypage-with-direct-bank-payment-options).
>
>The best way though, if you know what specific payment you want to use, is to go direct to that specific payment, bypassing the PayPage step, by using
>[`->usePaymentMethod(PaymentMethod)`] (https://github.com/sveawebpay/php-integration#154-paymentmethod-specified).
>You can check the optional payment methods configured on your account using the [WebPay::getPaymentMethods()] method.(https://github.com/sveawebpay/php-integration#8-getpaymentmethods)
>

I am using all payment methods in my integration.

>The most effective way is to use [`->useInvoicePayment()`](https://github.com/sveawebpay/php-integration#154-invoicepayment)
>and [`->usePaymentPlanPayment()`](https://github.com/sveawebpay/php-integration#154-paymentplanpayment)
>for the synchronous payments, and use the [`->usePaymentMethod(PaymentMethod)`] (https://github.com/sveawebpay/php-integration#154-paymentmethod-specified)
>for the asynchronous requests. First use [WebPay::getPaymentMethods($config)](https://github.com/sveawebpay/php-integration#8-getpaymentmethods)
>to get the different payment methods configured on you account.
>
>Alternatively you can go by *PayPage* for the asynchronous requests by using [`->usePayPageCardOnly()`] (https://github.com/sveawebpay/php-integration#151-paypage-with-card-payment-options)
>and [`->usePayPageDirectBankOnly()`] (https://github.com/sveawebpay/php-integration#152-paypage-with-direct-bank-payment-options).

I am using more than one payment and want them gathered on on place.

>You can go by *PayPage* and choose to show all your payments here, or modify to exclude or include one or more payments. Use [`->usePayPage()`] (https://github.com/sveawebpay/php-integration#153-paypagepayment) where you can custom your own *PayPage*. This introduces an additional step in the customer checkout flow, though. Note also that Invoice and Payment plan payments will return an asynchronous when used from PayPage.

### Synchronous payments - Invoice and PaymentPlan
The request gives an instant response.

### 1.5.1 InvoicePayment
Perform an invoice payment. This payment form will perform a synchronous payment and return a response.

```php
...
$order = WebPay::createOrder($config);
$order
    ->addOrderRow( ...
    ->addCustomerDetails( ...
    ->setCountryCode("SE")
    ->setOrderDate("2012-12-12")
;
$request = $order->useInvoicePayment();
$response = $request->doRequest();
...
```
Another complete, runnable example of a synchronous (invoice) order can be found in the examples/invoiceorder folder.

#### 1.5.2 PaymentPlanPayment
The Payment plan payment method is restricted to individual customers and can not be used by legal entities, i.e. companies or organisations.

First use WebPay::getPaymentPlanParams() to get the various campaign codes. Then chose a campaign to pass as parameter to the usePaymentPlanPayment() method.

```php
...
// fetch all available campaigns from Svea
$campaignsRequest = WebPay::getPaymentPlanParams($config);
$campaignsRequest->setCountryCode("SE");
$campaignsResponse = $campaignsRequest->doRequest();

// we pick the first available campaign from the response
$campaign = $campaignsResponse->campaignCodes[0]->campaignCode;

// create the order
$order = WebPay::createOrder($config);
$order
    ->addOrderRow( ...
    ->addCustomerDetails( ...
    ->setCountryCode("SE")
    ->setOrderDate("2012-12-12")
;

// send the request, using the first available campaign with the payment plan payment method
$request = $order->usePaymentPlanPayment($campaign)
$response = $request->doRequest();
...
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### Asynchronous payments - Hosted solutions
Create and build the order object. Then select the payment method to use and specify the various attributes using the methods applicable to hosted payments (see below). Get an instance of PaymentForm using the getPaymentForm() method.

```html
<form name='paymentForm' id='paymentForm' method='post' action='SveaConfig::SWP_TEST_URL'>
    <input type='hidden' name='merchantid' value='{$this->merchantid}' />
    <input type='hidden' name='message' value='{$this->xmlMessageBase64}' />
    <input type='hidden' name='mac' value='{$this->mac}' />
    <input type="submit" name="submit" value="Submit" />
</form>
```

The received form is sent using a http POST to the url indicated by i.e. SveaConfig::SWP_TEST_URL. The PaymentForm instance also contains the complete html form as string and the html form elements as an array. See the HostedService\PaymentForm class.

The service response is returned as XML, use the [SveaResponse](https://github.com/sveawebpay/php-integration#6-response-handler)
to format the response

#### Response URL:s

->setReturnUrl() When a hosted payment transaction completes (regardless of outcome, i.e. accepted or denied), the payment service will answer with a response xml message sent to the return url specified.

->setCallbackUrl() In case the hosted payment transaction completes, but the service is unable to return a response to the return url, the payment service will retry several times using the callback url as a fallback, if specified. This may happen if i.e. the user closes the browser before the payment service redirects back to the shop.

->setCancelUrl() In case the hosted payment service is cancelled by the user, the payment service will redirect back to the cancel url. Unless a return url is specified, no cancel button will be presented at the payment service.

See the HostedService\HostedPayment class.

### 1.5.3 PayPage with card payment options
*PayPage* with available card payments only.

#### 1.5.3.1 Request
```php
...
$form = $order
    ->usePayPageCardOnly()
        ->setPayPageLanguage("sv")                          // Optional, default english
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCallbackUrl("http://myurl.se")                 // Optional
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCall("http://myurl.se")                        // Optional
        ->getPaymentForm();
...
```

A complete, runnable example of a card order using PaymentMethodPayment can be found in the examples/cardorder folder. 

### 1.5.4 PayPage with direct bank payment options
Send user to *PayPage* to select from available banks (only), and then perform a direct bank payment at the chosen bank

```php
...
$form = $order
    ->usePayPageDirectBankOnly()
        ->setPayPageLanguage("sv")                          //Optional, default english
        ->setReturnUrl("http://myurl.se")                   //Required
        ->setCancelUrl("http://myurl.se")                   //Optional
        ->getPaymentForm()
;
...
```

### 1.5.5 PayPagePayment
Send user to *PayPage* to select from the available payment methods. You can customise which payment methods to display, using the PayPagePayment methods includePaymentMethods(), excludePaymentMethods(), excludeCardPaymentMethods() and excludeDirectPaymentMethods().

```php
...
$form = $order
    ->usePayPage()
        ->setPayPageLanguage("sv")                          //Optional, defaults to english
        ->setReturnUrl("http://myurl.se")                   //Required
        ->setCancelUrl("http://myurl.se")                   //Optional
        ->getPaymentForm()
;
...
```

##### 1.5.5.1 Exclude specific payment methods
Optional if you want to include specific payment methods for *PayPage*.
```php
...
$form = $order
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                                       //Required
        ->setCancelUrl("http://myurl.se")                                       //Optional
        ->excludePaymentMethods(PaymentMethod::SEB_SE,PaymentMethod::INVOICE)   //Optional
        ->getPaymentForm();
...
```

##### 1.5.5.2 Include specific payment methods
Optional if you want to include specific payment methods for *PayPage*.
```php
...
$form = $order
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                                        //Required
        ->includePaymentMethods(PaymentMethod::SEB_SE,PaymentMethod::INVOICE)   //Optional
        ->getPaymentForm();
...
```

##### 1.5.5.3 Exclude Card payments
Optional if you want to exclude all cardpayment methods from *PayPage*.
```php
...
$form = $order
   ->usePayPage()
        ->setReturnUrl("http://myurl.se")                   //Required
        ->excludeCardPaymentMethods()                       //Optional
        ->getPaymentForm();
...
```

##### 1.5.5.4 Exclude Direct payments
Optional if you want to exclude all direct bank payments methods from *PayPage*.
```php
...
$form = $order
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                       //Required
        ->excludeDirectPaymentMethods()                         //Optional
        ->getPaymentForm();
...
```

### 1.5.6 PaymentMethod specified
Go direct to specified payment method, bypassing the *PayPage* completetly.

```php
...
$form = $order
    ->usePaymentMethod(PaymentMethod::KORTCERT)             //Se APPENDIX for paymentmethods
        ->setReturnUrl("http://myurl.se")                   //Required
        ->setCancelUrl("http://myurl.se")                   //Optional
        ->setCardPageLanguage("se")                         //Optional,@param: languageCode As ISO639, eg. "en", defalut english
        ->getPaymentForm();
...
```

### 1.5.7 The getPaymentForm() returned form

The getPaymentForm() method returns an instance of HostedService\PaymentForm. Use the form class methods to get the form html.

```php
...
echo $form->completeHtmlFormWithSubmitButton;   // complete html of hidden form with method="post" and submit button to include in your code
//$form->htmlFormFieldsAsArray;                 // array of html form fields to include.
//$form->rawFields;                             // array of values included in the html form. ($merchantid, $xmlMessageBase64, $mac)
...
```

#### Other Synchronous requests

## 2. getPaymentPlanParams
Use this function to retrieve campaign codes for possible payment plan options. Use prior to create payment plan payment.

```php
    $response = WebPay::getPaymentPlanParams($config)
                ->setCountryCode("SE")
                ->doRequest();
```

The *PaymentPlanParamsResponse* object contains the available payment campaigns in the array "campaignCodes":
```php
    $response->accepted
    $response->resultcode
    $response->campaignCodes[0..n]      // all available campaign payment plans in an array
        ->campaignCode                      // numeric campaign code identifier
        ->description                       // localised description string
        ->paymentPlanType                   // human readable identifier (not guaranteed unique)
        ->contractLengthInMonths
        ->monthlyAnnuityFactor              // pricePerMonth = price * monthlyAnnuityFactor + notificationFee
        ->initialFee
        ->notificationFee
        ->interestRatePercent
        ->numberOfInterestFreeMonths
        ->numberOfPaymentFreeMonths
        ->fromAmount                        // amount lower limit for plan availability
        ->toAmount                          // amount upper limit for plan availability
```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 2.1 paymentPlanPricePerMonth

This is a helper function provided to calculate the monthly price for the different payment plan options for a given sum.
This information may be used when displaying i.e. payment options to the customer by checkout, or to display the lowest
amount due per month to display on a product level.

The returned instance of PaymentPlanPricePerMonth contains an array "values", where each element in turn contains an array of campaign code, description and price per month:

$paymentPlanParamsResonseObject->values[0..n] (for n campaignCodes), where values['campaignCode' => campaignCode, 'pricePerMonth' => pricePerMonth, 'description' => description]

**$paramsResonseObject** is response object from getPaymentPlanParams();
```php
    /**
     *
     * @param type decimal $price
     * @param type object $paramsResonseObject
     * @return \PaymentPlanPricePerMonth
     */
   $pricePerMonthPerCampaignCode = WebPay::paymentPlanPricePerMonth($price,$paymentPlanParamsResonseObject);
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 3. getAddresses
Returns *getAddressesResponse* object with an *AddressSelector* for the associated addresses for a specific security number.
Can be used when creating an order for Company customers to set what billing address to use. Only applicable for SE, NO and DK. In Norway, only getAddresses of companies is supported.

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 3.1 Order type
```php
    ->setOrderTypeInvoice()         //Required if this is an invoice order
or
    ->setOrderTypePaymentPlan()     //Required if this is a payment plan order
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 3.2 Customer type
```php
    ->setIndividual("194605092222")   //Required if this is an individual customer
or
    ->setCompany("CompanyId")       //Required if this is a company customer
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 3.3 Do request
```php
    $response = WebPay::getAddresses($config)
        ->setOrderTypeInvoice()                                              //See 3.1
        ->setCountryCode("SE")                                               //Required, accepts SE, DK and NO
        ->setIndividual("194605092222")                                      //See 3.2
        ->doRequest();
```

WebPay::getAddresses->...->doRequest() Returns a GetAddressesResponse object:
```php
    $response->accepted                 // boolean, true iff Svea accepted request
    $response->resultcode               // may contain an error code
    $response->customerIdentity         // if accepted, may define a GetAddressIdentity object:
        ->customerType;       // not guaranteed to be defined
        ->nationalIdNumber;   // not guaranteed to be defined
        ->phoneNumber;        // not guaranteed to be defined
        ->firstName;          // not guaranteed to be defined
        ->lastName;           // not guaranteed to be defined
        ->fullName;           // not guaranteed to be defined
        ->street;             // not guaranteed to be defined
        ->coAddress;          // not guaranteed to be defined
        ->zipCode;            // not guaranteed to be defined
        ->locality;           // not guaranteed to be defined

```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 4. deliverOrder
Use the WebPay::deliverOrder request to deliver to the customer invoices for fulfilled orders.
Svea will invoice the customer upon receiving the deliverOrder request.
A deliverOrder request may also be used to partly deliver an order on Invoice orders.

When Svea receives the deliverOrder request the status on the previous created order is set to *delivered*.

The deliverOrder functionallity is only applicable to invoice and payment plan payment method payments.

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 4.1 Build request object

Create an DeliverOrderBuilder object using WebPay::deliverOrder().

```php
    $foo = WebPay::deliverOrder( $sveaConfig );

```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 4.2 Deliver Invoice order

This works more or less like WebPay::createOrder above, and makes use of the same order item information.
Add the corresponding order id and the order rows that you want delivered before making the deliverOrder request.
The specified rows will automatically be matched with the previous rows that was sent when creating the order.
We recommend storing the order row data to ensure that matching orderrows can be recreated in the deliverOrder request.

If an item is left out from the deliverOrder request that was present in the createOrder request, a new invoice will be created as the order is assumed to be partially fulfilled.
Any left out items should not be delivered physically, as they will not be invoiced when the deliverOrder request is sent.

```php
    $response = WebPay::deliverOrder()
    ->addOrderRow(
        WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
        )
    ->setOrderId("id") //Recieved from CreateOrder request
    ->setInvoiceDistributionType(DistributionType::POST)
    ->deliverInvoiceOrder()
        ->doRequest();
```

You can add OrderRow, Fee and Discount. Choose the right WebPayItem object as parameter.
You can use the **add-** functions with an WebPayItem object or an array of WebPayItem objects as parameters.

```php
->addOrderRow(WebPayItem::orderRow()->...)

//or
$orderRows[] = WebPayItem::orderRow()->...;
->addOrderRow($orderRows)
```


[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

#### 4.2.1 OrderRow
All products and other items. It is required to have a minimum of one row.
```php
  ->addOrderRow(
    WebPayItem::orderRow()
       ->setQuantity(2)                     //Required
       ->setAmountExVat(100.00)             //Required
       ->setVatPercent(25)                  //Required
       ->setArticleNumber("1")              //Optional
       ->setDescription("Specification")    //Optional
       ->setName('Prod')                    //Optional
       ->setUnit("st")                      //Optional
       ->setDiscountPercent(0)              //Optional
   )
```

#### 4.2.2 ShippingFee
```php
->addFee(
    WebPayItem::shippingFee()
        ->setAmountExVat(50)                //Required
        ->setVatPercent(25)                 //Required
        ->setShippingId('33')               //Optional
        ->setName('shipping')               //Optional
        ->setDescription("Specification")   //Optional
        ->setUnit("st")                     //Optional
        ->setDiscountPercent(0)
    )
```
#### 4.2.3 InvoiceFee
```php
->addFee(
    WebPayItem::invoiceFee()
        ->setAmountExVat(50)                //Required
        ->setVatPercent(25)                 //Required
        ->setName('Svea fee')               //Optional
        ->setDescription("Fee for invoice") //Optional
        ->setUnit("st")                     //Optional
        ->setDiscountPercent(0)             //Optional
  )
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

#### 4.2.4 Other values
Required is the order id received when creating the order. Required for invoice orders are *InvoiceDistributionType*.
If invoice order is credit invoice use setCreditInvoice($invoiceId) and setNumberOfCreditDays($creditDaysAsInt)
```php
    ->setOrderId($orderId)                  //Required. Received when creating order.
    ->setNumberOfCreditDays(1)              //Use for Invoice orders.
    ->setInvoiceDistributionType(DistributionType::POST)    //Use for Invoice orders. DistributionType::POST or DistributionType::EMAIL

```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 4.3 Deliver PaymentPlan order
You cannot partially deliver paymentplans. When executing an deliverOrder on a payment plan all orderrows that aren’t cancelled will be delivered.

```php
    $response = WebPay::deliverOrder()
         ->setCountryCode("SE")
            ->setOrderId("id") //Recieved from CreateOrder request
            ->deliverPaymentPlanOrder()
                ->doRequest();
```


## 5. creditInvoice
When you want to credit an invoice. The order must first be delivered. When doing [DeliverOrder](https://github.com/sveawebpay/php-integration#4-deliverorder)
you will recieve an *InvoiceId* in the Response. To credit the invoice you follow the steps as in [4. DeliverOrder](https://github.com/sveawebpay/php-integration#4-deliverorder)
 but you add the call `->setCreditInvoice($InvoiceId)`:

```php
    $response = WebPay::deliverOrder()
    ->addOrderRow(
        WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
        )
    ->setOrderId("id")
    ->setInvoiceDistributionType(DistributionType::POST)
    //Credit invoice flag. Note that you first must deliver the order and recieve an InvoiceId, then do the deliver request again but with this call:
    ->setCreditInvoice($InvoiceId) //Use for invoice orders, if this should be a credit invoice. Params: InvoiceId recieved from when doing DeliverOrder
    ->deliverInvoiceOrder()
        ->doRequest();

```

TODO add info on CreditTransaction et al here


[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 6. closeOrder
Use when you want to cancel an undelivered order. Valid only for invoice and payment plan orders.
Required is the order id received when creating the order.

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 6.1 Close by payment type
```php
    ->closeInvoiceOrder()
or
    ->closePaymentPlanOrder()
```

```php
    $request =  WebPay::closeOrder($config)
        ->setOrderId($orderId)                                                  //Required, received when creating an order.
        ->closeInvoiceOrder()
             ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 7. Response handler
All synchronous responses are handled through *SveaResponse* and structured into objects.

Asynchronous responses recieved after sending the values *merchantid* and *xmlMessageBase64* to
hosted solutions can also be processed through the *SveaResponse* class. The response from server will be sent to the *returnUrl*
with POST or GET. 

The response contains the parameters: *response*, *merchantid*, and *mac*. The *response* is a Base64 encoded message. The *mac* is a calculated authorization message. Use *SveaResponse* to get a structured object similar to the synchronous answer instead.

For asynchronous services, create an instance of SveaResponse, pass it the resulting xml response as part of the $_REQUEST response along with countryCode and config, then receive your HostedResponse instance by calling the getResponse() method.

Params:
* The POST or GET message as an associative array with the keys "response", "merchantid" and "mac".
* CountryCode, i.e. "SE"
* Config(https://github.com/sveawebpay/php-integration#configuration), an object implementing the ConfigurationProvider interface.

(For synchronous services, the appropriate WebServiceResponse instance is returned when calling ->doRequest() on the order object.)

```php
  $response = (new SveaResponse($_REQUEST,$countryCode,$config))->getResponse();
```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

##8. GetPaymentMethods
Returns an array of SystemPaymentMethods available to a certain merchantId, which are constants defined in class PaymentMethod. Used to i.e. determine available Banks for direct bank payments.

See file PaymentMethodIntegrationTest.php for usage.

```php
  $fooArray = WebPay::getPaymentMethods( $config )  // optional, if no $config given, will use defaults from SveaConfig
                    ->setContryCode("SE")           // optional, if no country given, will use default country "SE"
                    ->doRequest();
```

## 9. Additional Developer Resources
In the Helper class we make available helper functions for i.e. bankers rounding, splitting a sum with an arbitrary tax rate over two fixed tax rates, as well as splitting street addresses into streetname and housenumber. See the Helper class definition for further information.

During module development or debugging, the WebServicePayment prepareRequest() and validateOrder() methods may be of use as an alternative to doRequest() as the final step in the createOrder process.

prepareRequest() will do everything doRequest does, but does not send the SOAP request to Svea. The prepared request object may then be inspected for errors.

validateOrder() validates that all required attributes are present in an order object, give the specific combination of country and payment method. It returns an array containing any discovered errors.

## APPENDIX

### PaymentMethods
Used in usePaymentMethod($paymentMethod) and in usePayPage(),
->includePaymentMethods(..., ..., ...), ->excludeCardPaymentMethods(..., ..., ...), ->excludeDirectPaymentMethods(), ->excludeCardPaymentMethods().

| Payment method                    | Description                                   |
|-----------------------------------|-----------------------------------------------|
| PaymentMethod::BANKAXESS          | Direct bank payments, Norway                  |
| PaymentMethod::NORDEA_SE          | Direct bank payment, Nordea, Sweden.          |
| PaymentMethod::SEB_SE             | Direct bank payment, private, SEB, Sweden.    |
| PaymentMethod::SEBFTG_SE          | Direct bank payment, company, SEB, Sweden.    |
| PaymentMethod::SHB_SE             | Direct bank payment, Handelsbanken, Sweden.   |
| PaymentMethod::SWEDBANK_SE        | Direct bank payment, Swedbank, Sweden.        |
| PaymentMethod::KORTCERT           | Card payments, Certitrade.                    |
| PaymentMethod::PAYPAL             | Paypal                                        |
| PaymentMethod::SKRILL             | Card payment with Dankort, Skrill.            |
| PaymentMethod::INVOICE            | Invoice by PayPage.                           |
| PaymentMethod::PAYMENTPLAN        | PaymentPlan by PayPage.                       |

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)
