<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class closeOrderBuilder {

    /**
     * Order Id recieved when creating order
     * @var Order id
     */
    public $orderId;
    /**
     * @var type String "Invoice" or "PaymentPlan"
     */
    public $orderType;
    /**
     * @var Instance of class SveaConfig
     */
    public $conf;
    public $countryCode;

    public function __construct($config) {
        $this->handleValidator = new HandleOrderValidator();
         $this->conf = $config;
    }

    /**
     * Required
     * @param type $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }

    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    public function closeInvoiceOrder() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
        return new CloseOrder($this);
    }

    public function closePaymentPlanOrder() {
        $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        return new CloseOrder($this);
    }
}
