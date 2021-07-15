<?php

namespace Svea\WebPay\WebService\WebServiceResponse;

use Svea\WebPay\WebService\WebServiceResponse\CustomerIdentity\CreateOrderIdentity;

/**
 * Handles Svea WebService (Invoice, Payment Plan) CreateOrder request response.
 *
 * CreateOrderResponse structure contains all attributes returned from the Svea
 * webservice.
 *
 * Possible resultcodes are i.e. 20xxx, 24xxx, 27xxx, 3xxxx, 4xxxx, 5xxxx, see svea webpay_eu_webservice documentation
 *
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CreateOrderResponse extends WebServiceResponse
{
	/**
	 * @var string $sveaOrderId Always present. Unique Id for the created order. Used for any further webservice requests.
	 */
	public $sveaOrderId;

	/**
	 * @var string $orderType Always present. One of {Invoice|PaymentPlan}
	 */
	public $orderType;  // TODO java: enum

	/**
	 * @var string $sveaWillBuyOrder Always present.
	 */
	public $sveaWillBuyOrder;   // TODO java: boolean

	/**
	 * @var string $amount Always present. The total amount including VAT, presented as a decimal number.
	 */
	public $amount;

	/**
	 * @var CreateOrderIdentity $customerIdentity May be present. Contains invoice address.
	 */
	public $customerIdentity;

	/**
	 * @var string $expirationDate Always present. Order expiration date. If the order isn’t delivered before
	 *			  this date the order is automatically closed.
	 */
	public $expirationDate;

	/**
	 * @var string $clientOrderNumber May be present. If passed in with request, a reference to the current order.
	 */
	public $clientOrderNumber;

	/**
	 * @var string $pending true if created order is pending at Svea
	 */
	public $pending = 0;

	/**
	 * @var string $pendingReason if pending is true then a reason can be found in this variable
	 */
	public $pendingReasons;
	/**
	 * CreateOrderResponse constructor.
	 * @param $response
	 * @param $logs
	 */
	public function __construct($response, $logs = NULL)
	{
		// was request accepted?
		$this->accepted = $response->CreateOrderEuResult->Accepted;
		$this->errormessage = isset($response->CreateOrderEuResult->ErrorMessage) ? $response->CreateOrderEuResult->ErrorMessage : "";

		// set response resultcode
		$this->resultcode = $response->CreateOrderEuResult->ResultCode;

		if(isset($logs))
		{
			$this->logs = $logs;
		}

		// set response attributes
		if ($this->accepted == 1) {

			// always present
			$this->sveaOrderId = $response->CreateOrderEuResult->CreateOrderResult->SveaOrderId;
			$this->sveaWillBuyOrder = $response->CreateOrderEuResult->CreateOrderResult->SveaWillBuyOrder;
			$this->amount = $response->CreateOrderEuResult->CreateOrderResult->Amount;
			$this->expirationDate = $response->CreateOrderEuResult->CreateOrderResult->ExpirationDate;

			// presence not guaranteed
			if (isset($response->CreateOrderEuResult->CreateOrderResult->ClientOrderNumber)) {
				$this->clientOrderNumber = $response->CreateOrderEuResult->CreateOrderResult->ClientOrderNumber;
			}
			if (isset($response->CreateOrderEuResult->CreateOrderResult->OrderType)) {
				$this->orderType = $response->CreateOrderEuResult->CreateOrderResult->OrderType;
			}
			if (isset($response->CreateOrderEuResult->CreateOrderResult->CustomerIdentity)) {
				$this->customerIdentity = new CreateOrderIdentity($response->CreateOrderEuResult->CreateOrderResult->CustomerIdentity);
			}
			if (isset($response->CreateOrderEuResult->CreateOrderResult->PendingReasons))
			{
				$this->pending = 1;
				$this->pendingReasons = $response->CreateOrderEuResult->CreateOrderResult->PendingReasons;
			}
		}
	}
}
