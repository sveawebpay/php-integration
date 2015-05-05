<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

$root = realpath(dirname(__FILE__));
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderRowsRequestTest extends \PHPUnit_Framework_TestCase {

    /// characterising test for INTG-462
    // invoice
    public function test_creditOrderRows_creditInvoiceOrderRows_does_not_validate_setOrderId() {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
                ->setInvoiceId(987654)
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCountryCode('SE')
                ->setRowToCredit(1)
        ;                
        
        // shouldn't raise any exception
        
        $request = $creditOrderRowsBuilder->creditInvoiceOrderRows()->prepareRequest();               
    }    

    // card
    public function test_creditOrderRows_creditCardOrderRows_validates_setOrderId() {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
                //->setOrderId(987654)    // i.e. setTransactionId()
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCountryCode('SE')
                ->setRowToCredit(1)
        ;                

        $this->setExpectedException(
          'Svea\ValidationException', 'orderId is required for creditCardOrderRows(). Use method setOrderId().'
        );    

        $request = $creditOrderRowsBuilder->creditCardOrderRows()->prepareRequest();
    } 
    
    // direct bank
    public function test_creditOrderRows_creditDirectBankOrderRows_validates_setOrderId() {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows( Svea\SveaConfig::getDefaultConfig() )
                //->setTransactionId(987654)    // alias for setOrderId()
                ->setInvoiceDistributionType(DistributionType::POST)
                ->setCountryCode('SE')
                ->setRowToCredit(1)
        ;  
        
        $this->setExpectedException(
          'Svea\ValidationException', 'orderId is required for creditCardOrderRows(). Use method setOrderId().'
        );    
        
        $request = $creditOrderRowsBuilder->creditDirectBankOrderRows()->prepareRequest();             
    } 

    
    
}
