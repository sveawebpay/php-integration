<?php
use Svea\WebPay\HostedService\Helper\ExcludePayments as ExcludePayments;


/**
 * @author Kristian Grossman-Madsen
 */
class ExcludePaymentsTest extends \PHPUnit_Framework_TestCase {
   
    public function test_excludeInvoicesAndPaymentPlan_SE() {
        
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("SE");
        
        $this->assertEquals(14, count((array)$excludedPaymentMethods));
        
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::INVOICESE, $excludedPaymentMethods));
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::PAYMENTPLANSE, $excludedPaymentMethods));
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::INVOICE_SE, $excludedPaymentMethods));
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::PAYMENTPLAN_SE, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::INVOICE_DE, $excludedPaymentMethods));
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::PAYMENTPLAN_DE, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::INVOICE_DK, $excludedPaymentMethods));
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::PAYMENTPLAN_DK, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::INVOICE_FI, $excludedPaymentMethods));
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::PAYMENTPLAN_FI, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::INVOICE_NL, $excludedPaymentMethods));
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::PAYMENTPLAN_NL, $excludedPaymentMethods));
        
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::INVOICE_NO, $excludedPaymentMethods));
        $this->assertTrue(in_array(\Svea\WebPay\Constant\SystemPaymentMethod::PAYMENTPLAN_NO, $excludedPaymentMethods));
    }
}