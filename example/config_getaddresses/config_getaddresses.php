<?php
/**
 * example file, shows how to use a modified config file to perform a getAddresses lookup
 *
 * @author Kristian Grossman-madsen for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

// Include my config file, populated with my account credentials
require("MyConfig.php");

use Svea\WebPay\WebPay;

error_reporting(E_ALL);
ini_set('display_errors', 'On');


// We wish to make requests directed to the Svea test servers
$testmode_enabled = true;
//$testmode_enabled = false;

// Get a config object  populated with the correct credentials, for test or production, respectively
if ($testmode_enabled) {
    $myConfig = Svea\MyConfig::getTestConfig();
} else {
    $myConfig = Svea\MyConfig::getProdConfig();
}

// Make a getAddresses lookup using my account test credentials:
$request = WebPay::getAddresses($myConfig);

// We assume that we know the following about the customer we wish to get the address for:
// We need to supply the (individual) customer's social security number
// We need to supply the country code that corresponds to the account credentials used in the address lookup, as well call corresponding setOrderType method

$getAddressesForIndividualSE = "4605092222";
$useCredentialsForCountry = "SE";

// Populate the request object
$request->setCustomerIdentifier($getAddressesForIndividualSE);
$request->getIndividualAddresses();
$request->setCountryCode($useCredentialsForCountry);
$request->setOrderTypeInvoice();

// Then send the request to Svea, and receive a response in return
$response = $request->doRequest();

echo "<pre>";
print_r("Raw GetAddressResponse contents for testperson $getAddressesForIndividualSE:\n\n");
print_r($response);


// Make another getAddresses lookup, this time for a Norwegian company customer:
$companyRequest = WebPay::getAddresses($myConfig);

$getAddressesForCompanyNO = 923313850;

$companyRequest->setCustomerIdentifier($getAddressesForCompanyNO);
$companyRequest->getCompanyAddresses();
$companyRequest->setCountryCode("NO");
$companyRequest->setOrderTypeInvoice();

$companyResponse = $companyRequest->doRequest();

print_r("Raw GetAddressResponse contents for testperson $getAddressesForCompanyNO:\n\n");
print_r($companyResponse);

//foreach( $companyResponse->customerIdentity as $address ) {
//    print_r( $address ); print_r( "\n" );
//}

?>
