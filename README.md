# PHP Integration Package API for Svea WebPay

### Current build status
| Branch                            | Build status                               |
|---------------------------------- |------------------------------------------- |
| master (latest release)           | [![Build Status](https://travis-ci.org/sveawebpay/php-integration.png?branch=master)](https://travis-ci.org/sveawebpay/php-integration) |
| develop                           | [![Build Status](https://travis-ci.org/sveawebpay/php-integration.png?branch=develop)](https://travis-ci.org/sveawebpay/php-integration) |

### Version 2.0.1
The Svea WebPay Integration package uses semantic versioning (http://semver.org). This means that you can expect your integrations to remain backwards compatible during a major version release cycle. 

Previous versions of the package can be accessed through <a href="https://github.com/sveawebpay/php-integration/releases" target="_blank">the github releases</a> view.

## Index
* [I. Introduction](https://github.com/sveawebpay/php-integration#i-introduction)
* [1. Installing](https://github.com/sveawebpay/php-integration#1-installing-and-configuration)
* [2. "Hello World"](https://github.com/sveawebpay/php-integration#2-hello-world)
* [3. Building an order](https://github.com/sveawebpay/php-integration#3-building-an-order)
    * [3.1 Order builder](https://github.com/sveawebpay/php-integration#31-order-builder)
    * [3.2 Order row items](https://github.com/sveawebpay/php-integration#32-order-row-items)
    * [3.3 Customer identity](https://github.com/sveawebpay/php-integration#33-customer-identity)
    * [3.4 Additional order attributes](https://github.com/sveawebpay/php-integration#34-additional-order-attributes)
    * [3.5 Payment method selection](https://github.com/sveawebpay/php-integration#35-payment-method-selection)
    * [3.6 Recommended payment method usage](https://github.com/sveawebpay/php-integration#36-recommended-payment-method-usage)
* [4. Payment method reference](https://github.com/sveawebpay/php-integration#4-payment-method-reference)
    * [4.1 Svea Invoice payment](https://github.com/sveawebpay/php-integration#41-svea-invoice-payment)
    * [4.2 Svea Payment plan payment](https://github.com/sveawebpay/php-integration#42-svea-payment-plan-payment)
    * [4.3 Card payment](https://github.com/sveawebpay/php-integration#43-card-payment)
    * [4.4 Direct bank payment](https://github.com/sveawebpay/php-integration#44-direct-bank-payment)
    * [4.5 Using the Svea PayPage](https://github.com/sveawebpay/php-integration#45-using-the-svea-paypage)
    * [4.6 Examples](https://github.com/sveawebpay/php-integration#46-examples)
* [5. WebPayItem reference](https://github.com/sveawebpay/php-integration#5-webpayitem-reference)
    * [5.1 Specifying item price](https://github.com/sveawebpay/php-integration#51-specifying-item-price)
    * [5.2 WebPayItem::orderRow()](https://github.com/sveawebpay/php-integration#52-webpayitemorderrow)
    * [5.3 WebPayItem::shippingFee()](https://github.com/sveawebpay/php-integration#53-webpayitemshippingfee)
    * [5.4 WebPayItem::invoiceFee()](https://github.com/sveawebpay/php-integration#54-webpayiteminvoicefee)
    * [5.5 WebPayItem::fixedDiscount()](https://github.com/sveawebpay/php-integration#55-webpayitemfixeddiscount)
    * [5.6 WebPayItem::relativeDiscount](https://github.com/sveawebpay/php-integration#56-webpayitemrelativediscount)
    * [5.7 WebPayItem::individualCustomer()](https://github.com/sveawebpay/php-integration#57-webpayitemindividualcustomer)
    * [5.8 WebPayItem::companyCustomer()](https://github.com/sveawebpay/php-integration#58-webpayitemcompanycustomer)
* [6. WebPay entrypoint method reference](https://github.com/sveawebpay/php-integration#6-webpay-entrypoint-method-reference)
    * [6.1 WebPay::createOrder()](https://github.com/sveawebpay/php-integration#61-webpaycreateorder)
    * [6.2 WebPay::deliverOrder()](https://github.com/sveawebpay/php-integration#62-webpaydeliverorder)
    * [6.3 WebPay::getAddresses()](https://github.com/sveawebpay/php-integration#63-webpaygetaddresses)
    * [6.4 WebPay::getPaymentPlanParams()](https://github.com/sveawebpay/php-integration#64-webpaygetpaymentplanparams)
    * [6.5 WebPay::paymentPlanPricePerMonth()](https://github.com/sveawebpay/php-integration#65-webpaypaymentplanpricepermonth)
    * [6.6 WebPay::listPaymentMethods](https://github.com/sveawebpay/php-integration#66-webpaylistpaymentmethods)
* [7. WebPayAdmin entrypoint method reference](https://github.com/sveawebpay/php-integration#7-webpayadmin-entrypoint-method-reference)
    * [7.1 WebPayAdmin::cancelOrder()](https://github.com/sveawebpay/php-integration#71-webpayadmincancelorder)
    * [7.2 WebPayAdmin::queryOrder()](https://github.com/sveawebpay/php-integration#72-webpayadminqueryorder)
    * [7.3 WebPayAdmin::cancelOrderRows()](https://github.com/sveawebpay/php-integration#73-webpayadmincancelorderrows)
    * [7.4 WebPayAdmin::creditOrderRows()](https://github.com/sveawebpay/php-integration#74-webpayadmincreditorderrows)
    * [7.5 WebPayAdmin::addOrderRows()](https://github.com/sveawebpay/php-integration#75-webpayadminaddorderrows)
    * [7.6 WebPayAdmin::updateOrderRows()](https://github.com/sveawebpay/php-integration#76-webpayadminupdateorderrows)
* [8. SveaResponse](https://github.com/sveawebpay/php-integration#8-svearesponse)
    * [8.1. Parsing an asynchronous service response](https://github.com/sveawebpay/php-integration#81-parsing-an-asynchronous-service-response)
* [9. Additional Developer Resources and notes](https://github.com/sveawebpay/php-integration#9-additional-developer-resources-and-notes)
    * [9.1 Helper class methods](https://github.com/sveawebpay/php-integration#91-helper-class-methods)
    * [9.2 Inspect prepareRequest(), validateOrder() methods](https://github.com/sveawebpay/php-integration#92-inspect-preparerequest-validateorder-methods)
* [APPENDIX](https://github.com/sveawebpay/php-integration#appendix)

## I. Introduction
The WebPay and WebPayAdmin classes make up the Svea WebPay API. Together they provide unified entrypoints to the various Svea web services. The API also encompass the support classes ConfigurationProvider, SveaResponse and WebPayItem, as well as various constant container classes.

The WebPay class methods contains the functions needed to create orders and perform payment requests using Svea payment methods. It contains methods to define order contents, send order requests, as well as support methods needed to do this.

The WebPayAdmin class methods are used to administrate orders after they have been accepted by Svea. It includes functions to update, deliver, cancel and credit orders et.al.

### Package design philosophy
In general, a request to Svea using the WebPay API starts out with you creating an instance of an order builder class, which is then built up with data using fluid method calls. At a certain point, a method is used to select which service the request will go against. This method then returns an service request instance of a different class, which handles the request to the service chosen. The service request will return a response object containing the various service responses and/or error codes.

The WebPay API consists of the entrypoint methods in the WebPay and WebPayAdmin classes. These instantiate order builder classes in the Svea namespace, or in some cases request builder classes in the WebService, HostedService and AdminService sub-namespaces.

Given an instance, you then use method calls in the respective classes to populate the order or request instance. For orders, you then choose the payment method and get a request class in return. Send the request and get a service response from Svea in return. In general, the request classes will validate that all required attributes are present, and if not throw an exception stating what is missing for the request in question. 

### Synchronous and asynchronous requests
Most service requests are synchronous and return a response immediately. For asynchronous hosted service payment requests, the customer will be redirected to i.e. the selected card payment provider or bank, and you will get a callback to a return url, where where you receive and parse the response.

### Namespaces
The package makes use of PHP namespaces, grouping most classes under the namespace Svea. The entrypoint classes WebPay, WebPayAdmin and associated support classes are excluded from the Svea namespace. See the generated documentation for available classes and methods.

The underlying services and methods are contained in the Svea sub-namespaces WebService, HostedService and AdminService, and may be accessed, though their api and interfaces are subject to change in the future.

See the PHP documentation for more information on [namespaces in PHP](http://php.net/manual/en/language.namespaces.php). 

### Documentation format
See the provided README.md file for an overview and examples how to utilise the WebPay and WebPayAdmin classes. The complete WebPay Integration package, including the underlying Svea service classes, methods and structures, is documented by generated documentation in the apidoc folder.   

### Fluid API
The WebPay and WebPayAdmin entrypoint methods are built as a fluent API so you can use method chaining when implementing it in your code. We recommend making sure that your IDE code completion is enabled to make full use of this feature.

### Development environment
The Svea WebPay PHP integration package is developed and tested using NetBeans IDE 7.3.1 with the phpunit 3.7.24 plugin.

<!--- 
the above section 1.x text is taken from the WebPay/WebPayAdmin class docblock
 -->

[<< To index](https://github.com/sveawebpay/php-integration#index)
## 1. Installing and configuration

### 1.1 Requirements
The integration package requires PHP 5.3 or higher to use. You also need to have soap support enabled.

To run the package test suite, phpunit 3.7 is needed. To regenerate the apidoc documentation, phpdocumentor 2.3 or higher is needed.

### 1.2 Package installation
The integration package files are located under the src/ folder. Copy the contents of the src/ folder to a folder in your project, we suggest the name "svea". Then include the package file *Includes.php* in your integration. You should now be able to access the package classes.

### 1.3 Configuration
In order to make use of the Svea services you need to supply your account credentials to authorize yourself against the Svea services. For the Invoice and Payment Plan payment methods, the credentials consist of a set of Username, Password and Client number (one set for each country and service type). For Card and Direct Bank payment methods, the credentials consist of a (single) set of Merchant id and Secret Word.

You should have received the above credentials from Svea when creating a service account. If not, please contact your Svea account manager.

### 1.4 Using your account credentials with the package
The WebPay and WebPayAdmin entrypoint methods all require a config object when called. The easiest way to get such an object is to use the SveaConfig::getDefaultConfig() method. Per default, it returns a config object with the Svea test account credentials as used by the integration package test suite. 

In order to use your own account credentials, either edit the SveaConfig.php file with your actual account credentials, or implement the ConfigurationProvider interface in a class of your own -- your implementation could for instance fetch the needed credentials from a database in place of the SveaConfig.php file.

See the provided example of how to customise the SveaConfig.php in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/config_getaddresses/" target="_blank">example/config_getaddresses/</a> folder.

See further the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/ConfigurationProvider.html" target="_blank">ConfigurationProvider</a> interface and the provided <a href="https://github.com/sveawebpay/php-integration/blob/master/src/Config/SveaConfig.php" target="_blank">SveaConfig.php/</a> file.

[<< To index](https://github.com/sveawebpay/php-integration#index)
## 2. "Hello World"
An example of the WebPay API workflow is the following invoice payment, where we wish to perform an invoice order. Assume that we have already collected all needed order data, and will now build an order containing the ordered items (with price, article number info, et al) and customer information (name, address, et al), select a payment method, and send the payment request to Svea.

### 2.1 A complete invoice order
The following is a complete example of how to place an order using the invoice payment method:

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

The above example can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/firstorder/" target="_blank">example/firstorder</a> folder.

### 2.2 What just happened?
Above, we start out by calling the API method WebPay::createOrder(), which returns an instance of the CreateOrderBuilder class. 

Then, the class methods addOrderRow(), addCustomerDetails(), setOrderDate(), setCountryCode(), setCustomerReference(), and setClientOrderNumber() are used to populate the orderbuilder object with all required order information needed for an invoice order.

Then, the useInvoicePayment() method is called, returning an instance of the WebService\InvoicePayment class. We then call the doRequest() method, which validates the provided order information, and makes the request to Svea, returning an instance of the WebService\CreateOrderResponse class.

To determine the outcome of the payment request, we can then inspect the response attributes, i.e. check if $response->accepted == true.

### 2.3 Oh, that's cool, but how to use the service api:s directly?
The above structure enables the WebPay and WebPayAdmin entrypoint methods to confine themselves to the order domain, and pushes the various service request details lower into the package stack, away from the immediate viewpoint of the integrator. Thus all payment methods and services are accessed in a uniform way, with the package doing the main work of massaging the order data to fit the selected payment method or service request. 

This also provides future compatibility, as the main WebPay and WebPayAdmin entrypoint methods stay stable whereas the details of how the services are bering called by the package may change in the future.

That being said, there are no additional prohibitions on using the various service call wrapper classes to access the Svea services directly, while still not having to worry about the details on how to i.e. build the various SOAP calls or format the XML data structures.

It is therefore possible to instantiate the service request classes directly, making sure to set all relevant methods before finishing with a method to perform the request to the service. In general, we validate that all required attributes are present, and if not, an exception will be thrown stating what attributes are missing for the service request in question. 

See further the package WebService, AdminService and HostedService namespaces for further information. All service classes are documented by generated documentation included in the package apidoc folder: <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/index.html" target="_blank">WebPay API documentation</a>.

Now continue reading, and we'll work through the WebPay order building procedure and the various WebPay and WebPayAdmin entrypoint methods.

[<< To index](https://github.com/sveawebpay/php-integration#index)
## 3. Building an order 
We show how to specify an order by showing how to specify a complete order, working through the various steps and options along the way.

### 3.1 Order builder
Start by creating an order using the WebPay::createOrder method. Pass in your configuration and get an instance of OrderBuilder in return.

```php
...
$myOrder = WebPay::createOrder($myConfig);
...
```

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.CreateOrderBuilder.html" target="_blank">CreateOrderBuilder</a> for methods used to add row items to an order.

### 3.2 Order row items
Order row, fee and discount items can be added to the order. Together the row items amount add up to the order total to pay.

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
See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/WebPayItem.html" target="_blank">WebPayItem</a> class for methods used to build order row item objects.

See [5.2](https://github.com/sveawebpay/php-integration#52-webpayitemorderrow) to 5.6 in the WebPayItem class documentation below for more
### 3.3 Customer identity
Create a customer identity object using the WebPayItem::individualCustomer() or WebPayItem::companyCustomer() methods. Use the addCustomerDetails() method to add the customer information to the order. 

Customer identity is required for Invoice and Payment plan orders. For Card and Direct bank orders it is optional but recommended.

####1.3.1 Options for individual customers
```php
...
$order->
    ...
    addCustomerDetails(
        WebPayItem::individualCustomer()
        ->setNationalIdNumber(194605092222)
        ->setName("Tess", "Testson")        
        ->setStreetAddress("Gatan", 23)     
        ->setZipCode(9999)                  
        ->setLocality("Stan")               
        ...
    )
;
...
```

See [5.7] (https://github.com/sveawebpay/php-integration#57-webpayitemindividualcustomer)and 5.8 in the WebPayItem class documentation below for more information on how to specify customer identity items.

[<< To index](https://github.com/sveawebpay/php-integration#index)
### 3.4 Additional order attributes
Set any additional attributes needed to complete the order using the OrderBuilder methods.

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

[<< To index](https://github.com/sveawebpay/php-integration#index)
### 3.5 Payment method selection
Finish the order specification process by choosing a payment method with the order builder useXX() methods.

#### 3.5.1 Synchronous payments
Invoice and Payment plan payment methods will perform a synchronous request to Svea and return a response object which you can then inspect.

#### 3.5.2 Asynchronous payments
Hosted payment methods, like Card, Direct Bank and any payment methods accessed via the PayPage, are asynchronous. 

After selecting an asynchronous payment method you generally use a request class method to get a payment form object in return. The form is then posted to Svea, where the customer is redirected to the card payment provider service or bank. After the customer completes the payment, a response is sent back to your provided return url, where it can be processed and inspected.

#### 3.5.3 Response URL:s
For asynchronous payment methods, you must specify where to receive the request response. Use the following methods:

`->setReturnUrl()` (required) When a hosted payment transaction completes the payment service will answer with a response xml message sent to the return url. This is also the return url used if the user cancels at i.e. the Certitrade card payment page.

`->setCallbackUrl()` (optional) In case the hosted payment transaction completes, but the service is unable to return a response to the return url, Svea will retry several times using the callback url as a fallback, if specified. This may happen if i.e. the user closes the browser before the payment service redirects back to the shop, or if the transaction times out in lieu of user input. In the latter case, Svea will fail the transaction after at most 30 minutes, and will try to redirect to the callback url.

`->setCancelUrl()` (optional, paypage only) Presents a cancel button on the PayPage. In case the payment method selection is cancelled by the user, Svea will redirect back to the cancel url. Unless a cancel url is specified, no cancel button will be presented at the PayPage.

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.HostedService.HostedPayment.html" target="_blank">HostedPayment</a> class for response url details.

The service response received is sent as an XML message, use the SveaResponse response handler to get a response object. For details, see [8. SveaResponse](https://github.com/sveawebpay/php-integration#8-svearesponse) below.

### 3.6 Recommended payment method usage
*I am using the invoice and/or payment plan payment methods in my integration.*

>The best way is to use `->useInvoicePayment()` and `->usePaymentPlanPayment()`. These payments are synchronous and will give you an instant response.

*I am using the card and/or direct bank payment methods in my integration.*

>The best way if you know what specific payment you want to use, is to go direct to that specific payment, bypassing the PayPage step, by using
`->usePaymentMethod`. You can check the optional payment methods configured on your account using the `WebPay::getPaymentMethods()` method.
>
>You can also use the PayPage with `->usePayPageCardOnly()` and `->usePayPageDirectBankOnly()`.

*I am using all payment methods in my integration, and wish to let the customer select which to use.*

>The most effective way is to use `->useInvoicePayment()` and `->usePaymentPlanPayment()` for the synchronous payments, and use the `->usePaymentMethod()` for the asynchronous requests. First use `WebPay::getPaymentMethods()` to fetch the different payment methods configured on you account.
>
>Alternatively you can go by *PayPage* for the asynchronous requests by using `->usePayPageCardOnly()` and `->usePayPageDirectBankOnly()`.

*I am using more than one payment and want them gathered on on place.*

>You can go by *PayPage* and choose to show all your payments here, or modify to exclude or include one or more payments. Use `->usePayPage()` where you can custom your own *PayPage*. This introduces an additional step in the customer checkout flow, though. Note also that Invoice and Payment plan payments will return an asynchronous when used from PayPage.

*I wish to prepare an order and receive a link that I can mail to a customer, who then will complete the order payment using their card.*

>Create and build the order, then select the card payment method with the `->usePaymentMethod()`, but instead of getting a form with `->getPaymentForm()`, use `->getPaymentUrl()` to get an url to present to the user.

*I wish to set up a subscription using recurring card payments, which will renew each month without further end user interaction.*

>For recurring payments, first create an order and select a card payment method with `->usePaymentMethod()`. Use the `setSubscriptionType()` method on the resulting payment request object. When the end user completes the transaction, you will receive a subscription id in the response.

For the monthly subscription payments, you build the order and again select the card payment method with `->usePaymentMethod()`. Then use `setSubscriptionId()` with the above subscription id and finally send the recur payment request using the `->doRecur()` method.

[<< To index](https://github.com/sveawebpay/php-integration#index)

## 4. Payment method reference
Select payment method to use with the CreateOrderBuilder class useXX() methods, which return an instance of the appropriate payment request class.

### 4.1 Svea Invoice payment
Select ->useInvoicePayment() to perform an invoice payment.

```php
...
$order = WebPay::createOrder($config);
$order                                  
    ->addOrderRow( ...                      // required, one or more
    ->addCustomerDetails( ...               // required, individualCustomer or companyCustomer
    ->setCountryCode("SE")                  // required
    ->setOrderDate("2012-12-12")            // required
;
$request = $order->useInvoicePayment();     // requires the above attributes in the order
$response = $request->doRequest();
...
```

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.CreateOrderBuilder.html" target="_blank">CreateOrderBuilder</a> class for methods used to build the order object and select the payment method type to use.

Another complete, runnable example of an invoice order can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/invoiceorder/" target="_blank">example/invoiceorder</a> folder.

### 4.2 Svea Payment plan payment
Select ->usePaymentPlanPayment() to perform an invoice payment.

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

### 4.3 Card payment 
Select i.e. ->usePaymentMethod(PaymentMethod::KORTCERT) to perform a card payment via the Certitrade card payment provider.

#### 4.3.1 ->getPaymentForm()
Get a html form containing the request XML data. The form is an instance of PaymentForm, and also contains the complete html form as a string along with the form elements in an array. 

```php
...
$form = $order
    ->usePaymentMethod(PaymentMethod::KORTCERT)             // Card payment, get available providers using WebPay::listPaymentMethods()
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("se")                         // Optional, languageCode As ISO639, eg. "en", default english
        ->getPaymentForm();
...
```

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.HostedService.PaymentForm.html" target="_blank">PaymentForm</a> class for for form methods and attributes.

#### 4.3.2 ->getPaymentUrl()
Get an url containing a link to the prepared payment. To get a payment url you need to supply the customer ip address and language in the order request.

```php
...
$form = $order

    ->addCustomerDetails( 
        ...
        ->setIpAddress()                                    // Required
        ...
    ->usePaymentMethod(PaymentMethod::KORTCERT)             // Card payment, get available providers using WebPay::listPaymentMethods()
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("se")                         // Required, languageCode As ISO639, eg. "en", default english
        ->getPaymentUrl();
...
```

#### 4.3.3 Recurring card payments
Recurring card payments are set up in two steps. First a card payment including the subscription request, where the customer enters their credentials, and then any subsequent recur payment requests, where the subscription id is used in lieu of customer interaction.

For recurring payments, first create an order and select a card payment method with `->usePaymentMethod()`. Use the `setSubscriptionType()` method on the resulting payment request object. When the end user completes the transaction, you will receive a subscription id in the response.

For the monthly subscription payments, you build the order and again select the card payment method with `->usePaymentMethod()`. Then use `setSubscriptionId()` with the above subscription id and finally send the recur payment request using the `->doRecur()` method.

An example of an recurring card order, both the setup transaction and a recurring payment, can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/cardorder_recur/" target="_blank">example/cardorder_recur</a> folder.

### 4.4 Direct bank payment
Select i.e. ->usePaymentMethod(PaymentMethod::NORDEA_SE) to perform a direct bank transfer payment using the Swedish bank Nordea.

```php
...
$form = $order
    ->usePaymentMethod(PaymentMethod::NORDEA_SE)            // Direct bank payment, get available banks using WebPay::listPaymentMethods()
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("se")                         // Optional, languageCode As ISO639, eg. "en", default english
        ->getPaymentForm();
...
```

### 4.5 Using the Svea PayPage 

#### 4.5.1 Bypassing payment method selection
Go direct to specified payment method, bypassing the *PayPage* completely. By specifying payment method you eliminate one step in the payment process.

You can use `WebPay::listPaymentMethods()` to get the various payment methods available. 

```php
...
$form = $order
    ->usePaymentMethod(PaymentMethod::KORTCERT)             // Use WebPay::listPaymentMethods() to get available payment methods
        ->setReturnUrl("http://myurl.se")                   // Required
        ->setCancelUrl("http://myurl.se")                   // Optional
        ->setCardPageLanguage("se")                         // Optional, languageCode As ISO639, eg. "en", default english
        ->getPaymentForm();
...
```
#### 4.5.2 Select a card payment method
Send user to *PayPage* to select from available cards (only), and then perform a card payment at the card payment page.

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

A complete, runnable example of a card order using PaymentMethodPayment can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/cardorder/" target="_blank">example/cardorder</a> folder.

#### 4.5.3 Select a direct bank payment method
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

#### 4.5.4 Specifying from available payment methods
Send user to *PayPage* to select from the available payment methods. 

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

You can customise which payment methods to display, using the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.HostedService.PayPagePayment.html" target="_blank">PayPagePayment</a> methods `includePaymentMethods()`, `excludePaymentMethods()`, `excludeCardPaymentMethods()` and `excludeDirectPaymentMethods()`.

Available payment methods are listed in the PaymentMethod class and the [Appendix](https://github.com/sveawebpay/php-integration#appendix).

### 4.6 Examples

#### 4.6.1 Svea invoice order
An example of a synchronous (invoice) order can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/invoiceorder/" target="_blank">example/invoiceorder</a> folder.

#### 4.6.2 Card order
An example of an asynchronous card order can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/cardorder/" target="_blank">example/cardorder</a> folder.

#### 4.6.3 Recurring card order
An example of an recurring card order, both the setup transaction and a recurring payment, can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/cardorder_recur/" target="_blank">example/cardorder_recur</a> folder.

[<< To index](https://github.com/sveawebpay/php-integration#index)

## 5. WebPayItem reference
The WebPayItem class provides entrypoint methods to the different row items that make up an order, as well as the customer identity information items.

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/WebPayItem.html" target="_blank">WebPayItem</a> class for available order row items.

### 5.1 Specifying item price
Specify item price using precisely two of these methods in order to specify the item price and tax rate: 
setAmountExVat(), setAmountIncVat() and setVatPercent().

We recommend specifying price using setAmountExVat() and setVatPercentage(). If not, make sure not retain as much precision as possible, i.e. use no premature rounding (87.4875 is a "better" PriceIncVat than 87.49).

If you use setAmountIncVat(), note that this may introduce a cumulative rounding error when ordering large quantities of an item, as the package bases the total order sum on a calculated price ex. vat.

### 5.2 WebPayItem::orderRow()
Use this to add all kinds of products and other items. An order is required to have at least one order row.

#### 5.2.1 Usage
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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.OrderRow.html" target="_blank">OrderRow</a> class methods for details on how to specify the item, including all *required* methods.

### 5.3 WebPayItem::shippingFee()
Use this class to add shipping fee to the order.

#### 5.3.1 Usage

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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.ShippingFee.html" target="_blank">ShippingFee</a> class methods for details on how to specify the item, including all *required* methods.

### 5.4 WebPayItem::invoiceFee()
Use this class to add fees associated with a payment method (i.e. invoice fee) to the order.

#### 5.4.1 Usage

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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.InvoiceFee.html" target="_blank">InvoiceFee</a> class methods for details on how to specify the item, including all *required* methods.

### 5.5 WebPayItem::fixedDiscount()
Use this method when the discount or coupon is expressed as a percentage of the total product amount.

#### 5.5.1 Usage
If only AmountIncVat is given, we calculate the discount split across the tax (vat) rates present in the order. This will ensure that the correct discount vat is applied to the order.

Otherwise, it is required to use at least two of the functions setAmountExVat(), setAmountIncVat() and setVatPercent(). If two of these three attributes are specified, we respect the amount indicated and include a discount with the appropriate tax rate.

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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.FixedDiscount.html" target="_blank">FixedDiscount</a> class methods for details on how to specify the item, including all *required* methods.

### 5.6 WebPayItem::relativeDiscount()
Use this method when the discount or coupon is expressed as a percentage of the total product amount.

#### 5.6.1 Usage
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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.RelativeDiscount.html" target="_blank">RelativeDiscount</a> class methods for details on how to specify the item, including all *required* methods.

### 5.7 WebPayItem::individualCustomer()
Read "required" below as a requirement only when the IndividualCustomer is used to identify the customer when using the invoice or payment plan payment methods. (For card and direct bank orders, adding customer information to the order is optional.)

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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.IndividualCustomer.html" target="_blank">IndividualCustomer</a> class methods for details on how to specify the item, including all *required* methods.

### 5.8 WebPayItem::companyCustomer()
Read "required" below as a requirement only when the CompanyCustomer is used to identify the customer when using the invoice or payment plan payment methods. (For card and direct bank orders, adding customer information to the order is optional.)

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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.CompanyCustomer.html" target="_blank">CompanyCustomer</a> class methods for details on how to specify the item, including all *required* methods.

[<< To index](https://github.com/sveawebpay/php-integration#index)

## 6. WebPay entrypoint method reference
The WebPay class methods contains the functions needed to create orders and perform payment requests using Svea payment methods. It contains entrypoint methods to define order contents, send order requests, as well as various support methods needed to do this.

* [6.1 WebPay::createOrder()](https://github.com/sveawebpay/php-integration#61-webpaycreateorder)
* [6.2 WebPay::deliverOrder()](https://github.com/sveawebpay/php-integration#62-webpaydeliverorder)
* [6.3 WebPay::getAddresses()](https://github.com/sveawebpay/php-integration#63-webpaygetaddresses)
* [6.4 WebPay::getPaymentPlanParams()](https://github.com/sveawebpay/php-integration#64-webpaygetpaymentplanparams)
* [6.5 WebPay::paymentPlanPricePerMonth()](https://github.com/sveawebpay/php-integration#65-webpaypaymentplanpricepermonth)
* [6.6 WebPay::listPaymentMethods](https://github.com/sveawebpay/php-integration#66-webpaylistpaymentmethods)

### 6.1 WebPay::createOrder()

Use createOrder() to create an order and pay via invoice, payment plan, card, or direct bank payment methods.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.CreateOrderBuilder.html" target="_blank">CreateOrderBuilder</a> class for methods used to build the order object and select the payment method type to use.

### 6.2 WebPay::deliverOrder()
The WebPay::deliverOrder request should generally be sent to Svea once the ordered items have been sent out, or otherwise delivered, to the customer.

For invoice and payment plan orders, the deliver order request triggers the customer invoice being sent out to the customer by Svea. 

For card orders, the deliver order request confirms the card transaction, which in turn causes the card transaction to be batch processed by Svea. An auto-confirm account setting is also available, ask your Svea integration manager about this.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.DeliverOrderBuilder.html" target="_blank">DeliverOrderBuilder</a> class for methods used to build the order object and select the order type to deliver.

#### 6.2.1 Order delivery -- deliver a sample invoice order
The following is a minimal example of how to deliver an invoice order:

For orders specifying orderrows, WebPay::deliverOrder is used in a similar way to WebPay::createOrder, and makes use of the same order item information. Add the order rows that you want delivered along with the Svea order id before sending the request. The specified rows will automatically be matched to the rows sent when creating the order.

We recommend storing the order row data to ensure that matching orderrows can be faithfully recreated in the deliverOrder request.

If an item that was present in the createOrder request is left out from the deliverOrder request, the order is assumed to be partially fulfilled. Any left out items will not be invoiced by Svea.

You cannot partially deliver payment plan orders. When using deliverOrder on a payment plan order, all orderrows that aren’t cancelled will be delivered.

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
The above example can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/firstdeliver/" target="_blank">example/firstdeliver</a> folder.

#### 6.2.2 Order delivery -- additional order attributes 
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

### 6.3 WebPay::getAddresses()
Use getAddresses() to fetch a list of validated addresses associated with a given customer identity. Used to i.e. present to the customer the invoice 
address used by Svea, which for invoice and payment plan orders also should match the order delivery address.

Returns an instance of WebService\getAddressesResponse containing a list of verified addresses and addressSelector strings for a given customer.

The GetAddresses service is only applicable for SE, NO and DK customers and accounts. In Norway, GetAddresses may only be performed on company customers.

#### 6.3.1 getAddresses response format

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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.WebService.GetAddresses.html" target="_blank">GetAddresses</a> and <a href=http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.WebService.GetAddressesResponse.html" target="_blank">GetAddressesResponse</a> classes.

#### 6.3.2 getAddresses request example
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

An complete usage example can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/config_getaddresses/" target="_blank">example/config_getaddresses</a> folder.

### 6.4 WebPay::getPaymentPlanParams()
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

See the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.WebService.GetPaymentPlanParams.html" target="_blank">GetPaymentPlanParams</a> and <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.WebService.PaymentPlanParamsResponse.html" target="_blank">PaymentPlanParamsResponse</a> classes.


### 6.5 WebPay::paymentPlanPricePerMonth()
This is a helper function provided to calculate the monthly price for the different payment plan options for a given sum. This information may be used when displaying i.e. payment options to the customer by checkout, or to display the lowest amount due per month to display on a product level.

The returned instance of PaymentPlanPricePerMonth contains an array "values", where each element in turn contains an array of campaign code, description and price per month.


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

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/WebPay.html#method_paymentPlanPricePerMonth" target="_blank">WebPay::paymentPlanPricePerMonth()</a> and the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.WebService.PaymentPlanPricePerMonth.html" target="_blank">PaymentPlanPricePerMonth</a> class.

### 6.6 WebPay::listPaymentMethods
The WebPayAdmin::listPaymentMethods method is used to fetch all available paymentmethods for a given ConfigurationProvider and country.

#### 6.6.1
Use the WebPay::listPaymentMethods() entrypoint to get an instance of ListPaymentMethods. Then provide more information about the transaction and
send the request using ListPaymentMethod methods. 

Following the ->doRequest call you receive an instance of ListPaymentMethodsResponse.

```php
  $fooArray = WebPay::listPaymentMethods( $config )     // optional, if no $config given, will use defaults from SveaConfig
                    ->setContryCode("SE")               // optional, if no country given, will use default country "SE"
                    ->doRequest();
```

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/WebPay.html#method_listPaymentMethods" target="_blank">WebPay::listPaymentMethods()</a> and the <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.HostedService.ListPaymentMethodsResponse.html" target="_blank">ListPaymentMethodsResponse</a> class.

#### 6.6.2 
*example to come later*

[<< To index](https://github.com/sveawebpay/php-integration#index)
## 7. WebPayAdmin entrypoint method reference
The WebPayAdmin class methods are used to administrate orders after they have been accepted by Svea. It includes functions to update, deliver, cancel and credit orders et.al.

* [7.1 WebPayAdmin::cancelOrder()](https://github.com/sveawebpay/php-integration#71-webpayadmincancelorder)
* [7.2 WebPayAdmin::queryOrder()](https://github.com/sveawebpay/php-integration#72-webpayadminqueryorder)
* [7.3 WebPayAdmin::cancelOrderRows()](https://github.com/sveawebpay/php-integration#73-webpayadmincancelorderrows)
* [7.4 WebPayAdmin::creditOrderRows()](https://github.com/sveawebpay/php-integration#74-webpayadmincreditorderrows)
* [7.5 WebPayAdmin::addOrderRows()](https://github.com/sveawebpay/php-integration#75-webpayadminaddorderrows)
* [7.6 WebPayAdmin::updateOrderRows()](https://github.com/sveawebpay/php-integration#76-webpayadminupdateorderrows)

### 7.1 WebPayAdmin::cancelOrder()
The WebPayAdmin::cancelOrder method is used to cancel an order with Svea, that has not yet been delivered (invoice, payment plan) or confirmed (card). 

Direct bank orders are not supported, see WebPayAdmin::creditOrder.

#### 7.1.1 Usage and return types
Cancel an undelivered/unconfirmed order. Supports Invoice, PaymentPlan and Card orders. (For Direct Bank orders, see CreditOrder instead.)

The final doRequest() returns either a CloseOrderResult or an AnnulTransactionResponse:

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.CancelOrderBuilder.html" target="_blank">CancelOrderBuilder</a> class for methods used to build the order object and select the order type to cancel.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.WebService.CloseOrderResult.html" target="_blank">CloseOrderResult</a> for invoice and payment plan orders response.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.HostedService.AnnulTransactionResponse.html" target="_blank">AnnulTransactionResponse</a> for card orders response.

#### 7.1.2 example
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

### 7.2 WebPayAdmin::queryOrder()
The WebPayAdmin::queryOrder method is used get information about an order, including Svea status et al.

#### 7.2.1 Usage and return types
Query information about an order. Supports all order payment methods.

Provide more information about the transaction and send the request using QueryOrderBuilder methods:

```
->setOrderId()
->setCountryCode()  

Then select the correct ordertype and perform the request:
->queryInvoiceOrder() | queryPaymentPlanOrder() | queryCardOrder() | queryDirectBankOrder()
  ->doRequest()
```
 
The final doRequest() returns either a GetOrdersResponse or an QueryTransactionResponse

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.QueryOrderBuilder.html" target="_blank">QueryOrderBuilder</a> method details.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.AdminService.GetOrdersResponse.html target="_blank">GetOrdersResponse</a> for invoice and payment plan orders response.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.HostedService.QueryTransactionResponse.html" target="_blank">QueryTransactionResponse</a> for card and direct bank orders response.

#### 7.2.2 example
*example to come later*

### 7.3 WebPayAdmin::cancelOrderRows()
The WebPayAdmin::cancelOrder method is used to cancel individual order rows in orders, that has not yet been delivered (invoice, payment plan) or confirmed (card).

This method is mainly intended to cancel order rows in invoice and payment plan orders. When used with card orders the following limitations apply: You need to supply the NumberedOrderRows on which to operate. These may be fetched using the queryOrder method, but if the order has been edited after creation they may not be accurate. Cancelled card order rows are handled by lowering the amount to charge on the card order.

#### 7.3.1 Usage and return types
Cancel order rows in an order. Supports Invoice, Payment Plan and Card orders. (Direct Bank orders are not supported, see CreditOrderRows instead.)

Use the WebPayAdmin::queryOrder() entrypoint to get information about the order, the queryOrder response numberedOrderRows attribute contains the order rows and their numbers.

Provide more information about the transaction and send the request using cancelOrderRowsBuilder methods:

```
->setOrderId()
->setCountryCode()
->setRowToCancel() (one or more)
->setRowsToCancel() (optional)
->addNumberedOrderRow() (card only, one or more)
->addNumberedOrderRows() (card only, optional)
 
Then select the correct ordertype and perform the request:
->cancelInvoiceOrderRows() | cancelPaymentPlanOrderRows() | cancelCardOrderRows()
  ->doRequest()
```
 
The final doRequest() returns either a CancelOrderRowsResponse or a LowerTransactionResponse

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.CancelOrderRowsBuilder.html" target="_blank">CancelOrderRowsBuilder</a> method details.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.AdminService.CancelOrderRowsResponse.html" target="_blank">CancelOrderRowsResponse</a> for invoice and payment plan orders response.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.HostedService.LowerTransactionResponse.html" target="_blank">LowerTransactionResponse</a> for card and direct bank orders response.

#### 7.3.2 example
*example to come later*

### 7.4 WebPayAdmin::creditOrderRows()
The WebPayAdmin::creditOrder method is used to credit individual order rows in delivered orders.

When used with card or direct bank orders the following limitations apply: You need to supply the NumberedOrderRows on which to operate. These may be fetched using the queryOrder method, but if the order has been edited after creation they may not be accurate.

#### 7.4.1 Usage and return types
Credit order rows in a delivered invoice order, a charged card order or a direct bank order. Supports Invoice, Card and Direct Bank orders. (Payment Plan orders are not supported, please contact the Svea customer service to credit a Payment Plan order.)

Use the WebPayAdmin::queryOrder() entrypoint to get information about the order, the queryOrder response numberedOrderRows attribute contains the order rows and their numbers.

Provide more information about the transaction and send the request using creditOrderRowsBuilder methods:

```
->setInvoiceId() 
->setCountryCode()
->setRowToCredit() (one or more)
->setRowsToCredit() (optional)
->addNumberedOrderRow() (card or direct bank only, one or more)
->addNumberedOrderRows() (card or direct bank only, optional)
->addCreditOrderRow() (optional, use if you want to specify new credit rows)
->addCreditOrderRows() (optional, use if you want to specify new credit rows)

Finish by selecting the correct ordertype and perform the request:
->creditInvoiceOrderRows() | creditCardOrderRows()| creditDirectBankOrderRows()
  ->doRequest()
```

The final doRequest() returns either a CreditOrderRowsResponse or a CreditTransactionResponse

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.CreditOrderRowsBuilder.html" target="_blank">CreditOrderRowsBuilder</a> method details.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.AdminService.CreditOrderRowsResponse.html" target="_blank">CreditOrderRowsResponse</a> for invoice and payment plan orders response.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.HostedService.CreditTransactionResponse.html" target="_blank">CreditTransactionResponse</a> for card and direct bank orders response.

#### 7.4.2 example
*example to come later*

### 7.5 WebPayAdmin::addOrderRows()
The WebPayAdmin::addOrderRows method is used to add individual order rows to non-delivered invoice and payment plan orders.

#### 7.5.1 Usage and return types
Add order rows to an order. Supports Invoice and Payment Plan orders. (Card and Direct Bank orders are not supported.)

Provide information about the new order rows and send the request using addOrderRowsBuilder methods:

```
->setOrderId()
->setCountryCode()
->addOrderRow() (one or more)
->addOrderRows() (optional)
 
Finish by selecting the correct ordertype and perform the request:
->addInvoiceOrderRows() | addPaymentPlanOrderRows()
  ->doRequest()
``` 

The final doRequest() returns an AddOrderRowsResponse

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.AddOrderRowsBuilder.html" target="_blank">AddOrderRowsBuilder</a> method details.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.AdminService.AddOrderRowsResponse.html" target="_blank">AddOrderRowsResponse</a> for invoice and payment plan orders response.

#### 7.5.2 example
*example to come later*

### 7.6 WebPayAdmin::updateOrderRows()
The `WebPayAdmin::updateOrderRows()` method is used to update individual order rows in non-delivered invoice and payment plan orders.

#### 7.6.1
Update order rows in a non-delivered invoice or payment plan order. (Card and Direct Bank orders are not supported.)

Provide information about the updated order rows and send the request using updateOrderRowsBuilder methods:

```
->setOrderId()
->setCountryCode()
->updateOrderRow() (one or more)
->updateOrderRows() (optional)
 
Finish by selecting the correct ordertype and perform the request:
->updateInvoiceOrderRows() | updatePaymentPlanOrderRows()
  ->doRequest()
```
 
The final doRequest() returns an UpdateOrderRowsResponse

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.UpdateOrderRowsBuilder.html" target="_blank">UpdateOrderRowsBuilder</a> method details.

See <a href="http://htmlpreview.github.io/?https://raw.github.com/sveawebpay/php-integration/master/apidoc/classes/Svea.AdminService.UpdateOrderRowsResponse.html" target="_blank">UpdateOrderRowsResponse</a> for invoice and payment plan orders response.

#### 7.6.2 example
*example to come later*

[<< To index](https://github.com/sveawebpay/php-integration#index)
## 8. SveaResponse 

### 8.1. Parsing an asynchronous service response
All synchronous service request responses are parsed by *SveaResponse* and structured into response objects by the request method itself. You do not need to invoke the SveaResponse object to for synchronous service requests.

Asynchronous payment request responses (i.e. card and direct bank payments) need to be processed from the page listening to posts on the specified request return url. The response contains the parameters: *response*, *merchantid*, and *mac*, where the response is a Base64 encoded message. Use an instance of the SveaResponse class to instead get a structured object similar to the synchronous service responses.

#### 8.1.1
First, create an instance of SveaResponse, pass it the resulting xml response as part of the $_REQUEST response along with the a ConfigurationProvider and countryCode, then receive your HostedResponse instance by calling the getResponse() method.

Params:
* The POST or GET message sent to the return url is an associative array with the keys "response", "merchantid" and "mac".
* CountryCode, i.e. "SE"
* Config(https://github.com/sveawebpay/php-integration#configuration), an object implementing the ConfigurationProvider interface.

```php
  $response = (new SveaResponse($_REQUEST,$countryCode,$config))->getResponse();
```

#### 8.1.2
An example of an asynchronous (card) order can be found in the <a href="https://github.com/sveawebpay/php-integration/blob/master/example/cardorder/" target="_blank">example/cardorder</a> folder.

[<< To index](https://github.com/sveawebpay/php-integration#index)
## 9. Additional Developer Resources and notes

### 9.1 Helper class methods
In the Helper class we make available helper functions for i.e. bankers rounding, splitting a sum with an arbitrary tax rate over two fixed tax rates, as well as splitting street addresses into streetname and housenumber. See the Helper class definition for further information.

### 9.2 Inspect prepareRequest(), validateOrder() methods
During module development or debugging, the `prepareRequest()` method may be of use as an alternative to `doRequest()` as the final step in the createOrder process.

`prepareRequest()` will do everything `doRequest()` does, but does not send the SOAP request to Svea. Call `prepareRequest()` and then inspect the contents of the request to be sent to Svea. The 

`validateOrder()` validates that all required attributes are present in an order object, give the specific combination of country and payment method. It returns an array containing any discovered errors.

[<< To index](https://github.com/sveawebpay/php-integration#index)
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
