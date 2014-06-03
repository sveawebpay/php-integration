<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class GetOrdersRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new Svea\OrderBuilder(Svea\SveaConfig::getDefaultConfig());  
        // TODO create classes w/methods for below
        $this->builderObject->orderId = 123456;
    }
    
    public function testClassExists() {
        $getOrdersRequestObject = new Svea\AdminService\GetOrdersRequest( $this->builderObject );
        $this->assertInstanceOf('Svea\AdminService\GetOrdersRequest', $getOrdersRequestObject);
    }
    
    public function test_validate_throws_exception_on_missing_OrderId() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderId is required.'
        );
        
        unset( $this->builderObject->orderId );
        $getOrdersRequestObject = new Svea\AdminService\GetOrdersRequest( $this->builderObject );
        $request = $getOrdersRequestObject->prepareRequest();
    }    

    public function test_validate_throws_exception_on_missing_OrderType() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderType is required.'
        );
        
        unset( $this->builderObject->orderType );
        $getOrdersRequestObject = new Svea\AdminService\GetOrdersRequest( $this->builderObject );
        $request = $getOrdersRequestObject->prepareRequest();
    }    
    
    public function test_validate_throws_exception_on_missing_CountryCode() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : countryCode is required.'
        );
        
        unset( $this->builderObject->countryCode );
        $getOrdersRequestObject = new Svea\AdminService\GetOrdersRequest( $this->builderObject );
        $request = $getOrdersRequestObject->prepareRequest();
    }
}
