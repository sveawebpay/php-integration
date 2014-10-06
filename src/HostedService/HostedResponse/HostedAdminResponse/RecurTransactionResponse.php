<?php
namespace Svea\HostedService;

require_once 'HostedAdminResponse.php'; // fix for class loader sequencing problem
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * RecurTransactionResponse handles the recur transaction response
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class RecurTransactionResponse extends HostedAdminResponse{

    /** @var string $transactionId  -- the order id at Svea */
    public $transactionId;
    /** @var string $clientOrderNumber -- the customer reference number, i.e. order number */
    public $clientOrderNumber;
    /** @var string $paymentMethod */
    public $paymentMethod;
    /** @var string $merchantId -- the merchant id */
    public $merchantId;    
    /** string $amount  total amount in minor currency */
    public $amount;
    public $currency;
    /** @var string $cardType */
    public $cardType;
    /** @var string $maskedCardNumber */
    public $maskedCardNumber;
    /** @var string $expiryMonth -- Expire month of the month */
    public $expiryMonth;
    /** @var string $expiryYear -- Expire year of the card */
    public $expiryYear;
    /** @var string $authCode -- EDB authorization code */
    public $authCode; 
    /** @var string $subscriptionId */
    public $subscriptionId;
            
    function __construct($message,$countryCode,$config) {
        parent::__construct($message,$countryCode,$config);
    }

    /**
     * formatXml() parses the recur transaction response xml into an object, and
     * then sets the response attributes accordingly.
     * 
     * @param string $hostedAdminResponseXML  hostedAdminResponse as xml
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

        $this->transactionId = (string)$hostedAdminResponse->transaction['id'];
        
        $this->clientOrderNumber = (string)$hostedAdminResponse->transaction->customerrefno;
        $this->paymentMethod = (string)$hostedAdminResponse->transaction->paymentmethod;
        $this->merchantId = (string)$hostedAdminResponse->transaction->merchantid;
        $this->amount = (string)$hostedAdminResponse->transaction->amount;
        $this->currency = (string)$hostedAdminResponse->transaction->currency;
        $this->cardType = (string)$hostedAdminResponse->transaction->cardtype;
        $this->maskedCardNumber = (string)$hostedAdminResponse->transaction->maskedcardno;
        $this->expiryMonth = (string)$hostedAdminResponse->transaction->expirymonth;
        $this->expiryYear = (string)$hostedAdminResponse->transaction->expiryyear;
        $this->authCode = (string)$hostedAdminResponse->transaction->authcode;
        $this->subscriptionId = (string)$hostedAdminResponse->transaction->subscriptionid;
    }
}
