<?php

require_once '../../vendor/autoload.php';

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\WebPay;

error_reporting( E_ALL );
ini_set('display_errors', 'On');



$ppCampaign = WebPay::getPaymentPlanParams(ConfigurationService::getDefaultConfig());

$ppCampaign->enableLogging(true);

$campaigns = $ppCampaign->setCountryCode('SE')
    ->doRequest();

echo "<pre>" . print_r($campaigns, true) . "</pre>";
