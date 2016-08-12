<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\Config\SveaConfigurationProvider; 
use Svea\WebPay\BuildOrder\Validator\ValidationException; 

/**
 * Class PaymentAdminOrderBuilder
 * @package Svea
 */
class PaymentAdminOrderBuilder
{
    /**
     * @var string $orderId
     */
    public $orderId;

    /**
     * @var SveaConfigurationProvider
     */
    public $conf;

    /**
     * @var string $countryCode
     */
    public $countryCode;

    /**
     * PaymentAdminOrderBuilder constructor.
     * @param $configProvider
     */
    public function __construct($configProvider)
    {
        $this->conf = $configProvider;
    }

     

    /**
     * @param $clientNumber
     */
    private function setClientNumberConfigurationValue($clientNumber)
    {
        $this->conf->setClientNumber($clientNumber);
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString)
    {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * Optional -- alias for setOrderId().
     * @param string $transactionIdAsString
     * @return $this
     */
    public function setTransactionId($transactionIdAsString)
    {
        return $this->setOrderId($transactionIdAsString);
    } 
    
}
