<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayAdminIntegrationTest extends PHPUnit_Framework_TestCase {

    /// cancelOrder()
    // invoice
    // partpayment
    // card
    public function test_cancelOrder_cancelInvoiceOrder_returns_closeOrder() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelInvoiceOrder();        
        $this->assertInstanceOf( "Svea\WebService\CloseOrder", $request );
        $this->assertEquals(\ConfigurationProvider::INVOICE_TYPE, $request->orderBuilder->orderType); 
    }
    
    public function test_cancelOrder_cancelPaymentPlanOrder_returns_closeOrder() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\WebService\CloseOrder", $request );
        $this->assertEquals(\ConfigurationProvider::PAYMENTPLAN_TYPE, $request->orderBuilder->orderType); 
    }

    public function test_cancelOrder_cancelCardOrder_returns_annulTransaction() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelCardOrder();        
        $this->assertInstanceOf( "Svea\HostedService\AnnulTransaction", $request );
    }

    
}
