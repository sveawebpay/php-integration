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
    
    public function validateRequestAttributes() {
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
    
    public function createRequestXml() {        
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
    public function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new LowerTransactionResponse($message, $countryCode, $config);
    }    
}