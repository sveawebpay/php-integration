<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayIntegrationTest extends PHPUnit_Framework_TestCase {

    /// WebPay::createOrder()
    // web service eu: invoice
    // web service eu: paymentplan
    // bypass paypage: usepaymentmethod
    // paypage: cardonly
    // paypage: directbankonly
    // paypage
    public function test_createOrder_useInvoicePayment_returns_InvoicePayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        // we should set attributes here if real request
        $request = $createOrder->useInvoicePayment();
        $this->assertInstanceOf("Svea\WebService\InvoicePayment", $request);
    }
    
    public function test_createOrder_usePaymentPlanPayment_returns_PaymentPlanPayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() );
        $this->assertInstanceOf("Svea\WebService\PaymentPlanPayment", $request);
    }    
    
    public function test_createOrder_usePayPageCardOnly_returns_CardPayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePayPageCardOnly();
        $this->assertInstanceOf("Svea\HostedService\CardPayment", $request);
    }   

    public function test_createOrder_usePayPageDirectBankOnly_returns_DirectPayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePayPageDirectBankOnly();
        $this->assertInstanceOf("Svea\HostedService\DirectPayment", $request);
    }       
    
    public function test_createOrder_usePaymentMethod_returns_PaymentMethodPayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePaymentMethod("mocked_paymentMethod");
        $this->assertInstanceOf("Svea\HostedService\PaymentMethodPayment", $request);
    }  

    public function test_createOrder_usePayPage_returns_PayPagePayment() {
        $createOrder = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $createOrder->usePayPage();
        $this->assertInstanceOf("Svea\HostedService\PayPagePayment", $request);
    }      
        
    /// WebPay::deliverOrder()
    // invoice
    // paymentplan
    // card
    public function test_deliverOrder_deliverInvoiceOrder_without_order_rows_returns_DeliverOrderEU() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $deliverOrder->deliverInvoiceOrder();        
        $this->assertInstanceOf( "Svea\AdminService\DeliverOrdersRequest", $request ); 
        $this->assertEquals("Invoice", $request->orderBuilder->orderType);    
    }

    public function test_deliverOrder_deliverPaymentPlanOrder_without_order_rows_returns_DeliverOrderEU() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $deliverOrder->deliverPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\AdminService\DeliverOrdersRequest", $request );
        $this->assertEquals("PaymentPlan", $request->orderBuilder->orderType); 
    }
    
    public function test_deliverOrder_deliverInvoiceOrder_with_order_rows_returns_DeliverOrdersRequest() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $deliverOrder->addOrderRow( WebPayItem::orderRow() );
        $request = $deliverOrder->deliverInvoiceOrder();     
        $this->assertInstanceOf( "Svea\WebService\DeliverInvoice", $request );         // WebService\DeliverInvoice => soap call DeliverOrderEU  
    }

    public function test_deliverOrder_deliverPaymentPlanOrder_with_order_rows_returns_DeliverOrdersRequest() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $deliverOrder->addOrderRow( WebPayItem::orderRow() );
        $request = $deliverOrder->deliverPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\WebService\DeliverPaymentPlan", $request );      
    }
    
    public function test_deliverOrder_deliverCardOrder_returns_ConfirmTransaction() {
        $deliverOrder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $deliverOrder->addOrderRow( WebPayItem::orderRow() );
        $request = $deliverOrder->deliverCardOrder();        
        $this->assertInstanceOf( "Svea\HostedService\ConfirmTransaction", $request );
    }
    
    /// WebPay::getAddresses()
    public function test_getAddresses_returns_GetAddresses() {
        $request = WebPay::getAddresses( Svea\SveaConfig::getDefaultConfig() );
        $this->assertInstanceOf( "Svea\WebService\GetAddresses", $request );
    }    
 
    /// WebPay::getPaymentPlanParams()
    public function test_getPaymentPlanParams_returns_GetPaymentPlanParams() {
        $request = WebPay::getPaymentPlanParams( Svea\SveaConfig::getDefaultConfig() );
        $this->assertInstanceOf( "Svea\WebService\GetPaymentPlanParams", $request );
    }    
    
    /// WebPay::getPaymentMethods()
    public function test_getPaymentMethods_returns_GetPaymentMethods() {
        $request = WebPay::getPaymentMethods( Svea\SveaConfig::getDefaultConfig() );
        $this->assertInstanceOf( "Svea\HostedService\GetPaymentMethods", $request );
    }   
    
    /// listPaymentMethods()
    public function test_listPaymentMethods_returns_ListPaymentMethods() {
        $request = WebPay::listPaymentMethods( Svea\SveaConfig::getDefaultConfig() );
        $this->assertInstanceOf( "Svea\HostedService\ListPaymentMethods", $request );
    } 
}