<?php

namespace Svea\WebPay\Checkout\Service\Admin;

use Svea\WebPay\BuildOrder\CheckoutAdminOrderBuilder;
use Svea\WebPay\BuildOrder\DeliverOrderRowsBuilder;

class DeliverOrderService extends AdminImplementationService
{
    /**
     * @var DeliverOrderRowsBuilder $adminBuilder
     */
    public $adminBuilder;

    /**
     * @var bool $isDeliverOrderRows
     */
    protected $isDeliverOrderRows;

    /**
     * DeliverOrderService constructor.
     * @param CheckoutAdminOrderBuilder $adminBuilder
     * @param bool $isDeliverOrderRows
     */
    public function __construct(CheckoutAdminOrderBuilder $adminBuilder, $isDeliverOrderRows = false)
    {
        parent::__construct($adminBuilder);
        $this->isDeliverOrderRows = $isDeliverOrderRows;
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

        if ($this->isDeliverOrderRows === true) {
            $orderRowIds = $this->adminBuilder->rowsToDeliver;
            if (!is_array($orderRowIds) || count($orderRowIds) === 0) {
                $errors['incorrect Order Row Ids'] = "Order Row Ids must be array of integers!";
            }
            foreach ($orderRowIds as $orderRowId) {
                if (empty($orderRowId) || !is_int($orderRowId)) {
                    $errors['incorrect Order Row Id'] = "Order Row Id can't be empty and must be Integer";
                }
            }
        }

        $this->processErrors($errors);
    }

    /**
     * Format given date so that will match data structure required for Admin API
     * @return mixed|void
     */
    public function prepareRequest()
    {
        $this->validate();

        $requestData = array(
            'orderId' => $this->adminBuilder->orderId
        );

        $orderRowIds = array();
        if ($this->isDeliverOrderRows === true) {
            $orderRowIds = $this->adminBuilder->rowsToDeliver;
        }
        $requestData['orderRowIds'] = $orderRowIds;

        return $requestData;
    }

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $preparedData = $this->prepareRequest();
        $response = $this->checkoutAdminConnection->deliverOrder($preparedData);

        return $response;
    }
}
