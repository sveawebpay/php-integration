<?php

/*
 *
 * This file is used if you want to autoload project classes without using Composer
 * */


/**
 * Return desired class
 * @param string $className
 */
function __autoload_svea_php_integration_library_classes($className)
{ 
    $filename = str_replace('Svea\\WebPay\\', '', $className);
    $fullPath = str_replace('\\', '/', __DIR__ . '\\' . $filename . ".php");

    if (file_exists($fullPath)) {
        include_once $fullPath;
    }
}

spl_autoload_register('__autoload_svea_php_integration_library_classes');
