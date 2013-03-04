<?php

require_once SVEA_REQUEST_DIR . '/WebServiceRequests/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Applicable for SE, NO & DK.
 * If customer has multiple addresses or you just want to show the address which
 * the invoice / product is to be delivered to for the customer you can use this 
 * class. It returns an array with all the associated addresses for a specific 
 * SecurityNumber. 
 * Each address gets an "AddressSelector" - hash to signify the address. This can
 * be used when Creating order to have the invoice be sent to the specified address. 
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

    function __construct() {
        $this->conf = SveaConfig::getConfig();
    }

    public function setTestmode() {
        $this->testmode = true;
        return $this;
    }
    
   /**
     * Alternative drop or change file in Config/SveaConfig.php
     * Note! This fuction may change in future updates.
     * @param type $merchantId
     * @param type $secret
     * @return \HostedPayment
     */
   public function setPasswordBasedAuthorization($username, $password, $clientNumber) {
        $this->conf = SveaConfig::getConfig();
        $this->conf->username = $username;
        $this->conf->password = $password;
        if ($this->orderType == "Invoice") {
            $this->conf->invoiceClientnumber = $clientNumber;
        } else {
            $this->conf->paymentPlanClientnumber = $clientNumber;
        }
        return $this;
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
     * @param type $countryCodeAsString
     * @return \GetAddresses
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * Required if customer is Company
     * @param type $companyIdAsString
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
     * @param type $NationalIdNumberAsInt
     * Sweden: Personnummer,
     * Norway: Persnonalnumber,
     * Denmark: CPR
     * @return \GetAddresses
     */
    public function setIndividual($NationalIdNumberAsInt) {
        $this->ssn = $NationalIdNumberAsInt;
        return $this;
    }
    
    /**
     * Returns prepared request
     * @return type
     */
    public function prepareRequest() {
        $authArray = $this->conf->getPasswordBasedAuthorization('Invoice');
        $auth = new SveaAuth();
        $auth->Username = $authArray['username'];
        $auth->Password = $authArray['password'];
        $auth->ClientNumber = $authArray['clientnumber'];

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
     * @return GetCustomerAddressesResponse
     */
    public function doRequest() {
        $object = $this->prepareRequest();
        $url = $this->testmode ? SveaConfig::SWP_TEST_WS_URL : SveaConfig::SWP_PROD_WS_URL;
        $request = new SveaDoRequest($url);
        $svea_req = $request->GetAddresses($object);
       
        $response = new SveaResponse($svea_req);
        return $response->response;
    }
}

?>