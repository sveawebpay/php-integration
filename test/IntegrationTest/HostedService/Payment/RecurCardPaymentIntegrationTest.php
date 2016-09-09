<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\HostedService\Payment;

use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\HostedService\HostedAdminRequest\RecurTransaction;
use Svea\WebPay\HostedService\Payment\CardPayment;


/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class RecurCardPaymentIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * test_manual_recurring_payment_step_1
     *
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */
    public function test_manual_recurring_payment_step_1()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of recurring payment' // TODO
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and get the subscription paymenturl from the output
        // 3. go to the paymenturl and complete the transaction.
        // 4. go to https://test.sveaekonomi.se/webpay-admin/admin/start.xhtml
        // 5. retrieve the subscriptionId from the response in the transaction log
        // 6. use the subscriptionId to run 

        $orderLanguage = "sv";
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";

        // create order
        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));
        // set payment method
        // call getPaymentURL
        $response = $order
            ->usePayPageCardOnly()
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->setSubscriptionType(CardPayment::RECURRINGCAPTURE)
            ->getPaymentUrl();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);

        // print the url to use to confirm the transaction
        //print_r( " test_manual_recurring_payment_step_1(): " . $response->testurl ." ");
    }

    /**
     * test_manual_recurring_payment_step_2
     *
     * run this test manually after you've performed a card transaction with
     * subscriptiontype set and have gotten the transaction details needed
     */
    function test_manual_recurring_payment_step_2()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of recur transaction amount'
        );

        // 1. enter the below values from the transaction log from test_manual_recurring_payment_step_1
        // 2. run the test and check the output for the subscriptionid and transactionid of the recur request

        // Set the below to match the original transaction, then run the test.
        $paymentmethod = "KORTCERT";
        $merchantid = 1130;
        $currency = "SEK";
        $cardtype = "VISA";
        $maskedcardno = "444433xxxxxx1100";
        $expirymonth = 02;
        $expiryyear = 16;
        $subscriptionid = 2960; // insert 

        // the below applies to the recur request, and may differ from the original transaction
        $new_amount = "2500"; // in minor currency  
        $new_customerrefno = "test_manual_recurring_payment_step_1 " . date('c');

        // below is actual test, shouldn't need to change it
        $request = new RecurTransaction(ConfigurationService::getDefaultConfig());
        $request->countryCode = "SE";
        $request->subscriptionId = $subscriptionid;
        $request->currency = $currency;
        $request->customerRefNo = $new_customerrefno;
        $request->amount = $new_amount;
        $response = $request->doRequest();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);

        // print the subscription id that succeeded to use to confirm the transaction
        //print_r( " test_manual_recurring_payment_step_2() recur succeded using subscriptionid: " . $response->subscriptionid ." " );
        //print_r( " test_manual_recurring_payment_step_2() for more info, check logs for transaction: " . $response->transactionid ." " );   
    }

}
