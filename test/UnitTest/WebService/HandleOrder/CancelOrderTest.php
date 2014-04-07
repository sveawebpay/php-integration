<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderTest extends \PHPUnit_Framework_TestCase {

    public function test_CancelOrderBuilder_class_exists() {
        $config = SveaConfig::getDefaultConfig();
        $cancelOrderObject = \WebPay::cancelOrder($config);        
        
        $this->assertInstanceOf("Svea\CancelOrderBuilder", $cancelOrderObject);
    }
    
    public function test_CancelOrderBuilder_setOrderId() {
        $orderId = "123456";
        
        $config = SveaConfig::getDefaultConfig();
        $cancelOrderObject = \WebPay::cancelOrder($config)
            ->setOrderId($orderId);
        
        $this->assertEquals($orderId, $cancelOrderObject->orderId);        
    }
    
    public function test_CancelOrderBuilder_setPaymentMethod_returns_CloseOrder() {
        $orderId = "123456";
        $paymentMethod = \PaymentMethod::INVOICE;
        
        $config = SveaConfig::getDefaultConfig();
        $cancelOrderObject = \WebPay::cancelOrder($config)
            ->setOrderId($orderId)
            ->usePaymentMethod($paymentMethod);
        
        $this->assertInstanceOf("Svea\CloseOrder", $cancelOrderObject);
    }
    
}
