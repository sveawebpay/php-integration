<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * CancelOrderBuilder is the class used to cancel an order with Svea, that has
 * not yet been delivered (invoice, payment plan) or been confirmed (card).
 * 
 * Use setOrderId() to specify the Svea order id, this is the order id returned 
 * with the original create order request response.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 * 
 * Use either cancelInvoiceOrder(), cancelPaymentPlanOrder or cancelCardOrder,
 * which ever matches the payment method used in the original order request.
 *  
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CancelOrderBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;

    /** @var string $orderId  Svea order id to cancel, as returned in the createOrder request response, either a transactionId or a SveaOrderId */
    public $orderId;
    
    public function __construct($config) {
         $this->conf = $config;
    }

    /**
     * Required for invoice or part payment orders -- use the order id (transaction id) recieved with the createOrder response.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }

    /**
     * Optional -- alias for setOrderId().
     * @param string $transactionIdAsString
     * @return $this
     */
    public function setTransactionId($transactionIdAsString) {
        return $this->setOrderId($transactionIdAsString);
    }         
        
    /**
     * Required. Use same country code as in createOrder request.
     * 
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
     * Use cancelInvoiceOrder() to close an Invoice order.
     * 
     * Use the method corresponding to the original createOrder payment method.
     * 
     * @return WebService\CloseOrder
     */
    public function cancelInvoiceOrder() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
        return new WebService\CloseOrder($this);
    }
    
    /**
     * Use cancelPaymentPlanOrder() to close a PaymentPlan order.
     * 
     * Use the method corresponding to the original createOrder payment method.
     * 
     * @return WebService\CloseOrder
     */
    public function cancelPaymentPlanOrder() {
        $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        return new WebService\CloseOrder($this);    
    }
    
    /** @var string  \ConfigurationProvider::INVOICE_TYPE or ::PAYMENTPLAN_TYPE */
    public $orderType;  

    /**
     * Use cancelCardOrder() to close a Card order.
     * 
     * Use the method corresponding to the original createOrder payment method.
     * 
     * @return HostedService\AnnulTransaction
     */
    public function cancelCardOrder() {
        $this->orderType = \ConfigurationProvider::HOSTED_ADMIN_TYPE;
        $annulTransaction = new HostedService\AnnulTransaction($this->conf);
        $annulTransaction->transactionId = $this->orderId;
        $annulTransaction->countryCode = $this->countryCode;
        return $annulTransaction;
    }  
}
