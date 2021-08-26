<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Validation;

use Svea\WebPay\BuildOrder\Validator\OrderValidator;
use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Checkout\Validation\CreateOrderValidator;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class CreateOrderValidationTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Validation
 */
class CreateOrderValidationTest extends TestCase
{
    /**
     * @var OrderValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = new CreateOrderValidator();
    }

    /**
     * @test
     */
    public function ifRequiredOrderFieldsArePassed()
    {
        $this->order->setCountryCode('SE')
            ->setCurrency('SEK')
            ->setLocale('sv-Se');

        $errors = $this->invokeMethod($this->validator, 'validateRequiredOrderFields', [$this->order, []]);

        $errorsNum = count($errors);

        $this->assertEquals(0, $errorsNum);
    }

    /**
     * @test
     */
    public function ifRequiredOrderFieldsAreNotPassed()
    {
        $errors = $this->invokeMethod($this->validator, 'validateRequiredOrderFields', [$this->order, []]);

        $errorsNum = count($errors);

        $this->assertGreaterThan(0, $errorsNum);
    }

    /**
     * @test
     */
    public function ifOrderRowsAreNotPassed()
    {
        $errors = $this->invokeMethod($this->validator, 'validateRequiredFieldsForOrder', [$this->order, []]);

        $errorsNum = count($errors);

        $this->assertGreaterThan(0, $errorsNum);
    }

    /**
     * @test
     */
    public function ifOrderRowsArePassed()
    {
        $this->order->addOrderRow($this->returnOrderRow());

        $errors = $this->invokeMethod($this->validator, 'validateRequiredFieldsForOrder', [$this->order, []]);

        $errorsNum = count($errors);

        $this->assertEquals(0, $errorsNum);
    }

    /**
     * @test
     */
    public function ifMerchantRowsAreNotPassed()
    {
        $this->order;

        $errors = $this->invokeMethod($this->validator, 'validateMerchantSettings', [$this->order, []]);

        $errorsNum = count($errors);

        $this->assertGreaterThan(0, $errorsNum);
    }

    /**
     * @test
     */
    public function ifMerchantRowsArePassed()
    {
        $this->order->setCheckoutUri('http://localhost:51925/')
            ->setConfirmationUri('http://localhost:51925/checkout/confirm')
            ->setPushUri('https://svea.com/push.aspx?sid=123&svea_order=123')
            ->setTermsUri('http://localhost:51898/terms');

        $errors = $this->invokeMethod($this->validator, 'validateMerchantSettings', [$this->order, []]);

        $errorsNum = count($errors);

        $this->assertEquals(0, $errorsNum);
    }

    /**
     * @test
     */
    public function ifPartnerKeyPassed()
    {
        $this->order->setPartnerKey("77FB33EC-505D-4CCF-AA21-D9DF50DC8344");

        $errors = $this->invokeMethod($this->validator, 'validatePartnerKey', [$this->order, []]);

        $errorsNum = count($errors);

        $this->assertEquals(0, $errorsNum);
    }

    /**
     * @test
     */
    public function requireElectronicIdAuthenticationWrongType()
    {
        $this->order->setRequireElectronicIdAuthentication(true);

        $errors = $this->invokeMethod($this->validator, 'validateRequireElectronicIdAuthentication', [$this->order, []]);

        $errorsNum = count($errors);

        $this->assertEquals(0, $errorsNum);
    }
}
