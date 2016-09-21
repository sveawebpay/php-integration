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
    /* Skip other namespaces */
    if (!preg_match('#^(Svea\\\\WebPay)#', $className)) {
        return;
    }

    $filename = str_replace('Svea\\WebPay\\', '', $className);
    $fullPath = str_replace('\\', '/', __DIR__ . '\\' . $filename . ".php");

    if (is_file($fullPath)) {
        include $fullPath;
    }
}

spl_autoload_register('__autoload_svea_php_integration_library_classes', true, true);
