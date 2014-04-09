<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Confirms a Card transaction. 
 * 
 * @author Kristian Grossman-Madsen
 */
class ConfirmTransaction extends HostedRequest {

    protected $transactionId;
    protected $captureDate;
    
    function __construct($config) {
        $this->method = "confirm";
        parent::__construct($config);
    }
    
    /**
     * @param string $transactionId  the transaction to capture
     * @return $this
     */
    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    /**
     * @param string $captureDate  ISO-8601 extended date format (YYYY-MM-DD)
     * @return $this
     */
    function setCaptureDate( $captureDate ) {
        $this->captureDate = $captureDate;
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
            "capturedate" => $this->captureDate
        ); 
        $message = $xmlBuilder->getConfirmTransactionXML( $messageContents );        

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