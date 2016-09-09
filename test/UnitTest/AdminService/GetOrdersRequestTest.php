<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\AdminService\GetOrdersRequest;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class GetOrdersRequestTest extends \PHPUnit_Framework_TestCase
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

    public function test_validate_throws_exception_on_missing_OrderId()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderId is required.'
        );

        unset($this->builderObject->orderId);
        $getOrdersRequestObject = new GetOrdersRequest($this->builderObject);
        $request = $getOrdersRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderType is required.'
        );

        unset($this->builderObject->orderType);
        $getOrdersRequestObject = new GetOrdersRequest($this->builderObject);
        $request = $getOrdersRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_CountryCode()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : countryCode is required.'
        );

        unset($this->builderObject->countryCode);
        $getOrdersRequestObject = new GetOrdersRequest($this->builderObject);
        $request = $getOrdersRequestObject->prepareRequest();
    }
}
