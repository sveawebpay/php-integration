<?php
namespace Svea\WebService;

/**
 *  CampaignCodes structure
 *
 *  @attrib     ->campaignCode                      // numeric campaign code identifier
 *  @attrib     ->description                       // localised description string
 *  @attrib     ->paymentPlanType                   // human readable identifier (not guaranteed unique)
 *  @attrib     ->contractLengthInMonths
 *  @attrib     ->monthlyAnnuityFactor              // pricePerMonth = price * monthlyAnnuityFactor + notificationFee
 *  @attrib     ->initialFee
 *  @attrib     ->notificationFee
 *  @attrib     ->interestRatePercent
 *  @attrib     ->numberOfInterestFreeMonths
 *  @attrib     ->numberOfPaymentFreeMonths
 *  @attrib     ->fromAmount                        // amount lower limit for plan availability
 *  @attrib     ->toAmount                          // amount upper limit for plan availability
 * 
 *  @author anne-hal, Kristian Grossman-Madsen
 */
class CampaignCode {
    public $campaignCode;
    public $description;
    public $paymentPlanType;
    public $contractLengthInMonths;
    public $monthlyAnnuityFactor;
    public $initialFee;
    public $notificationFee;
    public $interestRatePercent;
    public $numberOfInterestFreeMonths;
    public $numberOfPaymentFreeMonths;
    public $fromAmount;
    public $toAmount;
}
