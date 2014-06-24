<?php
use Svea\HostedService\HostedPayment as HostedPayment;
use Svea\HostedService\ExcludePayments as ExcludePayments;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

class FakeHostedPayment extends HostedPayment {
    
    /**
     * Creates a new FakeHostedPayment
     * @param FakeHostedPayment $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }
    
    
    protected function configureExcludedPaymentMethods() {
        $methods = ExcludePayments::excludeInvoicesAndPaymentPlan();
        return $methods;
    }
       
    /**
     * @param type $returnUrlAsString
     * @return \HostedPayment
     */
    public function setReturnUrl($returnUrlAsString) {
        $this->returnUrl = $returnUrlAsString;
        return $this;
    }
    
    /**
     * @param type $cancelUrlAsString
     * @return \HostedPayment
     */
    public function setCancelUrl($cancelUrlAsString) {
        $this->cancelUrl = $cancelUrlAsString;
        return $this;
    } 
}
