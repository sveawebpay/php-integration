<?php
namespace Svea;

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
        $this->builderObject = new cancelOrderBuilder(SveaConfig::getDefaultConfig());  
        $this->builderObject->setOrderId(123456);
    }
    
    public function testClassExists() {
        $cancelOrderRequestObject = new CancelOrderRequest( new cancelOrderBuilder(SveaConfig::getDefaultConfig() ) );
        $this->assertInstanceOf('Svea\CancelOrderRequest', $cancelOrderRequestObject);
    }
    
    /**
     * @expectedException Svea\ValidationException 
     * @expectedExceptionMessage -missing value: sveaOrderId is required. Use function setSveaOrderId().
     */
    public function test_validate_throws_exception_on_missing_OrderId() {
        unset( $this->builderObject->sveaOrderId );

        $cancelOrderRequestObject = new CancelOrderRequest( $this->builderObject );
        $request = $cancelOrderRequestObject->prepareRequest();
    }
    /**
     * @expectedException Svea\ValidationException 
     * @expectedExceptionMessage -missing value: orderType is required.
     */
    public function test_validate_throws_exception_on_missing_OrderType() {
        unset( $this->builderObject->orderType );

        $cancelOrderRequestObject = new CancelOrderRequest( $this->builderObject );
        $request = $cancelOrderRequestObject->prepareRequest();
    }      
}
