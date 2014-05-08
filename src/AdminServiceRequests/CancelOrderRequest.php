<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service CancelOrderRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class CancelOrderRequest extends AdminServiceRequest {
    
    // TODO mocked for now with a StdClass object containing attributes, will operate like other orderBuilder objects
    public $orderBuilder;    

    /**
     * @param type $orderBuilder
     */
    public function __construct($cancelOrderBuilder) {
        $this->orderBuilder = $cancelOrderBuilder;
    }
    
    
    /**
     * Prepare and send request to Svea admin service
     * @return CloseOrderEuResponse
     */
    public function doRequest() {
        $soapRequestObject = $this->prepareRequest();

        // TODO change to get from orderBuilder->conf instead
        $url = "https://partnerweb.sveaekonomi.se/WebPayAdminService_Test/AdminService.svc/backward";
        
        $soapclient = new \SoapClient(
            null,
            array(
                'location' => $url,
                'uri' => "http://tempuri.org/",
                'use' => SOAP_LITERAL,    
                'exceptions'=> 1,
                'connection_timeout' => 60,
                'trace' => 1,
                'soap_version' => SOAP_1_1
            )
        );

        $action = "CancelOrder";       
        try {
            $response = $soapclient->__soapCall( $action, array( $soapRequestObject ), 
                array( "soapaction" => 'http://tempuri.org/IAdminService/'.$action )
            );
        }
        catch( \SoapFault $e ) {
            echo "SoapFault Exception: ";
            echo $soapclient->__getLastRequest() . "\n";
            echo $soapclient->__getLastRequestHeaders();
        };
        
        return $response;        
    }
    
    /**
     * populate and return a soap request object
     * @return \StdClass
     */    
    public function prepareRequest() {
       
        $authentication = new Authentication( $this->orderBuilder->conf->username, 
                                              $this->orderBuilder->conf->password );
        
        $soapRequest = new SoapCancelOrderRequest(  $authentication, 
                                                    $this->orderBuilder->sveaOrderId, 
                                                    $this->orderBuilder->orderType,
                                                    $this->orderBuilder->conf->clientId );
        
        return new \SoapVar( $soapRequest, SOAP_ENC_OBJECT, "-", "--", "request", "http://tempuri.org/");
    }
}    
    
class Authentication {
    public $Password;
    public $Username;
    
    function __construct( $password, $username ) {
        $this->Password = new \SoapVar( $password, XSD_STRING,"-","--","Password","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->Username = new \SoapVar( $username, XSD_STRING,"-","--","Username","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}

class SoapCancelOrderRequest {
    public $Authentication;     // note that the order of the attributes matter!
    public $ClientId;
    public $OrderType;
    public $SveaOrderId;
    
    function __construct( $authentication, $sveaOrderId, $orderType, $clientId) {
        
        $this->Authentication = new \SoapVar( $authentication, SOAP_ENC_OBJECT, 
                "-","--","Authentication","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->ClientId = new \SoapVar( $clientId, XSD_LONG, 
                "-","--","ClientId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
        $this->OrderType = new \SoapVar( $orderType, XSD_STRING, 
                "-","--","OrderType","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");        
        $this->SveaOrderId = new \SoapVar( $sveaOrderId, XSD_LONG, 
                "-","--","SveaOrderId","http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service");
    }
}