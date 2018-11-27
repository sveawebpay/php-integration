<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\Helper;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;
use Svea\WebPay\HostedService\Helper\HostedRowFormatter as HostedRowFormatter;


/**
 * @author Kristian Grossman-Madsen et al for Svea Webpay
 */
class HostedRowFormatterTest extends \PHPUnit\Framework\TestCase
{

    private $order;

    protected function SetUp()
    {
        $this->order = WebPay::createOrder(ConfigurationService::getDefaultConfig());
    }

    //
    // VAT calculations

    // we calculate vat in three different ways requiring two out of three of amount inc vat, ex vat, vatpercent
    // case 1 ex vat, vat percent given
    public function testFormatOrderRows_VatCalculationFromAmountExVatAndVatPercentEquals25()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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

    public function testFormatOrderRows_VatCalculationFromAmountExVatAndVatPercentEquals6()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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
    public function testFormatOrderRows_VatCalculationFromAmountIncVatAndVatPercent()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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
    public function testFormatOrderRows_VatCalculationFromAmountExVatAndAmountIncVatAndVatPercent()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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
    public function testFormatOrderRows_VatCalculationFromAllThreeOfAmountExVatAndAmountIncVatAndVatPercent()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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
    public function testFormatOrderRows_SingleRowWithSingleItem()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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

    public function testFormatOrderRows_SingleRowWithMultipleItems()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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
    public function testFormatOrderRows_SingleRowSingleItemWithFractionalPrice()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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

    public function testFormatOrderRows_SingleRowMultipleItemsWithFractionalPrice()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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

    public function testFormatOrderRows_SingleRowSingleItemWithNoVat()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
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
    public function testFormatOrderRows_MultipleRowsOfMultipleItemsWithSameVatRate()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("0")
            ->setName("Tess")
            ->setDescription("Tester")
            ->setAmountExVat(69.99)
            ->setVatPercent(25)
            ->setQuantity(4)
            ->setUnit("st")
        )
            ->
            addOrderRow(WebPayItem::orderRow()
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

    public function testFormatOrderRows_MultipleRowsOfMultipleItemsWithDifferentVatRate()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("0")
            ->setName("Tess")
            ->setDescription("Tester")
            ->setAmountExVat(69.99)
            ->setVatPercent(25)
            ->setQuantity(4)
            ->setUnit("st")
        )
            ->
            addOrderRow(WebPayItem::orderRow()
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

    public function testFormatShippingFeeRows()
    {
        $this->order
            ->addFee(WebPayItem::shippingFee()
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

    public function testFormatFixedDiscountRows()
    {
        $this->order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4)
                ->setVatPercent(25)
                ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("0")
                ->setName("Tess")
                ->setDescription("Tester")
                ->setAmountIncVat(1)
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($this->order);
        $newRow = $newRows[1];

        $this->assertEquals("0", $newRow->sku);
        $this->assertEquals("Tess", $newRow->name);
        $this->assertEquals("Tester", $newRow->description);
        $this->assertEquals(-100, $newRow->amount);
        $this->assertEquals(-20, $newRow->vat);
    }

    public function testFormatRelativeDiscountRows()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(4)
            ->setVatPercent(25)
            ->setQuantity(1)
        )
            ->addDiscount(WebPayItem::relativeDiscount()
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

    public function testFormatTotalAmount()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100)
            ->setVatPercent(0)
            ->setQuantity(2)
        );

        $formatter = new HostedRowFormatter();
        $rows = $formatter->formatRows($this->order);
        $this->assertEquals(20000, $formatter->formatTotalAmount($rows));
    }

    public function testFormatTotalAmountNegative()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(-100)
            ->setVatPercent(0)
            ->setQuantity(2)
        );
        $formatter = new HostedRowFormatter();
        $rows = $formatter->formatRows($this->order);
        $this->assertEquals(-20000, $formatter->formatTotalAmount($rows));
    }

    public function testFormatTotalVat()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100)
            ->setVatPercent(100)
            ->setQuantity(2)
        );

        $formatter = new HostedRowFormatter();
        $rows = $formatter->formatRows($this->order);
        $this->assertEquals(20000, $formatter->formatTotalVat($rows));
    }

    public function testFormatTotalVatNegative()
    {
        $this->order->
        addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(-100)
            ->setVatPercent(100)
            ->setQuantity(2)
        );

        $formatter = new HostedRowFormatter();
        $rows = $formatter->formatRows($this->order);
        $this->assertEquals(-20000, $formatter->formatTotalVat($rows));
    }

