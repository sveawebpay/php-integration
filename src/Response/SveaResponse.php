<?php
require_once SVEA_REQUEST_DIR . '/Includes.php';
/**
 * Description of SveaResponse
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class SveaResponse {

    public $response;

    public function __construct($message, $countryCode,$config = NULL) {
         $config = $config == null ? SveaConfig::getDefaultConfig() : $config;
        if(is_object($message)){
            if(property_exists($message, "CreateOrderEuResult")){
                $this->response = new CreateOrderResponse($message);
            }elseif (property_exists($message, "GetAddressesResult")) {
                $this->response = new GetAddressesResponse($message);
            }elseif (property_exists($message, "GetPaymentPlanParamsEuResult")) {
                $this->response = new PaymentPlanParamsResponse($message);
            }elseif (property_exists($message, "DeliverOrderEuResult")) {
                $this->response = new DeliverOrderResult($message);
            }elseif (property_exists($message, "CloseOrderEuResult")) {
                $this->response = new CloseOrderResult($message);
            }

        }elseif($message != NULL){
            $this->response = new HostedResponse($message,$countryCode,$config);
        }else{
            $this->response = "Response is not recognized.";
        }

    }
}