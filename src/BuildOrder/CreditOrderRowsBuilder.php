<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';


/**
 * CreditOrderRowsBuilder is used to credit individual order rows in an order. 
 * (I.e. partially credit an order).
 * 
 * For Invoice and Payment Plan orders, the order row status of the order is updated
 * to reflect the credited order rows.
 * 
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 * 
 * Use creditOrderRow() or creditOrderRows() to specify the credit order row(s).
 * 
 * Then use either creditInvoiceOrderRows() or creditPaymentPlanOrderRows(), or 
 * creditCardOrderRows() or creditDirectBankOrderRows(), which ever matches
 * the payment method used in the original order request.
 * 
 * The final doRequest() will send the creditOrderRows request to Svea, and the 
 * resulting response code specifies the outcome of the request. 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditOrderRowsBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;
    
    /** @var OrderRows[] $orderRows */
    public $orderRows;

    public function __construct($config) {
         $this->conf = $config;
         $this->orderRows = array();
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
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }
    /** @var string $countryCode */
    public $countryCode;

    /**
     * Required.
     * @param string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE
     * @return $this
     */
    public function setOrderType($orderTypeAsConst) {
        $this->orderType = $orderTypeAsConst;
        return $this;
    }
    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE */
    public $orderType;    

    /**
     * Required.
     * @param OrderRow $row
     * @return $this
     */
    public function creditOrderRow( $row ) {
        $this->orderRows[] = $row;
        return $this;
    }    
    
    /**
     * Convenience method to credit several rows at once.
     * @param OrderRow[] $rows
     * @return $this
     */
    public function creditOrderRows( $rows ) {
        array_merge( $this->orderRows, $rows );
        return $this;
    }    

    /**
     * Use creditInvoiceOrderRows() to credit rows to an Invoice order using AdminServiceRequest CreditOrderRows request
     * @return CreditOrderRowsRequest 
     */
    public function creditInvoiceOrderRows() {
        $this->setOrderType(\ConfigurationProvider::INVOICE_TYPE );
        return new CreditOrderRowsRequest($this);
    }
    /**
     * Use creditPaymentPlanOrderRows() to credit rows to a PaymentPlan order using AdminServiceRequest CreditOrderRows request
     * @return CreditOrderRowsRequest 
     */
    public function creditPaymentPlanOrderRows() {
        $this->setOrderType(\ConfigurationProvider::PAYMENTPLAN_TYPE );
        return new CreditOrderRowsRequest($this);
    }    
}
