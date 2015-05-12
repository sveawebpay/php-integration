<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * HostedRequest is the parent of hosted webservice (admin) requests.
 * 
 * @author Kristian Grossman-Madsen
 */
abstract class HostedRequest {

    /** @var ConfigurationProvider $config */
    protected $config;

    /** @var string $method  set by the subclass, defines what webservice is called (including payment) */
    protected $method;

    /** @var string $countryCode  used to disambiguate between the various credentials in ConfigurationProvider */
    public $countryCode; 
        
    /** 
     * @param ConfigurationProvider $config
     */
    function __construct($config) {
        $this->config = $config;
    }
    
    /**
     * Validates the request to make sure that all required request attributes 
     * are present. If not, throws an exception. Actual validation is delegated 
     * to subclass validateAttributes() implementations.
     *
     * @throws \Svea\ValidationException
     */
    public function validateRequest() {

        // validate subclass request required attributes
        $errors = $this->validateRequestAttributes();
        
        // validate countrycode
        $errors = $this->validateCountryCode($this, $errors );
        
        if (count($errors) > 0) {
            $exceptionString = "";
            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }

            throw new \Svea\ValidationException($exceptionString);
        }    
    }           

    private function validateCountryCode($self, $errors) {
        if(isset($this->countryCode) == FALSE ) {
            $errors['missing value'] = 'CountryCode is required. Use function setCountryCode().';                                    
        }
        return $errors;
    }      
    
    /**
     * returns the request fields to post to service
     */
    public function prepareRequest() {
        $this->validateRequest();

        $xmlBuilder = new HostedXmlBuilder();
        
        // get our merchantid & secret
        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
        
        $message = $this->createRequestXml();        
        
        // calculate mac
        $mac = hash("sha512", base64_encode($message) . $secret);
        
        // encode the request elements
        $request_fields = array( 
            'merchantid' => urlencode($merchantId),
            'message' => urlencode(base64_encode($message)),
            'mac' => urlencode($mac)
        );
        return $request_fields;
    }    
    
    /**
     * Performs a request using cURL, parsing the response using SveaResponse 
     * and returning the resulting HostedAdminResponse instance.
     * 
     * @return HostedAdminResponse
     */
    public function doRequest(){
        $fields = $this->prepareRequest();
        
        $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->getEndpoint( \Svea\SveaConfigurationProvider::HOSTED_ADMIN_TYPE). $this->method);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //force curl to trust https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //returns a html page with redirecting to bank...
        $responseXML = curl_exec($ch);
        curl_close($ch);
        
        // create SveaResponse to handle response
        $responseObj = new \SimpleXMLElement($responseXML); 
        return $this->parseResponse( $responseObj );
    }
    
    /**
     * implemented by child classes, should validate that all required attributes for the method are present
     */
    abstract protected function validateRequestAttributes();
    
    /**
     * implemented by child classes, should return the request xml for the method (i.e. "message" in the HostedAdminRequest request wrapper)
     */
    abstract protected function createRequestXml();    
    
    /**
     * implemented by child classes, should return the request response class for the method
     */
    abstract protected function parseResponse( $response );
}