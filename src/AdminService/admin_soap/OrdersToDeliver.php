<?php
namespace Svea\AdminService\AdminSoap;

class OrdersToDeliver {
    public $DeliverOrderInformation;
    
    /**
     * AdminService OrdersToDeliver 
     * @param DeliverOrderInformation $deliverOrderInformation
     */
    function __construct( $deliverOrderInformation ) {
        
        $this->DeliverOrderInformation = new \SoapVar( $deliverOrderInformation, SOAP_ENC_OBJECT, 
                "-","--","DeliverOrderInformation","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}