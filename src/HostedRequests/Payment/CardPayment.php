<?php
namespace Svea;

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 * Goes to PayPage and excludes all methods that are not card payments
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class CardPayment extends HostedPayment {

    /**
     * Creates a new CardPayment containing a given order.
     * @param CreateOrderBuilder $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    /**
     * configureExcludedPaymentMethods injects the 'excludePaymentMethods' attribute
     * in the passed request array.
     * 
     * The methods excluded are 1) listed in the method, and 2) fetched from the
     * ExcludePayments() class (for various country invoice and paymentplan methods)
     * 
     * @param array  $request
     * @return array  the passed $request, incl. an 'excludePaymentMethods' attribute
     */
    protected function configureExcludedPaymentMethods($request) {
        //directbanks
        $methods[] = SystemPaymentMethod::BANKAXESS;
        $methods[] = SystemPaymentMethod::DBNORDEASE;
        $methods[] = SystemPaymentMethod::DBSEBSE;
        $methods[] = SystemPaymentMethod::DBSEBFTGSE;
        $methods[] = SystemPaymentMethod::DBSHBSE;
        $methods[] = SystemPaymentMethod::DBSWEDBANKSE;
        //other
        $methods[] = SystemPaymentMethod::PAYPAL;
        //get invoice and payment plan methods for all countries
        $exclude = new ExcludePayments();
        $methods = array_merge((array)$methods, (array)$exclude->excludeInvoicesAndPaymentPlan($this->order->countryCode));

        $request['excludePaymentMethods'] = $methods;
        return $request;
    }

}
