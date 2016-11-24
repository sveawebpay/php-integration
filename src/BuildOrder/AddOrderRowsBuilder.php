<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\AdminService\AddOrderRowsRequest;
use Svea\WebPay\Checkout\Service\Admin\AddOrderRowService;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\RowBuilders\OrderRow;
use Svea\WebPay\BuildOrder\Validator\ValidationException;

/**
 * AddOrderRowsBuilder is used to add individual order rows to Invoice or Payment Plan orders.
 * (Order rows cannot be added to Card or Direct Bank orders.)
 *
 * For Invoice and Payment Plan orders, the order row status of the order is updated
 * to reflect the added order rows.
 *
 * Use se   tOrderId() to specify the Svea order id, this is the order id returned
 * with the original create order request response.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 *
 * Use addOrderRow() or addOrderRows() to specify the order row(s) to add to the order.
 *
 * Then use either addInvoiceOrderRows() or addPaymentPlanOrderRows(), which ever matches
 * the payment method used in the original order request.
 *
 * The final doRequest() will send the addOrderRows request to Svea, and the
 * resulting response code specifies the outcome of the request.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class AddOrderRowsBuilder extends CheckoutAdminOrderBuilder
{
    /**
     * @var ConfigurationProvider $conf
     */
    public $conf;

    /**
     * @var OrderRow[] $orderRows
     */
    public $orderRows;

    /**
     * string $orderId  Svea order id to query, as returned in the createOrder request response,
     * either a transactionId or a SveaOrderId
     */
    public $orderId;

    /**
     * @var string $countryCode
     */
    public $countryCode;

    /**
     * @var string $orderType -- one of Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE
     */
    public $orderType;


    /**
     * AddOrderRowsBuilder constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->orderRows = array();
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
     * @param $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString)
    {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * Required.
     * @param OrderRow $row
     * @return $this
     */
    public function addOrderRow($row)
    {
        $this->orderRows[] = $row;
        return $this;
    }

    /**
     * Convenience method to add several rows at once.
     * @param \Svea\WebPay\BuildOrder\RowBuilders\OrderRow[] $rows
     * @return $this
     */
    public function addOrderRows($rows)
    {
        $this->orderRows = array_merge($this->orderRows, $rows);
        return $this;
    }

    /**
     * Use addInvoiceOrderRows() to add rows to an Invoice order using AdminServiceRequest AddOrderRows request
     * @return AddOrderRowsRequest
     */
    public function addInvoiceOrderRows()
    {
        $this->orderType = ConfigurationProvider::INVOICE_TYPE;
        return new AddOrderRowsRequest($this);
    }

    /**
     * Use addPaymentPlanOrderRows() to add rows to a PaymentPlan order using AdminServiceRequest AddOrderRows request
     * @return AddOrderRowsRequest
     */
    public function addPaymentPlanOrderRows()
    {
        $this->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;
        return new AddOrderRowsRequest($this);
    }

    /**
     * Use addCheckoutOrderRows() to add rows to a Checkout order
     * @return AddOrderRowService
     * @throws ValidationException
     * @throws \Exception
     */
    public function addCheckoutOrderRows()
    {
        return new AddOrderRowService($this);
    }
}
