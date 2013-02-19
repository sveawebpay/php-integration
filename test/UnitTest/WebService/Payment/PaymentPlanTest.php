<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/OrderBuilderTest.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../test/UnitTest/BuildOrder/TestRowFactory.php';

/**
 * Description of PaymentPlanTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */

class PaymentPlanTest extends PHPUnit_Framework_TestCase {

    function testPaymentPlanRequestObjectSpecifics() {
        $rowFactory = new TestRowFactory();
        $request = WebPay::createOrder()
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
                ->addCustomerDetails(Item::individualCustomer()->setSsn(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setClientOrderNumber("nr26")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->setAddressSelector("ad33")
                ->usePaymentPlanPayment("camp1")// returnerar InvoiceOrder object
                    ->prepareRequest();
        
        $this->assertEquals('camp1', $request->request->CreateOrderInformation->CreatePaymentPlanDetails['CampaignCode']);
        $this->assertEquals(0, $request->request->CreateOrderInformation->CreatePaymentPlanDetails['SendAutomaticGiroPaymentForm']);
    }
}

?>
