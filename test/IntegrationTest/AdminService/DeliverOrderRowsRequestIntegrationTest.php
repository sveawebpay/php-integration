<?php

namespace Svea\WebPay\Test\IntegrationTest\AdminService;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\HostedService\HostedAdminRequest\ConfirmTransaction;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrderRowsRequestIntegrationTest extends \PHPUnit\Framework\TestCase
{

    public function test_deliver_single_invoice_orderRow_returns_accepted_with_invoiceId()
    {

        // create order
        $country = "SE";

        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("second row")
                ->setQuantity(1)
                ->setAmountExVat(16.00)
                ->setVatPercent(25)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("third row")
                ->setQuantity(1)
                ->setAmountExVat(24.00)
                ->setVatPercent(25)
            );

        $orderResponse = $order->useInvoicePayment()->doRequest();
        ////print_r( $orderResponse );                   

        $myOrderId = $orderResponse->sveaOrderId;

        // deliver first row in order
        $deliverOrderRowsRequest = WebPayAdmin::deliverOrderRows(ConfigurationService::getDefaultConfig());
        $deliverOrderRowsRequest->setCountryCode($country);
        $deliverOrderRowsRequest->setOrderId($myOrderId);
        $deliverOrderRowsRequest->setInvoiceDistributionType(DistributionType::POST);
        $deliverOrderRowsRequest->setRowToDeliver(1);
        $deliverOrderRowsResponse = $deliverOrderRowsRequest->deliverInvoiceOrderRows()->doRequest();

        // Example DeliverPartial raw request response to parse:
        // 
        //stdClass Object
        //(
        //    [ErrorMessage] => 
        //    [ResultCode] => 0
        //    [OrdersDelivered] => stdClass Object
        //        (
        //            [DeliverOrderResult] => stdClass Object
        //                (
        //                    [ClientId] => 79021
        //                    [DeliveredAmount] => 250.00
        //                    [DeliveryReferenceNumber] => 1033899
        //                    [OrderType] => Invoice
        //                    [SveaOrderId] => 414180
        //                )
        //
        //        )
        //
        //)        

