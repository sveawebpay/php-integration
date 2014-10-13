<?php
namespace Svea\AdminService;

/**
 * Handles the Svea Admin Web Service DeliverOrder request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class DeliverOrdersResponse extends AdminServiceResponse {
 
    /** @var string $clientId */
    public $clientId;
    
    /** @var float $amount  (set iff accepted) the amount delivered with this request */
    public $amount;

    /** @var string $invoiceId  (set iff accepted, orderType Invoice)  the invoice id for the delivered order */
    public $invoiceId;

    /** @var string $contractNumber  (set iff accepted, orderType PaymentPlan)  the contract number for the delivered order */
    public $contractNumber;
    
    /** @var string $orderType */
    public $orderType;

    /** @var string $orderId */
    public $orderId;   
    
    function __construct($message) {
        $this->formatObject($message);  
    }
    
    protected function formatObject($message) {
        parent::formatObject($message);
        
        if ($this->accepted == 1) {
            
            $this->clientId = $message->OrdersDelivered->DeliverOrderResult->ClientId;            
            $this->amount = $message->OrdersDelivered->DeliverOrderResult->DeliveredAmount;
            
            if( $message->OrdersDelivered->DeliverOrderResult->OrderType == \ConfigurationProvider::INVOICE_TYPE ) {
                $this->invoiceId = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            } 
            
            if( $message->OrdersDelivered->DeliverOrderResult->OrderType == \ConfigurationProvider::PAYMENTPLAN_TYPE ) {
                $this->contractNumber = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            }     
            
            $this->orderType = $message->OrdersDelivered->DeliverOrderResult->OrderType;
            $this->orderId = $message->OrdersDelivered->DeliverOrderResult->SveaOrderId;
        }
    }
}