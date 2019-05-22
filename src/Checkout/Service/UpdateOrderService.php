<?php

namespace Svea\WebPay\Checkout\Service;

use Svea\WebPay\Checkout\Helper\CheckoutOrderBuilder;
use Svea\WebPay\Checkout\Validation\UpdateOrderValidator;

/**
 * Class UpdateOrderService
 * @package Svea\Svea\WebPay\WebPay\Checkout\Service
 */
class UpdateOrderService extends CheckoutService
{
    public $requestObject;

    /**
     * Send call Connection Library
     */
    public function doRequest()
    {
        $requestData = $this->prepareRequest();
        
        $response = $this->serviceConnection->update($requestData);

        return $response;
    }

    /**
     * Process Order for request data for Svea Checkout API
     * @return array
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    protected function prepareRequest()
    {
        $errors = $this->validateOrder();
        $this->processErrors($errors);
        $data = $this->mapCreateOrderData($this->order);

        return $data;
    }

    /**
     * Validate order data
     */
    protected function validateOrder()
    {
        $validator = new UpdateOrderValidator();
        $errors = $validator->validate($this->order);

        return $errors;
    }

    /**
     * Map Order to array
     * @param CheckoutOrderBuilder $order
     * @return array
     */
    protected function mapCreateOrderData(CheckoutOrderBuilder $order)
    {
        $data = array();
        $orderItems = $this->formatOrderInformationWithOrderRows();

        foreach ($orderItems as $item) {
            $data['cart']['items'][] = $this->mapOrderItem($item);
        }

        $data['orderId'] = $order->getId();
        $data['merchantData'] = $order->getMerchantData();
        return $data;
    }
}
