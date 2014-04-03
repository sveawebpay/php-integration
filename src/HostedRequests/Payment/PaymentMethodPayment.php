<?php
namespace Svea;

/**
 * @author anne-hal, Kristian Grossman-Madsen
 */
class PaymentMethodPayment extends HostedPayment{

    public $paymentMethod;
    
    /**
     * Creates a new PaymentMethodPayment containing a given order and using the given payment method.
     * @param CreateOrderBuilder $order
     * @param string $paymentmethod -- one of constants defined in PaymentMethod class
     */
    public function __construct($order, $paymentmethod) {
        parent::__construct($order);
        $this->paymentMethod = $paymentmethod;
    }

    /**
     * calls configurePaymentMethod
     */
    protected function configureExcludedPaymentMethods() {
        $this->configurePaymentMethod();
    }

    /**
     * @todo move setting all $request
     */
    protected function configurePaymentMethod() {
        if (isset($this->paymentMethod)) {
            if ($this->paymentMethod == \PaymentMethod::INVOICE) {
                $this->request['paymentMethod'] = "SVEAINVOICEEU_".$this->order->countryCode;
            } elseif ($this->paymentMethod == \PaymentMethod::PAYMENTPLAN) {
                $this->request['paymentMethod'] = "PAYMENTPLAN_".$this->order->countryCode;
            } else {
                $this->request['paymentMethod'] = $this->paymentMethod;
            }
        }
    }
    /*
     * Semantic wrapper for setPayPageLanguage
     * @see setPayPageLanguage
     * @param string $languageCodeAsISO639
     * @return \HostedPayment
     */
    public function setCardPageLanguage($languageCodeAsISO639){
        return $this->setPayPageLanguage($languageCodeAsISO639);
    }
    

}
