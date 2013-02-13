<?php

$root = realpath(dirname(__FILE__));
require_once $root . '\..\..\..\src\Includes.php';

/**
 * Description of WebServicePayments_RequestTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class WebServicePayments_RequestTest extends PHPUnit_Framework_TestCase {

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

    function testInvoiceRequestReturnsAcceptedResult() {
        $request = WebPay::createOrder()
                ->setTestmode()
                ->beginOrderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                ->endOrderRow()
                ->setCustomerSsn(194605092222)
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                    ->doRequest();
        
        $this->assertEquals(1, $request->accepted);
    }

    function testInvoiceCompanySe() {
        $request = WebPay::createOrder()
                ->setTestmode()
                ->beginOrderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                ->endOrderRow()
                ->setCustomerCompanyIdNumber(4608142222)               
                ->setCountryCode("SE")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                    ->doRequest();
        
        $this->assertEquals(true, $request->accepted);
    }
   
    function testPaymentPlanParamsResult() {
        $addressRequest = WebPay::getPaymentPlanParams();
        $request = $addressRequest
                ->setTestmode()
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    function testPaymentPlanRequestReturnsAcceptedResult() {
        $campaigncode = $this->getGetPaymentPlanParamsForTesting();           
        $request = WebPay::createOrder()
                ->setTestmode()
                ->beginOrderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(1000.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                ->endOrderRow()               
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
                ->setCustomerLocality("Stan")              
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setClientOrderNumber("nr26")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->usePaymentPlanPayment($campaigncode)// returnerar InvoiceOrder object
                    ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    function testGetAddressesResult() {
        $addressRequest = WebPay::getAddresses();
       $request = $addressRequest
            ->setTestmode()
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual(194605092222)
            ->doRequest();
       
        $this->assertEquals(1, $request->accepted);
     }
  
    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    function getInvoiceOrderId() {
        $request = WebPay::createOrder()
                ->setTestmode()
                ->beginOrderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                ->endOrderRow()                
                    ->setCustomerSsn(194605092222)
                    ->setCountryCode("SE")
                    ->setCustomerReference("33")
                    ->setOrderDate("2012-12-12")
                    ->setCurrency("SEK")
                    ->useInvoicePayment()// returnerar InvoiceOrder object
                        ->doRequest();

        return $request->sveaOrderId;
    }

    function testDeliverInvoiceOrderResult() {
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::deliverOrder();
        $request = $orderBuilder
                ->setTestmode()
               ->beginOrderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                ->endOrderRow()
                    ->setOrderId($orderId)
                    ->setNumberOfCreditDays(1)
                    ->setInvoiceDistributionType('Post')//Post or Email
                    ->deliverInvoiceOrder()
                        ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    function testCloseInvoiceOrderResult() {
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::closeOrder();
        $request = $orderBuilder
                ->setTestmode()
                ->setOrderId($orderId)
                ->closeInvoiceOrder()
                    ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }
}

?>
