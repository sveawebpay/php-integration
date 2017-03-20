<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\CreditOrderRowsBuilder;
use Svea\WebPay\AdminService\CreditAccountCreditRowsRequest;

class CreditAccountCreditRowsTest extends  \PHPUnit_Framework_TestCase
{
    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = ConfigurationProvider::ACCOUNTCREDIT_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->rowsToCredit = array(TestUtil::createOrderRow(10.00));
        $this->builderObject->creditOrderRows = array(TestUtil::createOrderRow(10.00));
    }

    public function testClassExists()
    {
        $AddOrderRowsRequestObject = new CreditAccountCreditRowsRequest($this->builderObject);
        $this->assertInstanceOf('Svea\WebPay\AdminService\CreditAccountCreditRowsRequest', $AddOrderRowsRequestObject);
    }

    public function test_validate_throws_exception_on_missing_OrderId()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', 'orderId is required, use setOrderId().'
        );

        unset($this->builderObject->orderId);
        $creditRequest = new CreditAccountCreditRowsRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_OrderType()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : orderType is required.'
        );

        unset($this->builderObject->orderType);
        $creditRequest = new CreditAccountCreditRowsRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_CountryCode()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : countryCode is required, use setCountryCode().'
        );

        unset($this->builderObject->countryCode);
        $creditRequest = new CreditAccountCreditRowsRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_RowsToCredit()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : no rows to credit, use setRow(s)ToCredit() or addCreditOrderRow(s)().'
        );

        $this->builderObject->rowsToCredit = array();
        $this->builderObject->creditOrderRows = array();
        $creditRequest = new CreditAccountCreditRowsRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }
}