<?php

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 * Extends HostedPayment
 * Goes to PayPage and excludes all methods that are not cardpayments
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package HostedRequests/Payment
 */
class CardPayment extends HostedPayment {

    /**
     * 
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    protected function configureExcludedPaymentMethods($request) {       
        //directbanks
        $methods[] = PaymentMethod::DBAKTIAFI;
        $methods[] = PaymentMethod::DBALANDSBANKENFI;
        $methods[] = PaymentMethod::DBDANSKEBANKSE;
        $methods[] = PaymentMethod::DBNORDEASE;
        $methods[] = PaymentMethod::DBNORDEAFI;
        $methods[] = PaymentMethod::DBNORDEAEE;
        $methods[] = PaymentMethod::DBPOHJOLAFI;
        $methods[] = PaymentMethod::DBSAMPOFI;
        $methods[] = PaymentMethod::DBSEBSE;
        $methods[] = PaymentMethod::DBSEBFTGSE;
        $methods[] = PaymentMethod::DBSHBFI;
        $methods[] = PaymentMethod::DBSHBSE;
        $methods[] = PaymentMethod::DBSPANKKIFI;
        $methods[] = PaymentMethod::DBSWEDBANKSE;
        $methods[] = PaymentMethod::DBTAPIOLAFI;
        //other
        $methods[] = PaymentMethod::BANKAXESS;
        $methods[] = PaymentMethod::MICRODEB;
        $methods[] = PaymentMethod::PAYGROUND;
        $methods[] = PaymentMethod::PAYPAL;
        //countrycheck
        switch ($this->order->countryCode) {
            case "SE":
            $methods[] = PaymentMethod::SKRILL;

                break;

            default:
                break;
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
