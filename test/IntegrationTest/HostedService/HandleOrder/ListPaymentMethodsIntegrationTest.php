<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * ListPaymentMethodsIntegrationTest 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class ListPaymentMethodsIntegrationTest extends \PHPUnit_Framework_TestCase {
 
    function test_listPaymentMethods_request_success() {
             
        $request = new Svea\HostedService\ListPaymentMethods( Svea\SveaConfig::getDefaultConfig() );
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf( "Svea\HostedService\ListPaymentMethodsResponse", $response );
        
        ////print_r( "test_listPaymentMethods_request_success: "); //print_r( $response );
        $this->assertEquals( 1, $response->accepted );
        $this->assertInternalType( "array", $response->paymentmethods );

        // from getpaymentmethods call, tied to merchantid
        $this->assertEquals( PaymentMethod::NORDEA_SE, $response->paymentmethods[0]);
        $this->assertEquals( PaymentMethod::SEB_SE, $response->paymentmethods[1]);        
        $this->assertEquals( PaymentMethod::KORTCERT, $response->paymentmethods[2]); 
        $this->assertEquals( \Svea\SystemPaymentMethod::INVOICE_SE, $response->paymentmethods[3]);
        $this->assertEquals( \Svea\SystemPaymentMethod::PAYMENTPLAN_SE, $response->paymentmethods[4]);
        
        // from ListPaymentMethods implementation, tied to clientid
        $this->assertEquals( PaymentMethod::INVOICE, $response->paymentmethods[5]);
        $this->assertEquals( PaymentMethod::PAYMENTPLAN, $response->paymentmethods[6]);                       
    }
}
?>
