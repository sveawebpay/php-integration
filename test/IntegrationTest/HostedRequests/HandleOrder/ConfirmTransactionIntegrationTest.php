<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * ConfirmTransactionIntegrationTest 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class ConfirmTransactionIntegrationTest extends \PHPUnit_Framework_TestCase {
 
   /**
     * test_card_confirmTransaction_success creates an order using card payment, 
     * pays using card & receives a transaction id, then confirms the transaction
     * 
     * used as acceptance criteria/smoke test for credit transaction feature
     */
    function _test_confirmTransaction_card_success() { 
      
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
                
        // make sure the transaction has status AUTHORIZED at Svea
        
        // confirm transcation using above the transaction transactionId
        
        // assert response from confirmTransaction equals success
    }
    
    
    /**
     * test_credit_card_transaction_not_found 
     * 
     * used as initial acceptance criteria for credit transaction feature
     */  
    function test_confirm_card_transaction_not_found() {
             
        $transactionId = 987654;
        $captureDate = "2014-03-21";
                
        $response = WebPay::confirmTransaction( Svea\SveaConfig::getDefaultConfig() )
            ->setTransactionId( $transactionId )
            ->setCaptureDate( $captureDate )
            ->setCountryCode( "SE" )
            ->doRequest();

        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }
    
    /**
     * test_manual_credit_card 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    function test_manual_confirm_card() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'skeleton for manual test of confirm card transaction'
        );
        
        // Set the below to match the transaction, then run the test.
        $customerrefno = 314;
        $transactionId = 579967;
        $captureDate = "2014-03-21";

                
        $request = WebPay::confirmTransaction( Svea\SveaConfig::getDefaultConfig() )
            ->setTransactionId( $transactionId )
            ->setCaptureDate( $captureDate )
            ->setCountryCode( "SE" );
    
        $response = $request->doRequest();        
        
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );

//        print_r( $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 1, $response->accepted );        
        $this->assertEquals( $customerrefno, $response->customerrefno );  
        

        
    }    
}
?>
