<?php
namespace Svea;

/**
 * @author Kristian Grossman-Madsen et al for Svea Webpay
 */

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

class HostedRowFormatterTest extends \PHPUnit_Framework_TestCase {
    
    //
    // VAT calculations
    
    // we calculate vat in three different ways requiring two out of three of amount inc vat, ex vat, vatpercent
    // case 1 ex vat, vat percent given
    public function testFormatOrderRows_VatCalculationFromAmountExVatAndVatPercentEquals25() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(4)
                ->setUnit("st")
            );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(4, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }
    
    public function testFormatOrderRows_VatCalculationFromAmountExVatAndVatPercentEquals6() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(69.99)
                ->setVatPercent(6)
                ->setQuantity(1)
                ->setUnit("st")
            );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(7419, $newRow->amount);
        $this->assertEquals(420, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }
    
    // case 2 inc vat, vat percent given
    public function testFormatOrderRows_VatCalculationFromAmountIncVatAndVatPercent() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountIncVat(87.49)
                ->setVatPercent(25)
                ->setQuantity(4)
                ->setUnit("st")
            );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(4, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }
    // case 3 ex vat, inc vat
    public function testFormatOrderRows_VatCalculationFromAmountExVatAndAmountIncVatAndVatPercent() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(69.99)
                ->setAmountIncVat(87.49)
                ->setQuantity(4)
                ->setUnit("st")
            );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(4, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }
 
    // case 4 all three given
    public function testFormatOrderRows_VatCalculationFromAllThreeOfAmountExVatAndAmountIncVatAndVatPercent() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(69.99)
                ->setAmountIncVat(87.49)
                ->setVatPercent(25)
                ->setQuantity(4)
                ->setUnit("st")
            );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(4, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }   

    //
    // order row and item composition
    public function testFormatOrderRows_SingleRowWithSingleItem() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4.0)
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

    public function testFormatOrderRows_SingleRowWithMultipleItems() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(4)
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
        $this->assertEquals(4, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }
        
    // 69,99 kr excl. 25% moms => 87,4875 kr including 17,4975 kr moms, expressed as öre
    public function testFormatOrderRows_SingleRowSingleItemWithFractionalPrice() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(69.99)
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
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }

    // TODO also to integration tests, check total amount charged!
    public function testFormatOrderRows_SingleRowMultipleItemsWithFractionalPrice() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(4)
                ->setUnit("st")
            );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(4, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }
    
    public function testFormatOrderRows_SingleRowSingleItemWithNoVat()    {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4.0)
                ->setVatPercent(0)
                ->setQuantity(1)
                ->setUnit("st")
            );
        
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(400, $newRow->amount);
        $this->assertEquals(0, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);            
    }
       
    // MultipleOrderRows
    public function testFormatOrderRows_MultipleRowsOfMultipleItemsWithSameVatRate() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(4)
                ->setUnit("st")
            )
            ->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4.0)
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
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(4, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
        
        $newRow = $newRows[1];
        $this->assertEquals("1", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(500, $newRow->amount);
        $this->assertEquals(100, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }

        public function testFormatOrderRows_MultipleRowsOfMultipleItemsWithDifferentVatRate() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(69.99)
                ->setVatPercent(25)
                ->setQuantity(4)
                ->setUnit("st")
            )
            ->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(10.0)
                ->setVatPercent(6)
                ->setQuantity(1)
                ->setUnit("st")
            );
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(4, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
        
        $newRow = $newRows[1];
        $this->assertEquals("1", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(1060, $newRow->amount);
        $this->assertEquals(60, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }

    public function testFormatShippingFeeRows() {
        $order = new createOrderBuilder(new SveaConfigurationProvider(SveaConfig::getDefaultConfig()));
        $order
        ->addFee(\WebPayItem::shippingFee()
                ->setShippingId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4.0)
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
        addOrderRow(\WebPayItem::orderRow()
                  ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addDiscount(\WebPayItem::fixedDiscount()
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
        addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
        ->addDiscount(\WebPayItem::relativeDiscount()
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
