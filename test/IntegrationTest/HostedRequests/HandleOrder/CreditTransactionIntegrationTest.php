<?php
// Integration tests should not need to use the namespace

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
     * test_card_creditTransaction_success creates an order using card payment, 
     * pays using card & receives a transaction id, then credits the transaction
     * 
     * used as acceptance criteria/smoke test for credit transaction feature
     */
    function _test_card_creditTransaction_success() { 
      
        // not yet implemented, requires webdriver support

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'not yet implemented, requires webdriver support' // TODO
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
                
        // make sure the transaction has status SUCCESS at Svea
        
        // credit transcation using above the transaction transactionId
        
        // assert response from creditTransaction equals success
    }
    
    
    /**
     * test_transaction_not_found 
     * 
     * used as initial acceptance criteria for credit transaction feature
     */  
    function test_transaction_not_found() {
             
        $transactionId = 987654;
        $amount = 100;
                
        $response = WebPay::creditTransaction( Svea\SveaConfig::getDefaultConfig() )
            ->setTransactionId( $transactionId )
            ->setCreditAmount( $amount )
            ->setCountryCode( "SE" )
            ->doRequest();

        // TODO remove -- make sure we get a SveaResponse object back
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );

        print_r( $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode ); // NO_SUCH_TRANS ??  // TODO check error code received        
    }
}
?>
