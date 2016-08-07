<?php

namespace Svea\WebPay\WebService\HandleOrder;

use Svea\WebPay\Response\SveaResponse;
use Svea\WebPay\BuildOrder\CloseOrderBuilder;
use Svea\WebPay\WebService\SveaSoap\SveaRequest;
use Svea\WebPay\WebService\SveaSoap\SveaDoRequest;
use Svea\WebPay\WebService\SveaSoap\SveaCloseOrder;
use Svea\WebPay\WebService\SveaSoap\SveaCloseOrderInformation;

/**
 * Cancel undelivered Invoice or PaymentPlan orders.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrder extends HandleOrder
{
    /**
     * @param CloseOrderBuilder $CloseOrderBuilder
     */
    public function __construct($CloseOrderBuilder)
    {
        parent::__construct($CloseOrderBuilder);
    }

    /**
     * Prepare and sends request
     * @return type CloseOrderEuResponse
     */
    public function doRequest()
    {
        $requestObject = $this->prepareRequest();
        $request = new SveaDoRequest($this->orderBuilder->conf, $this->orderBuilder->orderType);
        $response = $request->CloseOrderEu($requestObject);
        $responseObject = new SveaResponse($response, "");

        return $responseObject->response;
    }

    /**
     * Returns prepared closeOrder request
     * @return SveaRequest
     */
    public function prepareRequest()
    {
        $this->validateRequest();

        $sveaCloseOrder = new SveaCloseOrder;
        $sveaCloseOrder->Auth = $this->getStoreAuthorization();
        $orderInfo = new SveaCloseOrderInformation();
        $orderInfo->SveaOrderId = $this->orderBuilder->orderId;
        $sveaCloseOrder->CloseOrderInformation = $orderInfo;

        $object = new SveaRequest();
        $object->request = $sveaCloseOrder;

        return $object;
    }

    public function validate($order)
    {
        $errors = array();
        $errors = $this->validateOrderId($order, $errors);

        return $errors;
    }

    private function validateOrderId($order, $errors)
    {
        if (isset($order->orderId) == FALSE) {
            $errors['missing value'] = "OrderId is required. Use function setOrderId() with the SveaOrderId from the createOrder response.";
        }

        return $errors;
    }
}
