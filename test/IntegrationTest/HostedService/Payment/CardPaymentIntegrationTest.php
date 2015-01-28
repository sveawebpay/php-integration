<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CardPaymentIntegrationTest extends \PHPUnit_Framework_TestCase {

    public function test_createOrder_usePayPage_redirects_to_paypage() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $rowFactory = new TestUtil();
        $form = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->run($rowFactory->buildShippingFee())
                ->addDiscount(WebPayItem::relativeDiscount()
                        ->setDiscountId("1")
                        ->setDiscountPercent(50)
                        ->setUnit("st")
                        ->setName('Relative')
                        ->setDescription("RelativeDiscount")
                )
                ->setCountryCode("SE")
                ->setClientOrderNumber("foobar".date('c'))
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPage() // PayPageObject
                    ->setReturnUrl("http://myurl.se")
                    ->getPaymentForm();
        $url = "https://test.sveaekonomi.se/webpay/payment";

        /** CURL  **/
        $fields = array('merchantid' => urlencode($form->merchantid), 'message' => urlencode($form->xmlMessageBase64), 'mac' => urlencode($form->mac));
        $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);     // follow redirects
        curl_setopt($ch, CURLOPT_HEADER, true);             // include headers in transfer history
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        // return transfer history
        $cr = curl_exec($ch);
        
        $info = curl_getinfo($ch);                        

        curl_close($ch);      
        
        //print_r( $cr);        
        $this->assertEquals(200, $info['http_code']);
        $this->assertEquals(2, $info['redirect_count']);
        $this->assertEquals("https://test.sveaekonomi.se/webpay/public/static/paypage.html", $info['url']);
    }
}
