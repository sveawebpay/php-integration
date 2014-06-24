<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class GetOrdersRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_GetOrdersRequest() {
        
        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for test_manual_GetOrdersRequest' // TODO
//        );
        
        $countryCode = "SE";
        $sveaOrderIdToGet = 348629;
        $orderType = ConfigurationProvider::INVOICE_TYPE;
        
        $getOrdersBuilder = new Svea\QueryOrderBuilder( Svea\SveaConfig::getDefaultConfig() );
        $getOrdersBuilder->setOrderId($sveaOrderIdToGet);
        $getOrdersBuilder->setCountryCode($countryCode);
        $getOrdersBuilder->setOrderType($orderType);        

        $request = new Svea\AdminService\GetOrdersRequest( $getOrdersBuilder );
        $response = $request-> doRequest();
        
        //print_r( $response );        
        $this->assertInstanceOf('Svea\AdminService\GetOrdersResponse', $response);
        $this->assertEquals(1, $response->accepted );
    }
}
