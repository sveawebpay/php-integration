<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\AdminService\CancelOrderRowsRequest;
use Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow;
use Svea\WebPay\Checkout\Service\Admin\CancelOrderRowService;
use Svea\WebPay\Checkout\Service\Admin\CancelOrderService;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\HostedService\HostedAdminRequest\LowerTransaction;

/**
 * CancelOrderRowsBuilder is used to cancel individual order rows in a unified manner.
 *
 * For Invoice and Payment Plan orders, the order row status of the order is updated
 * to reflect the new status of the order rows.
 *
 * For Card orders, individual order rows will still reflect the status they got in
 * order creation, even if orders have since been cancelled, and the order amount
 * to be charged is simply lowered by sum of the order rows' amount.
 *
 * Use setOrderId() to specify the Svea order id, this is the order id returned
 * with the original create order request response.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 *
 * Use setRowToCancel or setRowsToCancel() to specify the order row(s) to cancel. The
 * order numbers should correspond to those returned by i.e. Svea\WebPay\WebPayAdmin::queryOrder;
 *
 * For card orders, use addNumberedOrderRow() or addNumberedOrderRows() to pass in
 * numbered order rows (from i.e. queryOrder) that will be matched with set rows to cancel.
 *
 * (You can use the Svea\WebPay\WebPayAdmin::queryOrder() entrypoint to get information about the order, the
 * queryOrder response attribute numberedOrderRows contains the serverside order rows w/numbers.
 * Note: if card order rows has been changed (i.e. credited, cancelled) after initial creation,
 * the returned rows may not be accurate.)
 *
 * Then use either cancelInvoiceOrderRows(), cancelPaymentPlanOrderRows or cancelCardOrderRows,
 * which ever matches the payment method used in the original order request.
 *
 * The final doRequest() will send the queryOrder request to Svea, and the
 * resulting response code specifies the outcome of the request.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CancelOrderRowsBuilder extends CheckoutAdminOrderBuilder
{

	/**
	 * @var ConfigurationProvider $conf
	 */
	public $conf;

	/**
	 * @var int[] $rowsToCancel
	 */
	public $rowsToCancel;

	/**
	 * @var NumberedOrderRow[] $numberedOrderRows
	 */
	public $numberedOrderRows;

	/**
	 * @var string $orderId Svea order id to query, as returned in the createOrder request response,
	 * either a transactionId or a SveaOrderId
	 */
	public $orderId;

	/**
	 * @var string $countryCode
	 */
	public $countryCode;

	/**
	 * @var string $orderType -- one of Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::HOSTED_TYPE
	 */
	public $orderType;

	/**
	 * CancelOrderRowsBuilder constructor.
	 * @param $config
	 */
	public function __construct($config)
	{
		parent::__construct($config);
		$this->rowsToCancel = [];
		$this->numberedOrderRows = [];
	}

	/**
	 * Optional for card orders -- use the order id (transaction id) received with the createOrder response.
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
	 * Required. Use same countryCode as in createOrder request.
	 * @param string $countryCodeAsString
	 * @return $this
	 */
	public function setCountryCode($countryCodeAsString)
	{
		$this->countryCode = $countryCodeAsString;

		return $this;
	}

	/**
	 * Required - add a row number to cancel
	 * @param int $rowNumber
	 * @return $this
	 */
	public function setRowToCancel($rowNumber)
	{
		$this->rowsToCancel[] = $rowNumber;

		return $this;
	}

	/**
	 * Optional - convenience method to provide several row numbers at once.
	 * @param int[] $rowNumbers
	 * @return $this
	 */
	public function setRowsToCancel($rowNumbers)
	{
		$this->rowsToCancel = array_merge($this->rowsToCancel, $rowNumbers);

		return $this;
	}

	/**
	 * Required - add information on a single numbered order row
	 *
	 * When cancelling card order rows, you must pass in information about the row
	 * along with the request. The rows are then matched with the order rows specified
	 * using setRow(s)ToCredit().
	 *
	 * Note: the card order does not update the state of any cancelled order rows, only
	 * the total order amount to be charged.
	 *
	 * @param NumberedOrderRow $rowNumber - instance of NumberedOrderRow
	 * @return $this
	 */
	public function addNumberedOrderRow($rowNumber)
	{
		$this->numberedOrderRows[] = $rowNumber;

		return $this;
	}

	/**
	 * Use cancelCheckoutOrderRows() to cancel a Checkout Order
	 * @return CancelOrderRowService
	 * @throws ValidationException
	 * @throws \Exception
	 */
	public function cancelCheckoutOrderRows()
	{
		return new CancelOrderRowService($this);
	}

	/**
	 * Optional - Convenience method to provide several numbered order rows at once.
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
	 * Use cancelCardOrderRows() to lower the amount of a Card order by the specified order row amounts using HostedRequests LowerTransaction request
	 *
	 * @return LowerTransaction
	 * @throws ValidationException  if addNumberedOrderRows() has not been used.
	 */
	public function cancelCardOrderRows()
	{
		$this->orderType = ConfigurationProvider::HOSTED_ADMIN_TYPE;

		$this->validateCancelCardOrderRows();
		$sumOfRowAmounts = $this->calculateSumOfRowAmounts($this->rowsToCancel, $this->numberedOrderRows);

		$lowerTransaction = new LowerTransaction($this->conf);
		$lowerTransaction->countryCode = $this->countryCode;
		$lowerTransaction->transactionId = $this->orderId;
		$lowerTransaction->amountToLower = $sumOfRowAmounts * 100; // *100, as setAmountToLower wants minor currency
		return $lowerTransaction;
	}

	private function validateCancelCardOrderRows()
	{
		if (count($this->numberedOrderRows) == 0) {
			$exceptionString = "numberedOrderRows is required for cancelCardOrderRows(). Use method addNumberedOrderRows().";
			throw new ValidationException($exceptionString);
		}
		if (count($this->rowsToCancel) == 0) {
			$exceptionString = "rowsToCancel is required for cancelCardOrderRows(). Use method setRowToCancel() or setRowsToCancel.";
			throw new ValidationException($exceptionString);
		}
	}

	private function calculateSumOfRowAmounts($rowIndexes, $numberedRows)
	{
		$sum = 0.0;
		$unique_indexes = array_unique($rowIndexes);
		foreach ($numberedRows as $numberedRow) {
			if (in_array($numberedRow->rowNumber, $unique_indexes)) {
				$sum += ($numberedRow->quantity * ($numberedRow->amountExVat * (1 + ($numberedRow->vatPercent / 100))));
			}
		}

		return $sum;
	}

	/**
	 * Use cancelInvoiceOrderRows() to cancel an Invoice order using AdminServiceRequest CancelOrderRows request
	 * @return CancelOrderRowsRequest
	 */
	public function cancelInvoiceOrderRows()
	{
		$this->orderType = ConfigurationProvider::INVOICE_TYPE;

		return new CancelOrderRowsRequest($this);
	}

	/**
	 * Use cancelPaymentPlanOrderRows() to cancel a PaymentPlan order using AdminServiceRequest CancelOrderRows request
	 * @return CancelOrderRowsRequest
	 */
	public function cancelPaymentPlanOrderRows()
	{
		$this->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;

		return new CancelOrderRowsRequest($this);
	}

	/**
	 * Use cancelAccountCreditOrderRows() to cancel a AccountCredit order using AdminServiceRequest CancelOrderRows request
	 * @return CancelOrderRowsRequest
	 */
	public function cancelAccountCreditOrderRows()
	{
		$this->orderType = ConfigurationProvider::ACCOUNTCREDIT_TYPE;

		return new CancelOrderRowsRequest($this);
	}
}
