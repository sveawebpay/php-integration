<?php
namespace Svea\HostedService;

require_once 'HostedAdminResponse.php'; // fix for class loader sequencing problem
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * ConfirmTransactionResponse handles the confirm transaction response
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class ConfirmTransactionResponse extends HostedAdminResponse{

    /** @var string $transactionId  transaction id that uniquely identifies the order at Svea */
    public $transactionId;          
  
    /** @var string $clientOrderNumber */
    public $clientOrderNumber;
    
    /** @var string $orderType  -- set to \ConfigurationProvider::HOSTED_TYPE for all hosted confirm order requests (currently only confirm card orders) */
    public $orderType;    
    
    function __construct($message,$countryCode,$config) {
        parent::__construct($message,$countryCode,$config);
    }

    /**
     * formatXml() parses the confirm transaction response xml into an object, and
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
        $this->orderType = \ConfigurationProvider::HOSTED_TYPE; // c.f. corresponding attribute in DeliverOrderResult
    }
}
