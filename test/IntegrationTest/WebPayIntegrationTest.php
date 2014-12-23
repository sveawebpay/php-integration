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
 
    /// WebPay::deliverOrder()
    // invoice
    // TODO actual integration test

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
    // TODO actual integration test

    // card
    // TODO actual integration test
    
    /// WebPay::getAddresses()
    // TODO
 
    /// WebPay::getPaymentPlanParams()
    // TODO
    
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