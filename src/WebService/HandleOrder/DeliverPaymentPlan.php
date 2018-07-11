<?php

namespace Svea\WebPay\WebService\HandleOrder;

use Svea\WebPay\Response\SveaResponse;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\DeliverOrderBuilder;
use Svea\WebPay\WebService\SveaSoap\SveaRequest;
use Svea\WebPay\WebService\SveaSoap\SveaDoRequest;
use Svea\WebPay\WebService\SveaSoap\SveaDeliverOrder;
use Svea\WebPay\WebService\WebServiceResponse\CloseOrderResult;
use Svea\WebPay\WebService\SveaSoap\SveaDeliverOrderInformation;

/**
 * Update Created PaymentPlanorder with additional information and prepare it for delivery.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DeliverPaymentPlan extends HandleOrder
{
    /**
     * @param DeliverOrderBuilder $order
     */
    public function __construct($order)
    {
        $order->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;
        parent::__construct($order);
    }

    /**
     * Prepare and sends request
     * @return CloseOrderResult
     */
    public function doRequest()
    {
        $requestObject = $this->prepareRequest();
        $request = new SveaDoRequest($this->orderBuilder->conf, $this->orderBuilder->orderType, "DeliverOrderEu", $requestObject, $this->orderBuilder->logging);
        $responseObject = new SveaResponse($request->result['requestResult'], "", NULL, NULL, isset($request->result['logs']) ? $request->result['logs'] : NULL);

        return $responseObject->response;
    }

    /**
     * Returns prepared request
     * @return SveaRequest
     */
    public function prepareRequest()
    {
        $errors = $this->validateRequest();

        $sveaDeliverOrder = new SveaDeliverOrder;
        $sveaDeliverOrder->Auth = $this->getStoreAuthorization();
        $orderInformation = new SveaDeliverOrderInformation($this->orderBuilder->orderType);
        $orderInformation->SveaOrderId = $this->orderBuilder->orderId;
        $orderInformation->OrderType = $this->orderBuilder->orderType;

        $sveaDeliverOrder->DeliverOrderInformation = $orderInformation;
        $object = new SveaRequest();
        $object->request = $sveaDeliverOrder;

        return $object;
    }

    public function validate($order)
    {
        $errors = array();
        $errors = $this->validateCountryCode($order, $errors);
        $errors = $this->validateOrderId($order, $errors);

        return $errors;
    }

    private function validateCountryCode($order, $errors)
    {
        if (isset($order->countryCode) == FALSE) {
            $errors['missing value'] = "CountryCode is required. Use function setCountryCode().";
        }

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
