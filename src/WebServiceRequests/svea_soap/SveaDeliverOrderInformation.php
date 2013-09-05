<?php
namespace Svea;

class SveaDeliverOrderInformation {

    public $SveaOrderId;
    public $OrderType;

    public function __construct($orderType) {
        if ($orderType == "Invoice") {
            $this->DeliverInvoiceDetails = "";
        }
    }
}
