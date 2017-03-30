<?php
require_once '../../vendor/autoload.php';

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\WebPay;

error_reporting( E_ALL );
ini_set('display_errors', 'On');



$ppCampaign = WebPay::getAccountCreditParams(ConfigurationService::getDefaultConfig());

$campaigns = $ppCampaign->setCountryCode('SE')
    ->doRequest();

var_dump($campaigns);