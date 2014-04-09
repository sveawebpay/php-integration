<?php
$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class AnnulTransactionTest extends PHPUnit_Framework_TestCase {
        
    protected $configObject;
    protected $annulObject;

    // fixture, run once before each test method
    protected function setUp() {
        $this->configObject = Svea\SveaConfig::getDefaultConfig();
        $this->annulObject = WebPay::annulTransaction( $this->configObject );
    }

    // test methods
    function test_class_exists(){
        
        $this->assertInstanceOf( "Svea\AnnulTransaction", $this->annulObject);      
    }
    
    function test_setCountryCode(){
        
        $countryCode = "SE";       
        $this->annulObject->setCountryCode( $countryCode );
        
        $this->assertEquals( $countryCode, PHPUnit_Framework_Assert::readAttribute($this->annulObject, 'countryCode') );
    }
    
    function test_setTransactionId( ){
        
        $transactionId = 987654;       
        $this->annulObject->setTransactionId( $transactionId );
        
        $this->assertEquals( $transactionId, PHPUnit_Framework_Assert::readAttribute($this->annulObject, 'transactionId') );
    }
              
    function test_prepareRequest_array_contains_mac_merchantid_message() {

        // set up cannulTransaction object & get request form
        $transactionId = 987654;       
        $this->annulObject->setTransactionId( $transactionId );

        $countryCode = "SE";
        $this->annulObject->setCountryCode($countryCode);
                
        $form = $this->annulObject->prepareRequest();

        // prepared request is message (base64 encoded), merchantid, mac
        $this->assertTrue( isset($form['merchantid']) );
        $this->assertTrue( isset($form['mac']) );
        $this->assertTrue( isset($form['message']) );
    }
    
        function test_prepareRequest_request_has_correct_merchantid_mac_and_annulTransaction_request_message_contents() {

        // set up creditTransaction object & get request form
        $transactionId = 987654;       
        $this->annulObject->setTransactionId( $transactionId );

        $countryCode = "SE";
        $this->annulObject->setCountryCode($countryCode);
                
        $form = $this->annulObject->prepareRequest();
        
        // get our merchantid & secret
        $merchantid = $this->configObject->getMerchantId( ConfigurationProvider::HOSTED_TYPE, $countryCode);
        $secret = $this->configObject->getSecret( ConfigurationProvider::HOSTED_TYPE, $countryCode);
         
        // check mechantid
        $this->assertEquals( $merchantid, urldecode($form['merchantid']) );

        // check valid mac
        $this->assertEquals( hash("sha512", urldecode($form['message']). $secret), urldecode($form['mac']) );
        
        // check annul request message contents
        $xmlMessage = new SimpleXMLElement( base64_decode(urldecode($form['message'])) );

        $this->assertEquals( "annul", $xmlMessage->getName() );   // root node        
        $this->assertEquals((string)$transactionId, $xmlMessage->transactionid);
        
    }

}
?>
