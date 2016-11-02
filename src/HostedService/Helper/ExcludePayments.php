<?php

namespace Svea\WebPay\HostedService\Helper;

use Svea\WebPay\Constant\SystemPaymentMethod;

/**
 * @author anne-hal
 */
class ExcludePayments
{
    /**
     * Fetch an array of all paymentmethods representing a payment plan or invoice payment.
     * @ignore @param type $countryCode -- ignored
     * @return string[]
     */
    public static function excludeInvoicesAndPaymentPlan()
    {
        $methods = array();

        $methods[] = SystemPaymentMethod::INVOICESE;
        $methods[] = SystemPaymentMethod::PAYMENTPLANSE;
        $methods[] = SystemPaymentMethod::INVOICE_SE;
        $methods[] = SystemPaymentMethod::PAYMENTPLAN_SE;

        $methods[] = SystemPaymentMethod::INVOICE_DE;
        $methods[] = SystemPaymentMethod::PAYMENTPLAN_DE;

        $methods[] = SystemPaymentMethod::INVOICE_DK;
        $methods[] = SystemPaymentMethod::PAYMENTPLAN_DK;

        $methods[] = SystemPaymentMethod::INVOICE_FI;
        $methods[] = SystemPaymentMethod::PAYMENTPLAN_FI;

        $methods[] = SystemPaymentMethod::INVOICE_NL;
        $methods[] = SystemPaymentMethod::PAYMENTPLAN_NL;

        $methods[] = SystemPaymentMethod::INVOICE_NO;
        $methods[] = SystemPaymentMethod::PAYMENTPLAN_NO;

        return $methods;
    }
}
