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

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## Configuration
In order to make use of the Svea services you need to supply your account credentials to authorize yourself against the Svea services. For the Invoice and Payment Plan payment methods, the credentials consist of a set of Username, Password and Client number (one set for each country and service type). For Card and Direct Bank payment methods, the credentials consist of a (single) set of Merchant id and Secret Word.

You should have received the above credentials from Svea when creating a service account. If not, please contact your Svea account manager.

### Using your account credentials with the package
The WebPay and WebPayAdmin entrypoint methods all require a config object when called. The easiest way to get such an object is to use the SveaConfig::getDefaultConfig() method. Per default, it returns a config object with the Svea test account credentials as used by the integration package test suite. 

In order to use your own account credentials, either edit the SveaConfig.php file with your actual account credentials, or implement the ConfigurationProvider interface in a class of your own -- your implementation could for instance fetch the needed credentials from a database in place of the SveaConfig.php file.

See further the ConfigurationProvider interface and the SveaConfig.php file.

See also the provided example MyConfig.php under example/config_getaddresses for an example of a customised SveaConfig.php file and how to use it.

##  1. WebPay class methods

The WebPay:: class methods contains the functions needed to create orders and
perform payment requests using Svea payment methods. It contains methods to
define order contents, send order requests, as well as support methods 
needed to do this.

*  createOrder -- create order and pay via invoice, payment plan, card, or direct bank payment methods
*  *createOrder -- (recurring orders)
*  deliverOrder -- (with orderRows) partially deliver, change or credit invoice, payment plan orders depending on set options
*  *deliverOrder -- (without orderRows) deliver in full invoice, payment plan orders, confirms card orders 
*  getAddresses -- fetch addresses connected with a provided customer identity
*  getPaymentMethods -- fetch available payment methods for a clientid, used by i.e. direct bank orders
*  getPaymentPlanParams -- fetch current campaigns (payment plan params) for a clientid, used by paymentplan orders
*  getPaymentPlanPricePerMonth -- calculates price per month over all available campaigns for a specified amount 
*  (closeOrder) -- cancel non-delivered invoice or payment plan

(methods in parenthesises are deprecated), *starred methods are new to 2.0)

## 1.1 WebPay::createOrder()
Use createOrder() to create an order and pay via invoice, payment plan, card, or direct bank payment methods.

See [CreateOrderBuilder] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.CreateOrderBuilder.html) class for methods used to build the order object and select the payment method used.

### 1.1.1 Order building -- a sample invoice order
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

The above example can be found in the [examples/firstorder] (https://github.com/sveawebpay/php-integration/blob/develop/example/firstorder/) folder.


## 1.1.2 Order building -- how to specify the order items
Order row, fee and discount items are added to the order. Together the row items amount make up the order total to pay.

Use the [WebPayItem] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/WebPayItem.html) class methods to instantiate various types of row items. You can add row items of the type OrderRow, ShippingFee, InvoiceFee, FixedDiscount and RelativeDiscount to the order. 

Then add the items to the order object using the [CreateOrderBuilder] (http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.CreateOrderBuilder.html) methods. 

The following is code excerpt example of how to instantiate and add an order row item to an order:

```php
...
$myOrderRow = WebPayItem::orderRow();       // create the order row object

$myOrderRow->setQuantity(1);                // required
$myOrderRow->setAmountExVat(10.00)          // recommended to specify price using AmountExVat & VatPercent
$myOrderRow->setVatPercent(12)              // recommended to specify price using AmountExVat & VatPercent

$myOrder->addOrderRow( $myOrderRow );       // add order row to the order
...

/* the same code expressed in a compact, fluent style:
$myOrder->addOrderRow( WebPayItem::orderRow()->setQuantity(1)->setAmountExVat(10.00)->setVatPercent(12) );
*/
```

Specify item price using precisely two of these methods in order to specify the item price and tax rate: 
setAmountExVat(), setAmountIncVat() and setVatPercent().

We recommend specifying price using setAmountExVat() and setVatPercentage(). If not, make sure not retain as much precision as
possible, i.e. use no premature rounding (87.4875 is a "better" PriceIncVat than 87.49).

If you use setAmountIncVat(), note that this may introduce a cumulative rounding error when ordering large
quantities of an item, as the package bases the total order sum on a calculated price ex. vat.


### 1.1.3 Order building -- WebPayItem::orderRow()
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

### 1.1.4 Order building -- WebPayItem::shippingFee()
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

### 1.1.5 Order building -- WebPayItem::invoiceFee()
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

### 1.1.6 Order building -- WebPayItem::fixedDiscount()
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

### 1.1.7 Order building -- WebPayItem::relativeDiscount() 
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

## 1.1.8 Order building -- Specifying customer information
Create a customer identity object using the WebPayItem::individualCustomer() or WebPayItem::companyCustomer() methods. Use the addCustomerDetails() method to add the customer information to the order. 

Set customer identity attributes using the setXX() customer class methods, respectively. Required attributes varies depending on country and customer type, as well as payment method chosen. See below for an overview and usage examples.

Adding a customer identity to the order is required for Invoice and Payment plan orders. For Card and Direct bank orders it is optional but recommended.

### 1.1.9 Order building -- WebPayItem::individualCustomer()
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

### 1.1.10 Order building -- WebPayItem::companyCustomer()
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

## 1.1.11 Order building -- Additional order attributes

