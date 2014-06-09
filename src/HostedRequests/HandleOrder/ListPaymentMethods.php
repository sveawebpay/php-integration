<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * List all available payment methods
 * 
 * @author Kristian Grossman-Madsen
 */
class ListPaymentMethods extends HostedRequest {

    function __construct($config) {
        $this->method = "getpaymentmethods";
        parent::__construct($config);
    }
 
    /**
     * prepares the elements used in the request to svea
     */
    public function prepareRequest() {
        $this->validateRequest();

        $xmlBuilder = new HostedXmlBuilder();
        
        // get our merchantid & secret
        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
        
        // message contains the credit request
        $messageContents = array(
            "merchantid" => $merchantId
        );
        $message = $xmlBuilder->getListPaymentMethodsXML( $messageContents );        
        
        // calculate mac
        $mac = hash("sha512", base64_encode($message) . $secret);
        
        // encode the request elements
        $request_fields = array( 
            'merchantid' => urlencode($merchantId),
            'message' => urlencode(base64_encode($message)),
            'mac' => urlencode($mac)
        );
        return $request_fields;
    }
    
    public function validate($self) {
        $errors = array();
        $errors = $this->validateMerchantId($self, $errors);
        return $errors;
    }
    
    private function validateMerchantId($self, $errors) {
        if ( null == $self->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode) ) {                                                    
            $errors['missing value'] = "merchantId is required, check your ConfigurationProvider credentials.";
        }
        return $errors;
    }     
    
}