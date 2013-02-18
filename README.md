# PHP Integration Package API for SveaWebPay

## Index
* [Introduction](https://github.com/sveawebpay/php-integration/tree/develop#introduction)
* [Configuration](https://github.com/sveawebpay/php-integration/tree/develop#configuration)
* [1. CreateOrder](https://github.com/sveawebpay/php-integration/tree/develop#1-createorder)
    * [Test mode](https://github.com/sveawebpay/php-integration/tree/develop#11-test-mode)
    * [Specify order](https://github.com/sveawebpay/php-integration/tree/develop#12-specify-order)
    * [Customer identity](https://github.com/sveawebpay/php-integration/tree/develop#13-customer-identity)
    * [Other values](https://github.com/sveawebpay/php-integration/tree/develop#14-other-values)
    * [Choose payment](https://github.com/sveawebpay/php-integration/tree/develop#15-choose-payment)
* [2. GetPaymentPlanParams](https://github.com/sveawebpay/php-integration/tree/develop#2-getpaymentplanparams)
* [3. GetAddresses](https://github.com/sveawebpay/php-integration/tree/develop#2-getpaymentplanparams)
* [4. DeliverOrder](https://github.com/sveawebpay/php-integration/tree/develop#2-getpaymentplanparams)
    * [Test mode](https://github.com/sveawebpay/php-integration/tree/develop#41-testmode)
    * [Specify order](https://github.com/sveawebpay/php-integration/tree/develop#42-specify-order)
    * [Other values](https://github.com/sveawebpay/php-integration/tree/develop#43-other-values)
* [5. CloseOrder](https://github.com/sveawebpay/php-integration/tree/develop#5-closeorder)
* [6. Response handler](https://github.com/sveawebpay/php-integration/tree/develop#6-response-handler)
* [APPENDIX](https://github.com/sveawebpay/php-integration/tree/develop#appendix)


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
$requestObject = 
$foo()->...
    ->..;
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

## Configuration 
There are three ways to configure Svea authorization. Choose one of the following:

* 1. Drop a file named SveaConfig.php in folder Config looking the same as existing file
   but with your own authorization values. This method is preffered as it simplyfies updates in the package.
* 2. Modify function __construct() in file SveaConfig.php in folder Config with your authorization values.
* 3. Everytime when creating an order, after choosing payment type, use function setPasswordBasedAuthorization() for Invoice/Payment plan 
    or setMerchantIdBasedAuthorization() for other hosted payments like card and direct bank payments.

[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

## 1. createOrder                                                            
Creates an order and performs payment for all payment forms. Invoice and payment plan will perform 
a synchronous payment and return a response. 
Other hosted payments, like card, direct bank and other payments from the *PayPage*,
on the other hand are asynchronous. They will return an html form with formatted message to send from your store.
For every new payment type implementation, you follow the steps from the beginning and chose your payment type preffered in the end:
Build order -> choose payment type -> doRequest/getPaymentForm

```php
$response = WebPay::createOrder()
//If testmode
    ->setTestmode()
//For all products and other items
   ->addOrderRow(Item::orderRow()...)
//If shipping fee
   ->addFee(Item::shippingFee()...)
//If invoice with invoice fee
    ->addFee(Item::invoiceFee()
//If discount or coupon with fixed amount
    ->addDiscount(Item::fixedDiscount()
//If discount or coupon with percent discount
   ->addDiscount(Item::relativeDiscount()
//Individual customer values. 
    ->addCustomerDetails(Item::individualCustomer()...)
//Company customer values
    ->addCustomerDetails(Item::companyCustomer()...)
//Other values
    ->setCountryCode("SE")
    ->setOrderDate("2012-12-12")
    ->setCustomerReference("33")
    ->setClientOrderNumber("nr26")
    ->setCurrency("SEK")
    ->setAddressSelector("7fd7768")

//Continue by choosing one of the following paths
    //Continue as a card payment
    ->usePayPageCardOnly() 
        ...
        ->getPaymentForm();
    //Continue as a direct bank payment		
    ->usePayPageDirectBankOnly()
        ...
        ->getPaymentForm();
    //Continue as a PayPage payment
    ->usePayPage()
        ...
        ->getPaymentForm();
    //Continue as a PayPage payment
    ->usePaymentMethod (PaymentMethod::DBSEBSE) //see APPENDIX for Constants
        ...
        ->getPaymentForm();
    //Continue as an invoice payment
    ->useInvoicePayment()
    ...
        ->doRequest();
    //Continue as a payment plan payment
    ->usePaymentPlanPayment("campaigncode", 0)
    ...
        ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 1.1 Test mode                                                            
Set test mode while developing to make the calls to our test server.
Remove when you change to production mode.	
```php
    ->setTestmode()
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)
	
### 1.2 Specify order                                                        
Continue by adding values for products and other. You can add OrderRow, Fee and Discount. Chose the right Item object as parameter.
You can use the *add-* functions with an Item object or an array of Item objects as parameters. 

```php
->addOrderRow(Item::orderRow()->...)

//or
$orderRows[] = Item::orderRow()->...;
->addOrderRow($orderRows)
```
	
#### 1.2.1 OrderRow
All products and other items. It´s required to have a minimum of one orderrow.
**The price can be set in a combination by using a minimum of two out of three functions: setAmountExVat(), setAmountIncVat() and setVatPercent().**
```php
->addOrderRow(
      Item::orderRow()     
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
    Item::shippingFee()
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
    Item::invoiceFee()
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
    Item::fixedDiscount()                
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
    Item::relativeDiscount()
        ->setDiscountPercent(50)                //Required
        ->setDiscountId("1")                    //Optional      
        ->setUnit("st")                         //Optional
        ->setName('Relative')                   //Optional
        ->setDescription("RelativeDiscount")    //Optional
    )
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 1.3 Customer Identity   
Customer identity is required for invoice and payment plan orders. Required values varies 
depending on country and customer type. For SE, NO, DK and FI ssn (Social Security Number)
or company id number is required. Email and ip address are desirable.

####1.3.1 Options for individual customers
```php
->addCustomerDetails(
    Item::individualCustomer()
    ->setSsn(194605092222)              //Required for individual customers in SE, NO, DK, FI
    ->setInitials("SB")                 //Required for individual customers in NL 
    ->setBirthDate(1923, 12, 12)        //Required for individual customers in NL and DE
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
    Item::companyCustomer()
    ->setCompanyIdNumber(2345234)       //Required for company customers in SE, NO, DK, FI
    ->setVatNumber("NL2345234")  //Required for NL and DE
    ->setCompanyName("TestCompagniet")  //Required for Eu countries like NL and DE
    )
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 1.4 Other values  
```php
    ->setCountryCode("SE")                      //Required for synchronous payments    
    ->setCurrency("SEK")                        //Required for card payment, direct payment and PayPage payment.
    ->setClientOrderNumber("nr26")              //Required for card payment, direct payment, PaymentMethod payment and PayPage payments.
    ->setAddressSelector("7fd7768")             //Optional. Recieved from getAddresses
    ->setOrderDate("2012-12-12")                //Required for synchronous payments
    ->setCustomerReference("33")                //Optional
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 1.5 Choose payment 
End process by choosing the payment method you desire.

Invoice and payment plan will perform a synchronous payment and return an object as response. 

Other hosted payments(card, direct bank and other payments from the *PayPage*)
on the other hand are asynchronous. They will return an html form with formatted message to send from your store.
The response is then returned to the return url you have specified in function setReturnUrl(). If you
use class *Response* with the xml response as parameter, you will receive a
formatted object as well. 

#### Asynchronous payments - Hosted solutions
Build order and recieve a *PaymentForm* object. Send the *PaymentForm* parameters: *merchantid*, *xmlMessageBase64* and *mac* by POST to
SveaConfig::SWP_TEST_URL or SveaConfig::SWP_PROD_URL. The *PaymentForm* object also contains a complete html form as string 
and the html form element as array.

```html
    <form name='paymentForm' id='paymentForm' method='post' action='SveaConfig::SWP_TEST_URL'>
        <input type='hidden' name='merchantid' value='{$this->merchantid}' />
        <input type='hidden' name='message' value='{$this->xmlMessageBase64}' />
        <input type='hidden' name='mac' value='{$this->mac}' />
        <noscript><p>Javascript is inactivated.</p></noscript>
        <input type="submit" name="submit" value="Submit" />
    </form>
```


#### 1.5.1 PayPage with card payment options
*PayPage* with availible card payments only.

##### 1.5.1.1 Request
If Config/SveaConfig.php is not modified you can set your store authorization here.
```php
$form = WebPay::createOrder()
->addOrderRow(
    Item::orderRow()
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
            ->setMerchantIdBasedAuthorization(1200, "f78hv9")   //Optional
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
If Config/SveaConfig.php is not modified you can set your store authorization here.
```php
$form = WebPay::createOrder()
->setTestmode()
->addOrderRow(
    Item::orderRow()
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
            ->setMerchantIdBasedAuthorization(1200, "f78hv9")   //Optional
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
        ->setTestmode()
->addOrderRow(
    Item::orderRow()
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
                ->setMerchantIdBasedAuthorization(1200, "f78hv9")   //Optional
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
        ->excludePaymentMethods(PaymentMethod::DBSEBSE,PaymentMethod::SVEAINVOICE_SE)   //Optional
        ->getPaymentForm();
```
###### 1.5.3.1.2 Include specific payment methods
Optional if you want to include specific payment methods for *PayPage*.
```php   
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")                                               //Required
        ->includePaymentMethods(PaymentMethod::DBSEBSE,PaymentMethod::SVEAINVOICE_SE)   //Optional
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
If Config/SveaConfig.php is not modified you can set your store authorization here.
```php
$form = WebPay::createOrder()
  ->addOrderRow(
    Item::orderRow()
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
            ->setMerchantIdBasedAuthorization(1200, "f78hv9")   //Optional
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

#### Synchronous solutions - Invoice and PaymentPlan
       
#### 1.5.4 InvoicePayment
Perform an invoice payment. This payment form will perform a synchronous payment and return a response.
Returns *CreateOrderResponse* object.
If Config/SveaConfig.php is not modified you can set your store authorization here.
```php
    $response = WebPay::createOrder()
        ->setTestmode()
      ->addOrderRow(
    Item::orderRow()
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
             ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
             ->doRequest();
```
#### 1.5.5 PaymentPlanPayment
Perform *PaymentPlanPayment*. This payment form will perform a synchronous payment and return a response.
Returns a *CreateOrderResponse* object. Preceded by WebPay::getPaymentPlanParams().
If Config/SveaConfig.php is not modified you can set your store authorization here.
Param: Campaign code recieved from getPaymentPlanParams().
```php
$response = WebPay::createOrder()
->setTestmode()
->addOrderRow(
    Item::orderRow()
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
           ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
           ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

## 2. getPaymentPlanParams   
Use this function to retrieve campaign codes for possible payment plan options. Use prior to create payment plan payment.
Returns *PaymentPlanParamsResponse* object.
If Config/SveaConfig.php is not modified you can set your store authorization here.

```php
    $response = WebPay::getPaymentPlanParams()
        ->setTestmode()
            ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
            ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

## 3. getAddresses 
Returns *getAddressesResponse* object with an *AddressSelector* for the associated addresses for a specific security number. 
Can be used when creating an order. Only applicable for SE, NO and DK.
If Config/Config.php is not modified you can set your store authorization here.

[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 3.1 Order type 
```php
    ->setOrderTypeInvoice()         //Required if this is an invoice order
or
    ->setOrderTypePaymentPlan()     //Required if this is a payment plan order
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 3.2 Customer type 
```php
    ->setIndividual(194605092222)   //Required if this is an individual customer
or
    ->setCompany("CompanyId")       //Required if this is a company customer
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 3.3                                                                      	
```php
    $response = WebPay::getAddresses()
        ->setTestmode()
        ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
        ->setOrderTypeInvoice()                                              //See 3.1   
        ->setCountryCode("SE")                                               //Required
        ->setIndividual(194605092222)                                        //See 3.2   
        ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

## 4. deliverOrder                                                           
Updates the status on a previous created order as delivered. Add rows that you want delivered. The rows will automatically be
matched with the rows that was sent when creating the order.
Only applicable for invoice and payment plan payments.
Returns *DeliverOrderResult* object.
If Config/SveaConfig.php is not modified you can set your store authorization here.

[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 4.1 Testmode                                                             
Set test mode while developing to make the calls to our test server.
Remove when you change to production mode.

Ex. 
```php
    ->setTestmode()
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 4.2 Specify order                                                        
Continue by adding values for products and other. You can add OrderRow, Fee and Discount. Chose the right Item object as parameter.
You can use the **add-** functions with an Item object or an array of Item objects as parameters. 

```php
->addOrderRow(Item::orderRow()->...)

//or
$orderRows[] = Item::orderRow()->...; 
->addOrderRow($orderRows)
```

[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

#### 4.2.1 OrderRow
All products and other items. It is required to have a minimum of one row.
```php
  ->addOrderRow(
    Item::orderRow()   
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
    Item::shippingFee()
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
    Item::invoiceFee()
        ->setAmountExVat(50)                //Required
        ->setVatPercent(25)                 //Required
        ->setName('Svea fee')               //Optional
        ->setDescription("Fee for invoice") //Optional       
        ->setUnit("st")                     //Optional
        ->setDiscountPercent(0)             //Optional
  )
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 4.3 Other values  
Required is the order id received when creating the order. Required for invoice orders are *InvoiceDistributionType*. 
If invoice order is credit invoice use setCreditInvoice($invoiceId) and setNumberOfCreditDays($creditDaysAsInt)
```php
    ->setOrderId($orderId)                  //Required. Received when creating order.
    ->setNumberOfCreditDays(1)              //Use for Invoice orders.
    ->setInvoiceDistributionType('Post')    //Use for Invoice orders. "Post" or "Email"
    ->setCreditInvoice                      //Use for invoice orders, if this should be a credit invoice.
    ->setNumberOfCreditDays(1)              //Use for invoice orders.
```

```php
    $response = WebPay::deliverOrder()
->setTestmode()
->addOrderRow(
    Item::orderRow()
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
            ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
            ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

## 5. closeOrder                                                             
Use when you want to cancel an undelivered order. Valid only for invoice and payment plan orders. 
Required is the order id received when creating the order. If Config/SveaConfig.php is not modified you can set your store authorization here.

[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

### 5.1 Close by payment type                                                
```php
    ->closeInvoiceOrder()
or
    ->closePaymentPlanOrder()
```

```php
    $request =  WebPay::closeOrder()
        ->setTestmode()
        ->setOrderId($orderId)                                                  //Required, received when creating an order.
        ->closeInvoiceOrder()
            ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)//Optional
            ->doRequest();
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

## 6. Response handler                                                       
All synchronous responses are handled through *SveaResponse* and structured into objects.
Responses recieved after sending the values *mac*, *merchantid* and *xmlMessageBase64* to
hosted solutions can also be processed through the *SveaResponse* class.

The response from server will be sent to the *returnUrl* with POST or GET as XML, and
*SveaResponse* will return a structured object similar to the synchronous aswer instead.
```php
  $respObject = new SveaResponse($_REQUEST['response']); 
```
[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)

## APPENDIX 

### PaymentMethods
Used in usePaymentMethod($paymentMethod) and in usePayPage(), 
->includePaymentMethods(...,...,...), ->excludeCardPaymentMethods(...,...,...), ->excludeDirectPaymentMethods(), ->excludeCardPaymentMethods().

| Payment method                    | Description                                   |
|-----------------------------------|-----------------------------------------------|
| PaymentMethod::DBNORDEASE         | Direct bank payment, Nordea, Sweden.          | 
| PaymentMethod::DBSEBSE            | Direct bank payment, private, SEB, Sweden.    |
| PaymentMethod::DBSEBFTGSE         | Direct bank payment, company, SEB, Sweden.    |
| PaymentMethod::DBSHBSE            | Direct bank payment, Handelsbanken, Sweden.   |
| PaymentMethod::DBSWEDBANKSE       | Direct bank payment, Swedbank, Sweden.        |
| PaymentMethod::KORTCERT           | Card payments, Certitrade.                    |
| PaymentMethod::PAYPAL             | Paypal                                        |
| PaymentMethod::SKRILL             | Card payment with Dankort, Skrill.            |
| PaymentMethod::SVEAINVOICESE      | Invoice by PayPage in SE only.                |
| PaymentMethod::SVEASPLITSE        | PaymentPlan by PayPage in SE only.            |
| PaymentMethod::SVEAINVOICEEU_SE   | Invoice by PayPage in SE.                     |
| PaymentMethod::SVEAINVOICEEU_NO   | Invoice by PayPage in NO.                     |
| PaymentMethod::SVEAINVOICEEU_DK   | Invoice by PayPage in DK.                     |
| PaymentMethod::SVEAINVOICEEU_FI   | Invoice by PayPage in FI.                     |
| PaymentMethod::SVEAINVOICEEU_NL   | Invoice by PayPage in NL.                     |
| PaymentMethod::SVEAINVOICEEU_DE   | Invoice by PayPage in DE.                     |
| PaymentMethod::SVEASPLITEU_SE     | PaymentPlan by PayPage in SE.                 |
| PaymentMethod::SVEASPLITEU_NO     | PaymentPlan by PayPage in NO.                 |
| PaymentMethod::SVEASPLITEU_DK     | PaymentPlan by PayPage in DK.                 |
| PaymentMethod::SVEASPLITEU_FI     | PaymentPlan by PayPage in FI.                 |
| PaymentMethod::SVEASPLITEU_DE     | PaymentPlan by PayPage in DE.                 |
| PaymentMethod::SVEASPLITEU_NL     | PaymentPlan by PayPage in NL.                 |

[<< To top](https://github.com/sveawebpay/php-integration/tree/develop#php-integration-package-api-for-sveawebpay)