<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * confirmTransaction can be performed on card transaction having the status 
 * AUTHORIZED. This will result in a CONFIRMED transaction that will be
 * captured on the given capturedate.
 * 
 * Note that this method only supports Card transactions.
 * 
 * @author Kristian Grossman-Madsen
 */
class ConfirmTransaction extends HostedRequest {

    public $transactionId;
    public $captureDate;
    
    function __construct($config) {
        $this->method = "confirm";
        parent::__construct($config);
    }
    
    /**
     * Set the transaction id, which must have status AUTHORIZED at Svea. After
     * the request, the transaction will have status CONFIRMED. 
     * 
     * Required.
     * 
     * @param string $transactionId  
     * @return $this
     */
//    function setTransactionId( $transactionId ) {
//        $this->transactionId = $transactionId;
//        return $this;
//    }
    
    /**
     * Set the date that the transaction will be captured (settled).
     * 
     * Required. 
     * 
     * @param string $captureDate  ISO-8601 extended date format (YYYY-MM-DD)
     * @return $this
     */
//    function setCaptureDate( $captureDate ) {
//        $this->captureDate = $captureDate;
//        return $this;
//    }
    
    public function validateRequestAttributes() {
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
    
    private function validateCaptureDate($self, $errors) {
        if (isset($self->captureDate) == FALSE) {                                                        
            $errors['missing value'] = "captureDate is required. Use function setCaptureDate().";
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
                $XMLWriter->writeElement("capturedate",$this->captureDate);
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }
    
    public function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new ConfirmTransactionResponse($message, $countryCode, $config);
    }   
}