<?php

require_once SVEA_REQUEST_DIR . '/WebServiceRequests/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Retrieves information about all the campaigns that are associated with the
 * current Client. Use this information to display information about the possible
 * payment plan options to customers. The returned CampaignCode is used when
 * creating a PaymentPlan order.
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebServiceRequests/GetPaymentPlanParams
 */
class GetPaymentPlanParams {

    public $testmode = false;
    public $object;
    public $conf;
    public $countryCode;


    function __construct($config) {
         $this->conf = $config;
    }

    /**
    public function setTestmode() {
        $this->testmode = true;
        return $this;
    }
     * /
     /*
     * @param type $countryCodeAsString
     */

    public function setCountryCode($countryCodeAsString){
        $this->countryCode = $countryCodeAsString;
        return $this;
    }


    /**
     * Alternative drop or change file in Config/SveaConfig.php
     * Note! This fuction may change in future updates.
     * @param type $merchantId
     * @param type $secret

    public function setPasswordBasedAuthorization($username, $password, $clientNumber) {
        $this->conf->username = $username;
        $this->conf->password = $password;
        $this->conf->paymentPlanClientnumber = $clientNumber;
        return $this;
    }
     *
     */

    /**
     * @return Prepared Request
     */
    public function prepareRequest() {
        $auth = new SveaAuth();
        $auth->Username = $this->conf->getUsername("PAYMENTPLAN",  $this->countryCode);//$authArray['username'];
        $auth->Password = $this->conf->getPassword("PAYMENTPLAN",  $this->countryCode);//$authArray['password'];
        $auth->ClientNumber = $this->conf->getClientNumber("PAYMENTPLAN",  $this->countryCode); //$authArray['clientnumber'];
        $object = new SveaRequest();
        $object->request = (object) array("Auth" => $auth);
        $this->object = $object;

        return $this->object;
    }

    /**
     * Prepares and sends request
     * @return type GetPaymentPlanParamsEuResponse
     */
    public function doRequest() {
        $object = $this->prepareRequest();
        $url = $this->conf->getEndPoint("PAYMENTPLAN");//$this->testmode ? SveaConfig::SWP_TEST_WS_URL : SveaConfig::SWP_PROD_WS_URL;
        $request = new SveaDoRequest($url);
        $svea_req = $request->GetPaymentPlanParamsEu($object);

        $response = new SveaResponse($svea_req,"");
        return $response->response;
    }
}