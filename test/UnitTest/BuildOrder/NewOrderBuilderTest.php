<?php

namespace Svea\WebPay\Test\UnitTest\BuildOrder;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\PaymentMethod;

/**
 * All functions named test...() will run as tests in PHP-unit framework
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class NewOrderBuilderTest extends \PHPUnit\Framework\TestCase
{

    public function testNewInvoiceOrderCompanyAddresselector()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow());
        $request = $request
            ->addCustomerDetails(WebPayItem::companyCustomer()->setNationalIdNumber(4608142222)->setAddressSelector("4446e9b2501d4cfca6cc888bf5960379"))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();
        $this->assertEquals("4446e9b2501d4cfca6cc888bf5960379", $request->request->CreateOrderInformation->AddressSelector);
    }

    public function testNewInvoiceOrderWithOrderRow()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow());
        $request = $request
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals(194605092222, $request->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber); //Check all in identity
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals(2, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals(100.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals("Product: Specification", $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals("st", $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->DiscountPercent);
    }

    public function testNewInvoiceOrderWithArray()
    {
        $orderRows[] = TestUtil::createOrderRow();
        $orderRows[] = WebPayItem::orderrow()
            ->setArticleNumber("2")
            ->setQuantity(2)
            ->setAmountExVat(110.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0);
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow($orderRows)
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->prepareRequest();

        $this->assertEquals(194605092222, $request->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber); //Check all in identity
    }

    public function testOrderWithShippingFee()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addFee(WebPayItem::shippingFee()
                ->setShippingId(1)
                ->setName('shipping')
                ->setDescription("Specification")
                ->setAmountExVat(50)
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            );
        $request = $request
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(50.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("shipping: Specification", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
    }

    public function testOrderWithShippingFeeZero()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addFee(WebPayItem::shippingFee()
                ->setShippingId(1)
                ->setName('shipping')
                ->setDescription("Specification")
                ->setAmountExVat(0)
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            );
        $request = $request
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(0.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("shipping: Specification", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
    }

    public function testOrderWithShippingFeeZeroVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addFee(WebPayItem::shippingFee()
                ->setShippingId(1)
                ->setName('shipping')
                ->setDescription("Specification")
                ->setAmountExVat(50)
                ->setUnit("st")
                ->setVatPercent(0)
                ->setDiscountPercent(0)
            );
        $request = $request
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(50.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("shipping: Specification", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
    }

    public function testOrderWithInvoiceFee()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addFee(WebPayItem::invoiceFee()
                ->setName('Svea fee')
                ->setDescription("Fee for invoice")
                ->setAmountExVat(50)
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            );
        $request = $request
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(50.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("Svea fee: Fee for invoice", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
    }

    public function testOrderWithFixedDiscount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("1")
                ->setAmountIncVat(100.00)
                ->setUnit("st")
                ->setDescription("testOrderWithFixedDiscount")
                ->setName("Fixed")
            );
        $request = $request
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("1", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(-80.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("Fixed: testOrderWithFixedDiscount", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
    }

    public function testOrderWithRelativeDiscount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addDiscount(
                WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
            );
        $request = $request
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("1", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(-100.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("Relative: RelativeDiscount", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
    }

    public function testBuildOrderWithIndividualCustomer()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()
                ->setNationalIdNumber(194605092222)
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
            );
        $request = $request
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals(194605092222, $request->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber);
        $this->assertEquals(999999, $request->request->CreateOrderInformation->CustomerIdentity->PhoneNumber);
        $this->assertEquals("Gatan", $request->request->CreateOrderInformation->CustomerIdentity->Street);
        $this->assertEquals(23, $request->request->CreateOrderInformation->CustomerIdentity->HouseNumber);
        $this->assertEquals(9999, $request->request->CreateOrderInformation->CustomerIdentity->ZipCode);
        $this->assertEquals("Stan", $request->request->CreateOrderInformation->CustomerIdentity->Locality);
        $this->assertEquals("Individual", $request->request->CreateOrderInformation->CustomerIdentity->CustomerType);
    }

    public function testBuildOrderWithCompanyCustomer()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::companyCustomer()
                ->setNationalIdNumber(666666)
                ->setEmail("test@svea.com")
                ->setPhoneNumber(999999)
                ->setIpAddress("123.123.123")
                ->setStreetAddress("Gatan", 23)
                ->setCoAddress("c/o Eriksson")
                ->setZipCode(9999)
                ->setLocality("Stan")
            );
        $request = $request
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals(666666, $request->request->CreateOrderInformation->CustomerIdentity->NationalIdNumber);
        $this->assertEquals("Company", $request->request->CreateOrderInformation->CustomerIdentity->CustomerType);
    }

    public function testBuildOrderWithCompanyCustomerDE()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::companyCustomer()
                ->setVatNumber("SE666666")
                ->setCompanyName("MyCompany")
                ->setEmail("test@svea.com")
                ->setPhoneNumber(999999)
                ->setIpAddress("123.123.123")
                ->setStreetAddress("Gatan", 23)
                ->setCoAddress("c/o Eriksson")
                ->setZipCode(9999)
                ->setLocality("Stan")
            );
        $request = $request
            ->setCountryCode("DE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("EUR")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("SE666666", $request->request->CreateOrderInformation->CustomerIdentity->CompanyIdentity->CompanyVatNumber);
        $this->assertEquals("Company", $request->request->CreateOrderInformation->CustomerIdentity->CustomerType);
        $this->assertEquals("MyCompany", $request->request->CreateOrderInformation->CustomerIdentity->FullName);
    }

    public function testAmountsZero()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(0.00)
                    ->setAmountIncVat(0.00)
            )
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("1", $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
    }

    public function testAmountIncVatZero()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setVatPercent(25)
                    ->setAmountIncVat(0.00)
            )
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("1", $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
    }

    public function testAmountExVatZero()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(1)
                    ->setAmountExVat(0.00)
                    ->setVatPercent(25)
            )
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("1", $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(25.0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
    }

    public function testOrderRowsInOrderAsAdded()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setDescription("1")
                    ->setQuantity(1)
                    ->setAmountIncVat(10.00)
                    ->setVatPercent(25)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setDescription("2")
                ->setVatPercent(25)
                ->setAmountIncVat(30)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setDescription("3")
                    ->setQuantity(1)
                    ->setAmountIncVat(20.00)
                    ->setVatPercent(25)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDescription("4")
                ->setAmountIncVat(10))
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("1", $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals("2", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("3", $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals("4", $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->Description);
    }

    public function testOrderRowsInOrderAsAdded2()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $request = $request
            ->addFee(WebPayItem::shippingFee()
                ->setDescription("1")
                ->setVatPercent(25)
                ->setAmountIncVat(30.00)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setDescription("2")
                    ->setQuantity(1)
                    ->setAmountIncVat(10.00)
                    ->setVatPercent(25)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setDescription("3")
                    ->setQuantity(1)
                    ->setAmountIncVat(20.00)
                    ->setVatPercent(25)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDescription("4")
                ->setDiscountPercent(10))
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()// returnerar InvoiceOrder object
            ->prepareRequest();

        $this->assertEquals("1", $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals("2", $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("3", $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals("4", $request->request->CreateOrderInformation->OrderRows['OrderRow'][3]->Description);
    }

    public function testOrderRowsInOrderAsAddedOnHosted()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $form = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setDescription("1")
                    ->setQuantity(1)
                    ->setAmountIncVat(10.00)
                    ->setVatPercent(25)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setDescription("2")
                ->setVatPercent(25)
                ->setAmountIncVat(30)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setDescription("3")
                    ->setQuantity(1)
                    ->setAmountIncVat(20.00)
                    ->setVatPercent(25)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDescription("4")
                ->setAmountIncVat(10))
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::SVEACARDPAY)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();
        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals('1', $xmlMessage->orderrows->row[0]->description);
        $this->assertEquals('2', $xmlMessage->orderrows->row[1]->description);
        $this->assertEquals('3', $xmlMessage->orderrows->row[2]->description);
        $this->assertEquals('4', $xmlMessage->orderrows->row[3]->description);

    }

    public function testOrderRowsInOrderAsAdded2OnHosted()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config);
        $form = $request
            ->addFee(WebPayItem::shippingFee()
                ->setDescription("1")
                ->setVatPercent(25)
                ->setAmountIncVat(30)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setDescription("2")
                    ->setQuantity(1)
                    ->setAmountIncVat(10.00)
                    ->setVatPercent(25)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setDescription("3")
                    ->setQuantity(1)
                    ->setAmountIncVat(20.00)
                    ->setVatPercent(25)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDescription("4")
                ->setDiscountPercent(10))
            ->setCountryCode("SE")
            ->setClientOrderNumber("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentMethod(PaymentMethod::SVEACARDPAY)
            ->setReturnUrl("http://myurl.se")
            ->getPaymentForm();

        $xmlMessage = new \SimpleXMLElement($form->xmlMessage);

        $this->assertEquals('1', $xmlMessage->orderrows->row[0]->description);
        $this->assertEquals('2', $xmlMessage->orderrows->row[1]->description);
        $this->assertEquals('3', $xmlMessage->orderrows->row[2]->description);
        $this->assertEquals('4', $xmlMessage->orderrows->row[3]->description);
    }


    /** example how to integrate with array_map
     * function testOrderRowsUsingMap() {
     * $orderRows[] = array_map(magentoRowToOrderRow, $magentoRows);
     *
     * \Svea\WebPay\WebPay::createOrder($config)->addOrderRow(array_map(magentoRowToOrderRow, $magentoRows));
     * }
     *
     * function magentoRowToOrderRow($magentoRow) {
     * return \Svea\WebPay\WebPay::orderrow()
     * ->setArticleNumber($magentoRow->productId)
     * ->setQuantity(..)
     * ->setAmountExVat(...)
     * ->setDescription(...)
     * ->setName('Prod')
     * ->setUnit("st")
     * ->setVatPercent(25)
     * ->setDiscountPercent(0);
     * }
     */
}
