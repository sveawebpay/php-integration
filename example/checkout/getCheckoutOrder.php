<?php //
/**
 * example file, how to create an checkout order request
 *
 * @author Savo Garovic and Janko Stevanovic  for Svea Svea\WebPay\WebPay
 */

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPay;
use Svea\WebPay\Config\ConfigurationService;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

// get config object
$myConfig = ConfigurationService::getTestConfig();

$orderBuilder = WebPay::checkout($myConfig);

$orderBuilder->setCheckoutOrderId(51721)
    ->setCountryCode('SE'); // optional line of code

try {
    $response = $orderBuilder->getOrder();
    echo "<pre>" . print_r($response, true) ."</pre>";
} catch (\Exception $e) {
    echo "<pre>" . print_r($e->getMessage(), true) . "</pre>";
}
