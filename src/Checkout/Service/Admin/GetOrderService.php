<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\QueryOrderBuilder;

class GetOrderService extends AdminImplementationService
{
    /**
     * @var QueryOrderBuilder $adminBuilder
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

        return $requestData;
    }

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $preparedData = $this->prepareRequest();
        $response = $this->checkoutAdminConnection->getOrder($preparedData);

        return $response;
    }
}
