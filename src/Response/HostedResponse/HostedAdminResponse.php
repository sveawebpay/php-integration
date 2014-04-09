<?php
namespace Svea;

require_once 'HostedResponse.php';

/**
 * Handles diverse administrative function responses from the webservice and
 * wrapped legacy services through the webservice.
 * 
 * @property string $customerrefno contains customer provided order reference
 * @property array<string> $paymentMethods set iff getPaymentMethod response
 * 
 * @author anne-hal, Kristian Grossman-Madsen for Svea WebPay
 */
class HostedAdminResponse extends HostedResponse{

    /**
     * Create an new HostedAdminResponse which handles the webservice response
     * from the following methods:
     * creditTransaction(),
     * annulTransaction(),
     * getPaymentMethods()
     * 
     * Will set response attribute accepted to 0 if the mac is invalid or the
     * response is malformed.
     * 
     * @param SimpleXMLElement $message
     * @param string $countryCode
     * @param SveaConfigurationProvider $config
     */
    function __construct($message,$countryCode,$config) {

        // TODO extract response sanity checks to parent HostedResponse class
        if (is_object($message)) {

            if (property_exists($message,"mac") && property_exists($message,"message")) {
                $decodedXml = base64_decode($message->message);
                $secret = $config->getSecret(\ConfigurationProvider::HOSTED_TYPE,$countryCode);

                if ($this->validateMac($message->message,$message->mac,$secret)) {
                    $this->formatXml($decodedXml);
                } else {
                    $this->accepted = 0;
                    $this->resultcode = '0';
                    $this->errormessage = "Response failed authorization. MAC not valid.";
                }
            }

        } else {
            $this->accepted = 0;
            $this->resultcode = '0';
            $this->errormessage = "Response is not recognized.";
        }
    }

    /**
     * formatXml() parses the hosted admin response xml into an object, and
     * then sets the response attributes accordingly.
     * 
     * Handles responses from the following method requests:
     * getPaymentMethods()
     * creditTransaction()
     * annulTransaction()
     * 
     * @param type $hostedAdminResponseXML
     */
    protected function formatXml($hostedAdminResponseXML) {
        $hostedAdminResponse = new \SimpleXMLElement($hostedAdminResponseXML);
        
        if ((string)$hostedAdminResponse->statuscode == '0') {
            $this->accepted = 1;
            $this->resultcode = '0';
        } else {
            $this->accepted = 0;
            $this->setErrorParams( (string)$hostedAdminResponse->statuscode ); 
        }
        
        //getPaymentMethods
        if(property_exists($hostedAdminResponse,"paymentmethods")){

            //$this->paymentMethods = (array)$hostedAdminResponse->paymentmethods->paymentmethod;     // seems to break under php 5.3            
            foreach( $hostedAdminResponse->paymentmethods->paymentmethod as $paymentmethod) {       // compatibility w/php 5.3
                $this->paymentMethods[] = (string)$paymentmethod;
            }            
        }
        
        // queryTransaction
        if(property_exists($hostedAdminResponse->transaction,"customerrefno") && property_exists($hostedAdminResponse->transaction,"merchantid")){
            $this->customerrefno = (string)$hostedAdminResponse->transaction->customerrefno;
        }  
        //creditTransaction/credit request or annulTransaction/annul request
        elseif(property_exists($hostedAdminResponse->transaction,"customerrefno")){
            $this->customerrefno = (string)$hostedAdminResponse->transaction->customerrefno;
        }    
   
        // getPaymentURL/preparepayment request
        if( property_exists($hostedAdminResponse,"preparedpayment")) {
            $url = "https://webpay.sveaekonomi.se/webpay/preparedpayment/";
            $testurl = "https://test.sveaekonomi.se/webpay/preparedpayment/";
            
            $this->id = (string)$hostedAdminResponse->preparedpayment->id;
            $this->created = (string)$hostedAdminResponse->preparedpayment->created;
            
            $this->url = $url.$this->id;            // in integration package only
            $this->testurl = $testurl.$this->id;    // in integration packge only
        }
    }

}
