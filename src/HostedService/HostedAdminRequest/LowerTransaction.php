<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * lowerTransaction modifies the amount in an existing card transaction 
 * having status AUTHORIZED or CONFIRMED. If the amount is lowered by an 
 * amount equal to the transaction authorized amount, then after a 
 * successful request the transaction will get the status ANNULLED.
 * 
 * @author Kristian Grossman-Madsen
 */
class LowerTransaction extends HostedRequest {

    /** @var string $transactionId  Required. */
    public $transactionId;

    /** @var numeric $amountToLower  Required. Use minor currency (i.e. 1 SEK => 100 in minor currency) */
    public $amountToLower;
    
    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $transactionId, $amountToLower
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     * @return \Svea\HostedService\LowerTransaction
     */
    function __construct($config) {
        $this->method = "loweramount";
        parent::__construct($config);
    }
       
    protected function validateRequestAttributes() {
        $errors = array();
        $errors = $this->validateTransactionId($this, $errors);
        $errors = $this->validateAmountToLower($this, $errors);
        return $errors;
    }
    
    private function validateTransactionId($self, $errors) {
        if (isset($self->transactionId) == FALSE) {                                                        
            $errors['missing value'] = "transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.";  
        }
        return $errors;
    }   
    
    private function validateAmountToLower($self, $errors) {
        if (isset($self->amountToLower) == FALSE) {                                                        
            $errors['missing value'] = "amountToLower is required. Use function setAmountToLower().";
        }
        return $errors;    
    }    
    
    protected function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
            $XMLWriter->startElement($this->method);   
                $XMLWriter->writeElement("transactionid",$this->transactionId);
                $XMLWriter->writeElement("amounttolower",$this->amountToLower);
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }
    
    protected function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new LowerTransactionResponse($message, $countryCode, $config);
    }    
}