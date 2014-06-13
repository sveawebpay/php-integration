<?php
// WebPay class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";


/**
 * The WebPay and WebPayAdmin classes make up the Svea WebPay API. Together they 
 * provide unified entrypoints to the various Svea web services. The API also
 * encompass the support classes ConfigurationProvider, SveaResponse and 
 * WebPayItem, as well as various constant container classes.
 * 
 * The WebPay:: class methods contains the functions needed to create orders and
 * perform payment requests using Svea payment methods. It contains methods to
 * define order contents, send order requests, as well as support methods 
 * needed to do this.
 * 
 * The WebPayAdmin:: methods are used to administrate orders after they have been
 * accepted by Svea. It includes functions to update, deliver, cancel and credit
 * orders et.al.
 * 
 * See the provided README.md file for an overview and examples how to utilise 
 * the WebPay and WebPayAdmin classes. The complete WebPay Integration package, 
 * including the underlying Svea service classes, methods and structures, is 
 * documented by generated documentation in the apidoc folder.   
 * 
 * The underlying services and methods are contained in the Svea namespace, and
 * may be accessed, though their api and interfaces are subject to change.
 * 
 * @version 2.0 (140611)
 * @package WebPay
 * @api 
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
     * deliverOrder (without orderRows) -- deliver in full an invoice or payment plan order, confirm a card order
     * 
     * deliverOrder (with orderRows) -- (deprecated) partially deliver, change or credit an invoice or payment plan order, depending on set options
     * 
     * See the DeliverOrderBuilder class for more info on required methods used to i.e. specify order rows, 
     * how to send the request to Svea, as well as the final response type. 
     * 
     * See also WebPayAdmin::deliverOrderRows for the preferred way to partially deliver an invoice or payment plan order.
     * 
     * @see \WebPayAdmin::deliverOrderRows() WebPayAdmin::deliverOrderRows()
     * 
     * @return Svea\DeliverOrderBuilder
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider Interface
     * @throws Exception
     */
    public static function deliverOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\DeliverOrderBuilder($config);
    }
          
    /**
     * getAddresses -- fetch validated addresses associated with a given customer identity
     * 
     * See the GetAddresses request class for more info on required methods, 
     * how to send the request to Svea, as well as the final response type.
     * 
     * The GetAddresses service is only applicable for SE, NO and DK customers and accounts. 
     * In Norway, GetAddresses may only be performed on company customers.
     * 
     * @return Svea\WebService\GetAddresses
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider Interface
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
     * See the GetPaymentPlanParams request class for more info on required methods, 
     * how to send the request to Svea, as well as the final response type.
     * 
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return string[] array of available paymentmethods for this ConfigurationProvider
     * @deprecated 2.0.0 use WebPayAdmin::listPaymentMethods instead, which returns a HostedResponse object instead of an array
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
        
    /** helper function, throws exception if no config is given */
    private static function throwMissingConfigException() {
        throw new Exception('-missing parameter: This method requires an ConfigurationProvider object as parameter. Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class SveaConfig e.g. SveaConfig::getDefaultConfig(). You can replace the default config values to return your own config values in the method.');   
    }
}
