<?php
namespace Svea\HostedService;

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 * Go to PayPage and exclude all methods that are not direct bank payments.
 * 
 * Send customer to *PayPage* to select from available banks (only). The customer
 * then performs a direct bank payment at the chosen bank.
 * 
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

    /**
     * configureExcludedPaymentMethods returns a list of payment methods not to present on the paypage for this payment method method class.
     * @return string[] the list of excluded payment methods, @see SystemPaymentMethod
     */
    protected function configureExcludedPaymentMethods() {
        // first, exclude all invoice/paymentplan payment methods
        $methods = ExcludePayments::excludeInvoicesAndPaymentPlan();
        //card
        $methods[] = \Svea\SystemPaymentMethod::KORTCERT;
        $methods[] = \Svea\SystemPaymentMethod::SKRILL;
        //other
        $methods[] = \Svea\SystemPaymentMethod::PAYPAL;

        return $methods;
    }

    /**
     * calculateRequestValues adds the payment methods not to present on the 
     * paypage to the request array
     */
    public function calculateRequestValues() {               
        $this->request['excludePaymentMethods'] = $this->configureExcludedPaymentMethods();        
        return parent::calculateRequestValues();       
    }    
    
}
