<?php
namespace Svea\WebService;

require_once SVEA_REQUEST_DIR . '/WebService/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Use getPaymentPlanParams() to fetch all campaigns associated with a given client number.
 * 
 * Retrieves information about all the campaigns that are associated with the
 * current Client. Use this information to display information about the possible
 * payment plan options to customers. The returned CampaignCode is used when
 * creating a PaymentPlan order.
 * 
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
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
     * Required
     * 
     * @param string $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * @return SveaRequest
     */
    public function prepareRequest() {
        $auth = new WebServiceSoap\SveaAuth( 
            $this->conf->getUsername(\ConfigurationProvider::PAYMENTPLAN_TYPE,  $this->countryCode),
            $this->conf->getPassword(\ConfigurationProvider::PAYMENTPLAN_TYPE,  $this->countryCode),   
            $this->conf->getClientNumber(\ConfigurationProvider::PAYMENTPLAN_TYPE,  $this->countryCode)   
        );

        $object = new WebServiceSoap\SveaRequest();
        $object->request = (object) array("Auth" => $auth);

        return $object;
    }
    
    /**
     * Prepares and sends request
     * 
     * @return PaymentPlanParamsResponse
     */
    public function doRequest() {
        $requestObject = $this->prepareRequest();
        $request = new WebServiceSoap\SveaDoRequest( $this->conf, \ConfigurationProvider::PAYMENTPLAN_TYPE);
        $response = $request->GetPaymentPlanParamsEu($requestObject);

        $responseObject = new \SveaResponse($response,"");
        return $responseObject->response;
    }  
}
