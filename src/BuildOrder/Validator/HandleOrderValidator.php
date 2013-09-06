<?php
namespace Svea;

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebServiceRequests/Helper
 */
class HandleOrderValidator {

    public $errors = array();
    
    /**
     * @param type $order
     */
    public function validate($order) {
        $this->errors = $this->validateOrderId($order, $this->errors);
        $this->errors = $this->validateInvoiceDetails($order, $this->errors);
        $this->errors = $this->validateOrderRows($order, $this->errors);
        return $this->errors;
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
