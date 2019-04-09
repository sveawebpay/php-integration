<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\WebService\Payment;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Test\TestUtil;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\WebService\WebServiceResponse\CustomerIdentity\CreateOrderIdentity;

class AccountCreditPaymentIntegrationTest extends \PHPUnit\Framework\TestCase
{
    public function getCustomerRow()
    {
        return WebPayItem::individualCustomer()
            ->setNationalIdNumber("194605092222")
            ->setBirthDate(1946, 05, 9)
            ->setName("Tess T", "Persson")
            ->setStreetAddress("Testgatan", 1)
            ->setCoAddress("c/o Eriksson, Erik")
            ->setLocality("Stan")
            ->setEmail('testt@svea.com')->
            setZipCode("99999");
    }

    // single order rows vat rate
    public function test_fixedDiscount_amount_with_exvat_vat_rate_creates_discount_rows_using_incvat_and_vatpercent()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addCustomerDetails($this->getCustomerRow())
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
                    ->setAmountExVat(30.00)
                    ->setVatPercent(10)
                    ->setQuantity(1)
                    ->setName("exvatRow2")
            )
            ->addFee(
                WebPayItem::invoiceFee()
                    ->setAmountExVat(8.00)
                    ->setVatPercent(10)
                    ->setName("exvatInvoiceFee")
            )
            ->addFee(
                WebPayItem::shippingFee()
                    ->setAmountExVat(16.00)
                    ->setVatPercent(10)
                    ->setName("exvatShippingFee")
            )
            ->addDiscount(
                WebPayItem::fixedDiscount()
                    ->setAmountExVat(10.0)
                    ->setVatPercent(10)
                    ->setDiscountId("ElevenCrownsOff")
                    ->setName("fixedDiscount: 10 @10% => 11kr")
            );
        $request = $order->useAccountCredit('111111')->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        // all discount rows
        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);

        $response = $order->useAccountCredit('111111')->doRequest();
        $this->assertEquals(true, $response->accepted);
    }

    // fixed discount -- created discount rows should use incvat + vatpercent
    /// fixed discount examples:
    // single order rows vat rate
    public function test_fixedDiscount_amount_with_incvat_vat_rate_creates_discount_rows_using_incvat_and_vatpercent()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addCustomerDetails($this->getCustomerRow())
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
            )
            ->addDiscount(
                WebPayItem::fixedDiscount()
                    ->setAmountExVat(10.0)
                    ->setVatPercent(10)
                    ->setDiscountId("ElevenCrownsOff")
                    ->setName("fixedDiscount: 10 @10% => 11kr")
            );
        $request = $order->useAccountCredit('111111')->prepareRequest();
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

        // check that service accepts order
        $response = $order->useAccountCredit('111111')->doRequest();
        $this->assertEquals(true, $response->accepted);
    }

    public function testOrderAndFixedDiscountSetWithMixedVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
                    ->setDescription("Test")
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDescription("TestDiscount")
                ->setAmountExVat(9.999)
            )
            ->addCustomerDetails($this->getCustomerRow())
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");

        $request = $order->useAccountCredit('111111')->prepareRequest();

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        // 9.999 *1.24 = 12.39876
        $this->assertEquals(-9.999, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useAccountCredit('111111')->doRequest();
        $this->assertEquals(true, $response->accepted);
    }

    public function testOrderAndFixedDiscountSetWithMixedVat3()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
                    ->setDescription("Test")
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(9.999)
                ->setDescription("TestDiscount")
            )
            ->addCustomerDetails($this->getCustomerRow())
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");
        $request = $order->useAccountCredit('111111')->prepareRequest();

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        // 9.999 *1.24 = 12.39876
        $this->assertEquals(-9.999, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useAccountCredit('111111')->doRequest();
        $this->assertEquals(true, $response->accepted);
    }
}