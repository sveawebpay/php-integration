<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';

class WebServiceRowFormatterTest extends PHPUnit_Framework_TestCase {
    
    public function testFormatOrderRows() {
        $order = WebPay::createOrder();
        $order->addOrderRow(Item::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                ->setUnit("st")
                );
        
        $formatter = new WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[0];
        
        $this->assertEquals("0", $newRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $newRow->Description);
        $this->assertEquals(4.0, $newRow->PricePerUnit);
        $this->assertEquals(25.0, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }
    
    public function testFormatShippingFeeRows() {
        $order = WebPay::createOrder();
        $order->addFee(Item::shippingFee()
                    ->setShippingId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountExVat(4)
                    ->setVatPercent(25)
                    ->setUnit("st")
                );
        
        $formatter = new WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[0];
        
        $this->assertEquals("0", $newRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $newRow->Description);
        $this->assertEquals(4.0, $newRow->PricePerUnit);
        $this->assertEquals(25.0, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }
    
    public function testFormatInvoiceFeeRows() {
        $order = WebPay::createOrder();
        $order->addFee(Item::invoiceFee()
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setUnit("st")
                );
        
        $formatter = new WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[0];
        
        $this->assertEquals("", $newRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $newRow->Description);
        $this->assertEquals(4.0, $newRow->PricePerUnit);
        $this->assertEquals(25.0, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }
    
    public function testFormatFixedDiscountRows() {
        $order = WebPay::createOrder();
        $order->addOrderRow(Item::orderRow()
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addDiscount(Item::fixedDiscount()
                    ->setDiscountId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountIncVat(1)
                    ->setUnit("st")
                );
        
        $formatter = new WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[1];
        $this->assertEquals("0", $newRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $newRow->Description);
        $this->assertEquals(-0.8, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }
    
    public function testFormatRelativeDiscountRows() {
        $order = WebPay::createOrder();
        $order->addOrderRow(Item::orderRow()
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
            ->addDiscount(Item::relativeDiscount()
                ->setDiscountId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setDiscountPercent(10)
                ->setUnit("st")  
                );
        
        $formatter = new WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[1];
        
        $this->assertEquals("0", $newRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $newRow->Description);
        $this->assertEquals(-0.4, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }
}
