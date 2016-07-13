<?php

use Svea\WebPay\BuildOrder\CancelOrderBuilder;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRequestTest extends \PHPUnit_Framework_TestCase {

    public $builderObject;
    
    public function setUp() {        
        $this->builderObject = new CancelOrderBuilder(\Svea\WebPay\Config\SveaConfig::getDefaultConfig());  
        $this->builderObject->setOrderId(123456);
        $this->builderObject->orderType = \Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE;                                                        
    }
    
    public function testClassExists() {
        $cancelOrderRequestObject = new \Svea\WebPay\AdminService\CancelOrderRequest( new CancelOrderBuilder(\Svea\WebPay\Config\SveaConfig::getDefaultConfig() ) );
        $this->assertInstanceOf('Svea\WebPay\AdminService\CancelOrderRequest', $cancelOrderRequestObject);
    }
    
    public function test_validate_throws_exception_on_missing_OrderId() {

        $this->setExpectedException(
          'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderId is required.'
        );
        
        unset( $this->builderObject->orderId );
        $cancelOrderRequestObject = new \Svea\WebPay\AdminService\CancelOrderRequest( $this->builderObject );
        $request = $cancelOrderRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType() {
        
        $this->setExpectedException(
          'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderType is required.'
        );
               
        unset( $this->builderObject->orderType );
        $cancelOrderRequestObject = new \Svea\WebPay\AdminService\CancelOrderRequest( $this->builderObject );
        $request = $cancelOrderRequestObject->prepareRequest();
    }      
}
