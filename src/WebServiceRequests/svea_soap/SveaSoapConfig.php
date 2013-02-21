<?php

/**
 * Autoload all classes for building svea-soap object
 * if (!defined('SVEA_DIR'))
 *     define('SVEA_DIR', dirname(__DIR__));
 * if (!defined('SVEA_REQUEST_DIR'))
 *     define('SVEA_REQUEST_DIR', dirname(__FILE__));
 */

foreach (glob(SVEA_REQUEST_DIR . "/WebServiceRequests/svea_soap/*.php") as $config)
    include_once($config);

?>