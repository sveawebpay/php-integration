<?php
use Svea\HostedService\CreditTransaction as CreditTransaction;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * CreditTransactionIntegrationTest 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditTransactionIntegrationTest extends \PHPUnit_Framework_TestCase {
 
    /**
     * test_credit_card_transaction_not_found 
     * 
     * used as initial acceptance criteria for credit transaction feature
     */  
    function test_credit_card_transaction_not_found() {
             
        $transactionId = 987654;
        $amount = 100;
                
        $request = new CreditTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request
            ->setTransactionId( $transactionId )
            ->setCreditAmount( $amount )
            ->setCountryCode( "SE" )
            ->doRequest();

        $this->assertInstanceOf( "Svea\HostedService\CreditTransactionResponse", $response );
        
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }
    
    /**
     * test_manual_credit_card 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    function test_manual_credit_card() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'skeleton for manual test of credit card transaction'
        );
        
        // Set the below to match the transaction, then run the test.
        //$customerrefno = 312;
        $transactionId = 583151;
        $amount = 100;
                
        $request = new CreditTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request
            ->setTransactionId( $transactionId )
            ->setCreditAmount( $amount )
            ->setCountryCode( "SE" )
            ->doRequest();        
        
        $this->assertInstanceOf( "Svea\HostedService\CreditTransactionResponse", $response );
        
        print_r( $response );
        $this->assertEquals( 1, $response->accepted );        
        //$this->assertEquals( $customerrefno, $response->customerrefno );  
    }    
}
?>
