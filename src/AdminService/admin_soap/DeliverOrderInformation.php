<?php
namespace Svea\AdminService\AdminSoap;

class DeliverOrderInformation {
    public $ClientId;
    public $OrderType;
    public $SveaOrderId;
    
    /**
     * AdminService DeliverOrderInformation 
     * @param long $clientId
     * @param string $orderType -- one of [Invoice|PaymentPlan]
     * @param long $sveaOrderId
     */
    function __construct( $clientId, $orderType, $sveaOrderId ) {
        
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG, 
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderType = new \SoapVar( $orderType, XSD_STRING, 
                "-","--","OrderType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->SveaOrderId = new \SoapVar( $sveaOrderId, XSD_LONG, 
                "-","--","SveaOrderId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}