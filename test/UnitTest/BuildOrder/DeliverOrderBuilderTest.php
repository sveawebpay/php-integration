<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder;

use Svea\WebPay\Config\SveaConfig;
use Svea\WebPay\BuildOrder\DeliverOrderBuilder;

/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class DeliverOrderBuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $deliverOrderObject;

    function setUp()
    {
        $this->deliverOrderObject = new DeliverOrderBuilder(SveaConfig::getDefaultConfig());
    }

    public function test_DeliverOrderBuilder_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\DeliverOrderBuilder", $this->deliverOrderObject);
    }

    public function test_DeliverOrderBuilder_setOrderId()
    {
        $orderId = "123456";
        $this->deliverOrderObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->deliverOrderObject->orderId);
    }

    public function test_DeliverOrderBuilder_setTransactionId()
    {
        $orderId = "123456";
        $this->deliverOrderObject->setTransactionId($orderId);
        $this->assertEquals($orderId, $this->deliverOrderObject->orderId);
    }

    public function test_DeliverOrderBuilder_setCountryCode()
    {
        $country = "SE";
        $this->deliverOrderObject->setCountryCode($country);
        $this->assertEquals($country, $this->deliverOrderObject->countryCode);
    }
}
