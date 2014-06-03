<?php
namespace Svea\AdminService\AdminSoap;

class OrdersToRetrieve {
    public $GetOrderInformation;
    
    /**
     * AdminService OrdersToRetrieve 
     * @param GetOrderInformation $getOrderInformation
     */
    function __construct( $getOrderInformation ) {
        
        $this->GetOrderInformation = new \SoapVar( $getOrderInformation, SOAP_ENC_OBJECT, 
                "-","--","GetOrderInformation","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}
