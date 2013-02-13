<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';

class WebServiceRowFormatterTest extends PHPUnit_Framework_TestCase {
    
    public function testFormatOrderRows() {
        $order = new createOrderBuilder();
        $order->beginOrderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                ->setUnit("st")
        ->endOrderRow();
        
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
        $order = new createOrderBuilder();
        $order->beginShippingFee()
                ->setShippingId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setUnit("st")
        ->endShippingFee();
        
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
        $order = new createOrderBuilder();
        $order->beginInvoiceFee()
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setUnit("st")
        ->endInvoiceFee();
        
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
        $order = new createOrderBuilder();
        $order->beginOrderRow()
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
            ->endOrderRow()
        ->beginFixedDiscount()
                ->setDiscountId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountIncVat(1)
                ->setUnit("st")
        ->endFixedDiscount();
        
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
        $order = new createOrderBuilder();
        $order->beginOrderRow()
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
        ->endOrderRow()
        ->beginRelativeDiscount()
                ->setDiscountId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setDiscountPercent(10)
                ->setUnit("st")
        ->endRelativeDiscount();
        
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

?>
