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

     protected function configureExcludedPaymentMethods($request) {
        if (isset($this->paymentMethod)) {
            if ($this->paymentMethod == \PaymentMethod::INVOICE) {
                $request['paymentMethod'] = "SVEAINVOICEEU_".$this->order->countryCode;
            } elseif ($this->paymentMethod == \PaymentMethod::PAYMENTPLAN) {
                $request['paymentMethod'] = "PAYMENTPLAN_".$this->order->countryCode;
            } else {
                $request['paymentMethod'] = $this->paymentMethod;
            }
        }
        return $request;
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
