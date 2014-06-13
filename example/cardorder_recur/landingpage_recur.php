<?php

/**
 * example file, how to handle a recur card order request response
 * 
 * @author Kristian Grossman-madsen for Svea WebPay
 */

error_reporting( E_ALL );
ini_set('display_errors', 'On');

// Include Svea PHP integration package.
$svea_directory = "../../src/";
require_once( $svea_directory . "Includes.php" );

// get config object
$myConfig = Svea\SveaConfig::getTestConfig();

$countryCode = "SE"; // should match request countryCode

// the raw request response is posted to the returnurl (this page) from Svea.
$rawResponse = $_POST;

// decode the raw response by passing it through the SveaResponse class
$myResponse = new SveaResponse( $rawResponse, $countryCode, $myConfig );
// The decoded response is available through the ->getResponse() method.
// Check the response attribute 'accepted' for true to see if the request succeeded, if not, see the attributes resultcode and/or errormessage
echo "<pre>Your request response:\n\n";

print_r( $myResponse->getResponse() );

// save the subscriptionid to a file, for use in recurorder.php
file_put_contents("subscription.txt", $myResponse->getResponse()->subscriptionId);

echo "\n</pre><font color='blue'><pre>\n\n

An example of a successful request response. The 'accepted' attribute is true (1), and resultcode/errormessage is not set.

Svea\HostedService\HostedPaymentResponse Object
(
    [transactionId] => 583429
    [clientOrderNumber] => order #2014-06-13T13:58:48 02:00
    [paymentMethod] => KORTCERT
    [merchantId] => 1130
    [amount] => 100
    [currency] => SEK
    [accepted] => 1
    [resultcode] => 
    [errormessage] => 
    [subscriptionId] => 3039
    [cardType] => VISA
    [maskedCardNumber] => 444433xxxxxx1100
    [expiryMonth] => 01
    [expiryYear] => 15
    [authCode] => 520721
)";

echo "\n</pre><font color='red'><pre>\n\n

An example of a rejected request response -- 'accepted' is false (0) and resultcode/errormessage indicates that the clientOrderNumber above has been reused, which is prohibited.   

Svea\HostedPaymentResponse Object
(
    [transactionId] => 582828
    [clientOrderNumber] => order #20140519-374.err
    [paymentMethod] => KORTCERT
    [merchantId] => 1130
    [amount] => 23.74
    [currency] => SEK
    [accepted] => 0
    [resultcode] => 127 (CUSTOMERREFNO_ALREADY_USED)
    [errormessage] => Customer reference number already used in another transaction.
)";
?>
