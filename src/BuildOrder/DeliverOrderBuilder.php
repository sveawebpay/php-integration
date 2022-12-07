<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\AdminService\DeliverOrdersRequest;
use Svea\WebPay\WebService\HandleOrder\DeliverAccountCredit;
use Svea\WebPay\WebService\HandleOrder\DeliverInvoice;
use Svea\WebPay\WebService\HandleOrder\DeliverPaymentPlan;
use Svea\WebPay\HostedService\HostedAdminRequest\ConfirmTransaction;

/**
 * DeliverOrderBuilder collects and prepares order data for use in a deliver
 * order request to Svea.
 *
 * Use setOrderId() to specify the Svea order id, this is the order id returned
 * with the original create order request response. For card orders, you can
 * optionally use setTransactionId() instead.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 *
 * Use setInvoiceDistributionType() with the Svea\WebPay\Constant\DistributionType matching how your
 * account is configured to send out invoices. (Please contact Svea if unsure.)
 *
 * Use setNumberOfCreditDays() to specify the number of credit days for an invoice.
 *
 * (Deprecated -- to partially deliver an invoice order, you can specify order rows to deliver
 * using the addOrderRows() method. Use the Svea\WebPay\WebPayAdmin::deliverOrderRows entrypoint instead.)
 *
 * (Deprecated -- to issue a credit invoice, you can specify credit order rows to deliver using
 * setCreditInvoice() and addOrderRows(). Use the Svea\WebPay\WebPayAdmin::creditOrderRow entrypoint instead.)
 *
 * To deliver an invoice, partpayment or card order in full, use the Svea\WebPay\WebPay::deliverOrder
 * entrypoint without specifying order rows.
 *
 * When specifying orderrows, Svea\WebPay\WebPay::deliverOrder is used in a similar way to
 * Svea\WebPay\WebPay::createOrder and makes use of the same order item information. Add order rows that you want
 * delivered and send the request, specified rows will automatically be matched to the rows sent when creating the
 * order.
 *
 * We recommend storing the createOrder orderRow objects to ensure that deliverOrder order rows match.
 * If an order row that was present in the createOrder request is not present in from the deliverOrder
 * request, the order will be partially delivered, and any left out items will not be invoiced by Svea.
 * You cannot partially deliver payment plan orders, where all un-cancelled order rows will be delivered.
 *
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DeliverOrderBuilder  extends CheckoutAdminOrderBuilder
{
	/**
	 * @var string $orderId order id/transaction id as returned in the createOrder request response,
	 */
	public $orderId;

	/**
	 * @var string $distributionType -- one of Svea\WebPay\Constant\DistributionType::POST, ::EMAIL
	 */
	public $distributionType;

	/**
	 * @var string $captureDate -- confirmation date on format "YYYY-MM-DD"
	 */
	public $captureDate;

	/**
	 * If Invoice is to be credit Invoice
	 * @var $invoiceIdToCredit - Invoice Id
	 */
	public $invoiceIdToCredit;

	/**
	 * @var int $numberOfCreditDays
	 */
	public $numberOfCreditDays;

	/**
	 * @var string
	 * \Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::ACCOUNTCREDIT_TYPE or ::HOSTED_TYPE
	 */
	public $orderType;

	/**
	 * @param \Svea\WebPay\Config\ConfigurationProvider $config
	 */
	public function __construct($config)
	{
		parent::__construct($config);
	}

	/**
	 * @deprecated 2.0.0 -- use Svea\WebPay\WebPayAdmin::updateOrderRows in order to modify an existing order.
	 *
	 * 2.x: Optional. Use setOrderRows to add invoice order rows to deliver.
	 * Rows matching the original create order request order rows will be
	 * invoiced by Svea. If not all order rows match, an invoice order will be
	 * partially delivered/invoiced, see the Svea Web Service EU API
	 * documentation for details.
	 * @param \Svea\WebPay\BuildOrder\RowBuilders\OrderRow $itemOrderRowObject
	 * @return $this
	 */
	public function addOrderRow($itemOrderRowObject)
	{
		return parent::addOrderRow($itemOrderRowObject);
	}

	/**
	 * Required for invoice or part payment orders -- use the order id (transaction id) recieved with the createOrder
	 * response.
	 * @param string $orderIdAsString
	 * @return $this
	 */
	public function setOrderId($orderIdAsString)
	{
		$this->orderId = $orderIdAsString;

		return $this;
	}

	/**
	 * Optional -- alias for setOrderId().
	 * @param string $transactionIdAsString
	 * @return $this
	 */
	public function setTransactionId($transactionIdAsString)
	{
		return $this->setOrderId($transactionIdAsString);
	}

	/**
	 * Optional for card orders -- confirmation date on format "YYYY-MM-DD"
	 *
	 * If no date is given the current date is used per default.
	 *
	 * @param $captureDateAsString
	 * @return $this
	 */
	public function setCaptureDate($captureDateAsString)
	{
		$this->captureDate = $captureDateAsString;

		return $this;
	}

	/**
	 * Invoice only, required.
	 * @param string $distributionTypeAsConst - Svea\WebPay\Constant\DistributionType i.e.
	 *	 Svea\WebPay\Constant\DistributionType::POST|Svea\WebPay\Constant\DistributionType::EMAIL
	 * @return $this
	 */
	public function setInvoiceDistributionType($distributionTypeAsConst)
	{
		$this->distributionType = $distributionTypeAsConst;

		return $this;
	}

	/**
	 * Invoice only, optional
	 * Use if this should be a credit invoice
	 * @param $invoiceId
	 * @return $this
	 */
	public function setCreditInvoice($invoiceId)
	{
		$this->invoiceIdToCredit = $invoiceId;

		return $this;
	}

	/**
	 * Invoice only, optional
	 * @param int $numberOfDaysAsInt
	 * @return $this
	 */
	public function setNumberOfCreditDays($numberOfDaysAsInt)
	{
		$this->numberOfCreditDays = $numberOfDaysAsInt;

		return $this;
	}

	/**
	 * To ensure backwards compatibility, deliverInvoiceOrder() checks if the
	 * order has any order rows defined, and if so performs a DeliverOrderEU
	 * request to Svea, passing on the order rows.
	 *
	 * If no order rows are defined, deliverInvoiceOrder() performs a
	 * DeliverOrders request using the Admin Web Service API at Svea.
	 *
	 * @return DeliverInvoice|DeliverOrdersRequest
	 */
	public function deliverInvoiceOrder()
	{
		if (count($this->orderRows) > 0) {
			return new DeliverInvoice($this);
		} else {
			$this->orderType = ConfigurationProvider::INVOICE_TYPE;

			return new DeliverOrdersRequest($this);
		}
	}

	/**
	 * deliverPaymentPlanOrder prepares the PaymentPlan order for delivery.
	 * @return DeliverPaymentPlan
	 */
	public function deliverPaymentPlanOrder()
	{
		$this->distributionType = DistributionType::POST;

		return new DeliverPaymentPlan($this);
	}

	/**
	 * deliverAccountCreditOrder prepares the AccountPlan order for delivery.
	 * @return DeliverAccountCredit
	 */
	public function deliverAccountCreditOrder()
	{
		if (count($this->orderRows) > 0) {
			return new DeliverAccountCredit($this);
		} else {
			$this->orderType = ConfigurationProvider::ACCOUNTCREDIT_TYPE;

			return new DeliverOrdersRequest($this);
		}

	}

	/**
	 * deliverCardOrder() sets the status of a card order to CONFIRMED.
	 *
	 * A default capturedate equal to the current date will be supplied. This
	 * may be overridden using the ConfirmTransaction setCaptureDate() method
	 * on the returned ConfirmTransaction object.
	 *
	 * @return ConfirmTransaction
	 */
	public function deliverCardOrder()
	{
		$this->orderType = ConfigurationProvider::HOSTED_TYPE;

		// validation is done in ConfirmTransaction

		// if no captureDate set, use today's date as default.
		if (!isset($this->captureDate)) {
			$defaultCaptureDate = explode("T", date('c')); // [0] contains date part
			$this->captureDate = $defaultCaptureDate[0];
		}

		$confirmTransaction = new ConfirmTransaction($this->conf);
		$confirmTransaction->transactionId = $this->orderId;
		$confirmTransaction->captureDate = $this->captureDate;
		$confirmTransaction->countryCode = $this->countryCode;

		return $confirmTransaction;
	}
}
