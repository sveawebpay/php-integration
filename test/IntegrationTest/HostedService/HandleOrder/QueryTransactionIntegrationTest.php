<?php
// Integration tests should not need to use the namespace
use Svea\HostedService\QueryTransaction as QueryTransaction;

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
                
        $request = new QueryTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request
            ->setTransactionId( $transactionId )
            ->setCountryCode( "SE" )
            ->doRequest();

        $this->assertInstanceOf( "Svea\HostedService\QueryTransactionResponse", $response );
        
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
        $this->markTestIncomplete(
            'skeleton for manual test of query card transaction' // TODO
        );

        // 1. go to test.sveaekonomi.se/webpay/admin/start.xhtml 
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>KORTCERT</paymentmethod><currency>SEK</currency><amount>25500</amount><vat>600</vat><customerrefno>test_manual_query_card_2</customerrefno><returnurl>https://test.sveaekonomi.se/webpay/admin/merchantresponsetest.xhtml</returnurl><orderrows><row><name>Orderrow1</name><amount>500</amount><vat>100</vat><description>Orderrow description</description><quantity>1</quantity><sku>123</sku><unit>st</unit></row><row><name>Orderrow2</name><amount>12500</amount><vat>2500</vat><description>Orderrow2 description</description><quantity>2</quantity><sku>124</sku><unit>m2</unit></row></orderrows>
        // 5. the result should be:
        // <response><transaction id="580964"><paymentmethod>KORTCERT</paymentmethod><merchantid>1130</merchantid><customerrefno>test_manual_query_card_3</customerrefno><amount>25500</amount><currency>SEK</currency><cardtype>VISA</cardtype><maskedcardno>444433xxxxxx1100</maskedcardno><expirymonth>02</expirymonth><expiryyear>15</expiryyear><authcode>898924</authcode></transaction><statuscode>0</statuscode></response>

        // 6. enter the received transaction id below and run the test
        
        // Set the below to match the transaction, then run the test.
        $transactionId = 580964;

        $request = new QueryTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request
            ->setTransactionId( $transactionId )
            ->setCountryCode( "SE" )
            ->doRequest();        
         
        $this->assertInstanceOf( "Svea\HostedService\QueryTransactionResponse", $response );
        
        print_r($response);
        $this->assertEquals( 1, $response->accepted );    
        $this->assertEquals( 0, $response->resultcode );
        
        $this->assertEquals( $transactionId, $response->transactionId );
        $this->assertEquals( "test_manual_query_card_3", $response->customerrefno );
        $this->assertEquals( "1130", $response->merchantid );
        $this->assertEquals( "AUTHORIZED", $response->status );
        $this->assertEquals( "25500", $response->amount );
        $this->assertEquals( "SEK", $response->currency );
        $this->assertEquals( "600", $response->vat );
        $this->assertEquals( "", $response->capturedamount );
        $this->assertEquals( "25500", $response->authorizedamount );
        //$this->assertEquals( "", $response->created );
        $this->assertEquals( "CREDNONE", $response->creditstatus );
        $this->assertEquals( "0", $response->creditedamount );
        $this->assertEquals( "0", $response->merchantresponsecode );
        $this->assertEquals( "KORTCERT", $response->paymentmethod );
        
        $this->assertInstanceOf( "Svea\OrderRow", $response->orderrows[0] );
        $this->assertEquals( "123", $response->orderrows[0]->articleNumber );
        $this->assertEquals( "1", $response->orderrows[0]->quantity );
        $this->assertEquals( "st", $response->orderrows[0]->unit );
        $this->assertEquals( 4, $response->orderrows[0]->amountExVat );
        $this->assertEquals( 25, $response->orderrows[0]->vatPercent );
        $this->assertEquals( "Orderrow1", $response->orderrows[0]->name );
        $this->assertEquals( "Orderrow description", $response->orderrows[0]->description );
        $this->assertEquals( 0, $response->orderrows[0]->vatDiscount );
                        
        $this->assertInstanceOf( "Svea\OrderRow", $response->orderrows[1] );
        $this->assertEquals( "124", $response->orderrows[1]->articleNumber );
        $this->assertEquals( "2", $response->orderrows[1]->quantity );
        $this->assertEquals( "m2", $response->orderrows[1]->unit );
        $this->assertEquals( 100, $response->orderrows[1]->amountExVat );
        $this->assertEquals( 25, $response->orderrows[1]->vatPercent );
        $this->assertEquals( "Orderrow2", $response->orderrows[1]->name );
        $this->assertEquals( "Orderrow2 description", $response->orderrows[1]->description );
        $this->assertEquals( 0, $response->orderrows[1]->vatDiscount );
                                                           
        
