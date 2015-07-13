<?php
namespace Svea\AdminService\AdminSoap;

class GetOrderInformation {
    public $ClientId;
    public $SveaOrderId;
    
    /**
     * AdminService GetOrderInformation 
     * @param long $clientId
     * @param long $sveaOrderId
     */
    function __construct( $clientId, $sveaOrderId ) {
        
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG, 
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // OrderType -- optional, not sent by package
        $this->SveaOrderId = new \SoapVar( $sveaOrderId, XSD_LONG, 
                "-","--","SveaOrderId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}