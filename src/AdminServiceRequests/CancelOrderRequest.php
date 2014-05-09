<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service CancelOrderRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class CancelOrderRequest extends AdminServiceRequest {

    /** string $action  the AdminService action modelled by this class */
    private $action; 
    
    // TODO mocked for now with a StdClass object containing attributes, will operate like other orderBuilder objects
    public $orderBuilder;    

    /**
     * @param type $orderBuilder
     */
    public function __construct($cancelOrderBuilder) {
        $this->action = "CancelOrder";
        $this->orderBuilder = $cancelOrderBuilder;
    }
    
    
    /**
     * Prepare and send request to Svea admin service using AdminSoap helpers
     * @return StdClass  raw response @todo
     */
    public function doRequest() {
        $soapRequest = $this->prepareRequest();
        
        $soapClient = new AdminSoap\SoapClient( $this->orderBuilder->conf->endpoint );
        $response = $soapClient->doSoapCall($this->action, $soapRequest );
        return $response;        
    }
    
    /**
     * populate and return soap request contents
     * @return Svea\AdminSoap\CancelOrderRequest
     */    
    public function prepareRequest() {

        $soapRequest = new AdminSoap\CancelOrderRequest( 
                new AdminSoap\Authentication( 
                    $this->orderBuilder->conf->username, 
                    $this->orderBuilder->conf->password 
                ),
                $this->orderBuilder->sveaOrderId, 
                $this->orderBuilder->orderType,
                $this->orderBuilder->conf->clientId 
        );
        
        return $soapRequest;
    }
}    
    
