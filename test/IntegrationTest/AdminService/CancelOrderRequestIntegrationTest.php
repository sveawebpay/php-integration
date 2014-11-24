<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_CancelOrderRequest() {

        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for test_manual_CancelOrderRequest'
//        );
                
        $countryCode = "SE";
        $sveaOrderIdToClose = 349698;        
        $orderType = \ConfigurationProvider::INVOICE_TYPE;
        
        $cancelOrderBuilder = new Svea\CancelOrderBuilder( Svea\SveaConfig::getDefaultConfig() );
        $cancelOrderBuilder->setCountryCode( $countryCode );
        $cancelOrderBuilder->setOrderId( $sveaOrderIdToClose );
        $cancelOrderBuilder->orderType = $orderType;
        
        $request = new Svea\AdminService\CancelOrderRequest( $cancelOrderBuilder );
        $response = $request->doRequest();
        
        ////print_r("cancelorderrequest: "); //print_r( $response );        
        $this->assertInstanceOf('Svea\AdminService\CancelOrderResponse', $response);
        $this->assertEquals(1, $response->accepted );    
        $this->assertEquals(0, $response->resultcode );        

    }
}
