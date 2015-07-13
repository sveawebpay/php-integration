<?php
namespace Svea\AdminService\AdminSoap;

class DeliverOrdersRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $InvoiceDistributionType;
    public $OrdersToDeliver;
    
    /**
     * AdminService DeliverOrdersRequest 
     * @param Authentication $authentication
     * @param string $invoiceDistributionType -- one of [Post|Email]
     * @param OrdersToDeliver $ordersToDeliver
     */
    function __construct( $authentication, $invoiceDistributionType, $ordersToDeliver ) {
        
        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT, 
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        $this->InvoiceDistributionType = new \SoapVar( $invoiceDistributionType, XSD_STRING, 
                "-","--","InvoiceDistributionType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");        
        $this->OrdersToDeliver = new \SoapVar( $ordersToDeliver, SOAP_ENC_OBJECT, 
                "-","--","OrdersToDeliver","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}
