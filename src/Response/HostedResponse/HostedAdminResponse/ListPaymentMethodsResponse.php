<?php
namespace Svea;

require_once 'HostedAdminResponse.php'; // fix for class loader sequencing problem
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * ListPaymentMethodsResponse handles the getpaymentmethods transaction response
 * consistent with other HostedAdmin responses (i.e. returns an HostedResponse object) 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class ListPaymentMethodsResponse extends HostedAdminResponse{

    /** string[] $paymentmethods  array containing available paymentmethods for this merchantid, @see PaymentMethod */
    public $paymentmethods;
    
    function __construct($message,$countryCode,$config) {
        parent::__construct($message,$countryCode,$config);
    }

    /**
     * formatXml() parses the getpaymentmethods transaction response xml into an object, and
     * then sets the response attributes accordingly.
     * 
     * @param string $hostedAdminResponseXML  hostedAdminResponse as xml
     */
    protected function formatXml($hostedAdminResponseXML) {
                
        $hostedAdminResponse = new \SimpleXMLElement($hostedAdminResponseXML);
        
        if ((string)$hostedAdminResponse->statuscode == '0') {
            $this->accepted = 1;
            $this->resultcode = '0';
        } else {
            $this->accepted = 0;
            $this->setErrorParams( (string)$hostedAdminResponse->statuscode ); 
        }

        //$this->paymentMethods = (array)$hostedAdminResponse->paymentmethods->paymentmethod;     // seems to break under php 5.3            
        foreach( $hostedAdminResponse->paymentmethods->paymentmethod as $paymentmethod) {       // compatibility w/php 5.3
            $this->paymentmethods[] = (string)$paymentmethod;
        }    
    }
}
