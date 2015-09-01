<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Update order in a non-delivered invoice or payment plan order.
 * (Card and Direct Bank orders are not supported.)
 */
class UpdateOrderBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;
     /** string $orderId  Svea order id to query, as returned in the createOrder request response, either a transactionId or a SveaOrderId */
    public $orderId;
       /** @var string $countryCode */
    public $countryCode;
    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE */
    public $orderType;
    /** @var string $clientOrderNumber */
    public $clientOrderNumber = null;
    /** @var string $notes */
    public $notes = null;

    public function __construct($config) {
         $this->conf = $config;
    }

    /**
     * Required. Use SveaOrderId recieved with createOrder response.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }

    /**
     * Required. Use same countryCode as in createOrder request.
     * @param string $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }
    /**
     * Optional. Update clientOrderNumber on order.
     * @param string $clientOrderNumberAsString
     * @return $this
     */
    public function setClientOrderNumber($clientOrderNumberAsString) {
        $this->clientOrderNumber = $clientOrderNumberAsString;
        return $this;
    }
    /**
     * Optional. Update notes on order.
     * @param string $notesAsString(200)
     * @return $this
     */
    public function setNotes($notesAsString) {
        $this->notes = $notesAsString;
        return $this;
    }

    /**
     * Use updateInvoiceOrder() to update an Invoice order using AdminServiceRequest UpdateOrder request
     * @return UpdateOrderRowsRequest
     */
    public function updateInvoiceOrder() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
        return new AdminService\UpdateOrderRequest($this);
    }

    /**
     * Use updatePaymentPlanOrder() to update a PaymentPlan order using AdminServiceRequest UpdateOrder request
     * @return UpdateOrderRequest
     */
    public function updatePaymentPlanOrder() {
        $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        return new AdminService\UpdateOrderRequest($this);
    }
}