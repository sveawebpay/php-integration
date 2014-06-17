<?php
namespace Svea\AdminService;

/**
 * Handles the Svea Admin Web Service GetOrders request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class GetOrdersResponse {

    /** @var int $accepted  true iff request was accepted by the service */
    public $accepted;    
    /** @var int $resultcode  response specific result code */
    public $resultcode;    
    /** @var string errormessage  may be set iff accepted above is false */
    public $errormessage;   

    /** @var numeric $clientId */
    public $clientId;
    /** @var string $clientOrderId */
    public $clientOrderId;
    /** @var string $currency */
    public $currency;
    /** @var boolean $isPossibleToAdminister */
    public $isPossibleToAdminister;
    /** @var boolean $isPossibleToCancel */
    public $isPossibleToCancel;
    /** @var string $orderDeliveryStatus */
    public $orderDeliveryStatus;
    /** @var string $orderStatus */
    public $orderStatus;
    /** @var string $orderType */
    public $orderType;
    /** @var numeric $orderId */
    public $orderId;
    /** @var Svea\OrderRow[] $numberedOrderRows  array of OrderRow objects */
    public $numberedOrderRows;
    
//     stdClass Object
//     (
//         [ErrorMessage] => 
//         [ResultCode] => 0
//         [Orders] => stdClass Object
//             (
//                 [Order] => stdClass Object
//                     (
//                         [ChangedDate] => 
//                         [ClientId] => 79021
//                         [ClientOrderId] => 449
//                         [CreatedDate] => 2014-05-19T16:04:54.787
//                         [CreditReportStatus] => stdClass Object
//                             (
//                                 [Accepted] => true
//                                 [CreationDate] => 2014-05-19T16:04:54.893
//                             )
//
//                         [Currency] => SEK
//                         [Customer] => stdClass Object
//                             (
//                                 [CoAddress] => c/o Eriksson, Erik
//                                 [CompanyIdentity] => 
//                                 [CountryCode] => SE
//                                 [CustomerType] => Individual
//                                 [Email] => foo.bar@sveaekonomi.se
//                                 [FullName] => Persson, Tess T
//                                 [HouseNumber] => 
//                                 [IndividualIdentity] => stdClass Object
//                                     (
//                                         [BirthDate] => 
//                                         [FirstName] => 
//                                         [Initials] => 
//                                         [LastName] => 
//                                     )
//
//                                 [Locality] => Stan
//                                 [NationalIdNumber] => 194605092222
//                                 [PhoneNumber] => 08 - 111 111 11
//                                 [PublicKey] => 
//                                 [Street] => Testgatan 1
//                                 [ZipCode] => 99999
//                             )
//
//                         [CustomerId] => 1000117
//                         [CustomerReference] => 
//                         [DeliveryAddress] => 
//                         [IsPossibleToAdminister] => false
//                         [IsPossibleToCancel] => true
//                         [Notes] => 
//                         [OrderDeliveryStatus] => Created
//                         [OrderRows] => stdClass Object
//                             (
//                                 [NumberedOrderRow] => Array
//                                     (
//                                         [0] => stdClass Object
//                                             (
//                                                 [ArticleNumber] => 
//                                                 [Description] => Dyr produkt 25%
//                                                 [DiscountPercent] => 0.00
//                                                 [NumberOfUnits] => 2.00
//                                                 [PricePerUnit] => 2000.00
//                                                 [Unit] => 
//                                                 [VatPercent] => 25.00
//                                                 [CreditInvoiceId] => 
//                                                 [InvoiceId] => 
//                                                 [RowNumber] => 1
//                                                 [Status] => NotDelivered
//                                             )
//
//                                         [1] => stdClass Object
//                                             (
//                                                 [ArticleNumber] => 
//                                                 [Description] => Testprodukt 1kr 25%
//                                                 [DiscountPercent] => 0.00
//                                                 [NumberOfUnits] => 1.00
//                                                 [PricePerUnit] => 1.00
//                                                 [Unit] => 
//                                                 [VatPercent] => 25.00
//                                                 [CreditInvoiceId] => 
//                                                 [InvoiceId] => 
//                                                 [RowNumber] => 2
//                                                 [Status] => NotDelivered
//                                             )
//
//                                         [2] => stdClass Object
//                                             (
//                                                 [ArticleNumber] => 
//                                                 [Description] => Fastpris (Fast fraktpris)
//                                                 [DiscountPercent] => 0.00
//                                                 [NumberOfUnits] => 1.00
//                                                 [PricePerUnit] => 4.00
//                                                 [Unit] => 
//                                                 [VatPercent] => 25.00
//                                                 [CreditInvoiceId] => 
//                                                 [InvoiceId] => 
//                                                 [RowNumber] => 3
//                                                 [Status] => NotDelivered
//                                             )
//
//                                         [3] => stdClass Object
//                                             (
//                                                 [ArticleNumber] => 
//                                                 [Description] => Svea Fakturaavgift:: 20.00kr (SE)
//                                                 [DiscountPercent] => 0.00
//                                                 [NumberOfUnits] => 1.00
//                                                 [PricePerUnit] => 20.00
//                                                 [Unit] => 
//                                                 [VatPercent] => 0.00
//                                                 [CreditInvoiceId] => 
//                                                 [InvoiceId] => 
//                                                 [RowNumber] => 4
//                                                 [Status] => NotDelivered
//                                             )
//                                     )
//                             )
//                         [OrderStatus] => Active
//                         [OrderType] => Invoice
//                         [PaymentPlanDetails] => 
//                         [PendingReasons] => 
//                         [SveaOrderId] => 348629
//                         [SveaWillBuy] => true
//                     )
//             )
//     )    
    /** @var StdClass $rawGetOrdersResponse  contains the raw GetOrders response */
    public $rawGetOrdersResponse;
    
    function __construct($message) {        
        $this->formatObject($message);  
    }
    
    protected function formatObject($message) {   
              
        // was request accepted?
        $this->accepted = ($message->ResultCode == 0) ? 1 : 0; // ResultCode of 0 means all went well.
        $this->errormessage = isset($message->ErrorMessage) ? $message->ErrorMessage : "";
        $this->resultcode = $message->ResultCode;

        // if successful, set deliverOrderResult, using the same attributes as for DeliverOrderEU?
        if ($this->accepted == 1) {
            
            $this->rawGetOrdersResponse = $message; // the raw GetOrders response

            // populate GetOrdersResponse select attributes from the raw GetOrders response
            $order = $message->Orders->Order;

            $this->clientId = $order->ClientId;
            $this->clientOrderId = $order->ClientOrderId;
            $this->currency = $order->Currency;
            
            $this->isPossibleToAdminister = $order->IsPossibleToAdminister;
            $this->isPossibleToCancel = $order->IsPossibleToCancel;
            $this->orderDeliveryStatus = $order->OrderDeliveryStatus;
            
            $this->orderStatus = $order->OrderStatus;
            $this->orderType = $order->OrderType;
            $this->orderId = $order->SveaOrderId;

            // for each numbered orderrow, add it to the numberedOrderRow array
            foreach( $order->OrderRows->NumberedOrderRow as $row ) {
                //GetOrders NumberedOrderRow:
                // [ArticleNumber]
                // [Description]
                // [DiscountPercent]
                // [NumberOfUnits]
                // [PricePerUnit]
                // [Unit]
                // [VatPercent]
                // [CreditInvoiceId]
                // [InvoiceId]
                // [RowNumber]
                // [Status]
            
                $newrow = new \Svea\NumberedOrderRow(); // webpay orderrow
                //WebPayItem OrderRow:          
                // $articleNumber   *
                // $quantity        *
                // $unit            *
                // $amountExVat     *
                // $amountIncVat    not used
                // $vatPercent      *
                // $name            not used
                // $description     *
                // $discountPercent *
                // $vatDiscount     not used
                
                $newrow
                    //->setName()
                    ->setAmountExVat( $row->PricePerUnit )
                    ->setDescription( $row->Description)
                    ->setQuantity( $row->NumberOfUnits )
                    ->setArticleNumber( $row->ArticleNumber )     
                    ->setUnit( $row->Unit )
                    ->setVatPercent( $row->VatPercent )
                    ->setDiscountPercent( $row->DiscountPercent )
                ;
                
                $newrow->creditInvoiceId = $row->CreditInvoiceId;
                $newrow->invoiceId = $row->InvoiceId;
                $newrow->rowNumber = $row->RowNumber;
                $newrow->status = $row->Status;
                
                $this->numberedOrderRows[] = $newrow;   
            }                
        }
    }
}
