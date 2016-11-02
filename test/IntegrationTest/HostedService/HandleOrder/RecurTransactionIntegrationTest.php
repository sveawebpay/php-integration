<?php

namespace Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\HostedService\HostedAdminRequest\RecurTransaction as RecurTransaction;


/**
 * Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder\RecurTransactionIntegrationTest
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class RecurTransactionIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * test_recur_subscriptionid_not_found
     *
     * used as initial acceptance criteria for credit transaction feature
     */
    function test_recur_subscriptionid_not_found()
    {

        $subscriptionId = 987654;
        $customerRefNo = "myCustomerRefNo";
        $currency = "SEK";
        $amount = 100;

        $request = new RecurTransaction(ConfigurationService::getDefaultConfig());
        $request->subscriptionId = $subscriptionId;
        $request->customerRefNo = $customerRefNo;
        $request->amount = $amount;
        $request->currency = $currency;

        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\RecurTransactionResponse", $response);

        // if we receive an error from the service, the integration test passes
        $this->assertEquals(0, $response->accepted);
        $this->assertEquals("322 (BAD_SUBSCRIPTION_ID)", $response->resultcode);
    }

    /**
     * test_manual_recur_transaction_amount
     *
     * run this test manually after you've performed a card transaction with
     * subscriptiontype set and have gotten the transaction details needed
     */
    function test_manual_recur_transaction_amount()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of recur transaction amount'
        );

        // 1. go to https://test.sveaekonomi.se/webpay-admin/admin/start.xhtml 
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>KORTCERT</paymentmethod><subscriptiontype>RECURRINGCAPTURE</subscriptiontype><currency>SEK</currency><amount>500</amount><vat>100</vat><customerrefno>test_recur_NN</customerrefno><returnurl>https://test.sveaekonomi.se/webpay-admin/admin/merchantresponsetest.xhtml</returnurl>
        // 5. the result should be:
        // <response><transaction id="581497"><paymentmethod>KORTCERT</paymentmethod><merchantid>1130</merchantid><customerrefno>test_recur_1</customerrefno><amount>500</amount><currency>SEK</currency><subscriptionid>2922</subscriptionid><cardtype>VISA</cardtype><maskedcardno>444433xxxxxx1100</maskedcardno><expirymonth>02</expirymonth><expiryyear>16</expiryyear><authcode>993955</authcode></transaction><statuscode>0</statuscode></response>

        // 6. enter the received subscription id, etc. below and run the test

        // Set the below to match the original transaction, then run the test.
        $paymentMethod = "KORTCERT";
        $merchantId = 1130;
        $currency = "SEK";
        $cardType = "VISA";
        $maskedCardNumber = "444433xxxxxx1100";
        $expiryMonth = 02;
        $expiryYear = 15;
        $subscriptionId = 3036;

        // the below applies to the recur request, and may differ from the original transaction
        $new_amount = "2500"; // in minor currency  
        $new_clientOrderNumber = "test_recur_" . date('c');

        // below is actual test, shouldn't need to change it
        $request = new RecurTransaction(ConfigurationService::getDefaultConfig());
        $request->subscriptionId = $subscriptionId;
        $request->customerRefNo = $new_clientOrderNumber;
        $request->amount = $new_amount;
        $request->currency = $currency;

        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\RecurTransactionResponse", $response);

        ////print_r($response);                
        $this->assertEquals(1, $response->accepted);

        $this->assertEquals("CARD", $response->paymentMethod);    // CARD is alias for KORTCERT, and this alias is returned by webservice
        $this->assertEquals($merchantId, $response->merchantId);
        $this->assertEquals($currency, $response->currency);
        $this->assertEquals($cardType, $response->cardType);
        $this->assertEquals($maskedCardNumber, $response->maskedCardNumber);
        $this->assertEquals($expiryMonth, $response->expiryMonth);
        $this->assertEquals($expiryYear, $response->expiryYear);
        $this->assertEquals($subscriptionId, $response->subscriptionId);

        $this->assertObjectHasAttribute("transactionId", $response);
        $this->assertEquals($new_clientOrderNumber, $response->clientOrderNumber);
        $this->assertEquals($new_amount, $response->amount);
    }
}

?>
