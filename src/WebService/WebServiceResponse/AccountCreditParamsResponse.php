<?php

namespace Svea\WebPay\WebService\WebServiceResponse;

use Svea\WebPay\WebService\WebServiceResponse\CampaignCode\AccountCreditCampaignCode;

/**
 * Handles the Svea Webservice GetAccountCreditParamsEU request response.
 *
 *  formatObject() sets the following AccountCreditParamsResponse attributes:
 *
 *      $response->accepted              // true iff request was accepted by the service
 *      $response->errormessage          // may be set iff accepted above is false
 *
 *      $response->resultcode            // 27xxx, reason
 *      $response->AccountCreditCampaignCodes[0..n]   // all available campaign account credit plans in an array
 *         ->campaignCode
 *         ->description
 *         ->initialFee
 *         ->lowestAmountToPayPerMonth
 *         ->lowestPercentToPayPerMonth
 *         ->lowestOrderAmount
 *         ->interestRatePercent
 *
 * For possible resultcodes (27xxx), see svea webpay_eu_webservice documentation
 *
 */
class AccountCreditParamsResponse extends WebServiceResponse
{
    /**
     * @var AccountCreditCampaignCode[] $AccountCreditCampaignCodes - array of AccountCreditCampaignCode
     */
    public $AccountCreditCampaignCodes = array();

    /**
     * AccountCreditParamsResponse constructor.
     * @param $response
     */
    public function __construct($response)
    {
        // was request accepted?
        $this->accepted = $response->GetAccountCreditParamsEuResult->Accepted;

        // set response resultcode & errormessage, if any
        $this->resultcode = $response->GetAccountCreditParamsEuResult->ResultCode;

        $this->errormessage = isset($response->GetAccountCreditParamsEuResult->ErrorMessage) ? $response->GetAccountCreditParamsEuResult->ErrorMessage : "";

        // set response attributes
        if ($this->accepted == 1) {
            if (is_array($response->GetAccountCreditParamsEuResult->AccountCreditCampaignCodes->AccountCreditCampaignCodeInfo)) {
                foreach ($response->GetAccountCreditParamsEuResult->AccountCreditCampaignCodes->AccountCreditCampaignCodeInfo as $code) {
                    $campaign = $this->mapResponseData($code);

                    array_push($this->AccountCreditCampaignCodes, $campaign);                        // add to available campaign payment plans array
                }
            } else {

                $code = $response->GetAccountCreditParamsEuResult->AccountCreditCampaignCodes->AccountCreditCampaignCodeInfo;

                $campaign = $this->mapResponseData($code);

                array_push($this->AccountCreditCampaignCodes, $campaign);
            }
        }
    }

    /**
     * Return AccountCreditCampaignCode mapped with AccountCreditCampaignCodeInfo
     *
     * @param $code
     * @return AccountCreditCampaignCode
     */
    private function mapResponseData($code)
    {
        $campaign = new AccountCreditCampaignCode();

        $campaign->initialFee = $code->InitialFee;
        $campaign->description = $code->Description;        // localised description string
        $campaign->campaignCode = $code->CampaignCode;      // numeric campaign code identifier
        $campaign->notificationFee = $code->NotificationFee;
        $campaign->lowestOrderAmount = $code->LowestOrderAmount;
        $campaign->interestRatePercent = $code->InterestRatePercent;
        $campaign->lowestAmountToPayPerMonth = $code->LowestAmountToPayPerMonth;
        $campaign->lowestPercentToPayPerMonth = $code->LowestPercentToPayPerMonth;

        return $campaign;
    }
}
