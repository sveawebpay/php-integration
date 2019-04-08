<?php

namespace Svea\WebPay\Test\UnitTest\WebService\Payment;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;


/**
 * @author Kristian Grossman-Madsen
 */
class FixedDiscountRowsTest extends \PHPUnit\Framework\TestCase
{

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

    // same order with discount exvat should be sent with PriceIncludingVat = false but with split discount rows based on order amounts ex vat
    public function test_mixed_order_with_fixedDiscount_as_exvat_only_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();
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
        $this->assertEquals(-6.67, round($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.33, round($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP); //=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
    }

    // same order with discount incvat should be sent with PriceIncludingVat = false but with split discount rows based on order amounts inc vat
    public function test_mixed_order_with_fixedDiscount_as_incvat_only_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();
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
        $this->assertEquals(-5.71, round($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-2.86, round($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
    }

    // same order with discount exvat+vat should be sent with PriceIncludingVat = false with one discount row amount based on given exvat + vat
    public function test_mixed_order_with_fixedDiscount_as_exvat_and_vatpercent_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();
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

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
    }

    // same order with discount incvat+vat should be sent with PriceIncludingVat = false with one discount row amount based on given incvat + vat
    public function test_mixed_order_with_fixedDiscount_as_incvat_and_vatpercent_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsTest::create_mixed_exvat_and_incvat_order_and_fee_rows_order();
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

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
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
        $order = FixedDiscountRowsTest::create_only_incvat_order_and_fee_rows_order();

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

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
    }

    // same order with discount exvat should be sent with PriceIncludingVat = false with split discount rows based on order amounts ex
    public function test_incvat_order_with_fixedDiscount_as_exvat_only_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsTest::create_only_incvat_order_and_fee_rows_order();
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
        $this->assertEquals(-6.67, round($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.33, round($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
    }


    // same order with discount incvat should be sent with PriceIncludingVat = false but with split discount rows based on order amounts inc vat
    public function test_incvat_order_with_fixedDiscount_as_incvat_only_has_priceIncludingVat_true()
    {
        $order = FixedDiscountRowsTest::create_only_incvat_order_and_fee_rows_order();
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
        $this->assertEquals(-6.86, round($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.14, round($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
    }

    // same order with discount exvat+vat should be sent with PriceIncludingVat = false with one discount row amount based on given exvat + vat
    public function test_incvat_order_with_fixedDiscount_as_exvat_and_vatpercent_has_priceIncludingVat_false()
    {
        $order = FixedDiscountRowsTest::create_only_incvat_order_and_fee_rows_order();
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

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
    }

    // same order with discount incvat+vat should be sent with PriceIncludingVat = false with one discount row amount based on given incvat + vat
    public function test_incvat_order_with_fixedDiscount_as_incvat_and_vatpercent_has_priceIncludingVat_true()
    {
        $order = FixedDiscountRowsTest::create_only_incvat_order_and_fee_rows_order();
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

        // See file IntegrationTest/WebService/Payment/Svea\WebPay\Test\IntegrationTest\WebService\Payment\FixedDiscountRowsIntegrationTest for service response tests.
    }
}