<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Helper;

use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;
use Svea\WebPay\WebPay;

/**
 * Class CheckoutOrderBuilderTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Helper
 */
class CheckoutOrderBuilderTest extends TestCase
{
    /**
     * @var CheckoutOrderBuilder
     */
    protected $order;

    public function setUp()
    {
        $this->order = $this->returnCreatedOrder();
    }

    /**
     * @test
     */
    public function returnCheckoutOrderBuilderInstance()
    {
        $this->assertInstanceOf('Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder', $this->order);
    }

    /**
     * @test
     */
    public function isInitializedMerchantSetting()
    {
        $this->assertInstanceOf('Svea\WebPay\Checkout\Model\MerchantSettings', $this->order->getMerchantSettings());
    }

    /**
     * @test
     */
    public function setCountryCode()
    {
        $this->order->setCountryCode('EN');

        $this->assertEquals($this->order->getCountryCode(), 'EN');
    }

    /**
     * @test
     */
    public function setDefaultCountryCode()
    {
        $this->assertEquals($this->order->getCountryCode(), 'SE');
    }

    /**
     * @test
     */
    public function setOrderId()
    {
        $this->order->setId(123);

        $this->assertEquals($this->order->getId(), 123);
    }

    /**
     * @test
     */
    public function setCurrency()
    {
        $this->order->setCurrency('SEK');

        $this->assertEquals($this->order->getCurrency(), 'SEK');
    }

     /**
     * @test
     */
    public function setSCOLocale()
    {
        $this->order->setLocale('sv-Se');

        $this->assertEquals($this->order->getLocale(), 'sv-Se');
    }

    /**
     * @test
     */
    public function setValidationCallbackUri()
    {
        $validationCallbackUri = 'http://localhost:51898/validation-callback';
        $this->order->setValidationCallbackUri($validationCallbackUri);
        $this->assertEquals($this->order->getMerchantSettings()->getValidationCallbackUri(), $validationCallbackUri);
    }

    /**
     * @test
     */
    public function setMerchantData()
    {
        $merchantData = 'Newsletter:true';
        $this->order->setMerchantData($merchantData);
        $this->assertEquals($this->order->getMerchantData(), $merchantData);
    }
}