        ////print_r( $deliverOrderRowsResponse );   
        //
        // Example DeliverPartialResponse
        //
        //Svea\WebPay\AdminService\AdminServiceResponse\DeliverPartialResponse Object
        //(
        //    [clientId] => 79021
        //    [amount] => 250.00
        //    [invoiceId] => 1033902
        //    [contractNumber] => 
        //    [orderType] => Invoice
        //    [orderId] => 414183
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)        

        $this->assertInstanceOf("Svea\WebPay\AdminService\AdminServiceResponse\DeliverPartialResponse", $deliverOrderRowsResponse);
        $this->assertEquals(1, $deliverOrderRowsResponse->accepted);
        $this->assertEquals(0, $deliverOrderRowsResponse->resultcode);
        $this->assertEquals(null, $deliverOrderRowsResponse->errormessage);

        $this->assertEquals(79021, $deliverOrderRowsResponse->clientId);
        $this->assertEquals(250.00, $deliverOrderRowsResponse->amount);
        $this->assertStringMatchesFormat("%d", $deliverOrderRowsResponse->invoiceId);   // %d => an unsigned integer value
        $this->assertEquals(null, $deliverOrderRowsResponse->contractNumber);
        $this->assertEquals("Invoice", $deliverOrderRowsResponse->orderType);
        $this->assertStringMatchesFormat("%d", $deliverOrderRowsResponse->orderId);   // %d => an unsigned integer value        
    }

    public function test_deliver_multiple_invoice_orderRows_returns_accepted_with_invoiceId()
    {

        // create order
        $country = "SE";

        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer($country))
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("second row")
                ->setQuantity(1)
                ->setAmountExVat(16.00)
                ->setVatPercent(25)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setDescription("third row")
                ->setQuantity(1)
                ->setAmountExVat(24.00)
                ->setVatPercent(25)
            );

        $orderResponse = $order->useInvoicePayment()->doRequest();
        ////print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);

        $myOrderId = $orderResponse->sveaOrderId;

        // deliver first row in order
        $deliverOrderRowsRequest = WebPayAdmin::deliverOrderRows(ConfigurationService::getDefaultConfig());
        $deliverOrderRowsRequest->setCountryCode($country);
        $deliverOrderRowsRequest->setOrderId($myOrderId);
        $deliverOrderRowsRequest->setInvoiceDistributionType(DistributionType::POST);
        $deliverOrderRowsRequest->setRowsToDeliver(array(1, 2));
        $deliverOrderRowsResponse = $deliverOrderRowsRequest->deliverInvoiceOrderRows()->doRequest();

        ////print_r( $deliverOrderRowsResponse );        
        $this->assertInstanceOf('Svea\WebPay\AdminService\AdminServiceResponse\DeliverPartialResponse', $deliverOrderRowsResponse);
        $this->assertEquals(true, $deliverOrderRowsResponse->accepted);    // truth
        $this->assertEquals(1, $deliverOrderRowsResponse->accepted);       // equals literal 1
        $this->assertEquals(0, $deliverOrderRowsResponse->resultcode);
        $this->assertEquals(270.00, $deliverOrderRowsResponse->amount);
        $this->assertEquals("Invoice", $deliverOrderRowsResponse->orderType);
        $this->assertNotNull($deliverOrderRowsResponse->invoiceId);
    }

    public function test_manual_deliver_single_card_orderRow_of_authorized_order_performs_loweramount_and_sets_status_confirmed()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_query_card_queryTransaction_returntype'
        );

        // 1. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        //<paymentmethod>KORTCERT</paymentmethod><currency>EUR</currency><amount>600</amount><vat>120</vat><customerrefno>test_14105300920232</customerrefno><returnurl>https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/merchantresponsetest.xhtml</returnurl><orderrows><row><name>A</name><amount>100</amount><vat>20</vat><description>rowA</description><quantity>1</quantity><sku>665</sku><unit>st</unit></row><row><name>B</name><amount>200</amount><vat>40</vat><description>rowB</description><quantity>1</quantity><sku>666</sku><unit>st</unit></row><row><name>C</name><amount>300</amount><vat>60</vat><description>rowA</description><quantity>1</quantity><sku>667</sku><unit>st</unit></row></orderrows>
        // 5. the result should be:
        //<response>
        //  <transaction id="586209">
        //    <paymentmethod>KORTCERT</paymentmethod>
        //    <merchantid>1130</merchantid>
        //    <customerrefno>test_1410530092038</customerrefno>
        //    <amount>600</amount>
        //    <currency>EUR</currency>
        //    <cardtype>VISA</cardtype>
        //    <maskedcardno>444433xxxxxx1100</maskedcardno>
        //    <expirymonth>01</expirymonth>
        //    <expiryyear>15</expiryyear>
        //    <authcode>763907</authcode>
        //  </transaction>
        //  <statuscode>0</statuscode>
        //</response>
        // 6. enter the received transaction id below and run the test

        // Set the below to match the transaction, then run the test.
        $transactionId = 586223;

        $queryRequest = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig());
        $queryResponse = $queryRequest->setCountryCode("SE")->setTransactionId($transactionId)->queryCardOrder()->doRequest();

        ////print_r( $queryResponse );                   
        $this->assertEquals(1, $queryResponse->accepted);
        $this->assertEquals("AUTHORIZED", $queryResponse->status);
        $this->assertEquals(600, $queryResponse->amount);
        $this->assertEquals(600, $queryResponse->authorizedamount); // not manipulated post creation
        $this->assertEquals(0, $queryResponse->creditedamount); // not manipulated post creation        

        $deliverRequest = WebPayAdmin::deliverOrderRows(ConfigurationService::getDefaultConfig());
        $deliverRequest->setCountryCode("SE")->setOrderId($transactionId);
        $deliverRequest->setRowToDeliver(1)->addNumberedOrderRows($queryResponse->numberedOrderRows);
        $deliverResponse = $deliverRequest->deliverCardOrderRows()->doRequest();

        //print_r( $deliverResponse );
        $this->assertEquals(1, $deliverResponse->accepted);
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse", $deliverResponse);

        // query orderrows
        $queryResponse2 = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($transactionId)->setCountryCode("SE")->queryCardOrder()->doRequest();

        //print_r( $queryResponse2); 
        $this->assertEquals(1, $queryResponse2->accepted);
        $this->assertEquals("CONFIRMED", $queryResponse2->status);
        $this->assertEquals(100, $queryResponse2->authorizedamount);

    }

    public function test_manual_deliver_single_card_orderRow_of_manipulated_order_returns_error()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_deliver_single_card_orderRow_of_manipulated_order_returns_error'
        );

        // 1. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        //<paymentmethod>KORTCERT</paymentmethod><currency>EUR</currency><amount>600</amount><vat>120</vat><customerrefno>test_14105300920232</customerrefno><returnurl>https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/merchantresponsetest.xhtml</returnurl><orderrows><row><name>A</name><amount>100</amount><vat>20</vat><description>rowA</description><quantity>1</quantity><sku>665</sku><unit>st</unit></row><row><name>B</name><amount>200</amount><vat>40</vat><description>rowB</description><quantity>1</quantity><sku>666</sku><unit>st</unit></row><row><name>C</name><amount>300</amount><vat>60</vat><description>rowA</description><quantity>1</quantity><sku>667</sku><unit>st</unit></row></orderrows>
        // 5. the result should be:
        //<response>
        //  <transaction id="586209">
        //    <paymentmethod>KORTCERT</paymentmethod>
        //    <merchantid>1130</merchantid>
        //    <customerrefno>test_1410530092038</customerrefno>
        //    <amount>600</amount>
        //    <currency>EUR</currency>
        //    <cardtype>VISA</cardtype>
        //    <maskedcardno>444433xxxxxx1100</maskedcardno>
        //    <expirymonth>01</expirymonth>
        //    <expiryyear>15</expiryyear>
        //    <authcode>763907</authcode>
        //  </transaction>
        //  <statuscode>0</statuscode>
        //</response>
        // 6. enter the received transaction id below and run the test

        // Set the below to match the transaction, then run the test.
        $transactionId = 586256;

        $queryRequest = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig());
        $queryResponse = $queryRequest->setCountryCode("SE")->setTransactionId($transactionId)->queryCardOrder()->doRequest();

        ////print_r( $queryResponse );                   
        $this->assertEquals(1, $queryResponse->accepted);

        $deliverRequest = WebPayAdmin::deliverOrderRows(ConfigurationService::getDefaultConfig());
        $deliverRequest->setCountryCode("SE")->setOrderId($transactionId);
        $deliverRequest->setRowToDeliver(1)->addNumberedOrderRows($queryResponse->numberedOrderRows);
        $deliverResponse = $deliverRequest->deliverCardOrderRows()->doRequest();

        ////print_r( $deliverResponse );
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse", $deliverResponse);
        $this->assertEquals(1, $deliverResponse->accepted);

        // again (i.e. lower too large an amount)
        $deliver2Request = WebPayAdmin::deliverOrderRows(ConfigurationService::getDefaultConfig());
        $deliver2Request->setCountryCode("SE")->setOrderId($transactionId);
        $deliver2Request->setRowToDeliver(1)->addNumberedOrderRows($queryResponse->numberedOrderRows);
        $deliver2Response = $deliver2Request->deliverCardOrderRows()->doRequest();

        ////print_r( $deliver2Response );
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse", $deliver2Response);
        $this->assertEquals(0, $deliver2Response->accepted);
        $this->assertEquals("100", $deliver2Response->resultcode);
        $this->assertEquals(
            "IntegrationPackage: LowerAmount request with flag alsoDoConfirm failed:305 (BAD_AMOUNT) Invalid value for amount.",
            $deliver2Response->errormessage
        );

    }

    public function test_manual_deliver_single_card_orderRow_of_confirmed_order_performs_loweramount_and_returns_ConfirmTransactionError()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_deliver_single_card_orderRow_of_confirmed_order_performs_loweramount_and_returns_ConfirmTransactionError'
        );

        // 1. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        //<paymentmethod>KORTCERT</paymentmethod><currency>EUR</currency><amount>600</amount><vat>120</vat><customerrefno>test_14105300920232</customerrefno><returnurl>https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/merchantresponsetest.xhtml</returnurl><orderrows><row><name>A</name><amount>100</amount><vat>20</vat><description>rowA</description><quantity>1</quantity><sku>665</sku><unit>st</unit></row><row><name>B</name><amount>200</amount><vat>40</vat><description>rowB</description><quantity>1</quantity><sku>666</sku><unit>st</unit></row><row><name>C</name><amount>300</amount><vat>60</vat><description>rowA</description><quantity>1</quantity><sku>667</sku><unit>st</unit></row></orderrows>
        // 5. the result should be:
        //<response>
        //  <transaction id="586209">
        //    <paymentmethod>KORTCERT</paymentmethod>
        //    <merchantid>1130</merchantid>
        //    <customerrefno>test_1410530092038</customerrefno>
        //    <amount>600</amount>
        //    <currency>EUR</currency>
        //    <cardtype>VISA</cardtype>
        //    <maskedcardno>444433xxxxxx1100</maskedcardno>
        //    <expirymonth>01</expirymonth>
        //    <expiryyear>15</expiryyear>
        //    <authcode>763907</authcode>
        //  </transaction>
        //  <statuscode>0</statuscode>
        //</response>
        // 6. enter the received transaction id below and run the test

        // Set the below to match the transaction, then run the test.
        $transactionId = 586263;

        // confirm the transaction
        $confirmRequest = new ConfirmTransaction(ConfigurationService::getDefaultConfig());
        $confirmRequest->transactionId = $transactionId;
        $confirmRequest->captureDate = date('c');
        $confirmRequest->countryCode = "SE";
        $confirmResponse = $confirmRequest->doRequest();

        ////print_r( $confirmResponse );
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse", $confirmResponse);
        $this->assertEquals(1, $confirmResponse->accepted);

        $queryRequest = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig());
        $queryResponse = $queryRequest->setCountryCode("SE")->setTransactionId($transactionId)->queryCardOrder()->doRequest();

        //print_r( $queryResponse );                   
        $this->assertEquals(1, $queryResponse->accepted);
        $this->assertEquals("CONFIRMED", $queryResponse->status);

        $deliverRequest = WebPayAdmin::deliverOrderRows(ConfigurationService::getDefaultConfig());
        $deliverRequest->setCountryCode("SE")->setOrderId($transactionId);
        $deliverRequest->setRowToDeliver(1)->addNumberedOrderRows($queryResponse->numberedOrderRows);
        $deliverResponse = $deliverRequest->deliverCardOrderRows()->doRequest();

        //print_r( $deliverResponse );
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse", $deliverResponse);
        $this->assertEquals(0, $deliverResponse->accepted);
        $this->assertEquals("105 (ILLEGAL_TRANSACTIONSTATUS)", $deliverResponse->resultcode);   // confirm of confirmed order
        $this->assertEquals("Invalid transaction status.", $deliverResponse->errormessage);
    }
}
