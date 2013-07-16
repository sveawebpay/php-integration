<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';

/**
 * Description of DeliverOrderTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class HandleOrderTest extends PHPUnit_Framework_TestCase {

    public function testBuildRequest() {
        $handler = WebPay::deliverOrder();
        $request = $handler
                ->setOrderId("id");
        $this->assertEquals("id", $request->orderId);
    }
    
    public function testDeliverInvoiceDistributionType() {
         $orderBuilder = WebPay::deliverOrder();
        $request = $orderBuilder
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
                ->setOrderId("id")
                ->setNumberOfCreditDays(1)
                ->setCountryCode("SE")
                ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
                ->setCreditInvoice("id")
                ->deliverInvoiceOrder()
                    ->prepareRequest();
        
        $this->assertEquals('Post', $request->request->DeliverOrderInformation->DeliverInvoiceDetails->InvoiceDistributionType);       
    }

    public function testDeliverInvoiceOrder() {
        $orderBuilder = WebPay::deliverOrder();
        $request = $orderBuilder
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
                ->addDiscount(Item::fixedDiscount()->setAmountIncVat(10))
                ->addFee(Item::shippingFee()
                    ->setShippingId('33')
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )                
                ->setOrderId("id")
                ->setNumberOfCreditDays(1)
                ->setCountryCode("SE")
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCreditInvoice("id")
                ->deliverInvoiceOrder()
                ->prepareRequest();
        
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Prod: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->DiscountPercent);

        //shippingfee
        $this->assertEquals("33", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals("shipping: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals(50, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->DiscountPercent);

        //discount
        $this->assertEquals(-8.0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->NumberOfCreditDays);
        $this->assertEquals("Post", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->InvoiceDistributionType);
        $this->assertEquals(true, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->IsCreditInvoice);
        $this->assertEquals("id", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->InvoiceIdToCredit);
        $this->assertEquals("id", $request->request->DeliverOrderInformation->SveaOrderId);
        $this->assertEquals("Invoice", $request->request->DeliverOrderInformation->OrderType);
    }
    
    public function testDeliverPaymentPlanOrder() {
        $orderBuilder = WebPay::deliverOrder();

        $request = $orderBuilder
                ->setCountryCode("SE")
                ->setOrderId("id")
                ->deliverPaymentPlanOrder()
                ->prepareRequest();
        $this->assertEquals("id", $request->request->DeliverOrderInformation->SveaOrderId);
        $this->assertEquals("PaymentPlan", $request->request->DeliverOrderInformation->OrderType);
    }

    public function testCloseInvoiceOrder() {
        $orderBuilder = WebPay::closeOrder();

        $request = $orderBuilder
                ->setOrderId("id")
                ->setCountryCode("SE")
                ->closeInvoiceOrder()
                ->prepareRequest();
        //->doRequest(); 
        $this->assertEquals("id", $request->request->CloseOrderInformation->SveaOrderId);
    }

    public function testClosePaymentPlanOrder() {
        $orderBuilder = WebPay::closeOrder();

        $request = $orderBuilder
                ->setCountryCode("SE")
                ->setOrderId("id")
                ->closePaymentPlanOrder()
                ->prepareRequest();
        $this->assertEquals("id", $request->request->CloseOrderInformation->SveaOrderId);
    }
}

?>
