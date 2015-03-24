<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * creditTransaction can be used to credit transactions. Only transactions that
 * have reached the status SUCCESS at Svea can be credited.
 * 
 * @author Kristian Grossman-Madsen
 */
class CreditTransaction extends HostedRequest {

    /** @var string $transactionId  Required. */
    public $transactionId;

    /** @var string $creditAmount  Required. Use minor currency (i.e. 1 SEK => 100 in minor currency) */
    public $creditAmount;
    
    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $transactionId, $creditAmount
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     * @return \Svea\HostedService\CreditTransaction
     */
    function __construct($config) {
        $this->method = "credit";
        parent::__construct($config);
    }
    
    protected function validateRequestAttributes() {
        $errors = array();
        $errors = $this->validateTransactionId($this, $errors);
        $errors = $this->validateCreditAmount($this, $errors);
        return $errors;
    }
    
    private function validateTransactionId($self, $errors) {
        if (isset($self->transactionId) == FALSE) {                                                        
            $errors['missing value'] = "transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.";  
        }
        return $errors;
    }   
    
    private function validateCreditAmount($self, $errors) {
        if (isset($self->creditAmount) == FALSE) {                                                        
            $errors['missing value'] = "creditAmount is required. Use function setCreditAmount().";
        }
        return $errors;    
    }
    
    protected function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");   
        $XMLWriter->writeComment( \Svea\Helper::getLibraryAndPlatformPropertiesAsJson( $this->config ) );                        
            $XMLWriter->startElement($this->method);   
                $XMLWriter->writeElement("transactionid",$this->transactionId);
                $XMLWriter->writeElement("amounttocredit",$this->creditAmount);
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }    

    protected function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new CreditTransactionResponse($message, $countryCode, $config);
    }   
}