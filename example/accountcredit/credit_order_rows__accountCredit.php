<?php

require_once '../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Config\ConfigurationService;

error_reporting( E_ALL );
ini_set('display_errors', 'On');

$orderId = 1000757;

// - sample for single row credit
$orderRowIndex = 2;

$credit = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
    ->setOrderId($orderId)
    ->setCountryCode('SE')
    ->setRowToCredit(2)
    ->creditAccountCreditOrderRows()
    ->doRequest();

var_dump($credit);