<?php
use Svea\WebService\CloseOrder as CloseOrder;


$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Jonas Lith
 */
class CloseOrderIntegrationTest extends PHPUnit_Framework_TestCase {

    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    private function getInvoiceOrderId() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $request = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->useInvoicePayment()// returnerar InvoiceOrder object
                //->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021)
                ->doRequest();

        return $request->sveaOrderId;
    }

    public function testCloseInvoiceOrder() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::closeOrder($config);
        $request = $orderBuilder
                ->setOrderId($orderId)
                ->setCountryCode("SE")
                ->closeInvoiceOrder()
                ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
    }
  
    /**
     * @expectedException Svea\ValidationException
     */ 
    public function testCloseInvoiceOrder_missing_setOrderId_throws_ValidationException() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::closeOrder($config);
        $request = $orderBuilder
//                ->setOrderId($orderId)
                ->setCountryCode("SE")
                ->closeInvoiceOrder()
                    ->doRequest();
    }
}
