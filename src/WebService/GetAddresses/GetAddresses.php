<?php
/**
 * Namespace Svea\WebService Implements SveaWebPay Europe Web Service API 1.4.5.
 */
namespace Svea\WebService;

require_once SVEA_REQUEST_DIR . '/WebService/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Applicable for SE, DK & NO (company only) customers. 
 *
 * Use this method to fetch the validated address that Svea will send the 
 * customer invoice/contract to, for invoice/payment plan orders, respectively.
 * 
 * The method returns an array of all the associated addresses for a given 
 * customer identity. Each address has an AddressSelector attribute that 
 * uniquely identifies the address.
 *
 * The GetAddresses service is only applicable for SE, NO and DK customers and accounts. 
 * In Norway, GetAddresses may only be performed on company customers.
 * 
 * $response = WebPay::getAddresses( $config )
 *     ->setCountryCode("SE")                  // Required -- supply the country code that corresponds to the account credentials used 
 *     ->setOrderTypeInvoice()                 // Required -- use invoice account credentials for getAddresses lookup
 *     //->setOrderTypePaymentPlan()           // Required -- use payment account plan credentials for getAddresses lookup
 *     ->setIndividual("194605092222")         // Required -- lookup the address of a private individual
 *     //->setCompany("CompanyId")             // Required -- lookup the address of a legal entity (i.e. company)
 *    ->doRequest();
 * ;
 * 
 *  @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class GetAddresses {

    public $object;
    public $countryCode;
    public $companyId;
    public $testmode = false;
    public $orderType;
    public $conf;

    /**
     * @param \ConfigurationProvider $config
     */
    function __construct($config) {
        $this->conf = $config;
    }

    /**
     * Required - you need call the method that corresponds to the account credentials (i.e. invoice or paymentplan) used for the address lookup.
     * @return $this
     */
    public function setOrderTypeInvoice() {
        $this->orderType = "Invoice";
        return $this;
    }

    /**
     * Required - you need call the method that corresponds to the account credentials (i.e. invoice or paymentplan) used for the address lookup.
     * @return $this
     */
    public function setOrderTypePaymentPlan() {
        $this->orderType = "PaymentPlan";
        return $this;
    }

    /**
     * Required - you need to supply the country code that corresponds to the account credentials used for the address lookup.
     *  
     * Note that this means that you cannot look up a user in a foreign country, this is a consequence of the fact that the
     * invoice and partpayment methods don't support foreign orders.
     * 
     * @param string $countryCodeAsString Country code as described by ISO 3166-1, one of "SE", "NO", "DK"
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
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
     * Sets and returns prepared request object. Used by doRequest().
     * 
     * (The prepared request object may be inspected to see what attributes will
     * be sent to Svea -- use ->prepareRequest() in place of ->doRequest() and
     * inspect the resulting SveaRequest object.)
     * 
     * @return WebServiceSoap\SveaRequest
     */
    public function prepareRequest() {
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
        
        $url = $this->conf->getEndPoint($this->orderType);
        $request = new WebServiceSoap\SveaDoRequest($url);

        $svea_req = $request->GetAddresses($this->request);

        $response = new \SveaResponse($svea_req,"");
        return $response->response;
    }
}
