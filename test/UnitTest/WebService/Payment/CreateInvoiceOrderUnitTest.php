<?php

namespace Svea\WebPay\Test\UnitTest\WebService\Payment;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Helper\Helper;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;


/**
 * Tests ported from Java webservice/payment/Svea\WebPay\Test\UnitTest\WebService\Payment\CreateInvoiceOrderUnitTest.java for INTG-550
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CreateInvoiceOrderUnitTest extends \PHPUnit\Framework\TestCase
{
    var $order;
    var $exvatRow;
    var $exvatRow2;
    var $exvatInvoiceFee;
    var $exvatShippingFee;
    var $incvatRow;
    var $incvatRow2;
    var $incvatInvoiceFee;
    var $incvatShippingFee;

    public function setUp()
    {   // run before each test, in effect resetting the default order

        $this->order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setOrderDate(date('c'));

        $this->exvatRow = WebPayItem::orderRow()
            ->setAmountExVat(80.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setName("exvatRow");
        $this->exvatRow2 = WebPayItem::orderRow()
            ->setAmountExVat(80.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setName("exvatRow2");

        $this->exvatInvoiceFee = WebPayItem::invoiceFee()
            ->setAmountExVat(8.00)
            ->setVatPercent(25)
            ->setName("exvatInvoiceFee");

        $this->exvatShippingFee = WebPayItem::shippingFee()
            ->setAmountExVat(16.00)
            ->setVatPercent(25)
            ->setName("exvatShippingFee");
        $this->incvatRow = WebPayItem::orderRow()
            ->setAmountIncvat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setName("incvatRow");
        $this->incvatRow2 = WebPayItem::orderRow()
            ->setAmountIncvat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setName("incvatRow2");

        $this->incvatInvoiceFee = WebPayItem::invoiceFee()
            ->setAmountIncvat(10.00)
            ->setVatPercent(25)
            ->setName("incvatInvoiceFee");

        $this->incvatShippingFee = WebPayItem::shippingFee()
            ->setAmountIncvat(20.00)
            ->setVatPercent(25)
            ->setName("incvatShippingFee");
    }

    /// tests preparing order rows price specification
    // invoice request
    public function test_orderRows_and_Fees_specified_exvat_and_vat_using_useInvoicePayment_are_prepared_as_exvat_and_vat()
    {
        $order = $this->order;

        $order->addOrderRow($this->exvatRow);
        $order->addOrderRow($this->exvatRow2);
        $order->addFee($this->exvatInvoiceFee);
        $order->addFee($this->exvatShippingFee);

        // all order rows
        // all shipping fee rows
        // all invoice fee rows
        $request = $order->useInvoicePayment()->prepareRequest();
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);

        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);


    }

    public function test_orderRows_and_Fees_specified_incvat_and_vat_using_useInvoicePayment_are_prepared_as_incvat_and_vat()
    {
        $order = $this->order;

        $order->addOrderRow($this->incvatRow);
        $order->addOrderRow($this->incvatRow2);
        $order->addFee($this->incvatInvoiceFee);
        $order->addFee($this->incvatShippingFee);

        // all order rows
        // all shipping fee rows
        // all invoice fee rows
        $request = $order->useInvoicePayment()->prepareRequest();
        $this->assertEquals(100.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(100.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);

        $this->assertEquals(20.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);

    }

    //validation of same order row price/vat specification in same order
    public function test_that_createOrder_with_uniform_orderRow_and_Fee_price_specifications_does_not_throw_validation_error()
    {
        $order = $this->order;

        $order->addOrderRow($this->exvatRow);
        $order->addOrderRow($this->exvatRow);
        $order->addFee($this->exvatInvoiceFee);
        $order->addFee($this->exvatShippingFee);

        try {
            $request = $order->useInvoicePayment()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    public function test_that_createOrder_with_mixed_orderRow_and_Fee_price_specifications_does_not_throw_validation_error()
    {
        $order = $this->order;

        $order->addOrderRow($this->exvatRow);
        $order->addOrderRow($this->incvatRow);
        $order->addFee($this->exvatInvoiceFee);
        $order->addFee($this->exvatShippingFee);

        try {
            $request = $order->useInvoicePayment()->prepareRequest();
            $this->assertTrue(true);
        } catch (Exception $e) {
            // fail on validation error
            $this->fail("Unexpected validation exception: " . $e->getMessage());
        }
    }

    //if no mixed specification types, default to sending order as incvat
    public function test_that_createOrder_request_is_sent_as_incvat_iff_no_exvat_specified_anywhere_in_order()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(72.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(33.00)
                ->setAmountExVat(30.00)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(8.80)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(17.60)
                ->setVatPercent(10)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(10.0)
                ->setDiscountId("TenCrownsOff")
                ->setName("fixedDiscount: 10 off incvat")
            );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(72.0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(33.0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.8, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(17.6, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 off incvat, order row amount are 66% at 20% vat, 33% at 10% vat
        // 1.2*0.66x + 1.1*0.33x = 10 => x = 8.6580 => 5.7143ex @20% and 2.8571ex @10% => 6.86inc @20%, 3.14inc @10%
        // NOTE that php package does not round the request amounts to two decimals, as the java integration package, hence the call to bround below
        $this->assertEquals(-6.86, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit, 2));
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.14, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit, 2));
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
        // order total should be (72+33+17.6+8.8)-10 = 121.40, see integration test
    }

    //if mixed specification types, send order as exvat if at least one exvat + vat found
    public function test_that_createOrder_request_is_sent_as_exvat_if_exvat_specified_anywhere_in_order()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(60.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(33.00)
                ->setAmountExVat(30.00)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(8.80)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(17.60)
                ->setVatPercent(10)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(10.0)
                ->setDiscountId("TenCrownsOff")
                ->setName("fixedDiscount: 10 off incvat")
            );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(30.0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(16.0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 off incvat, order row amount are 66% at 20% vat, 33% at 10% vat
        // 1.2*0.66x + 1.1*0.33x = 10 => x = 8.6580 => 5.7143ex @20% and 2.8571ex @10% =
        // NOTE that php package does not round the request amounts to two decimals, as the java integration package, hence the call to bround below
        $this->assertEquals(-5.71, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit, 2));
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-2.86, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit, 2));
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
        // order total should be (72+33+17.6+8.8)-10 = 121.40, see integration test
    }


    /// relative discount examples:
    public function test_exvat_only_order_with_relativeDiscount_with_single_vat_rates_order_sent_with_PriceIncludingVat_false()
    {
        $order = $this->order;

        $order->addOrderRow($this->exvatRow);
        $order->addOrderRow($this->exvatRow2);
        $order->addFee($this->exvatInvoiceFee);
        $order->addFee($this->exvatShippingFee);

        $order->addDiscount(WebPayItem::relativeDiscount()
            ->setDiscountPercent(10.0)
            ->setDiscountId("TenPercentOff")
            ->setName("relativeDiscount")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: 10% off orderRow rows: 2x 80.00 @25% => -16.00 @25% discount
        $this->assertEquals(-16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
    }

    public function test_exvat_only_order_with_relativeDiscount_with_multiple_vat_rates_order_sent_with_PriceIncludingVat_false()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(60.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(30.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(8.00)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(16.00)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::relativeDiscount()
            ->setDiscountPercent(10)
            ->setDiscountId("TenPercentOff")
            ->setName("relativeDiscount")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: 10% off orderRow rows: 1x60.00 @20%, 1x30@10% => split proportionally across order row (only) vat rate: -6.0 @20%, -3.0 @10%
        $this->assertEquals(-6.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
    }

    public function test_incvat_only_order_with_relativeDiscount_with_multiple_vat_rates_order_sent_with_PriceIncludingVat_true()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(72.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(33.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(8.80)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(17.60)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::relativeDiscount()
            ->setDiscountPercent(10)
            ->setDiscountId("TenPercentOff")
            ->setName("relativeDiscount")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(72.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(33.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(17.60, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: 10% off orderRow rows: 60.0 @20%, 30.0 @10% => split across order row (only) vat rate: 6.0ex @20% = 7.2inc, 3.0ex @10% = 3.3inc
        $this->assertEquals(-7.20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.30, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
    }

    // fixed discount examples:
    public function test_exvat_only_order_with_fixedDiscount_with_amount_specified_as_exvat_and_given_vat_rate_order_sent_with_PriceIncludingVat_false()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(60.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(30.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(8.00)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(16.00)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountExVat(10.0)
            ->setVatPercent(10.0)
            ->setDiscountId("ElevenCrownsOff")
            ->setName("fixedDiscount: 10 @10% => 11kr")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
    }

    public function test_incvat_only_order_with__fixedDiscount_with_amount_specified_as_exvat_and_given_vat_rate_order_sent_with_PriceIncludingVat_false()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(72.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(33.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(8.80)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(17.60)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountExVat(10.0)
            ->setVatPercent(10.0)
            ->setDiscountId("ElevenCrownsOff")
            ->setName("fixedDiscount: 10 @10% => 11kr")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
    }

    public function test_exvat_only_order_fixedDiscount_with_amount_specified_as_incvat_and_given_vat_rate_order_sent_with_PriceIncludingVat_false()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(60.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(30.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(8.00)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(16.00)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountIncVat(11.0)
            ->setVatPercent(10.0)
            ->setDiscountId("ElevenCrownsOff")
            ->setName("fixedDiscount: 11i @10% => 11kr")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
    }

    public function test_incvat_only_order_fixedDiscount_with_amount_specified_as_incvat_and_given_vat_rate_order_sent_with_PriceIncludingVat_true()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(72.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(33.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(8.80)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(17.60)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountIncVat(11.0)
            ->setVatPercent(10.0)
            ->setDiscountId("ElevenCrownsOff")
            ->setName("fixedDiscount: 11i @10% => 11kr")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(72.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(33.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(8.80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(17.60, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 @10% => 11kr, expressed as incvat + vat in request
        $this->assertEquals(-11.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
    }

    public function test_exvat_only_order_with_fixedDiscount_amount_specified_as_exvat_and_calculated_vat_rate_order_sent_with_PriceIncludingVat_false()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(600.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(300.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(160.00)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountExVat(10.0)
            ->setDiscountId("TenCrownsOff")
            ->setName("fixedDiscount: 10 off exvat")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(600.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(300.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(160.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 off exvat, order row amount are 66% at 20% vat, 33% at 10% vat => 6.67 @20% and 3.33 @10%
        $this->assertEquals(-6.67, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit, 2));
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.33, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit, 2));
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
    }

    public function test_incvat_only_order_with_fixedDiscount_amount_specified_as_exvat_and_calculated_vat_rate_order_sent_with_PriceIncludingVat_false()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(720.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(330.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(88.00)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(172.00)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountExVat(10.0)
            ->setDiscountId("TenCrownsOffExVat")
            ->setName("fixedDiscount: 10 off exvat")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(600.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(300.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(156.36, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit, 2));
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 off exvat, order row amount are 66% @20% vat, 33% @10% vat => 6.67ex @20% = 8.00 inc and 3.33ex @10% = 3.67inc
        $this->assertEquals(-6.67, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit, 2));
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.33, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit, 2));
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
    }

    public function test_exvat_only_order_with_fixedDiscount_amount_specified_as_incvat_and_calculated_vat_rate_order_sent_with_PriceIncludingVat_false()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(600.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(300.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(160.00)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountIncVat(10.0)
            ->setDiscountId("TenCrownsOff")
            ->setName("fixedDiscount: 10 off incvat")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(600.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(300.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(160.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 off incvat, order row amount are 66% at 20% vat, 33% at 10% vat
        // 1.2*0.66x + 1.1*0.33x = 10 => x = 8.6580 => 5.7143 @20% and 2.8571 @10% = 10kr
        $this->assertEquals(-5.71, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit, 2));
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-2.86, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit, 2));
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
    }

    public function test_incvat_only_order_with_fixedDiscount_amount_specified_as_incvat_and_calculated_vat_rate_order_sent_with_PriceIncludingVat_true()
    {
        $order = $this->order;
        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(720.00)
                ->setVatPercent(20)
                ->setQuantity(1)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(330.00)
                ->setVatPercent(10)
                ->setQuantity(1)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(88.00)
                ->setVatPercent(10)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(172.00)
                ->setVatPercent(10)
            );
        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountIncVat(10.0)
            ->setDiscountId("TenCrownsOff")
            ->setName("fixedDiscount: 10 off incvat")
        );

        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(720.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);
        $this->assertEquals(330.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);
        // all invoice fee rows
        $this->assertEquals(88.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);
        // all shipping fee rows
        $this->assertEquals(172.00, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit, 2));
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PriceIncludingVat);
        // all discount rows
        // expected: fixedDiscount: 10 off incvat, order row amount are 66% at 20% vat, 33% at 10% vat
        // 1.2*0.66x + 1.1*0.33x = 10 => x = 8.6580 => 5.7143 @20% and 2.8571 @10% = 10kr
        $this->assertEquals(-6.86, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit, 2));
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.14, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit, 2));
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
    }

    public function test_add_publickey_for_company_customer()
    {
        $config = ConfigurationService::getTestConfig();
        $order = WebPay::createOrder($config)
            ->addCustomerDetails(
                WebPayItem::companyCustomer()
                    ->setPublicKey('ac0f2573b58ff523')//String ex. ac0f2573b58ff523
            );
        $this->assertEquals('ac0f2573b58ff523', $order->customerIdentity->publicKey);


    }

    public function test_add_publickey_for_private_customer()
    {
        $config = ConfigurationService::getTestConfig();
        $order = WebPay::createOrder($config)
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setPublicKey('ac0f2573b58ff523')//String ex. ac0f2573b58ff523
            );

        $this->assertEquals('ac0f2573b58ff523', $order->customerIdentity->publicKey);

    }

    public function test_add_publickey_for_company_customer_full_request()
    {
        $config = ConfigurationService::getTestConfig();
        $order = WebPay::createOrder($config)
            ->addCustomerDetails(
                WebPayItem::companyCustomer()
                    ->setCompanyName('mycomp')
                    ->setNationalIdNumber('164701161111')
                    ->setPublicKey('ac0f2573b58ff523')//String ac0f2573b58ff523
            )
            ->setCountryCode('SE')
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(125.00)
                ->setVatPercent(25)
                ->setQuantity(1)
            )
            ->setOrderDate(date('c'))
            ->useInvoicePayment()
            ->prepareRequest();

        $this->assertEquals('ac0f2573b58ff523', $order->request->CreateOrderInformation->CustomerIdentity->PublicKey);

    }

    public function test_add_publickey_for_private_customer_full_request()
    {
        $config = ConfigurationService::getTestConfig();
        $order = WebPay::createOrder($config)
            ->addCustomerDetails(
                WebPayItem::companyCustomer()
                    ->setCompanyName('mycomp')
                    ->setNationalIdNumber('164701161111')
                    ->setPublicKey('ac0f2573b58ff523')//String ac0f2573b58ff523
            )
            ->setCountryCode('SE')
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountIncVat(125.00)
                ->setVatPercent(25)
                ->setQuantity(1)
            )
            ->setOrderDate(date('c'))
            ->useInvoicePayment()
            ->prepareRequest();

        $this->assertEquals('ac0f2573b58ff523', $order->request->CreateOrderInformation->CustomerIdentity->PublicKey);

    }

}