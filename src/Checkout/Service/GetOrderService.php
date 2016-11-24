<?php

namespace Svea\WebPay\Checkout\Service;

use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\Checkout\Validation\GetOrderValidator;

/**
 * Class GetOrderService
 * @package Svea\Svea\WebPay\WebPay\Checkout\Service
 */
class GetOrderService extends CheckoutService
{
    /**
     * Send call Connection Library
     * @return mixed
     */
    public function doRequest()
    {
        $this->prepareRequest();

        $requestData = array(
            'orderId' => $this->order->getId()
        );
        $response = $this->serviceConnection->get($requestData);

        return $response;
    }

    /**
     * Validate order data
     * @return array|mixed
     */
    protected function validateOrder()
    {
        $validator = new GetOrderValidator();
        $errors = $validator->validate($this->order);

        return $errors;
    }

    /**
     * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    protected function prepareRequest()
    {
        $errors = $this->validateOrder();
        $this->processErrors($errors);
    }
}
