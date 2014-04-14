<?php
namespace Svea;

require_once 'HandleOrder.php';

/**
 * Update Created Invoiceorder with additional information and prepare it for delivery.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DeliverInvoice extends HandleOrder {

    /**
     * @param DeliverOrderBuilder $deliverOrderBuilder
     */
    public function __construct($deliverOrderBuilder) {
        parent::__construct($deliverOrderBuilder);
    }
    
    /**
     * Returns prepared request
     * @return \SveaRequest
     */
    public function prepareRequest() {
        $errors = $this->validateRequest();

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
        $requestObject = $this->prepareRequest();
        $url = $this->orderBuilder->conf->getEndPoint($this->orderBuilder->orderType);
        $request = new SveaDoRequest($url);
        $response = $request->DeliverOrderEu($requestObject);
        $responseObject = new \SveaResponse($response,"");
        return $responseObject->response;
    }    


    public function validate($order) {
        $errors = array();
        $errors = $this->validateOrderId($order, $errors);
        $errors = $this->validateInvoiceDetails($order, $errors);
        $errors = $this->validateOrderRows($order, $errors);
        return $errors;
    }

    private function validateOrderId($order, $errors) {
        if (isset($order->orderId) == FALSE) {
            $errors['missing value'] = "OrderId is required. Use function setOrderId() with the id recieved when creating an order.";
        }
        return $errors;
    }

    private function validateInvoiceDetails($order, $errors) {
        if (isset($order->orderId) && $order->orderType == "Invoice" && isset($order->distributionType) == FALSE) {
            $errors['missing value'] = "InvoiceDistributionType is requred for deliverInvoiceOrder. Use function setInvoiceDistributionType().";
        }
        return $errors;
    }

    private function validateOrderRows($order, $errors) {
        if ($order->orderType == "Invoice" && empty($order->orderRows) && empty($order->shippingFee) && empty($order->invoiceFee)) {
            $errors['missing values'] = "No rows has been included. Use function beginOrderRow(), beginShippingfee() or beginInvoiceFee().";
        }
        return $errors;
    }    
    
    
}
