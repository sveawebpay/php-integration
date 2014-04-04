<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CardPaymentURLIntegrationTest extends \PHPUnit_Framework_TestCase {

    public function test_CardPayment_getPaymentURL_returns_HostedResponse() {
        $orderLanguage = "sv";   
        
        // create order
         $order = \TestUtil::createOrder();       
        // set payment method
        // call getPaymentURL
        $payment = $order->usePaymentMethod(\PaymentMethod::KORTCERT)
            ->setPayPageLanguage( $orderLanguage )
            ->getPaymentURL();
        // check that request response contains an URL
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );     
    }
}
