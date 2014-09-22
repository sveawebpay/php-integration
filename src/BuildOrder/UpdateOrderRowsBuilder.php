<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Update order rows in a non-delivered invoice or payment plan order. 
 * (Card and Direct Bank orders are not supported.)
 * 
 * For Invoice and Payment Plan orders, the order row status of the order is updated
 * to reflect the added order rows. If the updated rows order total exceeds the 
 * original order total, an error is returned by the service. 
 * 
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 * 
 * Use updateOrderRow() or updateOrderRows() to specify the order row(s) to update in the order. 
 * The supplied order row numbers must match order rows from the original createOrder request.
 * 
 * Then use either updateInvoiceOrderRows() or updatePaymentPlanOrderRows(), 
 * which ever matches the payment method used in the original order request.
 * 
 * The final doRequest() will send the updateOrderRows request to Svea, and the 
 * resulting response code specifies the outcome of the request. 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class UpdateOrderRowsBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;
    
    /** @var NumberedOrderRows[] $numberedOrderRows  the updated order rows */
    public $numberedOrderRows;
    
    public function __construct($config) {
         $this->conf = $config;
         $this->numberedOrderRows = array();
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
    /** string $orderId  Svea order id to query, as returned in the createOrder request response, either a transactionId or a SveaOrderId */
    public $orderId;
    
    /**
     * Required. Use same countryCode as in createOrder request.
     * @param string $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }
    /** @var string $countryCode */
    public $countryCode;

    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE */
    public $orderType;    

    /**
     * Required.
     * @param NumberedOrderRow $row
     * @return $this
     */
    public function updateOrderRow( $row ) {
        $this->numberedOrderRows[] = $row;
        return $this;
    }    
    
    /**
     * Convenience method to add several rows at once.
     * @param NumberedOrderRow[] $rows
     * @return $this
     */
    public function updateOrderRows( $rows ) {
        array_merge( $this->numberedOrderRows, $rows );
        return $this;
    }    

    /**
     * Use updateInvoiceOrderRows() to update an Invoice order using AdminServiceRequest UpdateOrderRows request
     * @return UpdateOrderRowsRequest 
     */
    public function updateInvoiceOrderRows() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
        return new AdminService\UpdateOrderRowsRequest($this);
    }
    
    /**
     * Use updatePaymentPlanOrderRows() to update a PaymentPlan order using AdminServiceRequest UpdateOrderRows request
     * @return UpdateOrderRowsRequest 
     */
    public function updatePaymentPlanOrderRows() {
        $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        return new AdminService\UpdateOrderRowsRequest($this);
    }
}