<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\HostedService\Payment;

use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Constant\PaymentMethod;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CardPaymentURLIntegrationTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage ipAddress is required. Use function setIpAddress() on the order customer.
     *
     * @todo move to unit test for getPaymentURL validation
     */
    public function test_CardPayment_getPaymentURL_throws_validationException_if_missing_ipAddress()
    {
        $orderLanguage = "sv";
        $returnUrl = "returnUrl";
        $ipAddress = "127.0.0.1";

        // create order
        $order = TestUtil::createOrder(); // default customer has no ipAddress set
        // set payment method
        // call getPaymentURL
        $payment = $order
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();
        // check that request response contains an URL
        $this->assertInstanceOf('Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\HostedAdminResponse', $response);
    }

    public function test_CardPayment_getPaymentURL_returns_HostedResponse()
    {
        $orderLanguage = "sv";
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";

        // create order
        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));
        $order->setClientOrderNumber("foobar" . date('c'));
        // set payment method
        // call getPaymentURL
        $response = $order
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\HostedAdminResponse", $response);
    }

    public function test_manual_CardPayment_getPaymentURL_response_is_accepted_and_contains_response_attributes()
    {
        // Stop here and mark this test as incomplete. -- run manual as this seems to fail randomly at the service
        $this->markTestIncomplete(
            'skeleton for manual test of card payment'
        );

        $orderLanguage = "sv";
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";

        // create order
        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));
        $order->setClientOrderNumber("foobar" . date('c'));
        // set payment method
        // call getPaymentURL
        $response = $order
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        // check that request was accepted
        ////print_r($response);
        $this->assertEquals(1, $response->accepted);

        // check that response set id, created exist and not null
        $this->assertTrue(isset($response->id));
        $this->assertTrue(isset($response->created));
        // check that request response contains url
        $this->assertEquals("https://test", substr($response->url, 0, 12));
        // check that request response contains testurl
        $this->assertEquals("https://test", substr($response->testurl, 0, 12));
    }

    /**
     * test_manual_CardPayment_getPaymentURL
     */
    public function test_manual_CardPayment_getPaymentUrl()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of card payment'
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and get the subscription paymenturl from the output
        // 3. go to the paymenturl and complete the transaction.
        // 4. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 5. retrieve the transactionid from the response in the transaction log

        $orderLanguage = "sv";
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";

        // create order
        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));
        // set payment method
        // call getPaymentURL
        $response = $order
            ->setClientOrderNumber("foobar" . date('c'))
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);

        // print the url to use to confirm the transaction
        //print_r( " test_manual_card_payment by going to: " . $response->testurl ." and complete payment manually" );
    }

    /**
     * test_manual_CardPayment_getPaymentURL
     */
    public function test_CardPayment_getPaymentUrl_response_remake()
    {
        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'skeleton for manual test of card payment'
//        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and get the subscription paymenturl from the output
        // 3. go to the paymenturl and complete the transaction.
        // 4. go to https://webpaypaymentgatewaystage.svea.com/webpay-admin/admin/start.xhtml
        // 5. retrieve the transactionid from the response in the transaction log

        $orderLanguage = "sv";
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";

        // create order
        $order = TestUtil::createOrder(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));
        // set payment method
        // call getPaymentURL
        $response = $order
            ->setClientOrderNumber("foobar" . date('c'))
            ->usePaymentMethod(PaymentMethod::KORTCERT)
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);
        $this->assertEquals($response->testurl, $response->url); //url should always contain the url where the order is created in
        //and test is always test. In this case booth should therefore be test


    }
}
