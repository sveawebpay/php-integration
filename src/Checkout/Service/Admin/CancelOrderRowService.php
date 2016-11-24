<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\CancelOrderRowsBuilder;

class CancelOrderRowService extends AdminImplementationService
{
    /**
     * @var CancelOrderRowsBuilder $adminBuilder
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

        $orderRowIds = $this->adminBuilder->rowsToCancel;
        if (!is_array($orderRowIds)) {
            $errors['incorrect Order Row Ids'] = "Order Row Ids must be not empty array";
        }

        if (count($orderRowIds) > 1) {
            $errors['incorrect Order Row Id'] = "You can Cancel just one Order Row";
        }

        if (is_array($orderRowIds) && count($orderRowIds) > 0) {
            $orderRowId = $orderRowIds[0];
            if (empty($orderRowId) || !is_int($orderRowId)) {
                $errors['incorrect Order Row Id'] = "Order Row Id can't be empty and must be Integer";
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

        $orderRowId = $this->adminBuilder->rowsToCancel[0];
        $requestData = array(
            'orderId' => $this->adminBuilder->orderId,
            'orderRowId' => $orderRowId
        );

        return $requestData;
    }

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $preparedData = $this->prepareRequest();
        $response = $this->checkoutAdminConnection->cancelOrderRow($preparedData);

        return $response;
    }
}
