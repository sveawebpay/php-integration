<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * CreateOrderBuilderIntegrationTest holds all tests for how to build orders for diverse
 * payment methods.
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreateOrderBuilderIntegrationTest extends PHPUnit_Framework_TestCase {

    public function test_createOrder_Invoice_SE_Accepted() {
        $order = TestUtil::createOrder();
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
    }
    
    public function test_createOrder_Paymentplan_SE_Accepted() {

        $order = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails( TestUtil::createIndividualCustomer("SE") )
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate( date('c') )
        ;
        $response = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();

        $this->assertEquals(1, $response->accepted);
    }    
    
    // CreateOrderBuilder card payment method
    // see CardPaymentURLIntegrationTest->test_manual_CardPayment_getPaymentUrl()
        
    // CreateOrderBuilder direct bank payment method   //TODO    
    
    
}


?>