<?php
$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class WebPayAdminUnitTest extends \PHPUnit_Framework_TestCase {
    
    public function test_WebPayAdmin_class_exists() {
        $adminObject = new WebPayAdmin();        
        $this->assertInstanceOf( "WebPayAdmin", $adminObject );
    }

    // WebPayAdmin::cancelOrder() ----------------------------------------------
    public function test_cancelOrder_returns_CancelOrderBuilder() {
        $builderObject = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );        
        $this->assertInstanceOf( "Svea\CancelOrderBuilder", $builderObject );
    }
    // TODO add validation unit tests

    
    // WebPayAdmin::cancelOrder() -------------------------------------------------------------------------------------	
    // returned request class
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
    /// validators
    // invoice
//    public void test_validates_all_required_methods_for_cancelOrder_cancelInvoiceOrder() {
//    public void test_missing_required_method_for_cancelOrder_cancelInvoiceOrder_setOrderId() {
//    public void test_missing_required_method_for_cancelOrder_cancelInvoiceOrder_setCountryCode() {
//    public void test_validates_all_required_methods_for_cancelOrder_cancelPaymentPlanOrder() {
//    public void test_missing_required_method_for_cancelOrder_cancelPaymentPlanOrder_setOrderId() {
//    public void test_missing_required_method_for_cancelOrder_cancelPaymentPlanOrder_setCountryCode() {
    // card
    function test_validates_all_required_methods_for_cancelOrder_cancelCardOrder() {
        $request = WebPayAdmin::cancelOrder(Svea\SveaConfig::getDefaultConfig())
            ->setOrderId("123456789")                
            ->setCountryCode("SE")            
        ;
        try {
            $request->cancelCardOrder()->prepareRequest();
        }
        catch (Exception $e){
            // fail on validation error
            $this->fail( "Unexpected validation exception: " . $e->getMessage() );
        }
    }  

    function test_missing_required_method_for_cancelOrder_cancelCardOrder_setOrderId() {
        $request = WebPayAdmin::cancelOrder(Svea\SveaConfig::getDefaultConfig())
            //->setOrderId("123456789")                
            ->setCountryCode("SE")            
        ;       
        $this->setExpectedException(
            'Svea\ValidationException', 
            '-missing value : transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response'
        );   
        $request->cancelCardOrder()->prepareRequest();
    } 
    
    function test_missing_required_method_for_cancelOrder_cancelCardOrder_setCountryCode() {
        $request = WebPayAdmin::cancelOrder(Svea\SveaConfig::getDefaultConfig())
            ->setOrderId("123456789")                
            //->setCountryCode("SE")            
        ;       
        $this->setExpectedException(
            'Svea\ValidationException', 
            '-missing value : CountryCode is required. Use function setCountryCode().'
        );   
        $request->cancelCardOrder()->prepareRequest();
    }  
    
    // WebPayAdmin::queryOrder() -----------------------------------------------
    // returned type
    /// queryOrder()
    // invoice
    // partpayment
    // card
    function test_queryOrder_queryCardOrder() {
        // Set the below to match the transaction, then run the test.
        $transactionId = 590177;

        $request = WebPayAdmin::queryOrder( 
                Svea\SveaConfig::getSingleCountryConfig(
                    "SE", 
                    "foo", "bar", "123456", // invoice
                    "foo", "bar", "123456", // paymentplan
                    "1200", // merchantid, secret
                    "27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d",
                    false // prod = false
                )
            )    
            ->setTransactionId( strval($transactionId) )
            ->setCountryCode("SE")
        ;
        $response = $request->queryCardOrder()->doRequest();            
//        echo "foo: ";
//        var_dump($response); die;
        
        $this->assertEquals( 1, $response->accepted );    
        
        $this->assertEquals( $transactionId, $response->transactionId );                            
        $this->assertInstanceOf( "Svea\NumberedOrderRow", $response->numberedOrderRows[0] );
        $this->assertEquals( "Soft213s", $response->numberedOrderRows[0]->articleNumber );
        $this->assertEquals( "1.0", $response->numberedOrderRows[0]->quantity );
        $this->assertEquals( "st", $response->numberedOrderRows[0]->unit );
        $this->assertEquals( 3212.00, $response->numberedOrderRows[0]->amountExVat );   // amount = 401500, vat = 80300 => 3212.00 @25%
        $this->assertEquals( 25, $response->numberedOrderRows[0]->vatPercent );
        $this->assertEquals( "Soft", $response->numberedOrderRows[0]->name );
//        $this->assertEquals( "Specification", $response->numberedOrderRows[1]->description );
        $this->assertEquals( 0, $response->numberedOrderRows[0]->vatDiscount );                      

        $this->assertInstanceOf( "Svea\NumberedOrderRow", $response->numberedOrderRows[1] );
        $this->assertEquals( "07", $response->numberedOrderRows[1]->articleNumber );
        $this->assertEquals( "1.0", $response->numberedOrderRows[1]->quantity );
        $this->assertEquals( "st", $response->numberedOrderRows[1]->unit );
        $this->assertEquals( 0, $response->numberedOrderRows[1]->amountExVat );   // amount = 401500, vat = 80300 => 3212.00 @25%
        $this->assertEquals( 0, $response->numberedOrderRows[1]->vatPercent );
        $this->assertEquals( "Sits: Hatfield Beige 6", $response->numberedOrderRows[1]->name );
//        $this->assertEquals( "Specification", $response->numberedOrderRows[1]->description );
        $this->assertEquals( 0, $response->numberedOrderRows[1]->vatDiscount );                      
    }
    
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
        
