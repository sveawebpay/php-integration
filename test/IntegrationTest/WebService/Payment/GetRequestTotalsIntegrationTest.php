<?php

namespace Svea\WebPay\Test\IntegrationTest\WebService\Payment;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Test\TestUtil;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;


/**
 * @author Kristian Grossman-Madsen
 */
class GetRequestTotalsIntegrationTest extends \PHPUnit\Framework\TestCase
{

    function test_get_invoice_total_amount_before_createorder()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK");
        $order->addOrderRow(WebPayItem::orderRow()
            ->setName('Universal Camera Charger')
            ->setAmountIncVat(19.60)
            ->setVatPercent(25)
            ->setQuantity(100)
        )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(29.00)
                ->setVatPercent(25)
                ->setName('Svea Invoice Fee')
            )
            ->addDiscount(
                WebPayItem::fixedDiscount()
                    ->setAmountIncVat(294.00)
                    ->setName('Discount')
            );
        $total = $order->useInvoicePayment()->getRequestTotals();

        $this->assertEquals(1695.0, $total['total_incvat']);
        $this->assertEquals(1356.0, $total['total_exvat']);
        $this->assertEquals(339.0, $total['total_vat']);

        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals($total['total_incvat'], $response->amount);
    }

    function test_get_invoice_total_amount_before_createorder_creates_discount_rows_using_incvat_and_vatpercent()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK");
        $order->addOrderRow(WebPayItem::orderRow()
            ->setName('Universal Camera Charger')
            ->setAmountIncVat(19.60)
            ->setVatPercent(25)
            ->setQuantity(100)
        )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(29.00)
                ->setVatPercent(25)
                ->setName('Svea Invoice Fee')
            )
            ->addDiscount(
                WebPayItem::fixedDiscount()
                    ->setAmountIncVat(294.00)
                    ->setName('Discount')
            );
        $total = $order->useInvoicePayment()->getRequestTotals();

        $this->assertEquals(1695.0, $total['total_incvat']);
        $this->assertEquals(1356.0, $total['total_exvat']);
        $this->assertEquals(339.0, $total['total_vat']);

        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals($total['total_incvat'], $response->amount);
    }

    /// example of order differing when sent incvat and exvat ----------------------------------------------------------------------

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

    public function test_getOrderTotals_has_same_amounts_as_service_when_order_sent_priceIncludingVat_false()
    {
        $order = GetRequestTotalsIntegrationTest::create_only_incvat_order_and_fee_rows_order();
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

        // verify that getRequestTotals() got the same amount as the service
        $preview = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals("119.74", $preview['total_incvat']);
        $this->assertEquals($preview['total_incvat'], $response->amount);
    }

    public function test_getOrderTotals_has_same_amounts_as_service_when_order_sent_priceIncludingVat_true()
    {
        $order = GetRequestTotalsIntegrationTest::create_only_incvat_order_and_fee_rows_order();
        $order->
        addDiscount(
            WebPayItem::fixedDiscount()
                ->setAmountIncVat(8.00)
                ->setVatPercent(20)
                ->setDiscountId("fixedDiscount")
                ->setName("fixedDiscount: 8.00i@20%")
        )
            ->addDiscount(
                WebPayItem::fixedDiscount()
                    ->setAmountIncVat(3.67)
                    ->setVatPercent(10)
                    ->setDiscountId("fixedDiscount")
                    ->setName("fixedDiscount: 3.67ie@10%")
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
        // expected: fixedDiscount: 10 exvat => split across 10e *(60/60+30) @20% + 10e *(30/60+30) @10% => 6.6666e @20% + 3.3333e @10% => 8.00i + 3.67i
        $this->assertEquals(-8.00, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.67, Helper::bround($request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit), 2, PHP_ROUND_HALF_UP);//=WS
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
        // r() is round($val, 2, PHP_ROUND_HALF_EVEN), i.e. bankers rounding
        // r(72.00*1) + r(33.00*1) + r(17.60*1) + r(8.80*1) + r(-8.00*1) + r(-3.66*1) => 72.00+33.00+17.60+8.80-8.00-3.67 => 119.73
        //$this->assertEquals( "119.73", $response->amount );     // TODO check that this is the amount in S1 invoice, vs 119.74 w/PriceIncludingVat = false
        $this->assertEquals("119.73", $response->amount);     // jfr vs 119.73 w/PriceIncludingVat = true

        // verify that getRequestTotals() got the same amount as the service
        $preview = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals("119.73", $preview['total_incvat']);
        $this->assertEquals($preview['total_incvat'], $response->amount);
    }

    /// example of getRequestTotals() not matching service --------------------------------------------------------------------------
    public function test_integrationtest_reference_1400_00_inc_behaviour()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1400.00)
                    ->setVatPercent(6)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2016-04-14");
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(1400, $response->amount);

        // verify that getRequestTotals() got the same amount as the service
        $preview = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals($preview['total_incvat'], $response->amount);


    }

    public function test_integrationtest_reference_1321_00_ex_behaviour()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(1321.00)
                    ->setVatPercent(6)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2016-04-14");
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(1400.26, $response->amount);

        // verify that getRequestTotals() got the same amount as the service
        $preview = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals($preview['total_incvat'], $response->amount);
    }

    public function test_getRequestTotals_reference_1400_00_inc_behaviour()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1400.00)
                    ->setVatPercent(6)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2016-04-14");

        $preview_total = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals(1400.00, $preview_total['total_incvat']);
        $this->assertEquals(1320.75, $preview_total['total_exvat']);
        $this->assertEquals(79.25, $preview_total['total_vat']);

        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals($preview_total['total_incvat'], $response->amount);
    }

    public function test_getRequestTotals_reference_1400_26_inc_behaviour()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1400.26)
                    ->setVatPercent(6)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2016-04-14");

        $preview_total = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals(1400.26, $preview_total['total_incvat']);
        $this->assertEquals(1321.00, $preview_total['total_exvat']);
        $this->assertEquals(79.26, $preview_total['total_vat']);

        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals($preview_total['total_incvat'], $response->amount);
    }

    public function test_getRequestTotals_reference_1321_00_ex_behaviour()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(1321.00)
                    ->setVatPercent(6)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2016-04-14");

        $preview_total = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals(1400.26, $preview_total['total_incvat']);
        $this->assertEquals(1321.00, $preview_total['total_exvat']);
        $this->assertEquals(79.26, $preview_total['total_vat']);

        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals($preview_total['total_incvat'], $response->amount);
    }


    public function test_getRequestTotals_reference_1321_00_ex_with_compensation_row()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(1321.00)
                    ->setVatPercent(6)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2016-04-14");

        $preview_total = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals(1400.26, $preview_total['total_incvat']);
        $this->assertEquals(1321.00, $preview_total['total_exvat']);
        $this->assertEquals(79.26, $preview_total['total_vat']);

        $target_total = 1400.00;
        $compensation_amount = (double)$preview_total['total_incvat'] - $target_total;

        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountIncVat($compensation_amount)
            ->setVatPercent(0)
        );

        $compensated_preview_total = $order->useInvoicePayment()->getRequestTotals();

        $this->assertEquals(1400.00, $compensated_preview_total['total_incvat']);
        $this->assertEquals(1320.74, $compensated_preview_total['total_exvat']);
        $this->assertEquals(79.26, $compensated_preview_total['total_vat']);

        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals($compensated_preview_total['total_incvat'], $response->amount);
        //print_r( "test_getRequestTotals_reference_1321_00_ex_with_compensation_row: " + $response->sveaOrderId );
    }

    public function test_getRequestTotals_reference_1400_26_inc_with_compensation_row()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1400.26)
                    ->setVatPercent(6)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2016-04-14");

        $preview_total = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals(1400.26, $preview_total['total_incvat']);
        $this->assertEquals(1321.00, $preview_total['total_exvat']);
        $this->assertEquals(79.26, $preview_total['total_vat']);

        $target_total = 1400.00;
        $compensation_amount = (double)$preview_total['total_incvat'] - $target_total;

        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountIncVat($compensation_amount)
            ->setVatPercent(0)
        );

        $compensated_preview_total = $order->useInvoicePayment()->getRequestTotals();

        $this->assertEquals(1400.00, $compensated_preview_total['total_incvat']);
        $this->assertEquals(1320.74, $compensated_preview_total['total_exvat']);
        $this->assertEquals(79.26, $compensated_preview_total['total_vat']);

        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals($compensated_preview_total['total_incvat'], $response->amount);
        //print_r( "test_getRequestTotals_reference_1400_26_inc_with_compensation_row: " + $response->sveaOrderId );

    }

    public function test_getRequestTotals_reference_1400_00_inc_cant_be_done_with_compensation_row()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1400.00)
                    ->setVatPercent(6)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2016-04-14");

        $preview_total = $order->useInvoicePayment()->getRequestTotals();
        $this->assertEquals(1400.00, $preview_total['total_incvat']);
        $this->assertEquals(1320.75, $preview_total['total_exvat']);
        $this->assertEquals(79.25, $preview_total['total_vat']);

        $target_total = 1400.00;

        $order->addDiscount(WebPayItem::fixedDiscount()
            ->setAmountIncVat(-0.25)
            ->setVatPercent(0)
        );

        $compensated_preview_total = $order->useInvoicePayment()->getRequestTotals();

        $this->assertEquals(1400.25, $compensated_preview_total['total_incvat']); // should be 1400.00!
        $this->assertEquals(1321.00, $compensated_preview_total['total_exvat']);
        $this->assertEquals(79.25, $compensated_preview_total['total_vat']);

        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals($compensated_preview_total['total_incvat'], $response->amount);
    }
}