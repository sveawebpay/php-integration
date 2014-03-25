<?php
namespace Svea;

require_once 'HostedRequest.php';
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Lowers the amount of a Card transaction. 
 * 
 * @author Kristian Grossman-Madsen
 */
class LowerTransaction extends HostedRequest {

    protected $transactionId;
    protected $amountToLower;
    
    function __construct($config) {
        $this->method = "loweramount";
        parent::__construct($config);
    }
    
    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    function setAmountToLower( $transactionId ) {
        $this->amountToLower = $transactionId;
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
            "transactionid" => $this->transactionId,
            "amounttolower" => $this->amountToLower
        ); 
        $message = $xmlBuilder->getLowerTransactionXML( $messageContents );     // TODO inject method into HostedXMLBuilder instead

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