<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * Description of WebServicePayments_RequestTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class WebServiceResponseTest extends PHPUnit_Framework_TestCase {

    /**
     * Use to get paymentPlanParams to be able to test PaymentPlanRequest
     * @return type
     */
    function getGetPaymentPlanParamsForTesting() {
        $addressRequest = WebPay::getPaymentPlanParams();
        $response = $addressRequest
                ->setTestmode()
                ->doRequest();
        return $response->campaignCodes[0]->campaignCode;
    }

    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    function getInvoiceOrderId() {
        $request = WebPay::createOrder()
                ->setTestmode()
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
                ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)
                ->doRequest();
        
        return $request->sveaOrderId;
    }

    function testResultGetAddresses() {
        $addressRequest = WebPay::getAddresses();
        $request = $addressRequest
                ->setTestmode()
                ->setOrderTypeInvoice()
                ->setCountryCode("SE")
                ->setIndividual(194605092222)
                ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
        $this->assertEquals('Accepted', $request->resultcode);
        //$this->assertEquals('5F445B19E8C87954904FB7531A51AEE57C5E9413', $request->customerIdentity[0]->addressSelector);
        $this->assertEquals('Person', $request->customerIdentity[0]->customerType);
        $this->assertEquals('08 - 111 111 11', $request->customerIdentity[0]->phoneNumber);
        $this->assertEquals('Persson, Tess T', $request->customerIdentity[0]->legalName);
        $this->assertEquals('Tess T', $request->customerIdentity[0]->firstName);
        $this->assertEquals('Persson', $request->customerIdentity[0]->lastName);
        $this->assertEquals('Testgatan 1', $request->customerIdentity[0]->street);
        $this->assertEquals('c/o Eriksson, Erik', $request->customerIdentity[0]->coAddress);
        $this->assertEquals(99999, $request->customerIdentity[0]->zipCode);
        $this->assertEquals('Stan', $request->customerIdentity[0]->locality);
        $this->assertEquals(4605092222, $request->customerIdentity[0]->nationalIdNumber);
    }
    
    function testResultGetPaymentPlanParams() {
        $addressRequest = WebPay::getPaymentPlanParams();
        $request = $addressRequest
                ->setTestmode()
                ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals(213060, $request->campaignCodes[0]->campaignCode);
        $this->assertEquals('Köp nu betala om 3 månader (räntefritt)', $request->campaignCodes[0]->description);
        $this->assertEquals('InterestAndAmortizationFree', $request->campaignCodes[0]->paymentPlanType);
        $this->assertEquals(3, $request->campaignCodes[0]->contractLengthInMonths);
        $this->assertEquals(100, $request->campaignCodes[0]->initialFee);
        $this->assertEquals(29, $request->campaignCodes[0]->notificationFee);
        $this->assertEquals(0, $request->campaignCodes[0]->interestRatePercent);
        $this->assertEquals(3, $request->campaignCodes[0]->numberOfInterestFreeMonths);
        $this->assertEquals(3, $request->campaignCodes[0]->numberOfPaymentFreeMonths);
        $this->assertEquals(1000, $request->campaignCodes[0]->fromAmount);
        $this->assertEquals(50000, $request->campaignCodes[0]->toAmount);
    }
    
    function testErrorMessageResponse(){
        $addressRequest = WebPay::getPaymentPlanParams();
        $request = $addressRequest
                ->setTestmode()
                ->setPasswordBasedAuthorization('sverigetest', 'sverjetest', 59999)
                    ->doRequest();
         $this->assertEquals(0, $request->accepted);
         $this->assertEquals('User lacks sufficient privileges', $request->errormessage);
         $this->assertEquals(0, $request->resultcode);
    }

    function testResultForInvoicePaymentNL() {
        $request = WebPay::createOrder()
                ->setTestmode()
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
                ->setPasswordBasedAuthorization("hollandtest", "hollandtest", 85997)
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
    
    function testResultDeliverInvoiceOrder() {
        $orderId = $this->getInvoiceOrderId();        
        $orderBuilder = WebPay::deliverOrder();
        $request = $orderBuilder
                ->setTestmode()
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
                    ->setOrderId($orderId)
                    ->setNumberOfCreditDays(1)
                    ->setInvoiceDistributionType('Post')//Post or Email
                    ->deliverInvoiceOrder()
                        ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals(250, $request->amount);
        $this->assertEquals('Invoice', $request->orderType);
        //Invoice specifics
        //$this->assertEquals(0000, $request->invoiceId); //differs in every test
        //$this->assertEquals(date(), $request->dueDate); //differs in every test
        //$this->assertEquals(date(), $request->invoiceDate); //differs in every test
        $this->assertEquals('Post', $request->invoiceDistributionType);
        
        //$this->assertEquals('Invoice', $request->contractNumber); //for paymentplan
    }
    
    function testResultCloseInvoiceOrder(){
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::closeOrder();
        $request = $orderBuilder
                ->setTestmode()
                ->setOrderId($orderId)
                ->closeInvoiceOrder()
                    ->doRequest();
        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
    }
}

?>
