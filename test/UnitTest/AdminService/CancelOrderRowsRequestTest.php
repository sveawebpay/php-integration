<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\OrderBuilder;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\AdminService\CancelOrderRowsRequest;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRowsRequestTest extends \PHPUnit_Framework_TestCase
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

    public function test_validate_throws_exception_on_missing_OrderId()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderId is required.'
        );

        unset($this->builderObject->orderId);
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderType is required.'
        );

        unset($this->builderObject->orderType);
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_CountryCode()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : countryCode is required.'
        );

        unset($this->builderObject->countryCode);
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_RowsToCancel()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : rowsToCancel is required.'
        );

        unset($this->builderObject->rowsToCancel);
        $CancelOrderRowsRequestObject = new CancelOrderRowsRequest($this->builderObject);
        $request = $CancelOrderRowsRequestObject->prepareRequest();
    }
}
