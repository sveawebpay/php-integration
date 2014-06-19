<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class DeliverPaymentPlanIntegrationTest extends PHPUnit_Framework_TestCase {
    
    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    private function getPaymentPlanOrderId() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $response = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow(1000.00, 1))
                ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
                ->setCountryCode("SE")
                ->setCustomerReference("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )
                    ->doRequest();
        
        return $response->sveaOrderId;
    }

    public function testDeliverPaymentPlanOrder() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderId = $this->getPaymentPlanOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
                ->addOrderRow(TestUtil::createOrderRow(1000.00, 1))
                ->setOrderId($orderId)
                ->setCountryCode("SE")
                ->deliverPaymentPlanOrder()
                    ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals(1250, $request->amount);
        $this->assertEquals('PaymentPlan', $request->orderType);
    }
    
    /**
     * @expectedException Svea\ValidationException
     */ 
    public function testDeliverPaymentPlanOrder_missing_setOrderId_throws_ValidationException() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $orderId = $this->getPaymentPlanOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
                //->setOrderId($orderId)
                ->setCountryCode("SE")
                ->deliverPaymentPlanOrder()
                    ->doRequest();
    }     
}
