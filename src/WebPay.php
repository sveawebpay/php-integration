<?php
// WebPay class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
## Introduction
The WebPay and WebPayAdmin classes make up the Svea WebPay API. Together they provide unified entrypoints to the various Svea web services. The API also encompass the support classes ConfigurationProvider, SveaResponse and WebPayItem, as well as various constant container classes.

The WebPay class methods contains the functions needed to create orders and perform payment requests using Svea payment methods. It contains methods to define order contents, send order requests, as well as support methods needed to do this.

The WebPayAdmin class methods are used to administrate orders after they have been accepted by Svea. It includes functions to update, deliver, cancel and credit orders et.al.

### Package design philosophy
In general, a request to Svea using the WebPay API starts out with you creating an instance of an order builder class, which is then built up with data using fluid method calls. Note that not all methods are applicable to all payment methods, see documentation. At a certain point, a method is used to select which service the request will go against. This method then returns an service request instance of a different class, which handles the request to the service chosen. The service request will return a response object containing the various service responses and/or error codes.

The WebPay API consists of the entrypoint methods in the WebPay and WebPayAdmin classes. These instantiate order builder classes in the Svea namespace, or in some cases request builder classes in the WebService, HostedService and AdminService sub-namespaces.

Given an instance, you then use method calls in the respective classes to populate the order or request instance. For orders, you then choose the payment method and get a request class in return. Send the request and get a service response from Svea in return. In general, the request classes will validate that all required attributes are present, and if not throw an exception stating what is missing for the request in question.

### Synchronous and asynchronous requests
Most service requests are synchronous and return a response immediately. For asynchronous hosted service payment requests, the customer will be redirected to i.e. the selected card payment provider or bank, and you will get a callback to a return url, where where you receive and parse the response.

### Namespaces
The package makes use of PHP namespaces, grouping most classes under the namespace Svea. The entrypoint classes WebPay, WebPayAdmin and associated support classes are excluded from the Svea namespace. See the generated documentation for available classes and methods.

The underlying services and methods are contained in the Svea sub-namespaces WebService, HostedService and AdminService, and may be accessed, though their api and interfaces are subject to change in the future.

### Documentation format
See the provided README.md file for an overview and examples how to utilise the WebPay and WebPayAdmin classes. The complete WebPay Integration package, including the underlying Svea service classes, methods and structures, is documented by generated documentation in the apidoc folder.

### Fluid API
The WebPay and WebPayAdmin entrypoint methods are built as a fluent API so you can use method chaining when implementing it in your code. We recommend making sure that your IDE code completion is enabled to make full use of this feature.

