<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Validation;

use Svea\WebPay\BuildOrder\Validator\OrderValidator;
use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Checkout\Validation\GetOrderValidator;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class GetOrderValidationTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Validation
 */
class GetOrderValidationTest extends TestCase
{
    /**
     * @var \Svea\WebPay\BuildOrder\Validator\OrderValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = new GetOrderValidator();
    }

    /**
     * @test
     */
    public function ifCheckoutOrderIdNotPassed()
    {
        $errors = $this->validator->validate($this->order);

        $errorsNum = count($errors);

        $this->assertGreaterThan(0, $errorsNum);
    }

    /**
     * @test
     */
    public function ifCheckoutOrderIdPassed()
    {
        $this->order->setId(123);

        $errors = $this->validator->validate($this->order);

        $errorsNum = count($errors);

        $this->assertEquals(0, $errorsNum);
    }
}
