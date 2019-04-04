<?php

namespace Svea\WebPay\Test\IntegrationTest\WebService\Payment;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Helper\Helper;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;

/**
 * @author Kristian Grossman-Madsen
 */
class FixedDiscountRowsIntegrationTest extends \PHPUnit\Framework\TestCase
{

    // This file contains the same tests as UnitTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest
    // but also sends the create order request to the service and checks response for success and order amount.

    /**
     * Tests that orders created with a mix of order and fee rows specified as exvat/incvat and vatpercent
     * are sent to the webservice using the correct PriceIncludingVat flag.
     *
     * Also tests that fixed discount rows specified as amount inc/exvat only are split correctly across
     * all the order vat rates, and that the split discount rows are sent to the service using the correct
     * PriceIncludingVat flag.
     */
    private static function create_mixed_exvat_and_incvat_order_and_fee_rows_order()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(60.00)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                    ->setName("exvatRow")
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(33.00)
                    ->setVatPercent(10)
                    ->setQuantity(1)
                    ->setName("incvatRow")
            )
            ->addFee(
                WebPayItem::invoiceFee()
                    ->setAmountIncVat(8.80)
                    ->setVatPercent(10)
                    ->setName("incvatInvoiceFee")
            )
            ->addFee(
                WebPayItem::shippingFee()
                    ->setAmountExVat(16.00)
                    ->setVatPercent(10)
                    ->setName("exvatShippingFee")
            );

        return $order;
    }

    // order with order/fee rows mixed exvat+vat / incvat+vat should be sent with PriceIncludingVat = false
    public function test_mixed_order_row_and_shipping_fees_only_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsIntegrationTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();

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

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        $this->assertEquals("131.4", $response->amount);
    }

    // same order with discount exvat should be sent with PriceIncludingVat = false but with split discount rows based on order amounts ex vat
    public function test_mixed_order_with_fixedDiscount_as_exvat_only_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsIntegrationTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountExVat(10.0)
                //->setVatPercent(10)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 10e")
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
        // expected: fixedDiscount: 10 exvat => split across 10e *(60/60+30) @20% + 10e *(30/60+30) @10% => 6.67e @20% + 3.33e @10%
        $this->assertEquals(-6.67, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.33, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP); //=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(60*1.20*1) + r(30*1.10*1) + r(16*1.10*1) + r(8*1.10*1) + r(-6.67*1.20*1) + r(-3.33*1.10*1) => 72.00+33.00+17.60+8.80-8.00-3.66 => 119.74
        $this->assertEquals("119.74", $response->amount);
    }

    // same order with discount incvat should be sent with PriceIncludingVat = false but with split discount rows based on order amounts inc vat
    public function test_mixed_order_with_fixedDiscount_as_incvat_only_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsIntegrationTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountIncVat(10.0)
                //->setVatPercent(10)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 10i")
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
        // expected: fixedDiscount: 10 incvat => split across 10i *(72/72+33) @20% + 10i *(33/72+33) @10% => 6.8571i @20% + 3.1428i @10% =>5.71 + 2.86
        $this->assertEquals(-5.71, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-2.86, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(60*1.20*1) + r(30*1.10*1) + r(16*1.10*1) + r(8*1.10*1) + r(-5.72*1.20*1) + r(-2.85*1.10*1) => 72.00+33.00+17.60+8.80-6.85-3.15 => 121.40
        $this->assertEquals("121.40", $response->amount);
    }

    // same order with discount exvat+vat should be sent with PriceIncludingVat = false with one discount row amount based on given exvat + vat
    public function test_mixed_order_with_fixedDiscount_as_exvat_and_vatpercent_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsIntegrationTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountExVat(10.0)
                ->setVatPercent(10)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 10e@10%")
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
        // expected: fixedDiscount: 11 incvat @10% => -10e @10%
        $this->assertEquals(-10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertFalse(isset($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]));

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(60*1.20*1) + r(30*1.10*1) + r(16*1.10*1) + r(8*1.10*1) + r(-10*1.10*1) = 72+33+17.60 + 8.80 -11.00 = 120.40
        $this->assertEquals("120.4", $response->amount);
    }

    // same order with discount incvat+vat should be sent with PriceIncludingVat = false with one discount row amount based on given incvat + vat
    public function test_mixed_order_with_fixedDiscount_as_incvat_and_vatpercent_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsIntegrationTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountIncVat(11.0)
                ->setVatPercent(10)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 11i@10%")
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
        // expected: fixedDiscount: 11 incvat @10% => -10e @10%
        $this->assertEquals(-10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertFalse(isset($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]));

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(60*1.20*1) + r(30*1.10*1) + r(16*1.10*1) + r(8*1.10*1) + r(-10*1.10*1) = 72+33+17.60 + 8.80 -11.00 = 120.40
        $this->assertEquals("120.4", $response->amount);
    }

    private static function create_only_incvat_order_and_fee_rows_order()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(72.00)
                    ->setVatPercent(20)
                    ->setQuantity(1)
                    ->setName("incvatRow")
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(33.00)
                    ->setVatPercent(10)
                    ->setQuantity(1)
                    ->setName("incvatRow2")
            )
            ->addFee(
                WebPayItem::invoiceFee()
                    ->setAmountIncVat(8.80)
                    ->setVatPercent(10)
                    ->setName("incvatInvoiceFee")
            )
            ->addFee(
                WebPayItem::shippingFee()
                    ->setAmountIncVat(17.60)
                    ->setVatPercent(10)
                    ->setName("incvatShippingFee")
            );

        return $order;
    }

    // order with order/fee rows all having incvat should be sent with PriceIncludingVat = true
    public function test_incvat_order_row_and_shipping_fees_only_has_priceIncludingVat_true()
    {
        $order = FixedDiscountRowsIntegrationTest::create_only_incvat_order_and_fee_rows_order();

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

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        $this->assertEquals("131.4", $response->amount);
    }

    // same order with discount exvat should be sent with PriceIncludingVat = true but with split discount rows based on order amounts ex vat
    public function test_incvat_order_with_fixedDiscount_as_exvat_only_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsIntegrationTest::create_only_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountExVat(10.0)
                //->setVatPercent(10)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 10e")
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
        // expected: fixedDiscount: 10 exvat => split across 10e *(60/60+30) @20% + 10e *(30/60+30) @10% => 6.6666e @20% + 3.3333e @10% => 8.00i + 3.67i
        $this->assertEquals(-6.67, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.33, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(72.00*1) + r(33.00*1) + r(17.60*1) + r(8.80*1) + r(-8.00*1) + r(-3.66*1) => 72.00+33.00+17.60+8.80-8.00-3.67 => 119.73
        //$this->assertEquals( "119.73", $response->amount );     // TODO check that this is the amount in S1 invoice, vs 119.74 w/PriceIncludingVat = false
        $this->assertEquals("119.74", $response->amount);     // jfr vs 119.73 w/PriceIncludingVat = true
    }

    // same order with discount incvat should be sent with PriceIncludingVat = false but with split discount rows based on order amounts inc vat
    public function test_incvat_order_with_fixedDiscount_as_incvat_only_has_priceIncludingVat_true()
    {
        $order = FixedDiscountRowsIntegrationTest::create_only_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountIncVat(10.0)
                //->setVatPercent(10)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 10i")
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
        // expected: fixedDiscount: 10 incvat => split across 10i *(72/72+33) @20% + 10i *(33/72+33) @10% => 6.8571i @20% + 3.1428i @10% =>5.71 + 2.86
        $this->assertEquals(-6.86, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.14, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(72*1) + r(33*1) + r(17.60*1) + r(8.80*1) + r(-5.72*1.20*1) + r(-2.85*1.10*1) => 72.00+33.00+17.60+8.80-6.86-3.14 => 121.40
        $this->assertEquals("121.40", $response->amount);
    }

    // same order with discount exvat+vat should be sent with PriceIncludingVat = false with one discount row amount based on given exvat + vat
    public function test_incvat_order_with_fixedDiscount_as_exvat_and_vatpercent_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsIntegrationTest::create_only_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountExVat(10.0)
                ->setVatPercent(10)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 10e@10%")
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
        // expected: fixedDiscount: 10exvat @10% = -11.00
        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertFalse(isset($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]));

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(72*1) + r(33*1) + r(17.60*1) + r(8.80*1) + r(-5.72*1.20*1) + r(-2.85*1.10*1) => 72.00+33.00+17.60+8.80-11.00 => 120.40
        $this->assertEquals("120.4", $response->amount);
    }

    // same order with discount incvat+vat should be sent with PriceIncludingVat = false with one discount row amount based on given incvat + vat
    public function test_incvat_order_with_fixedDiscount_as_incvat_and_vatpercent_has_priceIncludingVat_true()
    {
        $order = FixedDiscountRowsIntegrationTest::create_only_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountIncVat(11.0)
                ->setVatPercent(10)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 11i@10%")
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
        // expected: fixedDiscount: 11incvat @10% = -11.00
        $this->assertEquals(-11.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertFalse(isset($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]));

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(72*1) + r(33*1) + r(17.60*1) + r(8.80*1) + r(-5.72*1.20*1) + r(-2.85*1.10*1) => 72.00+33.00+17.60+8.80-11.00 => 120.40
        $this->assertEquals("120.4", $response->amount);
    }
}