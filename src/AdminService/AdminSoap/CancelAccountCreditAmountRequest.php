<?php

namespace Svea\WebPay\AdminService\AdminSoap;

use SoapVar;

class CancelAccountCreditAmountRequest
{
    public $Authentication;
    public $AmountIncVat;     // note that the order of the attributes matter!
    public $ClientAccountCreditId;  // order id
    public $ClientId;
    public $Description;

    /**
     * AdminService CreditInvoiceRowsRequest
     *
     * @param Authentication $authentication
     * @param AmountIncVat $amountInclVat
     * @param string $description
     * @param string $clientId
     * @param string $orderId
     */
    function __construct($authentication, $amountIncVat, $description, $clientId, $orderId)
    {
        $regular = 'http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service';
        $request = 'http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service.Requests';

        $this->Authentication = new SoapVar($authentication, SOAP_ENC_OBJECT,
            "-", "--", "Authentication", $regular);
        $this->AmountIncVat = new SoapVar($amountIncVat, XSD_DECIMAL,
            "-", "--", "AmountInclVat", $request);
        $this->ClientAccountCreditId = new SoapVar($orderId, XSD_LONG,
            "-", "--", "ClientAccountCreditId", $request);
        $this->ClientId = new SoapVar($clientId, XSD_LONG,
            "-", "--", "ClientId", $request);
        $this->Description = new SoapVar($description, XSD_STRING,
            "-", "--", "Description", $request);
    }
}
