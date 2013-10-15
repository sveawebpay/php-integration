<?php
namespace Svea;

require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 Extends HostedPayment
 * Goes to PayPage
 * Possibilitys to customize what payment methods to be shown on paypage
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package HostedRequests/Payment
 */
class PayPagePayment extends HostedPayment {

    public $paymentMethod;
    public $excludedPaymentMethods;
    public $langCode = "en";

    /**
     *
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    protected function configureExcludedPaymentMethods($request) {
        if (isset($this->paymentMethod)) {
            $request['paymentMethod'] = $this->paymentMethod;
        }

        if (isset($this->excludedPaymentMethods)) {
            $request['excludePaymentMethods'] = $this->excludedPaymentMethods;
        }

        return $request;
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

    /**
     * Required
     * Set return Url for redirect when payment is completed
     * @param type $returnUrlAsString
     * @return \HostedPayment
     */
    public function setReturnUrl($returnUrlAsString) {
        $this->returnUrl = $returnUrlAsString;
        return $this;
    }

    /**
     * Set callback Url which contacts the store in case the return success URL
     * wasn't reached
     * @param type $callbackUrlAsString
     * @return \HostedPayment
     */
    public function setCallbackUrl($callbackUrlAsString) {
        $this->callbackUrl = $callbackUrlAsString;
        return $this;
    }

    /**
     * @param type $cancelUrlAsString
     * @return \HostedPayment
     */
    public function setCancelUrl($cancelUrlAsString) {
        $this->cancelUrl = $cancelUrlAsString;
        return $this;
    }

    /**
     * @param type $languageCodeAsISO639
     * @return \HostedPayment|\PayPagePayment
     */

    public function setPayPageLanguage($languageCodeAsISO639) {
        switch ($languageCodeAsISO639) {
            case "sv":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "en":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "da":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "fi":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "no":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "de":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "es":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "fr":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "it":
                $this->langCode = $languageCodeAsISO639;
                break;
            case "nl":
                $this->langCode = $languageCodeAsISO639;
                break;
            default:
                $this->langCode = "en";
                break;
        }

        return $this;
    }
}
