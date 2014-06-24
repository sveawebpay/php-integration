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
            $payPage = "";
            $response = $info['http_code'];

            if (isset($info['redirect_url'])) {
                $payPage = $info['redirect_url'];
            }
        }
        curl_close($ch);

        if ($response) {
            $status = $response;
            $redirect = substr($payPage, 41, 7);
        } else {
            $status = 'No answer';
        }

        $this->assertEquals(302, $status); //Curl response code "Found"
        $this->assertEquals("payPage", $redirect);
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
            $payPage = "";
            $response = $info['http_code'];

            if (isset($info['redirect_url'])) {
                $payPage = $info['redirect_url'];
            }
        }
        curl_close($ch);

        if ($response) {
            $status = $response;
            $redirect = substr($payPage, 41, 7);
        } else {
            $status = 'No answer';
        }

        $this->assertEquals(302, $status); //Curl response code "Found"
        $this->assertEquals("payPage", $redirect);
    }

    // manual tests
    
    // recurring payments
    
    
    
    /**
     * test_manual_recurring_payment_step_1 
     * 
     * run this manually after you've performed a card transaction and have set
     * the transaction status to success using the tools in the logg admin.
     */  
    public function test_manual_recurring_payment_step_1() {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test of recurring payment' // TODO
        );
               
        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and get the subscription paymenturl from the output
        // 3. go to the paymenturl and complete the transaction.
        // 4. go to test.sveaekonomi.se/webpay/admin/start.xhtml
        // 5. retrieve the subscriptionId from the response in the transaction log
        // 6. use the subscriptionId to run 
        
        $orderLanguage = "sv";   
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";
        
        // create order
        $order = \TestUtil::createOrder( TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress) );
        // set payment method
        // call getPaymentURL
        $response = $order
            ->usePayPageCardOnly()
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->setSubscriptionType( Svea\CardPayment::RECURRINGCAPTURE)
            ->getPaymentURL();

        // check that request was accepted
        $this->assertEquals( 1, $response->accepted );                

        // print the url to use to confirm the transaction
        print_r( " test_manual_recurring_payment_step_1(): " . $response->testurl ." ");
    }
    
    /**
     * test_manual_recurring_payment_step_2 
     * 
     * run this test manually after you've performed a card transaction with 
     * subscriptiontype set and have gotten the transaction details needed
     */  
    function test_manual_recurring_payment_step_2() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'skeleton for manual test of recur transaction amount'
        );
        
        // 1. enter the below values from the transaction log from test_manual_recurring_payment_step_1
        // 2. run the test and check the output for the subscriptionid and transactionid of the recur request
        
        // Set the below to match the original transaction, then run the test.
        $paymentmethod = "KORTCERT";  
        $merchantid = 1130;  
        $currency = "SEK";  
        $cardtype = "VISA";  
        $maskedcardno = "444433xxxxxx1100";
        $expirymonth = 02;  
        $expiryyear = 16;  
        $subscriptionid = 2960; // insert 

        // the below applies to the recur request, and may differ from the original transaction
        $new_amount = "2500"; // in minor currency  
        $new_customerrefno = "test_manual_recurring_payment_step_1 ".date('c');  

        // below is actual test, shouldn't need to change it
        $request = new Svea\RecurTransaction( Svea\SveaConfig::getDefaultConfig() );
        $response = $request                
            ->setSubscriptionId( $subscriptionid )
            ->setCurrency( $currency )
            ->setCustomerRefNo( $new_customerrefno )
            ->setAmount( $new_amount )
            ->setCountryCode( "SE" )
            ->doRequest();        
            
        // check that request was accepted
        $this->assertEquals( 1, $response->accepted );                

        // print the subscription id that succeeded to use to confirm the transaction
        print_r( " test_manual_recurring_payment_step_2() recur succeded using subscriptionid: " . $response->subscriptionid ." " );
        print_r( " test_manual_recurring_payment_step_2() for more info, check logs for transaction: " . $response->transactionid ." " );   
    }        
    
}
