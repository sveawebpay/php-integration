<?php
// WebPay class is excluded from Svea namespace

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * Start building request objects by choosing the right method in WebPay.
 * 
 * Class WebPay is external to Svea namespace along with class WebPayItem.
 * This is so that existing integrations don't need to worry about 
 * prefixing their existing calls to WebPay:: and orderrow item functions.
 *  
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 * @package WebPay
 */
class WebPay {

    /**
     * Start Building Order Request to create order for all Payments
     * @return \CreateOrderBuilder object
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function createOrder($config = NULL) {
       $config = $config == null ? Svea\SveaConfig::getDefaultConfig() : $config;
        return new Svea\CreateOrderBuilder($config);
    }

    /**
     * Get Payment Plan Params to present to Customer before doing PaymentPlan Payment
     * @return \GetPaymentPlanParams object
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function getPaymentPlanParams($config = NULL) {
       $config = $config == null ? Svea\SveaConfig::getDefaultConfig() : $config;
        return new Svea\GetPaymentPlanParams($config);
    }

    /**
     * Start Building Request Deliver Orders.
     * @return \deliverOrderBuilder object
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function deliverOrder($config = NULL) {
         $config = $config == null ? Svea\SveaConfig::getDefaultConfig() : $config;
        return new Svea\deliverOrderBuilder($config);
       // return new OrderBuilder();
    }

    /**
     * Start building Request to close orders.
     * @return \closeOrderBuilder object
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function closeOrder($config = NULL) {
         $config = $config == null ? Svea\SveaConfig::getDefaultConfig() : $config;
        return new Svea\closeOrderBuilder($config);
       // return new OrderBuilder();
    }

    /**
     * Start building Request for getting Address
     * @return \GetAddresses object
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function getAddresses($config = NULL) {
         $config = $config == null ? Svea\SveaConfig::getDefaultConfig() : $config;
        return new Svea\GetAddresses($config);
    }
    
    /**
     *
     * @param type decimal $price
     * @param type object $paramsResonseObject
     * @return \PaymentPlanPricePerMonth
     */
    public static function paymentPlanPricePerMonth($price,$paramsResonseObject) {
        return new Svea\PaymentPlanPricePerMonth($price,$paramsResonseObject);
    }
}
