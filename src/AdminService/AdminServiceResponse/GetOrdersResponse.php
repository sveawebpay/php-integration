<?php
namespace Svea\AdminService;

/**
 * Handles the Svea Admin Web Service GetOrders request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class GetOrdersResponse extends AdminServiceResponse {
    
    // TODO annotate phpdoc attributes below w/info from admin service api Order structure
    
    /** @var string $changedDate */
    public $changedDate;
    /** @var numeric $clientId */
    public $clientId;
    /** @var string $clientOrderId */
    public $clientOrderId;
    /** @var string $createdDate */
    public $createdDate;
    
    /** @var boolean $creditReportStatusAccepted */
    public $creditReportStatusAccepted;
    /** @var string $creditReportStatusCreationDate */    
    public $creditReportStatusCreationDate;
    
    /** @var string $currency */ 
    public $currency;    
//Customer -- skapa CustomerIdentity & returnera -- samma för HostedService queryTransaction isf??
// ... (18 subentries)
    
    /** @var numeric $customerId */
    public $customerId;
    /** @var string $customerReference */
    public $customerReference;
    /** @var $deliveryAddress */
    public $deliveryAddress;     
    /** @var boolean $isPossibleToAdminister */
    public $isPossibleToAdminister;
    /** @var boolean $isPossibleToCancel */
    public $isPossibleToCancel;
    /** @var string $notes */
    public $notes;
    /** @var string $orderDeliveryStatus */
    public $orderDeliveryStatus;

    /** @var Svea\OrderRow[] $numberedOrderRows  array of OrderRow objects */
    public $numberedOrderRows;

    /** @var string $orderStatus */
    public $orderStatus;
    /** @var string $orderType */
    public $orderType;
    /** @var $paymentPlanDetails */
    public $paymentPlanDetails;    
    /** @var $pendingReasons */
    public $pendingReasons;    
    /** @var numeric $orderId */
    public $orderId;
    /** @var boolean $sveaWillBuy */
    public $sveaWillBuy;
    
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

            $this->changedDate = $order->ChangedDate;
            $this->clientId = $order->ClientId;
            $this->clientOrderId = $order->ClientOrderId;
            $this->createdDate = $order->CreatedDate;
            
            $this->creditReportStatusAccepted = ($order->CreditReportStatus->Accepted === "true") ? true : false;
            $this->creditReportStatusCreationDate = $order->CreditReportStatus->CreationDate;
         
            $this->currency = $order->Currency;
//Customer -- skapa CustomerIdentity & returnera -- samma för HostedService queryTransaction isf??
// ... (18 subentries)

            $this->customerId = $order->CustomerId;
            $this->customerReference = $order->CustomerReference;
            $this->deliveryAddress = $order->DeliveryAddress;             
            $this->isPossibleToAdminister = ($order->IsPossibleToAdminister === "true") ? true : false;
            $this->isPossibleToCancel = ($order->IsPossibleToCancel === 'true') ? true : false;
            $this->notes = $order->Notes;
            $this->orderDeliveryStatus = $order->OrderDeliveryStatus;

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
            $this->paymentPlanDetails = $order->PaymentPlanDetails;
            $this->pendingReasons = $order->PendingReasons;         
            $this->orderId = $order->SveaOrderId;
            $this->sveaWillBuy = ($order->SveaWillBuy === 'true') ? true : false;

        }
    }
}
