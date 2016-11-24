<?php

namespace UnitTest\Checkout\Service\Admin;

use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

class CreditOrderRowsServiceTest extends TestCase
{
    public function testPrepareDataWithValidData()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderRow = WebPayItem::orderRow()
            ->setAmountIncVat(300.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setDescription("Credited order with new Order row");

        $orderService = WebPayAdmin::creditOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setDeliveryId(1)
            ->addCreditOrderRow($orderRow)
            ->creditCheckoutOrderWithNewOrderRow();

        $preparedData = $this->invokeMethod($orderService, 'prepareRequest');

        $this->assertArrayHasKey('orderId', $preparedData);
        $this->assertArrayHasKey('deliveryId', $preparedData);
        $this->assertArrayHasKey('newCreditRow', $preparedData);
        $this->assertEquals(7383, $preparedData['orderId']);
        $this->assertEquals(1, $preparedData['deliveryId']);
        $this->assertEquals(true, is_array($preparedData['newCreditRow']));
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Id : Order Id can't be empty and must be Integer
     */
    public function testPrepareDataWithOrderIdAsString()
    {
        $sveaCheckoutOrderId = '7383';
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::creditOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setDeliveryId(1)
            ->creditCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Order Id : Order Id can't be empty and must be Integer
     */
    public function testPrepareDataWithoutOrderId()
    {
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::creditOrderRows($testConfig)
            ->setDeliveryId(1)
            ->creditCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage missing order rows : must be at least one Order row set
     */
    public function testPrepareDataWithoutOrderRow()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderService = WebPayAdmin::creditOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setDeliveryId(1)
            ->creditCheckoutOrderRows();

       $this->invokeMethod($orderService, 'prepareRequest');
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage incorrect Delivery Id : Delivery Id can't be empty and must be Integer
     */
    public function testPrepareDataWithDeliveryIdAsString()
    {
        $sveaCheckoutOrderId = 7383;
        $testConfig = ConfigurationService::getTestConfig();

        $orderRow = WebPayItem::orderRow()
            ->setAmountIncVat(300.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setDescription("Credited order with new Order row");

        $orderService = WebPayAdmin::creditOrderRows($testConfig)
            ->setCheckoutOrderId($sveaCheckoutOrderId)
            ->setDeliveryId('1')
            ->addCreditOrderRow($orderRow)
            ->creditCheckoutOrderRows();

        $this->invokeMethod($orderService, 'prepareRequest');
    }
}
