<?php
namespace Svea\AdminService;

/**
 * Handles the Svea Admin Web Service GetOrders request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class GetOrdersResponse extends AdminServiceResponse {

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
    
    /** @var StdClass $rawGetOrdersResponse  contains the raw GetOrders response */
    public $rawGetOrdersResponse;
    
    function __construct($message) {        
        $this->formatObject($message);  
    }
    
    protected function formatObject($message) {   
   
        // was request accepted?
        parent::formatObject($message);
                      
        // if successful, set deliverOrderResult, using the same attributes as for DeliverOrderEU?
        if ($this->accepted == 1) {
            
            $this->rawGetOrdersResponse = $message; // the raw GetOrders response

            // populate GetOrdersResponse select attributes from the raw GetOrders response
            $order = $message->Orders->Order;

// changedDate
            $this->clientId = $order->ClientId;
            $this->clientOrderId = $order->ClientOrderId;
// createdDate
//creditReportStatusAccepted
//creditReportStatusCreatedDate            
            $this->currency = $order->Currency;
//Customer -- skapa CustomerIdentity & returnera -- samma för HostedService queryTransaction isf??
// ... (18 subentries)

//CustomerId
//CustomerReference
//DeliveryAddress -- ?                
            $this->isPossibleToAdminister = ($order->IsPossibleToAdminister === "true") ? true : false;
            $this->isPossibleToCancel = ($order->IsPossibleToCancel === 'true') ? true : false;
//Notes
            $this->orderDeliveryStatus = $order->OrderDeliveryStatus;
//Orderrows in här
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
                                    
            $this->orderStatus = $order->OrderStatus;
            $this->orderType = $order->OrderType;
//PaymentPlanDetails
//PendingReasons            
            $this->orderId = $order->SveaOrderId;
//SveaWillBuy

        }
    }
}
