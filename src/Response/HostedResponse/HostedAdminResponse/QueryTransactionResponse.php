<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * QueryTransactionResponse handles the query transaction response
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class QueryTransactionResponse extends HostedAdminResponse{

    /**
     * Calls HostedAdminResponse
     * 
     * @param SimpleXMLElement $message
     * @param string $countryCode
     * @param SveaConfigurationProvider $config
     */
    function __construct($message,$countryCode,$config) {
        parent::__construct($message,$countryCode,$config);
    }

    /**
     * formatXml() parses the query transaction response xml into an object, and
     * then sets the response attributes accordingly.
     * 
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
        
        // queryTransaction
        if(property_exists($hostedAdminResponse->transaction,"customerrefno") && property_exists($hostedAdminResponse->transaction,"merchantid")){
            
            print_r($hostedAdminResponse->transaction);
            
            $this->transactionId = $hostedAdminResponse->transaction->id;
            
            // todo set attributes to return according to spec?
            
//            $this->customerrefno = (string)$hostedAdminResponse->transaction->customerrefno;
//            $this->merchantid = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->status = (string)$hostedAdminResponse->transaction->status;
//            $this->amount = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->currency = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->vat = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->capturedamount = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->authorizedamount = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->created = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->creditstatus = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->creditedamount = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->merchantresponsecode = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->paymentmethod = (string)$hostedAdminResponse->transaction->merchantid;
//            $this->orderrows = (string)$hostedAdminResponse->transaction->merchantid;               //todo
//           
//SimpleXMLElement Object
//(
//    [@attributes] => Array
//        (
//            [id] => 579929
//        )
//
//    [customerrefno] => 313
//    [merchantid] => 1130
//    [status] => ANNULLED
//    [amount] => 13000
//    [currency] => SEK
//    [vat] => 2600
//    [capturedamount] => SimpleXMLElement Object
//        (
//        )
//
//    [authorizedamount] => SimpleXMLElement Object
//        (
//        )
//
//    [created] => 2014-03-17 13:08:00.897
//    [creditstatus] => CREDNONE
//    [creditedamount] => 0
//    [merchantresponsecode] => 0
//    [paymentmethod] => KORTCERT
//    [callbackurl] => SimpleXMLElement Object
//        (
//        )
//
//    [capturedate] => SimpleXMLElement Object
//        (
//        )
//
//    [subscriptionid] => SimpleXMLElement Object
//        (
//        )
//
//    [subscriptiontype] => SimpleXMLElement Object
//        (
//        )
//
//    [customer] => SimpleXMLElement Object
//        (
//            [@attributes] => Array
//                (
//                    [id] => 8011
//                )
//
//            [firstname] => SimpleXMLElement Object
//                (
//                )
//
//            [lastname] => SimpleXMLElement Object
//                (
//                )
//
//            [initials] => SimpleXMLElement Object
//                (
//                )
//
//            [email] => SimpleXMLElement Object
//                (
//                )
//
//            [ssn] => SimpleXMLElement Object
//                (
//                )
//
//            [address] => SimpleXMLElement Object
//                (
//                )
//
//            [address2] => SimpleXMLElement Object
//                (
//                )
//
//            [city] => SimpleXMLElement Object
//                (
//                )
//
//            [country] => SE
//            [zip] => SimpleXMLElement Object
//                (
//                )
//
//            [phone] => SimpleXMLElement Object
//                (
//                )
//
//            [vatnumber] => SimpleXMLElement Object
//                (
//                )
//
//            [housenumber] => SimpleXMLElement Object
//                (
//                )
//
//            [companyname] => SimpleXMLElement Object
//                (
//                )
//
//            [fullname] => SimpleXMLElement Object
//                (
//                )
//
//        )
//
//    [cardtype] => VISA
//    [maskedcardno] => 444433xxxxxx1100
//    [eci] => SimpleXMLElement Object
//        (
//        )
//
//    [mdstatus] => SimpleXMLElement Object
//        (
//        )
//
//    [expiryyear] => 16
//    [expirymonth] => 02
//    [chname] => SimpleXMLElement Object
//        (
//        )
//
//    [authcode] => 340112
//    [orderrows] => SimpleXMLElement Object
//        (
//            [row] => Array
//                (
//                    [0] => SimpleXMLElement Object
//                        (
//                            [id] => 43233
//                            [name] => SimpleXMLElement Object
//                                (
//                                )
//
//                            [amount] => 12500
//                            [vat] => 2500
//                            [description] => Testprodukt 25%
//                            [quantity] => 1.0
//                            [sku] => SimpleXMLElement Object
//                                (
//                                )
//
//                            [unit] => SimpleXMLElement Object
//                                (
//                                )
//
//                        )
//
//                    [1] => SimpleXMLElement Object
//                        (
//                            [id] => 43234
//                            [name] => SimpleXMLElement Object
//                                (
//                                )
//
//                            [amount] => 500
//                            [vat] => 100
//                            [description] => Fastpris (Fast fraktpris)
//                            [quantity] => 1.0
//                            [sku] => SimpleXMLElement Object
//                                (
//                                )
//
//                            [unit] => SimpleXMLElement Object
//                                (
//                                )
//
//                        )
//
//                )
//
//        )
//
//)

        }  
    }
}
