<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\HandleOrder;

use SimpleXMLElement;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedAdminRequest\CreditTransaction;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CreditTransactionTest extends PHPUnit_Framework_TestCase
{

    protected $configObject;
    protected $creditObject;

    // fixture, run once before each test method
    protected function setUp()
    {
        $this->configObject = ConfigurationService::getDefaultConfig();
        $this->creditObject = new CreditTransaction($this->configObject);
    }

    // test methods
    function test_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\CreditTransaction", $this->creditObject);
        $this->assertEquals("credit", PHPUnit_Framework_Assert::readAttribute($this->creditObject, 'method'));
    }

    function test_prepareRequest_array_contains_mac_merchantid_message()
    {

        // set up creditTransaction object & get request form
        $transactionId = 987654;
        $this->creditObject->transactionId = $transactionId;

        $creditAmount = 100;
        $this->creditObject->creditAmount = $creditAmount;

        $countryCode = "SE";
        $this->creditObject->countryCode = $countryCode;

        $form = $this->creditObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue(isset($form['merchantid']));
        $this->assertTrue(isset($form['mac']));
        $this->assertTrue(isset($form['message']));
    }

    function test_prepareRequest_has_correct_merchantid_mac_and_creditTransaction_request_message_contents()
    {

        // set up creditTransaction object & get request form
        $transactionId = 987654;
        $this->creditObject->transactionId = $transactionId;

        $creditAmount = 100;
        $this->creditObject->creditAmount = $creditAmount;

        $countryCode = "SE";
        $this->creditObject->countryCode = $countryCode;

        $form = $this->creditObject->prepareRequest();

        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId(ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret(ConfigurationProvider::HOSTED_TYPE, $countryCode);

        // check mechantid
        $this->assertEquals($merchantid, urldecode($form['merchantid']));

        // check valid mac
        $this->assertEquals(hash("sha512", urldecode($form['message']) . $secret), urldecode($form['mac']));

        // check credit request message contents
        $xmlMessage = new SimpleXMLElement(base64_decode(urldecode($form['message'])));

        $this->assertEquals("credit", $xmlMessage->getName());   // root node        
        $this->assertEquals((string)$transactionId, $xmlMessage->transactionid);
        $this->assertEquals((string)$creditAmount, $xmlMessage->amounttocredit);

    }

    function test_prepareRequest_missing_transactionId_throws_exception()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.'
        );

        $creditAmount = 100;
        $this->creditObject->creditAmount = $creditAmount;

        $countryCode = "SE";
        $this->creditObject->countryCode = $countryCode;

        $form = $this->creditObject->prepareRequest();
    }

    function test_prepareRequest_missing_creditAmount_throws_exception()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : creditAmount is required. Use function setCreditAmount().'
        );

        $transactionId = 987654;
        $this->creditObject->transactionId = $transactionId;

        $countryCode = "SE";
        $this->creditObject->countryCode = $countryCode;

        $form = $this->creditObject->prepareRequest();
    }
}

?>
