<?php
require_once 'WebServiceResponse.php';
/**
 * Description of PaymentPlanParamsResponse
 *
 * @author anne-hal
 */
class PaymentPlanParamsResponse extends WebServiceResponse{

    public $campaignCodes = array();


    function __construct($message) {
        parent::__construct($message);
        if(isset($message->GetPaymentPlanParamsEuResult->ErrorMessage)){
            $this->errormessage = $message->GetPaymentPlanParamsEuResult->ErrorMessage;
        }
    }

    protected function formatObject($message){
        $this->accepted = $message->GetPaymentPlanParamsEuResult->Accepted;
        $this->resultcode = $message->GetPaymentPlanParamsEuResult->ResultCode;
        if($this->accepted == 1){
            foreach ($message->GetPaymentPlanParamsEuResult->CampaignCodes->CampaignCodeInfo as $code) {
            $campaign = new CampaignCode();
            $campaign->campaignCode = $code->CampaignCode;
            $campaign->description = $code->Description;
            $campaign->paymentPlanType = $code->PaymentPlanType;
            $campaign->contractLengthInMonths = $code->ContractLengthInMonths;
            $campaign->monthlyAnnuityFactor = $code->MonthlyAnnuityFactor;
            $campaign->initialFee = $code->InitialFee;
            $campaign->notificationFee = $code->NotificationFee;
            $campaign->interestRatePercent = $code->InterestRatePercent;
            $campaign->numberOfInterestFreeMonths = $code->NumberOfInterestFreeMonths;
            $campaign->numberOfPaymentFreeMonths = $code->NumberOfPaymentFreeMonths;
            $campaign->fromAmount = $code->FromAmount;
            $campaign->toAmount = $code->ToAmount;

            array_push($this->campaignCodes, $campaign);
            }
        }
    }
}