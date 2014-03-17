<?php
// Integration tests should not need to use the namespace

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
     * test_card_creditTransaction_success creates an order using card payment, 
     * pays using card & receives a transaction id, then credits the transaction
     * 
     * used as acceptance criteria/smoke test for credit transaction feature
     */
    function _test_annulTransaction_card_success() {
      
        // not yet implemented, requires webdriver support

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'not yet implemented, requires webdriver support'
        );
        
        // also, needs to have SUCCESS status set on transaction

        // set up order (from testUtil?)
        $order = TestUtil::createOrder();
        
        // pay with card, receive transactionId
        $form = $order
            ->UsePaymentMethod( PaymentMethod::KORTCERT )
            ->setReturnUrl("http://myurl.se")
            //->setCancelUrl()
            //->setCardPageLanguage("SE")
            ->getPaymentForm();
        
        $url = "https://test.sveaekonomi.se/webpay/payment";

        // do request modeled on CardPymentIntegrationTest.php
                
        // make sure the transaction has status AUTHORIZED or CONFIRMED at Svea
        
        // annul transcation with the above transactionId
        
        // assert response from annulTransaction equals success
    }
    
    
    /**
     * test_annul_card_transaction_not_found 
     * 
     * used as initial acceptance criteria for annul transaction feature
     */  
    function test_annul_card_transaction_not_found() {
             
        $transactionId = 987654;
                
        $response = WebPay::annulTransaction( Svea\SveaConfig::getDefaultConfig() )
            ->setTransactionId( $transactionId )
            ->setCountryCode( "SE" )
            ->doRequest();

        // TODO remove -- make sure we get a SveaResponse object back
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }
    
    /**
     * test_manual_card_credit 
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
        $customerrefno = 312;
        $transactionId = 579893;

        $request = WebPay::annulTransaction( Svea\SveaConfig::getDefaultConfig() )
            ->setTransactionId( $transactionId )
            ->setCountryCode( "SE" );
    
        $response = $request->doRequest();        
        
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 1, $response->accepted );        
        $this->assertEquals( $customerrefno, $response->customerrefno );  
    }    
}
?>
