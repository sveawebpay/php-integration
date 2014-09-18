<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class DeliverOrderBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $deliverOrderObject;
    
    function setUp() {
        $this->deliverOrderObject = new Svea\DeliverOrderBuilder(Svea\SveaConfig::getDefaultConfig());  
    }
    
    public function test_DeliverOrderBuilder_class_exists() {     
        $this->assertInstanceOf("Svea\DeliverOrderBuilder", $this->deliverOrderObject);
    }
    
    public function test_DeliverOrderBuilder_setOrderId() {
        $orderId = "123456";
        $this->deliverOrderObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->deliverOrderObject->orderId);        
    }

    public function test_DeliverOrderBuilder_setTransactionId() {
        $orderId = "123456";
        $this->deliverOrderObject->setTransactionId($orderId);
        $this->assertEquals($orderId, $this->deliverOrderObject->orderId);        
    }    
    
    public function test_DeliverOrderBuilder_setCountryCode() {
        $country = "SE";
        $this->deliverOrderObject->setCountryCode($country);
        $this->assertEquals($country, $this->deliverOrderObject->countryCode);        
    }
}
