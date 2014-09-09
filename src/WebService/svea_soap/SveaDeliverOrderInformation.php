<?php
namespace Svea\WebService\WebServiceSoap;

class SveaDeliverOrderInformation {

    public $SveaOrderId;
    public $OrderType;

    public function __construct($orderType) {
        if ($orderType == \ConfigurationProvider::INVOICE_TYPE) {
            $this->DeliverInvoiceDetails = "";
        }
    }
}
