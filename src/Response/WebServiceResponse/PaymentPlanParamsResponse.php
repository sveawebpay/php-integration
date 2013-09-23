<?php
namespace Svea;

require_once 'WebServiceResponse.php';

/**
 * For attribute descriptions, see comments by attribute assignment below.
 * For possible resultcodes, see http://www.sveawebpay.se/PageFiles/229/webpay_eu_webservice.pdf, PaymentPlans p.24.
 * 
 * @author anne-hal
 */
class PaymentPlanParamsResponse extends WebServiceResponse{

    public $campaignCodes = array();

    function __construct($message) {
        parent::__construct($message);
        if (isset($message->GetPaymentPlanParamsEuResult->ErrorMessage)) {
            $this->errormessage = $message->GetPaymentPlanParamsEuResult->ErrorMessage;
        }
    }

    protected function formatObject($message) {
        $this->accepted = $message->GetPaymentPlanParamsEuResult->Accepted;
        $this->resultcode = $message->GetPaymentPlanParamsEuResult->ResultCode;
        if ($this->accepted == 1) {
            foreach ($message->GetPaymentPlanParamsEuResult->CampaignCodes->CampaignCodeInfo as $code) {
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

            array_push($this->campaignCodes, $campaign);                        // all available campaign payment plans in an array
            }
        }
    }
}
