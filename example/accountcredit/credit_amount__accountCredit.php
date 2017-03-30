<?php

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Config\ConfigurationService;

error_reporting( E_ALL );
ini_set('display_errors', 'On');

$referenceNumber = 1000760;

$credit = WebPayAdmin::creditAmount(ConfigurationService::getDefaultConfig())
    ->setOrderId($referenceNumber)
    ->setCountryCode('SE')
    ->setDescription('try and credit desc')
    ->setAmountIncVat(150.00)
    ->creditAccountCredit()
    ->doRequest();

var_dump($credit);
