<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Validation;

use Svea\WebPay\Checkout\Validation\UpdateOrderValidator;
use Svea\WebPay\BuildOrder\Validator\OrderValidator;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class UpdateOrderValidationTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Validation
 */
class UpdateOrderValidationTest extends TestCase
{
    /**
     * @var OrderValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = new UpdateOrderValidator();
    }

    /**
     * @test
     */
    public function ifBlankCheckoutOrderIsPassed()
    {
        $this->order->setId(123);

        $errors = $this->invokeMethod($this->validator, 'validate', array($this->order, array()));

        $errorsNum = count($errors);

        $this->assertEquals(0, $errorsNum);
    }

    /**
     * @test
     */
    public function ifCheckoutOrderIdNotPassed()
    {
        $this->order->addOrderRow($this->returnOrderRow());

        $errors = $this->invokeMethod($this->validator, 'validateOrderRows', array($this->order, array()));

        $errorsNum = count($errors);

        $this->assertEquals(0, $errorsNum);
    }
}
