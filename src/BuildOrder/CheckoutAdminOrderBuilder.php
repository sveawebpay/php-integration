<?php

namespace Svea\WebPay\BuildOrder;

/**
 * Class CheckoutAdminOrderBuilder
 * @package Svea
 *
 * CheckoutAdminOrderBuilder is the class used as helper to create input data for Checkout Admin functions
 *
 * Use setCheckoutOrderId() to specify the Svea Checkout order id, this is the order id returned
 * with the original create Checkout order request response.
 *
 * Use setDeliveryId() to specify the delivery Id. This delivery id is returned on delivery checkout order request
 *
 * @author Savo Garovic for Svea WebPay
 */
class CheckoutAdminOrderBuilder extends OrderBuilder
{
    /**
     * @var string $orderId
     */
    public $orderId;

    /**
     * @var integer $deliveryId
     */
    public $deliveryId;

    public $amountIncVat;

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
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
     * @param mixed $deliveryId
     * @return $this
     */
    public function setDeliveryId($deliveryId)
    {
        $this->deliveryId = $deliveryId;
        return $this;
    }

    /**
     * @param mixed $amountIncVat
     * @return $this
     */
    public function setAmountIncVat($amountIncVat)
    {
        $this->amountIncVat = $amountIncVat;
        return $this;
    }
}
