<?php
namespace Svea;

require_once 'HostedResponse.php';

/**
 * @author anne-hal
 */
class HostedPaymentResponse extends HostedResponse{

    public $transactionId;
    public $clientOrderNumber;
    public $paymentMethod;
    public $merchantId;
    public $amount;
    public $currency;


    function __construct($response,$countryCode,$config) {
        if (is_array($response)) {
            if (array_key_exists("mac",$response) && array_key_exists("response",$response)) {
                $decodedXml = base64_decode($response["response"]);
                $secret = $config->getSecret(\ConfigurationProvider::HOSTED_TYPE,$countryCode);
                if ($this->validateMac($response["response"],$response['mac'],$secret)) {
                    $this->formatXml($decodedXml); // sets ->accepted, but not ->resultcode
                } else {
                    $this->accepted = 0;
                    $this->resultcode = '0';
                    $this->errormessage = "Response failed authorization. MAC not valid.";
                }
            }
        } 
        else {
            $this->accepted = 0;
            $this->resultcode = '0';
            $this->errormessage = "Response is not recognized.";
        }
    }

    protected function formatXml($xml) {
        
        $xmlElement = new \SimpleXMLElement($xml);

        // we set accepted iff xml statuscode is 0;
        if ((string)$xmlElement->statuscode == 0) {
            $this->accepted = 1;
        } else {
            $this->accepted = 0;
            $this->setErrorParams($xmlElement->statuscode);
        }

        $this->transactionId = (string)$xmlElement->transaction['id'];
        $this->paymentMethod = (string)$xmlElement->transaction->paymentmethod;
        $this->merchantId = (string)$xmlElement->transaction->merchantid;
        $this->clientOrderNumber = (string)$xmlElement->transaction->customerrefno;
        $minorAmount = (int)($xmlElement->transaction->amount);
        $this->amount = $minorAmount * 0.01;
        $this->currency = (string)$xmlElement->transaction->currency;

        if (property_exists($xmlElement->transaction, "subscriptionid")) {
            $this->subscriptionId = (string)$xmlElement->transaction->subscriptionid;
            $this->subscriptionType = (string)$xmlElement->transaction->subscriptiontype;
        }

        if (property_exists($xmlElement->transaction, "cardtype")) {
           $this->cardType = (string)$xmlElement->transaction->cardtype;
           $this->maskedCardNumber = (string)$xmlElement->transaction->maskedcardno;
           $this->expiryMonth = (string)$xmlElement->transaction->expirymonth;
           $this->expiryYear = (string)$xmlElement->transaction->expiryyear;
           $this->authCode = (string)$xmlElement->transaction->authcode;
        }
    }
}
