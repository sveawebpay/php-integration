<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * CancelOrderBuilder is the class used to cancel an order with Svea, that has
 * not yet been delivered (invoice, payment plan) or been confirmed (card).
 * 
 * setOrderId() specifies the Svea order id to cancel, this must be the order id
 * returned with the create order doRequest response.
 * 
 * usePaymentMethod() specifies the payment method used when creating the order.
 *
 * doRequest() will send the cancelOrder request to Svea, and the resulting 
 * response specifies the outcome of the request. 
 * 
 * @TODO give response outcome details here
 * 
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class CancelOrderBuilder {

    /** ConfigurationProvider $conf  */
    public $conf;
    /** string $countryCode */
    public $countryCode;

    /** string $orderId  Svea order id to cancel, as returned in the createOrder request response, either a transactionId or a SveaOrderId */
    public $orderId;

    public function __construct($config) {
         $this->conf = $config;
    }

    /**
     * Required. The id of the order to cancel.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }
    
    /**
     * Required. The countryCode of the order to cancel. 
     * @todo check why this is needed, and see if can be done away with?
     * @param string $countryAsString
     * @return $this
     */
    public function setCountryCode($countryAsString) {
        $this->countryCode = $countryAsString;
        return $this;
    }
    
    /**
     * Required. The payment method used when placing the createOrder request.
     * @param string $orderIdAsString
     * @return $this
     */
    public function usePaymentMethod( $paymentMethod ) {
        switch( $paymentMethod ) {
            case \PaymentMethod::INVOICE:
                $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
                return new CloseOrder($this);
            break;
            
            case \PaymentMethod::PAYMENTPLAN:
                $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
                return new CloseOrder($this);
            break;
            
            case \PaymentMethod::KORTCERT:
                $this->orderType = \ConfigurationProvider::HOSTED_ADMIN_TYPE;
                 $annulTransaction = new AnnulTransaction($this->conf);
                 return $annulTransaction->setTransactionId($this->orderId)->setCountryCode($this->countryCode);
            break;
        
            default:
                // TODO error handling here + tests
            break;
                
        }
        
        return $this;
    }

}
