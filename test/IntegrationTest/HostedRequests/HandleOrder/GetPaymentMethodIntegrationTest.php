<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class GetPaymentMethodIntegrationTest extends \PHPUnit_Framework_TestCase {

    function testGetAllPaymentMethods(){
        
print_r("\ntestGetAllPaymentMethods: " . date('c') . "\n " );
        
        $config = Svea\SveaConfig::getDefaultConfig();
        $response = WebPay::getPaymentMethods($config)
                ->setCountryCode("SE")
                ->doRequest();
        
print_r( "\ntestGetAllPaymentMethods: " ); print_r( $response );
        
        $this->assertEquals(PaymentMethod::NORDEA_SE, $response[0]);
        $this->assertEquals(PaymentMethod::SEB_SE, $response[1]);
        $this->assertEquals(PaymentMethod::KORTCERT, $response[2]);
        $this->assertEquals(\Svea\SystemPaymentMethod::INVOICE_SE, $response[3]);
        $this->assertEquals(\Svea\SystemPaymentMethod::PAYMENTPLAN_SE, $response[4]);
        $this->assertEquals(PaymentMethod::INVOICE, $response[5]);
        $this->assertEquals(PaymentMethod::PAYMENTPLAN, $response[6]);
    }
    
    
    function t_estParseGetAllPaymentMethodsResponseA() {
        
        print_r( "testParseGetAllPaymentMethodsResponseA\nT13:22:42+00:00 \nS \n");
        
        $responseXML = 
"<?xml version='1.0' encoding='UTF-8'?><response><message>PD94bWwgdmVyc2lvbj0nMS4wJyBlbmNvZGluZz0nVVRGLTgnPz48cmVzcG9uc2U+PHBheW1lbnRtZXRob2RzPjxwYXltZW50bWV0aG9kPkRCTk9SREVBU0U8L3BheW1lbnRtZXRob2Q+PHBheW1lbnRtZXRob2Q+REJTRUJTRTwvcGF5bWVudG1ldGhvZD48cGF5bWVudG1ldGhvZD5LT1JUQ0VSVDwvcGF5bWVudG1ldGhvZD48cGF5bWVudG1ldGhvZD5TVkVBSU5WT0lDRUVVX1NFPC9wYXltZW50bWV0aG9kPjxwYXltZW50bWV0aG9kPlNWRUFTUExJVEVVX1NFPC9wYXltZW50bWV0aG9kPjwvcGF5bWVudG1ldGhvZHM+PHN0YXR1c2NvZGU+MDwvc3RhdHVzY29kZT48L3Jlc3BvbnNlPg==</message>  
<merchantid>1130</merchantid>  
<mac>9ee8dd4d657ca26b16b26f0d72fa6fa3247fde1bac10bdc05ef4e37be31451bfd2aaa5a04360e58ce11e307a81aa9d53ae2141fc74d45f8dc1a2bab577475aa5</mac>  
</response>";        
        
        $config = Svea\SveaConfig::getDefaultConfig();

        $responseObj = new \SimpleXMLElement($responseXML);

        //print_r( "\ngetPaymentMethods::doRequest responseObj: " ); print_r( $responseObj );             

        $sveaResponse = new \SveaResponse($responseObj, "SE", $config);
        
        print_r( "\ngetPaymentMethods::doRequest sveaResponse: " ); print_r( $sveaResponse );     
        
    }     
}
?>
