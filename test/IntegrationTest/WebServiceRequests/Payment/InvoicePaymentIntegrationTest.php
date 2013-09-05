<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class InvoicePaymentIntegrationTest extends \PHPUnit_Framework_TestCase {
    
    public function testInvoiceRequestAccepted() {
        $request = \WebPay::createOrder()
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(4605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
    }
    
    public function testInvoiceRequestDenied() {
        $request = \WebPay::createOrder()
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(4606082222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                ->doRequest();
        
        $this->assertEquals(0, $request->accepted);
    }
    
    //Turned off?
    public function tes_tInvoiceIndividualForDk() {
        $request = \WebPay::createOrder()
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(\WebPayItem::individualCustomer()->setNationalIdNumber(2603692503))
                ->setCountryCode("DK")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("DKK")
                ->useInvoicePayment()
                ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
    }

    public function testInvoiceCompanySe() {
        $request = \WebPay::createOrder()
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(\WebPayItem::companyCustomer()->setNationalIdNumber(4608142222))
                ->setCountryCode("SE")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                ->doRequest();
        
        $this->assertEquals(true, $request->accepted);
    }
    
    public function testResultForInvoicePaymentNL() {
        $request = \WebPay::createOrder()
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(\WebPayItem::individualCustomer()
                        ->setBirthDate(1955, 03, 07)
                        ->setName("Sneider", "Boasman")
                        ->setStreetAddress("Gate", 42)
                        ->setLocality("BARENDRECHT")
                        ->setZipCode("1102 HG")
                        ->setInitials("SB")
                        ->setCoAddress(138)
                )
                ->setCountryCode("NL")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("EUR")
                ->useInvoicePayment()
                //->setPasswordBasedAuthorization("hollandtest", "hollandtest", 85997)
                ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals('Invoice', $request->orderType);
        //$this->assertEquals(54086, $request->sveaOrderId);
        $this->assertEquals(1, $request->sveaWillBuyOrder);
        $this->assertEquals(250, $request->amount);
        //$this->assertEquals(date(), $request->expirationDate);
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
}
