<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new \Svea\CancelOrderBuilder(Svea\SveaConfig::getDefaultConfig());  
        $this->builderObject->setOrderId(123456);
        $this->builderObject->orderType = \ConfigurationProvider::INVOICE_TYPE;                                                        
    }
    
    public function testClassExists() {
        $cancelOrderRequestObject = new Svea\AdminService\CancelOrderRequest( new Svea\CancelOrderBuilder(Svea\SveaConfig::getDefaultConfig() ) );
        $this->assertInstanceOf('Svea\AdminService\CancelOrderRequest', $cancelOrderRequestObject);
    }
    
    public function test_validate_throws_exception_on_missing_OrderId() {

        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderId is required.'
        );
        
        unset( $this->builderObject->orderId );
        $cancelOrderRequestObject = new Svea\AdminService\CancelOrderRequest( $this->builderObject );
        $request = $cancelOrderRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType() {
        
        $this->setExpectedException(
          'Svea\ValidationException', '-missing value : orderType is required.'
        );
               
        unset( $this->builderObject->orderType );
        $cancelOrderRequestObject = new Svea\AdminService\CancelOrderRequest( $this->builderObject );
        $request = $cancelOrderRequestObject->prepareRequest();
    }      
}
