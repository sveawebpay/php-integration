<?php

/**
 * example file, writes card order request response transactionid to file for use when crediting transaction
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

// abort if request failed
if( $myResponse->getResponse()->accepted == 0 ) {
    echo "<pre>Request failed. aborting";
    print_r( $myResponse->getResponse() );
    die;
}

// The decoded response is available through the ->getResponse() method.
// Check the response attribute 'accepted' for true to see if the request succeeded, if not, see the attributes resultcode and/or errormessage
echo "<pre>Your card order request response, this is the transaction to be credited in the next step:\n\n";
print_r( $myResponse->getResponse() );

// save the subscriptionid to a file, for use in recurorder.php
$myTransactionId = $myResponse->getResponse()->transactionId;
file_put_contents("transactionid.txt", $myTransactionId);

$creditorderrowsUrl = "http://localhost/".getPath()."/creditorderrows.php";

echo "\nFollow the link to credit all rows in this order ($myTransactionId):\n";
print_r("<a href=\"$creditorderrowsUrl\">$creditorderrowsUrl</a>");

echo "\n</pre><font color='blue'><pre>\n\n

An example of a successful request response. The 'accepted' attribute is true (1), and resultcode/errormessage is not set.

Svea\HostedService\HostedPaymentResponse Object
(
    [transactionId] => 585588
    [clientOrderNumber] => order #2014-08-26T13:49:48 02:00
    [paymentMethod] => KORTCERT
    [merchantId] => 1130
    [amount] => 136.2
    [currency] => SEK
    [accepted] => 1
    [resultcode] => 
    [errormessage] => 
    [cardType] => VISA
    [maskedCardNumber] => 444433xxxxxx1100
    [expiryMonth] => 02
    [expiryYear] => 15
    [authCode] => 104747
    [rawHostedPaymentResponse] => SimpleXMLElement Object 
        /.../
)";

/**
 * get the path to this file, for use in specifying the returnurl etc.
 */
function getPath() {
    $myURL = $_SERVER['SCRIPT_NAME'];
    $myPath = explode('/', $myURL);
    unset( $myPath[count($myPath)-1]);
    $myPath = implode( '/', $myPath);

    return $myPath;
}
?>