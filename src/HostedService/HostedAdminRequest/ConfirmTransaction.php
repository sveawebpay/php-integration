<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * confirmTransaction can be performed on transactions having the status 
 * AUTHORIZED at Svea. After as successful request, the transaction will have 
 * status CONFIRMED and that will be captured on the given capturedate.
 * 
 * Note that this method does not support Direct Bank transactions.
 * 
 * @author Kristian Grossman-Madsen
 */
class ConfirmTransaction extends HostedRequest {

    /** @var string $transactionId  Required. */
    public $transactionId;
    
    /** @var string $captureDate  Required. Use ISO-8601 extended date format (YYYY-MM-DD) */
    public $captureDate;
    
    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $transactionId, $captureDate
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     * @return \Svea\HostedService\ConfirmTransaction
     */
    function __construct($config) {
        $this->method = "confirm";
        parent::__construct($config);
    }
        
    protected function validateRequestAttributes() {
        $errors = array();
        $errors = $this->validateTransactionId($this, $errors);
        $errors = $this->validateCaptureDate($this, $errors);
        return $errors;
    }
    
    private function validateTransactionId($self, $errors) {
        if (isset($self->transactionId) == FALSE) {                                                        
            $errors['missing value'] = "transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.";  
        }
        return $errors;
    }   
    
    // this is optional coming through the api, as the orderbuilder deliverCardOrder sets a default capturedate
    private function validateCaptureDate($self, $errors) {
        if (isset($self->captureDate) == FALSE) {                                                        
            $errors['missing value'] = "captureDate is required. Use function setCaptureDate().";
        }
        return $errors;
    }       

    /** returns xml for hosted webservice "confirm" request */
    protected function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
        $XMLWriter->writeComment( \Svea\Helper::getLibraryAndPlatformPropertiesAsJson( $this->config ) );                
            $XMLWriter->startElement($this->method);   
                $XMLWriter->writeElement("transactionid",$this->transactionId);
                $XMLWriter->writeElement("capturedate",$this->captureDate);
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }
    
    protected function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new ConfirmTransactionResponse($message, $countryCode, $config);
    }   
}