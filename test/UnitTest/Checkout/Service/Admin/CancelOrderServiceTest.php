<?php

namespace UnitTest\Checkout\Service\Admin;

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

class CancelOrderServiceTest extends TestCase
{
    public function testPrepareDataWithValidData()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrder($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->cancelCheckoutOrder();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertArrayHasKey('orderId', $preparedData);
        $this->assertEquals(7383, $preparedData['orderId']);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Id : Order Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderIdAsString()
    {
        $sveaCheckoutOrderId = '7383';
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrder($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->cancelCheckoutOrder();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertArrayHasKey('orderId', $preparedData);
        $this->assertEquals(7383, $preparedData['orderId']);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Id : Order Id can't be empty and must be Integer
     */
    public function testPrepareDataWithoutOrderId()
    {
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrder($testConfig)
            ->cancelCheckoutOrder();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertArrayHasKey('orderId', $preparedData);
        $this->assertEquals(7383, $preparedData['orderId']);
    }

    public function testPrepareDataWithCancelingAmount()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrder($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setAmountIncVat(5.00)
            ->cancelCheckoutOrderAmount();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertArrayHasKey('orderId', $preparedData);
        $this->assertEquals(7383, $preparedData['orderId']);
        $this->assertArrayHasKey('cancelledAmount', $preparedData);
        $this->assertEquals(500, $preparedData['cancelledAmount']);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Amount for cancel order : Amount can't be empty and must be Integer
     */
    public function testPrepareDataWithCancelingAmountAsString()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrder($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setAmountIncVat('5.00')
            ->cancelCheckoutOrderAmount();

        $this->invokeMethod($orderService, 'prepareRequest');
    }
    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Amount for cancel order : Amount can't be empty and must be Integer
     */
    public function testPrepareDataWithoutCancelingAmount()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrder($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->cancelCheckoutOrderAmount();

        $this->invokeMethod($orderService, 'prepareRequest');
    }
}
