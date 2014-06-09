<?php
namespace Svea\HostedService;

/**
 * @author anne-hal
 */
class ExcludePayments {

    /**
     * Fetch an array of all paymentmethods representing a payment plan or invoice payment.
     * @ignore @param type $countryCode -- ignored
     * @return string[] 
     */
    public static function excludeInvoicesAndPaymentPlan() {
        $methods = array();

        $methods[] = \Svea\SystemPaymentMethod::INVOICESE;
        $methods[] = \Svea\SystemPaymentMethod::PAYMENTPLANSE;
        $methods[] = \Svea\SystemPaymentMethod::INVOICE_SE;
        $methods[] = \Svea\SystemPaymentMethod::PAYMENTPLAN_SE;

        $methods[] = \Svea\SystemPaymentMethod::INVOICE_DE;
        $methods[] = \Svea\SystemPaymentMethod::PAYMENTPLAN_DE;

        $methods[] = \Svea\SystemPaymentMethod::INVOICE_DK;
        $methods[] = \Svea\SystemPaymentMethod::PAYMENTPLAN_DK;

        $methods[] = \Svea\SystemPaymentMethod::INVOICE_FI;
        $methods[] = \Svea\SystemPaymentMethod::PAYMENTPLAN_FI;

        $methods[] = \Svea\SystemPaymentMethod::INVOICE_NL;
        $methods[] = \Svea\SystemPaymentMethod::PAYMENTPLAN_NL;

        $methods[] = \Svea\SystemPaymentMethod::INVOICE_NO;
        $methods[] = \Svea\SystemPaymentMethod::PAYMENTPLAN_NO;

        return $methods;
    }
}
