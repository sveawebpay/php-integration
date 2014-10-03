<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/*
 * @author Kristian Grossman-Madsen
 */
class ListPaymentMethods extends HostedRequest {

    /*
     * Use the WebPay::listPaymentMethods() entrypoint to get an instance of ListPaymentMethods. 
     * Then provide more information about the transaction and send the request using ListPaymentMethod methods.
     *    
     *       $methods = WebPay::listPaymentMethods( $config )
     *          ->setCountryCode("SE")      // required
     *          ->doRequest()
     *       ;
     *    
     * Following the ->doRequest call you receive an instance of ListPaymentMethodsResponse.
     *  
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     * @return \Svea\HostedService\ListPaymentMethods
     */
     function __construct($config) {
        $this->method = "getpaymentmethods";
        parent::__construct($config);
    }
    
    protected function validateRequestAttributes() {
        $errors = array();
        $errors = $this->validateCountryCode($this, $errors );
        $errors = $this->validateMerchantId($this, $errors);
        return $errors;
    }

    private function validateMerchantId($self, $errors) {
        if ( null == $self->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode) ) {
            $errors['missing value'] = "merchantId is required, check your ConfigurationProvider credentials.";
        }
        return $errors;
    }     

    private function validateCountryCode($self, $errors) {
        if ( null == $this->countryCode ) {
            $errors['missing value'] = "countryCode is required, use setCountryCode().";
        }
        return $errors;
    }     
    
    protected function createRequestXml() {        
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");        
            $XMLWriter->startElement($this->method);   
                $XMLWriter->writeElement("merchantid",$this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode));
            $XMLWriter->endElement();
        $XMLWriter->endDocument();
        
        return $XMLWriter->flush();
    }     
    
    protected function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new ListPaymentMethodsResponse($message, $countryCode, $config);
    }   
}