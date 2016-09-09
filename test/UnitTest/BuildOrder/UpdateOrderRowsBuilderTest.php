<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\UpdateOrderRowsBuilder;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class UpdateOrderRowsBuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $updateOrderRowsObject;

    function setUp()
    {
        $this->updateOrderRowsObject = new UpdateOrderRowsBuilder(ConfigurationService::getDefaultConfig());
    }

    public function test_updateOrderRowsBuilder_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\UpdateOrderRowsBuilder", $this->updateOrderRowsObject);
    }

    public function test_updateOrderRowsBuilder_setOrderId()
    {
        $orderId = "123456";
        $this->updateOrderRowsObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->updateOrderRowsObject->orderId);
    }

    public function test_updateOrderRowsBuilder_setCountryCode()
    {
        $country = "SE";
        $this->updateOrderRowsObject->setCountryCode($country);
        $this->assertEquals($country, $this->updateOrderRowsObject->countryCode);
    }

    public function test_updateOrderRowsBuilder_updateInvoiceOrderRowsBuilder_returns_UpdateOrderRowsRequest()
    {
        $orderId = "123456";
        $updateOrderRowsObject = $this->updateOrderRowsObject->setOrderId($orderId)->updateInvoiceOrderRows();

        $this->assertInstanceOf("Svea\WebPay\AdminService\UpdateOrderRowsRequest", $updateOrderRowsObject);
    }

    public function test_updateOrderRowsBuilder_updatePaymentPlanOrderRowsBuilder_returns_UpdateOrderRowsRequest()
    {
        $orderId = "123456";
        $updateOrderRowsObject = $this->updateOrderRowsObject->setOrderId($orderId)->updatePaymentPlanOrderRows();

        $this->assertInstanceOf("Svea\WebPay\AdminService\UpdateOrderRowsRequest", $updateOrderRowsObject);
    }
}
