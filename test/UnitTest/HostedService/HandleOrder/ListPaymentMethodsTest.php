<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\HandleOrder;

use SimpleXMLElement;
use PHPUnit_Framework_Assert;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedAdminRequest\ListPaymentMethods;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class ListPaymentMethodsTest extends \PHPUnit\Framework\TestCase
{

    protected $configObject;
    protected $listpaymentmethodsObject;

    // fixture, run once before each test method
    protected function setUp()
    {
        $this->configObject = ConfigurationService::getDefaultConfig();
        $this->listpaymentmethodObject = new ListPaymentMethods($this->configObject);
    }

    // test methods
    function test_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\ListPaymentMethods", $this->listpaymentmethodObject);
        $this->assertEquals("getpaymentmethods", \PHPUnit\Framework\Assert::readAttribute($this->listpaymentmethodObject, 'method'));
    }

    function test_prepareRequest_array_contains_mac_merchantid_message()
    {

        // set up ListPaymentMethods object & get request form
        $countryCode = "SE";
        $this->listpaymentmethodObject->countryCode = $countryCode;

        $form = $this->listpaymentmethodObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue(isset($form['merchantid']));
        $this->assertTrue(isset($form['mac']));
        $this->assertTrue(isset($form['message']));
    }

    function test_prepareRequest_request_has_correct_merchantid_mac_and_ListPaymentMethods_request_message_contents()
    {

        $countryCode = "SE";
        $this->listpaymentmethodObject->countryCode = $countryCode;

        $form = $this->listpaymentmethodObject->prepareRequest();

        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId(ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret(ConfigurationProvider::HOSTED_TYPE, $countryCode);

        // check mechantid
        $this->assertEquals($merchantid, urldecode($form['merchantid']));

        // check valid mac
        $this->assertEquals(hash("sha512", urldecode($form['message']) . $secret), urldecode($form['mac']));

        // check request message contents
        $xmlMessage = new SimpleXMLElement(base64_decode(urldecode($form['message'])));

        $this->assertEquals("getpaymentmethods", $xmlMessage->getName());   // root node        
        $this->assertEquals((string)$merchantid, $xmlMessage->merchantid);
    }
}

?>
