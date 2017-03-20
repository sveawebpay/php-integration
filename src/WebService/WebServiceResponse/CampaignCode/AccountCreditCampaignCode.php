<?php

namespace Svea\WebPay\WebService\WebServiceResponse\CampaignCode;

/**
 *  CampaignCodes structure
 *
 * @attrib     ->campaignCode                      // numeric campaign code identifier
 * @attrib     ->description                       // localised description string
 * @attrib     ->initialFee
 * @attrib     ->lowestAmountToPayPerMonth
 * @attrib     ->lowestPercentToPayPerMonth
 * @attrib     ->lowestOrderAmount                 // amount lower limit for plan availability
 * @attrib     ->interestRatePercent
 * @attrib     ->notificationFee
 *
 */
class AccountCreditCampaignCode
{
    public $campaignCode;
    public $description;
    public $initialFee;
    public $lowestAmountToPayPerMonth;
    public $lowestPercentToPayPerMonth;
    public $lowestOrderAmount;
    public $interestRatePercent;
    public $notificationFee;
}
