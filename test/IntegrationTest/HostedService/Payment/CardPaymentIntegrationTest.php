<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CardPaymentIntegrationTest extends \PHPUnit_Framework_TestCase {

    public function test_doRequest_to_paypage_get_status_302_found() {
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
                ->setClientOrderNumber(rand(0, 1000))
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //force curl to trust https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //returns a html page with redirecting to bank...
        curl_exec($ch);

        // Check if any error occurred
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);            
            //[url] => https://test.sveaekonomi.se/webpay/payment
            //[content_type] => 
            //[http_code] => 302
            //[header_size] => 513
            //[request_size] => 166
            //[filetime] => -1
            //[ssl_verify_result] => 0
            //[redirect_count] => 0
            //[total_time] => 1.747
            //[namelookup_time] => 0
            //[connect_time] => 0.016
            //[pretransfer_time] => 1.077
            //[size_upload] => 1476
            //[size_download] => 0
            //[speed_download] => 0
            //[speed_upload] => 844
            //[download_content_length] => 0
            //[upload_content_length] => 1476
            //[starttransfer_time] => 1.092
            //[redirect_time] => 0
            //[redirect_url] => http://test.sveaekonomi.se/webpay/public/static/paypage.html#payment=S1hDZVl1UmdLVWdtV2dTaGllRmNVUT09
            //[primary_ip] => 10.1.15.220
            //[certinfo] => Array
            //(
            //)
            //
            //[primary_port] => 443
            //[local_ip] => 10.222.201.36
            //[local_port] => 64102
            
            $response = $info['http_code'];

            if (isset($info['redirect_url'])) {
                $payPage = $info['redirect_url'];
            }
        }
        curl_close($ch);

        if ($response) {
            $status = $response;
            $redirect = substr($payPage, 48, 7);
        } else {
            $status = 'No answer';
        }

        if( $status == 200 ) {
            print_r( $info );
            $this->fail( "got status 200, expected 302" );
        }        
        $this->assertEquals(302, $status); //Curl response code "Found"
        $this->assertEquals("paypage", $redirect);
    }
    
    public function test_accepts_relativeDiscount_as_float() {
        $config = Svea\SveaConfig::getDefaultConfig();
        $rowFactory = new TestUtil();
        $form = WebPay::createOrder($config)
                ->addOrderRow(TestUtil::createOrderRow())
                ->run($rowFactory->buildShippingFee())
                ->addDiscount(WebPayItem::relativeDiscount()
                        ->setDiscountId("1")
                        ->setDiscountPercent(50.5)
                        ->setUnit("st")
                        ->setName('Relative')
                        ->setDescription("RelativeDiscount")
                )
                ->setCountryCode("SE")
                ->setClientOrderNumber(rand(0, 1000))
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //force curl to trust https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //returns a html page with redirecting to bank...
        curl_exec($ch);

        // Check if any error occurred
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            //[url] => https://test.sveaekonomi.se/webpay/payment
            //[content_type] => 
            //[http_code] => 302
            //[header_size] => 513
            //[request_size] => 166
            //[filetime] => -1
            //[ssl_verify_result] => 0
            //[redirect_count] => 0
            //[total_time] => 0.608
            //[namelookup_time] => 0
            //[connect_time] => 0
            //[pretransfer_time] => 0.016
            //[size_upload] => 1492
            //[size_download] => 0
            //[speed_download] => 0
            //[speed_upload] => 2453
            //[download_content_length] => 0
            //[upload_content_length] => 1492
            //[starttransfer_time] => 0.016
            //[redirect_time] => 0
            //[redirect_url] => http://test.sveaekonomi.se/webpay/public/static/paypage.html#payment=NEtGRElwcE9wcjZBS1JBQVpEQmplQT09
            //[primary_ip] => 10.1.15.220
            //[certinfo] => Array
            //    (
            //    )
            //
            //[primary_port] => 443
            //[local_ip] => 10.222.201.36
            //[local_port] => 65525                  
            
            $payPage = "";
            $response = $info['http_code'];

            if (isset($info['redirect_url'])) {
                $payPage = $info['redirect_url'];
            }
        }
        curl_close($ch);

        if ($response) {
            $status = $response;
            $redirect = substr($payPage, 48, 7);
        } else {
            $status = 'No answer';
        }

        if( $status == 200 ) {
            print_r( $info );
            //[url] => https://test.sveaekonomi.se/webpay/payment
            //[content_type] => text/html;charset=ISO-8859-1
            //[http_code] => 200
            //[header_size] => 431
            //[request_size] => 166
            //[filetime] => -1
            //[ssl_verify_result] => 0
            //[redirect_count] => 0
            //[total_time] => 0.421
            //[namelookup_time] => 0
            //[connect_time] => 0
            //[pretransfer_time] => 0.015
            //[size_upload] => 1476
            //[size_download] => 860
            //[speed_download] => 2042
            //[speed_upload] => 3505
            //[download_content_length] => 860
            //[upload_content_length] => 1476
            //[starttransfer_time] => 0.031
            //[redirect_time] => 0
            //[redirect_url] => 
            //[primary_ip] => 10.1.15.220
            //[certinfo] => Array
            //(
            //)
            //
            //[primary_port] => 443
            //[local_ip] => 10.222.201.36
            //[local_port] => 49171
            $this->fail( "got status 200, expected 302" );
        }
        
        $this->assertEquals(302, $status); //Curl response code "Found"
        $this->assertEquals("paypage", $redirect);
    }
}
