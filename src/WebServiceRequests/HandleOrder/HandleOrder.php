<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/WebServiceRequests/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Parent of CloseOrder, DeliverInvoice, DeliverPaymentPlan
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebServiceRequests/HandleOrder
 */
class HandleOrder {

    public $handler;

    /**
     * @param type $handler
     */
    public function __construct($handler) {
        $this->handler = $handler;
    }

    protected function getStoreAuthorization() {
        $auth = new SveaAuth();
         $auth->Username = $this->handler->conf->getUsername($this->handler->orderType,  $this->handler->countryCode);
        $auth->Password = $this->handler->conf->getPassword($this->handler->orderType,  $this->handler->countryCode);
        $auth->ClientNumber = $this->handler->conf->getClientNumber($this->handler->orderType,  $this->handler->countryCode);
        return $auth;
    }

    public function validateRequest() {
        $validator = new HandleOrderValidator();
         $errors = $validator->validate($this->handler);
         return $errors;
    }

    /**
     * Returns prepared request
     * @return \SveaRequest
     */
    public function prepareRequest() {
        $errors = $this->validateRequest();
        if (count($errors) > 0) {
            $exceptionString = "";
            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }

            throw new ValidationException($exceptionString);
        }
        $sveaDeliverOrder = new SveaDeliverOrder;
        $sveaDeliverOrder->Auth = $this->getStoreAuthorization();
        $orderInformation = new SveaDeliverOrderInformation($this->handler->orderType);
        $orderInformation->SveaOrderId = $this->handler->orderId;
        $orderInformation->OrderType = $this->handler->orderType;

        if ($this->handler->orderType == "Invoice") {
            $invoiceDetails = new SveaDeliverInvoiceDetails();
            $invoiceDetails->InvoiceDistributionType = $this->handler->distributionType;
            $invoiceDetails->IsCreditInvoice = isset($this->handler->invoiceIdToCredit) ? TRUE : FALSE;
            if (isset($this->handler->invoiceIdToCredit)) {
                $invoiceDetails->InvoiceIdToCredit = $this->handler->invoiceIdToCredit;
            }
            $invoiceDetails->NumberOfCreditDays = isset($this->handler->numberOfCreditDays) ? $this->handler->numberOfCreditDays : 0;
            $formatter = new WebServiceRowFormatter($this->handler);
            $orderRow['OrderRow'] = $formatter->formatRows();
            $invoiceDetails->OrderRows = $orderRow;
            $orderInformation->DeliverInvoiceDetails = $invoiceDetails;
        }

        $sveaDeliverOrder->DeliverOrderInformation = $orderInformation;
        $object = new SveaRequest();
        $object->request = $sveaDeliverOrder;
        return $object;
    }

    /**
     * Prepare and sends request
     * @return type CloseOrderEuResponse
     */
    public function doRequest() {
        $object = $this->prepareRequest();
        $url = $this->handler->conf->getEndPoint($this->handler->orderType);
        $request = new SveaDoRequest($url);
        $svea_req = $request->DeliverOrderEu($object);

        $response = new \SveaResponse($svea_req,"");
        return $response->response;
    }
}
