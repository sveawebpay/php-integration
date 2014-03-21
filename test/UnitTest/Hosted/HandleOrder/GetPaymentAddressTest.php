<?php
$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class GetPaymentAddressTest extends PHPUnit_Framework_TestCase {
        
    protected $configObject;
    protected $getPaymentAddressObject;

    // fixture, run once before each test method
    protected function setUp() {
        $this->configObject = Svea\SveaConfig::getDefaultConfig();
        $this->getPaymentAddressObject = new \Svea\GetPaymentAddress( $this->configObject );
    }

    // test methods
    function test_class_exists(){
        
        $this->assertInstanceOf( "Svea\GetPaymentAddress", $this->getPaymentAddressObject );      
    }

//    function test_setCountryCode(){
//        
//        $countryCode = "SE";       
//        $this->confirmObject->setCountryCode( $countryCode );
//        
//        $this->assertEquals( $countryCode, PHPUnit_Framework_Assert::readAttribute($this->confirmObject, 'countryCode') );
//    }
//    
//    function test_setTransactionId( ){
//        
//        $transactionId = 987654;       
//        $this->confirmObject->setTransactionId( $transactionId );
//        
//        $this->assertEquals( $transactionId, PHPUnit_Framework_Assert::readAttribute($this->confirmObject, 'transactionId') );
//    }
//    
//              
//    function test_prepareRequest_array_contains_mac_merchantid_message() {
//
//        // set up confirmTransaction object & get request form
//        $transactionId = 987654;       
//        $this->confirmObject->setTransactionId( $transactionId );
//
//        $captureDate = "2014-03-21";
//        $this->confirmObject->setCaptureDate( $captureDate );
//        
//        $countryCode = "SE";
//        $this->confirmObject->setCountryCode($countryCode);
//                
//        $form = $this->confirmObject->prepareRequest();
//
//        // prepared request is message (base64 encoded), merchantid, mac
//        $this->assertTrue( isset($form['merchantid']) );
//        $this->assertTrue( isset($form['mac']) );
//        $this->assertTrue( isset($form['message']) );
//    }
//    
//    function test_prepareRequest_has_correct_merchantid_mac_and_confirmTransaction_request_message_contents() {
//
//        // set up confirmTransaction object & get request form
//        $transactionId = 987654;       
//        $this->confirmObject->setTransactionId( $transactionId );
//
//        $captureDate = "2014-03-21";
//        $this->confirmObject->setCaptureDate( $captureDate );
//        
//        $countryCode = "SE";
//        $this->confirmObject->setCountryCode($countryCode);
//                
//        $form = $this->confirmObject->prepareRequest();
//        
//        // get our merchantid & secret
//        $merchantid = $this->configObject->getMerchantId( ConfigurationProvider::HOSTED_TYPE, $countryCode);
//        $secret = $this->configObject->getSecret( ConfigurationProvider::HOSTED_TYPE, $countryCode);
//         
//        // check mechantid
//        $this->assertEquals( $merchantid, urldecode($form['merchantid']) );
//
//        // check valid mac
//        $this->assertEquals( hash("sha512", urldecode($form['message']). $secret), urldecode($form['mac']) );
//        
//        // check confirm request message contents
//        $xmlMessage = new SimpleXMLElement( base64_decode(urldecode($form['message'])) );
//
//        $this->assertEquals( "confirm", $xmlMessage->getName() );   // root node        
//        $this->assertEquals((string)$transactionId, $xmlMessage->transactionid);
//        $this->assertEquals((string)$captureDate, $xmlMessage->capturedate);     
//    }
}
?>
