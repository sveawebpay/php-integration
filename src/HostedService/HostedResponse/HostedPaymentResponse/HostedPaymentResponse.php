<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * @author anne-hal
 */
class HostedPaymentResponse extends HostedResponse{

    /** @var string $transactionId  -- the order id at Svea */
    public $transactionId;         
    /** @var string $clientOrderNumber -- the customer reference number, i.e. order number */
    public $clientOrderNumber;    
    /** @var string $paymentMethod */
    public $paymentMethod;    
    /** @var string $merchantId -- the merchant id */
    public $merchantId;
    /** @var string $amount The total amount including VAT, presented as a decimal number */
    public $amount;     // TODO java: Double
    /** @var string $currency -- ISO 4217 alphabetic, e.g. SEK */
    public $currency;

    /**
     * HostedPaymentResponse validates the hosted payment response.
     * 
     * For successful payment requests it sets the accepted attribute to 1 and
     * other response attributes accordingly.
     * 
     * In case of a response error, it sets the the accepted attribute to 0 and
     * the resultcode to 0. For responses indicating that something went wrong
     * at the service, it sets accepted to 0 and responsecode with corresponding
     * errormessage accordingly.
     * 
     * @param string $response  hosted request response xml message
     * @param string $countryCode  two-letter country code
     * @param ConfigurationProvider $config
     */
    function __construct($response,$countryCode,$config) {
        if (is_array($response)) {
            if (array_key_exists("mac",$response)) {
                if( array_key_exists("response",$response) ) {
                    $decodedXml = base64_decode($response["response"]);
                    $secret = $config->getSecret(\ConfigurationProvider::HOSTED_TYPE,$countryCode);
                    if ($this->validateMac($response["response"],$response['mac'],$secret)) {
                        $this->formatXml($decodedXml);
                    } else {
                        $this->accepted = 0;
                        $this->resultcode = '0';
                        $this->errormessage = "Response failed authorization. MAC not valid.";
                    }
                }
                else {
                    $this->accepted = 0;
                    $this->resultcode = '0';
                    $this->errormessage = "Response is not recognized.";
        
                }
            }
            else {
                $this->accepted = 0;
                $this->resultcode = '0';
                $this->errormessage = "Response is not recognized.";
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
        $this->amount = number_format( ($minorAmount * 0.01), 2, ".", "" );
        $this->currency = (string)$xmlElement->transaction->currency;

        if (property_exists($xmlElement->transaction, "subscriptionid")) {
            $this->subscriptionId = (string)$xmlElement->transaction->subscriptionid;
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
