<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\HostedService\Payment\CardPayment;
use Svea\WebPay\WebService\Payment\AccountCredit;
use Svea\WebPay\WebService\Payment\InvoicePayment;
use Svea\WebPay\HostedService\Payment\DirectPayment;
use Svea\WebPay\HostedService\Payment\PayPagePayment;
use Svea\WebPay\WebService\Payment\PaymentPlanPayment;
use Svea\WebPay\HostedService\Payment\PaymentMethodPayment;

/**
 * CreateOrderBuilder collects and prepares order data to be sent using one of Svea's payment methods.
 *
 * Add order row items, fees and discounts along with customer information details to the order builder instance,
 * and set all required order attributes using the order builder methods. Finish by selecting which payment method
 * to use.
 *
 * You can then go on specifying any payment method specific settings, using the methods provided by the
 * payment method request class.
 *
 * The order builder and request class instance methods can be chained together in a fluent manner.
 *
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CreateOrderBuilder extends OrderBuilder
{

    /**
     * Use useInvoicePayment to initiate an invoice payment.
     *
     * Set additional attributes using InvoicePayment methods.
     *
     * @return InvoicePayment
     */
    public function useInvoicePayment()
    {
        return new InvoicePayment($this);
    }

    /**
     * Use usePaymentPlanPayment to initate a payment plan payment.
     *
     * You can use Svea\WebPay\WebPay::getPaymentPlanParams() to get available campaign codes (payment plans).
     *
     * Set additional attributes using PaymentPlanPayment methods.
     *
     * @see \WebPay::getPaymentPlanParams() Svea\WebPay\WebPay::getPaymentPlanParams()
     *
     * @param string $campaignCodeAsString
     * @param int $sendAutomaticGiroPaymentFormAsBool (optional boolean)
     * @return PaymentPlanPayment
     */
    public function usePaymentPlanPayment($campaignCodeAsString, $sendAutomaticGiroPaymentFormAsBool = 0)
    {
        $this->campaignCode = $campaignCodeAsString;
        $this->sendAutomaticGiroPaymentForm = $sendAutomaticGiroPaymentFormAsBool;

        return new PaymentPlanPayment($this);
    }

    /**
     * Use useAccountCredit to initate a Account Credit payment.
     *
     * You can use Svea\WebPay\WebPay::getAccountCreditParams() to get available campaign codes (payment plans).
     *
     *
     * @see \WebPay::getAccountCreditParams() Svea\WebPay\WebPay::getAccountCreditParams()
     *
     * @param string $campaignCode
     * @return AccountCredit
     */
    public function useAccountCredit($campaignCode)
    {
        $this->campaignCode = $campaignCode;

        return new AccountCredit($this);
    }

    /**
     * Use usePaymentMethod to initate a payment bypassing the PayPage completely, going straight to the payment method
     * specified. This is the preferred way to perform a payment, as it cuts down on the number of payment steps in the
     * end user checkout flow.
     *
     * You can use Svea\WebPay\WebPay::getPaymentMethods() to get available payment methods. See also the
     * Svea\WebPay\Constant\PaymentMethod class constants.
     *
     * Set additional attributes using PaymentMethodPayment methods.
     *
     * @see \WebPay::getPaymentMethods() Svea\WebPay\WebPay::getPaymentMethods()
     * @see \PaymentMethod Svea\WebPay\Constant\PaymentMethod
     *
     * @param string $paymentMethodAsConst i.e. Svea\WebPay\Constant\PaymentMethod::SEB_SE et al
     * @return PaymentMethodPayment
     */
    public function usePaymentMethod($paymentMethodAsConst)
    {
        return new PaymentMethodPayment($this, $paymentMethodAsConst);
    }

    /**
     * Use usePayPageCardOnly to initate a card payment via PayPage, showing only the available card payment methods.
     *
     * Set additional attributes using CardPayment methods.
     * @return CardPayment
     */
    public function usePayPageCardOnly()
    {
        return new CardPayment($this);
    }

    /**
     * Use usePayPageDirectBankOnly to initate a direct bank payment via PayPage, showing only the available direct
     * bank payment methods.
     *
     * Set additional attributes using DirectPayment methods.
     * @return DirectPayment
     */
    public function usePayPageDirectBankOnly()
    {
        return new DirectPayment($this);
    }

    /**
     * Use usePayPage to initate a payment via PayPage, showing all available payment methods to the user.
     *
     * Set additional attributes using PayPagePayment methods.
     * @return PayPagePayment
     */
    public function usePayPage()
    {
        $paypagepayment = new PayPagePayment($this);

        return $paypagepayment;
    }

    /**
     * @param \Svea\WebPay\Config\ConfigurationProvider $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
    }

    /**
     * @internal for testfunctions
     * @param type $func
     * @return $this
     */
    public function run($func)
    {
        call_user_func($func, $this);

        return $this;
    }
}
