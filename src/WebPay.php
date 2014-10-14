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
 * @version 2.2.3
 * @package WebPay
 *
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class WebPay {

    /**
     * createOrder  -- create order and pay via invoice, payment plan, card, or direct bank payment methods
     *
     * See the CreateOrderBuilder class for more info on methods used to specify order contents
     * and chosing payment type, followed by sending the request to Svea and parsing the response.
     *
     * @return Svea\CreateOrderBuilder
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
     * specifying order rows. To partially deliver an order, use WebPayAdmin::deliverOrderRows().
     *  
     * Get an order builder instance using the WebPay::deliverOrder entrypoint, then
     * provide more information about the transaction using DeliverOrderBuilder methods. 
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
     *          ->deliverInvoiceOrder()         // select request class, use same order type as in createOrder request
     *              ->doRequest()               // and perform the request, returns DeliverOrderResult 
     *
     *          //->deliverPaymentPlanOrder()->doRequest()  // returns DeliverOrderResult 
     *          //->deliverCardOrder()->doRequest()         // returns ConfirmTransactionResponse 
     *      ;
     * 
     * @see \Svea\DeliverOrderBuilder \Svea\DeliverOrderBuilder
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
     * The WebPay::getAddresses() entrypoint is used to fetch validated addresses 
     * associated with a given customer identity. Only applicable for SE, NO and DK 
     * customers. Note that in Norway, company customers only are supported.
     *
     * Use getAddresses() to fetch a list of validated addresses associated with a given 
     * customer identity. This list can in turn be used to i.e. verify that an order delivery 
     * address matches the invoice address used by Svea for invoice and payment plan orders.
     * 
     * Get an request class instance using the WebPay::getAddresses entrypoint, then
     * provide more information about the transaction and send the request using the
     * request class methods:
     * 
     * ->setCountryCode()           (required -- supply the country code that corresponds to the account credentials used) 
     * ->setIdentifier()            (required -- i.e. the social security number, company vat number et al for the country in question)
     * 
     * Finish by selecting the correct customer type and perform the request:
     * ->getIndividualAddresses() // or getCompanyAddresses()
     *   ->doRequest()
     * 
     * The final doRequest() returns a GetAddressesResponse.
     *  
     * @see \Svea\WebService\GetAddressesResponse \Svea\WebService\GetAddressesResponse
     * 
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider Interface
     * @return Svea\WebService\GetAddresses
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
