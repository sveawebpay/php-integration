<?php
$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * Tests ported from Java webservice/payment/CreateInvoiceOrderUnitTest.java for INTG-550
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CreateInvoiceOrderUnitTest extends PHPUnit_Framework_TestCase {

    /// tests preparing order rows price specification
    // invoice request	
    public function test_orderRows_and_Fees_specified_exvat_and_vat_using_useInvoicePayment_are_prepared_as_exvat_and_vat() {
        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setOrderDate(date('c'))
        ;
        
        $exvatRow = WebPayItem::orderRow()
                ->setAmountExVat(80.00)
                ->setVatPercent(25)
                ->setQuantity(1)
                ->setName("exvatRow")
        ;
        $exvatRow2 = WebPayItem::orderRow()
                ->setAmountExVat(80.00)
                ->setVatPercent(25)
                ->setQuantity(1)
                ->setName("exvatRow2")
        ;        
        
        $exvatInvoiceFee = WebPayItem::invoiceFee()
                ->setAmountExVat(8.00)
                ->setVatPercent(25)
                ->setName("exvatInvoiceFee")
        ;
        
        $exvatShippingFee = WebPayItem::shippingFee()
                ->setAmountExVat(16.00)
                ->setVatPercent(25)
                ->setName("exvatShippingFee")
        ;
        
        $order->addOrderRow( $exvatRow );
        $order->addOrderRow( $exvatRow2 );
        $order->addFee( $exvatInvoiceFee );
        $order->addFee( $exvatShippingFee );
        
        // all order rows
        // all shipping fee rows
        // all invoice fee rows
        $request = $order->useInvoicePayment()->prepareRequest();
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);        
         $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
         $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
         $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
         $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
         $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
         $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);   
    }
