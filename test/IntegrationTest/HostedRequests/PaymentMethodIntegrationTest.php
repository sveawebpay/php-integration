<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class PaymentMethodIntegrationTest extends \PHPUnit_Framework_TestCase {

    function testGetAllPaymentMethods(){
        $response = WebPay::getPaymentMethods()
                ->setContryCode("SE")
                ->doRequest();
        
        $this->assertEquals(PaymentMethod::NORDEA_SE, $response[0]);
        $this->assertEquals(PaymentMethod::KORTCERT, $response[1]);
        $this->assertEquals(\Svea\SystemPaymentMethod::INVOICE_SE, $response[2]);
        $this->assertEquals(PaymentMethod::INVOICE, $response[3]);
        $this->assertEquals(PaymentMethod::PAYMENTPLAN, $response[4]);
    }
}
?>
