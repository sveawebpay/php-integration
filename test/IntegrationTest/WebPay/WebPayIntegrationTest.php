<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayIntegrationTest extends PHPUnit_Framework_TestCase {

    // CreateOrderBuilder synchronous payment methods
    public function test_CreateOrderBuilder_Invoice_Accepted() {
        $country = "SE";
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) );
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
    }
    
    public function _test_CreateOrderBuilder_Paymentplan_Accepted() {
        $country = "SE";
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
        ;
        $response = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();

        $this->assertEquals(1, $response->accepted);
    }    
    
    // CreateOrderBuilder asynchronous payment methods   //TODO
    
}