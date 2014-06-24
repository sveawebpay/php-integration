<?php
namespace Svea\WebService\WebServiceSoap;

/**
 * Autoload all classes for building svea-soap object
 */

foreach (glob(SVEA_REQUEST_DIR . "/WebService/svea_soap/*.php") as $config)
    include_once($config);
