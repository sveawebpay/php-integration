<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../../src/WebServiceRequests/svea_soap/SveaSoapConfig.php';


/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class SveaResponseTest extends \PHPUnit_Framework_TestCase {

    public function test_handles_response_is_null() {
        $config = SveaConfig::getDefaultConfig();

        $response = new \SveaResponse( NULL, "SE", $config );
        $this->assertInternalType('string', $response->getResponse() );
        $this->assertEquals('Response is not recognized.', $response->getResponse() );
    }
    
    public function test_handles_response_is_xml() {
        $config = SveaConfig::getDefaultConfig();

        $message = "string_that_pretends_to_be_an_encoded_xml_response";
                
        $response = new \SveaResponse( $message, "SE", $config );
        $this->assertInstanceOf('Svea\HostedPaymentResponse', $response->getResponse() );
        
    }
    
    public function test_handles_response_is_SimpleXMLElement_object() {
        $config = SveaConfig::getDefaultConfig();

        $message = (object)array( "CloseOrderEuResult" => (object) array( "Accepted" => "1", "ResultCode" => "0" ) );
        
        $this->assertTrue( \is_object($message) ); 
                     
        $response = new \SveaResponse( $message, "SE", $config );
        $this->assertInstanceOf('Svea\CloseOrderResult', $response->getResponse() );
    }
}
