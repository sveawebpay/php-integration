<?php
// Integration tests should not need to use the namespace
namespace Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\HostedService\HostedAdminRequest\RecurTransaction;
use Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction as QueryTransaction;
use Svea\WebPay\WebPayAdmin;


/**
 * Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder\QueryTransactionIntegrationTest
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class QueryTransactionIntegrationTest extends \PHPUnit\Framework\TestCase
{

    // Svea\WebPay\WebPayAdmin::queryOrder() -----------------------------------------------
    // returned type
    /// queryOrder()
    // invoice
    // partpayment
    // card
    function test_queryOrder_queryCardOrder()
    {
        // Set the below to match the transaction, then run the test.
        $transactionId = 590177;

        $request = WebPayAdmin::queryOrder(
            ConfigurationService::getSingleCountryConfig(
                "SE",
                "foo", "bar", "123456", // invoice
                "foo", "bar", "123456", // paymentplan
                "foo", "bar", "123456", // accountplan
                "1200", // merchantid, secret
                "27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d",
                false // prod = false
            )
        )
            ->setTransactionId(strval($transactionId))
            ->setCountryCode("SE");
        $response = $request->queryCardOrder()->doRequest();
//        echo "foo: ";
//        var_dump($response); die;

        $this->assertEquals(1, $response->accepted);

        $this->assertEquals($transactionId, $response->transactionId);
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[0]);
        $this->assertEquals("Soft213s", $response->numberedOrderRows[0]->articleNumber);
        $this->assertEquals("1.0", $response->numberedOrderRows[0]->quantity);
        $this->assertEquals("st", $response->numberedOrderRows[0]->unit);
        $this->assertEquals(3212.00, $response->numberedOrderRows[0]->amountExVat);   // amount = 401500, vat = 80300 => 3212.00 @25%
        $this->assertEquals(25, $response->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("Soft", $response->numberedOrderRows[0]->name);
//        $this->assertEquals( "Specification", $response->numberedOrderRows[1]->description );
        $this->assertEquals(0, $response->numberedOrderRows[0]->vatDiscount);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[1]);
        $this->assertEquals("07", $response->numberedOrderRows[1]->articleNumber);
        $this->assertEquals("1.0", $response->numberedOrderRows[1]->quantity);
        $this->assertEquals("st", $response->numberedOrderRows[1]->unit);
        $this->assertEquals(0, $response->numberedOrderRows[1]->amountExVat);   // amount = 401500, vat = 80300 => 3212.00 @25%
        $this->assertEquals(0, $response->numberedOrderRows[1]->vatPercent);
        $this->assertEquals("Sits: Hatfield Beige 6", $response->numberedOrderRows[1]->name);
//        $this->assertEquals( "Specification", $response->numberedOrderRows[1]->description );
        $this->assertEquals(0, $response->numberedOrderRows[1]->vatDiscount);
    }

    /**
     * test_query_card_transaction_not_found
     *
     * used as initial acceptance criteria for query transaction feature
     */
    function test_query_card_transaction_not_found()
    {

        $transactionId = 987654;

        $request = new QueryTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse", $response);

        // if we receive an error from the service, the integration test passes
        $this->assertEquals(0, $response->accepted);
        $this->assertEquals("128 (NO_SUCH_TRANS)", $response->resultcode);
    }

    /**
     * test_manual_parsing_of_queried_payment_order_works
     *
     * run this manually after you've performed a card transaction and have set the
     * transaction status to success using the tools in the backoffice admin menu.
     */
    function test_manual_parsing_of_queried_payment_order_works()
    {
        $this->markTestSkipped('deprecated.');
        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'test_manual_parsing_of_queried_payment_order_works'
//        );

        // 1. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>KORTCERT</paymentmethod><currency>SEK</currency><amount>25500</amount><vat>600</vat><customerrefno>test_manual_query_card_2xz</customerrefno><returnurl>https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/merchantresponsetest.xhtml</returnurl><orderrows><row><name>Orderrow1</name><amount>500</amount><vat>100</vat><description>Orderrow description</description><quantity>1</quantity><sku>123</sku><unit>st</unit></row><row><name>Orderrow2</name><amount>12500</amount><vat>2500</vat><description>Orderrow2 description</description><quantity>2</quantity><sku>124</sku><unit>m2</unit></row></orderrows>
        // 5. the result should be:
        // <response><transaction id="587401"><paymentmethod>KORTCERT</paymentmethod><merchantid>1130</merchantid><customerrefno>test_manual_query_card_2xz</customerrefno><amount>25500</amount><currency>SEK</currency><cardtype>VISA</cardtype><maskedcardno>444433xxxxxx1100</maskedcardno><expirymonth>02</expirymonth><expiryyear>15</expiryyear><authcode>878087</authcode></transaction><statuscode>0</statuscode></response>

        // 6. enter the received transaction id below and run the test

        // Set the below to match the transaction, then run the test.
        $transactionId = 587401;

        $request = new QueryTransaction(ConfigurationService::getDefaultConfig());
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

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse", $response);

        ////print_r($response);  // uncomment to dump our processed request response:
        //Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse Object
        //(
        //    [transactionId] => 587401
        //    [clientOrderNumber] => test_manual_query_card_2xz
        //    [merchantId] => 1130
        //    [status] => AUTHORIZED
        //    [amount] => 25500
        //    [currency] => SEK
        //    [vat] => 600
        //    [capturedamount] => 
        //    [authorizedamount] => 25500
        //    [created] => 2014-10-06 15:35:55.327
        //    [creditstatus] => CREDNONE
        //    [creditedamount] => 0
        //    [merchantresponsecode] => 0
        //    [paymentMethod] => KORTCERT
        //    [numberedOrderRows] => Array
        //        (
        //            [0] => Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow Object
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
        //            [1] => Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow Object
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
        //    [capturedate] => 
        //    [subscriptionId] => 
        //    [subscriptiontype] => 
        //    [cardType] => 
        //    [maskedCardNumber] => 
        //    [eci] => 
        //    [mdstatus] => 
        //    [expiryYear] => 
        //    [expiryMonth] => 
        //    [chname] => 
        //    [authCode] => 
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)       

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(0, $response->resultcode);
        $this->assertEquals(null, $response->errormessage);

        $this->assertEquals($transactionId, $response->transactionId);
        $this->assertEquals("test_manual_query_card_2xz", $response->clientOrderNumber); //
        $this->assertEquals("1130", $response->merchantId);
        //$this->assertEquals( "AUTHORIZED", $response->status );  // if just created
        $this->assertEquals("SUCCESS", $response->status);    // after having been confirmed & batch process by bank
        $this->assertEquals("25500", $response->amount);
        $this->assertEquals("SEK", $response->currency);
        $this->assertEquals("600", $response->vat);
        //$this->assertEquals( "", $response->capturedamount ); // if just created
        $this->assertEquals("25500", $response->capturedamount); // after having been confirmed & batch process by bank
        $this->assertEquals("25500", $response->authorizedamount);
        $this->assertEquals("2014-10-06 15:35:55.327", $response->created);
        $this->assertEquals("CREDNONE", $response->creditstatus);
        $this->assertEquals("0", $response->creditedamount);
        $this->assertEquals("0", $response->merchantresponsecode);
        $this->assertEquals("KORTCERT", $response->paymentMethod);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[0]);
        $this->assertEquals("123", $response->numberedOrderRows[0]->articleNumber);
        $this->assertEquals("1", $response->numberedOrderRows[0]->quantity);
        $this->assertEquals("st", $response->numberedOrderRows[0]->unit);
        $this->assertEquals(4, $response->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25, $response->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("Orderrow1", $response->numberedOrderRows[0]->name);
        $this->assertEquals("Orderrow description", $response->numberedOrderRows[0]->description);
        $this->assertEquals(0, $response->numberedOrderRows[0]->vatDiscount);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\OrderRow", $response->numberedOrderRows[1]);
        $this->assertEquals("124", $response->numberedOrderRows[1]->articleNumber);
        $this->assertEquals("2", $response->numberedOrderRows[1]->quantity);
        $this->assertEquals("m2", $response->numberedOrderRows[1]->unit);
        $this->assertEquals(100, $response->numberedOrderRows[1]->amountExVat);
        $this->assertEquals(25, $response->numberedOrderRows[1]->vatPercent);
        $this->assertEquals("Orderrow2", $response->numberedOrderRows[1]->name);
        $this->assertEquals("Orderrow2 description", $response->numberedOrderRows[1]->description);
        $this->assertEquals(0, $response->numberedOrderRows[1]->vatDiscount);

        $this->assertEquals(null, $response->callbackurl);
        //$this->assertEquals( null, $response->capturedate ); // if just created
        $this->assertEquals("2014-10-07 00:15:17.857", $response->capturedate); // after having been confirmed & batch process by bank
        $this->assertEquals(null, $response->subscriptionId);
        $this->assertEquals(null, $response->subscriptiontype);
        $this->assertEquals(null, $response->cardType);
        $this->assertEquals(null, $response->maskedCardNumber);
        $this->assertEquals(null, $response->eci);
        $this->assertEquals(null, $response->mdstatus);
        $this->assertEquals(null, $response->expiryYear);
        $this->assertEquals(null, $response->expiryMonth);
        $this->assertEquals(null, $response->chname);
        $this->assertEquals(null, $response->authCode);
    }

    function test_manual_parsing_of_queried_recur_order_without_orderrows_works()
    {
        $this->markTestSkipped('deprecated');
        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'test_manual_parsing_of_queried_recur_order_without_orderrows_works'
//        );               

        // 1. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>KORTCERT</paymentmethod><subscriptiontype>RECURRINGCAPTURE</subscriptiontype><currency>SEK</currency><amount>500</amount><vat>100</vat><customerrefno>test_recur_NN</customerrefno><returnurl>https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/merchantresponsetest.xhtml</returnurl>
        // 5. the result should be:
        // <response><transaction id="581497"><paymentmethod>KORTCERT</paymentmethod><merchantid>1130</merchantid><customerrefno>test_recur_1</customerrefno><amount>500</amount><currency>SEK</currency><subscriptionid>2922</subscriptionid><cardtype>VISA</cardtype><maskedcardno>444433xxxxxx1100</maskedcardno><expirymonth>02</expirymonth><expiryyear>16</expiryyear><authcode>993955</authcode></transaction><statuscode>0</statuscode></response>

        // 6. enter the received subscription id, etc. below and run the test

        // Set the below to match the transaction, then run the test.
        $transactionId = 581497;

        $request = new QueryTransaction(ConfigurationService::getDefaultConfig());
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

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse", $response);

        ////print_r($response);  // uncomment to dump our processed request response:
        //Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse Object
        //(
        //    [transactionId] => 581497
        //    [clientOrderNumber] => test_recur_1
        //    [merchantId] => 1130
        //    [status] => SUCCESS
        //    [amount] => 500
        //    [currency] => SEK
        //    [vat] => 100
        //    [capturedamount] => 500
        //    [authorizedamount] => 500
        //    [created] => 2014-04-16 14:51:34.917
        //    [creditstatus] => CREDNONE
        //    [creditedamount] => 0
        //    [merchantresponsecode] => 0
        //    [paymentMethod] => KORTCERT
        //    [numberedOrderRows] => 
        //    [callbackurl] => 
        //    [capturedate] => 2014-04-18 00:15:12.287
        //    [subscriptionId] => 2922
        //    [subscriptiontype] => RECURRINGCAPTURE
        //    [cardType] => 
        //    [maskedCardNumber] => 
        //    [eci] => 
        //    [mdstatus] => 
        //    [expiryYear] => 
        //    [expiryMonth] => 
        //    [chname] => 
        //    [authCode] => 
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(0, $response->resultcode);
        $this->assertEquals(null, $response->errormessage);

        $this->assertEquals($transactionId, $response->transactionId);
        $this->assertEquals("test_recur_1", $response->clientOrderNumber); //
        $this->assertEquals("1130", $response->merchantId);
        $this->assertEquals("SUCCESS", $response->status);    // after having been confirmed & batch process by bank
        $this->assertEquals("500", $response->amount);
        $this->assertEquals("SEK", $response->currency);
        $this->assertEquals("100", $response->vat);
        $this->assertEquals("500", $response->capturedamount); // after having been confirmed & batch process by bank
        $this->assertEquals("500", $response->authorizedamount);
        $this->assertEquals("2014-04-16 14:51:34.917", $response->created);
        $this->assertEquals("CREDNONE", $response->creditstatus);
        $this->assertEquals("0", $response->creditedamount);
        $this->assertEquals("0", $response->merchantresponsecode);
        $this->assertEquals("KORTCERT", $response->paymentMethod);
        $this->assertEquals(null, $response->numberedOrderRows);
        $this->assertEquals(null, $response->callbackurl);
        $this->assertEquals("2014-04-18 00:15:12.287", $response->capturedate);
        $this->assertEquals("2922", $response->subscriptionId);
        $this->assertEquals("RECURRINGCAPTURE", $response->subscriptiontype);
        $this->assertEquals(null, $response->cardType);
        $this->assertEquals(null, $response->maskedCardNumber);
        $this->assertEquals(null, $response->eci);
        $this->assertEquals(null, $response->mdstatus);
        $this->assertEquals(null, $response->expiryYear);
        $this->assertEquals(null, $response->expiryMonth);
        $this->assertEquals(null, $response->chname);
        $this->assertEquals(null, $response->authCode);
    }

    // TODO not updated
    function test_manual_parsing_of_queried_preparepayment_order_works()
    {
        $this->markTestSkipped('deprecated');
        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'test_manual_parsing_of_queried_recur_order_without_orderrows_works'
//        );               

        // 1. See Svea\WebPay\Test\IntegrationTest\HostedService\Payment\CardPaymentURLIntegrationTest::test_manual_CardPayment_getPaymentUrl():
        // 2. run the test, and get the subscription paymenturl from the output
        // 3. go to the paymenturl and complete the transaction.
        // 4. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 5. retrieve the transactionid from the response in the transaction log

        // Set the below to match the transaction, then run the test.
        $transactionId = 586076;

        $request = new QueryTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        // Example of raw recur order 586076 response (see QueryTransactionResponse class) to parse
        //     
        //SimpleXMLElement Object
        //(
        //    [transaction] => SimpleXMLElement Object
        //        (
        //            [@attributes] => Array
        //                (
        //                    [id] => 586076
        //                )
        //
        //            [customerrefno] => clientOrderNumber:2014-09-10T14:27:23 02:00
        //            [merchantid] => 1130
        //            [status] => AUTHORIZED
        //            [amount] => 25000
        //            [currency] => SEK
        //            [vat] => 5000
        //            [capturedamount] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [authorizedamount] => 25000
        //            [created] => 2014-09-10 14:27:23.04
        //            [creditstatus] => CREDNONE
        //            [creditedamount] => 0
        //            [merchantresponsecode] => 0
        //            [paymentmethod] => KORTCERT
        //            [callbackurl] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [capturedate] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [subscriptionid] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [subscriptiontype] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [customer] => SimpleXMLElement Object
        //                (
        //                    [@attributes] => Array
        //                        (
        //                            [id] => 12536
        //                        )
        //
        //                    [firstname] => Tess T
        //                    [lastname] => Persson
        //                    [initials] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [email] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [ssn] => 194605092222
        //                    [address] => Testgatan
        //                    [address2] => c/o Eriksson, Erik
        //                    [city] => Stan
        //                    [country] => SE
        //                    [zip] => 99999
        //                    [phone] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [vatnumber] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [housenumber] => 1
        //                    [companyname] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [fullname] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                )
        //
        //            [orderrows] => SimpleXMLElement Object
        //                (
        //                    [row] => SimpleXMLElement Object
        //                        (
        //                            [id] => 53730
        //                            [name] => Product
        //                            [amount] => 12500
        //                            [vat] => 2500
        //                            [description] => Specification
        //                            [quantity] => 2.0
        //                            [sku] => 1
        //                            [unit] => st
        //                        )
        //
        //                )
        //
        //        )
        //
        //    [statuscode] => 0
        //)

        $this->assertEquals(1, $response->accepted);
        $this->assertEquals(0, $response->resultcode);
        $this->assertEquals(null, $response->errormessage);

        $this->assertEquals($transactionId, $response->transactionId);
        $this->assertEquals("clientOrderNumber:2014-09-10T14:27:23 02:00", $response->clientOrderNumber); //
        $this->assertEquals("1130", $response->merchantId);
        //$this->assertEquals( "AUTHORIZED", $response->status ); // if just created
        $this->assertEquals("SUCCESS", $response->status);
        $this->assertEquals("25000", $response->amount);
        $this->assertEquals("SEK", $response->currency);
        $this->assertEquals("5000", $response->vat);
        //$this->assertEquals( null, $response->capturedamount ); // if just created
        $this->assertEquals("25000", $response->capturedamount);
        $this->assertEquals("25000", $response->authorizedamount);
        $this->assertEquals("2014-09-10 14:27:23.04", $response->created);
        $this->assertEquals("CREDNONE", $response->creditstatus);
        $this->assertEquals("0", $response->creditedamount);
        $this->assertEquals("0", $response->merchantresponsecode);
        $this->assertEquals("KORTCERT", $response->paymentMethod);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\OrderRow", $response->numberedOrderRows[0]);
        $this->assertEquals("1", $response->numberedOrderRows[0]->articleNumber);
        $this->assertEquals("2.0", $response->numberedOrderRows[0]->quantity);
        $this->assertEquals("st", $response->numberedOrderRows[0]->unit);
        $this->assertEquals(100.00, $response->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25.00, $response->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("Product", $response->numberedOrderRows[0]->name);
        $this->assertEquals("Specification", $response->numberedOrderRows[0]->description);
        $this->assertEquals(0, $response->numberedOrderRows[0]->vatDiscount);

        $this->assertEquals(null, $response->callbackurl);
        //$this->assertEquals( null, $response->capturedate ); // if just created
        $this->assertEquals("2014-09-11 00:15:11.313", $response->capturedate);
        $this->assertEquals(null, $response->subscriptionId);
        $this->assertEquals(null, $response->subscriptiontype);
        $this->assertEquals(null, $response->cardType);
        $this->assertEquals(null, $response->maskedCardNumber);
        $this->assertEquals(null, $response->eci);
        $this->assertEquals(null, $response->mdstatus);
        $this->assertEquals(null, $response->expiryYear);
        $this->assertEquals(null, $response->expiryMonth);
        $this->assertEquals(null, $response->chname);
        $this->assertEquals(null, $response->authCode);
    }

    /**
     * test_manual_query_card_queryTransactionResponse
     *
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */
    function test_manual_query_card_queryTransaction_returntype()
    {

        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'test_manual_query_card_queryTransaction_returntype'
//        );

        // 1. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>KORTCERT</paymentmethod><currency>SEK</currency><amount>25500</amount><vat>600</vat><customerrefno>test_manual_query_card_2</customerrefno><returnurl>https://webpaypaymentgatewaystage.svea.com/webpay/admin/merchantresponsetest.xhtml</returnurl><orderrows><row><name>Orderrow1</name><amount>500</amount><vat>100</vat><description>Orderrow description</description><quantity>1</quantity><sku>123</sku><unit>st</unit></row><row><name>Orderrow2</name><amount>12500</amount><vat>2500</vat><description>Orderrow2 description</description><quantity>2</quantity><sku>124</sku><unit>m2</unit></row></orderrows>
        // 5. the result should be:
        // <response><transaction id="580964"><paymentmethod>KORTCERT</paymentmethod><merchantid>1130</merchantid><customerrefno>test_manual_query_card_3</customerrefno><amount>25500</amount><currency>SEK</currency><cardtype>VISA</cardtype><maskedcardno>444433xxxxxx1100</maskedcardno><expirymonth>02</expirymonth><expiryyear>15</expiryyear><authcode>898924</authcode></transaction><statuscode>0</statuscode></response>

        // 6. enter the received transaction id below and run the test

        // Set the below to match the transaction, then run the test.
        $transactionId = 582690;

        $request = new QueryTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse", $response);

        ////print_r($response);
        $this->assertEquals(1, $response->accepted);
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[0]);
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[1]);

        $this->assertEquals(0, $response->numberedOrderRows[1]->vatDiscount);
    }

    function test_manual_query_recur_card_initial_transaction()
    {

        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'test_manual_query_recur_card_initial_transaction'
//        );

        // Set the below to match the transaction, then run the test.
        $transactionId = 586159;

        // Example of raw recur order 586159 response (see QueryTransactionResponse class) to parse
        //    
        //SimpleXMLElement Object
        //(
        //    [transaction] => SimpleXMLElement Object
        //        (
        //            [@attributes] => Array
        //                (
        //                    [id] => 586159
        //                )
        //
        //            [customerrefno] => clientOrderNumber:2014-09-12T11:02:50 02:00
        //            [merchantid] => 1130
        //            [status] => AUTHORIZED
        //            [amount] => 25000
        //            [currency] => SEK
        //            [vat] => 5000
        //            [capturedamount] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [authorizedamount] => 25000
        //            [created] => 2014-09-12 11:04:24.347
        //            [creditstatus] => CREDNONE
        //            [creditedamount] => 0
        //            [merchantresponsecode] => 0
        //            [paymentmethod] => KORTCERT
        //            [callbackurl] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [capturedate] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [subscriptionid] => 3050
        //            [subscriptiontype] => RECURRINGCAPTURE
        //            [customer] => SimpleXMLElement Object
        //                (
        //                    [@attributes] => Array
        //                        (
        //                            [id] => 12615
        //                        )
        //
        //                    [firstname] => Tess T
        //                    [lastname] => Persson
        //                    [initials] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [email] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [ssn] => 194605092222
        //                    [address] => Testgatan
        //                    [address2] => c/o Eriksson, Erik
        //                    [city] => Stan
        //                    [country] => SE
        //                    [zip] => 99999
        //                    [phone] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [vatnumber] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [housenumber] => 1
        //                    [companyname] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                    [fullname] => SimpleXMLElement Object
        //                        (
        //                        )
        //
        //                )
        //
        //            [orderrows] => SimpleXMLElement Object
        //                (
        //                    [row] => SimpleXMLElement Object
        //                        (
        //                            [id] => 53878
        //                            [name] => Product
        //                            [amount] => 12500
        //                            [vat] => 2500
        //                            [description] => Specification
        //                            [quantity] => 2.0
        //                            [sku] => 1
        //                            [unit] => st
        //                        )
        //
        //                )
        //
        //        )
        //
        //    [statuscode] => 0
        //)

        $request = new QueryTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse", $response);

        ////print_r($response);

        $this->assertEquals(1, $response->accepted);

        $this->assertEquals(3050, $response->subscriptionId);
        $this->assertEquals("RECURRINGCAPTURE", $response->subscriptiontype);
    }


    function test_manual_query_recur_card_recur_transaction()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_query_recur_card_initial_transaction'
        );

        // 1. enter the below values from the transaction log from test_manual_recurring_payment_step_1
        // 2. run the test and check the output for the subscriptionId and transactionid of the recur request

        // Set the below to match the original transaction, then run the test.
        $paymentmethod = "KORTCERT";
        $merchantid = 1130;
        $currency = "SEK";
        $cardType = "VISA";
        $maskedCardNumber = "444433xxxxxx1100";
        $expiryMonth = 01;
        $expiryYear = 15;
        $subscriptionId = 3050; // insert 

        // the below applies to the recur request, and may differ from the original transaction
        $new_amount = "2500"; // in minor currency  
        $new_customerrefno = "test_manual_recurring_payment_step_1 " . date('c');

        // below is actual test, shouldn't need to change it
        $request = new RecurTransaction(ConfigurationService::getDefaultConfig());
        $request->countryCode = "SE";
        $request->subscriptionId = $subscriptionId;
        $request->currency = $currency;
        $request->customerRefNo = $new_customerrefno;
        $request->amount = $new_amount;
        $response = $request->doRequest();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);

        ////print_r("Recur card transaction response: "); //print_r( $response );        
        //
        //Recur card transaction response: Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\RecurTransactionResponse Object
        //(
        //    [transactionid] => 586165
        //    [customerrefno] => test_manual_recurring_payment_step_1 2014-09-12T11:38:30+02:00
        //    [paymentmethod] => CARD
        //    [merchantid] => 1130
        //    [amount] => 2500
        //    [currency] => SEK
        //    [cardType] => VISA
        //    [maskedCardNumber] => 444433xxxxxx1100
        //    [expiryMonth] => 01
        //    [expiryYear] => 15
        //    [authCode] => 153124
        //    [subscriptionid] => 3050
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)        

        // Set the below to match the transaction, then run the test.
        $transactionId = $response->transactionId;

        //SimpleXMLElement Object
        //(
        //    [transaction] => SimpleXMLElement Object
        //        (
        //            [@attributes] => Array
        //                (
        //                    [id] => 586165
        //                )
        //
        //            [customerrefno] => test_manual_recurring_payment_step_1 2014-09-12T11:38:30+02:00
        //            [merchantid] => 1130
        //            [status] => AUTHORIZED
        //            [amount] => 2500
        //            [currency] => SEK
        //            [vat] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [capturedamount] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [authorizedamount] => 2500
        //            [created] => 2014-09-12 11:38:32.557
        //            [creditstatus] => CREDNONE
        //            [creditedamount] => 0
        //            [merchantresponsecode] => 0
        //            [paymentmethod] => CARD
        //            [callbackurl] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [capturedate] => SimpleXMLElement Object
        //                (
        //                )
        //
        //            [subscriptionid] => 3050
        //            [subscriptiontype] => RECURRINGCAPTURE
        //        )
        //
        //    [statuscode] => 0
        //)

        $request = new QueryTransaction(ConfigurationService::getDefaultConfig());
        $request->transactionId = $transactionId;
        $request->countryCode = "SE";
        $queryResponse = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse", $queryResponse);

        ////print_r($queryResponse);        
        //
        //Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse Object
        //(
        //    [transactionId] => 587424
        //    [clientOrderNumber] => test_manual_recurring_payment_step_1 2014-10-06T15:53:45+02:00
        //    [merchantId] => 1130
        //    [status] => AUTHORIZED
        //    [amount] => 2500
        //    [currency] => SEK
        //    [vat] => 
        //    [capturedamount] => 
        //    [authorizedamount] => 2500
        //    [created] => 2014-10-06 15:53:45.367
        //    [creditstatus] => CREDNONE
        //    [creditedamount] => 0
        //    [merchantresponsecode] => 0
        //    [paymentMethod] => CARD
        //    [numberedOrderRows] => 
        //    [callbackurl] => 
        //    [capturedate] => 
        //    [subscriptionId] => 3050
        //    [subscriptiontype] => RECURRINGCAPTURE
        //    [cardType] => 
        //    [maskedCardNumber] => 
        //    [eci] => 
        //    [mdstatus] => 
        //    [expiryYear] => 
        //    [expiryMonth] => 
        //    [chname] => 
        //    [authCode] => 
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)

        $this->assertEquals(1, $response->accepted);
    }
}

?>
