<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';

/**
 * Description of DeliverOrderTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class NewHandleOrderTest extends PHPUnit_Framework_TestCase {

    function testNewDeliverInvoiceOrderRow() {
        $request = WebPay::deliverOrder();
            //->setTestmode()();
        //foreach...
        $request = $request
            ->addOrderRow(
                Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    );
        //end foreach
            $request = $request ->setOrderId("id")
                ->setNumberOfCreditDays(1)
                ->setCountryCode("SE")
                ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
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
        }

    function testDeliverOrderWithInvoiceFeeAndFixedDiscount() {
        $request = WebPay::deliverOrder();
            //->setTestmode()();
        //foreach...
        $request = $request
            ->addOrderRow(
                Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
                ->addFee(Item::invoiceFee()
                    ->setName('Svea fee')
                    ->setDescription("Fee for invoice")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                        )
                ->addDiscount(Item::fixedDiscount()
                   ->setDiscountId("1")
                    ->setAmountIncVat(100.00)
                    ->setUnit("st")
                    ->setDescription("FixedDiscount")
                    ->setName("Fixed")
                        );
        //end foreach
            $request = $request ->setOrderId("id")
                ->setNumberOfCreditDays(1)
                ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
                ->setCreditInvoice("id")
                ->setCountryCode("SE")
                ->deliverInvoiceOrder()
                    ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Prod: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
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

    function testDeliverOrderWithShippingFeeAndRelativeDiscount() {
        $request = WebPay::deliverOrder();
            //->setTestmode()();
        //foreach...
        $request = $request
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
                ->addFee(Item::shippingFee()
                    ->setShippingId(1)
                    ->setName('shipping')
                    ->setDescription("Specification")
                    ->setAmountExVat(50)
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                    )
                ->addDiscount(Item::relativeDiscount()
                    ->setDiscountId("1")
                    ->setDiscountPercent(50)
                    ->setUnit("st")
                    ->setName('Relative')
                    ->setDescription("RelativeDiscount")
                    );
        //end foreach
            $request = $request ->setOrderId("id")
                ->setNumberOfCreditDays(1)
                ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
                ->setCreditInvoice("id")
                ->setCountryCode("SE")
                ->deliverInvoiceOrder()
                    ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Prod: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
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

?>