// ------------------------

    // TODO! move these to Svea\WebPay\Test\UnitTest\WebService\Helper\WebServiceRowFormatterTest as well (as in java package
    // ported over tests of discounts from WebserviceRowFormatterTest 

    /// fixed discount
    // iff no specified vatPercent => split discount excl. vat over the diffrent tax rates present in order
    public function test_FixedDiscount_specified_using_amountExVat_in_order_with_single_vat_rate()
    {
        $this->order->addOrderRow(WebPayItem::orderRow()
            // cover all three ways to specify items here: iv+vp, ev+vp, iv+ev
            ->setAmountExVat(4.0)
            ->setVatPercent(25)
            ->setQuantity(1)
        )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("f1e")
                ->setName("couponName")
                ->setDescription("couponName")
                ->setAmountExVat(1.0)
                ->setUnit("st")
            );

        $formatter = new HostedRowFormatter($this->order);
        $newRows = $formatter->formatRows($this->order);
        $newRow = $newRows[1];

        // 1.0 kr @25% => -125 öre discount, of which -25 öre is vat
        $this->assertEquals("f1e", $newRow->sku);
        $this->assertEquals("couponName", $newRow->name);
        $this->assertEquals("couponName", $newRow->description);
        $this->assertEquals(-125, $newRow->amount);
        $this->assertEquals(-25, $newRow->vat);

        $paymentForm = $this->order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        // 5.00 (1.00) - 1.25 (.25) = 3.75 (.75)
        $this->assertRegexp('/<amount>375<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>75<\/vat>/', $paymentXml);
    }

    public function test_FixedDiscount_specified_using_amountExVat_in_order_with_multiple_vat_rates()
    {
        $this->order->addOrderRow(WebPayItem::orderRow()
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

        $formatter = new HostedRowFormatter($this->order);
        $newRows = $formatter->formatRows($this->order);
        $testedRow = $newRows[2];

        // 100*200/300 = 66.67 ex. 25% vat => 83.34 vat as amount of which 16.67 is vat
        // 100*100/300 = 33.33 ex. 6% vat => 35.33 vat as amount  2.00 is vat
        // In one discount row -11867 öre, of which 1867 is vat
        $this->assertEquals("f100e", $testedRow->sku);
        $this->assertEquals("couponName", $testedRow->name);
        $this->assertEquals("couponDesc", $testedRow->description);
        $this->assertEquals(-11867, $testedRow->amount);
        $this->assertEquals(-1867, $testedRow->vat);

        $paymentForm = $this->order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        //	newRows.get(0).PricePerUnit * newRows.get(0).NumberOfUnits  +	// 250.00
        //	newRows.get(1).PricePerUnit * newRows.get(1).NumberOfUnits  +	// 106.00
        //	newRows.get(2).PricePerUnit * newRows.get(2).NumberOfUnits  +	// -83.34
        //	newRows.get(3).PricePerUnit * newRows.get(3).NumberOfUnits 		// -35.33
        //assertEquals( 237.33, Double.valueOf(String.format(Locale.ENGLISH,"%.2f",total)), 0.001 );		    
        $this->assertRegexp('/<amount>23733<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>3733<\/vat>/', $paymentXml);
    }

    // iff no specified vatPercent => split discount incl. vat over the diffrent tax rates present in order
    // public void test_FixedDiscount_specified_using_amountIncVat_in_order_with_single_vat_rate() {
    public function test_FixedDiscount_specified_using_amountIncVat_in_order_with_single_vat_rate()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(4.00)
                ->setVatPercent(25)
                ->setQuantity(1.0)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("f1e")
                ->setName("couponName")
                ->setDescription("couponDesc")
                ->setAmountIncVat(1.00)
                ->setUnit("st")
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // validate HostedOrderRowBuilder row contents
        $newRow = $newRows[1];
        $this->assertEquals("f1e", $newRow->sku);
        $this->assertEquals("couponName", $newRow->name);
        $this->assertEquals("couponDesc", $newRow->description);
        $this->assertEquals(-100, $newRow->amount);
        $this->assertEquals(-20, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        // 5.00 (1.00) - 1.00 (.20) = 4.00 (1.00)
        $this->assertRegexp('/<amount>400<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>100<\/vat>/', $paymentXml);
    }

    public function test_FixedDiscount_specified_using_amountIncVat_in_order_with_multiple_vat_rates()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @25% = 125")
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @6% = 106")
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1.0)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("f100e")
                ->setName("couponName")
                ->setDescription("couponDesc")
                ->setAmountIncVat(100.00)
                ->setUnit("st")
            );


        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 100*250/356 = 70.22 incl. 25% vat => 14.04 vat as amount 
        // 100*106/356 = 29.78 incl. 6% vat => 1.69 vat as amount 
        // => total discount is 100.00 (incl 15.73 vat)
        $newRow = $newRows[2];
        $this->assertEquals("f100e", $newRow->sku);
        $this->assertEquals("couponName", $newRow->name);
        $this->assertEquals("couponDesc", $newRow->description);
        $this->assertEquals(-10000, $newRow->amount);
        $this->assertEquals(-1573, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        //	newRows.get(0).PricePerUnit * newRows.get(0).NumberOfUnits  +	// 250.00
        //	newRows.get(1).PricePerUnit * newRows.get(1).NumberOfUnits  +	// 106.00
        //	newRows.get(2).PricePerUnit * newRows.get(2).NumberOfUnits  +	// -70.22 (14.04) + 29.78 (1.69) = -100.00 (-15.73)
        $this->assertRegexp('/<amount>25600<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>4027<\/vat>/', $paymentXml);
    }

    // iff specified vatPercent => add as single row with specified vat rate only honouring specified amount and vatPercent 
    public function test_FixedDiscount_specified_using_IncVat_and_vatPercent_is_added_as_single_discount_row()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1.0)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(111.00)
                ->setVatPercent(25)
            );


        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // -111.00 (-22.20)
        $newRow = $newRows[2];
        $this->assertEquals(-11100, $newRow->amount);
        $this->assertEquals(-2220, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        //	newRows.get(0).PricePerUnit * newRows.get(0).NumberOfUnits  +	// 250.00
        //	newRows.get(1).PricePerUnit * newRows.get(1).NumberOfUnits  +	// 106.00
        //	newRows.get(2).PricePerUnit * newRows.get(2).NumberOfUnits  +	// -111.00 (-22.20)
        $this->assertRegexp('/<amount>24500<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>3380<\/vat>/', $paymentXml);
    }

    public function test_FixedDiscount_specified_using_ExVat_and_vatPercent_is_added_as_single_discount_row()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1.0)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(88.80)
                ->setVatPercent(25)
            );


        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 88.80ex @25% => -111.00 (-22.20)
        $newRow = $newRows[2];
        $this->assertEquals(-11100, $newRow->amount);
        $this->assertEquals(-2220, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        //	newRows.get(0).PricePerUnit * newRows.get(0).NumberOfUnits  +	// 250.00
        //	newRows.get(1).PricePerUnit * newRows.get(1).NumberOfUnits  +	// 106.00
        //	newRows.get(2).PricePerUnit * newRows.get(2).NumberOfUnits  +	// -111.00 (-22.20)
        $this->assertRegexp('/<amount>24500<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>3380<\/vat>/', $paymentXml);
    }

    public function test_FixedDiscount_specified_using_ExVat_and_IncVat_is_added_as_single_discount_row()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1.0)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(111.00)
                ->setAmountExVat(88.80)
            );


        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 111.00inc and 88.80ex => @25% => -111.00 (-22.20)
        $newRow = $newRows[2];
        $this->assertEquals(-11100, $newRow->amount);
        $this->assertEquals(-2220, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        //	newRows.get(0).PricePerUnit * newRows.get(0).NumberOfUnits  +	// 250.00
        //	newRows.get(1).PricePerUnit * newRows.get(1).NumberOfUnits  +	// 106.00
        //	newRows.get(2).PricePerUnit * newRows.get(2).NumberOfUnits  +	// -111.00 (-22.20)
        $this->assertRegexp('/<amount>24500<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>3380<\/vat>/', $paymentXml);
    }

    // check that fixed discount split over vat rates ratios are present based on order item rows only, not shipping or invoice fees
    public function test_FixedDiscount_specified_using_amountExVat_is_calculated_from_order_item_rows_only()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @25% = 125")
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @6% = 106")
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1.0)
            )
            ->addFee(WebPayItem::shippingFee()// fee row should be ignored by discount calculation
            ->setName("shipping with price 50 @6% = 53")
                ->setAmountExVat(50.00)
                ->setVatPercent(6)
            )
            ->addFee(WebPayItem::invoiceFee()// fee row should be ignored by discount calculation
            ->setAmountExVat(23.20)
                ->setVatPercent(25)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("f100e")
                ->setName("couponName")
                ->setDescription("couponDesc")
                ->setAmountExVat(100.00)
                ->setUnit("st")
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 100*200/300 = 66.67 ex. 25% vat => discount 83.34 (incl. 16.67 vat @25%)
        // 100*100/300 = 33.33 ex. 6% vat => discount 35.33 (incl 2.00 vat @6%)
        // => total discount is 118.67 (incl 18.67 vat @18.67%)

        $newRow = $newRows[4];
        $this->assertEquals("f100e", $newRow->sku);
        $this->assertEquals("couponName", $newRow->name);
        $this->assertEquals("couponDesc", $newRow->description);
        $this->assertEquals(-11867, $newRow->amount);
        $this->assertEquals(-1867, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        // 250.00 (50.00)
        // 106.00 (6.00)
        // 53.00 (3.00)
        // 29.00 (5.80)
        // -118.67 (-18.67)
        // => 319.33 (46.13)
        $this->assertRegexp('/<amount>31933<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>4613<\/vat>/', $paymentXml);
    }

    // check that fixed discount split over vat rates ratios are present based on order item rows only, not shipping or invoice fees
    public function test_FixedDiscount_specified_using_amountIncVat_is_calculated_from_order_item_rows_only()
    {

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @25% = 125")
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @6% = 106")
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1.0)
            )
            ->addFee(WebPayItem::shippingFee()// fee row should be ignored by discount calculation
            ->setName("shipping with price 50 @6% = 53")
                ->setAmountExVat(50.00)
                ->setVatPercent(6)
            )
            ->addFee(WebPayItem::invoiceFee()// fee row should be ignored by discount calculation
            ->setAmountExVat(23.20)
                ->setVatPercent(25)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("f100i")
                ->setName("couponName")
                ->setDescription("couponDesc")
                ->setAmountIncVat(100.00)
                ->setUnit("st")
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 100*250/356 = 70.22 incl. 25% vat => 14.04 vat as amount 
        // 100*106/356 = 29.78 incl. 6% vat => 1.69 vat as amount 
        // => total discount is 100.00 (incl 15.73 vat)

        // validate HostedOrderRowBuilder row contents
        $newRow = $newRows[4];
        $this->assertEquals("f100i", $newRow->sku);
        $this->assertEquals("couponName", $newRow->name);
        $this->assertEquals("couponDesc", $newRow->description);
        $this->assertEquals(-10000, $newRow->amount);
        $this->assertEquals(-1573, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        // 250.00 (50.00)
        // 106.00 (6.00)
        // 53.00 (3.00)
        // 29.00 (5.80)
        // -70.22 (14.04) + 29.78 (1.69) = -100.00 (-15.73)
        // => 338.00 (49.07)
        $this->assertRegexp('/<amount>33800<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>4907<\/vat>/', $paymentXml);
    }

    /// relative discount    
    // iff no specified discount vat rate, check that calculated vat rate is split correctly across vat rates
    public function test_RelativeDiscount_in_order_with_single_vat_rate()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(40.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(50.00)
                ->setVatPercent(25)
                ->setQuantity(1.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(40.00)
                ->setAmountIncVat(50.00)
                ->setQuantity(1.0)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountId("r10%i")
                ->setDiscountPercent(10.00)
                ->setUnit("kr"));

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 10% of (80ex + 40ex + 40ex =) 160ex @25% => -16ex @25% => -20 (-4)
        $newRow = $newRows[3];
        $this->assertEquals("r10%i", $newRow->sku);
        $this->assertEquals(-2000, $newRow->amount);
        $this->assertEquals(-400, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("kr", $newRow->unit);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        // 100 (20) + 50 (10) + 50 (10) -20 (-4) => 180 (36)
        $this->assertRegexp('/<amount>18000<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>3600<\/vat>/', $paymentXml);
    }

    public function test_RelativeDiscount_in_order_with_multiple_vat_rates()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(40.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(50.00)
                ->setVatPercent(25)
                ->setQuantity(1.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(40.00)
                ->setAmountIncVat(50.00)
                ->setQuantity(1.0)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountId("r10%i")
                ->setDiscountPercent(10.00)
                ->setUnit("kr"));

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 10% of (80ex + 40ex + 40ex =) 160ex @25% => -16ex @25% => -20 (-4)
        $newRow = $newRows[3];
        $this->assertEquals("r10%i", $newRow->sku);
        $this->assertEquals(-2000, $newRow->amount);
        $this->assertEquals(-400, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("kr", $newRow->unit);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        // 100 (20) + 50 (10) + 50 (10) -20 (-4) => 180 (36)
        $this->assertRegexp('/<amount>18000<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>3600<\/vat>/', $paymentXml);
    }

    // check that relative discount split over vat rates ratios are present based on order item rows only, not shipping or invoice fees
    public function test_RelativeDiscount_is_calculated_from_order_item_rows_only()
    {

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1.0)
            )
            ->addFee(WebPayItem::shippingFee()// fee row should be ignored by discount calculation
            ->setAmountIncVat(53.00)
                ->setVatPercent(6)
            )
            ->addFee(WebPayItem::invoiceFee()// fee row should be ignored by discount calculation
            ->setAmountIncVat(29.00)
                ->setVatPercent(25)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10.00)
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 10% of (250 (50) + 106 (6)) = 356 (56) => -35.6 (5.6)	    
        $newRow = $newRows[4];
        $this->assertEquals(-3560, $newRow->amount);
        $this->assertEquals(-560, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        // 250 (50) + 106 (6) +53 (3) + 29 (5.8) -35.6 (-5.6) => 402.4 (59.2)	    
        $this->assertRegexp('/<amount>40240<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>5920<\/vat>/', $paymentXml);
    }

// ----------------------------------------------------

    // FixedDiscountRow specified using only amountIncVat => split discount incl. vat over the diffrent tax rates present in order
    public function test_FixedDiscount_specified_using_amountIncVat_in_order_with_single_vat_rate_php_original_version()
    {
        $this->order->addOrderRow(WebPayItem::orderRow()
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
    public function test_FixedDiscount_specified_using_amountIncVat_in_order_with_multiple_vat_rates_php_original_version()
    {
        $this->order->addOrderRow(WebPayItem::orderRow()
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
    public function test_FixedDiscount_specified_using_amountIncVat_is_calculated_from_order_item_rows_only_php_original_version()
    {
        $this->order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(2)
        )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(50.00)
                ->setVatPercent(6)
            )
//                ->addFee(\Svea\WebPay\WebPayItem::invoiceFee()    // Invoice fees are not processed by HostedRowFormatter
//                ->setAmountExVat(23.20)
//                ->setVatPercent(25)
//                )
            ->addDiscount(WebPayItem::fixedDiscount()
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

    // check that fixed discount split over vat rates ratios are present based on order item rows only, not shipping or invoice fees
    public function test_FixedDiscount_specified_using_amountExVat_is_calculated_from_order_item_rows_only_php_original_version()
    {

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @25% = 125")
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
                ->setQuantity(2.0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setName("product with price 100 @6% = 106")
                ->setAmountExVat(100.00)
                ->setVatPercent(6)
                ->setQuantity(1.0)
            )
            ->addFee(WebPayItem::shippingFee()// fee row should be ignored by discount calculation
            ->setName("shipping with price 50 @6% = 53")
                ->setAmountExVat(50.00)
                ->setVatPercent(6)
            )
            ->addFee(WebPayItem::invoiceFee()// fee row should be ignored by discount calculation
            ->setAmountExVat(23.20)
                ->setVatPercent(25)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("f100e")
                ->setName("couponName")
                ->setDescription("couponDesc")
                ->setAmountExVat(100.00)
                ->setUnit("st")
            );

        $formatter = new HostedRowFormatter();
        $newRows = $formatter->formatRows($order);  // of type HostedOrderRowBuilder

        // 100*200/300 = 66.67 ex. 25% vat => discount 83.34 (incl. 16.67 vat @25%)
        // 100*100/300 = 33.33 ex. 6% vat => discount 35.33 (incl 2.00 vat @6%)
        // => total discount is 118.67 (incl 18.67 vat @18.67%)

        // validate HostedOrderRowBuilder row contents
        $newRow = $newRows[4];
        $this->assertEquals("f100e", $newRow->sku);
        $this->assertEquals("couponName", $newRow->name);
        $this->assertEquals("couponDesc", $newRow->description);
        $this->assertEquals(-11867, $newRow->amount);
        $this->assertEquals(-1867, $newRow->vat);
        $this->assertEquals(1, $newRow->quantity);
        $this->assertEquals("st", $newRow->unit);

        $paymentForm = $order
            ->setCountryCode("SE")
            ->setOrderDate("2015-02-23")
            ->setClientOrderNumber("unique")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setReturnUrl("http://myurl.com")
            ->getPaymentForm();
        $paymentXml = $paymentForm->xmlMessage;

        //	    		// 250.00 (50.00)
        //	    		// 106.00 (6.00)
        //	    		// 53.00 (3.00)
        //	    		// 29.00 (5.80)
        //		    	// -118.67 (-18.67)
        //		    	// => 319.33 (46.13)
        $this->assertRegexp('/<amount>31933<\/amount>/', $paymentXml);
        $this->assertRegexp('/<vat>4613<\/vat>/', $paymentXml);
    }


// ---------------------------------    

    public function test_RelativeDiscount_in_order_with_single_vat_rate_php_original_version()
    {
        $this->order->addOrderRow(WebPayItem::orderRow()
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

        $formatter = new HostedRowFormatter();
        $resultRows = $formatter->formatRows($this->order);
        $testedRow = $resultRows[3];

        $this->assertEquals("r10", $testedRow->sku);
        $this->assertEquals("couponName", $testedRow->name);
        $this->assertEquals("couponDesc", $testedRow->description);
        $this->assertEquals(-150, $testedRow->amount);
        $this->assertEquals(-30, $testedRow->vat);
    }

}
