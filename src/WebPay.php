<?php

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * Start Building Request Objects By choosing the right Method.
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebPay
 */
class WebPay {

    /**
     * Start Building Order Request to create order for all Payments
     * @return \createOrder
     */
    public static function createOrder($config = NULL) {
      // $config = $config==null ? SveaConfig::getDefaultConfig() : $config;
       $config = $config==null ? new SveaConfigurationProvider(SveaConfig::getDefaultConfig()) : $config;
        return new CreateOrderBuilder($config);
    }
    
    /**
     * Get Payment Plan Params to present to Customer before doing PaymentPlan Payment
     * @return \GetPaymentPlanParams
     */
    public static function getPaymentPlanParams($config = NULL) {
       $config = $config==null ? new SveaConfigurationProvider(SveaConfig::getDefaultConfig()) : $config;
        return new GetPaymentPlanParams($config);
    }
    
    /**
     * Start Building Request Deliver Orders.
     * @return \deliverOrder
     */
    public static function deliverOrder($config = NULL) {
         $config = $config==null ? new SveaConfigurationProvider(SveaConfig::getDefaultConfig()) : $config;
        return new deliverOrderBuilder($config);
       // return new OrderBuilder();
    }

    /**
     * Start building Request to close orders.
     * @return \closeOrder
     */
    public static function closeOrder($config = NULL) {
         $config = $config==null ? new SveaConfigurationProvider(SveaConfig::getDefaultConfig()) : $config;
        return new closeOrderBuilder($config);
       // return new OrderBuilder();
    }
    
    /**
     * Start building Request for getting Address
     * @return \GetAddresses
     */
    public static function getAddresses($config = NULL) {
         $config = $config==null ? new SveaConfigurationProvider(SveaConfig::getDefaultConfig()) : $config;
        return new GetAddresses($config);
    }
    
    
}

?>
