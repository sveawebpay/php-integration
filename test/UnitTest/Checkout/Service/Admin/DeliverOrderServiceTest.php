<?php

namespace UnitTest\Checkout\Service\Admin;

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

class DeliverOrderServiceTest extends TestCase
{
    public function testPrepareDataWithOrderIdAsInteger()
    {
        $sveaCheckoutOrderId = 7383;
        $orderRowIds = array(1, 3);
        $testConfig = ConfigurationService::getTestConfig();

        $getOrderService = WebPayAdmin::deliverOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setRowsToDeliver($orderRowIds)
            ->deliverCheckoutOrderRows();

        $preparedData = $this->invokeMethod($getOrderService, 'prepareRequest');

        $this->assertEquals(7383, $preparedData['orderId']);
        $this->assertEquals(array(1, 3), $preparedData['orderRowIds']);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Id : Order Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderIdAsString()
    {
        $sveaCheckoutOrderId = '7383';
        $testConfig = ConfigurationService::getTestConfig();

        $getOrderService = WebPayAdmin::deliverOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->deliverCheckoutOrderRows();

        $this->invokeMethod($getOrderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Row Id : Order Row Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderIdAsIntegerAndOrderRowIdsAsString()
    {
        $sveaCheckoutOrderId = 7383;
        $orderRowIds = array('1', '3');
        $testConfig = ConfigurationService::getTestConfig();

        $getOrderService = WebPayAdmin::deliverOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setRowsToDeliver($orderRowIds)
            ->deliverCheckoutOrderRows();

        $this->invokeMethod($getOrderService, 'prepareRequest');
    }

    public function testPrepareDataWithOrderIdAsIntegerAndOrderRowIdsAsInteger()
    {
        $sveaCheckoutOrderId = 7383;
        $orderRowIds = array(1, 3);
        $testConfig = ConfigurationService::getTestConfig();

        $getOrderService = WebPayAdmin::deliverOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setRowsToDeliver($orderRowIds)
            ->deliverCheckoutOrderRows();

        $preparedData = $this->invokeMethod($getOrderService, 'prepareRequest');

        $this->assertEquals(array(
            'orderId' => 7383,
            'orderRowIds' => array(1, 3)
        ), $preparedData);
    }
}
