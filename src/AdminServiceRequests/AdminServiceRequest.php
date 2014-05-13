<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * AdminServiceRequest is the parent of all admin webservice requests.
 * 
 * @author Kristian Grossman-Madsen
 */
abstract class AdminServiceRequest {
    
    const ADMIN_SERVICE_TEST = "https://partnerweb.sveaekonomi.se/WebPayAdminService_test/AdminService.svc/backward";   // TODO add to ConfigurationProvider

    /** @var string $action  the AdminService soap action called by this class */
    protected $action; 

    /** @var string $countryCode */
    protected $countryCode; 
          
    /**
     * Set up the soap client and perform the soap call, with the soap action and prepared request from the relevant subclass 
     * @return StdClass  raw response @todo
     */
    public function doRequest() { 
        $soapClient = new AdminSoap\SoapClient( AdminServiceRequest::ADMIN_SERVICE_TEST );
        $response = $soapClient->doSoapCall($this->action, $this->prepareRequest() );
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

    abstract function prepareRequest(); // prepare the soap request data
    
    abstract function validate(); // validate is defined by subclasses, should validate all elements required for call is present
}
