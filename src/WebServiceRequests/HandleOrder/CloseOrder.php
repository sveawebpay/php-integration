<?php
namespace Svea;

require_once 'HandleOrder.php';

/**
 * Cancel undelivered Invoice or PaymentPlan orders.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrder extends HandleOrder {

    /**
     * @param CloseOrderBuilder $closeOrderBuilder
     */
    public function __construct($closeOrderBuilder) {
        parent::__construct($closeOrderBuilder);
    }

    /**
     * Returns prepared closeOrder request
     * @return \SveaRequest
     */
    public function prepareRequest() {
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

    /**
     * Prepare and sends request
     * @return type CloseOrderEuResponse
     */
    public function doRequest() {
        $requestObject = $this->prepareRequest();
        $url = $this->orderBuilder->conf->getEndPoint($this->orderBuilder->orderType);
        $request = new SveaDoRequest($url);
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
            $errors['missing value'] = "OrderId is required. Use function setOrderId() with the SveaOrderIdd fromn the createOrder response.";
        }
        return $errors;
    }  
}
