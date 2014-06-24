<?php

use Svea\HostedService\HostedRowFormatter as HostedRowFormatter;
use Svea\CreateOrderBuilder as createOrderBuilder;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen et al for Svea Webpay
 */
class HostedRowFormatterTest extends \PHPUnit_Framework_TestCase {

    private $order;
    
    protected function SetUp() {
        $this->order = \WebPay::createOrder(Svea\SveaConfig::getDefaultConfig());
    }
    
    //
    // VAT calculations

    // we calculate vat in three different ways requiring two out of three of amount inc vat, ex vat, vatpercent
    // case 1 ex vat, vat percent given
    public function testFormatOrderRows_VatCalculationFromAmountExVatAndVatPercentEquals25() {
        $this->order->
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
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
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
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
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
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
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
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
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
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
        addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                ->setUnit("st")
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(4)
                ->setUnit("st")
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
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
        $newRows = $formatter->formatRows($this->order);
        $newRow = $newRows[0];
        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(8749, $newRow->amount);
        $this->assertEquals(1750, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);
    }

    public function testFormatOrderRows_SingleRowMultipleItemsWithFractionalPrice() {
        $this->order->
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
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
            addOrderRow(\WebPayItem::orderRow()
                ->setArticleNumber("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(0)
                ->setQuantity(1)
                ->setUnit("st")
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
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
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
                ->setUnit("st")
            );
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($this->order);
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
        $this->order->
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
                ->setAmountExVat(10)
                ->setVatPercent(6)
                ->setQuantity(1)
                ->setUnit("st")
            );
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($this->order);
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
        $this->order
            ->addFee(\WebPayItem::shippingFee()
                ->setShippingId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setUnit("st")
            );
        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($this->order);
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
        $this->order
            ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
            )
            ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("0")
                    ->setName("Tess")
                    ->setDescription("Tester")
                    ->setAmountIncVat(1)
            )
        ;

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($this->order);
        $newRow = $newRows[1];

        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(-100, $newRow->amount);
        $this->assertEquals(-20, $newRow->vat);
    }

    public function testFormatRelativeDiscountRows() {
        $this->order->
        addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(4)
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
        $newRows = $formatter->formatRows($this->order);
        $newRow = $newRows[1];

        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(-50, $newRow->amount);
        $this->assertEquals(-10, $newRow->vat);
    }

    public function testFormatTotalAmount() {
        $this->order->
        addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100)
                ->setVatPercent(0)
                ->setQuantity(2)
                );

        $formatter = new HostedRowFormatter();
        $rows = $formatter->formatRows($this->order);
        $this->assertEquals(20000, $formatter->formatTotalAmount($rows));
    }

    public function testFormatTotalAmountNegative() {
        $this->order->
        addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(-100)
                ->setVatPercent(0)
                ->setQuantity(2)
                );
        $formatter = new HostedRowFormatter();
        $rows = $formatter->formatRows($this->order);
        $this->assertEquals(-20000, $formatter->formatTotalAmount($rows));
    }

    public function testFormatTotalVat() {
        $this->order->
        addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100)
                ->setVatPercent(100)
                ->setQuantity(2)
                );

        $formatter = new HostedRowFormatter();
        $rows = $formatter->formatRows($this->order);
        $this->assertEquals(20000, $formatter->formatTotalVat($rows));
    }

    public function testFormatTotalVatNegative() {
        $this->order->
        addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(-100)
                ->setVatPercent(100)
                ->setQuantity(2)
                );

        $formatter = new HostedRowFormatter();
        $rows = $formatter->formatRows($this->order);
        $this->assertEquals(-20000, $formatter->formatTotalVat($rows));
    }

    // FixedDiscountRow specified using only amountExVat
    public function test_FixedDiscount_specified_using_amountExVat_in_order_with_single_vat_rate() {
        $this->order->addOrderRow(\WebPayItem::orderRow()
                // cover all three ways to specify items here: iv+vp, ev+vp, iv+ev
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountIncVat(5.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setAmountIncVat(5.0)
                ->setQuantity(1)
                )
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("f1e")
                    ->setName("couponName")
                    ->setDescription("couponName")
                    ->setAmountExVat(1.0)
                    ->setUnit("st")
                );

        $formatter = new HostedRowFormatter($this->order);
        $newRows = $formatter->formatRows($this->order);
        $newRow = $newRows[3];

        // 1.0 kr @25% => -125 öre discount, of which -25 öre is vat
        $this->assertEquals("f1e", $newRow->sku);
        $this->assertEquals("couponName", $newRow->name);
        $this->assertEquals("couponName", $newRow->description);
        $this->assertEquals(-125, $newRow->amount);
        $this->assertEquals(-25, $newRow->vat);
    }

    // FixedDiscountRow specified using only amountExVat => split discount excl. vat over the diffrent tax rates present in order
    public function test_FixedDiscount_specified_using_amountExVat_in_order_with_multiple_vat_rates() {
        $this->order->addOrderRow(\WebPayItem::orderRow()
                ->setName("product with price 100 @25% = 125")
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setName("product with price 100 @6% = 106")
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("f100e")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setAmountExVat(100.00)
                );

        $formatter = new HostedRowFormatter($this->order);
        $resultRows = $formatter->formatRows($this->order);

        // 100*200/300 = 66.67 ex. 25% vat => 83.34 vat as amount of which 16.67 is vat
        // 100*100/300 = 33.33 ex. 6% vat => 35.33 vat as amount  2.00 is vat
        // In one discount row -11867 öre, of which 1867 is vat
        $testedRow = $resultRows[2];
        $this->assertEquals("f100e", $testedRow->sku);
        $this->assertEquals("couponName", $testedRow->name);
        $this->assertEquals("couponDesc", $testedRow->description);
        $this->assertEquals(-11867, $testedRow->amount);
        $this->assertEquals(-1867, $testedRow->vat);

   }

    // FixedDiscountRow specified using only amountIncVat => split discount incl. vat over the diffrent tax rates present in order
    public function test_FixedDiscount_specified_using_amountIncVat_in_order_with_single_vat_rate() {
        $this->order->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountIncVat(5.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setAmountIncVat(5.0)
                ->setQuantity(1)
                )
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("f1i")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setAmountIncVat(1.0)
                    ->setUnit("kr")
                );
        $formatter = new HostedRowFormatter($this->order);
        $resultRows = $formatter->formatRows($this->order);

        // 1.0 kr @25% => -100 öre discount, of which -20 öre is vat
        $testedRow = $resultRows[3];
        $this->assertEquals("f1i", $testedRow->sku);
        $this->assertEquals("couponName", $testedRow->name);
        $this->assertEquals("couponDesc", $testedRow->description);
        $this->assertEquals(-100, $testedRow->amount);
        $this->assertEquals(-20, $testedRow->vat);
    }

    // FixedDiscountRow specified using only amountIncVat => split discount incl. vat over the diffrent tax rates present in order
    // if we have two orders items with different vat rate, we need to create
    // two discount order rows, one for each vat rate
    // funcation testFormatFixedDiscountRows_amountIncVat_WithDifferentVatRatesPresent() { // don't remove until java/dotnet packages are updated.
    public function test_FixedDiscount_specified_using_amountIncVat_in_order_with_multiple_vat_rates() {
        $this->order->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1)
                )
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("f100i")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setAmountIncVat(100)
                    ->setUnit("st")
                );

        $formatter = new HostedRowFormatter($this->order);
        $resultRows = $formatter->formatRows($this->order);

        // 100*250/356 = -7022 öre discount, of which -1404 öre is vat
        // 100*106/356 = -2978 öre discount, of which -169 öre is vat
        // HostedRowBuilder only creates a single discount row
        $testedRow = $resultRows[2];
        $this->assertEquals("f100i", $testedRow->sku);
        $this->assertEquals("couponName", $testedRow->name);
        $this->assertEquals("couponDesc", $testedRow->description);
        $this->assertEquals(-10000, $testedRow->amount);
        $this->assertEquals(-1573, $testedRow->vat);

   }

    // FixedDiscount should only look at vat rates from order item rows, not shipping or invoice fees
    public function test_FixedDiscount_specified_using_amountIncVat_are_calculated_from_order_item_rows_only() {
        $this->order->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2)
                )
                ->addFee(\WebPayItem::shippingFee()
                ->setAmountExVat(50.00)
                ->setVatPercent(6)
                )
