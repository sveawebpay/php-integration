<?php

namespace Svea\WebPay\AdminService\AdminSoap;

use SoapVar;

class CancelPaymentPlanRowsRequest
{
    public $Authentication;     // note that the order of the attributes matter!
    public $CancellationRows;
    public $ClientId;
    public $ContractNumber;

    /**
     * AdminService CreditInvoiceRowsRequest
     *
     * @param Authentication $authentication
     * @param CancellationRows $newCancellationRows
     * @param string $clientId
     * @param ContractNumber $contractNumber
     */
    function __construct($authentication, $newCancellationRows, $clientId, $contractNumber)
    {
        $this->Authentication = new SoapVar($authentication, SOAP_ENC_OBJECT,
            "-", "--", "Authentication", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        if (count($newCancellationRows) > 0) {
            $this->CancellationRows = new SoapVar($newCancellationRows, SOAP_ENC_OBJECT,
                "-", "--", "NewCancellationRows", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        } else {
            unset($this->NewCancellationRows);
        }

        $this->ClientId = new SoapVar($clientId, XSD_LONG,
            "-", "--", "ClientId", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->ContractNumber = new SoapVar($contractNumber, XSD_LONG,
            "-", "--", "ContractNumber", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");

    }
}
