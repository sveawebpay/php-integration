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
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebServiceRequests/GetAddresses
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
     * @return \GetAddresses
     */
    public function setOrderTypeInvoice() {
        $this->orderType = "Invoice";
        return $this;
    }

    /**
     * Required for PaymentPlan type
     * @return \GetAddresses
     */
    public function setOrderTypePaymentPlan() {
        $this->orderType = "PaymentPlan";
        return $this;
    }

    /**
     * Required
     * @param string $countryCodeAsString
     * @return \GetAddresses
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
     * @return \GetAddresses
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
     * @return \GetAddresses
     */
    public function setIndividual($NationalIdNumberAsString) {

        $this->ssn = $NationalIdNumberAsString;
        return $this;
    }

    /**
     * Returns prepared request
     * @return type
     */
    public function prepareRequest() {
        $auth = new SveaAuth();
        $auth->Username = $this->conf->getUsername($this->orderType,  $this->countryCode);
        $auth->Password = $this->conf->getPassword($this->orderType,  $this->countryCode);
        $auth->ClientNumber = $this->conf->getClientNumber($this->orderType,  $this->countryCode);

        $address = new SveaAddress();
        $address->Auth = $auth;
        $address->IsCompany = isset($this->companyId) ? true : false;
        $address->CountryCode = $this->countryCode;
        $address->SecurityNumber = isset($this->companyId) ? $this->companyId : $this->ssn;

        $object = new SveaRequest();
        $object->request = $address;
        $this->object = $object;

        return $this->object;
    }

    /**
     * Prepares and Sends request
     * @return @GetAddressesResponse object
     */
    public function doRequest() {
        $object = $this->prepareRequest();
        $url = $this->conf->getEndPoint($this->orderType);
        $request = new SveaDoRequest($url);
        $svea_req = $request->GetAddresses($object);

        $response = new \SveaResponse($svea_req,"");
        return $response->response;
    }
}
