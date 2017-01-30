<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\CancelOrderBuilder;
use Svea\WebPay\BuildOrder\CheckoutAdminOrderBuilder;
use Svea\WebPay\Helper\Helper;

class CancelOrderService extends AdminImplementationService
{
    /**
     * @var CancelOrderBuilder $adminBuilder
     */
    public $adminBuilder;

    /**
     * @var bool $isCancelAmount
     */
    protected $isCancelAmount;

    /**
     * CancelOrderService constructor.
     * @param CheckoutAdminOrderBuilder $adminBuilder
     * @param bool $isCancelAmount
     */
    public function __construct(CheckoutAdminOrderBuilder $adminBuilder, $isCancelAmount = false)
    {
        parent::__construct($adminBuilder);
        $this->isCancelAmount = $isCancelAmount;
    }

    /**
     * Validate order data
     */
    public function validate()
    {
        $errors = array();

        $orderId = $this->adminBuilder->orderId;
        if (empty($orderId) || !is_int($orderId)) {
            $errors['incorrect Order Id'] = "Order Id can't be empty and must be Integer";
        }

        if ($this->isCancelAmount === true) {
            $amount = $this->adminBuilder->amountIncVat;
            if (empty($amount) || (!is_float($amount) && !is_int($amount))) {
                $errors['incorrect Amount for cancel order'] = "Amount can't be empty and must be Integer";
            }
        }

        $this->processErrors($errors);
    }

    /**
     * Format given date so that will match data structure required for Admin API
     * @return mixed
     */
    public function prepareRequest()
    {
        $this->validate();

        $requestData = array(
            'orderId' => $this->adminBuilder->orderId
        );

        if ($this->isCancelAmount === true) {
            $amount = $this->adminBuilder->amountIncVat;
            $minorCurrencyAmount = Helper::bround($amount, 2) * 100;
            $requestData['cancelledAmount'] = intval((string)$minorCurrencyAmount);;
        }

        return $requestData;
    }

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $preparedData = $this->prepareRequest();
        if ($this->isCancelAmount === true) {
            $response = $this->checkoutAdminConnection->cancelOrderAmount($preparedData);
        } else {
            $response = $this->checkoutAdminConnection->cancelOrder($preparedData);
        }

        return $response;
    }
}
