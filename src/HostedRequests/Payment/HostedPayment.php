<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Description of HostedPayment
 * Parent to CardPayment, DirectPayment and PayPagePayment class
 * Prepares $order and creates a paymentform to integrate on webpage
 * Uses SveaXmlBuilder to turn formatted $order into xml
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 *  * @package HostedRequests/Payment
 * 
 */
class HostedPayment {
    
    public $order;
    public $xmlMessage;
    public $xmlMessageBase64;
    public $returnUrl;
    public $cancelUrl;
    public $langCode;
    public $simulatorCode;

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
    public function validateOrder(){
        $validator = new HostedOrderValidator();
        $errors = $validator->validate($this->order);
        return $errors;
    }

    public function getPaymentForm() {
        //validate input
       $errors = $this->validateOrder(); 
        $exceptionString = "";
        if(count($errors) > 0 || (isset($this->returnUrl) == FALSE && isset($this->paymentMethod) == FALSE)){
            if(isset($this->returnUrl) == FALSE){
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
        $formObject->endPointUrl = $this->order->conf->getEndPoint("HOSTED");
        $formObject->merchantid = $this->order->conf->getMerchantId("HOSTED",  $this->order->countryCode);//$conf->getMerchantIdBasedAuthorization()[0];
        $formObject->secretWord = $this->order->conf->getSecret("HOSTED",  $this->order->countryCode);;//$conf->getMerchantIdBasedAuthorization()[1];
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
        $request['cancelUrl'] = $this->cancelUrl;
        $request['langCode'] = $this->langCode;
        $request['simulatorCode'] = $this->simulatorCode;
        $currency = trim($this->order->currency);
        $currency = strtoupper($currency);
        $request['currency'] = $currency;
        return $this->configureExcludedPaymentMethods($request); //Method in child class
    }
    
    /**
     * 
     * @param type $simulatorCode
     * @return \HostedPayment
     */
    public function setSimulatorCode($simulatorCode) {
        $this->simulatorCode = $simulatorCode;
        return $this;
    } 
}

?>