//                ->addFee(\WebPayItem::invoiceFee()    // Invoice fees are not processed by HostedRowFormatter
//                ->setAmountExVat(23.20)
//                ->setVatPercent(25)
//                )
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("f100i")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setAmountIncVat(100)
                    ->setUnit("kr")
                );

        $formatter = new HostedRowFormatter($this->order);
        $resultRows = $formatter->formatRows($this->order);

        // 100*250/250 = 100 discount incl. 25% vat => 20 discount vat as amount
        $testedRow = $resultRows[2];
        $this->assertEquals("f100i", $testedRow->sku);
        $this->assertEquals("couponName", $testedRow->name);
        $this->assertEquals("couponDesc", $testedRow->description);
        $this->assertEquals(-10000, $testedRow->amount);
        $this->assertEquals(-2000, $testedRow->vat);

        // order total should be 250-100+53+29 = 232 kr
//        $total = HostedRowFormatter::convertExVatToIncVat( $resultRows[0]->PricePerUnit, $resultRows[0]->VatPercent ) * $resultRows[0]->NumberOfUnits +
//                HostedRowFormatter::convertExVatToIncVat( $resultRows[1]->PricePerUnit, $resultRows[1]->VatPercent )  * $resultRows[1]->NumberOfUnits +
//                HostedRowFormatter::convertExVatToIncVat( $resultRows[2]->PricePerUnit, $resultRows[2]->VatPercent )  * $resultRows[2]->NumberOfUnits +
//                HostedRowFormatter::convertExVatToIncVat( $resultRows[3]->PricePerUnit, $resultRows[3]->VatPercent ) * $resultRows[3]->NumberOfUnits;
//        $this->assertEquals(232.00, $total);
//
   }

