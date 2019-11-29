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

/**
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen, Fredrik Sundell for Svea Webpay
 */
class InvoicePaymentIntegrationTest extends \PHPUnit\Framework\TestCase
{

    // order with order/fee rows mixed exvat+vat / incvat+vat should be sent with PriceIncludingVat = false
    public function test_mixed_order_row_and_shipping_fees_only_has_priceIncludingVat_false()
    {
        $order = $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
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

    public function testInvoiceRequestwithPeppolId()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(
                WebPayItem::companyCustomer()
                    ->setNationalIdNumber(194608142222))
            ->setOrderDate("2019-04-01")
            ->setCountryCode("SE")
            ->setPeppolId("1234:asdf")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    public function testInvoiceRequestAccepted()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setNationalIdNumber(4605092222)
            )
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }


    public function testInvoiceRequestNLAcceptedWithDoubleHousenumber()
    {
        $this->markTestIncomplete("NL flow not maintained by webpay-dev");

        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setBirthDate(1955, 03, 07)
                ->setName("Sneider", "Boasman")
                ->setStreetAddress("Gate 42", "23")// result of splitStreetAddress w/Svea testperson
                ->setCoAddress(138)
                ->setLocality("BARENDRECHT")
                ->setZipCode("1102 HG")
                ->setInitials("SB")
            )
            ->setCountryCode("NL")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }


    public function testInvoiceRequestUsingISO8601dateAccepted()
    {
        $this->markTestIncomplete("NL flow not maintained by webpay-dev");

        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(4605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate(date('c'))
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }


    public function testInvoiceRequestDenied()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(4606082222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(0, $request->accepted);
    }

    //Turned off?
    public function testInvoiceIndividualForDk()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(2603692503))
            ->setCountryCode("DK")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("DKK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    public function testInvoiceCompanySE()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::companyCustomer()->setNationalIdNumber(4608142222))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(true, $request->accepted);
    }

    public function testAcceptsFractionalQuantities()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(80.00)
                ->setVatPercent(25)
                ->setQuantity(1.25)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("EUR")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals('Invoice', $request->orderType);
        $this->assertEquals(1, $request->sveaWillBuyOrder);
        $this->assertEquals(125, $request->amount);
    }

    public function testAcceptsIntegerQuantities()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                ->setAmountExVat(80.00)
                ->setVatPercent(25)
                ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("EUR")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals('Invoice', $request->orderType);
        $this->assertEquals(1, $request->sveaWillBuyOrder);
        $this->assertEquals(100, $request->amount);
    }

    /**
     * NL vat rates are 6%, 21% (as of 131018, see https://www.government.nl/topics/vat/vat-rates-and-exemptions)
     */
    public function t___estNLInvoicePaymentAcceptsVatRates()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRowWithVat(6))
            ->addOrderRow(TestUtil::createOrderRowWithVat(21))
            ->addCustomerDetails(TestUtil::createIndividualCustomer("NL"))
            ->setCountryCode("NL")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("EUR")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals('Invoice', $request->orderType);
        $this->assertEquals(1, $request->sveaWillBuyOrder);
        $this->assertEquals(106 + 121, $request->amount);           // 1x100 @ 6% vat + 1x100 @ 21%
        $this->assertEquals('', $request->customerIdentity->email);
        $this->assertEquals('', $request->customerIdentity->ipAddress);
        $this->assertEquals('NL', $request->customerIdentity->countryCode);
        $this->assertEquals(23, $request->customerIdentity->houseNumber);
        $this->assertEquals('Individual', $request->customerIdentity->customerType);
        $this->assertEquals('', $request->customerIdentity->phoneNumber);
        $this->assertEquals('Sneider Boasman', $request->customerIdentity->fullName);
        $this->assertEquals('Gate 42', $request->customerIdentity->street);
        $this->assertEquals(138, $request->customerIdentity->coAddress);
        $this->assertEquals('1102 HG', $request->customerIdentity->zipCode);
        $this->assertEquals('BARENDRECHT', $request->customerIdentity->locality);
    }

    /**
     * make sure opencart bug w/corporate invoice payments for one 25% vat product with free shipping (0% vat)
     * resulting in request with illegal vat rows of 24% not originating in integration package
     */

    public function test_InvoiceFee_ExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(2032.80)
            ->setVatPercent(25)
            ->setQuantity(1)
        )
            ->addOrderRow(WebPayItem::shippingFee()
                ->setAmountExVat(0.00)
                ->setVatPercent(0)
            )
            ->addOrderRow(WebPayItem::invoiceFee()
                ->setAmountExVat(29.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createCompanyCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2013-10-28")
            ->setCurrency("SEK");

        // asserts on request
        $request = $order->useInvoicePayment()->prepareRequest();

        $newRows = $request->request->CreateOrderInformation->OrderRows['OrderRow'];

        $newRow = $newRows[0];
        $this->assertEquals(2032.80, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[1];
        $this->assertEquals(0, $newRow->PricePerUnit);
        $this->assertEquals(0, $newRow->VatPercent);

        $newRow = $newRows[2];
        $this->assertEquals(29.00, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        // asserts on result
        $result = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $result->accepted);
        $this->assertEquals(0, $result->resultcode);
        $this->assertEquals('Invoice', $result->orderType);
        $this->assertEquals(1, $result->sveaWillBuyOrder);
        $this->assertEquals(2577.25, $result->amount);

    }

    public function test_InvoiceFee_IncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
        )
            ->addOrderRow(WebPayItem::shippingFee()
                ->setAmountIncVat(0.00)
                ->setVatPercent(0)
            )
            ->addOrderRow(WebPayItem::invoiceFee()
                ->setAmountIncVat(29.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createCompanyCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2013-10-28")
            ->setCurrency("SEK");

        // asserts on request
        $request = $order->useInvoicePayment()->prepareRequest();

        $newRows = $request->request->CreateOrderInformation->OrderRows['OrderRow'];

        $newRow = $newRows[0];
        $this->assertEquals(100, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[1];
        $this->assertEquals(0, $newRow->PricePerUnit);
        $this->assertEquals(0, $newRow->VatPercent);

        $newRow = $newRows[2];
        $this->assertEquals(23.20, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        // asserts on result
        $result = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $result->accepted);
        $this->assertEquals(0, $result->resultcode);
        $this->assertEquals('Invoice', $result->orderType);
        $this->assertEquals(1, $result->sveaWillBuyOrder);
        $this->assertEquals(154, $result->amount);
    }

    public function test_InvoiceFee_IncVatAndExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
        )
            ->addOrderRow(WebPayItem::shippingFee()
                ->setAmountIncVat(0.00)
                ->setVatPercent(0)
            )
            ->addOrderRow(WebPayItem::invoiceFee()
                ->setAmountIncVat(29.00)
                ->setAmountExVat(23.20)
            )
            ->addCustomerDetails(TestUtil::createCompanyCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2013-10-28")
            ->setCurrency("SEK");

        // asserts on request
        $request = $order->useInvoicePayment()->prepareRequest();
        $newRows = $request->request->CreateOrderInformation->OrderRows['OrderRow'];

        $newRow = $newRows[0];
        $this->assertEquals(100, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[1];
        $this->assertEquals(0, $newRow->PricePerUnit);
        $this->assertEquals(0, $newRow->VatPercent);

        $newRow = $newRows[2];
        $this->assertEquals(23.20, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        // asserts on result
        $result = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $result->accepted);
        $this->assertEquals(0, $result->resultcode);
        $this->assertEquals('Invoice', $result->orderType);
        $this->assertEquals(1, $result->sveaWillBuyOrder);
        $this->assertEquals(154, $result->amount);
    }

    public function test_ShippingFee_ExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
        )
            ->addOrderRow(WebPayItem::shippingFee()
                ->setAmountExVat(20.00)
                ->setVatPercent(6)
            )
            ->addOrderRow(WebPayItem::invoiceFee()
                ->setAmountExVat(23.20)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createCompanyCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2013-10-28")
            ->setCurrency("SEK");

        // asserts on request
        $request = $order->useInvoicePayment()->prepareRequest();

        $newRows = $request->request->CreateOrderInformation->OrderRows['OrderRow'];

        $newRow = $newRows[0];
        $this->assertEquals(100, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[1];
        $this->assertEquals(20.00, $newRow->PricePerUnit);
        $this->assertEquals(6, $newRow->VatPercent);

        $newRow = $newRows[2];
        $this->assertEquals(23.20, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        // asserts on result
        $result = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $result->accepted);
        $this->assertEquals(0, $result->resultcode);
        $this->assertEquals('Invoice', $result->orderType);
        $this->assertEquals(1, $result->sveaWillBuyOrder);
        $this->assertEquals(175.2, $result->amount);
    }

    public function test_ShippingFee_IncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
        )
            ->addOrderRow(WebPayItem::shippingFee()
                ->setAmountIncVat(21.20)
                ->setVatPercent(6)
            )
            ->addOrderRow(WebPayItem::invoiceFee()
                ->setAmountExVat(23.20)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createCompanyCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2013-10-28")
            ->setCurrency("SEK");

        // asserts on request
        $request = $order->useInvoicePayment()->prepareRequest();

        $newRows = $request->request->CreateOrderInformation->OrderRows['OrderRow'];

        $newRow = $newRows[0];
        $this->assertEquals(100, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[1];
        $this->assertEquals(20.00, $newRow->PricePerUnit);
        $this->assertEquals(6, $newRow->VatPercent);

        $newRow = $newRows[2];
        $this->assertEquals(23.20, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        // asserts on result
        $result = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $result->accepted);
        $this->assertEquals(0, $result->resultcode);
        $this->assertEquals('Invoice', $result->orderType);
        $this->assertEquals(1, $result->sveaWillBuyOrder);
        $this->assertEquals(175.2, $result->amount);
    }

    public function test_ShippingFee_IncVatAndExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config);
        $order->addOrderRow(WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
        )
            ->addOrderRow(WebPayItem::shippingFee()
                ->setAmountExVat(20.00)
                ->setAmountIncVat(21.20)
            )
            ->addOrderRow(WebPayItem::invoiceFee()
                ->setAmountExVat(23.20)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createCompanyCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2013-10-28")
            ->setCurrency("SEK");

        // asserts on request
        $request = $order->useInvoicePayment()->prepareRequest();

        $newRows = $request->request->CreateOrderInformation->OrderRows['OrderRow'];

        $newRow = $newRows[0];
        $this->assertEquals(100, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        $newRow = $newRows[1];
        $this->assertEquals(20.00, $newRow->PricePerUnit);
        $this->assertEquals(6, $newRow->VatPercent);

        $newRow = $newRows[2];
        $this->assertEquals(23.20, $newRow->PricePerUnit);
        $this->assertEquals(25, $newRow->VatPercent);

        // asserts on result
        $result = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $result->accepted);
        $this->assertEquals(0, $result->resultcode);
        $this->assertEquals('Invoice', $result->orderType);
        $this->assertEquals(1, $result->sveaWillBuyOrder);
        $this->assertEquals(175.2, $result->amount);
    }

    public function testInvoiceRequest_optional_clientOrderNumber_present_in_response_if_sent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setNationalIdNumber(4605092222)
            )
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->setClientOrderNumber("I_exist!")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(true, isset($request->clientOrderNumber));
        $this->assertEquals("I_exist!", $request->clientOrderNumber);
    }

    public function testInvoiceRequest_optional_clientOrderNumber_not_present_in_response_if_not_sent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setNationalIdNumber(4605092222)
            )
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(false, isset($request->clientOrderNumber));
    }

    public function testInvoiceRequest_OrderType_set_in_response_if_useInvoicePayment_set()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals("Invoice", $request->orderType);
    }

    /**
     * Tests for rounding**
     */

    public function testPriceSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

    public function testFixedDiscountSetAsExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(8)
                ->setVatPercent(0))
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(24)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

    public function testResponseOrderRowPriceSetAsInkVatAndVatPercentSetAmountAsIncVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

    public function testResponseFeeSetAsIncVatAndVatPercentWhenPriceSetAsIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(100.00)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(100.00)
                ->setVatPercent(24)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }

    public function testResponseDiscountSetAsIncVatWhenPriceSetAsIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()->setAmountIncVat(10))
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);


    }

    public function testResponseDiscountSetAsExVatAndVatPercentWhenPriceSetAsIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(10)
                ->setVatPercent(0))
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }


    public function testResponseDiscountPercentAndVatPercentWhenPriceSetAsIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }

    public function testResponseOrderSetAsIncVatAndExVatAndRelativeDiscount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }

    public function testResponseOrderAndFixedDiscountSetWithMixedVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(9.999)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }

    public function testResponseOrderAndFixedDiscountSetWithMixedVat2()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(12.39876)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }

    public function testResponseOrderAndFixedDiscountSetWithMixedVat3()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(9.999)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }

    public function testTaloonRoundingExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(116.94)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(7.26)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(4.03)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("FI"))
            ->setCountryCode("FI")
            ->setCurrency("EUR")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(159.01, $request->amount);//sends the old way, so still wrong rounding

    }

    public function testTaloonRoundingIncVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(145.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(9.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(5.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("FI"))
            ->setCountryCode("FI")
            ->setCurrency("EUR")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(159.0, $request->amount);

    }

    // Test that test suite returns complete address in each country
    // SE
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_SE()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("4605092222")
            )
            ->setCountryCode("SE")
            ->setOrderDate(date('c'));
        $response = $order->useInvoicePayment()->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertTrue($response->customerIdentity instanceof CreateOrderIdentity);
        // verify returned address
        $this->assertEquals("Persson Tess T", $response->customerIdentity->fullName);    // Note: order may vary between countries, given by UC
        $this->assertEquals("Testgatan 1", $response->customerIdentity->street);
        $this->assertEquals("c/o Eriksson, Erik", $response->customerIdentity->coAddress);
        $this->assertEquals("99999", $response->customerIdentity->zipCode);
        $this->assertEquals("Stan", $response->customerIdentity->locality);
    }

    // NO
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_NO()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("17054512066")
            )
            ->setCountryCode("NO")
            ->setOrderDate(date('c'));
        $response = $order->useInvoicePayment()->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertTrue($response->customerIdentity instanceof CreateOrderIdentity);
        // verify returned address
        $this->assertEquals("Ola Normann", $response->customerIdentity->fullName);    // Note: order may vary between countries, given by UC
        $this->assertEquals("Testveien 2", $response->customerIdentity->street);
        $this->assertEquals("", $response->customerIdentity->coAddress);
        $this->assertEquals("0359", $response->customerIdentity->zipCode);
        $this->assertEquals("Oslo", $response->customerIdentity->locality);
    }

    // DK
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_DK()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("2603692503")
            )
            ->setCountryCode("DK")
            ->setOrderDate(date('c'));
        $response = $order->useInvoicePayment()->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertTrue($response->customerIdentity instanceof CreateOrderIdentity);
        // verify returned address
        $this->assertEquals("Hanne Jensen", $response->customerIdentity->fullName);    // Note: order may vary between countries, given by UC
        $this->assertEquals("Testvejen 42", $response->customerIdentity->street);
        $this->assertEquals("c/o Test A/s", $response->customerIdentity->coAddress);
        $this->assertEquals("2100", $response->customerIdentity->zipCode);
        $this->assertEquals("Københvn Ø", $response->customerIdentity->locality);
    }

    // FI
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_FI()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setNationalIdNumber("160264-999N")
            )
            ->setCountryCode("FI")
            ->setOrderDate(date('c'));
        $response = $order->useInvoicePayment()->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertTrue($response->customerIdentity instanceof CreateOrderIdentity);
        // verify returned address
        $this->assertEquals("Kanerva Haapakoski Kukka-Maaria", $response->customerIdentity->fullName);    // Note: order may vary between countries, given by UC
        $this->assertEquals("Atomitie 2 C", $response->customerIdentity->street);
        $this->assertEquals("", $response->customerIdentity->coAddress);
        $this->assertEquals("00370", $response->customerIdentity->zipCode);
        $this->assertEquals("Helsinki", $response->customerIdentity->locality);
    }

    // DE
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_DE()
    {
        $this->markTestIncomplete("NL flow not maintained by webpay-dev");

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
                    ->setDescription("IntegrationTest")
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setBirthDate("19680403")
                    ->setName("Theo", "Giebel")
                    ->setStreetAddress("Zörgiebelweg", 21)
                    ->setZipCode("13591")
                    ->setLocality("BERLIN")
            )
            ->setCountryCode("DE")
            ->setOrderDate(date('c'));
        $response = $order->useInvoicePayment()->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertTrue($response->customerIdentity instanceof CreateOrderIdentity);
        // verify returned address
        $this->assertEquals("Theo Giebel", $response->customerIdentity->fullName);    // Note: order may vary between countries, given by UC
        $this->assertEquals("Zörgiebelweg", $response->customerIdentity->street);
        $this->assertEquals("21", $response->customerIdentity->houseNumber);
        $this->assertEquals("", $response->customerIdentity->coAddress);
        $this->assertEquals("13591", $response->customerIdentity->zipCode);
        $this->assertEquals("BERLIN", $response->customerIdentity->locality);
    }

    // NL
    // IndividualCustomer validation
    function test_validates_all_required_methods_for_createOrder_useInvoicePayment_IndividualCustomer_NL()
    {
        $this->markTestIncomplete("NL flow not maintained by webpay-dev");

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setQuantity(1.0)
                    ->setAmountExVat(4.0)
                    ->setAmountIncVat(5.0)
                    ->setDescription("IntegrationTest")
            )
            ->addCustomerDetails(
                WebPayItem::individualCustomer()
                    ->setBirthDate("19550307")
                    ->setInitials("SB")
                    ->setName("Sneider", "Boasman")
                    ->setStreetAddress("Gate 42", 23)
                    ->setZipCode("1102 HG")
                    ->setLocality("BARENDRECHT")
            )
            ->setCountryCode("NL")
            ->setOrderDate(date('c'));
        $response = $order->useInvoicePayment()->doRequest();
        //print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertTrue($response->customerIdentity instanceof CreateOrderIdentity);
        // verify returned address
        $this->assertEquals("Sneider Boasman", $response->customerIdentity->fullName);    // Note: order may vary between countries, given by UC
        $this->assertEquals("Gate 42", $response->customerIdentity->street);
        $this->assertEquals("23", $response->customerIdentity->houseNumber);
        $this->assertEquals("", $response->customerIdentity->coAddress);
        $this->assertEquals("1102 HG", $response->customerIdentity->zipCode);
        $this->assertEquals("BARENDRECHT", $response->customerIdentity->locality);
    }

    public function testInvoiceRequestNLReturnsSameAddress()
    {
        $this->markTestIncomplete("NL flow not maintained by webpay-dev");

        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setBirthDate(1955, 03, 07)
                ->setName("Sneider", "Boasman")
                ->setStreetAddress("Gate 42", "23")// result of splitStreetAddress w/Svea testperson
                ->setCoAddress(138)
                ->setLocality("BARENDRECHT")
                ->setZipCode("1102 HG")
                ->setInitials("SB")
            )
            ->setCountryCode("NL")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertTrue($request->customerIdentity instanceof CreateOrderIdentity);
        // verify returned address
        $this->assertEquals("Sneider Boasman", $request->customerIdentity->fullName);
        $this->assertEquals("Gate 42", $request->customerIdentity->street);
        $this->assertEquals("23", $request->customerIdentity->houseNumber);
        $this->assertEquals("1102 HG", $request->customerIdentity->zipCode);
        $this->assertEquals("BARENDRECHT", $request->customerIdentity->locality);
    }

    public function testInvoiceRequestNLReturnsCorrectAddress()
    {
        $this->markTestIncomplete("NL flow not maintained by webpay-dev");

        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setBirthDate(1955, 03, 07)
                ->setName("Sneider", "Boasman")
                ->setStreetAddress("Gate 42", "23")// result of splitStreetAddress w/Svea testperson
                ->setCoAddress(138)
                ->setLocality("BARENDRECHT")
                ->setZipCode("1102 HG")
                ->setInitials("SB")
            )
            ->setCountryCode("NL")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertTrue($request->customerIdentity instanceof CreateOrderIdentity);
        // verify returned address
        $this->assertEquals("Sneider Boasman", $request->customerIdentity->fullName);
        $this->assertEquals("Gate 42", $request->customerIdentity->street);
        $this->assertEquals("23", $request->customerIdentity->houseNumber);
        $this->assertEquals("1102 HG", $request->customerIdentity->zipCode);
        $this->assertEquals("BARENDRECHT", $request->customerIdentity->locality);

        //<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="https://webservices.sveaekonomi.se/webpay">
        //  <SOAP-ENV:Body>
        //    <ns1:CreateOrderEu>
        //      <ns1:request>
        //        <ns1:Auth>
        //          <ns1:ClientNumber>85997</ns1:ClientNumber>
        //          <ns1:Username>hollandtest</ns1:Username>
        //          <ns1:Password>hollandtest</ns1:Password>
        //        </ns1:Auth>
        //        <ns1:CreateOrderInformation>
        //          <ns1:OrderRows>
        //            <ns1:OrderRow>
        //              <ns1:ArticleNumber>1</ns1:ArticleNumber>
        //              <ns1:Description>Product: Specification</ns1:Description>
        //              <ns1:PricePerUnit>100</ns1:PricePerUnit>
        //              <ns1:PriceIncludingVat>false</ns1:PriceIncludingVat>
        //              <ns1:NumberOfUnits>2</ns1:NumberOfUnits>
        //              <ns1:Unit>st</ns1:Unit>
        //              <ns1:VatPercent>25</ns1:VatPercent>
        //              <ns1:DiscountPercent>0</ns1:DiscountPercent>
        //            </ns1:OrderRow>
        //          </ns1:OrderRows>
        //          <ns1:CustomerIdentity>
        //            <ns1:Email>
        //            </ns1:Email>
        //            <ns1:PhoneNumber>
        //            </ns1:PhoneNumber>
        //            <ns1:IpAddress>
        //            </ns1:IpAddress>
        //            <ns1:FullName>Sneider Boasman</ns1:FullName>
        //            <ns1:Street>Gate 42</ns1:Street>
        //            <ns1:CoAddress>138</ns1:CoAddress>
        //            <ns1:ZipCode>1102 HG</ns1:ZipCode>
        //            <ns1:HouseNumber>23</ns1:HouseNumber>
        //            <ns1:Locality>BARENDRECHT</ns1:Locality>
        //            <ns1:CountryCode>NL</ns1:CountryCode>
        //            <ns1:CustomerType>Individual</ns1:CustomerType>
        //            <ns1:IndividualIdentity>
        //              <ns1:FirstName>Sneider</ns1:FirstName>
        //              <ns1:LastName>Boasman</ns1:LastName>
        //              <ns1:Initials>SB</ns1:Initials>
        //              <ns1:BirthDate>19550307</ns1:BirthDate>
        //            </ns1:IndividualIdentity>
        //          </ns1:CustomerIdentity>
        //          <ns1:OrderDate>2012-12-12</ns1:OrderDate>
        //          <ns1:AddressSelector>
        //          </ns1:AddressSelector>
        //          <ns1:CustomerReference>33</ns1:CustomerReference>
        //          <ns1:OrderType>Invoice</ns1:OrderType>
        //        </ns1:CreateOrderInformation>
        //      </ns1:request>
        //    </ns1:CreateOrderEu>
        //  </SOAP-ENV:Body>
        //</SOAP-ENV:Envelope>
    }

    public function testInvoiceRequestNLReproduceErrorIn471193()
    {
        $this->markTestIncomplete("NL flow not maintained by webpay-dev");

        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setBirthDate(1955, 03, 07)// BirthDate and ZipCode is sufficient for a successful test order
                ->setZipCode("1102 HG")//
                ->setName("foo", "bar")
                ->setStreetAddress("foo", "bar")
                ->setCoAddress(1337)
                ->setLocality("dns")
                ->setInitials("nsl")
            )
            ->setCountryCode("NL")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();
        //->prepareRequest();
        //var_dump($request->request->CreateOrderInformation->CustomerIdentity);

        $this->assertEquals(1, $request->accepted);
        $this->assertTrue($request->customerIdentity instanceof CreateOrderIdentity);

        //print_r( $request->sveaOrderId);
        // verify returned address is wrong
        $this->assertNotEquals("Sneider Boasman", $request->customerIdentity->fullName);
        $this->assertNotEquals("Gate 42", $request->customerIdentity->street);
        $this->assertNotEquals("23", $request->customerIdentity->houseNumber);
        $this->assertNotEquals("BARENDRECHT", $request->customerIdentity->locality);
        //$this->assertNotEquals( "1102 HG", $request->customerIdentity->zipCode );

        $this->assertEquals("foo bar", $request->customerIdentity->fullName);
        $this->assertEquals("foo", $request->customerIdentity->street);
        $this->assertEquals("bar", $request->customerIdentity->houseNumber);
        $this->assertEquals("dns", $request->customerIdentity->locality);
        $this->assertEquals("1102 HG", $request->customerIdentity->zipCode);


        //<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="https://webservices.sveaekonomi.se/webpay" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        //  <SOAP-ENV:Body>
        //    <ns1:CreateOrderEu>
        //      <ns1:request>
        //        <ns1:Auth>
        //          <ns1:ClientNumber>85997</ns1:ClientNumber>
        //          <ns1:Username>hollandtest</ns1:Username>
        //          <ns1:Password>hollandtest</ns1:Password>
        //        </ns1:Auth>
        //        <ns1:CreateOrderInformation>
        //          <ns1:ClientOrderNumber>133</ns1:ClientOrderNumber>
        //          <ns1:OrderRows>
        //            <ns1:OrderRow>
        //              <ns1:ArticleNumber>NTB03</ns1:ArticleNumber>
        //              <ns1:Description>Making candles and soaps for dummies: Making candles and soaps for dummies</ns1:Description>
        //              <ns1:PricePerUnit>12.12</ns1:PricePerUnit>
        //              <ns1:PriceIncludingVat xsi:nil="true" />
        //              <ns1:NumberOfUnits>2</ns1:NumberOfUnits>
        //              <ns1:Unit>st</ns1:Unit>
        //              <ns1:VatPercent>25</ns1:VatPercent>
        //              <ns1:DiscountPercent>0</ns1:DiscountPercent>
        //            </ns1:OrderRow>
        //            <ns1:OrderRow>
        //              <ns1:ArticleNumber>SHIP25</ns1:ArticleNumber>
        //              <ns1:Description>Frakt / Shipping</ns1:Description>
        //              <ns1:PricePerUnit>8.66</ns1:PricePerUnit>
        //              <ns1:PriceIncludingVat xsi:nil="true" />
        //              <ns1:NumberOfUnits>1</ns1:NumberOfUnits>
        //              <ns1:Unit>st</ns1:Unit>
        //              <ns1:VatPercent>25</ns1:VatPercent>
        //              <ns1:DiscountPercent>0</ns1:DiscountPercent>
        //            </ns1:OrderRow>
        //            <ns1:OrderRow>
        //              <ns1:ArticleNumber>HAND25</ns1:ArticleNumber>
        //              <ns1:Description>Expeditionsavgift / Handling</ns1:Description>
        //              <ns1:PricePerUnit>2.51</ns1:PricePerUnit>
        //              <ns1:PriceIncludingVat xsi:nil="true" />
        //              <ns1:NumberOfUnits>1</ns1:NumberOfUnits>
        //              <ns1:Unit>st</ns1:Unit>
        //              <ns1:VatPercent>25</ns1:VatPercent>
        //              <ns1:DiscountPercent>0</ns1:DiscountPercent>
        //            </ns1:OrderRow>
        //          </ns1:OrderRows>
        //          <ns1:CustomerIdentity>
        //            <ns1:Email>
        //            </ns1:Email>
        //            <ns1:PhoneNumber>
        //            </ns1:PhoneNumber>
        //            <ns1:IpAddress>
        //            </ns1:IpAddress>
        //            <ns1:FullName>asdf ghij</ns1:FullName>
        //            <ns1:Street>Postbus</ns1:Street>
        //            <ns1:CoAddress>
        //            </ns1:CoAddress>
        //            <ns1:ZipCode>1010 AB</ns1:ZipCode>
        //            <ns1:HouseNumber>626</ns1:HouseNumber>
        //            <ns1:Locality>Amsterdam</ns1:Locality>
        //            <ns1:CountryCode>NL</ns1:CountryCode>
        //            <ns1:CustomerType>Individual</ns1:CustomerType>
        //            <ns1:IndividualIdentity>
        //              <ns1:FirstName>asdf</ns1:FirstName>
        //              <ns1:LastName>ghij</ns1:LastName>
        //              <ns1:Initials>ag</ns1:Initials>
        //              <ns1:BirthDate>19550307</ns1:BirthDate>
        //            </ns1:IndividualIdentity>
        //          </ns1:CustomerIdentity>
        //          <ns1:OrderDate>2014-11-19</ns1:OrderDate>
        //          <ns1:AddressSelector>
        //          </ns1:AddressSelector>
        //          <ns1:OrderType>Invoice</ns1:OrderType>
        //        </ns1:CreateOrderInformation>
        //      </ns1:request>
        //    </ns1:CreateOrderEu>
        //  </SOAP-ENV:Body>
        //</SOAP-ENV:Envelope>

    }

    function test_orderRow_discountPercent_not_used()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(100.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);
        $this->assertEquals("125.00", $orderResponse->amount);
        //print_r($orderResponse);

        $query = WebPayAdmin::queryOrder($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals(100.00, $query->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25.00, $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals(0.00, $query->numberedOrderRows[0]->discountPercent);
    }

    function test_orderRow_discountPercent_50percent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(100.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
                    ->setDiscountPercent(50)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);
        $this->assertEquals("62.50", $orderResponse->amount);

        $query = WebPayAdmin::queryOrder($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals(100.00, $query->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25.00, $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals(50.00, $query->numberedOrderRows[0]->discountPercent);
    }

    function test_orderRow_discountPercent_50_percent_order_sent_as_incvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(125.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
                    ->setDiscountPercent(50)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);
        $this->assertEquals("62.5", $orderResponse->amount);   // this is where

        $query = WebPayAdmin::queryOrder($config)
            ->setCountryCode('SE')
            ->setOrderId($orderResponse->sveaOrderId)
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals(125.00, $query->numberedOrderRows[0]->amountIncVat);
        $this->assertEquals(25.00, $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals(50.00, $query->numberedOrderRows[0]->discountPercent);
    }

    // fixed discount -- created discount rows should use incvat + vatpercent
    /// fixed discount examples:
    // single order rows vat rate
    public function test_fixedDiscount_amount_with_incvat_vat_rate_creates_discount_rows_using_incvat_and_vatpercent()
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
            )
            ->addDiscount(
                WebPayItem::fixedDiscount()
                    ->setAmountExVat(10.0)
                    ->setVatPercent(10)
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

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
    }

    // single order rows vat rate
    public function test_fixedDiscount_amount_with_exvat_vat_rate_creates_discount_rows_using_incvat_and_vatpercent()
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
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        // all discount rows
        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);

        $response = $order->useInvoicePayment()->doRequest();
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
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(9.999)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");

        $request = $order->useInvoicePayment()->prepareRequest();

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        // 9.999 *1.24 = 12.39876
        $this->assertEquals(-9.999, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
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
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(9.999)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");
        $request = $order->useInvoicePayment()->prepareRequest();

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        // 9.999 *1.24 = 12.39876
        $this->assertEquals(-9.999, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        // check that service accepts order
        $response = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(true, $response->accepted);
    }
}