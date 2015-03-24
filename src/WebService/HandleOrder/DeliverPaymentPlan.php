<?php
namespace Svea\WebService;

/**
 * Update Created PaymentPlanorder with additional information and prepare it for delivery.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DeliverPaymentPlan extends HandleOrder {

    /**
     * @param DeliverOrderBuilder $order
     */
    public function __construct($order) {
        $order->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        parent::__construct($order);
    }

    /**
     * Returns prepared request
     * @return \SveaRequest
     */
    public function prepareRequest() {
        $errors = $this->validateRequest();

        $sveaDeliverOrder = new WebServiceSoap\SveaDeliverOrder;
        $sveaDeliverOrder->Auth = $this->getStoreAuthorization();
        $orderInformation = new WebServiceSoap\SveaDeliverOrderInformation($this->orderBuilder->orderType);
        $orderInformation->SveaOrderId = $this->orderBuilder->orderId;
        $orderInformation->OrderType = $this->orderBuilder->orderType;

        $sveaDeliverOrder->DeliverOrderInformation = $orderInformation;
        $object = new WebServiceSoap\SveaRequest();
        $object->request = $sveaDeliverOrder;
        return $object;
    }        

    /**
     * Prepare and sends request
     * @return CloseOrderResult
     */
    public function doRequest() {
        $requestObject = $this->prepareRequest();
        $request = new WebServiceSoap\SveaDoRequest($this->orderBuilder->conf, $this->orderBuilder->orderType );
        $response = $request->DeliverOrderEu($requestObject);
        $responseObject = new \SveaResponse($response,"");
        return $responseObject->response;
    }        

    public function validate($order) {
        $errors = array();
        $errors = $this->validateCountryCode($order, $errors);
        $errors = $this->validateOrderId($order, $errors);
        return $errors;
    }

    private function validateCountryCode($order, $errors) {
        if (isset($order->countryCode) == FALSE) {
            $errors['missing value'] = "CountryCode is required. Use function setCountryCode().";
        }
        return $errors;
    }
    
    private function validateOrderId($order, $errors) {
        if (isset($order->orderId) == FALSE) {
            $errors['missing value'] = "OrderId is required. Use function setOrderId() with the SveaOrderId from the createOrder response.";
        }
        return $errors;
    }
}
