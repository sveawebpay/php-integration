<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DeliverOrderTest extends PHPUnit_Framework_TestCase {

    public function testBuildRequest() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $handler = WebPay::deliverOrder($config);
        $request = $handler
                ->setOrderId("id");

        $this->assertEquals("id", $request->orderId);
    }

    public function testDeliverInvoiceDistributionType() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
            ->addOrderRow(TestUtil::createOrderRow())
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
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
                ->addOrderRow(TestUtil::createOrderRow())
                ->addDiscount(WebPayItem::fixedDiscount()->setAmountIncVat(10))
                ->addFee(WebPayItem::shippingFee()
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
                ->setInvoiceDistributionType(\DistributionType::POST)
                ->setCreditInvoice("id")
                ->deliverInvoiceOrder()
                ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Product: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
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
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderBuilder = WebPay::deliverOrder($config);

        $request = $orderBuilder
                ->addOrderRow(TestUtil::createOrderRow())
                ->setCountryCode("SE")
                ->setOrderId("id")
                ->deliverPaymentPlanOrder()
                ->prepareRequest();
        $this->assertEquals("id", $request->request->DeliverOrderInformation->SveaOrderId);
        $this->assertEquals("PaymentPlan", $request->request->DeliverOrderInformation->OrderType);
    }

    public function testNewDeliverInvoiceOrderRow() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow());
            $request = $request ->setOrderId("id")
                ->setNumberOfCreditDays(1)
                ->setCountryCode("SE")
                ->setInvoiceDistributionType(\DistributionType::POST)//Post or Email
                ->setCreditInvoice("id")
                ->deliverInvoiceOrder()
                ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Product: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->DiscountPercent);
    }

    public function testDeliverOrderWithInvoiceFeeAndFixedDiscount() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
                ->addFee(WebPayItem::invoiceFee()
                    ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                )
                ->addDiscount(WebPayItem::fixedDiscount()
                   ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
                    ->setName("Fixed")
                );
            $request = $request ->setOrderId("id")
                ->setNumberOfCreditDays(1)
                ->setInvoiceDistributionType(\DistributionType::POST)//Post or Email
                ->setCreditInvoice("id")
                ->setCountryCode("SE")
                ->deliverInvoiceOrder()
                ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Product: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->DiscountPercent);
        //invoicefee
        $this->assertEquals("", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(50.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("Svea fee: Fee for invoice", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->DiscountPercent);
        //fixeddiscount
        $this->assertEquals("1", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals(-80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals("Fixed: FixedDiscount", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    public function testDeliverOrderWithShippingFeeAndRelativeDiscount() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
                ->addOrderRow(TestUtil::createOrderRow())
                ->addFee(WebPayItem::shippingFee()
                    ->setShippingId(1)
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                )
                ->addDiscount(WebPayItem::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
                );
            $request = $request ->setOrderId("id")
                ->setNumberOfCreditDays(1)
                ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
                ->setCreditInvoice("id")
                ->setCountryCode("SE")
                ->deliverInvoiceOrder()
                ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Product: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->DiscountPercent);
        //shipping
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(50.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("shipping: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->DiscountPercent);
        //relative discount
        $this->assertEquals("1", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals(-100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals("Relative: RelativeDiscount", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->DiscountPercent);
    }
    
}
