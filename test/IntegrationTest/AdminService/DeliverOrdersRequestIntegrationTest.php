<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrdersRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_DeliverOrdersRequest_on_closed_order_returns_resultcode_20000() {
        
        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for test_manual_DeliverOrdersRequest'
//        );
        
        $countryCode = "SE";
        $sveaOrderIdToDeliver = 349699; // need to exist, be closed
        $orderType = \ConfigurationProvider::INVOICE_TYPE;
        
        $DeliverOrderBuilder = new Svea\DeliverOrderBuilder( Svea\SveaConfig::getDefaultConfig() );
        $DeliverOrderBuilder->setCountryCode( $countryCode );
        $DeliverOrderBuilder->setOrderId( $sveaOrderIdToDeliver );
        $DeliverOrderBuilder->setInvoiceDistributionType(DistributionType::POST);
        $DeliverOrderBuilder->orderType = $orderType;
          
        $request = new Svea\AdminService\DeliverOrdersRequest( $DeliverOrderBuilder );
        $response = $request->doRequest();
        
        ////print_r( $response );        
        $this->assertInstanceOf('Svea\AdminService\DeliverOrdersResponse', $response);
        $this->assertEquals(0, $response->accepted ); // 
        $this->assertEquals(20000, $response->resultcode ); // 20000, order is closed.
    }

    // create order and make sure you can deliver partial rows
    public function test_DeliverOrdersRequest_on_open_order_returns_accepted_true() {
                    
        // create order
        $country = "SE"; 
           
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) );
                
        $orderResponse = $order->useInvoicePayment()->doRequest();
        ////print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);           
               
        $myOrderId = $orderResponse->sveaOrderId;
        
        // deliver order
        $DeliverOrderBuilder = new Svea\DeliverOrderBuilder( Svea\SveaConfig::getDefaultConfig() );
        $DeliverOrderBuilder->setCountryCode( $country );
        $DeliverOrderBuilder->setOrderId( $myOrderId );
        $DeliverOrderBuilder->setInvoiceDistributionType(DistributionType::POST);
        $DeliverOrderBuilder->orderType = ConfigurationProvider::INVOICE_TYPE;
        
        
        $request = new Svea\AdminService\DeliverOrdersRequest( $DeliverOrderBuilder );
        $response = $request->doRequest();
                
        ////print_r( $response );        
        $this->assertInstanceOf('Svea\AdminService\DeliverordersResponse', $response);
        $this->assertEquals(true, $response->accepted );    // truth
        $this->assertEquals(1, $response->accepted );       // equals literal 1
        $this->assertEquals(0, $response->resultcode );
        $this->assertEquals(250.00, $response->amount );
        $this->assertEquals("Invoice", $response->orderType );
        $this->assertNotNull($response->invoiceId );
        $this->assertNull($response->contractNumber );
    }   
}
