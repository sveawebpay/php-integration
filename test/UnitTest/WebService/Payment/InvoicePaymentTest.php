<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';
require_once $root . '/../../../../test/UnitTest/BuildOrder/TestRowFactory.php';

/**
 * Description of InvoicePaymentTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class InvoicePaymentTest extends PHPUnit_Framework_TestCase {

     function testInvoiceRequestObjectForCustomerIdentityIndividualFromSE(){
           $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                ->setArticleNumber(1)
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
                )
            ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
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

    function testSetAuth(){
           $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                ->setArticleNumber(1)
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
                )
            ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
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



    function testInvoiceRequestObjectForCustomerIdentityIndividualFromNL(){
         $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                ->setArticleNumber(1)
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
                )
              ->addCustomerDetails(Item::individualCustomer()
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
    function testInvoiceRequestObjectForCustomerIdentityIndividualFromDE(){
         $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                ->setArticleNumber(1)
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
                )
              ->addCustomerDetails(Item::individualCustomer()
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

    function testInvoiceRequestObjectForCustomerIdentityCompanyFromNL(){
         $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                ->setArticleNumber(1)
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
                )
            ->addCustomerDetails(Item::individualCustomer()
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

    function testInvoiceRequestObjectForCustomerIdentityCompanyFromSE(){
          $request = WebPay::createOrder()
            //->setTestmode()()
              ->addOrderRow(Item::orderRow()
                ->setArticleNumber(1)
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
                )
            ->addCustomerDetails(Item::companyCustomer()->setNationalIdNumber("vat234"))
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

    function testInvoiceRequestObjectForSEorderOnOneProductRow() {
        $rowFactory = new TestRowFactory();
        $request = WebPay::createOrder()
            //->setTestmode()()
             ->addOrderRow(Item::orderRow()
                ->setArticleNumber(1)
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setDescription("Specification")
                ->setName('Prod')
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
                )
                ->run($rowFactory->buildShippingFee())
                ->run($rowFactory->buildInvoiceFee())
                  ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                    ->prepareRequest();

        //First orderrow is a product
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals('Prod: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
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
    function testInvoiceRequestUsingAmountIncVatWithVatPercent() {
        $rowFactory = new TestRowFactory();
        $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountIncVat(125)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
            ->addFee(Item::shippingFee()
                  ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountIncVat(62.50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
            ->addFee(Item::invoiceFee()
                    ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountIncVat(62.50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
                ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                    ->prepareRequest();

        //First orderrow is a product
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals('Prod: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
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
    function testInvoiceRequestUsingAmountIncVatWithAmountExVat() {
        $rowFactory = new TestRowFactory();
        $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountIncVat(125)
                    ->setAmountExVat(100)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                    )
            ->addFee(Item::shippingFee()
                     ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountIncVat(62.50)
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                    )
            ->addFee(Item::invoiceFee()
                   ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountIncVat(62.50)
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                    )
            ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                    ->prepareRequest();

        //First orderrow is a product
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals('Prod: Specification', $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->Description);
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

    function testInvoiceRequestObjectWithRelativeDiscountOnDifferentProductVat() {
        $request = WebPay::createOrder()
                //->setTestmode()()
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    //->setAmountIncVat(300)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    //->setAmountIncVat(200)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                    )
                ->addDiscount(Item::relativeDiscount()
                    ->setDiscountId("1")
                     ->setDiscountPercent(20)
                     ->setDescription("RelativeDiscount")
                    )
                ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->useInvoicePayment()
                        ->prepareRequest();

        //couponrow
        $this->assertEquals('1', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('RelativeDiscount', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(-85.74, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(16.64, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    function testInvoiceRequestObjectWithFixedDiscountOnDifferentProductVat() {
        $request = WebPay::createOrder()
                //->setTestmode()()
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(240.00)
                    ->setDescription("CD")
                    ->setVatPercent(25)
                    )
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(1)
                    ->setAmountExVat(188.68)
                    ->setDescription("Bok")
                    ->setVatPercent(6)
                    )
                ->addDiscount(Item::fixedDiscount()
                        ->setAmountIncVat(100.00)
                        ->setDescription('FixedDiscount')
                        ->setDiscountId('1')
                    )
                ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(194605092222))
                    ->setCountryCode("SE")
                    ->setCustomerReference("33")
                    ->setOrderDate("2012-12-12")
                    ->setCurrency("SEK")
                    ->useInvoicePayment()
                        ->prepareRequest();

        //couponrow
        $this->assertEquals('1', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals('FixedDiscount', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(-85.74, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(16.64, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][2]->DiscountPercent);
    }

     function testInvoiceRequestObjectWithCreateOrderInformation(){
        $rowFactory = new TestRowFactory();
           $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                   ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
                ->run($rowFactory->buildShippingFee())
            ->addCustomerDetails(Item::companyCustomer()->setNationalIdNumber(194605092222)->setAddressSelector("ad33"))
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

    function testInvoiceRequestObjectWithAuth(){
        $rowFactory = new TestRowFactory();
            $request = WebPay::createOrder()
            //->setTestmode()()
            ->addOrderRow(Item::orderRow()
                     ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
            ->run($rowFactory->buildShippingFee())
            ->addCustomerDetails(Item::companyCustomer()->setNationalIdNumber(194605092222)->setAddressSelector("ad33"))
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

?>
