<?php
/**
 * classes that support building SOAP requests
 */
namespace Svea\AdminService\AdminSoap;

class AddOrderRowsRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $ClientId;
    public $OrderRows;
    public $OrderType;
    public $SveaOrderId;
    
    /**
     * AdminService AddOrderRowsRequest 
     * @param Authentication $authentication
     * @param string $clientId
     * @param OrderRows $orderRows
     * @param string $orderType
     * @param string $sveaOrderId
     */
    function __construct( $authentication, $clientId, $orderRows, $orderType, $sveaOrderId ) {
        
        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT, 
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG, 
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderRows = new \SoapVar( $orderRows, SOAP_ENC_OBJECT, 
                "-","--","OrderRows","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderType = new \SoapVar( $orderType, XSD_STRING,
                "-","--","OrderType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->SveaOrderId = new \SoapVar( $sveaOrderId, XSD_LONG,
                "-","--","SveaOrderId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}