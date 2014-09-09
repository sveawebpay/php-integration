<?php
namespace Svea\WebService;

require_once 'WebServicePayment.php';

/**
 * Extends WebServicePayment. Creates Invoice order.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
*/
class InvoicePayment extends WebServicePayment {

    public $orderType;

    public function __construct($order) {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
        parent::__construct($order);
    }

    public function setOrderType($orderInformation) {
        $orderInformation->AddressSelector = isset($this->order->customerIdentity->addressSelector) ? $this->order->customerIdentity->addressSelector : "";
        $orderInformation->OrderType = $this->orderType;
        return $orderInformation;
    }
}
