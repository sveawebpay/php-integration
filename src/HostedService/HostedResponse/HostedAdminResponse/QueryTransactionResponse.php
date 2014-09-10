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
    
    /** @var string $transactionId  -- the order id at Svea */
    public $transactionId;         

    /** @var string $customerrefno -- the customer reference number */
    public $customerrefno;
    
    /** @var string $merchantid -- the merchant id */
    public $merchantid;
    
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
    
    /** @var string $paymentmethod */
    public $paymentmethod;
    
    /** @var NumberedOrderRow[] $numberedOrderRows  array of NumberedOrderRows w/set Name, Description, ArticleNumber, AmountExVat, VatPercent, Quantity and Unit, rowNumber */
    public $numberedOrderRows;
    
    /** @var string $capturedate -- The date the transaction was captured, e.g. 2011-09-27 16:55:01.21 */ 
    public $capturedate;
    /** @var string $eci -- Enrollment status from MPI. If the card is 3Dsecure enabled or not. */
    public $eci;    
    /** @var string $mdstatus -- Value calculated from eci as requested by acquiring bank. */
    public $mdstatus;
    /** @var string $expiryyear -- Expire year of the card */
    public $expiryyear;
    /** @var string $expirymonth -- Expire month of the month */
    public $expirymonth;
    /** @var string $ch_name -- Cardholder name as entered by cardholder */
    public $ch_name;
    /** @var string $authcode -- EDB authorization code */
    public $authcode;
      
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
        if(property_exists($hostedAdminResponse->transaction,"customerrefno") && property_exists($hostedAdminResponse->transaction,"merchantid")){
                
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

            $this->capturedate = (string)$hostedAdminResponse->transaction->capturedate;
            $this->eci = (string)$hostedAdminResponse->transaction->eci;    
            $this->mdstatus = (string)$hostedAdminResponse->transaction->mdstatus;
            $this->expiryyear = (string)$hostedAdminResponse->transaction->expiryyear;
            $this->expirymonth = (string)$hostedAdminResponse->transaction->expirymonth;
            $this->ch_name = (string)$hostedAdminResponse->transaction->ch_name;
            $this->authcode = (string)$hostedAdminResponse->transaction->authcode;            
                        
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