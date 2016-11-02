<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\AdminService\DeliverOrderRowsRequest;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrderRowsRequestTest extends \PHPUnit_Framework_TestCase
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

    public function test_validate_throws_exception_on_missing_OrderId()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderId is required.'
        );

        unset($this->builderObject->orderId);
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderType is required.'
        );

        unset($this->builderObject->orderType);
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_CountryCode()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : countryCode is required.'
        );

        unset($this->builderObject->countryCode);
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_RowsToDeliver()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : rowsToDeliver is required.'
        );

        unset($this->builderObject->rowsToDeliver);
        $DeliverOrderRowsRequestObject = new DeliverOrderRowsRequest($this->builderObject);
        $request = $DeliverOrderRowsRequestObject->prepareRequest();
    }
}
