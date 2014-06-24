<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrdersRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_DeliverOrdersRequest() {
        
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
        
        //print_r( $response );        
        $this->assertInstanceOf('Svea\AdminService\DeliverOrdersResponse', $response);
        $this->assertEquals(0, $response->accepted ); // 
        $this->assertEquals(20000, $response->resultcode ); // 20000, order is closed.
    }
}
