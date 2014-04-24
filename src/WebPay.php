<?php
// WebPay class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * Start building request objects by choosing the right method in WebPay.
 *
 * Class WebPay is external to Svea namespace along with class WebPayItem.
 * This is so that existing integrations don't need to worry about
 * prefixing their existing calls to WebPay:: and orderrow item functions.
 * @version 2.0.0
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 * @package WebPay
 * @api 
 */
class WebPay {

    /**
     * Entry point for order creation process.
     *
     * See CreateOrderBuilder class for more info on specifying order contents 
     * and chosing payment type, followed by sending the request to Svea and 
     * parsing the response.
     * 
     * @return Svea\CreateOrderBuilder
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @throws Exception if $config == NULL
     *
     */
    public static function createOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\CreateOrderBuilder($config);
    }

    /**
     * Get Payment Plan Params to present to Customer before doing PaymentPlan Payment
     * @return Svea\GetPaymentPlanParams instance
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     */
    public static function getPaymentPlanParams($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\GetPaymentPlanParams($config);
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
     * @return Svea\PaymentPlanPricePerMonth $paymentPlanParamsResonseObject
     *
     */
    public static function paymentPlanPricePerMonth($price, $paymentPlanParamsResonseObject) {
        return new Svea\PaymentPlanPricePerMonth($price, $paymentPlanParamsResonseObject);
    }

    /**
     * Start Building Request Deliver Orders.
     * @return deliverOrderBuilder object
     * @param ConfigurationProvider $config  instance of implementation class of ConfigurationProvider Interface
     */
    public static function deliverOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\deliverOrderBuilder($config);
    }
    
    /**
     * Query information about an order. Support Card and Directbank orders.
     * Use the follwing methods:
     * ->setOrderId( transactionId ) from createOrder request response
     * ->setCountryCode() 
     * @todo fix rest of documentation
     * 
     * @param ConfigurationProvider $config  instance of implementation class of ConfigurationProvider Interface
     * @return Svea\QueryTransaction
     * @throws Exception
     */
    public static function queryOrder( $config = NULL ) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\QueryTransaction($config);
    }
    
    /**
     * Cancel an undelivered/unconfirmed order. Supports Invoice, PaymentPlan and Card orders.
     * Use the following methods to set the order attributes needed in the request: 
     * ->setOrderId( sveaOrderId or transactionId from createOrder request response)
     * ->setCountryCode()
     * 
     * Then select the correct ordertype and perform the request:
     * ->cancelInvoiceOrder() | cancelPartPaymentOrder() | cancelCardOrder()
     *   ->doRequest
     * 
     * The final doRequest() response is of one of the following types and may 
     * contain different attributes depending on type:
     * @see HostedAdminResponse (Card orders) or
     * @see CloseOrderResult (Invoice or PartPayment orders). 
     * 
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return Svea\CancelOrderBuilder object
     * @throws Exception
     */
    public static function cancelOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\CancelOrderBuilder($config);
    }
    
    /**
     * Start building Request to close orders. Only supports Invoice or Payment plan orders.
     * @return Svea\closeOrderBuilder object
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @deprecated 2.0.0 -- use cancelOrder instead, which supports both synchronous and asynchronous orders
     */
    public static function closeOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\closeOrderBuilder($config);
    }
    
    /**
     * Start building Request for getting Address
     * @return Svea\GetAddresses object
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     */
    public static function getAddresses($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\GetAddresses($config);
    }
    
    /**
     * Get all paymentmethods connected to your account
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return string[] array of available paymentmethods for this ConfigurationProvider
     * @deprecated 2.0.0 use WebPayAdmin::listPaymentMethods instead, which returns a HostedResponse object instead of an array
     */
    public static function getPaymentMethods($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        return new Svea\GetPaymentMethods($config);
    }
                    
    /** helper function, throws exception if no config is given */
    private static function throwMissingConfigException() {
        throw new Exception('-missing parameter: This method requires an ConfigurationProvider object as parameter. Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class SveaConfig e.g. SveaConfig::getDefaultConfig(). You can replace the default config values to return your own config values in the method.');   
    }
}