```php
...
$order
    ...
    ->setCountryCode("SE")              // required
    ->setCurrency("SEK")                // required for card payment, direct payment and PayPage payment.
    ->setClientOrderNumber("14050626")  // required for card payment, direct payment, PaymentMethod payment and PayPage payments.
    ->setCustomerReference("att: kgm")  // optional, ignored for card and direct bank orders
    ->setOrderDate("2012-12-12")        // required for invoice and payment plan payments
;
...
```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 1.1.12 Payment method -- choose the payment method to use for the order
Finish the order specification process by choosing a payment method with the order useXX() methods.

Invoice and Payment plan payment methods will perform a synchronous request to Svea and return a response object.

Hosted payment methods, like Card, Direct bank and payment methods accessed via the PayPage, are asynchronous. They will return an html form with formatted message. You then send the form to Svea, and the customer is redirected, complete the payment, and the payment response is sent back to the provided return url. The response may also be sent to the url specified with setCallbackUrl() in case the customer doesn't return to the store after the transaction has concluded at the bank/card payment page. Process the response via the SveaResponse class, and you will receive a formatted response object.

#### Which of the various useXX() methods should I use?
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


#### Synchronous payments -- Invoice and Payment plan

### 1.1.13 Payment method -- InvoicePayment
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

### 1.1.14 Payment method -- PaymentPlanPayment
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

#### Asynchronous payments -- Hosted payments

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

#### The getPaymentForm() returned form

The getPaymentForm() method returns an instance of HostedService\PaymentForm. Use the form class methods to get the form html.

```php
...
echo $form->completeHtmlFormWithSubmitButton;   // complete html of hidden form with method="post" and submit button to include in your code
//$form->htmlFormFieldsAsArray;                 // array of html form fields to include.
//$form->rawFields;                             // array of values included in the html form. ($merchantid, $xmlMessageBase64, $mac)
...
```

### 1.1.15 Payment method -- PayPage with card payment options
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

A complete, runnable example of a card order using PaymentMethodPayment can be found in the examples/cardorder folder. 

### 1.1.16 Payment method -- PayPage with direct bank payment options
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

### 1.1.17 Payment method -- PayPagePayment
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

### 1.1.18 Payment method -- Exclude specific payment methods
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

### 1.1.19 Payment method -- Include specific payment methods
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

### 1.1.20 Payment method -- Exclude Card payments
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

### 1.1.21 Payment method -- Exclude Direct payments
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

### 1.1.22 Payment method -- PaymentMethod specified
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

### 1.1.23 Payment method -- Additional examples

An example of a synchronous (invoice) order can be found in the [examples/invoiceorder] (https://github.com/sveawebpay/php-integration/blob/develop/example/invoiceorder/invoiceorder.php) folder.

An example of an asynchronous (card) order can be found in the [examples/cardorder] (https://github.com/sveawebpay/php-integration/blob/develop/example/cardorder/cardorder.php) folder.

## 2. WebPay::getPaymentPlanParams()
Use getPaymentPlanParams() to fetch all campaigns associated with a given client number. Use prior to create payment plan payment.

```php
...
$response = 
    WebPay::getPaymentPlanParams($config)
        ->setCountryCode("SE")                  // Required
        ->doRequest();
...
```

The response is an instance of WebService\PaymentPlanParamsResponse with the available campaigns in the array campaignCodes:
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

### 2.1 WebPay::paymentPlanPricePerMonth()

TODO check this

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

## 3. WebPay::getAddresses()
Use getAddresses() to fetch validated addresses associated with a given customer identity

Returns an instance of WebService\getAddressesResponse object containing a listo of verified addresses and addressSelector strings for the customer.
Can be used when creating an order for Company customers to set what billing address to use. 

The GetAddresses service is only applicable for SE, NO and DK customers and accounts. In Norway, GetAddresses may only be performed on company customers.

See the Svea\WebService\GetAddresses class for more information

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
The WebPay::deliverOrder request should generally be sent to Svea when an order 
is fulfilled (i.e. the product is sent out or delivered).

For invoice and payment plan orders, the deliver order request should be performed 
once the ordered items have been sent out to the customer. The deliver order request
triggers the customer invoice being sent out to the customer by Svea. 

For card orders, the deliver order request confirms the card transaction, which in
turn causes the card transaction to be batch processed by Svea.

Set all required order attributes in a DeliverOrderBuilder instance by using the 
OrderBuilder setAttribute() methods. Instance methods can be chained together, as 
they return the instance itself in a fluent manner.

Finish by using the delivery method matching the payment method specified in the 
createOrder request.

You can then go on specifying any payment method specific settings, using methods provided by the 
returned deliver order request class.

A deliverOrder request may also be used to partly deliver an invoice order.

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


x.1 WebPayAdmin::cancelOrder()

CancelOrderBuilder is the class used to cancel an order with Svea, that has
not yet been delivered (invoice, payment plan) or confirmed (card).

Supports Invoice, Payment Plan and Card orders. For Direct Bank orders, @see
CreditOrderBuilder instead.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/develop/apidoc/classes/Svea.CancelOrderBuilder.html" target="_blank">CancelOrderBuilder</a> class for methods used to build the order object and select the order type to cancel.

```php
$request =  
    WebPay::cancelOrder($config)
        ->setCountryCode("SE")          // Required. Use same country code as in createOrder request.
        ->setOrderId($orderId)          // Required. Use SveaOrderId recieved with createOrder response
        ->cancelInvoiceOrder()          // Use the method corresponding to the original createOrder payment method.
        //->cancelPaymentPlanOrder()     
        //->cancelCardOrder()           
             ->doRequest()
;             
```
