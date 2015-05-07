<?php
namespace Svea\WebService;

require_once SVEA_REQUEST_DIR . '/WebService/svea_soap/SveaSoapConfig.php';
require_once SVEA_REQUEST_DIR . '/Config/SveaConfig.php';

/**
 * Calculates price per month for all available campaigns.
 * 
 * This is a helper function provided to calculate the monthly price for the 
 * different payment plan options for a given sum. This information may be used 
 * when displaying i.e. payment options to the customer by checkout, or to 
 * display the lowest amount due per month to display on a product level.
 *
 * The returned instance of PaymentPlanPricePerMonth contains an array "values", 
 * where each element in turn contains an array of campaign code, description &
 * price per month:
 * 
 * $paymentPlanParamsResonseObject->values[0..n] (for n campaignCodes), where 
 * values['campaignCode' => campaignCode, 'pricePerMonth' => pricePerMonth, 'description' => description]
 * 
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */
class PaymentPlanPricePerMonth {

    public $values = array();

    function __construct($price, $params, $ignoreMaxAndMinFlag = false) {
        $this->calculate($price, $params, $ignoreMaxAndMinFlag);
    }

    private function calculate($price, $params, $ignoreMaxAndMinFlag) {
        if (!empty($params)) {
            foreach ($params->campaignCodes as $key => $value) {
                if( $ignoreMaxAndMinFlag || ($price >= $value->fromAmount && $price <= $value->toAmount) ) {
                    $pair = array();
                    $pair['pricePerMonth'] = $price * $value->monthlyAnnuityFactor + $value->notificationFee;
                    foreach ($value as $key => $val) {
                        if ($key == "campaignCode") {
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
