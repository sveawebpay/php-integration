<?php

namespace Svea\WebPay\WebService\GetPaymentPlanParams;

use Svea\WebPay\Helper\Helper;
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
class PaymentPlanPricePerMonth
{
    public $values = array();

    /**
     * PaymentPlanPricePerMonth constructor.
     * @param $price
     * @param $params
     * @param bool $ignoreMaxAndMinFlag
     * @param int decimals
     */
    function __construct($price, $params, $ignoreMaxAndMinFlag = false, $decimals = 0)
    {
        $this->calculate($price, $params, $ignoreMaxAndMinFlag, $decimals);
    }

    /**
     * @param $price
     * @param $params
     * @param $ignoreMaxAndMinFlag
     * @param $decimals
     */
    private function calculate($price, $params, $ignoreMaxAndMinFlag, $decimals)
    {
        if (!empty($params)) {
            foreach ($params->campaignCodes as $key => $value) {
                if ($ignoreMaxAndMinFlag || ($price >= $value->fromAmount && $price <= $value->toAmount)) {
                    $pair = Helper::objectToArray($value);
                    $pair['pricePerMonth'] = round(($value->initialFee + (ceil($price * $value->monthlyAnnuityFactor) + $value->notificationFee) * max(1, $value->contractLengthInMonths - $value->numberOfPaymentFreeMonths)) / max(1, $value->contractLengthInMonths - $value->numberOfPaymentFreeMonths), $decimals);
                    array_push($this->values, $pair);
                }
            }
        }
    }
}
