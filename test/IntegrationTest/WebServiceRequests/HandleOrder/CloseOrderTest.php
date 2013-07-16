<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

/**
 * @author Jonas Lith
 */
class CloseOrderTest extends PHPUnit_Framework_TestCase {
    
    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    private function getInvoiceOrderId() {
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
    
    function testCloseInvoiceOrder() {
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
