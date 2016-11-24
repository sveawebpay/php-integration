<?php

require_once '../../../vendor/autoload.php';

use Svea\WebPay\WebPayAdmin;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$testConfig = \Svea\WebPay\Config\ConfigurationService::getTestConfig();

$taskUrl = 'http://paymentadminapi.svea.com/api/v1/queue/1';

try {
    $response = WebPayAdmin::queryTaskInfo($testConfig)
        ->setTaskUrl($taskUrl)
        ->getTaskInfo()
        ->doRequest();

    print_r($response);
} catch (\Exception $ex) {
    var_dump('Error message -> ' . $ex->getMessage());
    var_dump('Error code -> ' . $ex->getCode());
}
