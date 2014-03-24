<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Prepares a hosted payment. Returns an URL whereto the customer can be directed
 * to finish a the payment at a later time. Implements webservice preparepayment.
 * 
 * @author Kristian Grossman-Madsen
 */
class GetPaymentAddress {

    /** @var ConfigurationProvider */
    private $config;
    
    //preparepayment
    /** @var string  */
    private $countryCode;
    /** @var string */
    private $ipAddress;
    
    //payment
    private $paymentMethod;
//    private $lang;
//    private $currency;
//    private $amount;
//    private $vat;
//    private $customerrefno;
//    private $returnurl;
//    private $cancelurl;
//    private $callbackurl;
//    private $subscriptiontype;
//    private $simulatorcode;
//    private $excludepaymentmethods;
//    private $orderrows;
    
    
    /** @param ConfigurationProvider $config  instance implementing ConfigurationProvider */
    function __construct( $config ) {
        $this->config = $config;
    }

    /** @param string $countryCode */ 
    function setCountryCode( $countryCode ) {
        $this->countryCode = $countryCode;
        return $this;
    }
    
    /** @param string $ipAddress */ 
    function setIpAddress( $ipAddress ) {
        $this->ipAddress = $ipAddress;
        return $this;
    }
    
    /** @param string $paymentMethod */ 
    function setPaymentMethod( $paymentMethod ) {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
    

    // TODO write test for validation!
    /** 
     * checks that all mandatory attributes are set before building request 
     * note that isset() requires that an element is not only defined, but also
     * that it has been assigned a value.
     */
    function validateRequest() {
        if( !isset( $this->currency ) ) throw new \InvalidArgumentException("currency not set for preparepayment request");
        if( !isset( $this->amount ) ) throw new \InvalidArgumentException("amount not set for preparepayment request");
        if( !isset( $this->returnurl ) ) throw new \InvalidArgumentException("returnurl not set for preparepayment request");        
        // TODO doesn't validate contents of orderrows row elements
        
//        
//    /**
//     * prepares the elements used in the request to svea
//     */
//    public function prepareRequest() {
//
//        $xmlBuilder = new HostedXmlBuilder();
//        
//        // get our merchantid & secret
//        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
//        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
//        
//        // message contains the confirm request
//        $messageContents = array(
//            "transactionid" => $this->transactionId,
//            "capturedate" => $this->captureDate
//        ); 
//        $message = $xmlBuilder->getConfirmTransactionXML( $messageContents );        
//
//        // calculate mac
//        $mac = hash("sha512", base64_encode($message) . $secret);
//        
//        // encode the request elements
//        $request_fields = array( 
//            'merchantid' => urlencode($merchantId),
//            'message' => urlencode(base64_encode($message)),
//            'mac' => urlencode($mac)
//        );
//        return $request_fields;
//    }
//    /**
//     * Do request using cURL
//     * @return HostedAdminResponse
//     */
//    public function doRequest(){
//        $fields = $this->prepareRequest();
//        
//        $fieldsString = "";
//        foreach ($fields as $key => $value) {
//            $fieldsString .= $key.'='.$value.'&';
//        }
//        rtrim($fieldsString, '&');
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $this->config->getEndpoint(SveaConfigurationProvider::HOSTED_ADMIN_TYPE). "confirm");
//        curl_setopt($ch, CURLOPT_POST, count($fields));
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        //force curl to trust https
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        //returns a html page with redirecting to bank...
//        $responseXML = curl_exec($ch);
//        curl_close($ch);
//        
//        // create SveaResponse to handle confirm response
//        $responseObj = new \SimpleXMLElement($responseXML);        
//        $sveaResponse = new \SveaResponse($responseObj, $this->countryCode, $this->config);
//
//        return $sveaResponse->response; 
    }
}