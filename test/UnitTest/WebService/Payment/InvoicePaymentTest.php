<?php

namespace Svea\WebPay\Test\UnitTest\WebService\Payment;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;

/**
 * @author Anneli Halld'n, Daniel Brolund, Fredrik Sundell for Svea Webpay
 */
class InvoicePaymentTest extends \PHPUnit\Framework\TestCase
{

    public function testInvoiceRequestwithPeppolId()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(
                WebPayItem::companyCustomer()
                    ->setNationalIdNumber(194608142222))
            ->setOrderDate("2019-04-01")
            ->setPeppolId("1234:asdf")
            ->setCountryCode("SE")
            ->useInvoicePayment()
            ->prepareRequest();

        $this->assertEquals("1234:asdf", $request->request->CreateOrderInformation->PeppolId);
    }

    public function testInvoiceRequestObjectForCustomerIdentityIndividualFromSE()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testInvoiceRequestOnProductVatCero()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testSetAuth()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testInvoiceRequestObjectForCustomerIdentityIndividualFromNL()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testInvoiceRequestObjectForCustomerIdentityIndividualFromDE()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testInvoiceRequestObjectForCustomerIdentityCompanyFromNL()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testInvoiceRequestObjectForCustomerIdentityCompanyFromSE()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testInvoiceRequestObjectForSEorderOnOneProductRow()
    {
        $config = ConfigurationService::getDefaultConfig();
        $rowFactory = new TestUtil();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->run($rowFactory->buildShippingFee())
            ->run($rowFactory->buildInvoiceFee())
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

    public function testInvoiceRequestUsingAmountIncVatWithVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
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
        $this->assertEquals(125.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->DiscountPercent);
        //Second orderrow is shipment
        $this->assertEquals('33', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals('shipping: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals(62.50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
        //Third orderrow is invoicefee
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('Svea fee: Fee for invoice', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(62.50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    public function testInvoiceRequestUsingAmountIncVatWithAmountExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
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
        $this->assertEquals(125.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->DiscountPercent);
        //Second orderrow is shipment
        $this->assertEquals('33', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals('shipping: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals(62.50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
        //Third orderrow is invoicefee
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('Svea fee: Fee for invoice', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(62.50, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('st', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    public function testInvoiceRequestObjectWithRelativeDiscountOnDifferentProductVat()
    {
        $config = ConfigurationService::getDefaultConfig();
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
        $this->assertEquals(-37.736, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->NumberOfUnits);
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->Unit);
        $this->assertEquals(6, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->DiscountPercent);
    }

    public function test_RelativeDiscountAsInt()
    {
        $config = ConfigurationService::getDefaultConfig();
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
        $this->assertEquals(-39.6228, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);

    }

    public function test_RelativeDiscountAsFloat()
    {
        $config = ConfigurationService::getDefaultConfig();
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
                ->setDiscountPercent(20.5)//
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
        $this->assertEquals(-38.6794, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
    }

    public function t_estInvoiceRequestObjectWithFixedDiscountOnDifferentProductVat()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testInvoiceWithFixedDiscountWithUneavenAmount()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    public function testInvoiceRequestObjectWithCreateOrderInformation()
    {
        $config = ConfigurationService::getDefaultConfig();
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
        $this->assertEquals("2012-12-12", $request->request->CreateOrderInformation->OrderDate);
        $this->assertEquals('33', $request->request->CreateOrderInformation->CustomerReference);
        $this->assertEquals('Invoice', $request->request->CreateOrderInformation->OrderType);
        $this->assertEquals('nr26', $request->request->CreateOrderInformation->ClientOrderNumber); //check in identity
        $this->assertEquals('ad33', $request->request->CreateOrderInformation->AddressSelector); //check in identity
    }

    public function testInvoiceRequestObjectWithAuth()
    {
        $config = ConfigurationService::getDefaultConfig();
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

    /**
     * Tests for rounding**
     */
    public function testOrderSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
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
            ->prepareRequest();
        $this->assertEquals(80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


    }

    public function testDiscountSetAsExVatWhenPriceSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()->setAmountExVat(8))
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-8, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


    }

    public function testDiscountSetAsExVatAndVatPercentWhenPriceSetAsExVatAndVatPercent()
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
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();
        $this->assertEquals(80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


        $this->assertEquals(-8, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


    }

    public function testDiscountPercentAndVatPercentWhenPriceSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
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
            ->prepareRequest();
        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


        $this->assertEquals(-9.999, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


    }

    public function testFeeSetAsExVatAndVatPercentWhenPriceSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(24)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();

        $this->assertEquals(80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


        $this->assertEquals(80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


        $this->assertEquals(80, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


    }

    public function testOrderRowPriceSetAsInkVatAndVatPercentSetAmountAsIncVat()
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
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

    }

    public function testFeeSetAsIncVatAndVatPercentWhenPriceSetAsIncVatAndVatPercent()
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
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


        $this->assertEquals(100, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


        $this->assertEquals(100, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

    }

    public function testDiscountSetAsIncVatWhenPriceSetAsIncVatAndVatPercent()
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
            ->prepareRequest();
        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);


    }

    public function testDiscountSetAsExVatAndVatPercentWhenPriceSetAsIncVatAndVatPercent()
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
            ->prepareRequest();
        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

    }


    public function testDiscountPercentAndVatPercentWhenPriceSetAsIncVatAndVatPercent()
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
            ->prepareRequest();
        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-12.39876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

    }

    public function testOrderSetAsIncVatAndExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();
        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

    }

    public function testOrderAndFeesSetAsIncVatAndExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1230.9876)
                    ->setAmountExVat(990.99)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(123.9876)->setAmountExVat(99.99)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(123.9876)->setAmountExVat(99.99)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testOrderAndFixedDiscountSetAsIncVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1230.9876)
                    ->setAmountExVat(990.99)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(12.39876)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();

        $this->assertEquals(1230.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-12.39876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testOrderSetAsIncVatAndExVatAndRelativeDiscount()
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
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-12.39876, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testOrderSetWithMixedMethods1()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setAmountIncVat(123.9876)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();
        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testOrderSetWithMixedMethods2()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setName('incvat')
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setName('exvat')
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setName('exvat')
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();
        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testOrderSetWithMixedOrderRowAndFee()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(
                WebPayItem::invoiceFee()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();
        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testOrderSetWithMixedOrderRowAndFeeAndVatPercentSet()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(
                WebPayItem::invoiceFee()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();
        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testOrderAndFixedDiscountSetWithMixedVat2()
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
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-9.999, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testOrderSetAsMixedVatAndRelativeDiscount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(5)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(-9.999, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }


    /// relative discount examples:
    // single order rows vat rate
    public function test_relativeDiscount_amount()
    {
        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
                    ->setName("exvatRow")
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(25)
                    ->setQuantity(1)
                    ->setName("exvatRow2")
            )
            ->addFee(
                WebPayItem::invoiceFee()
                    ->setAmountExVat(8.00)
                    ->setVatPercent(25)
                    ->setName("exvatInvoiceFee")
            )
            ->addFee(
                WebPayItem::shippingFee()
                    ->setAmountExVat(16.00)
                    ->setVatPercent(25)
                    ->setName("exvatShippingFee")
            )
            ->addDiscount(
                WebPayItem::relativeDiscount()
                    ->setDiscountPercent(10.0)
                    ->setDiscountId("TenPercentOff")
                    ->setName("relativeDiscount")
            );
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        // all discount rows
        // expected: 10% off orderRow rows: 2x 80.00 @25% => -16.00 @25% discount
        $this->assertEquals(-16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
    }

    // relative discount on multiple order row defined exvat/vatpercent vat rates
    public function test_relativeDiscount_amount_multiple_vat_rates_defined_exvat_creates_discount_rows_using_exvat_and_vatpercent()
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
                WebPayItem::relativeDiscount()
                    ->setDiscountPercent(10.0)
                    ->setDiscountId("TenPercentOff")
                    ->setName("relativeDiscount")
            );
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        // all discount rows
        // expected: 10% off orderRow rows: 1x60.00 @20%, 1x30@10% => split proportionally across order row (only) vat rate: -6.0 @20%, -3.0 @10%
        $this->assertEquals(-6.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(false, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);

    }

    // relative discount -- created discount rows should use incvat + vatpercent
    // relative discount on multiple order row defined exvat/vatpercent vat rates
    public function test_relativeDiscount_amount_multiple_vat_rates_defined_incvat_creates_discount_rows_using_incvat_and_vatpercent()
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
                    ->setName("exvatRow")
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(33.00)
                    ->setVatPercent(10)
                    ->setQuantity(1)
                    ->setName("exvatRow2")
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
                WebPayItem::relativeDiscount()
                    ->setDiscountPercent(10.0)
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
        // expected: 10% off orderRow rows: 1x60.00 @20%, 1x30@10% => split proportionally across order row (only) vat rate: -6.0 @20%, -3.0 @10%
        $this->assertEquals(-7.20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PriceIncludingVat);
        $this->assertEquals(-3.30, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
        $this->assertEquals(true, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PriceIncludingVat);
    }

    /// fixed discount examples:
    // single order rows vat rate
    public function test_fixedDiscount_amount_with_set_exvat_vat_rate()
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

    public function test_fixedDiscount_amount_with_set_incvat_vat_rate()
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
                    ->setAmountIncVat(11.0)
                    ->setVatPercent(10)
                    ->setDiscountId("ElevenCrownsOff")
                    ->setName("fixedDiscount: 10 @10% => 11kr")
            );
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        // all discount rows
        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
    }

    public function test_fixedDiscount_amount_with_calculated_vat_rate_exvat()
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
                    ->setDiscountId("TenCrownsOff")
                    ->setName("fixedDiscount: 10 off exvat")
            );
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        // all discount rows
        // expected: fixedDiscount: 10 off exvat, order row amount are 66% at 20% vat, 33% at 10% vat => 6.67 @20% and 3.33 @10%
        $this->assertEquals(-6.6666666666667, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(-3.3333333333333, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
    }

    public function test_fixedDiscount_amount_with_calculated_vat_rate_incvat()
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
                    ->setAmountIncVat(10.0)
                    ->setDiscountId("TenCrownsOff")
                    ->setName("fixedDiscount: 10 off incvat")
            );
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        // all discount rows
        // expected: fixedDiscount: 10 off incvat, order row amount are 66% at 20% vat, 33% at 10% vat
        // 1.2*0.66x + 1.1*0.33x = 10 => x = 8.6580 => 5.7143 @20% and 2.8571 @10% =
        $this->assertEquals(-5.7142857142857, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(-2.8571428571429, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
    }

    public function test_fixedDiscount_amount_with_set_incvat_vat_rate_creates_discount_rows_using_incvat_and_vatpercent()
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
                    ->setAmountIncVat(11.0)
                    ->setVatPercent(10)
                    ->setDiscountId("ElevenCrownsOff")
                    ->setName("fixedDiscount: 10 @10% => 11kr")
            );
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        // all discount rows
        // expected: fixedDiscount: 10 @10% => 11kr, expressed as exvat + vat in request
        $this->assertEquals(-10.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
    }

    public function test_fixedDiscount_amount_with_calculated_vat_rate_exvat_creates_discount_rows_using_incvat_and_vatpercent()
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
                    ->setDiscountId("TenCrownsOff")
                    ->setName("fixedDiscount: 10 off exvat")
            );
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        // all discount rows
        // expected: fixedDiscount: 10 off exvat, order row amount are 66% at 20% vat, 33% at 10% vat => 6.67 @20% and 3.33 @10%
        $this->assertEquals(-6.6666666666667, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(-3.3333333333333, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
    }

    public function test_fixedDiscount_amount_with_calculated_vat_rate_incvat_creates_discount_rows_using_incvat_and_vatpercent()
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
                    ->setAmountIncVat(10.0)
                    ->setDiscountId("TenCrownsOff")
                    ->setName("fixedDiscount: 10 off incvat")
            );
        $request = $order->useInvoicePayment()->prepareRequest();
        // all order rows
        $this->assertEquals(60.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(30.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        // all invoice fee rows
        $this->assertEquals(8.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        // all shipping fee rows
        $this->assertEquals(16.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->VatPercent);
        // all discount rows
        // expected: fixedDiscount: 10 off incvat, order row amount are 66% at 20% vat, 33% at 10% vat
        // 1.2*0.66x + 1.1*0.33x = 10 => x = 8.6580 => 5.7143 @20% and 2.8571 @10% =
        $this->assertEquals(-5.7142857142857, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->PricePerUnit);
        $this->assertEquals(20, $request->request->CreateOrderInformation->OrderRows['OrderRow'][4]->VatPercent);
        $this->assertEquals(-2.8571428571429, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->PricePerUnit);
        $this->assertEquals(10, $request->request->CreateOrderInformation->OrderRows['OrderRow'][5]->VatPercent);
    }

    // See file Svea\WebPay\Test\UnitTest\WebService\Payment\FixedDiscountRowsTest for specification of FixedDiscount row behaviour.
}