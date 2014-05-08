<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRequestTest extends PHPUnit_Framework_TestCase {

    public function testClassExists() {
        $cancelOrderRequestObject = new Svea\CancelOrderRequest( new stdClass() );
        $this->assertInstanceOf('Svea\CancelOrderRequest', $cancelOrderRequestObject);
    }
    
    public function test_prepareRequest_contains_Authentication() {
        
        $builderObject = new stdClass();
        
        $cancelOrderRequestObject = new Svea\CancelOrderRequest( $builderObject );
        $request = $cancelOrderRequestObject->prepareRequest();
        
        $this->assertTrue( isset($request->Authentication) );
        $this->assertTrue( isset($request->Authentication->Username) );
        $this->assertTrue( isset($request->Authentication->Password) );        
    }    
}