// TODO add validation unit tests
    
    // WebPayAdmin::cancelOrderRows() ------------------------------------------
    public function test_cancelOrderRows_returns_AddOrderRowsBuilder() {
        $builderObject = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );        
        $this->assertInstanceOf( "Svea\CancelOrderRowsBuilder", $builderObject );
    }
    // invoice
    public function test_cancelOrderRows_cancelInvoiceOrderRows_returns_CancelOrderRowsRequest() {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrderRowsBuilder->cancelInvoiceOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\CancelOrderRowsRequest", $request );
    }      
    // partpayment
    public function test_cancelOrderRows_cancelPaymentPlanOrderRows_returns_CancelOrderRowsRequest() {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );
        $request = $cancelOrderRowsBuilder->cancelPaymentPlanOrderRows();        
        $this->assertInstanceOf( "Svea\AdminService\CancelOrderRowsRequest", $request );
    }              
    // card
    public function test_cancelOrderRows_cancelCardOrderRows_returns_LowerTransaction() {
        $cancelOrderRowsBuilder = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->addNumberedOrderRow( TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            ->setRowToCancel(1)
        ;
        $request = $cancelOrderRowsBuilder->cancelCardOrderRows();        
        $this->assertInstanceOf( "Svea\HostedService\LowerTransaction", $request );
    }  
    // TODO add validation tests here
    
    // WebPayAdmin::creditOrderRows --------------------------------------------    
    public function test_creditOrderRows_returns_CreditOrderRowsBuilder() {
        $builderObject = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() );        
        $this->assertInstanceOf( "Svea\CreditOrderRowsBuilder", $builderObject );
    } 
     
    // creditInvoiceOrderRows  
    function test_validates_all_required_methods_for_creditOrderRows_creditInvoiceRows() {       
        // needs either setRow(s)ToCredit or addCreditOrderRow(s)    
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )         
            ->setRowToCredit(1)              
            //->setRowsToCredit(array(1,2))
        ;
        try {
            $request->creditInvoiceOrderRows()->prepareRequest();
        }
        catch (Exception $e){
            // fail on validation error
            $this->fail( "Unexpected validation exception: " . $e->getMessage() );
        }
    }
    
    function test_validates_missing_required_method_for_creditOrderRows_creditInvoiceOrderRows_missing_rows() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )         
            //->setRowToCredit(1)              
            //->setRowsToCredit(array(1,2))
        ;        
        $this->setExpectedException(
            'Svea\ValidationException', 
            '-missing value : no rows to credit, use setRow(s)ToCredit() or addCreditOrderRow(s)().'
        );   
        $request->creditInvoiceOrderRows()->prepareRequest();
    }
    
    function test_validates_missing_required_method_for_creditOrderRows_creditInvoiceOrderRows_missing_setCountryCode() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            //->setCountryCode("SE")            
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )         
            ->setRowToCredit(1)              
            //->setRowsToCredit(array(1,2))
        ;        
        $this->setExpectedException(
            'Svea\ValidationException', 
            '-missing value : countryCode is required, use setCountryCode().'
        );   
        $request->creditInvoiceOrderRows()->prepareRequest();
    }    

    function test_validates_missing_required_method_for_creditOrderRows_creditInvoiceOrderRows_missing_setInvoiceDistributionType() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setInvoiceId("123456789")                
            //->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )         
            ->setRowToCredit(1)              
            //->setRowsToCredit(array(1,2))
        ;        
        $this->setExpectedException(
            'Svea\ValidationException', 
            '-missing value : distributionType is required, use setInvoiceDistributionType().'
        );   
        $request->creditInvoiceOrderRows()->prepareRequest();
    }     

    function test_validates_missing_required_method_for_creditOrderRows_creditInvoiceOrderRows_missing_setInvoiceId() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            //->setInvoiceId("123456789")                
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCountryCode("SE")            
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )         
            ->setRowToCredit(1)              
            //->setRowsToCredit(array(1,2))
        ;        
        $this->setExpectedException(
            'Svea\ValidationException', 
            '-missing value : invoiceId is required, use setInvoiceId().'
        );   
        $request->creditInvoiceOrderRows()->prepareRequest();
    }       
    
    // creditCardOrderRows
    function test_validates_all_required_methods_for_creditOrderRows_creditCardOrderRows() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId("123456")              
            ->setCountryCode("SE")
            ->addCreditOrderRow( TestUtil::createOrderRow() )
            ->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )    
            ->setRowToCredit(1)
            ->setRowsToCredit(array(2,3))
            ->addNumberedOrderRow( TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            ->addNumberedOrderRows( array( TestUtil::createNumberedOrderRow( 100.00, 1, 2),TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;
        try {
            $request->creditCardOrderRows()->prepareRequest();
        }
        catch (Exception $e){
            // fail on validation error
            $this->fail( "Unexpected validation exception: " . $e->getMessage() );
        }        
    }
    
    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows_missing_setOrderId() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            //->setOrderId("123456")              
            //->setCountryCode("SE")              
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )    
            //->setRowToCredit(1)
            //->setRowsToCredit(array(2,3))
            //->addNumberedOrderRow( TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            //->addNumberedOrderRows( array( TestUtil::createNumberedOrderRow( 100.00, 1, 2),TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;        
        $this->setExpectedException(
            'Svea\ValidationException', 
            'orderId is required for creditCardOrderRows(). Use method setOrderId()'
        );   
        $request->creditCardOrderRows()->prepareRequest();
    }    
    
    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows_missing_setCountryCode() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId("123456")              
            //->setCountryCode("SE")              
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )    
            //->setRowToCredit(1)
            //->setRowsToCredit(array(2,3))
            //->addNumberedOrderRow( TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            //->addNumberedOrderRows( array( TestUtil::createNumberedOrderRow( 100.00, 1, 2),TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;        
        $this->setExpectedException(
            'Svea\ValidationException', 
            'countryCode is required for creditCardOrderRows(). Use method setCountryCode().'
        );   
        $request->creditCardOrderRows()->prepareRequest();
    }    

    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows_missing_rows_to_credit() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId("123456")              
            ->setCountryCode("SE")              
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )    
            //->setRowToCredit(1)
            //->setRowsToCredit(array(2,3))
            //->addNumberedOrderRow( TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            //->addNumberedOrderRows( array( TestUtil::createNumberedOrderRow( 100.00, 1, 2),TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;        
        $this->setExpectedException(
            'Svea\ValidationException', 
            'at least one of rowsToCredit or creditOrderRows must be set. Use setRowToCredit() or addCreditOrderRow().'
        );   
        $request->creditCardOrderRows()->prepareRequest();
    }    

    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows_missing_numberedOrderRows() {
        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId("123456")              
            ->setCountryCode("SE")              
            //->addCreditOrderRow( TestUtil::createOrderRow() )
            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )    
            ->setRowToCredit(1)
            //->setRowsToCredit(array(2,3))
            //->addNumberedOrderRow( TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
            //->addNumberedOrderRows( array( TestUtil::createNumberedOrderRow( 100.00, 1, 2),TestUtil::createNumberedOrderRow( 100.00, 1, 3 ) ) )
        ;        
        $this->setExpectedException(
            'Svea\ValidationException', 
            'every entry in rowsToCredit must have a corresponding numberedOrderRows. Use setRowsToCredit() and addNumberedOrderRow()'
        );   
        $request->creditCardOrderRows()->prepareRequest();
    }        
        
    function test_validates_missing_required_method_for_creditOrderRows_creditCardOrderRows__mismatched_numberedOrderRows() {              
        $creditOrderRowsObject = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
                ->setOrderId("123456")
                ->setCountryCode("SE")              
                ->addNumberedOrderRow( TestUtil::createNumberedOrderRow( 100.00, 1, 1 ) )
                ->setRowToCredit(9)
        ;
        $this->setExpectedException(
          '\Svea\ValidationException', 'every entry in rowsToCredit must match a numberedOrderRows. Use setRowsToCredit() and addNumberedOrderRow().'
        );      
        $request = $creditOrderRowsObject->creditCardOrderRows(); // exception thrown in builder when selecting request class   
    }        
    
    // creditDirectBankOrderRows
    function test_no_separate_validation_tests_for_creditOrderRows_creditDirectBankOrderRows() {
        // creditDirectBankOrderRows is an alias of creditCardOrderRows, so no separate tests are needed
    }
     
    // end creditOrderRows tests -----------------------------------------------
    
    // WebPayAdmin::addOrderRows() ---------------------------------------------
    public function test_addOrderRows_returns_AddOrderRowsBuilder() {
        $builderObject = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() );        
        $this->assertInstanceOf( "Svea\AddOrderRowsBuilder", $builderObject );
    } 
    // TODO add validation unit tests
    // end addOrderRows tests --------------------------------------------------
    
    // WebPayAdmin::updateOrderRows() ------------------------------------------
    public function test_updateOrderRows_returns_AddOrderRowsBuilder() {
        $builderObject = WebPayAdmin::updateOrderRows( Svea\SveaConfig::getDefaultConfig() );        
        $this->assertInstanceOf( "Svea\UpdateOrderRowsBuilder", $builderObject );
    }
    // TODO add validation unit tests
    // end updateOrderRows tests -----------------------------------------------
    
    // WebPayAdmin::deliverOrderRows() -----------------------------------------
    // TODO add validation unit tests
    // end deliverOrderRows tests ----------------------------------------------

    
   
}
