<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/WebServiceRequests/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Applicable for SE, NO & DK.
 *
 * If customer has multiple addresses, or you just want to show the customer
 * the address which the invoice or product is to be delivered to, you can use
 * this class. It returns an array with all the associated addresses for a
 * specific SecurityNumber.
 *
 * Each address gets an AddressSelector-hash that identifies the address. This
 * can be used when creating orders to have the invoice be sent to the specified
 * address.
 *
 * TODO document attributes
 * 
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */
class GetAddresses {

    public $object;
    public $countryCode;
    public $companyId;
    public $testmode = false;
    public $orderType;
    public $conf;

    function __construct($config) {
        $this->conf = $config;
    }

    /**
     * Required for Invoice type
     * @return $this
     */
    public function setOrderTypeInvoice() {
        $this->orderType = "Invoice";
        return $this;
    }

    /**
     * Required for PaymentPlan type
     * @return $this
     */
    public function setOrderTypePaymentPlan() {
        $this->orderType = "PaymentPlan";
        return $this;
    }

    /**
     * Required
     * @param string $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * Required if customer is Company
     * @param string $companyIdAsString
     * Sweden: Organisationsnummer,
     * Norway: Vat number,
     * Denmark: CVR
     * @return $this
     */
    public function setCompany($companyIdAsString) {
        $this->companyId = $companyIdAsString;
        return $this;
    }

    /**
     * Required if customer is Individual
     * @param string $NationalIdNumberAsString
     * Sweden: Personnummer,
     * Norway: Personalnumber,
     * Denmark: CPR
     * @return $this
     */
    public function setIndividual($NationalIdNumberAsString) {

        $this->ssn = $NationalIdNumberAsString;
        return $this;
    }

    /**
     * Sets and returns prepared request object attribute, which can then be 
     * inspected to see the contents of the request be sent to Svea 
     * @return SveaRequest
     */
    public function prepareRequest() {
        $auth = new SveaAuth(
            $this->conf->getUsername($this->orderType,  $this->countryCode),
            $this->conf->getPassword($this->orderType,  $this->countryCode),
            $this->conf->getClientNumber($this->orderType,  $this->countryCode)                
        );

        $address = new SveaAddress( 
            $auth, 
            (isset($this->companyId) ? true : false), 
            $this->countryCode, 
            (isset($this->companyId) ? $this->companyId : $this->ssn) 
        );

        $this->request = new SveaRequest( $address );

        return $this->request;
    }

    /**
     * Prepares and Sends request
     * @return GetAddressesResponse object
     */
    public function doRequest() {
        $this->request = $this->prepareRequest();
        
        $url = $this->conf->getEndPoint($this->orderType);
        $request = new SveaDoRequest($url);

        $svea_req = $request->GetAddresses($this->request);

        $response = new \SveaResponse($svea_req,"");
        return $response->response;
    }
}
