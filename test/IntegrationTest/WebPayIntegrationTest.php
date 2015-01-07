<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayIntegrationTest extends PHPUnit_Framework_TestCase {

    /// WebPay::createOrder()
    // web service eu: invoice
    public function test_createOrder_useInvoicePayment_returns_InvoicePayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        // we should set attributes here if real request
        $request = $createOrder->useInvoicePayment();
        $this->assertInstanceOf("Svea\WebService\InvoicePayment", $request);
    }
    
    // web service eu: paymentplan
    public function test_createOrder_usePaymentPlanPayment_returns_PaymentPlanPayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() );
        $this->assertInstanceOf("Svea\WebService\PaymentPlanPayment", $request);
    }    
    
    // paypage: cardonly
    public function test_createOrder_usePayPageCardOnly_returns_CardPayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePayPageCardOnly();
        $this->assertInstanceOf("Svea\HostedService\CardPayment", $request);
    }   

    // paypage: directbankonly
    public function test_createOrder_usePayPageDirectBankOnly_returns_DirectPayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePayPageDirectBankOnly();
        $this->assertInstanceOf("Svea\HostedService\DirectPayment", $request);
    }       
    
    // bypass paypage: usepaymentmethod
    public function test_createOrder_usePaymentMethod_returns_PaymentMethodPayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePaymentMethod("mocked_paymentMethod");
        $this->assertInstanceOf("Svea\HostedService\PaymentMethodPayment", $request);
    }  

    // usepaymentmethod KORTCERT with recurring payment
    // TODO add recur example when implementing webdriver integration tests
    
    // paypage
    public function test_createOrder_usePayPage_returns_PayPagePayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePayPage();
        $this->assertInstanceOf("Svea\HostedService\PayPagePayment", $request);
    }      
        
    /// WebPay::deliverOrder()
    // invoice
    public function test_deliverOrder_deliverInvoiceOrder_without_order_rows_goes_against_adminservice_DeliverOrders() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $deliverOrder->deliverInvoiceOrder();        
        $this->assertInstanceOf( "Svea\AdminService\DeliverOrdersRequest", $request ); 
        $this->assertEquals("Invoice", $request->orderBuilder->orderType);    
    }    
    // TODO actual integration test
    
    public function test_deliverOrder_deliverInvoiceOrder_with_order_rows_goes_against_DeliverOrderEU() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $deliverOrder->addOrderRow( WebPayItem::orderRow() );
        $request = $deliverOrder->deliverInvoiceOrder();     
        $this->assertInstanceOf( "Svea\WebService\DeliverInvoice", $request );         // WebService\DeliverInvoice => soap call DeliverOrderEU  
    }
    // DeliverOrderEU - deliver order and credit order
    public function test_deliverOrder_deliverInvoiceOrder_with_order_rows_first_deliver_then_credit_order() {
        // create order using order row specified with ->setName() and ->setDescription
        $specifiedOrderRow = WebPayItem::orderRow()
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
        ;   
             
        $order = TestUtil::createOrderWithoutOrderRows()
            ->addOrderRow($specifiedOrderRow);
        
        $createOrderResponse = $order->useInvoicePayment()->doRequest();
        
        //print_r( $createOrderResponse );
        $this->assertInstanceOf ("Svea\WebService\CreateOrderResponse", $createOrderResponse );
        $this->assertTrue( $createOrderResponse->accepted );        

        $createdOrderId = $createOrderResponse->sveaOrderId;        

        // deliver order
        $deliverOrderBuilder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId( $createdOrderId )  
            ->setCountryCode("SE")
            ->setInvoiceDistributionType( DistributionType::POST )   
            ->addOrderRow($specifiedOrderRow)
        ;
        $deliverOrderResponse = $deliverOrderBuilder->deliverInvoiceOrder()->doRequest();        
        
        //print_r( $deliverOrderResponse );
        $this->assertInstanceOf ("Svea\WebService\DeliverOrderResult", $deliverOrderResponse );
        $this->assertTrue( $createOrderResponse->accepted );        
        
        $deliveredInvoiceId = $deliverOrderResponse->invoiceId;
        
        // credit order
        $creditOrderBuilder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId( $createdOrderId )  
            ->setCountryCode("SE")
            ->setInvoiceDistributionType( DistributionType::POST )   
            ->addOrderRow($specifiedOrderRow)
            ->setCreditInvoice($deliveredInvoiceId)   
        ;
        $creditOrderResponse = $creditOrderBuilder->deliverInvoiceOrder()->doRequest();

        //print_r( $creditOrderResponse );
        $this->assertInstanceOf ("Svea\WebService\DeliverOrderResult", $deliverOrderResponse );
        $this->assertTrue( $creditOrderResponse->accepted );        
    }
    
    // paymentplan
    public function test_deliverOrder_deliverPaymentPlanOrder_without_order_rows_goes_against_DeliverOrderEU() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $deliverOrder->deliverPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\WebService\DeliverPaymentPlan", $request );
        $this->assertEquals("PaymentPlan", $request->orderBuilder->orderType); 
    }
    // TODO actual integration test
    
    public function test_deliverOrder_deliverPaymentPlanOrder_with_order_rows_goes_against_DeliverOrderEU() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $deliverOrder->addOrderRow( WebPayItem::orderRow() );   // order rows are ignored by DeliverOrderEU, can't partially deliver PaymentPlan
        $request = $deliverOrder->deliverPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\WebService\DeliverPaymentPlan", $request );      
    }
    // TODO actual integration test

    // card
    public function test_deliverOrder_deliverCardOrder_returns_ConfirmTransaction() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $deliverOrder->addOrderRow( WebPayItem::orderRow() );
        $request = $deliverOrder->deliverCardOrder();        
        $this->assertInstanceOf( "Svea\HostedService\ConfirmTransaction", $request );
    }
    // TODO actual integration test
    
    /// WebPay::getAddresses()
    public function test_getAddresses_returns_GetAddresses() {
        $request = WebPay::getAddresses( Svea\SveaConfig::getDefaultConfig() );
        $this->assertInstanceOf( "Svea\WebService\GetAddresses", $request );
    }    
 
    /// WebPay::getPaymentPlanParams()
    public function test_getPaymentPlanParams_returns_GetPaymentPlanParams() {
        $request = WebPay::getPaymentPlanParams( Svea\SveaConfig::getDefaultConfig() );
        $this->assertInstanceOf( "Svea\WebService\GetPaymentPlanParams", $request );
    }    
    
    /// WebPay::listPaymentMethods()
    public function test_listPaymentMethods_returns_ListPaymentMethods() {
        $response = WebPay::listPaymentMethods( Svea\SveaConfig::getDefaultConfig() )
                ->setCountryCode("SE")
                ->doRequest()
        ;
        $this->assertInstanceOf( "Svea\HostedService\ListPaymentMethodsResponse", $response );        
        $this->assertEquals( true, $response->accepted );       
    }     
}