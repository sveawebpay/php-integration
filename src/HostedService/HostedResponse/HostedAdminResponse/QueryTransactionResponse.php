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
 * There may be inconsistencies in how class attributes are capitalized, this is
 * due to a wish to conform with pre-existing attribute names. Please refer to 
 * the phpdoc for an authoritative listing of all response attribute names.
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class QueryTransactionResponse extends HostedAdminResponse{
    
    /** @var string $transactionId  -- the order id at Svea */
    public $transactionId;         

    /** @var string $clientOrderNumber -- the customer reference number, i.e. order number */
    public $clientOrderNumber;
    
    /** @var string $merchantId -- the merchant id */
    public $merchantId;
    
    /** @var string $status -- Latest transaction status, one of {AUTHORIZED, CONFIRMED, SUCCESS} */
    public $status;
    
    /** @var string $amount -- Total amount including VAT, in minor currency (e.g. SEK 10.50 = 1050) */
    public $amount;
    
    /** @var string $currency -- ISO 4217 alphabetic, e.g. SEK */
    public $currency;
    
    /** @var string $vat -- VAT, in minor currency */
    public $vat;
    
    /** @var string $capturedamount -- Captured amount */
    public $capturedamount;
    
    /** @var string $authorizedamount -- Authorized amount */
    public $authorizedamount;
    
    /** @var string $created -- Timestamp when transaction was created in Sveas' system, e.g. 2011-09-27 16:55:01.21 */
    public $created;
    
    /** @var string $creditstatus -- Status of the last credit attempt */
    public $creditstatus;
    
    /** @var string $creditedamount -- Total amount that has been credited, in minor currency */
    public $creditedamount;
    
    /** @var string $merchantresponsecode -- Last statuscode response returned to merchant */
    public $merchantresponsecode;
    
    /** @var string $paymentMethod */
    public $paymentMethod;
    
    /** @var NumberedOrderRow[] $numberedOrderRows  array of NumberedOrderRows w/set Name, Description, ArticleNumber, AmountExVat, VatPercent, Quantity and Unit, rowNumber */
    public $numberedOrderRows;
    
    /** @var string $callbackurl */
    public $callbackurl;
    
    /** @var string $capturedate -- The date the transaction was captured, e.g. 2011-09-27 16:55:01.21 */ 
    public $capturedate;

    /** @var string $subscriptionId */
    public $subscriptionId;
    
    /** @var string $subscriptiontype */
    public $subscriptiontype;

    /** @var string $cardType */
    public $cardType;
    
    /** @var string $maskedCardNumber */
    public $maskedCardNumber;
    
    /** @var string $eci -- Enrollment status from MPI. If the card is 3Dsecure enabled or not. */
    public $eci;    
    
    /** @var string $mdstatus -- Value calculated from eci as requested by acquiring bank. */
    public $mdstatus;
    
    /** @var string $expiryYear -- Expire year of the card */
    public $expiryYear;
    
    /** @var string $expiryMonth -- Expire month of the month */
    public $expiryMonth;
    
    /** @var string $chname -- Cardholder name as entered by cardholder */
    public $chname;
    
    /** @var string $authCode -- EDB authorization code */
    public $authCode; 
    
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
       
        //print_r( $hostedAdminResponse ); // uncomment to dump raw request response
                   
        // queryTransaction
        if( property_exists($hostedAdminResponse->transaction,"customerrefno") && property_exists($hostedAdminResponse->transaction,"merchantid") ){
                
            $this->transactionId = (string)$hostedAdminResponse->transaction['id'];
            
            $this->clientOrderNumber = (string)$hostedAdminResponse->transaction->customerrefno; // to confirm with HostedPaymentResponse
            $this->merchantId = (string)$hostedAdminResponse->transaction->merchantid;
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
            $this->paymentMethod = (string)$hostedAdminResponse->transaction->paymentmethod;
            $this->callbackurl = (string)$hostedAdminResponse->transaction->callbackurl;            
            $this->capturedate = (string)$hostedAdminResponse->transaction->capturedate;
            $this->subscriptionId = (string)$hostedAdminResponse->transaction->subscriptionid;
            $this->subscriptiontype = (string)$hostedAdminResponse->transaction->subscriptiontype;
            $this->cardType = (string)$hostedAdminResponse->transaction->cardtype;
            $this->maskedCardNumber = (string)$hostedAdminResponse->transaction->maskedcardno;                    
            $this->eci = (string)$hostedAdminResponse->transaction->eci;    
            $this->mdstatus = (string)$hostedAdminResponse->transaction->mdstatus;
            $this->expiryYear = (string)$hostedAdminResponse->transaction->expiryyear;
            $this->expiryMonth = (string)$hostedAdminResponse->transaction->expirymonth;
            $this->chname = (string)$hostedAdminResponse->transaction->chname;
            $this->authCode = (string)$hostedAdminResponse->transaction->authcode;            
                       
            //SimpleXMLElement Object
            //(
            //    [transaction] => SimpleXMLElement Object
            //        (
            //            [@attributes] => Array
            //                (
            //                    [id] => 581497
            //                )
            //
            //            [customerrefno] => test_recur_1
            //            [merchantid] => 1130
            //            [status] => SUCCESS
            //            [amount] => 500
            //            [currency] => SEK
            //            [vat] => 100
            //            [capturedamount] => 500
            //            [authorizedamount] => 500
            //            [created] => 2014-04-16 14:51:34.917
            //            [creditstatus] => CREDNONE
            //            [creditedamount] => 0
            //            [merchantresponsecode] => 0
            //            [paymentmethod] => KORTCERT
            //            [callbackurl] => SimpleXMLElement Object
            //                (
            //                )
            //
            //            [capturedate] => 2014-04-18 00:15:12.287
            //            [subscriptionid] => 2922
            //            [subscriptiontype] => RECURRINGCAPTURE
            //        )
            //
            //    [statuscode] => 0
            //)   

            if( property_exists($hostedAdminResponse->transaction, "orderrows") ) {            
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
                    ->setVatPercent( $this->calculateVatPercentFromVatAndAmount( $orderrow['vat'],$orderrow['amount'] ) )
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
    
    function calculateVatPercentFromVatAndAmount( $vat, $amount ) {
        $amountExVat = ($amount-$vat);
        $unroundedVatPercent = ($amountExVat > 0) ? ($vat/$amountExVat) : 0.00; // catch potential divide by zero
        $vatPercent = \Svea\Helper::bround($unroundedVatPercent,2) *100; // OrderRow has vatpercent as int.
        return $vatPercent;
    }
}
