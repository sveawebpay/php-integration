<?php


namespace Svea\WebPay\Test\UnitTest\AdminService;


use Svea\WebPay\AdminService\CreditAmountAccountCreditRequest;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\CreditOrderRowsBuilder;

class CreditAmountAccountCreditTestextends extends  \PHPUnit_Framework_TestCase
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

    public function test_validate_throws_exception_on_missing_OrderId()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', 'orderId is required, use setOrderId().'
        );

        unset($this->builderObject->orderId);
        $creditRequest = new CreditAmountAccountCreditRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }


    public function test_validate_throws_exception_on_missing_CountryCode()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-missing value : countryCode is required, use setCountryCode().'
        );

        unset($this->builderObject->countryCode);
        $creditRequest = new CreditAmountAccountCreditRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    public function test_validate_throws_exception_on_missing_amountIncVat()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-incorrect value : amountIncVat is too small'
        );

        unset($this->builderObject->amountIncVat);
        $creditRequest = new CreditAmountAccountCreditRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

    public function test_validate_throws_exception_on_bad_amount_value()
    {
        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException', '-incorrect value : amountIncVat is too small'
        );

        $this->builderObject->amountIncVat = "badValue";
        $creditRequest = new CreditAmountAccountCreditRequest($this->builderObject);
        $request = $creditRequest->prepareRequest();
    }

}