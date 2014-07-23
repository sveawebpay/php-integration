<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Recur a Card transaction. 
 * 
 * @author Kristian Grossman-Madsen
 */
class RecurTransaction extends HostedRequest {

    protected $subscriptionId;
    protected $customerRefNo;
    protected $amount;
    
    function __construct($config) {
        $this->method = "recur";
        parent::__construct($config);
    }
    
    /**
     * Optional
     * 
     * If recur is to an international acquirer the currency for the recurring transaction must be the same as for the registration transaction.
     * 
     * @param string $currency
     * @return \Svea\RecurTransaction
     */
    function setCurrency( $currency ) {
        $this->currency = $currency;
        return $this;
    }
    
    /**
     * Required 
     * 
     * Note that if subscriptiontype is either RECURRING or RECURRINGCAPTURE, 
     * the amount must be given in the same currency as the initial transaction. 
     * 
     * @param int $amount  amount in minor currency
     * @return \Svea\RecurTransaction
     */
    function setAmount( $amount ) {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Required - the new unique customer reference number.
     * 
     * @param string $customerRefNo
     * @return \Svea\RecurTransaction
     */
    function setCustomerRefNo( $customerRefNo ) {
        $this->customerRefNo = $customerRefNo;
        return $this;
    }
    
    /**
     * Required - the subscription id returned with the inital transaction response.
     *  
     * @param int $subscriptionId
     * @return \Svea\RecurTransaction
     */
    function setSubscriptionId( $subscriptionId ) {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }
    
    public function validateRequestAttributes() {
        $errors = array();
        $errors = $this->validateAmount($this, $errors);
        $errors = $this->validateCustomerRefNo($this, $errors);
        $errors = $this->validateSubscriptionId($this, $errors);
        return $errors;
    }
    
    private function validateAmount($self, $errors) {
        if (isset($self->amount) == FALSE) {                                                        
            $errors['missing value'] = "amount is required. Use function setAmount().";
        }
        return $errors;
    }  
    
    private function validateCustomerRefNo($self, $errors) {
        if (isset($self->customerRefNo) == FALSE) {                                                        
            $errors['missing value'] = "customerRefNo is required. Use function setCustomerRefNo (also check setClientOrderNumber in order builder).";
        }
        return $errors;
    }  
    
    private function validateSubscriptionId($self, $errors) {
        if (isset($self->subscriptionId) == FALSE) {                                                        
            $errors['missing value'] = "subscriptionId is required. Use function setSubscriptionId() with the subscriptionId from the createOrder response.";
        }
        return $errors;
    }    
    
    public function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
            $XMLWriter->startElement($this->method);   
                $XMLWriter->writeElement("amount",$this->amount);
                $XMLWriter->writeElement("customerrefno",$this->customerRefNo);
                $XMLWriter->writeElement("subscriptionid",$this->subscriptionId);
                if( isset( $this->currency ) ) { $XMLWriter->writeElement("currency",$this->currency); }                 
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }
    public function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new RecurTransactionResponse($message, $countryCode, $config);
    }   
}