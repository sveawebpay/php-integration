<?php

require_once 'FakeHostedPayment.php';

class HostedPaymentTest extends PHPUnit_Framework_TestCase {
    
    public function testexcludeInvoicesAndPaymentPlanSe() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("SE");
        $this->assertEquals(14, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::INVOICESE, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLANSE, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_SE, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_SE, $excludedPaymentMethods));
        
         $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_DE, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_DE, $excludedPaymentMethods));
        
         $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_DK, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_DK, $excludedPaymentMethods));
        
         $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_FI, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_FI, $excludedPaymentMethods));
        
         $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_NL, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_NL, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(SystemPaymentMethod::INVOICE_NO, $excludedPaymentMethods));
        $this->assertTrue(in_array(SystemPaymentMethod::PAYMENTPLAN_NO, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanDe() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("DE");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::INVOICE_DE, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::PAYMENTPLAN_DE, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanDk() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("DK");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::INVOICE_DK, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::PAYMENTPLAN_DK, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanFi() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("FI");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::INVOICE_FI, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::PAYMENTPLAN_FI, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanNl() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("NL");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::INVOICE_NL, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::PAYMENTPLAN_NL, $excludedPaymentMethods));
    }
    
    public function t_estexcludeInvoicesAndPaymentPlanNo() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("NO");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::INVOICE_NO, $excludedPaymentMethods));
        $this->assertTrue(in_array(PaymentMethod::PAYMENTPLAN_NO, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanNull() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan(null);
        
        $this->assertEquals(0, count((array)$excludedPaymentMethods));
    }
}

?>
