<?php


namespace Svea\WebPay\Test\UnitTest\Checkout\Model;

use Svea\WebPay\Test\UnitTest\Checkout\TestCase;
use Svea\WebPay\WebPayItem;

class CheckoutOrderRowTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testSetMerchantData()
    {
        $order = $this->order->addOrderRow(
            WebPayItem::orderRow()
                ->setAmountIncVat(rand(1, 1000) + rand(1, 99) / 100)// - required
                ->setVatPercent($this->getRandVatPercent())// - required
                ->setQuantity($this->getRandQuantity())
                ->setDiscountPercent($this->getRandDiscountPercent())
                ->setArticleNumber("123456")
                ->setName('Fork')
                ->setMerchantData('test string'));

        $this->assertEquals($order->orderRows[0]->merchantData, "test string");
    }

}