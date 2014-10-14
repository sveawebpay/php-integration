<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayAdminIntegrationTest extends PHPUnit_Framework_TestCase {

    /// cancelOrder()
    // invoice
    // partpayment
    // card
    public function test_cancelOrder_cancelInvoiceOrder_returns_CloseOrder() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelInvoiceOrder();        
        $this->assertInstanceOf( "Svea\WebService\CloseOrder", $request );
        $this->assertEquals(\ConfigurationProvider::INVOICE_TYPE, $request->orderBuilder->orderType); 
    }
    
    public function test_cancelOrder_cancelPaymentPlanOrder_returns_CloseOrder() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\WebService\CloseOrder", $request );
        $this->assertEquals(\ConfigurationProvider::PAYMENTPLAN_TYPE, $request->orderBuilder->orderType); 
    }

    public function test_cancelOrder_cancelCardOrder_returns_AnnulTransaction() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelCardOrder();        
        $this->assertInstanceOf( "Svea\HostedService\AnnulTransaction", $request );
    }

    /// queryOrder()
    // invoice
    // partpayment
    // card
    // direct bank
    public function test_queryOrder_queryInvoiceOrder_returns_GetOrdersRequest() {
        $queryOrder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $queryOrder->queryInvoiceOrder();        
        $this->assertInstanceOf( "Svea\AdminService\GetOrdersRequest", $request );
        $this->assertEquals(\ConfigurationProvider::INVOICE_TYPE, $request->orderBuilder->orderType); 
    }    
    
    public function test_queryOrder_queryPaymentPlanOrder_returns_GetOrdersRequest() {
        $queryOrder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $queryOrder->queryPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\AdminService\GetOrdersRequest", $request );
        $this->assertEquals(\ConfigurationProvider::PAYMENTPLAN_TYPE, $request->orderBuilder->orderType); 
    }       

    public function test_queryOrder_queryCardOrder_returns_QueryTransaction() {
        $queryOrder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $queryOrder->queryCardOrder();        
        $this->assertInstanceOf( "Svea\HostedService\QueryTransaction", $request );
    } 

    public function test_queryOrder_queryDirectBankOrder_returns_QueryTransaction() {
        $queryOrder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $queryOrder->queryDirectBankOrder();
        $this->assertInstanceOf( "Svea\HostedService\QueryTransaction", $request );
    }     
    
    /// cancelOrderRows()
    // invoice
    // partpayment
    // card
    public function test_cancelOrderRows_cancelInvoiceOrderRows_returns_CancelOrderRowsRequest() {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrderRowsBuilder->cancelInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\CancelOrderRowsRequest", $request );
    }      
    
    public function test_cancelOrderRows_cancelPaymentPlanOrderRows_returns_CancelOrderRowsRequest() {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrderRowsBuilder->cancelPaymentPlanOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\CancelOrderRowsRequest", $request );
    }          
    
    public function test_cancelOrderRows_cancelCardOrderRows_returns_LowerTransaction() {
        $mockedNumberedOrderRow = new Svea\NumberedOrderRow();
        $mockedNumberedOrderRow
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;   
        
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $cancelOrderRowsBuilder->addNumberedOrderRow( $mockedNumberedOrderRow );
        $cancelOrderRowsBuilder->setRowToCancel(1);
        
        $request = $cancelOrderRowsBuilder->cancelCardOrderRows();        
        $this->assertInstanceOf( "Svea\HostedService\LowerTransaction", $request );
    }  
    
    /// creditOrderRows()
    // invoice
    // card
    // direct bank
    public function test_creditOrderRows_creditInvoiceOrderRows_returns_CreditOrderRowsRequest() {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $creditOrderRowsBuilder->creditInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\CreditOrderRowsRequest", $request );
    }    

    public function test_creditOrderRows_creditCardOrderRows_returns_CreditTransaction() {
        $mockedNumberedOrderRow = new Svea\NumberedOrderRow();
        $mockedNumberedOrderRow
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;  
        
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsBuilder->setOrderId( 123456 );
        $creditOrderRowsBuilder->addNumberedOrderRow( $mockedNumberedOrderRow );
        $creditOrderRowsBuilder->setRowToCredit(1);
        $request = $creditOrderRowsBuilder->creditCardOrderRows();                
        $this->assertInstanceOf( "Svea\HostedService\CreditTransaction", $request );
    } 
    
    public function test_creditOrderRows_creditDirectBankOrderRows_returns_CreditTransaction() {
        $mockedNumberedOrderRow = new Svea\NumberedOrderRow();
        $mockedNumberedOrderRow
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;  
        
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsBuilder->setOrderId( 123456 );
        $creditOrderRowsBuilder->addNumberedOrderRow( $mockedNumberedOrderRow );
        $creditOrderRowsBuilder->setRowToCredit(1);
        $request = $creditOrderRowsBuilder->creditDirectBankOrderRows();                
        $this->assertInstanceOf( "Svea\HostedService\CreditTransaction", $request );
    } 
    
    /// addOrderRows()
    // invoice
    // paymentplan
    public function test_addOrderRows_addInvoiceOrderRows_returns_AddOrderRowsRequest() {
        $addOrderRowsBuilder = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $addOrderRowsBuilder->addInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\AddOrderRowsRequest", $request );
    }

    public function test_addOrderRows_addPaymentPlanOrderRows_returns_AddOrderRowsRequest() {
        $addOrderRowsBuilder = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $addOrderRowsBuilder->addPaymentPlanOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\AddOrderRowsRequest", $request );
    }    
    
    /// updateOrderRows()
    // invoice
    // paymentplan
    public function test_updateOrderRows_updateInvoiceOrderRows_returns_UpdateOrderRowsRequest() {
        $updateOrderRowsBuilder = WebPayAdmin::updateOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $updateOrderRowsBuilder->updateInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\UpdateOrderRowsRequest", $request );
    }

    public function test_updateOrderRows_updatePaymentPlanOrderRows_returns_UpdateOrderRowsRequest() {
        $updateOrderRowsBuilder = WebPayAdmin::updateOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $updateOrderRowsBuilder->updatePaymentPlanOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\UpdateOrderRowsRequest", $request );
    } 
    
    // deliverOrderRows()
    // invoice
    public function test_deliverOrderRows_deliverInvoiceOrderRows_returns_DeliverOrderRowsRequest() {
        $deliverOrderRowsBuilder = WebPayAdmin::deliverOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $deliverOrderRowsBuilder
            ->setCountryCode("SE")
            ->setOrderId(123456)
            ->setInvoiceDistributionType( \DistributionType::POST )
            ->setRowTodeliver(1)
            ->deliverInvoiceOrderRows();
        $this->assertInstanceOf ("Svea\AdminService\DeliverOrderRowsRequest", $request );
    }    
    
    
    
    

    /// queryOrder
    //queryInvoiceOrder
    //-queryPaymentPlanOrder
    //-queryCardOrder
    //-queryDirectBankOrder
    public function test_queryOrder_queryInvoiceOrder_multiple_order_rows() {
    
        // create order using order row specified with ->setName() and ->setDescription
        $specifiedOrderRow = WebPayItem::orderRow()
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setName("orderrow 1")                 // optional
            ->setDescription("description 1")       // optional
        ;   

        // create order using order row specified with ->setName() and ->setDescription
        $specifiedOrderRow2 = WebPayItem::orderRow()
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setName("orderrow 2")                 // optional
            ->setDescription("description 2")       // optional
        ;         
                
        $order = WebPay::createOrder( \Svea\SveaConfig::getTestConfig() )
            ->addOrderRow($specifiedOrderRow)
            ->addOrderRow($specifiedOrderRow2)
            ->addCustomerDetails(TestUtil::createIndividualCustomer())
            ->setClientOrderNumber("123")
            ->setCountryCode("SE")
            ->setOrderDate(date('c'))
            ->setCustomerReference("test_queryOrder_queryInvoiceOrder_multiple_order_rows()")
        ;
        
        $createOrderResponse = $order->useInvoicePayment()->doRequest();
        
        //print_r( $createOrderResponse );
        $this->assertInstanceOf ("Svea\WebService\CreateOrderResponse", $createOrderResponse );
        $this->assertTrue( $createOrderResponse->accepted );
        
        $createdOrderId = $createOrderResponse->sveaOrderId;        
        
        // WPA::queryOrder()
        // ->queryInvoiceOrder()
        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId( $createdOrderId )
            ->setCountryCode("SE")
        ;
                
        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest();         
        
        print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);    
                
        // assert that order rows are the same 
        $this->assertEquals( 1, $queryResponse->accepted); 
        
        $this->assertEquals( 1, $queryResponse->numberedOrderRows[0]->rowNumber );
        $this->assertEquals( 1.00, $queryResponse->numberedOrderRows[0]->quantity );
        $this->assertEquals( 100.00, $queryResponse->numberedOrderRows[0]->amountExVat );
        $this->assertEquals( 25, $queryResponse->numberedOrderRows[0]->vatPercent );
        $this->assertEquals( null, $queryResponse->numberedOrderRows[0]->name );
        $this->assertEquals( "orderrow 1: description 1", $queryResponse->numberedOrderRows[0]->description );
        
        $this->assertEquals( 2, $queryResponse->numberedOrderRows[1]->rowNumber );
        $this->assertEquals( 1.00, $queryResponse->numberedOrderRows[1]->quantity );
        $this->assertEquals( 100.00, $queryResponse->numberedOrderRows[1]->amountExVat );
        $this->assertEquals( 25, $queryResponse->numberedOrderRows[1]->vatPercent );
        $this->assertEquals( null, $queryResponse->numberedOrderRows[1]->name );
        $this->assertEquals( "orderrow 2: description 2", $queryResponse->numberedOrderRows[1]->description );

    }        
    
    public function test_queryOrder_queryInvoiceOrder_single_order_row() {
    
        // create order using order row specified with ->setName() and ->setDescription
        $specifiedOrderRow = WebPayItem::orderRow()
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setName("orderrow 1")                 // optional
            ->setDescription("description 1")       // optional
        ;   

       
        $order = WebPay::createOrder( \Svea\SveaConfig::getTestConfig() )
            ->addOrderRow($specifiedOrderRow)
            ->addCustomerDetails(TestUtil::createIndividualCustomer())
            ->setClientOrderNumber("123")
            ->setCountryCode("SE")
            ->setOrderDate(date('c'))
            ->setCustomerReference("test_queryOrder_queryInvoiceOrder_single_order_row()")
        ;
        
        $createOrderResponse = $order->useInvoicePayment()->doRequest();
        
        print_r( $createOrderResponse );
        $this->assertInstanceOf ("Svea\WebService\CreateOrderResponse", $createOrderResponse );
        $this->assertTrue( $createOrderResponse->accepted );
        
        $createdOrderId = $createOrderResponse->sveaOrderId;        
        
        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId( $createdOrderId )
            ->setCountryCode("SE")
        ;
                
        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest();         
        
        print_r( $queryResponse);
        $this->assertEquals( 1, $queryResponse->accepted); 
        
        $this->assertEquals( 1, $queryResponse->numberedOrderRows[0]->rowNumber );
        $this->assertEquals( 1.00, $queryResponse->numberedOrderRows[0]->quantity );
        $this->assertEquals( 100.00, $queryResponse->numberedOrderRows[0]->amountExVat );
        $this->assertEquals( 25, $queryResponse->numberedOrderRows[0]->vatPercent );
        $this->assertEquals( null, $queryResponse->numberedOrderRows[0]->name );
        $this->assertEquals( "orderrow 1: description 1", $queryResponse->numberedOrderRows[0]->description );
    }          
}
