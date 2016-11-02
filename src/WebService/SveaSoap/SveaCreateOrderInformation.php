<?php

namespace Svea\WebPay\WebService\SveaSoap;

class SveaCreateOrderInformation
{
    public $ClientOrderNumber;
    public $OrderRows = array(); //contains orderRows objects
    public $CustomerIdentity;
    public $OrderDate;
    public $AddressSelector;
    public $CustomerReference;
    public $OrderType;

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

    public function addOrderRow($orderRow)
    {
        array_push($this->OrderRows['OrderRow'], $orderRow);
    }
}
