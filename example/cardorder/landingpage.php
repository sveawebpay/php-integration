<?php

/**
 * example file, how to handle a card order request response
 * 
 * @author Kristian Grossman-madsen for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\Response\SveaResponse;

error_reporting( E_ALL );
ini_set('display_errors', 'On');


// get config object
$myConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();

$countryCode = "SE"; // should match request countryCode

// the raw request response is posted to the returnurl (this page) from Svea.
$rawResponse = $_POST;

// decode the raw response by passing it through the Svea\WebPay\Response\SveaResponse class
$myResponse = new SveaResponse( $rawResponse, $countryCode, $myConfig );

// The decoded response is available through the ->getResponse() method.
// Check the response attribute 'accepted' for true to see if the request succeeded, if not, see the attributes resultcode and/or errormessage
echo "<pre>Your request response:\n\n";

print_r( $myResponse->getResponse() );

echo "\n</pre><font color='blue'><pre>\n\n

An example of a successful request response. The 'accepted' attribute is true (1), and resultcode/errormessage is not set.

Svea\HostedPaymentResponse Object
(
    [transactionId] => 582827
    [clientOrderNumber] => order #20140519-374
    [paymentMethod] => KORTCERT
    [merchantId] => 1130
    [amount] => 23.74
    [currency] => SEK
    [accepted] => 1
    [resultcode] => 
    [cardType] => VISA
    [maskedCardNumber] => 444433xxxxxx1100
    [expiryMonth] => 02
    [expiryYear] => 15
    [authCode] => 941033
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
