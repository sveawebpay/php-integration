<?php
namespace Svea\WebService;

require_once 'HandleOrder.php';

/**
 * Cancel undelivered Invoice or PaymentPlan orders.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrder extends HandleOrder {

    /**
     * @param CloseOrderBuilder $CloseOrderBuilder
     */
    public function __construct($CloseOrderBuilder) {
        parent::__construct($CloseOrderBuilder);
    }

    /**
     * Returns prepared closeOrder request
     * @return Svea\WebService\WebServiceSoap\SveaRequest
     */
    public function prepareRequest() {
        $this->validateRequest();
                
        $sveaCloseOrder = new WebServiceSoap\SveaCloseOrder;
        $sveaCloseOrder->Auth = $this->getStoreAuthorization();
        $orderInfo = new WebServiceSoap\SveaCloseOrderInformation();
        $orderInfo->SveaOrderId = $this->orderBuilder->orderId;
        $sveaCloseOrder->CloseOrderInformation = $orderInfo;

        $object = new WebServiceSoap\SveaRequest();
        $object->request = $sveaCloseOrder;

        return $object;
    }

    /**
     * Prepare and sends request
     * @return type CloseOrderEuResponse
     */
    public function doRequest() {
        $requestObject = $this->prepareRequest();
        $request = new WebServiceSoap\SveaDoRequest($this->orderBuilder->conf, $this->orderBuilder->orderType);
        $response = $request->CloseOrderEu($requestObject);
        $responseObject = new \SveaResponse($response,"");
        return $responseObject->response;
    }
    
    public function validate($order) {
        $errors = array();
        $errors = $this->validateOrderId($order, $errors);
        return $errors;
    }

    private function validateOrderId($order, $errors) {
        if (isset($order->orderId) == FALSE) {
            $errors['missing value'] = "OrderId is required. Use function setOrderId() with the SveaOrderId from the createOrder response.";
        }
        return $errors;
    }  
}
