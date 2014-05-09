<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AdminServiceRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_CancelOrderRequest() {
        
        $username = "sverigetest";
        $password = "sverigetest";
        $clientid = 79021;
        
        $orderType = "Invoice";
        $sveaOrderIdToClose = 344807;
        
        $config = new stdClass();
        $config->username = $username;
        $config->password = $password;
        $config->clientId = $clientid;
        $config->endpoint = "https://partnerweb.sveaekonomi.se/WebPayAdminService_Test/AdminService.svc/backward";

                
        $mockedBuilder = new StdClass();
        $mockedBuilder->conf = $config;
        $mockedBuilder->sveaOrderId = $sveaOrderIdToClose;
        $mockedBuilder->orderType = $orderType;
        
        $request = new Svea\CancelOrderRequest( $mockedBuilder );
        $response = $request->doRequest();
        
        //print_r( $response );        
        $this->assertEquals(0, $response->ResultCode );    // raw response
    }
}
