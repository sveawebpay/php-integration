<?php
namespace Svea\AdminService\AdminSoap;

class OrderRow {
    public $ArticleNumber;
    public $Description;
    public $DiscountPercent;
    public $NumberOfUnits;
    public $PriceIncludingVat;
    public $PricePerUnit;
    public $Unit;
    public $VatPercent;


    function __construct( $articleNumber, $description, $discountPercent, $quantity, $amount, $unit, $vatPercent, $priceIncludingVat ) {

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
        $this->PricePerUnit = new \SoapVar( $amount, XSD_DECIMAL,
                "-","--","PricePerUnit","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->Unit = new \SoapVar( $unit, XSD_STRING,
                "-","--","Unit","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->VatPercent = new \SoapVar( $vatPercent, XSD_DECIMAL,
                "-","--","VatPercent","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");

        }
}