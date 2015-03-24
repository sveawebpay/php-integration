<?php
/**
 * Namespace Svea\WebService Implements Europe Web Service API 1.4.5.
 */
namespace Svea\WebService;

require_once SVEA_REQUEST_DIR . '/WebService/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * The following methods are deprecated starting with 2.2 of the package
 * ->setIndividual()                    (deprecated, -- lookup the address of a private individual, set to i.e. social security number)
 * ->setCompany()                       (deprecated -- lookup the addresses associated with a legal entity (i.e. company)
 * ->setOrderTypeInvoice()              (deprecated -- supply the method that corresponds to the account credentials used for the address lookup)
 * ->setOrderTypePaymentPlan()          (deprecated -- supply the method that corresponds to the account credentials used for the address lookup)
 * 
 *  @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class GetAddresses {

    public $conf;
    public $countryCode;
    public $customerIdentifier;    
    public $orderType;
    public $companyId;
    public $ssn;
    public $testmode = false;

    /**
     * @param \ConfigurationProvider $config
     */
    function __construct($config) {
        $this->conf = $config;
    }

    /**
     * required -- supply the country code that corresponds to the account credentials used for the address lookup
     *  
     * Use setCountryCode() to supply the country code that corresponds to the account credentials used for the address lookup.
     * Note: this means that you can only look up user addresses in a country where you have a corresponding client id.
     * 
     * @param string $countryCodeAsString Country code as described by ISO 3166-1, one of "SE", "NO", "DK"
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }
    
    /**
     * required -- i.e. the social security number, company vat number et al for the country in question
     * 
     * Use setCustomerIdentifier() to provide the exact credentials needed to identify the customer according to country:
     * SE: Personnummer (private individual) or Organisationsnummer (company or other legal entity)
     * NO: Organisasjonsnummer (company or other legal entity)
     * DK: Cpr.nr (private individual) or CVR-nummer (company or other legal entity)
     * 
     * @param string $customerIdentifier
     * @return \Svea\WebService\GetAddresses
     */
    public function setCustomerIdentifier( $customerIdentifier ) {
        $this->customerIdentifier = $customerIdentifier;
        return $this;
    }

    /**
     * required -- use to define the customer type to lookup address for.
     * @return \Svea\WebService\GetAddresses
     */
    public function getIndividualAddresses() {
        $this->ssn = isset($this->customerIdentifier) ? $this->customerIdentifier : null;        
        
        return $this;
    }

    /**
     * required -- use to define the customer type to lookup address for.
     * @return \Svea\WebService\GetAddresses
     */
    public function getCompanyAddresses() {
        $this->companyId = isset($this->customerIdentifier) ? $this->customerIdentifier : null;         
        return $this;
    }
    
    /**
     * @deprecated 2.2
     * Required for Invoice and Payment Plan orders - use this to identify a company customer 
     * 
     * @param string $companyIdAsString  SE: Organisationsnummer, DK: CVR, NO: Vat number
     * @return $this
     */
    public function setCompany($companyIdAsString) {
        $this->companyId = $companyIdAsString;
        return $this;
    }

    /**
     * @deprecated 2.2
     * Required for Invoice and Payment Plan orders - use this to identify an individual customer 
     * 
     * @param string $NationalIdNumberAsString  SE: Personnummer, DK: CPR, NO: N/A
     * @return $this
     */
    public function setIndividual($NationalIdNumberAsString) {
        $this->ssn = $NationalIdNumberAsString;
        return $this;
    }
    
    /**
     * @deprecated 2.2
     * Required - you need call the method that corresponds to the account credentials (i.e. invoice or paymentplan) used for the address lookup.
     * @return $this
     */
    public function setOrderTypeInvoice() {
        $this->orderType = \ConfigurationProvider::INVOICE_TYPE;
        return $this;
    }

    /**
     * @deprecated 2.2
     * Required - you need call the method that corresponds to the account credentials (i.e. invoice or paymentplan) used for the address lookup.
     * @return $this
     */
    public function setOrderTypePaymentPlan() {
        $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        return $this;
    }
    
    
    public function validateRequest() {
        $errors = $this->validate($this);        
        if (count($errors) > 0) {
            $exceptionString = "";
            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }

            throw new \Svea\ValidationException($exceptionString);
        }  
    }
    
    public function validate($getaddressesrequest) {
        $errors = array();
        
        // countrycode -> ssn/companyid -> check credentials
        $errors = $this->validateCountryCode($getaddressesrequest, $errors);        
        $errors = $this->validateCustomerIdentifier($getaddressesrequest, $errors);
        if(count($errors) == 0) {
            $this->orderType = $this->checkAndSetConfiguredPaymentMethod();                
        }
        $errors = $this->validateCountryCodeConfigurationExists($getaddressesrequest, $errors);
        return $errors;
    }    

    private function validateCountryCode($getaddressesrequest, $errors) {
        if (isset($getaddressesrequest->countryCode) == FALSE) {
            $errors[] = "countryCode is required. Use function setCountryCode().";
        }
        // TODO add validation of accepted countries
        return $errors;
    } 
    
    private function validateCustomerIdentifier($getaddressesrequest, $errors) {
        if( !isset($getaddressesrequest->ssn) && !isset($getaddressesrequest->companyId) ) {
            $errors[] = "customerIdentifier is required. Use function setCustomerIdentifer().";
        }
        return $errors;
    }     
    
    private function validateCountryCodeConfigurationExists($getaddressesrequest, $errors) {
        if( !isset($getaddressesrequest->orderType) ) {
            $errors[] = "missing authentication credentials. Check configuration.";
        }
        return $errors;
    }   

    private function checkAndSetConfiguredPaymentMethod() {  
        if( $this->orderType == null) { // no order type set, so try and determine which configuration provider order type to use
            $orderType = \ConfigurationProvider::INVOICE_TYPE;                
            try {
                $u = $this->conf->getUsername($orderType, $this->countryCode);
                $p = $this->conf->getPassword($orderType, $this->countryCode);
                $c = $this->conf->getClientNumber($orderType, $this->countryCode);    
            }
            catch( Svea\InvalidTypeException $e ) {         // thrown if no config found
                // go on
            }
            if( isset($u) && isset($p) && isset($c) ) {
                return \ConfigurationProvider::INVOICE_TYPE;
            }

            $orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;            
            try {
                $u = $this->conf->getUsername($orderType, $this->countryCode);
                $p = $this->conf->getPassword($orderType, $this->countryCode);
                $c = $this->conf->getClientNumber($orderType, $this->countryCode);    
            }
            catch( Svea\InvalidTypeException $e ) {
                return null;                                // i.e. will throw exception in validation
            }
            if( isset($u) && isset($p) && isset($c) ) {  // i.e. not set or null
                return \ConfigurationProvider::PAYMENTPLAN_TYPE;
            }
            else {
                return null;
            }
        }
        else {
            return $this->orderType; // if set, honour given order type
        }
    }
    
    /**
     * Sets and returns prepared request object. Used by doRequest().
     * 
     * (The prepared request object may be inspected to see what attributes will
     * be sent to Svea -- use ->prepareRequest() in place of ->doRequest() and
     * inspect the resulting SveaRequest object.)
     * 
     * @return WebServiceSoap\SveaRequest
     */
    public function prepareRequest() {
        
        $this->validateRequest();        
        
        $auth = new WebServiceSoap\SveaAuth(
            $this->conf->getUsername($this->orderType,  $this->countryCode),
            $this->conf->getPassword($this->orderType,  $this->countryCode),
            $this->conf->getClientNumber($this->orderType,  $this->countryCode)                
        );

        $address = new WebServiceSoap\SveaAddress( 
            $auth, 
            (isset($this->companyId) ? true : false), 
            $this->countryCode, 
            (isset($this->companyId) ? $this->companyId : $this->ssn) 
        );

        $this->request = new WebServiceSoap\SveaRequest( $address );

        return $this->request;
    }

    /**
     * Prepares and Sends request
     * @return GetAddressesResponse
     */
    public function doRequest() {
        $this->request = $this->prepareRequest();
        
        $request = new WebServiceSoap\SveaDoRequest($this->conf, $this->orderType);

        $svea_req = $request->GetAddresses($this->request);

        $response = new \SveaResponse($svea_req,"");
        return $response->response;
    }
}
