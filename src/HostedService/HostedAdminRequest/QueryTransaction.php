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

    /** @var string $transactionId  Required. */
    public $transactionId;
    
    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $transactionId
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     * @return \Svea\HostedService\QueryTransaction
     */
    function __construct($config) {
        $this->method = "querytransactionid";
        parent::__construct($config);
    }
        
    protected function validateRequestAttributes() {
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
    
    protected function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
        $XMLWriter->writeComment( \Svea\Helper::getLibraryAndPlatformPropertiesAsJson( $this->config ) );                
            $XMLWriter->startElement("query");  // note, not the same as $this->method above   
                $XMLWriter->writeElement("transactionid",$this->transactionId);
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }
    
    protected function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new QueryTransactionResponse($message, $countryCode, $config);
    }   
}