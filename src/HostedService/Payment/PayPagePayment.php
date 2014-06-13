<?php
namespace Svea\HostedService;
use Svea\SystemPaymentMethod as SystemPaymentMethod;

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

    public function calculateRequestValues() {
        if (isset($this->paymentMethod)) {
            $this->request['paymentMethod'] = $this->paymentMethod;
        }
        if (isset($this->excludedPaymentMethods)) {
            $this->request['excludePaymentMethods'] = $this->excludedPaymentMethods;
        }
        return parent::calculateRequestValues();
    }

    /**
     * Exclude specific payment methods from being shown of the PayPage.
     * @params string $paymentMethod  use the constants listed in PaymentMethod 
     * Flexible number of params
     * @return $this
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
     * Include specific payment methods to show on the PayPage.
     * @params string $paymentMethod  use the constants listed in SystemPaymentMethod
     * Flexible number of params
     * @return $this
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
     * Exclude all cardpayments from being shown on the PayPage.
     * @return $this
     */
    public function excludeCardPaymentMethods() {
        $this->excludedPaymentMethods[] = SystemPaymentMethod::KORTCERT;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::SKRILL;
        $this->excludedPaymentMethods[] = SystemPaymentMethod::KORTWN;
        return $this;
    }

    /**
     * Exclude all direct bank payments from being shown on the PayPage.
     * @return $this
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
