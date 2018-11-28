<?php


namespace Svea\WebPay\Test\UnitTest\AdminService;


use Svea\WebPay\AdminService\CreditAmountAccountCreditRequest;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\CreditOrderRowsBuilder;

class CreditAmountAccountCreditTestextends extends  \PHPUnit\Framework\TestCase
{
    public $builderObject;

    public function setUp()
    {
        $this->builderObject = new CreditOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $this->builderObject->orderId = 123456;
        $this->builderObject->countryCode = "SE";
        $this->builderObject->amountIncVat = 150.00;
    }


    public function testClassExists()
    {
        $AddOrderRowsRequestObject = new CreditAmountAccountCreditRequest($this->builderObject);
        $this->assertInstanceOf('Svea\WebPay\AdminService\CreditAmountAccountCreditRequest', $AddOrderRowsRequestObject);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage orderId is required, use setOrderId().
     */
    public function test_validate_throws_exception_on_missing_OrderId()
    {
        unset($this->builderObject->orderId);
        $creditRequest = new CreditAmountAccountCreditRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }


    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : countryCode is required, use setCountryCode().
     */
    public function test_validate_throws_exception_on_missing_CountryCode()
    {
        unset($this->builderObject->countryCode);
        $creditRequest = new CreditAmountAccountCreditRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect value : amountIncVat is too small
     */
    public function test_validate_throws_exception_on_missing_amountIncVat()
    {
        unset($this->builderObject->amountIncVat);
        $creditRequest = new CreditAmountAccountCreditRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -incorrect value : amountIncVat is too small
     */
    public function test_validate_throws_exception_on_bad_amount_value()
    {
        $this->builderObject->amountIncVat = "badValue";
        $creditRequest = new CreditAmountAccountCreditRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

}