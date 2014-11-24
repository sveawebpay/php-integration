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
                
        $config = Svea\SveaConfig::getDefaultConfig();
        $response = WebPay::getPaymentMethods($config)
                ->setCountryCode("SE")
                ->doRequest();
         
        ////print_r( "testGetAllPaymentMethods: "); //print_r( $response );        
        $this->assertEquals(PaymentMethod::NORDEA_SE, $response[0]);
        $this->assertEquals(PaymentMethod::SEB_SE, $response[1]);
        $this->assertEquals(PaymentMethod::KORTCERT, $response[2]);
        $this->assertEquals(\Svea\SystemPaymentMethod::INVOICE_SE, $response[3]);
        $this->assertEquals(\Svea\SystemPaymentMethod::PAYMENTPLAN_SE, $response[4]);
        $this->assertEquals(PaymentMethod::INVOICE, $response[5]);
        $this->assertEquals(PaymentMethod::PAYMENTPLAN, $response[6]);
    }
}
?>
