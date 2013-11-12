<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentPlanTest extends PHPUnit_Framework_TestCase {

      /**
     * Use to get paymentPlanParams to be able to test PaymentPlanRequest
     * @return type
     */
    public function getGetPaymentPlanParamsForTesting() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $addressRequest = WebPay::getPaymentPlanParams($config);
        $response = $addressRequest
                ->setCountryCode("SE")
                ->doRequest();
         return $response->campaignCodes[0]->campaignCode;
    }

    public function testPaymentPlanRequestObjectSpecifics() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $rowFactory = new TestUtil();
        $request = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->run($rowFactory->buildShippingFee())
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setClientOrderNumber("nr26")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePaymentPlanPayment("camp1")// returnerar InvoiceOrder object
                    ->prepareRequest();

        $this->assertEquals('camp1', $request->request->CreateOrderInformation->CreatePaymentPlanDetails['CampaignCode']);
        $this->assertEquals(0, $request->request->CreateOrderInformation->CreatePaymentPlanDetails['SendAutomaticGiroPaymentForm']);
    }

    public function testInvoiceRequestObjectWithRelativeDiscountOnTwoProducts() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(2)
                    ->setAmountExVat(240.00)
                    ->setAmountIncVat(300.00)
                    ->setDescription("CD")
                    )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                     ->setDiscountPercent(10)
                     ->setDescription("RelativeDiscount")
                    )
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->useInvoicePayment()
                        ->prepareRequest();
        //couponrow
        $this->assertEquals('1', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals('RelativeDiscount', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals(-48.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(1, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals('', $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->CreateOrderInformation->OrderRows['OrderRow'][1]->DiscountPercent);
    }

    public function testPaymentPlanWithPriceAsDecimal() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $campaign = $this->getGetPaymentPlanParamsForTesting();
        $request = WebPay::createOrder($config)
                ->addOrderRow(WebPayItem::orderRow()
                    ->setArticleNumber("1")
                    ->setQuantity(2)
                    ->setAmountExVat(240.00)
                    ->setAmountIncVat(300.00)
                    ->setDescription("CD")
                    )
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                    ->usePaymentPlanPayment($campaign)
                        ->prepareRequest();
        //couponrow

        $this->assertEquals(240.00, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals(25, $request->request->CreateOrderInformation->OrderRows['OrderRow'][0]->VatPercent);
    }
}
