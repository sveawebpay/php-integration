<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\AdminService\DeliverOrderRowsRequest;
use Svea\WebPay\Checkout\Service\Admin\DeliverOrderService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\HostedService\HostedAdminRequest\LowerTransaction;
use Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse;

/**
 * The Svea\WebPay\WebPayAdmin::deliverOrderRows entrypoint method is used to partially deliver an
 * order. Supports Invoice and Card orders. (To partially deliver a Payment Plan or
 * Direct Bank order, contact Svea customer service.)
 *
 * To deliver an order row in full, you specify the index of the order row to
 * credit (and for card orders, supply the numbered order row data itself).
 *
 * Use setOrderId() to specify the card or direct bank transaction (delivered order) to credit.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 *
 * Use setRowToDeliver() or setRowsToDeliver() to specify order rows to deliver.
 * The given row numbers must correspond with the serverside row numbers.
 * For card orders, the rows must match order rows specified using addNumberedOrderRow() or addNumberedOrderRows().
 * For invoice orders, the serverside order rows is updated after a deliverOrderRows request.
 * Note that for Card and  orders the serverside order rows will not be updated.
 * For card orders, it is required to use addNumberedOrderRow() or addNumberedOrderRows()
 * to pass in a copy of the serverside order row data. All order rows must be supplied.
 *
 * You can use the Svea\WebPay\WebPayAdmin::queryOrder() entrypoint to get information about the order,
 * the QueryOrderResponse->numberedOrderRows attribute contains the order rows, but
 * note that if the order has been modified after creation these may not be accurate.
 *
 * Then use deliverInvoiceOrderRows() or deliver CardOrderRows to get a request object,
 * which ever matches the payment method used in the original order. deliverCardOrderRows
 * Calculates the correct amount to deliver from supplied order rows and when followed by a
 * a ->doRequest() call performs a LowerTransaction followed by a ConfirmTransaction. Note
 * that the card transaction must have status AUTHORIZED at Svea in order to be delivered.
 *
 * Calling doRequest() on the request object will send the request to Svea and
 * return either a DeliverOrderRowsResponse or a ConfirmTransactionResponse.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class DeliverOrderRowsBuilder extends CheckoutAdminOrderBuilder
{
	/**
	 * @var ConfigurationProvider $conf
	 */
	public $conf;

	/**
	 * @var string $orderId order id/transaction id as returned in the createOrder request response,
	 */
	public $orderId;

	/**
	 * @var string $countryCode
	 */
	public $countryCode;

	/**
	 * @var string $orderType -- one of Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::HOSTED_ADMIN_TYPE
	 */
	public $orderType;

	/**
	 * @var string $distributionType -- one of Svea\WebPay\Constant\DistributionType::POST, ::EMAIL
	 */
	public $distributionType;

	/**
	 * @var int[] $rowsToDeliver array of original order row indexes to deliver
	 */
	public $rowsToDeliver;

	public $numberedOrderRows;

	/**
	 * DeliverOrderRowsBuilder constructor.
	 * @param $config
	 */
	public function __construct($config)
	{
		parent::__construct($config);
		$this->rowsToDeliver = [];
		$this->numberedOrderRows = [];
	}

	/**
	 * Required -- use same countryCode as in createOrder request
	 *
	 * Use setCountryCode() to specify the country code matching the original
	 * createOrder request.
	 *
	 * @param string $countryCodeAsString
	 * @return $this
	 */
	public function setCountryCode($countryCodeAsString)
	{
		$this->countryCode = $countryCodeAsString;

		return $this;
	}

	/**
	 * Optional for deliverCardOrder() -- use the order id (transaction id) received with the createOrder response.
	 *
	 * This is an alias for setOrderId().
	 *
	 * @param string $orderIdAsString
	 * @return $this
	 */
	public function setTransactionId($orderIdAsString)
	{
		return $this->setOrderId($orderIdAsString);
	}

	/**
	 * Required -- must match the invoice distribution type for the order
	 *
	 * @param DistributionType $distributionTypeAsConst -  i.e.
	 *	 Svea\WebPay\Constant\DistributionType::POST|Svea\WebPay\Constant\DistributionType::EMAIL
	 * @return $this
	 */
	public function setInvoiceDistributionType($distributionTypeAsConst)
	{
		$this->distributionType = $distributionTypeAsConst;

		return $this;
	}

	/**
	 * Required -- a row number to deliver
	 *
	 * Use setRowToDeliver() or setRowsToDeliver() to specify order rows to deliver.
	 * The given row numbers must correspond with the serverside row numbers.
	 *
	 * For card orders, the rows must match order rows specified using addNumberedOrderRow() or addNumberedOrderRows().
	 *
	 * For invoice orders, the serverside order rows is updated after a deliverOrderRows request.
	 * Note that for Card and  orders the serverside order rows will not be updated.
	 *
	 * @param string $rowNumber
	 * @return $this
	 */
	public function setRowToDeliver($rowNumber)
	{
		$this->rowsToDeliver[] = $rowNumber;

		return $this;
	}

	/**
	 * Optional -- convenience method to provide several row numbers at once.
	 *
	 * @param int[] $rowNumbers
	 * @return $this
	 */
	public function setRowsToDeliver($rowNumbers)
	{
		$this->rowsToDeliver = array_merge($this->rowsToDeliver, $rowNumbers);

		return $this;
	}

	/**
	 * Required for card orders -- add information on a single numbered order row
	 *
	 * For card orders, it is required to use addNumberedOrderRow() or addNumberedOrderRows()
	 * to pass in a copy of the serverside order row data. All order rows must be supplied.
	 *
	 * You can use the Svea\WebPay\WebPayAdmin::queryOrder() entrypoint to get information about the order,
	 * the QueryOrderResponse->numberedOrderRows attribute contains the order rows, but
	 * note that if the order has been modified after creation these may not be accurate.
	 *
	 * @param \Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow $numberedOrderRows instance of NumberedOrderRow
	 * @return $this
	 */
	public function addNumberedOrderRow($numberedOrderRow)
	{
		$this->numberedOrderRows[] = $numberedOrderRow;

		return $this;
	}

	/**
	 * Optional for card orders -- convenience method to provide several numbered order rows at once.
	 *
	 * @param \Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow[] $numberedOrderRows array of NumberedOrderRow
	 * @return $this
	 */
	public function addNumberedOrderRows($numberedOrderRows)
	{
		$this->numberedOrderRows = array_merge($this->numberedOrderRows, $numberedOrderRows);

		return $this;
	}

	/**
	 * Use deliverInvoiceOrderRows() to deliver rows to an Invoice order using AdminServiceRequest DeliverOrderRows
	 * request
	 * @return DeliverOrderRowsRequest
	 */
	public function deliverInvoiceOrderRows()
	{
		$this->orderType = ConfigurationProvider::INVOICE_TYPE;

		$this->validateDeliverInvoiceOrderRows();

		return new DeliverOrderRowsRequest($this);
	}

	/**
	 * Use deliverAccountCreditOrderRows() to deliver rows to an AccountCredit order using AdminServiceRequest DeliverOrderRows
	 * request
	 * @return DeliverOrderRowsRequest
	 */
	public function deliverAccountCreditOrderRows()
	{
		$this->orderType = ConfigurationProvider::ACCOUNTCREDIT_TYPE;

		$this->validateDeliverInvoiceOrderRows();

		return new DeliverOrderRowsRequest($this);
	}

	/**
	 * Use deliverCardOrderRows() to deliver rows to an Card order using HostedService requests.
	 *
	 * Then use deliverInvoiceOrderRows() or deliver CardOrderRows to get a request object,
	 * which ever matches the payment method used in the original order. deliverCardOrderRows
	 * Calculates the correct amount to deliver from supplied order rows and when followed by a
	 * a ->doRequest() call performs a LowerTransaction followed by a ConfirmTransaction. Note
	 * that the card transaction must have status AUTHORIZED at Svea in order to be delivered.
	 *
	 * @return ConfirmTransactionResponse
	 */
	public function deliverCardOrderRows()
	{
		$this->orderType = ConfigurationProvider::HOSTED_TYPE;

		$this->validateDeliverCardOrderRows();

		$total_amount = 0;
		foreach ($this->numberedOrderRows as $row) {
			$total_amount += ($row->amountExVat * (1 + $row->vatPercent / 100));
		}

		$total_delivered = 0;
		foreach ($this->rowsToDeliver as $row_index) {
			$row = $this->numberedOrderRows[$row_index - 1];
			$total_delivered += ($row->amountExVat * (1 + $row->vatPercent / 100));
		}

		$amountToLower = $total_amount - $total_delivered;
		$amountToLower *= 100; // minor currency

		$lowerTransactionRequest = new LowerTransaction($this->conf);
		$lowerTransactionRequest->countryCode = $this->countryCode;
		$lowerTransactionRequest->transactionId = $this->orderId;
		$lowerTransactionRequest->amountToLower = $amountToLower;
		$lowerTransactionRequest->alsoDoConfirm = true;

		return $lowerTransactionRequest;
	}

	/**
	 * Use deliverCheckoutOrderRows() to deliver rows to an Checkout Order.
	 * @return DeliverOrderService
	 * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
	 * @throws \Exception
	 */
	public function deliverCheckoutOrderRows()
	{
		return new DeliverOrderService($this, true);
	}

	/**
	 * Use deliverCheckoutOrder() to deliver whole Checkout Order.
	 * @return DeliverOrderService
	 * @throws \Svea\WebPay\BuildOrder\Validator\ValidationException
	 * @throws \Exception
	 */
	public function deliverCheckoutOrder()
	{
		return new DeliverOrderService($this);
	}

	/**
	 * @internal
	 */
	private function validateDeliverInvoiceOrderRows()
	{
		if (!isset($this->orderId)) {
			$exceptionString = "orderId is required for deliverInvoiceOrderRows(). Use method setOrderId().";
			throw new ValidationException($exceptionString);
		}

		if (!isset($this->countryCode)) {
			$exceptionString = "countryCode is required for deliverInvoiceOrderRows(). Use method setCountryCode().";
			throw new ValidationException($exceptionString);
		}

		if (!isset($this->distributionType)) {
			$exceptionString = "distributionType is required for deliverInvoiceOrderRows(). Use method setInvoiceDistributionType().";
			throw new ValidationException($exceptionString);
		}

		if ((count($this->rowsToDeliver) == 0)) {
			$exceptionString = "rowsToDeliver is required for deliverInvoiceOrderRows(). Use methods setRowToDeliver() or setRowsToDeliver().";
			throw new ValidationException($exceptionString);
		}
	}

	/**
	 * @internal
	 */
	private function validateDeliverCardOrderRows()
	{
		if (!isset($this->orderId)) {
			$exceptionString = "orderId is required for deliverInvoiceOrderRows(). Use method setOrderId().";
			throw new ValidationException($exceptionString);
		}

		if (!isset($this->countryCode)) {
			$exceptionString = "countryCode is required for deliverInvoiceOrderRows(). Use method setCountryCode().";
			throw new ValidationException($exceptionString);
		}

		if ((count($this->rowsToDeliver) == 0)) {
			$exceptionString = "rowsToDeliver is required for deliverCardOrderRows(). Use methods setRowToDeliver() or setRowsToDeliver().";
			throw new ValidationException($exceptionString);
		}

		if ((count($this->numberedOrderRows) == 0)) {
			$exceptionString = "numberedOrderRows is required for deliverCardOrderRows(). Use setNumberedOrderRow() or setNumberedOrderRows().";
			throw new ValidationException($exceptionString);
		}
	}
}
