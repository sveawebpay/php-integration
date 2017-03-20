<?php

namespace Svea\WebPay\AdminService\AdminSoap;

use SoapVar;

class CancelAccountCreditRowsRequest
{
    public $Authentication;     // note that the order of the attributes matter!
    public $CancellationRows;
    public $ClientAccountCreditId;
    public $ClientId;

    /**
     * AdminService CreditInvoiceRowsRequest
     *
     * @param Authentication $authentication
     * @param CancellationRows $newCancellationRows
     * @param string $clientId
     * @param ClientAccountCreditId $contractNumber
     */
    function __construct($authentication, $newCancellationRows, $clientId, $orderId)
    {
        $this->Authentication = new SoapVar($authentication, SOAP_ENC_OBJECT,
            "-", "--", "Authentication", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        if (count($newCancellationRows) > 0) {
            $this->CancellationRows = new SoapVar($newCancellationRows, SOAP_ENC_OBJECT,
                "-", "--", "NewCancellationRows", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service.Requests");
        } else {
            unset($this->NewCancellationRows);
        }

        $this->ClientId = new SoapVar($clientId, XSD_LONG,
            "-", "--", "ClientId", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service.Requests");
        $this->ClientAccountCreditId = new SoapVar($orderId, XSD_LONG,
            "-", "--", "ClientAccountCreditId", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service.Requests");

    }
}