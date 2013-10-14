<?php
namespace Svea;

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
  Extends HostedPayment
 * Goes to PayPage and excludes all methods that are not direct payments
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package HostedRequests/Payment
 */
class DirectPayment extends HostedPayment {

    public $langCode = "en";

    /**
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    protected function configureExcludedPaymentMethods($request) {
        //card
        $methods[] = SystemPaymentMethod::KORTCERT;
        $methods[] = SystemPaymentMethod::SKRILL;
        //other
        $methods[] = SystemPaymentMethod::PAYPAL;

        $exclude = new ExcludePayments();
        $methods = array_merge((array)$methods, (array)$exclude->excludeInvoicesAndPaymentPlan($this->order->countryCode));

        $request['excludePaymentMethods'] = $methods;
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

    /**
     * @param type $languageCodeAsISO639
     * @return \HostedPayment|\DirectPayment
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
