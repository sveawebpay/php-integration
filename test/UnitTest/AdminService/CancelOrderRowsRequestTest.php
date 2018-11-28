<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\AdminService\CancelOrderRowsRequest;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRowsRequestTest extends \PHPUnit\Framework\TestCase
{

    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new OrderBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->rowsToCancel = array(1);
    }

    public function testClassExists()
    {
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $this->assertInstanceOf('Svea\WebPay\AdminService\CancelOrderRowsRequest', $CancelOrderRowsRequestObject);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderId is required.
     */
    public function test_validate_throws_exception_on_missing_OrderId()
    {
        unset($this->builderObject->orderId);
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderType is required.
     */
    public function test_validate_throws_exception_on_missing_OrderType()
    {
        unset($this->builderObject->orderType);
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : countryCode is required.
     */
    public function test_validate_throws_exception_on_missing_CountryCode()
    {
        unset($this->builderObject->countryCode);
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : rowsToCancel is required.
     */
    public function test_validate_throws_exception_on_missing_RowsToCancel()
    {
        unset($this->builderObject->rowsToCancel);
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }
}
