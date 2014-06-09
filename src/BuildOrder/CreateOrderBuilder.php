<?php
namespace Svea;

require_once 'OrderBuilder.php'; 
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * CreateOrderBuilder collects and prepares order data to be sent to Svea.
 * 
 * Set all required order attributes in CreateOrderBuilder instance by using the 
 * OrderBuilder setAttribute() methods. Instance methods can be chained together, as 
 * they return the instance itself in a fluent fashion.
 * 
 * Finish setting order attributes by chosing a payment method using one of the
 * usePaymentMethod() methods below. You can then go on specifying any payment 
 * method specific settings, see methods provided by the returned payment class.
 * 
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class CreateOrderBuilder extends OrderBuilder {
    /** @var boolean  true indicates test mode, false indicates production mode */
    public $testmode = false;
    
    /**  
     * @param ConfigurationProvider $config 
     */
    public function __construct($config) {
        parent::__construct($config);

    }
    /**
     * Use usePayPageCardOnly to initate a card payment via PayPage. 
     * 
     * Set additional attributes using CardPayment methods.
     * @return CardPayment
     */
    public function usePayPageCardOnly() {
        return new CardPayment($this);
    }

    /**
     * Use usePayPageDirectBankOnly to initate a direct bank payment via PayPage. 
     * 
     * Set additional attributes using DirectPayment methods.
     * @return DirectPayment
     */
    public function usePayPageDirectBankOnly() {
        return new DirectPayment($this);
    }

    /**
     * Use usePayPage to initate a payment via PayPage. 
     * 
     * Set additional attributes using PayPagePayment methods.
     * @return PayPagePayment
     */
    public function usePayPage() {
        $paypagepayment = new PayPagePayment($this);
        return $paypagepayment;
    }

    /**
     * Use usePayPage to initate a payment via PayPage, going straight to the payment method specified. 
     * 
     * Set additional attributes using PayPagePayment methods.
     * Paymentmethods are found in appendix in our documentation and are available in the PaymentMethod class.
     * @see PaymentMethod class
     * @param string $paymentMethodAsConst  i.e. PaymentMethod::SEB_SE et al
     * @return PaymentMethodPayment
     */
    public function usePaymentMethod($paymentMethodAsConst) {
        return new PaymentMethodPayment($this, $paymentMethodAsConst);
    }

    /**
     * Use useInvoicePayment to initate an invoice payment. Set additional attributes using InvoicePayment methods.
     * @return InvoicePayment
     */
    public function useInvoicePayment() {
        return new WebService\InvoicePayment($this);
    }

    /**
     * Use usePaymentPlanPayment to initate a payment plan payment. Set additional attributes using PaymentPlanPayment methods.
     * @param string $campaignCodeAsString
     * @param boolean $sendAutomaticGiroPaymentFormAsBool (optional)
     * @return PaymentPlanPayment
     */
    public function usePaymentPlanPayment($campaignCodeAsString, $sendAutomaticGiroPaymentFormAsBool = 0) {
        $this->campaignCode = $campaignCodeAsString;
        $this->sendAutomaticGiroPaymentForm = $sendAutomaticGiroPaymentFormAsBool;
        return new WebService\PaymentPlanPayment($this);
    }

   /**
     * @internal for testfunctions
     * @param type $func
     * @return $this
     */
    public function run($func) {
        $func($this);
        return $this;
    }
}
