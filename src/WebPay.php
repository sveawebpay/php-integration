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
    public static function createOrder() {
        return new createOrderBuilder();
    }
    
    /**
     * Get Payment Plan Params to present to Customer before doing PaymentPlan Payment
     * @return \GetPaymentPlanParams
     */
    public static function getPaymentPlanParams() {
        return new GetPaymentPlanParams();
    }
    
    /**
     * Start Building Request Deliver Orders.
     * @return \deliverOrder
     */
    public static function deliverOrder() {
       return new deliverOrderBuilder();
       // return new OrderBuilder();
    }

    /**
     * Start building Request to close orders.
     * @return \closeOrder
     */
    public static function closeOrder() {
       return new closeOrderBuilder();
       // return new OrderBuilder();
    }
    
    /**
     * Start building Request for getting Address
     * @return \GetAddresses
     */
    public static function getAddresses() {
        return new GetAddresses();
    }
    
    
}

?>
