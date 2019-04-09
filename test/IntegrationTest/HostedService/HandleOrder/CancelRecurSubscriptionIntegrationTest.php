<?php

namespace Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\HostedService\HostedAdminRequest\CancelRecurSubscription;
use Svea\WebPay\HostedService\HostedAdminRequest\RecurTransaction as RecurTransaction;


/**
 * Svea\WebPay\Test\IntegrationTest\HostedService\HandleOrder\CancelRecurSubscriptionIntegrationTest
 *
 * @author Fredrik Sundell for Svea Webpay
 */
class CancelRecurSubscriptionIntegrationTest extends \PHPUnit\Framework\TestCase
{

    /**
     * test_cancel_recur_subscriptionid_not_found
     *
     * used as initial acceptance criteria for cancel recur transaction feature
     */
    function test_cancel_recur_subscriptionid_not_found()
    {

        $subscriptionId = 987654;

        $request = new CancelRecurSubscription(ConfigurationService::getDefaultConfig());
        $request->subscriptionId = $subscriptionId;

        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\CancelRecurSubscriptionResponse", $response);

        // if we receive an error from the service, the integration test passes
        $this->assertEquals(0, $response->accepted);
        $this->assertEquals("128 (NO_SUCH_TRANS)", $response->resultcode);
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
            'skeleton for manual test of cancel recur subscription'
        );

        // 1. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>SVEACARDPAY</paymentmethod><subscriptiontype>RECURRINGCAPTURE</subscriptiontype><currency>SEK</currency><amount>500</amount><vat>100</vat><customerrefno>test_recur_NN</customerrefno><returnurl>https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/merchantresponsetest.xhtml</returnurl>
        // 5. the result should be:
        //<response>
        //<transaction id="690341">
        //<paymentmethod>SVEACARDPAY</paymentmethod>
        //<merchantid>1130</merchantid>
        //<customerrefno>test_recur_5958696</customerrefno>
        //<amount>500</amount>
        //<currency>SEK</currency>
        //<subscriptionid>6169</subscriptionid>
        //<cardtype>VISA</cardtype>
        //<maskedcardno>491642******8102</maskedcardno>
        //<expirymonth>1</expirymonth>
        //<expiryyear>20</expiryyear>
        //<chname>-</chname>
        //<authcode>042434</authcode>
        //</transaction>
        //<statuscode>0</statuscode>
        //</response>
        // 6. enter the received subscription id, etc. below and run the test


        $subscriptionId = 6169;


        // below is actual test, shouldn't need to change it
        $request = new CancelRecurSubscription(ConfigurationService::getDefaultConfig());
        $request->subscriptionId = $subscriptionId;

        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\CancelRecurSubscriptionResponse", $response);

        ////print_r($response);                
        $this->assertEquals(1, $response->accepted);
    }
}

?>
