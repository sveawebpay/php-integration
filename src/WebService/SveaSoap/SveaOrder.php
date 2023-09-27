<?php

namespace Svea\WebPay\WebService\SveaSoap;

/**
 * Order object
 */
class SveaOrder
{
    public $Auth;
    public $CreateOrderInformation;
    
    /**
     * Navigation
     *
     * @var \Svea\WebPay\WebService\SveaSoap\SveaNavigation
     */
    public $Navigation;
}
