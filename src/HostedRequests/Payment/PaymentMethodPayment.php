<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PaymentMethodPayment
 *
 * @author anne-hal
 */
class PaymentMethodPayment extends HostedPayment{
    
     public $paymentMethod;
     /**
     * 
     * @param type $order, $paymentmethod
     */
    public function __construct($order, $paymentmethod) {
        parent::__construct($order);
        $this->paymentMethod = $paymentmethod;
    }
    
     protected function configureExcludedPaymentMethods($request) {
        if (isset($this->paymentMethod)) {
            $request['paymentMethod'] = $this->paymentMethod;
        }

        return $request;
    }
    
    /**
     * Set return Url for redirect when payment is completed
     * @param type $returnUrlAsString
     * @return \HostedPayment
     */
    public function setReturnUrl($returnUrlAsString) {
        $this->returnUrl = $returnUrlAsString;
        return $this;
    }
    
    /**
     * 
     * @param type $cancelUrlAsString
     * @return \HostedPayment
     */
    public function setCancelUrl($cancelUrlAsString) {
        $this->cancelUrl = $cancelUrlAsString;
        return $this;
    }
    
    /**
     * Alternative drop or change file in Config/SveaConfig.php
     * Note! This fuction may change in future updates.
     * @param type $merchantId
     * @param type $secret
     * @return \HostedPayment
     */
    public function setMerchantIdBasedAuthorization($merchantId,$secret){
        $this->order->conf->merchantId = $merchantId;
        $this->order->conf->secret = $secret;
        return $this;
    }
}

?>
