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
    public function _test_CreateOrderBuilder_Invoice_Accepted() {
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
    public function _test_CancelOrderBuilder_Invoice_success() {
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
    
    public function _test_CancelOrderBuilder_PaymentPlan_success() {
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
}