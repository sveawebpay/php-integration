<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder;

use Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\DeliverOrderBuilder;

/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class DeliverOrderBuilderTest extends \PHPUnit\Framework\TestCase
{

    protected $deliverOrderObject;

    function setUp()
    {
        $this->deliverOrderObject = new DeliverOrderBuilder(ConfigurationService::getDefaultConfig());
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



    public function returnProduct()
    {
        $mockedNumberedOrderRow = new NumberedOrderRow();
        $mockedNumberedOrderRow
            ->setAmountExVat(100.00)// recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)// recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)// required
            ->setRowNumber(1);

        return $mockedNumberedOrderRow;
    }
}
