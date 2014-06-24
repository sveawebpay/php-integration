<?php
use Svea\HostedService\RecurTransaction as RecurTransaction;

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
                
        $request = new RecurTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request  
            ->setSubscriptionId( $subscriptionId )
            ->setCustomerRefNo( $customerRefNo )
            ->setAmount( $amount )
            ->setCurrency( $currency )
            ->setCountryCode( "SE" )
            ->doRequest();

        $this->assertInstanceOf( "Svea\HostedService\RecurTransactionResponse", $response );
        
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
        
        // 1. go to test.sveaekonomi.se/webpay/admin/start.xhtml 
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>KORTCERT</paymentmethod><subscriptiontype>RECURRINGCAPTURE</subscriptiontype><currency>SEK</currency><amount>500</amount><vat>100</vat><customerrefno>test_recur_NN</customerrefno><returnurl>https://test.sveaekonomi.se/webpay-admin/admin/merchantresponsetest.xhtml</returnurl>
        // 5. the result should be:
        // <response><transaction id="581497"><paymentmethod>KORTCERT</paymentmethod><merchantid>1130</merchantid><customerrefno>test_recur_1</customerrefno><amount>500</amount><currency>SEK</currency><subscriptionid>2922</subscriptionid><cardtype>VISA</cardtype><maskedcardno>444433xxxxxx1100</maskedcardno><expirymonth>02</expirymonth><expiryyear>16</expiryyear><authcode>993955</authcode></transaction><statuscode>0</statuscode></response>

        // 6. enter the received subscription id, etc. below and run the test

        // Set the below to match the original transaction, then run the test.
        $paymentmethod = "KORTCERT";  
        $merchantid = 1130;  
        $currency = "SEK";  
        $cardtype = "VISA";  
        $maskedcardno = "444433xxxxxx1100";
        $expirymonth = 02;  
        $expiryyear = 15;  
        $subscriptionid = 3036; 

        // the below applies to the recur request, and may differ from the original transaction
        $new_amount = "2500"; // in minor currency  
        $new_customerrefno = "test_recur_".date('c');  

        // below is actual test, shouldn't need to change it
        $request = new RecurTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request                
            ->setSubscriptionId( $subscriptionid )
            ->setCurrency( $currency )
            ->setCustomerRefNo( $new_customerrefno )
            ->setAmount( $new_amount )
            ->setCountryCode( "SE" )
            ->doRequest();        
        
        $this->assertInstanceOf( "Svea\HostedService\RecurTransactionResponse", $response );
        
        print_r($response);                
        $this->assertEquals( 1, $response->accepted );  
        
        $this->assertEquals( "CARD", $response->paymentmethod );    // CARD is alias for KORTCERT, used by webservice...
        $this->assertEquals( $merchantid, $response->merchantid );  
        $this->assertEquals( $currency, $response->currency );  
        $this->assertEquals( $cardtype, $response->cardtype );  
        $this->assertEquals( $maskedcardno, $response->maskedcardno );  
        $this->assertEquals( $expirymonth, $response->expirymonth );  
        $this->assertEquals( $expiryyear, $response->expiryyear );  
        $this->assertEquals( $subscriptionid, $response->subscriptionid );  

        $this->assertObjectHasAttribute( "transactionid", $response );
        $this->assertEquals( $new_customerrefno, $response->customerrefno );  
        $this->assertEquals( $new_amount, $response->amount );         
    }    
}
?>
