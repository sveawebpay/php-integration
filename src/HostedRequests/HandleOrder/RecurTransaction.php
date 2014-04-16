<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Recur a Card transaction. 
 * 
 * @author Kristian Grossman-Madsen
 */
class RecurTransaction extends HostedRequest {

    protected $subscriptionId;
    protected $amountToLower;
    
    function __construct($config) {
        $this->method = "recur";
        parent::__construct($config);
    }
    
    function setCurrency( $currency ) {
        $this->currency = $currency;
        return $this;
    }
    
    function setAmount( $amount ) {
        $this->amount = $amount;
        return $this;
    }

    function setCustomerRefNo( $customerRefNo ) {
        $this->customerRefNo = $customerRefNo;
        return $this;
    }
    
    function setSubscriptionId( $subscriptionId ) {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }
    
    
    /**
     * prepares the elements used in the request to svea
     */
    public function prepareRequest() {

        $xmlBuilder = new HostedXmlBuilder();
        
        // get our merchantid & secret
        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
        
        // message contains the confirm request
        $messageContents = array(
            "currency" => $this->currency,
            "amount" => $this->amount,
            "customerrefno" => $this->customerRefNo,
            "subscriptionid" => $this->subscriptionId
        ); 
        $message = $xmlBuilder->getRecurTransactionXML( $messageContents );     // TODO inject method into HostedXMLBuilder instead

        // calculate mac
        $mac = hash("sha512", base64_encode($message) . $secret);
        
        // encode the request elements
        $request_fields = array( 
            'merchantid' => urlencode($merchantId),
            'message' => urlencode(base64_encode($message)),
            'mac' => urlencode($mac)
        );
        return $request_fields;
    }

}