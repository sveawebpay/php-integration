<?php
namespace Svea\AdminService\AdminSoap;

class CancelOrderRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $ClientId;
    public $OrderType;
    public $SveaOrderId;
    
    /**
     * AdminService CloseOrderRequest 
     * @param Authentication $authentication
     * @param long $sveaOrderId
     * @param string $orderType -- one of [Invoice|PaymentPlan]
     * @param long $clientId
     */
    function __construct( $authentication, $sveaOrderId, $orderType, $clientId) {
        
        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT, 
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG, 
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderType = new \SoapVar( $orderType, XSD_STRING, 
                "-","--","OrderType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");        
        $this->SveaOrderId = new \SoapVar( $sveaOrderId, XSD_LONG, 
                "-","--","SveaOrderId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}