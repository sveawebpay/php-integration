<?php
/**
 * example file, how to create a card order request
 */
error_reporting( E_ALL );
ini_set('display_errors', 'On');

// Include Svea PHP integration package.
$svea_directory = "../../src/";
require_once( $svea_directory . "Includes.php" );

// get config object
$myConfig = Svea\SveaConfig::getTestConfig(); //replace with class holding your merchantid, secretword, et al, adopted from package Config/SveaConfig.php

// We assume that you've collected the following information about the order in your shop:

// customer information:
$customerFirstName = "Tess T";
$customerLastName = "Persson";
$customerAddress = "Testgatan 1";
$customerZipCode = "99999";
$customerCity = "Stan";
$customerCountry = "Sverige";

// The customer has bought three items, one "Billy" which cost 700,99 kr excluding vat (25%) and two hotdogs for 5 kr (incl. vat).

// We'll also need information about the customer country, and the currency used for this order, etc., see below

// Start the order creation process by creating the order builder object by calling WebPay::createOrder():
$myOrder = WebPay::createOrder( $myConfig );

// You then add information to the order object by using the methods in the Svea\CreateOrderBuilder class.
// For a Card order, the following methods are required:
$myOrder->setCountryCode("SE");                         // customer country, we recommend basing this on the customer billing address
$myOrder->setCurrency("SEK");                           // order currency
// You may also chain fluent methods together:
$myOrder->setCustomerReference("customer #123")         // This should contain a customer reference, as in "customer #123".
        ->setClientOrderNumber("order #20140519-371")   // This should contain the client side order number, i.e. "order #20140519-371"
        ->setOrderDate("2014-05-28");                   // or use an ISO801 date as produced by i.e. date('c')

// Then specify the items bought as order rows, using the methods in the Svea\OrderRow class, and adding them to the order:
$firstBoughtItem = WebPayItem::orderRow();
$firstBoughtItem->setAmountExVat( 10.99 );
$firstBoughtItem->setVatPercent( 25 );
$firstBoughtItem->setQuantity( 1 );
$firstBoughtItem->setDescription( "Billy" );
$firstBoughtItem->setArticleNumber("123456789A");

// Add firstBoughtItem to order row
$myOrder->addOrderRow( $firstBoughtItem ); 

// Add secondBoughtItem in a fluent fashion 
$myOrder->addOrderRow( 
            WebPayItem::orderRow()
                ->setAmountIncVat( 5.00 )
                ->setVatPercent( 12 )
                ->setQuantity( 2 )
                ->setDescription( "Korv med bröd" )
);

// For card orders the ->addCustomerDetails() method is optional, but recommended, so we'll add what info we have
$myCustomerInformation = WebPayItem::individualCustomer(); // there's also a ::companyCustomer() method, used for non-person entities

// Set customer information, using the methods from the IndividualCustomer class
$myCustomerInformation->setName( $customerFirstName, $customerLastName);
$sveaAddress = Svea\Helper::splitStreetAddress($customerAddress); // Svea requires an address and a house number
$myCustomerInformation->setStreetAddress( $sveaAddress[0], $sveaAddress[1] );
$myCustomerInformation->setZipCode( $customerZipCode )->setLocality( $customerCity );

// We have now completed specifying the order, and wish to send the payment request to Svea. To do so, we first select a payment method.
// For card orders, we recommend using the ->usePaymentMethod(PaymentMethod::KORTCERT), which processes card orders via Certitrade.
$myCardOrderRequest = $myOrder->usePaymentMethod(PaymentMethod::KORTCERT);

// Then set any additional required request attributes as detailed below. (See Svea\PaymentMethodPayment and Svea\HostedPayment classes for details.)
$myCardOrderRequest
    ->setCardPageLanguage("SV")                                // ISO639 language code, i.e. "SV", "EN" etc. Defaults to English.
    ->setCancelUrl("http://www.myshop.se/checkout")            // The cancel url to which the user is redirected if it should cancel the card payment  
    ->setReturnUrl("http://www.myshop.se/myCardLandingPage");  // The return url which receives and processes the finished card payment request response
        
// Get a prepared payment form object which you can use to send the payment request to Svea
$myCardOrderPaymentForm = $myCardOrderRequest->getPaymentForm();

// Then send the form to Svea, below we do so using curl, pretending everything on this page happened in response to a user "confirm payment" request.
postFormDataUsingCurl( $myCardOrderPaymentForm, $myConfig->getEndPoint( ConfigurationProvider::HOSTED_TYPE ));

function postFormDataUsingCurl( $form, $url ) {
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
//
//    $this->assertEquals(302, $status); //Curl response code "Found"
//    $this->assertEquals("payPage", $redirect);
}

?>
