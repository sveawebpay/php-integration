<?php
namespace Svea;

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 * Goes to PayPage and excludes all methods that are not direct payments
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class DirectPayment extends HostedPayment {

    /**
     * Creates a new DirectPayment containing a given order.
     * @param CreateOrderBuilder $order
     */
    public function __construct($order) {
        parent::__construct($order);      
    }

    protected function configureExcludedPaymentMethods($request) {
        //card
        $methods[] = SystemPaymentMethod::KORTCERT;
        $methods[] = SystemPaymentMethod::SKRILL;
        //other
        $methods[] = SystemPaymentMethod::PAYPAL;

        $exclude = new ExcludePayments();
        $methods = array_merge((array)$methods, (array)$exclude->excludeInvoicesAndPaymentPlan($this->order->countryCode));

        $request['excludePaymentMethods'] = $methods;
        return $request;
    }

}
