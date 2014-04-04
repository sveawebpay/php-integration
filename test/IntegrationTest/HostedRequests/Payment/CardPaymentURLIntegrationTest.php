<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CardPaymentURLIntegrationTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException Svea\ValidationException 
     * @expectedExceptionMessage "ipAddress is required. Use function setIpAddress() on the order customer."
     * 
     * @todo move to unit test for getPaymentURL validation
     */
    public function test_CardPayment_getPaymentURL_throws_validationException_if_missing_ipAddress() {
        $orderLanguage = "sv";   
        $returnUrl = "returnUrl";
        $ipAddress = "127.0.0.1";
        
        // create order
        $order = \TestUtil::createOrder(); // default customer has no ipAddress set
        // set payment method
        // call getPaymentURL
        $payment = $order
            ->usePaymentMethod(\PaymentMethod::KORTCERT)
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentURL();
        // check that request response contains an URL
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );     
    }
    
//    /**
//     * acceptance test for INTG-269: getPaymentURL/preparepayment request
//     */
//    public function test_CardPayment_getPaymentURL_returns_HostedResponse() {
//        $orderLanguage = "sv";   
//        $returnUrl = "returnUrl";
//        $ipAddress = "127.0.0.1";
//        
//        // create order
//        $order = \TestUtil::createOrder( TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress) );
//        // set payment method
//        // call getPaymentURL
//        $payment = $order
//            ->usePaymentMethod(\PaymentMethod::KORTCERT)
//            ->setPayPageLanguage($orderLanguage)
//            ->setReturnUrl($returnUrl)
//            ->getPaymentURL();
//        // check that request response contains an URL
//        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );     
//    }
//    
    
}
