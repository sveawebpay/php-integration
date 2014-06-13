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

    public $transactionid;
    /** string $customerrefno */
    public $customerrefno;
    public $paymentmethod;
    public $merchantid;
    /** string $amount  total amount in minor currency */
    public $amount;
    public $currency;
    public $cardtype;
    public $maskedcardno;
    public $expirymonth;
    public $expiryyear;
    public $authcode;
    public $subscriptionid;
        
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

        $this->transactionid = (string)$hostedAdminResponse->transaction['id'];
        
        $this->customerrefno = (string)$hostedAdminResponse->transaction->customerrefno;
        $this->paymentmethod = (string)$hostedAdminResponse->transaction->paymentmethod;
        $this->merchantid = (string)$hostedAdminResponse->transaction->merchantid;
        $this->amount = (string)$hostedAdminResponse->transaction->amount;
        $this->currency = (string)$hostedAdminResponse->transaction->currency;
        $this->cardtype = (string)$hostedAdminResponse->transaction->cardtype;
        $this->maskedcardno = (string)$hostedAdminResponse->transaction->maskedcardno;
        $this->expirymonth = (string)$hostedAdminResponse->transaction->expirymonth;
        $this->expiryyear = (string)$hostedAdminResponse->transaction->expiryyear;
        $this->authcode = (string)$hostedAdminResponse->transaction->authcode;
        $this->subscriptionid = (string)$hostedAdminResponse->transaction->subscriptionid;
    }
}
