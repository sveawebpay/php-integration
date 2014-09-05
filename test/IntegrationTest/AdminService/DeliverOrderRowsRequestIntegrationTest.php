<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrderRowsRequestIntegrationTest extends PHPUnit_Framework_TestCase{
    
    public function test_deliver_single_invoice_orderRow_() {
                    
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
        print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);           
               
        $myOrderId = $orderResponse->sveaOrderId;
        
        // deliver first row in order
        $deliverOrderRowsRequest = WebPayAdmin::deliverOrderRows( Svea\SveaConfig::getDefaultConfig() );  
        $deliverOrderRowsRequest->setCountryCode( $country );
        $deliverOrderRowsRequest->setOrderId($myOrderId);
        $deliverOrderRowsRequest->setInvoiceDistributionType(DistributionType::POST);
        $deliverOrderRowsRequest->setRowToDeliver(1);        
        $deliverOrderRowsResponse = $deliverOrderRowsRequest->deliverInvoiceOrderRows()->doRequest();
        
        print_r( $deliverOrderRowsResponse );        
        $this->assertInstanceOf('Svea\AdminService\DeliverPartialResponse', $deliverOrderRowsResponse);
        $this->assertEquals(1, $deliverOrderRowsResponse->accepted );        
    }

//    public function test_deliver_single_paymentplan_orderRow_() {
//                    
//        // create order
//        $country = "SE";         
//        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
//        
//        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setDescription("second row")
//                ->setQuantity(1)
//                ->setAmountExVat(1600.00)
//                ->setVatPercent(25)
//            )        
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setDescription("third row")
//                ->setQuantity(1)
//                ->setAmountExVat(2400.00)
//                ->setVatPercent(25)
//            )
//        ;
//                
//        $orderResponse = $order->usePaymentPlanPayment($campaigncode)->doRequest();
//        //print_r( $orderResponse );
//        $this->assertEquals(1, $orderResponse->accepted);           
//               
//        $myOrderId = $orderResponse->sveaOrderId;
//        
//        // deliver first row in order                
//        $deliverOrderRowsRequest = WebPayAdmin::deliverOrderRows( Svea\SveaConfig::getDefaultConfig() );  
//        $deliverOrderRowsRequest->setCountryCode( $country );
//        $deliverOrderRowsRequest->setOrderId($myOrderId);
//        $deliverOrderRowsRequest->setRowToDeliver( 1 );
//        $deliverOrderRowsResponse = $deliverOrderRowsRequest->deliverPaymentPlanOrderRows()->doRequest();
//        
//        //print_r( $deliverOrderRowsResponse );        
//        $this->assertInstanceOf('Svea\AdminService\DeliverOrderRowsResponse', $deliverOrderRowsResponse);
//        $this->assertEquals(1, $deliverOrderRowsResponse->accepted );     
//    }    
//
//    public function test_deliver_multiple_paymentplan_orderRows_() {
//                    
//        // create order
//        $country = "SE";         
//        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
//        
//        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setDescription("second row")
//                ->setQuantity(1)
//                ->setAmountExVat(1600.00)
//                ->setVatPercent(25)
//            )        
//            ->addOrderRow( WebPayItem::orderRow()
//                ->setDescription("third row")
//                ->setQuantity(1)
//                ->setAmountExVat(2400.00)
//                ->setVatPercent(25)
//            )
//        ;
//                
//        $orderResponse = $order->usePaymentPlanPayment($campaigncode)->doRequest();
//        //print_r( $orderResponse );
//        $this->assertEquals(1, $orderResponse->accepted);           
//               
//        $myOrderId = $orderResponse->sveaOrderId;
//        
//        // deliver first row in order                
//        $deliverOrderRowsRequest = WebPayAdmin::deliverOrderRows( Svea\SveaConfig::getDefaultConfig() );  
//        $deliverOrderRowsRequest->setCountryCode( $country );
//        $deliverOrderRowsRequest->setOrderId($myOrderId);
//        $deliverOrderRowsRequest->setRowsToDeliver( array(1,2) );
//        $deliverOrderRowsResponse = $deliverOrderRowsRequest->deliverPaymentPlanOrderRows()->doRequest();
//        
//        //print_r( $deliverOrderRowsResponse );        
//        $this->assertInstanceOf('Svea\AdminService\DeliverOrderRowsResponse', $deliverOrderRowsResponse);
//        $this->assertEquals(1, $deliverOrderRowsResponse->accepted );     
//    }    
    
}
