<?php

$root = realpath(dirname(__FILE__));
require_once $root . '\..\..\..\src\Includes.php';
require_once $root . '\..\..\..\src\WebServiceRequests\svea_soap\SveaSoapConfig.php';
require_once $root . '\..\VoidValidator.php';

$root = realpath(dirname(__FILE__));
require_once $root . '\TestRowFactory.php';

/**
 * All functions named test...() will run as tests in PHP-unit framework
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class OrderBuilderTest extends PHPUnit_Framework_TestCase {

    //Set up orderobject
    protected function setUp() {
        $this->orderBuilder = WebPay::createOrder();
        $this->orderBuilder->validator = new VoidValidator();
    }
    
    function testBuildOrderWithOrderRow() {
        $sveaRequest = $this->orderBuilder
                ->beginOrderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                ->endOrderRow();
        
        $this->assertEquals(1, $sveaRequest->orderRows[0]->articleNumber);
        $this->assertEquals(2, $sveaRequest->orderRows[0]->quantity);
        $this->assertEquals(100.00, $sveaRequest->orderRows[0]->amountExVat);
        $this->assertEquals("Specification", $sveaRequest->orderRows[0]->description);
        $this->assertEquals("st", $sveaRequest->orderRows[0]->unit);
        $this->assertEquals(25, $sveaRequest->orderRows[0]->vatPercent);
        $this->assertEquals(0, $sveaRequest->orderRows[0]->vatDiscount);
        //test type
        $this->assertInternalType("int", $sveaRequest->orderRows[0]->quantity);
        $this->assertInternalType("int", $sveaRequest->orderRows[0]->vatPercent);
    }

    function testBuildOrderWithShippingFee() {
        $rowFactory = new TestRowFactory();
        $sveaRequest =
                WebPay::createOrder()
                ->run($rowFactory->buildShippingFee());
        
        $this->assertEquals("Specification", $sveaRequest->shippingFeeRows[0]->description);
        $this->assertEquals(50, $sveaRequest->shippingFeeRows[0]->amountExVat);
        $this->assertEquals(25, $sveaRequest->shippingFeeRows[0]->vatPercent);
    }

    function testBuildOrderWithInvoicefee() {
        $rowFactory = new TestRowFactory();
        $sveaRequest = WebPay::createOrder()
                ->beginOrderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                ->endOrderRow()
                ->run($rowFactory->buildInvoiceFee());

        $this->assertEquals("Svea fee", $sveaRequest->invoiceFeeRows[0]->name);
        $this->assertEquals("Fee for invoice", $sveaRequest->invoiceFeeRows[0]->description);
        $this->assertEquals(50, $sveaRequest->invoiceFeeRows[0]->amountExVat);
        $this->assertEquals("st", $sveaRequest->invoiceFeeRows[0]->unit);
        $this->assertEquals(25, $sveaRequest->invoiceFeeRows[0]->vatPercent);
        $this->assertEquals(0, $sveaRequest->invoiceFeeRows[0]->discountPercent);
    }

    function testBuildOrderWithFixedDiscount() {
        $sveaRequest = WebPay::createOrder()
                ->beginFixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
                    ->setName("Fixed")
                ->endFixedDiscount(0);
        
        $this->assertEquals("1", $sveaRequest->fixedDiscountRows[0]->discountId);
        $this->assertEquals(100.00, $sveaRequest->fixedDiscountRows[0]->amount);
        $this->assertEquals("FixedDiscount", $sveaRequest->fixedDiscountRows[0]->description);
        //test type
        $this->assertInternalType("float", $sveaRequest->fixedDiscountRows[0]->amount);
    }

    function testBuildOrderWithRelativeDiscount() {
        $sveaRequest =
                WebPay::createOrder()
                ->beginRelativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
                ->endRelativeDiscount();
        
        $this->assertEquals("1", $sveaRequest->relativeDiscountRows[0]->discountId);
        $this->assertEquals(50, $sveaRequest->relativeDiscountRows[0]->discountPercent);
        $this->assertEquals("RelativeDiscount", $sveaRequest->relativeDiscountRows[0]->description);
        //test type
        $this->assertInternalType("int", $sveaRequest->relativeDiscountRows[0]->discountPercent);
    }

    function testBuildOrderWithCustomer() {
        $sveaRequest =
                WebPay::createOrder()
                ->setCustomerSsn(194605092222)
                ->setCustomerInitials("SB")
                ->setCustomerBirthDate(1923, 12, 12)
                ->setCustomerName("Tess", "Testson")
                ->setCustomerEmail("test@svea.com")
                ->setCustomerPhoneNumber(999999)
                ->setCustomerIpAddress("123.123.123")
                ->setCustomerStreetAddress("Gatan", 23)
                ->setCustomerCoAddress("c/o Eriksson")
                ->setCustomerZipCode(9999)
                ->setCustomerLocality("Stan");	
                
                
        
        $this->assertEquals(194605092222, $sveaRequest->ssn);
        $this->assertEquals("SB", $sveaRequest->initials);
        $this->assertEquals(19231212, $sveaRequest->birthDate);
        $this->assertEquals("Tess", $sveaRequest->firstname);
        $this->assertEquals("Testson", $sveaRequest->lastname);
        $this->assertEquals("test@svea.com", $sveaRequest->email);
        $this->assertEquals(999999, $sveaRequest->phonenumber);
        $this->assertEquals("123.123.123", $sveaRequest->ipAddress);
        $this->assertEquals("Gatan", $sveaRequest->street);
        $this->assertEquals(23, $sveaRequest->housenumber);
        $this->assertEquals("c/o Eriksson", $sveaRequest->coAddress);
        $this->assertEquals(9999, $sveaRequest->zipCode);
        $this->assertEquals("Stan", $sveaRequest->locality);
    }
    
    function testBuildOrderWithCompanyDetails() {
        $sveaRequest = WebPay::createOrder()
                    ->setCustomerCompanyIdNumber("2345234")
                    ->setCustomerCompanyName("TestCompagniet");
        
        $this->assertEquals("2345234", $sveaRequest->orgNumber);
        $this->assertEquals("TestCompagniet", $sveaRequest->companyName);
    }

    function testBuildOrderWithOrderDate() {
        $sveaRequest = WebPay::createOrder()
                ->setOrderDate("2012-12-12");
        
        $this->assertEquals("2012-12-12", $sveaRequest->orderDate);
    }

    function testBuildOrderWithCountryCode() {
        $sveaRequest = WebPay::createOrder()
                ->setCountryCode("SE");
        
        $this->assertEquals("SE", $sveaRequest->countryCode);
    }

    function testBuildOrderWithCurrency() {
        $sveaRequest = WebPay::createOrder()
                ->setCurrency("SEK");
        
        $this->assertEquals("SEK", $sveaRequest->currency);
    }

    function testBuildOrderWithCustomerRefNumber() {
        $sveaRequest = WebPay::createOrder()
                ->setCustomerReference("33");
        
        $this->assertEquals("33", $sveaRequest->customerReference);
    }
    
    function testBuildOrderWithClientOrderNumber() {
        $sveaRequest = WebPay::createOrder()
                ->setClientOrderNumber("33");
        
        $this->assertEquals("33", $sveaRequest->clientOrderNumber);
    }

    /**
      function testThatValidatorIsCalledOnBuild(){
      $this->orderBuilder->build();
      $this->assertEquals(1, $this->orderBuilder->validator->nrOfCalls);
      }
     * 
     */
}

?>