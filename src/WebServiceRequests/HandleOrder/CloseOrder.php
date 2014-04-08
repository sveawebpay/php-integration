<?php
namespace Svea;

require_once 'HandleOrder.php';

/**
 * Cancel undelivered Invoice or PaymentPlan orders.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrder extends HandleOrder {

    public $orderType;

    /**
     * @param CloseOrderBuilder $closeOrderBuilder
     */
    public function __construct($closeOrderBuilder) {
        parent::__construct($closeOrderBuilder);
    }

    /**
     * Returns prepared request
     * @return \SveaRequest
     */
    public function prepareRequest() {
        $this->orderType = $this->handler->orderType;
        $sveaCloseOrder = new SveaCloseOrder;
        $sveaCloseOrder->Auth = $this->getStoreAuthorization();
        $orderInfo = new SveaCloseOrderInformation();
        $orderInfo->SveaOrderId = $this->handler->orderId;
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
        $object = $this->prepareRequest();
        $url = $this->handler->conf->getEndPoint($this->orderType);
        $request = new SveaDoRequest($url);
        $svea_req = $request->CloseOrderEu($object);

        $response = new \SveaResponse($svea_req,"");
        return $response->response;
    }
}
