<?php
namespace Svea\WebService\WebServiceSoap;

/**
 * Include in SveaDeliverOrderInformation for Invoice only
 */
class SveaDeliverInvoiceDetails {

    ////Already set at Svea, but needs to be included in array
    public $NumberOfCreditDays = "";
    //Post or Email
    public $InvoiceDistributionType;
    //Can leave blank
    public $IsCreditInvoice = "";
    public $InvoiceIdToCredit;
    //If order not changed, set with orderRowarray from CreateOrderEu
    public $OrderRows = array();

    public function __construct() {
        $this->OrderRows['OrderRow'] = array();
    }

    //only use if order is changed
    public function addOrderRow($orderRow) {
        array_push($this->OrderRows['OrderRow'], $orderRow);
    }
}
