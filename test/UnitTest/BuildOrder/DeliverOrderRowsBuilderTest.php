<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder;

use Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\BuildOrder\DeliverOrderRowsBuilder;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrderRowsBuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $deliverOrderRowsObject;

    function setUp()
    {
        $this->deliverOrderRowsObject = new DeliverOrderRowsBuilder(ConfigurationService::getDefaultConfig());
    }

    public function test_deliverOrderRowsBuilder_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\DeliverOrderRowsBuilder", $this->deliverOrderRowsObject);
    }

    public function test_deliverOrderRowsBuilder_setOrderId()
    {
        $orderId = "123456";
        $this->deliverOrderRowsObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->deliverOrderRowsObject->orderId);
    }

    public function test_deliverOrderRowsBuilder_setCountryCode()
    {
        $country = "SE";
        $this->deliverOrderRowsObject->setCountryCode($country);
        $this->assertEquals($country, $this->deliverOrderRowsObject->countryCode);
    }

    public function test_deliverOrderRowsBuilder_setInvoiceDistributionType()
    {
        $distributionType = DistributionType::POST;
        $this->deliverOrderRowsObject->setInvoiceDistributionType($distributionType);
        $this->assertEquals($distributionType, $this->deliverOrderRowsObject->distributionType);
    }

    public function test_deliverOrderRowsBuilder_deliverInvoiceOrderRowsBuilder_returns_deliverOrderRowsRequest()
    {
        $deliverOrderRowsObject = $this->deliverOrderRowsObject
            ->setCountryCode("SE")
            ->setOrderId(123456)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setRowTodeliver(1)
            ->deliverInvoiceOrderRows();

        $this->assertInstanceOf("Svea\WebPay\AdminService\deliverOrderRowsRequest", $deliverOrderRowsObject);
    }

    public function test_deliverOrderRowsBuilder_deliverAccountCreditRowsBuilder_returns_deliverOrderRowsRequest()
    {
        $deliverOrderRowsObject = $this->deliverOrderRowsObject
            ->setCountryCode("SE")
            ->setOrderId(123456)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setRowTodeliver(1)
            ->deliverAccountCreditOrderRows();

        $this->assertInstanceOf("Svea\WebPay\AdminService\deliverOrderRowsRequest", $deliverOrderRowsObject);
    }


    /// validations

    public function test_deliverInvoiceOrderRows_throws_ValidationException_on_missing_setCountryCode()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', "countryCode is required for deliverInvoiceOrderRows(). Use method setCountryCode()."
        );

        $deliverOrderRowsObject = $this->deliverOrderRowsObject
            //->setCountryCode("SE")
            ->setOrderId(123456)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setRowTodeliver(1);

        $deliverOrderRowsObject->deliverInvoiceOrderRows();
    }

    public function test_deliverInvoiceOrderRows_throws_ValidationException_on_missing_setOrderId()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', "orderId is required for deliverInvoiceOrderRows(). Use method setOrderId()."
        );

        $deliverOrderRowsObject = $this->deliverOrderRowsObject
            ->setCountryCode("SE")
            //->setOrderId(123456)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setRowTodeliver(1);

        $deliverOrderRowsObject->deliverInvoiceOrderRows();
    }

    public function test_deliverInvoiceOrderRows_throws_ValidationException_on_missing_setInvoiceDistributionType()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', "distributionType is required for deliverInvoiceOrderRows(). Use method setInvoiceDistributionType()."
        );

        $deliverOrderRowsObject = $this->deliverOrderRowsObject
            ->setCountryCode("SE")
            ->setOrderId(123456)
            //->setInvoiceDistributionType( \Svea\WebPay\Constant\DistributionType::POST )
            ->setRowTodeliver(1);

        $deliverOrderRowsObject->deliverInvoiceOrderRows();
    }

    public function test_deliverInvoiceOrderRows_throws_ValidationException_on_missing_setRowToDeliver()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', "rowsToDeliver is required for deliverInvoiceOrderRows(). Use methods setRowToDeliver() or setRowsToDeliver()."
        );

        $deliverOrderRowsObject = $this->deliverOrderRowsObject
            ->setCountryCode("SE")
            ->setOrderId(123456)
            ->setInvoiceDistributionType(DistributionType::POST)//->setRowTodeliver(1)
        ;

        $deliverOrderRowsObject->deliverInvoiceOrderRows();
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
