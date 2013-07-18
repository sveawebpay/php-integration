<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

class FakeHostedPayment extends HostedPayment {
    
    protected function configureExcludedPaymentMethods($request) {
        return $request;
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
