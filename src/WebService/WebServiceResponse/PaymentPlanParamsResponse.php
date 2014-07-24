<?php
namespace Svea\WebService;

require_once 'WebServiceResponse.php';

/**
 * Handles the Svea Webservice GetPyamentPlanParamsEU request response.
 * 
 *  formatObject() sets the following PaymentPlanParamsResponse attributes:
 * 
 *      $response->accepted              // true iff request was accepted by the service 
 *      $response->errormessage          // may be set iff accepted above is false
 *
 *      $response->resultcode            // 27xxx, reason
 *      $response->campaignCodes[0..n]   // all available campaign payment plans in an array
 *         ->campaignCode                // numeric campaign code identifier
 *         ->description                 // localised description string
 *         ->paymentPlanType             // human readable identifier (not guaranteed unique)
 *         ->contractLengthInMonths
 *         ->monthlyAnnuityFactor        // pricePerMonth = price * monthlyAnnuityFactor + notificationFee
 *         ->initialFee
 *         ->notificationFee
 *         ->interestRatePercent
 *         ->numberOfInterestFreeMonths
 *         ->numberOfPaymentFreeMonths
 *         ->fromAmount                  // amount lower limit for plan availability
 *         ->toAmount                    // amount upper limit for plan availability
 *         ->campaignCode                // numeric campaign code identifier  
 * 
 * For possible resultcodes (27xxx), see svea webpay_eu_webservice documentation
 * 
 * @author anne-hal, Kristian Grossman-Madsen
 */
class PaymentPlanParamsResponse extends WebServiceResponse{
    
    /** @var \Svea\WebService\CampaignCode $campaignCodes  array of CampaignCode */
    public $campaignCodes = array();    

    public function __construct($response) {
        
        // was request accepted?
        $this->accepted = $response->GetPaymentPlanParamsEuResult->Accepted;

        // set response resultcode & errormessage, if any
        $this->resultcode = $response->GetPaymentPlanParamsEuResult->ResultCode;  
        $this->errormessage = isset($response->GetPaymentPlanParamsEuResult->ErrorMessage) ? $response->GetPaymentPlanParamsEuResult->ErrorMessage : "";
        
        // set response attributes
        if ($this->accepted == 1) {
            foreach ($response->GetPaymentPlanParamsEuResult->CampaignCodes->CampaignCodeInfo as $code) {
                
                $campaign = new CampaignCode();
                $campaign->campaignCode = $code->CampaignCode;                      // numeric campaign code identifier
                $campaign->description = $code->Description;                        // localised description string
                $campaign->paymentPlanType = $code->PaymentPlanType;                // human readable identifier (not guaranteed unique)
                $campaign->contractLengthInMonths = $code->ContractLengthInMonths;  
                $campaign->monthlyAnnuityFactor = $code->MonthlyAnnuityFactor;      // pricePerMonth = price * monthlyAnnuityFactor + notificationFee
                $campaign->initialFee = $code->InitialFee;
                $campaign->notificationFee = $code->NotificationFee;
                $campaign->interestRatePercent = $code->InterestRatePercent;
                $campaign->numberOfInterestFreeMonths = $code->NumberOfInterestFreeMonths;
                $campaign->numberOfPaymentFreeMonths = $code->NumberOfPaymentFreeMonths;
                $campaign->fromAmount = $code->FromAmount;                          // amount lower limit for plan availability
                $campaign->toAmount = $code->ToAmount;                              // amount upper limit for plan availability

                array_push($this->campaignCodes, $campaign);                        // add to available campaign payment plans array
            }
        }
    }
}
