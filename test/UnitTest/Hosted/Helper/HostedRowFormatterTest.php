<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

class HostedRowFormatterTest extends \PHPUnit_Framework_TestCase {
    
    public function testFormatOrderRows() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(Item::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                ->setUnit("st")
            );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(500, $newRow->amount);
        $this->assertEquals(100, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }
    
    public function testFormatShippingFeeRows() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order
        ->addFee(Item::shippingFee()
                ->setShippingId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setUnit("st")
            );
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(500, $newRow->amount);
        $this->assertEquals(100, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }
    
    public function testFormatFixedDiscountRows() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(Item::orderRow()
                  ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addDiscount(Item::fixedDiscount()
                    ->setDiscountId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountIncVat(1)
                    );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[1];
        
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(-100, $newRow->amount);
        $this->assertEquals(-20, $newRow->vat);
    }
    
    public function testFormatRelativeDiscountRows() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(Item::orderRow()
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
        ->addDiscount(Item::relativeDiscount()
                ->setDiscountId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setDiscountPercent(10)
                );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[1];
        
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(-50, $newRow->amount);
        $this->assertEquals(-10, $newRow->vat);
    }
    
    public function testFormatTotalAmount() {
        $row = new HostedOrderRowBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $row->setAmount(100);
        $row->setQuantity(2);
        $rows = array();
        array_push($rows, $row);
        
        $formatter = new HostedRowFormatter();
        $this->assertEquals(200, $formatter->formatTotalAmount($rows));
    }
    
    public function testFormatTotalAmountNegative() {
        $row = new HostedOrderRowBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $row->setAmount(-100)
            ->setQuantity(2);
        $rows = array();
        array_push($rows, $row);
        
        $formatter = new HostedRowFormatter();
        $this->assertEquals(-200, $formatter->formatTotalAmount($rows));
    }
    
    public function testFormatTotalVat() {
        $row = new HostedOrderRowBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $row->setVat(100);
        $row->setQuantity(2);
        $rows = array();
        array_push($rows, $row);
        
        $formatter = new HostedRowFormatter();
        $this->assertEquals(200, $formatter->formatTotalVat($rows));
    }
    
    public function testFormatTotalVatNegative() {
        $row = new HostedOrderRowBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $row->setVat(-100);
        $row->setQuantity(2);
        $rows = array();
        array_push($rows, $row);
        
        $formatter = new HostedRowFormatter();
        $this->assertEquals(-200, $formatter->formatTotalVat($rows));
    }
}
