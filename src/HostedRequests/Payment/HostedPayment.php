<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Parent to CardPayment, DirectPayment and PayPagePayment class
 * Prepares $order and creates a paymentform to integrate on webpage
 * Uses SveaXmlBuilder to turn formatted $order into xml
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package HostedRequests/Payment
 */
class HostedPayment {

    public $order;
    public $xmlMessage;
    public $xmlMessageBase64;
    public $returnUrl;
    public $callbackUrl;
    public $cancelUrl;
    public $langCode;

    /**
     * @param type $order
     */
    public function __construct($order) {
        $this->order = $order;
    }

    /**
     *
     * @return type $errors
     */
    public function validateOrder() {
        $validator = new HostedOrderValidator();
        $errors = $validator->validate($this->order);
        if (($this->order->countryCode == "NL" || $this->order->countryCode == "DE") && isset($this->paymentMethod)) {
            if (isset($this->paymentMethod) && ($this->paymentMethod == \PaymentMethod::INVOICE || $this->paymentMethod ==  \PaymentMethod::PAYMENTPLAN)) {
                $errors = $validator->validateEuroCustomer($this->order, $errors);
            }
        }

        return $errors;
    }

    public function getPaymentForm() {
        //validate input
        $errors = $this->validateOrder();
        $exceptionString = "";
        if (count($errors) > 0 || (isset($this->returnUrl) == FALSE && isset($this->paymentMethod) == FALSE)) {
            if (isset($this->returnUrl) == FALSE) {
             $exceptionString .="-missing value : ReturnUrl is required. Use function setReturnUrl().\n";
            }

            foreach ($errors as $key => $value) {
                $exceptionString .="-". $key. " : ".$value."\n";
            }

            throw new ValidationException($exceptionString);
        }

        $xmlBuilder = new HostedXmlBuilder();
        $this->xmlMessage = $xmlBuilder->getOrderXML($this->calculateRequestValues(),$this->order);
        $this->xmlMessageBase64 = base64_encode($this->xmlMessage);
        $formObject = new PaymentForm();
        $formObject->xmlMessage = $this->xmlMessage;
        $formObject->xmlMessageBase64 = $this->xmlMessageBase64;
        $formObject->endPointUrl = $this->order->conf->getEndPoint(\ConfigurationProvider::HOSTED_TYPE);
        $formObject->merchantid = $this->order->conf->getMerchantId(\ConfigurationProvider::HOSTED_TYPE,  $this->order->countryCode);//$conf->getMerchantIdBasedAuthorization()[0];
        $formObject->secretWord = $this->order->conf->getSecret(\ConfigurationProvider::HOSTED_TYPE,  $this->order->countryCode);//$conf->getMerchantIdBasedAuthorization()[1];
        //$formObject->setSubmitMessage($this->order->countryCode);
        $formObject->setForm();
        $formObject->setHtmlFields();
        $formObject->setRawFields();
        return $formObject;
    }

    public function calculateRequestValues() {
        $formatter = new HostedRowFormatter();
        $request = array();
        $request['rows'] = $formatter->formatRows($this->order);
        $request['amount'] = $formatter->formatTotalAmount($request['rows']);
        $request['totalVat'] = $formatter->formatTotalVat( $request['rows']);
        $request['returnUrl'] = $this->returnUrl;
        $request['callbackUrl'] = $this->callbackUrl;
        $request['cancelUrl'] = $this->cancelUrl;
        $request['langCode'] = $this->langCode;
        $currency = trim($this->order->currency);
        $currency = strtoupper($currency);
        $request['currency'] = $currency;

        return $this->configureExcludedPaymentMethods($request); //Method in child class
    }
}
