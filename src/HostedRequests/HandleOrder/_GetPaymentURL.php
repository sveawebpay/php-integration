<?php
namespace Svea;

require_once 'HostedRequest.php';
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Prepares a hosted payment. Returns an URL whereto the customer can be directed
 * to finish a the payment at a later time. Implements webservice preparepayment.
 * 
 * @author Kristian Grossman-Madsen
 */
class getPaymentURL extends HostedRequest {

    //preparepayment
    /** @var string  */
    protected $countryCode;

    /** @var string */
    protected $ipAddress;
    
    //payment
    protected $paymentMethod;
//    protected $lang;
//    protected $currency;
//    protected $amount;
//    protected $vat;
//    protected $customerrefno;
//    protected $returnurl;
//    protected $cancelurl;
//    protected $callbackurl;
//    protected $subscriptiontype;
//    protected $simulatorcode;
//    protected $excludepaymentmethods;
//    protected $orderrows;
    
    
    /** @param ConfigurationProvider $config  instance implementing ConfigurationProvider */
    function __construct( $config ) {
        $this->method = "preparepayment";
        parent::__construct($config);
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
    }
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
}