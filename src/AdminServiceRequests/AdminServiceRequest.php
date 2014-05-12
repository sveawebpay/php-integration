<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * AdminServiceRequest is the parent of all admin webservice requests.
 * 
 * @author Kristian Grossman-Madsen
 */
abstract class AdminServiceRequest {

    /** @var string $action  the AdminService soap action called by this class */
    protected $action; 

    /** @var string $countryCode */
    protected $countryCode; 
          
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
     * Validates the orderBuilder object to make sure that all required settings
     * are present. If not, throws an exception. Actual validation is delegated
     * to subclass validate() implementations.
     *
     * @throws ValidationException
     */
    public function validateRequest() {
        // validate sub-class requirements by calling sub-class validate() method
        $errors = $this->validate();
        
        if (count($errors) > 0) {
            $exceptionString = "";
            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }

            throw new ValidationException($exceptionString);
        }    
    }       

    abstract function validate(); // validate is defined by subclasses, should validate all elements required for call is present
}
