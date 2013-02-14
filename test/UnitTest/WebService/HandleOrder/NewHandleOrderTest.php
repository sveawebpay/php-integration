<?php

$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';

/**
 * Description of DeliverOrderTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class NewHandleOrderTest extends PHPUnit_Framework_TestCase {
  
    function testNewDeliverInvoiceOrderRow(){ 
        $request = WebPay::deliverOrder()
            ->setTestmode();
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
}

?>
