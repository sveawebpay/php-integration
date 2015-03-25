<?php
namespace Svea\WebService;

require_once 'HandleOrder.php';

/**
 * DeliverInvoice sets up a DeliverOrderEU request, using information from the
 * provided DeliverOrderBuilder object. If all provided order rows match the
 * order rows from the corresponding createOrderEU request the order is invoiced
 * in full by Svea. If not, the order will be partially delivered. See further
 * the Svea Web Service EU API.
 *
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverInvoice extends HandleOrder {

    /**
     * @param DeliverOrderBuilder $DeliverOrderBuilder
     */
    public function __construct($DeliverOrderBuilder) {
        $DeliverOrderBuilder->orderType = \ConfigurationProvider::INVOICE_TYPE;

        parent::__construct($DeliverOrderBuilder);
    }

    /**
     * Returns prepared request
     * @return WebServiceSoap\SveaRequest
     */
    public function prepareRequest($priceIncludingVat = NULL) {
        $errors = $this->validateRequest();

        $sveaDeliverOrder = new WebServiceSoap\SveaDeliverOrder;
        $sveaDeliverOrder->Auth = $this->getStoreAuthorization();
        $orderInformation = new WebServiceSoap\SveaDeliverOrderInformation($this->orderBuilder->orderType);
        $orderInformation->SveaOrderId = $this->orderBuilder->orderId;
        $orderInformation->OrderType = $this->orderBuilder->orderType;

        if ($this->orderBuilder->orderType == \ConfigurationProvider::INVOICE_TYPE) {
            $invoiceDetails = new WebServiceSoap\SveaDeliverInvoiceDetails();
            $invoiceDetails->InvoiceDistributionType = $this->orderBuilder->distributionType;
            $invoiceDetails->IsCreditInvoice = isset($this->orderBuilder->invoiceIdToCredit) ? TRUE : FALSE;    // required
            if (isset($this->orderBuilder->invoiceIdToCredit)) {
                $invoiceDetails->InvoiceIdToCredit = $this->orderBuilder->invoiceIdToCredit;                    // optional
            }
            $invoiceDetails->NumberOfCreditDays = isset($this->orderBuilder->numberOfCreditDays) ? $this->orderBuilder->numberOfCreditDays : 0;
            $formatter = new WebServiceRowFormatter($this->orderBuilder,$priceIncludingVat);
            $orderRow['OrderRow'] = $formatter->formatRows();
            $invoiceDetails->OrderRows = $orderRow;
            $orderInformation->DeliverInvoiceDetails = $invoiceDetails;
        }

        $sveaDeliverOrder->DeliverOrderInformation = $orderInformation;
        $object = new WebServiceSoap\SveaRequest();
        $object->request = $sveaDeliverOrder;
        return $object;
    }

    /**
     * Prepare and sends request
     * @return DeliverOrderResult
     */
    public function doRequest() {
        $requestObject = $this->prepareRequest();
        $priceIncludingVat =  $requestObject->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat;
        $request = new WebServiceSoap\SveaDoRequest($this->orderBuilder->conf, $this->orderBuilder->orderType);
        $response = $request->DeliverOrderEu($requestObject);
        $responseObject = new \SveaResponse($response,"");
        if ($responseObject->response->resultcode == "50036") {
            $requestObject = $this->prepareRequest($priceIncludingVat);
            $request = new WebServiceSoap\SveaDoRequest($this->orderBuilder->conf,$this->orderBuilder->orderType);
            $response = $request->DeliverOrderEu($requestObject);
            $responseObject = new \SveaResponse($response,"");
        }
        return $responseObject->response;
    }

    public function validate($order) {
        $errors = array();
        $errors = $this->validateCountryCode($order, $errors);
        $errors = $this->validateOrderId($order, $errors);
        $errors = $this->validateInvoiceDetails($order, $errors);
        $errors = $this->validateOrderRows($order, $errors);
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

    private function validateInvoiceDetails($order, $errors) {
        if (isset($order->orderId) && $order->orderType == \ConfigurationProvider::INVOICE_TYPE && isset($order->distributionType) == FALSE) {
            $errors['missing value'] = "InvoiceDistributionType is required for deliverInvoiceOrder. Use function setInvoiceDistributionType().";
        }
        return $errors;
    }

    private function validateOrderRows($order, $errors) {
        if ($order->orderType == \ConfigurationProvider::INVOICE_TYPE && empty($order->orderRows) && empty($order->shippingFee) && empty($order->invoiceFee)) {
            $errors['missing values'] = "No rows has been included. Use function beginOrderRow(), beginShippingfee() or beginInvoiceFee().";
        }
        return $errors;
    }




}
