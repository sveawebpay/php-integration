<?php

namespace UnitTest\Checkout\Service\Admin;

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

class UpdateOrderRowsServiceTest extends TestCase
{
    public function testPrepareDataWithValidData()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderRow = WebPayItem::numberedOrderRow()
            ->setRowId(4)
            ->setName('someProd')
            ->setVatPercent(6)
            ->setDiscountPercent(50)
            ->setAmountIncVat(123.9876)
            ->setQuantity(4)
            ->setUnit('pc');

        $orderService = WebPayAdmin::updateOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->updateOrderRow($orderRow)
            ->updateCheckoutOrderRows();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertEquals(7383, $preparedData['orderId']);
        $this->assertEquals(4, $preparedData['orderRowId']);
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

        $orderRow = WebPayItem::numberedOrderRow()
            ->setRowId(4)
            ->setName('someProd')
            ->setVatPercent(6)
            ->setDiscountPercent(50)
            ->setAmountIncVat(123.9876)
            ->setQuantity(4)
            ->setUnit('pc');

        $orderService = WebPayAdmin::updateOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->updateOrderRow($orderRow)
            ->updateCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Row data : You can Update just one Order Row
     */
    public function testPrepareDataWithMultipleOrderRows()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderRow = WebPayItem::numberedOrderRow()
            ->setRowId(4)
            ->setName('someProd')
            ->setVatPercent(6)
            ->setDiscountPercent(50)
            ->setAmountIncVat(123.9876)
            ->setQuantity(4)
            ->setUnit('pc');

        $orderService = WebPayAdmin::updateOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->updateOrderRow($orderRow)
            ->updateOrderRow($orderRow)
            ->updateCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Row data : Order Row data can't be empty and must be Array
     */
    public function testPrepareDataWithoutOrderRow()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::updateOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->updateCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }
}
