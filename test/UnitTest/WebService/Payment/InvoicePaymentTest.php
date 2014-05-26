<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class InvoicePaymentTest extends PHPUnit_Framework_TestCase {

     public function testInvoiceRequestObjectForCustomerIdentityIndividualFromSE() {
         $config = Svea\SveaConfig::getDefaultConfig();
         $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                    ->prepareRequest();

        $this->assertEquals(194605092222, $request->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber); //Check all in identity
        $this->assertEquals("SE", $request->request->CreateOrderInformation->CustomerIdentity->CountryCode); //Check all in identity
        $this->assertEquals("Individual", $request->request->CreateOrderInformation->CustomerIdentity->CustomerType); //Check all in identity
    }

    public function testInvoiceRequestOnProductVatCero() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(0)
                ->setDiscountPercent(0)
                )
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                    ->prepareRequest();

        $this->assertEquals(100, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit); //Check all in identity
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent); //Check all in identity
    }

    public function testSetAuth() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                //->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)
                ->prepareRequest();

        $this->assertEquals(79021, $request->request->Auth->ClientNumber); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Username); //Check all in identity
        $this->assertEquals("sverigetest", $request->request->Auth->Password); //Check all in identity
    }

    public function testInvoiceRequestObjectForCustomerIdentityIndividualFromNL() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(WebPayItem::individualCustomer()
                        ->setInitials("SB")
                        ->setBirthDate(1923, 12, 12)
                        ->setName("Tess", "Testson")
                        ->setEmail("test@svea.com")
                        ->setPhoneNumber(999999)
                        ->setIpAddress("123.123.123")
                        ->setStreetAddress("Gatan", 23)
                        ->setCoAddress("c/o Eriksson")
                        ->setZipCode(9999)
                        ->setLocality("Stan")
                )
                ->setCountryCode("NL")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                ->prepareRequest();

        $this->assertEquals("test@svea.com", $request->request->CreateOrderInformation->CustomerIdentity->Email); //Check all in identity
        $this->assertEquals(999999, $request->request->CreateOrderInformation->CustomerIdentity->PhoneNumber); //Check all in identity
        $this->assertEquals("123.123.123", $request->request->CreateOrderInformation->CustomerIdentity->IpAddress); //Check all in identity
        $this->assertEquals("Tess Testson", $request->request->CreateOrderInformation->CustomerIdentity->FullName); //Check all in identity
        $this->assertEquals("Gatan", $request->request->CreateOrderInformation->CustomerIdentity->Street); //Check all in identity
        $this->assertEquals("c/o Eriksson", $request->request->CreateOrderInformation->CustomerIdentity->CoAddress); //Check all in identity
        $this->assertEquals(9999, $request->request->CreateOrderInformation->CustomerIdentity->ZipCode); //Check all in identity
        $this->assertEquals(23, $request->request->CreateOrderInformation->CustomerIdentity->HouseNumber); //Check all in identity
        $this->assertEquals("Stan", $request->request->CreateOrderInformation->CustomerIdentity->Locality); //Check all in identity
        $this->assertEquals("NL", $request->request->CreateOrderInformation->CustomerIdentity->CountryCode); //Check all in identity
        $this->assertEquals("Individual", $request->request->CreateOrderInformation->CustomerIdentity->CustomerType); //Check all in identity
        $this->assertEquals("Tess", $request->request->CreateOrderInformation->CustomerIdentity->IndividualIdentity->FirstName); //Check all in identity
        $this->assertEquals("Testson", $request->request->CreateOrderInformation->CustomerIdentity->IndividualIdentity->LastName); //Check all in identity
        $this->assertEquals("SB", $request->request->CreateOrderInformation->CustomerIdentity->IndividualIdentity->Initials); //Check all in identity
        $this->assertEquals(19231212, $request->request->CreateOrderInformation->CustomerIdentity->IndividualIdentity->BirthDate); //Check all in identity
    }

    public function testInvoiceRequestObjectForCustomerIdentityIndividualFromDE() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                    ->setBirthDate(1923, 12, 12)
                    ->setName("Tess", "Testson")
                    ->setEmail("test@svea.com")
                    ->setPhoneNumber(999999)
                    ->setIpAddress("123.123.123")
                    ->setStreetAddress("Gatan", 23)
                    ->setCoAddress("c/o Eriksson")
                    ->setZipCode(9999)
                    ->setLocality("Stan")
            )
            ->setCountryCode("DE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("EUR")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("test@svea.com", $request->request->CreateOrderInformation->CustomerIdentity->Email); //Check all in identity
        $this->assertEquals(999999, $request->request->CreateOrderInformation->CustomerIdentity->PhoneNumber); //Check all in identity
        $this->assertEquals("123.123.123", $request->request->CreateOrderInformation->CustomerIdentity->IpAddress); //Check all in identity
        $this->assertEquals("Tess Testson", $request->request->CreateOrderInformation->CustomerIdentity->FullName); //Check all in identity
        $this->assertEquals("Gatan", $request->request->CreateOrderInformation->CustomerIdentity->Street); //Check all in identity
        $this->assertEquals("c/o Eriksson", $request->request->CreateOrderInformation->CustomerIdentity->CoAddress); //Check all in identity
        $this->assertEquals(9999, $request->request->CreateOrderInformation->CustomerIdentity->ZipCode); //Check all in identity
        $this->assertEquals(23, $request->request->CreateOrderInformation->CustomerIdentity->HouseNumber); //Check all in identity
        $this->assertEquals("Stan", $request->request->CreateOrderInformation->CustomerIdentity->Locality); //Check all in identity
        $this->assertEquals("DE", $request->request->CreateOrderInformation->CustomerIdentity->CountryCode); //Check all in identity
        $this->assertEquals("Individual", $request->request->CreateOrderInformation->CustomerIdentity->CustomerType); //Check all in identity
        $this->assertEquals("Tess", $request->request->CreateOrderInformation->CustomerIdentity->IndividualIdentity->FirstName); //Check all in identity
        $this->assertEquals("Testson", $request->request->CreateOrderInformation->CustomerIdentity->IndividualIdentity->LastName); //Check all in identity
        $this->assertEquals(19231212, $request->request->CreateOrderInformation->CustomerIdentity->IndividualIdentity->BirthDate); //Check all in identity
    }

    public function testInvoiceRequestObjectForCustomerIdentityCompanyFromNL() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                 ->setInitials("SB")
                 ->setBirthDate(1923, 12, 12)
                 ->setName("Tess", "Testson")
                 ->setEmail("test@svea.com")
                 ->setPhoneNumber(999999)
                 ->setIpAddress("123.123.123")
                 ->setStreetAddress("Gatan", 23)
                 ->setCoAddress("c/o Eriksson")
                 ->setZipCode(9999)
                 ->setLocality("Stan")
            )
            ->setCountryCode("NL")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("test@svea.com", $request->request->CreateOrderInformation->CustomerIdentity->Email); //Check all in identity
        $this->assertEquals(999999, $request->request->CreateOrderInformation->CustomerIdentity->PhoneNumber); //Check all in identity
        $this->assertEquals("123.123.123", $request->request->CreateOrderInformation->CustomerIdentity->IpAddress); //Check all in identity
        $this->assertEquals("Tess Testson", $request->request->CreateOrderInformation->CustomerIdentity->FullName); //Check all in identity
        $this->assertEquals("Gatan", $request->request->CreateOrderInformation->CustomerIdentity->Street); //Check all in identity
        $this->assertEquals("c/o Eriksson", $request->request->CreateOrderInformation->CustomerIdentity->CoAddress); //Check all in identity
        $this->assertEquals(9999, $request->request->CreateOrderInformation->CustomerIdentity->ZipCode); //Check all in identity
        $this->assertEquals(23, $request->request->CreateOrderInformation->CustomerIdentity->HouseNumber); //Check all in identity
        $this->assertEquals("Stan", $request->request->CreateOrderInformation->CustomerIdentity->Locality); //Check all in identity
        $this->assertEquals("NL", $request->request->CreateOrderInformation->CustomerIdentity->CountryCode); //Check all in identity
        $this->assertEquals("Individual", $request->request->CreateOrderInformation->CustomerIdentity->CustomerType); //Check all in identity
    }

    public function testInvoiceRequestObjectForCustomerIdentityCompanyFromSE() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(WebPayItem::companyCustomer()->setNationalIdNumber("vat234"))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                ->prepareRequest();

        $this->assertEquals("vat234", $request->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber); //Check all in identity
        $this->assertEquals("SE", $request->request->CreateOrderInformation->CustomerIdentity->CountryCode); //Check all in identity
        $this->assertEquals("Company", $request->request->CreateOrderInformation->CustomerIdentity->CustomerType); //Check all in identity
    }

    public function testInvoiceRequestObjectForSEorderOnOneProductRow() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $rowFactory = new TestUtil();
        $request = WebPay::createOrder($config)
             ->addOrderRow(TestUtil::createOrderRow())
                ->run($rowFactory->buildShippingFee())
                ->run($rowFactory->buildInvoiceFee())
                  ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                ->prepareRequest();

        //First orderrow is a product
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals('Product: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->DiscountPercent);
        //Second orderrow is shipment
        $this->assertEquals('33', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals('shipping: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals(50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
        //Third orderrow is invoicefee
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('Svea fee: Fee for invoice', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    public function testInvoiceRequestUsingAmountIncVatWithVatPercent() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $rowFactory = new TestUtil();
        $request = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(2)
                    ->setAmountIncVat(125.00)
                    ->setDescription("Specification")
                    ->setName('Product')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
            ->addFee(WebPayItem::shippingFee()
                  ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountIncVat(62.50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
            ->addFee(WebPayItem::invoiceFee()
                    ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountIncVat(62.50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                    ->prepareRequest();

        //First orderrow is a product
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals('Product: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->DiscountPercent);
        //Second orderrow is shipment
        $this->assertEquals('33', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals('shipping: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals(50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
        //Third orderrow is invoicefee
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('Svea fee: Fee for invoice', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    public function testInvoiceRequestUsingAmountIncVatWithAmountExVat() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $rowFactory = new TestUtil();
        $request = WebPay::createOrder($config)
            ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(2)
                    ->setAmountIncVat(125.00)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Product')
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                    )
            ->addFee(WebPayItem::shippingFee()
                     ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountIncVat(62.50)
                    ->setAmountExVat(50.00)
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                    )
            ->addFee(WebPayItem::invoiceFee()
                   ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountIncVat(62.50)
                    ->setAmountExVat(50.00)
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                    )
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                    ->prepareRequest();

        //First orderrow is a product
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals('Product: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->DiscountPercent);
        //Second orderrow is shipment
        $this->assertEquals('33', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals('shipping: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals(50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
        //Third orderrow is invoicefee
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('Svea fee: Fee for invoice', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    public function testInvoiceRequestObjectWithRelativeDiscountOnDifferentProductVat() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    //->setAmountIncVat(300.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    //->setAmountIncVat(200.00)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                    )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                     ->setDiscountPercent(20)
                     ->setDescription("RelativeDiscount")
                    )
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->useInvoicePayment()
                        ->prepareRequest();

        //couponrows

        $this->assertEquals('1', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('RelativeDiscount (25%)', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(-48.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);

        $this->assertEquals('1', $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->ArticleNumber);
        $this->assertEquals('RelativeDiscount (6%)', $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->Description);
        $this->assertEquals(-37.74, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->NumberOfUnits);
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->Unit);
        $this->assertEquals(6, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->DiscountPercent);
    }

        public function test_RelativeDiscountAsInt() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    //->setAmountIncVat(300.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    //->setAmountIncVat(200.00)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                    )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                     ->setDiscountPercent(21)
                     ->setDescription("RelativeDiscount")
                    )
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->useInvoicePayment()
                        ->prepareRequest();

        //couponrows
        $this->assertEquals(-50.40, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(-39.62, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);

    }

        public function test_RelativeDiscountAsFloat() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    //->setAmountIncVat(300.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    //->setAmountIncVat(200.00)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                    )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                     ->setDiscountPercent(20.5)                         // 
                     ->setDescription("RelativeDiscount")
                    )
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->useInvoicePayment()
                        ->prepareRequest();

        //couponrows
        $this->assertEquals(-49.20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(-38.68, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
    }
    
    public function t_estInvoiceRequestObjectWithFixedDiscountOnDifferentProductVat() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                    )
                ->addDiscount(WebPayItem::fixedDiscount()
                        ->setAmountIncVat(100.00)
                        ->setDescription('FixedDiscount')
                        ->setDiscountId('1')
                    )
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                    ->setCountryCode("SE")
                    ->setCustomerReference("33")
                    ->setOrderDate("2012-12-12")
                    ->setCurrency("SEK")
                    ->useInvoicePayment()
                        ->prepareRequest();

        //couponrow
        $this->assertEquals('1', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('FixedDiscount (25%)', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(-85.74, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(17, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    public function testInvoiceWithFixedDiscountWithUneavenAmount() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addDiscount(WebPayItem::fixedDiscount()
                        ->setAmountIncVat(101.50)
                        ->setDescription('FixedDiscount')
                        ->setDiscountId('1')
                    )
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                    ->setCountryCode("SE")
                    ->setCustomerReference("33")
                    ->setOrderDate("2012-12-12")
                    ->setCurrency("SEK")
                    ->useInvoicePayment()
                        ->prepareRequest();
        //couponrow
        $this->assertEquals('1', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals('FixedDiscount', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals(-81.2, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
    }

     public function testInvoiceRequestObjectWithCreateOrderInformation() {
        $config = Svea\SveaConfig::getDefaultConfig();
         $rowFactory = new TestUtil();
           $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
                ->run($rowFactory->buildShippingFee())
            ->addCustomerDetails(WebPayItem::companyCustomer()->setNationalIdNumber(194605092222)->setAddressSelector("ad33"))
                    ->setCountryCode("SE")
                    ->setCustomerReference("33")
                    ->setClientOrderNumber("nr26")
                    ->setOrderDate("2012-12-12")
                    ->setCurrency("SEK")
                    ->useInvoicePayment()// returnerar InvoiceOrder object
                        ->prepareRequest();
        /**
         * Test that all data is in the right place for SoapRequest
         */
        //First orderrow is a product
        $this->assertEquals("2012-12-12",$request->request->CreateOrderInformation->OrderDate);
        $this->assertEquals('33',$request->request->CreateOrderInformation->CustomerReference);
        $this->assertEquals('Invoice',$request->request->CreateOrderInformation->OrderType);
        $this->assertEquals('nr26',$request->request->CreateOrderInformation->ClientOrderNumber); //check in identity
        $this->assertEquals('ad33',$request->request->CreateOrderInformation->AddressSelector); //check in identity
     }

    public function testInvoiceRequestObjectWithAuth() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $rowFactory = new TestUtil();
            $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->run($rowFactory->buildShippingFee())
            ->addCustomerDetails(WebPayItem::companyCustomer()->setNationalIdNumber(194605092222)->setAddressSelector("ad33"))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setClientOrderNumber("nr26")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                ->prepareRequest();

        $this->assertEquals('sverigetest', $request->request->Auth->Username);
        $this->assertEquals('sverigetest', $request->request->Auth->Password);
        $this->assertEquals(79021, $request->request->Auth->ClientNumber);
    }
}
