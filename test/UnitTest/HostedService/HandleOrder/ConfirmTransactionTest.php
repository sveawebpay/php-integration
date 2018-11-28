<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\HandleOrder;

use SimpleXMLElement;
use PHPUnit_Framework_Assert;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedAdminRequest\ConfirmTransaction;

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class ConfirmTransactionTest extends \PHPUnit\Framework\TestCase
{

    protected $configObject;
    protected $confirmObject;

    // fixture, run once before each test method
    protected function setUp()
    {
        $this->configObject = ConfigurationService::getDefaultConfig();
        $this->confirmObject = new ConfirmTransaction($this->configObject);
    }

    // test methods
    function test_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\ConfirmTransaction", $this->confirmObject);
        $this->assertEquals("confirm", \PHPUnit\Framework\Assert::readAttribute($this->confirmObject, 'method'));
    }

    function test_prepareRequest_array_contains_mac_merchantid_message()
    {

        // set up confirmTransaction object & get request form
        $transactionId = 987654;
        $this->confirmObject->transactionId = $transactionId;

        $captureDate = "2014-03-21";
        $this->confirmObject->captureDate = $captureDate;

        $countryCode = "SE";
        $this->confirmObject->countryCode = $countryCode;

        $form = $this->confirmObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue(isset($form['merchantid']));
        $this->assertTrue(isset($form['mac']));
        $this->assertTrue(isset($form['message']));
    }

    function test_prepareRequest_has_correct_merchantid_mac_and_confirmTransaction_request_message_contents()
    {

        // set up confirmTransaction object & get request form
        $transactionId = 987654;
        $this->confirmObject->transactionId = $transactionId;

        $captureDate = "2014-03-21";
        $this->confirmObject->captureDate = $captureDate;

        $countryCode = "SE";
        $this->confirmObject->countryCode = $countryCode;

        $form = $this->confirmObject->prepareRequest();

        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId(ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret(ConfigurationProvider::HOSTED_TYPE, $countryCode);

        // check mechantid
        $this->assertEquals($merchantid, urldecode($form['merchantid']));

        // check valid mac
        $this->assertEquals(hash("sha512", urldecode($form['message']) . $secret), urldecode($form['mac']));

        // check confirm request message contents
        $xmlMessage = new SimpleXMLElement(base64_decode(urldecode($form['message'])));

        $this->assertEquals("confirm", $xmlMessage->getName());   // root node
        $this->assertEquals((string)$transactionId, $xmlMessage->transactionid);
        $this->assertEquals((string)$captureDate, $xmlMessage->capturedate);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.
     */
    function test_prepareRequest_missing_transactionId_throws_exception()
    {
        $captureDate = "2014-03-21";
        $this->confirmObject->captureDate = $captureDate;

        $countryCode = "SE";
        $this->confirmObject->countryCode = $countryCode;

        $form = $this->confirmObject->prepareRequest();
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : captureDate is required. Use function setCaptureDate().
     */
    function test_prepareRequest_missing_captureDate_throws_exception()
    {
        $transactionId = 987654;
        $this->confirmObject->transactionId = $transactionId;

        $countryCode = "SE";
        $this->confirmObject->countryCode = $countryCode;

        $form = $this->confirmObject->prepareRequest();
    }

    // really a test of parent class HostedRequest countryCode requirement...
    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : CountryCode is required. Use function setCountryCode().
     */
    function test_prepareRequest_missing_countryCode_throws_exception()
    {
        $transactionId = 987654;
        $this->confirmObject->transactionId = $transactionId;

        $captureDate = "2014-03-21";
        $this->confirmObject->captureDate = $captureDate;

        $form = $this->confirmObject->prepareRequest();
    }
}

?>
