<?php
namespace Svea\AdminService\AdminSoap;

class DeliverPartialRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $InvoiceDistributionType;
    public $OrderToDeliver;
    public $RowNumbers;
    
    /**
     * AdminService DeliverPartialRequest 
     * 
     * @param Authentication $authentication
     * @param string $invoiceDistributionType
     * @param OrderToDeliver $orderToDeliver
     * @param string[] $orderRowNumbers
     */
    function __construct( $authentication, $invoiceDistributionType, $orderToDeliver, $orderRowNumbers ) {
        
        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT, 
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        $this->InvoiceDistributionType = new \SoapVar( $invoiceDistributionType, XSD_STRING, 
                "-","--","InvoiceDistributionType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");   
        $this->OrderToDeliver = new \SoapVar( $orderToDeliver, SOAP_ENC_OBJECT, 
                "-","--","OrdersToDeliver","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");               
        $this->RowNumbers = new \SoapVar( $orderRowNumbers, SOAP_ENC_OBJECT, 
                "-","--","RowNumbers","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");  
    }
}