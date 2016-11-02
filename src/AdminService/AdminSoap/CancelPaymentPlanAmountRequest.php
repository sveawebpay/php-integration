<?php

namespace Svea\WebPay\AdminService\AdminSoap;

use SoapVar;

class CancelPaymentPlanAmountRequest
{
    public $Authentication;
    public $AmountInclVat;     // note that the order of the attributes matter!
    public $ClientId;
    public $ContractNumber;
    public $Description;

    /**
     * AdminService CreditInvoiceRowsRequest
     *
     * @param Authentication $authentication
     * @param AmountIncVat $amountInclVat
     * @param string $description
     * @param string $clientId
     * @param ContractNumber $contractNumber
     */
    function __construct($authentication, $amountInclVat, $description, $clientId, $contractNumber)
    {
        $this->Authentication = new SoapVar($authentication, SOAP_ENC_OBJECT,
            "-", "--", "Authentication", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->AmountInclVat = new SoapVar($amountInclVat, XSD_DECIMAL,
            "-", "--", "AmountInclVat", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->ClientId = new SoapVar($clientId, XSD_LONG,
            "-", "--", "ClientId", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->ContractNumber = new SoapVar($contractNumber, XSD_LONG,
            "-", "--", "ContractNumber", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->Description = new SoapVar($description, XSD_STRING,
            "-", "--", "Description", "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}
