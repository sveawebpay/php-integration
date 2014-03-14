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
     * 
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

    protected function formatXml($xml) {
        $xmlElement = new \SimpleXMLElement($xml);
        
        if ((string)$xmlElement->statuscode == '0') {
            $this->accepted = 1;
            $this->resultcode = '0';
        } else {
            $this->accepted = 0;
            $this->setErrorParams( (string)$xmlElement->statuscode ); 
        }
        
        //getPaymentMethods
        if(property_exists($xmlElement,"paymentmethods")){
            $this->paymentMethods = (array)$xmlElement->paymentmethods->paymentmethod;
        }
        
        //creditTransaction
        if(property_exists($xmlElement->transaction,"customerrefno")){
            $this->customerrefno = (string)$xmlElement->transaction->customerrefno;
        }    
    }

}
