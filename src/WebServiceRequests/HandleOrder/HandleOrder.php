<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/WebServiceRequests/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Parent of CloseOrder, DeliverInvoice, DeliverPaymentPlan
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class HandleOrder {

    /** CloseOrderBuilder|DeliverOrderBuilder $handler  object containing the settings for the HandleOrder request */
    public $orderBuilder;

    /**
     * @param CloseOrderBuilder|DeliverOrderBuilder $handleOrderBuilder
     */
    public function __construct($handleOrderBuilder) {
        $this->orderBuilder = $handleOrderBuilder;
    }

    /** 
     * creates a SveaAuth object using the passed orderBuilder configuration
     * @return \Svea\SveaAuth
     */
    protected function getStoreAuthorization() {
        return new SveaAuth( 
                    $this->orderBuilder->conf->getUsername($this->orderBuilder->orderType,  $this->orderBuilder->countryCode),
                    $this->orderBuilder->conf->getPassword($this->orderBuilder->orderType,  $this->orderBuilder->countryCode),
                    $this->orderBuilder->conf->getClientNumber($this->orderBuilder->orderType,  $this->orderBuilder->countryCode)
                )
        ;
    }

    public function validateRequest() {
        $validator = new HandleOrderValidator();
         $errors = $validator->validate($this->orderBuilder);
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
        $orderInformation = new SveaDeliverOrderInformation($this->orderBuilder->orderType);
        $orderInformation->SveaOrderId = $this->orderBuilder->orderId;
        $orderInformation->OrderType = $this->orderBuilder->orderType;

        if ($this->orderBuilder->orderType == "Invoice") {
            $invoiceDetails = new SveaDeliverInvoiceDetails();
            $invoiceDetails->InvoiceDistributionType = $this->orderBuilder->distributionType;
            $invoiceDetails->IsCreditInvoice = isset($this->orderBuilder->invoiceIdToCredit) ? TRUE : FALSE;
            if (isset($this->orderBuilder->invoiceIdToCredit)) {
                $invoiceDetails->InvoiceIdToCredit = $this->orderBuilder->invoiceIdToCredit;
            }
            $invoiceDetails->NumberOfCreditDays = isset($this->orderBuilder->numberOfCreditDays) ? $this->orderBuilder->numberOfCreditDays : 0;
            $formatter = new WebServiceRowFormatter($this->orderBuilder);
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
        $url = $this->orderBuilder->conf->getEndPoint($this->orderBuilder->orderType);
        $request = new SveaDoRequest($url);
        $svea_req = $request->DeliverOrderEu($object);

        $response = new \SveaResponse($svea_req,"");
        return $response->response;
    }
}
