<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AdminServiceRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    public function testCancelOrderRequest() {
        
        $config = new stdClass();
        $sveaOrderIdToClose = 123456;
        
        $mockedBuilder = new StdClass();
        $mockedBuilder->conf = $config;
        $mockedBuilder->sveaOrderId = $sveaOrderIdToClose;
        
        $request = new Svea\CancelOrderRequest( $mockedBuilder );
        $response = $request->doRequest();

        $this->assertEquals(0, $response->ResultCode );    // raw response
    }
}