//            [0] => Svea\OrderRow Object
//                (
//                    [articleNumber] => 123
//                    [quantity] => 1
//                    [unit] => st
//                    [amountExVat] => 4
//                    [amountIncVat] => 
//                    [vatPercent] => 25
//                    [name] => Orderrow1
//                    [description] => Orderrow description
//                    [discountPercent] => 
//                    [vatDiscount] => 0
//                )
//
//            [1] => Svea\OrderRow Object
//                (
//                    [articleNumber] => 124
//                    [quantity] => 2
//                    [unit] => m2
//                    [amountExVat] => 122.5
//                    [amountIncVat] => 
//                    [vatPercent] => 2.0408163265306
//                    [name] => Orderrow2
//                    [description] => Orderrow2 description
//                    [discountPercent] => 
//                    [vatDiscount] => 0
//                )
        
        
    }    

    /**
     * test_manual_query_card_queryTransactionResponse 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    function test_manual_query_card_queryTransaction_returntype() {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of query card transaction' // TODO
        );

        // 1. go to test.sveaekonomi.se/webpay/admin/start.xhtml 
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>KORTCERT</paymentmethod><currency>SEK</currency><amount>25500</amount><vat>600</vat><customerrefno>test_manual_query_card_2</customerrefno><returnurl>https://test.sveaekonomi.se/webpay/admin/merchantresponsetest.xhtml</returnurl><orderrows><row><name>Orderrow1</name><amount>500</amount><vat>100</vat><description>Orderrow description</description><quantity>1</quantity><sku>123</sku><unit>st</unit></row><row><name>Orderrow2</name><amount>12500</amount><vat>2500</vat><description>Orderrow2 description</description><quantity>2</quantity><sku>124</sku><unit>m2</unit></row></orderrows>
        // 5. the result should be:
        // <response><transaction id="580964"><paymentmethod>KORTCERT</paymentmethod><merchantid>1130</merchantid><customerrefno>test_manual_query_card_3</customerrefno><amount>25500</amount><currency>SEK</currency><cardtype>VISA</cardtype><maskedcardno>444433xxxxxx1100</maskedcardno><expirymonth>02</expirymonth><expiryyear>15</expiryyear><authcode>898924</authcode></transaction><statuscode>0</statuscode></response>

        // 6. enter the received transaction id below and run the test
        
        // Set the below to match the transaction, then run the test.
        $transactionId = 582690;

        $request = new QueryTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request
            ->setTransactionId( $transactionId )
            ->setCountryCode( "SE" )
            ->doRequest();        
         
        $this->assertInstanceOf( "Svea\HostedService\QueryTransactionResponse", $response );
        
        print_r($response);
        $this->assertEquals( 1, $response->accepted );    
        $this->assertInstanceOf( "Svea\NumberedOrderRow", $response->numberedOrderRows[0] );              
        $this->assertInstanceOf( "Svea\NumberedOrderRow", $response->numberedOrderRows[1] );

        $this->assertEquals( 0, $response->orderrows[1]->vatDiscount );
    }
    
    
//<paymentmethod>KORTCERT</paymentmethod>
//    <currency>EUR</currency>
//    <amount>1000</amount>
//    <vat>200</vat>
//    <customerrefno>test_1400770436503</customerrefno>
//    <returnurl>https://test.sveaekonomi.se/webpay/public/merchantresponsetest.xhtml</returnurl>
//<orderrows>
//    <row>
//        <name>Orderrow1</name>
//        <amount>500</amount>
//        <vat>100</vat>
//        <description>row A</description>
//        <quantity>1</quantity>
//        <sku>665</sku>
//        <unit>st</unit>
//    </row>
//    <row>
//        <name>Orderrow1</name>
//        <amount>1000</amount>
//        <vat>200</vat>
//        <description>row B</description>
//        <quantity>1</quantity>
//        <sku>665</sku>
//        <unit>st</unit>
//    </row>
//</orderrows>
    
    }
?>
