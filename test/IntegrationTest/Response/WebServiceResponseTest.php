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
                //->setTestmode()()
                ->doRequest();
        return $response->campaignCodes[0]->campaignCode;
    }

    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    function getInvoiceOrderId() {
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
                ->doRequest();
        
        return $request->sveaOrderId;
    }
    
     /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid or missing Country code
     */
    function testErrorMessageResponse() {
        $addressRequest = WebPay::getPaymentPlanParams();
        $request = $addressRequest
                    //->setCountryCode("SE")
                    ->doRequest();
    }

    function testResultForInvoicePaymentNL() {
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
    
    function testResultDeliverInvoiceOrder() {
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::deliverOrder();
        $request = $orderBuilder
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
                    ->setOrderId($orderId)
                    ->setCountryCode("SE")
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
    
    function testResultCloseInvoiceOrder() {
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::closeOrder();
        $request = $orderBuilder
                //->setTestmode()()
                ->setOrderId($orderId)
                ->setCountryCode("SE")
                ->closeInvoiceOrder()
                    ->doRequest();
        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
    }
}

?>
