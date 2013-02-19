<?php

require_once 'WebServicePayment.php';

/**
 * Extends WebServicePayment. Creates Invoice order.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
*/
class InvoicePayment extends WebServicePayment {

    public $orderType = 'Invoice';

    public function __construct($order) {
        parent::__construct($order);
    }

    public function setOrderType($orderInformation) {
        $orderInformation->AddressSelector = ($this->order->addressSelector ? $this->order->addressSelector : "");
        $orderInformation->OrderType = $this->orderType;
        return $orderInformation;
    }
}

?>
