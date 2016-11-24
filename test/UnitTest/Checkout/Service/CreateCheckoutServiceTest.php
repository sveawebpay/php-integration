<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Service;

use Svea\WebPay\Checkout\Service\CheckoutService;
use Svea\WebPay\Checkout\Service\CreateOrderService;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class CreateCheckoutServiceTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Service
 */
class CreateCheckoutServiceTest extends TestCase
{
    /**
     * @var CheckoutService
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();

        $this->order->setCountryCode("SE")
            ->setCurrency('SEK')
            ->setCheckoutUri('http://localhost:51925/')
            ->setConfirmationUri('http://localhost:51925/checkout/confirm')
            ->setPushUri('https://svea.com/push.aspx?sid=123&svea_order=123')
            ->setTermsUri('http://localhost:51898/terms')
            ->setLocale('sv-Se');

        $this->service = new CreateOrderService($this->order);
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
            ->addPresetValue($this->returnPresetValue())
            ->addOrderRow($this->returnOrderRow())
            ->addOrderRow($this->returnOrderRow());

        $formattedOrderRows = $this->invokeMethod($this->service, 'mapCreateOrderData', array($this->order));

        $this->assertArrayHasKey('cart', $formattedOrderRows);
        $this->assertArrayHasKey('currency', $formattedOrderRows);
        $this->assertArrayHasKey('countryCode', $formattedOrderRows);
        $this->assertArrayHasKey('locale', $formattedOrderRows);
        $this->assertArrayHasKey('merchantSettings', $formattedOrderRows);
        $this->assertArrayHasKey('presetValues', $formattedOrderRows);
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
            ->addOrderRow($this->returnOrderRow())
            ->addOrderRow($this->returnOrderRow());

        $errors = $this->invokeMethod($this->service, 'validateOrder');

        $this->assertEquals(0, count($errors));
    }

}
