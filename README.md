# Svea PHP Integration Package Documentation

## Version 3.2.1

### Current build status
| Branch                    received| Build status                               |
|---------------------------------- |------------------------------------------- |
| master (latest release)           | [![Build Status](https://travis-ci.org/sveawebpay/php-integration.png?branch=master)](https://travis-ci.org/sveawebpay/php-integration) |
| develop                           | [![Build Status](https://travis-ci.org/sveawebpay/php-integration.png?branch=develop)](https://travis-ci.org/sveawebpay/php-integration) |


## Index <a name="index"></a>

* [I. Introduction](#i-introduction)
* [1. Installing and configuration](#i1)
    * [1.1 Requirements](#i1-1)
    * [1.2 Installation](#i1-2)
    * [1.3 Configuration](#i1-3)
    * [1.4 Using your account credentials with the package](#i1-4)
    * [1.5 Additional integration properties configuration](#i1-5)
* [2. "Hello World"](#i2)
* [3. Building an order](#i3)
    * [3.1 Order builder](#i3-1)
    * [3.2 Order row items](#i3-2)
    * [3.3 Customer identity](#i3-3)
    * [3.4 Additional order attributes](#i3-4)
    * [3.5 Payment method selection](#i3-5)
    * [3.6 Recommended payment method usage](#i3-6)
* [4. Payment method reference](#i4)
    * [4.1 Svea Invoice payment](#i4-1)
    * [4.2 Svea Payment plan payment](#i4-2)
    * [4.3 Card payment](#i4-3)
    * [4.4 Direct bank payment](#i4-4)
    * [4.5 Using the Svea PayPage](#i4-5)
    * [4.6 Svea Checkout](#i4-6)
    * [4.7 Account Credit](#i4-7)
    * [4.8 Examples](#i4-8)
* [5. WebPayItem reference](#i5)
    * [5.1 Specifying item price](#i5-1)
    * [5.2 WebPayItem::orderRow()](#i5-2)
    * [5.3 WebPayItem::shippingFee()](#i5-3)
    * [5.4 WebPayItem::invoiceFee()](#i5-4)
    * [5.5 WebPayItem::fixedDiscount()](#i5-5)
    * [5.6 WebPayItem::relativeDiscount](#i5-6)
    * [5.7 WebPayItem::individualCustomer()](#i5-7)
    * [5.8 WebPayItem::companyCustomer()](#i5-8)
    * [5.9 WebPayItem::numberedOrderRow()](#i5-9)
* [6. WebPay entrypoint method reference](#i6)
    * [6.1 WebPay::createOrder()](#i6-1)
    * [6.2 WebPay::deliverOrder()](#i6-2)
    * [6.3 WebPay::getAddresses()](#i6-3)
    * [6.4 WebPay::getPaymentPlanParams()](#i6-4)
    * [6.5 WebPay::paymentPlanPricePerMonth()](#i6-5)
    * [6.6 WebPay::listPaymentMethods()](#i6-6)
    * [6.7 WebPay::getAccountCreditParams()](#i6-7)
    * [6.8 WebPay::checkout()](#i6-8)
* [7. WebPayAdmin entrypoint method reference](#i7)
    * [7.1 WebPayAdmin::queryTaskInfo](#i7-1)
    * [7.2 WebPayAdmin::cancelOrder()](#i7-2)
    * [7.3 WebPayAdmin::queryOrder()](#i7-3)
    * [7.4 WebPayAdmin::cancelOrderRows()](#i7-4)
    * [7.5 WebPayAdmin::creditOrderRows()](#i7-5)
    * [7.6 WebPayAdmin::addOrderRows()](#i7-6)
    * [7.7 WebPayAdmin::updateOrderRows()](#i7-7)
    * [7.8 WebPayAdmin::deliverOrderRows()](#i7-8)
    * [7.9 WebPayAdmin::updateOrder()](#i7-9)
    * [7.10 WebPayAdmin::creditAmount()](#i7-10)
    * [7.11 WebPayAdmin::creditOrderRows()](#i7-11)
* [8. SveaResponse and response classes](#i8)
    * [8.1. Parsing an asynchronous service response](#i8-1)
    * [8.2. Response accepted and result code](#i8-2)
* [9. Helper Class and Additional Developer Resources and Notes](#i9)
    * [9.1 Helper::paymentPlanPricePerMonth()](#i9-1)
    * [9.2 Request validateOrder(), prepareRequest(), getRequestTotals() methods](#i9-2)
* [10. Frequently Asked Questions](#i10)
    * [10.1 Supported currencies](#i10-1)
    * [10.2 Other payment method credentials](#i10-2)
    * [10.3 My store order totals does not match the order totals in Svea's systems](#i10-3)

* [APPENDIX](#appendix)

## I. Introduction

### Svea API

**New!** The WebPay class now includes an entrypoint to the Svea Checkout.

If you want a lightweight installation with only the checkout, you can also use the [php-checkout-integration library instead](https://github.com/sveawebpay/php-checkout-integration/blob/master/README.md)
Read more in the section [6.7 WebPay::checkout()](#i6-7) and if you wish to see more detailed data structures, please see the documentation in the [*connection library*](https://github.com/sveawebpay/php-checkout-integration).

The WebPay class methods contains the functions needed to create orders and perform payment requests using Svea payment methods. It contains methods to define order contents, send order requests, as well as support methods needed to do this.

The WebPayAdmin class methods are used to administrate orders after they have been accepted by Svea.
It includes methods that update, deliver, cancel and credit orders et.al. and can administrate all types of orders.

### Package design philosophy
In general, a request using the Svea API starts out with you creating an instance of an order builder class, which is then built up with data using fluent method calls. At a certain point, a method is used to select which service the request will go against. This method then returns an instance of a service request class which handles the specifics of building the request, which in turn returns an instance of the corresponding service response class for inspection.

The WebPay API consists of the entrypoint methods in the WebPay and WebPayAdmin classes. These instantiate builder classes in the Svea namespace. Given i.e. an order builder instance, you then use method calls to populate it with order rows and customer identifiction data. You then choose the payment method and get a request class in return. You then send the request and get a service response from Svea in return. In general, the request classes will validate that all required builder class attributes are present, and if not will throw an exception stating what methods are missing for the request in question.

### Synchronous and asynchronous requests
Most service requests are synchronous and return a response immediately. For asynchronous hosted service payment requests, the customer will be redirected to i.e. the selected card payment provider or bank, and you will get a callback to a return url, where where you receive and parse the response.

### Namespaces
The package makes use of PHP namespaces, grouping most classes under the namespace Svea. The entrypoint classes WebPay, WebPayAdmin and associated support classes are excluded from the Svea namespace. The underlying internal services and methods are contained in the Svea sub-namespaces WebService, HostedService and AdminService.

### Fluent API
The package is built as a fluent API so you can use method chaining when utilising the WebPay and WebPayAdmin entrypoint classes. Available methods should show up in the IDE along with their associated DocBlock, including information on which methods are required for the various payment methods. We recommend making sure that your IDE code completion is enabled to make full use of this feature.

[Back to top](#index)

## 1. Installing and configuration <a name="i1"></a>

### 1.1 Requirements<a name="i1-1"></a>
This integration package has the following requirements:
* PHP 5.3 or higher
* <a href="https://getcomposer.org/download/">Composer</a>
* SOAP needs to be enabled

Svea Checkout requires [**jQuery**](https://jquery.com) in order to be able to load the IFrame.

If you wish to run the package test suite, PHPUnit 3.7 is required.

### 1.2 Installation<a name="i1-2"></a>

First of run the following command in your command-line interface:

    Composer require sveaekonomi/webpay

or add this part to your composer.json:

```json
    {
        "require": {
            "sveaekonomi/webpay": "dev-master"
        }
    }
```

and then run this command in your command-line interface:

    composer update

Doing this will pull the library into your project and store it in the `vendor` folder with the name `sveaekonomi`.

When you are working with files that will use the library you need to include `vendor/autoload.php`

### 1.3 Configuration <a name="i1-3"></a>
In order to make use of the Svea services you need to supply your account credentials to authorize yourself against the Svea services. For the Invoice and Payment Plan payment methods, the credentials consist of a set of Username, Password and Client number (one set for each country and service type). For Card and Direct Bank payment methods, and also for using the Checkout, the credentials consist of a (single) set of Merchant id and Secret Word.

You should have received the above credentials from Svea when creating a service account. If not, please contact your Svea account manager.

### 1.4 Using your account credentials with the package <a name="i1-4"></a>
The WebPay and WebPayAdmin entrypoint methods all require a config object when called. The easiest way to get such an object is to use the ConfigurationService::getDefaultConfig() method. Per default, it returns a config object with the Svea test account credentials as used by the integration package test suite.

In order to use your own account credentials, either edit the config_test.php or config_prod.php file (depending on the desired environment) with your actual account credentials, or implement the ConfigurationProvider interface in a class of your own -- your implementation could for instance fetch the needed credentials from a database in place of the config files.

### 1.5 Additional integration properties configuration <a name="i1-5"></a>
You should also add information about your integration platform (i.e. Magento, OpenCart, or MyAwesomeECommerceSystem etc.), platform version and providing company. See ConfigurationProvider getIntegrationPlatform(), getIntegrationVersion() and getIntegrationCompany() methods, or add that information into config files. When configured, the integration properties information will be passed to Svea alongside the various service requests.

See the provided example of how to customize the config files in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/config_getaddresses/" target="_blank">example/config_getaddresses/</a> folder.

See the <a href="http://sveawebpay.github.io/php-integration/api/classes/ConfigurationProvider.html" target="_blank">ConfigurationProvider</a> interface and the provided <a href="http://github.com/sveawebpay/php-integration/blob/master/src/Config/config_test.php" target="_blank">example of config file</a> for more information.

[Back to top](#index)

## 2. "Hello World" <a name="i2"></a>
An example of the WebPay API workflow is the following invoice payment, where we wish to perform an invoice order. Let's assume that we have already collected all the needed order data, and will now build an order containing the ordered items (with price, article number info, et al) and customer information (name, address, et al), select a payment method, and send the payment request to Svea.

### 2.1 A complete invoice order <a name="i2-1"></a>
The following is a complete example of how to place an order using the invoice payment method:

```php
<?php
//Include the library
require_once 'vendor/autoload.php';

// Add namespaces
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Config\ConfigurationService;

//Display all errors in case something is wrong
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Get configuration object holding the Svea service login credentials
$myConfig = ConfigurationService::getDefaultConfig();

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


// If the response attribute accepted is true, the payment succeeded.
if($myResponse->accepted == true)
{
    echo "Invoice payment succeeded!";
};

// The response also contains a customerIdentity object containing the invoice address of the customer, which should match the order shipping address.
print_r($myResponse->customerIdentity);
?>
```

The above example can be found in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/firstorder/" target="_blank">example/firstorder</a> folder.

### 2.2 What just happened? <a name="i2-2"></a>
Above, we start out by calling the API method WebPay::createOrder(), which returns an instance of the CreateOrderBuilder class.

Then, the class methods setCountryCode(), setOrderDate(), addCustomerDetails(), and addOrderRow() are used to populate the OrderBuilder object with all required order information needed for an invoice order.

Then, the useInvoicePayment() method is called, returning an instance of the WebService\InvoicePayment class. We then call the doRequest() method, which validates the provided order information, and makes the request to Svea, returning an instance of the WebService\CreateOrderResponse class.

To determine the outcome of the payment request, we can then inspect the response attributes, i.e. check if $myResponse->accepted == true.

### 2.3 Oh, that's cool, but how to use the services directly? <a name="i2-3"></a>
The package structure enables the WebPay and WebPayAdmin entrypoint methods to confine themselves to the order domain, and pushes the various service request details lower into the package stack, away from the immediate viewpoint of the integrator. Thus all payment methods and services are accessed in a uniform way, with the package doing the main work of massaging the order data to fit the selected payment method or service request.

This also provides future compatibility, as the main WebPay and WebPayAdmin entrypoint methods stay stable whereas the details of how the services are being called by the package may change in the future.

That being said, there are no additional prohibitions on using the various service call wrapper classes to access the Svea services directly, while still not having to worry about the details on how to i.e. build the various SOAP calls or format the XML data structures.

It is possible to instantiate the service request classes directly, making sure to set all relevant attributes before performing service request. In general you need to set attributes in the internal request classes directly, as no setter methods are provided.

See further the package WebService, AdminService and HostedService namespaces for further information.

Now continue reading, and we'll work through the recommended WebPay order building procedure using the WebPay and WebPayAdmin entrypoint methods.

[Back to top](#index)

## 3. Building an order  <a name="i3"></a>
In this section we will show you the various steps necessary to create an order.

### 3.1 Order builder <a id="i3-1"></a>
Start by creating an order using the WebPay::createOrder method, for payment methods, or WebPay::checkout for checkout methods. Pass in your configuration and get an instance of OrderBuilder in return.

```php
<?php
...
$myOrderBuilder = WebPay::createOrder($myConfig);
//or
$myOrder = WebPay::checkout($myConfig);
...

```

### 3.2 Order row items <a id="i3-2"></a>
Order rows, fees and discount items can be added to the order. Together the row items amount add up to the order total that the customer will pay.

```php
<?php
...
$myOrderRow = WebPayItem::orderRow();       // create the order row object

$myOrderRow->setQuantity(1);                // required
$myOrderRow->setAmountExVat(10.00);          // recommended to specify price using AmountExVat & VatPercent
$myOrderRow->setVatPercent(25);              // recommended to specify price using AmountExVat & VatPercent

$myOrder->addOrderRow($myOrderRow);       // add order row to the order
...

/* the same code expressed in a more compact, fluent style:
$myOrder->addOrderRow( WebPayItem::orderRow()->setQuantity(1)->setAmountExVat(10.00)->setVatPercent(25) );
*/
```

See [5.2](#i5-2) to [5.6](#i5-6) in the WebPayItem class documentation below for more information.

### 3.3 Customer identity <a id="i3-3"></a>
Create a customer identity object using the WebPayItem::individualCustomer() or WebPayItem::companyCustomer() methods. Use the addCustomerDetails() method to add the customer information to the order.

A customer identity is required for invoice and payment plan orders. It's optional but recommended for card and direct bank orders.

####3.3.1 Options for individual customers
```php
<?php
...
$order->
    ...
    addCustomerDetails(
        WebPayItem::individualCustomer()
        ->setNationalIdNumber(194605092222)
        ->setName("Tess", "Testson")
        ->setStreetAddress("Gatan 23")
        ->setZipCode(9999)
        ->setLocality("Stan")
        ...
    )
;
...
```

See [5.7](#i5-7) and [5.8](#i5-8) in the WebPayItem class documentation below for more information on how to specify customer identity items.

### 3.4 Additional order attributes <a id="i3-4"></a>
Set any additional attributes needed to complete the order using the OrderBuilder methods.

```php
<?php
...
$order
    ...
    ->setCountryCode("SE")                  // required, Optional for hosted payments when using implementation of ConfigurationProvider Interface
    ->setCurrency("SEK")                    // required for card payment, direct bank & PayPage payments. Ignored for invoice and payment plan.
    ->setClientOrderNumber("A123456")       // Required for card payment, direct payment, Unique String(65). Optional for Invoice and Payment plan String(32).
    ->setCustomerReference("att: kgm")      // Optional for invoice and payment plan String(32), ignored for card & direct bank orders.
    ->setOrderDate("2012-12-12")            // required for invoice and payment plan payments
;
...
```

### 3.5 Payment method selection <a id="i3-5"></a>
There are two ways of using Svea Payments. Either by using the checkout or by implementing the payment methods directly. To integrate the checkout, read more in section [4.6 Svea Checkout](#i4-6).

For payment method implementation, finish the order builder specification process by choosing a payment method with the order builder useXX() methods. I.e. $myOrder->useInvoicePayment()

#### 3.5.1 Synchronous payments
Invoice and Payment plan payment methods will perform a synchronous request to Svea and return a response object which you can then inspect.

#### 3.5.2 Asynchronous payments
Hosted payment methods, like Card, Direct Bank and any payment methods accessed via the PayPage, are asynchronous.

After selecting an asynchronous payment method you generally use a request class method to get a payment form object in return. The form is then posted to Svea, where the customer is redirected to the card payment provider service or bank. After the customer completes the payment, a response is sent back to your provided return url, where it can be processed and inspected.

#### 3.5.3 Response URL:s
For asynchronous payment methods, you must specify where to receive the request response. Use the following methods:

`->setReturnUrl()` (required) When a hosted payment transaction completes the payment service will answer with a response xml message sent to the return url. This is also the return url used if the user cancels at i.e. the card payment page.

`->setCallbackUrl()` (optional) In case the hosted payment transaction completes, but the service is unable to return a response to the return url, Svea will retry several times using the callback url as a fallback, if specified. This may happen if i.e. the user closes the browser before the payment service redirects back to the shop, or if the transaction times out in lieu of user input. In the latter case, Svea will fail the transaction after at most 30 minutes, and will try to redirect to the callback url.

`->setCancelUrl()` (optional, pay page only) Presents a cancel button on the PayPage. In case the payment method selection is cancelled by the user, Svea will redirect back to the cancel url. Unless a cancel url is specified, no cancel button will be presented at the PayPage.

All of the above url:s should be specified in full, including the scheme part. I.e. always use an url on the format "http://myshop.com/callback", with a maximum length of 256 characters. (See http://www.w3.org/Addressing/URL/url-spec.txt). The callback url further needs to be publicly visible; it can't be on i.e. localhost or only accessible via a private ip address.


The service response received is sent as an XML message, use the SveaResponse response handler to get a response object. For more details, see [Svea Response](#i8) below.

### 3.6 Recommended payment method usage <a id="i3-6"></a>
*I am using the invoice and/or payment plan payment methods in my integration.*

>The best way is to use `->useInvoicePayment()` and `->usePaymentPlanPayment()`. These payment methods are synchronous and will give you an instant response.

*I am using the card and/or direct bank payment methods in my integration.*

>The best way if you know what specific payment you want to use, is to go direct to that specific payment, bypassing the PayPage step, by using
`->usePaymentMethod`. You can check the optional payment methods configured on your account using the `WebPay::getPaymentMethods()` method.
>
>You can also use the PayPage with `->usePayPageCardOnly()` and `->usePayPageDirectBankOnly()`.

*I am using all payment methods in my integration, and wish to let the customer select which one to use.*

>The most effective way is to use `->useInvoicePayment()` and `->usePaymentPlanPayment()` for the synchronous payments, and use the `->usePaymentMethod()` for the asynchronous requests. First use `WebPay::getPaymentMethods()` to fetch the different payment methods configured on you account.
>
>Alternatively you can go by *PayPage* for the asynchronous requests by using `->usePayPageCardOnly()` and `->usePayPageDirectBankOnly()`.

*I am using more than one payment and want them gathered on on place.*

>You can go by *PayPage* and choose to show all your payments here, or modify to exclude or include one or more payments. Use `->usePayPage()` where you can custom your own *PayPage*. This introduces an additional step in the customer checkout flow, though. Note also that Invoice and Payment plan payments will return an asynchronous when used from PayPage.

*I wish to prepare an order and receive a link that I can mail to a customer, who then will complete the order payment using their card. (url is valid up to one hour)*

>Create and build the order, then select the card payment method with the `->usePaymentMethod()`, but instead of getting a form for sending the customer to Webpay through HTTP POST with `->getPaymentForm()`, use `->getPaymentUrl()` to get an prepared url to send the customer to WebPay through a HTTP GET.

*I wish to set up a subscription using recurring card payments, which will renew each month without further end user interaction.*

>For recurring payments, first create an order and select a card payment method with `->usePaymentMethod()`. You then use the `setSubscriptionType()` method on the resulting payment request object. When the end user completes the transaction, you will receive a subscription id in the response.

>For subsequent recurring payments, you build an order and again select the card payment method with `->usePaymentMethod()`. Then use `setSubscriptionId()` with the subscription id from the initial request. Then send the payment request using the `->doRecur()` method.

>See also section 4.3.3.

[Back to top](#index)

## 4. Payment method reference <a name="i4"></a>
Select payment method to use with the CreateOrderBuilder class useXX() methods, which return an instance of the appropriate payment request class.

### 4.1 Svea Invoice payment  <a name="i4-1"></a>
Select ->useInvoicePayment() to perform an invoice payment.

```php
<?php
...
$order = WebPay::createOrder($config);
$order
    ->addOrderRow( ...                      // required, one or more
    ->addCustomerDetails( ...               // required, individualCustomer or companyCustomer
    ->setCountryCode("SE")                  // required* Optional for hosted payments when using implementation of ConfigurationProvider Interface
    ->setOrderDate("2012-12-12")            // required
;
$request = $order->useInvoicePayment();     // requires the above attributes in the order
$response = $request->doRequest();
...
```

Another complete, runnable example of an invoice order can be found in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/invoiceorder/" target="_blank">example/invoiceorder</a> folder.

### 4.2 Svea Payment plan payment  <a name="i4-2"></a>
Select ->usePaymentPlanPayment() to perform an invoice payment.

The Payment plan payment method is restricted to individual customers and can not be used by legal entities, i.e. companies or organisations.

First use WebPay::getPaymentPlanParams() to get the various campaigns. Then chose a campaign to pass as parameter to the usePaymentPlanPayment() method.

```php
<?php
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

### 4.3 Card payment  <a name="i4-3"></a>
Select i.e. ->usePaymentMethod(PaymentMethod::SVEACARDPAY) to perform a card payment via the SveaCardPay card payment provider.

#### 4.3.1 ->getPaymentForm()
Get a html form containing the request XML data. The form is an instance of PaymentForm, and also contains the complete html form as a string along with the form elements in an array.

```php
<?php
...
$form = $order
    ->usePaymentMethod(PaymentMethod::SVEACARDPAY)          // Card payment, get available providers using WebPay::listPaymentMethods()
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("sv")                         // Optional, languageCode As ISO639, eg. "en", default english
        ->getPaymentForm();
...
```

#### 4.3.2 ->getPaymentUrl()
Get an url containing a link to the prepared payment. A prepared payment is valid up to one hour after creation. To get a payment url you need to supply the customer ip address and language in the order request.

```php
<?php
...
$form = $order

    ->addCustomerDetails(
        ...
        ->setIpAddress()                                    // Required
        ...
    ->usePaymentMethod(PaymentMethod::SVEACARDPAY)          // Card payment, get available providers using WebPay::listPaymentMethods()
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("sv")                         // Required, languageCode As ISO639, eg. "en", default english
        ->getPaymentUrl();
...
```

#### 4.3.3 Recurring card payments
Recurring card payments are set up in two steps. First a card payment including the subscription request, where the customer enters their credentials, and then any subsequent recur payment requests, where the subscription id is used in lieu of customer interaction.

For recurring payments, first create an order and select a card payment method with `->usePaymentMethod()`. You then use the `setSubscriptionType()` method on the resulting payment request object. When the end user completes the transaction, you will receive a subscription id in the response.

For subsequent recurring payments, you build an order and again select the card payment method with `->usePaymentMethod()`. Then use `setSubscriptionId()` with the subscription id from the initial request. Then send the payment request using the `->doRecur()` method.

An example of an recurring card order, both the setup transaction and a recurring payment, can be found in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/cardorder_recur/" target="_blank">example/cardorder_recur</a> folder.

### 4.4 Direct bank payment   <a name="i4-4"></a>
Select i.e. ->usePaymentMethod(PaymentMethod::NORDEA_SE) to perform a direct bank transfer payment using the Swedish bank Nordea.

```php
<?php
...
$form = $order
    ->usePaymentMethod(PaymentMethod::NORDEA_SE)            // Direct bank payment, get available banks using WebPay::listPaymentMethods()
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("sv")                         // Optional, languageCode As ISO639, eg. "en", default english
        ->getPaymentForm();
...
```

### 4.5 Using the Svea PayPage   <a name="i4-5"></a>

#### 4.5.1 Bypassing payment method selection
Go direct to specified payment method, bypassing the *PayPage* completely. By specifying payment method you eliminate one step in the payment process.

You can use `WebPay::listPaymentMethods()` to get the various payment methods available.

```php
<?php
...
$form = $order
    ->usePaymentMethod(PaymentMethod::SVEACARDPAY)          // Use WebPay::listPaymentMethods() to get available payment methods
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("sv")                         // Optional, languageCode As ISO639, eg. "en", default english
        ->getPaymentForm();
...
```

#### 4.5.2 Select a card payment method
Send user to *PayPage* to select from available cards (only), and then perform a card payment at the card payment page.

```php
<?php
...
$form = $order
    ->usePayPageCardOnly()
        ->setPayPageLanguage("sv")                          // Optional, languageCode As ISO639, eg. "en", default english
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCallbackUrl("http://myurl.se")                 // Optional
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->getPaymentForm();
...
```

A complete, runnable example of a card order using PaymentMethodPayment can be found in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/cardorder/" target="_blank">example/cardorder</a> folder.

#### 4.5.3 Select a direct bank payment method
Send user to *PayPage* to select from available banks (only), and then perform a direct bank payment at the chosen bank

```php
<?php
...
$form = $order
    ->usePayPageDirectBankOnly()
        ->setPayPageLanguage("sv")                          // Optional, languageCode As ISO639, eg. "en", default english
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->getPaymentForm()
;
...
```

#### 4.5.4 Specifying from available payment methods
Send user to *PayPage* to select from the available payment methods.

```php
<?php
...
$form = $order
    ->usePayPage()
        ->setPayPageLanguage("sv")                          // Optional, languageCode As ISO639, eg. "en", default english
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->getPaymentForm()
;
...
```

You can customise which payment methods to display, using the PayPagePayment methods `includePaymentMethods()`, `excludePaymentMethods()`, `excludeCardPaymentMethods()` and `excludeDirectPaymentMethods()`.

Available payment methods are listed in the PaymentMethod class and the [Appendix](#appendix).

### 4.6 Svea Checkout <a name="i4-6"></a>
The checkout offers a complete solution with a variety of payment methods. The underlying systems for the checkout is our payment plan, invoice, account payments. 
Also including our own payment gateway with PCI level 1 for card payments. 
The checkout supports both B2C and B2B payments, fast customer identification and caches customers behaviour.

#### 4.6.1 Create Checkout Order <a name="i4-6-1"></a>
Creates a new checkout order and returns the order information and the Gui needed to display the IFramed Svea checkout.
Returns full order response.

[See full Create order example](example/checkout/createCheckoutOrder.php)

Checkout order is being created through **WebPay** class.

```php
<?php
    $orderBuilder = WebPay::checkout($myConfig);
 ```

Example for populating order with required fields.

```php
<?php

    $orderBuilder->setCountryCode('SE')// customer country, we recommend basing this on the customer billing address
    ->setCurrency('SEK')
        ->setClientOrderNumber('123-das-555-32')// required and Unique
        ->setCheckoutUri('http://localhost:51925/') //required
        ->setConfirmationUri('http://localhost:51925/checkout/confirm') //required
        ->setPushUri('https://svea.com/push.aspx?sid=123&svea_order=123') //required
        ->setTermsUri('http://localhost:51898/terms') //required
        ->setLocale('sv-Se'); // required
  ```

#### 4.6.1.1 Preset values
If you set preset values, the values will be populated in the checkout gui form when it's loaded. The fields can be "read only" or editable.

```php
<?php
    $presetPhoneNumber = WebPayItem::presetValue()
        ->setTypeName(\Svea\WebPay\Checkout\Model\PresetValue::PHONE_NUMBER)
        ->setValue('some value')
        ->setIsReadonly(true);

    $orderBuilder->addPresetValue($presetPhoneNumber);
```

#### 4.6.1.2 Order Rows
To be able to connect the Svea order row Id with your order row id you can use the setTemporaryReference function.
The Svea order row id will be returned in the create order response.
By setting the setTemporaryReference you will also get the temporary reference in the response.

```php
    <?php
    WebPayItem::fixedDiscount()->setAmountIncVat(10); // this is 10 kr for example, not percent

    // create and add items to order
    $firstBoughtItem = WebPayItem::orderRow()
        ->setAmountIncVat(100.00)// - required
        ->setVatPercent(25)// - required
        // - if you want to use discount use
        ->setQuantity(1)
        ->setDiscountPercent(20)
        ->setArticleNumber('123')
        ->setTemporaryReference('230') // optional. Checkout orders only. Will not be applicable for other order types.
        ->setName('Fork');

    $secondBoughtItem = WebPayItem::orderRow()
        ->setAmountIncVat(10.00)
        ->setVatPercent(12)
        ->setQuantity(2)
        ->setDescription('Korv med bröd')
        ->setArticleNumber('321')
        ->setTemporaryReference('231') // optional. Checkout orders only. Will not be applicable for other order types.
        ->setName('Fork');

    $discountItem = WebPayItem::fixedDiscount()
        ->setName('Promo coupon')
        ->setVatPercent(10)
        ->setAmountIncVat(20);

    $shippingItem = WebPayItem::shippingFee()
        ->setAmountIncVat(17.60)
        ->setVatPercent(10)
        ->setName('incvatShippingFee');

    $orderBuilder->addOrderRow($firstBoughtItem);
    $orderBuilder->addOrderRow($secondBoughtItem);
    $orderBuilder->addDiscount($discountItem);
    $orderBuilder->addFee($shippingItem);
```

#### 4.6.1.3 Run request

```php
    $response = $orderBuilder->createOrder();
```

#### 4.6.2 Get Checkout Order <a name="i4-6-2"></a>
Get information about the order.
Returns full order response.

```php
<?php
    $orderBuilder = WebPay::checkout($config)
```

Now when we have initialized orderBuilder we can pass to it CheckoutOrderId

and optional country code.
```php
<?php
     $orderBuilder->setCheckoutOrderId(50)
                  ->setCountryCode('SE');
```
Only thing that is left is to call getOrder method to get response from Api.

```php
<?php
     $response = $orderBuilder->getOrder();
```

#### 4.6.3 Update Order <a name="i4-6-3"></a>
You can use the update order function to update a created order. Use this function if consumer changes the cart after an order has been created.
Returns full order response.

```php
<?php
    $orderBuilder = WebPay::checkout($myConfig);

    $orderBuilder->setCheckoutOrderId(5)
                 ->setCountryCode('SE');

    // create and add items to order
    $firstBoughtItem = WebPayItem::orderRow()
        ->setAmountExVat(10.99)
        ->setVatPercent(25)
        ->setQuantity(1)
        ->setDescription("Billy")
        ->setArticleNumber("123456789A")
        ->setTemporaryReference('230') // optional. Checkout orders only. Will not be applicable for other order types.
        ->setName('Fork');

    $secondBoughtItem = WebPayItem::orderRow()
        ->setAmountIncVat(5.00)
        ->setVatPercent(12)
        ->setQuantity(2)
        ->setDescription("Korv med bröd")
        ->setArticleNumber("123456789B")
        ->setTemporaryReference('231') // optional. Checkout orders only. Will not be applicable for other order types.
        ->setName('Fork');

    $orderBuilder->addOrderRow($firstBoughtItem);
    $orderBuilder->addOrderRow($secondBoughtItem);
```
#### 4.6.4 Response <a name="i4-6-4"></a>
The checkout methods will return an array with the response data. The response contains information about the Cart,
merchantSettings, Customer and the Gui for the checkout.

| Parameters OUT                | Type                 | Description |
|-------------------------------|----------------------|-------------|
| MerchantSettings              | MerchantSettings     | Specific merchant URIs |
| Cart                          | Cart                 | A cart-object containing the OrderRows |
| Gui                           | Gui                  | See Gui data structure in the [*connection library*](https://github.com/sveawebpay/php-checkout) |
| Customer                      | Customer             | Identified Customer of the order. |
| ShippingAddress               | Address              | Shipping Address of identified customer. |
| BillingAddress                | Address              | Billing Address of identified customer. |
| Locale                        | String               | The current locale of the checkout, i.e. sv-SE etc. |
| Currency                      | String               | The current currency as defined by ISO 4217, i.e. SEK, NOK etc. |
| CountryCode                   | String               | Defined by two-letter ISO 3166-1 alpha-2, i.e. SE, DE, FI etc.  |
| ClientOrderNumber             | String               | A string with maximum of 32 characters that identifies the order in the merchant’s systems |
| PresetValues                  | List of PresetValues | Preset values chosen by the merchant to be prefilled and eventually locked for changing by the customer. |
| OrderId                       | Long                 | checkoutOrderId of the order. |
| Status                        | CheckoutOrderStatus | The current state of the order |
| EmailAddress                  | String               | The customer’s email address |
| PhoneNumber                   | String               | The customer’s phone number |
| PaymentType                   | String               | The final payment method for the order. Will only have a value when the order is locked, otherwise null. |

See section 7 in the [*connection library*](https://github.com/sveawebpay/php-checkout) for more details regarding the data structures.

Sample response
```
Array
(
    [MerchantSettings] => Array
        (
            [TermsUri] => http://localhost:51898/terms
            [CheckoutUri] => http://localhost:51925/
            [ConfirmationUri] => http://localhost:51925/checkout/confirm
            [PushUri] => https://svea.com/push.aspx?sid=123&svea_order=123
        )

    [Cart] => Array
        (
            [Items] => Array
                (
                    [0] => Array
                        (
                            [ArticleNumber] => 123456789
                            [Name] => Dator
                            [Quantity] => 200
                            [UnitPrice] => 12300
                            [DiscountPercent] => 1000
                            [VatPercent] => 2500
                            [Unit] =>
                            [TemporaryReference] => "230"
                        )
                    [1] => Array
                        (
                            [ArticleNumber] => SHIPPING
                            [Name] => Shipping Fee Updated
                            [Quantity] => 100
                            [UnitPrice] => 4900
                            [DiscountPercent] => 0
                            [VatPercent] => 2500
                            [Unit] =>
                            [TemporaryReference] => "231"
                        )
                )
         )
    [Customer] =>
    [ShippingAddress] =>
    [BillingAddress] =>
    [Gui] => Array
        (
            [Layout] => desktop
            [Snippet] =>
        )
    [Locale] => sv-SE
    [Currency] =>
    [CountryCode] =>
    [PresetValues] =>
    [OrderId] => 9
    [Status] => Created
    [PaymentType] => NULL
)
```

The checkout Gui contains the Snippet and the Layout. The Snippet contains the Html and JavaScript that you implement on your
page where you want to display the IFramed checkout. The Layout is a String defining the orientation of the customers screen.

```php
echo $response['Gui']['Snippet']
```

##### Callbacks
Two callbacks, at two different stages in the consumers order flow, are sent as POST to the pushUri with the checkout order id.
eg. http://localhost:63473/shop/orderCallback/{checkout.order.uri}

When your server receives a callback it's a notification that something has changed in the order. Make a GetOrder request to get the latest order data.

[Back to top](#index)

### 4.7 Account Credit <a name="i4-7"></a>

Select ->useAccountCredit() to perform an accountCredit payment.

```php
<?php
...
$order = WebPay::createOrder($config);
$order
    ->addOrderRow( ...                      // required, one or more
    ->addCustomerDetails( ...               // required, individualCustomer or companyCustomer
    ->setCountryCode("SE")                  // required* Optional for hosted payments when using implementation of ConfigurationProvider Interface
    ->setCurrency("SEK")                    // required
    ->setOrderDate("2012-12-12")            // required
;
$request = $order->useAccountCredit("111111");     // required campaign code, see example for getting campaigns
$response = $request->doRequest();
...
```

Another complete, runnable example of an invoice order can be found in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/accountcredit/" target="_blank">example/accountcredit</a> folder.



### 4.8 Examples  <a name="i4-8"></a>

#### 4.8.1 Svea checkout order
Full Checkout examples order can be found in the [example/checkout](example/checkout) folder.

#### 4.8.2 Svea invoice order
An example of a synchronous (invoice) order can be found in the [example/invoiceorder](example/invoiceorder) folder.

#### 4.8.3 Card order
An example of an asynchronous card order can be found in the [example/cardorder](example/cardorder) folder.

#### 4.8.4 Recurring card order
An example of an recurring card order, both the setup transaction and a recurring payment, can be found in the [example/cardorder_recur](example/cardorder_recur) folder.

#### 4.8.5 Svea Account Credit
An example of an recurring card order, both the setup transaction and a recurring payment, can be found in the [example/accoutncredit(example/accoutncredit) folder.


[Back to top](#index)

## 5. WebPayItem reference <a name="i5"></a>

The WebPayItem class provides entrypoint methods to the different row items that make up an order, as well as the customer identity information items.

An order must contain one or more order rows. You may add invoice fees, shipping fees and discounts to an order.

Note that while it is possible to add multiples of fee and discount rows, the package will group rows according to type before sending them to Svea:

    1. all order rows, in the order they were added using addOrderRow()
    2. any shipping fee rows, in the order they were added using addShippingFee()
    3. any invoice fee rows, in the order they were added using addShippingFee()
    4. any fixed discount rows, in the order they were added using addFixedDiscount()
    5. any relative discount rows, in the order they were added using addRelativeDiscount()

Also, for relative discounts, or fixed discounts specified using only setAmountIncVat() or only setAmountExVat() there may be several discount rows added, should the order include more than one different vat rate. It is not recommended to specify more than one relative discount row per order, or more than one fixed discount specified using only setAmountIncVat() or only setAmountExVat().

### 5.1 Specifying item price <a name="i5-1"></a>
Specify item price using precisely two of these methods in order to specify the item price and tax rate: `setAmountIncVat()`, `setVatPercent()` and `setAmountExVat()`.

The recommended way to specify an item price is by using the setAmountIncVat() and setVatPercent() methods. This will ensure that the total order amount and vat sums precisely match the amount and vat specified in the order items.

When using setAmountExVat() and setVatPercent(), the service will work with less accuracy and may accumulate rounding errors, resulting in order totals differing from total of the amount and vat specified in the row items. It is not recommended to specify price using the setAmountExVat() method.

When using setAmountIncVat() with setAmountExVat() to specify an item price, the package converts the price to amount including vat and vat percent, in an effort to maintain maximum accuracy.

Note: when using WebPayAdmin functions with order rows, you may only use WebPayItem::orderRow with price specified as amountExVat and vatPercent.

### 5.2 WebPayItem::orderRow() <a name="i5-2"></a>

The WebPayItem::orderRow() entrypoint method is used to specify order items like products and services.
It is required to have a minimum of one order row in an order.

Specify the item price using precisely two of these methods in order to specify the item price and tax rate:
setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().

If you use setAmountIncVat(), note that this may introduce a cumulative rounding error when ordering large
quantities of an item, as the package bases the total order sum on a calculated price ex. vat.

```php
<?php
...
$orderrow = WebPayItem::orderRow()
   ->setAmountExVat(100.00)        // optional, Float, recommended, use precisely two of the price specification methods
   ->setVatPercent(25)             // optional, Integer, recommended, use precisely two of the price specification methods
   ->setAmountIncVat(125.00)       // optional, Float, use precisely two of the price specification methods
   ->setQuantity(2)                // required
   ->setUnit("pcs")               // optional, String(3) for invoice and payment plan, String(64) for card and direct bank
   ->setName('name')               // optional, invoice & payment plan orders will merge "name" with "description", String(256) for card and direct
   ->setDescription("description") // optional, String(40) for invoice & payment plan orders will merge "name" with "description" , String(512) for card and direct
   ->setArticleNumber("1")         // optional, String(10) for invoice and payment plan, String (256) for card and direct
   ->setDiscountPercent(0)         // optional
;
...
```

### 5.3 WebPayItem::shippingFee() <a name="i5-3"></a>

The WebPayItem::shippingFee() entrypoint method is used to specify order shipping fee rows.
It is not required to have a shipping fee row in an order.

Specify the item price using precisely two of these methods in order to specify the item price and tax rate:
setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().

```php
<?php
...
$shippingFee = WebPayItem::shippingFee()
   ->setAmountExVat(100.00)       //  optional, Float, recommended, use precisely two of the price specification methods
   ->setVatPercent(25)             // optional, Integer, recommended, use precisely two of the price specification methods
   ->setAmountIncVat(125.00)       // optional, Float, use precisely two of the price specification methods
   ->setUnit("pcs")               // optional, String(3) for invoice and payment plan, String(64) for card and direct bank
   ->setName('name')              // optional, invoice & payment plan orders will merge "name" with "description", String(256) for card and direct
   ->setDescription("description") // optional, String(40) for invoice & payment plan orders will merge "name" with "description" , String(512) for card and direct
   ->setShippingId('33')           // optional
   ->setDiscountPercent(0)
;         // optional
...
```

### 5.4 WebPayItem::invoiceFee() <a name="i5-4"></a>

The WebPayItem::invoiceFee() entrypoint method is used to specify fees associated with a payment method (i.e. invoice fee).
It is not required to have an invoice fee row in an order.

Specify the item price using precisely two of these methods in order to specify the item price and tax rate:
setAmountExVat(), setAmountIncVat() and setVatPercent(). We recommend using setAmountExVat() and setVatPercentage().

```php
<?php
...
$invoiceFee = WebPayItem::invoiceFee()
   ->setAmountExVat(100.00)        // optional, Float, recommended, use precisely two of the price specification methods
   ->setVatPercent(25)             // optional, Integer, recommended, use precisely two of the price specification methods
   ->setAmountIncVat(125.00)       // optional, Float, use precisely two of the price specification methods
   ->setUnit("pcs")               // optional, String(3)
   ->setName('name')               // optional, will merge "name" with "description"
   ->setDescription("description") // optional, String(40) will merge "name" with "description"
   ->setDiscountPercent(0)
;         // optional
...
```

### 5.5 WebPayItem::fixedDiscount() <a name="i5-5"></a>
Use WebPayItem::fixedDiscount() when the discount or coupon is expressed as a fixed discount amount.

If no vat rate is given, we calculate the discount split across the order row vat rates present in the order.
This will ensure that the correct discount vat is applied to the order.

If there are several vat rates present in the order, the discount will be split proportionally across the order row vat
rates. For examples, including the resulting discount rows, see the test suite file UnitTest/InvoicePaymentTest.php.

Otherwise, it is required to use at least two of the functions setAmountExVat(), setAmountIncVat() and setVatPercent().
If two of these three attributes are specified, we honour the amount indicated and the given discount tax rate.

```php
<?php
...
     $fixedDiscount = WebPayItem::fixedDiscount()
         ->setAmountIncVat(100.00)               // optional, Float, use precisely two of the price specification methods
         ->setAmountExVat(1.0)                   // optional, Float, recommended, use precisely two of the price specification methods
         ->setVatPercent(25)                     // optional, Integer, recommended, use precisely two of the price specification methods
         ->setDiscountId("1")                    // optional
         ->setUnit("st")                         // optional
         ->setName("Fixed")                      // optional, invoice & payment plan orders will merge "name" with "description", String(256) for card and direct
         ->setDescription("FixedDiscount");       // optional, String(40) for invoice & payment plan orders will merge "name" with "description" , String(512) for card and direct
...
```

### 5.6 WebPayItem::relativeDiscount() <a name="i5-6"></a>
Use WebPayItem::relativeDiscount() when the discount or coupon is expressed as a percentage of the total product amount.

The discount will be calculated based on the total sum of all order rows specified using addOrderRow(), it does not
apply to invoice or shipping fees.

If there are several vat rates present in the order, the discount will be split proportionally across the order row vat
rates. For examples, including the resulting discount rows, see the test suite file UnitTest/InvoicePaymentTest.php.

Specify the discount using RelativeDiscount methods:

```php
<?php
...
$relativeDiscount = WebPayItem::relativeDiscount()
  ->setDiscountPercent(10.0)          // required
  ->setDiscountId("1")                // optional
  ->setUnit("st.")                    // optional
  ->setName("DiscountName")           // optional, invoice & payment plan orders will merge "name" with "description", String(256) for card and direct
  ->setDescription("DiscountDesc.")   // optional, String(40) for invoice & payment plan orders will merge "name" with "description" , String(512) for card and direct
;
...
```

### 5.7 WebPayItem::individualCustomer() <a name="i5-7"></a>
Use WebPayItem::individualCustomer() to add individual customer information to an order.

#### 5.7.1 Using IndividualCustomer when specifying an order
Note that "required" below as a requirement only when using the invoice or payment plan payment methods, and that the required attributes vary between countries.

(For card and direct bank orders, adding customer information to the order is optional, unless you're using getPaymentUrl() to set up a prepared payment.)

```php
<?php
...
$individual = WebPayItem::individualCustomer()
  ->setNationalIdNumber() // Numeric	// invoice, payment plan: required for customers in SE, NO, DK, FI
  ->setName()             // String	// invoice, payment plan: required, use (firstName, lastName) for customers in NL and DE
  ->setBirthDate()        // Numeric	// invoice, payment plan: required for individual customers in NL and DE, use date format YYYY-MM-DD
  ->setInitials()         // String	// invoice, payment plan: required for individual customers in NL
  ->setCoAddress()      	// String	// invoice, payment plan: optional
  ->setStreetAddress()    // String	// invoice, payment plan: required, use (street, houseNumber) in NL and DE
  ->setZipCode()           // String	// invoice, payment plan: required in NL and DE
  ->setLocality()         // String	// invoice, payment plan: required in NL and DE
  ->setPhoneNumber()      // String	// invoice, payment plan: optional but desirable
  ->setEmail()         	// String	// invoice, payment plan: optional but desirable
  ->setIpAddress()       	// String	// invoice, payment plan: optional but desirable; card: required for getPaymentUrl() orders only
  ->setPublicKey()        // String       // invoice, payment plan: optional; identifier for selecting a specific pre-approved address
;
...
```

### 5.8 WebPayItem::companyCustomer() <a name="i5-8"></a>
Use WebPayItem::companyCustomer() to add individual customer information to an order.

Note that "required" below as a requirement only when using the invoice or payment plan payment methods, and that the required attributes vary between countries.

(For card and direct bank orders, adding customer information to the order is optional, unless you're using getPaymentUrl() to set up a prepared payment.)

```php
<?php
...
$companyCustomer = WebPayItem::companyCustomer()
  ->setNationalIdNumber() // Numeric	// invoice: required for customers in SE, NO, DK, FI
  ->setCompanyName()      // String	// invoice: required (companyName) for company customers in NL and DE
  ->setVatNumber()        // Numeric	// invoice: required for company customers in NL and DE
  ->setCoAddress()        // String	// invoice: optional
  ->setStreetAddress()    // String	// invoice: required, use (street, houseNumber) in NL and DE
  ->setZipCode()           // String	// invoice: required in NL and DE
  ->setLocality()         // String	// invoice: required in NL and DE
  ->setPhoneNumber()      // String	// invoice: optional but desirable
  ->setEmail()            // String	// invoice: optional but desirable
  ->setIpAddress()        // String	// invoice: optional but desirable; card: required for getPaymentUrl() orders only
  ->setAddressSelector()  // String	// invoice: optional but recommended; received from WebPay::getAddresses() request response
  ->setPublicKey()        // String       // invoice, payment plan: optional; identifier for selecting a specific pre-approved address
;
...
```

### 5.9 WebPayItem::numberedOrderRow() <a name="i5-9"></a>
This is an extension of the orderRow class, used in the WebPayAdmin::queryOrder() response and methods that administer individual order rows.

```php
<?php
...
$myNumberedOrderRow = WebPayItem::numberedOrderRow() //inherited from OrderRow
  ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
  ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
  ->setAmountIncVat(125.00)               // optional, need to use two out of three of the price specification methods
  ->setQuantity(2)                        // required
  ->setUnit("st")                         // optional
  ->setName('Prod')                       // optional
  ->setDescription("Specification")       // optional
  ->setArticleNumber("1")                 // optional
  ->setDiscountPercent(0)                 // optional

  //numberedOrderRow
  ->setCreditInvoiceId($creditInvoiceIdAsNumeric)         //optional
  ->setInvoiceId($invoiceIdAsNumeric)                     //optional
  ->setRowNumber($rowNumberAsNumeric)                     //required for updateOrderRow
  ->setStatus(NumberedOrderRow::ORDERROWSTATUS_DELIVERED) //optional, one of _DELIVERED, _NOTDELIVERED, _CANCELLED
;
...
```

[Back to top](#index)

## 6. WebPay entrypoint method reference <a name="i6"></a>
The WebPay class methods contains the functions needed to create orders and perform payment requests using Svea payment methods. It contains entrypoint methods used to define order contents, send order requests, as well as various support methods needed to do this.

* [6.1 WebPay::createOrder()](#i6-1)
* [6.2 WebPay::deliverOrder()](#i6-2)
* [6.3 WebPay::getAddresses()](#i6-3)
* [6.4 WebPay::getPaymentPlanParams()](#i6-4)
* [6.5 WebPay::paymentPlanPricePerMonth()](#i6-5)
* [6.6 WebPay::listPaymentMethods()](#i6-6)
* [6.7 WebPay::checkout()](#i6-7)

### 6.1 WebPay::createOrder() <a name="i6-1"></a>


Use WebPay::createOrder() to create an order using invoice, payment plan, card, or direct bank payment methods.

See the CreateOrderBuilder class for more info on methods used to specify the order builder contents
and choosing a payment method to use, followed by sending the request to Svea and parsing the response.

See the CreateOrderBuilder class for more info on methods used to specify the order builder contents
and choosing a payment method to use, followed by sending the request to Svea and parsing the response.

Invoice and Payment plan orders will perform a synchronous payment on doRequest(), and will return a response
object immediately.

Card, Direct bank, and other hosted methods accessed via PayPage are asynchronous. They provide an html form
containing a formatted message to send to Svea, which in turn will send a request response to a given return url,
where the response can be parsed using the SveaResponse class.

```php
<?php
...
$order = WebPay::createOrder($config)
  ->addOrderRow($orderrow)          // required, see WebPayItem::orderRow
  ->addFee($shippingfee)            // optional, see WebPayItem for invoice, shipping fee
  ->addDiscount($discount)          // optional, see WebPayItem for fixed, relative discount
  ->addCustomerDetails($customer)   // required for invoice and payment plan payments, see WebPayItem for individual, company id.
  ->setCountryCode("SE")              // required* Optional for hosted payments when using implementation of ConfigurationProvider Interface
  ->setOrderDate(date('c'))           // required for invoice and payment plan payments
  ->setCurrency("SEK")                // required for card payment, direct bank & PayPage payments. Ignored for invoice and payment plan.
  ->setClientOrderNumber("A123456")   // Required for card payment, direct payment, Unique String(65). Optional for Invoice and Payment plan String(32).
  ->setCustomerReference("att: kgm")  // Optional for invoice and payment plan String(32), ignored for card & direct bank orders.
;
...
```

### 6.2 WebPay::deliverOrder() <a name="i6-2"></a>

Use the WebPay::deliverOrder() entrypoint when you deliver an order to the customer.
Supports Invoice, Payment Plan and Card orders. (Direct Bank orders are not supported.)

The deliver order request should generally be sent to Svea once the ordered
items have been sent out, or otherwise delivered, to the customer.

For invoice and part payment orders, the deliver order request triggers the
invoice being sent out to the customer by Svea. (This assumes that your account
has auto-approval of invoices turned on, please contact Svea if unsure).

For card orders, the deliver order request confirms the card transaction,
which in turn allows nightly batch processing of the transaction by Svea.
(Delivering card orders is only needed if your account has auto-confirm
turned off, please contact Svea if unsure.)

To deliver an invoice, part payment or card order in full, you do not need to
specify order rows. To partially deliver an order, the recommended way is to
use WebPayAdmin::deliverOrderRows().

For more information on using deliverOrder to partially deliver and/or credit
an order, see 6.2.3 below.

Get an order builder instance using the WebPay::deliverOrder entrypoint, then
provide more information about the transaction using DeliverOrderBuilder methods:

```php
<?php
...
$request = WebPay::deliverOrder($config)
   ->setOrderId()                  // invoice, payment plan and accountCredit only - required
   ->setTransactionId()            // card only, optional, alias for setOrderId
   ->setCountryCode()              // required
   ->setInvoiceDistributionType()  // invoice only and accountCredit, required
   ->setNumberOfCreditDays()       // invoice only, optional
   ->setCaptureDate()              // card only, optional
   ->addOrderRow()                 // deprecated, optional -- use WebPayAdmin::deliverOrderRows instead
   ->setCreditInvoice()            // deprecated, optional -- use WebPayAdmin::creditOrderRows instead
;
// then select the corresponding request class and send request
$response = $request->deliverInvoiceOrder()->doRequest();       // returns DeliverOrdersResponse (no rows) or DeliverOrderResult (with rows)
$response = $request->deliverPaymentPlanOrder()->doRequest();   // returns DeliverOrdersResponse (no rows) or DeliverOrderResult (with rows)
$response = $request->deliverCardOrder()->doRequest();          // returns ConfirmTransactionResponse
$response = $request->deliverAccountCreditOrder()->doRequest(); // returns DeliverOrderResponse
...
```

#### 6.2.1 Usage
<!-- DeliverOrderBuilder docblock below -->
DeliverOrderBuilder collects and prepares order data for use in a deliver order request to Svea.

Use setOrderId() to specify the Svea order id, this is the order id returned with the original create order request response. For card orders, you can optionally use setTransactionId() instead.

Use setCountryCode() to specify the country code matching the original create order request.

Use setInvoiceDistributionType() with the DistributionType matching how your account is configured to send out invoices. (Please contact Svea if unsure.)

Use setNumberOfCreditDays() to specify the number of credit days for an invoice.

(Deprecated -- to partially deliver an invoice order, you can specify order rows to deliver using the addOrderRows() method. Use the WebPayAdmin::deliverOrderRows entrypoint instead.)

(Deprecated -- to issue a credit invoice, you can specify credit order rows to deliver using setCreditInvoice() and addOrderRows(). Use the WebPayAdmin::creditOrderRow entrypoint instead.)

To deliver an invoice, part payment or card order in full, use the WebPay::deliverOrder entrypoint without specifying order rows.

When specifying orderRows, WebPay::deliverOrder is used in a similar way to WebPay::createOrder and makes use of the same order item information. Add order rows that you want delivered and send the request, specified rows will automatically be matched to the rows sent when creating the order.

We recommend storing the createOrder orderRow objects to ensure that deliverOrder order rows match. If an order row that was present in the createOrder request is not present in from the deliverOrder request, the order will be partially delivered, and any left out items will not be invoiced by Svea. You cannot partially deliver payment plan orders, where all un-cancelled order rows will be delivered.

#### 6.2.2 Example
The following is a minimal example of how to deliver in its entirety an invoice order:

```php
<?php
require_once 'vendor/autoload.php';

// Add namespaces
use Svea\WebPay\WebPay;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;

//Display all errors in case something is wrong
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Get configuration object holding the Svea service login credentials
$myConfig = ConfigurationService::getDefaultConfig();

// We assume that you've previously run the firstorder.php file and successfully made a createOrder request to Svea using the invoice payment method.
$mySveaOrderId = "123456";

// Begin the order creation process by creating an order builder object using the WebPay::createOrder() method:
$myOrder = WebPay::deliverOrder($myConfig);

// We then add information to the order object by using the various methods in the Svea\DeliverOrderBuilder class.

// We begin by adding any additional information required by the payment method, which for an invoice order means:
$myOrder->setCountryCode("SE");
$myOrder->setOrderId($mySveaOrderId);
$myOrder->setInvoiceDistributionType(DistributionType::POST);

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select the invoice payment method:
$myDeliverOrderRequest = $myOrder->deliverInvoiceOrder();

// Then send the request to Svea using the doRequest method, and immediately receive the service response object
$myResponse = $myDeliverOrderRequest->doRequest();
?>
```
The above example can be found in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/firstdeliver/" target="_blank">example/firstdeliver</a> folder.

#### 6.2.3 On using WebPay::deliverOrder with order rows
WebPay::deliverOrder may be used to partially deliver, amend or credit an order, by specifying order rows using the DeliverOrderBuilder addOrderRow() method. We recommend using WebPayAdmin::deliverOrderRows to partially deliver an order and WebPayAdmin::creditOrderRows to credit an order.

##### 6.2.3.1 Partial delivery using WebPay::deliverOrder
When using WebPay::deliverOrder to partially deliver an order, care must be taken that the order rows to deliver precisely match the order row specification used in the original WebPay::createOrder request. Unless all order rows in the deliverOrder request exactly match rows in the original createOrder request, unmatched order rows in the original order will be cancelled. See also 6.2.3.2 below.

If on the other hand all deliver order rows match with original order rows, then the original order rows matched by the deliver order rows will be invoiced, with the invoice id being returned in the DeliverOrderResponse. The remaining original order rows will remain undelivered and may be delivered in a subsequent deliverOrder request.

```
Example:
1. cResponse = WebPay::createOrder()->addOrderRows(A)->addOrderRows(B)->addOrderRows(C)->...->doRequest();
2. dResponse = WebPay::deliverOrder()->addOrderRows(A)->...->doRequest(); // A matches A
Will result in the order having status
A: delivered	// found on invoice # dResponse->getInvoiceId()
B: undelivered  // may be delivered later
C: undelivered  // may be delivered later
```

##### 6.2.3.2 Amending an order using WebPay::deliverOrder
If you wish to add an order row to an existing order, any original order rows still undelivered will be cancelled to make room for the added order rows within the original order total amount (you may deliver order rows in the same request by adding order rows that exactly match the original order rows).

The exact behaviour is that if there are order rows in the deliver order request that does not match any undelivered original order row, all unmatched and undelivered original order rows are cancelled, and the unmatched deliver order rows are added to the original order as new delivered order rows, given that as the total of all existing delivered rows and the newly added order rows does not exceed the total original order row total amount. This means that the sum of the unmatched (i.e. added) deliver order rows cannot exceed the sum of the cancelled original order rows.

When there are delivered order rows to an amount equal to the original order total amount the order will be closed, preventing further modification. Delivered order rows can only be credited, see also 6.2.3.3 below.

```
Example (cont. from 6.2.3.1):
3. dResponse2 = WebPay::deliverOrder()->addOrderRows(D)->...->doRequest(); // D does not match any rows
Will result in the order having status
A: delivered	// found on invoice1; dResponse->getInvoiceId()
B: cancelled
C: cancelled
D: delivered	// found on invoice2; dResponse2->getInvoiceId()
```

##### 6.2.3.3 Crediting a (partially) delivered order using WebPay::deliverOrder
To credit an order use the setCreditInvoice(invoiceId) method when delivering an order. Add an order row made out to the amount to be credited to the deliver order request. A credit invoice with the order rows specified will be issued to the customer.

When crediting a delivered order, you are really crediting an invoice. This means that if you i.e. partially delivered an order, and then need to credit the entire order, you will need to make several credit requests, as a credit invoice amount can't exceed the individual invoice total amount.

The invoice id received will point to the new credit invoice itself, and the original invoice will be be credited at Svea by the specified amount. Note that the original order row status will not change, the as the request operates on the invoice, not the order in itself.

```
Example (cont. from 6.2.3.2):
4. dResponse3 = WebPay::deliverOrder()->addOrderRows(E)->setCreditInvoice(invoice1)...->doRequest();
//To credit i.e. 50% of the price for order row A we created a new order row E with half the price of A.
//The credit invoice id is returned in dResponse3->getInvoiceId()
```

### 6.3 WebPay::getAddresses() <a name="i6-3"></a>

The WebPay::getAddresses() entrypoint is used to fetch a list validated addresses associated with a given customer identity. This list can in turn be used to i.e. verify that an order delivery address matches the invoice address used by Svea for invoice and payment plan orders. Only applicable for SE, NO and DK customers. Note that in Norway, company customers only are supported.

Get an request class instance using the WebPay::getAddresses entrypoint, then provide more information about the transaction and send the request using the
request class methods:

Use setCountryCode() to supply the country code that corresponds to the account credentials used for the address lookup. Note that this means that you cannot look up a user in a foreign country, this is a consequence of the fact that the invoice and part payment methods don't support foreign orders.

Use setCustomerIdentifier() to provide the exact credentials needed to identify
the customer according to country:
    * SE: Personnummer (private individual) or Organisationsnummer (company/legal entity)
    * NO: Organisasjonsnummer (company or other legal entity)
    * DK: Cpr.nr (private individual) or CVR-nummer (company or other legal entity)

Then use either getIndividualAddresses() or getCompanyAddresses() depending on what kind of customer you want to look up.

The final doRequest() will send the getAddresses request to Svea and return the result.

The doRequest() method will then check if there exists credentials to use for the request in the given configurationProvider.

(Note that this behaviour may cause problems if your integration is set to use different (test/production) credentials for invoice and payment plan -- if you get an error and this is the case, you may use one of the deprecated methods setOrderTypeInvoice() or setOrderTypePaymentPlan() to explicitly state which method credentials to use.)

```php
<?php
...
$request = WebPay::getAddresses($config)
   ->setCountryCode()                  // required -- the country to perform the customer address lookup in
   ->setCustomerIdentifier()           // required -- social security number, company vat number etc. used to identify customer
   ->setOrderTypeInvoice()             // deprecated -- method that corresponds to the ConfigurationProvider account credentials used
   ->setOrderTypePaymentPlan()         // deprecated -- method that corresponds to the ConfigurationProvider account credentials used
;
// then select the corresponding request class and send request
$response = $request->getIndividualAddresses()->doRequest();    // returns GetAddressesResponse
$response = $request->getCompanyAddresses()->doRequest();       // returns GetAddressesResponse
```

#### 6.3.1 getAddresses response format
The Webpay::getAddresses request returns an instance of GetAddressesResponse, containing the actual customer addresses in an array of GetAddressIdentity:

```php
<?php
...
$myGetAddressResponse = WebPay::getAddresses($myConfig);

// GetAddressResponse attributes:
$myGetAddressResponse->accepted;            // Boolean  // true iff request was accepted
$myGetAddressResponse->resultcode;          // String   // set iff accepted false
$myGetAddressResponse->errormessage;        // String   // set iff accepted false
$myGetAddressResponse->customerIdentity;    // Array of GetAddressIdentity

$firstCustomerAddress = $myGetAddressesResponse->customerIdentity[0];

// GetAddressIdentity attributes:
$firstCustomerAddress->customerType;        // String   // "Person" or "Business" for individual and company customers, respectively
$firstCustomerAddress->nationalIdNumber;    // Numeric  // national id number of individual or company
$firstCustomerAddress->fullName;            // String   // amalgamated firstName and surname for individual, or company name for company customers
$firstCustomerAddress->coAddress;           // String   // optional
$firstCustomerAddress->street;              // String   // required, streetName including houseNumber
$firstCustomerAddress->zipCode;             // String   // required
$firstCustomerAddress->locality;            // String   // required, city name
$firstCustomerAddress->phoneNumber;         // String   // optional
$firstCustomerAddress->firstName;           // String   // optional, present in GetAddressResponse, not returned in CreateOrderResponse
$firstCustomerAddress->lastName;            // String   // optional, present in GetAddressResponse, not returned in CreateOrderResponse
$firstCustomerAddress->addressSelector;      // String   // optional, uniquely disambiguates company addresses
...
```

If defined, the customer `fullName` method will contain the amalgamated customer firstName and surname as returned by the various credit providers we use in the respective country. Unfortunately, there is no way of knowing the exact format of the amalgamated name; i.e. "Joan Doe", "Joan, Doe", "Doe, Joan".

#### 6.3.2 getAddresses request example (new style)
```php
<?php
$request = WebPay::getAddresses($config)
  ->setCountryCode()                  // required -- the country to perform the customer address lookup in
  ->setCustomerIdentifier()           // required -- social security number, company vat number etc. used to identify customer
  ->setOrderTypeInvoice()             // deprecated -- method that corresponds to the ConfigurationProvider account credentials used
  ->setOrderTypePaymentPlan()         // deprecated -- method that corresponds to the ConfigurationProvider account credentials used
;
// then select the corresponding request class and send request
$response = $request->getIndividualAddresses()->doRequest();    // returns GetAddressesResponse
$response = $request->getCompanyAddresses()->doRequest();       // returns GetAddressesResponse
```

#### 6.3.3 getAddresses request example
```php
<?php
$response = WebPay::getAddresses( $config )
   ->setCountryCode("SE")                  // Required -- supply the country code that corresponds to the account credentials used
   ->setOrderTypeInvoice()                 // Required -- use invoice account credentials for getAddresses lookup
   ->setCustomerIdentifier("194605092222") // Required -- lookup the address
   ->getIndividualAddresses()              // get private individual address
   ->doRequest();
   ;
```

An complete usage example can be found in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/config_getaddresses/" target="_blank">example/config_getaddresses</a> folder.

### 6.4 WebPay::getPaymentPlanParams() <a name="i6-4"></a>
Use getPaymentPlanParams() to fetch all campaigns associated with a given client number before creating the payment plan payment.

```php
<?php
...
$response =
WebPay::getPaymentPlanParams($config)
  ->setCountryCode("SE")                  // Required
  ->doRequest();
...
```

The response is an instance of WebService\PaymentPlanParamsResponse, with the available campaigns in the array campaignCodes:

```php
<?php
...
$response->accepted                 // true if request was accepted by the service
$response->errormessage             // may be set if accepted above is false
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
...
```

### 6.5 WebPay::paymentPlanPricePerMonth() <a name="i6-5"></a>
This is a helper function provided to calculate the monthly price for the different payment plan options for a given sum. This information may be used when displaying i.e. payment options to the customer by checkout, or to display the lowest amount due per month to display on a product level.

The returned instance of PaymentPlanPricePerMonth contains an array "values", where each element in turn contains an array of campaign code, description and price per month.

### 6.6 WebPay::listPaymentMethods() <a name="i6-6"></a>
The WebPay::listPaymentMethods method is used to fetch all available payment methods configured for a given country.

#### 6.6.1 Usage
Use the WebPay::listPaymentMethods() entrypoint to get an instance of ListPaymentMethods. Then provide more information about the transaction and
send the request using ListPaymentMethod methods.

```php
<?php
...
$methods = WebPay::listPaymentMethods( $config )
   ->setCountryCode("SE")      // required
   ->doRequest()
;
...
```
Following the ->doRequest call you receive an instance of ListPaymentMethodsResponse.

### 6.7 WebPay::getAccountCreditParams() <a name="i6-7"></a>
The WebPay::getAccountCreditParams method is used to fetch all available campaigns for configured country

#### 6.7.1 Usage

```php
<?php
...

$campaigns = WebPay::getAccountCreditParams( $config )
    ->setCountryCode('SE')  // required
    ->doRequest();
...
```
Following the ->doRequest call you receive an instance of AccountCreditParamsResponse.


### 6.8 WebPay::checkout() <a name="i6-7"></a>
The WebPay::checkout method is used to call all available Svea Checkout functionality.

#### 6.8.1 Creating orders
Use the WebPay::checkout()->createOrder() method to create a new Checkout order.

```php
<?php
...
$order = WebPay::checkout($config)
  ->addOrderRow($orderRow)                                            // required, see WebPayItem::orderRow
  ->addFee($shippingFee)                                              // optional, see WebPayItem for shipping fee
  ->addDiscount($discount)                                            // optional, see WebPayItem for fixed discount
  ->setCountryCode("SE")                                              // required for Svea Checkout
  ->setCurrency("SEK")                                                // required for Svea Checkout
  ->setCheckoutUri("http://localhost:51925/")                         // required Merchant settings (checkout uri)
  ->setConfirmationUri("http://localhost:51925/checkout/confirm")     // required Merchant settings (confirmation uri)
  ->setPushUri("https://svea.com/push.aspx?sid=123&svea_order=123")   // required Merchant settings (push uri)
  ->setTermsUri("http://localhost:51898/terms")                       // required Merchant settings (terms uri)
  ->setLocale('sv-SE')                                                // required for Svea Checkout
  ->createOrder()                                                     // Create new Checkout order
   ;
...
```

#### 6.7.2 Getting information from orders
Use the WebPay::checkout()->getOrder() method to get existing Checkout order information.

```php
<?php
...
$locale = 'sv-SE';

$order = WebPay::checkout($config)
  ->setId(50)             // required Checkout order ID
  ->getOrder()            // Get existing Checkout order
;
...
```

#### 6.7.3 Updating order
Use the WebPay::checkout()->updateOrder() method to update existing Checkout order.

```php
<?php
...
$locale = 'sv-SE';

$order = WebPay::checkout($config)
  ->setId(5)                           // required Checkout order ID
  ->addOrderRow($orderRow)             // see WebPayItem::orderRow
  ->updateOrder()                      // Update existing Checkout order
;
...
```

[Back to top](#index)

## 7. WebPayAdmin entrypoint method reference <a name="i7"></a>
The WebPayAdmin class methods are used to administrate orders after they have been accepted by Svea. It includes functions to update, deliver, cancel and credit orders et.al.

When administrating orders that are made through the checkout you set the CheckoutOrderId instead of using the sveaOrderId or TransactionID. Use [7.3 WebPayAdmin::queryOrder](#i7-3) in order to find out which actions that can be performed on a checkout order. The examples assumes that the action can be performed on that order.

All response objects from the checkout can be find in the [*connection library*](https://github.com/sveawebpay/php-checkout).

See all full examples in the example folder.

* [7.1 WebPayAdmin::queryTaskInfo](#i7-1)
* [7.2 WebPayAdmin::cancelOrder()](#i7-2)
* [7.3 WebPayAdmin::queryOrder()](#i7-3)
* [7.4 WebPayAdmin::cancelOrderRows()](#i7-4)
* [7.5 WebPayAdmin::creditOrderRows()](#i7-5)
* [7.6 WebPayAdmin::addOrderRows()](#i7-6)
* [7.7 WebPayAdmin::updateOrderRows()](#i7-7)
* [7.8 WebPayAdmin::deliverOrderRows()](#i7-8)
* [7.9 WebPayAdmin::updateOrder()](#i7-9)
* [7.10 WebPayAdmin::creditAmount()](#i7-10)
* [7.11 WebPayAdmin::creditOrderRows()](#i7-11)

### 7.1 WebPayAdmin::queryTaskInfo() <a name="i7-1"></a>
WebPayAdmin::queryTaskInfo() is used to explain the status of a previously performed operation. When finished it will point towards the new resource with the Location.

The response will contain information about the task and a object containing details regarding a queued task.

```php
<?php
...
$taskUrl = 'http://paymentadminapi.svea.com/api/v1/queue/1';

$response = WebPayAdmin::queryTaskInfo($testConfig)
        ->setTaskUrl($taskUrl)
        ->getTaskInfo()
        ->doRequest();
        
...
```

### 7.2 WebPayAdmin::cancelOrder() <a name="i7-2"></a>

The WebPayAdmin::cancelOrder() entrypoint method is used to cancel an order with Svea,
that has not yet been delivered (invoice, payment plan) or confirmed (card).

Supports Invoice, Payment Plan, Card orders and also orders made in the checkout using those payment methods. 

Direct Bank orders need to be credited instead, see [7.5 WebPayAdmin::creditOrderRows()](#i7-5).

Get an instance using the WebPayAdmin.cancelOrder entrypoint, then provide more information about the order and send the request using the CancelOrderBuilder methods:

```php
<?php
...
$request = WebPayAdmin->cancelOrder($config)
   ->setOrderId()		// required for invoice and payment plan orders, use SveaOrderId received with createOrder response
   ->setTransactionId()	// required for card orders only
   ->setCountryCode()	// required, use same country code as in createOrder request
   ->setCheckoutOrderId() // required for checkout orders
;
// then select the corresponding request class and send request
$response = $request->cancelInvoiceOrder()->doRequest();        // returns CloseOrderResponse
$response = $request->cancelPaymentPlanOrder()->doRequest();    // returns CloseOrderResponse
$response = $request->cancelCardOrder()->doRequest();           // returns AnnulTransactionResponse
$response = $request->cancelCheckoutOrder()->doRequest();       // returns empty content for valid response
$response = $request->cancelAccountCreditOrder()->doRequest();  // returns CloseOrderResponse
...
```

For Checkout, cancelOrder() supports and canceling amount of an order.

```php
...
    $request = WebPayAdmin::cancelOrder($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setAmountIncVat(5.00)
            ->cancelCheckoutOrderAmount();
    
    $response = $request->doRequest();
...
```

### 7.3 WebPayAdmin::queryOrder() <a name="i7-3"></a>
The WebPayAdmin::queryOrder entrypoint method is used to get information about an order.

Note that for invoice and payment plan orders, the order rows name and description is merged
into the description field in the query response.

Get an instance using the WebPayAdmin::queryOrder entrypoint, then provide more information
about the order and send the request using the QueryOrderBuilder methods:

```php
<?php
...
$request = WebPay::queryOrder($config)
   ->setOrderId()           // required for invoice and payment plan orders, use SveaOrderId received with createOrder response
   ->setTransactionId()	    // required for card orders only
   ->setCountryCode()       // required, use same country code as in createOrder request
   ->setCheckoutOrderId()   // required for checkoutOrder
;
// then select the corresponding request class and send request
$response = $request->queryInvoiceOrder()->doRequest();     // returns GetOrdersResponse
$response = $request->queryPaymentPlanOrder()->doRequest(); // returns GetOrdersResponse
$response = $request->queryCardOrder()->doRequest();        // returns QueryTransactionResponse
$response = $request->queryDirectBankOrder()->doRequest();  // returns QueryTransactionResponse
$response = $request->queryCheckoutOrder()->doRequest();    // returns formatted array response with order information
$response = $request->queryAccountCreditOrder()->doRequest(); // returns GetOrderResponse
...
```

#### 7.3.1 QueryOrder Example

Example of an order with an order row specified using setName() and setDescription():

```php
<?php
...
$orderRow = WebPayItem::orderRow()
    ...
    ->setName("orderrow 1")                 // optional
    ->setDescription("description 1")       // optional
;
$order->addOrderRow($orderRow)->useInvoicePayment()->doRequest();
...
```

Querying the above order will result in a response having order rows with the following name and description:

```php
<?php
...
$queryBuilder = WebPayAdmin::queryOrder($config)
    ->setOrderId($invoiceOrderIdToQuery)
    ->setCountryCode($country)
;
$queryInvoiceOrderResponse = $queryBuilder->queryInvoiceOrder()->doRequest();

$queryInvoiceOrderResponse->name;          // contains null
$queryInvoiceOrderResponse->description;   // contains "orderrow 1: description 1"
```

whereas a card, direct bank or checkout order specified in the same way will have

```php
<?php
...
$queryOrderResponse->name;             // contains "orderrow 1"
$queryOrderResponse->description;      // contains "description 1"
```

As you can see, the order row name field is present but always null in the GetOrdersResponse class. This is correct and due to reuse of the existing NumberOrderRow class for the response. The queryOrderResponse has the original name and description in the respective fields.

QueryCheckoutOrder() is only used for orders created over checkout method.

### 7.4 WebPayAdmin::cancelOrderRows() <a name="i7-4"></a>

The WebPayAdmin::cancelOrderRows entrypoint method is used to cancel rows in an order before it has been delivered. Supports Invoice, Payment Plan, Card orders and also orders made in the checkout using those payment methods. (Direct Bank orders are not supported, see CreditOrderRows instead.)

For Invoice and Payment Plan orders, the order row status is updated at Svea following each successful request.

For card orders, the request can only be sent once, and if all original order rows are cancelled, the order then receives status ANNULLED at Svea.

Get an order builder instance using the WebPayAdmin.cancelOrderRows entrypoint, then provide more information about the transaction and send the request using the CancelOrderRowsBuilder methods:

Use setRowToCancel() or setRowsToCancel() to specify the order row(s) to cancel. The order row indexes should correspond to those returned by i.e. WebPayAdmin::queryOrder();

For card orders, use addNumberedOrderRow() or addNumberedOrderRows() to pass in a copy of the original order rows. The original order rows can be retrieved using WebPayAdmin::queryOrder(); the numberedOrderRows attribute contains the server-side order rows w/indexes. Note that if a card order has been modified (i.e. rows cancelled or credited) after the initial order creation, the returned order rows will not be accurate.

```php
<?php
...
$request = WebPayAdmin::cancelOrderRows($config)
   ->setOrderId()               // required for invoice and payment plan orders, use SveaOrderId received with createOrder response
   ->setTransactionId()	        // required for card orders only
   ->setCountryCode()           // required, use same country code as in createOrder request
   ->setCheckoutOrderId()       // required for checkoutOrder
   ->setRowToCancel()	   		// required, index of original order rows you wish to cancel
   ->addNumberedOrderRow()		// required for card orders, should match original row indexes
;
// then select the corresponding request class and send request
$response = $request->cancelInvoiceOrderRows()->doRequest();       // returns CancelOrderRowsResponse
$response = $request->cancelPaymentPlanOrderRows()->doRequest();   // returns CancelOrderRowsResponse
$response = $request->cancelCardOrderRows()->doRequest();          // returns LowerTransactionResponse
$response = $request->cancelCheckoutOrderRows()->doRequest();      // returns no content if response is success
...
```

See example/checkout/admin/credit-order-rows.php for an example of cancelling checkout order rows.

### 7.5 WebPayAdmin::creditOrderRows() <a name="i7-5"></a>

 The WebPayAdmin::creditOrderRows entrypoint method is used to credit rows in an order after it has been delivered.
Supports all payment methods.
(To credit a payment plan order, please contact Svea customer service first.)

If you wish to credit an amount not present in the original order, use addCreditOrderRow() or addCreditOrderRows()
and supply a new order row for the amount to credit. This is the recommended way to credit a card or direct bank order.

If you wish to credit an invoice or Payment Plan order row in full, you can specify the index of the order row to credit using setRowToCredit().
The corresponding order row at Svea will then be credited. (For card or direct bank orders you need to first query and then
supply the corresponding numbered order rows using the addNumberedOrderRows() method.)

     * Following the request Svea will issue a credit invoice including the original order rows specified using setRowToCredit(),
     * as well as any new credit order rows specified using addCreditOrderRow(). For card or direct bank orders, the order row amount
     * will be credited to the customer.

Get an order builder instance using the WebPayAdmin::creditOrderRows entrypoint, then provide more information about the
transaction and send the request using the creditOrderRowsBuilder methods:

```php
<?php
...
$request = WebPayAdmin::creditOrderRows($config)
  ->setCheckoutOrderId()          // required for checkout orders
  ->setDeliveryId()               // required for checkout orders, found in the response from a delivery action    
  ->setInvoiceId()                // required for invoice
  ->setInvoiceDistributionType()  // required for invoice
  ->setContractNumber()           // required for payment plan
  ->setOrderId()                  // required for card, direct bank and accountCredit
  ->setCountryCode()              // required
  ->addCreditOrderRow()           // optional, use to specify a new credit row, i.e. for amounts not present in the original order
  ->addCreditOrderRows()          // optional
  ->setRowToCredit()              // optional, index of one of the original order row you wish to credit
  ->setRowsToCredit()             // optional, list of row indexes you wish to credit
  ->addNumberedOrderRow()         // card and direct bank only, required with setRowToCredit()
  ->addNumberedOrderRows()        // card and direct bank only, optional
;
// then select the corresponding request class and send request
$response = $request->creditInvoiceOrderRows()->doRequest();     // returns CreditInvoiceRowsResponse
$response = $request->creditPaymentplanOrderRows()->doRequest(); // returns CreditOrderRowsRequest
$response = $request->creditCardOrderRows()->doRequest();        // returns CreditTransactionResponse
$response = $request->creditDirectBankOrderRows()->doRequest();  // returns CreditTransactionResponse
$response = $request->creditCheckoutOrderRows()->doRequest();
$response = $request->creditAccountCreditOrderRows()->doRequest(); // returns CreditIOrderRowsResponse

...
```

#### 7.5.1 Usage
The WebPayAdmin::creditOrderRows entrypoint method is used to credit rows in an order after it has been delivered.
Supports all payment methods. (To credit a Payment Plan order, contact Svea customer service.)

To credit an order row in full, you specify the index of the order row to credit (and for card orders, supply the numbered order row data itself).

If you wish to credit an amount not present in the original order, you need to supply new order row(s) for the credited amount using addCreditOrderRow() or addCreditOrderRows().
These rows will then be credited in addition to any rows specified using setRow(s)ToCredit below.

Use setCheckoutOrderId() when crediting orders made in the checkout.

Use setInvoiceId() to specify the invoice (delivered order) to credit.

Use setContractNumber() to specify the Payment plan contract (delivered order) to credit. (In reality this really means to cancel an orderRow from the contract)

Use setOrderId() to specify the card or direct bank transaction (delivered order) to credit.

Use setCountryCode() to specify the country code matching the original create
order request.

Use setRowToCredit() or setRowsToCredit() to specify order rows to credit.
The given row numbers must correspond with the the server-side row number.

For card or direct bank orders, it is required to use addNumberedOrderRow()
or addNumberedOrderRows() to pass in a copy of the server-side order row data.

You can use the WebPayAdmin::queryOrder() entrypoint to get information about the order,
the queryOrder response numberedOrderRows attribute contains the order rows with numbers.
The orderRows will have the rowNumbers in the order they are set, so you can also save orderRowNumber
when doing the createOrder request, and use for this purpose.
For invoice orders, the server-side order rows is updated after a creditOrderRows request.
Note that for Card and Direct bank orders the server-side order rows will not be updated.

Then use either creditInvoiceOrderRows(),creditAccountCreditOrderRows(), creditPaymentPlanOrderRows(), creditCardOrderRows() or
creditDirectBankOrderRows() to get a request object, which ever matches the
payment method used in the original order.

Calling doRequest() on the request object will send the request to Svea and
return either a CreditOrderRowsResponse or a CreditTransactionResponse.


### 7.6 WebPayAdmin::addOrderRows() <a name="i7-6"></a>
The WebPayAdmin::addOrderRows method is used to add individual order rows to non-delivered invoice, payment plan orders and also checkout orders using those payment methods.

#### 7.6.1 Usage
Add order rows to an order. Supports Invoice, Payment Plan and also checkout orders using those payment methods. (Card and Direct Bank orders are not supported.)

Provide information about the new order rows and send the request using addOrderRowsBuilder methods:

```php
<?php
...
$request = WebPayAdmin::addOrderRows($testConfig)
   ->setCheckoutOrderId() // Required for checkout orders
   ->setOrderId()         // Required for invoice or payment plan
   ->setCountryCode()
   ->addOrderRow()        // One or more, for checkout is limit only one row
   ->addOrderRows()       // optional

//Finish by selecting the correct orderType and perform the request:
$response = $request->addInvoiceOrderRows()->doRequest();
$response = $request->addPaymentPlanOrderRows()->doRequest();
$response = $request->addCheckoutOrderRows()->doRequest();
...
```


### 7.7 WebPayAdmin::updateOrderRows() <a name="i7-7"></a>
The WebPayAdmin::updateOrderRows() method is used to update individual order rows in non-delivered invoice and
payment plan orders.

Supports Invoice, Payment Plan and also checkout orders using those payment methods.

The order row status of the order is updated at Svea to reflect the updated order rows. If the updated rows'
order total amount exceeds the original order total amount, an error is returned by the service.

Get an order builder instance using the WebPayAdmin::updateOrderRows() entrypoint, then provide more information
about the transaction and send the request using the UpdateOrderRowsBuilder methods:

Use setCountryCode() to specify the country code matching the original create order request.

Use updateOrderRow() with a new WebPayItem::numberedOrderRow() object to pass in the updated order row. Use the
NumberedOrderRowBuilder member functions to specify the updated order row contents. Notably, the setRowNumber()
method specifies which original order row contents is to be replaced, in full, by the NumberedOrderRow contents.

Then use either updateInvoiceOrderRows() or updatePaymentPlanOrderRows() to get a request object, which ever
matches the payment method used in the original order.

Calling doRequest() on the request object will send the request to Svea and return UpdateOrderRowsResponse.

```php
<?php
...
$request = WebPayAdmin::updateOrderRows($config)
    ->setCheckoutOrderId()  // required if it's a checkout order
    ->setOrderId()		    // required if it's not a checkout order
    ->setCountryCode()      // required
    ->updateOrderRow()      // required, NumberedOrderRow w/RowNumber attribute matching row index of original order row

// then select the corresponding request class and send request
$response = $request->updateInvoiceOrderRows()->doRequest();     // returns UpdateOrderRowsResponse
$response = $request->updatePaymentPlanOrderRows()->doRequest(); // returns UpdateOrderRowsResponse
$response = $request->updateCheckoutOrderRows()->doRequest();
...
```

See example/checkout/admin/update-order-rows.php for an example of updating order rows in a checkout order.

### 7.8 WebPayAdmin::deliverOrderRows() <a name="i7-8"></a>

The WebPayAdmin::deliverOrderRows entrypoint method is used to deliver individual order rows. Supports invoice and card orders. (To partially deliver PaymentPlan or Direct Bank orders, please contact Svea.)
The method also supports checkout orders(if made using invoice or card).

For Invoice orders, the order row status is updated at Svea following each successful request.

For card orders, an order can only be delivered once, and any non-delivered order rows will be cancelled (i.e. the order amount will be lowered by the sum of the non-delivered order rows). A delivered card order has status CONFIRMED at Svea.

Get an order builder instance using the WebPayAdmin::deliverOrderRows() entrypoint, then provide more information about the transaction and send the request using the DeliverOrderRowsBuilder methods:

Use setRowToDeliver() or setRowsToDeliver() to specify the order row(s) to deliver. The order row indexes should correspond to those returned by i.e. WebPayAdmin::queryOrder();

For card orders, use addNumberedOrderRow() or addNumberedOrderRows() to pass in a copy of the original order rows. The original order rows can be retrieved using WebPayAdmin::queryOrder(); the numberedOrderRows attribute contains the server-side order rows w/indexes. Note that if a card order has been modified (i.e. rows cancelled or credited) after the initial order creation, the returned order rows will not be accurate.

```php
<?php
...
$request = WebPayAdmin::deliverOrderRows($config)
   ->setCheckoutOrderId()           // required if order is made using the checkout
   ->setOrderId()		            // required if order is invoice and  isn't made in the checkout
   ->setTransactionId()	            // required if order is card and isn't made in the checkout
   ->setCountryCode()      		    // required
   ->setInvoiceDistributionType()   // required for invoice only
   ->setRowToDeliver()	   		    // required, index of original order rows you wish to deliver
   ->addNumberedOrderRow()		    // required for card orders, should match original row indexes
;
// then select the corresponding request class and send request
$response = $request->deliverInvoiceOrderRows()->doRequest();       // returns DeliverOrderRowsResponse
$response = $request->deliverPaymentPlanOrderRows()->doRequest();   // returns DeliverOrderRowsResponse
$response = $request->deliverCardOrderRows()->doRequest();          // returns ConfirmTransactionResponse
$response = $request->deliverCheckoutOrderRows()->doRequest();
...
```

[Back to top](#index)

### 7.9 WebPayAdmin::updateOrder() <a name="i7-9"></a>
The WebPayAdmin::updateOrder() method is used to add or change ClientOrderNumber and/or Notes.
Supports invoice and payment plan orders.

Get an order builder instance using the WebPayAdmin::updateOrder() entrypoint, then provide more information about the transaction and send the request using the UpdateOrderBuilder methods:

Use setCountryCode() to specify the country code matching the original create order request.
Use setOrderId() to specify which order
Use setClientOrderNumber() if you want to add or change the client order number.
Use setNotes() if you want to add or change the Notes on invoice from client to customer.
Then use either updateInvoiceOrder() or updatePaymentPlanOrder() to get a request object, which ever matches the payment method used in the original order.

Calling doRequest() on the request object will send the request to Svea and return UpdateOrderResponse.

```php
<?php
...
$request = WebPayAdmin::updateOrder($config)
    ->setOrderId()		     // required for invoice and payment plan orders made without the checkout
    ->setCountryCode()       // required
    ->setClientOrderNumber() // optional String(32)
    ->setNotes()             // optional String(200)
;
// then select the corresponding request class and send request
$response = $request->updateInvoiceOrder()->doRequest();     // returns UpdateOrderResponse
$response = $request->updatePaymentPlanOrder()->doRequest(); // returns UpdateOrderResponse
...
```

### 7.10 WebPayAdmin::creditAmount() <a name="i7-10"></a>
If you wish to credit an amount in a checkout order see [7.10.1 Credit amount in a checkout order](#i7-10-1).

The WebPayAdmin::creditAmount() entrypoint method is used to credit an amount in an order after it has been delivered.
Supports PaymentPlan

Get an order builder instance using the WebPayAdmin::creditAmount entrypoint, then provide more information about the
transaction and send the request using the CreditAmountBuilder methods:

```php
<?php
...
$request = WebPayAdmin::creditAmount($config)
    ->setContractNumber(12345)
    ->setCountryCode('SE')
    ->setDescription('credit desc')
    ->setAmountIncVat(1500.00);

$response = $request->creditPaymentPlanAmount()->doRequest();
...
```

#### 7.10.1 Credit amount in a checkout order <a name="i7-10-1"></a>
The WebPayAdmin::creditAmount() entrypoint method is used to credit an amount in an order after it has been delivered.

Get an order builder instance using the WebPayAdmin::creditAmount entrypoint, then provide more information about the
transaction and send the request using the CreditAmountBuilder methods:
```php
<?php
...
$request = WebPayAdmin::creditAmount($myConfig)
        ->setCheckoutOrderId($sveaCheckoutOrderId) // required
        ->setDeliveryId(1) // required for checkout orders, found in the response from a delivery action   
        ->setAmountIncVat(20.00)
        ->creditCheckoutAmount()
        ->doRequest();
...
```

### 7.11 WebPayAdmin::creditOrderRows() <a name="i7-11"></a>
WebPayAdmin::creditOrderRows is used to credit a single order row or several

```php
<?php
...
$creditOrderRowsBuilder = WebPayAdmin::creditOrderRows($testConfig)
    ->setCheckoutOrderId($sveaCheckoutOrderId) // Set the checkout order id
    ->setDeliveryId(1)      // required for checkout orders, found in the response from a delivery action   
    ->setRowsToCredit(2);   // This will credit the second order row, can also be an array of rows
            
$response = $creditOrderRowsBuilder->creditCheckoutOrderRows()->doRequest();
...
```

#### 7.11.1 Crediting with a new order row <a name="i7-12"></a>
If you want to create a new order row in which you wish to specify an amount which is going to be credited, you can do so by adding a WebPayItem::orderRow() to the WebPayAdmin::creditOrderRows() method like the following example:

```php
<?php
...
$creditOrderRowsBuilder = WebPayAdmin::creditOrderRows($testConfig)
        ->setCheckoutOrderId($sveaCheckoutOrderId)
        ->setDeliveryId(1) // required for checkout orders, found in the response from a delivery action   

    $myCreditRow = WebPayItem::orderRow()
        ->setAmountIncVat(300.00)
        ->setVatPercent(25)
        ->setQuantity(1)
        ->setDescription("Credited order with new Order row");

    $creditOrderRowsBuilder->addCreditOrderRow($myCreditRow);

    $response = $creditOrderRowsBuilder->creditCheckoutOrderWithNewOrderRow()->doRequest();
...
```
    
[Back to top](#index)

## 8. SveaResponse and response classes <a name="i8"></a>
All synchronous Payment responses returns a SveaResponse object.

All Asynchronous responses from the payment gateway returns xml, but can be parsed and MAC validated through the SveaResponse class.

All response objects from the checkout can be find in the [*connection library*](https://github.com/sveawebpay/php-checkout).

### 8.1. Parsing an asynchronous service response <a name="i8-1"></a>
All synchronous Payment service request responses are parsed by *SveaResponse* and structured into response objects by the request method itself. You do not need to invoke the SveaResponse object to for synchronous service requests.

Asynchronous payment request responses (i.e. card and direct bank payments) need to be processed from the page listening to posts on the specified request return url. The response contains the parameters: *response*, *merchantId*, and *mac*, where the response is a Base64 encoded message. Use an instance of the SveaResponse class to instead get a structured object similar to the synchronous service responses.

#### 8.1.1
First, create an instance of SveaResponse, pass it the resulting xml response as part of the $_REQUEST response along with the a ConfigurationProvider and countryCode, then receive a HostedResponse instance by calling the getResponse() method, passing in the message sent to the return url, the request countryCode and configuration. An example of how to process the response received in the $_REQUEST super global:

```php
<?php
...
  $response = (new SveaResponse($_REQUEST,$countryCode,$config))->getResponse();
...
```

#### 8.1.2
An example of an asynchronous (card) order can be found in the <a href="http://github.com/sveawebpay/php-integration/blob/master/example/cardorder/" target="_blank">example/cardorder</a> folder.

### 8.2 Response accepted and result code <a name="i8-2"></a>
In the integration package all service response objects implement the following attributes, that may be checked to determine the outcome of a request:

* `accepted`      -- if set to logical true if the request was accepted by Svea
* `resultcode`    -- if set to a value >0, indicates a problem with the service request
* `errormessage`  -- a human readable version of the result code, only set if resultCode > 0

As the request responses from the various Svea web services varies in implementation, we strongly suggest i.e. checking the "accepted" attribute for logical truth instead of checking if it holds a particular value or type. I.e. use: "if( $response->accepted == true )" instead of ~~"if( $response->accepted === 1)"~~ or ~~"if( $response->accepted > 0)"~~ etc.

See the respective response classes for further information on response attributes, possible result codes etc.

[Back to top](#index)

## 9. Helper Class and Additional Developer Resources and Notes <a name="i9"></a>
In the Helper class we make available helper functions for i.e. bankers rounding, getting the different tax rates present in an order object, dividing an order row with an arbitrary mean tax rate across one or two new order rows with given tax rates, as well as splitting street addresses into streetName and houseNumber. See the Helper class definition for further information.

### 9.1 Helper::paymentPlanPricePerMonth() <a name="i9-1"></a>
This is a helper function provided to calculate the monthly price for the different payment plan options for a given sum. This information may be used when displaying i.e. payment options to the customer by checkout, or to display the lowest amount due per month to display on a product level.

If the ignoreMaxAndMinFlag is set to true, the returned array also contains the theoretical monthly installments for a given amount, even if the campaign may not actually be available to use in a payment request, should the amount fall outside of the actual campaign min/max limit. If the flag is set to false or left out, the values array will not include such amounts, which may result in an empty values array in the result.

```php
<?php
...
$paymentPlanParams = WebPay::getPaymentPlanParams($config)->setCountryCode("SE")->doRequest();

$response = Helper::paymentPlanPricePerMonth($amount, $paymentPlanParams, $ignoreMaxAndMinFlag = false);    // returns PaymentPlanPricePerMonth

$firstCampaign = $response->values[0]['campaignCode'];                      // i.e. [campaignCode] => 310012
$firstCampaignDescription = $response->values[0]['description'];            // i.e. [description] => Dela upp betalningen på 12 månader (räntefritt)
$pricePerMonthForFirstCampaign = $response->values[0]['pricePerMonth'];     // i.e. [pricePerMonth] => 201.66666666667
...
```

### 9.2 Request validateOrder(), prepareRequest(), getRequestTotals() methods <a name="i9-2"></a>
During module development or debugging, various informational methods may be of use as an alternative to `doRequest()` as the final step in the createOrder process in order to get more information about the actual request data that will be sent to Svea.

#### 9.2.1 prepareRequest()
The `prepareRequest()` method will do everything `doRequest()` does, except send the SOAP request to Svea -- instead it returns an inspectable object containing the data that will be sent in the doRequest call. The output of prepareRequest is also used internally by the doRequest method. To use, simply substitute `prepareRequest()` for the final `doRequest()` and then inspect the contents of the returned object.


#### 9.2.2 validateOrder()
The `validateOrder()` method validates that all required attributes are present in an order object, give the specific combination of country and chosen payment method, it returns an array containing any discovered errors.

#### 9.2.3 getRequestTotals()
If you find yourself in need of knowing what the order total at Svea will amount to before sending the request, you can use the `getRequestTotals()` method to get the amount including vat, amount excluding vat and total vat amount.

For example, if your integration only handles integer order amounts, you may have to supply a compensation row with the order to ensure that the invoiced order total amount in Svea's system match your integration order totals:

Your integration has the following order totals (in integers only) for an order containing one item:
1 item at cost 1400 kr inclusive of 6% vat (i.e. 1321 kr excl vat, 79 kr vat in your system)

The order row is specified using the following code, and then sent to Svea

```php
<?php
$order = WebPay::createOrder($config)->
         ->addOrderRow(
         WebPayItem::orderRow()
            ->setAmountIncVat(1400.00)
            ->setVatPercent(6)
            ->setQuantity(1)
         );
...
$response = $order->useInvoicePayment->doRequest();
```

As Svea always re-calculate an order to a sum and a vat percentage, this order will be represented in Svea backoffice as:

```
Price (excl. VAT)   Price (incl. VAT)	Totalt netto	VAT%	Sum (incl. VAT)
1320,75             1400.00             1320,75          6.00	1400,00
                                        -------         -----   -------
                                        1320,75         79,25	1400,00
```

If we try to make up the difference by adding a compensation (i.e. discount row):

```php
<?php
$order->addDiscount( WebPayItem::fixedDiscount()
                       ->setAmountIncVat(-0.25)    // a negative discount shows up as a positive adjustment
                       ->setVatPercent(0)
                   )
                ...
```

it will show up as

```
Price (excl. VAT)   Price (incl. VAT)	Totalt netto	VAT%	Sum (incl. VAT)
1320,75             1400.00             1320,75          6.00	1400,00
0,25                0,25                0,25             0.00   0,25
                                        -------         -----   -------
                                        1321,00         79,25	1400,25
```
Which is not what we want.

The correct way to do this is to send the order using the total amount incl. vat calculated from the item price ex. vat, and then add a discount row, that way the item row amount ex. vat and vat amount is correct, as well as the total amount charged to the customer:

```php
<?php>
$order = WebPay::createOrder($config)
          ->addOrderRow(
              WebPayItem::orderRow()
                  ->setAmountIncVat(1400.26)
                  ->setVatPercent(6)
                  ->setQuantity(1)
          )
          ->addDiscount( WebPayItem::fixedDiscount()
                          ->setAmountIncVat(-0.26)
                          ->setVatPercent(0)
                      )
          ...
```

which will show up as

```
Price (excl. VAT)   Price (incl. VAT)	Totalt netto	VAT%	Sum (incl. VAT)
1321,00             1400,26             1321,00          6.00	1400,26
-0,26               -0,26               -0,26            0.00   -0,26
                                        -------         -----   -------
                                        1320,74         79,26	1400,00
```

Which is about as exact as we can get. (Unfortunately there is no way to introduce a discount of vat only, as you need to pay vat on the entire 1321 kr, regardless on the total amount actually charged to the customer.)

`getRequestTotal()` for webservice requests returns the sums calculated for the ordeRrows as it will be handled in our systems. Returns an array with total_exvat, total_incvat and total_vat.

[Back to top](#index)

## 10. Frequently Asked Questions <a name="i10"></a>

### 10.1 Supported currencies <a name="i10-1"></a>
**Q**: What currencies does each payment method support?

**A**:
*Invoice and part payment*
For invoice and direct bank payment methods, the assumed currency is tied to the merchant account (client id), where each account in turn is tied to a specific country. This is why you as the merchant need to specify a country code in the order, and must supply the amount in the corresponding currency (i.e. an invoice order with setCountryCode('SE') is always assumed to be made out in SEK).

*Credit card and direct bank transfer*
For credit card orders, Svea accepts any currency when specifying the order.

The acquirer in turn asks (via the credit card company) the end user's Issuing bank (i.e. the bank that provides the end user their card) if the transaction is accepted. If so this information is passed on to the merchant via Svea, and this is the end of the story as far as the end user is concerned.

The merchant, i.e. your web shop, then receives money from their Acquiring bank. This is usually done nightly, when the acquiring bank (via the credit card company) receives a list of confirmed card transactions for the merchant in question, and pays the merchant accordingly.

*Acquiring bank support*
The key point is that the merchant must have an agreement with their acquiring bank as to which currencies they accept. Svea has no way of knowing this, so it is up to the merchant to supply the correct currency in the original request.

*tl;dr*
For invoice and part payment, the order amount is assumed to be made out in the country currency. For credit card and direct bank transfer, we honour the specified currency and amount, but you should only specify currencies that you have agreed upon with your acquiring bank.

### 10.2 Other payment method credentials <a name="i10-2"></a>
**Q**: What credentials do I need to make use of i.e. PayPal as a payment method through the PayPage?

**A**: When you sign up with Svea you will be provided with a merchant id which is used for all hosted payment methods. For a merchant id, one or more payment methods may be enabled, such as credit cards, direct bank payments using various banks, PayPal etc.

To enable a new payment method, your merchant will need to be configured with various credentials as requested by Svea. Please ask your Svea integration manager for more information on what exact credentials are needed.

### 10.3 My store order totals does not match the order totals in Svea's systems <a name="i10-3"></a>
**Q**: The order row sums and totals in my e-commerce platform does not match the ones in Svea's backoffice and/or on the invoice?

**A**: Unfortunately this may happen, primarily when using the invoice payment method, and may be due to a couple of different reasons, the main culprit being how prices are represented internally by your integration (i.e. store/e-commerce platform). If the way that the integration i.e. sums up the order totals (rounding after each row or of the total amounts, quantity applied before/after tax et al) differs from the way Svea does the invoice calculation, the order totals may not match between the systems.

Should this be the case, and you are unable to tweak the platform settings to match, we recommend that you use the getOrderTotals function as described in section 9.2.3 to add a compensation row to the order.

[Back to top](#index)

## APPENDIX <a name="appendix"></a>

### PaymentMethods
Used in usePaymentMethod($paymentMethod) and in usePayPage()->includePaymentMethods(..., ..., ...) et al.
```
| Payment method                    | Description                                   |
|-----------------------------------|-----------------------------------------------|
| PaymentMethod::BANKAXESS          | Direct bank payments, Norway                  |
| PaymentMethod::NORDEA_SE          | Direct bank payment, Nordea, Sweden.          |
| PaymentMethod::SEB_SE             | Direct bank payment, private, SEB, Sweden.    |
| PaymentMethod::SEBFTG_SE          | Direct bank payment, company, SEB, Sweden.    |
| PaymentMethod::SHB_SE             | Direct bank payment, Handelsbanken, Sweden.   |
| PaymentMethod::SWEDBANK_SE        | Direct bank payment, Swedbank, Sweden.        |
| PaymentMethod::KORTCERT           | Card payments, Certitrade.                    |
| PaymentMethod::SVEACARDPAY        | Card payments, SveaCardPay.                   |
| PaymentMethod::PAYPAL             | Paypal                                        |
| PaymentMethod::SKRILL             | Card payment with Dankort, Skrill.            |
| PaymentMethod::INVOICE            | Invoice by PayPage.                           |
| PaymentMethod::PAYMENTPLAN        | PaymentPlan by PayPage.                       |
```
