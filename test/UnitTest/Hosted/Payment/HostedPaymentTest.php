<?php

require_once '/FakeHostedPayment.php';

class HostedPaymentTest extends PHPUnit_Framework_TestCase {
    
    public function testexcludeInvoicesAndPaymentPlanSe() {
         $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("SE");
        
        $this->assertEquals(4, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEAINVOICESE, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEASPLITSE, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEAINVOICEEU_SE, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEASPLITEU_SE, $excludedPaymentMethods));
    }
    
    public function testexcludeInvoicesAndPaymentPlanDe() {
         $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("DE");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEAINVOICEEU_DE, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEASPLITEU_DE, $excludedPaymentMethods));
    }
    
    public function testexcludeInvoicesAndPaymentPlanDk() {
         $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("DK");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEAINVOICEEU_DK, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEASPLITEU_DK, $excludedPaymentMethods));
    }
    
    public function testexcludeInvoicesAndPaymentPlanFi() {
         $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("FI");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEAINVOICEEU_FI, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEASPLITEU_FI, $excludedPaymentMethods));
    }
    
    public function testexcludeInvoicesAndPaymentPlanNl() {
         $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("NL");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEAINVOICEEU_NL, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEASPLITEU_NL, $excludedPaymentMethods));
    }
    
    public function testexcludeInvoicesAndPaymentPlanNo() {
         $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("NO");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEAINVOICEEU_NO, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::SVEASPLITEU_NO, $excludedPaymentMethods));
    }
    
    public function testexcludeInvoicesAndPaymentPlanNull() {
         $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan(null);
        
        $this->assertEquals(0, count((array)$excludedPaymentMethods));
    }
}

?>
