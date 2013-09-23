<?php
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class SveaResponse {

    public $response;

    public function __construct($message, $countryCode,$config = NULL) {
         
        $config = $config == null ? Svea\SveaConfig::getDefaultConfig() : $config;
        
        if (is_object($message)) {
            
            if (property_exists($message, "CreateOrderEuResult")) {
                $this->response = new Svea\CreateOrderResponse($message);
            } 
            elseif (property_exists($message, "GetAddressesResult")) {
                $this->response = new Svea\GetAddressesResponse($message);
            } 
            elseif (property_exists($message, "GetPaymentPlanParamsEuResult")) {
                $this->response = new Svea\PaymentPlanParamsResponse($message);
            } 
            elseif (property_exists($message, "DeliverOrderEuResult")) {
                $this->response = new Svea\DeliverOrderResult($message);
            } 
            elseif (property_exists($message, "CloseOrderEuResult")) {
                $this->response = new Svea\CloseOrderResult($message);
            }
            //webservice from hosted_admin
            elseif (property_exists($message, "message"))   {
                 $this->response = new Svea\HostedAdminResponse($message,$countryCode,$config);
            }

        } 
        elseif ($message != NULL) {
            $this->response = new Svea\HostedPaymentResponse($message,$countryCode,$config);
        } 
        else {
            $this->response = "Response is not recognized.";
        }
    }
}
