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

// abort if request failed
if( $myResponse->getResponse()->accepted == 0 ) {
    echo "<pre>Request failed. aborting";
    print_r( $myResponse->getResponse() );
    die;
}

// The decoded response is available through the ->getResponse() method.
// Check the response attribute 'accepted' for true to see if the request succeeded, if not, see the attributes resultcode and/or errormessage
echo "<pre>Your inital card order request response, including the the subscription id for use in future recur order requests:\n\n";
print_r( $myResponse->getResponse() );

// save the subscriptionid to a file, for use in recurorder.php
$mySubscriptionId = $myResponse->getResponse()->subscriptionId;
file_put_contents("subscription.txt", $mySubscriptionId);

$recurorderUrl = "http://localhost/".getPath()."/recurorder.php";

echo "\nFollow the link to place a recur card order using subscriptionId $mySubscriptionId:\n";
print_r("<a href=\"$recurorderUrl\">$recurorderUrl</a>");

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
