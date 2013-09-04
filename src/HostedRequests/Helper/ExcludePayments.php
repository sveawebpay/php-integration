<?php
namespace Svea;

/**
 * @author anne-hal
 */
class ExcludePayments {

    public function excludeInvoicesAndPaymentPlan($countryCode) {
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
