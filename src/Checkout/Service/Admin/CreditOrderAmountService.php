<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\CreditAmountBuilder;
use Svea\WebPay\Helper\Helper;

class CreditOrderAmountService extends AdminImplementationService
{
    /**
     * @var CreditAmountBuilder $adminBuilder
     */
    public $adminBuilder;
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

        $deliveryId = $this->adminBuilder->deliveryId;
        if (empty($deliveryId) || !is_int($deliveryId)) {
            $errors['incorrect Delivery Id'] = "Delivery Id can't be empty and must be Integer";
        }

        $creditAmount = $this->adminBuilder->amountIncVat;
        if (empty($creditAmount) || !is_numeric($creditAmount)) {
            $errors['incorrect Credit Amount'] = "Credit amount can't be empty and must be number";
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
            'orderId' => $this->adminBuilder->orderId,
            'deliveryId' => $this->adminBuilder->deliveryId
        );
        $amount = $this->adminBuilder->amountIncVat;
        $minorCreditAmount = Helper::bround($amount, 2) * 100;
        $requestData['creditedAmount'] = intval((string)$minorCreditAmount);

        return $requestData;
    }

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $preparedData = $this->prepareRequest();
        $response = $this->checkoutAdminConnection->creditOrderAmount($preparedData);

        return $response;
    }
}
