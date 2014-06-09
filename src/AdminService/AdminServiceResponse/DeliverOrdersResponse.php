<?php
namespace Svea\AdminService;

/**
 * Handles the Svea Admin Web Service DeliverOrder request response.
 * 
 * @author Kristian Grossman-Madsen
 */
class DeliverOrdersResponse {

    /** @var int $accepted  true iff request was accepted by the service */
    public $accepted;    
    /** @var int $resultcode  response specific result code */
    public $resultcode;

    
    /** @var string errormessage  may be set iff accepted above is false */
    public $errormessage;   
    
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
        // was request accepted?
        $this->accepted = $message->ResultCode == 0 ? 1 : 0; // ResultCode of 0 means all went well.
        $this->errormessage = isset($message->ErrorMessage) ? $message->ErrorMessage : "";
        $this->resultcode = $message->ResultCode;

        // if successful, set deliverOrderResult, using the same attributes as for DeliverOrderEU?
        if ($this->accepted == 1) {

            $this->amount = $message->OrdersDelivered->DeliverOrderResult->DeliveredAmount;
            $this->orderType = $message->OrdersDelivered->DeliverOrderResult->OrderType;
            if( $this->orderType == "Invoice" ) {
                $this->invoiceId = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            } 
            else {
                $this->contractNumber = $message->OrdersDelivered->DeliverOrderResult->DeliveryReferenceNumber;
            }
            // we ignore ClientId and SveaOrderId
            //[ClientId] => 79021
            //[SveaOrderId] => 346761        
        }
    }
}
