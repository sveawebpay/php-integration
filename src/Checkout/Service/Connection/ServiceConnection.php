<?php

namespace Svea\WebPay\Checkout\Service\Connection;

/**
 * Interface ServiceConnection
 * @package Svea\Svea\WebPay\WebPay\Checkout\Service\Connection
 */
interface ServiceConnection
{
    public function create($requestData);
    public function get($requestData);
    public function update($requestData);
    public function getCheckoutSubsystemInfo($requestData);
}
