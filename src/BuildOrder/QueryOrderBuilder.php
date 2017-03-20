<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\AdminService\AdminSoap\AccountCredit\AccountCreditInformation;
use Svea\WebPay\AdminService\GetOrdersRequest;
use Svea\WebPay\Checkout\Service\Admin\GetOrderService;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction;

/**
 * QueryOrderBuilder is the class used to query information about an order from Svea.
 *
 * Use setOrderId() to specify the Svea order id, this is the order id returned
 * with the original create order request response as either sveaOrderId or transactionId.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 *
 * Then get a request object using either queryInvoiceOrder(), queryPaymentPlanOrder(),
 * queryCardOrder(), or queryDirectBankOrder() or for Checkout order
 * use queryCheckoutOrder() which ever matches the payment method
 * used in the original order request, and send the query request to svea using the
 * request object doRequest() method.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class QueryOrderBuilder extends CheckoutAdminOrderBuilder
{
    /**
     * @var ConfigurationProvider $conf
     */
    public $conf;

    /**
     * @var string $orderId
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

    public function __construct($config) {
        $this->conf = $config;
    }

    /**
     * Required for invoice or part payment orders -- use the order id (transaction id) recieved with the createOrder response.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }
    /**
     * Optional -- alias for setOrderId().
     * @param string $transactionIdAsString
     * @return $this
     */
    public function setTransactionId($transactionIdAsString) {
        return $this->setOrderId($transactionIdAsString);
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
     * Use queryInvoiceOrder() to query an Invoice order.
     * @return GetOrdersRequest
     */
    public function queryInvoiceOrder()
    {
        $this->orderType = ConfigurationProvider::INVOICE_TYPE;
        return new GetOrdersRequest($this);
    }

    /**
     * Use queryPaymentPlanOrder() to query an PaymentPlan order.
     * @return GetOrdersRequest
     */
    public function queryPaymentPlanOrder()
    {
        $this->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;
        return new GetOrdersRequest($this);
    }

    public function queryAccountCreditOder()
    {
        $this->orderType = ConfigurationProvider::ACCOUNTCREDIT_TYPE;
        return new GetOrdersRequest($this);
    }


    /**
     * Use queryCardOrder() to query a Card order.
     * @return QueryTransaction
     */
    public function queryCardOrder()
    {
        $this->orderType = ConfigurationProvider::HOSTED_ADMIN_TYPE;
        $queryTransaction = new QueryTransaction($this->conf);
        $queryTransaction->transactionId = $this->orderId;
        $queryTransaction->countryCode = $this->countryCode;
        return $queryTransaction;
    }

    /**
     * Use queryDirectBankOrder() to query a Direct Bank order.
     * @return QueryTransaction
     */
    public function queryDirectBankOrder()
    {
        $this->orderType = ConfigurationProvider::HOSTED_ADMIN_TYPE;
        $queryTransaction = new QueryTransaction($this->conf);
        $queryTransaction->transactionId = $this->orderId;
        $queryTransaction->countryCode = $this->countryCode;
        return $queryTransaction;
    }

    public function queryCheckoutOrder()
    {
        return new GetOrderService($this);
    }
}
