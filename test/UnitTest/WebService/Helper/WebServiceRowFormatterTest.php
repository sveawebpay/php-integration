<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';

class WebServiceRowFormatterTest extends PHPUnit_Framework_TestCase {

    public function test_convertIncVatToExVat() {
        
        $this->assertEquals( 8.00, Svea\WebServiceRowFormatter::convertIncVatToExVat( 10.00, 25 ) );
        $this->assertEquals( 69.99, Svea\WebServiceRowFormatter::convertIncVatToExVat( 69.99*1.25, 25 ) );

        $this->assertEquals( 0, Svea\WebServiceRowFormatter::convertIncVatToExVat( 0, 0 ) );
        $this->assertEquals( 1, Svea\WebServiceRowFormatter::convertIncVatToExVat( 1, 0 ) );
        $this->assertEquals( 0, Svea\WebServiceRowFormatter::convertIncVatToExVat( 0*1.25, 25 ) );
        
        $this->assertEquals( 100.00, Svea\WebServiceRowFormatter::convertIncVatToExVat( 100.00*1.06, 6 ) );
        $this->assertEquals( 100.00, Svea\WebServiceRowFormatter::convertIncVatToExVat( 100.00*1.0825, 8.25 ) );
    }
    
    public function test_convertExVatToIncVat() {
        
        $this->assertEquals( 10.00, Svea\WebServiceRowFormatter::convertExVatToIncVat( 8.00, 25 ) );
        $this->assertEquals( round(69.99*1.25,2,PHP_ROUND_HALF_EVEN), Svea\WebServiceRowFormatter::convertExVatToIncVat( 69.99, 25 ) );

        $this->assertEquals( 0, Svea\WebServiceRowFormatter::convertExVatToIncVat( 0, 0 ) );
        $this->assertEquals( 1, Svea\WebServiceRowFormatter::convertExVatToIncVat( 1, 0 ) );
        $this->assertEquals( 0, Svea\WebServiceRowFormatter::convertExVatToIncVat( 0, 25 ) );
        
        $this->assertEquals( 100.00*1.06, Svea\WebServiceRowFormatter::convertExVatToIncVat( 100.00, 6 ) );
        $this->assertEquals( round(100.00*1.0825,2,PHP_ROUND_HALF_EVEN), Svea\WebServiceRowFormatter::convertExVatToIncVat( 100.00, 8.25 ) );
    }
    
