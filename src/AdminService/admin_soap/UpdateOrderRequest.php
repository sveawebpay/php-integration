<?php
namespace Svea\AdminService\AdminSoap;

class UpdateOrderRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $ClientId;
    public $ClientOrderNumber;
    public $Notes;
    public $OrderType;
    public $SveaOrderId;

    /**
     * @param Authentication $authentication
     * @param string $clientId
     * @param string $orderType
     * @param string $sveaOrderId
     */
    function __construct( $authentication, $clientId, $orderType, $sveaOrderId, $clientOrderNumber, $notes ) {

        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT,
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        // Settings -- optional, not sent by package
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG,
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderType = new \SoapVar( $orderType, XSD_STRING,
                "-","--","OrderType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->SveaOrderId = new \SoapVar( $sveaOrderId, XSD_LONG,
                "-","--","SveaOrderId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->ClientOrderNumber = new \SoapVar( $clientOrderNumber, XSD_STRING,
                "-","--","ClientOrderNumber","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->Notes = new \SoapVar( $notes, XSD_STRING,
                "-","--","Notes","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}