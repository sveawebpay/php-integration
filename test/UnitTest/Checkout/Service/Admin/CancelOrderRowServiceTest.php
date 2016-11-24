<?php

namespace UnitTest\Checkout\Service\Admin;

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

class CancelOrderRowServiceTest extends TestCase
{
    public function testPrepareDataWithValidData()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setRowToCancel(1)
            ->cancelCheckoutOrderRows();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertArrayHasKey('orderId', $preparedData);
        $this->assertEquals(7383, $preparedData['orderId']);
        $this->assertArrayHasKey('orderRowId', $preparedData);
        $this->assertEquals(1, $preparedData['orderRowId']);
    }


    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Id : Order Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderIdAsString()
    {
        $sveaCheckoutOrderId = '7383';
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setRowToCancel(1)
            ->cancelCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Row Id : Order Row Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderRowIdAsString()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::cancelOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setRowToCancel('1')
            ->cancelCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }
}
