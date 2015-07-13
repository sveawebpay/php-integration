<?php
namespace Svea\AdminService\AdminSoap;

class NumberedOrderRow {
    //Webservice
    public $ArticleNumber;
    public $Description;
    public $DiscountPercent;
    public $CreditInvoiceId;
    public $NumberOfUnits;
    public $PriceIncludingVat;
    public $PricePerUnit;
    public $Unit;
    public $VatPercent;
    //Admin.service
    public $InvoiceId;
    public $Status;
    public $RowNumber;




    function __construct( $articleNumber, $description, $discountPercent, $quantity, $amountExVat, $unit, $vatPercent,
                          $creditInvoiceId, $invoiceId, $rowNumber,$priceIncludingVat) {

        //Webservice
        $this->ArticleNumber = new \SoapVar( $articleNumber, XSD_STRING,
                "-","--","ArticleNumber","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->Description = new \SoapVar( $description, XSD_STRING,
                "-","--","Description","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->DiscountPercent = new \SoapVar( $discountPercent, XSD_DECIMAL,
                "-","--","DiscountPercent","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->NumberOfUnits = new \SoapVar( $quantity, XSD_DECIMAL,
                "-","--","NumberOfUnits","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->PriceIncludingVat = new \SoapVar( $priceIncludingVat, XSD_BOOLEAN,
                "-","--","VatPercent","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->PricePerUnit = new \SoapVar( $amountExVat, XSD_DECIMAL,
                "-","--","PricePerUnit","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->Unit = new \SoapVar( $unit, XSD_STRING,
                "-","--","Unit","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->VatPercent = new \SoapVar( $vatPercent, XSD_DECIMAL,
                "-","--","VatPercent","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");

        //admin.service
        if( !isset($creditInvoiceId) ) {
            unset( $this->CreditInvoiceId ); // nullable attributes should not be included in soap xml if not set, so unset them
        }
        else {
            $this->CreditInvoiceId = new \SoapVar( $creditInvoiceId, XSD_LONG,
                    "-","--","CreditInvoiceId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        }
        if( !isset($invoiceId) ) {
            unset( $this->InvoiceId );
        }
        else {
            $this->InvoiceId = new \SoapVar( $invoiceId, XSD_LONG,
                    "-","--","InvoiceId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        }
        $this->RowNumber = new \SoapVar( $rowNumber, XSD_LONG,
                "-","--","RowNumber","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
     
        }
        // Status -- optional, not sent by package
}