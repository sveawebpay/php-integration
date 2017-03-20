<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\Checkout\Service\Admin\CancelOrderService;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\WebService\HandleOrder\CloseOrder;
use Svea\WebPay\HostedService\HostedAdminRequest\AnnulTransaction;

/**
 * CancelOrderBuilder is the class used to cancel an order with Svea, that has
 * not yet been delivered (invoice, payment plan) or been confirmed (card).
 *
 * Use setOrderId() to specify the Svea order id, this is the order id returned
 * with the original create order request response.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 *
 * Use either cancelInvoiceOrder(), cancelPaymentPlanOrder or cancelCardOrder,
 * which ever matches the payment method used in the original order request.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CancelOrderBuilder extends CheckoutAdminOrderBuilder
{ 
    /**
     * @var ConfigurationProvider $conf
     */
    public $conf;

    /**
     * @var string $orderId Svea order id to cancel, as returned in the createOrder request response,
     * either a transactionId or a SveaOrderId
     */
    public $orderId;

    /**
     * @var string $countryCode
     */
    public $countryCode;

    /**
     * @var ConfigurationProvider::INVOICE_TYPE, ::ACCCOUNTCREDIT_TYPE or ::PAYMENTPLAN_TYPE
     */
    public $orderType;

    /**
     * Optional -- alias for setOrderId().
     * @param string $transactionIdAsString
     * @return $this
     */
    public function setTransactionId($transactionIdAsString) {
        return $this->setOrderId($transactionIdAsString);
    }

    /**
     * Required. Use same country code as in createOrder request.
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
     * Use cancelInvoiceOrder() to close an Invoice order.
     *
     * Use the method corresponding to the original createOrder payment method.
     *
     * @return CloseOrder
     */
    public function cancelInvoiceOrder()
    {
        $this->orderType = ConfigurationProvider::INVOICE_TYPE;

        return new CloseOrder($this);
    }

    /**
     * Use cancelPaymentPlanOrder() to close a PaymentPlan order.
     *
     * Use the method corresponding to the original createOrder payment method.
     *
     * @return CloseOrder
     */
    public function cancelPaymentPlanOrder()
    {
        $this->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;

        return new CloseOrder($this);
    }

    /**
     * Use cancelAccountCreditOrder() to close a AccountCredit order.
     *
     * Use the method corresponding to the original createOrder payment method.
     *
     * @return CloseOrder
     */
    public function cancelAccountCreditOrder()
    {
        $this->orderType = ConfigurationProvider::ACCOUNTCREDIT_TYPE;

        return new CloseOrder($this);
    }

    /**
     * Use cancelCardOrder() to close a Card order.
     *
     * Use the method corresponding to the original createOrder payment method.
     *
     * @return AnnulTransaction
     */
    public function cancelCardOrder()
    {
        $this->orderType = ConfigurationProvider::HOSTED_ADMIN_TYPE;
        $annulTransaction = new AnnulTransaction($this->conf);
        $annulTransaction->transactionId = $this->orderId;
        $annulTransaction->countryCode = $this->countryCode;

        return $annulTransaction;
    }

    public function cancelCheckoutOrder()
    {
        return new CancelOrderService($this);
    }

    public function cancelCheckoutOrderAmount()
    {
        return new CancelOrderService($this, true);
    }
}
