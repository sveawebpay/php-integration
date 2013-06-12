<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Description of closeOrder
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class closeOrderBuilder {

     /**
     * Order Id recieved when creating order
     * @var Order id
     */
    public $orderId;
    /**
     * @var type String "Invoice" or "PaymentPlan"
     */
    public $orderType;
    /**
     * @var Instance of class SveaConfig
     */
    public $conf;
    public $countryCode;

    public function __construct($config) {
        $this->handleValidator = new HandleOrderValidator();
         $this->conf = $config;
    }

    /**
     * When function is called it turns into testmode
     * @return \CloseOrderBuilder

    public function setTestmode() {
        $this->testmode = TRUE;
        return $this;
    }
     *
     */

    /**
     * Required
     * @param type $orderIdAsString
     * @return \CloseOrderBuilder
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }

    /**
     * @param string $countryCodeAsString
     * @return \CloseOrderBuilder
     */
    public function setCountryCode($countryCodeAsString){
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * @return \CloseOrder
     */
    public function closeInvoiceOrder() {
        $this->orderType = "Invoice";
        return new CloseOrder($this);
    }

    /**
     * @return \CloseOrder
     */
    public function closePaymentPlanOrder() {
        $this->orderType = "PaymentPlan";
        return new CloseOrder($this);
    }
}