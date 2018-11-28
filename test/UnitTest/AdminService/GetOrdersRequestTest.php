<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\AdminService\GetOrdersRequest;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class GetOrdersRequestTest extends \PHPUnit\Framework\TestCase
{

    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new OrderBuilder(ConfigurationService::getDefaultConfig());
        // TODO create classes w/methods for below
        $this->builderObject->orderId = 123456;
    }

    public function testClassExists()
    {
        $getOrdersRequestObject = new GetOrdersRequest($this->builderObject);
        $this->assertInstanceOf('Svea\WebPay\AdminService\GetOrdersRequest', $getOrdersRequestObject);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderId is required.
     */
    public function test_validate_throws_exception_on_missing_OrderId()
    {
        unset($this->builderObject->orderId);
        $getOrdersRequestObject = new GetOrdersRequest($this->builderObject);
        $request = $getOrdersRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderType is required.
     */
    public function test_validate_throws_exception_on_missing_OrderType()
    {
        unset($this->builderObject->orderType);
        $getOrdersRequestObject = new GetOrdersRequest($this->builderObject);
        $request = $getOrdersRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : countryCode is required.
     */
    public function test_validate_throws_exception_on_missing_CountryCode()
    {
        unset($this->builderObject->countryCode);
        $getOrdersRequestObject = new GetOrdersRequest($this->builderObject);
        $request = $getOrdersRequestObject->prepareRequest();
    }
}
