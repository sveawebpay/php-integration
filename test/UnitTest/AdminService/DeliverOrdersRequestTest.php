<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\DeliverOrderBuilder;
use Svea\WebPay\AdminService\DeliverOrdersRequest;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrdersRequestTest extends \PHPUnit_Framework_TestCase
{

    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new DeliverOrderBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->setOrderId(123456);
        $this->builderObject->setCountryCode("SE");
        $this->builderObject->setInvoiceDistributionType(DistributionType::POST);
        $this->builderObject->orderType = ConfigurationProvider::INVOICE_TYPE;
    }

    public function testClassExists()
    {
        $deliverOrdersRequestObject = new DeliverOrdersRequest(new DeliverOrderBuilder(ConfigurationService::getDefaultConfig()));
        $this->assertInstanceOf('Svea\WebPay\AdminService\DeliverOrdersRequest', $deliverOrdersRequestObject);
    }

    public function test_validate_throws_exception_on_missing_DistributionType()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : distributionType is required.'
        );

        unset($this->builderObject->distributionType);

        $deliverOrderRequestObject = new DeliverOrdersRequest($this->builderObject);
        $request = $deliverOrderRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderId()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderId is required.'
        );

        unset($this->builderObject->orderId);

        $deliverOrderRequestObject = new DeliverOrdersRequest($this->builderObject);
        $request = $deliverOrderRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderType is required.'
        );

        unset($this->builderObject->orderType);

        $deliverOrderRequestObject = new DeliverOrdersRequest($this->builderObject);
        $request = $deliverOrderRequestObject->prepareRequest();
    }
}
