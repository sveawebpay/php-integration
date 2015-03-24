<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Performs a recurring card transaction, using a previously set up subscription id.
 * 
 * Note: If recur is to an international acquirer, the currency for the recurring transaction must be the same as for the registration transaction.
 * Note: If subscriptiontype is either RECURRING or RECURRINGCAPTURE, the amount must be given in the same currency as the initial transaction. 
 * 
 * @author Kristian Grossman-Madsen
 */
class RecurTransaction extends HostedRequest {

    /** @var string $subscriptionId  Required. This is the subscription id returned with the inital transaction that set up the subscription response. */
    public $subscriptionId;

    /** @var string $customerRefNo  Required. This is the new unique customer reference number for the resulting recur transaction. */
    public $customerRefNo;
    
    /** @var string $amount  Required. Use minor currency (i.e. 1 SEK => 100 in minor currency) */
    public $amount;
    
    /** @var string $currency  Optional. */
    public $currency;

    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $subscriptionId, $customerRefNo, $amount
     * Optional: $currency
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     * @return \Svea\HostedService\RecurTransaction
     */
    function __construct($config) {
        $this->method = "recur";
        parent::__construct($config);
    }
    
    protected function validateRequestAttributes() {
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
    
    protected function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
        $XMLWriter->writeComment( \Svea\Helper::getLibraryAndPlatformPropertiesAsJson( $this->config ) );                
            $XMLWriter->startElement($this->method);   
                $XMLWriter->writeElement("amount",$this->amount);
                $XMLWriter->writeElement("customerrefno",$this->customerRefNo);
                $XMLWriter->writeElement("subscriptionid",$this->subscriptionId);
                if( isset( $this->currency ) ) { $XMLWriter->writeElement("currency",$this->currency); }                 
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }
    
    protected function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new RecurTransactionResponse($message, $countryCode, $config);
    }   
}