<?php

namespace UnitTest\Checkout\Service\Admin;

use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

class AddOrderRowServiceTest extends TestCase
{
    public function testPrepareDataWithValidData()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderRow = WebPayItem::orderRow()
            ->setArticleNumber('prod-01')
            ->setName('someProd1')
            ->setVatPercent(0)// required - 0, 6, 12, 25.
            ->setAmountIncVat(50.00)
            ->setQuantity(1)
            ->setUnit('pc');

        $orderService = WebPayAdmin::addOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->addOrderRow($orderRow)
            ->addCheckoutOrderRows();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertEquals(7383, $preparedData['orderId']);
        $this->assertArrayHasKey('articleNumber', $preparedData['orderRow']);
        $this->assertArrayHasKey('name', $preparedData['orderRow']);
        $this->assertArrayHasKey('quantity', $preparedData['orderRow']);
        $this->assertArrayHasKey('vatPercent', $preparedData['orderRow']);
        $this->assertArrayHasKey('unitPrice', $preparedData['orderRow']);
        $this->assertArrayHasKey('unit', $preparedData['orderRow']);
    }


    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Id : Order Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderIdAsString()
    {
        $sveaCheckoutOrderId = '7383';
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::addOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->addCheckoutOrderRows();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertEquals(7383, $preparedData['orderId']);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public function testPrepareDataWithoutOrderRow()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::addOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->addCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }
}
