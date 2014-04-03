<?php
namespace Svea;

require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea WebPay
 */
class PayPagePayment extends HostedPayment {

    public $paymentMethod;
    public $excludedPaymentMethods;

    /**
     * Creates a new PayPagePayment containing a given order.
     * @param CreateOrderBuilder $order
     */
    public function __construct($order) {
        parent::__construct($order);    
    }

    
    /**
     * calls configurePaymentMethod
     */
    protected function configureExcludedPaymentMethods() {
        $this->configurePaymentMethod();

        if (isset($this->excludedPaymentMethods)) {
            return $this->excludedPaymentMethods;
        }
        else {
            return array();
        }
    }

    /**
     * @todo move setting all $request
     */
    protected function configurePaymentMethod() {
        if (isset($this->paymentMethod)) {
            $this->request['paymentMethod'] = $this->paymentMethod;
        }
    }

    /**
     * Exclude specific payment methods.
     * @params type Paymentmethod $paymentMethod ex. PaymentMethod::DBSEBSE,Paymentmethod::SVEAINVOICE_SE
     * Flexible number of params
     * @return \PayPagePayment
     */
    public function excludePaymentMethods() {
        $excludes = func_get_args();

        foreach ($excludes as $method) {
            if ($method == \PaymentMethod::INVOICE) {
                $this->excludedPaymentMethods[] ="SVEAINVOICEEU_".$this->order->countryCode;
                $this->excludedPaymentMethods[] ="SVEAINVOICE".$this->order->countryCode;
            } elseif ($this->paymentMethod == \PaymentMethod::PAYMENTPLAN) {
                $this->excludedPaymentMethods[] = "SVEASPLITEU_".$this->order->countryCode;
            } else {
                $this->excludedPaymentMethods[] = $method;
            }
        }
        return $this;
    }

    /**
     *
     * @return \PayPagePayment
     */
    public function includePaymentMethods() {
        //get parameters sent no matter how many
        $include = func_get_args();
        //exclude all functions
        $this->excludedPaymentMethods[] = SystemPaymentMethod::BANKAXESS;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::KORTCERT;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::SKRILL;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::INVOICESE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::PAYMENTPLANSE;
        $this->excludedPaymentMethods[] = "SVEAINVOICEEU_".$this->order->countryCode;
        $this->excludedPaymentMethods[] = "SVEASPLITEU_".$this->order->countryCode;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::PAYPAL;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBSWEDBANKSE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBSHBSE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBSEBFTGSE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBSEBSE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBNORDEASE;

        //remove the include functions from the excludedPaymentMethods
        foreach ($include as $key => $value) {
            $trimmed = trim($value);
            $cleanValue = strtoupper($trimmed);
            //loop through the include requests
            foreach ($this->excludedPaymentMethods as $k => $v) {
                //unset if a match in exlude array
                if ($cleanValue == $v) {
                    unset($this->excludedPaymentMethods[$k]);
                //unset the invoice methods if INVOICE is desired
                } elseif ($cleanValue == \PaymentMethod::INVOICE) {
                    if ($v == "SVEAINVOICEEU_".$this->order->countryCode || $k == SystemPaymentMethod::INVOICESE) {
                        unset($this->excludedPaymentMethods[$k]);
                    }
                //unset the paymentplan methods if PAYMENTPLAN is desired
                } elseif ($cleanValue == \PaymentMethod::PAYMENTPLAN) {
                    if ($k == "SVEASPLITEU_".$this->order->countryCode || $k == SystemPaymentMethod::PAYMENTPLANSE) {
                        unset($this->excludedPaymentMethods[$k]);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Exclude all cardpayments
     * @return \PayPagePayment
     */
    public function excludeCardPaymentMethods() {
        $this->excludedPaymentMethods[] = SystemPaymentMethod::KORTCERT;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::SKRILL;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::KORTWN;
        return $this;
    }

    /**
     * Exclude all direct bank payments
     * @return \PayPagePayment
     *
     */
    public function excludeDirectPaymentMethods() {
        $this->excludedPaymentMethods[] = SystemPaymentMethod::BANKAXESS;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBNORDEASE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBSEBSE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBSEBFTGSE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBSHBSE;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::DBSWEDBANKSE;
        return $this;
    }

}
