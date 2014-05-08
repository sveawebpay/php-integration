<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AdminServiceRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    public function testCancelOrderRequest() {
        
        $username = "sverigetest";
        $password = "sverigetest";
        $clientid = 79021;
        
        $orderType = "Invoice";
        $sveaOrderIdToClose = 344801;
        
        $config = new stdClass();
        $config->username = $username;
        $config->password = $password;
        $config->clientId = $clientid;
                
        $mockedBuilder = new StdClass();
        $mockedBuilder->conf = $config;
        $mockedBuilder->sveaOrderId = $sveaOrderIdToClose;
        $mockedBuilder->orderType = $orderType;
        
        $request = new Svea\CancelOrderRequest( $mockedBuilder );
        $response = $request->doRequest();

        $this->assertEquals(0, $response->ResultCode );    // raw response
    }
}
