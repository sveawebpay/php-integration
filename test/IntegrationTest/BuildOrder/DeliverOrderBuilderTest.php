<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * DeliverOrderBuilder test holds all tests for how to deliver orders using DeliverOrderBuilder
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class DeliverOrderBuilderIntegrationTest extends PHPUnit_Framework_TestCase {

    public function test_deliverOrder_deliverInvoiceOrder_with_orderrows_use_DeliverOrderEU_and_is_accepted() {
        
        // create order, get orderid to deliver
        $createOrderBuilder = TestUtil::createOrder();
        $response = $createOrderBuilder->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);
        
        $orderId = $response->sveaOrderId;
        
        $DeliverOrderBuilder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() )
                ->addOrderRow( TestUtil::createOrderRow() )
                ->setCountryCode("SE")
                ->setOrderId( $orderId )
                ->setInvoiceDistributionType(\DistributionType::POST)
        ;
        
        $response = $DeliverOrderBuilder->deliverInvoiceOrder()->doRequest();

        //print_r( $response );
        $this->assertEquals(1, $response->accepted);                
        $this->assertInstanceOf( "Svea\WebService\DeliverOrderResult", $response );    // deliverOrderResult => deliverOrderEU 
    }
   
    public function test_deliverOrder_deliverPaymentPlanOrder_with_orderrows_use_DeliverOrderEU_and_is_accepted() {
        
        $order = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails( TestUtil::createIndividualCustomer("SE") )
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setCustomerReference("created by TestUtil::createOrder()")
            ->setClientOrderNumber( "clientOrderNumber:".date('c'))
            ->setOrderDate( date('c') )
        ;
        $response = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();

        $this->assertEquals(1, $response->accepted);
        
        $orderId = $response->sveaOrderId;

        $DeliverOrderBuilder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->setCountryCode("SE")
            ->setOrderId( $orderId )
        ;        
        
        $response = $DeliverOrderBuilder->deliverPaymentPlanOrder()->doRequest();

        //print_r( $response );
        $this->assertEquals(1, $response->accepted);
        $this->assertInstanceOf( "Svea\WebService\DeliverOrderResult", $response );
    }
    
    public function test_manual_deliverOrder_deliverCardOrder_use_ConfirmTransaction_and_is_accepted() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test, needs a pre-existing card transactionId with status AUTHORIZED'
        );
               
        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and check status of transaction in backoffice logs
        
        $orderId = 582406;  // pre-existing card transactionId with status AUTHORIZED  
        
        $DeliverOrderBuilder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setCountryCode("SE")
            ->setOrderId( $orderId )
        ;
        
        $response = $DeliverOrderBuilder->deliverCardOrder()->doRequest();

        //print_r( $response );
        $this->assertEquals(1, $response->accepted);
        $this->assertInstanceOf( "Svea\ConfirmTransactionResponse", $response );                
    }

    public function test_deliverOrder_deliverInvoiceOrder_without_orderrows_use_admin_service_deliverOrders_and_is_accepted() {
        // create order, get orderid to deliver
        $createOrderBuilder = TestUtil::createOrder();        
        $createResponse = $createOrderBuilder->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $createResponse->accepted);
        
        $orderId = $createResponse->sveaOrderId;        
        $DeliverOrderBuilder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() )
                //->addOrderRow( TestUtil::createOrderRow() )
                ->setCountryCode("SE")
                ->setOrderId( $orderId )
                ->setInvoiceDistributionType(\DistributionType::POST)
        ;
        
        $deliverResponse = $DeliverOrderBuilder->deliverInvoiceOrder()->doRequest();

        //print_r( $deliverResponse );        
        $this->assertEquals(1, $deliverResponse->accepted);
        $this->assertInstanceOf( "Svea\AdminService\DeliverOrdersResponse", $deliverResponse );  // deliverOrder_s_Response => Admin service deliverOrders  
    }        
 
    
}


?>