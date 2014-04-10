<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * QueryTransactionIntegrationTest 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class QueryTransactionIntegrationTest extends \PHPUnit_Framework_TestCase {
 
   /**
     * test_queryTransaction_card_success creates an order using card payment, 
     * pays using card & receives a transaction id, then credits the transaction
     * 
     * used as acceptance criteria/smoke test for query transaction feature
     */
    function test_queryTransaction_card_success() {
      
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
        
        $url = "https://test.sveaekonomi.se/webpay/payment";    //TODO get this via ConfigurationProvider

        // do request modeled on CardPymentIntegrationTest.php
                
        // make sure the transaction has status AUTHORIZED or CONFIRMED at Svea
        
        // query transcation with the above transactionId
        
        // assert response from queryTransaction equals success
    }    
    
    /**
     * test_query_card_transaction_not_found 
     * 
     * used as initial acceptance criteria for query transaction feature
     */  
    function test_query_card_transaction_not_found() {
             
        $transactionId = 987654;
                
        $request = new Svea\QueryTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request
            ->setTransactionId( $transactionId )
            ->setCountryCode( "SE" )
            ->doRequest();

        $this->assertInstanceOf( "Svea\QueryTransactionResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }
    
    /**
     * test_manual_query_card 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    function test_manual_query_card() {

        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for manual test of query card transaction' // TODO
//        );
        
        // Set the below to match the transaction, then run the test.
        $customerrefno = 313;
        $transactionId = 579929;

        $request = new Svea\QueryTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request
            ->setTransactionId( $transactionId )
            ->setCountryCode( "SE" )
            ->doRequest();        
         
        $this->assertInstanceOf( "Svea\QueryTransactionResponse", $response );
        
        print_r($response);
        $this->assertEquals( 1, $response->accepted );    
        
        $this->assertEquals( $transactionId, $response->transactionId );     
        // TODO rest of attributes from query, when decided upon...
    }    
}
?>
