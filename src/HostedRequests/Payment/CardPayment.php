<?php
namespace Svea;

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
 * Extends HostedPayment
 * Goes to PayPage and excludes all methods that are not cardpayments
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package HostedRequests/Payment
 */
class CardPayment extends HostedPayment {

    public $langCode = 'en';

    /**
     *
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    protected function configureExcludedPaymentMethods($request) {
        //directbanks
        $methods[] = SystemPaymentMethod::BANKAXESS;
        $methods[] = SystemPaymentMethod::DBNORDEASE;
        $methods[] = SystemPaymentMethod::DBSEBSE;
        $methods[] = SystemPaymentMethod::DBSEBFTGSE;
        $methods[] = SystemPaymentMethod::DBSHBSE;
        $methods[] = SystemPaymentMethod::DBSWEDBANKSE;
        //other
        $methods[] = SystemPaymentMethod::PAYPAL;

        $exclude = new ExcludePayments();
        $methods = array_merge((array)$methods, (array)$exclude->excludeInvoicesAndPaymentPlan($this->order->countryCode));

        $request['excludePaymentMethods'] = $methods;
        return $request;
    }

    /**
     * Required.
     * 
     * When a hosted payment transaction completes (regardless of outcome, i.e. accepted or denied), 
     * the payment service will answer with a response xml message sent to the return url specified.
     * 
     * Use setReturnUrl to set the return url.
     * 
     * @param string $returnUrlAsString
     * @return Svea\HostedPayment
     */
    public function setReturnUrl($returnUrlAsString) {
        $this->returnUrl = $returnUrlAsString;
        return $this;
    }

    /**
     * Optional.
     * 
     * In case the hosted payment transaction completes, but the service is unable to return a response to the return url,
     * the payment service will retry several times using the callback url as a fallback, if specified. This may happen if
     * i.e. the user closes the browser before the payment service redirects back to the shop.
     * 
     * Use setCallbackUrl to set the callback url.
     * 
     * @param string $callbackUrlAsString
     * @return Svea\HostedPayment
     */
    public function setCallbackUrl($callbackUrlAsString) {
        $this->callbackUrl = $callbackUrlAsString;
        return $this;
    }

    /**
     * Optional.
     * 
     * In case the hosted payment service is cancelled by the user, the payment service will redirect back to the cancel url. 
     * Unless a return url is specified, no cancel button will be presented at the payment service. 
     * 
     * Use setReturnUrl to set the return url and include a cancel button in the payment service.
     * 
     * @param string $cancelUrlAsString
     * @return \HostedPayment
     */
    public function setCancelUrl($cancelUrlAsString) {
        $this->cancelUrl = $cancelUrlAsString;
        return $this;
    }

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
