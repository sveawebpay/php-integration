<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\AdminService\UpdateOrderRowsRequest;
use Svea\WebPay\Checkout\Service\Admin\UpdateOrderRowsService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow;
use Svea\WebPay\BuildOrder\Validator\ValidationException;

/**
 * Update order rows in a non-delivered invoice or payment plan order.
 * (Card and Direct Bank orders are not supported.)
 *
 * For Invoice and Payment Plan orders, the order row status of the order is updated
 * to reflect the added order rows. If the updated rows order total exceeds the
 * original order total, an error is returned by the service.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 *
 * Use updateOrderRow() or updateOrderRows() to specify the order row(s) to update in the order.
 * The supplied order row numbers must match order rows from the original createOrder request.
 *
 * Then use either updateInvoiceOrderRows() or updatePaymentPlanOrderRows(),
 * which ever matches the payment method used in the original order request.
 *
 * For Checkout order use updateCheckoutOrderRows(),
 * which ever matches the payment method used in the Checkout process.
 *
 * The final doRequest() will send the updateOrderRows request to Svea, and the
 * resulting response code specifies the outcome of the request.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class UpdateOrderRowsBuilder extends CheckoutAdminOrderBuilder
{
    /**
     * @var ConfigurationProvider $conf
     */
    public $conf;

    /**
     * @var NumberedOrderRow[] $numberedOrderRows the updated order rows
     */
    public $numberedOrderRows;

    /**
     * string $orderId  Svea order id to query, as returned in the createOrder request response,
     * either a transactionId or a SveaOrderId
     */
    public $orderId;

    /** @var string $countryCode */
    public $countryCode;

    /** @var string $orderType -- one of Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE */
    public $orderType;


    /**
     * UpdateOrderRowsBuilder constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->conf = $config;
        $this->numberedOrderRows = array();
    }

    /**
     * Required. Use SveaOrderId recieved with createOrder response.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
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
     * Required.
     * @param NumberedOrderRow $row
     * @return $this
     */
    public function updateOrderRow($row)
    {
        $this->numberedOrderRows[] = $row;

        return $this;
    }

    /**
     * Convenience method to add several rows at once.
     * @param NumberedOrderRow[] $rows
     * @return $this
     */
    public function updateOrderRows($rows)
    {
        $this->numberedOrderRows = array_merge($this->numberedOrderRows, $rows);

        return $this;
    }

    /**
     * Use updateInvoiceOrderRows() to update an Invoice order using AdminServiceRequest UpdateOrderRows request
     * @return UpdateOrderRowsRequest
     */
    public function updateInvoiceOrderRows()
    {
        $this->orderType = ConfigurationProvider::INVOICE_TYPE;

        return new UpdateOrderRowsRequest($this);
    }

    /**
     * Use updatePaymentPlanOrderRows() to update a PaymentPlan order using AdminServiceRequest UpdateOrderRows request
     * @return UpdateOrderRowsRequest
     */
    public function updatePaymentPlanOrderRows()
    {
        $this->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;

        return new UpdateOrderRowsRequest($this);
    }

    public function updateCheckoutOrderRows()
    {
        return new UpdateOrderRowsService($this);
    }
}
