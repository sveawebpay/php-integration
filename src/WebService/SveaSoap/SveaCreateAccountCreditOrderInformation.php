<?php

namespace Svea\WebPay\WebService\SveaSoap;

class SveaCreateAccountCreditOrderInformation extends CreateOrderInformation
{
    /**
     * @var array $CreateAccountCreditDetails
     */
    public $CreateAccountCreditDetails = [];

    /**
     * Sets Variable if contains CampaignCode for AccountCredit
     * @param string $CampaignCode
     */
    public function __construct($CampaignCode = "")
    {
        $this->OrderRows['OrderRow'] = [];

        if ($CampaignCode != "") {
            $this->CreateAccountCreditDetails = [
                "CampaignCode" => $CampaignCode,
            ];
        }
    }
}
