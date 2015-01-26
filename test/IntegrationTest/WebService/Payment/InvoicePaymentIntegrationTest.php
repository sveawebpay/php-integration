<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */
class InvoicePaymentIntegrationTest extends PHPUnit_Framework_TestCase {

    public function testInvoiceRequestAccepted() {
        $config = Svea\SveaConfig::getDefaultConfig();
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


    public function testInvoiceRequestNLAcceptedWithDoubleHousenumber() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                    ->addOrderRow(TestUtil::createOrderRow())
                    ->addCustomerDetails(WebPayItem::individualCustomer()
                        ->setBirthDate(1955, 03, 07)
                        ->setName("Sneider", "Boasman")
                        ->setStreetAddress("Gate", "42 23")     // result of splitStreetAddress w/Svea testperson
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


    public function testInvoiceRequestUsingISO8601dateAccepted() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(4605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate( date('c') )
                ->setCurrency("SEK")
                ->useInvoicePayment()
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }


    public function testInvoiceRequestDenied() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
    public function testInvoiceIndividualForDk() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

    public function testInvoiceCompanySE() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

    public function testAcceptsFractionalQuantities() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow( WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(25)
                    ->setQuantity(1.25)
                )
                ->addCustomerDetails( TestUtil::createIndividualCustomer("SE") )
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

    public function testAcceptsIntegerQuantities() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow( WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
                )
                ->addCustomerDetails( TestUtil::createIndividualCustomer("SE") )
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

    // TODO make corresponding tests for other country tax rates
    /**
     * NL vat rates are 6%, 21% (as of 131018, see http://www.government.nl/issues/taxation/vat-and-excise-duty)
     */
    public function t___estNLInvoicePaymentAcceptsVatRates() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow( TestUtil::createOrderRowWithVat( 6 ) )
                ->addOrderRow( TestUtil::createOrderRowWithVat( 21 ) )
                ->addCustomerDetails( TestUtil::createIndividualCustomer("NL") )
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

        public function test_InvoiceFee_ExVatAndVatPercent() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

                ->addCustomerDetails( TestUtil::createCompanyCustomer("SE") )
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

    public function test_InvoiceFee_IncVatAndVatPercent() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

                ->addCustomerDetails( TestUtil::createCompanyCustomer("SE") )
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

    public function test_InvoiceFee_IncVatAndExVat() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

                ->addCustomerDetails( TestUtil::createCompanyCustomer("SE") )
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

    public function test_ShippingFee_ExVatAndVatPercent() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

                ->addCustomerDetails( TestUtil::createCompanyCustomer("SE") )
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

    public function test_ShippingFee_IncVatAndVatPercent() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

                ->addCustomerDetails( TestUtil::createCompanyCustomer("SE") )
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

    public function test_ShippingFee_IncVatAndExVat() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

                ->addCustomerDetails( TestUtil::createCompanyCustomer("SE") )
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

    public function testInvoiceRequest_optional_clientOrderNumber_present_in_response_if_sent() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
        $this->assertEquals(true, isset($request->clientOrderNumber) );
        $this->assertEquals("I_exist!", $request->clientOrderNumber);
    }

    public function testInvoiceRequest_optional_clientOrderNumber_not_present_in_response_if_not_sent() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
        $this->assertEquals(false, isset($request->clientOrderNumber) );
    }

    public function testInvoiceRequest_OrderType_set_in_response_if_useInvoicePayment_set() {
        $config = Svea\SveaConfig::getDefaultConfig();
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

    public function testPriceSetAsExVatAndVatPercent(){
        $config = Svea\SveaConfig::getDefaultConfig();
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
    public function testFixedDiscountSetAsExVat(){
        $config = Svea\SveaConfig::getDefaultConfig();
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

     public function testResponseOrderRowPriceSetAsInkVatAndVatPercentSetAmountAsIncVat(){
        $config = Svea\SveaConfig::getDefaultConfig();
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

      public function testResponseFeeSetAsIncVatAndVatPercentWhenPriceSetAsIncVatAndVatPercent(){
        $config = Svea\SveaConfig::getDefaultConfig();
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

      public function testResponseDiscountSetAsIncVatWhenPriceSetAsIncVatAndVatPercent(){
        $config = Svea\SveaConfig::getDefaultConfig();
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

     public function testResponseDiscountSetAsExVatAndVatPercentWhenPriceSetAsIncVatAndVatPercent(){
        $config = Svea\SveaConfig::getDefaultConfig();
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


    public function testResponseDiscountPercentAndVatPercentWhenPriceSetAsIncVatAndVatPercent(){
        $config = Svea\SveaConfig::getDefaultConfig();
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

     public function testResponseOrderSetAsIncVatAndExVatAndRelativeDiscount(){
        $config = Svea\SveaConfig::getDefaultConfig();
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

    public function testResponseOrderAndFixedDiscountSetWithMixedVat(){
        $config = Svea\SveaConfig::getDefaultConfig();
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
      public function testResponseOrderAndFixedDiscountSetWithMixedVat2(){
        $config = Svea\SveaConfig::getDefaultConfig();
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
      public function testResponseOrderAndFixedDiscountSetWithMixedVat3(){
        $config = Svea\SveaConfig::getDefaultConfig();
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

    public function testTaloonRoundingExVat(){
         $config = Svea\SveaConfig::getDefaultConfig();
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
    public function testTaloonRoundingIncVat(){
         $config = Svea\SveaConfig::getDefaultConfig();
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

    function test_orderRow_discountPercent_not_used() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
//        print_r($orderResponse);

        $query = WebPayAdmin::queryOrder($config)
                ->setCountryCode('SE')
                ->setOrderId($orderResponse->sveaOrderId)
                ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals(100.00, $query->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25.00, $query->numberedOrderRows[0]->vatPercent);
        $this->assertEquals(0.00, $query->numberedOrderRows[0]->discountPercent);    
    }

    function test_orderRow_discountPercent_50percent() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
    
    function test_orderRow_discountPercent_50_percent_order_sent_as_incvat() {
        $config = Svea\SveaConfig::getDefaultConfig();
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
        $this->assertEquals("62.50", $orderResponse->amount);   // this is where 

        $query = WebPayAdmin::queryOrder($config)
                ->setCountryCode('SE')
                ->setOrderId($orderResponse->sveaOrderId)
                ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $query->accepted);
        $this->assertEquals(125.00, $query->numberedOrderRows[0]->amountIncVat);
        $this->assertEquals(25.00, $query->numberedOrderRows[0]->vatPercent);    
        $this->assertEquals(50.00, $query->numberedOrderRows[0]->discountPercent);    
    }
}