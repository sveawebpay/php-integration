<?php

namespace Svea\WebPay\Checkout\Service\Connection;

use Svea\Checkout\CheckoutClient;
use Svea\Checkout\Transport\Connector;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\Checkout\Response\CheckoutResponseHelper;

/**
 * Class CheckoutServiceConnection
 * @package Svea\Svea\WebPay\WebPay\Checkout\Service\Connection
 */
class CheckoutServiceConnection implements ServiceConnection
{
    /**
     * @var \Svea\WebPay\Config\ConfigurationProvider
     */
    protected $config;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @var CheckoutClient
     */
    protected $checkoutClient;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * CheckoutServiceConnection constructor.
     * @param        $config
     * @param string $countryCode
     */
    public function __construct(ConfigurationProvider $config, $countryCode)
    {
        $this->config      = $config;
        $this->countryCode = $countryCode;

        $this->setConnector();
        $this->setClient();
    }


    private function setConnector()
    {
        $this->connector = Connector::init(
            $this->config->getCheckoutMerchantId(strtolower($this->countryCode)),
            $this->config->getCheckoutSecret(strtolower($this->countryCode)),
            $this->config->getEndPoint(ConfigurationProvider::CHECKOUT)
        );
    }

    private function setClient()
    {
        $this->checkoutClient = new CheckoutClient($this->connector);
    }

    /**
     * @param mixed $requestData
     * @return mixed
     */
    public function create($requestData)
    {
        return CheckoutResponseHelper::processData($this->checkoutClient->create($requestData));
    }

    /**
     * @param mixed $requestData
     * @return mixed
     */
    public function get($requestData)
    {
        return CheckoutResponseHelper::processData($this->checkoutClient->get($requestData));
    }

    /**
     * @param mixed $requestData
     * @return mixed
     */
    public function update($requestData)
    {
        $data = $this->checkoutClient->update($requestData);

        return CheckoutResponseHelper::processData($data);
    }

    /**
     * @param mixed $requestData
     * @return mixed
     */
    public function getAvailablePartPaymentCampaigns($requestData)
    {
        return CheckoutResponseHelper::processData($this->checkoutClient->getAvailablePartPaymentCampaigns($requestData));
    }
}
