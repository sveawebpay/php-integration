<?php

namespace Svea\WebPay\WebService\SveaSoap;

class SveaCreateAccountCreditOrderInformation extends CreateOrderInformation
{
    /**
     * @var array $CreateAccountCreditDetails
     */
    public $CreateAccountCreditDetails = array();

    /**
     * Sets Variable if contains CampaignCode for AccountCredit
     * @param string $CampaignCode
     */
    public function __construct($CampaignCode = "")
    {
        $this->OrderRows['OrderRow'] = array();

        if ($CampaignCode != "") {
            $this->CreateAccountCreditDetails = array(
                "CampaignCode" => $CampaignCode,
            );
        }
    }
}
