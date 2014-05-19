<?php
namespace Svea;

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
        $this->builderObject = new OrderBuilder(SveaConfig::getDefaultConfig());  
        // TODO create classes w/methods for below
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = \ConfigurationProvider::INVOICE_TYPE;                                                        
    }
    
    public function testClassExists() {
        $getOrdersRequestObject = new GetOrdersRequest( $this->builderObject );
        $this->assertInstanceOf('Svea\GetOrdersRequest', $getOrdersRequestObject);
    }
    
//    public function test_validate_throws_exception_on_missing_OrderId() {
//
//        $this->setExpectedException(
//          'Svea\ValidationException', '-missing value : orderId is required.'
//        );
//        
//        unset( $this->builderObject->orderId );
//        $cancelOrderRequestObject = new CancelOrderRequest( $this->builderObject );
//        $request = $cancelOrderRequestObject->prepareRequest();
//    }
//
//    public function test_validate_throws_exception_on_missing_OrderType() {
//        
//        $this->setExpectedException(
//          'Svea\ValidationException', '-missing value : orderType is required.'
//        );
//
//        unset( $this->builderObject->orderType );
//        $cancelOrderRequestObject = new CancelOrderRequest( $this->builderObject );
//        $request = $cancelOrderRequestObject->prepareRequest();
//    }      
}
