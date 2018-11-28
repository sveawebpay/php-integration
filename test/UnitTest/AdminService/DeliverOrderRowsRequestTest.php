<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\AdminService\DeliverOrderRowsRequest;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrderRowsRequestTest extends \PHPUnit\Framework\TestCase
{

    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new OrderBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = ConfigurationProvider::INVOICE_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->rowsToDeliver = array(1);
    }

    public function testClassExists()
    {
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $this->assertInstanceOf('Svea\WebPay\AdminService\DeliverOrderRowsRequest', $DeliverOrderRowsRequestObject);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderId is required.
     */
    public function test_validate_throws_exception_on_missing_OrderId()
    {
        unset($this->builderObject->orderId);
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderType is required.
     */
    public function test_validate_throws_exception_on_missing_OrderType()
    {
        unset($this->builderObject->orderType);
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : countryCode is required.
     */
    public function test_validate_throws_exception_on_missing_CountryCode()
    {
        unset($this->builderObject->countryCode);
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : rowsToDeliver is required.
     */
    public function test_validate_throws_exception_on_missing_RowsToDeliver()
    {
        unset($this->builderObject->rowsToDeliver);
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }
}
