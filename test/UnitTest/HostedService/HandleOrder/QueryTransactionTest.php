<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebService/svea_soap/SveaSoapConfig.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class QueryTransactionTest extends PHPUnit_Framework_TestCase {
        
    protected $configObject;
    protected $queryObject;

    // fixture, run once before each test method
    protected function setUp() {
        $this->configObject = Svea\SveaConfig::getDefaultConfig();
        $this->queryObject = new Svea\HostedService\QueryTransaction( $this->configObject );
    }

    // test methods
    function test_class_exists(){        
        $this->assertInstanceOf( "Svea\HostedService\QueryTransaction", $this->queryObject);
        $this->assertEquals( "querytransactionid", PHPUnit_Framework_Assert::readAttribute($this->queryObject, 'method') );        
    }
    
    function test_setCountryCode(){
        $countryCode = "SE";       
        $this->queryObject->setCountryCode( $countryCode ); 
        $this->assertEquals( $countryCode, PHPUnit_Framework_Assert::readAttribute($this->queryObject, 'countryCode') );
    }
    
    function test_setTransactionId( ){
        $transactionId = 987654;       
        $this->queryObject->setTransactionId( $transactionId );
        $this->assertEquals( $transactionId, PHPUnit_Framework_Assert::readAttribute($this->queryObject, 'transactionId') );
    }
              
    function test_prepareRequest_array_contains_mac_merchantid_message() {

        // set up annulTransaction object & get request form
        $transactionId = 987654;       
        $this->queryObject->setTransactionId( $transactionId );

        $countryCode = "SE";
        $this->queryObject->setCountryCode($countryCode);
                
        $form = $this->queryObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue( isset($form['merchantid']) );
        $this->assertTrue( isset($form['mac']) );
        $this->assertTrue( isset($form['message']) );
    }
    
    function test_prepareRequest_request_has_correct_merchantid_mac_and_querytransactionid_request_message_contents() {

        // set up creditTransaction object & get request form
        $transactionId = 987654;       
        $this->queryObject->setTransactionId( $transactionId );

        $countryCode = "SE";
        $this->queryObject->setCountryCode($countryCode);
                
        $form = $this->queryObject->prepareRequest();
        
        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId( ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret( ConfigurationProvider::HOSTED_TYPE, $countryCode);
         
        // check mechantid
        $this->assertEquals( $merchantid, urldecode($form['merchantid']) );

        // check valid mac
        $this->assertEquals( hash("sha512", urldecode($form['message']). $secret), urldecode($form['mac']) );
        
        // check annul request message contents
        $xmlMessage = new SimpleXMLElement( base64_decode(urldecode($form['message'])) );

        $this->assertEquals( "query", $xmlMessage->getName() );   // root node        
        $this->assertEquals((string)$transactionId, $xmlMessage->transactionid); 
    }
    
    function test_prepareRequest_missing_transactionId_throws_exception() {

        $this->setExpectedException(
            'Svea\ValidationException', 
            '-missing value : transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.'
        );        
        
        $countryCode = "SE";
        $this->queryObject->setCountryCode($countryCode);
                
        $form = $this->queryObject->prepareRequest();     
    }
}
?>
