<?php

/**
 * example file, how to handle a card order request response
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
if( $myResponse->getResponse()->accepted == 0 ) {
    echo
"<pre>\nThe payment request failed, please try again.
resultcode: {$myResponse->getResponse()->resultcode}, errormessage: {$myResponse->getResponse()->errormessage}\n"
    ;
    die;
}
else {
    echo 
"<pre>\nRemember, we bought a shelf and two hot dogs.
Go to <a href=\"creditorderrows.php\">creditorderrows.php</a> to credit all but one of the hot dogs.\n"
    ;
}

?>
