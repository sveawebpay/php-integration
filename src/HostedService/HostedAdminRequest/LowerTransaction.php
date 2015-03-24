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

    /** @var string $amountToLower  Required. Use minor currency (i.e. 1 SEK => 100 in minor currency) */
    public $amountToLower;
    
    /** @var boolean $alsoDoConfirm  Optional. Iff true, doRequest() will perform a ConfirmTransaction request following a successful doRequest */
    public $alsoDoConfirm;
    
    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $transactionId, $amountToLower
     * Option: $alsoDoRequest
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
        $XMLWriter->writeComment( \Svea\Helper::getLibraryAndPlatformPropertiesAsJson( $this->config ) );                
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
    
    /**
     * Performs a request using cURL, parsing the response using SveaResponse 
     * and returning the resulting HostedAdminResponse instance.
     * 
     * Iff $alsoDoConfirm is true, LowerTransaction doRequest() will also 
     * perform a ConfirmTransaction request following a successful doRequest
     * @return HostedAdminResponse
     * @override
     */
    public function doRequest(){
        $fields = $this->prepareRequest();
        
        $fieldsString = "";
        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->getEndpoint( \Svea\SveaConfigurationProvider::HOSTED_ADMIN_TYPE). $this->method);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //force curl to trust https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //returns a html page with redirecting to bank...
        $responseXML = curl_exec($ch);
        curl_close($ch);
        
        // create SveaResponse to handle response
        $responseObj = new \SimpleXMLElement($responseXML); 
        $lowerTransactionResponse = $this->parseResponse( $responseObj );
        $returnResponse = $lowerTransactionResponse;

        // handle alsoDoConfirm flag
        if( $this->alsoDoConfirm == true ) {
            
            // if there were an error, return a ConfirmTransactionResponse with errormessage set
            if( $lowerTransactionResponse->accepted != true ) {
                $confirmTransactionResponse = new ConfirmTransactionResponse( null, null, null ); // hack to get empty response
                $confirmTransactionResponse->accepted = 0;
                $confirmTransactionResponse->resultcode = '100';  //INTERNAL_ERROR
                $confirmTransactionResponse->errormessage = 
                    "IntegrationPackage: LowerAmount request with flag alsoDoConfirm failed:" .
                    $lowerTransactionResponse->resultcode . " " . $lowerTransactionResponse->errormessage
                ;
                $returnResponse = $confirmTransactionResponse;
            }
            // lowerTransaction request went well, do confirmTransaction request
            else {
                $confirmTransactionRequest = new ConfirmTransaction($this->config);
                $confirmTransactionRequest->countryCode = $this->countryCode;
                $confirmTransactionRequest->transactionId = $this->transactionId;

                $defaultCaptureDate = explode("T", date('c')); // [0] contains date part
                $confirmTransactionRequest->captureDate = $defaultCaptureDate[0];
                $confirmTransactionResponse = $confirmTransactionRequest->doRequest();

                $returnResponse = $confirmTransactionResponse;
            }            
        }        
        return $returnResponse;
    }        
}