<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\Config\SveaConfigurationProvider;
use Svea\WebPay\Checkout\Model\CheckoutSubsystemInfo;
use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\Checkout\Config\CheckoutConfigurationProvider;
use Svea\WebPay\Checkout\Service\Connection\CheckoutServiceConnection;

/**
 * Class CheckoutAdminOrderBuilder
 * @package Svea
 */
class CheckoutAdminOrderBuilder
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
     * CheckoutAdminOrderBuilder constructor.
     * @param $configProvider
     */
    public function __construct($configProvider)
    {
        $this->conf = $configProvider;
    }

    /**
     * Use Svea orderId to get Checkout subsystem information
     * Set SveaOrderId instead of checkoutOrderId and override clientId in configuration with subsystem clientId
     *
     * @param $sveaOrderId
     * @return CheckoutSubsystemInfo
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     * @throws \Exception
     */
    public function processCheckoutOrderInformation($sveaOrderId)
    {
        if (empty($sveaOrderId)) {
            throw new ValidationException('CheckoutOrderId is required!, use setCheckoutOrderId()');
        }

        $checkoutServiceConnection = new CheckoutServiceConnection($this->conf, $this->countryCode);

        try {
            $subsystemInfo = $checkoutServiceConnection->getCheckoutSubsystemInfo($sveaOrderId);
        } catch (\Exception $ex) {
            throw new  \Exception($ex->getMessage());
        }

        $confProvider = $this->conf;
        $checkoutConfigurationProvider = new CheckoutConfigurationProvider($confProvider->conf);
        $this->conf = $checkoutConfigurationProvider;

        $this->orderId = $subsystemInfo->getSveaOrderId();
        $this->setClientNumberConfigurationValue($subsystemInfo->getClientId());

        $transactionId = $subsystemInfo->getTransactionId();
        if (!empty($transactionId)) {
            $this->setTransactionId($transactionId);
        }

        return $subsystemInfo;
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

    /**
     * Optional -- alias for setOrderId().
     * @param string $checkoutOrderId
     * @return $this
     */
    public function setCheckoutOrderId($checkoutOrderId)
    {
        return $this->setOrderId($checkoutOrderId);
    }
}
