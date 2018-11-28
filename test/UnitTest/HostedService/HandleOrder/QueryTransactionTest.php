<?php

namespace Svea\WebPay\Test\UnitTest\HostedService\HandleOrder;

use SimpleXMLElement;
use PHPUnit_Framework_Assert;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction;


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class QueryTransactionTest extends \PHPUnit\Framework\TestCase
{

    protected $configObject;
    protected $queryObject;

    // fixture, run once before each test method
    protected function setUp()
    {
        $this->configObject = ConfigurationService::getDefaultConfig();
        $this->queryObject = new QueryTransaction($this->configObject);
    }

    // test methods
    function test_class_exists()
    {
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction", $this->queryObject);
        $this->assertEquals("querytransactionid", \PHPUnit\Framework\Assert::readAttribute($this->queryObject, 'method'));
    }

    function test_setCountryCode()
    {
        $countryCode = "SE";
        $this->queryObject->countryCode = $countryCode;
        $this->assertEquals($countryCode, \PHPUnit\Framework\Assert::readAttribute($this->queryObject, 'countryCode'));
    }

    function test_prepareRequest_array_contains_mac_merchantid_message()
    {

        // set up annulTransaction object & get request form
        $transactionId = 987654;
        $this->queryObject->transactionId = $transactionId;

        $countryCode = "SE";
        $this->queryObject->countryCode = $countryCode;

        $form = $this->queryObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue(isset($form['merchantid']));
        $this->assertTrue(isset($form['mac']));
        $this->assertTrue(isset($form['message']));
    }

    function test_prepareRequest_request_has_correct_merchantid_mac_and_querytransactionid_request_message_contents()
    {

        // set up creditTransaction object & get request form
        $transactionId = 987654;
        $this->queryObject->transactionId = $transactionId;

        $countryCode = "SE";
        $this->queryObject->countryCode = $countryCode;

        $form = $this->queryObject->prepareRequest();

        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId(ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret(ConfigurationProvider::HOSTED_TYPE, $countryCode);

        // check mechantid
        $this->assertEquals($merchantid, urldecode($form['merchantid']));

        // check valid mac
        $this->assertEquals(hash("sha512", urldecode($form['message']) . $secret), urldecode($form['mac']));

        // check annul request message contents
        $xmlMessage = new SimpleXMLElement(base64_decode(urldecode($form['message'])));

        $this->assertEquals("query", $xmlMessage->getName());   // root node        
        $this->assertEquals((string)$transactionId, $xmlMessage->transactionid);
    }

    /**
     * @expectedException Svea\WebPay\BuildOrder\Validator\ValidationException
     * @expectedExceptionMessage -missing value : transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.
     */
    function test_prepareRequest_missing_transactionId_throws_exception()
    {
        $countryCode = "SE";
        $this->queryObject->countryCode = $countryCode;

        $form = $this->queryObject->prepareRequest();
    }
}

?>