### Development environment
The Svea WebPay PHP integration package is developed and tested using NetBeans IDE 7.3.1 with the phpunit 3.7.24 plugin.
 *
 * @api
 * @version 2.2.4
 * @package WebPay
 *
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class WebPay {

    /**
     * Use WebPay::createOrder() to create an order using invoice, payment plan, card, or direct bank payment methods.
     *
     * See the CreateOrderBuilder class for more info on methods used to specify the order builder contents
     * and chosing a payment method to use, followed by sending the request to Svea and parsing the response.
     *
     * Invoice and Payment plan orders will perform a synchronous payment on doRequest(), and will return a response 
     * object immediately.
     * 
     * Card, Direct bank, and other hosted methods accessed via PayPage are asynchronous. They provide an html form 
     * containing a formatted message to send to Svea, which in turn will send a request response to a given return url,
     * where the response can be parsed using the SveaResponse class.
     * 
     *      $order = WebPay::createOrder($config)
     *          ->addOrderRow( $orderrow )          // required, see WebPayItem::orderRow
     *          ->addFee( $shippingfee )            // optional, see WebPayItem for invoice, shipping fee
     *          ->addDiscount( $discount )          // optional, see WebPayItem for fixed, relative discount
     *          ->addCustomerDetails( $customer )   // required for invoice and payment plan payments, see WebPayItem for individual, company id.
     *          ->setCountryCode("SE")              // required
     *          ->setOrderDate(date('c'))           // required for invoice and payment plan payments
     *          ->setCurrency("SEK")                // required for card payment, direct bank & PayPage payments. Ignored for invoice and payment plan.
     *          ->setClientOrderNumber("A123456")   // required for card payment, direct payment, PaymentMethod & PayPage payments, max length 30 chars.
     *          ->setCustomerReference("att: kgm")  // optional, ignored for card & direct bank orders, max length 30 chars.
     *      ;
     * 
     * @see \Svea\OrderRow \Svea\OrderRow
     * @see \Svea\InvoiceFee \Svea\InvoiceFee
     * @see \Svea\ShippingFee \Svea\ShippingFee
     * @see \Svea\FixedDiscount \Svea\FixedDiscount
     * @see \Svea\Relativeiscount \Svea\RelativeDiscount
     * @see \Svea\IndividualCustomer \Svea\IndividualCustomer
     * @see \Svea\CompanyCustomer \Svea\CompanyCustomer
     * @return \Svea\CreateOrderBuilder
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider Interface
     * @throws Exception
     *
     */
    public static function createOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\CreateOrderBuilder($config);
    }

    /**
     * Use the WebPay::deliverOrder() entrypoint when you deliver an order to the customer. 
     * Supports Invoice, Payment Plan and Card orders. (Direct Bank orders are not supported.)
     * 
     * The deliver order request should generally be sent to Svea once the ordered 
     * items have been sent out, or otherwise delivered, to the customer. 
     * 
     * For invoice and partpayment orders, the deliver order request triggers the 
     * invoice being sent out to the customer by Svea. (This assumes that your account
     * has auto-approval of invoices turned on, please contact Svea if unsure). 
     * 
     * For card orders, the deliver order request confirms the card transaction, 
     * which in turn allows nightly batch processing of the transaction by Svea.  
     * (Delivering card orders is only needed if your account has auto-confirm
     * turned off, please contact Svea if unsure.)
     * 
     * To deliver an invoice, partpayment or card order in full, you do not need to 
     * specify order rows. To partially deliver an order, the recommended way is to
     * use WebPayAdmin::deliverOrderRows().
     *  
     * Get an order builder instance using the WebPay::deliverOrder entrypoint, then
     * provide more information about the transaction using DeliverOrderBuilder methods: 
     * 
     *      $request = WebPay::deliverOrder($config)
     *          ->setOrderId()                  // invoice or payment plan only, required
     *          ->setTransactionId()            // card only, optional -- you can also use setOrderId
     *          ->setCountryCode()              // required
     *          ->setInvoiceDistributionType()  // invoice only, required
     *          ->setNumberOfCreditDays()       // invoice only, optional
     *          ->setCaptureDate()              // card only, optional
     *          ->addOrderRow()                 // deprecated, optional -- use WebPayAdmin::deliverOrderRows instead
     *          ->setCreditInvoice()            // deprecated, optional -- use WebPayAdmin::creditOrderRows instead
     *      ;
     *      // then select the corresponding request class and send request
     *      $response = $request->deliverInvoiceOrder()->doRequest();       // returns DeliverOrdersResponse (no rows) or DeliverOrderResult (with rows)
     *      $response = $request->deliverPaymentPlanOrder()->doRequest();   // returns DeliverOrdersResponse (no rows) or DeliverOrderResult (with rows)
     *      $response = $request->deliverCardOrder()->doRequest();          // returns ConfirmTransactionResponse
     * 
     * @see \Svea\DeliverOrderBuilder \Svea\DeliverOrderBuilder
     * @see \Svea\AdminService\DeliverOrdersResponse \Svea\AdminService\DeliverOrdersResponse
     * @see \Svea\WebService\DeliverOrderResult \Svea\WebService\DeliverOrderResult
     * @see \Svea\HostedService\ConfirmTransactionResponse \Svea\HostedService\ConfirmTransactionResponse
     * 
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider Interface
     * @return Svea\DeliverOrderBuilder
     * @throws ValidationException
     */
    public static function deliverOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\DeliverOrderBuilder($config);
    }
  
    /**
     * The WebPay::getAddresses() entrypoint is used to fetch a list validated addresses 
     * associated with a given customer identity. This list can in turn be used to i.e. 
     * verify that an order delivery address matches the invoice address used by Svea for 
     * invoice and payment plan orders. Only applicable for SE, NO and DK customers. 
     * Note that in Norway, company customers only are supported.
     * 
     * Get an request class instance using the WebPay::getAddresses entrypoint, then
     * provide more information about the transaction and send the request using the
     * request class methods:
     * 
     * Use setCountryCode() to supply the country code that corresponds to the account 
     * credentials used for the address lookup. Note that this means that you cannot 
     * look up a user in a foreign country, this is a consequence of the fact that the 
     * invoice and partpayment methods don't support foreign orders.
     *
     * Use setCustomerIdentifier() to provide the exact credentials needed to identify 
     * the customer according to country:
     *      SE: Personnummer (private individual) or Organisationsnummer (company/legal entity)
     *      NO: Organisasjonsnummer (company or other legal entity)
     *      DK: Cpr.nr (private individual) or CVR-nummer (company or other legal entity)
     * 
     * Then use either getIndividualAddresses() or getCompanyAddresses() depending on what kind of customer you want to look up.
     *  
     * The final doRequest() will send the getAddresses request to Svea and return the result. 
     *
     * The doRequest() method will then check if there exists credentials to use for the request in the given configurationProvider.
     * 
     * (Note that this behaviour may cause problems if your integration is set to use different (test/production) credentials 
     * for invoice and payment plan -- if you get an error and this is the case, you may use one of the deprecated methods 
     * setOrderTypeInvoice() or setOrderTupePaymentPlan() to explicity state which method credentials to use.)
     *
     *      $request = WebPay::getAddresses($config)
     *          ->setCountryCode()                  // required -- the country to perform the customer address lookup in
     *          ->setCustomerIdentifier()           // required -- social security number, company vat number etc. used to identify customer
     *          ->setOrderTypeInvoice()             // deprecated -- method that corresponds to the ConfigurationProvider account credentials used 
     *          ->setOrderTypePaymentPlan()         // deprecated -- method that corresponds to the ConfigurationProvider account credentials used 
     *      ;
     *      // then select the corresponding request class and send request
     *      $response = $request->getIndividualAddresses()->doRequest();    // returns GetAddressesResponse
     *      $response = $request->getCompanyAddresses()->doRequest();       // returns GetAddressesResponse
     *
     * @see Svea\WebService\GetAddresses Svea\WebService\GetAddresses
     * @return Svea\WebService\GetAddressesResponse Svea\WebService\GetAddressesResponse
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider Interface
     * @throws \Svea\ValidationException        
     */    
    public static function getAddresses($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\WebService\GetAddresses($config);
    }

    /**
     * getPaymentPlanParams -- fetch current campaigns (payment plans) for a given client, used by i.e. paymentplan orders
     *
     * See the GetPaymentPlanParams request class for more info on required methods,
     * how to send the request to Svea, as well as the final response type.
     *
     * @return Svea\WebService\GetPaymentPlanParams
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     */
    public static function getPaymentPlanParams($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\WebService\GetPaymentPlanParams($config);
    }

    /**
     * getPaymentMethods -- fetch available payment methods for a given client, used to define i.e. paymentmethod in payments
     *
     * See the GetPaymentMethods request class for more info on required methods,
     * how to send the request to Svea, as well as the final response type.
     *
     * @deprecated 2.0.0 use WebPayAdmin::listPaymentMethods() instead, which returns a response object instead of an array
     * @see \Svea\HostedService\ListPaymentMethods() \Svea\HostedService\ListPaymentMethods()
     *
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return string[] array of available paymentmethods for this ConfigurationProvider
     */
    public static function getPaymentMethods($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\HostedService\GetPaymentMethods($config);
    }

    /**
     * Calculates price per month for all available campaigns.
     *
     * This is a helper function provided to calculate the monthly price for the
     * different payment plan options for a given sum. This information may be
     * used when displaying i.e. payment options to the customer by checkout, or
     * to display the lowest amount due per month to display on a product level.
     *
     * The returned instance contains an array value, where each element in turn
     * contains a pair of campaign code and price per month:
     * $paymentPlanParamsResonseObject->value[0..n] (for n campaignCodes), where
     * value['campaignCode' => campaignCode, 'pricePerMonth' => pricePerMonth]
     *
     * @param float $price
     * @param object $paymentPlanParamsResonseObject
     * @return Svea\WebService\PaymentPlanPricePerMonth
     *
     */
    public static function paymentPlanPricePerMonth($price, $paymentPlanParamsResponseObject) {
        return new Svea\WebService\PaymentPlanPricePerMonth($price, $paymentPlanParamsResponseObject);
    }

    /**
     * Start building Request to close orders. Only supports Invoice or Payment plan orders.
     * @deprecated 2.0.0 -- use WebPayAdmin::cancelOrder instead, which supports both synchronous and asynchronous orders
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return Svea\CloseOrderBuilder
     */
    public static function closeOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\CloseOrderBuilder($config);
    }

    /**
     * The WebPay::listPaymentMethods method is used to fetch all available paymentmethods configured for a given country.
     *
     * Use the WebPay::listPaymentMethods() entrypoint to get an instance of
     * ListPaymentMethods. Then provide more information about the transaction and
     * send the request using ListPaymentMethod methods.
     *
     *   $methods = WebPay::listPaymentMethods( $config )
     *      ->setCountryCode("SE")      // required
     *      ->doRequest();
     * 
     * Following the ->doRequest call you receive an instance of ListPaymentMethodsResponse.
     *
     * @see \Svea\HostedService\ListPaymentMethods \Svea\HostedService\ListPaymentMethods
     * @see \Svea\HostedService\ListPaymentMethodsResponse \Svea\HostedService\ListPaymentMethodsResponse
     *
     * @param ConfigurationProvider $config
     * @return Svea\HostedService\ListPaymentMethods
     */
    static function listPaymentMethods($config) {
        return new Svea\HostedService\ListPaymentMethods($config);
    }

    /** helper function, throws exception if no config is given */
    private static function throwMissingConfigException() {
        throw new Exception('-missing parameter: This method requires an ConfigurationProvider object as parameter. Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class SveaConfig e.g. SveaConfig::getDefaultConfig(). You can replace the default config values to return your own config values.');
    }
}
