<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class OrderBuilderIntegrationTest extends PHPUnit_Framework_TestCase {

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

    
    // CancelOrderBuilder synchronous payment methods
    public function test_CancelOrderBuilder_Invoice_success() {
        $country = "SE";
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) );
        $orderResponse = $order->useInvoicePayment()->doRequest();
       
        $this->assertEquals(1, $orderResponse->accepted);
         
        $cancelResponse = WebPay::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->usePaymentMethod(\PaymentMethod::INVOICE)
                    ->doRequest();
        
        $this->assertEquals(1, $cancelResponse->accepted);
    }
    
    public function test_CancelOrderBuilder_PaymentPlan_success() {
        $country = "SE";
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
        ;
        $orderResponse = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();

        $this->assertEquals(1, $orderResponse->accepted);
        
        $cancelResponse = WebPay::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->usePaymentMethod(\PaymentMethod::PAYMENTPLAN)
                    ->doRequest();
        
        $this->assertEquals(1, $cancelResponse->accepted);
    }    
    
    public function test_CancelOrderBuilder_with_wrong_paymentmethod_fails() {
        $country = "SE";
        $order = TestUtil::createOrder( TestUtil::createIndividualCustomer($country) )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
        ;
        $orderResponse = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();

        $this->assertEquals(1, $orderResponse->accepted);
        
        $cancelResponse = WebPay::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
                ->setOrderId($orderResponse->sveaOrderId)
                ->setCountryCode($country)
                ->usePaymentMethod(\PaymentMethod::INVOICE)
                    ->doRequest();
        
        $this->assertEquals(0, $cancelResponse->accepted);
        $this->assertEquals(50016, $cancelResponse->resultcode);
    }
    
    // CancelOrderBuilder asynchronous payment methods   //TODO
    
    /**
     * test_manual_annul_card 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    function test_manual_CancelOrderBuilder_Card_success() {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of cancelOrder for a card order' // TODO
        );
        
        // Set the below to match the transaction, then run the test.
        $customerrefno = "test_1396964349955";
        $transactionId = 580658;

        $request = WebPay::cancelOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId( $transactionId )
            ->setCountryCode( "SE" )
            ->usePaymentMethod(PaymentMethod::KORTCERT);
    
        $response = $request->doRequest();        
         
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );
        
        print_r($response );
        $this->assertEquals( 1, $response->accepted );        
        $this->assertEquals( $customerrefno, $response->customerrefno );  
    }   
    
    
}