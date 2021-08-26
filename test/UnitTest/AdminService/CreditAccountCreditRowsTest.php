<?php

namespace Svea\WebPay\Test\UnitTest\AdminService;

use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\CreditOrderRowsBuilder;
use Svea\WebPay\AdminService\CreditAccountCreditRowsRequest;

class CreditAccountCreditRowsTest extends  \PHPUnit\Framework\TestCase
{
    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->orderType = ConfigurationProvider::ACCOUNTCREDIT_TYPE;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->rowsToCredit = [TestUtil::createOrderRow(10.00)];
        $this->builderObject->creditOrderRows = [TestUtil::createOrderRow(10.00)];
    }

    public function testClassExists()
    {
        $AddOrderRowsRequestObject = new CreditAccountCreditRowsRequest($this->builderObject);
        $this->assertInstanceOf('Svea\WebPay\AdminService\CreditAccountCreditRowsRequest', $AddOrderRowsRequestObject);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage orderId is required, use setOrderId().
     */
    public function test_validate_throws_exception_on_missing_OrderId()
    {
        unset($this->builderObject->orderId);
        $creditRequest = new CreditAccountCreditRowsRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : orderType is required.
     */
    public function test_validate_throws_exception_on_missing_OrderType()
    {
        unset($this->builderObject->orderType);
        $creditRequest = new CreditAccountCreditRowsRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : countryCode is required, use setCountryCode().
     */
    public function test_validate_throws_exception_on_missing_CountryCode()
    {
        unset($this->builderObject->countryCode);
        $creditRequest = new CreditAccountCreditRowsRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : no rows to credit, use setRow(s)ToCredit() or addCreditOrderRow(s)().
     */
    public function test_validate_throws_exception_on_missing_RowsToCredit()
    {
        $this->builderObject->rowsToCredit = [];
        $this->builderObject->creditOrderRows = [];
        $creditRequest = new CreditAccountCreditRowsRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }
}