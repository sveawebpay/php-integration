<?php
use Svea\HostedService\AnnulTransaction as AnnulTransaction;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * AnnulTransactionIntegrationTest 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class AnnulTransactionIntegrationTest extends \PHPUnit_Framework_TestCase {
        
    /**
     * test_annul_card_transaction_not_found 
     * 
     * used as initial acceptance criteria for annul transaction feature
     */  
    function test_annul_card_transaction_not_found() {
             
        $transactionId = 987654;

        $request = new AnnulTransaction( Svea\SveaConfig::getDefaultConfig() );
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf( "Svea\HostedService\AnnulTransactionResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }
    
    /**
     * test_manual_annul_card 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    function test_manual_annul_card() {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of annul card transaction' // TODO
        );
        
        // Set the below to match the transaction, then run the test.
        $customerrefno = "794";
        $transactionId = 587947;

        $request = new AnnulTransaction( Svea\SveaConfig::getDefaultConfig() );
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();   
              
        //print_r( $response); 
        $this->assertInstanceOf( "Svea\HostedService\AnnulTransactionResponse", $response );
        $this->assertEquals( 1, $response->accepted );        
        $this->assertStringMatchesFormat( "%d", $response->transactionId);   // %d => an unsigned integer value
        
        $this->assertEquals( $customerrefno, $response->clientOrderNumber );  
    }    
}
?>
