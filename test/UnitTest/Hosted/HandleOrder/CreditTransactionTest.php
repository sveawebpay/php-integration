<?php
$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CreditTransactionTest extends PHPUnit_Framework_TestCase {
        
    protected $configObject;
    protected $creditObject;

    // fixture, run once before each test method
    protected function setUp() {
        $this->configObject = Svea\SveaConfig::getDefaultConfig();
        $this->creditObject = WebPay::creditTransaction( $this->configObject );
    }

    // test methods
    function test_class_exists(){
        $this->assertInstanceOf( "Svea\CreditTransaction", $this->creditObject);      
        $this->assertEquals( "credit", PHPUnit_Framework_Assert::readAttribute($this->creditObject, 'method') );        
    }
    
    function test_setCountryCode(){
        $countryCode = "SE";       
        $this->creditObject->setCountryCode( $countryCode );
        $this->assertEquals( $countryCode, PHPUnit_Framework_Assert::readAttribute($this->creditObject, 'countryCode') );
    }
    
    function test_setTransactionId( ){
        $transactionId = 987654;       
        $this->creditObject->setTransactionId( $transactionId );
        $this->assertEquals( $transactionId, PHPUnit_Framework_Assert::readAttribute($this->creditObject, 'transactionId') );
    }
    
    function test_setCreditAmount() {
        $creditAmount = 100;
        $this->creditObject->setCreditAmount( $creditAmount );
        $this->assertEquals( $creditAmount, PHPUnit_Framework_Assert::readAttribute($this->creditObject, 'creditAmount') );
    }
              
    function test_prepareRequest_array_contains_mac_merchantid_message() {

        // set up creditTransaction object & get request form
        $transactionId = 987654;       
        $this->creditObject->setTransactionId( $transactionId );

        $creditAmount = 100;
        $this->creditObject->setCreditAmount( $creditAmount );
        
        $countryCode = "SE";
        $this->creditObject->setCountryCode($countryCode);
                
        $form = $this->creditObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue( isset($form['merchantid']) );
        $this->assertTrue( isset($form['mac']) );
        $this->assertTrue( isset($form['message']) );
    }
    
    function test_prepareRequest_has_correct_merchantid_mac_and_creditTransaction_request_message_contents() {

        // set up creditTransaction object & get request form
        $transactionId = 987654;       
        $this->creditObject->setTransactionId( $transactionId );

        $creditAmount = 100;
        $this->creditObject->setCreditAmount( $creditAmount );
        
        $countryCode = "SE";
        $this->creditObject->setCountryCode($countryCode);
                
        $form = $this->creditObject->prepareRequest();
        
        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId( ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret( ConfigurationProvider::HOSTED_TYPE, $countryCode);
         
        // check mechantid
        $this->assertEquals( $merchantid, urldecode($form['merchantid']) );

        // check valid mac
        $this->assertEquals( hash("sha512", urldecode($form['message']). $secret), urldecode($form['mac']) );
        
        // check credit request message contents
        $xmlMessage = new SimpleXMLElement( base64_decode(urldecode($form['message'])) );

        $this->assertEquals( "credit", $xmlMessage->getName() );   // root node        
        $this->assertEquals((string)$transactionId, $xmlMessage->transactionid);
        $this->assertEquals((string)$creditAmount, $xmlMessage->amounttocredit);
        
    }
}
?>
