<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder;

use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\AddOrderRowsBuilder;


/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class AddOrderRowsBuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $addOrderRowsObject;

    function setUp()
    {
        $this->addOrderRowsObject = new AddOrderRowsBuilder(ConfigurationService::getDefaultConfig());
    }

    public function test_addOrderRowsBuilder_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\AddOrderRowsBuilder", $this->addOrderRowsObject);
    }

    public function test_addOrderRowsBuilder_setOrderId()
    {
        $orderId = "123456";
        $this->addOrderRowsObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->addOrderRowsObject->orderId);
    }

    public function test_addOrderRowsBuilder_setCountryCode()
    {
        $country = "SE";
        $this->addOrderRowsObject->setCountryCode($country);
        $this->assertEquals($country, $this->addOrderRowsObject->countryCode);
    }

    public function test_addOrderRowsBuilder_addInvoiceOrderRowsBuilder_returns_AddOrderRowsRequest()
    {
        $orderId = "123456";
        $addOrderRowsObject = $this->addOrderRowsObject
            ->setOrderId($orderId)
            ->addOrderRow(TestUtil::createOrderRow(1.00))
            ->addInvoiceOrderRows();

        $this->assertInstanceOf("Svea\WebPay\AdminService\AddOrderRowsRequest", $addOrderRowsObject);
    }

    public function test_addOrderRowsBuilder_addPaymentPlanOrderRowsBuilder_returns_AddOrderRowsRequest()
    {
        $orderId = "123456";
        $addOrderRowsObject = $this->addOrderRowsObject
            ->setOrderId($orderId)
            ->addOrderRow(TestUtil::createOrderRow(1.00))
            ->addPaymentPlanOrderRows();

        $this->assertInstanceOf("Svea\WebPay\AdminService\AddOrderRowsRequest", $addOrderRowsObject);
    }

    public function test_addOrderRowsBuilder_missing_orderRows_throws_exception()
    {

        $this->setExpectedException('Svea\WebPay\BuildOrder\Validator\ValidationException');

        $orderId = "123456";
        $addOrderRowsObject = $this->addOrderRowsObject
            ->setOrderId($orderId)
            //->addOrderRow( \Svea\WebPay\Test\TestUtil::createOrderRow(1.00) )
            ->addInvoiceOrderRows();;

        $addOrderRowsObject->doRequest();
    }
}
