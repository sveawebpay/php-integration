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

    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_ItemsDefinedWithIncVatAndVatPercent() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("testCalculateRequestValues_CorrectTotalAmountFromMultipleItems")
                ->setDescription("testCalculateRequestValues_CorrectTotalAmountFromMultipleItems")
                ->setAmountIncVat(87.4875)    // if low precision here, i.e. 87.49, we'll get a cumulative rounding error
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

    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_ItemsDefinedWithExVatAndIncVat() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("testCalculateRequestValues_CorrectTotalAmountFromMultipleItems")
                ->setDescription("testCalculateRequestValues_CorrectTotalAmountFromMultipleItems")
                ->setAmountExVat(69.99)
                ->setAmountIncVat(87.4875)   // if low precision here, i.e. 87.49, we'll get a cumulative rounding error
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
    
    
//    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_ItemsDefinedWithIncVatAndExVat() {}
    
    // calculated fixed discount vat rate, single vat rate in order
    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_WithFixedDiscountIncVatOnly() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(30)
            )
            ->addDiscount(\WebPayItem::fixedDiscount()
                ->setAmountIncVat(10.00)
            );
     
        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        $this->assertEquals(261462, $request['amount']);    // 262462,5 rounded half-to-even    - 1000 discount
        $this->assertEquals(52292, $request['totalVat']);   // 52492,5 rounded half-to-even     -  200 discount (= 10/2624,62*524,92)
    }  
    
    // explicit fixed discount vat rate, , single vat rate in order 
    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_WithFixedDiscountIncVatAndVatPercent() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(30)
            )
            ->addDiscount(\WebPayItem::fixedDiscount()
                ->setAmountIncVat(12.50)
                ->setVatPercent(25)
            );
     
        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        $this->assertEquals(261212, $request['amount']);    // 262462,5 rounded half-to-even    - 1250 discount
        $this->assertEquals(52242, $request['totalVat']);   // 52492,5 rounded half-to-even     - 250 discount 
    }
    
    // calculated fixed discount vat rate, multiple vat rate in order
    public function testCalculateRequestValues_CorrectTotalAmount_WithFixedDiscountIncVatOnly_WithDifferentVatRatesPresent() {
        $order = \WebPay::createOrder();
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
            )
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
            )
            ->addDiscount(\WebPayItem::fixedDiscount()
                ->setAmountIncVat(100.00)
            );

        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        // 100*250/356 = 70.22 incl. 25% vat => 14.04 vat as amount 
        // 100*106/356 = 29.78 incl. 6% vat => 1.69 vat as amount 
        // matches 15,73 discount (= 100/356 *56) discount
        $this->assertEquals(25600, $request['amount']);    // 35600    - 10000 discount
        $this->assertEquals(4027, $request['totalVat']);   //  5600    -  1573 discount (= 10000/35600 *5600) discount
    }
    
    // explicit fixed discount vat rate, multiple vat rate in order
        public function testCalculateRequestValues_CorrectTotalAmount_WithFixedDiscountIncVatAndVatPercent_WithDifferentVatRatesPresent() {
        $order = \WebPay::createOrder();
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
            )
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
            )
            ->addDiscount(\WebPayItem::fixedDiscount()
                ->setAmountIncVat(125.00)
                ->setVatPercent(25)
            );

        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        $this->assertEquals(23100, $request['amount']);    // 35600    - 12500 discount
        $this->assertEquals(3100, $request['totalVat']);   //  5600    -  2500 discount
    }     
  
    public function testCalculateRequestValues_CorrectTotalAmount_WithFixedDiscountExVatAndVatPercent_WithDifferentVatRatesPresent() {
        $order = \WebPay::createOrder();
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
            )
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
            )
            ->addDiscount(\WebPayItem::fixedDiscount()
                ->setAmountExVat(100.00)
                ->setVatPercent(0)
            );

        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        $this->assertEquals(25600, $request['amount']);    // 35600    - 10000 discount
        $this->assertEquals(5600, $request['totalVat']);   //  5600    -     0 discount
    }     
    
    public function testCalculateRequestValues_CorrectTotalAmount_WithFixedDiscountExVatAndIncVat_WithDifferentVatRatesPresent() {
        $order = \WebPay::createOrder();
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
            )
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
            )
            ->addDiscount(\WebPayItem::fixedDiscount()
                ->setAmountExVat(80.00)
                ->setAmountIncVat(100.00)
            );

        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        $this->assertEquals(25600, $request['amount']);    // 35600    - 10000 discount
        $this->assertEquals(3600, $request['totalVat']);   //  5600    -  2000 discount
    }     

    // calculated relative discount vat rate, single vat rate in order
    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_WithRelativeDiscount_WithDifferentVatRatesPresent() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(30)
            )
            ->addDiscount(\WebPayItem::relativeDiscount()
                ->setDiscountPercent(25.00)
            );
     
        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        $this->assertEquals(196846, $request['amount']);    // 262462,5 rounded half-to-even    - 65615,625 discount (25%) unrounded
        $this->assertEquals(39369, $request['totalVat']);   //  52492,5 rounded half-to-even    - 13123,125 discount (25%) unrounded
    }
    
    // calculated relative discount vat rate, single vat rate in order
    public function testCalculateRequestValues_CorrectTotalAmountFromMultipleItems_WithRelativeDiscount_WithDifferentVatRatesPresent2() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(1)
            )
            ->addDiscount(\WebPayItem::relativeDiscount()
                ->setDiscountPercent(25.00)
            );
     
        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        $this->assertEquals(6562, $request['amount']);              // 8748,75 rounded half-to-even - 2187,18 discount
        $this->assertEquals(1312, $request['totalVat']);            // 1749,75 rounded half-to-even - 437,5 discount (1750*.25) 
    }  

    // calculated relative discount vat rate, multiple vat rate in order
    public function testCalculateRequestValues_CorrectTotalAmount_WithRelativeDiscount_WithDifferentVatRatesPresent() {
        $order = \WebPay::createOrder();
        $order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
            )
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
            )
            ->addDiscount(\WebPayItem::relativeDiscount()
                ->setDiscountPercent(25)
            );

        // follows HostedPayment calculateRequestValues() outline:
        $formatter = new HostedRowFormatter();
        $request = array();
        
        $request['rows'] = $formatter->formatRows($order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        
        // 5000*.25 = 1250
        // 600*.25 = 150  
        // matches 1400 discount
        $this->assertEquals(26700, $request['amount']);    // 35600    - 8900 discount
        $this->assertEquals(4200, $request['totalVat']);   //  5600    - 1400 discount (= 10000/35600 *5600) discount
    }

}