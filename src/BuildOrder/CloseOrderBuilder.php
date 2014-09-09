<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CloseOrderBuilder {
    
    public function __construct($config) {
        $this->conf = $config;
    }
    /** @var Instance of class SveaConfig */
    public $conf;

    /**
     * Required. Use SveaOrderId recieved with createOrder response.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }
    /** @var string $orderId  */
    public $orderId;
    
    /**
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
     * Use closeInvoiceOrder() to close an Invoice order.
     * @return CloseOrder
     */
    public function closeInvoiceOrder() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
        return new WebService\CloseOrder($this);
    }
    
    /**
     * Use closePaymentPlanOrder() to close a PaymentPlan order.
     * @return CloseOrder
     */
    public function closePaymentPlanOrder() {
        $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        return new WebService\CloseOrder($this);
    }
    
    /** @var string  \ConfigurationProvider::INVOICE_TYPE or ::PAYMENTPLAN_TYPE */
    public $orderType;
    
}
