<?php

require_once '../../vendor/autoload.php';

use \Svea\WebPay\WebPayAdmin;
use \Svea\WebPay\Config\ConfigurationService;

$config = ConfigurationService::getTestConfig();

try {
    $request = WebpayAdmin::cancelRecurSubscription($config)
        ->setCountryCode("SE");

    $subscriptionId = file_get_contents("subscription.txt");
    if ($subscriptionId) {
        $request->setSubscriptionId($subscriptionId);
    } else {
        echo "<pre>Error: subscription.txt not found, first run cardorder_recur.php to set up the card order subscription. aborting.";
        die;
    }

    $response = $request->cancelRecurSubscription()->doRequest();

    if ($response->accepted == 1) {
        echo "SubscriptionId " . $subscriptionId . " was cancelled.";
    }
    else
    {
        echo "Statuscode: " . $response->resultcode . " Error message: " . $response->errormessage;
    }
}
catch(Exception $exception)
{
    echo $exception->getMessage();
}