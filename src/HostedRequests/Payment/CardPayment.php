<?php
namespace Svea\HostedService;

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 * Goes to PayPage and excludes all methods that are not card payments
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class CardPayment extends HostedPayment {
    
    const RECURRINGCAPTURE = "RECURRINGCAPTURE";
    const ONECLICKCAPTURE = "ONECLICKCAPTURE";
    const RECURRING = "RECURRING";
    const ONECLICK = "ONECLICK";

    /**
     * Creates a new CardPayment containing a given order.
     * @param CreateOrderBuilder $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    /**
     * configureExcludedPaymentMethods returns a list of payment methods not to 
     * present on the paypage for this payment method method class.
     * @return string[] the list of excluded payment methods, @see SystemPaymentMethod
     */
    protected function configureExcludedPaymentMethods() {       
        // first, exclude all invoice/paymentplan payment methods
        $methods = ExcludePayments::excludeInvoicesAndPaymentPlan();

        //directbanks
        $methods[] = \Svea\SystemPaymentMethod::BANKAXESS;
        $methods[] = \Svea\SystemPaymentMethod::DBNORDEASE;
        $methods[] = \Svea\SystemPaymentMethod::DBSEBSE;
        $methods[] = \Svea\SystemPaymentMethod::DBSEBFTGSE;
        $methods[] = \Svea\SystemPaymentMethod::DBSHBSE;
        $methods[] = \Svea\SystemPaymentMethod::DBSWEDBANKSE;
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
    
    /**
     * Set subscription type for recurring payments. Subscription type may be one
     * of CardPayment::RECURRINGCAPTURE | CardPayment::ONECLICKCAPTURE (all countries)
     * or CardPayment::RECURRING | CardPayment::ONECLICK (Scandinavian countries only) 
     * 
     * The initial transaction status will either be AUTHORIZED (i.e. it may be charged
     * after it has been confirmed) or REGISTERED (i.e. the initial amount will be
     * reserved for a time by the bank, and then released) for RECURRING and ONECLICK.
     * 
     * Use of setSubscriptionType() will set the attributes subscriptionId and subscriptionType
     * in the HostedPaymentResponse.
     * 
     * @todo write test for this.
     * 
     * @todo write tests to find out which countries are "scandinavian"?
     * 
     * @param string $subscriptionType  @see CardPayment constants
     * @return $this
     */
    public function setSubscriptionType( $subscriptionType ) {
        $this->subscriptionType = $subscriptionType;
        return $this;
    }
    
}
