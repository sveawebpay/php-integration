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
             
        $request = new Svea\ListPaymentMethods( Svea\SveaConfig::getDefaultConfig() );
        $response = $request
            ->setCountryCode( "SE" )
            ->doRequest();

        $this->assertInstanceOf( "Svea\ListPaymentMethodsResponse", $response );
        
        //print_r( $response );
        $this->assertEquals( 1, $response->accepted );
        $this->assertInternalType( "array", $response->paymentmethods );
        $this->assertEquals( Svea\SystemPaymentMethod::DBNORDEASE, $response->paymentmethods[0]);
    }
}
?>
