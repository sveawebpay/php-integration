<?php

namespace Svea\WebPay\Test\UnitTest\Checkout\Helper;

use Svea\WebPay\Checkout\Helper\CheckoutRowFormatter;
use Svea\WebPay\Test\UnitTest\Checkout\TestCase;

/**
 * Class CheckoutRowFormatter
 * @package Svea\Svea\WebPay\WebPay\Test\UnitTest\Checkout\Helper
 */
class CheckoutRowFormatterTest extends TestCase
{
    /**
     * @var CheckoutRowFormatter
     */
    protected $formatter;

    public function setUp()
    {
        parent::setUp();

        $this->formatter = new CheckoutRowFormatter($this->order, true);
    }

    /**
     * @test
     */
    public function ifRowsAreFormatted()
    {
        $row = $this->returnOrderRow();

        $this->invokeMethod($this->formatter, 'formatOrderRows', array($row));

        $newRows = $this->getPrivateProperty($this->formatter, 'newRows');

        $this->assertInstanceOf('Svea\WebPay\WebService\SveaSoap\SveaOrderRow', $newRows[0]);
    }
}
