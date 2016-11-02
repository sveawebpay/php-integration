<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\HandleOrder;

use SimpleXMLElement;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedAdminRequest\LowerTransaction;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class LowerTransactionTest extends PHPUnit_Framework_TestCase
{

    protected $configObject;
    protected $lowerTransactionObject;

    // fixture, run once before each test method
    protected function setUp()
    {
        $this->configObject = ConfigurationService::getDefaultConfig();
        $this->lowerTransactionObject = new LowerTransaction($this->configObject);
    }

    // test methods
    function test_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\LowerTransaction", $this->lowerTransactionObject);
        $this->assertEquals("loweramount", PHPUnit_Framework_Assert::readAttribute($this->lowerTransactionObject, 'method'));
    }

    function test_setCountryCode()
    {
        $countryCode = "SE";
        $this->lowerTransactionObject->countryCode = $countryCode;
        $this->assertEquals($countryCode, PHPUnit_Framework_Assert::readAttribute($this->lowerTransactionObject, 'countryCode'));
    }

    function test_prepareRequest_array_contains_mac_merchantid_message()
    {

        // set up lowerTransaction object & get request form
        $transactionId = 987654;
        $this->lowerTransactionObject->transactionId = $transactionId;

        $amountToLower = 100;
        $this->lowerTransactionObject->amountToLower = $amountToLower;

        $countryCode = "SE";
        $this->lowerTransactionObject->countryCode = $countryCode;

        $form = $this->lowerTransactionObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue(isset($form['merchantid']));
        $this->assertTrue(isset($form['mac']));
        $this->assertTrue(isset($form['message']));
    }

    function test_prepareRequest_has_correct_merchantid_mac_and_lowerTransaction_request_message_contents()
    {

        // set up lowerTransaction object & get request form
        $transactionId = 987654;
        $this->lowerTransactionObject->transactionId = $transactionId;

        $amountToLower = 100;
        $this->lowerTransactionObject->amountToLower = $amountToLower;

        $countryCode = "SE";
        $this->lowerTransactionObject->countryCode = $countryCode;

        $form = $this->lowerTransactionObject->prepareRequest();

        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId(ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret(ConfigurationProvider::HOSTED_TYPE, $countryCode);

        // check mechantid
        $this->assertEquals($merchantid, urldecode($form['merchantid']));

        // check valid mac
        $this->assertEquals(hash("sha512", urldecode($form['message']) . $secret), urldecode($form['mac']));

        // check credit request message contents
        $xmlMessage = new SimpleXMLElement(base64_decode(urldecode($form['message'])));

        $this->assertEquals("loweramount", $xmlMessage->getName());   // root node        
        $this->assertEquals((string)$transactionId, $xmlMessage->transactionid);
        $this->assertEquals((string)$amountToLower, $xmlMessage->amounttolower);
    }

    function test_prepareRequest_missing_transactionId_throws_exception()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.'
        );

        $amountToLower = 100;
        $this->lowerTransactionObject->amountToLower = $amountToLower;

        $countryCode = "SE";
        $this->lowerTransactionObject->countryCode = $countryCode;

        $form = $this->lowerTransactionObject->prepareRequest();
    }


    function test_prepareRequest_missing_amountToLower_throws_exception()
    {

        $this->setExpectedException(
            'Svea\WebPay\BuildOrder\Validator\ValidationException',
            '-missing value : amountToLower is required. Use function setAmountToLower().'
        );

        $transactionId = 987654;
        $this->lowerTransactionObject->transactionId = $transactionId;

        $countryCode = "SE";
        $this->lowerTransactionObject->countryCode = $countryCode;

        $form = $this->lowerTransactionObject->prepareRequest();
    }

}

?>
