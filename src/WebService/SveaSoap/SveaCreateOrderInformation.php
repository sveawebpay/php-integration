<?php

namespace Svea\WebPay\WebService\SveaSoap;

class SveaCreateOrderInformation extends CreateOrderInformation
{
	/**
	 * Sets Variable if contains CampaignCode for Paymentplan
	 * @param string $CampaignCode
	 * @param int $sendAutomaticGiroPaymentForm
	 */
	public function __construct($CampaignCode = "", $sendAutomaticGiroPaymentForm = 0)
	{
		$this->OrderRows['OrderRow'] = [];

		if ($CampaignCode != "") {
			$this->CreatePaymentPlanDetails = [
				"CampaignCode" => $CampaignCode,
				"SendAutomaticGiroPaymentForm" => $sendAutomaticGiroPaymentForm
			];
		}
	}
}
