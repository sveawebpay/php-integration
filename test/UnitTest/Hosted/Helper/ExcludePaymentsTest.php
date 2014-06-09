<?php
use Svea\HostedService\ExcludePayments as ExcludePayments;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen
 */
class ExcludePaymentsTest extends \PHPUnit_Framework_TestCase {
   
    public function test_excludeInvoicesAndPaymentPlan_SE() {
        
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("SE");
        
        $this->assertEquals(14, count((array)$excludedPaymentMethods));
        
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::INVOICESE, $excludedPaymentMethods));
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::PAYMENTPLANSE, $excludedPaymentMethods));
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::INVOICE_SE, $excludedPaymentMethods));
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::PAYMENTPLAN_SE, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::INVOICE_DE, $excludedPaymentMethods));
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::PAYMENTPLAN_DE, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::INVOICE_DK, $excludedPaymentMethods));
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::PAYMENTPLAN_DK, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::INVOICE_FI, $excludedPaymentMethods));
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::PAYMENTPLAN_FI, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::INVOICE_NL, $excludedPaymentMethods));
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::PAYMENTPLAN_NL, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::INVOICE_NO, $excludedPaymentMethods));
        $this->assertTrue(in_array(Svea\SystemPaymentMethod::PAYMENTPLAN_NO, $excludedPaymentMethods));
    }
}