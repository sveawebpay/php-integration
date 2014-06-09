<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * QueryTransactionResponse handles the query transaction response
 * 
 * Note that the amount and vat attributes only reflect the initial payment 
 * transaction result, they are not authorative after i.e. loweramount has
 * been called on a transaction, see the authorizedamount attribute.
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class QueryTransactionResponse extends HostedAdminResponse{

    public $rawQueryTransactionsResponse;
    
    /** @var string $transactionId  the queried transactionId */
    public $transactionId;
    /** @var string $customerrefno */
    public $customerrefno;
    /** @var string $merchantid */
    public $merchantid;
    /** @var string $status */
    public $status;
    /** @var string $amount  amount inc. vat in minor currency*/
    public $amount;
    /** @var string $currency */
    public $currency;
    /** @var string $vat  vat amount in minor currency */
    public $vat ;
    /** @var string $capturedamount */
    public $capturedamount;
    /** @var string $authorizedamount */
    public $authorizedamount;
    /** @var string $created */
    public $created;
    /** @var string $creditstatus */
    public $creditstatus;
    /** @var string $creditedamount */
    public $creditedamount;
    /** @var string $merchantresponsecode */
    public $merchantresponsecode;
    /** @var string $paymentmethod */
    public $paymentmethod;
    /** @var NumberedOrderRow[] $numberedOrderRows  array of NumberedOrderRows w/set Name, Description, ArticleNumber, AmountExVat, VatPercent, Quantity and Unit, rowNumber */
    public $numberedOrderRows;
      
    function __construct($message,$countryCode,$config) {
        parent::__construct($message,$countryCode,$config);
    }

    /**
     * formatXml() parses the query transaction response xml and sets the
     * response attributes accordingly.
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
       
        //        //SimpleXMLElement Object
        //(
        //    [@attributes] => Array
        //        (
        //            [id] => 579929
        //        )
        //    [customerrefno] => 313
        //    [merchantid] => 1130
        //    [status] => ANNULLED
        //    [amount] => 13000
        //    [currency] => SEK
        //    [vat] => 2600
        //    [capturedamount] => SimpleXMLElement Object
        //    [authorizedamount] => SimpleXMLElement Object
        //    [created] => 2014-03-17 13:08:00.897
        //    [creditstatus] => CREDNONE
        //    [creditedamount] => 0
        //    [merchantresponsecode] => 0
        //    [paymentmethod] => KORTCERT
        //    [callbackurl] => SimpleXMLElement Object
        //    [capturedate] => SimpleXMLElement Object
        //    [subscriptionid] => SimpleXMLElement Object
        //    [subscriptiontype] => SimpleXMLElement Object
        //    [customer] => SimpleXMLElement Object
        //        (
        //            [@attributes] => Array
        //                (
        //                    [id] => 8011
        //                )
        //            [firstname] => SimpleXMLElement Object
        //            [lastname] => SimpleXMLElement Object
        //            [initials] => SimpleXMLElement Object
        //            [email] => SimpleXMLElement Object
        //            [ssn] => SimpleXMLElement Object
        //            [address] => SimpleXMLElement Object
        //            [address2] => SimpleXMLElement Object
        //            [city] => SimpleXMLElement Object
        //            [country] => SE
        //            [zip] => SimpleXMLElement Object
        //            [phone] => SimpleXMLElement Object
        //            [vatnumber] => SimpleXMLElement Object
        //            [housenumber] => SimpleXMLElement Object
        //            [companyname] => SimpleXMLElement Object
        //            [fullname] => SimpleXMLElement Object
        //        )
        //    [cardtype] => VISA
        //    [maskedcardno] => 444433xxxxxx1100
        //    [eci] => SimpleXMLElement Object
        //    [mdstatus] => SimpleXMLElement Object
        //    [expiryyear] => 16
        //    [expirymonth] => 02
        //    [chname] => SimpleXMLElement Object
        //    [authcode] => 340112
        //    [orderrows] => SimpleXMLElement Object
        //        (
        //            [row] => Array
        //                (
        //                    [0] => SimpleXMLElement Object
        //                        (
        //                            [id] => 43233
        //                            [name] => SimpleXMLElement Object
        //                            [amount] => 12500
        //                            [vat] => 2500
        //                            [description] => Testprodukt 25%
        //                            [quantity] => 1.0
        //                            [sku] => SimpleXMLElement Object
        //                            [unit] => SimpleXMLElement Object
        //
        //                        )
        //                    [1] => SimpleXMLElement Object
        //                        (
        //                            [id] => 43234
        //                            [name] => SimpleXMLElement Object
        //                            [amount] => 500
        //                            [vat] => 100
        //                            [description] => Fastpris (Fast fraktpris)
        //                            [quantity] => 1.0
        //                            [sku] => SimpleXMLElement Object
        //                            [unit] => SimpleXMLElement Object
        //                )
        //        )
        //)
            
        // queryTransaction
        if(property_exists($hostedAdminResponse->transaction,"customerrefno") && property_exists($hostedAdminResponse->transaction,"merchantid")){
                
            $this->rawQueryTransactionsResponse = $hostedAdminResponse; // the raw GetOrders response
            
            $this->transactionId = (string)$hostedAdminResponse->transaction['id'];
            
            $this->customerrefno = (string)$hostedAdminResponse->transaction->customerrefno;
            $this->merchantid = (string)$hostedAdminResponse->transaction->merchantid;
            $this->status = (string)$hostedAdminResponse->transaction->status;
            $this->amount = (string)$hostedAdminResponse->transaction->amount;
            $this->currency = (string)$hostedAdminResponse->transaction->currency;
            $this->vat = (string)$hostedAdminResponse->transaction->vat;
            $this->capturedamount = (string)$hostedAdminResponse->transaction->capturedamount;
            $this->authorizedamount = (string)$hostedAdminResponse->transaction->authorizedamount;
            $this->created = (string)$hostedAdminResponse->transaction->created;
            $this->creditstatus = (string)$hostedAdminResponse->transaction->creditstatus;
            $this->creditedamount = (string)$hostedAdminResponse->transaction->creditedamount;
            $this->merchantresponsecode = (string)$hostedAdminResponse->transaction->merchantresponsecode;
            $this->paymentmethod = (string)$hostedAdminResponse->transaction->paymentmethod;

            $rownumber = 1;
            foreach( $hostedAdminResponse->transaction->orderrows->row as $orderrow ) {

                $orderrow = (array)$orderrow;
                //queried orderrow:
                // [name]
                // [amount]
                // [vat]
                // [description]
                // [quantity]
                // [sku]
                // [unit]
                
                $newrow = new \Svea\NumberedOrderRow(); // webpay orderrow
                //WebPayItem OrderRow:          
                // $articleNumber
                // $quantity
                // $unit
                // $amountExVat
                // $amountIncVat
                // $vatPercent
                // $name
                // $description
                // $discountPercent
                // $vatDiscount
                
                $newrow
                    ->setName( (string)$orderrow['name'] )
                    ->setAmountExVat( floatval( ($orderrow['amount']-$orderrow['vat']) )/100 )
                    ->setDescription( (string)$orderrow['description'] )
                    ->setQuantity( floatval((string)$orderrow['quantity']) )
                    ->setArticleNumber( (string)$orderrow['sku'] )     
                    ->setUnit( (string)$orderrow['unit'] )
                    ->setVatPercent( ( $orderrow['vat']/($orderrow['amount']-$orderrow['vat'])*100 ) )
                ;

                $newrow->creditInvoiceId = null;
                $newrow->invoiceId = null;
                $newrow->rowNumber = $rownumber;
                $newrow->status = null;

                $rownumber +=1;
                
                $this->numberedOrderRows[] = $newrow;
            }
        }  
    }
}
