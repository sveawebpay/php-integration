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
    
    /**
     * investigation of github php-integration issue #39, non-confidential data
     */
    public function test_successful_test_card_order_has_accepted_non_zero() {

        $config = SveaConfig::getSingleCountryConfig(
            null, //SE
            null, null, null,
            null, null, null,
            null, null,
            false // $prod = false
        );
        
        $message = "PD94bWwgdmVyc2lvbj0nMS4wJyBlbmNvZGluZz0nVVRGLTgnPz48cmVzcG9uc2U+PHRyYW5zYWN0aW9uIGlkPSI1ODEzODAiPjxwYXltZW50bWV0aG9kPktPUlRDRVJUPC9wYXltZW50bWV0aG9kPjxtZXJjaGFudGlkPjExMzA8L21lcmNoYW50aWQ+PGN1c3RvbWVycmVmbm8+MzY8L2N1c3RvbWVycmVmbm8+PGFtb3VudD4xODU3ODwvYW1vdW50PjxjdXJyZW5jeT5TRUs8L2N1cnJlbmN5PjxjYXJkdHlwZT5WSVNBPC9jYXJkdHlwZT48bWFza2VkY2FyZG5vPjQ0NDQzM3h4eHh4eDExMDA8L21hc2tlZGNhcmRubz48ZXhwaXJ5bW9udGg+MDE8L2V4cGlyeW1vbnRoPjxleHBpcnl5ZWFyPjE1PC9leHBpcnl5ZWFyPjxhdXRoY29kZT40NTM2MjY8L2F1dGhjb2RlPjxjdXN0b21lcj48Zmlyc3RuYW1lLz48bGFzdG5hbWUvPjxpbml0aWFscy8+PGVtYWlsPnRlc3RAdGltLWludGVybmF0aW9uYWwubmV0PC9lbWFpbD48c3NuPjwvc3NuPjxhZGRyZXNzPktsb2NrYXJnYXRhbiA1QzwvYWRkcmVzcz48YWRkcmVzczIvPjxjaXR5PlbDpHN0ZXLDpXM8L2NpdHk+PGNvdW50cnk+U0U8L2NvdW50cnk+PHppcD43MjM0NDwvemlwPjxwaG9uZT40NjcwNDE2MDA5MDwvcGhvbmU+PHZhdG51bWJlci8+PGhvdXNlbnVtYmVyPjU8L2hvdXNlbnVtYmVyPjxjb21wYW55bmFtZS8+PGZ1bGxuYW1lLz48L2N1c3RvbWVyPjwvdHJhbnNhY3Rpb24+PHN0YXR1c2NvZGU+MDwvc3RhdHVzY29kZT48L3Jlc3BvbnNlPg==";
        $mac = "0411ed66739c251308b70c642fc5f7282f89050421408b74bdd909fb0c13c37c4c2efd6da3593dc388dd28952478aeb1ce5259caf33fd68d364fc4f82914e055";
        
        $merchantId = $config->getMerchantId(\ConfigurationProvider::HOSTED_TYPE, "SE"); 
        
        $request = array();
        $request['response'] = $message;
        $request['mac'] = $mac;
        $request['merchantId'] = $merchantId;
  
        $response = new \SveaResponse( $request, "SE", $config);
        
//        print_r( $response->getResponse() );
//        var_dump( $response->getResponse() );
//        
//        if( empty($response->getResponse()->accepted ) ) { print_r( "test accepted is empty"); }
//        if( !empty($response->getResponse()->accepted ) ) { print_r( "test accepted not empty"); }
      
        $this->assertInstanceOf('Svea\HostedPaymentResponse', $response->getResponse() );
        $this->assertEquals(1, $response->getResponse()->accepted );
        
    }
}