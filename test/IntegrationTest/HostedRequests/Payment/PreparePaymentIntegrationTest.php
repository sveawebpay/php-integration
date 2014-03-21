<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class PreparedPaymentIntegrationTest extends \PHPUnit_Framework_TestCase {

    /**
     * test_doRequest_success 
     * 
     * used as initial acceptance criteria for preparePayment feature
     */  
    function test_doRequest_success() {
             
        // set up order & select payment type incl. required all settings here

        $ipAddress = "127.0.0.1";
        
        $order = TestUtil::createOrder();
        $hostedPayment = $order->usePaymentMethod(PaymentMethod::KORTCERT);        
        $response = $hostedPayment
            ->getPaymentAddress();
                
        $this->assertInstanceOf( "Svea\HostedAdminResponse", $response );
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'not yet complete' // TODO KGM
        );
        
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 1, $response->accepted );
//        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }    
    
    
//    public function test_doRequest_to_paypage_get_status_302_found() {
//        $config = Svea\SveaConfig::getDefaultConfig();
//        $rowFactory = new TestUtil();
//        $form = WebPay::createOrder($config)
//                ->addOrderRow(TestUtil::createOrderRow())
//                ->run($rowFactory->buildShippingFee())
//                ->addDiscount(WebPayItem::relativeDiscount()
//                        ->setDiscountId("1")
//                        ->setDiscountPercent(50)
//                        ->setUnit("st")
//                        ->setName('Relative')
//                        ->setDescription("RelativeDiscount")
//                )
//                ->setCountryCode("SE")
//                ->setClientOrderNumber(rand(0, 1000))
//                ->setOrderDate("2012-12-12")
//                ->setCurrency("SEK")
//                ->usePayPage() // PayPageObject
//                ->setReturnUrl("http://myurl.se")
//                ->getPaymentForm();
//        $url = "https://test.sveaekonomi.se/webpay/payment";
//
//        /** CURL  **/
//        $fields = array('merchantid' => urlencode($form->merchantid), 'message' => urlencode($form->xmlMessageBase64), 'mac' => urlencode($form->mac));
//        $fieldsString = "";
//        foreach ($fields as $key => $value) {
//            $fieldsString .= $key.'='.$value.'&';
//        }
//        rtrim($fieldsString, '&');
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_POST, count($fields));
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        //force curl to trust https
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        //returns a html page with redirecting to bank...
//        curl_exec($ch);
//
//        // Check if any error occurred
//        if (!curl_errno($ch)) {
//            $info = curl_getinfo($ch);
//            $payPage = "";
//            $response = $info['http_code'];
//
//            if (isset($info['redirect_url'])) {
//                $payPage = $info['redirect_url'];
//            }
//        }
//        curl_close($ch);
//
//        if ($response) {
//            $status = $response;
//            $redirect = substr($payPage, 41, 7);
//        } else {
//            $status = 'No answer';
//        }
//
//        $this->assertEquals(302, $status); //Curl response code "Found"
//        $this->assertEquals("payPage", $redirect);
//    }
//    
//        public function test_accepts_relativeDiscount_as_float() {
//        $config = Svea\SveaConfig::getDefaultConfig();
//        $rowFactory = new TestUtil();
//        $form = WebPay::createOrder($config)
//                ->addOrderRow(TestUtil::createOrderRow())
//                ->run($rowFactory->buildShippingFee())
//                ->addDiscount(WebPayItem::relativeDiscount()
//                        ->setDiscountId("1")
//                        ->setDiscountPercent(50.5)
//                        ->setUnit("st")
//                        ->setName('Relative')
//                        ->setDescription("RelativeDiscount")
//                )
//                ->setCountryCode("SE")
//                ->setClientOrderNumber(rand(0, 1000))
//                ->setOrderDate("2012-12-12")
//                ->setCurrency("SEK")
//                ->usePayPage() // PayPageObject
//                ->setReturnUrl("http://myurl.se")
//                ->getPaymentForm();
//        $url = "https://test.sveaekonomi.se/webpay/payment";
//
//        /** CURL  **/
//        $fields = array('merchantid' => urlencode($form->merchantid), 'message' => urlencode($form->xmlMessageBase64), 'mac' => urlencode($form->mac));
//        $fieldsString = "";
//        foreach ($fields as $key => $value) {
//            $fieldsString .= $key.'='.$value.'&';
//        }
//        rtrim($fieldsString, '&');
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_POST, count($fields));
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        //force curl to trust https
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        //returns a html page with redirecting to bank...
//        curl_exec($ch);
//
//        // Check if any error occurred
//        if (!curl_errno($ch)) {
//            $info = curl_getinfo($ch);
//            $payPage = "";
//            $response = $info['http_code'];
//
//            if (isset($info['redirect_url'])) {
//                $payPage = $info['redirect_url'];
//            }
//        }
//        curl_close($ch);
//
//        if ($response) {
//            $status = $response;
//            $redirect = substr($payPage, 41, 7);
//        } else {
//            $status = 'No answer';
//        }
//
//        $this->assertEquals(302, $status); //Curl response code "Found"
//        $this->assertEquals("payPage", $redirect);
//    }

}
