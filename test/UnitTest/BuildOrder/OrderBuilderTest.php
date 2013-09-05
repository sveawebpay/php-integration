<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';
require_once $root . '/../VoidValidator.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/TestRowFactory.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * All functions named test...() will run as tests in PHP-unit framework
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class OrderBuilderTest extends \PHPUnit_Framework_TestCase {

    //Set up orderobject

    protected function setUp() {
        $this->orderBuilder = \WebPay::createOrder();
        $this->orderBuilder->validator = new VoidValidator();
    }

    public function testBuildOrderWithOrderRow() {
        $sveaRequest = \WebPay::createOrder(SveaConfig::getProdConfig())
                ->addOrderRow(TestUtil::createOrderRow());

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

    public function testBuildOrderWithShippingFee() {
        $rowFactory = new TestRowFactory();
        $sveaRequest =
                \WebPay::createOrder()
                ->run($rowFactory->buildShippingFee());

        $this->assertEquals("Specification", $sveaRequest->shippingFeeRows[0]->description);
        $this->assertEquals(50, $sveaRequest->shippingFeeRows[0]->amountExVat);
        $this->assertEquals(25, $sveaRequest->shippingFeeRows[0]->vatPercent);
    }

    public function testBuildOrderWithInvoicefee() {
        $rowFactory = new TestRowFactory();
        $sveaRequest = \WebPay::createOrder()
                ->addOrderRow(TestUtil::createOrderRow())
                ->run($rowFactory->buildInvoiceFee());

        $this->assertEquals("Svea fee", $sveaRequest->invoiceFeeRows[0]->name);
        $this->assertEquals("Fee for invoice", $sveaRequest->invoiceFeeRows[0]->description);
        $this->assertEquals(50, $sveaRequest->invoiceFeeRows[0]->amountExVat);
        $this->assertEquals("st", $sveaRequest->invoiceFeeRows[0]->unit);
        $this->assertEquals(25, $sveaRequest->invoiceFeeRows[0]->vatPercent);
        $this->assertEquals(0, $sveaRequest->invoiceFeeRows[0]->discountPercent);
    }

    public function testBuildOrderWithFixedDiscount() {
        $sveaRequest = \WebPay::createOrder()
                ->addDiscount(\WebPayItem::fixedDiscount()
                    ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
                    ->setName("Fixed")
                    );

        $this->assertEquals("1", $sveaRequest->fixedDiscountRows[0]->discountId);
        $this->assertEquals(100.00, $sveaRequest->fixedDiscountRows[0]->amount);
        $this->assertEquals("FixedDiscount", $sveaRequest->fixedDiscountRows[0]->description);
        //test type
        $this->assertInternalType("float", $sveaRequest->fixedDiscountRows[0]->amount);
    }

    public function testBuildOrderWithRelativeDiscount() {
        $sveaRequest =
                \WebPay::createOrder()
                ->addDiscount(\WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
                    );

        $this->assertEquals("1", $sveaRequest->relativeDiscountRows[0]->discountId);
        $this->assertEquals(50, $sveaRequest->relativeDiscountRows[0]->discountPercent);
        $this->assertEquals("RelativeDiscount", $sveaRequest->relativeDiscountRows[0]->description);
        //test type
        $this->assertInternalType("int", $sveaRequest->relativeDiscountRows[0]->discountPercent);
    }

    public function testBuildOrderWithCustomer() {
        $sveaRequest = \WebPay::createOrder()
                ->addCustomerDetails(\WebPayItem::individualCustomer()
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

        $this->assertEquals(194605092222, $sveaRequest->customerIdentity->ssn);
        $this->assertEquals("SB", $sveaRequest->customerIdentity->initials);
        $this->assertEquals(19231212, $sveaRequest->customerIdentity->birthDate);
        $this->assertEquals("Tess", $sveaRequest->customerIdentity->firstname);
        $this->assertEquals("Testson", $sveaRequest->customerIdentity->lastname);
        $this->assertEquals("test@svea.com", $sveaRequest->customerIdentity->email);
        $this->assertEquals(999999, $sveaRequest->customerIdentity->phonenumber);
        $this->assertEquals("123.123.123", $sveaRequest->customerIdentity->ipAddress);
        $this->assertEquals("Gatan", $sveaRequest->customerIdentity->street);
        $this->assertEquals(23, $sveaRequest->customerIdentity->housenumber);
        $this->assertEquals("c/o Eriksson", $sveaRequest->customerIdentity->coAddress);
        $this->assertEquals(9999, $sveaRequest->customerIdentity->zipCode);
        $this->assertEquals("Stan", $sveaRequest->customerIdentity->locality);
    }

    public function testBuildOrderWithAllCustomerTypes() {
        $company = TRUE;
        $sveaRequest = \WebPay::createOrder();
        if ($company == TRUE) {
               $item = \WebPayItem::companyCustomer();
               $item = $item->setNationalIdNumber(194605092222)
                    ->setEmail("test@svea.com")
                    ->setCompanyName("TestCompagniet")
                    ->setZipCode(9999)
                    ->setLocality("Stan")
                    ->setIpAddress("123.123.123")
                    ->setPhoneNumber(999999);

        if ("DE" == "DE") {
            $item = $item
                ->setVatNumber("NL2345234")
                ->setStreetAddress("Gatan", 23);
            }
        }
        $sveaRequest = $sveaRequest->addCustomerDetails($item);

        $this->assertEquals(194605092222, $sveaRequest->customerIdentity->orgNumber);
        $this->assertEquals("NL2345234", $sveaRequest->customerIdentity->companyVatNumber);
        $this->assertEquals("test@svea.com", $sveaRequest->customerIdentity->email);
        $this->assertEquals(999999, $sveaRequest->customerIdentity->phonenumber);
        $this->assertEquals("123.123.123", $sveaRequest->customerIdentity->ipAddress);
        $this->assertEquals("Gatan", $sveaRequest->customerIdentity->street);
        $this->assertEquals(23, $sveaRequest->customerIdentity->housenumber);
        $this->assertEquals(9999, $sveaRequest->customerIdentity->zipCode);
        $this->assertEquals("Stan", $sveaRequest->customerIdentity->locality);
    }

    public function testBuildOrderWithCompanyDetails() {
        $sveaRequest = \WebPay::createOrder()
                    ->addCustomerDetails(\WebPayItem::companyCustomer()
                        ->setNationalIdNumber("2345234")
                        ->setCompanyName("TestCompagniet")
                    );

        $this->assertEquals("2345234", $sveaRequest->customerIdentity->orgNumber);
        $this->assertEquals("TestCompagniet", $sveaRequest->customerIdentity->companyName);
    }

    public function testBuildOrderWithOrderDate() {
        $sveaRequest = \WebPay::createOrder()
                ->setOrderDate("2012-12-12");

        $this->assertEquals("2012-12-12", $sveaRequest->orderDate);
    }

    public function testBuildOrderWithCountryCode() {
        $sveaRequest = \WebPay::createOrder()
                ->setCountryCode("SE");

        $this->assertEquals("SE", $sveaRequest->countryCode);
    }

    public function testBuildOrderWithCurrency() {
        $sveaRequest = \WebPay::createOrder()
                ->setCurrency("SEK");

        $this->assertEquals("SEK", $sveaRequest->currency);
    }

    public function testBuildOrderWithCustomerRefNumber() {
        $sveaRequest = \WebPay::createOrder()
                ->setCustomerReference("33");

        $this->assertEquals("33", $sveaRequest->customerReference);
    }

    public function testBuildOrderWithClientOrderNumber() {
        $sveaRequest = \WebPay::createOrder()
                ->setClientOrderNumber("33");

        $this->assertEquals("33", $sveaRequest->clientOrderNumber);
    }

    /**
      function testThatValidatorIsCalledOnBuild() {
      $this->orderBuilder->build();
      $this->assertEquals(1, $this->orderBuilder->validator->nrOfCalls);
      }
     */
}
