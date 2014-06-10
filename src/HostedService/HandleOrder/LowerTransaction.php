<?php
namespace Svea\HostedService;

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
    /**
     * Set the id of the transaction to modify. This is received with the 
     * response from Svea following a successful createOrder request.
     * 
     * @param numeric $transactionId
     * @return \Svea\LowerTransaction
     */
    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    /**
     * The amount in minor currecy (i.e. 1 SEK => 100)
     * 
     * @param numeric $amountInMinorCurrency
     * @return \Svea\LowerTransaction
     */
    function setAmountToLower( $amountInMinorCurrency ) {
        $this->amountToLower = $amountInMinorCurrency;
        return $this;
    }
    
    /**
     * prepares the elements used in the request to svea
     */
    public function prepareRequest() {
        $this->validateRequest();

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

    public function validate($self) {
        $errors = array();
        $errors = $this->validateTransactionId($self, $errors);
        $errors = $this->validateAmountToLower($self, $errors);
        return $errors;
    }
    
    private function validateTransactionId($self, $errors) {
        if (isset($self->transactionId) == FALSE) {                                                        
            $errors['missing value'] = "transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response."; // TODO check if the createOrder response sets transactionId or SveaOrderId and update error string accordingly
        }
        return $errors;
    }   
    
    private function validateAmountToLower($self, $errors) {
        if (isset($self->amountToLower) == FALSE) {                                                        
            $errors['missing value'] = "amountToLower is required. Use function setAmountToLower().";
        }
        return $errors;    
    }    
    
}