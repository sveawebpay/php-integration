<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Query information about an existing card or direct bank transaction.
 * 
 * Note that this only supports queries based on the Svea transactionId.
 *
 * @author Kristian Grossman-Madsen
 */
class QueryTransaction extends HostedRequest {

    public $transactionId;
    
    function __construct($config) {
        $this->method = "querytransactionid";
        parent::__construct($config);
    }

    /**
     * @param string $transactionId
     * @return $this
     */
//    function setTransactionId( $transactionId ) {
//        $this->transactionId = $transactionId;
//        return $this;
//    }
        
    public function validateRequestAttributes() {
        $errors = array();
        $errors = $this->validateTransactionId($this, $errors);
        return $errors;
    }
    
    private function validateTransactionId($self, $errors) {
        if (isset($self->transactionId) == FALSE) {                                                        
            $errors['missing value'] = "transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.";  
        }
        return $errors;
    } 
    
    public function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
            $XMLWriter->startElement("query");  // note, different than $this->method above   
                $XMLWriter->writeElement("transactionid",$this->transactionId);
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }
    public function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new QueryTransactionResponse($message, $countryCode, $config);
    }   
}