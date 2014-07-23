<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * creditTransaction can be used to credit transactions. Only transactions that
 * have reached the status SUCCESS can be credited.
 * 
 * @author Kristian Grossman-Madsen
 */
class CreditTransaction extends HostedRequest {

    protected $transactionId;
    protected $creditAmount;
    
    function __construct($config) {
        $this->method = "credit";
        parent::__construct($config);
    }

    /**
     * Set the transaction id, which must have status SUCCESS at Svea.
     * 
     * Required.
     * 
     * @param string $transactionId
     * @return $this
     */
    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    /**
     * Set the amount to credit.
     * 
     * Required.
     * 
     * @param int $creditAmount  amount to credit, in minor currency (i.e. 1 SEK => 100 in minor currency)
     * @return $this
     */
    function setCreditAmount( $creditAmount ) {
        $this->creditAmount = $creditAmount;
        return $this;
    }
    
    public function validateRequestAttributes() {
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
    
    public function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
            $XMLWriter->startElement($this->method);   
                $XMLWriter->writeElement("transactionid",$this->transactionId);
                $XMLWriter->writeElement("amounttocredit",$this->creditAmount);
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }       
}