    public function test_FormatOrderRows_includes_all_attributes_in_formatted_rows() {
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
        $resultRows = $formatter->formatRows();
        $testedRow = $resultRows[0];

        $this->assertEquals("0", $testedRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $testedRow->Description);
        $this->assertEquals(4.0, $testedRow->PricePerUnit);
        $this->assertEquals(25.0, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("st", $testedRow->Unit);
    }

    // TODO write tests for the three different ways we can specify an order here (ex+vat, inc+vat, ex+inc)
    
    public function test_FormatShippingFeeRows_includes_all_attributes_in_formatted_rows() {
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
        $resultRows = $formatter->formatRows();
        $testedRow = $resultRows[0];

        $this->assertEquals("0", $testedRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $testedRow->Description);
        $this->assertEquals(4.0, $testedRow->PricePerUnit);
        $this->assertEquals(25.0, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("st", $testedRow->Unit);
    }
    
    // TODO write tests for the three different ways we can specify a shipping fee here (ex+vat, inc+vat, ex+inc?)
    
    public function test_FormatInvoiceFeeRows_includes_all_attributes_in_formatted_rows() {
        $order = WebPay::createOrder();
        $order->addFee(WebPayItem::invoiceFee()
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $resultRows = $formatter->formatRows();
        $testedRow = $resultRows[0];

        $this->assertEquals("", $testedRow->ArticleNumber);
        $this->assertEquals("Tess: Tester", $testedRow->Description);
        $this->assertEquals(4.0, $testedRow->PricePerUnit);
        $this->assertEquals(25.0, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("st", $testedRow->Unit);
    }
    
    // TODO write tests for the three different ways we can specify an invoice fee here (ex+vat, inc+vat, ex+inc?)
  
    // FixedDiscountRow specified using only amountExVat
    public function test_FixedDiscount_specified_using_amountExVat_in_order_with_single_vat_rate() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                // cover all three ways to specify items here: iv+vp, ev+vp, iv+ev
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(5.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setAmountIncVat(5.0)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("f1e")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setAmountExVat(1.0)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $resultRows = $formatter->formatRows();
        $testedRow = $resultRows[3];
        
        $this->assertEquals("f1e", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc", $testedRow->Description);
        $this->assertEquals(-1.0, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("st", $testedRow->Unit);
    }

    // FixedDiscountRow specified using only amountExVat => split discount excl. vat over the diffrent tax rates present in order
    public function test_FixedDiscount_specified_using_amountExVat_in_order_with_multiple_vat_rates() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @25% = 125")
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @6% = 106")
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("f100e")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setAmountExVat(100.00)
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $resultRows = $formatter->formatRows();
        
        // 100*200/300 = 66.66 ex. 25% vat => 83.33 vat as amount 
        $testedRow = $resultRows[2];
        $this->assertEquals("f100e", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc (25%)", $testedRow->Description);
        $this->assertEquals(-66.67, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);

        // 100*100/300 = 33.33 ex. 6% vat => 35.33 vat as amount 
        $testedRow = $resultRows[3];
        $this->assertEquals("f100e", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc (6%)", $testedRow->Description);
        $this->assertEquals(-round((100/300)*100,2,PHP_ROUND_HALF_EVEN), $testedRow->PricePerUnit);
        $this->assertEquals(6, $testedRow->VatPercent);
   }
    
    // FixedDiscountRow specified using only amountIncVat => split discount incl. vat over the diffrent tax rates present in order
    // function testFormatFixedDiscountRowsfromAmountExVatAndVatPercent_amountIncVat_WithSingleVatRatePresent() { // don't remove until java/dotnet packages are updated.
    public function test_FixedDiscount_specified_using_amountIncVat_in_order_with_single_vat_rate() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(5.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setAmountIncVat(5.0)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                    ->setDiscountId("f1i")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setAmountIncVat(1.0)
                    ->setUnit("kr")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $resultRows = $formatter->formatRows();
        $testedRow = $resultRows[3];
        $this->assertEquals("f1i", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc", $testedRow->Description);
        $this->assertEquals(-0.8, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("kr", $testedRow->Unit);
    }

    // FixedDiscountRow specified using only amountIncVat => split discount incl. vat over the diffrent tax rates present in order
    // if we have two orders items with different vat rate, we need to create
    // two discount order rows, one for each vat rate
    // funcation testFormatFixedDiscountRows_amountIncVat_WithDifferentVatRatesPresent() { // don't remove until java/dotnet packages are updated.
    public function test_FixedDiscount_specified_using_amountIncVat_in_order_with_multiple_vat_rates() {
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
                    ->setDiscountId("f100i")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setAmountIncVat(100)
                    ->setUnit("st")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $resultRows = $formatter->formatRows();
        
        // 100*250/356 = 70.22 incl. 25% vat => 14.04 vat as amount 
        $testedRow = $resultRows[2];
        $this->assertEquals("f100i", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc (25%)", $testedRow->Description);
        $this->assertEquals(-56.18, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);

        // 100*106/356 = 29.78 incl. 6% vat => 1.69 vat as amount 
        $testedRow = $resultRows[3];
        $this->assertEquals("f100i", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc (6%)", $testedRow->Description);
        $this->assertEquals(-28.09, $testedRow->PricePerUnit);
        $this->assertEquals(6, $testedRow->VatPercent);
   }
        
    // amountIncVat and vatPercent => add as single row with specified vat rate only
    public function test_FixedDiscount_specified_using_amountIncVat_and_vatPercent() {
//function testFormatFixedDiscountRows_amountIncVatAndVatPercent_WithDifferentVatRatesPresent() {    //old name, don't remove unit java & dotnet pkg updated
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
        $resultRows = $formatter->formatRows();
        
        // 100 @25% vat = -80 excl. vat
        $testedRow = $resultRows[2];
        $this->assertEquals("42", $testedRow->ArticleNumber);
        $this->assertEquals("->setAmountIncVat(111), ->vatPercent(25): testFormatFixedDiscountRows_amountIncVatAndVatPercent_WithDifferentVatRatesPresent", $testedRow->Description);
        $this->assertEquals(-88.80, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("st", $testedRow->Unit);
        
        $this->assertEquals( false, isset($resultRows[3]) );   // no more rows   
   }  
       
    // amountExVat and vatPercent => add as single row with specified vat rate only
    public function test_FixedDiscount_specified_using_amountExVat_and_vatPercent() {
//function testFormatFixedDiscountRows_amountExVatAndVatPercent_WithDifferentVatRatesPresent2() {    //old name, don't remove unit java & dotnet pkg updated
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
        $resultRows = $formatter->formatRows();
        
        $testedRow = $resultRows[2];
        $this->assertEquals("->setAmountExVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent2 (25%)", $testedRow->Description);
        $this->assertEquals(-66.67, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);

        $testedRow = $resultRows[3];
        $this->assertEquals("->setAmountExVat(100): testFormatFixedDiscountRowsWithDifferentVatRatesPresent2 (6%)", $testedRow->Description);
        $this->assertEquals(-33.33, $testedRow->PricePerUnit);
        $this->assertEquals(6, $testedRow->VatPercent);

        $this->assertEquals( false, isset($resultRows[4]) );   // no more rows   
    }  

    // specifying discounts with incVat+exVat is not supported -- public function test_FixedDiscount_specified_using_amountIncVat_and_amountExVat() { 
   
    public function test_RelativeDiscount_in_order_with_single_vat_rate() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(5.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setAmountIncVat(5.0)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("r10")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setDiscountPercent(10)
                    ->setUnit("kr")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $resultRows = $formatter->formatRows();
        $testedRow = $resultRows[3];

        $this->assertEquals("r10", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc", $testedRow->Description);
        $this->assertEquals(-1.2, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("kr", $testedRow->Unit);
    }
    
    public function test_RelativeDiscount_in_order_with_multiple_vat_rates() {
        $order = WebPay::createOrder();
        $order->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(12)
                ->setQuantity(1)
                )
                ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("r20")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setDiscountPercent(25)
                    ->setUnit("kr")
                );

        $formatter = new Svea\WebServiceRowFormatter($order);
        $resultRows = $formatter->formatRows();

        $testedRow = $resultRows[3];        
        $this->assertEquals("r20", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc (25%)", $testedRow->Description);
        $this->assertEquals(-25.00, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("kr", $testedRow->Unit);
        
        $testedRow = $resultRows[4];
        $this->assertEquals("r20", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc (12%)", $testedRow->Description);
        $this->assertEquals(-25.00, $testedRow->PricePerUnit);
        $this->assertEquals(12, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("kr", $testedRow->Unit);
        
        $testedRow = $resultRows[5];
        $this->assertEquals("r20", $testedRow->ArticleNumber);
        $this->assertEquals("couponName: couponDesc (6%)", $testedRow->Description);
        $this->assertEquals(-25.00, $testedRow->PricePerUnit);
        $this->assertEquals(6, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $testedRow->NumberOfUnits);
        $this->assertEquals("kr", $testedRow->Unit);
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
        $resultRows = $formatter->formatRows();
        
        $testedRow = $resultRows[2];
        $this->assertEquals("42", $testedRow->ArticleNumber);
        $this->assertEquals("->setDiscountPercent(10): testFormatRelativeDiscountRows_WithDifferentVatRatesPresent (25%)", $testedRow->Description);
        $this->assertEquals(-20.00, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $testedRow->NumberOfUnits);     // 1 "discount unit"
        $this->assertEquals("st", $testedRow->Unit);

        $testedRow = $resultRows[3];
        $this->assertEquals("42", $testedRow->ArticleNumber);
        $this->assertEquals("->setDiscountPercent(10): testFormatRelativeDiscountRows_WithDifferentVatRatesPresent (6%)", $testedRow->Description);
        $this->assertEquals(-10.00, $testedRow->PricePerUnit);
        $this->assertEquals(6, $testedRow->VatPercent);
        $this->assertEquals(0, $testedRow->DiscountPercent);   // not the same thing as in our WebPayItem...
        $this->assertEquals(1, $testedRow->NumberOfUnits);     // 1 "discount unit"
        $this->assertEquals("st", $testedRow->Unit);
   }
   
    /**
     * Regression test for float to int conversion error, where we lost accuracy
     * on straight cast of 25f (eg. 24.9999999964) to 24i
     * See also test in InvoicePaymentIntegrationTest.
     */
      public function test_regressiontest_for_WEB193() {
        
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
        $resultRows = $formatter->formatRows();
        
        $testedRow = $resultRows[0];
        $this->assertEquals(100, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);

        $testedRow = $resultRows[1];
        $this->assertEquals(0, $testedRow->PricePerUnit);
        $this->assertEquals(0, $testedRow->VatPercent);
        
        $testedRow = $resultRows[2];
        $this->assertEquals(23.20, $testedRow->PricePerUnit);
        $this->assertEquals(25, $testedRow->VatPercent);
    }     
}
