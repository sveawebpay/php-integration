<?php

require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 Extends HostedPayment
 * Goes to PayPage
 * Possibilitys to customize what payment methods to be shown on paypage
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package HostedRequests/Payment
 */
class PayPagePayment extends HostedPayment {

    public $paymentMethod;
    public $excludedPaymentMethods;

    /**
     * 
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    protected function configureExcludedPaymentMethods($request) {
        if (isset($this->paymentMethod)) {
            $request['paymentMethod'] = $this->paymentMethod;
        }

        if (isset($this->excludedPaymentMethods)) {
            $request['excludePaymentMethods'] = $this->excludedPaymentMethods;
        }

        return $request;
    }

    /** Moved
     * Set specific paymentmethod
     * @param type $paymentMethod ex. "DBSEBSE"
     * @return \PayPagePayment
     
    public function setPaymentMethod($paymentMethod) {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
     * 
     */

    /** 
     * Exclude specific payment methods.
     * @params type Paymentmethod $paymentMethod ex. PaymentMethod::DBSEBSE,Paymentmethod::SVEAINVOICE_SE
     * Flexible number of params                              
     * @return \PayPagePayment
     */     
    public function excludePaymentMethods() {
        $this->excludedPaymentMethods = func_get_args();
        return $this;
    }
     /** 
     * 
     * @return \PayPagePayment
     */
    public function includePaymentMethods() {
        //get parameters sent no matter how many
        $include = func_get_args();
        //exclude all functions
        $this->excludedPaymentMethods[] = PaymentMethod::KORTCERT;
        $this->excludedPaymentMethods[] = PaymentMethod::SKRILL;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEAINVOICESE;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEASPLITSE;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEAINVOICEEU_SE;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEASPLITEU_SE;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEAINVOICEEU_DE;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEASPLITEU_DE;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEAINVOICEEU_DK;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEASPLITEU_DK;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEAINVOICEEU_FI;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEASPLITEU_FI;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEAINVOICEEU_NL;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEASPLITEU_NL;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEAINVOICEEU_NO;
        $this->excludedPaymentMethods[] = PaymentMethod::SVEASPLITEU_NO;
        $this->excludedPaymentMethods[] = PaymentMethod::PAYPAL;        
        $this->excludedPaymentMethods[] = PaymentMethod::DBSWEDBANKSE;
        $this->excludedPaymentMethods[] = PaymentMethod::DBSHBSE;
        $this->excludedPaymentMethods[] = PaymentMethod::DBSEBFTGSE;
        $this->excludedPaymentMethods[] = PaymentMethod::DBSEBSE;
        $this->excludedPaymentMethods[] = PaymentMethod::DBNORDEASE;
        
        //remove the include functions from the excludedPaymentMethods
        foreach ($include as $key => $value) {
            $trimmed = trim($value);
            $cleanValue = strtoupper($trimmed);
            if ($this->excludedPaymentMethods[$key] == $cleanValue)
                unset($this->excludedPaymentMethods[$key]);
        }
        return $this;
    }

    /** Deprecated
     * Exclude all cardpayments
     * @return \PayPagePayment
     */     
    public function excludeCardPaymentMethods() {
        $this->excludedPaymentMethods[] = PaymentMethod::KORTCERT;
        $this->excludedPaymentMethods[] = PaymentMethod::SKRILL;
        return $this;
    }
    

    /** Deprecated
     * Exclude all direct bank payments
     * @return \PayPagePayment
     * 
     */     
    public function excludeDirectPaymentMethods() {
        $this->excludedPaymentMethods[] = PaymentMethod::DBNORDEASE;
        $this->excludedPaymentMethods[] = PaymentMethod::DBSEBSE;
        $this->excludedPaymentMethods[] = PaymentMethod::DBSEBFTGSE;
        $this->excludedPaymentMethods[] = PaymentMethod::DBSHBSE;
        $this->excludedPaymentMethods[] = PaymentMethod::DBSWEDBANKSE;
        return $this;
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
    
    public function setPayPageLanguage($languageCodeAsISO639){
        switch ($languageCodeAsISO639) {
            case "sv":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "fi":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "es":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "en":
                $this->langCode = $languageCodeAsISO639;

                break;

            default:
                 $this->langCode = "en";
                break;
        }
        
        return $this;
    }
}

?>
