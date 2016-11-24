<?php

namespace Svea\WebPay\Checkout\Model;

class CheckoutSubsystemInfo
{
    /**
     * @var string $sveaOrderId
     */
    protected $sveaOrderId;

    /**
     * @var string $transactionId
     */
    protected $transactionId;

    /**
     * @var string $clientId
     */
    protected $clientId;

    /**
     * @var string $paymentType
     */
    protected $paymentType;

    /**
     * CheckoutSubsystemInfo constructor.
     * @param array $subsystemData
     */
    public function __construct(array $subsystemData)
    {
        $this->sveaOrderId = $subsystemData['SveaOrderId'];
        $this->transactionId = $subsystemData['TransactionId'];
        $this->clientId = $subsystemData['ClientId'];
        $this->paymentType = $subsystemData['PaymentType'];
    }

    /**
     * @return string
     */
    public function getSveaOrderId()
    {
        return $this->sveaOrderId;
    }

    /**
     * @param string $sveaOrderId
     */
    public function setSveaOrderId($sveaOrderId)
    {
        $this->sveaOrderId = $sveaOrderId;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }
}
