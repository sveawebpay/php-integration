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
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function createOrder($config = NULL) {
       $config = $config == null ? SveaConfig::getDefaultConfig() : $config;
        return new CreateOrderBuilder($config);
    }

    /**
     * Get Payment Plan Params to present to Customer before doing PaymentPlan Payment
     * @return \GetPaymentPlanParams
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function getPaymentPlanParams($config = NULL) {
       $config = $config == null ? SveaConfig::getDefaultConfig() : $config;
        return new GetPaymentPlanParams($config);
    }

    /**
     * Start Building Request Deliver Orders.
     * @return \deliverOrder
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function deliverOrder($config = NULL) {
         $config = $config == null ?SveaConfig::getDefaultConfig() : $config;
        return new deliverOrderBuilder($config);
       // return new OrderBuilder();
    }

    /**
     * Start building Request to close orders.
     * @return \closeOrder
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function closeOrder($config = NULL) {
         $config = $config == null ? SveaConfig::getDefaultConfig() : $config;
        return new closeOrderBuilder($config);
       // return new OrderBuilder();
    }

    /**
     * Start building Request for getting Address
     * @return \GetAddresses
     * @param instance of implementation class of ConfigurationProvider Interface
     * If left blank, default settings from SveaConfig will be used
     */
    public static function getAddresses($config = NULL) {
         $config = $config == null ? SveaConfig::getDefaultConfig() : $config;
        return new GetAddresses($config);
    }
    /**
     *
     * @param type decimal $price
     * @param type object $paramsResonseObject
     * @return \PaymentPlanPricePerMonth
     */
    public static function paymentPlanPricePerMonth($price,$paramsResonseObject){
        return new PaymentPlanPricePerMonth($price,$paramsResonseObject);
    }

}