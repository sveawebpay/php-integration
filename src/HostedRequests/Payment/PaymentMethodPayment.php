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
}

?>
