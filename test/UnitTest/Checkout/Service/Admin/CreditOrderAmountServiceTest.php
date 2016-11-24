<?php

namespace UnitTest\Checkout\Service\Admin;

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

class CreditOrderAmountServiceTest extends TestCase
{
    public function testPrepareDataWithValidData()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::creditAmount($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setDeliveryId(1)
            ->setAmountIncVat(20.00)
            ->creditCheckoutAmount();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertEquals(7383, $preparedData['orderId']);
        $this->assertEquals(1, $preparedData['deliveryId']);
        $this->assertEquals(2000, $preparedData['creditedAmount']);
        $this->assertArrayHasKey('orderId', $preparedData);
        $this->assertArrayHasKey('deliveryId', $preparedData);
        $this->assertArrayHasKey('creditedAmount', $preparedData);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Id : Order Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderIdAsString()
    {
        $sveaCheckoutOrderId = '7383';
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::creditAmount($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setDeliveryId(1)
            ->setAmountIncVat(20.00)
            ->creditCheckoutAmount();

        $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Delivery Id : Delivery Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderDeliveryIdAsString()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::creditAmount($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setDeliveryId('1')
            ->setAmountIncVat(20.00)
            ->creditCheckoutAmount();

        $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Delivery Id : Delivery Id can't be empty and must be Integer
     */
    public function testPrepareDataWithoutOrderDeliveryId()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::creditAmount($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setAmountIncVat(20.00)
            ->creditCheckoutAmount();

        $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Credit Amount : Credit amount can't be empty and must be number
     */
    public function testPrepareDataWithoutAmount()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::creditAmount($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setDeliveryId(1)
            ->creditCheckoutAmount();

        $this->invokeMethod($orderService, 'prepareRequest');
    }
}
