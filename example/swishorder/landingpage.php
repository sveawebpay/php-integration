<?php

/**
 * example file, how to handle a swish order request response
 * 
 * @author Fredrik Sundell / fre-sund
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\Response\SveaResponse;

error_reporting( E_ALL );
ini_set('display_errors', 'On');


// get config object
$myConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();


// the raw request response is posted to the returnurl (this page) from Svea.
$rawResponse = $_REQUEST;

// decode the raw response by passing it through the Svea\WebPay\Response\SveaResponse class
try
{
    $myResponse = new SveaResponse($rawResponse, $countryCode = NULL, $myConfig);
}
catch (Exception $e)
{
    echo $e->getMessage();
}

// The decoded response is available through the ->getResponse() method.
// Check the response attribute 'accepted' for true to see if the request succeeded, if not, see the attributes resultcode and/or errormessage
echo "<pre>Your request response:\n\n";

print_r( $myResponse->getResponse() );

echo "\n</pre><font color='blue'><pre>\n\n

An example of a successful request response. The 'accepted' attribute is true (1), and resultcode/errormessage is not set.

Svea\WebPay\HostedService\HostedResponse\HostedPaymentResponse Object
(
    [transactionId] => 722742
    [clientOrderNumber] => order #2019-11-29T14:28:35 01:00
    [paymentMethod] => SWISH
    [merchantId] => 1130
    [amount] => 3.75
    [currency] => SEK
    [accepted] => 1
    [resultcode] => 
    [errormessage] => 
)

)";

echo "\n</pre><font color='red'><pre>\n\n

An example of a rejected request response -- 'accepted' is false (0) and resultcode/errormessage indicates that the clientOrderNumber above has been reused, which is prohibited.   

Svea\HostedPaymentResponse Object
(
    [transactionId] => 582828
    [clientOrderNumber] => order #2019-11-29T14:28:35 01:00
    [paymentMethod] => SWISH
    [merchantId] => 1130
    [amount] => 3.75
    [currency] => SEK
    [accepted] => 0
    [resultcode] => 127 (CUSTOMERREFNO_ALREADY_USED)
    [errormessage] => Customer reference number already used in another transaction.
)";
?>
