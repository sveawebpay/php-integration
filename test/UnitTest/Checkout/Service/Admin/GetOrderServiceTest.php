<?php

namespace UnitTest\Checkout\Service\Admin;

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\QueryOrderBuilder;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;
use Svea\WebPay\Checkout\Service\Admin\GetOrderService;

class GetOrderServiceTest extends TestCase
{
    public function testPrepareDataWithOrderIdAsInteger()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $getOrderService = WebPayAdmin::queryOrder($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->queryCheckoutOrder();

        $preparedData = $this->invokeMethod($getOrderService, 'prepareRequest');

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

        $getOrderService = WebPayAdmin::queryOrder($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->queryCheckoutOrder();

        $this->invokeMethod($getOrderService, 'prepareRequest');
    }

    public function testDoRequest()
    {
        $this->markTestSkipped('Skip Do request test');
        $testConfig = ConfigurationService::getTestConfig();

        /**
         * @var QueryOrderBuilder|\PHPUnit_Framework_MockObject_MockObject $queryOrderBuilder
         */
        $queryOrderBuilder = $this->getMockBuilder('\Svea\WebPay\BuildOrder\QueryOrderBuilder')
            ->setConstructorArgs(array($testConfig))
            ->getMock();

        /**
         * @var GetOrderService|\PHPUnit_Framework_MockObject_MockObject $getOrderServiceMock
         */
        $getOrderServiceMock = $this->getMockBuilder('\Svea\WebPay\Checkout\Service\Admin\GetOrderService')
            ->setConstructorArgs(array($queryOrderBuilder))
            ->setMethods(array('validate', 'prepareData', 'doRequest'))
            ->getMock();

        $getOrderServiceMock->expects($this->once())->method('prepareRequest');

        $getOrderServiceMock->doRequest();
    }
}
