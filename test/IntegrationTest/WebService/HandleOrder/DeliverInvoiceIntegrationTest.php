<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author jona-lit
 */
class DeliverInvoiceIntegrationTest extends PHPUnit_Framework_TestCase {

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
                ->useInvoicePayment()
                    ->doRequest();

        return $request->sveaOrderId;
    }

    public function testDeliverInvoiceOrder() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
                ->addOrderRow(TestUtil::createOrderRow())
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
    
    /**
     * @expectedException Svea\ValidationException
     */ 
    public function testDeliverInvoiceOrder_missing_setOrderId_throws_ValidationException() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
                ->addOrderRow(TestUtil::createOrderRow())
                //->setOrderId($orderId)
                ->setNumberOfCreditDays(1)
                ->setCountryCode("SE")
                ->setInvoiceDistributionType('Post')//Post or Email
                ->deliverInvoiceOrder()
                    ->doRequest();
    }     

    /**
     * @expectedException Svea\ValidationException
     * 
     * bypasses WebPay::deliverOrders, as 2.0 allows deliverOrder w/o orderRows
     */ 
    public function test_DeliverInvoice_missing_addOrderRow_throws_ValidationException() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = new \Svea\DeliverOrderBuilder($config);
        $orderBuilder = $orderBuilder
                //->addOrderRow(TestUtil::createOrderRow())
                ->setOrderId($orderId)
                ->setNumberOfCreditDays(1)
                ->setCountryCode("SE")
                ->setInvoiceDistributionType('Post')//Post or Email
        ;
        $deliverInvoiceObject = new Svea\WebService\DeliverInvoice( $orderBuilder );
        $response = $deliverInvoiceObject->doRequest();                
    }
    /**
     * @expectedException Svea\ValidationException
     */ 
    public function testDeliverInvoiceOrder_missing_setInvoiceDistributionType_throws_ValidationException() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
                ->addOrderRow(TestUtil::createOrderRow())
                ->setOrderId($orderId)
                ->setNumberOfCreditDays(1)
                ->setCountryCode("SE")
                //->setInvoiceDistributionType('Post')//Post or Email
                ->deliverInvoiceOrder()
                    ->doRequest();
    }    
}
