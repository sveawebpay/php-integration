<?php
// WebPay class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * Start building request objects by choosing the right method in WebPay.
 *
 * Class WebPay is external to Svea namespace along with class WebPayItem.
 * This is so that existing integrations don't need to worry about
 * prefixing their existing calls to WebPay:: and orderrow item functions.
 * @version 1.6.1
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
     * @param type decimal $price
     * @param type object $paramsResonseObject
     * @return Svea\PaymentPlanPricePerMonth $paymentPlanParamsResonseObject
     *
     */
    public static function paymentPlanPricePerMonth($price, $paymentPlanParamsResonseObject) {
        return new Svea\PaymentPlanPricePerMonth($price, $paymentPlanParamsResonseObject);
    }

    /**
     * Start Building Request Deliver Orders.
     * @return \deliverOrderBuilder object
     * @param instance of implementation class of ConfigurationProvider Interface
     */
    public static function deliverOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\deliverOrderBuilder($config);
    }

    /**
     * Start building Request to close orders.
     * @return \closeOrderBuilder object
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     */
    public static function closeOrder($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\closeOrderBuilder($config);
    }

    /**
     * Start building Request for getting Address
     * @return \GetAddresses object
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     */
    public static function getAddresses($config = NULL) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }

        return new Svea\GetAddresses($config);
    }

    /**
     * Get all paymentmethods connected to your account
     * @param ConfigurationProvider $config  instance implementing ConfigurationProvider
     * @return \Svea\GetPaymentMethods Array of Paymentmethods
     */
    public static function getPaymentMethods($config = NULL) {
        if ($config == NULL) {
           throw new Exception('-missing parameter:
                                This method requires an ConfigurationProvider object as parameter.
                                Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class.
                                Alternative create an instance from SveaConfigurationProvider that will return Svea default testvalues.
                                There you can replace the default config values to return your own config values in the method.'
                                );
       }
        return new Svea\GetPaymentMethods($config);
    }
    
    /**
     * Credit an existing Card or Bank transaction.
     * The transaction must have reached Svea status SUCCESS.
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     */
    public static function creditTransaction( $config = NULL ) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        
        return new Svea\CreditTransaction($config);
    }
    
    /**
     * Annul an existing Card transaction.
     * The transaction must have Svea status AUTHORIZED or CONFIRMED.
     * Note that this only supports Card transactions.
     * 
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     */
    public static function annulTransaction( $config = NULL ) {
        if( $config == NULL ) { WebPay::throwMissingConfigException(); }
        
        return new Svea\AnnulTransaction($config);
    }
    
    private static function throwMissingConfigException() {
        throw new Exception('-missing parameter: This method requires an ConfigurationProvider object as parameter. Create a class that implements class ConfigurationProvider. Set returnvalues to configuration values. Create an object from that class. Alternative use static function from class SveaConfig e.g. SveaConfig::getDefaultConfig(). You can replace the default config values to return your own config values in the method.');   
    }
}
