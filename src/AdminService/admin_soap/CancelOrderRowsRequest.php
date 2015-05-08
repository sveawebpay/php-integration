<?php
namespace Svea\AdminService\AdminSoap;

class CancelOrderRowsRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $ClientId;
    public $OrderRowNumbers;
    public $OrderType;
    public $SveaOrderId;
    
    /**
     * AdminService CancelOrderRowsRequest 
     * @param Authentication $authentication
     * @param OrdersToRetrieve $ordersToRetrieve
     */
    function __construct( $authentication, $clientId, $orderRowNumbers, $orderType, $sveaOrderId ) {
        
        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT, 
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG, 
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderRowNumbers = new \SoapVar( $orderRowNumbers, SOAP_ENC_OBJECT, 
                "-","--","OrderRowNumbers","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderType = new \SoapVar( $orderType, XSD_STRING,
                "-","--","OrderType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->SveaOrderId = new \SoapVar( $sveaOrderId, XSD_LONG,
                "-","--","SveaOrderId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}