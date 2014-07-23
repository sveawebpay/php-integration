<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * ListPaymentMethods fetches all paymentmethods connected to the given 
 * ConfigurationProvider and country.
 *
 * @author Kristian Grossman-Madsen
 */
class ListPaymentMethods extends HostedRequest {

    function __construct($config) {
        $this->method = "getpaymentmethods";
        parent::__construct($config);
    }
    
    public function validateRequestAttributes() {
        $errors = array();
        $errors = $this->validateMerchantId($this, $errors);
        return $errors;
    }

    private function validateMerchantId($self, $errors) {
        if ( null == $self->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode) ) {
            $errors['missing value'] = "merchantId is required, check your ConfigurationProvider credentials.";
        }
        return $errors;
    }     

    public function createRequestXml() {        
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
    public function parseResponse($message) {        
        $countryCode = $this->countryCode;
        $config = $this->config;
        return new ListPaymentMethodsResponse($message, $countryCode, $config);
    }   
}