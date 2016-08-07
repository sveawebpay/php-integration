<?php

namespace Svea\WebPay\AdminService\AdminSoap;

use SoapVar;

class CancellationRow
{
    public $AmountInclVat;
    public $Description;
    public $RowNumber;
    public $VatPercent;

    /**
     * CancellationRow constructor.
     * @param $amount
     * @param $description
     * @param $vatPercent
     * @param null $rowNumber
     */
    function __construct($amount, $description, $vatPercent, $rowNumber = null)
    {

        $this->AmountInclVat = new SoapVar($amount, XSD_DECIMAL,
            "-", "--", "AmountInclVat", "http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->Description = new SoapVar($description, XSD_STRING,
            "-", "--", "Description", "http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        
        if ($rowNumber) {
            $this->RowNumber = new SoapVar($rowNumber, XSD_DECIMAL,
                "-", "--", "RowNumber", "http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        }
        
        $this->VatPercent = new SoapVar($vatPercent, XSD_DECIMAL,
            "-", "--", "VatPercent", "http://schemas.datacontract.org/2004/07/DataObjects.Webservice");

    }
}