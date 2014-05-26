<?php
namespace Svea\AdminSoap;

class OrderRow {
    public $ArticleNumber;
    public $Description;
    public $DiscountPercent;
    public $NumberOfUnits;
    public $PricePerUnit;
    public $Unit;
    public $VatPercent;
    
    function __construct( $orderRow ) {
        
        // todo make sure works w/all different specifications of price/vat (i.e. no amountExVat set??
        
        $this->ArticleNumber = new \SoapVar( $orderRow->articleNumber, XSD_STRING, 
                "-","--","ArticleNumber","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->Description = new \SoapVar( $orderRow->description, XSD_STRING, 
                "-","--","Description","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->DiscountPercent = new \SoapVar( $orderRow->discountPercent, XSD_DECIMAL, 
                "-","--","DiscountPercent","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");   
        $this->NumberOfUnits = new \SoapVar( $orderRow->quantity, XSD_DECIMAL, 
                "-","--","NumberOfUnits","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");   
        $this->PricePerUnit = new \SoapVar( $orderRow->amountExVat, XSD_DECIMAL, 
                "-","--","PricePerUnit","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");   
        $this->Unit = new \SoapVar( $orderRow->unit, XSD_STRING, 
                "-","--","Unit","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");
        $this->VatPercent = new \SoapVar( $orderRow->vatPercent, XSD_DECIMAL, 
                "-","--","VatPercent","http://schemas.datacontract.org/2004/07/DataObjects.Webservice");          
        }
}