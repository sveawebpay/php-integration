<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Credit a Card or Direct Bank transaction
 * 
 * @author Kristian Grossman-Madsen
 */
class CreditTransaction {

    private $config;
    private $countryCode;

    private $transactionId;
    private $creditAmount;
    
    function __construct($config) {
        $this->config = $config;
    }
    
    function setCountryCode( $countryCode ) {
        $this->countryCode = $countryCode;
        return $this;
    }
    
    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    function setCreditAmount( $creditAmount ) {
        $this->creditAmount = $creditAmount;
        return $this;
    }
    
    /**
     * prepares the elements used in the request to svea
     * 
     * @return array $request -- encoded merchantId, message and calculated mac 
     */
    public function prepareRequest() {

        $xmlBuilder = new HostedXmlBuilder();
        
        // get our merchantid & secret
        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
        
        // message contains the credit request
        $messageContents = array(
            "transactionid" => $this->transactionId,
            "amounttocredit" => $this->creditAmount
        ); 
        $message = $xmlBuilder->getCreditTransactionXML( $messageContents );        

        // calculate mac
        $mac = hash("sha512", base64_encode($message) . $secret);
        
        // encode the request elements
        $request = array(   'merchantid' => urlencode($merchantId),
                            'message' => urlencode(base64_encode($message)),
                            'mac' => urlencode($mac)
                        );
        return $request;
    }
//    /**
//     * Do request using cURL
//     * @return array
//     */
//    public function doRequest(){
//        $fields = $this->prepareRequest();
//               $fieldsString = "";
//        foreach ($fields as $key => $value) {
//            $fieldsString .= $key.'='.$value.'&';
//        }
//        rtrim($fieldsString, '&');
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $this->config->getEndpoint(SveaConfigurationProvider::HOSTED_ADMIN_TYPE).  $this->method);
//        curl_setopt($ch, CURLOPT_POST, count($fields));
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        //force curl to trust https
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        //returns a html page with redirecting to bank...
//        $responseXML = curl_exec($ch);
//        curl_close($ch);
//        
//        $responseObj = new \SimpleXMLElement($responseXML);
//
//
////        $sveaResponse = new \SveaResponse($responseObj, $this->countryCode, $this->config);
////        $paymentmethods = array();
////        foreach ($sveaResponse->response->paymentMethods as $method) {
////            $paymentmethods[] = (string)$method;
////        }
//     
//       return $paymentmethods;
//
//    }
}