<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Service;

use Svea\WebPay\Checkout\Service\GetOrderService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;
use Svea\WebPay\Checkout\Service\CheckoutService;

/**
 * Class GetCheckoutServiceTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Service
 */
class GetCheckoutServiceTest extends TestCase
{
    /**
     * @var CheckoutService
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();

        $this->service = new GetOrderService($this->order);
    }

    /**
     * @test
     */
    public function orderValidationWithBadOrder()
    {
        $errors = $this->invokeMethod($this->service, 'validateOrder');

        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * @test
     */
    public function orderValidationWithGoodOrder()
    {
        $this->order
            ->setId(123);

        $errors = $this->invokeMethod($this->service, 'validateOrder');

        $this->assertEquals(0, count($errors));
    }

}
