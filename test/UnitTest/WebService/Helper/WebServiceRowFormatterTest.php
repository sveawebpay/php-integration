<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';

class WebServiceRowFormatterTest extends PHPUnit_Framework_TestCase {

    public function testFormatOrderRows() {
        $order = WebPay::createOrder();
        $order->addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
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
        $order->addFee(WebPayItem::shippingFee()
                    ->setShippingId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountExVat(4)
                    ->setVatPercent(25)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
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
        $order->addFee(WebPayItem::invoiceFee()
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
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

    public function testFormatFixedDiscountRows_fromExIncOrderRow_amountIncVat_WithSingleVatRatePresent() {

        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setAmountIncVat(5.0)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountIncVat(1.0)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
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
    
    public function testFormatFixedDiscountRows_fromAmountIncVatAndVatPercent_amountIncVat_WithSingleVatRatePresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(5.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountIncVat(1.0)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
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
    
    // only amountIncVat => calculate mean vat split into diffrent tax rates present
    public function testFormatFixedDiscountRowsfromAmountExVatAndVatPercent_amountIncVat_WithSingleVatRatePresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountIncVat(1.0)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
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

    // only amountIncVat => calculate mean vat split into diffrent tax rates present
    // if we have two orders items with different vat rate, we need to create
    // two discount order rows, one for each vat rate
    public function testFormatFixedDiscountRows_amountIncVat_WithDifferentVatRatesPresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("42")
                    ->setName("->setAmountIncVat(100)")
                    ->setDescription("testFormatFixedDiscountRowsWithDifferentVatRatesPresent")
                    ->setAmountIncVat(100)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        
        // 100*250/356 = 70.22 incl. 25% vat => 14.04 vat as amount 
        $newRow = $newRows[2];
        $this->assertEquals("42", $newRow->ArticleNumber);
        $this->assertEquals("->setAmountIncVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent (25%)", $newRow->Description);
        $this->assertEquals(-56.18, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);

        // 100*106/356 = 29.78 incl. 6% vat => 1.69 vat as amount 
        $newRow = $newRows[3];
        $this->assertEquals("42", $newRow->ArticleNumber);
        $this->assertEquals("->setAmountIncVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent (6%)", $newRow->Description);
        $this->assertEquals(-28.09, $newRow->PricePerUnit);
        $this->assertEquals(6, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
   }
   
       // only amountIncVat => calculate mean vat split into diffrent tax rates present
    // if we have two orders items with different vat rate, we need to create
    // two discount order rows, one for each vat rate
    public function testFormatFixedDiscountRows_MixedItemVatSpec_amountIncVat_WithDifferentVatRatesPresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setAmountIncVat(125.00)
                ->setQuantity(2)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(106.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("42")
                    ->setName("->setAmountIncVat(100)")
                    ->setDescription("testFormatFixedDiscountRowsWithDifferentVatRatesPresent")
                    ->setAmountIncVat(100)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        
        // 100*250/356 = 70.22 incl. 25% vat => 14.04 vat as amount 
        $newRow = $newRows[2];
        $this->assertEquals("42", $newRow->ArticleNumber);
        $this->assertEquals("->setAmountIncVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent (25%)", $newRow->Description);
        $this->assertEquals(-56.18, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);

        // 100*106/356 = 29.78 incl. 6% vat => 1.69 vat as amount 
        $newRow = $newRows[3];
        $this->assertEquals("42", $newRow->ArticleNumber);
        $this->assertEquals("->setAmountIncVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent (6%)", $newRow->Description);
        $this->assertEquals(-28.09, $newRow->PricePerUnit);
        $this->assertEquals(6, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
   }

   
   // amountIncVat and vatPercent => add as one row with specified vat rate only
   public function testFormatFixedDiscountRows_amountIncVatAndVatPercent_WithSingleVatRatePresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("0")
                    ->setName("->setAmountExVat(4.0), ->setVatPercent(25)")
                    ->setDescription("testFormatFixedDiscountRows_amountIncVatAndVatPercent_WithSingleVatRatePresent")
                    ->setAmountIncVat(1.0)
                    ->setVatPercent(25)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[1];
        $this->assertEquals("0", $newRow->ArticleNumber);
        $this->assertEquals("->setAmountExVat(4.0), ->setVatPercent(25): testFormatFixedDiscountRows_amountIncVatAndVatPercent_WithSingleVatRatePresent", $newRow->Description);
        $this->assertEquals(-0.8, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }   
    
    // amountIncVat and vatPercent => add as one row with specified vat rate only
    public function testFormatFixedDiscountRows_amountIncVatAndVatPercent_WithDifferentVatRatesPresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("42")
                    ->setName("->setAmountIncVat(111), ->vatPercent(25)")
                    ->setDescription("testFormatFixedDiscountRows_amountIncVatAndVatPercent_WithDifferentVatRatesPresent")
                    ->setAmountIncVat(111)
                    ->setVatPercent(25)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        
        // 100 @25% vat = -80 excl. vat
        $newRow = $newRows[2];
        $this->assertEquals("42", $newRow->ArticleNumber);
        $this->assertEquals("->setAmountIncVat(111), ->vatPercent(25): testFormatFixedDiscountRows_amountIncVatAndVatPercent_WithDifferentVatRatesPresent", $newRow->Description);
        $this->assertEquals(-88.80, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
   }  

   // amountExVat and vatPercent => add as one row with specified vat rate only
   public function testFormatFixedDiscountRows_amountExVatAndVatPercent_WithSingleVatRatePresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountExVat(1.0)
                    ->setVatPercent(25)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[1];
        $this->assertEquals("0", $newRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $newRow->Description);
        $this->assertEquals(-1.0, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }   
       
    // amountExVat and vatPercent => add as one row with specified vat rate only
    public function testFormatFixedDiscountRows_amountExVatAndVatPercent_WithDifferentVatRatesPresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("42")
                    ->setName("->setAmountExVat(100)")
                    ->setDescription("testFormatFixedDiscountRowsWithDifferentVatRatesPresent")
                    ->setAmountExVat(111)
                    ->setVatPercent(25)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        
        // 100 @25% vat = -80 excl. vat
        $newRow = $newRows[2];
        $this->assertEquals("42", $newRow->ArticleNumber);
        $this->assertEquals("->setAmountExVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent", $newRow->Description);
        $this->assertEquals(-111.00, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
   }  

   // amountExVat and vatPercent => add as one row with specified vat rate only
    public function testFormatFixedDiscountRows_amountExVatAndVatPercent_WithDifferentVatRatesPresent2() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setName("->setAmountExVat(100)")
                    ->setDescription("testFormatFixedDiscountRowsWithDifferentVatRatesPresent2 (25%)")
                    ->setAmountExVat(66.67)
                    ->setVatPercent(25)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setName("->setAmountExVat(100)")
                    ->setDescription("testFormatFixedDiscountRowsWithDifferentVatRatesPresent2 (6%)")
                    ->setAmountExVat(33.33)
                    ->setVatPercent(6)
                );
                
        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        
        $newRow = $newRows[2];
        $this->assertEquals("->setAmountExVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent2 (25%)", $newRow->Description);
        $this->assertEquals(-66.67, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[3];
        $this->assertEquals("->setAmountExVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent2 (6%)", $newRow->Description);
        $this->assertEquals(-33.33, $newRow->PricePerUnit);
        $this->assertEquals(6, $newRow->VatPercent);
    }  
       
    public function testFormatRelativeDiscountRows() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setDiscountPercent(10)
                ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[1];

        $this->assertEquals("0", $newRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $newRow->Description);
        $this->assertEquals(-0.4, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }
    
    public function testFormatRelativeDiscountRows_WithSingleVatRatePresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(12)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("0")
                    ->setName("->setDiscountPercent(20)")
                    ->setDescription("testFormatRelativeDiscountRows_WithSingleVatRatePresent")
                    ->setDiscountPercent(20)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        $newRow = $newRows[1];
        $this->assertEquals("0", $newRow->ArticleNumber);
        $this->assertEquals("->setDiscountPercent(20): testFormatRelativeDiscountRows_WithSingleVatRatePresent", $newRow->Description);
        $this->assertEquals(-20.00, $newRow->PricePerUnit);
        $this->assertEquals(12, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $newRow->NumberOfUnits);
        $this->assertEquals("st", $newRow->Unit);
    }

    // if we have two orders items with different vat rate, we need to create
    // two discount order rows, one for each vat rate
    public function testFormatRelativeDiscountRows_WithDifferentVatRatesPresent() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("42")
                    ->setName("->setDiscountPercent(10)")
                    ->setDescription("testFormatRelativeDiscountRows_WithDifferentVatRatesPresent")
                    ->setDiscountPercent(10)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        
        $newRow = $newRows[2];
        $this->assertEquals("42", $newRow->ArticleNumber);
        $this->assertEquals("->setDiscountPercent(10): testFormatRelativeDiscountRows_WithDifferentVatRatesPresent (25%)", $newRow->Description);
        $this->assertEquals(-20.00, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $newRow->NumberOfUnits);     // 1 "discount unit"
        $this->assertEquals("st", $newRow->Unit);

        $newRow = $newRows[3];
        $this->assertEquals("42", $newRow->ArticleNumber);
        $this->assertEquals("->setDiscountPercent(10): testFormatRelativeDiscountRows_WithDifferentVatRatesPresent (6%)", $newRow->Description);
        $this->assertEquals(-10.00, $newRow->PricePerUnit);
        $this->assertEquals(6, $newRow->VatPercent);
        $this->assertEquals(0, $newRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $newRow->NumberOfUnits);     // 1 "discount unit"
        $this->assertEquals("st", $newRow->Unit);
   }
   
    /**
     * make sure opencart bug w/corporate invoice payments for one 25% vat product with free shipping (0% vat) 
     * resulting in request with illegal vat rows of 24% not originating in integration package, also see 
     * InvoicePaymentIntegrationTest
     */
    public function test_InvoiceWithFreeShippingAndCorporateCustomer_begetsWeirdVATInRequestInOpenCart() {
        
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::shippingFee()
                ->setAmountExVat(0.00)
                ->setVatPercent(0)
                )
                ->addOrderRow(WebPayItem::invoiceFee()
                ->setAmountExVat(23.20)
                ->setVatPercent(25)
                );
        
        $formatter = new Svea\WebServiceRowFormatter($order);
        $newRows = $formatter->formatRows();
        
        $newRow = $newRows[0];
        $this->assertEquals(100, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[1];
        $this->assertEquals(0, $newRow->PricePerUnit);
        $this->assertEquals(0, $newRow->VatPercent);
        
        $newRow = $newRows[2];
        $this->assertEquals(23.20, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);
    }     
}
