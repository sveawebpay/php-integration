<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\CancelOrderBuilder;
use Svea\WebPay\AdminService\CancelOrderRequest;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRequestTest extends \PHPUnit\Framework\TestCase
{

    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new CancelOrderBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->setOrderId(123456);
        $this->builderObject->orderType = ConfigurationProvider::INVOICE_TYPE;
    }

    public function testClassExists()
    {
        $cancelOrderRequestObject = new CancelOrderRequest(new CancelOrderBuilder(ConfigurationService::getDefaultConfig()));
        $this->assertInstanceOf('Svea\WebPay\AdminService\CancelOrderRequest', $cancelOrderRequestObject);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderId is required.
     */
    public function test_validate_throws_exception_on_missing_OrderId()
    {
        unset($this->builderObject->orderId);
        $cancelOrderRequestObject = new CancelOrderRequest($this->builderObject);
        $request = $cancelOrderRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderType is required.
     */
    public function test_validate_throws_exception_on_missing_OrderType()
    {
        unset($this->builderObject->orderType);
        $cancelOrderRequestObject = new CancelOrderRequest($this->builderObject);
        $request = $cancelOrderRequestObject->prepareRequest();
    }
}
