<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Model;


use Svea\WebPay\Checkout\Model\IdentityFlags;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class CheckoutIdentityFlagsTest
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Model
 */
class CheckoutIdentityFlagsTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     *
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public function setNonExistingIdentityFlag()
    {
        $this->setUp();

        // Set some valid flags and one invalid flag
        $this->order->addIdentityFlag(IdentityFlags::HIDEANONYMOUS);
        $this->order->addIdentityFlag("NonExistingFlag");
        $this->order->addIdentityFlag(IdentityFlags::HIDECHANGEADDRESS);

        $this->order->createOrder();
    }

    /**
     * @test
     */
    public function setValidIdentityFlag()
    {
        $this->setUp();
        $this->order->addIdentityFlag(IdentityFlags::HIDEANONYMOUS);
        $this->order->addIdentityFlag(IdentityFlags::HIDECHANGEADDRESS);
        $this->order->addIdentityFlag(IdentityFlags::HIDENOTYOU);

        $arr = $this->order->getIdentityFlags();

        $this->assertEquals("HideAnonymous", $arr[0]);
        $this->assertEquals("HideChangeAddress", $arr[1]);
        $this->assertEquals("HideNotYou", $arr[2]);

    }
}
