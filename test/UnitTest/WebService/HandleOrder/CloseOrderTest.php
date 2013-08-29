<?php
namespace swp_;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrderTest extends \PHPUnit_Framework_TestCase {
    
    public function testCloseInvoiceOrder() {
        $orderBuilder = WebPay::closeOrder();
        $request = $orderBuilder
                ->setOrderId("id")
                ->setCountryCode("SE")
                ->closeInvoiceOrder()
                ->prepareRequest();
        
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
