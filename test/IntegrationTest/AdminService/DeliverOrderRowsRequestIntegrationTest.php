<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase{
    
    public function test_deliver_single_invoice_orderRow_returns_accepted_with_invoiceId() {
                    
        // create order
        $country = "SE"; 
           
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
            ->addOrderRow( WebPayItem::orderRow()
                ->setDescription("second row")
                ->setQuantity(1)
                ->setAmountExVat(16.00)
                ->setVatPercent(25)
            )        
            ->addOrderRow( WebPayItem::orderRow()
                ->setDescription("third row")
                ->setQuantity(1)
                ->setAmountExVat(24.00)
                ->setVatPercent(25)
            )
        ;
             
        $orderResponse = $order->useInvoicePayment()->doRequest();
        //print_r( $orderResponse );                   
         
        $myOrderId = $orderResponse->sveaOrderId;
        
        // deliver first row in order
        $deliverOrderRowsRequest = WebPayAdmin::deliverOrderRows( Svea\SveaConfig::getDefaultConfig() );  
        $deliverOrderRowsRequest->setCountryCode( $country );
        $deliverOrderRowsRequest->setOrderId($myOrderId);
        $deliverOrderRowsRequest->setInvoiceDistributionType(DistributionType::POST);
        $deliverOrderRowsRequest->setRowToDeliver(1);        
        $deliverOrderRowsResponse = $deliverOrderRowsRequest->deliverInvoiceOrderRows()->doRequest();

        // Example DeliverPartial raw request response to parse:
        // 
        //stdClass Object
        //(
        //    [ErrorMessage] => 
        //    [ResultCode] => 0
        //    [OrdersDelivered] => stdClass Object
        //        (
        //            [DeliverOrderResult] => stdClass Object
        //                (
        //                    [ClientId] => 79021
        //                    [DeliveredAmount] => 250.00
        //                    [DeliveryReferenceNumber] => 1033899
        //                    [OrderType] => Invoice
        //                    [SveaOrderId] => 414180
        //                )
        //
        //        )
        //
        //)        
        
        //print_r( $deliverOrderRowsResponse );   
        //
        // Example DeliverPartialResponse
        //
        //Svea\AdminService\DeliverPartialResponse Object
        //(
        //    [clientId] => 79021
        //    [amount] => 250.00
        //    [invoiceId] => 1033902
        //    [contractNumber] => 
        //    [orderType] => Invoice
        //    [orderId] => 414183
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)        
            
        $this->assertInstanceOf( "Svea\AdminService\DeliverPartialResponse", $deliverOrderRowsResponse );
        $this->assertEquals(1, $deliverOrderRowsResponse->accepted);
        $this->assertEquals(0, $deliverOrderRowsResponse->resultcode);
        $this->assertEquals(null, $deliverOrderRowsResponse->errormessage);
 
        $this->assertEquals( 79021, $deliverOrderRowsResponse->clientId );
        $this->assertEquals( 250.00, $deliverOrderRowsResponse->amount);
        $this->assertStringMatchesFormat( "%d", $deliverOrderRowsResponse->invoiceId);   // %d => an unsigned integer value
        $this->assertEquals( null, $deliverOrderRowsResponse->contractNumber);
        $this->assertEquals( "Invoice", $deliverOrderRowsResponse->orderType);
        $this->assertStringMatchesFormat( "%d", $deliverOrderRowsResponse->orderId);   // %d => an unsigned integer value        
   }

    public function test_deliver_multiple_invoice_orderRows_returns_accepted_with_invoiceId() {
                    
        // create order
        $country = "SE"; 
           
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
            ->addOrderRow( WebPayItem::orderRow()
                ->setDescription("second row")
                ->setQuantity(1)
                ->setAmountExVat(16.00)
                ->setVatPercent(25)
            )        
            ->addOrderRow( WebPayItem::orderRow()
                ->setDescription("third row")
                ->setQuantity(1)
                ->setAmountExVat(24.00)
                ->setVatPercent(25)
            )
        ;
                
        $orderResponse = $order->useInvoicePayment()->doRequest();
        //print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);           
               
        $myOrderId = $orderResponse->sveaOrderId;
        
        // deliver first row in order
        $deliverOrderRowsRequest = WebPayAdmin::deliverOrderRows( Svea\SveaConfig::getDefaultConfig() );  
        $deliverOrderRowsRequest->setCountryCode( $country );
        $deliverOrderRowsRequest->setOrderId($myOrderId);
        $deliverOrderRowsRequest->setInvoiceDistributionType(DistributionType::POST);
        $deliverOrderRowsRequest->setRowsToDeliver( array(1,2) );       
        $deliverOrderRowsResponse = $deliverOrderRowsRequest->deliverInvoiceOrderRows()->doRequest();
        
        //print_r( $deliverOrderRowsResponse );        
        $this->assertInstanceOf('Svea\AdminService\DeliverPartialResponse', $deliverOrderRowsResponse);
        $this->assertEquals(true, $deliverOrderRowsResponse->accepted );    // truth
        $this->assertEquals(1, $deliverOrderRowsResponse->accepted );       // equals literal 1
        $this->assertEquals(0, $deliverOrderRowsResponse->resultcode );
        $this->assertEquals(270.00, $deliverOrderRowsResponse->amount );
        $this->assertEquals("Invoice", $deliverOrderRowsResponse->orderType );
        $this->assertNotNull($deliverOrderRowsResponse->invoiceId );
    }                    
}
