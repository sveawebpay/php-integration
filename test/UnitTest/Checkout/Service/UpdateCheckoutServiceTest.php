<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Service;

use Svea\WebPay\Checkout\Service\CheckoutService;
use Svea\WebPay\Checkout\Service\UpdateOrderService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class CreateCheckoutServiceTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Service
 */
class UpdateCheckoutServiceTest extends TestCase
{
    /**
     * @var CheckoutService
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();

        $this->order->setCountryCode("SE");

        $this->service = new UpdateOrderService($this->order);
    }

    /**
     * @test
     */
    public function gettingFormattedRows()
    {
        $this->order
            ->addOrderRow($this->returnOrderRow())
            ->addOrderRow($this->returnOrderRow());

        $formattedOrderRows = $this->invokeMethod($this->service, 'formatOrderInformationWithOrderRows');

        $this->assertEquals(2, count($formattedOrderRows));
    }

    /**
     * @test
     */
    public function preparingData()
    {
        $this->order
            ->addOrderRow($this->returnOrderRow())
            ->addOrderRow($this->returnOrderRow());

        $formattedOrderRows = $this->invokeMethod($this->service, 'mapCreateOrderData', array($this->order));

        $this->assertArrayHasKey('cart', $formattedOrderRows);
        $this->assertArrayHasKey('orderId', $formattedOrderRows);
        $this->assertArrayHasKey('merchantData', $formattedOrderRows);
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
            ->setId(123)
            ->addOrderRow($this->returnOrderRow())
            ->addOrderRow($this->returnOrderRow());

        $errors = $this->invokeMethod($this->service, 'validateOrder');

        $this->assertEquals(0, count($errors));
    }

}
