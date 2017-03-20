<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\QueryOrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class QueryOrderBuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $queryOrderObject;

    function setUp()
    {
        $this->queryOrderObject = new QueryOrderBuilder(ConfigurationService::getDefaultConfig());
    }

    public function test_queryOrderBuilder_class_exists()
    {
        $this->assertInstanceOf('Svea\WebPay\BuildOrder\QueryOrderBuilder', $this->queryOrderObject);
    }

    public function test_queryOrderBuilder_setOrderId()
    {
        $orderId = "123456";
        $this->queryOrderObject->setOrderId($orderId);
        $this->assertEquals($orderId, $this->queryOrderObject->orderId);
    }

    public function test_queryOrderBuilder_setTransactionId()
    {
        $orderId = "123456";
        $this->queryOrderObject->setTransactionId($orderId);
        $this->assertEquals($orderId, $this->queryOrderObject->orderId);
    }

    public function test_queryOrderBuilder_setCountryCode()
    {
        $country = "SE";
        $this->queryOrderObject->setCountryCode($country);
        $this->assertEquals($country, $this->queryOrderObject->countryCode);
    }

    public function test_queryOrderBuilder_queryInvoiceOrder_returns_GetOrdersRequest_with_correct_orderType()
    {
        $orderId = "123456";
        $paymentMethod = ConfigurationProvider::INVOICE_TYPE;   // todo check these ws ConfigProvicer::INVOICE_TYPE et al...

        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryInvoiceOrder();

        $this->assertInstanceOf("Svea\WebPay\AdminService\GetOrdersRequest", $queryOrderObject);
        $this->assertEquals($paymentMethod, $queryOrderObject->orderBuilder->orderType);

    }

    public function test_queryOrderBuilder_queryPaymentPlanOrder_returns_GetOrdersRequest_with_correct_orderType()
    {
        $orderId = "123456";
        $paymentMethod = ConfigurationProvider::PAYMENTPLAN_TYPE;   // todo check these ws ConfigProvicer::INVOICE_TYPE et al...

        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryPaymentPlanOrder();

        $this->assertInstanceOf("Svea\WebPay\AdminService\GetOrdersRequest", $queryOrderObject);
        $this->assertEquals($paymentMethod, $queryOrderObject->orderBuilder->orderType);

    }

    public function test_queryOrderBuilder_queryAccountCreditOrder_returns_GetOrdersRequest_with_correct_orderType()
    {
        $orderId = "123456";
        $paymentMethod = ConfigurationProvider::ACCOUNTCREDIT_TYPE;   // todo check these ws ConfigProvicer::ACCOUNTCREDIT_TYPE et al...

        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryAccountCreditOder();

        $this->assertInstanceOf("Svea\WebPay\AdminService\GetOrdersRequest", $queryOrderObject);
        $this->assertEquals($paymentMethod, $queryOrderObject->orderBuilder->orderType);

    }

    public function test_queryOrderBuilder_queryCardOrder_returns_QueryTransaction()
    {
        $orderId = "123456";

        $queryOrderObject = $this->queryOrderObject->setOrderId($orderId)->queryCardOrder();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction", $queryOrderObject);
    }
}
