<?php
namespace Svea;

require_once 'HostedResponse.php';


/**
 * @author anne-hal
 */
class HostedAdminResponse extends HostedResponse{

    public $paymentMethods;

    function __construct($response,$countryCode,$config) {
        if (is_object($response)) {

            if (property_exists($response,"mac") && property_exists($response,"message")) {
                $decodedXml = base64_decode($response->message);
                $secret = $config->getSecret(\ConfigurationProvider::HOSTED_TYPE,$countryCode);
                if ($this->validateMac($response->message,$response->mac,$secret)) {
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
        if ((string)$xmlElement->statuscode == 0) {
            $this->accepted = 1;
        } else {
            $this->accepted = 0;
            $this->setErrorParams($xmlElement->statuscode);
        }
        //getPaymentMethods
        if(property_exists($xmlElement,"paymentmethods")){
            $this->paymentMethods = $xmlElement->paymentmethods->paymentmethod;

        }



    }

}
