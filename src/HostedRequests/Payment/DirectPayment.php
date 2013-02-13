<?php

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
  Extends HostedPayment
 * Goes to PayPage and excludes all methods that are not direct payments
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package HostedRequests/Payment
 */
class DirectPayment extends HostedPayment {

    /**
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    protected function configureExcludedPaymentMethods($request) {       
         //card
        $methods[] = PaymentMethod::KORTCERT;
        $methods[] = PaymentMethod::SKRILL;
       //other      
        $methods[] = PaymentMethod::PAYPAL;
       
        //countrycheck
       if($this->order->countryCode != "SE") {
            $methods[] = PaymentMethod::DBNORDEASE;
            $methods[] = PaymentMethod::DBSEBSE;
            $methods[] = PaymentMethod::DBSEBFTGSE;
            $methods[] = PaymentMethod::DBSHBSE;         
            $methods[] = PaymentMethod::DBSWEDBANKSE;
        }
        $exclude = new ExcludePayments();
        $methods = array_merge((array)$methods, (array)$exclude->excludeInvoicesAndPaymentPlan($this->order->countryCode));

        $request['excludePaymentMethods'] = $methods;
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
