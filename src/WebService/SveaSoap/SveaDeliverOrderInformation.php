<?php

namespace Svea\WebPay\WebService\SveaSoap;

use Svea\WebPay\Config\ConfigurationProvider;

class SveaDeliverOrderInformation
{
    public $SveaOrderId;
    public $OrderType;
    public $DeliverInvoiceDetails;
    public $DeliverAccountCreditDetails;

    /**
     * SveaDeliverOrderInformation constructor.
     * @param $orderType
     */
    public function __construct($orderType)
    {
        if ($orderType == ConfigurationProvider::INVOICE_TYPE) {
            $this->DeliverInvoiceDetails = "";
        }
        else if ($orderType == ConfigurationProvider::ACCOUNTCREDIT_TYPE) {
            $this->DeliverAccountCreditDetails = "";
        }
    }
}
