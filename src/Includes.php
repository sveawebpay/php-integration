<?php
namespace Svea;

/**
 * Autoload all classes
 */

if (!defined('SVEA_REQUEST_DIR'))
    define('SVEA_REQUEST_DIR', dirname(__FILE__));

include_once(SVEA_REQUEST_DIR . "/WebPay.php");
include_once(SVEA_REQUEST_DIR . "/WebPayAdmin.php");
include_once(SVEA_REQUEST_DIR . "/WebPayItem.php");

foreach (glob(SVEA_REQUEST_DIR . "/BuildOrder/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/BuildOrder/Validator/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/BuildOrder/RowBuilders/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/Config/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/Constant/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/Helper/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/AdminService/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/AdminService/AdminServiceResponse/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/AdminService/admin_soap/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/HostedService/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/HostedService/HostedAdminRequest/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/HostedService/Helper/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/HostedService/Payment/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/HostedService/HostedResponse/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/HostedService/HostedResponse/HostedAdminResponse/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/HostedService/HostedResponse/HostedPaymentResponse/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/WebService/GetAddresses/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebService/GetPaymentPlanParams/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebService/HandleOrder/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebService/Helper/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebService/Payment/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/WebService/WebServiceResponse/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebService/WebServiceResponse/CustomerIdentity/*.php") as $config)
    include_once($config);
foreach (glob(SVEA_REQUEST_DIR . "/WebService/WebServiceResponse/CampaignCode/*.php") as $config)
    include_once($config);

foreach (glob(SVEA_REQUEST_DIR . "/Response/*.php") as $config)
    include_once($config);