//        
//    
//    
////	@Test
////	public void test_orderRows_and_Fees_specified_exvat_and_vat_using_useInvoicePayment_are_prepared_as_exvat_and_vat() {
////		
////		CreateOrderBuilder order = WebPay.createOrder(SveaConfig.getDefaultConfig())
////			.addCustomerDetails(TestingTool.createIndividualCustomer(COUNTRYCODE.SE))
////			.setCountryCode(TestingTool.DefaultTestCountryCode)
////			.setOrderDate(new java.sql.Date(new java.util.Date().getTime()));
////		;				
////		OrderRowBuilder exvatRow = WebPayItem.orderRow()
////			.setAmountExVat(80.00)
////			.setVatPercent(25)			
////			.setQuantity(1.0)
////			.setName("exvatRow")
////		;
////		OrderRowBuilder exvatRow2 = WebPayItem.orderRow()
////			.setAmountExVat(80.00)
////			.setVatPercent(25)			
////			.setQuantity(1.0)
////			.setName("exvatRow2")
////		;		
////
////		InvoiceFeeBuilder exvatInvoiceFee = WebPayItem.invoiceFee()
////			.setAmountExVat(8.00)
////			.setVatPercent(25)
////			.setName("exvatInvoiceFee")
////		;		
////		
////		ShippingFeeBuilder exvatShippingFee = WebPayItem.shippingFee()
////			.setAmountExVat(16.00)
////			.setVatPercent(25)
////			.setName("exvatShippingFee")
////		;	
////		
////		order.addOrderRow(exvatRow);
////		order.addOrderRow(exvatRow2);
////		order.addFee(exvatInvoiceFee);
////		order.addFee(exvatShippingFee);
////	
////		// all order rows
////		// all shipping fee rows
////		// all invoice fee rows		
////		SveaRequest<SveaCreateOrder> soapRequest = order.useInvoicePayment().prepareRequest();
////		assertEquals( (Object)80.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(0).PricePerUnit  ); // cast avoids deprecation
////		assertEquals( (Object)25.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(0).VatPercent  ); // cast avoids deprecation		
////		assertEquals( false, soapRequest.request.CreateOrderInformation.OrderRows.get(0).PriceIncludingVat );
////		assertEquals( (Object)80.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(1).PricePerUnit  ); // cast avoids deprecation
////		assertEquals( (Object)25.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(1).VatPercent  ); // cast avoids deprecation		
////		assertEquals( false, soapRequest.request.CreateOrderInformation.OrderRows.get(1).PriceIncludingVat );
////		assertEquals( (Object)16.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(2).PricePerUnit  ); // cast avoids deprecation
////		assertEquals( (Object)25.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(2).VatPercent  ); // cast avoids deprecation		
////		assertEquals( false, soapRequest.request.CreateOrderInformation.OrderRows.get(2).PriceIncludingVat );
////		assertEquals( (Object)8.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(3).PricePerUnit  ); // cast avoids deprecation
////		assertEquals( (Object)25.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(3).VatPercent  ); // cast avoids deprecation		
////		assertEquals( false, soapRequest.request.CreateOrderInformation.OrderRows.get(3).PriceIncludingVat );
////				
////	}
////
////	@Test
////	public void test_orderRows_and_Fees_specified_incvat_and_vat_using_useInvoicePayment_are_prepared_as_incvat_and_vat() {
////		
////		CreateOrderBuilder order = WebPay.createOrder(SveaConfig.getDefaultConfig())
////			.addCustomerDetails(TestingTool.createIndividualCustomer(COUNTRYCODE.SE))
////			.setCountryCode(TestingTool.DefaultTestCountryCode)
////			.setOrderDate(new java.sql.Date(new java.util.Date().getTime()));
////		;				
////		OrderRowBuilder incvatRow = WebPayItem.orderRow()
////			.setAmountIncVat(100.00)
////			.setVatPercent(25)			
////			.setQuantity(1.0)
////			.setName("incvatRow")
////		;
////		OrderRowBuilder incvatRow2 = WebPayItem.orderRow()
////			.setAmountIncVat(100.00)
////			.setVatPercent(25)			
////			.setQuantity(1.0)
////			.setName("incvatRow2")
////		;		
////		
////		InvoiceFeeBuilder incvatInvoiceFee = WebPayItem.invoiceFee()
////			.setAmountIncVat(10.00)
////			.setVatPercent(25)
////			.setName("incvatInvoiceFee")
////		;		
////		
////		ShippingFeeBuilder incvatShippingFee = WebPayItem.shippingFee()
////			.setAmountIncVat(20.00)
////			.setVatPercent(25)
////			.setName("incvatShippingFee")
////		;	
////		
////		order.addOrderRow(incvatRow);
////		order.addOrderRow(incvatRow2);
////		order.addFee(incvatInvoiceFee);
////		order.addFee(incvatShippingFee);
////				
////		SveaRequest<SveaCreateOrder> soapRequest = order.useInvoicePayment().prepareRequest();
////		assertEquals( (Object)100.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(0).PricePerUnit  ); // cast avoids deprecation
////		assertEquals( (Object)25.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(0).VatPercent  ); // cast avoids deprecation		
////		assertEquals( true, soapRequest.request.CreateOrderInformation.OrderRows.get(0).PriceIncludingVat );
////		assertEquals( (Object)100.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(1).PricePerUnit  ); // cast avoids deprecation
////		assertEquals( (Object)25.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(1).VatPercent  ); // cast avoids deprecation		
////		assertEquals( true, soapRequest.request.CreateOrderInformation.OrderRows.get(0).PriceIncludingVat );
////		assertEquals( (Object)20.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(2).PricePerUnit  ); // cast avoids deprecation
////		assertEquals( (Object)25.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(2).VatPercent  ); // cast avoids deprecation		
////		assertEquals( true, soapRequest.request.CreateOrderInformation.OrderRows.get(0).PriceIncludingVat );
////		assertEquals( (Object)10.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(3).PricePerUnit  ); // cast avoids deprecation
////		assertEquals( (Object)25.0, (Object)soapRequest.request.CreateOrderInformation.OrderRows.get(3).VatPercent  ); // cast avoids deprecation		
////		assertEquals( true, soapRequest.request.CreateOrderInformation.OrderRows.get(0).PriceIncludingVat );
////
////	}
////
////	@Test
////	public void test_orderRows_and_Fees_specified_incvat_and_exvat_using_useInvoicePayment_are_prepared_as_incvat_and_vat() {    
////    
//      
//    /// from php -------------------------------------
//    
//    /// relative discount examples:
//    // single order rows vat rate
//    public function test_relativeDiscount_amount() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setOrderDate(date('c'))
//        ;
//        
//        $exvatrow = WebPayItem::orderRow()
//                ->setAmountExVat(80.00)
//                ->setVatPercent(25)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//        ;
//        $exvatrow2 = WebPayItem::orderRow()
//                ->setAmountExVat(80.00)
//                ->setVatPercent(25)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//        ;        
//        
//        $exvatInvoiceFee = WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(25)
//                ->setName("exvatInvoiceFee")
//        ;
//        
//        $exvatShippingFee = WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(25)
//                ->setName("exvatShippingFee")
//        ;
//        
//        $order->addOrderRow( $exvatRow );
//        $order->addOrderRow( $exvatRow2 );
//        $order->addFee( $exvatInvoiceFee );
//        $order->addFee( $exvatShippingFee );
//        
//        // all order rows
//        // all shipping fee rows
//        // all invoice fee rows
//        $request = $order->useInvoicePayment()->prepareRequest();
//        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);        
//         $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
//         $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//         $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//         $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
//        // all discount rows
//        // expected: 10% off orderRow rows: 2x 80.00 @25% => -16.00 @25% discount
//        $this->assertEquals(-16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//    }
//
//    // relative discount on multiple order row defined exvat/vatpercent vat rates 
//    public function test_relativeDiscount_amount_multiple_vat_rates_defined_exvat_creates_discount_rows_using_exvat_and_vatpercent() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::relativeDiscount()
//                ->setDiscountPercent(10.0)
//                ->setDiscountId("TenPercentOff")
//                ->setName("relativeDiscount")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        // all discount rows
//        // expected: 10% off orderRow rows: 1x60.00 @20%, 1x30@10% => split proportionally across order row (only) vat rate: -6.0 @20%, -3.0 @10%
//        $this->assertEquals(-6.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);        
//        $this->assertEquals(-3.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
//
//    }
//        
//    // relative discount -- created discount rows should use incvat + vatpercent
//    // relative discount on multiple order row defined exvat/vatpercent vat rates
//    public function test_relativeDiscount_amount_multiple_vat_rates_defined_incvat_creates_discount_rows_using_incvat_and_vatpercent() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountIncVat(72.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountIncVat(33.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountIncVat(8.80)
//                ->setVatPercent(10)
//                ->setName("incvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountIncVat(17.60)
//                ->setVatPercent(10)
//                ->setName("incvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::relativeDiscount()
//                ->setDiscountPercent(10.0)
//                ->setDiscountId("TenPercentOff")
//                ->setName("relativeDiscount")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(72.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);        
//        $this->assertEquals(33.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);        
//        // all shipping fee rows
//        $this->assertEquals(17.60, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);        
//        // all invoice fee rows
//        $this->assertEquals(8.80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);        
//        // all discount rows
//        // expected: 10% off orderRow rows: 1x60.00 @20%, 1x30@10% => split proportionally across order row (only) vat rate: -6.0 @20%, -3.0 @10%
//        $this->assertEquals(-7.20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);        
//        $this->assertEquals(-3.30, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
//        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);        
//    }
//    
//    public function testOrderAndFixedDiscountSetWithMixedVat(){
//        $config = Svea\SveaConfig::getDefaultConfig();
//        $order = WebPay::createOrder($config)
//                    ->addOrderRow(
//                            WebPayItem::orderRow()
//                                ->setAmountIncVat(123.9876)
//                                ->setVatPercent(24)
//                                ->setQuantity(1)
//                            )
//                    ->addDiscount(WebPayItem::fixedDiscount()
//                            ->setAmountExVat(9.999)
//                            )
//                    ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
//                    ->setCountryCode("SE")
//                    ->setOrderDate("2012-12-12")
//        ;
//
//        $request = $order->useInvoicePayment()->prepareRequest();
//
//        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
//
//        // 9.999 *1.24 = 12.39876
//        $this->assertEquals(-9.999, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
//
//        // check that service accepts order
//        $response = $order->useInvoicePayment()->doRequest();
//        $this->assertEquals( true, $response->accepted );          
//    }
//
//    /// fixed discount examples:
//    // single order rows vat rate
//    public function test_fixedDiscount_amount_with_set_exvat_vat_rate() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountExVat(10.0)
//                ->setVatPercent(10)
//                ->setDiscountId("ElevenCrownsOff")
//                ->setName("fixedDiscount: 10 @10% => 11kr")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
//        // all discount rows
//        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
//        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
//    }
//    public function test_fixedDiscount_amount_with_set_incvat_vat_rate() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountIncVat(11.0)
//                ->setVatPercent(10)
//                ->setDiscountId("ElevenCrownsOff")
//                ->setName("fixedDiscount: 10 @10% => 11kr")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        // all discount rows
//        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
//        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//    }
//
//    public function test_fixedDiscount_amount_with_calculated_vat_rate_exvat() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountExVat(10.0)
//                ->setDiscountId("TenCrownsOff")
//                ->setName("fixedDiscount: 10 off exvat")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        // all discount rows
//        // expected: fixedDiscount: 10 off exvat, order row amount are 66% at 20% vat, 33% at 10% vat => 6.67 @20% and 3.33 @10%
//        $this->assertEquals(-6.6666666666667, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(-3.3333333333333, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
//    }
//
//    public function test_fixedDiscount_amount_with_calculated_vat_rate_incvat() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountIncVat(10.0)
//                ->setDiscountId("TenCrownsOff")
//                ->setName("fixedDiscount: 10 off incvat")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        // all discount rows
//        // expected: fixedDiscount: 10 off incvat, order row amount are 66% at 20% vat, 33% at 10% vat
//        // 1.2*0.66x + 1.1*0.33x = 10 => x = 8.6580 => 5.7143 @20% and 2.8571 @10% =
//        $this->assertEquals(-5.7142857142857, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(-2.8571428571429, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
//    }
//
//    // fixed discount -- created discount rows should use incvat + vatpercent
//    /// fixed discount examples:
//    // single order rows vat rate
//    public function test_fixedDiscount_amount_with_incvat_vat_rate_creates_discount_rows_using_incvat_and_vatpercent() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountIncVat(72.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("incvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountIncVat(33.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("incvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountIncVat(8.80)
//                ->setVatPercent(10)
//                ->setName("incvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountIncVat(17.60)
//                ->setVatPercent(10)
//                ->setName("incvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountExVat(10.0)
//                ->setVatPercent(10)
//                ->setDiscountId("ElevenCrownsOff")
//                ->setName("fixedDiscount: 10 @10% => 11kr")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
//        // all discount rows
//        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
//        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
//        
//        // check that service accepts order
//        $response = $order->useInvoicePayment()->doRequest();
//        $this->assertEquals( true, $response->accepted );
//    }
//
//    // single order rows vat rate
//    public function test_fixedDiscount_amount_with_exvat_vat_rate_creates_discount_rows_using_incvat_and_vatpercent() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountExVat(10.0)
//                ->setVatPercent(10)
//                ->setDiscountId("ElevenCrownsOff")
//                ->setName("fixedDiscount: 10 @10% => 11kr")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
//
//        // all discount rows
//        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
//        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
//        
//        $response = $order->useInvoicePayment()->doRequest();
//        $this->assertEquals( true, $response->accepted );
//    }
//
//    public function test_fixedDiscount_amount_with_set_incvat_vat_rate_creates_discount_rows_using_incvat_and_vatpercent() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountIncVat(11.0)
//                ->setVatPercent(10)
//                ->setDiscountId("ElevenCrownsOff")
//                ->setName("fixedDiscount: 10 @10% => 11kr")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        // all discount rows
//        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
//        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//    }
//
//    public function test_fixedDiscount_amount_with_calculated_vat_rate_exvat_creates_discount_rows_using_incvat_and_vatpercent() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountExVat(10.0)
//                ->setDiscountId("TenCrownsOff")
//                ->setName("fixedDiscount: 10 off exvat")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        // all discount rows
//        // expected: fixedDiscount: 10 off exvat, order row amount are 66% at 20% vat, 33% at 10% vat => 6.67 @20% and 3.33 @10%
//        $this->assertEquals(-6.6666666666667, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(-3.3333333333333, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
//    }
//
//    public function test_fixedDiscount_amount_with_calculated_vat_rate_incvat_creates_discount_rows_using_incvat_and_vatpercent() {
//        $order = WebPay::createOrder(Svea\SveaConfig::getDefaultConfig())
//            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
//            ->setCountryCode("SE")
//            ->setCustomerReference("33")
//            ->setOrderDate("2012-12-12")
//            ->setCurrency("SEK")
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(60.00)
//                ->setVatPercent(20)
//                ->setQuantity(1)
//                ->setName("exvatRow")
//            )
//            ->addOrderRow(
//                WebPayItem::orderRow()
//                ->setAmountExVat(30.00)
//                ->setVatPercent(10)
//                ->setQuantity(1)
//                ->setName("exvatRow2")
//            )
//            ->addFee(
//                WebPayItem::invoiceFee()
//                ->setAmountExVat(8.00)
//                ->setVatPercent(10)
//                ->setName("exvatInvoiceFee")
//            )
//            ->addFee(
//                WebPayItem::shippingFee()
//                ->setAmountExVat(16.00)
//                ->setVatPercent(10)
//                ->setName("exvatShippingFee")
//            )
//            ->addDiscount(
//                WebPayItem::fixedDiscount()
//                ->setAmountIncVat(10.0)
//                ->setDiscountId("TenCrownsOff")
//                ->setName("fixedDiscount: 10 off incvat")
//            )
//        ;
//        $request = $order->useInvoicePayment()->prepareRequest();
//        // all order rows
//        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
//        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
//        // all shipping fee rows
//        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
//        // all invoice fee rows
//        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
//        // all discount rows
//        // expected: fixedDiscount: 10 off incvat, order row amount are 66% at 20% vat, 33% at 10% vat
//        // 1.2*0.66x + 1.1*0.33x = 10 => x = 8.6580 => 5.7143 @20% and 2.8571 @10% =
//        $this->assertEquals(-5.7142857142857, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
//        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
//        $this->assertEquals(-2.8571428571429, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
//        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
//    }
//    
//    // See file FixedDiscountRowsTest for specification of FixedDiscount row behaviour.
}