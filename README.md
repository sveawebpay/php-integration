# PHP Integration Package API for SveaWebPay
## Version 1.3.0

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
* [8. GetPaymentMethods](https://github.com/sveawebpay/php-integration#8-GetPaymentMethods)
* [APPENDIX](https://github.com/sveawebpay/php-integration#appendix)


## Introduction
This integration package is built for developers to simplify the integration of Svea WebPay services.
Using this package will make your implementation sustainable and unaffected for changes
in our payment system. Just make sure to update the package regularly.

The API is built as a *Fluent API* so you can use *method chaining* when implementing it in your code.
Make sure to open your implementation as a project in your IDE to make the code completion work properly.
Package is developed and tested in NetBeans IDE 7.2.

Include the file *Includes.php* from the php integration package in your file.
Call class *WebPay* and the suitable static function for your action.

```php
require_once 'Includes.php';

$foo = WebPay::createOrder();
$foo = $foo->...
        ->..;
```

## Changes in release 1.2.1

### Namespace
From release 1.2.0 on the package makes use of a namespace, Svea. We have made
efforts to avoid impacting existing integrations, so the classes WebPay and Item
are excluded from the namespace along with the new class WebPayItem. Also, the
interface ConfigurationProvider and the two constant container classes
DistributionType and PaymentMethod lie outside the Svea namespace.

For compatibility with existing integrations, the provided Item class is a now
a wrapper for the WebPayItem class. Item is marked as deprecated with release
1.2.0, please use WebPayItem instead.

In the event that you make use of other classes than WebPay or WebPayItem,
you'll need to prefix the Svea namespace to package classnames.

See the PHP documentation for more information on [namespaces in PHP](http://php.net/manual/en/language.namespaces.php).

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## Configuration
The Configuration needed to be set differs of how many different paymentmethods and Countries you have i the same installation.
The SveaConfig file can handle multiple paymentmethods and countries. The authorization values are recieved from Svea Ekonomi when creating an account.
If no configuration is done, default settings and testdata found in SveaConfig::getDefaultConfig() will be used.

There are two ways to configure Svea authorization. Choose one of the following:

1 - **Replace the file named SveaConfig.php** in folder Config looking the same as existing file
   but with your own authorization values. Determining to use the test or the prod values there are two ways:

```php
    /**
    * 1.
    * Manually changing the getDefaultConfig() to return the getProdConfig() or the getTestConfig().
    * In this case no parameter is neccesary when calling a function in WebPay.
    */

   $foo = WebPay::createOrder();
```

```php
    /**
    * 2.
    * Giving either the getProdConfig() or the getTestConfig() as parameter when calling a function in WebPay.
    * This way makes it possible to put a condition in implemantation code to check testmode.
    */

    if ($testmode == TRUE) {
        $config = SveaConfig::getTestConfig();
    } else {
        $config = SveaConfig::getProdConfig();
    }
   $foo = WebPay::createOrder($config);
```

2 - If your have the authorization values saved in a database, probably using an administration interface in your shop.
    **Create a class** (eg. one for testing values, one for production) that implements the ConfigurationProvider Interface. Let the implemented functions return the authorization values asked for.
    The integration package will then call these functions to get the value from your database.

```php
    require_once Includes.php

   class MyConfigTest implements ConfigurationProvider{

        /**
        * Constants for the endpoint url found in the class SveaConfig.php
        * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
        */
        public function getEndPoint($type) {
            if ($type == ConfigurationProvider::HOSTED_TYPE) {
                return   SveaConfig::SWP_TEST_URL;;
            } elseif ($type == ConfigurationProvider::INVOICE_TYPE || $type == ConfigurationProvider::PAYMENTPLAN_TYPE) {
                return SveaConfig::SWP_TEST_WS_URL;
            } else {
               throw new Exception('Invalid type. Accepted values: INVOICE, PAYMENTPLAN or HOSTED');
            }
        }
        /**
        * get the return value from your database or likewise
        * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
        * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
        */
        public function getMerchantId($type, $country) {
            //if you have different countries or types the parameters are a help to put up conditions
            return $myMerchantId;
        }
         /**
        * get the return value from your database or likewise
        * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
        * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
        */
        public function getPassword($type, $country) {
            return $myPassword;
        }
        /**
        * get the return value from your database or likewise
        * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
        * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
        */
        public function getSecret($type, $country) {
            return $mySecret;
        }
        /**
        * get the return value from your database or likewise
        * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
        * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
        */
        public function getUsername($type, $country) {
            return $myUsername;
        }
        /**
        * get the return value from your database or likewise
        * @param $type eg. HOSTED, INVOICE or PAYMENTPLAN
        * $param $country CountryCode eg. SE, NO, DK, FI, NL, DE
        */
        public function getClientNumber($type, $country) {
            return $myClientNumber;
        }
    }
```

  Later when starting an WebPay action in your integration file, put an instance of your class as parameter to the constructor.
  If left blank, the default settings in the class SveaConfig will be used.

```php
    //Find your testmode settings
    if ($this->testmode == 1) {
        //if test, use your class that returns test authorization
        $conf = new MyConfigTest();
    } else {
        //if production mode, use your class that returns production authorization
        $conf = new MyConfigProd();
    }
        //Create your CreateOrder object and continue building your order. Se next steps.
        $foo = WebPay::CreateOrder($conf);
```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 1. createOrder
Creates an order and performs payment for all payment forms. Invoice and payment plan will perform
a synchronous payment and return a response.
Other hosted payments, like card, direct bank and other payments from the *PayPage*,
on the other hand are asynchronous. They will return an html form with formatted message to send from your store.
For every new payment type implementation, you follow the steps from the beginning and chose your payment type preffered in the end:
Build order -> choose payment type -> doRequest/getPaymentForm

```php
$response = WebPay::createOrder()
//For all products and other items
   ->addOrderRow(WebPayItem::orderRow()...)
//If shipping fee
   ->addFee(WebPayItem::shippingFee()...)
//If invoice with invoice fee
    ->addFee(WebPayItem::invoiceFee()
//If discount or coupon with fixed amount
    ->addDiscount(WebPayItem::fixedDiscount()
//If discount or coupon with percent discount
   ->addDiscount(WebPayItem::relativeDiscount()
//Individual customer values.
    ->addCustomerDetails(WebPayItem::individualCustomer()...)
//Company customer values
    ->addCustomerDetails(WebPayItem::companyCustomer()...)
//Other values
    ->setCountryCode("SE")
    ->setOrderDate("2012-12-12")    // or ISO801 date produced by i.e. date('c')
    ->setCustomerReference("33")
    ->setClientOrderNumber("nr26")
    ->setCurrency("SEK")


//Continue by choosing one of the following paths
    //Continue as an invoice payment with instant response
    ->useInvoicePayment()
    ...
        ->doRequest();
    //Continue as a payment plan payment with instant response
    ->usePaymentPlanPayment("campaigncode", 0)
    ...
        ->doRequest();
    //Continue as a card payment with asynchronous response
    ->usePayPageCardOnly()
        ...
        ->getPaymentForm();
    //Continue as a direct bank payment	with asynchronous response
    ->usePayPageDirectBankOnly()
        ...
        ->getPaymentForm();
    //Continue as a PayPage payment, where you can custom the alternatives on your paypage, with asynchronous response
    ->usePayPage()
        ...
        ->getPaymentForm();
    //Go direct to specified paymentmethod, whithout stopping on the PayPage, with asynchronous response
    ->usePaymentMethod (PaymentMethod::SEB_SE) //see APPENDIX for Constants
        ...
        ->getPaymentForm();

```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 1.2 Specify order
Continue by adding values for products and other. You can add OrderRow, Fee and Discount. Chose the right WebPayItem object as parameter.
You can use the *add-* functions with an WebPayItem object or an array of WebPayItem objects as parameters.

```php
->addOrderRow(WebPayItem::orderRow()->...)

//or
$orderRows[] = WebPayItem::orderRow()->...;
->addOrderRow($orderRows);

```

#### 1.2.1 OrderRow
All products and other items. It´s required to have a minimum of one orderrow.
**The price can be set in a combination by using a minimum of two out of three functions: setAmountExVat(), setAmountIncVat() and setVatPercent().**
```php
->addOrderRow(
      WebPayItem::orderRow()
        ->setQuantity(2)                        //Required
        ->setAmountExVat(100.00)                //Optional, see info above
        ->setAmountIncVat(125.00)               //Optional, see info above
        ->setVatPercent(25)                     //Optional, see info above
        ->setArticleNumber(1)                   //Optional
        ->setDescription("Specification")       //Optional
        ->setName('Prod')                       //Optional
        ->setUnit("st")                         //Optional
        ->setDiscountPercent(0)                 //Optional
    )
```

#### 1.2.2 ShippingFee
**The price can be set in a combination by using a minimum of two out of three functions: setAmountExVat(), setAmountIncVat()and setVatPercent().**
```php
->addFee(
    WebPayItem::shippingFee()
        ->setShippingId('33')                   //Optional
        ->setName('shipping')                   //Optional
        ->setDescription("Specification")       //Optional
        ->setAmountExVat(50)                    //Optional, see info above
        ->setAmountIncVat(62.50)                //Optional, see info above
        ->setVatPercent(25)                     //Optional, see info above
        ->setUnit("st")                         //Optional
        ->setDiscountPercent(0)                 //Optional
   )
```
#### 1.2.3 InvoiceFee
**The price can be set in a combination by using a minimum of two out of three functions: setAmountExVat(), setAmountIncVat() and setVatPercent().**
```php
->addFee(
    WebPayItem::invoiceFee()
        ->setName('Svea fee')                   //Optional
        ->setDescription("Fee for invoice")     //Optional
        ->setAmountExVat(50)                    //Optional, see info above
        ->setAmountIncVat(62.50)                //Optional, see info above
        ->setVatPercent(25)                     //Optional, see info above
        ->setUnit("st")                         //Optional
        ->setDiscountPercent(0)                 //Optional
    )
```
#### 1.2.4 Fixed Discount
When discount or coupon is a fixed amount on total product amount.
```php
->addDiscount(
    WebPayItem::fixedDiscount()
        ->setAmountIncVat(100.00)               //Required
        ->setDiscountId("1")                    //Optional
        ->setUnit("st")                         //Optional
        ->setDescription("FixedDiscount")       //Optional
        ->setName("Fixed")                      //Optional
    )
```
#### 1.2.5 Relative Discount
When discount or coupon is a percentage on total product amount.
```php
->addDiscount(
    WebPayItem::relativeDiscount()
        ->setDiscountPercent(50)                //Required
        ->setDiscountId("1")                    //Optional
        ->setUnit("st")                         //Optional
        ->setName('Relative')                   //Optional
        ->setDescription("RelativeDiscount")    //Optional
    )
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 1.3 Customer Identity
Customer identity is required for invoice and payment plan orders. Required values varies
depending on country and customer type. For SE, NO, DK and FI NationalIdNumber (Social Security Number)
or company id number is required. Email and ip address are desirable.

To make it easy to set the right data depending on the customer type this example
```php

//create company or individual object
$foo = WebPayItem::individualCustomer();
if (*condition*) {
 $foo = $foo ->setEmail("test@svea.com") ;
}
->addOrderRow($foo);

```

####1.3.1 Options for individual customers
```php
->addCustomerDetails(
    WebPayItem::individualCustomer()
    ->setNationalIdNumber(194605092222) //Required for individual customers in SE, NO, DK, FI
    ->setInitials("SB")                 //Required for individual customers in NL
    ->setBirthDate(1923, 12, 20)        //Required for individual customers in NL and DE
    ->setName("Tess", "Testson")        //Required for individual customers in NL and DE
    ->setStreetAddress("Gatan", 23)     //Required in NL and DE
    ->setZipCode(9999)                  //Required in NL and DE
    ->setLocality("Stan")               //Required in NL and DE
    ->setEmail("test@svea.com")         //Optional but desirable
    ->setIpAddress("123.123.123")       //Optional but desirable
    ->setCoAddress("c/o Eriksson")      //Optional
    ->setPhoneNumber(999999)            //Optional
    )
```

####1.3.2 Options for company customers
```php
->addCustomerDetails(
    WebPayItem::companyCustomer()
    ->setNationalIdNumber(2345234)       //Required in SE, NO, DK, FI
    ->setVatNumber("NL2345234")         //Required in NL and DE
    ->setCompanyName("TestCompagniet")  //Required in NL and DE
    ->setStreetAddress("Gatan", 23)     //Required in NL and DE
    ->setZipCode(9999)                  //Required in NL and DE
    ->setLocality("Stan")               //Required in NL and DE
    ->setEmail("test@svea.com")         //Optional but desirable
    ->setIpAddress("123.123.123")       //Optional but desirable
    ->setCoAddress("c/o Eriksson")      //Optional
    ->setPhoneNumber(999999)            //Optional
    ->setAddressSelector("7fd7768")     //Optional, string recieved from WebPay::getAddress() request
    )
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 1.4 Other values
```php
    ->setCountryCode("SE")                      //Required
    ->setCurrency("SEK")                        //Required for card payment, direct payment and PayPage payment.
    ->setClientOrderNumber("nr26")              //Required for card payment, direct payment, PaymentMethod payment and PayPage payments.
    ->setOrderDate("2012-12-12")                //Required for synchronous payments
    ->setCustomerReference("33")                //Optional
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 1.5 Choose payment
End process by choosing the payment method you desire.

Invoice and payment plan will perform a synchronous payment and return an object as response.

Other hosted payments(card, direct bank and other payments from the *PayPage*)
on the other hand are asynchronous. They will return an html form with formatted message to send from your store.
The response is then returned to the return url you have specified in function setReturnUrl(). If you
use class *Response* with the xml response as parameter, you will receive a
formatted object as well.

###### Which payment method should I choose?
I am using invoice and/or payment plan payments.

>The best way is to use [`->useInvoicePayment()`] (https://github.com/sveawebpay/php-integration#154-invoicepayment) and
>[`->usePaymentPlanPayment()`] (https://github.com/sveawebpay/php-integration#154-paymentplanpayment).
>These payments are synchronous and will give you an instant response.

I am using card and/or direct bank payments.

>You can go by *PayPage* by using [`->usePayPageCardOnly()`] (https://github.com/sveawebpay/php-integration#151-paypage-with-card-payment-options)
>and [`->usePayPageDirectBankOnly()`] (https://github.com/sveawebpay/php-integration#152-paypage-with-direct-bank-payment-options).
>
>If you for example only have one specific bank payment, you can go direct to that specific bank payment by using
>[`->usePaymentMethod(PaymentMethod)`] (https://github.com/sveawebpay/php-integration#154-paymentmethod-specified)

I am using all payments.

>The most effective way is to use [`->useInvoicePayment()`](https://github.com/sveawebpay/php-integration#154-invoicepayment)
>and [`->usePaymentPlanPayment()`](https://github.com/sveawebpay/php-integration#154-paymentplanpayment)
>for the synchronous payments, and use the *PayPage* for the asynchronous requests by using [`->usePayPageCardOnly()`] (https://github.com/sveawebpay/php-integration#151-paypage-with-card-payment-options)
>and [`->usePayPageDirectBankOnly()`] (https://github.com/sveawebpay/php-integration#152-paypage-with-direct-bank-payment-options).

I am using more than one payment and want them gathered on on place.

>You can go by PayPage and choose to show all your payments here, or modify to exclude or include one or more payments. Use [`->usePayPage()`] (https://github.com/sveawebpay/php-integration#153-paypagepayment) where you can custom your own *PayPage*.

Note that Invoice and Payment plan payments will return an asynchronous response from here.

#### Synchronous payments - Invoice and PaymentPlan
The request gives an instant response.

#### 1.5.4 InvoicePayment
Perform an invoice payment. This payment form will perform a synchronous payment and return a response.
Returns *CreateOrderResponse* object.
```php
    $response = WebPay::createOrder()
      ->addOrderRow(
    WebPayItem::orderRow()
        ->setArticleNumber(1)
        ->setQuantity(2)
        ->setAmountExVat(100.00)
        ->setDescription("Specification")
        ->setName('Prod')
        ->setUnit("st")
        ->setVatPercent(25)
        ->setDiscountPercent(0)
    )
         ->setCountryCode("SE")
         ->setCustomerReference("33")
         ->setOrderDate("2012-12-12")
         ->setCurrency("SEK")
             ->useInvoicePayment()
                ->doRequest();
```
#### 1.5.5 PaymentPlanPayment
Only individual customers can use this payment type.
Perform *PaymentPlanPayment*. This payment form will perform a synchronous payment and return a response.
Returns a *CreateOrderResponse* object. Preceded by WebPay::getPaymentPlanParams().
Param: Campaign code recieved from getPaymentPlanParams().
```php
$response = WebPay::createOrder()
->addOrderRow(
    WebPayItem::orderRow()
        ->setArticleNumber(1)
        ->setQuantity(2)
        ->setAmountExVat(100.00)
        ->setDescription("Specification")
        ->setName('Prod')
        ->setUnit("st")
        ->setVatPercent(25)
        ->setDiscountPercent(0)
    )
        ->setCountryCode("SE")
        ->setCustomerReference("33")
        ->setOrderDate("2012-12-12")
        ->setCurrency("SEK")
        ->usePaymentPlanPayment("camp1")                //Parameter: campaign code recieved from getPaymentPlanParams
            ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

#### Asynchronous payments - Hosted solutions
Build order and recieve a *PaymentForm* object. Send the *PaymentForm* parameters: *merchantid*, *xmlMessageBase64* and *mac* by POST to
SveaConfig::SWP_TEST_URL or SveaConfig::SWP_PROD_URL. The *PaymentForm* object also contains a complete html form as string
and the html form element as array. The response is returned as XML, but you can use our [response handler](https://github.com/sveawebpay/php-integration#6-response-handler)
to format the response.

```html
    <form name='paymentForm' id='paymentForm' method='post' action='SveaConfig::SWP_TEST_URL'>
        <input type='hidden' name='merchantid' value='{$this->merchantid}' />
        <input type='hidden' name='message' value='{$this->xmlMessageBase64}' />
        <input type='hidden' name='mac' value='{$this->mac}' />
        <input type="submit" name="submit" value="Submit" />
    </form>
```


#### 1.5.1 PayPage with card payment options
*PayPage* with availible card payments only.

##### 1.5.1.1 Request
```php
$form = WebPay::createOrder()
->addOrderRow(
    WebPayItem::orderRow()
        ->setArticleNumber(1)
        ->setQuantity(2)
        ->setAmountExVat(100.00)
        ->setDescription("Specification")
        ->setName('Prod')
        ->setUnit("st")
        ->setVatPercent(25)
        ->setDiscountPercent(0)
    )
    ->setCountryCode("SE")
    ->setClientOrderNumber("33")
    ->setOrderDate("2012-12-12")
    ->setCurrency("SEK")
        ->usePayPageCardOnly()
            ->setPayPageLanguage("sv")                          //Optional, default english
            ->setReturnUrl("http://myurl.se")                   //Required
            ->setCancelUrl("http://myurl.se")                   //Optional
                ->getPaymentForm();

```
##### 1.5.1.2 Return
The values of *xmlMessageBase64*, *merchantid* and *mac* are to be sent as xml to SveaWebPay.
Function getPaymentForm() returns object type *PaymentForm* with accessible members:

| Member                            | Description                               |
|---------------------------------- |-------------------------------------------|
| xmlMessageBase64                  | Payment information in XML-format, Base64 encoded.|
| xmlMessage                        | Payment information in XML-format.        |
| merchantid                        | Authorization                             |
| secretWord                        | Authorization                             |
| mac                               | Message authentication code.              |
| completeHtmlFormWithSubmitButton  | A complete Html form with method= "post" with submit button to include in your code. |
| htmlFormFieldsAsArray             | Array of Html form fields to include.     |
| rawFields                         | Array of values to send in Html form. ($merchantid, $xmlMessageBase64, $mac) |

```php
$form = ...
        ->getPaymentForm();

echo $form->completeHtmlFormWithSubmitButton; //Will render a hidden form with submit button in browser
```

#### 1.5.2 PayPage with direct bank payment options
*PayPage* with available direct bank payments only.

##### 1.5.2.1 Request
```php
$form = WebPay::createOrder()
->addOrderRow(
    WebPayItem::orderRow()
        ->setArticleNumber(1)
        ->setQuantity(2)
        ->setAmountExVat(100.00)
        ->setDescription("Specification")
        ->setName('Prod')
        ->setUnit("st")
        ->setVatPercent(25)
        ->setDiscountPercent(0)
    )
    ->setCountryCode("SE")
    ->setCustomerReference("33")
    ->setOrderDate("2012-12-12")
    ->setCurrency("SEK")
        ->usePayPageDirectBankOnly()
            ->setPayPageLanguage("sv")                          //Optional, default english
            ->setReturnUrl("http://myurl.se")                   //Required
            ->setCancelUrl("http://myurl.se")                   //Optional
            ->getPaymentForm();
```
##### 1.5.2.2 Return
Returns object type PaymentForm:

| Member                            | Description                               |
|---------------------------------- |-------------------------------------------|
| xmlMessageBase64                  | Payment information in XML-format, Base64 encoded.|
| xmlMessage                        | Payment information in XML-format.        |
| merchantid                        | Authorization                             |
| secretWord                        | Authorization                             |
| mac                               | Message authentication code.              |
| completeHtmlFormWithSubmitButton  | A complete Html form with method= "post" with submit button to include in your code. |
| htmlFormFieldsAsArray             | Array of Html form fields to include.     |
| rawFields                         | Array of values to send in Html form. ($merchantid, $xmlMessageBase64, $mac) |

```php
$form = ...
        ->getPaymentForm();

echo $form->completeHtmlFormWithSubmitButton; //Will render a hidden form with submit button in browser
```

#### 1.5.3 PayPagePayment
*PayPage* with all available payments. You can also custom the *PayPage* by using one of the methods for *PayPagePayments*:
setPaymentMethod, includePaymentMethods, excludeCardPaymentMethods or excludeDirectPaymentMethods.

##### 1.5.3.1 Request
```php
$form = WebPay::createOrder()
    ->addOrderRow(
        WebPayItem::orderRow()
            ->setArticleNumber(1)
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
        )
        ->setCountryCode("SE")
        ->setCustomerReference("33")
        ->setOrderDate("2012-12-12")
        ->setCurrency("SEK")
            ->usePayPage()
                ->setPayPageLanguage("sv")                          //Optional, default english
                ->setReturnUrl("http://myurl.se")                   //Required
                ->setCancelUrl("http://myurl.se")                   //Optional
                ->getPaymentForm();
```

###### 1.5.3.1.1 Exclude specific payment methods
Optional if you want to include specific payment methods for *PayPage*.
```php
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                                               //Required
        ->setCancelUrl("http://myurl.se")                                               //Optional
        ->excludePaymentMethods(PaymentMethod::SEB_SE,PaymentMethod::INVOICE)   //Optional
        ->getPaymentForm();
```
###### 1.5.3.1.2 Include specific payment methods
Optional if you want to include specific payment methods for *PayPage*.
```php
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                                               //Required
        ->includePaymentMethods(PaymentMethod::SEB_SE,PaymentMethod::INVOICE)   //Optional
        ->getPaymentForm();
```

###### 1.5.3.1.3 Exclude Card payments
Optional if you want to exclude all cardpayment methods from *PayPage*.
```php
   ->usePayPage()
        ->setReturnUrl("http://myurl.se")                   //Required
        ->excludeCardPaymentMethods()                       //Optional
        ->getPaymentForm();
```

###### 1.5.3.1.4 Exclude Direct payments
Optional if you want to exclude all direct bank payments methods from *PayPage*.
```php
->usePayPage()
    ->setReturnUrl("http://myurl.se")                       //Required
    ->excludeDirectPaymentMethods()                         //Optional
    ->getPaymentForm();
```
##### 1.5.3.6 Return
Returns object type *PaymentForm*:

| Member                            | Description                               |
|---------------------------------- |-------------------------------------------|
| xmlMessageBase64                  | Payment information in XML-format, Base64 encoded.|
| xmlMessage                        | Payment information in XML-format.        |
| merchantid                        | Authorization                             |
| secretWord                        | Authorization                             |
| mac                               | Message authentication code.              |
| completeHtmlFormWithSubmitButton  | A complete Html form with method= "post" with submit button to include in your code. |
| htmlFormFieldsAsArray             | Array of Html form fields to include.     |
| rawFields                         | Array of values to send in Html form. ($merchantid, $xmlMessageBase64, $mac) |

```php
$form = ...
        ->getPaymentForm();

echo $form->completeHtmlFormWithSubmitButton; //Will render a hidden form with submit button in browser
```

#### 1.5.4 PaymentMethod specified
Go direct to specified payment method without the step *PayPage*.

##### 1.5.1.1 Request
```php
$form = WebPay::createOrder()
  ->addOrderRow(
    WebPayItem::orderRow()
        ->setArticleNumber(1)
        ->setQuantity(2)
        ->setAmountExVat(100.00)
        ->setDescription("Specification")
        ->setName('Prod')
        ->setUnit("st")
        ->setVatPercent(25)
        ->setDiscountPercent(0)
    )
    ->setCountryCode("SE")
    ->setClientOrderNumber("33")
    ->setOrderDate("2012-12-12")
    ->setCurrency("SEK")
        ->usePaymentMethod(PaymentMethod::KORTCERT)             //Se APPENDIX for paymentmethods
            ->setReturnUrl("http://myurl.se")                   //Required
            ->setCancelUrl("http://myurl.se")                   //Optional
            ->getPaymentForm();

```
##### 1.5.1.2 Return
The values of *xmlMessageBase64*, *merchantid* and *mac* are to be sent as xml to SveaWebPay.
Function getPaymentForm() returns Object type PaymentForm with accessible members:

| Member                            | Description                               |
|---------------------------------- |-------------------------------------------|
| xmlMessageBase64                  | Payment information in XML-format, Base64 encoded.|
| xmlMessage                        | Payment information in XML-format.        |
| merchantid                        | Authorization                             |
| secretWord                        | Authorization                             |
| mac                               | Message authentication code.              |
| completeHtmlFormWithSubmitButton  | A complete Html form with method= "post" with submit button to include in your code. |
| htmlFormFieldsAsArray             | Array of Html form fields to include.     |
| rawFields                         | Array of values to send in Html form. ($merchantid, $xmlMessageBase64, $mac) |

```php
$form = ...
        ->getPaymentForm();

echo $form->completeHtmlFormWithSubmitButton; //Will render a hidden form with submit button in browser
```

#### Other Synchronous requests

## 2. getPaymentPlanParams
Use this function to retrieve campaign codes for possible payment plan options. Use prior to create payment plan payment.

```php
    $response = WebPay::getPaymentPlanParams()
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
Can be used when creating an order. Only applicable for SE, NO and DK.

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
    $response = WebPay::getAddresses()
        ->setOrderTypeInvoice()                                              //See 3.1
        ->setCountryCode("SE")                                               //Required
        ->setIndividual("194605092222")                                      //See 3.2
        ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 4. deliverOrder
Updates the status on a previous created order as delivered. Add rows that you want delivered. The rows will automatically be
matched with the rows that was sent when creating the order.
Only applicable for invoice and payment plan payments.
Returns *DeliverOrderResult* object.

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

### 4.2 Specify order
Continue by adding values for products and other. You can add OrderRow, Fee and Discount. Chose the right WebPayItem object as parameter.
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
       ->setArticleNumber(1)                //Optional
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

### 4.3 Other values
Required is the order id received when creating the order. Required for invoice orders are *InvoiceDistributionType*.
If invoice order is credit invoice use setCreditInvoice($invoiceId) and setNumberOfCreditDays($creditDaysAsInt)
```php
    ->setOrderId($orderId)                  //Required. Received when creating order.
    ->setNumberOfCreditDays(1)              //Use for Invoice orders.
    ->setInvoiceDistributionType('Post')    //Use for Invoice orders. "Post" or "Email"
    ->setNumberOfCreditDays(1)              //Use for invoice orders.

```

```php
    $response = WebPay::deliverOrder()
    ->addOrderRow(
        WebPayItem::orderRow()
            ->setArticleNumber(1)
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
        )
        ->setOrderId("id")
        ->setInvoiceDistributionType('Post')
        ->deliverInvoiceOrder()
            ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 5. creditInvoice
When you want to credit an invoice. The order must first be delivered. When doing [DeliverOrder](https://github.com/sveawebpay/php-integration#4-deliverorder)
you will recieve an *InvoiceId* in the Response. To credit the invoice you follow the steps as in [4. DeliverOrder](https://github.com/sveawebpay/php-integration#4-deliverorder)
 but you add the call `->setCreditInvoice($InvoiceId)`:

```php
    $response = WebPay::deliverOrder()
    ->addOrderRow(
        WebPayItem::orderRow()
            ->setArticleNumber(1)
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
        )
        ->setOrderId("id")
        ->setInvoiceDistributionType('Post')
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
    $request =  WebPay::closeOrder()
        ->setOrderId($orderId)                                                  //Required, received when creating an order.
        ->closeInvoiceOrder()
             ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

## 7. Response handler
All synchronous responses are handled through *SveaResponse* and structured into objects.

Asynchronous responses recieved after sending the values *merchantid* and *xmlMessageBase64* to
hosted solutions can also be processed through the *SveaResponse* class. The response from server will be sent to the *returnUrl*
with POST or GET. The response contains the parameters:
*response*, *merchantid*, and *mac*. The *response* is a Base64 encoded message.The *mac* is a calculated authorization message.
Class *SveaResponse* will return a structured object similar to the synchronous answer instead.
Params:
* The POST or GET message
* CountryCode
* [Config](https://github.com/sveawebpay/php-integration#configuration) object. //Optional. If not given, test values from SveaConfig.php will be used

```php
  $respObject = new SveaResponse($_REQUEST,$countryCode,$config);
```

[<< To top](https://github.com/sveawebpay/php-integration#php-integration-package-api-for-sveawebpay)

##8. GetPaymentMethods
Returns an Array of SystemPaymentMethods (See Constants).
```php
  $fooArray = WebPay::getPaymentMethods()
                    ->setContryCode("SE") //optional. Default SE
                    ->doRequest();
```

## APPENDIX

### PaymentMethods
Used in usePaymentMethod($paymentMethod) and in usePayPage(),
->includePaymentMethods(...,...,...), ->excludeCardPaymentMethods(...,...,...), ->excludeDirectPaymentMethods(), ->excludeCardPaymentMethods().

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
