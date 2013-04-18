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
            if($this->paymentMethod == PaymentMethod::INVOICE){
                $request['paymentMethod'] = "SVEAINVOICEEU_".$this->order->countryCode;
            }  elseif ($this->paymentMethod == PaymentMethod::PAYMENTPLAN) {
                $request['paymentMethod'] = "PAYMENTPLAN_".$this->order->countryCode;
            }  else {
                $request['paymentMethod'] = $this->paymentMethod;
            }
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