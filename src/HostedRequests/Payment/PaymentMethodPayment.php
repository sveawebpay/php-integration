<?php
namespace Svea;

/**
 * @author anne-hal
 */
class PaymentMethodPayment extends HostedPayment{

    public $paymentMethod;
    public $langCode = "en";
    /**
     * @param type $order, $paymentmethod
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

    /**
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
     *
     * @param type $cancelUrlAsString
     * @return \HostedPayment
     */
    public function setCancelUrl($cancelUrlAsString) {
        $this->cancelUrl = $cancelUrlAsString;
        return $this;
    }
    /*
     * @param type $languageCodeAsISO639
     * @return \HostedPayment
     */
    public function setCardPageLanguage($languageCodeAsISO639){
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
