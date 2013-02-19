<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../UnitTest/BuildOrder/TestRowFactory.php';

/**
 * Description of HostedPayments_RequestTest
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class HostedPayments_RequestTest extends PHPUnit_Framework_TestCase {

    function testDoCardPaymentRequest() {
        $rowFactory = new TestRowFactory();
        $form = WebPay::createOrder()
                ->setTestmode()
                ->addOrderRow(
                Item::orderRow()
                    ->setArticleNumber(1)
                    ->setQuantity(2)
                    ->setAmountExVat(100.00)
                    ->setDescription("Specification")
                    ->setName('Prod')
                    ->setUnit("st")
                    ->setVatPercent(25)
                    ->setDiscountPercent(0)
                        )
                
                ->run($rowFactory->buildShippingFee())              
                ->addDiscount(Item::relativeDiscount()
                        ->setDiscountId("1")
                        ->setDiscountPercent(50)
                        ->setUnit("st")
                        ->setName('Relative')
                        ->setDescription("RelativeDiscount")
                        )
                ->addCustomerDetails(Item::individualCustomer()->setSsn(2345234))             
                ->setCountryCode("SE")
                ->setClientOrderNumber("33")
                ->setOrderDate("2012-12-12")
                ->setCurrency("SEK")
                ->usePayPageCardOnly() // PayPageObject
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
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);

             $response = $info['http_code'];
            }
            curl_close($ch);
            if($response){
                $status = $response;
            }  else {
                $status = 'No answer';
            }
       
            
             $this->assertEquals(200, $status);
    }
}

?>
