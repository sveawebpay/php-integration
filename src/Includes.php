<?php
namespace Svea;

/**
 * Autoload all classes
 */

if (!defined('SVEA_REQUEST_DIR'))
    define('SVEA_REQUEST_DIR', dirname(__FILE__));

include_once(SVEA_REQUEST_DIR . "/WebPay.php");

foreach (glob(SVEA_REQUEST_DIR . "/BuildOrder/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/BuildOrder/Validator/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/BuildOrder/RowBuilders/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/HostedRequests/Payment/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/HostedRequests/HandleOrder/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/HostedRequests/Helper/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebServiceRequests/GetAddresses/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebServiceRequests/GetPaymentPlanParams/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebServiceRequests/HandleOrder/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebServiceRequests/Helper/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebServiceRequests/Payment/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/Response/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/Response/WebServiceResponse/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/Response/WebServiceResponse/CustomerIdentity/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/Response/WebServiceResponse/CampaignCode/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/Response/HostedResponse/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/Config/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/Constant/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/Helper/*.php") as $config)
    include_once($config);
