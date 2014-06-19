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
    public function test_cancelOrder_cancelInvoiceOrder_returns_CloseOrder() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelInvoiceOrder();        
        $this->assertInstanceOf( "Svea\WebService\CloseOrder", $request );
        $this->assertEquals(\ConfigurationProvider::INVOICE_TYPE, $request->orderBuilder->orderType); 
    }
    
    public function test_cancelOrder_cancelPaymentPlanOrder_returns_CloseOrder() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\WebService\CloseOrder", $request );
        $this->assertEquals(\ConfigurationProvider::PAYMENTPLAN_TYPE, $request->orderBuilder->orderType); 
    }

    public function test_cancelOrder_cancelCardOrder_returns_AnnulTransaction() {
        $cancelOrder = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrder->cancelCardOrder();        
        $this->assertInstanceOf( "Svea\HostedService\AnnulTransaction", $request );
    }

    /// queryOrder()
    // invoice
    // partpayment
    // card
    // direct bank
    public function test_queryOrder_queryInvoiceOrder_returns_GetOrdersRequest() {
        $queryOrder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $queryOrder->queryInvoiceOrder();        
        $this->assertInstanceOf( "Svea\AdminService\GetOrdersRequest", $request );
        $this->assertEquals(\ConfigurationProvider::INVOICE_TYPE, $request->orderBuilder->orderType); 
    }    
    
    public function test_queryOrder_queryPaymentPlanOrder_returns_GetOrdersRequest() {
        $queryOrder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $queryOrder->queryPaymentPlanOrder();        
        $this->assertInstanceOf( "Svea\AdminService\GetOrdersRequest", $request );
        $this->assertEquals(\ConfigurationProvider::PAYMENTPLAN_TYPE, $request->orderBuilder->orderType); 
    }       

    public function test_queryOrder_queryCardOrder_returns_QueryTransaction() {
        $queryOrder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $queryOrder->queryCardOrder();        
        $this->assertInstanceOf( "Svea\HostedService\QueryTransaction", $request );
    } 

    public function test_queryOrder_queryDirectBankOrder_returns_QueryTransaction() {
        $queryOrder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() );
        $request = $queryOrder->queryDirectBankOrder();
        $this->assertInstanceOf( "Svea\HostedService\QueryTransaction", $request );
    }     
    
    /// cancelOrderRows()
    // invoice
    // partpayment
    // card
    public function test_cancelOrderRows_cancelInvoiceOrderRows_returns_CancelOrderRowsRequest() {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrderRowsBuilder->cancelInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\CancelOrderRowsRequest", $request );
    }      
    
    public function test_cancelOrderRows_cancelPaymentPlanOrderRows_returns_CancelOrderRowsRequest() {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrderRowsBuilder->cancelPaymentPlanOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\CancelOrderRowsRequest", $request );
    }          
    
    public function test_cancelOrderRows_cancelCardOrderRows_returns_LowerTransaction() {
        $mockedNumberedOrderRow = new Svea\NumberedOrderRow();
        $mockedNumberedOrderRow
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;   
        
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $cancelOrderRowsBuilder->addNumberedOrderRow( $mockedNumberedOrderRow );
        $cancelOrderRowsBuilder->setRowToCancel(1);
        
        $request = $cancelOrderRowsBuilder->cancelCardOrderRows();        
        $this->assertInstanceOf( "Svea\HostedService\LowerTransaction", $request );
    }  
    
    /// creditOrderRows()
    // invoice
    // card
    // direct bank
    public function test_creditOrderRows_creditInvoiceOrderRows_returns_CreditOrderRowsRequest() {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $creditOrderRowsBuilder->creditInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\CreditOrderRowsRequest", $request );
    }    

    public function test_creditOrderRows_creditCardOrderRows_returns_CreditTransaction() {
        $mockedNumberedOrderRow = new Svea\NumberedOrderRow();
        $mockedNumberedOrderRow
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;  
        
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsBuilder->setOrderId( 123456 );
        $creditOrderRowsBuilder->addNumberedOrderRow( $mockedNumberedOrderRow );
        $creditOrderRowsBuilder->setRowToCredit(1);
        $request = $creditOrderRowsBuilder->creditCardOrderRows();                
        $this->assertInstanceOf( "Svea\HostedService\CreditTransaction", $request );
    } 
    
    public function test_creditOrderRows_creditDirectBankOrderRows_returns_CreditTransaction() {
        $mockedNumberedOrderRow = new Svea\NumberedOrderRow();
        $mockedNumberedOrderRow
            ->setAmountExVat(100.00)                // recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)                     // recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)                        // required
            ->setRowNumber(1)
        ;  
        
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsBuilder->setOrderId( 123456 );
        $creditOrderRowsBuilder->addNumberedOrderRow( $mockedNumberedOrderRow );
        $creditOrderRowsBuilder->setRowToCredit(1);
        $request = $creditOrderRowsBuilder->creditDirectBankOrderRows();                
        $this->assertInstanceOf( "Svea\HostedService\CreditTransaction", $request );
    } 
    
    /// addOrderRows()
    // invoice
    // paymentplan
    public function test_addOrderRows_addInvoiceOrderRows_returns_AddOrderRowsRequest() {
        $addOrderRowsBuilder = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $addOrderRowsBuilder->addInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\AddOrderRowsRequest", $request );
    }

    public function test_addOrderRows_addPaymentPlanOrderRows_returns_AddOrderRowsRequest() {
        $addOrderRowsBuilder = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $addOrderRowsBuilder->addPaymentPlanOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\AddOrderRowsRequest", $request );
    }    
    
    /// updateOrderRows()
    // invoice
    // paymentplan
    public function test_updateOrderRows_updateInvoiceOrderRows_returns_UpdateOrderRowsRequest() {
        $updateOrderRowsBuilder = WebPayAdmin::updateOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $updateOrderRowsBuilder->updateInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\UpdateOrderRowsRequest", $request );
    }

    public function test_updateOrderRows_updatePaymentPlanOrderRows_returns_UpdateOrderRowsRequest() {
        $updateOrderRowsBuilder = WebPayAdmin::updateOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $updateOrderRowsBuilder->updatePaymentPlanOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\UpdateOrderRowsRequest", $request );
    } 
     
}
