<?php
namespace Svea;

/**
 * Autoload all classes for building svea-soap object
 */

foreach (glob(SVEA_REQUEST_DIR . "/WebServiceRequests/svea_soap/*.php") as $config)
    include_once($config);
