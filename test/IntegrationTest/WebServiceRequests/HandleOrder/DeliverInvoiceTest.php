<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author jona-lit
 */
class DeliverInvoiceTest extends PHPUnit_Framework_TestCase {
    
    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    private function getInvoiceOrderId() {
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
    
    public function testDeliverInvoiceOrder() {
        $orderId = $this->getInvoiceOrderId();
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
                ->setOrderId($orderId)
                ->setNumberOfCreditDays(1)
                ->setCountryCode("SE")
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
}

?>
