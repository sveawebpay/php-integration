<?php
namespace Svea\AdminService\AdminSoap;

class GetOrdersRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $OrdersToRetrieve;
    
    /**
     * AdminService GetOrdersRequest 
     * @param Authentication $authentication
     * @param OrdersToRetrieve $ordersToRetrieve
     */
    function __construct( $authentication, $ordersToRetrieve) {
        
        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT, 
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        $this->OrdersToRetrieve = new \SoapVar( $ordersToRetrieve, SOAP_ENC_OBJECT, 
                "-","--","OrdersToRetrieve","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}