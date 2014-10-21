<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class WebPayAdminUnitTest extends \PHPUnit_Framework_TestCase {

//    public function test_WebPayAdmin_class_exists() {
//        $adminObject = new WebPayAdmin();        
//        $this->assertInstanceOf( "WebPayAdmin", $adminObject );
//    }
//
//    public function test_addOrderRows_returns_AddOrderRowsBuilder() {
//        $builderObject = WebPayAdmin::addOrderRows( Svea\SveaConfig::getDefaultConfig() );        
//        $this->assertInstanceOf( "Svea\AddOrderRowsBuilder", $builderObject );
//    }    
//    
//    public function test_cancelOrderRows_returns_AddOrderRowsBuilder() {
//        $builderObject = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );        
//        $this->assertInstanceOf( "Svea\CancelOrderRowsBuilder", $builderObject );
//    }    
//    
//    public function test_creditOrderRows_returns_CreditOrderRowsBuilder() {
//        $builderObject = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() );        
//        $this->assertInstanceOf( "Svea\CreditOrderRowsBuilder", $builderObject );
//    }        

    /// creditOrderRows
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
        $this->fail( "Expected validation exception not thrown." ); // fail if validation passes, i.e. no exception was thrown                 
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
        $this->fail( "Expected validation exception not thrown." ); // fail if validation passes, i.e. no exception was thrown                 
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
        $this->fail( "Expected validation exception not thrown." ); // fail if validation passes, i.e. no exception was thrown                 
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
        $this->fail( "Expected validation exception not thrown." ); // fail if validation passes, i.e. no exception was thrown                 
    }       
    
//
//    // creditCardOrderRows
//    function test_validates_all_required_methods_for_creditOrderRows_creditCardOrderRows() {
//        $request = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
//            ->setOrderId("123456")              
//            ->setCountryCode("SE")              
//            //->addCreditOrderRow( TestUtil::createOrderRow() )
//            //->addCreditOrderRows( array( TestUtil::createOrderRow(), TestUtil::createOrderRow() ) )    
//            //->setRowToCredit(1)
//            //->setRowsToCredit(array(1,2))
//            //->addNumberedOrderRow( TestUtil::createNumberedOrderRow() )
//            //->addNumberedOrderRows( array( TestUtil::createNumberedOrderRow(),TestUtil::createNumberedOrderRow() ) )
//        ;
//        try {
//            $request->creditCardOrderRows()->prepareRequest();
//        }
//        catch (Exception $e){
//            // fail on validation error
//            $this->fail( "Unexpected validation exception: " . $e->getMessage() );
//        }        
//    }
//    
//    // creditDirectBankOrderRows
//
//    
//
//    public function test_updateOrderRows_returns_AddOrderRowsBuilder() {
//        $builderObject = WebPayAdmin::updateOrderRows( Svea\SveaConfig::getDefaultConfig() );        
//        $this->assertInstanceOf( "Svea\UpdateOrderRowsBuilder", $builderObject );
//    }    
//    
//    public function test_cancelOrder_returns_CancelOrderBuilder() {
//        $builderObject = WebPayAdmin::cancelOrder( Svea\SveaConfig::getDefaultConfig() );        
//        $this->assertInstanceOf( "Svea\CancelOrderBuilder", $builderObject );
//    }
        
    // todo add tests for rest of orderBuilder classes here
    
    //HostedRequest/HandleOrder classes
    
//    public function test_annulTransaction() {
//        $config = SveaConfig::getDefaultConfig();
//        $annulTransactionObject = \WebPayAdmin::annulTransaction($config);        
//        $this->assertInstanceOf( "Svea\AnnulTransaction", $annulTransactionObject );
//    }
//    
//    public function test_confirmTransaction() {
//        $config = SveaConfig::getDefaultConfig();
//        $confirmTransactionObject = \WebPayAdmin::confirmTransaction($config);        
//        $this->assertInstanceOf( "Svea\ConfirmTransaction", $confirmTransactionObject );
//    }
//
//    public function test_creditTransaction() {
//        $config = SveaConfig::getDefaultConfig();
//        $creditTransactionObject = \WebPayAdmin::creditTransaction($config);        
//        $this->assertInstanceOf( "Svea\CreditTransaction", $creditTransactionObject );
//    }
//
//    public function test_listPaymentMethods() {
//        $config = SveaConfig::getDefaultConfig();
//        $listPaymentMethodsObject = \WebPayAdmin::listPaymentMethods($config);        
//        $this->assertInstanceOf( "Svea\ListPaymentMethods", $listPaymentMethodsObject );
//    }
//
//    public function test_lowerTransaction() {
//        $config = SveaConfig::getDefaultConfig();
//        $lowerTransactionObject = \WebPayAdmin::lowerTransaction($config);        
//        $this->assertInstanceOf( "Svea\LowerTransaction", $lowerTransactionObject );
//    }     
//    
//    public function test_queryTransaction() {
//        $config = SveaConfig::getDefaultConfig();
//        $queryTransactionObject = \WebPayAdmin::queryTransaction($config);        
//        $this->assertInstanceOf( "Svea\QueryTransaction", $queryTransactionObject );
//    }      
    
}
