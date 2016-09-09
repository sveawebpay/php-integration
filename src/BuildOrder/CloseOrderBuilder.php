<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\WebService\HandleOrder\CloseOrder;

/**
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrderBuilder
{
    /**
     * @var ConfigurationService
     */
    public $conf;

    /**
     * @var ConfigurationProvider::INVOICE_TYPE|ConfigurationProvider::PAYMENTPLAN_TYPE
     */
    public $orderType;

    /**
     * @var string $orderId
     */
    public $orderId;

    /**
     * @var string $countryCode
     */
    public $countryCode;

    /**
     * CloseOrderBuilder constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->conf = $config;
    }

    /**
     * Required. Use SveaOrderId recieved with createOrder response.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString)
    {
        $this->orderId = $orderIdAsString;

        return $this;
    }

    /**
     * @param string $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString)
    {
        $this->countryCode = $countryCodeAsString;

        return $this;
    }

    /**
     * Use closeInvoiceOrder() to close an Invoice order.
     * @return CloseOrder
     */
    public function closeInvoiceOrder()
    {
        $this->orderType = ConfigurationProvider::INVOICE_TYPE;

        return new CloseOrder($this);
    }

    /**
     * Use closePaymentPlanOrder() to close a PaymentPlan order.
     * @return CloseOrder
     */
    public function closePaymentPlanOrder()
    {
        $this->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;

        return new CloseOrder($this);
    }
}
