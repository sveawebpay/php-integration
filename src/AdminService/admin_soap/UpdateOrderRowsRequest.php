<?php
namespace Svea\AdminService\AdminSoap;

class UpdateOrderRowsRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $ClientId;
    public $OrderType;
    public $SveaOrderId;
    public $UpdatedOrderRows;    
    /**
     * AdminService AddOrderRowsRequest 
     * @param Authentication $authentication
     * @param string $clientId
     * @param string $orderType
     * @param string $sveaOrderId
     * @param NumberedOrderRows $updatedOrderRows;
     */
    function __construct( $authentication, $clientId, $orderType, $sveaOrderId, $updatedOrderRows ) {
        
        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT, 
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG, 
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderType = new \SoapVar( $orderType, XSD_STRING,
                "-","--","OrderType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->SveaOrderId = new \SoapVar( $sveaOrderId, XSD_LONG,
                "-","--","SveaOrderId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->UpdatedOrderRows = new \SoapVar( $updatedOrderRows, SOAP_ENC_OBJECT, 
                "-","--","UpdatedOrderRows","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}