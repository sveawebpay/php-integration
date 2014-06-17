# PHP Integration Package API for SveaWebPay

## Version 2.0.0
The Svea WebPay Integration package uses semantic versioning (http://semver.org). This means that you can expect your integrations to remain backwards compatible during a major version release cycle. 

Previous versions of the package can be accessed through the github releases view (https://github.com/sveawebpay/php-integration/releases).

### Current build status
| Branch                            | Build status                               |
|---------------------------------- |------------------------------------------- |
| master (latest release)           | [![Build Status](https://travis-ci.org/sveawebpay/php-integration.png?branch=master)](https://travis-ci.org/sveawebpay/php-integration) |
| develop                           | [![Build Status](https://travis-ci.org/sveawebpay/php-integration.png?branch=develop)](https://travis-ci.org/sveawebpay/php-integration) |


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

## Configuration
In order to make use of the Svea services you need to supply your account credentials to authorize yourself against the Svea services. For the Invoice and Payment Plan payment methods, the credentials consist of a set of Username, Password and Client number (one set for each country and service type). For Card and Direct Bank payment methods, the credentials consist of a (single) set of Merchant id and Secret Word.

You should have received the above credentials from Svea when creating a service account. If not, please contact your Svea account manager.

### Using your account credentials with the package
The WebPay and WebPayAdmin entrypoint methods all require a config object when called. The easiest way to get such an object is to use the SveaConfig::getDefaultConfig() method. Per default, it returns a config object with the Svea test account credentials as used by the integration package test suite. 

In order to use your own account credentials, either edit the SveaConfig.php file with your actual account credentials, or implement the ConfigurationProvider interface in a class of your own -- your implementation could for instance fetch the needed credentials from a database in place of the SveaConfig.php file.

See further the ConfigurationProvider interface and the SveaConfig.php file.

See also the provided example MyConfig.php under example/config_getaddresses for an example of a customised SveaConfig.php file and how to use it.

## 1. WebPay class methods

The WebPay:: class methods contains the functions needed to create orders and
perform payment requests using Svea payment methods. It contains methods to
define order contents, send order requests, as well as support methods 
needed to do this.

*  1.1 createOrder -- create order and pay via invoice, payment plan, card, or direct bank payment methods
*  1.2 *createOrder -- (recurring orders)
*  1.3 deliverOrder -- (with orderRows) partially deliver, change or credit invoice, payment plan orders depending on set options
*  1.4 *deliverOrder -- (without orderRows) deliver in full invoice, payment plan orders, confirms card orders 
*  1.5 getAddresses -- fetch addresses connected with a provided customer identity
*  1.6 getPaymentPlanParams -- fetch current campaigns (payment plan params) for a clientid, used by paymentplan orders
*  1.7 getPaymentMethods -- fetch available payment methods for a clientid, used by i.e. direct bank orders
*  1.8 getPaymentPlanPricePerMonth -- calculates price per month over all available campaigns for a specified amount 
*  1.9 (closeOrder) -- cancel non-delivered invoice or payment plan

(methods in parenthesises are deprecated), *starred methods are new to 2.0)

### 1.1 WebPay::createOrder()
Use createOrder() to create an order and pay via invoice, payment plan, card, or direct bank payment methods.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.CreateOrderBuilder.html" target="_blank">CreateOrderBuilder</a> class for methods used to build the order object and select the payment method type to use.

#### 1.1.1 Order building -- a sample invoice order
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

The above example can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/develop/example/firstorder/" target="_blank">examples/firstorder</a> folder.

#### 1.1.2 Order building -- how to create and specify order row items
Order row, fee and discount items can be added to the order. Together the row items amount add up to the order total to pay.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/WebPayItem.html" target="_blank">WebPayItem</a> class for methods used to build order row item objects.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.CreateOrderBuilder.html" target="_blank">CreateOrderBuilder</a> for methods used to add row items to an order.

The following is an example showing how to instantiate and add an order row item to an order:

```php
...
$myOrderRow = WebPayItem::orderRow();       // create the order row object

$myOrderRow->setQuantity(1);                // required
$myOrderRow->setAmountExVat(10.00)          // recommended to specify price using AmountExVat & VatPercent
$myOrderRow->setVatPercent(12)              // recommended to specify price using AmountExVat & VatPercent

$myOrder->addOrderRow( $myOrderRow );       // add order row to the order
...

/* the same code expressed in a more compact, fluent style:
$myOrder->addOrderRow( WebPayItem::orderRow()->setQuantity(1)->setAmountExVat(10.00)->setVatPercent(12) );
*/
```

#### 1.1.3 Order building -- WebPayItem::orderRow()
Use this to add all kinds of products and other items. An order is required to have at least one order row.

Use the [OrderRow] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.OrderRow.html) class methods to specify the item using setXX(), including all *required* methods. 

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

#### 1.1.4 Order building -- WebPayItem::shippingFee()
Use this class to add shipping to the order.

Use the [ShippingFee] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.ShippingFee.html) class methods to specify the item using setXX(), including all *required* methods. 

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

#### 1.1.5 Order building -- WebPayItem::invoiceFee()
Use this class to add fees associated with a payment method (i.e. invoice fee) to the order.

Use the [InvoiceFee] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.InvoiceFee.html) class methods to specify the item using setXX(), including all *required* methods. 

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
;
...
```

#### 1.1.6 Order building -- WebPayItem::fixedDiscount()
Use this method when the discount or coupon is expressed as a percentage of the total product amount.

Use the [FixedDiscount] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.FixedDiscount.html) class methods to specify the item using setXX(), including all *required* methods. 

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

#### 1.1.7 Order building -- WebPayItem::relativeDiscount() 
Use this method when the discount or coupon is expressed as a percentage of the total product amount.

Use the [RelativeDiscount] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.RelativeDiscount.html) class methods to specify the item using setXX(), including all *required* methods. 

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

#### 1.1.8 Order building -- note on specifying item price
Specify item price using precisely two of these methods in order to specify the item price and tax rate: 
setAmountExVat(), setAmountIncVat() and setVatPercent().

We recommend specifying price using setAmountExVat() and setVatPercentage(). If not, make sure not retain as much precision as
possible, i.e. use no premature rounding (87.4875 is a "better" PriceIncVat than 87.49).

If you use setAmountIncVat(), note that this may introduce a cumulative rounding error when ordering large
quantities of an item, as the package bases the total order sum on a calculated price ex. vat.

#### 1.1.9 Order building -- Specifying customer information
Create a customer identity object using the WebPayItem::individualCustomer() or WebPayItem::companyCustomer() methods. Use the addCustomerDetails() method to add the customer information to the order. 

Set customer identity attributes using the setXX() customer class methods, respectively. Required attributes varies depending on country and customer type, as well as payment method chosen. See below for an overview and usage examples.

Adding a customer identity to the order is required for Invoice and Payment plan orders. For Card and Direct bank orders it is optional but recommended.

#### 1.1.10 Order building -- WebPayItem::individualCustomer()
Read "required" below as a requirement only when the IndividualCustomer is used to identify the customer when using the invoice or payment plan payment methods. (For card and direct bank orders, adding customer information to the order is optional.)

Use the [IndividualCustomer] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.IndividualCustomer.html) class methods to specify the item using setXX(), including all *required* methods. 

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

#### 1.1.11 Order building -- WebPayItem::companyCustomer()
Read "required" below as a requirement only when the CompanyCustomer is used to identify the customer when using the invoice or payment plan payment methods. (For card and direct bank orders, adding customer information to the order is optional.)

Use the [CompanyCustomer] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.CompanyCustomer.html) class methods to specify the item using setXX(), including all *required* methods. 

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

#### 1.1.12 Order building -- Additional order attributes
Set additional attributes needed to complete the order.

```php
...
$order
    ...
    ->setCountryCode("SE")                  // required
    ->setCurrency("SEK")                    // required for card payment, direct payment and PayPage payment.
    ->setClientOrderNumber("140506-261")    // required for card payment, direct payment, PaymentMethod payment and PayPage payments.
    ->setCustomerReference("att: kgm")      // optional, ignored for card and direct bank orders
    ->setOrderDate("2012-12-12")            // required for invoice and payment plan payments
;
...
```

#### 1.1.13 Payment method -- choose the payment method to use for the order
Finish the order specification process by choosing a payment method with the order useXX() methods.

Invoice and Payment plan payment methods will perform a synchronous request to Svea and return a response object.

Hosted payment methods, like Card, Direct Bank and payment methods accessed via the PayPage, are asynchronous. They will return an html form with 
formatted message. You then send the form to Svea, and the customer is redirected, complete the payment, and the payment response is sent back to 
the provided return url. The response may also be sent to the url specified with setCallbackUrl() in case the customer doesn't return to the store 
after the transaction has concluded at the bank/card payment page. Process the response via the SveaResponse class, and you will receive a formatted 
response object.

##### Which of the various useXX() methods should I use in the following scenarios?
*I am using the invoice and/or payment plan payment methods in my integration.*

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

*I am using all payment methods in my integration.*

>The most effective way is to use [`->useInvoicePayment()`](https://github.com/sveawebpay/php-integration#154-invoicepayment)
>and [`->usePaymentPlanPayment()`](https://github.com/sveawebpay/php-integration#154-paymentplanpayment)
>for the synchronous payments, and use the [`->usePaymentMethod(PaymentMethod)`] (https://github.com/sveawebpay/php-integration#154-paymentmethod-specified)
>for the asynchronous requests. First use [WebPay::getPaymentMethods($config)](https://github.com/sveawebpay/php-integration#8-getpaymentmethods)
>to get the different payment methods configured on you account.
>
>Alternatively you can go by *PayPage* for the asynchronous requests by using [`->usePayPageCardOnly()`] (https://github.com/sveawebpay/php-integration#151-paypage-with-card-payment-options)
>and [`->usePayPageDirectBankOnly()`] (https://github.com/sveawebpay/php-integration#152-paypage-with-direct-bank-payment-options).

*I am using more than one payment and want them gathered on on place.*

>You can go by *PayPage* and choose to show all your payments here, or modify to exclude or include one or more payments. Use [`->usePayPage()`] (https://github.com/sveawebpay/php-integration#153-paypagepayment) where you can custom your own *PayPage*. This introduces an additional step in the customer checkout flow, though. Note also that Invoice and Payment plan payments will return an asynchronous when used from PayPage.

##### Synchronous payments -- Invoice and Payment plan

#### 1.1.14 Payment method -- InvoicePayment
Perform an invoice payment. This payment form will perform a synchronous payment request and returns a response object.

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

#### 1.1.15 Payment method -- PaymentPlanPayment
Perform an payment plan payment. This payment form will perform a synchronous payment request and returns a response object.

The Payment plan payment method is restricted to individual customers and can not be used by legal entities, i.e. companies or organisations.

First use WebPay::getPaymentPlanParams() to get the various campaigns. Then chose a campaign to pass as parameter to the usePaymentPlanPayment() method.

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

##### Asynchronous payments -- Hosted payments

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

##### Response URL:s

->setReturnUrl() When a hosted payment transaction completes (regardless of outcome, i.e. accepted or denied), the payment service will answer with a response xml message sent to the return url specified. This is also the return address if the user cancels at i.e. the Certitrade page.

->setCallbackUrl() In case the hosted payment transaction completes, but the service is unable to return a response to the return url, the payment service will retry several times using the callback url as a fallback, if specified. This may happen if i.e. the user closes the browser before the payment service redirects back to the shop.

->setCancelUrl() In case the payment method selection is cancelled by the user at the PayPage, Svea will redirect back to the cancel url.

See the HostedService\HostedPayment class.

##### The getPaymentForm() returned form

The getPaymentForm() method returns an instance of HostedService\PaymentForm. Use the form class methods to get the form html.

```php
...
echo $form->completeHtmlFormWithSubmitButton;   // complete html of hidden form with method="post" and submit button to include in your code
//$form->htmlFormFieldsAsArray;                 // array of html form fields to include.
//$form->rawFields;                             // array of values included in the html form. ($merchantid, $xmlMessageBase64, $mac)
...
```

#### 1.1.16 Payment method -- PayPage with card payment options
*PayPage* with available card payments only.

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


A complete, runnable example of a card order using PaymentMethodPayment can be found in the [examples/cardorder] (https://github.com/sveawebpay/php-integration/blob/develop/example/cardorder/cardorder.php) folder.

#### 1.1.17 Payment method -- PayPage with direct bank payment options
Send user to *PayPage* to select from available banks (only), and then perform a direct bank payment at the chosen bank

```php
...
$form = $order
    ->usePayPageDirectBankOnly()
        ->setPayPageLanguage("sv")                          // Optional, default english
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->getPaymentForm()
;
...
```

#### 1.1.18 Payment method -- PayPagePayment
Send user to *PayPage* to select from the available payment methods. You can customise which payment methods to display, using the PayPagePayment methods includePaymentMethods(), excludePaymentMethods(), excludeCardPaymentMethods() and excludeDirectPaymentMethods().

```php
...
$form = $order
    ->usePayPage()
        ->setPayPageLanguage("sv")                          // Optional, defaults to english
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->getPaymentForm()
;
...
```

#### 1.1.19 Payment method -- Exclude specific payment methods
Optional if you want to include specific payment methods for *PayPage*.
```php
...
$form = $order
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                                       // Required
        ->setCancelUrl("http://myurl.se")                                       // Optional
        ->excludePaymentMethods(PaymentMethod::SEB_SE,PaymentMethod::INVOICE)   // Optional
        ->getPaymentForm();
...
```

#### 1.1.20 Payment method -- Include specific payment methods
Optional if you want to include specific payment methods for *PayPage*.
```php
...
$form = $order
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                                       // Required
        ->includePaymentMethods(PaymentMethod::SEB_SE,PaymentMethod::INVOICE)   // Optional
        ->getPaymentForm();
...
```

#### 1.1.21 Payment method -- Exclude Card payments
Optional if you want to exclude all cardpayment methods from *PayPage*.
```php
...
$form = $order
   ->usePayPage()
        ->setReturnUrl("http://myurl.se")                   // Required
        ->excludeCardPaymentMethods()                       // Optional
        ->getPaymentForm();
...
```

#### 1.1.22 Payment method -- Exclude Direct payments
Optional if you want to exclude all direct bank payments methods from *PayPage*.
```php
...
$form = $order
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                       // Required
        ->excludeDirectPaymentMethods()                         // Optional
        ->getPaymentForm();
...
```

#### 1.1.23 Payment method -- PaymentMethod specified
Go direct to specified payment method, bypassing the *PayPage* completetly.

```php
...
$form = $order
    ->usePaymentMethod(PaymentMethod::KORTCERT)             // Se APPENDIX for paymentmethods
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("se")                         // Optional,@param: languageCode As ISO639, eg. "en", defalut english
        ->getPaymentForm();
...
```

#### 1.1.24 Payment method -- examples

An example of a synchronous (invoice) order can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/develop/example/invoiceorder/" target="_blank">examples/invoiceorder</a> folder.

An example of an asynchronous (card) order can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/develop/example/cardorder/" target="_blank">examples/cardorder</a> folder.

An example of an recurring card order, both the setup transaction and a recurring payment, can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/develop/example/cardorder_recur/" target="_blank">examples/cardorder_recur</a> folder.

### 1.2 WebPay::deliverOrder()
The WebPay::deliverOrder request should generally be sent to Svea once the 
ordered items have been sent out, or otherwise delivered, to the customer.

For invoice and payment plan orders, the deliver order request
triggers the customer invoice being sent out to the customer by Svea. 

For card orders, the deliver order request confirms the card transaction, which 
in turn causes the card transaction to be batch processed by Svea. An auto-confirm 
account setting is also available, ask your Svea integration manager about this.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.DeliverOrderBuilder.html" target="_blank">DeliverOrderBuilder</a> class for methods used to build the order object and select the order type to deliver.


#### 1.2.1 Order delivery -- deliver a sample invoice order
The following is a minimal example of how to deliver an invoice order:

For orders specifying orderrows, WebPay::deliverOrder is used in a similar way 
to WebPay::createOrder, and makes use of the same order item information. Add 
the order rows that you want delivered along with the Svea order id before 
sending the request. The specified rows will automatically be matched to the 
rows sent when creating the order.

We recommend storing the order row data to ensure that matching orderrows can 
be faithfully recreated in the deliverOrder request.

If an item that was present in the createOrder request is left out from the 
deliverOrder request, the order is assumed to be partially fulfilled. Any left 
out items will not be invoiced by Svea.

You cannot partially deliver payment plan orders. When using deliverOrder on a 
payment plan order, all orderrows that aren’t cancelled will be delivered.


```php
<?php
// Include Svea PHP integration package.
require( "Includes.php" );

// get configuration object holding the Svea service login credentials
$myConfig = Svea\SveaConfig::getTestConfig(); 

// We assume that you've previously run the firstorder.php file and successfully made a createOrder request to Svea using the invoice payment method.
$mySveaOrderId = "123456";

// Begin the order creation process by creating an order builder object using the WebPay::createOrder() method:
$myOrder = WebPay::deliverOrder( $myConfig );

// We then add information to the order object by using the various methods in the Svea\DeliverOrderBuilder class.

// We begin by adding any additional information required by the payment method, which for an invoice order means:
$myOrder->setCountryCode("SE");                         
$myOrder->setOrderId( $mySveaOrderId );
$myOrder->setInvoiceDistributionType(\DistributionType::POST);

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select the invoice payment method:
$myDeliverOrderRequest = $myOrder->deliverInvoiceOrder();

// Then send the request to Svea using the doRequest method, and immediately receive the service response object
$myResponse = $myDeliverOrderRequest->doRequest();
?>
```

The above example can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/develop/example/firstdeliver/" target="_blank">examples/firstdeliver</a> folder.

#### 1.2.2 Order delivery -- additional order attributes 
```php
$myDeliverOrder->
    ...
    ->setOrderId($orderId)                                  // Required - received with createOrder response (SveaOrderId or TransactionId)
    ->setCountryCode("SE")                                  // Required - should match countryCode given in the createOrder request 
    ->setInvoiceDistributionType(\DistributionType::POST)   // Required - use for Invoice orders
    ->setNumberOfCreditDays(1)                              // Optional - use for Invoice orders
    ...
;
```

### 1.3 WebPay::getAddresses()
Use getAddresses() to fetch a list of validated addresses associated with a 
given customer identity. Used to i.e. present to the customer the invoice 
address used by Svea, which for invoice and payment plan orders also should 
match the order delivery address.

Returns an instance of WebService\getAddressesResponse containing a list of 
verified addresses and addressSelector strings for a given customer.

The GetAddresses service is only applicable for SE, NO and DK customers and 
accounts. In Norway, GetAddresses may only be performed on company customers.

See the Svea\WebService\GetAddresses class for more information.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.WebService.GetAddresses.html" target="_blank">GetAddresses</a> class for methods used to build and send a getAddresses request.

#### 1.3.1 getAddresses request example

```php
$response = WebPay::getAddresses( $config )
    ->setCountryCode("SE")                  // Required -- supply the country code that corresponds to the account credentials used 
    ->setOrderTypeInvoice()                 // Required -- use invoice account credentials for getAddresses lookup
    //->setOrderTypePaymentPlan()           // Required -- use payment account plan credentials for getAddresses lookup
    ->setIndividual("194605092222")         // Required -- lookup the address of a private individual
    //->setCompany("CompanyId")             // Required -- lookup the address of a legal entity (i.e. company)
    ->doRequest();
;
```

An complete usage example can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/develop/example/config_getaddresses/" target="_blank">examples/config_getaddresses</a> folder.

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

#### 1.3.2 getAddresses response format

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.WebService.GetAddressesResponse.html" target="_blank">GetAddresses</a> class for more.

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

### 1.4 WebPay::getPaymentPlanParams()
Use getPaymentPlanParams() to fetch all campaigns associated with a given client number before creating the payment plan payment.

```php
...
$response = 
    WebPay::getPaymentPlanParams($config)
        ->setCountryCode("SE")                  // Required
        ->doRequest();
...
```

The response is an instance of WebService\PaymentPlanParamsResponse, with the available campaigns in the array campaignCodes:
```php
    $response->accepted                 // true iff request was accepted by the service 
    $response->errormessage             // may be set iff accepted above is false
    $response->resultcode               // 27xxx, reason
    $response->campaignCodes[]          // array of all available campaign payment plans in an array
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

### 1.5 WebPay::getPaymentMethods()
Returns an array of SystemPaymentMethods available to a certain merchantId, which 
are constants defined in class PaymentMethod.

See file PaymentMethodIntegrationTest.php for usage.

```php
  $fooArray = WebPay::getPaymentMethods( $config )  // optional, if no $config given, will use defaults from SveaConfig
                    ->setContryCode("SE")           // optional, if no country given, will use default country "SE"
                    ->doRequest();
```

### 1.6 WebPay::paymentPlanPricePerMonth()
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


## 2. WebPayAdmin

### 2.1 WebPayAdmin::cancelOrder()
The WebPayAdmin::cancelOrder method is used to cancel an order with Svea, that has
not yet been delivered (invoice, payment plan) or confirmed (card). 

Direct bank orders are not supported, see WebPayAdmin::creditOrder.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.CancelOrderBuilder.html" target="_blank">CancelOrderBuilder</a> class for methods used to build the order object and select the order type to cancel.

#### 2.1.1. cancelOrder example

```php
...
$request =                               
    WebPay::cancelOrder($config)
        ->setCountryCode("SE")          // Required. Use same country code as in createOrder request.
        ->setOrderId($orderId)          // Required. Use SveaOrderId recieved with createOrder response
        ->cancelInvoiceOrder()          // Use the method corresponding to the original createOrder payment method.
        //->cancelPaymentPlanOrder()     
        //->cancelCardOrder()           
;
$response = $request->doRequest();      // send request and receive either WebService\CloseOrderResponse or HostedService\AnnulTransactionResponse
...
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




 5. WebPayAdmin::creditInvoice
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


## x.2 WebPayAdmin::creditOrder()

The WebPayAdmin::creditOrder method is used to credit an order with Svea, that has been delivered (invoice, payment plan) or confirmed (card). (For direct bank orders, see WebPayAdmin::creditOrder.)

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.CancelOrderBuilder.html" target="_blank">CancelOrderBuilder</a> class for methods used to build the order object and select the order type to cancel.

```php
$result =  
    WebPay::creditOrder($config)
//        ->setCountryCode("SE")          // Required. Use same country code as in createOrder request.
//        ->setOrderId($orderId)          // Required. Use SveaOrderId recieved with createOrder response
        ->creditInvoiceOrder()          // Use the method corresponding to the original createOrder payment method.
        //->creditPaymentPlanOrder()     
        //->creditCardOrder()           
        //->creditDirectBankOrder()
             ->doRequest()
;             
```



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

