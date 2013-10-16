<?php
namespace Svea;

require_once 'FakeHostedPayment.php';

class HostedPaymentTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->assertTrue(in_array(\PaymentMethod::INVOICE_DE, $excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::PAYMENTPLAN_DE, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanDk() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("DK");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::INVOICE_DK, $excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::PAYMENTPLAN_DK, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanFi() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("FI");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::INVOICE_FI, $excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::PAYMENTPLAN_FI, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanNl() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("NL");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::INVOICE_NL, $excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::PAYMENTPLAN_NL, $excludedPaymentMethods));
    }
    
    public function t_estexcludeInvoicesAndPaymentPlanNo() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan("NO");
        
        $this->assertEquals(2, count((array)$excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::INVOICE_NO, $excludedPaymentMethods));
        $this->assertTrue(in_array(\PaymentMethod::PAYMENTPLAN_NO, $excludedPaymentMethods));
    }
    
    public function te_stexcludeInvoicesAndPaymentPlanNull() {
        $exclude = new ExcludePayments();
        $excludedPaymentMethods = $exclude->excludeInvoicesAndPaymentPlan(null);
        
        $this->assertEquals(0, count((array)$excludedPaymentMethods));
    }
    
    /**
     * 30*69.99*1.25 = 2624.625 => 2624.62 w/Bankers rounding (half-to-even)
     * 
     * problem, sums to 2624.7, in xml request, i.e. calculates 30* round( (69.99*1.25), 2) :( 
     * 
     */
    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_ItemsDefinedWithExVatAndVatPercent() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("testCalculateRequestValues_CorrectTotalAmountFromMultipleItems")
                ->setDescription("testCalculateRequestValues_CorrectTotalAmountFromMultipleItems")
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(30)
                ->setUnit("st")
            );
     
        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        $this->assertEquals(262462, $request['amount']);    // 262462,5 rounded half-to-even
        $this->assertEquals(52492, $request['totalVat']);   // 52492,5 rounded half-to-even
    }  

    // TODO 
//    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_ItemsDefinedWithIncVatAndVatPercent() {}
//    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_ItemsDefinedWithIncVatAndExVat() {}
}