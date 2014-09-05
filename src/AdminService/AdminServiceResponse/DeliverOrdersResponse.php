<?php
namespace Svea\AdminService;

/**
 * Handles the Svea Admin Web Service DeliverOrder request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class DeliverOrdersResponse extends AdminServiceResponse {
    
    /** @var float $amount  (set iff accepted) the amount delivered with this request */
    public $amount;

    /** @var string $orderType  (set iff accepted)  one of [Invoice|PaymentPlan] */
    public $orderType;
    
    /** @var numeric $invoiceId  (set iff accepted, orderType Invoice)  the invoice id for the delivered order */
    public $invoiceId;

    /** @var numeric $contractNumber  (set iff accepted, orderType PaymentPlan)  the contract number for the delivered order */
    public $contractNumber;
   
    function __construct($message) {
        $this->formatObject($message);  
    }
    
    protected function formatObject($message) {
        parent::formatObject($message);
        
        if ($this->accepted == 1) {

            $this->rawDeliverOrdersResponse = $message;

            $this->amount = $message->OrdersDelivered->DeliverOrderResult->DeliveredAmount;
            $this->orderType = $message->OrdersDelivered->DeliverOrderResult->OrderType;
            if( $this->orderType == "Invoice" ) {
                $this->invoiceId = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            } 
            if( $this->orderType == "PaymentPlan" ) {
                $this->contractNumber = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            }     
        }
    }
}
