<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * RecurTransactionIntegrationTest 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class RecurTransactionIntegrationTest extends \PHPUnit_Framework_TestCase {
     
    /**
     * test_recur_subscriptionid_not_found 
     * 
     * used as initial acceptance criteria for credit transaction feature
     */  
    function test_recur_subscriptionid_not_found() {
             
        $subscriptionId = 987654;
        $customerRefNo = "myCustomerRefNo";
        $currency = "SEK";
        $amount = 100;
                
        $request = new Svea\RecurTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request  
            ->setSubscriptionId( $subscriptionId )
            ->setCustomerRefNo( $customerRefNo )
            ->setAmount( $amount )
            ->setCurrency( $currency )
            ->setCountryCode( "SE" )
            ->doRequest();

        $this->assertInstanceOf( "Svea\RecurTransactionResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "322 (BAD_SUBSCRIPTION_ID)", $response->resultcode );    
    }
    
    /**
     * test_manual_recur_transaction_amount 
     * 
     * run this test manually after you've performed a card transaction with 
     * subscriptiontype set and have gotten the transaction details needed
     */  
    function test_manual_recur_transaction_amount() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'skeleton for manual test of recur transaction amount'
        );
        
        // Set the below to match the transaction, then run the test.
        $customerrefno = 317;
        $subscriptionId = 580012;
        $amount = 3000;
// @todo + övriga från original subscription-transaktionen
        
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
        
        $request = new Svea\RecurTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request                
            ->setTransactionId( $transactionId )
            ->setAmountToLower( $amountToLower )
            ->setCountryCode( "SE" )
            ->doRequest();        
        
        $this->assertInstanceOf( "Svea\RecurTransactionResponse", $response );
        
        print_r($response);                
        $this->assertEquals( 1, $response->accepted );        
        $this->assertEquals( $customerrefno, $response->customerrefno );  
        $this->assertEquals( $paymentmethod, $response->paymentmethod );  
        $this->assertEquals( $merchantid, $response->merchantid );  
        $this->assertEquals( $amount, $response->amount );  
        $this->assertEquals( $currency, $response->currency );  
        $this->assertEquals( $cardtype, $response->cardtype );  
        $this->assertEquals( $maskedcardno, $response->maskedcardno );  
        $this->assertEquals( $expirymonth, $response->expirymonth );  
        $this->assertEquals( $expiryyear, $response->expiryyear );  
        $this->assertEquals( $authcode, $response->authcode );  
        $this->assertEquals( $subscriptionid, $response->subscriptionid );  
    }    
}
?>
