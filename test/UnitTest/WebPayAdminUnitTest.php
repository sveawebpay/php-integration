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
    
    // WebPayAdmin::queryOrder() -----------------------------------------------
    // TODO add validation unit tests
    
    // WebPayAdmin::cancelOrderRows() ------------------------------------------
    public function test_cancelOrderRows_returns_AddOrderRowsBuilder() {
        $builderObject = WebPayAdmin::cancelOrderRows( Svea\SveaConfig::getDefaultConfig() );        
        $this->assertInstanceOf( "Svea\CancelOrderRowsBuilder", $builderObject );
    }
    // TODO add validation unit tests
    
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
        $this->fail( "Expected validation exception not thrown." ); // fail if validation passes, i.e. no exception was thrown                 
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
        $this->fail( "Expected validation exception not thrown." ); // fail if validation passes, i.e. no exception was thrown                 
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
        $this->fail( "Expected validation exception not thrown." ); // fail if validation passes, i.e. no exception was thrown                 
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
        $this->fail( "Expected validation exception not thrown." ); // fail if validation passes, i.e. no exception was thrown                 
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
