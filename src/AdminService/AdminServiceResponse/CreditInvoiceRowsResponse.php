<?php

namespace Svea\WebPay\AdminService\AdminServiceResponse;

/**
 * Handles the Svea Admin Web Service CreditInvoiceRows request response.
 *
 * @author Kristian Grossman-Madsen, Fredrik Sundell
 */
class CreditInvoiceRowsResponse extends AdminServiceResponse
{
	/**
	 * @var float $amount (set iff accepted) the amount credited with this request (a negative amount)
	 */
	public $amount;

	/**
	 * @var string $orderType (set iff accepted)  one of [Invoice|PaymentPlan]
	 */
	public $orderType;

	/**
	 * @var string $creditInvoiceId (set iff accepted, orderType Invoice)  the $creditInvoiceId for the credit invoice issued with this request
	 */
	public $creditInvoiceId;

	/**
	 * @var string $clientId (set iff accepted)
	 */
	public $clientId;

	/**
	 * @var string $orderId (set iff accepted)
	 */
	public $orderId;

	/**
	 * CreditInvoiceRowsResponse constructor.
	 * @param $message
	 * @param $logs
	 */
	function __construct($message, $logs)
	{
		$this->formatObject($message, $logs);
	}

	/**
	 * Parses response and sets attributes.
	 * @param $message
	 * @param $logs
	 */
	protected function formatObject($message, $logs)
	{
		parent::formatObject($message, $logs);

		if ($this->accepted == 1) {
			$this->amount = (-1) * $message->OrdersDelivered->DeliverOrderResult->DeliveredAmount;
			$this->orderType = $message->OrdersDelivered->DeliverOrderResult->OrderType;
			$this->creditInvoiceId = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
			$this->clientId = $message->OrdersDelivered->DeliverOrderResult->ClientId;
			$this->orderId = $message->OrdersDelivered->DeliverOrderResult->SveaOrderId;
		}
	}
}

// raw response message example:
//
//stdClass Object
//(
//	[ErrorMessage] =>
//	[ResultCode] => 0
//	[OrdersDelivered] => stdClass Object
//		(
//			[DeliverOrderResult] => stdClass Object
//				(
//					[ClientId] => 79021
//					[DeliveredAmount] => 125.00
//					[DeliveryReferenceNumber] => 1027080
//					[OrderType] => Invoice
//					[SveaOrderId] => 352701
//				)
//
//		)
//)