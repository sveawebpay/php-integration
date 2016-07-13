<?php

namespace Svea\WebPay\HostedService\Payment;

use Svea\WebPay\BuildOrder\CreateOrderBuilder;
use Svea\WebPay\Constant\SystemPaymentMethod;
use Svea\WebPay\HostedService\Helper\ExcludePayments;

/**
 * Goes to PayPage and excludes all methods that are not card payments
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CardPayment extends HostedPayment
{       
    /**
     * Creates a new CardPayment containing a given order.
     * @param CreateOrderBuilder $order
     */
    public function __construct($order)
    {
        parent::__construct($order);
    }

    /**
     * configureExcludedPaymentMethods returns a list of payment methods not to
     * present on the paypage for this payment method method class.
     * @return string[] the list of excluded payment methods, @see SystemPaymentMethod
     */
    protected function configureExcludedPaymentMethods()
    {
        // first, exclude all invoice/paymentplan payment methods
        $methods = ExcludePayments::excludeInvoicesAndPaymentPlan();

        //directbanks
        $methods[] = SystemPaymentMethod::BANKAXESS;
        $methods[] = SystemPaymentMethod::DBNORDEASE;
        $methods[] = SystemPaymentMethod::DBSEBSE;
        $methods[] = SystemPaymentMethod::DBSEBFTGSE;
        $methods[] = SystemPaymentMethod::DBSHBSE;
        $methods[] = SystemPaymentMethod::DBSWEDBANKSE;
        
        //other
        $methods[] = SystemPaymentMethod::PAYPAL;

        return $methods;
    }

    /**
     * calculateRequestValues adds the payment methods not to present on the
     * paypage to the request array
     */
    public function calculateRequestValues()
    {
        $this->request['excludePaymentMethods'] = $this->configureExcludedPaymentMethods();

        return parent::calculateRequestValues();
    }
}
