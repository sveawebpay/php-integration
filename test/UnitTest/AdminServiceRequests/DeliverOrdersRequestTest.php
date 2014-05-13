<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrdersRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new deliverOrderBuilder(SveaConfig::getDefaultConfig());  
//        $this->builderObject->setOrderId(123456);
    }
    
    public function testClassExists() {
        $deliverOrderBuilder = new deliverOrderBuilder( SveaConfig::getDefaultConfig() );        
        $deliverOrdersRequestObject = new DeliverOrdersRequest( $deliverOrderBuilder );
        $this->assertInstanceOf('Svea\DeliverOrdersRequest', $deliverOrdersRequestObject);
    }
    
    /**
     * @expectedException Svea\ValidationException 
     * @expectedExceptionMessage -missing value: sveaOrderId is required. Use function setSveaOrderId().
     */
//    public function test_validate_throws_exception_on_missing_OrderId() {
//        unset( $this->builderObject->sveaOrderId );
//
//        $cancelOrderRequestObject = new CancelOrderRequest( $this->builderObject );
//        $request = $cancelOrderRequestObject->prepareRequest();
//    }
    /**
     * @expectedException Svea\ValidationException 
     * @expectedExceptionMessage -missing value: orderType is required.
     */
//    public function test_validate_throws_exception_on_missing_OrderType() {
//        unset( $this->builderObject->orderType );
//
//        $cancelOrderRequestObject = new CancelOrderRequest( $this->builderObject );
//        $request = $cancelOrderRequestObject->prepareRequest();
//    }      
}
