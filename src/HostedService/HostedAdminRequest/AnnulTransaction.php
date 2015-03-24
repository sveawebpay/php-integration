<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * AnnulTransaction is used to cancel (annul) a card transaction. 
 * The transaction must have status AUTHORIZED or CONFIRMED at Svea.
 * After a successful request the transaction will get the status ANNULLED.
 *
 * @author Kristian Grossman-Madsen
 */
class AnnulTransaction extends HostedRequest {

    /** @var string $transactionid */
    public $transactionId;
    
    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $transactionId
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     * @return \Svea\HostedService\AnnulTransaction
     */
    function __construct($config) {
        $this->method = "annul";
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

    /** returns xml for hosted webservice "annul" request */
    protected function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
        $XMLWriter->writeComment( \Svea\Helper::getLibraryAndPlatformPropertiesAsJson( $this->config ) );                
            $XMLWriter->startElement($this->method);   
                $XMLWriter->writeElement("transactionid",$this->transactionId);
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }  
    
    protected function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new AnnulTransactionResponse($message, $countryCode, $config);
    }    
}
