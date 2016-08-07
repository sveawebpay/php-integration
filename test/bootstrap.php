<?php

$loader = include dirname(__DIR__) . '/vendor/autoload.php';

$loader->addPsr4('Svea\\WebPay\\Test\\', __DIR__ . '/');
date_default_timezone_set('UTC');
