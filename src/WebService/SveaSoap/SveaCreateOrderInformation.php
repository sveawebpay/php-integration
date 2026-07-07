<?php

namespace Svea\WebPay\WebService\SveaSoap;

class SveaCreateOrderInformation extends CreateOrderInformation
{
    public $CreatePaymentPlanDetails;

    /**
     * Sets Variable if contains CampaignCode for Paymentplan
     * @param string $CampaignCode
     * @param int $sendAutomaticGiroPaymentForm
     */
    public function __construct($CampaignCode = "", $sendAutomaticGiroPaymentForm = 0)
    {
        $this->OrderRows['OrderRow'] = array();

        if ($CampaignCode != "") {
            $this->CreatePaymentPlanDetails = array(
                "CampaignCode" => $CampaignCode,
                "SendAutomaticGiroPaymentForm" => $sendAutomaticGiroPaymentForm
            );
        }
    }
}
