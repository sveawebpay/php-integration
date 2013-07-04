<?php

require_once SVEA_REQUEST_DIR . '/WebServiceRequests/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Calculates price per month for all campaigns
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package WebServiceRequests/GetPaymentPlanParams
 */
class PaymentPlanPricePerMonth {

    public $values = array();

    function __construct($price,$params) {
        $this->calculate($price,$params);
        return $this->values;
    }

    private function calculate($price, $params){
        if (!empty($params)) {
            foreach ($params->campaignCodes as $key => $value) {
                if($price >= $value->fromAmount && $price <= $value->toAmount){
                                   $pair = array();
                $pair['pricePerMonth'] = $price * $value->monthlyAnnuityFactor + $value->notificationFee;
                foreach ($value as $key => $val) {
                   if($key == "campaignCode"){
                        $pair[$key] = $val;
                    }
                    if($key == "description"){
                        $pair[$key] = $val;
                    }

                }
                array_push($this->values, $pair);
                }

            }
        }
    }

}