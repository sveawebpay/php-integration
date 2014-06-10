<?php
use Svea\HostedService\LowerTransaction as LowerTransaction;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * LowerTransactionIntegrationTest 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class LowerTransactionIntegrationTest extends \PHPUnit_Framework_TestCase {
 
   /**
     * test_lowerTransaction_card_success creates an order using card payment, 
     * pays using card & receives a transaction id, then credits the transaction
     * 
     * used as acceptance criteria/smoke test for credit transaction feature
     */
    function test_lowerTransaction_card_success() { 
      
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
        
        // assert response from lowerTransactionAmount equals success
    }
        
    /**
     * test_lower_transaction_transaction_not_found 
     * 
     * used as initial acceptance criteria for credit transaction feature
     */  
    function test_lower_transaction_transaction_not_found() {
             
        $transactionId = 987654;
        $amountToLower = 100;
                
        $request = new LowerTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request  
            ->setTransactionId( $transactionId )
            ->setAmountToLower( $amountToLower )
            ->setCountryCode( "SE" )
            ->doRequest();

        $this->assertInstanceOf( "Svea\HostedService\LowerTransactionResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }
    
    /**
     * test_manual_lower_transaction_amount 
     * 
     * run this test manually after you've performed a card transaction and have
     * gotten the the transaction details needed
     */  
    function test_manual_lower_transaction_amount() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'skeleton for manual test of lower transaction amount'
        );
        
        // Set the below to match the transaction, then run the test.
        $customerrefno = 317;
        $transactionId = 580012;
        $amountToLower = 3000;   // also check that status if lower by entire amount == ANNULLED
                
        // i.e. order of 117 kr => 11700 at Svea, Svea status AUTHORIZED
        // - 100 => success, 11600 at Svea, Svea status AUTHORIZED
        // - 11600 => success, Svea status ANNULLED
        // - 1 => failure, accepted = 0, resultcode = "105 (ILLEGAL_TRANSACTIONSTATUS)", errormessage = "Invalid transaction status."
        // 
        // new order of 130 kr => 13000 at Svea
        // - 13001 => failure, accepted = 0, resultcode = "305 (BAD_AMOUNT), errormessage = "Invalid value for amount."
        // - 10000 => success, success, 3000 at Svea, Svea status AUTHORIZED
        // - 3001 => failure, accepted = 0, resultcode = "305 (BAD_AMOUNT), errormessage = "Invalid value for amount."
        // - 3000 => success, Svea status ANNULLED
        
        $request = new LowerTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request                
            ->setTransactionId( $transactionId )
            ->setAmountToLower( $amountToLower )
            ->setCountryCode( "SE" )
            ->doRequest();        
        
        $this->assertInstanceOf( "Svea\HostedService\LowerTransactionResponse", $response );
        
        print_r($response);                
        $this->assertEquals( 1, $response->accepted );        
        $this->assertEquals( $customerrefno, $response->customerrefno );  
    }    
}
?>
