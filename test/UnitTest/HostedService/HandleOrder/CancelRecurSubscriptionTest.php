<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\HandleOrder;

use SimpleXMLElement;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\WebpayAdmin;
use Svea\WebPay\HostedService\HostedAdminRequest\CancelRecurSubscription;


/**
 * @author Fredrik Sundell for Svea Webpay
 */
class CancelRecurSubscriptionTest extends \PHPUnit\Framework\TestCase
{

    protected $configObject;
    protected $cancelRecurSubscriptionObject;

    // fixture, run once before each test method
    protected function setUp()
    {
        $this->configObject = ConfigurationService::getDefaultConfig();
        $this->cancelRecurSubscriptionObject = new CancelRecurSubscription($this->configObject);
    }

    // test methods
    function test_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\CancelRecurSubscription", $this->cancelRecurSubscriptionObject);
        $this->assertEquals("cancelrecursubscription", \PHPUnit\Framework\Assert::readAttribute($this->cancelRecurSubscriptionObject, 'method'));
    }

    function test_setCountryCode()
    {
        $countryCode = "SE";
        $this->cancelRecurSubscriptionObject->countryCode = $countryCode;
        $this->assertEquals($countryCode, \PHPUnit\Framework\Assert::readAttribute($this->cancelRecurSubscriptionObject, 'countryCode'));
    }

    function test_prepareRequest_array_contains_mac_merchantid_message()
    {

        // set up annulTransaction object & get request form
        $subscriptionId = 334455;
        $this->cancelRecurSubscriptionObject->subscriptionId = $subscriptionId;

        $countryCode = "SE";
        $this->cancelRecurSubscriptionObject->countryCode = $countryCode;

        $form = $this->cancelRecurSubscriptionObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue(isset($form['merchantid']));
        $this->assertTrue(isset($form['mac']));
        $this->assertTrue(isset($form['message']));
    }

    function test_prepareRequest_request_has_correct_merchantid_mac_and_querytransactionid_request_message_contents()
    {

        // set up creditTransaction object & get request form
        $subscriptionId = "334455";
        $this->cancelRecurSubscriptionObject->subscriptionId = $subscriptionId;

        $countryCode = "SE";
        $this->cancelRecurSubscriptionObject->countryCode = $countryCode;

        $form = $this->cancelRecurSubscriptionObject->prepareRequest();

        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId(ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret(ConfigurationProvider::HOSTED_TYPE, $countryCode);

        // check mechantid
        $this->assertEquals($merchantid, urldecode($form['merchantid']));

        // check valid mac
        $this->assertEquals(hash("sha512", urldecode($form['message']) . $secret), urldecode($form['mac']));

        // check annul request message contents
        $xmlMessage = new SimpleXMLElement(base64_decode(urldecode($form['message'])));

        $this->assertEquals("cancelrecursubscription", $xmlMessage->getName());   // root node
        $this->assertEquals((string)$subscriptionId, $xmlMessage->subscriptionid);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : subscriptionId is required. Use function setSubscriptionId() with the subscriptionId from the createOrder response.
     */
    function test_prepareRequest_missing_transactionId_throws_exception()
    {
        $countryCode = "SE";
        $this->cancelRecurSubscriptionObject->countryCode = $countryCode;

        $form = $this->cancelRecurSubscriptionObject->prepareRequest();
    }
}