//  TODO port over this test as well from WebServiceRowFormatterTest
    public function test_FixedDiscount_specified_using_amountExVat_are_calculated_from_order_item_rows_only() {}

//  TODO port over this test as well from WebServiceRowFormatterTest
    public function test_FixedDiscount_specified_using_amountExVat_are_calculated_from_order_item_rows_only_multiple_vat_rates() {}

//  TODO port over this test as well from WebServiceRowFormatterTest
    public function test_FixedDiscount_specified_using_amountIncVat_are_calculated_from_order_item_rows_only_multiple_vat_rates() {}

//  TODO port over this test as well from WebServiceRowFormatterTest
    public function test_FixedDiscount_specified_using_amountIncVat_and_vatPercent() {}

//  TODO port over this test as well from WebServiceRowFormatterTest
    public function test_FixedDiscount_specified_using_amountExVat_and_vatPercent() {}

    public function test_RelativeDiscount_in_order_with_single_vat_rate() {
        $this->order->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountIncVat(5.0)
                ->setVatPercent(25)
                ->setQuantity(1)
                )
                ->addOrderRow(\WebPayItem::orderRow()
                ->setAmountExVat(4.0)
                ->setAmountIncVat(5.0)
                ->setQuantity(1)
                )
                ->addDiscount(\WebPayItem::relativeDiscount()
                    ->setDiscountId("r10")
                    ->setName("couponName")
                    ->setDescription("couponDesc")
                    ->setDiscountPercent(10)
                    ->setUnit("kr")
                );

        $formatter = new HostedRowFormatter();
        $resultRows = $formatter->formatRows($this->order);
        $testedRow = $resultRows[3];

        $this->assertEquals("r10", $testedRow->sku);
        $this->assertEquals("couponName", $testedRow->name);
        $this->assertEquals("couponDesc", $testedRow->description);
        $this->assertEquals(-150, $testedRow->amount);
        $this->assertEquals(-30, $testedRow->vat);
    }

//  TODO port over this test as well from WebServiceRowFormatterTest
    public function test_RelativeDiscount_in_order_with_multiple_vat_rates() {}

}
