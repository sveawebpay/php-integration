<?php
$root = realpath(dirname(__FILE__));

require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class getPaymentURLTest extends PHPUnit_Framework_TestCase {
        
    protected $configObject;
    protected $getPaymentURLObject;

    // fixture, run once before each test method
    protected function setUp() {
        $this->configObject = Svea\SveaConfig::getDefaultConfig();
        $this->getPaymentURLObject = new \Svea\getPaymentURL( $this->configObject );
    }

    // test methods
    function test_class_exists(){
        
        $this->assertInstanceOf( "Svea\getPaymentURL", $this->getPaymentURLObject );      
    }

    function test_setCountryCode(){
        
        $countryCode = "SE";       
        $this->getPaymentURLObject->setCountryCode( $countryCode );
        
        $this->assertEquals( $countryCode, PHPUnit_Framework_Assert::readAttribute($this->getPaymentURLObject, 'countryCode') );
    }
    
    function test_setIpAddress( ){
        
        $ipAddress = "10.10.10.10";       
        $this->getPaymentURLObject->setIpAddress( $ipAddress );
        
        $this->assertEquals( $ipAddress, PHPUnit_Framework_Assert::readAttribute($this->getPaymentURLObject, 'ipAddress') );
    }

    function test_setPaymentMethod() {
        $paymentMethod = "KORTCERT";
        
        $this->getPaymentURLObject->setPaymentMethod( $paymentMethod );

        $this->assertEquals( $paymentMethod, PHPUnit_Framework_Assert::readAttribute($this->getPaymentURLObject, 'paymentMethod') );
    }
    
    // TODO write tests for other attribute setters here
    
    // TODO KGM use HostedPayment formater methods to set up attributes for call
    
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
