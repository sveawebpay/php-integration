<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */
class InvoicePaymentIntegrationTest extends PHPUnit_Framework_TestCase {
    
    public function testInvoiceRequestAccepted() {
        $request = WebPay::createOrder()
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(4605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
    }
    
    public function testInvoiceRequestUsingISO8601dateAccepted() {
        $request = WebPay::createOrder()
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
        $request = WebPay::createOrder()
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
    public function tes_tInvoiceIndividualForDk() {
        $request = WebPay::createOrder()
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
        $request = WebPay::createOrder()
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
        $request = WebPay::createOrder()
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
        $request = WebPay::createOrder()
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
        $request = WebPay::createOrder()
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
}
