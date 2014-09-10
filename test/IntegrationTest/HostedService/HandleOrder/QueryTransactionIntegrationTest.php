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
     * test_query_card_transaction_not_found 
     * 
     * used as initial acceptance criteria for query transaction feature
     */  
    function test_query_card_transaction_not_found() {
             
        $transactionId = 987654;
                
        $request = new QueryTransaction( Svea\SveaConfig::getDefaultConfig() );
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf( "Svea\HostedService\QueryTransactionResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }
     
    /**
     * test_manual_parsing_of_queried_payment_order_works 
     * 
     * run this manually after you've performed a card transaction and have set the
     * transaction status to success using the tools in the backoffice admin menu.
     */  
    function test_manual_parsing_of_queried_payment_order_works() {

        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'test_manual_parsing_of_queried_payment_order_works'
//        );

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
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();    

        // Example of raw card order 580964 response to parse (from QueryTransactionResponse formatXml):
        //
        //SimpleXMLElement Object
        //(
        //    [transaction] => SimpleXMLElement Object
        //        (
        //            [@attributes] => Array
        //                (
        //                    [id] => 580964
        //                )
        //
        //            [customerrefno] => test_manual_query_card_3
        //            [merchantid] => 1130
        //            [status] => SUCCESS
        //            [amount] => 25500
        //            [currency] => SEK
        //            [vat] => 600
        //            [capturedamount] => 25500
        //            [authorizedamount] => 25500
        //            [created] => 2014-04-11 15:49:30.647
        //            [creditstatus] => CREDNONE
        //            [creditedamount] => 0
        //            [merchantresponsecode] => 0
        //            [paymentmethod] => KORTCERT
        //            [callbackurl] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [capturedate] => 2014-04-13 00:15:14.267
        //            [subscriptionid] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [subscriptiontype] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [orderrows] => SimpleXMLElement Object
        //                (
        //                    [row] => Array
        //                        (
        //                            [0] => SimpleXMLElement Object
        //                                (
        //                                    [id] => 45355
        //                                    [name] => Orderrow1
        //                                    [amount] => 500
        //                                    [vat] => 100
        //                                    [description] => Orderrow description
        //                                    [quantity] => 1.0
        //                                    [sku] => 123
        //                                    [unit] => st
        //                                )
        //
        //                            [1] => SimpleXMLElement Object
        //                                (
        //                                    [id] => 45356
        //                                    [name] => Orderrow2
        //                                    [amount] => 12500
        //                                    [vat] => 2500
        //                                    [description] => Orderrow2 description
        //                                    [quantity] => 2.0
        //                                    [sku] => 124
        //                                    [unit] => m2
        //                                )
        //
        //                        )
        //
        //                )
        //
        //        )
        //
        //    [statuscode] => 0
        //)        
        
        $this->assertInstanceOf( "Svea\HostedService\QueryTransactionResponse", $response );
        
        //print_r($response);  // uncomment to dump our processed request response
        // 
        //Svea\HostedService\QueryTransactionResponse Object
        //(
        //    [transactionId] => 580964
        //    [customerrefno] => test_manual_query_card_3
        //    [clientOrderNumber] => test_manual_query_card_3
        //    [merchantid] => 1130
        //    [status] => SUCCESS
        //    [amount] => 25500
        //    [currency] => SEK
        //    [vat] => 600
        //    [capturedamount] => 25500
        //    [authorizedamount] => 25500
        //    [created] => 2014-04-11 15:49:30.647
        //    [creditstatus] => CREDNONE
        //    [creditedamount] => 0
        //    [merchantresponsecode] => 0
        //    [paymentmethod] => KORTCERT
        //    [numberedOrderRows] => Array
        //        (
        //            [0] => Svea\NumberedOrderRow Object
        //                (
        //                    [creditInvoiceId] => 
        //                    [invoiceId] => 
        //                    [rowNumber] => 1
        //                    [status] => 
        //                    [articleNumber] => 123
        //                    [quantity] => 1
        //                    [unit] => st
        //                    [amountExVat] => 4
        //                    [vatPercent] => 25
        //                    [amountIncVat] => 
        //                    [name] => Orderrow1
        //                    [description] => Orderrow description
        //                    [discountPercent] => 
        //                    [vatDiscount] => 0
        //                )
        //
        //            [1] => Svea\NumberedOrderRow Object
        //                (
        //                    [creditInvoiceId] => 
        //                    [invoiceId] => 
        //                    [rowNumber] => 2
        //                    [status] => 
        //                    [articleNumber] => 124
        //                    [quantity] => 2
        //                    [unit] => m2
        //                    [amountExVat] => 100
        //                    [vatPercent] => 25
        //                    [amountIncVat] => 
        //                    [name] => Orderrow2
        //                    [description] => Orderrow2 description
        //                    [discountPercent] => 
        //                    [vatDiscount] => 0
        //                )
        //
        //        )
        //
        //    [callbackurl] => 
        //    [capturedate] => 2014-04-13 00:15:14.267
        //    [subscriptionid] => 
        //    [subscriptiontype] => 
        //    [cardtype] => 
        //    [maskedcardno] => 
        //    [eci] => 
        //    [mdstatus] => 
        //    [expiryyear] => 
        //    [expirymonth] => 
        //    [chname] => 
        //    [authcode] => 
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)        
              
        $this->assertEquals( 1, $response->accepted );    
        $this->assertEquals( 0, $response->resultcode );
        $this->assertEquals( null, $response->errormessage );
        
        $this->assertEquals( $transactionId, $response->transactionId );
        $this->assertEquals( "test_manual_query_card_3", $response->customerrefno );
        $this->assertEquals( "test_manual_query_card_3", $response->clientOrderNumber ); //
        $this->assertEquals( "1130", $response->merchantid );
        //$this->assertEquals( "AUTHORIZED", $response->status );  // if just created
        $this->assertEquals( "SUCCESS", $response->status );    // after having been confirmed & batch process by bank
        $this->assertEquals( "25500", $response->amount );
        $this->assertEquals( "SEK", $response->currency );
        $this->assertEquals( "600", $response->vat );
        //$this->assertEquals( "", $response->capturedamount ); // if just created
        $this->assertEquals( "25500", $response->capturedamount ); // after having been confirmed & batch process by bank
        $this->assertEquals( "25500", $response->authorizedamount );
        //$this->assertEquals( "", $response->created ); // if just created
        $this->assertEquals( "2014-04-11 15:49:30.647", $response->created );
        $this->assertEquals( "CREDNONE", $response->creditstatus );
        $this->assertEquals( "0", $response->creditedamount );
        $this->assertEquals( "0", $response->merchantresponsecode );
        $this->assertEquals( "KORTCERT", $response->paymentmethod );
        
        $this->assertInstanceOf( "Svea\NumberedOrderRow", $response->numberedOrderRows[0] );
        $this->assertEquals( "123", $response->numberedOrderRows[0]->articleNumber );
        $this->assertEquals( "1", $response->numberedOrderRows[0]->quantity );
        $this->assertEquals( "st", $response->numberedOrderRows[0]->unit );
        $this->assertEquals( 4, $response->numberedOrderRows[0]->amountExVat );
        $this->assertEquals( 25, $response->numberedOrderRows[0]->vatPercent );
        $this->assertEquals( "Orderrow1", $response->numberedOrderRows[0]->name );
        $this->assertEquals( "Orderrow description", $response->numberedOrderRows[0]->description );
        $this->assertEquals( 0, $response->numberedOrderRows[0]->vatDiscount );
                        
        $this->assertInstanceOf( "Svea\OrderRow", $response->numberedOrderRows[1] );
        $this->assertEquals( "124", $response->numberedOrderRows[1]->articleNumber );
        $this->assertEquals( "2", $response->numberedOrderRows[1]->quantity );
        $this->assertEquals( "m2", $response->numberedOrderRows[1]->unit );
        $this->assertEquals( 100, $response->numberedOrderRows[1]->amountExVat );
        $this->assertEquals( 25, $response->numberedOrderRows[1]->vatPercent );
        $this->assertEquals( "Orderrow2", $response->numberedOrderRows[1]->name );
        $this->assertEquals( "Orderrow2 description", $response->numberedOrderRows[1]->description );
        $this->assertEquals( 0, $response->numberedOrderRows[1]->vatDiscount );     
                
        $this->assertEquals( "", $response->callbackurl );
        $this->assertEquals( "2014-04-13 00:15:14.267", $response->capturedate );
        $this->assertEquals( "", $response->subscriptionid );
        $this->assertEquals( "", $response->subscriptiontype );
        $this->assertEquals( "", $response->cardtype );
        $this->assertEquals( "", $response->maskedcardno );
        $this->assertEquals( "", $response->eci );
        $this->assertEquals( "", $response->mdstatus );
        $this->assertEquals( "", $response->expiryyear );
        $this->assertEquals( "", $response->expirymonth );
        $this->assertEquals( "", $response->chname );
        $this->assertEquals( "", $response->authcode );
    }    

    function test_manual_parsing_of_queried_recur_order_works() {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_parsing_of_queried_recur_order_works'
        );               

        // Set the below to match the transaction, then run the test.
        $transactionId = 581497;

        $request = new QueryTransaction( Svea\SveaConfig::getDefaultConfig() );
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();    
         
        // Example of raw recur order 581497 response (see QueryTransactionResponse class) to parse
        //        
        //SimpleXMLElement Object
        //(
        //    [transaction] => SimpleXMLElement Object
        //        (
        //            [@attributes] => Array
        //                (
        //                    [id] => 581497
        //                )
        //
        //            [customerrefno] => test_recur_1
        //            [merchantid] => 1130
        //            [status] => SUCCESS
        //            [amount] => 500
        //            [currency] => SEK
        //            [vat] => 100
        //            [capturedamount] => 500
        //            [authorizedamount] => 500
        //            [created] => 2014-04-16 14:51:34.917
        //            [creditstatus] => CREDNONE
        //            [creditedamount] => 0
        //            [merchantresponsecode] => 0
        //            [paymentmethod] => KORTCERT
        //            [callbackurl] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [capturedate] => 2014-04-18 00:15:12.287
        //            [subscriptionid] => 2922
        //            [subscriptiontype] => RECURRINGCAPTURE
        //        )
        //
        //    [statuscode] => 0
        //)               
                
        $this->assertInstanceOf( "Svea\HostedService\QueryTransactionResponse", $response );
        
        //print_r($response);
        $this->assertEquals( 1, $response->accepted );    
        $this->assertEquals( 0, $response->resultcode );        
    }     
        
     // TODO -- function test_manual_parsing_of_queried_preparepayment_order_works() {
    
    /**
     * test_manual_query_card_queryTransactionResponse 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    function test_manual_query_card_queryTransaction_returntype() {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_query_card_queryTransaction_returntype'
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
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();       
         
        $this->assertInstanceOf( "Svea\HostedService\QueryTransactionResponse", $response );
        
        //print_r($response);
        $this->assertEquals( 1, $response->accepted );    
        $this->assertInstanceOf( "Svea\NumberedOrderRow", $response->numberedOrderRows[0] );              
        $this->assertInstanceOf( "Svea\NumberedOrderRow", $response->numberedOrderRows[1] );

        $this->assertEquals( 0, $response->numberedOrderRows[1]->vatDiscount );
    } 
}
?>
