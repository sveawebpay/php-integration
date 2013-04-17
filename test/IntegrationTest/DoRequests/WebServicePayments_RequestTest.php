<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

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
                //->setTestmode()()
                ->setCountryCode("SE")
                ->doRequest();
         return $response->campaignCodes[0]->campaignCode;
    }

    function testInvoiceRequestReturnsAcceptedResult() {
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
                   ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(4605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                    ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }
    function testInvoiceRequestReturnsDeniedResult() {
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
                   ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(4606082222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()
                    ->doRequest();

        $this->assertEquals(0, $request->accepted);
    }
    function tes_tInvoiceIndividualForDK() {
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
                   ->addCustomerDetails(Item::individualCustomer()->setNationalIdNumber(2603692503))
                ->setCountryCode("DK")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("DKK")
                ->useInvoicePayment()
                    ->doRequest();
        $this->assertEquals(1, $request->accepted);
    }

    function testInvoiceCompanySe() {
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
                ->addCustomerDetails(Item::companyCustomer()->setNationalIdNumber(4608142222))
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
                //->setTestmode()()
                ->setCountryCode("SE")
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    function testPaymentPlanRequestReturnsAcceptedResult() {
        $campaigncode = $this->getGetPaymentPlanParamsForTesting();
        $request = WebPay::createOrder()
                //->setTestmode()()
                ->addOrderRow(Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(1000.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                        )
                ->addCustomerDetails(Item::individualCustomer()
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
                        )

                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setClientOrderNumber("nr26")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->usePaymentPlanPayment($campaigncode)// returnerar InvoiceOrder object
                    ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    function testGetAddressesResultForPrivate() {
        $addressRequest = WebPay::getAddresses();
        $request = $addressRequest
            //->setTestmode()()
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setIndividual(194605092222)
            ->doRequest();
        $this->assertEquals(1, $request->accepted);
     }
    function testGetAddressesResultForCompany() {
        $addressRequest = WebPay::getAddresses();
        $request = $addressRequest
            //->setTestmode()()
            ->setOrderTypeInvoice()
            ->setCountryCode("SE")
            ->setCompany(4608142222)
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
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
                        ->doRequest();

        return $request->sveaOrderId;
    }

    function testDeliverInvoiceOrderResult() {
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
                    ->setNumberOfCreditDays(1)
                    ->setCountryCode("SE")
                    ->setInvoiceDistributionType('Post')//Post or Email
                    ->deliverInvoiceOrder()
                        ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }

    function testCloseInvoiceOrderResult() {
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::closeOrder();
        $request = $orderBuilder
                //->setTestmode()()
                ->setOrderId($orderId)
                ->setCountryCode("SE")
                ->closeInvoiceOrder()
                    ->doRequest();

        $this->assertEquals(1, $request->accepted);
    }
}

?>
