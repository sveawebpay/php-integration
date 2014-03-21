<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * HostedPayment and its descendants sets up the various hosted payment methods.
 * 
 * Set all required attributes in the hosted payment class instance using the 
 * setAttribute() methods. Instance methods can be chained together, as they 
 * return the instance itself in a fluent fashion.
 * 
 * Finish by using the getPaymentForm() method which returns an HTML form with
 * the POST request to Svea prepared. After the customer has completed the
 * hosted payment request, a response xml message is returned to the specified
 * return url, where it can be parsed using i.e. the SveaResponse class.
 * 
 * Alternatively, you can use the getPaymentAddress() to get a response with
 * an URL that the customer can visit later to complete the payment at a later
 * time.
 * 
 * Uses HostedXmlBuilder to turn formatted $order into xml
 * @author Anneli Halld'n, Daniel Brolund, Kristian Grossman-Madsen for Svea Webpay
 */

class HostedPayment {

    /** @var CreateOrderBuilder $order  holds the order information */
    public $order;
    
    /** @var string $xmlMessage  holds the generated message XML used in request */
    public $xmlMessage;
    
    /** @var string $xmlMessageBase64  holds the Base64-encoded $xmlMessage */
    public $xmlMessageBase64;
    
    /** @var string $returnUrl  holds the return URL used in request */    
    public $returnUrl;
    
    /** @var string $callbackUrl  holds the callback URL used in request */    
    public $callbackUrl;
    
    /** @var string $cancelUrl  holds the cancel URL used in request */    
    public $cancelUrl;
    
    /** @var string $langCode  holds the language code used in request */
    public $langCode;

    /**
     * Creates a HostedPayment, sets default language to english
     * @param CreateOrderBuilder $order
     */
    public function __construct($order) {
        $this->langCode = "en";
        $this->order = $order;
    }

    /**
     * setReturnUrl sets the hosted payment return url. Required.
     * 
     * When the payment completes the service will answer with a response sent to the return url.
     * 
     * @param string $returnUrlAsString
     * @return $this
     */
    public function setReturnUrl($returnUrlAsString) {
        $this->returnUrl = $returnUrlAsString;
        return $this;
    }
    
    /**
     * setCallbackUrl sets up a callback url. Optional.
     * 
     * In case the payment transaction completes, but the service is unable to return a response to the return url, the service will retry several times using the callback url as a fallback. This may happen if i.e. the user closes the browser before the service redirects back to the return url.
     * 
     * @param string $callbackUrlAsString
     * @return $this
     */
    public function setCallbackUrl($callbackUrlAsString) {
        $this->callbackUrl = $callbackUrlAsString;
        return $this;
    }    
    
    /**
     * setCancelUrl sets the hosted payment cancel url and includes a cancel button on the hosted pay page. Optional.
     * 
     * In case payment is cancelled by the user, the service will answer with a response sent to the cancel url. Unless a cancel url is specified, no cancel button will be presented on the pay page. 
     * 
     * @param string $cancelUrlAsString
     * @return $this
     */
    public function setCancelUrl($cancelUrlAsString) {
        $this->cancelUrl = $cancelUrlAsString;
        return $this;
    }    
    
    /* Sets the pay page display language. Optional.
     * Default pay page language is English, unless another is specified using this method.
     * @param string $languageCodeAsISO639
     * @return $this
     */
    public function setPayPageLanguage($languageCodeAsISO639){
        switch ($languageCodeAsISO639) {
            case "sv":
            case "en":
            case "da":
            case "no":
            case "fi":
            case "es":
            case "nl":
            case "fr":
            case "de":
            case "it":
                $this->langCode = $languageCodeAsISO639;
                break;
            default:
                $this->langCode = "en";
                break;
        }
        return $this;
    }
    
    /**
     * getPaymentForm
     * @return PaymentForm
     * @throws ValidationException
     */
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
        
        $formObject = new PaymentForm( $this->xmlMessage, $this->order->conf, $this->order->countryCode );
        return $formObject;
    }

    /**
     * getPaymentAddress implements the webservice preparedpayment request
     * 
     * @return HostedAdminResponse
     * @throws ValidationException
     */
    public function getPaymentAddress() {
        
        $message = "nothing here";
        
        return new HostedAdminResponse($message, $this->order->countryCode, $this->order->conf);
        
    }    
    
    /**
     * @return string[] $errors an array containing the validation errors found
     */
    public function validateOrder() {
        $validator = new HostedOrderValidator();
        $errors = $validator->validate($this->order);
        if (($this->order->countryCode == "NL" || $this->order->countryCode == "DE") && isset($this->paymentMethod)) {
            if( isset($this->paymentMethod) && 
                ($this->paymentMethod == \PaymentMethod::INVOICE || $this->paymentMethod == \PaymentMethod::PAYMENTPLAN)) {
                $errors = $validator->validateEuroCustomer($this->order, $errors);
            }
        }
        return $errors;
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
