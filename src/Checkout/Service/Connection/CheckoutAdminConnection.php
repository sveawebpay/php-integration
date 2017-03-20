<?php

namespace Svea\WebPay\Checkout\Service\Connection;

use Svea\Checkout\CheckoutAdminClient;
use Svea\Checkout\Transport\Connector;
use Svea\WebPay\Checkout\Response\CheckoutAdminResponseHelper;
use Svea\WebPay\Config\ConfigurationProvider;

class CheckoutAdminConnection
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
     * @var CheckoutAdminClient
     */
    protected $checkoutClient;

    /**
     * CheckoutServiceConnection constructor.
     * @param $config
     */
    public function __construct(ConfigurationProvider $config)
    {
        $this->config = $config;

        $this->setConnector();
        $this->setClient();
    }

    public function getOrder($requestData)
    {
        return CheckoutAdminResponseHelper::processResponse($this->checkoutClient->getOrder($requestData));
    }

    public function deliverOrder($requestData)
    {
        return $this->checkoutClient->deliverOrder($requestData);
    }

    public function cancelOrder($requestData)
    {
        return $this->checkoutClient->cancelOrder($requestData);
    }

    public function cancelOrderAmount($requestData)
    {
        return $this->checkoutClient->cancelOrderAmount($requestData);
    }

    public function cancelOrderRow($requestData)
    {
        return $this->checkoutClient->cancelOrderRow($requestData);
    }

    /* Credit */
    public function creditOrderRows($requestData)
    {
        return $this->checkoutClient->creditOrderRows($requestData);
    }

    public function creditNewOrderRow($requestData)
    {
        return $this->checkoutClient->creditNewOrderRow($requestData);
    }

    public function creditOrderAmount($requestData)
    {
        return $this->checkoutClient->creditOrderAmount($requestData);
    }

    public function addOrderRow($requestData)
    {
        return $this->checkoutClient->addOrderRow($requestData);
    }

    public function updateOrderRow($requestData)
    {
        return $this->checkoutClient->updateOrderRow($requestData);
    }

    public function getTask($requestData)
    {
        return $this->checkoutClient->getTask($requestData);
    }

    private function setConnector()
    {
        $this->connector = Connector::init(
            $this->config->getCheckoutMerchantId(),
            $this->config->getCheckoutSecret(),
            $this->config->getEndPoint(ConfigurationProvider::CHECKOUT_ADMIN)
        );
    }

    private function setClient()
    {
        $this->checkoutClient = new CheckoutAdminClient($this->connector);
    }
}
