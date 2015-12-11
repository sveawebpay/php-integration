<?php
namespace Svea\AdminService\AdminSoap;

class CancelPaymentPlanAmountRequest {
    public $Authentication;
    public $AmountInclVat;     // note that the order of the attributes matter!
    public $ContractNumber;
    public $ClientId;
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
    function __construct( $authentication, $amountInclVat, $description, $clientId, $contractNumber ) {

        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT,
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->AmountInclVat = new \SoapVar( $amountInclVat, SOAP_ENC_OBJECT,
                "-","--","AmountInclVat","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->ContractNumber = new \SoapVar( $contractNumber, XSD_LONG,
                "-","--","ContractNumber","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG,
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->Description = new \SoapVar( $description, SOAP_ENC_OBJECT,
              "-","--","Description","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");

    }
}